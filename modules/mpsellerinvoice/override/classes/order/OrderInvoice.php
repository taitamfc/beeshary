<?php
/*
* 2010-2016 Webkul
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
*  @copyright  2010-2016 Webkul IN
*/
class OrderInvoice extends OrderInvoiceCore
{
    public function getInvoiceNumberFormatted($id_lang, $id_shop = null)
    {
        if (Module::IsEnabled('mpsellerinvoice') && Module::IsEnabled('marketplace')) {
            if (Configuration::get('MP_SELLER_INVOICE_ACTIVE') == 1) {
                include_once _PS_MODULE_DIR_.'mpsellerinvoice/mpsellerinvoice.php';
                $order = new Order((int)$this->id_order);
                $id_lang = $id_lang;
                $id_shop = (int) $id_shop;
                $format = '%1$s%2$06d';
                foreach ($order->getProducts() as $prod_detail) {
                    $isBelongToSeller = WkMpSellerProduct::getSellerProductByPsIdProduct($prod_detail['product_id']);
                    $id_order_invoice = '';
                    if ($isBelongToSeller) {
                        $mp_invoice = new MpSellerOrderInvoiceRecord();
                        $id_order_invoice = $mp_invoice->getOrderInvoiceNumber($this->id_order, $isBelongToSeller['id_seller']);
                    }
                }
                if ($id_order_invoice) {
                    return sprintf(
                        $format,
                        Configuration::get('PS_INVOICE_PREFIX', $id_lang),
                        $id_order_invoice['invoice_number'],
                        date('Y', strtotime($this->date_add))
                    );
                } else {
                    return parent::getInvoiceNumberFormatted($id_lang, $id_shop);
                }
            } else {
                return parent::getInvoiceNumberFormatted($id_lang, $id_shop);
            }
        } else {
            return parent::getInvoiceNumberFormatted($id_lang, $id_shop);
        }
    }
}