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

class CreateCommissionInvoice
{
    public function __construct()
    {
        $this->context = Context::getContext();
    }

    public function createInvoice(
        $orders,
        $id_seller_customer,
        $download = false,
        $admin = false,
        $invoiceHistory = false,
        $eachOrder = false,
        $isSendToSeller = false
    ) {
        $orientation = 'P';
        $file_attachement = array();

        $this->pdf_renderer = new PDFGenerator((bool) Configuration::get('PS_PDF_USE_CACHE'), $orientation);
        $this->context->smarty->escape_html = false;
        $objAdminInvoice = new AdminInvoice();
        $objAdminInvoice->createOrderInvoiceData($orders, $id_seller_customer, $admin, $invoiceHistory);

        $this->pdf_renderer->setFontForLang(Context::getContext()->language->iso_code);
        $this->pdf_renderer->createHeader($objAdminInvoice->getHeader($isSendToSeller));
        $this->pdf_renderer->createFooter($objAdminInvoice->getFooter());
        $this->pdf_renderer->createContent($objAdminInvoice->getContent($eachOrder));
        $this->pdf_renderer->writePage();
        $render = true;

        if ($render) {
            // clean the output buffer
            if (ob_get_level() && ob_get_length() > 0) {
                ob_clean();
            }
            $file_attachement['content'] = $this->pdf_renderer->render($objAdminInvoice->getFilename(), $download);
            $file_attachement['name'] = $objAdminInvoice->getFilename();
            $file_attachement['invoice']['mime'] = 'application/pdf';
            $file_attachement['mime'] = 'application/pdf';
        } else {
            $file_attachement = null;
        }
        unset($obj_seller);

        return $file_attachement;
    }

