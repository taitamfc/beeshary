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

class MpSellerInvoicePdfDownloadModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $idSeller = Tools::getValue('id_seller');
        if (isset($this->context->customer->id)) {
            $seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if ($seller && $seller['active']) {
                if ($seller['id_seller'] !== $idSeller) {
                    die($this->module->l('The invoice was not found.', 'pdfdownload'));
                } else {
                    $this->seller = $seller;
                }
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function postProcess()
    {
        if (!(int) Configuration::get('PS_INVOICE')) {
            die($this->module->l('Invoices are disabled in this shop.', 'pdfdownload'));
        }

        $submitAction = Tools::getValue('submitAction');
        $id_order = (int) Tools::getValue('id_order');
        if ($id_order) {
            if (Validate::isUnsignedId($id_order)) {
                $order = new Order((int) $id_order);
                if (!Validate::isLoadedObject($order)) {
                    die($this->module->l('The invoice was not found.', 'pdfdownload'));
                }
                $objSellerOrderInvoiceRecord = new MpSellerOrderInvoiceRecord();
                if(!$objSellerOrderInvoiceRecord->getOrderInvoiceNumber($id_order, $this->seller['id_seller'])) {
                    die($this->module->l('The invoice was not found.', 'pdfdownload'));
                }
                $this->order = $order;
            }
        } elseif ($submitAction != 'downloadAdminCommissionInvoice') {
            die($this->module->l('Order Id not found!', 'pdfdownload'));
        }
    }

    public function display()
    {
        $send = Tools::getValue('send');
        $invoice = Tools::getValue('invoice');
        $id_customer = $this->context->customer->id;
        $create_invoice = new CreateInvoice();

        if ($id_customer && $invoice) {
            $create_invoice->createInvoice($this->order, $id_customer, true);
        } elseif ($id_customer && $send) {
            $adminAttachmentPDF = $create_invoice->createInvoice($this->order, $id_customer, false);
            if ($create_invoice->sendPaymentEmailToCustomer($this->order, $adminAttachmentPDF, true)) {
                $params = array(
                    'id_order' => $this->order->id,
                    'invoice_sent' => 1
                );
            } else {
                $params = array(
                    'id_order' => $this->order->id,
                    'invoice_sent' => 2
                );
            }
            Tools::redirect($this->context->link->getModuleLink('marketplace', 'mporderdetails', $params));
        } elseif ($id_customer && Tools::getValue('submitAction') == 'downloadCommissionInvoice') {
            $idOrder = Tools::getValue('idOrder');
            if ($idOrder) {
                $orderDetail = new WkMpSellerOrderDetail();
                if (!$orderDetail->getSellerProductFromOrder($idOrder, $this->context->customer->id)) {
                    die($this->module->l('No Record Found'));
                }
            }
            $order = new Order((int) $idOrder);
            if (!Validate::isLoadedObject($order)) {
                die($this->module->l('The order cannot be found within your database.'));
            }
            $create_commission_invoice = new CreateCommissionInvoice();
            $create_commission_invoice->createInvoice($idOrder, $id_customer, true, true, false, true);
        } elseif ($id_customer && Tools::getValue('submitAction') == 'downloadAdminCommissionInvoice') {
            if ($invoiceNumber = Tools::getValue('invoice_number')) {
                $result = MpCommissionInvoiceHistory::getSellerInvoiceHistoryByInvoiceNumber($invoiceNumber);
                if ($result && $result['id_seller'] == $this->seller['id_seller']) {
                    if ($result['invoice_based'] == 1) {
                        $order = new Order((int) $result['orders']);
                        $sellerCustomerId = $this->seller['seller_customer_id'];
                        if (!Validate::isLoadedObject($order)) {
                            die($this->module->l('The order cannot be found within your database.', 'pdfdownload'));
                        }
                        $create_commission_invoice = new CreateCommissionInvoice();
                        $create_commission_invoice->createInvoice($result['orders'], $sellerCustomerId, true, true, false, true);
                    } elseif ($result['invoice_based'] == 2 || $result['invoice_based'] == 3) {
                        $sellerCustomerId = $this->seller['seller_customer_id'];
                        $orders = explode(',', $result['orders']);
                        foreach ($orders as $idOrder) {
                            $order = new Order((int) $idOrder);
                            if (!Validate::isLoadedObject($order)) {
                                die($this->module->l('The order cannot be found within your database.', 'pdfdownload'));
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
                die('Hard Luck');
            }
        }
    }
}
