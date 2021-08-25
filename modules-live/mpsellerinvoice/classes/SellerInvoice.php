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

class SellerInvoice extends HTMLTemplate
{
    public $order;

    public function __construct(Order $order, $id_seller_customer)
    {
        $this->id_seller_customer = $id_seller_customer;
        $this->order = $order;
        $this->currency = new Currency($this->order->id_currency);
        $this->shop = new Shop((int) $this->order->id_shop);
        $this->context = Context::getContext();
        $this->date = Tools::displayDate($order->date_add);
        if ($id_seller_customer == 'admin') {
            $this->id_seller = 0;
            $this->obj_seller = '';
        } else {
            $this->obj_seller = $this->getSellerObject($id_seller_customer);
            $this->id_seller = $this->obj_seller->id;
            $this->sellerInvoiceConfig = $this->getSellerInvoiceConfig();
        }
    }

    /**
     * [getSellerObject -> creating seller object].
     *
     * @param [type] $id [prestashop customer id]
     *
     * @return [type] [description]
     */
    public function getSellerObject($id)
    {
        if ($sellerDetail = WkMpSeller::getSellerDetailByCustomerId($id)) {
            return $this->obj_seller = new WkMpSeller($sellerDetail['id_seller'], $this->context->language->id);
        }

        return false;
    }

    public function getSellerInvoiceConfig()
    {
        $objConfig = new MpSellerInvoiceConfig();
        $sellerConfig = $objConfig->getSellerInvoiceConfig($this->id_seller, $this->context->language->id);
        if ($sellerConfig) {
            return $sellerConfig;
        }

        return false;
    }

    /**
     * [getHeader -> setting PDF header].
     *
     * @return [type] [description]
     */
    public function getHeader($admin = false, $isCreateInvoice = 0)
    {
        //saving order id and invoice number in seller's table
        if ($this->id_seller_customer != 'admin' && $isCreateInvoice == 1) {
            $this->saveSellerInvoiceRecord();
        }
        //---------------------------------------------------
        $this->assignCommonHeaderDataForSeller($admin);
        $objModule = new MpSellerInvoice();
        $this->context->smarty->assign(array(
            'header' => $objModule->l('Facture'),
        ));


        return $this->context->smarty->fetch($this->getTemplate('sellerinvoice_header'));
    }

    /**
     * [getFooter -> setting PDF footer].
     *
     * @return [type] [description]
     */
    public function getFooter()
    {
        $this->assignCommonFooterDataForSeller();
        $objModule = new MpSellerInvoice();
        $this->context->smarty->assign(array(
            'footer' => $objModule->l('Seller Invoice'),
        ));

        return $this->context->smarty->fetch($this->getTemplate('sellerinvoice_footer'));
    }

