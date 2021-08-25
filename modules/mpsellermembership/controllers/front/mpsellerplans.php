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

class MpSellerMembershipMpSellerPlansModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $id_lang = $this->context->language->id;
        $id_customer = $this->context->customer->id;
        if ($id_customer) {
            $mp_seller_details = WkMpSeller::getSellerDetailByCustomerId($id_customer);
            if ($mp_seller_details && $mp_seller_details['active']) {
                $mp_seller_id = $mp_seller_details['id_seller'];
                if (Tools::getValue('addtocart') == 1) {
                    $id_product = Tools::getValue('id_product');
                    $cart_product_list = $this->context->cart->getProducts();
                    $is_already_plan_in_cart = false;
                    if ($cart_product_list) {
                        foreach ($cart_product_list as $cart_product) {
                            if (MarketplaceSellerplan::getPlanByIdProduct($cart_product['id_product'])) {
                                $is_already_plan_in_cart = true;
                                break;
                            }
                        }
                    }

                    if ($is_already_plan_in_cart) {
                        $this->errors[] = $this->module->l('Membership plan already in cart, first remove that membership plan from your cart.', 'mpsellerplans');
                    }

                    if (!count($this->errors)) {
                        $plan_info = MarketplaceSellerplan::getPlanInfoByIdProduct($id_product, $id_lang);
                        if ($plan_info) {
                            $active_products = 0;
                            $seller_products = MarketplaceSellerplanDetail::getSellerProduct($mp_seller_id, true);
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
                            $this->errors[] = $this->l('Membership plan already in cart, first remove that membership plan from your cart.', 'mpsellerplans');
                        } else {
                            Tools::redirect($this->context->link->getPageLink('cart').'?token='.Tools::getToken(false));
                        }
                    }
                }

                $is_any_plan_active = 0;
                $obj_mp_seller_plan_details = new MarketplaceSellerplanDetail();
                $free_plan = $obj_mp_seller_plan_details->getFreePlanDetailsBySellerId($mp_seller_id);
                if ($free_plan) {
                    $status = 1;
                    if ($free_plan['is_this_current_plan'] && $free_plan['active']) {
                        $is_any_plan_active = 1;
                        $status = 3;
                    } elseif (!$free_plan['is_this_current_plan'] && !$free_plan['active']) {
                        $is_any_plan_active = 1;
                        $status = 2;
                    }

                    $free_plan['status'] = $status;
                    $this->context->smarty->assign('free_plan', $free_plan);
                }

                $all_plan = $obj_mp_seller_plan_details->getAllPlansBySellerId($mp_seller_id, $id_lang);
                if ($all_plan) {
                    foreach ($all_plan as $key => $plan_details) {
                        $status = 1;
                        if ($plan_details['is_this_current_plan'] && $plan_details['active']) {
                            $is_any_plan_active = 1;
                            $status = 3;
                        } elseif (!$plan_details['is_this_current_plan'] && !$plan_details['active']) {
                            $is_any_plan_active = 1;
                            $status = 2;
                        }

                        $all_plan[$key]['status'] = $status;
                        $all_plan[$key]['plan_price'] = Tools::displayPrice($all_plan[$key]['plan_price'], (int) Configuration::get('PS_CURRENCY_DEFAULT'));
                    }

                    $this->context->smarty->assign(array(
                        'all_plan' => $all_plan,
                        'is_any_plan_active' => $is_any_plan_active,
                        'all_plan_count' => count($all_plan),
                    ));
                }
                $this->context->smarty->assign('logic', 'mpsellerplans');
                $this->setTemplate('module:mpsellermembership/views/templates/front/mpsellerplans.tpl');
            } else {
                Tools::redirect($this->context->link->getPageLink('my-account'));
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Marketplace', 'mpsellerplans'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        );

        $breadcrumb['links'][] = array(
            'title' => $this->module->l('MemberShip Plan Details', 'mpsellerplans'),
            'url' => '',
        );

        return $breadcrumb;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');
    }
}