    public function orderDetailsInformation($order, $idOrderState, $download = false)
    {
        if (!$download && $idOrderState != Configuration::get('PS_OS_ERROR') && $idOrderState != Configuration::get('PS_OS_CANCELED')) {
            $virtualProduct = true;
            $productVarTplList = array();
            $sellerTotalProducts = 0;
            $sellerTotalProductswt = 0;
            foreach ($order->getProducts() as $key => $product) {
                $price = Product::getPriceStatic((int) $product['product_id'], false, ($product['product_attribute_id'] ? (int) $product['product_attribute_id'] : null), 6, null, false, true, $product['product_quantity'], false, (int) $order->id_customer, (int) $order->id_cart, (int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
                $pricewt = Product::getPriceStatic((int) $product['product_id'], true, ($product['product_attribute_id'] ? (int) $product['product_attribute_id'] : null), 2, null, false, true, $product['product_quantity'], false, (int) $order->id_customer, (int) $order->id_cart, (int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
                $sellerTotalProducts += $price;
                $sellerTotalProductswt += $pricewt;
                $productPrice = Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, 2) : $pricewt;
                $productVarTpl = array(
                    'reference' => $product['reference'],
                    'name' => $product['product_name'].(isset($product['attributes']) ? ' - '.$product['attributes'] : ''),
                    'unit_price' => Tools::displayPrice($productPrice, $this->context->currency, false),
                    'price' => Tools::displayPrice($productPrice * $product['product_quantity'], $this->context->currency, false),
                    'quantity' => $product['product_quantity'],
                    'customization' => array(),
                );
                $customizedDatas = Product::getAllCustomizedDatas((int) $order->id_cart);
                if (isset($customizedDatas[$product['product_id']][$product['product_attribute_id']])) {
                    $productVarTpl['customization'] = array();
                    foreach ($customizedDatas[$product['product_id']][$product['product_attribute_id']][$order->id_address_delivery] as $customization) {
                        $customizationText = '';
                        if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
                            foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {
                                $customizationText .= $text['product_name'].': '.$text['value'].'<br />';
                            }
                        }
                        if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {
                            $customizationText .= sprintf(Tools::displayError('%d image(s)'), count($customization['datas'][Product::CUSTOMIZE_FILE])).'<br />';
                        }
                        $customizationQuantity = (int) $product['customization_quantity'];
                        $productVarTpl['customization'][] = array(
                            'customization_text' => $customizationText,
                            'customization_quantity' => $customizationQuantity,
                            'quantity' => Tools::displayPrice($customizationQuantity * $productPrice, $this->context->currency, false),
                        );
                    }
                }
                $productVarTplList[] = $productVarTpl;
                // Check if is not a virutal product for the displaying of shipping
                if (!$product['is_virtual']) {
                    $virtualProduct &= false;
                }
            } // end foreach ($products)
            $productListTxt = '';
            $productListHtml = '';
            if (count($productVarTplList) > 0) {
                $productListTxt = $this->getEmailTemplateContent('order_conf_product_list.txt', Mail::TYPE_TEXT, $productVarTplList);
                $productListHtml = $this->getEmailTemplateContent('order_conf_product_list.tpl', Mail::TYPE_HTML, $productVarTplList);
            }
            $cartRulesList = array();
            $total_reduction_value_ti = 0;
            $total_reduction_value_tex = 0;
            $cart_rules = $this->context->cart->getCartRules();
            foreach ($cart_rules as $cart_rule) {
                $package = array('id_carrier' => $order->id_carrier, 'id_address' => $order->id_address_delivery, 'products' => $order->product_list);
                $values = array(
                    'tax_incl' => $cart_rule['obj']->getContextualValue(true, $this->context, CartRule::FILTER_ACTION_ALL_NOCAP, $package),
                    'tax_excl' => $cart_rule['obj']->getContextualValue(false, $this->context, CartRule::FILTER_ACTION_ALL_NOCAP, $package),
                );
                // If the reduction is not applicable to this order, then continue with the next one
                if (!$values['tax_excl']) {
                    continue;
                }

                // IF
                //  This is not multi-shipping
                //  The value of the voucher is greater than the total of the order
                //  Partial use is allowed
                //  This is an "amount" reduction, not a reduction in % or a gift
                // THEN
                //  The voucher is cloned with a new value corresponding to the remainder
                if (count($order_list) == 1 && $values['tax_incl'] > ($order->total_products_wt - $total_reduction_value_ti) && $cart_rule['obj']->partial_use == 1 && $cart_rule['obj']->reduction_amount > 0) {
                    // Create a new voucher from the original
                    $voucher = new CartRule((int) $cart_rule['obj']->id); // We need to instantiate the CartRule without lang parameter to allow saving it
                    unset($voucher->id);
                    // Set a new voucher code
                    $voucher->code = empty($voucher->code) ? substr(md5($order->id.'-'.$order->id_customer.'-'.$cart_rule['obj']->id), 0, 16) : $voucher->code.'-2';
                    if (preg_match('/\-([0-9]{1,2})\-([0-9]{1,2})$/', $voucher->code, $matches) && $matches[1] == $matches[2]) {
                        $voucher->code = preg_replace('/'.$matches[0].'$/', '-'.(intval($matches[1]) + 1), $voucher->code);
                    }
                    // Set the new voucher value
                    if ($voucher->reduction_tax) {
                        $voucher->reduction_amount = ($total_reduction_value_ti + $values['tax_incl']) - $order->total_products_wt;
                        // Add total shipping amout only if reduction amount > total shipping
                        if ($voucher->free_shipping == 1 && $voucher->reduction_amount >= $order->total_shipping_tax_incl) {
                            $voucher->reduction_amount -= $order->total_shipping_tax_incl;
                        }
                    } else {
                        $voucher->reduction_amount = ($total_reduction_value_tex + $values['tax_excl']) - $order->total_products;
                        // Add total shipping amout only if reduction amount > total shipping
                        if ($voucher->free_shipping == 1 && $voucher->reduction_amount >= $order->total_shipping_tax_excl) {
                            $voucher->reduction_amount -= $order->total_shipping_tax_excl;
                        }
                    }
                    if ($voucher->reduction_amount <= 0) {
                        continue;
                    }
                    if ($this->context->customer->isGuest()) {
                        $voucher->id_customer = 0;
                    } else {
                        $voucher->id_customer = $order->id_customer;
                    }
                    $voucher->quantity = 1;
                    $voucher->reduction_currency = $order->id_currency;
                    $voucher->quantity_per_user = 1;
                    $voucher->free_shipping = 0;
                    $voucher->add();
                    $values['tax_incl'] = $order->total_products_wt - $total_reduction_value_ti;
                    $values['tax_excl'] = $order->total_products - $total_reduction_value_tex;
                }
                $total_reduction_value_ti += $values['tax_incl'];
                $total_reduction_value_tex += $values['tax_excl'];
                $order->addCartRule($cart_rule['obj']->id, $cart_rule['obj']->name, $values, 0, $cart_rule['obj']->free_shipping);
                if ($idOrderState != Configuration::get('PS_OS_ERROR') && $idOrderState != Configuration::get('PS_OS_CANCELED') && !in_array($cart_rule['obj']->id, $cart_rule_used)) {
                    $cart_rule_used[] = $cart_rule['obj']->id;
                    // Create a new instance of Cart Rule without id_lang, in order to update its quantity
                    $cart_rule_to_update = new CartRule((int) $cart_rule['obj']->id);
                    $cart_rule_to_update->quantity = max(0, $cart_rule_to_update->quantity - 1);
                    $cart_rule_to_update->update();
                }
                $cartRulesList[] = array(
                    'voucher_name' => $cart_rule['obj']->name,
                    'voucher_reduction' => ($values['tax_incl'] != 0.00 ? '-' : '').Tools::displayPrice($values['tax_incl'], $this->context->currency, false),
                );
            }
            $cartRulesListTxt = '';
            $cartRulesListHtml = '';
            if (count($cartRulesList) > 0) {
                $cartRulesListTxt = $this->getEmailTemplateContent('order_conf_cart_rules.txt', Mail::TYPE_TEXT, $cartRulesList);
                $cartRulesListHtml = $this->getEmailTemplateContent('order_conf_cart_rules.tpl', Mail::TYPE_HTML, $cartRulesList);
            }
            $carrier = new Carrier((int) $order->id_carrier, $this->context->language->id);
            $invoice = new Address((int) $order->id_address_invoice);
            $delivery = new Address((int) $order->id_address_delivery);
            $deliveryState = $delivery->id_state ? new State((int) $delivery->id_state) : false;
            $invoiceState = $invoice->id_state ? new State((int) $invoice->id_state) : false;
            $data = array(
                '{firstname}' => $this->context->customer->firstname,
                '{lastname}' => $this->context->customer->lastname,
                '{email}' => $this->context->customer->email,
                '{delivery_block_txt}' => $this->getFormatedAddress($delivery, "\n"),
                '{invoice_block_txt}' => $this->getFormatedAddress($invoice, "\n"),
                '{delivery_block_html}' => $this->getFormatedAddress($delivery, '<br />', array(
                    'firstname' => '<span style="font-weight:bold;">%s</span>',
                    'lastname' => '<span style="font-weight:bold;">%s</span>',
                    )),
                '{invoice_block_html}' => $this->getFormatedAddress($invoice, '<br />', array(
                    'firstname' => '<span style="font-weight:bold;">%s</span>',
                    'lastname' => '<span style="font-weight:bold;">%s</span>',
                    )),
                '{delivery_company}' => $delivery->company,
                '{delivery_firstname}' => $delivery->firstname,
                '{delivery_lastname}' => $delivery->lastname,
                '{delivery_address1}' => $delivery->address1,
                '{delivery_address2}' => $delivery->address2,
                '{delivery_city}' => $delivery->city,
                '{delivery_postal_code}' => $delivery->postcode,
                '{delivery_country}' => $delivery->country,
                '{delivery_state}' => $delivery->id_state ? $deliveryState->name : '',
                '{delivery_phone}' => ($delivery->phone) ? $delivery->phone : $delivery->phone_mobile,
                '{delivery_other}' => $delivery->other,
                '{invoice_company}' => $invoice->company,
                '{invoice_vat_number}' => $invoice->vat_number,
                '{invoice_firstname}' => $invoice->firstname,
                '{invoice_lastname}' => $invoice->lastname,
                '{invoice_address2}' => $invoice->address2,
                '{invoice_address1}' => $invoice->address1,
                '{invoice_city}' => $invoice->city,
                '{invoice_postal_code}' => $invoice->postcode,
                '{invoice_country}' => $invoice->country,
                '{invoice_state}' => $invoice->id_state ? $invoiceState->name : '',
                '{invoice_phone}' => ($invoice->phone) ? $invoice->phone : $invoice->phone_mobile,
                '{invoice_other}' => $invoice->other,
                '{order_name}' => $order->getUniqReference(),
                '{date}' => Tools::displayDate(date('Y-m-d H:i:s'), null, 1),
                '{carrier}' => ($virtualProduct || !isset($carrier->name)) ? 'No carrier' : $carrier->name,
                '{payment}' => Tools::substr($order->payment, 0, 32),
                '{products}' => $productListHtml,
                '{products_txt}' => $productListTxt,
                '{discounts}' => $cartRulesListHtml,
                '{discounts_txt}' => $cartRulesListTxt,
                '{total_paid}' => Tools::displayPrice($order->total_paid, $this->context->currency, false),
                '{total_products}' => Tools::displayPrice(Product::getTaxCalculationMethod() == PS_TAX_EXC ? $sellerTotalProducts : $sellerTotalProductswt, $this->context->currency, false),
                '{total_discounts}' => Tools::displayPrice($order->total_discounts, $this->context->currency, false),
                '{total_shipping}' => Tools::displayPrice($order->total_shipping, $this->context->currency, false),
                '{total_wrapping}' => Tools::displayPrice($order->total_wrapping, $this->context->currency, false),
                '{total_tax_paid}' => Tools::displayPrice(($order->total_products_wt - $order->total_products) + ($order->total_shipping_tax_incl - $order->total_shipping_tax_excl), $this->context->currency, false),
                );
            return $data;
        }
    }

    public function sendPaymentEmailToCustomer($order, $file_attachement, $admin = false)
    {
        $template_path = _PS_MODULE_DIR_.'mpsellerinvoice/mails/';
        $customer = new Customer((int) $order->id_customer);
        $data = array(
            '{lastname}' => $customer->lastname,
            '{firstname}' => $customer->firstname,
            '{id_order}' => (int) $order->id,
            '{order_name}' => $order->getUniqReference(),
        );

        $data['{total_paid}'] = Tools::displayPrice((float) $order->total_paid, new Currency((int) $order->id_currency), false);
        if ($admin) {
            $obj_emp = new Employee(1);
            $mail = $obj_emp->email;
        } else {
            $mail = $customer->email;
        }

        return Mail::Send(
            (int) $order->id_lang,
            'seller_payment',
            Mail::l('Payment Accepted', (int) $order->id_lang),
            $data,
            $mail,
            $customer->firstname.' '.$customer->lastname,
            null,
            null,
            $file_attachement,
            null,
            $template_path,
            false,
            (int) $order->id_shop
        );
    }

    public function sendInvoiceEmailToSeller($order, $file_attachement, $seller)
    {
        $data = array(
            '{lastname}' => $seller['seller_lastname'],
            '{firstname}' => $seller['seller_firstname'],
            '{id_order}' => (int) $order->id,
            '{order_name}' => $order->getUniqReference(),
        );

        $super_admins = Employee::getEmployeesByProfile(_PS_ADMIN_PROFILE_);
        foreach ($super_admins as $super_admin) {
            $employee = new Employee((int) $super_admin['id_employee']);
            if (Validate::isLoadedObject($employee)) {
                $adminEmail = $employee->email;
                break;
            }
        }
		return true;
        return Mail::Send(
            (int) $seller['default_lang'],
            'admin_commission',
            Mail::l('Admin Commission', (int) $seller['default_lang']),
            $data,
            $seller['business_email'],
            $seller['seller_firstname'].' '.$seller['seller_lastname'],
            null,
            null,
            $file_attachement,
            null,
            _PS_MODULE_DIR_.'mpsellerinvoice/mails/',
            false,
            (int) $order->id_shop
        );
    }

    public function sendCommissionInvoice($adminEmail, $file_attachement, $seller = null)
    {
        $data = array();
        if ($seller) {
            $data = array(
                '{lastname}' => $seller->seller_lastname,
                '{firstname}' => $seller->seller_firstname,
            );
        }
		return true;
        return Mail::Send(
            (int) Configuration::get('PS_LANG_DEFAULT'),
            'admin_commission_invoice',
            Mail::l('Admin Commission Invoice', (int) Configuration::get('PS_LANG_DEFAULT')),
            $data,
            $adminEmail,
            'Admin',
            null,
            null,
            $file_attachement,
            null,
            _PS_MODULE_DIR_.'mpsellerinvoice/mails/',
            false,
            (int) Context::getContext()->shop->id
        );
    }

    public function getFormatedAddress(Address $the_address, $line_sep, $fields_style = array())
    {
        return AddressFormat::generateAddress($the_address, array('avoid' => array()), $line_sep, ' ', $fields_style);
    }

    public function getSellerInfo($id_seller_customer)
    {
        $seller_detail = WkMpSeller::getSellerDetailByCustomerId($id_seller_customer);
        if ($seller_detail) {
            return $seller_detail;
        }

        return false;
    }

    public function getSellerProductInfo($id_seller_customer, $id_product)
    {
        $seller_info = $this->getSellerInfo($id_seller_customer);
        if ($seller_info) {
            $details = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'marketplace_seller_product where `id_seller` = '.(int) $seller_info['id'].' AND `id_ps_product` = '.(int) $id_product);
            if ($details) {
                return $details;
            }

            return false;
        }

        return false;
    }

    public function getEmailTemplateContent($template_name, $mail_type, $var)
    {
        $email_configuration = Configuration::get('PS_MAIL_TYPE');
        if ($email_configuration != $mail_type && $email_configuration != Mail::TYPE_BOTH) {
            return '';
        }
        $theme_template_path = _PS_MODULE_DIR_.'mpsellerinvoice/mails'.DIRECTORY_SEPARATOR.$this->context->language->iso_code.DIRECTORY_SEPARATOR.$template_name;
        if (Tools::file_exists_cache($theme_template_path)) {
            $this->context->smarty->assign('list', $var);

            return $this->context->smarty->fetch($theme_template_path);
        }

        return '';
    }
}