    /**
     * [getContent -> calculating PDF content seller' product wise].
     *
     * @return [type] [description]
     */
    public function getContent()
    {
        $objModule = new MpSellerInvoice();
        $invoiceAddressPatternRules = Tools::jsonDecode(Configuration::get('PS_INVCE_INVOICE_ADDR_RULES'), true);
        $deliveryAddressPatternRules = Tools::jsonDecode(Configuration::get('PS_INVCE_DELIVERY_ADDR_RULES'), true);
        $invoiceAddress = new Address((int) $this->order->id_address_invoice);
        $formattedInvoiceAddress = AddressFormat::generateAddress($invoiceAddress, $invoiceAddressPatternRules, '<br />', ' ');
        $deliveryAddress = null;
        $formattedDeliveryAddress = '';
        $deliveryAddress = new Address((int) $this->order->id_address_delivery);
        $formattedDeliveryAddress = AddressFormat::generateAddress($deliveryAddress, $deliveryAddressPatternRules, '<br />', ' ');
        $customer = new Customer((int) $this->order->id_customer);
        $orderDetails = $this->order->getProducts();

        $sellerTotalProducts = 0;
        // customize orderDetails to maintain tax
        foreach ($orderDetails as $key => $row) {
            // Add information for virtual product
            if ($orderDetails[$key]['download_hash'] && !empty($row['download_hash'])) {
                $orderDetails[$key]['filename'] = ProductDownload::getFilenameFromIdProduct((int) $row['product_id']);
                // Get the display filename
                $orderDetails[$key]['display_filename'] = ProductDownload::getFilenameFromFilename($row['filename']);
            }
            $orderDetails[$key]['id_address_delivery'] = $this->order->id_address_delivery;
            /* Ecotax */
            $roundMode = $this->order->round_mode;
            $orderDetails[$key]['ecotax_tax_excl'] = $row['ecotax']; // alias for coherence
            $orderDetails[$key]['ecotax_tax_incl'] = $row['ecotax'] * (100 + $row['ecotax_tax_rate']) / 100;
            $row['ecotax_tax_incl'] = $row['ecotax'] * (100 + $row['ecotax_tax_rate']) / 100;
            $row['ecotax_tax_excl'] = $row['ecotax'];
            $orderDetails[$key]['ecotax_tax'] = $row['ecotax_tax_incl'] - $row['ecotax_tax_excl'];
            if ($roundMode == Order::ROUND_ITEM) {
                $row['ecotax_tax_incl'] = Tools::ps_round($row['ecotax_tax_incl'], _PS_PRICE_COMPUTE_PRECISION_, $roundMode);
            }
            $orderDetails[$key]['total_ecotax_tax_excl'] = $row['ecotax_tax_excl'] * $row['product_quantity'];
            $orderDetails[$key]['total_ecotax_tax_incl'] = $row['ecotax_tax_incl'] * $row['product_quantity'];
            $row['total_ecotax_tax_incl'] = $row['ecotax_tax_incl'] * $row['product_quantity'];
            $row['total_ecotax_tax_excl'] = $row['ecotax_tax_excl'] * $row['product_quantity'];
            $orderDetails[$key]['total_ecotax_tax'] = $row['total_ecotax_tax_incl'] - $row['total_ecotax_tax_excl'];
            $row['total_ecotax_tax'] = $row['total_ecotax_tax_incl'] - $row['total_ecotax_tax_excl'];
            $row['ecotax_tax'] = $row['ecotax_tax_incl'] - $row['ecotax_tax_excl'];
            foreach (array(
                'ecotax_tax_excl',
                'ecotax_tax_incl',
                'ecotax_tax',
                'total_ecotax_tax_excl',
                'total_ecotax_tax_incl',
                'total_ecotax_tax',
            ) as $ecotaxfield) {
                $orderDetails[$key][$ecotaxfield] = Tools::ps_round($row[$ecotaxfield], _PS_PRICE_COMPUTE_PRECISION_, $roundMode);
            }
            // Aliases
            $orderDetails[$key]['unit_price_tax_excl_including_ecotax'] = $row['unit_price_tax_excl'];
            $orderDetails[$key]['unit_price_tax_incl_including_ecotax'] = $row['unit_price_tax_incl'];
            $orderDetails[$key]['total_price_tax_excl_including_ecotax'] = $row['total_price_tax_excl'];
            $orderDetails[$key]['total_price_tax_incl_including_ecotax'] = $row['total_price_tax_incl'];
        }

        $hasDiscount = false;
        foreach ($orderDetails as $id => &$orderDetail) {
            if ($this->id_seller == 0) {
                $isBelongToSeller = $this->getSellerProductInfoByProductId($orderDetail['product_id']);
                //sending admin invoice then check config if seller invoice enabled and product is seller product then unset otherwise send seller product in admin invoice bescause seller invoice is disabled
                if ($isBelongToSeller && Configuration::get('MP_SELLER_INVOICE_ACTIVE') == 1) {
                    unset($orderDetails[$id]);
                    continue;
                }
            } else {
                $isBelongToSeller = $this->getSellerProductInfo($orderDetail['product_id']);
                if (!$isBelongToSeller) {
                    unset($orderDetails[$id]);
                    continue;
                }
            }
            // Find out if column 'price before discount' is required
            if ($orderDetail['reduction_amount_tax_excl'] > 0) {
                $hasDiscount = true;
                $orderDetail['unit_price_tax_excl_before_specific_price'] = $orderDetail['unit_price_tax_excl_including_ecotax'] + $orderDetail['reduction_amount_tax_excl'];
            } elseif ($orderDetail['reduction_percent'] > 0) {
                $hasDiscount = true;
                $orderDetail['unit_price_tax_excl_before_specific_price'] = (100 * $orderDetail['unit_price_tax_excl_including_ecotax']) / (100 - $orderDetail['reduction_percent']);
            }
            if (isset($orderDetail['unit_price_tax_excl_before_specific_price'])) {
                $orderDetail['unit_price_tax_excl_before_specific_price'] = Tools::displayPrice($orderDetail['unit_price_tax_excl_before_specific_price'], $this->currency);
            }
            // Set tax_code
            $taxes = OrderDetail::getTaxListStatic($id);
            $taxTemp = array();
            foreach ($taxes as $tax) {
                $obj = new Tax($tax['id_tax']);
                $taxTemp[] = sprintf($objModule->l('%1$s%2$s%%'), ($obj->rate + 0), '&nbsp;');
            }
            $sellerTotalProducts += $orderDetail['total_price_tax_excl'];
            $orderDetail['order_detail_tax'] = $taxes;
            $orderDetail['order_detail_tax_label'] = implode(', ', $taxTemp);
            $sellerOrder = $this->getProductCommissionDetail($orderDetail['product_id'], $orderDetail['product_attribute_id']);
            if ($sellerOrder) {
                $orderDetails[$id]['admin_commission'] = Tools::displayPrice($sellerOrder['admin_commission'], $this->currency);
                $orderDetails[$id]['admin_commission_tax'] = Tools::displayPrice($sellerOrder['admin_tax'], $this->currency);
                $orderDetails[$id]['seller_amount'] = Tools::displayPrice($sellerOrder['seller_amount'], $this->currency);
                $orderDetails[$id]['seller_amount_tax'] = Tools::displayPrice($sellerOrder['seller_tax'], $this->currency);
                $orderDetails[$id]['order_total_commission'] = Tools::displayPrice($sellerOrder['admin_commission'] + $sellerOrder['admin_tax'], $this->currency);
            }
            unset($sellerOrder);
        }

        unset($taxTemp);
        unset($orderDetail);
        // calculating seller voucher if applied
        $mpVoucher = WkMpOrderVoucher::getVoucherDetailByIdSeller($this->order->id, $this->id_seller);
        $voucherTotal = 0;
        if ($mpVoucher) {
            foreach ($mpVoucher as &$voucher) {
                $voucherTotal = $voucherTotal + $voucher['voucher_value'];
                $voucher['voucher_value'] = Tools::displayPrice($voucher['voucher_value'], $this->currency);
            }
        }
        // add product image in invoice
        if (Configuration::get('PS_PDF_IMG_INVOICE')) {
            foreach ($orderDetails as &$orderDetail) {
                if ($this->id_seller == 0) {
                    $isBelongToSeller = $this->getSellerProductInfoByProductId($orderDetail['product_id']);
                    //sending admin invoice then check config if seller invoice enabled and product is seller product then unset otherwise send seller product in admin invoice bescause seller invoice is disabled
                    if ($isBelongToSeller && Configuration::get('MP_SELLER_INVOICE_ACTIVE') == 1) {
                        unset($orderDetails[$id]);
                        continue;
                    }
                } else {
                    $isBelongToSeller = $this->getSellerProductInfo($orderDetail['product_id']);
                    if (!$isBelongToSeller) {
                        unset($orderDetails[$id]);
                        continue;
                    }
                }
                if ($orderDetail['image'] != null) {
                    $name = 'product_mini_'.(int) $orderDetail['product_id'].(isset($orderDetail['product_attribute_id']) ? '_'.(int) $orderDetail['product_attribute_id'] : '').'.jpg';
                    $path = _PS_PROD_IMG_DIR_.$orderDetail['image']->getExistingImgPath().'.jpg';
                    $orderDetail['image_tag'] = preg_replace(
                        '/\.*'.preg_quote(__PS_BASE_URI__, '/').'/',
                        _PS_ROOT_DIR_.DIRECTORY_SEPARATOR,
                        ImageManager::thumbnail($path, $name, 45, 'jpg', false),
                        1
                    );
                    if (file_exists(_PS_TMP_IMG_DIR_.$name)) {
                        $orderDetail['image_size'] = getimagesize(_PS_TMP_IMG_DIR_.$name);
                    } else {
                        $orderDetail['image_size'] = false;
                    }
                }
            }
            unset($orderDetail); // don't overwrite the last order_detail later
        }
        $cartRules = $this->order->getCartRules($this->order->id);
        $freeShipping = false;
        foreach ($cartRules as $key => $cartRule) {
            if ($cartRule['free_shipping']) {
                $freeShipping = true;
                /*
                 * Adjust cart rule value to remove the amount of the shipping.
                 * We're not interested in displaying the shipping discount as it is already shown as "Free Shipping".
                 */
                $cartRules[$key]['value_tax_excl'] -= $this->order->total_shipping_tax_excl;
                $cartRules[$key]['value'] -= $this->order->total_shipping_tax_incl;
                /*
                 * Don't display cart rules that are only about free shipping and don't create
                 * a discount on products.
                 */
                if ($cartRules[$key]['value'] == 0) {
                    unset($cartRules[$key]);
                }
            }
        }
        $productTaxes = 0;
        foreach ($this->getProductTaxesBreakdown() as $details) {
            $productTaxes += $details['total_amount'];
        }
        $productDiscountsTaxExcl = $this->order->total_discounts_tax_excl;
        $productDiscountsTaxIncl = $this->order->total_discounts_tax_incl;
        if ($freeShipping) {
            $productDiscountsTaxExcl -= $this->order->total_shipping_tax_excl;
            $productDiscountsTaxIncl -= $this->order->total_shipping_tax_incl;
        }
        $productsAfterDiscountsTaxExcl = $this->order->total_products - $productDiscountsTaxExcl;
        $productsAfterDiscountsTaxIncl = $this->order->total_products_wt - $productDiscountsTaxIncl;
        $shippingTaxExcl = $freeShipping ? 0 : $this->order->total_shipping_tax_excl;
        $shippingTaxIncl = $freeShipping ? 0 : $this->order->total_shipping_tax_incl;
        $shippingTaxes = $shippingTaxIncl - $shippingTaxExcl;
        $wrappingTaxes = $this->order->total_wrapping_tax_incl - $this->order->total_wrapping_tax_excl;
        $totalTaxes = $this->order->total_paid_tax_incl - $this->order->total_paid_tax_excl;
        $free = 1;
        if ($shippingTaxExcl > 0) {
            $free = 0;
        }
        $footer = array(
            'products_before_discounts_tax_excl' => $sellerTotalProducts,
            'product_discounts_tax_excl' => $productDiscountsTaxExcl,
            'products_after_discounts_tax_excl' => $productsAfterDiscountsTaxExcl,
            'products_before_discounts_tax_incl' => $this->order->total_products_wt,
            'product_discounts_tax_incl' => $productDiscountsTaxIncl,
            'products_after_discounts_tax_incl' => $productsAfterDiscountsTaxIncl,
            'product_taxes' => $productTaxes,
            'shipping_tax_excl' => $shippingTaxExcl,
            'shipping_taxes' => $shippingTaxes,
            'shipping_tax_incl' => $shippingTaxIncl,
            'wrapping_tax_excl' => $this->order->total_wrapping_tax_excl,
            'wrapping_taxes' => $wrappingTaxes,
            'wrapping_tax_incl' => $this->order->total_wrapping_tax_incl,
            'ecotax_taxes' => $totalTaxes - $productTaxes - $wrappingTaxes - $shippingTaxes,
            'total_taxes' => $totalTaxes,
            'total_paid_tax_excl' => $this->order->total_paid_tax_excl,
            'total_paid_tax_incl' => $this->order->total_paid_tax_incl,
            'free_shipping' => $free
        );
        foreach ($footer as $key => $value) {
            $footer[$key] = Tools::ps_round($value, _PS_PRICE_COMPUTE_PRECISION_, $this->order->round_mode);
        }
        $roundType = $this->getRoundType($this->order->round_type);
        $displayProductImages = Configuration::get('PS_PDF_IMG_INVOICE');
        $taxExcludedDisplay = Group::getPriceDisplayMethod($customer->id_default_group);
        $layout = $this->computeLayout(array('has_discount' => $hasDiscount));
        $legalFreeText = Hook::exec('displayInvoiceLegalFreeText', array('order' => $this->order));
        if (!$legalFreeText) {
            $legalFreeText = Configuration::get('PS_INVOICE_LEGAL_FREE_TEXT', (int) Context::getContext()->language->id, null, (int) $this->order->id_shop);
        }
        // Calculate all the seller and admin amount from the order
        $totalAdminCommission = $totalAdminCommissionTax = $sellerTotal = $sellerTotalTax = 0;
        $objOrderDetail = new WkMpSellerOrderDetail();
        $sellerOrderDetail = $objOrderDetail->getSellerProductFromOrder($this->order->id, $this->id_seller_customer);
        if ($sellerOrderDetail) {
            foreach ($sellerOrderDetail as $sellerDetail) {
                if (isset($orderDetails[$sellerDetail['id_order_detail']])) {
                    $orderDetails[$sellerDetail['id_order_detail']]['commission_rate'] = Tools::ps_round(
                        $sellerDetail['commission_rate'],
                        2
                    );
                }
                $totalAdminCommission += $sellerDetail['admin_commission'];
                $totalAdminCommissionTax += $sellerDetail['admin_tax'];
                $sellerTotal += $sellerDetail['seller_amount'];
                $sellerTotalTax += $sellerDetail['seller_tax'];
            }
        }
        $sellerCommission = new WkMpCommission();
        $commissionRate = $sellerCommission->getCommissionRate($this->id_seller_customer);
        if (!$commissionRate) {
            $commissionRate = Configuration::get('WK_MP_GLOBAL_COMMISSION');
        }
        $objCurrency = new Currency($this->order->id_currency);
        if ($orderDetails) {
            foreach ($orderDetails as $key => $value) {
                $orderDetails[$key]['unit_price_tax_excl'] = Tools::displayPrice($value['unit_price_tax_excl'], $objCurrency);
                $orderDetails[$key]['ecotax_tax_excl'] = Tools::displayPrice($value['ecotax_tax_excl'], $objCurrency);
                $orderDetails[$key]['unit_price_tax_incl'] = Tools::displayPrice($value['unit_price_tax_incl'], $objCurrency);
                $orderDetails[$key]['ecotax_tax_incl'] = Tools::displayPrice($value['ecotax_tax_incl'], $objCurrency);
                $orderDetails[$key]['total_price_tax_excl'] = Tools::displayPrice($value['total_price_tax_excl'], $objCurrency);
            }
        }
        $sellerTotal = $footer['products_before_discounts_tax_excl'] + $footer['product_taxes'];
        $footer = $this->getFooterFormatedData($footer, $objCurrency, $voucherTotal);
        $carrier = new Carrier($this->order->id_carrier);
        $data = array(
            'order' => $this->order,
            'carrierName' => $carrier->name,
            'currency' => new Currency($this->order->id_currency),
            'seller_obj' => $this->obj_seller,
            'order_invoice' => $this->order,
            'order_details' => $orderDetails,
            'cart_rules' => $cartRules,
            'delivery_address' => $formattedDeliveryAddress,
            'invoice_address' => $formattedInvoiceAddress,
            'addresses' => array('invoice' => $invoiceAddress, 'delivery' => $deliveryAddress),
            'tax_excluded_display' => $taxExcludedDisplay,
            'display_product_images' => $displayProductImages,
            'layout' => $layout,
            'tax_tab' => $this->getTaxTabContent(),
            'customer' => $customer,
            'footer' => $footer,
            'sellerTotal' => Tools::displayPrice($sellerTotal, $objCurrency),
            'sellerTotalTax' => Tools::displayPrice($sellerTotalTax, $objCurrency),
            'totalAdminCommission' => Tools::displayPrice($totalAdminCommission, $objCurrency),
            'totalAdminCommissionTax' => Tools::displayPrice($totalAdminCommissionTax, $objCurrency),
            'totalCommission' => Tools::displayPrice($totalAdminCommissionTax + $totalAdminCommission, $objCurrency),
            'sellerCommissionRate' => $commissionRate,
            'ps_price_compute_precision' => _PS_PRICE_COMPUTE_PRECISION_,
            'round_type' => $roundType,
            'legal_free_text' => $legalFreeText,
        );
        $this->context->smarty->assign($data);
        $this->context->smarty->assign(array('invoice_admin_seller' => 2));
        //To check admin product or not
        if ($this->id_seller) {
            $this->context->smarty->assign(array('is_admin_product' => $objModule->l('Seller')));
        } else {
            $this->context->smarty->assign(array('is_admin_product' => $objModule->l('Admin')));
        }
        $tpls = $this->getTpls();
        $this->context->smarty->assign($tpls);
        return $this->context->smarty->fetch($this->getTemplate('invoice'));
    }

