<?php
/**
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class SplitPayment extends CartRule
{
    public function __construct()
    {
        $this->context = Context::getContext();
    }

    public function sellerWiseSplitedAmount($params, $save_voucher = false)
    {
        $order = $params['order'];
        $cart = $params['cart'];

        $id_order = $order->id;

        $voucher_all_info = array();
        $order_product_details = array();
        $order_product_details['is_gift_product'] = 0;

        $cart_rules = $cart->getCartRules();
        $cart_products = $cart->getProducts();

        if ($cart_rules) {
            $voucher_all_info = $this->calculateVoucher($cart_rules, $cart_products); //get information of all vouchers
            $applied_voucher_list_info = $voucher_all_info['order']; //voucher for order
            $applied_voucher_fixed_price_info = $voucher_all_info['fixed_price']; // specific amount voucher
            $gift_product_list_info = $voucher_all_info['gift_product']; //all git product info
            $cheapest_product = $voucher_all_info['cheapest_product']; //cheapest product voucher info
        }

        $cart_products = $order->getProducts();
        foreach ($cart_products as $cp_key => $product) {
            $cart_products[$cp_key]['id_product'] = $product['product_id'];
            $cart_products[$cp_key]['id_product_attribute'] = $product['product_attribute_id'];
            $cart_products[$cp_key]['cart_quantity'] = $product['product_quantity'];
            $cart_products[$cp_key]['price_wt'] = $product['unit_price_tax_incl'];
            $cart_products[$cp_key]['price'] = $product['unit_price_tax_excl'];
        }

        // get cart order products, customer, seller details
        $obj_mpsellerorderdetails = new MarketplaceSellerOrderDetails();
        $seller_cart_products = $obj_mpsellerorderdetails->getSellerOrderedProductDetails($id_order);
        if ($seller_cart_products) {
            $obj_mpcommission = new MarketplaceCommision();

            $seller_product = array();
            $order_total_weight = 0;
            $order_total_products = 0;
            $order_total_price = 0;
            $conversion_rate = MarketplaceSellerOrders::getCurrencyConversionRate($this->context->currency->id, Configuration::get('PS_CURRENCY_DEFAULT'));
            $show_voucher_details = array();
            foreach ($seller_cart_products as $product) {
                if (!empty($voucher_all_info)) {
                    $order_product_details = $this->checkGiftProduct($product['product_id'], $product['product_attribute_id'], $product['product_quantity'], $gift_product_list_info); // check if gift product availble in order
                    
                    $product['product_quantity'] = $order_product_details['quantity']; // ordered product quantity excluding gift product

                    if (!$order_product_details['is_gift_product']) {
                        $reductionDetails = $this->calculateReductionPercentage($product['product_id'], $product['product_attribute_id'], $applied_voucher_list_info); // calculate reduction order reduction percentage
                        $product_price_percentage = 100 - $reductionDetails['price'];
                        $product_tax_percentage = 100 - $reductionDetails['tax'];

                        $product_price = $this->getCartProductPriceByIdProductAndIdAttribute($cart_products, $product['product_id'], $product['product_attribute_id']);
                        $tax_amount = (((($product_price['price_ti'] - $product_price['price_te']) * $product['product_quantity']) * $product_tax_percentage) / 100); // calculate product tax
                        $mp_product_price_te = ((($product_price['price_te'] * $product['product_quantity']) * $product_price_percentage) / 100); // calculate product price
                        if ($save_voucher) {
                            $voucher_value = (float) Tools::ps_round((float) ($product_price['price_ti'] * $product['product_quantity']), 2);
                            $show_voucher_details = $this->calculateReductionPercentageForShowingVoucher($product['product_id'], $product['product_attribute_id'], $applied_voucher_list_info, $voucher_value, $id_order, $product['id_seller'], $show_voucher_details);
                        }

                        if (array_key_exists($product['product_id'], $applied_voucher_fixed_price_info)) {
                            // if voucher for specific product of fixed amount than that amount is deducted from total product price and tax

                            foreach ($applied_voucher_fixed_price_info[$product['product_id']] as $key => $value) {
                                $tax_amount -= $value['reduction_tax'];
                                $mp_product_price_te -= $value['reduction_amount'];

                                if (array_key_exists($product['id_seller'], $show_voucher_details) && array_key_exists($value['ps_id_cart_rule'], $show_voucher_details[$product['id_seller']])) {
                                        $show_voucher_details[$product['id_seller']][$value['ps_id_cart_rule']]['voucher_value'] += $value['reduction_amount'] + $value['reduction_tax'];
                                } else {
                                    $show_voucher_details[$product['id_seller']][$value['ps_id_cart_rule']]['voucher_value'] = $value['reduction_amount'] + $value['reduction_tax'];
                                    $show_voucher_details[$product['id_seller']][$value['ps_id_cart_rule']]['id_order'] = $id_order;
                                    $show_voucher_details[$product['id_seller']][$value['ps_id_cart_rule']]['voucher_name'] = $value['voucher_name'];
                                }
                            }
                        }

                        if (array_key_exists('id_product', $cheapest_product)) { // if voucher for cheapest product
                            if ($cheapest_product['id_product'] == $product['product_id'] && $cheapest_product['id_product_attribute'] == $product['product_attribute_id']) {
                                $product_price_of_cheapest_product = (($cheapest_product['discount_percentage'] * $product_price['price_te']) / 100);
                                $product_tax_of_cheapest_product = (($cheapest_product['discount_percentage'] * ($product_price['price_ti'] - $product_price['price_te'])) / 100);
                                
                                $mp_product_price_te -= $product_price_of_cheapest_product;
                                $tax_amount -= $product_tax_of_cheapest_product;
                                foreach ($cheapest_product['cheapest_voucher'] as $key => $value) {
                                    if (array_key_exists($product['id_seller'], $show_voucher_details) && array_key_exists($value['ps_id_cart_rule'], $show_voucher_details[$product['id_seller']])) {
                                        $show_voucher_details[$product['id_seller']][$value['ps_id_cart_rule']]['voucher_value'] += (float)($value['value'] * $product_price['price_ti']) / 100;
                                    } else {
                                        $show_voucher_details[$product['id_seller']][$value['ps_id_cart_rule']]['voucher_value'] = (float)($value['value'] * $product_price['price_ti']) / 100;
                                        $show_voucher_details[$product['id_seller']][$value['ps_id_cart_rule']]['id_order'] = $id_order;
                                        $show_voucher_details[$product['id_seller']][$value['ps_id_cart_rule']]['voucher_name'] = $value['voucher_name'];
                                    }
                                }
                            }
                        }

                        $product['total_price_tax_incl'] = $mp_product_price_te + $tax_amount;
                        $product['total_price_tax_excl'] = $mp_product_price_te;
                    }
                }

                if (!($voucher_all_info && $order_product_details['is_gift_product'])) {
                    $order_total_products += $product['product_quantity'];
                    $order_total_weight += ($product['product_weight'] * $product['product_quantity']);
                    $order_total_price += $product['total_price_tax_incl'];
                    if ($product['mp_id_product']) {
                        $commission_by_seller = $obj_mpcommission->getCommissionRateBySeller($product['id_customer']);
                        //apply global commission, if commission by particular seller not defined and if commission set to 0.00 no commission applied for this seller
                        if (!is_numeric($commission_by_seller)) {
                            if ($global_commission = Configuration::get('MP_GLOBAL_COMMISSION')) {
                                $commission_rate = $global_commission;
                            } else {
                                $commission_rate = 0;
                            }
                        } else {
                            $commission_rate = $commission_by_seller;
                        }

                        $admin_tax = 0;
                        $seller_tax = 0;
                        $admin_commission = 0;
                        $seller_amount = 0;
                        // create seller order commission details
                        $admin_commission = (($product['total_price_tax_excl']) * $commission_rate) / 100;

                        //create seller amount, the rest amount from 100 after seller commission
                        $seller_amount = (($product['total_price_tax_excl']) * (100 - $commission_rate)) / 100;

                        //Distribution of product tax
                        $total_tax = $product['total_price_tax_incl'] - $product['total_price_tax_excl'];
                        if (Configuration::get('MP_PRODUCT_TAX_DISTRIBUTION') == 'admin') {
                            $admin_tax = $total_tax;
                        } elseif (Configuration::get('MP_PRODUCT_TAX_DISTRIBUTION') == 'seller') {
                            $seller_tax = $total_tax;
                        } elseif (Configuration::get('MP_PRODUCT_TAX_DISTRIBUTION') == 'distribute_both') {
                            $admin_tax = ($total_tax * $commission_rate) / 100; //for ex: 10% to admin
                            $seller_tax = $total_tax - $admin_tax; //the rest 90% to seller
                        }
                        //Distribution of product tax close

                        $seller_product[$product['id_customer']]['product_list'][$product['id_ps_product']][$product['product_attribute_id']] = $product;
                        $seller_product[$product['id_customer']]['product_list'][$product['id_ps_product']][$product['product_attribute_id']]['admin_commission'] = $admin_commission;
                        $seller_product[$product['id_customer']]['product_list'][$product['id_ps_product']][$product['product_attribute_id']]['admin_tax'] = $admin_tax;
                        $seller_product[$product['id_customer']]['product_list'][$product['id_ps_product']][$product['product_attribute_id']]['seller_amount'] = $seller_amount;
                        $seller_product[$product['id_customer']]['product_list'][$product['id_ps_product']][$product['product_attribute_id']]['seller_tax'] = $seller_tax;
                        
                        $seller_product[$product['id_customer']]['seller_name'] = $product['seller_name'];
                        $seller_product[$product['id_customer']]['seller_email'] = $product['business_email'];
                        $seller_product[$product['id_customer']]['seller_default_lang_id'] = $product['default_lang'];

                        /* In seller_product array products are grouped by seller.
                            First index is seller's customer id, inside this array 'product_list' index have all product of currenct index seller, total_admin_commission have total admin commission of currenct index seller, total_admin_tax is total admin tax if current index seller
                        */
                        if (array_key_exists('total_admin_commission', $seller_product[$product['id_customer']])) {
                            $seller_product[$product['id_customer']]['total_admin_commission'] += ($admin_commission * $conversion_rate);
                            $seller_product[$product['id_customer']]['total_admin_tax'] += ($admin_tax * $conversion_rate);
                            $seller_product[$product['id_customer']]['total_seller_amount'] += ($seller_amount * $conversion_rate);
                            $seller_product[$product['id_customer']]['total_seller_tax'] += ($seller_tax * $conversion_rate);
                            $seller_product[$product['id_customer']]['total_earn_ti'] += ($product['total_price_tax_incl'] * $conversion_rate);
                            $seller_product[$product['id_customer']]['total_earn_te'] += ($product['total_price_tax_excl'] * $conversion_rate);
                            $seller_product[$product['id_customer']]['total_price_tax_incl'] += $product['total_price_tax_incl'];
                            $seller_product[$product['id_customer']]['total_product_weight'] += ($product['product_weight'] * $product['product_quantity']);
                            $seller_product[$product['id_customer']]['no_of_products'] += $product['product_quantity'];
                        } else {
                            $seller_product[$product['id_customer']]['total_admin_commission'] = ($admin_commission * $conversion_rate);
                            $seller_product[$product['id_customer']]['total_admin_tax'] = ($admin_tax * $conversion_rate);
                            $seller_product[$product['id_customer']]['total_seller_amount'] = ($seller_amount * $conversion_rate);
                            $seller_product[$product['id_customer']]['total_seller_tax'] = ($seller_tax * $conversion_rate);
                            $seller_product[$product['id_customer']]['total_earn_ti'] = ($product['total_price_tax_incl'] * $conversion_rate);
                            $seller_product[$product['id_customer']]['total_earn_te'] = ($product['total_price_tax_excl'] * $conversion_rate);
                            $seller_product[$product['id_customer']]['total_price_tax_incl'] = $product['total_price_tax_incl'];
                            $seller_product[$product['id_customer']]['total_product_weight'] = ($product['product_weight'] * $product['product_quantity']);
                            $seller_product[$product['id_customer']]['no_of_products'] = $product['product_quantity'];
                        }
                    } else {
                        if (array_key_exists('admin', $seller_product)) {
                            $seller_product['admin']['total_price_tax_incl'] += $product['total_price_tax_incl'];
                            $seller_product['admin']['total_product_weight'] += ($product['product_weight'] * $product['product_quantity']);
                        } else {
                            $seller_product['admin']['total_price_tax_incl'] = $product['total_price_tax_incl'];
                            $seller_product['admin']['total_product_weight'] = ($product['product_weight'] * $product['product_quantity']);
                        }
                    }
                }

                // if gift product then reduce the quantity of that product
                if (!empty($voucher_all_info) && !empty($gift_product_list_info)) {
                    if (isset($gift_product_list_info[$product['product_id']])) {
                        if (isset($gift_product_list_info[$product['product_id']][$product['product_attribute_id']])) {
                            $obj_mp_product = new SellerProductDetail();
                            $mp_product_detail = $obj_mp_product->getMpProductDetailsByPsProductId($product['product_id']);
                            if ($mp_product_detail) {
                                $obj_mp_product = new SellerProductDetail($mp_product_detail['id']);
                                $obj_mp_product->quantity -= $gift_product_list_info[$product['product_id']][$product['product_attribute_id']];
                                $obj_mp_product->save();
                            }
                        }
                    }
                }
            }
            if ($save_voucher && $show_voucher_details) {
                foreach ($show_voucher_details as $key => $value) {
                    if ($key) {
                        $seller_id = $key;
                        foreach ($value as $val) {
                            $obj_mp_order_voucher = new MarketplaceOrderVoucherDetails();
                            $obj_mp_order_voucher->order_id = $id_order;
                            $obj_mp_order_voucher->seller_id = $seller_id;
                            $obj_mp_order_voucher->voucher_name = $val['voucher_name'];
                            $obj_mp_order_voucher->voucher_value = $val['voucher_value'];
                            $obj_mp_order_voucher->save();
                        }
                    }
                }
            }
            return $seller_product;
        } else {
            return false;
        }
    }

    public function paymentGatewaySplitedAmount($cart_rules = false, $cart_products = false)
    {
        if (!$cart_rules) {
            $cart_rules = $this->context->cart->getCartRules();
        }

        if (!$cart_products) {
            $cart_products = $this->context->cart->getProducts();
        }

        //$id_order = Order::getOrderByCartId((int)$this->context->cart->id);

        $voucher_all_info = $this->calculateVoucher($cart_rules, $cart_products); //get information of all vouchers
        //ddd($voucher_all_info);
        $applied_voucher_list_info = $voucher_all_info['order']; //voucher for order
        $applied_voucher_fixed_price_info = $voucher_all_info['fixed_price']; // specific amount voucher
        $gift_product_list_info = $voucher_all_info['gift_product']; //all git product info
        $cheapest_product = $voucher_all_info['cheapest_product']; //cheapest product voucher info
        $is_free_shipping_on_applied_voucher = $voucher_all_info['free_shipping']; //is free shipping in any voucher

        $cust_comm = array();
        $cart_product_list = array(); // this array contain product details after voucher processing
        foreach ($cart_products as $details) {
            $cart_product_id = $details['id_product'];
            $cart_product_id_attribute = $details['id_product_attribute'];
            $cart_product_details = $this->checkGiftProduct($cart_product_id, $cart_product_id_attribute, $details['quantity'], $gift_product_list_info); // check is git product availble in cart
            $cart_product_quantity = $cart_product_details['quantity']; // cart product quantity after gift product quantity reduction

            if (!$cart_product_details['is_gift_product']) {
                $reductionDetails = $this->calculateReductionPercentage($cart_product_id, $cart_product_id_attribute, $applied_voucher_list_info); // calculate reduction order reduction percentage
                $product_price_percentage = 100 - $reductionDetails['price'];
                $product_tax_percentage = 100 - $reductionDetails['tax'];
                $mkt_place_seller_pd = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_product` WHERE `id_ps_product`='.(int) $details['id_product'].''); // check is mp product
                if ($mkt_place_seller_pd['id'] > 0) {
                    $mkt_place_shop = Db::getInstance()->getRow('SELECT *  FROM `'._DB_PREFIX_.'marketplace_seller_info` WHERE `id`='.(int) $mkt_place_seller_pd['id_seller'].'');

                    $mkt_place_commision = Db::getInstance()->getValue('SELECT `commision`  FROM `'._DB_PREFIX_.'marketplace_commision` WHERE `seller_customer_id`='.(int) $mkt_place_shop['seller_customer_id'].'');

                    // calculate commission
                    if (!is_numeric($mkt_place_commision)) {
                        if ($global_commission = Configuration::get('MP_GLOBAL_COMMISSION')) {
                            $admincommision = $global_commission;
                        } else {
                            $admincommision = 0;
                        }
                        
                        $sellercommision = (100 - $admincommision);
                    } else {
                        $admincommision = $mkt_place_commision;
                        $sellercommision = (100 - $mkt_place_commision);
                    }
                    
                    // get product price
                    $product_price = $this->getCartProductPriceByIdProductAndIdAttribute($cart_products, $cart_product_id, $cart_product_id_attribute);
                    $tax_amount = (((($product_price['price_ti'] - $product_price['price_te']) * $cart_product_quantity) * $product_tax_percentage) / 100); // calculate product tax
                    $mp_product_price_te = ((($product_price['price_te'] * $cart_product_quantity) * $product_price_percentage) / 100); // calculate product price
                    /*if ($save_voucher) {
                        $voucher_value = (float) Tools::ps_round((float) ($product_price['price_te'] * $cart_product_quantity), 2);
                        $this->calculateReductionPercentageForShowingVoucher($cart_product_id, $cart_product_id_attribute, $applied_voucher_list_info, $voucher_value, $id_order, $mkt_place_seller_pd['id_seller']);
                    }*/

                    if (array_key_exists($cart_product_id, $applied_voucher_fixed_price_info)) {
                        // if voucher for specific product of fixed amount than that amount is deducted from total product price and tax
                        foreach ($applied_voucher_fixed_price_info[$cart_product_id] as $value) {
                            $tax_amount -= $value['reduction_tax'];
                            $mp_product_price_te -= $value['reduction_amount'];
                            /*if ($save_voucher) {
                                $obj_mp_order_voucher = new MarketplaceOrderVoucherDetails();
                                $obj_mp_order_voucher->order_id = $id_order;
                                $obj_mp_order_voucher->seller_id = $mkt_place_seller_pd['id_seller'];
                                $obj_mp_order_voucher->voucher_name = $value['voucher_name'];
                                $obj_mp_order_voucher->voucher_value = $value['reduction_amount'];
                                $obj_mp_order_voucher->save();
                            }*/
                        }
                    }

                    if (array_key_exists('id_product', $cheapest_product)) { // if voucher for cheapest product
                        if ($cheapest_product['id_product'] == $cart_product_id && $cheapest_product['id_product_attribute'] == $cart_product_id_attribute) {
                            $product_price_of_cheapest_product = (($cheapest_product['discount_percentage'] * $product_price['price_te']) / 100);
                            $product_tax_of_cheapest_product = (($cheapest_product['discount_percentage'] * ($product_price['price_ti'] - $product_price['price_te'])) / 100);

                            $mp_product_price_te -= $product_price_of_cheapest_product;
                            $tax_amount -= $product_tax_of_cheapest_product;
                            /*if ($save_voucher) {
                                foreach ($cheapest_product['cheapest_voucher'] as $key => $value) {
                                    $obj_mp_order_voucher = new MarketplaceOrderVoucherDetails();
                                    $obj_mp_order_voucher->order_id = $id_order;
                                    $obj_mp_order_voucher->seller_id = $mkt_place_seller_pd['id_seller'];
                                    $obj_mp_order_voucher->voucher_name = $value['voucher_name'];
                                    $obj_mp_order_voucher->voucher_value = (float)($value['value'] * $product_price['price_te']) / 100;
                                    $obj_mp_order_voucher->save();
                                }
                            }*/
                        }
                    }

                    $seller_commision_amt = (float) Tools::ps_round((float) (($sellercommision * $mp_product_price_te) / 100), 2);
                    $admin_commision_amt = (float) Tools::ps_round(($mp_product_price_te - $seller_commision_amt), 2);

                    $cart_product_list[$details['id_product'].'_'.$details['id_product_attribute']]['weight'] = ($details['weight'] * $cart_product_quantity);
                    $cart_product_list[$details['id_product'].'_'.$details['id_product_attribute']]['price'] = ($mp_product_price_te+$tax_amount);
                    $cart_product_list[$details['id_product'].'_'.$details['id_product_attribute']]['qty'] = $cart_product_quantity;
                    $cart_product_list[$details['id_product'].'_'.$details['id_product_attribute']]['seller_customer_id'] = $mkt_place_shop['seller_customer_id'];

                    // commission calculation
                    if (array_key_exists('admin', $cust_comm)) {
                        if (array_key_exists($mkt_place_shop['seller_customer_id'], $cust_comm['admin'])) {
                            $cust_comm['admin'][$mkt_place_shop['seller_customer_id']] += $admin_commision_amt;
                        } else {
                            $cust_comm['admin'][$mkt_place_shop['seller_customer_id']] = $admin_commision_amt;
                        }
                    } else {
                        $cust_comm['admin'][$mkt_place_shop['seller_customer_id']] = $admin_commision_amt;
                    }


                    if (array_key_exists($mkt_place_shop['seller_customer_id'], $cust_comm)) {
                        $cust_comm[$mkt_place_shop['seller_customer_id']] += $seller_commision_amt;
                    } else {
                        $cust_comm[$mkt_place_shop['seller_customer_id']] = $seller_commision_amt;
                    }

                    // tax distribution
                    if (Configuration::get('MP_PRODUCT_TAX_DISTRIBUTION') == 'admin') {
                        $cust_comm['admin'][$mkt_place_shop['seller_customer_id']] += $tax_amount;
                    } elseif (Configuration::get('MP_PRODUCT_TAX_DISTRIBUTION') == 'seller') {
                        $cust_comm[$mkt_place_shop['seller_customer_id']] += $tax_amount;
                    } elseif (Configuration::get('MP_PRODUCT_TAX_DISTRIBUTION') == 'distribute_both') {
                        $commision_to_seller = (($sellercommision * $tax_amount) / 100);
                        $commision_to_admin = $tax_amount - $commision_to_seller;

                        $cust_comm['admin'][$mkt_place_shop['seller_customer_id']] += $commision_to_admin;
                        $cust_comm[$mkt_place_shop['seller_customer_id']] += $commision_to_seller;
                    }
                } else { // admin product
                    $product_price = $this->getCartProductPriceByIdProductAndIdAttribute($cart_products, $cart_product_id, $cart_product_id_attribute);
                    $tax_amount = (((($product_price['price_ti'] - $product_price['price_te']) * $cart_product_quantity) * $product_tax_percentage) / 100);
                    $product_price_ti = ((($product_price['price_te'] * $cart_product_quantity) * $product_price_percentage) / 100);

                    if (array_key_exists($cart_product_id, $applied_voucher_fixed_price_info)) {
                        $tax_amount -= $applied_voucher_fixed_price_info[$cart_product_id]['reduction_tax'];
                        $product_price_ti -= $applied_voucher_fixed_price_info[$cart_product_id]['reduction_amount'];
                    }

                    if (array_key_exists('id_product', $cheapest_product)) {
                        if ($cheapest_product['id_product'] == $cart_product_id && $cheapest_product['id_product_attribute'] == $cart_product_id_attribute) {
                            $product_price_of_cheapest_product = (($cheapest_product['discount_percentage'] * $product_price['price_te']) / 100);
                            $product_tax_of_cheapest_product = (($cheapest_product['discount_percentage'] * ($product_price['price_ti'] - $product_price['price_te'])) / 100);

                            $product_price_ti -= $product_price_of_cheapest_product;
                            $tax_amount -= $product_tax_of_cheapest_product;
                        }
                    }
                    $product_price_ti += $tax_amount;
                    if (array_key_exists('admin', $cust_comm)) {
                        if (array_key_exists('own', $cust_comm['admin'])) {
                            $cust_comm['admin']['own'] += $product_price_ti;
                        } else {
                            $cust_comm['admin']['own'] = $product_price_ti;
                        }
                    } else {
                        $cust_comm['admin']['own'] = $product_price_ti;
                    }
                    
                    $cart_product_list[$details['id_product'].'_'.$details['id_product_attribute']]['weight'] = ($details['weight'] * $cart_product_quantity);
                    $cart_product_list[$details['id_product'].'_'.$details['id_product_attribute']]['price'] = $product_price_ti;
                    $cart_product_list[$details['id_product'].'_'.$details['id_product_attribute']]['qty'] = $cart_product_quantity;
                    $cart_product_list[$details['id_product'].'_'.$details['id_product_attribute']]['seller_customer_id'] = 'admin';
                }
            }
        }
        // check is free shipping in any voucher
        if (!array_key_exists('own', $cust_comm['admin'])) {
            $cust_comm['admin']['own'] = 0;
        }
        if (!$is_free_shipping_on_applied_voucher) {
            $cust_comm['admin']['own'] += $this->context->cart->getTotalShippingCost();
        }
        return $cust_comm;
    }

    public function calculateVoucher($cart_rules, $cart_products)
    {
        // calculate all voucher's and their type
        $voucher_all_info = array();
        $applied_voucher_list_info = array();
        $applied_voucher_fixed_price_info = array();
        $cheapest_product = array();
        $is_free_shipping_on_applied_voucher = false;

        $gift_info = $this->getGiftProducts($cart_rules);
        $gift_product_list_info = $gift_info['gift_product_list'];
        $is_free_shipping_on_applied_voucher = $gift_info['free_shipping'];
        $i = 0;
        $j = 0;
        $l = 0;
        $m = 0;
        foreach ($cart_rules as $key => $cart_rule) {
            $obj_cart_rule = new CartRule($cart_rule['id_cart_rule']);
            if ((float) $obj_cart_rule->reduction_amount) { //voucher is created as fixed amount
                $cart_rule_reduction_amount = $obj_cart_rule->reduction_amount;
                if ($this->context->cart->id_currency != $obj_cart_rule->reduction_currency) {  // if voucher amount currency and cart currency are different
                    $voucherCurrency = new Currency($obj_cart_rule->reduction_currency);

                    // First we convert the voucher value to the default currency
                    if ($cart_rule_reduction_amount == 0 || $voucherCurrency->conversion_rate == 0) {
                        $cart_rule_reduction_amount = 0;
                    } else {
                        $cart_rule_reduction_amount /= $voucherCurrency->conversion_rate;
                    }

                    // Then we convert the voucher value in the default currency into the cart currency
                    $cart_rule_reduction_amount *= $this->context->currency->conversion_rate;
                    $cart_rule_reduction_amount = Tools::ps_round($cart_rule_reduction_amount);
                }

                $product_id = $obj_cart_rule->reduction_product;
                if ($product_id > 0) { // voucher for specific product
                    $product_price = $this->getCartProductPriceByIdProduct($cart_products, $product_id);
                    $product_price_ti = $product_price['price_ti'];
                    $product_price_te = $product_price['price_te'];
                    $product_vat_amount = $product_price_ti - $product_price_te;

                    if ($product_vat_amount == 0 || $product_price_te == 0) {
                        $product_vat_rate = 0;
                    } else {
                        $product_vat_rate = $product_vat_amount / $product_price_te;
                    }
                    $product_vat = $product_vat_rate * $cart_rule_reduction_amount;

                    if ($obj_cart_rule->reduction_tax) {
                        $reduction_amount = $cart_rule_reduction_amount - $product_vat;
                    } else {
                        $reduction_amount = $cart_rule_reduction_amount;
                    }
                    $applied_voucher_fixed_price_info[$product_id][$l]['reduction_tax'] = $product_vat;
                    $applied_voucher_fixed_price_info[$product_id][$l]['reduction_amount'] = $reduction_amount;
                    $applied_voucher_fixed_price_info[$product_id][$l]['ps_id_cart_rule'] = $cart_rule['id_cart_rule'];
                    $applied_voucher_fixed_price_info[$product_id][$l]['voucher_name'] = $cart_rule['name'];
                    $applied_voucher_fixed_price_info[$product_id][$l]['voucher_description'] = $cart_rule['description'];
                    $l++;
                } elseif ($product_id == 0) { // voucher for order without shipping
                    $cart_amount = $this->getOrderTotalWithOutGiftProductPrice($cart_products, $gift_product_list_info);
                    $cart_amount_ti = $cart_amount['ti'];
                    $cart_amount_te = $cart_amount['te'];
                    $cart_vat_amount = $cart_amount_ti - $cart_amount_te;

                    if ($cart_vat_amount == 0 || $cart_amount_te == 0) {
                        $cart_average_vat_rate = 0;
                    } else {
                        $cart_average_vat_rate = Tools::ps_round($cart_vat_amount / $cart_amount_te, 3);
                    }

                    $cart_rule_vat_amount = $cart_average_vat_rate * $cart_rule_reduction_amount;

                    if ($obj_cart_rule->reduction_tax) {
                        $reduction_amount = $cart_rule_reduction_amount - $cart_rule_vat_amount;
                    } else {
                        $reduction_amount = $cart_rule_reduction_amount;
                    }

                    if ($cart_vat_amount && $cart_rule_vat_amount) {
                        $applied_voucher_list_info['order'][$i]['reduction_tax'] = (($cart_rule_vat_amount * 100) / $cart_vat_amount);
                    } else {
                        $applied_voucher_list_info['order'][$i]['reduction_tax'] = 0;
                    }

                    if ($reduction_amount > $cart_amount_te) {
                        $this->errors[] = Tools::displayError('Reduction amount is greater then cart amount.');
                    }

                    $applied_voucher_list_info['order'][$i]['reduction_percent'] = (($reduction_amount * 100) / $cart_amount_te);
                    $applied_voucher_list_info['order'][$i]['ps_id_cart_rule'] = $cart_rule['id_cart_rule'];
                    $applied_voucher_list_info['order'][$i]['voucher_name'] = $cart_rule['name'];
                    $applied_voucher_list_info['order'][$i]['voucher_description'] = $cart_rule['description'];
                    $i++;
                }
            } elseif ((float) $obj_cart_rule->reduction_percent) { //voucher is created as percentage
                $reduction_percent = $obj_cart_rule->reduction_percent;
                $voucher_type = (int) $obj_cart_rule->reduction_product;
                if ($voucher_type > 0) { // voucher for specific product and voucher_type is product id of that product

                    if (array_key_exists($voucher_type, $applied_voucher_list_info)) {
                        $applied_voucher_list_info[$voucher_type]['all_attr'][$j]['value'] = $reduction_percent;
                    } else {
                        $applied_voucher_list_info[$voucher_type]['all_attr'][$j]['value'] = $reduction_percent;
                    }
                    $applied_voucher_list_info[$voucher_type]['all_attr'][$j]['ps_id_cart_rule'] = $cart_rule['id_cart_rule'];
                    $applied_voucher_list_info[$voucher_type]['all_attr'][$j]['voucher_name'] = $cart_rule['name'];
                    $applied_voucher_list_info[$voucher_type]['all_attr'][$j]['voucher_description'] = $cart_rule['description'];
                    $j++;
                } elseif ($voucher_type == 0) { // voucher for order without shipping

                    $applied_voucher_list_info['order'][$i]['reduction_tax'] = $reduction_percent;
                    $applied_voucher_list_info['order'][$i]['reduction_percent'] = $reduction_percent;
                    $applied_voucher_list_info['order'][$i]['ps_id_cart_rule'] = $cart_rule['id_cart_rule'];
                    $applied_voucher_list_info['order'][$i]['voucher_name'] = $cart_rule['name'];
                    $applied_voucher_list_info['order'][$i]['voucher_description'] = $cart_rule['description'];
                    $i++;
                } elseif ($voucher_type == -1) { // vaucher for cart cheapest product
                    $min_product_price = $cart_products[0]['price_wt'];
                    $min_product_id = $cart_products[0]['id_product'];
                    $min_product_id_attribute = $cart_products[0]['id_product_attribute'];
                    foreach ($cart_products as $cart_product) {
                        $product_price_wt = $cart_product['price_wt'];
                        if ($product_price_wt < $min_product_price) {
                            $min_product_price = $product_price_wt;
                            $min_product_id = $cart_product['id_product'];
                            $min_product_id_attribute = $cart_product['id_product_attribute'];
                        }
                    }

                    if ($min_product_id != 0) {
                        $cheapest_product['id_product'] = $min_product_id;
                        $cheapest_product['id_product_attribute'] = $min_product_id_attribute;
                        if (array_key_exists('discount_percentage', $cheapest_product)) {
                            $cheapest_product['discount_percentage'] += $reduction_percent;
                        } else {
                            $cheapest_product['discount_percentage'] = $reduction_percent;
                        }
                        $cheapest_product['cheapest_voucher'][$m]['value'] = $reduction_percent;
                        $cheapest_product['cheapest_voucher'][$m]['ps_id_cart_rule'] = $cart_rule['id_cart_rule'];
                        $cheapest_product['cheapest_voucher'][$m]['voucher_name'] = $cart_rule['name'];
                        $cheapest_product['cheapest_voucher'][$m]['voucher_description'] = $cart_rule['description'];
                        $m++;
                        //}
                    }
                } elseif ($voucher_type == -2) {  // vaucher for selected product
                    $selected_products = $obj_cart_rule->checkProductRestrictions($this->context, true);
                    if (is_array($selected_products)) {
                        $k = 0;
                        foreach ($cart_products as $product) {
                            if ((in_array($product['id_product'].'-'.$product['id_product_attribute'], $selected_products) || in_array($product['id_product'].'-0', $selected_products))) {
                                if (array_key_exists($product['id_product'], $applied_voucher_list_info)) {
                                    $applied_voucher_list_info[$product['id_product']][$product['id_product_attribute']][$k]['value'] = $reduction_percent;
                                    $applied_voucher_list_info[$product['id_product']][$product['id_product_attribute']][$k]['ps_id_cart_rule'] = $cart_rule['id_cart_rule'];
                                    $applied_voucher_list_info[$product['id_product']][$product['id_product_attribute']][$k]['voucher_name'] = $cart_rule['name'];
                                    $applied_voucher_list_info[$product['id_product']][$product['id_product_attribute']][$k]['voucher_description'] = $cart_rule['description'];
                                } else {
                                    $applied_voucher_list_info[$product['id_product']]['all_attr'] = array();
                                    $applied_voucher_list_info[$product['id_product']][$product['id_product_attribute']][$k]['value'] = $reduction_percent;
                                    $applied_voucher_list_info[$product['id_product']][$product['id_product_attribute']][$k]['ps_id_cart_rule'] = $cart_rule['id_cart_rule'];
                                    $applied_voucher_list_info[$product['id_product']][$product['id_product_attribute']][$k]['voucher_name'] = $cart_rule['name'];
                                    $applied_voucher_list_info[$product['id_product']][$product['id_product_attribute']][$k]['voucher_description'] = $cart_rule['description'];
                                }
                                $k++;
                            }
                        }
                    }
                }
            }
        }
        $voucher_all_info['order'] = $applied_voucher_list_info;
        $voucher_all_info['fixed_price'] = $applied_voucher_fixed_price_info;
        $voucher_all_info['gift_product'] = $gift_product_list_info;
        $voucher_all_info['cheapest_product'] = $cheapest_product;
        $voucher_all_info['free_shipping'] = $is_free_shipping_on_applied_voucher;
        return $voucher_all_info;
    }

    public function getGiftProducts($cart_rules)
    {
        $is_free_shipping_on_applied_voucher = false;
        $gift_product_list_info = array();

        foreach ($cart_rules as $key => $cart_rule) {
            $obj_cart_rule = new CartRule($cart_rule['id_cart_rule']);
            if ($obj_cart_rule->gift_product != 0) {
                $product_id = $obj_cart_rule->gift_product;

                $product_id_attribute = $obj_cart_rule->gift_product_attribute;
                if (array_key_exists($product_id, $gift_product_list_info)) {
                    if (array_key_exists($product_id_attribute, $gift_product_list_info[$product_id])) {
                        $gift_product_list_info[$product_id][$product_id_attribute] += 1;
                    } else {
                        $gift_product_list_info[$product_id][$product_id_attribute] = 1;
                    }
                } else {
                    $gift_product_list_info[$product_id] = array();
                    if (array_key_exists($product_id_attribute, $gift_product_list_info[$product_id])) {
                        $gift_product_list_info[$product_id][$product_id_attribute] += 1;
                    } else {
                        $gift_product_list_info[$product_id][$product_id_attribute] = 1;
                    }
                }
            }

            if ($obj_cart_rule->free_shipping) {
                $is_free_shipping_on_applied_voucher = true;
            }
            unset($obj_cart_rule);
        }
        return array('gift_product_list' => $gift_product_list_info, 'free_shipping' => $is_free_shipping_on_applied_voucher);
    }

    public function checkGiftProduct($cart_product_id, $cart_product_id_attribute, $cart_product_quantity, $gift_product_list_info)
    {
        $cart_product_details = array();
        $cart_product_details['is_gift_product'] = false;
        if (array_key_exists($cart_product_id, $gift_product_list_info)) {
            if (array_key_exists($cart_product_id_attribute, $gift_product_list_info[$cart_product_id])) {
                if ($cart_product_quantity > $gift_product_list_info[$cart_product_id][$cart_product_id_attribute]) {
                    $cart_product_quantity = $cart_product_quantity - $gift_product_list_info[$cart_product_id][$cart_product_id_attribute];
                } else {
                    $cart_product_details['is_gift_product'] = true;
                }
            }
        }
        $cart_product_details['quantity'] = $cart_product_quantity;
        return $cart_product_details;
    }

    public function calculateReductionPercentage($cart_product_id, $cart_product_id_attribute, $applied_voucher_list_info)
    {
        $product_tax_reduction_percentage = 0;
        $product_price_reduction_percentage = 0;
        if (array_key_exists('order', $applied_voucher_list_info)) {
            foreach ($applied_voucher_list_info['order'] as $key => $value) {
                $product_tax_reduction_percentage += $value['reduction_tax'];
                $product_price_reduction_percentage += $value['reduction_percent'];
            }
        }

        if (array_key_exists($cart_product_id, $applied_voucher_list_info)) {
            if (array_key_exists('all_attr', $applied_voucher_list_info[$cart_product_id])) {
                foreach ($applied_voucher_list_info[$cart_product_id]['all_attr'] as $key => $value) {
                    $product_tax_reduction_percentage += $value['value'];
                    $product_price_reduction_percentage += $value['value'];
                }
            }

            if (array_key_exists($cart_product_id_attribute, $applied_voucher_list_info[$cart_product_id])) {
                foreach ($applied_voucher_list_info[$cart_product_id][$cart_product_id_attribute] as $key => $value) {
                    $product_tax_reduction_percentage += $value['value'];
                    $product_price_reduction_percentage += $value['value'];
                }
            }
        }
        return array('price' => $product_price_reduction_percentage, 'tax' => $product_tax_reduction_percentage);
    }

    public function calculateReductionPercentageForShowingVoucher($cart_product_id, $cart_product_id_attribute, $applied_voucher_list_info, $mp_product_price_ti, $id_order, $id_seller, $show_voucher_details)
    {
        if (array_key_exists('order', $applied_voucher_list_info)) {
            foreach ($applied_voucher_list_info['order'] as $key => $value) {
                if (array_key_exists($id_seller, $show_voucher_details) && array_key_exists($value['ps_id_cart_rule'], $show_voucher_details[$id_seller])) {
                    $show_voucher_details[$id_seller][$value['ps_id_cart_rule']]['voucher_value'] += (float) Tools::ps_round((float) (($mp_product_price_ti * $value['reduction_percent']) / 100), 2);
                } else {
                    $show_voucher_details[$id_seller][$value['ps_id_cart_rule']]['voucher_value'] = (float) Tools::ps_round((float) (($mp_product_price_ti * $value['reduction_percent']) / 100), 2);
                    $show_voucher_details[$id_seller][$value['ps_id_cart_rule']]['id_order'] = $id_order;
                    $show_voucher_details[$id_seller][$value['ps_id_cart_rule']]['voucher_name'] = $value['voucher_name'];
                }
            }
        }

        if (array_key_exists($cart_product_id, $applied_voucher_list_info)) {
            if (array_key_exists('all_attr', $applied_voucher_list_info[$cart_product_id])) {
                foreach ($applied_voucher_list_info[$cart_product_id]['all_attr'] as $key => $value) {
                    if (array_key_exists($id_seller, $show_voucher_details) && array_key_exists($value['ps_id_cart_rule'], $show_voucher_details[$id_seller])) {
                        $show_voucher_details[$id_seller][$value['ps_id_cart_rule']]['voucher_value'] += (float) Tools::ps_round((float) (($mp_product_price_ti * $value['value']) / 100), 2);
                    } else {
                        $show_voucher_details[$id_seller][$value['ps_id_cart_rule']]['voucher_value'] = (float) Tools::ps_round((float) (($mp_product_price_ti * $value['value']) / 100), 2);
                        $show_voucher_details[$id_seller][$value['ps_id_cart_rule']]['id_order'] = $id_order;
                        $show_voucher_details[$id_seller][$value['ps_id_cart_rule']]['voucher_name'] = $value['voucher_name'];
                    }
                }
            }

            if (array_key_exists($cart_product_id_attribute, $applied_voucher_list_info[$cart_product_id])) {
                foreach ($applied_voucher_list_info[$cart_product_id][$cart_product_id_attribute] as $key => $value) {
                    if (array_key_exists($id_seller, $show_voucher_details) && array_key_exists($value['ps_id_cart_rule'], $show_voucher_details[$id_seller])) {
                        $show_voucher_details[$id_seller][$value['ps_id_cart_rule']]['voucher_value'] += (float) Tools::ps_round((float) (($mp_product_price_ti * $value['value']) / 100), 2);
                    } else {
                        $show_voucher_details[$id_seller][$value['ps_id_cart_rule']]['voucher_value'] = (float) Tools::ps_round((float) (($mp_product_price_ti * $value['value']) / 100), 2);
                        $show_voucher_details[$id_seller][$value['ps_id_cart_rule']]['id_order'] = $id_order;
                        $show_voucher_details[$id_seller][$value['ps_id_cart_rule']]['voucher_name'] = $value['voucher_name'];
                    }
                }
            }
        }
        return $show_voucher_details;
    }

    public function getCartProductPriceByIdProduct($cart_products, $product_id)
    {
        $result = array();
        $result['price_ti'] = 0;
        $result['price_te'] = 0;
        foreach ($cart_products as $product) {
            if ($product['id_product'] == $product_id) {
                $result['price_ti'] = $product['price_wt'];
                $result['price_te'] = $product['price'];
            }
        }

        return $result;
    }

    public function getCartProductPriceByIdProductAndIdAttribute($cart_products, $cart_product_id, $cart_product_id_attribute)
    {
        $result = array();
        $result['price_ti'] = 0;
        $result['price_te'] = 0;
        foreach ($cart_products as $product) {
            if (($product['id_product'] == $cart_product_id) && ($product['id_product_attribute'] == $cart_product_id_attribute)) {
                $result['price_ti'] = $product['price_wt'];
                $result['price_te'] = $product['price'];
            }
        }

        return $result;
    }

    public function getOrderTotalWithOutGiftProductPrice($cart_products, $gift_product_list_info)
    {
        $order_total_amount = array();
        $order_total_amount['te'] = 0;
        $order_total_amount['ti'] = 0;
        foreach ($cart_products as $cart_product) {
            if (array_key_exists($cart_product['id_product'], $gift_product_list_info)) {
                if (array_key_exists($cart_product['id_product_attribute'], $gift_product_list_info[$cart_product['id_product']])) {
                    if ($gift_product_list_info[$cart_product['id_product']][$cart_product['id_product_attribute']] < $cart_product['cart_quantity']) {
                        $cart_quantity = $cart_product['cart_quantity'] - $gift_product_list_info[$cart_product['id_product']][$cart_product['id_product_attribute']];
                        $order_total_amount['ti'] += ($cart_product['price_wt'] * $cart_quantity);
                        $order_total_amount['te'] += ($cart_product['price'] * $cart_quantity);
                    }
                } else {
                    $order_total_amount['ti'] += ($cart_product['price_wt'] * $cart_product['cart_quantity']);
                    $order_total_amount['te'] += ($cart_product['price'] * $cart_product['cart_quantity']);
                }
            } else {
                $order_total_amount['ti'] += ($cart_product['price_wt'] * $cart_product['cart_quantity']);
                $order_total_amount['te'] += ($cart_product['price'] * $cart_product['cart_quantity']);
            }
        }
        return $order_total_amount;
    }
}
