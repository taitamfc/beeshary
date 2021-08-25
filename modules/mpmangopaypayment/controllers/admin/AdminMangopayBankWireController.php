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

class AdminMangopayBankWireController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->identifier = 'id';
        $this->table = 'wk_mp_mangopay_transaction';
        $this->className = 'MangopayTransaction';
        parent::__construct();

        $this->_join .= ' JOIN `'._DB_PREFIX_.'wk_mp_mangopay_bankwire_details` mbd ON
        (mbd.`id_mangopay_transaction` = a.`id`)';
        $this->_select = ' mbd.id_mangopay_transaction, mbd.mgp_wire_reference, mbd.mgp_account_type,
        mbd.mgp_account_owner_name, mbd.mgp_account_iban, mbd.mgp_account_bic';

        $this->_where .= ' AND a.`payment_type` = \''.pSQL(MangopayTransaction::WK_PAYMENT_TYPE_BANKWIRE).'\'';

        $this->_orderBy = 'a.date_add';
        $this->_orderWay = 'DESC';

        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('Id'),
                'align' => 'center',
            ),
            'order_reference' => array(
                'title' => $this->l('Order Reference'),
                'align' => 'center',
                'hint' => $this->l('Prestashop Order Reference'),
            ),
            'transaction_id' => array(
                'title' => $this->l('PayIn Id'),
                'align' => 'center',
                'hint' => $this->l('Mangopay PayIn Id'),
            ),
            'mgp_wire_reference' => array(
                'title' => $this->l('BankWire Ref.'),
                'align' => 'center',
                'hint' => $this->l('Mangopay BankWire Reference'),
            ),
            'mgp_account_type' => array(
                'title' => $this->l('Account Type'),
                'align' => 'center',
            ),
            'status' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
                'hint' => $this->l('Mangopay Bankwire Status'),
            ),
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'align' => 'center',
                'type' => 'date',
                'hint' => $this->l('Order Date'),
            ),
        );
        $this->addRowAction('view');
    }

    public function initToolbar()
    {
        unset($this->toolbar_btn['new']);
    }

    public function renderView()
    {
        $objMgpTransac = new MangopayTransaction(Tools::getValue('id'));
        if (Validate::isLoadedObject($objMgpTransac) && $objMgpTransac->transaction_id) {
            // set API required variables
            $mangoPayApi = new MangoPay\MangoPayApi();
            $mangoPayApi->Config->ClientId = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
            $mangoPayApi->Config->ClientPassword = Configuration::get('WK_MP_MANGOPAY_PASSPHRASE');
            $mangoPayApi->Config->TemporaryFolder = _PS_MODULE_DIR_.'mpmangopaypayment/temp/';

            if (Configuration::get('WK_MP_MANGOPAY_MODE') == 'sandbox') {
                $mangoPayApi->Config->BaseUrl = 'https://api.sandbox.mangopay.com';
            } else {
                $mangoPayApi->Config->BaseUrl = 'https://api.mangopay.com';
            }
            $transferDetails = array();
            if ($bankwireDetails = $objMgpTransac->getBankWirePayinByOrderReference($objMgpTransac->order_reference)) {
                $objMgpTransfer = new MangopayTransferDetails();
                if ($transferDetails = $objMgpTransfer->getTransferDetailsByOrderReference(
                    $objMgpTransac->order_reference
                )) {
                    foreach ($transferDetails as &$transfer) {
                        if ($transfer['id_seller']) {
                            $seller = WkMpSeller::getSeller($transfer['id_seller'], $this->context->language->id);
                            if ($seller) {
                                $transfer['name'] = $seller['seller_firstname'].' '.$seller['seller_lastname'];
                                $transfer['email'] = $seller['business_email'];
                                $transfer['my_account_link'] = $this->context->link->getAdminLink(
                                    'AdminSellerInfoDetail'
                                ).'&id_seller='.$transfer['id_seller'].'&viewwk_mp_seller';
                            } else {
                                $transfer['name'] = $this->l('Seller not exist');
                                $transfer['email'] = $this->l('Seller not exist');
                                $transfer['my_account_link'] = '#';
                                $transfer['not_exist_seller'] = 1;
                            }
                        } else {
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
                }
                $this->context->smarty->assign('bankwireDetails', $bankwireDetails);
                $this->context->smarty->assign('transferDetails', $transferDetails);
            }
            $status = 'CREATED';
            try {
                $payIn = $mangoPayApi->PayIns->Get($objMgpTransac->transaction_id);
                if ($payIn->Status == 'SUCCEEDED') {
                    $status = 'SUCCEEDED';
                }
            } catch (Exception $e) {
                $this->errors[] = $e->getMessage();
            }
            $this->context->smarty->assign('status', $status);
        } else {
            $this->errors[] = $this->l('The object cannot be loaded (or found).');
        }
        return parent::renderView();
    }

    public function postProcess()
    {
        try {
            if (($id = Tools::getValue('id')) && Tools::getValue('releasepayment')) {
                $objMgpTransac = new MangopayTransaction(Tools::getValue('id'));
                if (Validate::isLoadedObject($objMgpTransac)) {
                    $orderRef = $objMgpTransac->order_reference;
                    if ($bankwireDetails = $objMgpTransac->getBankWirePayinByOrderReference($orderRef)) {
                        if (isset($bankwireDetails['declared_amount']) && $bankwireDetails['declared_amount']) {
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
            }
            if (Tools::isSubmit('partial_mgp_transfer_Refund')) {
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
                            Tools::redirectAdmin(
                                self::$currentIndex.'&id='.$id.'&viewwk_mp_mangopay_transaction&conf=3&token='.
                                $this->token
                            );
                        } else {
                            $this->errors[] = $this->l('Some error occurred while updating refund transfer details');
                        }
                    } else {
                        $this->errors[] = $this->l('Some error occurred in transfer refund process. Please try again.');
                    }
                }
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
        if (count($this->errors)) {
            $this->display = 'view';
        }
        parent::postProcess();
    }
}