    public function getRoundType($round)
    {
        /*
         * Need the $round_mode for the tests.
         */
        $roundType = null;
        switch ($round) {
            case Order::ROUND_TOTAL:
                    $roundType = 'total';
                break;
            case Order::ROUND_LINE:
                    $roundType = 'line';
                break;
            case Order::ROUND_ITEM:
                    $roundType = 'item';
                break;
            default:
                    $roundType = 'line';
                break;
        }
        return $roundType;
    }

    public function getFooterFormatedData($footer, $objCurrency, $voucherTotal)
    {
        if ($footer) {
            //$sellertotal = $footer['products_before_discounts_tax_excl'] + $footer['product_taxes'] - $voucherTotal;
            $footer['products_before_discounts_tax_excl'] = Tools::displayPrice($footer['products_before_discounts_tax_excl'], $objCurrency);
            $footer['show_product_discounts_tax_excl'] = 0;
            if ($footer['product_discounts_tax_excl'] > 0) {
                $footer['show_product_discounts_tax_excl'] = 1;
            }
            $footer['product_discounts_tax_excl'] = Tools::displayPrice($footer['product_discounts_tax_excl'], $objCurrency);
            $footer['products_after_discounts_tax_excl'] = Tools::displayPrice($footer['products_after_discounts_tax_excl'], $objCurrency);
            $footer['products_before_discounts_tax_incl'] = Tools::displayPrice($footer['products_before_discounts_tax_incl'], $objCurrency);
            $footer['product_discounts_tax_incl'] = Tools::displayPrice($footer['product_discounts_tax_incl'], $objCurrency);
            $footer['products_after_discounts_tax_incl'] = Tools::displayPrice($footer['products_after_discounts_tax_incl'], $objCurrency);
            $footer['product_taxes'] = Tools::displayPrice($footer['product_taxes'], $objCurrency);
            $footer['shipping_tax_excl'] = Tools::displayPrice($footer['shipping_tax_excl'], $objCurrency);
            $footer['shipping_taxes'] = Tools::displayPrice($footer['shipping_taxes'], $objCurrency);
            $footer['shipping_tax_incl'] = Tools::displayPrice($footer['shipping_tax_incl'], $objCurrency);
            $footer['show_wrapping_tax_excl'] = 0;
            if ($footer['wrapping_tax_excl'] > 0) {
                $footer['show_wrapping_tax_excl'] = 1;
            }
            $footer['wrapping_tax_excl'] = Tools::displayPrice($footer['wrapping_tax_excl'], $objCurrency);
            $footer['wrapping_taxes'] = Tools::displayPrice($footer['wrapping_taxes'], $objCurrency);
            $footer['ecotax_taxes'] = Tools::displayPrice($footer['ecotax_taxes'], $objCurrency);
            $footer['total_taxes'] = Tools::displayPrice($footer['total_taxes'], $objCurrency);
            $footer['total_paid_tax_excl'] = Tools::displayPrice($footer['total_paid_tax_excl'], $objCurrency);
            $footer['total_paid_tax_incl'] = Tools::displayPrice($footer['total_paid_tax_incl'], $objCurrency);
        }
        $footer['show_seller_voucher'] = 0;
        if ($voucherTotal > 0) {
            $footer['show_seller_voucher'] = 1;
        }
        $footer['seller_voucher'] = Tools::displayPrice($voucherTotal, $objCurrency);
        return $footer;
    }

