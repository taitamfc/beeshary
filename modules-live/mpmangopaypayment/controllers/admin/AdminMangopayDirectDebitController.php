<?php
/**
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminMangopayDirectDebitController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->identifier = 'id';
        $this->table = 'wk_mp_mangopay_transaction';
        $this->className = 'MangopayTransaction';
        parent::__construct();

        $this->_select = 'a.`transaction_id`, a.`order_reference`,
        CONCAT(a.`credited_amount`, " ", a.`currency`) as total_paid, a.`date_add`';

        // select only which are create with mandate Id (direct debit transactions)
        $this->_where .= ' AND a.`payment_type` = \''.pSQL(MangopayTransaction::WK_PAYMENT_TYPE_DIRECT_DEBIT).'\'';

        if ($mangopayClientId = Configuration::get('WK_MP_MANGOPAY_CLIENTID')) {
            $this->_where .= ' AND a.`mgp_clientid` = \''.pSQL($mangopayClientId).'\'';
        }
        $this->_orderBy = 'a.date_add';
        $this->_orderWay = 'DESC';
        $this->fields_list = array(
            'order_reference' => array(
                'title' => $this->l('Order Reference'),
                'align' => 'center',
                'havingFilter' => true,
            ),
            'transaction_id' => array(
                'title' => $this->l('Transaction Id'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'mandate_id' => array(
                'title' => $this->l('Mandate ID'),
                'align' => 'center',
            ),
            'total_paid' => array(
                'title' => $this->l('Credited Amount'),
                'align' => 'center',
                'havingFilter' => true,
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'align' => 'center',
                'type' => 'date',
                'havingFilter' => true,
            ),
        );
    }

    /**
     * Remove new toolbar button on backend.
     * @return void
     */
    public function initToolbar()
    {
        unset($this->toolbar_btn['new']);
    }

    /**
     * renderList generate renderlist with edit and delete action.
     * @return [type] [description]
     */
    public function renderList()
    {
        $this->addRowAction('view');
        return parent::renderList();
    }

    public function renderView()
    {
        $mangopayClientId = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
        $id = Tools::getValue('id');
        $objMgpTransaction = new MangopayTransaction($id);
        $orderReference = $objMgpTransaction->order_reference;
        if ($orderReference) {
            $objMgpTransfers = new MangopayTransferDetails();
            if ($allTransfersDetails = $objMgpTransfers->getTransferDetailsByOrderReference($orderReference)) {
                foreach ($allTransfersDetails as &$transfer) {
                    $idEmployee = MangopayConfig::getSupperAdmin();
                    $objEmployee = new Employee($idEmployee);
                    if (Configuration::get('WK_MP_SUPERADMIN_EMAIL')) {
                        $admin_email = Configuration::get('WK_MP_SUPERADMIN_EMAIL');
                    } else {
                        $admin_email = $objEmployee->email;
                    }
                    $transfer['name'] = $objEmployee->firstname.' '.$objEmployee->lastname;
                    $transfer['email'] = $admin_email;
                    $transfer['my_account_link'] = '';
                }
            }
            $transactionsDetails = $objMgpTransaction->getTransactionsDetailsByOrderReference($orderReference);
            if ($transactionsDetails) {
                $objPayinRefund = new MangopayPayInRefund();
                $transactionsDetails['refunded_amt'] = $objPayinRefund->getRefundSumByPayIn(
                    $transactionsDetails['transaction_id']
                ) * 100;
            }
            $objMangopayService = new MangopayMpService();
            $payInDetail = $objMangopayService->getMangopayPayInDetails($objMgpTransaction->transaction_id);

            $this->context->smarty->assign('payInStatus', $payInDetail['Status']);
            $this->context->smarty->assign('allTransfersDetails', $allTransfersDetails);
            $this->context->smarty->assign('transactionsDetails', $transactionsDetails);
        }
        $this->context->smarty->assign('id_transaction', $id);
        $this->context->smarty->assign('mgpClientId', $mangopayClientId);

        return parent::renderView();
    }

    public function postProcess()
    {
        try {
            if (($id = Tools::getValue('id')) && Tools::getValue('releasepayment')) {
                $objMgpTransaction = new MangopayTransaction(Tools::getValue('id'));
                if (Validate::isLoadedObject($objMgpTransaction)) {
                    $orderRef = $objMgpTransaction->order_reference;
                    if ($objMgpTransaction->payment_type == MangopayTransaction::WK_PAYMENT_TYPE_DIRECT_DEBIT) {
                        if ($objMgpTransaction->amount_paid) {
                            $objMgpTransfer = new MangopayTransferDetails();
                            if (!$objMgpTransfer->getTransferDetailsByOrderReference($orderRef)) {
                                $objMgpConfig = new MangopayConfig();
                                if ($orders = $objMgpConfig->getOrderIdsByOrderReference($orderRef)) {
                                    if (Validate::isLoadedObject(
                                        $order = new Order((int) $orders[0]['id_order'])
                                    )) {
                                        $cart = new Cart((int) $order->id_cart);
                                        $this->context->cart = $cart;
                                        $cartRules = $cart->getCartRules();
                                        $cartProducts = $cart->getProducts();
                                        $objSplitPayment = new WkMpSellerPaymentSplit();

                                        foreach ($cartProducts as $key => $product) {
                                            $orderProductData = $objMgpTransfer->getOrderTimeProductData(
                                                $cart->id,
                                                $product['id_product'],
                                                $product['id_product_attribute']
                                            );
                                            $cartProducts[$key]['price'] = $orderProductData['unit_price_tax_excl'];
                                            $cartProducts[$key]['price_wt'] = $orderProductData['unit_price_tax_incl'];
                                            $cartProducts[$key]['total'] = $orderProductData['total_price_tax_excl'];
                                            $cartProducts[$key]['total_wt'] = $orderProductData['total_price_tax_incl'];
                                        }
                                        $custComm = $objSplitPayment->paymentGatewaySplitedAmount(
                                            $cartRules,
                                            $cartProducts
                                        );
                                        $objMgpTransac = new MangopayTransaction();
                                        $newCustCum = $objMgpTransac->getCustomizedCustomerCommissionArray($custComm);
                                        $objMgpTransac->transferAmountsToWallets(
                                            $newCustCum,
                                            $cart->id,
                                            (int) $cart->id_customer
                                        );
                                        Tools::redirectAdmin(
                                            self::$currentIndex.'&id='.$id.
                                            '&viewwk_mp_mangopay_transaction&conf=3&token='.$this->token
                                        );
                                    } else {
                                        $this->errors[] = $this->l('Order not found.');
                                    }
                                } else {
                                    $this->errors[] = $this->l('Order not found.');
                                }
                            } else {
                                $this->errors[] = $this->l('Money has transfered already to respective mangopay
                                wallets.');
                            }
                        } else {
                            $this->errors[] = $this->l('0 amount found for transfer.');
                        }
                    } else {
                        $this->errors[] = $this->l('Bankwire details not found for this transaction.');
                    }
                } else {
                    $this->errors[] = $this->l('The object cannot be loaded (or found).');
                }
            } elseif (Tools::isSubmit('partial_mgp_transfer_Refund')) {
                $this->mangopayTransferRefundProcess();
            } elseif (Tools::isSubmit('partial_mgp_payin_Refund')) {
                $this->mangopayPayInRefundProcess();
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
        if (count($this->errors)) {
            $this->display = 'view';
        }
        parent::postProcess();
    }

    private function mangopayTransferRefundProcess()
    {
        $idTrans = trim(Tools::getValue('id'));
        $mgpTransferId = trim(Tools::getValue('mgp_transfer_id'));
        $mgpUserId = trim(Tools::getValue('mgp_transfer_author_id'));
        $orderReference = trim(Tools::getValue('mgp_order_reference'));
        if (!$idTrans) {
            $this->errors[] = $this->l('Transaction not found. Please try again.');
        }
        if (!$mgpTransferId) {
            $this->errors[] = $this->l('Transfer Id not found for refund. Please try again.');
        }
        if (!$mgpUserId) {
            $this->errors[] = $this->l('Transfer Author Id not found for this transfer. Please try again.');
        }
        if (!$orderReference) {
            $this->errors[] = $this->l('Order not found for this transaction. Please try again.');
        }
        if (!count($this->errors)) {
            $objMangopayService = new MangopayMpService();
            $params = array('transfer_id' => $mgpTransferId, 'author_id' => $mgpUserId);
            $mgpTransfRefund = $objMangopayService->createMangopayTransferRefund($params);
            if ($mgpTransfRefund['Status'] != 'FAILED') {
                $objMangopayTransfer = new MangopayTransferDetails();
                if ($objMangopayTransfer->updateRefundDetailsByTransferId(
                    $mgpTransfRefund['InitialTransactionId'],
                    $mgpTransfRefund['Id'],
                    'Admin'
                )) {
                    if (Configuration::get('WK_MP_MANGOPAY_TRANSFER_TO') == 2) { //for transfer to card
                        $objMangopayTrans = new MangopayTransaction();
                        $mgpTransaction = $objMangopayTrans->getTransactionsDetailsByOrderReference(
                            $orderReference
                        );
                        if ($mgpPayinId = $mgpTransaction['transaction_id']) {
                            $mangopayTransferDetail = $objMangopayTransfer->getTransferDetailByTransferId(
                                $mgpTransferId
                            );
                            $amount = $mangopayTransferDetail['amount'];
                            $mgpCurrency = $mangopayTransferDetail['currency'];

                            $params = array(
                                'payin_id' => $mgpPayinId,
                                'author_id' => $mgpUserId,
                                'amount' => $amount,
                                'currency' => $mgpCurrency
                            );
                            $mgpPayinRefund = $objMangopayService->createMangopayPayInRefund($params);
                            if ($mgpPayinRefund['Status'] != 'FAILED') {
                                $objPayInRefund = new MangopayPayInRefund();
                                $objPayInRefund->payin_id = $mgpPayinId;
                                $objPayInRefund->amount = $amount;
                                $objPayInRefund->refund_id = $mgpPayinRefund['Id'];
                                if ($objPayInRefund->save()) {
                                    //Update transfer detail table
                                    if ($objMangopayTransfer->updatePayInRefundDetailsByOrderReference(
                                        $orderReference
                                    )) {
                                        Tools::redirectAdmin(
                                            self::$currentIndex.'&id='.$idTrans.
                                            '&viewwk_mangopay_transaction&conf=3&token='.$this->token
                                        );
                                    }
                                }
                            } else {
                                $this->errors[] = $this->l(
                                    'Some error occured while transfering amount to buyer card'
                                );
                            }
                        } else {
                            $this->errors[] = $this->l('Transaction Id not found while transfering amount to
                            buyer card');
                        }
                    } else {
                        Tools::redirectAdmin(
                            self::$currentIndex.'&id='.$idTrans.'&viewwk_mangopay_transaction&conf=3&token='.
                            $this->token
                        );
                    }
                } else {
                    $this->errors[] = $this->l('Some error occurred while updating refund transfer details');
                }
            } else {
                $this->errors[] = $this->l('Some error occurred in transfer refund process. Please try again.');
            }
        }
    }

    private function mangopayPayInRefundProcess()
    {
        $id =Tools::getValue('id');
        $amount = Tools::getValue('mgp_partial_amount');
        $mgpPayinId = Tools::getValue('mgp_payin_id');
        $mgpUserId = Tools::getValue('mgp_author_id');
        $mgpCurrency = Tools::getValue('mgp_currency');
        if (!$mgpPayinId) {
            $this->errors[] = $this->l('Mangopay Pay In Id is missing for this transaction.');
        }
        if (!$mgpCurrency) {
            $this->errors[] = $this->l('Mangopay Pay In currency is missing for this transaction.');
        }
        if (!$mgpUserId) {
            $this->errors[] = $this->l('Mangopay Author Id is missing for this transaction.');
        }
        if (!$amount) {
            $this->errors[] = $this->l('Enter the pay in refund amount.');
        } elseif (!Validate::isPrice($amount)) {
            $this->errors[] = $this->l('Enter the payin amount in 0.00 formate.');
        }
        if (!count($this->errors)) {
            $objMgpService = new MangopayMpService();
            $params = array(
                'payin_id' => $mgpPayinId,
                'author_id' => $mgpUserId,
                'amount' => $amount,
                'currency' => $mgpCurrency
            );
            $mgpPayinRefund = $objMgpService->createMangopayPayInRefund($params);
            if ($mgpPayinRefund['Status'] != 'FAILED') {
                $objPayinRefund = new MangopayPayInRefund();
                $objPayinRefund->payin_id = $mgpPayinId;
                $objPayinRefund->amount = $amount;
                $objPayinRefund->refund_id = $mgpPayinRefund['Id'];
                $objPayinRefund->save();
                Tools::redirectAdmin(
                    self::$currentIndex.'&id='.$id.'&viewwk_mangopay_transaction&conf=3&token='.$this->token
                );
            } else {
                if (isset($mgpPayinRefund['ResultMessage']) && $mgpPayinRefund['ResultMessage']) {
                    $this->errors[] = $mgpPayinRefund['ResultMessage'];
                } else {
                    $this->errors[] = $this->l('Some error occured while PayIn refund. Please try again.');
                }
            }
        }
    }
}
