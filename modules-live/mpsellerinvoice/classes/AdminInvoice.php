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

class AdminInvoice extends HTMLTemplate
{
    public $result;

    public function createOrderInvoiceData($orders, $id_seller_customer, $adminInvoice = false, $invoiceHistory = false)
    {
        if ($orders) {
            $this->orders = $orders;
            $orders = explode(',', $orders);
            $this->invoiceHistory = $invoiceHistory;
            // Calculate all the seller and admin amount from the order
            $totalAdminCommission = $totalAdminCommissionTax = $totalCommission = $sellerTotal = $sellerTotalTax = 0;
            sort($orders);
            foreach ($orders as $idOrder) {
                $order = new Order((int) $idOrder);
                $this->id_seller_customer = $id_seller_customer;
                $this->order = $order;
                $this->currency = new Currency($this->order->id_currency);
                $this->shop = new Shop((int) $this->order->id_shop);
                $this->context = Context::getContext();
                $this->date = Tools::displayDate($order->date_add);
                $this->adminInvoice = $adminInvoice;
                if ($id_seller_customer == 'admin') {
                    $this->id_seller = 0;
                    $this->obj_seller = '';
                } else {
                    $this->obj_seller = $this->getSellerObject($id_seller_customer);
                    $this->id_seller = $this->obj_seller->id;
                }
                $this->result[$this->order->id] = array(
                    'order_reference' => $this->order->reference,
                );
                $this->result[$this->order->id]['created_date'] = $this->order->date_add;
                $this->result[$this->order->id]['from'] = isset($invoiceHistory['from'])?$invoiceHistory['from']:'';
                $this->result[$this->order->id]['to'] = isset($invoiceHistory['to'])?$invoiceHistory['to']:'';
                $orderDetail = $this->getAdminCommissionInvoiceContent();
                if ($orderDetail) {
                    $this->result[$this->order->id]['commission_fee'] = $orderDetail['totalAdminCommission'] + $orderDetail['totalAdminCommissionTax'];
                    $this->result[$this->order->id]['ps_price_compute_precision'] = $orderDetail['ps_price_compute_precision'];
                    $this->result[$this->order->id]['currency'] = $orderDetail['currency'];
                    $this->result[$this->order->id]['order'] = $this->order;
                    $this->result[$this->order->id]['admin_discount'] = $orderDetail['adminDiscount'];
                }

                $objOrderDetail = new WkMpSellerOrderDetail();
                $sellerOrderDetail = $objOrderDetail->getSellerProductFromOrder($this->order->id, $this->id_seller_customer);

                if ($sellerOrderDetail) {
                    foreach ($sellerOrderDetail as $sellerDetail) {
                        $totalAdminCommission += $sellerDetail['admin_commission'];
                        $totalAdminCommissionTax += $sellerDetail['admin_tax'];
                        $sellerTotal += $sellerDetail['seller_amount'];
                        $sellerTotalTax += $sellerDetail['seller_tax'];
                        $totalCommission += $sellerDetail['admin_commission'];
                        $totalCommission += $sellerDetail['admin_tax'];
                    }
                }
            }
            $this->result[$this->order->id]['totalAdminCommission'] = $totalAdminCommission;
            $this->result[$this->order->id]['totalCommission'] = $totalCommission;
            $this->result[$this->order->id]['totalAdminCommissionTax'] = $totalAdminCommissionTax;
            $this->result[$this->order->id]['sellerTotal'] = $sellerTotal;
            $this->result[$this->order->id]['sellerTotalTax'] = $sellerTotalTax;
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

    /**
     * [getHeader -> setting PDF header].
     *
     * @return [type] [description]
     */
    public function getHeader($isSendToSeller = false)
    {
        //saving order id and invoice number in seller's table
        $this->saveAdminCommissionInvoiceRecord($isSendToSeller);
        //---------------------------------------------------
        $this->assignCommonHeaderDataForSeller();
        $this->context->smarty->assign(array(
            'header' => self::l('Invoice'),
        ));

        if ($this->adminInvoice) {
            $this->context->smarty->assign('adminInvoice', 1);
        }

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
        $this->context->smarty->assign(array(
            'footer' => self::l('Seller Invoice'),
        ));

        return $this->context->smarty->fetch($this->getTemplate('sellerinvoice_footer'));
    }

    /**
     * [getContent -> calculating PDF content seller' product wise].
     *
     * @return [type] [description]
     */
    public function getAdminCommissionInvoiceContent()
    {
        $invoiceAddressPatternRules = Tools::jsonDecode(Configuration::get('PS_INVCE_INVOICE_ADDR_RULES'), true);
        $deliveryAddressPatternRules = Tools::jsonDecode(Configuration::get('PS_INVCE_DELIVERY_ADDR_RULES'), true);
        $invoice_address = new Address((int) $this->order->id_address_invoice);
        $country = new Country((int) $invoice_address->id_country);

        $formatted_invoice_address = AddressFormat::generateAddress($invoice_address, $invoiceAddressPatternRules, '<br />', ' ');
        $delivery_address = null;
        $formatted_delivery_address = '';

        $delivery_address = new Address((int) $this->order->id_address_delivery);
        $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, $deliveryAddressPatternRules, '<br />', ' ');

        $customer = new Customer((int) $this->order->id_customer);

        $orderDetails = $this->order->getProducts();

        $seller_total_products = 0;
        // customize order_details to maintain tax
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
            ) as $ecotaxField) {
                $orderDetails[$key][$ecotaxField] = Tools::ps_round($row[$ecotaxField], _PS_PRICE_COMPUTE_PRECISION_, $roundMode);
            }
            // Aliases
            $orderDetails[$key]['unit_price_tax_excl_including_ecotax'] = $row['unit_price_tax_excl'];
            $orderDetails[$key]['unit_price_tax_incl_including_ecotax'] = $row['unit_price_tax_incl'];
            $orderDetails[$key]['total_price_tax_excl_including_ecotax'] = $row['total_price_tax_excl'];
            $orderDetails[$key]['total_price_tax_incl_including_ecotax'] = $row['total_price_tax_incl'];
        }

        $hasDiscount = false;
        $context = Context::getContext();
        // $locale = static::getContextLocale($context);
        foreach ($orderDetails as $id => &$orderDetail) {
            if ($this->id_seller == 0) {
                $isBelongToSeller = $this->getSellerProductInfoByProductId($orderDetail['product_id']);
                if ($isBelongToSeller) {
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
                $orderDetail['unit_price_tax_excl_before_specific_price'] = Tools::displayPrice(
                    Tools::ps_round($orderDetail['unit_price_tax_excl_before_specific_price'], 2),
                    (new Currency($this->order->id_currency))->iso_code
                );
            }
            // Set tax_code
            $taxes = OrderDetail::getTaxListStatic($id);
            $tax_temp = array();
            $objModule = new MpSellerInvoice();
            foreach ($taxes as $tax) {
                $obj = new Tax($tax['id_tax']);
                $tax_temp[] = sprintf($objModule->l('%1$s%2$s%%'), ($obj->rate + 0), '&nbsp;');
            }
            $seller_total_products += $orderDetail['total_price_tax_excl'];
            $orderDetail['order_detail_tax'] = $taxes;
            $orderDetail['order_detail_tax_label'] = implode(', ', $tax_temp);
            $sellerOrder = $this->getProductCommissionDetail($orderDetail['product_id'], $orderDetail['product_attribute_id']);

            if ($sellerOrder) {
                $orderDetails[$id]['admin_commission'] = $sellerOrder['admin_commission'];
                $orderDetails[$id]['admin_commission_tax'] = $sellerOrder['admin_tax'];
                $orderDetails[$id]['seller_amount'] = $sellerOrder['seller_amount'];
                $orderDetails[$id]['seller_amount_tax'] = $sellerOrder['seller_tax'];
            }
            unset($sellerOrder);
        }

        unset($tax_temp);
        unset($orderDetail);

        // Calculate all the seller and admin amount from the order
        $totalCommission = $totalAdminCommission = $totalAdminCommissionTax = $sellerTotal = $sellerTotalTax = 0;
        $objOrderDetail = new WkMpSellerOrderDetail();
        $sellerOrderDetail = $objOrderDetail->getSellerProductFromOrder($this->order->id, $this->id_seller_customer);

        if ($sellerOrderDetail) {
            foreach ($sellerOrderDetail as $sellerDetail) {
                $totalAdminCommission += $sellerDetail['admin_commission'];
                $totalAdminCommissionTax += $sellerDetail['admin_tax'];
                $sellerTotal += $sellerDetail['seller_amount'];
                $sellerTotalTax += $sellerDetail['seller_tax'];
                $totalCommission += $sellerDetail['admin_commission'];
                $totalCommission += $sellerDetail['admin_tax'];
            }
        }
        // ---------------------------------------------------------------
        $sellerCommission = new WkMpCommission();
        $commissionRate = $sellerCommission->getCommissionRate($this->id_seller_customer);
        if (!$commissionRate) {
            $commissionRate = Configuration::get('WK_MP_GLOBAL_COMMISSION');
        }
        $adminDiscount = 0;
        if ($this->order->total_discounts > 0) {
            $roundMode = $this->order->round_mode;
            $adminDiscount = (Tools::ps_round($this->order->total_discounts, _PS_PRICE_COMPUTE_PRECISION_, $roundMode))* $commissionRate;
            $adminDiscount = $adminDiscount / 100;
            $adminDiscount = Tools::ps_round($adminDiscount, _PS_PRICE_COMPUTE_PRECISION_, $roundMode);
        }

        $data = array(
            'currency' => new Currency($this->order->id_currency),
            'sellerTotal' => $sellerTotal,
            'sellerTotalTax' => $sellerTotalTax,
            'totalAdminCommission' => $totalAdminCommission,
            'totalAdminCommissionTax' => $totalAdminCommissionTax,
            'totalCommission' => $totalCommission,
            'sellerCommissionRate' => $commissionRate,
            'ps_price_compute_precision' => _PS_PRICE_COMPUTE_PRECISION_,
            'adminDiscount' => $adminDiscount
             //'round_type' => $round_type,
        );

        if (Tools::getValue('debug')) {
            die(json_encode($data));
        }

        if ($this->adminInvoice) {
            unset($data['tax_tab']);
        }

        return $data;
    }

    /**
     * [getContent -> calculating PDF content seller' product wise].
     *
     * @return [type] [description]
     */
    public function getContent($eachOrder = false)
    {
        $orders = explode(',', $this->orders);
        // Calculate all the seller and admin amount from the order
        $totalCommission = $totalAdminCommission = $totalAdminCommissionTax = $sellerTotal = $sellerTotalTax = 0;
        $objCurrency = new Currency($this->order->id_currency);
        if (!$eachOrder) {
            foreach ($orders as $idOrder) {
                $objOrderDetail = new WkMpSellerOrderDetail();
                $sellerOrderDetail = $objOrderDetail->getSellerProductFromOrder($idOrder, $this->id_seller_customer);
                if ($sellerOrderDetail) {
                    foreach ($sellerOrderDetail as $sellerDetail) {
                        $totalAdminCommission += $sellerDetail['admin_commission'];
                        $totalAdminCommissionTax += $sellerDetail['admin_tax'];
                        $sellerTotal += $sellerDetail['seller_amount'];
                        $sellerTotalTax += $sellerDetail['seller_tax'];
                        $totalCommission += $sellerDetail['admin_commission'];
                        $totalCommission += $sellerDetail['admin_tax'];
                    }
                }
            }
        }
        if ($eachOrder) {
            $orderDetails = $this->order->getProducts();
            // customize order_details to maintain tax
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
                ) as $ecotaxField) {
                    $orderDetails[$key][$ecotaxField] = Tools::ps_round($row[$ecotaxField], _PS_PRICE_COMPUTE_PRECISION_, $roundMode);
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
                    if ($isBelongToSeller) {
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
                $sellerOrder = $this->getProductCommissionDetail($orderDetail['product_id'], $orderDetail['product_attribute_id']);
                if ($sellerOrder) {
                    $orderDetails[$id]['admin_commission'] = Tools::displayPrice(Tools::ps_round($sellerOrder['admin_commission'], 2), $this->currency);
                    $orderDetails[$id]['admin_commission_tax'] = Tools::displayPrice(Tools::ps_round($sellerOrder['admin_tax'], 2), $this->currency);
                    $orderDetails[$id]['seller_amount'] = Tools::displayPrice(Tools::ps_round($sellerOrder['seller_amount'], 2), $this->currency);
                    $orderDetails[$id]['seller_amount_tax'] = Tools::displayPrice(Tools::ps_round($sellerOrder['seller_tax'], 2), $this->currency);
                    $orderDetails[$id]['order_total_commission'] = Tools::displayPrice(Tools::ps_round($sellerOrder['admin_commission'] + $sellerOrder['admin_tax'], 2), $this->currency);
                }
            }
            if ($orderDetails) {
                foreach ($orderDetails as $key => $value) {
                    $orderDetails[$key]['unit_price_tax_excl'] = Tools::displayPrice(Tools::ps_round($value['unit_price_tax_excl'], 2), $objCurrency);
                    $orderDetails[$key]['ecotax_tax_excl'] = Tools::displayPrice(Tools::ps_round($value['ecotax_tax_excl'], 2), $objCurrency);
                    $orderDetails[$key]['unit_price_tax_incl'] = Tools::displayPrice(Tools::ps_round($value['unit_price_tax_incl'], 2), $objCurrency);
                    $orderDetails[$key]['ecotax_tax_incl'] = Tools::displayPrice(Tools::ps_round($value['ecotax_tax_incl'], 2), $objCurrency);
                    $orderDetails[$key]['total_price_tax_excl'] = Tools::displayPrice(Tools::ps_round($value['total_price_tax_excl'], 2), $objCurrency);
                }
            }
            $invoice_address = new Address((int) $this->order->id_address_invoice);
            $delivery_address = null;
            $delivery_address = new Address((int) $this->order->id_address_delivery);
            $layout = $this->computeLayout(array('has_discount' => $hasDiscount));
            $displayProductImages = Configuration::get('PS_PDF_IMG_INVOICE');
            $objOrderDetail = new WkMpSellerOrderDetail();
            $sellerOrderDetail = $objOrderDetail->getSellerProductFromOrder($this->order->id, $this->id_seller_customer);
            $sellerOrderDetail = $objOrderDetail->getSellerProductFromOrder($this->order->id, $this->id_seller_customer);
            if ($sellerOrderDetail) {
                $totalCommission = 0;
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
        }
        //----------------------------------------------------
        if ($eachOrder) {
            $this->context->smarty->assign(array(
                'addresses' => array('invoice' => $invoice_address, 'delivery' => $delivery_address),
                'order' => $this->order,
                'layout' => $layout,
                'order_details' => $orderDetails,
                'display_product_images' => $displayProductImages
            ));
        }
        $this->context->smarty->assign(array(
            'admin_shop_address' => trim(OrderInvoice::getCurrentFormattedShopAddress((int) $this->order->id_shop)),
            'adminCommissionData' => $this->result,
            'invoiceHistory' => $this->invoiceHistory,
            'currency' => $this->order->id_currency,
            'totalAdminCommission' => Tools::displayPrice(Tools::ps_round($totalAdminCommission, 2), $objCurrency),
            'totalAdminCommissionTax' => Tools::displayPrice(Tools::ps_round($totalAdminCommissionTax, 2), $objCurrency),
            'totalCommission' => Tools::displayPrice(Tools::ps_round($totalAdminCommissionTax + $totalAdminCommission, 2), $objCurrency),
            'sellerTotal' => $sellerTotal,
            'sellerTotalTax' => $sellerTotalTax
        ));
        $productDiscountsTaxExcl = $this->order->total_discounts_tax_excl;
        if ($productDiscountsTaxExcl > 0) {
            $this->context->smarty->assign(
                array(
                    'productDiscountsTaxExcl' => Tools::displayPrice(Tools::ps_round($productDiscountsTaxExcl, 2), $objCurrency)
                )
            );
        }
        $tpls = array(
            'style_tab' => $this->context->smarty->fetch($this->getTemplate('invoice.style-tab')),
            'addresses_tab' => $this->context->smarty->fetch($this->getTemplate('invoice.admin-invoice-address')),
            'admin_total_tab' => $this->context->smarty->fetch(
                $this->getTemplate('invoice.admin-commission-total-breakup')
            ),
        );
        if ($eachOrder) {
            if ($this->adminInvoice) {
                $this->context->smarty->assign(array('invoice_admin_seller' => 1));
            } else {
                $this->context->smarty->assign(array('invoice_admin_seller' => 2));
            }
            $this->context->smarty->assign(array(
                'admin_shop_address' => trim(OrderInvoice::getCurrentFormattedShopAddress((int) $this->order->id_shop)),
            ));
            $tpls['summary_tab'] = $this->context->smarty->fetch($this->getTemplate('invoice.summary-tab'));
            $tpls['product_tab'] = $this->context->smarty->fetch($this->getTemplate('invoice.product-tab'));
            $tpls['admin_total_tab'] = $this->context->smarty->fetch($this->getTemplate('invoice.admin-commission-total'));
        } else {
            $tpls['summary_tab'] = $this->context->smarty->fetch($this->getTemplate('invoice.admin-summary-tab'));
            $tpls['product_tab'] = $this->context->smarty->fetch($this->getTemplate('invoice.admin-commission-breakup'));
        }
        $this->context->smarty->assign($tpls);
        return $this->context->smarty->fetch($this->getTemplate('invoice'));
    }

    public function computeLayout($params)
    {
        $layout = array(
            'reference' => array(
                'width' => 15,
            ),
            'product' => array(
                'width' => 23,
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
        if ($this->adminInvoice) {
            //$layout['product']['width'] += 8;
            //$layout['total_tax_excl']['width'] += 7;
        } else {
            $layout['product']['width'] += 7;
        }

        return $layout;
    }

    public function getTaxTabContent()
    {
        $debug = Tools::getValue('debug');
        $address = new Address((int) $this->order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $tax_exempt = Configuration::get('VATNUMBER_MANAGEMENT') && !empty($address->vat_number) && $address->id_country != Configuration::get('VATNUMBER_COUNTRY');
        $carrier = new Carrier($this->order->id_carrier);
        $tax_breakdowns = $this->getTaxBreakdown();
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
                if ($isBelongToSeller) {
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
            $breakdown[$rate]['total_price_tax_excl'] = Tools::ps_round(
                $data['total_price_tax_excl'],
                _PS_PRICE_COMPUTE_PRECISION_,
                $this->order->round_mode
            );
            $breakdown[$rate]['total_amount'] = Tools::ps_round(
                $data['total_amount'],
                _PS_PRICE_COMPUTE_PRECISION_,
                $this->order->round_mode
            );
        }

        ksort($breakdown);

        return $breakdown;
    }

    public function getShippingTaxesBreakdown()
    {
        // No shipping breakdown if no shipping!
        if ($this->order->total_shipping_tax_excl == 0) {
            return array();
        }

        // No shipping breakdown if it's free!
        foreach ($this->order->getCartRules() as $cart_rule) {
            if ($cart_rule['free_shipping']) {
                return array();
            }
        }

        $shipping_tax_amount = $this->order->total_shipping_tax_incl - $this->order->total_shipping_tax_excl;

        if (Configuration::get('PS_INVOICE_TAXES_BREAKDOWN') || Configuration::get('PS_ATCP_SHIPWRAP')) {
            $shipping_breakdown = Db::getInstance()->executeS(
                'SELECT t.id_tax, t.rate, oit.amount as total_amount
				 FROM `'._DB_PREFIX_.'tax` t
				 INNER JOIN `'._DB_PREFIX_.'order_invoice_tax` oit ON oit.id_tax = t.id_tax
				 WHERE oit.type = "shipping" '
            );

            $sum_of_split_taxes = 0;
            $sum_of_tax_bases = 0;
            foreach ($shipping_breakdown as &$row) {
                if (Configuration::get('PS_ATCP_SHIPWRAP')) {
                    $row['total_tax_excl'] = Tools::ps_round($row['total_amount'] / $row['rate'] * 100, _PS_PRICE_COMPUTE_PRECISION_, $this->getOrder()->round_mode);
                    $sum_of_tax_bases += $row['total_tax_excl'];
                } else {
                    $row['total_tax_excl'] = $this->total_shipping_tax_excl;
                }

                $row['total_amount'] = Tools::ps_round($row['total_amount'], _PS_PRICE_COMPUTE_PRECISION_, $this->order->round_mode);
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
                $row['total_tax_excl'] = Tools::ps_round($row['total_amount'] / $row['rate'] * 100, _PS_PRICE_COMPUTE_PRECISION_, $this->order->round_mode);
                $sum_of_tax_bases += $row['total_tax_excl'];
            } else {
                $row['total_tax_excl'] = $this->order->total_wrapping_tax_excl;
            }

            $row['total_amount'] = Tools::ps_round($row['total_amount'], _PS_PRICE_COMPUTE_PRECISION_, $this->order->round_mode);
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
        $objCommissionInvoice = new MpCommissionInvoiceHistory();
        $idOrderInvoice = $objCommissionInvoice->getOrderInvoiceNumber($this->orders, $this->id_seller);

        $format = '%1$s%2$06d';
        $objConfig = new MpSellerInvoiceConfig();
        $sellerConfig = $objConfig->getSellerInvoiceConfig($this->id_seller, $this->order->id_lang);
        if ($sellerConfig && $sellerConfig['invoice_prefix']) {
            $sellerInvoicePrefix = $sellerConfig['invoice_prefix'];
        } else {
            $sellerInvoicePrefix = Configuration::get('PS_INVOICE_PREFIX', $this->order->id_lang);
        }
        if ($extention) {
            return sprintf(
                $format,
                $sellerInvoicePrefix,
                $idOrderInvoice['invoice_number'],
                date('Y', strtotime($this->order->date_add))
            ).'.pdf';
        } else {
            return sprintf(
                $format,
                $sellerInvoicePrefix,
                $idOrderInvoice['invoice_number'],
                date('Y', strtotime($this->order->date_add))
            );
        }
    }

    public function getTemplate($template)
    {
        $path = _PS_MODULE_DIR_.'mpsellerinvoice/pdf/'.$template.'.tpl';

        return $path;
    }

    public function assignCommonHeaderDataForSeller()
    {
        $id_shop = (int) $this->context->shop->id;
        if ($this->id_seller == 0) {
            $shop_name = Configuration::get('PS_SHOP_NAME');
        } else {
            $shop_name = $this->obj_seller->shop_name;
        }
        if ($this->adminInvoice) {
            $path_logo = $this->getLogo();
        } else {
            $path_logo = $this->getShopImageLink();
        }

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
            $this->context->smarty->assign(array(
                'city' => $this->obj_seller->city,
                ));
        }
        if (isset($this->obj_seller->id_country)) {
            $country = new Country($this->obj_seller->id_country, $this->context->language->id);
            $this->context->smarty->assign(array(
                'country' => $country->name, ));
        }
        $this->context->smarty->assign(array(
            'logo_path' => $path_logo,
            'img_ps_dir' => 'http://'.Tools::getMediaServer(_PS_IMG_)._PS_IMG_,
            'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
            'date' => $this->order->date_upd,
            'seller_invoice_title' => $this->getFilename(false),
            'shop_name' => $shop_name,
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
        $id_mp_seller = $this->id_seller;
        if (!$id_mp_seller) {
            $logo = '';
            $id_shop = (int) $this->context->shop->id;
            if (Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop))) {
                $logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop);
            } elseif (Configuration::get('PS_LOGO', null, null, $id_shop) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $id_shop))) {
                $logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $id_shop);
            }

            return $logo;
        }

        $shopimage = $this->id_seller.'-'.$this->obj_seller->shop_name_unique.'.jpg';
        if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$shopimage)) {
            return _PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$shopimage;
        } else {
            return _PS_MODULE_DIR_.'marketplace/views/img/shop_img/defaultshopimage.jpg';
        }
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

    public function saveAdminCommissionInvoiceRecord($isSendToSeller)
    {
        $objInvoiceRecord = new MpCommissionInvoiceHistory();
        if ($isExist = $objInvoiceRecord->getOrderInvoiceNumber($this->orders, $this->id_seller)) {
            $objInvoiceRecord = new MpCommissionInvoiceHistory($isExist['id']);
        } else {
            $lastInsertRow = $objInvoiceRecord->getLastRowByIdSeller($this->id_seller);
            if ($lastInsertRow) {
                ++$lastInsertRow;
            } else {
                $lastInsertRow = 1;
            }
            $objInvoiceRecord->invoice_number = $lastInsertRow;
        }
        $objInvoiceConfig = new MpSellerInvoiceConfig();
        $sellerConfig = $objInvoiceConfig->isSellerInvoiceConfigExist($this->id_seller);
        if ($sellerConfig) {
            $from = date('Y-m-d H:i:m');
            $to = date('Y-m-d H:i:m');
            $objInvoiceRecord->invoice_based = (int) $sellerConfig['invoice_based'];
            $objInvoiceRecord->from = $from;
            $objInvoiceRecord->to = $to;
            $objInvoiceRecord->orders = $this->orders;
            if ($isSendToSeller) {
                $objInvoiceRecord->is_send_to_seller = 1;
            }
            $objInvoiceRecord->id_seller = (int) $this->id_seller;
            $objInvoiceRecord->save();
        }
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
