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
class PdfInvoiceController extends PdfInvoiceControllerCore
{
    public function display()
    {
        if (Module::IsEnabled('mpsellerinvoice') && Module::IsEnabled('marketplace')) {
            //if Send seller invoice to customers is enabled from config
            if (Configuration::get('MP_SELLER_INVOICE_ACTIVE') == 1) {
                include_once _PS_MODULE_DIR_.'mpsellerinvoice/mpsellerinvoice.php';
                $order = new Order((int) $this->order->id);
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
                parent::display();
            }
        } else {
            parent::display();
        }
    }
}