    public function getTpls()
    {
        $tpls = array(
            'style_tab' => $this->context->smarty->fetch($this->getTemplate('invoice.style-tab')),
            'addresses_tab' => $this->context->smarty->fetch($this->getTemplate('invoice.addresses-tab')),
            'summary_tab' => $this->context->smarty->fetch($this->getTemplate('invoice.summary-tab')),
            'product_tab' => $this->context->smarty->fetch($this->getTemplate('invoice.product-tab')),
            'tax_tab' => $this->getTaxTabContent(),
            'payment_tab' => $this->context->smarty->fetch($this->getTemplate('invoice.payment-tab')),
            'total_tab' => $this->context->smarty->fetch($this->getTemplate('invoice.total-tab')),
        );
        return $tpls;
    }

    public function computeLayout($params)
    {
        $layout = array(
            'reference' => array(
                'width' => 15,
            ),
            'product' => array(
                'width' => 30,
            ),
            'quantity' => array(
                'width' => 6,
            ),
            'tax_code' => array(
                'width' => 16,
            ),
            'unit_price_tax_excl' => array(
                'width' => 11,
            ),
            'unit_price_tax_incl' => array(
                'width' => 11,
            ),
            'total_tax_excl' => array(
                'width' => 11,
            ),
        );

        if (isset($params['has_discount']) && $params['has_discount']) {
            $layout['before_discount'] = array('width' => 0);
            $layout['product']['width'] -= 7;
            $layout['reference']['width'] -= 3;
        }

        $total_width = 0;
        $free_columns_count = 0;
        foreach ($layout as $data) {
            if ($data['width'] === 0) {
                ++$free_columns_count;
            }

            $total_width += $data['width'];
        }

        $delta = 100 - $total_width;

        foreach ($layout as $row => $data) {
            if ($data['width'] === 0) {
                $layout[$row]['width'] = $delta / $free_columns_count;
            }
        }

        $layout['_colCount'] = count($layout);


        return $layout;
    }

