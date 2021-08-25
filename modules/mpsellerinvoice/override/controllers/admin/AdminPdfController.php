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
class AdminPdfController extends AdminPdfControllerCore
{

    public function generateInvoicePDFByIdOrder($id_order)
    {
        if (Module::IsEnabled('mpsellerinvoice') && Module::IsEnabled('marketplace')) {
            include_once _PS_MODULE_DIR_.'mpsellerinvoice/mpsellerinvoice.php';
            $order = new Order((int)$id_order);
            if (!Validate::isLoadedObject($order)) {
                die(Tools::displayError('The order cannot be found within your database.'));
            }
            foreach ($order->getProducts() as $prod_detail) {
                $isBelongToSeller = WkMpSellerProduct::getSellerProductByPsIdProduct($prod_detail['product_id']);
                if ($isBelongToSeller) {
                    $seller_info = new WkMpSeller($isBelongToSeller['id_seller']);
                    $orderDetails[$isBelongToSeller['id_seller']] = $seller_info->seller_customer_id;
                } else {
                    $orderDetails['admin'] = 'admin';
                }
            }
            $create_invoice = new CreateInvoice();
            if (count($orderDetails) > 1){
                foreach ($orderDetails as $key => $detail) {
                    $attachmentPDF[$detail] = $create_invoice->createInvoice($order, $detail, false);
                }
                if ($attachmentPDF) {
                    $dir = _PS_MODULE_DIR_ . 'mpsellerinvoice/views/download_invoice/'.$this->context->customer->id;
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    $zipFileName = 'mpsellerinvoice/views/download_invoice/'.$this->context->customer->id.'/invoices.zip';
                    $zip = new ZipArchive();
                    $zip->open(_PS_MODULE_DIR_ . $zipFileName, ZipArchive::OVERWRITE | ZipArchive::CREATE);
                    $zip->unchangeAll();
                    foreach ($attachmentPDF as $detail) {
                        $zip->addFromString($detail['name'], $detail['content']);
                    }
                    $zip->close();
                    header("Content-type: application/zip");
                    header("Content-Disposition: attachment; filename=invoices.zip");
                    header("Pragma: no-cache");
                    header("Expires: 0");
                    readfile(_PS_MODULE_DIR_ . $zipFileName);
                }
            } else {
                foreach ($orderDetails as $key => $detail) {
                    $attachmentPDF[$detail] = $create_invoice->createInvoice($order, $detail, true);
                }
            }
        } else {
            parent::generateInvoicePDFByIdOrder($id_order);
        }
    }

    public function generateInvoicePDFByIdOrderInvoice($id_order_invoice)
    {
        if (Module::IsEnabled('mpsellerinvoice') && Module::IsEnabled('marketplace')) {
            include_once _PS_MODULE_DIR_.'mpsellerinvoice/mpsellerinvoice.php';
            $orderInvoice = new OrderInvoice((int)$id_order_invoice);
            $order = new Order((int)$orderInvoice->id_order);
            if (!Validate::isLoadedObject($order)) {
                die(Tools::displayError('The order cannot be found within your database.'));
            }
            foreach ($order->getProducts() as $prod_detail) {
                $isBelongToSeller = WkMpSellerProduct::getSellerProductByPsIdProduct($prod_detail['product_id']);
                if ($isBelongToSeller) {
                    $seller_info = new WkMpSeller($isBelongToSeller['id_seller']);
                    $orderDetails[$isBelongToSeller['id_seller']] = $seller_info->seller_customer_id;
                } else {
                    $orderDetails['admin'] = 'admin';
                }
            }
            $create_invoice = new CreateInvoice();
            if (count($orderDetails) > 1){
                foreach ($orderDetails as $key => $detail) {
                    $attachmentPDF[$detail] = $create_invoice->createInvoice($order, $detail, false);
                }
                if ($attachmentPDF) {
                    $dir = _PS_MODULE_DIR_ . 'mpsellerinvoice/views/download_invoice/'.$this->context->customer->id;
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    $zipFileName = 'mpsellerinvoice/views/download_invoice/'.$this->context->customer->id.'/invoices.zip';
                    $zip = new ZipArchive();
                    $zip->open(_PS_MODULE_DIR_ . $zipFileName, ZipArchive::OVERWRITE | ZipArchive::CREATE);
                    $zip->unchangeAll();
                    foreach ($attachmentPDF as $detail) {
                        $zip->addFromString($detail['name'], $detail['content']);
                    }
                    $zip->close();
                    header("Content-type: application/zip");
                    header("Content-Disposition: attachment; filename=invoices.zip");
                    header("Pragma: no-cache");
                    header("Expires: 0");
                    readfile(_PS_MODULE_DIR_ . $zipFileName);
                }
            } else {
                foreach ($orderDetails as $key => $detail) {
                    $attachmentPDF[$detail] = $create_invoice->createInvoice($order, $detail, true);
                }
            }
        } else {
            parent::generateInvoicePDFByIdOrderInvoice($id_order_invoice);
        }
    }

}