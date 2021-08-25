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

class MpSellerMembershipMpMembershipPlansModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $id_customer = $this->context->customer->id;
        $id_lang = $this->context->language->id;
        $is_already_plan_in_cart = false;

        if ($id_customer) {
            if (Configuration::get('MP_SELLER_MEMBERSHIP_IS_CONFIGURED')) {
                $page_no = Tools::getValue('page');
                $free_plan = Tools::getValue('free_plan');
                $seller_info = WkMpSeller::getSellerDetailByCustomerId($id_customer);

                if ($free_plan) {
                    $this->context->smarty->assign('free_plan', $free_plan);
                }

                if (Tools::getValue('product_added')) {
                    $this->context->smarty->assign('product_added', Tools::getValue('product_added'));
                }

                if (!$page_no) {
                    $page_no = 1;
                }

                if ($seller_info && $seller_info['active']) {
                    $idSeller = (int) $seller_info['id_seller'];
                    $obj_mp_seller_plan = new MarketplaceSellerplan();
                    $free_plan_details = $obj_mp_seller_plan->getFreePlanBySellerId($idSeller);
                    $is_free_plan_display = Configuration::get('MP_SELLER_MEMBERSHIP_FREE_PLAN');
                    if (Tools::getValue('addtocart') == 1) {
                        $id_product = Tools::getValue('id_product');
                        $cart_product_list = $this->context->cart->getProducts();
                        if ($cart_product_list) {
                            foreach ($cart_product_list as $cart_product) {
                                if (MarketplaceSellerplan::getPlanByIdProduct($cart_product['id_product'])) {
                                    $is_already_plan_in_cart = true;
                                    break;
                                }
                            }
                        }

                        if ($is_already_plan_in_cart) {
                            $this->errors[] = $this->module->l('Membership plan already in cart, first remove that membership plan from your cart.', 'mpmembershipplans');
                        }

                        if (!count($this->errors)) {
                            $plan_info = MarketplaceSellerplan::getPlanInfoByIdProduct($id_product, $id_lang);
                            if ($plan_info) {
                                $active_products = 0;
                                $seller_products = MarketplaceSellerplanDetail::getSellerProduct($idSeller, true);
                                if ($seller_products) {
                                    $active_products = count($seller_products);
                                }

                                if ($active_products > $plan_info['num_products_allow']) {
                                    $this->errors[] = $this->module->l('Your active products are greater than the plan limit, first deactive some your products.');
                                }
                            }
                        }

                        if (!count($this->errors)) {
                            if (!$this->context->cart->id) {
                                $this->context->cart->add();
                                $this->context->cookie->id_cart = $this->context->cart->id;
                                $this->context->cookie->write();
                            }
                            $update_quantity = $this->context->cart->updateQty(1, $id_product);
                            if (!$update_quantity) {
                                $this->errors[] = $this->l('Membership plan already in cart, first remove that membership plan from your cart.', 'mpmembershipplans');
                            } else {
                                Tools::redirect($this->context->link->getModuleLink('mpsellermembership', 'mpmembershipplans', array('product_added' => 1)));
                            }
                        }
                    } elseif (Tools::getValue('freeplancart') == 1) {
                        if (!$free_plan_details && $is_free_plan_display) {
                            $no_of_days = Configuration::get('MP_SELLER_MEMBERSHIP_FREE_PLAN_DURATION') * Configuration::get('MP_SELLER_MEMBERSHIP_FREE_PLAN_DURATION_TYPE');
                            $total_product_allow = Configuration::get('MP_SELLER_MEMBERSHIP_FREE_PLAN_PRODUCTS');
                            $cart_product_list = $this->context->cart->getProducts();
                            if ($cart_product_list) {
                                foreach ($cart_product_list as $cart_product) {
                                    if (MarketplaceSellerplan::getPlanByIdProduct($cart_product['id_product'])) {
                                        $is_already_plan_in_cart = true;
                                        break;
                                    }
                                }
                            }

                            if ($is_already_plan_in_cart) {
                                $this->errors[] = $this->module->l('Membership already in cart, first remove that membership plan from your cart.');
                            }

                            if (!count($this->errors) && $total_product_allow) {
                                $active_products = 0;
                                $seller_products = MarketplaceSellerplanDetail::getSellerProduct($idSeller, true);
                                if ($seller_products) {
                                    $active_products = count($seller_products);
                                }

                                if ($active_products > $total_product_allow) {
                                    $this->errors[] = $this->module->l('Your active products are greater than the plan limit, first deactive some your products.');
                                }
                            }

                            if (!count($this->errors)) {
                                $duration = $no_of_days - 1;
                                if ($duration < 0) {
                                    $duration = 0;
                                }

                                $active_from = date('Y-m-d');
                                $expire_on = date('Y-m-d', strtotime(date('Y-m-d').' + '.$duration.' day'));
                                DB::getInstance()->update('wk_mp_seller', array('is_free_plan_taken' => 1), ' `id_seller` = '.$idSeller);

                                $obj_mp_seller_plan_detail = new MarketplaceSellerplanDetail();
                                $obj_mp_seller_plan_detail->updateCurrentPlanBySellerId($idSeller, 0);

                                $obj_mp_sellerplan_detail = new MarketplaceSellerplanDetail();
                                $obj_mp_sellerplan_detail->id_plan = 0;
                                $obj_mp_sellerplan_detail->id_order = 0;
                                $obj_mp_sellerplan_detail->mp_id_seller = $idSeller;
                                $obj_mp_sellerplan_detail->num_products_allow = $total_product_allow;
                                $obj_mp_sellerplan_detail->plan_duration = $no_of_days;
                                $obj_mp_sellerplan_detail->active_from = $active_from;
                                $obj_mp_sellerplan_detail->expire_on = $expire_on;
                                $obj_mp_sellerplan_detail->is_this_current_plan = 1;
                                $obj_mp_sellerplan_detail->active = 1;
                                $obj_mp_sellerplan_detail->save();
                                Tools::redirect($this->context->link->getModuleLink('mpsellermembership', 'mpmembershipplans', array('free_plan' => 1)));
                            }
                        }
                    }

                    if (!$free_plan_details && $is_free_plan_display) {
                        $free_no_products = Configuration::get('MP_SELLER_MEMBERSHIP_FREE_PLAN_PRODUCTS');
                        $free_no_of_days = Configuration::get('MP_SELLER_MEMBERSHIP_FREE_PLAN_DURATION') * Configuration::get('MP_SELLER_MEMBERSHIP_FREE_PLAN_DURATION_TYPE');
                        $this->context->smarty->assign('free_no_products', $free_no_products);
                        $this->context->smarty->assign('free_no_of_days', $free_no_of_days);
                    }

                    $all_active_plan = $obj_mp_seller_plan->getAllActivePlanOrderBySequenceNumber($id_lang);
                    if ($all_active_plan) {
                        $display_no_of_plan = Configuration::get('MP_SELLER_MEMBERSHIP_NO_OF_PLAN_DISPLAY');
                        $all_active_plan_count = count($all_active_plan);
                        $start_index = ($page_no - 1) * $display_no_of_plan;
                        if (!$free_plan_details && $is_free_plan_display && ($page_no != 1)) {
                            --$start_index;
                        }

                        if ($start_index < $all_active_plan_count) {
                            $last_index = $all_active_plan_count - $start_index;
                            if ($last_index > $display_no_of_plan) {
                                $last_index = $display_no_of_plan;
                            }

                            if (!$free_plan_details && $is_free_plan_display && ($page_no == 1) && $all_active_plan_count >= $display_no_of_plan) {
                                --$last_index;
                            }
                            $page_plan = array();
                            for ($i = 0; $i < $last_index; ++$i) {
                                $page_plan[] = $all_active_plan[$start_index + $i];
                            }

                            if (!$free_plan_details && $is_free_plan_display) {
                                ++$all_active_plan_count;
                            }

                            // for count total pages
                            $total_pages = (int) ($all_active_plan_count / $display_no_of_plan);
                            if (($all_active_plan_count % $display_no_of_plan) != 0) {
                                ++$total_pages;
                            }
                            $conversionRate = $this->getCartCurrencyRate(Configuration::get('PS_CURRENCY_DEFAULT'), $this->context->currency->id);
                            foreach ($page_plan as $key => $plan) {
                                if (file_exists(_PS_MODULE_DIR_.'mpsellermembership/views/img/'.$plan['id'].'.jpg')) {
                                    $page_plan[$key]['img'] = _MODULE_DIR_.'mpsellermembership/views/img/'.$plan['id'].'.jpg';
                                } else {
                                    $page_plan[$key]['img'] = _MODULE_DIR_.'mpsellermembership/views/img/default-plan.png';
                                }
                                $page_plan[$key]['plan_price'] = Tools::displayPrice($page_plan[$key]['plan_price'] * $conversionRate);
                            }

                            $this->context->smarty->assign(array(
                                'total_pages' => $total_pages,
                                'all_active_plan' => $page_plan,
                            ));
                        }
                    }
                    $this->context->smarty->assign('current_page', $page_no);
                } else {
                    $this->context->smarty->assign('no_perm', 1);
                }
            } else {
                $this->context->smarty->assign('not_config', 1);
            }
            $this->defineJSVars();
            $this->setTemplate('module:mpsellermembership/views/templates/front/mpmembershipplans.tpl');
        } else {
            Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('mpsellermembership', 'mpmembershipplans')));
        }
    }

    public function defineJSVars()
    {
        $jsVars = array(
            'conf_msg' => $this->module->l('Are you sure?', 'mpmembershipplans'),
        );

        Media::addJsDef($jsVars);
    }

    public function getCartCurrencyRate($id_currency_from, $id_currency_to)
    {
        $conversionRate = 1;
        if ($id_currency_from != $id_currency_to) {
            $currencyFrom = new Currency((int) $id_currency_from);
            $conversionRate /= $currencyFrom->conversion_rate;
            $currencyTo = new Currency((int) $id_currency_to);
            $conversionRate *= $currencyTo->conversion_rate;
        }

        return $conversionRate;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Marketplace', 'mpmembershipplans'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard')
        );

        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Seller Membership Plan', 'mpmembershipplans'),
            'url' => ''
        );

        return $breadcrumb;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->registerStylesheet('mpmembershipplans', 'modules/'.$this->module->name.'/views/css/mpmembershipplans.css');
        $this->registerJavascript('mpmembershipplans', 'modules/'.$this->module->name.'/views/js/mpmembershipplans.js');
    }
}