    public function getTaxTabContent()
    {
        $debug = Tools::getValue('debug');
        $address = new Address((int) $this->order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $tax_exempt = Configuration::get('VATNUMBER_MANAGEMENT') && !empty($address->vat_number) && $address->id_country != Configuration::get('VATNUMBER_COUNTRY');
        $carrier = new Carrier($this->order->id_carrier);
        $tax_breakdowns = $this->getTaxBreakdown();

        if ($tax_breakdowns) {
            foreach ($tax_breakdowns as $key => $value) {
                if ($value) {
                    foreach ($value as $k => $v) {
                        if (isset($v['total_price_tax_excl'])) {
                            $tax_breakdowns[$key][$k]['total_price_tax_excl'] = Tools::displayPrice(
                                $v['total_price_tax_excl'],
                                $this->currency
                            );
                        }
                        if (isset($v['total_amount'])) {
                            $tax_breakdowns[$key][$k]['total_amount'] = Tools::displayPrice(
                                $v['total_amount'],
                                $this->currency
                            );
                        }
                        if (isset($v['total_unit_price_tax_excl'])) {
                            $tax_breakdowns[$key][$k]['total_unit_price_tax_excl'] = Tools::displayPrice(
                                $v['total_unit_price_tax_excl'],
                                $this->currency
                            );
                        }
                        if (isset($v['total_tax_excl'])) {
                            $tax_breakdowns[$key][$k]['total_tax_excl'] = Tools::displayPrice(
                                $v['total_tax_excl'],
                                $this->currency
                            );
                        }
                    }
                }
            }
        }

        $data = array(
            'tax_exempt' => $tax_exempt,
            'tax_breakdowns' => $tax_breakdowns,
            'carrier' => $carrier,
            'order' => $this->order,
            'use_one_after_another_method' => $this->useOneAfterAnotherTaxComputationMethod(),
            'display_tax_bases_in_breakdowns' => $this->displayTaxBasesInProductTaxesBreakdown(),
            'product_tax_breakdown' => $this->getProductTaxesBreakdown(),
            'shipping_tax_breakdown' => $this->getShippingTaxesBreakdown(),
            'ecotax_tax_breakdown' => $this->getEcoTaxTaxesBreakdown(),
            'wrapping_tax_breakdown' => $this->getWrappingTaxesBreakdown(),
        );
        if ($debug) {
            return $data;
        }

        $this->context->smarty->assign($data);

        return $this->context->smarty->fetch($this->getTemplate('invoice.tax-tab'));
    }

    public function getTaxBreakdown()
    {
        $breakdowns = array(
            'product_tax' => $this->getProductTaxesBreakdown(),
            'shipping_tax' => $this->getShippingTaxesBreakdown(),
            'ecotax_tax' => $this->getEcoTaxTaxesBreakdown(),
            'wrapping_tax' => $this->getWrappingTaxesBreakdown(),
        );
        foreach ($breakdowns as $type => $bd) {
            if (empty($bd)) {
                unset($breakdowns[$type]);
            }
        }

        if (empty($breakdowns)) {
            $breakdowns = false;
        }

        if (isset($breakdowns['product_tax'])) {
            foreach ($breakdowns['product_tax'] as &$bd) {
                $bd['total_tax_excl'] = $bd['total_price_tax_excl'];
            }
        }

        if (isset($breakdowns['ecotax_tax'])) {
            foreach ($breakdowns['ecotax_tax'] as &$bd) {
                $bd['total_tax_excl'] = $bd['ecotax_tax_excl'];
                $bd['total_amount'] = $bd['ecotax_tax_incl'] - $bd['ecotax_tax_excl'];
            }
        }

        return $breakdowns;
    }

    public function useOneAfterAnotherTaxComputationMethod()
    {
        // if one of the order details use the tax computation method the display will be different
        return Db::getInstance()->getValue('
		SELECT od.`tax_computation_method`
		FROM `'._DB_PREFIX_.'order_detail_tax` odt
		LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
		WHERE od.`id_order` = '.(int) $this->order->id.'
		AND od.`tax_computation_method` = '.(int) TaxCalculator::ONE_AFTER_ANOTHER_METHOD)
        || Configuration::get('PS_INVOICE_TAXES_BREAKDOWN');
    }

    public function displayTaxBasesInProductTaxesBreakdown()
    {
        return !$this->useOneAfterAnotherTaxComputationMethod();
    }

    public function getProductTaxesBreakdown()
    {
        $sum_composite_taxes = !$this->useOneAfterAnotherTaxComputationMethod();

        // $breakdown will be an array with tax rates as keys and at least the columns:
        //  - 'total_price_tax_excl'
        //  - 'total_amount'
        $breakdown = array();
        $details = $this->order->getProductTaxesDetails();
        // checking if order has no product of current seller then unset the array
        foreach ($details as $key => $detail) {
            $obj_ord_detail = new OrderDetail($detail['id_order_detail']);
            if ($this->id_seller == 0) {
                $isBelongToSeller = $this->getSellerProductInfoByProductId($obj_ord_detail->product_id);
                //sending admin invoice then check config if seller invoice enabled and product is seller product then unset otherwise send seller product in admin invoice bescause seller invoice is disabled
                if ($isBelongToSeller && Configuration::get('MP_SELLER_INVOICE_ACTIVE') == 1) {
                    unset($details[$key]);
                    continue;
                }
            } else {
                if (!empty($obj_ord_detail)) {
                    $isBelongToSeller = $this->getSellerProductInfo($obj_ord_detail->product_id);
                    if (!$isBelongToSeller) {
                        unset($details[$key]);
                    }
                }
            }
        }
        if ($sum_composite_taxes) {
            $grouped_details = array();
            foreach ($details as $row) {
                if (!isset($grouped_details[$row['id_order_detail']])) {
                    $grouped_details[$row['id_order_detail']] = array(
                        'tax_rate' => 0,
                        'total_tax_base' => 0,
                        'total_amount' => 0,
                        'unit_tax_base' => 0,
                        'id_tax' => $row['id_tax'],
                    );
                }

                $grouped_details[$row['id_order_detail']]['tax_rate'] += $row['tax_rate'];
                $grouped_details[$row['id_order_detail']]['total_tax_base'] += $row['total_tax_base'];
                $grouped_details[$row['id_order_detail']]['total_amount'] += $row['total_amount'];
                $grouped_details[$row['id_order_detail']]['unit_tax_base'] += $row['unit_tax_base'];
            }
            $details = $grouped_details;
        }
        foreach ($details as $row) {
            $rate = sprintf('%.3f', $row['tax_rate']);
            if (!isset($breakdown[$rate])) {
                $breakdown[$rate] = array(
                    'total_price_tax_excl' => 0,
                    'total_amount' => 0,
                    'total_unit_price_tax_excl' => 0,
                    'id_tax' => $row['id_tax'],
                    'rate' => $rate,
                );
            }

            $breakdown[$rate]['total_price_tax_excl'] += $row['total_tax_base'];
            $breakdown[$rate]['total_unit_price_tax_excl'] += $row['unit_tax_base'];
            $breakdown[$rate]['total_amount'] += $row['total_amount'];
        }
        foreach ($breakdown as $rate => $data) {
            $breakdown[$rate]['total_price_tax_excl'] = Tools::ps_round($data['total_price_tax_excl'], _PS_PRICE_COMPUTE_PRECISION_, $this->order->round_mode);
            $breakdown[$rate]['total_amount'] = Tools::ps_round($data['total_amount'], _PS_PRICE_COMPUTE_PRECISION_, $this->order->round_mode);
        }

        ksort($breakdown);

        return $breakdown;
    }

    public function getShippingTaxesBreakdown()
    {
        $shipping_breakdown = array();
        // No shipping breakdown if no shipping!
        if ($this->order->total_shipping_tax_excl == 0) {
            return array();
        }

        // No shipping breakdown if it's free!
        foreach ($this->order->getCartRules() as $cartRule) {
            if ($cartRule['free_shipping']) {
                return array();
            }
        }

        $shipping_tax_amount = $this->order->total_shipping_tax_incl - $this->order->total_shipping_tax_excl;

        if (Configuration::get('PS_INVOICE_TAXES_BREAKDOWN') || Configuration::get('PS_ATCP_SHIPWRAP')) {
            if ($idOrderInvoice = $this->getIdOrderInvoiceNumberByIdOrder($this->order->id)) {
                $shipping_breakdown = Db::getInstance()->executeS(
                    'SELECT t.id_tax, t.rate, oit.amount as total_amount
                    FROM `'._DB_PREFIX_.'tax` t
                    INNER JOIN `'._DB_PREFIX_.'order_invoice_tax` oit ON oit.id_tax = t.id_tax
                    WHERE oit.type = "shipping" AND oit.id_order_invoice = '.(int)$idOrderInvoice.''
                );

                $sum_of_split_taxes = 0;
                $sum_of_tax_bases = 0;
                foreach ($shipping_breakdown as &$row) {
                    if (Configuration::get('PS_ATCP_SHIPWRAP')) {
                        $row['total_tax_excl'] = Tools::ps_round($row['total_amount'] / $row['rate'] * 100, _PS_PRICE_COMPUTE_PRECISION_, $this->order->round_mode);
                        $sum_of_tax_bases += $row['total_tax_excl'];
                    } else {
                        $row['total_tax_excl'] = $this->order->total_shipping_tax_excl;
                    }

                    $row['total_amount'] = Tools::ps_round(
                        $row['total_amount'],
                        _PS_PRICE_COMPUTE_PRECISION_,
                        $this->order->round_mode
                    );
                    $sum_of_split_taxes += $row['total_amount'];
                }
                unset($row);

                $delta_amount = $shipping_tax_amount - $sum_of_split_taxes;

                if ($delta_amount != 0) {
                    Tools::spreadAmount($delta_amount, _PS_PRICE_COMPUTE_PRECISION_, $shipping_breakdown, 'total_amount');
                }

                $delta_base = $this->order->total_shipping_tax_excl - $sum_of_tax_bases;

                if ($delta_base != 0) {
                    Tools::spreadAmount($delta_base, _PS_PRICE_COMPUTE_PRECISION_, $shipping_breakdown, 'total_tax_excl');
                }
            }
        } else {
            $shipping_breakdown = array(
                array(
                    'total_tax_excl' => $this->order->total_shipping_tax_excl,
                    'rate' => $this->order->carrier_tax_rate,
                    'total_amount' => $shipping_tax_amount,
                    'id_tax' => null,
                ),
            );
        }

        return $shipping_breakdown;
    }

    public function getIdOrderInvoiceNumberByIdOrder($idOrder)
    {
        return Db::getInstance()->getValue(
            'SELECT `id_order_invoice` FROM '._DB_PREFIX_.'order_invoice WHERE `id_order` ='.(int) $idOrder
        );
    }

    public function getEcoTaxTaxesBreakdown()
    {
        $result = Db::getInstance()->executeS('
		SELECT `ecotax_tax_rate` as `rate`, SUM(`ecotax` * `product_quantity`) as `ecotax_tax_excl`, SUM(`ecotax` * `product_quantity`) as `ecotax_tax_incl`
		FROM `'._DB_PREFIX_.'order_detail`
		WHERE `id_order` = '.(int) $this->order->id.'
		GROUP BY `ecotax_tax_rate`');

        $taxes = array();
        foreach ($result as $row) {
            if ($row['ecotax_tax_excl'] > 0) {
                $row['ecotax_tax_incl'] = Tools::ps_round($row['ecotax_tax_excl'] + ($row['ecotax_tax_excl'] * $row['rate'] / 100), _PS_PRICE_DISPLAY_PRECISION_);
                $row['ecotax_tax_excl'] = Tools::ps_round($row['ecotax_tax_excl'], _PS_PRICE_DISPLAY_PRECISION_);
                $taxes[] = $row;
            }
        }

        return $taxes;
    }

    public function getWrappingTaxesBreakdown()
    {
        if ($this->order->total_wrapping_tax_excl == 0) {
            return array();
        }

        $wrapping_tax_amount = $this->order->total_wrapping_tax_incl - $this->order->total_wrapping_tax_excl;

        $wrapping_breakdown = Db::getInstance()->executeS(
            'SELECT t.id_tax, t.rate, oit.amount as total_amount
			FROM `'._DB_PREFIX_.'tax` t
			INNER JOIN `'._DB_PREFIX_.'order_invoice_tax` oit ON oit.id_tax = t.id_tax
			WHERE oit.type = "wrapping"'
        );

        $sum_of_split_taxes = 0;
        $sum_of_tax_bases = 0;
        $total_tax_rate = 0;
        foreach ($wrapping_breakdown as &$row) {
            if (Configuration::get('PS_ATCP_SHIPWRAP')) {
                $row['total_tax_excl'] = Tools::ps_round($row['total_amount'] / $row['rate'] * 100, _PS_PRICE_COMPUTE_PRECISION_, $this->order->getOrder()->round_mode);
                $sum_of_tax_bases += $row['total_tax_excl'];
            } else {
                $row['total_tax_excl'] = $this->order->total_wrapping_tax_excl;
            }

            $row['total_amount'] = Tools::ps_round($row['total_amount'], _PS_PRICE_COMPUTE_PRECISION_, $this->order->getOrder()->round_mode);
            $sum_of_split_taxes += $row['total_amount'];
            $total_tax_rate += (float) $row['rate'];
        }
        unset($row);

        $delta_amount = $wrapping_tax_amount - $sum_of_split_taxes;

        if ($delta_amount != 0) {
            Tools::spreadAmount($delta_amount, _PS_PRICE_COMPUTE_PRECISION_, $wrapping_breakdown, 'total_amount');
        }

        $delta_base = $this->order->total_wrapping_tax_excl - $sum_of_tax_bases;

        if ($delta_base != 0) {
            Tools::spreadAmount($delta_base, _PS_PRICE_COMPUTE_PRECISION_, $wrapping_breakdown, 'total_tax_excl');
        }

        if (!Configuration::get('PS_INVOICE_TAXES_BREAKDOWN') && !Configuration::get('PS_ATCP_SHIPWRAP')) {
            $wrapping_breakdown = array(
                array(
                    'total_tax_excl' => $this->order->total_wrapping_tax_excl,
                    'rate' => $total_tax_rate,
                    'total_amount' => $wrapping_tax_amount,
                ),
            );
        }

        return $wrapping_breakdown;
    }

    public function getBulkFilename()
    {
        return 'invoices.pdf';
    }

    public function getFilename($extention = true)
    {
        
		$idLang = Context::getContext()->language->id;
        $idShop = (int) $this->order->id_shop;
        $format = '%1$s%2$06d';
        if ($this->id_seller) {
            $objConfig = new MpSellerInvoiceConfig();
            $sellerConfig = $objConfig->getSellerInvoiceConfig($this->id_seller, $idLang);
            if ($sellerConfig && $sellerConfig['invoice_prefix']) {
                $sellerInvoicePrefix = $sellerConfig['invoice_prefix'];
            } else {
                $sellerInvoicePrefix = Configuration::get('PS_INVOICE_PREFIX', $idLang);
            }

            $objInvoiceRecord = new MpSellerOrderInvoiceRecord();
            if ($isExist = $objInvoiceRecord->getOrderInvoiceNumber($this->order->id, $this->id_seller)) {
                $objInvoiceRecord = new MpSellerOrderInvoiceRecord($isExist['id_order_invoice']);
                $invoiceNumber = $objInvoiceRecord->invoice_number;
				$invoiceNumber = $this->order->invoice_number;
            } elseif ($sellerConfig['invoice_number']) {
                $invoiceNumber = $sellerConfig['invoice_number'];
            } else {
                $invoiceNumber = 0;
				$invoiceNumber = $this->order->invoice_number;
            }
        } else {
            $sellerInvoicePrefix = Configuration::get('PS_INVOICE_PREFIX', $idLang);
            $invoiceNumber = $this->order->invoice_number;
        }

        if ($extention) {
            return sprintf(
                $format,
                $sellerInvoicePrefix,
                $invoiceNumber,
                date('Y', strtotime($this->order->date_add))
            ).'.pdf';
        } else {
            return sprintf(
                $format,
                $sellerInvoicePrefix,
                $invoiceNumber,
                date('Y', strtotime($this->order->date_add))
            );
        }
    }

    public function getTemplate($template)
    {
        $path = _PS_MODULE_DIR_.'mpsellerinvoice/pdf/'.$template.'.tpl';

        return $path;
    }

    public function assignCommonHeaderDataForSeller($admin = false)
    {
        $id_shop = (int) $this->context->shop->id;
        if ($this->id_seller == 0) {
            $shopName = Configuration::get('PS_SHOP_NAME');
            $isAdmin = 1;
        } else {
            $shopName = $this->obj_seller->shop_name;
            $isAdmin = 0;
        }

        $path_logo = $this->getShopImageLink();


        $width = 0;
        $height = 0;
        if (!empty($path_logo)) {
            list($width, $height) = getimagesize($path_logo);
        }

        // Limit the height of the logo for the PDF render
        $maximum_height = 100;
        if ($height > $maximum_height) {
            $ratio = $maximum_height / $height;
            $height *= $ratio;
            $width *= $ratio;
        }

        if (isset($this->obj_seller->city)) {
            $this->context->smarty->assign(
                array(
                    'city' => $this->obj_seller->city,
                )
            );
        }
        if (isset($this->obj_seller->id_state)) {
            $state = new State($this->obj_seller->id_state, $this->context->language->id);
            $this->context->smarty->assign(
                array(
                    'state' => $state->name,
                )
            );
        }
        if (isset($this->obj_seller->postcode)) {
            $this->context->smarty->assign(
                array(
                    'postcode' => $this->obj_seller->postcode,
                )
            );
        }
        if (isset($this->obj_seller->id_country)) {
            $country = new Country($this->obj_seller->id_country, $this->context->language->id);
            $this->context->smarty->assign(
                array(
                    'country' => $country->name,
                )
            );
        }
        if (isset($this->sellerInvoiceConfig)) {
            if ($this->sellerInvoiceConfig['invoice_vat']) {
                $this->context->smarty->assign(
                    array(
                        'sellerVat' => $this->sellerInvoiceConfig['invoice_vat'],
                    )
                );
            }
            if ($this->sellerInvoiceConfig['invoice_legal_text']) {
                $this->context->smarty->assign(
                    array(
                        'seller_invoice_legal_text' => $this->sellerInvoiceConfig['invoice_legal_text'],
                    )
                );
            }
            if ($this->sellerInvoiceConfig['invoice_footer_text']) {
                $this->context->smarty->assign(
                    array(
                        'seller_invoice_footer_text' => $this->sellerInvoiceConfig['invoice_footer_text'],
                    )
                );
            }
        }

        $invoiceNum = $this->getFilename(false);

        $this->context->smarty->assign(array(
            'logo_path' => $path_logo,
            'img_ps_dir' => 'http://'.Tools::getMediaServer(_PS_IMG_)._PS_IMG_,
            'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
            'date' => $this->order->date_upd,
            'seller_invoice_title' => $invoiceNum,
            'shop_name' => $shopName,
            'isAdmin' => $isAdmin,
            'shop_details' => ($this->obj_seller) ? $this->obj_seller->address : '',
            'width_logo' => $width,
            'height_logo' => $height,
        ));
    }

    public function assignCommonFooterDataForSeller()
    {
        $this->context->smarty->assign(array(
            'available_in_your_account' => true,
            'shop_address' => ($this->obj_seller) ? $this->obj_seller->address : '',
            'shop_fax' => ($this->obj_seller) ? $this->obj_seller->fax : '',
            'shop_phone' => ($this->obj_seller) ? $this->obj_seller->phone : '',
            'shop_email' => ($this->obj_seller) ? $this->obj_seller->business_email : '',
            'free_text' => Configuration::get('PS_INVOICE_FREE_TEXT', (int) Context::getContext()->language->id, null, $this->context->shop->id),
        ));
    }

    public function getShopImageLink()
    {
        if ($this->id_seller) {
            if ($this->obj_seller->shop_image && file_exists(_PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$this->obj_seller->shop_image)) {
                return _PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$this->obj_seller->shop_image;
            }
        }

        return $this->getLogo();
    }

    public function getLogo()
    {
        $logo = '';

        $id_shop = (int) $this->shop->id;

        if (Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop))) {
            $logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop);
        } elseif (Configuration::get('PS_LOGO', null, null, $id_shop) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $id_shop))) {
            $logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $id_shop);
        }

        return $logo;
    }

    public function getSellerProductInfo($id_product)
    {
        $details = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'wk_mp_seller_order_detail where `seller_customer_id` = '.(int) $this->id_seller_customer.' AND `product_id` = '.(int) $id_product);
        if ($details) {
            return $details;
        }

        return false;
    }

    public function getSellerProductInfoByProductId($id_product)
    {
        $details = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'wk_mp_seller_product where `id_ps_product` = '.(int) $id_product);
        if ($details) {
            return $details;
        }

        return false;
    }

    public function saveSellerInvoiceRecord()
    {
        $objInvoiceRecord = new MpSellerOrderInvoiceRecord();
        if ($isExist = $objInvoiceRecord->getOrderInvoiceNumber($this->order->id, $this->id_seller)) {
            $objInvoiceRecord = new MpSellerOrderInvoiceRecord($isExist['id_order_invoice']);
        } else {
            $lastInsertRow = $objInvoiceRecord->getLastRowByIdSeller($this->id_seller);
            if ($lastInsertRow) {
                ++$lastInsertRow;
            } else {
                $lastInsertRow = 1;
            }
            $objInvoiceRecord->invoice_number = $lastInsertRow;
            if ($this->sellerInvoiceConfig && $this->sellerInvoiceConfig['invoice_number']) {
                if ($this->sellerInvoiceConfig['invoice_number'] > $lastInsertRow) {
                    $lastInsertRow = $this->sellerInvoiceConfig['invoice_number'] + 1;
                }
                $objInvoiceRecord->invoice_number = (int) $lastInsertRow;
            }
        }

        $objInvoiceRecord->id_order = (int) $this->order->id;
        $objInvoiceRecord->id_seller = (int) $this->id_seller;
        $objInvoiceRecord->save();
    }

    public function getProductCommissionDetail($idProduct, $idProductAttribute)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM '._DB_PREFIX_.'wk_mp_seller_order_detail WHERE
                `product_id` = '.(int) $idProduct.' AND
                `product_attribute_id` = '.(int) $idProductAttribute.' AND
                `seller_customer_id` = '.(int) $this->id_seller_customer.' AND
                `id_order` = '.(int) $this->order->id
        );
    }
}
