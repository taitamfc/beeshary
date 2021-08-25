<?php
/**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminMangopayRefundController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->identifier = 'id';
        $this->table = 'wk_mp_mangopay_transaction';
        $this->className = 'MangopayTransaction';
        parent::__construct();
        $this->_select = 'a.`transaction_id`, a.`order_reference` as temp_order_reference, a.`order_reference`,
        CONCAT(a.`credited_amount`, " ", a.`currency`) as total_paid, a.`date_add`';
        //if filter only
        $this->_where .= ' AND a.`payment_type` = \''.pSQL(MangopayTransaction::WK_PAYMENT_TYPE_CARD).'\'';
        if ($clientId = Configuration::get('WK_MP_MANGOPAY_CLIENTID')) {
            $this->_where .= 'AND a.`mgp_clientid` = \''.pSQL($clientId).'\'';
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
            'total_paid' => array(
                'title' => $this->l('Total'),
                'align' => 'center',
                'havingFilter' => true,
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'align' => 'center',
                'type' => 'date',
                'havingFilter' => true,
            ),
            'temp_order_reference' => array(
                'title' => $this->l('Details'),
                'align' => 'center',
                'search' => false,
                'remove_onclick' => true,
                'callback' => 'orderDetail',
                'orderby' => false,
            ),
        );
    }

    public function orderDetail($orderReference)
    {
        if ($orderReference) {
            $objMgpTransfer = new MangopayTransferDetails();
            $orderId = $objMgpTransfer->getOrderIdByOrderReference($orderReference);
            $orderDetailLink = $this->context->link->getAdminlink('AdminOrders').'&id_order='.$orderId.'&vieworder';
            return '<span class="btn-group-action"><span class="btn-group">
                        <a target="_blank" class="btn btn-default" href="'.$orderDetailLink.'">
                        '.$this->l('View Order').'</a>
                    </span>
                </span>';
        }
    }

    public function initToolbar()
    {
        unset($this->toolbar_btn['new']);
    }

    public function renderList()
    {
        $this->addRowAction('view');
        return parent::renderList();
    }

    public function renderView()
    {
        $clientId = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
        $id = Tools::getValue('id');
        $objMgpTransaction = new MangopayTransaction($id);
        $orderReference = $objMgpTransaction->order_reference;
        if ($orderReference) {
            $objMgpTransfers = new MangopayTransferDetails();
            $allTransfersDetails = $objMgpTransfers->getTransferDetailsByOrderReference($orderReference);
            if ($allTransfersDetails) {
                foreach ($allTransfersDetails as &$transfer) {
                    if ($transfer['id_seller']) {
                        $seller = WkMpSeller::getSeller($transfer['id_seller'], $this->context->language->id);
                        if ($seller) {
                            $transfer['name'] = $seller['seller_firstname'].' '.$seller['seller_lastname'];
                            $transfer['email'] = $seller['business_email'];
                            $transfer['my_account_link'] = $this->context->link->getAdminLink('AdminSellerInfoDetail').
                            '&id_seller='.$transfer['id_seller'].'&viewwk_mp_seller';
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
            if ($transactionsDetails = $objMgpTransaction->getTransactionsDetailsByOrderReference(
                $orderReference
            )) {
                $objPayInRefund = new MangopayPayInRefund();
                $transactionsDetails['refunded_amt'] = (
                    $objPayInRefund->getRefundSumByPayIn(
                        $transactionsDetails['transaction_id']
                    )) * 100;
            }
            $this->context->smarty->assign('all_mangopay_transfers_details', $allTransfersDetails);
            $this->context->smarty->assign('transactionsDetails', $transactionsDetails);
        }
        $this->context->smarty->assign('mgp_client_id', $clientId);
        return parent::renderView();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('partial_mgp_payin_Refund')) {
            $this->mangopayPayInRefundProcess();
        } elseif (Tools::isSubmit('partial_mgp_transfer_Refund')) {
            $this->mangopayTransferRefundProcess();
        }
        parent::postProcess();
    }

    private function mangopayPayInRefundProcess()
    {
        $id =Tools::getValue('id');
        $amount = Tools::getValue('mgp_partial_amount');
        $mgpPayinId = Tools::getValue('mgp_payin_id');
        $orderReference = Tools::getValue('mgp_order_reference');
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
            $objMangopayMpService = new MangopayMpService();
            $params = array(
                'payin_id' => $mgpPayinId,
                'author_id' => $mgpUserId,
                'amount' => $amount,
                'currency' => $mgpCurrency
            );
            $mgpPayinRef = $objMangopayMpService->createMangopayPayInRefund($params);
            if ($mgpPayinRef['Status'] != 'FAILED') {
                $objPayinRefund = new MangopayPayInRefund();
                $objPayinRefund->payin_id = $mgpPayinId;
                $objPayinRefund->amount = $amount;
                $objPayinRefund->refund_id = $mgpPayinRef['Id'];
                $objPayinRefund->save();
                if (Configuration::get('WK_MP_MANGOPAY_MAIL_ADMIN_REFUND') == 1) {
                    $objMgpTransferDetail = new MangopayTransferDetails();
                    $mgpTransferDetail = $objMgpTransferDetail->getTransferDetailsByOrderReference($orderReference);
                    foreach ($mgpTransferDetail as $value) {
                        if ($value['id_seller']) {
                            MangopayTransferDetails::sendMailToSellerOnAdminRefund(
                                $value['id_seller'],
                                $orderReference,
                                2
                            );
                        }
                    }
                }
                Tools::redirectAdmin(
                    self::$currentIndex.'&id='.$id.'&viewwk_mp_mangopay_transaction&conf=3&token='.$this->token
                );
            } else {
                if (isset($mgpPayinRef['ResultMessage']) && $mgpPayinRef['ResultMessage']) {
                    $this->errors[] = $mgpPayinRef['ResultMessage'];
                } else {
                    $this->errors[] = $this->l('Some error occured while PayIn refund. Please try again.');
                }
            }
        }
    }

    private function mangopayTransferRefundProcess()
    {
        $id =Tools::getValue('id');
        $mgpTransferId = Tools::getValue('mgp_transfer_id');
        $orderReference = Tools::getValue('mgp_order_reference');
        $mgpUserId = Tools::getValue('mgp_transfer_author_id');

        $objMangopayTransaction = new MangopayTransaction();
        $objMangopayTransaction = $objMangopayTransaction->getTransactionsDetailsByOrderReference($orderReference);
        $mgpPayinId = $objMangopayTransaction['transaction_id'];

        $objMangopayTransfer = new MangopayTransferDetails();
        $mangopayTransferDetail = $objMangopayTransfer->getTransferDetailByTransferId($mgpTransferId);
        $amount = $mangopayTransferDetail['amount'];
        $mgpCurrency = $mangopayTransferDetail['currency'];
        $idSeller = $mangopayTransferDetail['id_seller'];

        if (!$mgpTransferId) {
            $this->errors[] = $this->l('Mangopay Transfer Id is missing for this transaction.');
        }
        if (!count($this->errors)) {
            $objMangopayMpService = new MangopayMpService();
            $params = array('transfer_id' => $mgpTransferId, 'author_id' => $mgpUserId);
            $mgpTransferRef = $objMangopayMpService->createMangopayTransferRefund($params);
            if ($mgpTransferRef['Status'] != 'FAILED') {
                $refundedBy = 'Admin';
                $update_transfer_details = (new MangopayTransferDetails())->updateRefundDetailsByTransferId(
                    $mgpTransferRef['InitialTransactionId'],
                    $mgpTransferRef['Id'],
                    $refundedBy
                );
                if ($update_transfer_details) {
                    if (Configuration::get('WK_MP_MANGOPAY_TRANSFER_TO') == 2) { //for transfer to card
                        $this->adminTransferToBuyerCard(
                            $mgpPayinId,
                            $mgpUserId,
                            $mgpCurrency,
                            $orderReference,
                            $amount,
                            $idSeller
                        );
                    } else {
                        if (Configuration::get('WK_MP_MANGOPAY_MAIL_ADMIN_REFUND') == 1) {
                            $objMgpTransferDetail = new MangopayTransferDetails();
                            $idSeller = $objMgpTransferDetail->getSellerIdByTransferIdAndOrderReference(
                                $mgpTransferId,
                                $orderReference
                            );
                            if ($idSeller) {
                                //for transfer refund from seller wallet
                                MangopayTransferDetails::sendMailToSellerOnAdminRefund($idSeller, $orderReference, 1);
                            }
                        }
                        Tools::redirectAdmin(
                            self::$currentIndex.'&id='.$id.'&viewwk_mp_mangopay_transaction&conf=3&token='.$this->token
                        );
                    }
                } else {
                    $this->errors[] = $this->l('Some error occurred while updating refund transfer details');
                }
            } else {
                if (isset($mgpTransferRef['ResultMessage']) && $mgpTransferRef['ResultMessage']) {
                    $this->errors[] = $mgpTransferRef['ResultMessage'];
                } else {
                    $this->errors[] = $this->l('Some error occured while Transfer refund. Please try again.');
                }
            }
        }
    }

    public function adminTransferToBuyerCard($mgpPayinId, $mgpUserId, $mgpCurrency, $orderReference, $amount, $idSeller)
    {
        $id =Tools::getValue('id');
        if (!$mgpPayinId) {
            $this->context->controller->errors[] = $this->l('Mangopay Pay In Id is missing for this transaction.');
        }
        if (!$mgpCurrency) {
            $this->context->controller->errors[] = $this->l('Mangopay Pay In currency is missing for this transaction.');
        }
        if (!$mgpUserId) {
            $this->context->controller->errors[] = $this->l('Mangopay Author Id is missing for this transaction.');
        }
        if (!count($this->context->controller->errors)) {
            $objMangopayMpService = new MangopayMpService();
            $params = array(
                'payin_id' => $mgpPayinId,
                'author_id' => $mgpUserId,
                'amount' => $amount,
                'currency' => $mgpCurrency
            );
            $mgpPayinRef = $objMangopayMpService->createMangopayPayInRefund($params);
            if ($mgpPayinRef['Status'] != 'FAILED') {
                $objPayinRefund = new MangopayPayInRefund();
                $objPayinRefund->payin_id = $mgpPayinId;
                $objPayinRefund->amount = $amount;
                $objPayinRefund->refund_id = $mgpPayinRef['Id'];
                $objPayinRefund->save();
                $objMangopayTransfer = new MangopayTransferDetails();
                $objMangopayTransfer->updatePayInRefundDetailsBySellerIdOrderReference($idSeller, $orderReference);
                if (Configuration::get('WK_MP_MANGOPAY_MAIL_ADMIN_REFUND') == 1) {
                    if ($idSeller) {
                        //for payin refund from seller wallet
                        MangopayTransferDetails::sendMailToSellerOnAdminRefund($idSeller, $orderReference, 2);
                    }
                }
                Tools::redirectAdmin(
                    self::$currentIndex.'&id='.$id.'&viewwk_mp_mangopay_transaction&conf=3&token='.$this->token
                );
            } else {
                if (isset($mgpPayinRef['ResultMessage']) && $mgpPayinRef['ResultMessage']) {
                    $this->context->smarty->assign('mgpRefundErr', $mgpPayinRef['ResultMessage']);
                } else {
                    $this->context->smarty->assign(
                        'mgpRefundErr',
                        $this->l('Some error occured while PayIn refund. Please try again.')
                    );
                }
            }
        }
    }
}
