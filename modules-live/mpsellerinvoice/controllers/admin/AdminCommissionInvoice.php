<?php
/*
* 2010-2019 Webkul
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
*  @author Webkul IN <support@webkul.com>
*  @copyright  2010-2019 Webkul IN
*/

class AdminCommissionInvoiceController extends ModuleAdminController
{
    public function __construct()
    {
        $this->identifier = 'invoice_number';
        parent::__construct();
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->list_no_link = true;
        $this->table = 'mp_admin_commission_invoice_history';

        $this->_select = '
            a.`invoice_number` as temp_invoice_number,
            a.`from` as from_date,
            a.`to` as to_date,
            seller.`shop_name_unique` as shop_name_unique';

        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller` seller on (a.`id_seller` = seller.`id_seller`)';
        $this->invoice_based = array(
            '1' => $this->l('Each order'),
            '2' => $this->l('Time period'),
            '3' => $this->l('Threshold Amount Value'),
        );
        $this->fields_list = array(
            'invoice_number' => array(
                'title' => $this->l('Id'),
                'align' => 'center',
            ),
            'id_seller' => array(
                'title' => $this->l('Seller Id'),
                'align' => 'center',
                'havingFilter' => true
            ),
            'shop_name_unique' => array(
                'title' => $this->l('Seller Unique Shop'),
                'align' => 'center',
                'havingFilter' => true
            ),
            'invoice_based' => array(
                'title' => $this->l('Invoice Based On'),
                'align' => 'center',
                'callback' => 'checkInvoiceBased',
                'havingFilter' => true,
                'type' => 'select',
                'list' => $this->invoice_based,
                'filter_key' => 'a!invoice_based',
                'filter_type' => 'int',
            ),
            'orders' => array(
                'title' => $this->l('Order Id'),
                'align' => 'center',
                'callback' => 'checkOrder',
                'havingFilter' => true
            ),
            'from' => array(
                'title' => $this->l('From'),
                'align' => 'center',
                'type' => 'date',
                'filter_key' => 'from_date'
            ),
            'to' => array(
                'title' => $this->l('To'),
                'align' => 'center',
                'type' => 'date',
                'filter_key' => 'to_date'
            ),
            'last_notification' => array(
                'title' => $this->l('Last Notified'),
                'align' => 'center',
                'type' => 'date',
            ),
            'temp_invoice_number' => array(
                'align' => 'center',
                'title' => $this->l('Action'),
                'callback' => 'downloadLink',
                'search' => false,
            )
        );

        $this->addRowAction('SendInvoice');
        $this->_conf['10'] = $this->l('Commission invoice sent successfully');
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function initProcess()
    {
        parent::initProcess();
        if ($action = Tools::getValue('submitAction')) {
            $this->action = $action;
        }

    }

    public function processDownloadAdminInvoice()
    {
        if ($invoiceNumber = Tools::getValue('invoice_number')) {
            $result = MpCommissionInvoiceHistory::getSellerInvoiceHistoryByInvoiceNumber($invoiceNumber);
            if ($result) {
                if ($result['invoice_based'] == 1) {
                    $order = new Order((int) $result['orders']);
                    $seller = new WkMpSeller($result['id_seller']);
                    $sellerCustomerId = $seller->seller_customer_id;

                    if (!Validate::isLoadedObject($order)) {
                        die(Tools::displayError('The order cannot be found within your database.'));
                    }

                    $create_commission_invoice = new CreateCommissionInvoice();
                    $create_commission_invoice->createInvoice($result['orders'], $sellerCustomerId, true, true, false, true);
                } elseif ($result['invoice_based'] == 2 || $result['invoice_based'] == 3) {
                    $seller = new WkMpSeller($result['id_seller']);
                    $sellerCustomerId = $seller->seller_customer_id;
                    $orders = explode(',', $result['orders']);
                    foreach ($orders as $idOrder) {
                        $order = new Order((int) $idOrder);
                        if (!Validate::isLoadedObject($order)) {
                            die(Tools::displayError('The order cannot be found within your database.'));
                        }
                    }
                    $create_commission_invoice = new CreateCommissionInvoice();
                    $create_commission_invoice->createInvoice(
                        $result['orders'],
                        $sellerCustomerId,
                        true,
                        true,
                        $result
                    );
                }
            }
        }
    }

    public function processSendAdminInvoice()
    {
        $idOrder = Tools::getValue('idOrder');

        $sellerCustomerId = Tools::getValue('sellerCustomerId');
        $order = new Order((int) $idOrder);
        if (!Validate::isLoadedObject($order)) {
            die(Tools::displayError('The order cannot be found within your database.'));
        }

        $create_commission_invoice = new CreateCommissionInvoice();
        $adminAttachmentPDF = $create_commission_invoice->createInvoice($idOrder, $sellerCustomerId, false, true, false, true, true);
        $create_invoice = new CreateInvoice();
        $seller = WkMpSeller::getSellerByCustomerId($sellerCustomerId, Configuration::get('PS_DEFAULT_LANG'));
        if ($create_invoice->sendInvoiceEmailToSeller($order, $adminAttachmentPDF, $seller)) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders').'&vieworder&conf=10&id_order='.(int)$idOrder);
        } else {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders').'&vieworder&conf=10&id_order='.(int)$idOrder);
        }
    }

    public function displaySendInvoiceLink($token = null, $id = null)
    {
        $this->context->smarty->assign(array(
            'invoice_number' => $id
        ));
        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'mpsellerinvoice/views/templates/hook/send_commission_invoice.tpl'
        );
    }

    public function downloadLink($val, $arr)
    {
        if ($val) {
            $seller = new WkMpSeller($arr['id_seller']);
            if ($seller) {
                if ($arr['invoice_based'] == 1) {
                    $this->context->smarty->assign(array(
                        'wk_link' => $this->context->link->getAdminLink('AdminCommissionInvoice').'&submitAction=downloadAdminInvoice&sellerCustomerId='.$seller->seller_customer_id.'&invoice_number='.$val
                    ));
                } else {
                    $this->context->smarty->assign(array(
                        'wk_link' => $this->context->link->getAdminLink('AdminCommissionInvoice').'&submitAction=downloadAdminInvoice&sellerCustomerId='.$seller->seller_customer_id.'&invoice_number='.$val
                    ));
                }
                return $this->context->smarty->fetch(
                    _PS_MODULE_DIR_.'mpsellerinvoice/views/templates/hook/download_commission_invoice.tpl'
                );
            }
        }
    }

    public function checkInvoiceBased($val)
    {
        if ($val == 1) {
            return $this->l('Each order');
        } elseif ($val == 2) {
            return $this->l('Time period');
        } elseif ($val == 3) {
            return $this->l('Threshold Amount Value');
        }
    }

    public function checkOrder($val, $arr)
    {
        if ($arr['invoice_based'] == 1) {
            return Tools::jsonDecode($val);
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submit_btn')) {
            $invoiceNumber = Tools::getValue('invoice_number');
            $email = Tools::getValue('wk_commission_email');
            if (!$invoiceNumber) {
                $this->errors[] = $this->l('Invoice number is missing');
            } elseif (!$email) {
                $this->errors[] = $this->l('Email address is missing');
            } elseif (!Validate::isEmail($email)) {
                $this->errors[] = $this->l('Email is not valid');
            } else {
                $result = MpCommissionInvoiceHistory::getSellerInvoiceHistoryByInvoiceNumber($invoiceNumber);
                if ($result) {
                    if ($result['invoice_based'] == 1) {
                        $order = new Order((int) $result['orders']);
                        $seller = new WkMpSeller($result['id_seller']);
                        $sellerCustomerId = $seller->seller_customer_id;

                        if (!Validate::isLoadedObject($order)) {
                            die(Tools::displayError('The order cannot be found within your database.'));
                        }

                        $create_commission_invoice = new CreateCommissionInvoice();
                        $adminAttachmentPDF = $create_commission_invoice->createInvoice($result['orders'], $sellerCustomerId, false, true, false, true);
                        $create_commission_invoice = new CreateCommissionInvoice();
                        if ($create_commission_invoice->sendCommissionInvoice(
                            $email,
                            $adminAttachmentPDF,
                            $seller
                        )) {
                            $history = MpCommissionInvoiceHistory::getSellerInvoiceHistoryByInvoiceNumber($invoiceNumber);
                            if ($history) {
                                $history = new MpCommissionInvoiceHistory($history['id']);
                                $history->last_notification = date('Y-m-d H:i:s');
                                $history->is_send_to_seller = 1; // Update to 1 to maintain commission invoice send to seller
                                $history->update();
                            }
                            Tools::redirectAdmin($this->context->link->getAdminLink('AdminCommissionInvoice').'&conf=10');
                        } else {
                            Tools::redirectAdmin($this->context->link->getAdminLink('AdminCommissionInvoice').'r&conf=10');
                        }
                    } elseif ($result['invoice_based'] == 2 || $result['invoice_based'] == 3) {
                        $seller = new WkMpSeller($result['id_seller']);
                        $sellerCustomerId = $seller->seller_customer_id;
                        $orders = explode(',', $result['orders']);
                        foreach ($orders as $idOrder) {
                            $order = new Order((int) $idOrder);
                            if (!Validate::isLoadedObject($order)) {
                                die(Tools::displayError('The order cannot be found within your database.'));
                            }
                        }
                        $create_commission_invoice = new CreateCommissionInvoice();
                        $adminAttachmentPDF = $create_commission_invoice->createInvoice(
                            $result['orders'],
                            $sellerCustomerId,
                            false,
                            true,
                            $result
                        );
                        if ($create_commission_invoice->sendCommissionInvoice(
                            $email,
                            $adminAttachmentPDF,
                            $seller
                        )) {
                            $history = MpCommissionInvoiceHistory::getSellerInvoiceHistoryByInvoiceNumber($invoiceNumber);
                            if ($history) {
                                $history = new MpCommissionInvoiceHistory($history['id']);
                                $history->last_notification = date('Y-m-d H:i:s');
                                $history->is_send_to_seller = 1; // Update to 1 to maintain commission invoice send to seller
                                $history->update();
                            }
                            Tools::redirectAdmin($this->context->link->getAdminLink('AdminCommissionInvoice').'&conf=10');
                        } else {
                            Tools::redirectAdmin($this->context->link->getAdminLink('AdminCommissionInvoice').'r&conf=10');
                        }
                    }
                }
            }
        }
        parent::postProcess();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme = false);
        $this->addJs(_MODULE_DIR_.'mpsellerinvoice/views/js/admincommissioninvoice.js');
    }

    public function processViewAdminInvoice()
    {
        $idOrder = Tools::getValue('idOrder');
        $sellerCustomerId = Tools::getValue('sellerCustomerId');
        $order = new Order((int) $idOrder);
        if (!Validate::isLoadedObject($order)) {
            die(Tools::displayError('The order cannot be found within your database.'));
        }
        $create_commission_invoice = new CreateCommissionInvoice();
        $create_commission_invoice->createInvoice($idOrder, $sellerCustomerId, true, true, false, true);
    }
}
