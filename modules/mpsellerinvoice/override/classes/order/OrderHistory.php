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
class OrderHistory extends OrderHistoryCore
{
    public function addWithemail($autodate = true, $template_vars = false, Context $context = null)
    {
        $order = new Order($this->id_order);

        if (!$this->add($autodate)) {
            return false;
        }
        if (Module::IsEnabled('mpsellerinvoice')) {
            if (!Context::getContext()->cookie->no_admin_product && !Tools::isSubmit('submitState')) {
                if (!$this->sendEmail($order, $template_vars)) {
                    return false;
                }
            }
        } else  {
            if (!$this->sendEmail($order, $template_vars)) {
                return false;
            }
        }
        return true;
    }

    public function sendEmail($order, $template_vars = false)
    {
        if (Module::IsEnabled('mpsellerinvoice') && Module::IsEnabled('marketplace')) {
            include_once _PS_MODULE_DIR_.'mpsellerinvoice/mpsellerinvoice.php';
            $result = Db::getInstance()->getRow('
                SELECT osl.`template`, c.`lastname`, c.`firstname`, osl.`name` AS osname, c.`email`, os.`module_name`, os.`id_order_state`, os.`pdf_invoice`, os.`pdf_delivery`
                FROM `' . _DB_PREFIX_ . 'order_history` oh
                    LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON oh.`id_order` = o.`id_order`
                    LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON o.`id_customer` = c.`id_customer`
                    LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON oh.`id_order_state` = os.`id_order_state`
                    LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = o.`id_lang`)
                WHERE oh.`id_order_history` = ' . (int) $this->id . ' AND os.`send_email` = 1');
            if (isset($result['template']) && Validate::isEmail($result['email'])) {
                ShopUrl::cacheMainDomainForShop($order->id_shop);

                $topic = $result['osname'];
                $carrierUrl = '';
                if (Validate::isLoadedObject($carrier = new Carrier((int) $order->id_carrier, $order->id_lang))) {
                    $carrierUrl = $carrier->url;
                }
                $data = array(
                    '{lastname}' => $result['lastname'],
                    '{firstname}' => $result['firstname'],
                    '{id_order}' => (int) $this->id_order,
                    '{order_name}' => $order->getUniqReference(),
                    '{followup}' => str_replace('@', $order->getWsShippingNumber(), $carrierUrl),
                    '{shipping_number}' => $order->getWsShippingNumber(),
                );

                if ($result['module_name']) {
                    $module = Module::getInstanceByName($result['module_name']);
                    if (Validate::isLoadedObject($module) && isset($module->extra_mail_vars) && is_array($module->extra_mail_vars)) {
                        $data = array_merge($data, $module->extra_mail_vars);
                    }
                }

                if (is_array($template_vars)) {
                    $data = array_merge($data, $template_vars);
                }

                $data['{total_paid}'] = Tools::displayPrice((float) $order->total_paid, new Currency((int) $order->id_currency), false);

                if (Validate::isLoadedObject($order)) {
                    // Attach invoice and / or delivery-slip if they exists and status is set to attach them
                    if (($result['pdf_invoice'] || $result['pdf_delivery'])) {
                        $context = Context::getContext();
                        $invoice = $order->getInvoicesCollection();
                        $file_attachement = array();
                        if ($result['pdf_invoice'] && (int) Configuration::get('PS_INVOICE') && $order->invoice_number) {
                            Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => $invoice));
                            $pdf = new PDF($invoice, PDF::TEMPLATE_INVOICE, $context->smarty);
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
                            $attachmentPDF = array();
                            foreach ($orderDetails as $key => $detail) {
                                $pdf = $create_invoice->createInvoice($order, $detail, false);
                                $attachmentPDF[$detail] = $pdf;
                            }
                            if ($attachmentPDF) {
                                $file_attachement = $attachmentPDF;
                            } else {
                                $file_attachement['invoice']['content'] = $pdf->render(false);
                                $file_attachement['invoice']['name'] = Configuration::get('PS_INVOICE_PREFIX', (int) $order->id_lang, null, $order->id_shop) . sprintf('%06d', $order->invoice_number) . '.pdf';
                            }
                            $file_attachement['invoice']['mime'] = 'application/pdf';
                        }
                        if ($result['pdf_delivery'] && $order->delivery_number) {
                            $pdf = new PDF($invoice, PDF::TEMPLATE_DELIVERY_SLIP, $context->smarty);
                            $file_attachement['delivery']['content'] = $pdf->render(false);
                            $file_attachement['delivery']['name'] = Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id, null, $order->id_shop) . sprintf('%06d', $order->delivery_number) . '.pdf';
                            $file_attachement['delivery']['mime'] = 'application/pdf';
                        }
                    } else {
                        $file_attachement = null;
                    }

                    if (!Mail::Send(
                        (int) $order->id_lang,
                        $result['template'],
                        $topic,
                        $data,
                        $result['email'],
                        $result['firstname'] . ' ' . $result['lastname'],
                        null,
                        null,
                        $file_attachement,
                        null,
                        _PS_MAIL_DIR_,
                        false,
                        (int) $order->id_shop
                    )) {
                        return false;
                    }
                }

                ShopUrl::resetMainDomainCache();
            }

            return true;
        } else {
            parent::sendEmail($order, $template_vars = false);
        }
    }
}
