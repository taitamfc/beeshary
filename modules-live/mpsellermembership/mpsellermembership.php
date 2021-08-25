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

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__).'/../marketplace/classes/WkMpRequiredClasses.php';
include_once dirname(__FILE__).'/classes/MembershipClassInclude.php';
class MpSellerMembership extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    protected $errors = array();
    public function __construct()
    {
        $this->name = 'mpsellermembership';
        $this->tab = 'front_office_features';
        $this->version = '5.0.1';
        $this->author = 'Webkul';
        $this->bootstrap = true;
        $this->dependencies = array('marketplace');
        $this->controllers = array(
            'mpsellerplans',
            'mpmembershipplans',
        );
        $this->secure_key = Tools::encrypt($this->name);
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        parent::__construct();
        $this->displayName = $this->l('Marketplace Seller Membership');
        $this->description = $this->l('Assign User with member type');
    }

    public function getContent()
    {
        $this->context->controller->addJs($this->_path.'/views/js/admin.js');

        $smarty_vars = array(
            'this_path' => $this->_path,
            'action_url' => Tools::safeOutput($_SERVER['REQUEST_URI']),
            'free_plan_display' => Configuration::get('MP_SELLER_MEMBERSHIP_FREE_PLAN'),
            'num_of_products' => Configuration::get('MP_SELLER_MEMBERSHIP_FREE_PLAN_PRODUCTS'),
            'plan_duration' => Configuration::get('MP_SELLER_MEMBERSHIP_FREE_PLAN_DURATION'),
            'plan_duration_type' => Configuration::get('MP_SELLER_MEMBERSHIP_FREE_PLAN_DURATION_TYPE'),
            'warn_num_of_days' => Configuration::get('MP_SELLER_MEMBERSHIP_WARN_DAYS'),
            'warn_num_of_products' => Configuration::get('MP_SELLER_MEMBERSHIP_WARN_PRODUCTS'),
            'old_seller_days' => Configuration::get('MP_SELLER_MEMBERSHIP_OLD_SELLER_DAYS'),
            'display_no_of_plan' => (Configuration::get('MP_SELLER_MEMBERSHIP_NO_OF_PLAN_DISPLAY') / 3),
            'mail_warn_days' => Configuration::get('MP_SELLER_MEMBERSHIP_MAIL_WARN_DAYS'),
            'warning_mail' => Configuration::get('MP_SELLER_MEMBERSHIP_WARNING_MAIL'),
            'expire_mail' => Configuration::get('MP_SELLER_MEMBERSHIP_EXPIRE_MAIL'),
        );

        //Updating approval setting
        if (Tools::isSubmit('submitMembership')) {
            $free_plan_display = Tools::getValue('free_plan_display');
            $num_of_products = Tools::getValue('num_of_products');
            $plan_duration = Tools::getValue('plan_duration');
            $plan_duration_type = Tools::getValue('plan_duration_type');
            $warn_num_of_days = Tools::getValue('warn_num_of_days');
            $warn_num_of_products = Tools::getValue('warn_num_of_products');
            $old_seller_days = Tools::getValue('old_seller_days');
            $display_no_of_plan = Tools::getValue('display_no_of_plan');
            $is_module_configured = Tools::getValue('is_module_configured');
            $mail_warn_days = Tools::getValue('mail_warn_days');
            $warning_mail = Tools::getValue('warning_mail');
            $expire_mail = Tools::getValue('expire_mail');

            if ($warning_mail) {
                if ($mail_warn_days == '') {
                    $this->errors[] = $this->l('Number of days left to upgrade plan, when warning mail will be send is required field.');
                } elseif (!Validate::isUnsignedInt($mail_warn_days)) {
                    $this->errors[] = $this->l('Number of days left to upgrade plan, when warning mail will be send must be numeric and greater than 0.');
                } elseif ($mail_warn_days <= 0) {
                    $this->errors[] = $this->l('Number of days left to upgrade plan, when warning mail will be send must be greater than 0.');
                }
            }

            if ($warn_num_of_days == '') {
                $this->errors[] = $this->l('Number of days left to upgrade plan, when warning will be displayed is required field.');
            } elseif (!Validate::isUnsignedInt($warn_num_of_days)) {
                $this->errors[] = $this->l('Number of days left to upgrade plan, when warning will be displayed must be numeric and greater than 0.');
            } elseif ($warn_num_of_days <= 0) {
                $this->errors[] = $this->l('Number of days left to upgrade plan, when warning will be displayed must be greater than 0.');
            }

            if ($warn_num_of_products == '') {
                $this->errors[] = $this->l('Number of products left to upgrade plan, when warning will be displayed is required field.');
            } elseif (!Validate::isUnsignedInt($warn_num_of_products)) {
                $this->errors[] = $this->l('Number of products left to upgrade plan, when warning will be displayed must be numeric and greater than 0.');
            } elseif ($warn_num_of_products <= 0) {
                $this->errors[] = $this->l('Number of products left to upgrade plan, when warning will be displayed must be greater than 0.');
            }

            if (!$is_module_configured) {
                if ($old_seller_days == '') {
                    $this->errors[] = $this->l('Remaining days to buy plan for existing sellers is required field.');
                } elseif (!Validate::isUnsignedInt($old_seller_days)) {
                    $this->errors[] = $this->l('Remaining days to buy plan for existing sellers must be numeric and greater than 0.');
                } elseif ($old_seller_days <= 0) {
                    $this->errors[] = $this->l('Remaining days to buy plan for existing sellers must be greater than 0.');
                }
            }

            if ($display_no_of_plan == '') {
                $this->errors[] = $this->l('Display Number of plan on plan page is required field.');
            } elseif (!Validate::isUnsignedInt($display_no_of_plan)) {
                $this->errors[] = $this->l('Display Number of plan on plan page page must be numeric and greater than 0.');
            } elseif ($display_no_of_plan <= 0) {
                $this->errors[] = $this->l('Display Number of plan on plan page must be greater than 0.');
            }

            if ($free_plan_display) {
                if ($num_of_products == '') {
                    $this->errors[] = $this->l('Products quantity is required field.');
                } elseif (!Validate::isUnsignedInt($num_of_products)) {
                    $this->errors[] = $this->l('Products quantity must be numeric and greater than 0.');
                } elseif ($num_of_products <= 0) {
                    $this->errors[] = $this->l('Products quantity must be greater than 0.');
                }

                if ($plan_duration == '') {
                    $this->errors[] = $this->l('Plan duration is required field.');
                } elseif (!Validate::isUnsignedInt($plan_duration)) {
                    $this->errors[] = $this->l('Plan duration must be numeric and greater than 0.');
                } elseif ($plan_duration <= 0) {
                    $this->errors[] = $this->l('Plan duration must be greater than 0');
                }

                if (!count($this->errors)) {
                    Configuration::updateValue('MP_SELLER_MEMBERSHIP_FREE_PLAN', $free_plan_display);
                    Configuration::updateValue('MP_SELLER_MEMBERSHIP_FREE_PLAN_PRODUCTS', $num_of_products);
                    Configuration::updateValue('MP_SELLER_MEMBERSHIP_FREE_PLAN_DURATION', $plan_duration);
                    Configuration::updateValue('MP_SELLER_MEMBERSHIP_FREE_PLAN_DURATION_TYPE', $plan_duration_type);
                }
            } else {
                Configuration::updateValue('MP_SELLER_MEMBERSHIP_FREE_PLAN', $free_plan_display);
            }

            if (count($this->errors)) {
                $this->context->smarty->assign('errors', $this->errors);
            } else {
                if (!Configuration::get('MP_SELLER_MEMBERSHIP_IS_CONFIGURED')) {
                    $this->addWarnDaysInOldSeller($old_seller_days);
                    Configuration::updateValue('MP_SELLER_MEMBERSHIP_OLD_SELLER_DAYS', $old_seller_days);
                }

                Configuration::updateValue('MP_SELLER_MEMBERSHIP_NO_OF_PLAN_DISPLAY', ($display_no_of_plan * 3));
                Configuration::updateValue('MP_SELLER_MEMBERSHIP_IS_CONFIGURED', 1);
                Configuration::updateValue('MP_SELLER_MEMBERSHIP_WARN_DAYS', $warn_num_of_days);
                Configuration::updateValue('MP_SELLER_MEMBERSHIP_WARN_PRODUCTS', $warn_num_of_products);
                Configuration::updateValue('MP_SELLER_MEMBERSHIP_MAIL_WARN_DAYS', $mail_warn_days);
                Configuration::updateValue('MP_SELLER_MEMBERSHIP_EXPIRE_MAIL', $expire_mail);
                Configuration::updateValue('MP_SELLER_MEMBERSHIP_WARNING_MAIL', $warning_mail);
                $this->context->smarty->assign('success', $this->l('Settings updated.'));
            }

            $smarty_vars['num_of_products'] = $num_of_products;
            $smarty_vars['plan_duration'] = $plan_duration;
            $smarty_vars['plan_duration_type'] = $plan_duration_type;
            $smarty_vars['warn_num_of_days'] = $warn_num_of_days;
            $smarty_vars['warn_num_of_products'] = $warn_num_of_products;
            $smarty_vars['free_plan_display'] = $free_plan_display;
            $smarty_vars['old_seller_days'] = $old_seller_days;
            $smarty_vars['display_no_of_plan'] = $display_no_of_plan;
            $smarty_vars['mail_warn_days'] = $mail_warn_days;
            $smarty_vars['warning_mail'] = $warning_mail;
            $smarty_vars['expire_mail'] = $expire_mail;
        }

        $smarty_vars['is_module_configured'] = Configuration::get('MP_SELLER_MEMBERSHIP_IS_CONFIGURED');
        $smarty_vars['cron_url'] = Configuration::get('MP_SELLER_MEMBERSHIP_CRON_URL').'?token='.$this->secure_key;
        $smarty_vars['mp_module_dir'] = _MODULE_DIR_;
        $this->context->smarty->assign($smarty_vars);

        return $this->display(__FILE__, './views/templates/admin/admin.tpl');
    }

    public function hookActionFrontControllerSetMedia($params)
    {
        // Only on cart page
        if ('cart' === $this->context->controller->php_self && Configuration::get('MP_SELLER_MEMBERSHIP_IS_CONFIGURED')) {
            $cart_product_list = $this->context->cart->getProducts();
            $product_id = 0;
            if ($cart_product_list) {
                foreach ($cart_product_list as $cart_product) {
                    if (MarketplaceSellerplan::getPlanByIdProduct($cart_product['id_product'])) {
                        $product_id = $cart_product['id_product'];
                    }
                }
            }

            $jsDef = array(
                'plan_product_id' => $product_id,
            );

            Media::addJsDef($jsDef);
            $this->context->controller->registerJavascript('mp_cart', 'modules/'.$this->name.'/views/js/mp_cart.js');
        } elseif ('product' === $this->context->controller->php_self && Configuration::get('MP_SELLER_MEMBERSHIP_IS_CONFIGURED')) {
            $idProduct = Tools::getValue('id_product');
            $product_id = 0;
            if (MarketplaceSellerplan::getPlanByIdProduct($idProduct)) {
                $this->context->controller->addJS($this->_path.'views/js/mp_product.js');
            }
        }
    }

    public function addWarnDaysInOldSeller($days)
    {
        $all_sellers = WkMpSeller::getAllSeller();
        if ($all_sellers) {
            $duration = $days - 1;
            if ($duration < 0) {
                $duration = 0;
            }

            $active_from = date('Y-m-d');
            $expire_on = date('Y-m-d', strtotime('+'.$duration.' days'));
            foreach ($all_sellers as $seller) {
                $active_products = 0;
                $seller_products = MarketplaceSellerplanDetail::getSellerProduct($seller['id_seller'], true);
                if ($seller_products) {
                    $active_products = count($seller_products);
                }
                if ($active_products) {
                    $obj_mp_old_seller = new MarketplaceOldSellerPlan();
                    $obj_mp_old_seller->id_seller = $seller['id_seller'];
                    $obj_mp_old_seller->active_from = $active_from;
                    $obj_mp_old_seller->expire_on = $expire_on;
                    $obj_mp_old_seller->save();

                    $temp_path = _PS_MODULE_DIR_.'mpsellermembership/mails/';
                    $templateVars = array(
                        '{seller_name}' => $seller['seller_firstname'].' '.$seller['seller_lastname'],
                        '{all_plan_link}' => $this->context->link->getModuleLink('mpsellermembership', 'mpmembershipplans'),
                        '{expire_date}' => $expire_on,
                    );
                    $id_lang = $this->context->language->id;
                    Mail::Send(
                        $id_lang,
                        'warning_mail',
                        Mail::l('Seller Membership Warning', $id_lang),
                        $templateVars,
                        $seller['business_email'],
                        $seller['seller_firstname'].' '.$seller['seller_lastname'],
                        null,
                        null,
                        null,
                        null,
                        $temp_path,
                        false,
                        null,
                        null
                    );
                }
            }
        }
    }

    public function hookActionBeforeToggleMPProductStatus($params)
    {
        $mp_product_id = $params['id_mp_product'];
        $obj_mp_product = new WkMpSellerProduct($mp_product_id);
        $id_seller = $obj_mp_product->id_seller;
        if ($mp_product_id && $id_seller && Configuration::get('MP_SELLER_MEMBERSHIP_IS_CONFIGURED')) {
            if (!$obj_mp_product->active) {
                $flag = 1;
                $obj_mp_sellerplan_detail = new MarketplaceSellerplanDetail();
                $active_plan = $obj_mp_sellerplan_detail->getCurrentActivePlanBySellerId($id_seller);
                $pending_plan = $obj_mp_sellerplan_detail->getLastRequestedPlanBySellerId($id_seller);
                $expired_plan = $obj_mp_sellerplan_detail->getLastDeactivePlanBySellerId($id_seller);

                $active_products = 0;
                $seller_products = MarketplaceSellerplanDetail::getSellerProduct($id_seller, true);
                if ($seller_products) {
                    $active_products = count($seller_products);
                }
                $today = date('Y-m-d');
                if ($active_plan) {
                    $flag = 0;
                    if ($active_plan['num_products_allow'] <= $active_products) {
                        if (Tools::getValue('controller') == 'productlist') {
                            $this->context->controller->errors[] = $this->l('You already have maximum number of active products, so you can not active product.');
                        } else {
                            $this->context->controller->errors[] = $this->l('This product\'s seller already have maximum number of active products, so you can not active this seller\'s product');
                        }
                    }
                }

                if (empty($this->context->controller->errors) && $pending_plan) {
                    $flag = 0;
                    if ($active_plan) { // if currently their is any active plan and required plan do not allow to active product
                        if ($pending_plan['num_products_allow'] <= $active_products) {
                            if (Tools::getValue('controller') == 'productlist') {
                                $this->context->controller->errors[] = $this->l('Your requested membership plan do not allow you to active this product, because you already have maximum number of active products according requested membership plan.');
                            } else {
                                $this->context->controller->errors[] = $this->l('This product\'s seller requested membership plan do not allow to active this product, because seller of this product already have maximum number of active products according requested membership plan.');
                            }
                        }
                    } else { // currently their is not any active plan
                        if (Tools::getValue('controller') == 'productlist') {
                            $this->context->controller->errors[] = $this->l('Your marketplace membership plan is not active. Please wait for admin approval.');
                        } else {
                            $this->context->controller->errors[] = $this->l('Marketplace membership plan of this product\'s seller is not active.');
                        }
                    }
                }

                // plan expired
                if (empty($this->context->controller->errors) && $expired_plan && $flag) {
                    if (Tools::getValue('controller') == 'productlist') {
                        $this->context->controller->errors[] = $this->l('Your marketplace membership plan has been expired, so you can not active product.');
                    } else {
                        $this->context->controller->errors[] = $this->l('Marketplace membership plan of this product\'s seller has been expired.');
                    }
                }

                if (empty($this->context->controller->errors) && $flag) {
                    $is_old_seller = MarketplaceOldSellerPlan::getInfoByIdSeller($id_seller);
                    if ($is_old_seller) {
                        if ($today > $is_old_seller['expire_on']) {
                            if (Tools::getValue('controller') == 'productlist') {
                                $this->context->controller->errors[] = $this->l('You didn\'t requested any marketplace membership plan, so you can not active product. First request any marketplace membership plan.');
                            } else {
                                $this->context->controller->errors[] = $this->l('This product\'s seller did not requested any marketplace membership plan, so you can not active product.');
                            }
                        }
                    } else {
                        if (Tools::getValue('controller') == 'productlist') {
                            $this->context->controller->errors[] = $this->l('You didn\'t buy any marketplace membership plan, so you can not active product. First you need to request any marketplace membership plan.');
                        } else {
                            $this->context->controller->errors[] = $this->l('This product\'s seller did not request any marketplace membership plan, so you can not active product.');
                        }
                    }
                }
            }
        }
    }

    public function hookActionObjectLanguageAddAfter($params)
    {
        if ($params['object']->id) {
            $new_lang_id = $params['object']->id;

            //Assign all lang's main table in an ARRAY
            $lang_tables = array('wk_mp_seller_plan');

            //If Admin create any new language when we do entry in module all lang tables.
            WkMpHelper::updateIdLangInLangTables($new_lang_id, $lang_tables);
        }
    }

    public function hookActionProductDelete($params)
    {
        $id_product = $params['id_product'];
        if ($id_product) {
            if ($plan_id = MarketplaceSellerplan::getPlanByIdProduct($id_product)) {
                $obj_mp_seller_plan = new MarketplaceSellerplan($plan_id);
                $obj_mp_seller_plan->delete();
            }
        }
    }

    public function hookActionMpSellerDelete($params)
    {
        $mp_seller_id = $params['id_seller'];
        if ($mp_seller_id) {
            $obj_mp_seller = new WkMpSeller();
            $id_customer = $obj_mp_seller->getCustomerIdBySellerId($mp_seller_id);
            $obj_customer = new Customer($id_customer);
            $mp_group_id = Configuration::get('WK_MP_SELLER_GROUP');
            $customer_groups = $obj_customer->getGroups();
            foreach ($customer_groups as $key => $group_id) {
                if ($group_id == $mp_group_id) {
                    unset($customer_groups[$key]);
                }
            }
            $obj_customer->updateGroup($customer_groups);
            $obj_mp_sellerplan_detail = new MarketplaceSellerplanDetail();
            $obj_mp_sellerplan_detail->deleteByIdSeller($mp_seller_id);
        }
    }

    public function hookActionToogleSellerStatus($params)
    {
        if ($params) {
            $obj_mp_seller = new WkMpSeller();
            $id_customer = $obj_mp_seller->getCustomerIdBySellerId($params['id_seller']);
            $obj_customer = new Customer($id_customer);
            $mp_group_id = Configuration::get('WK_MP_SELLER_GROUP');
            if ($params['is_seller']) {
                $customer_groups = $obj_customer->getGroups();
                foreach ($customer_groups as $key => $group_id) {
                    if ($group_id == $mp_group_id) {
                        unset($customer_groups[$key]);
                    }
                }
                $obj_customer->updateGroup($customer_groups);
            } else {
                $group = array();
                $group[] = $mp_group_id;
                $obj_customer->addGroups($group);
                unset($obj_customer);
            }
        }
    }

    public function hookActionValidateOrder($params)
    {
        if (Configuration::get('MP_SELLER_MEMBERSHIP_IS_CONFIGURED')) {
            $id_lang = $this->context->language->id;
            $id_order = $params['order']->id;
            $id_customer = $params['order']->id_customer;
            $obj_mp_seller = new WkMpSeller();
            $seller_info = $obj_mp_seller->getSellerDetailByCustomerId($id_customer);
            if ($seller_info) {
                $order_products_details = OrderDetail::getList($id_order);
                foreach ($order_products_details as $order_product) {
                    $seller_plan = MarketplaceSellerplan::getPlanInfoByIdProduct($order_product['product_id'], $id_lang);
                    if ($seller_plan) {
                        $obj_mp_sellerplan_detail = new MarketplaceSellerplanDetail();
                        $obj_mp_sellerplan_detail->id_plan = $seller_plan['id'];
                        $obj_mp_sellerplan_detail->id_order = $id_order;
                        $obj_mp_sellerplan_detail->mp_id_seller = $seller_info['id_seller'];
                        $obj_mp_sellerplan_detail->num_products_allow = $seller_plan['num_products_allow'];
                        $obj_mp_sellerplan_detail->plan_duration = $seller_plan['plan_duration'];
                        $obj_mp_sellerplan_detail->is_this_current_plan = 0;
                        $obj_mp_sellerplan_detail->active = 0;
                        $obj_mp_sellerplan_detail->save();
                        unset($obj_mp_sellerplan_detail);
                    }
                }
            }
        }
    }

    public function hookActionAfterAddSeller($params)
    {
        $seller_id = $params['id_seller'];
        if ($seller_id) {
            $obj_mp_seller = new WkMpSeller();
            $seller_details = $obj_mp_seller->getSeller($seller_id);
            if ($seller_details && $seller_details['active']) {
                $obj_customer = new Customer($seller_details['seller_customer_id']);
                $group = array();
                $group[] = Configuration::get('WK_MP_SELLER_GROUP');
                $obj_customer->addGroups($group);
            }
        }
    }

    public function hookActionAfterAddMPProduct($params)
    {
        if ($params['id_mp_product'] && Configuration::get('MP_SELLER_MEMBERSHIP_IS_CONFIGURED')) {
            $obj_mp_product = new WkMpSellerProduct($params['id_mp_product']);
            $id_seller = $obj_mp_product->id_seller;
            if ($id_seller) {
                $obj_mp_sellerplan_detail = new MarketplaceSellerplanDetail();
                $plan_details = $obj_mp_sellerplan_detail->getCurrentActivePlanBySellerId($id_seller);
                $today = date('Y-m-d');
                if ($plan_details) {
                    $active_products = 0;
                    $seller_products = MarketplaceSellerplanDetail::getSellerProduct($id_seller, true);
                    if ($seller_products) {
                        $active_products = count($seller_products);
                    }
                    if ($today > $plan_details['expire_on'] || ($plan_details['num_products_allow'] < $active_products)) {
                        $this->deactiveProductByMpIdProduct($params['id_mp_product']);
                    }
                } else {
                    $is_old_seller = MarketplaceOldSellerPlan::getInfoByIdSeller($id_seller);
                    if ($is_old_seller) {
                        if ($is_old_seller['expire_on'] < $today) {
                            $this->deactiveProductByMpIdProduct($params['id_mp_product']);
                        }
                    } else {
                        $this->deactiveProductByMpIdProduct($params['id_mp_product']);
                    }
                }
            }
        }
    }

    public function deactiveProductByMpIdProduct($mp_id_product)
    {
        $obj_mp_product = new WkMpSellerProduct($mp_id_product);
        $obj_mp_product->active = 0;
        $obj_mp_product->save();
        if ($obj_mp_product->id_ps_product) {
            $product = new Product($obj_mp_product->id_ps_product);
            $product->active = 0;
            $product->save();
        }
    }

    public function hookActionBeforeAddMPProduct($params)
    {
        if (Configuration::get('MP_SELLER_MEMBERSHIP_IS_CONFIGURED')) {
            $idSeller = $params['id_seller'];
            $obj_mp_sellerplan_detail = new MarketplaceSellerplanDetail();
            $plan_details = $obj_mp_sellerplan_detail->getCurrentActivePlanBySellerId($idSeller);
            $plan_expire = 1;
            $today = date('Y-m-d');
            if ($plan_details) {
                $plan_expire = 0;

                $active_products = 0;
                $seller_products = MarketplaceSellerplanDetail::getSellerProduct($idSeller, true);
                if ($seller_products) {
                    $active_products = count($seller_products);
                }

                if ($today > $plan_details['expire_on']) {
                    $plan_expire = 2;
                } elseif ($plan_details['num_products_allow'] <= $active_products) {
                    $plan_expire = 4;
                }
            } else {
                $flag = 1;
                $is_pending_plan = $obj_mp_sellerplan_detail->getLastRequestedPlanBySellerId($idSeller);
                if ($is_pending_plan) {
                    $flag = 0;
                    $plan_expire = 3;
                }

                $is_old_seller = MarketplaceOldSellerPlan::getInfoByIdSeller($idSeller);
                if ($is_old_seller && $flag) {
                    if ($is_old_seller['expire_on'] >= $today) {
                        $plan_expire = 0;
                        $flag = 0;
                    }
                }

                $is_deactive_plan = $obj_mp_sellerplan_detail->getLastDeactivePlanBySellerId($idSeller);
                if ($is_deactive_plan && $flag) {
                    $plan_expire = 2;
                }
            }

            if ($plan_expire == 1) {
                if (Tools::getValue('controller') == 'addproduct') {
                    $this->context->controller->errors[] = $this->l('You did not request any membership plan yet for add product buy new plan.');
                } else {
                    $this->context->controller->errors[] = $this->l('Seller of this product did not request any membership plan yet.');
                }
            } elseif ($plan_expire == 2) {
                if (Tools::getValue('controller') == 'addproduct') {
                    $this->context->controller->errors[] = $this->l('Your membership plan has been expired.');
                } else {
                    $this->context->controller->errors[] = $this->l('Membership plan of this product\'s seller has been expired.');
                }
            } elseif ($plan_expire == 3) {
                if (Tools::getValue('controller') == 'addproduct') {
                    $this->context->controller->errors[] = $this->l('Your request of marketplace membership plan has been sent to admin. Please wait till the approval from admin');
                } else {
                    $this->context->controller->errors[] = $this->l('You did not approve marketplace membership plan of the seller of this product.');
                }
            } elseif ($plan_expire == 4) {
                if (Tools::getValue('controller') == 'addproduct') {
                    $this->context->controller->errors[] = $this->l('You have reached your membership plan maximum product limit');
                } else {
                    $this->context->controller->errors[] = $this->l('The membership plan of seller of this product has been reached maximum product limit.');
                }
            }
        }
    }

    public function hookDisplayMpAddProductHeader()
    {
        $controller = Tools::getValue('controller');
        if (Configuration::get('MP_SELLER_MEMBERSHIP_IS_CONFIGURED') && $controller != 'AdminSellerProductDetail') {
            $id_customer = $this->context->customer->id;
            $no_permi = 1;
            $plan_expire = 1;
            $obj_mp_seller = new WkMpSeller();
            $seller_info = $obj_mp_seller->getSellerDetailByCustomerId($id_customer);
            if ($seller_info && $seller_info['active']) {
                $mp_seller_id = $seller_info['id_seller'];
                $obj_mp_sellerplan_detail = new MarketplaceSellerplanDetail();
                $plan_details = $obj_mp_sellerplan_detail->getCurrentActivePlanBySellerId($mp_seller_id);
                $today = date('Y-m-d');
                if ($plan_details) {
                    $plan_expire = 0;
                    $warning_date = date('Y-m-d', strtotime('+'.Configuration::get('MP_SELLER_MEMBERSHIP_WARN_DAYS').' days')); // days after today, accroding config variable for display remianing day warning

                    $active_products = 0;
                    $seller_products = MarketplaceSellerplanDetail::getSellerProduct($mp_seller_id, true);
                    if ($seller_products) {
                        $active_products = count($seller_products);
                    }

                    if ($today > $plan_details['expire_on']) {
                        $plan_expire = 2;
                    } elseif ($plan_details['num_products_allow'] <= $active_products) {
                        $plan_expire = 4;
                    } elseif ($warning_date > $plan_details['expire_on']) {
                        $date1 = new DateTime($plan_details['expire_on']);
                        $date2 = new DateTime($today);
                        $day_diff = $date2->diff($date1)->format('%a');
                        $plan_expire = 3;
                        $this->context->smarty->assign('days', $day_diff);
                    } elseif ($plan_details['num_products_allow'] > $active_products) {
                        $remaining_products = $plan_details['num_products_allow'] - $active_products;
                        if ($remaining_products <= Configuration::get('MP_SELLER_MEMBERSHIP_WARN_PRODUCTS')) {
                            $plan_expire = 5;
                            $this->context->smarty->assign('products', $remaining_products);
                        }
                    }
                } else {
                    $flag = 1;
                    $is_pending_plan = $obj_mp_sellerplan_detail->getLastRequestedPlanBySellerId($mp_seller_id);
                    if ($is_pending_plan) {
                        $flag = 0;
                        $plan_expire = 6;
                    }

                    $is_old_seller = MarketplaceOldSellerPlan::getInfoByIdSeller($mp_seller_id);
                    if ($is_old_seller && $flag) {
                        if ($is_old_seller['expire_on'] >= $today) {
                            $plan_expire = 0;
                            $flag = 0;
                        }
                    }

                    $is_deactive_plan = $obj_mp_sellerplan_detail->getLastDeactivePlanBySellerId($mp_seller_id);
                    if ($is_deactive_plan && $flag) {
                        $plan_expire = 2;
                    }
                }

                $no_permi = 0;
            }

            $this->context->smarty->assign('plan_expire', $plan_expire);
            $this->context->smarty->assign('no_permi', $no_permi);
            $this->context->smarty->assign('plan_request_link', $this->context->link->getModuleLink('mpsellermembership', 'mpmembershipplans'));

            return $this->fetch('module:mpsellermembership/views/templates/hook/plan_request.tpl');
        }
    }

    public function hookActionBeforeAssignMpProduct($params)
    {
        $id_customer = $params['id_customer'];
        if (Configuration::get('MP_SELLER_MEMBERSHIP_IS_CONFIGURED') && $id_customer) {
            $obj_mp_seller = new WkMpSeller();
            $seller_info = $obj_mp_seller->getSellerDetailByCustomerId($id_customer);
            if ($seller_info && $seller_info['active']) {
                $mp_seller_id = $seller_info['id_seller'];
                $obj_mp_sellerplan_detail = new MarketplaceSellerplanDetail();
                $plan_details = $obj_mp_sellerplan_detail->getCurrentActivePlanBySellerId($mp_seller_id);
                $plan_expire = 1;
                $today = date('Y-m-d');
                if ($plan_details) {
                    $plan_expire = 0;

                    $active_products = 0;
                    $seller_products = MarketplaceSellerplanDetail::getSellerProduct($mp_seller_id, true);
                    if ($seller_products) {
                        $active_products = count($seller_products);
                    }

                    if ($today > $plan_details['expire_on']) {
                        $plan_expire = 2;
                    } elseif ($plan_details['num_products_allow'] <= $active_products) {
                        $plan_expire = 4;
                    }
                } else {
                    $flag = 1;
                    $is_pending_plan = $obj_mp_sellerplan_detail->getLastRequestedPlanBySellerId($mp_seller_id);
                    if ($is_pending_plan) {
                        $flag = 0;
                        $plan_expire = 3;
                    }

                    $is_old_seller = MarketplaceOldSellerPlan::getInfoByIdSeller($mp_seller_id);
                    if ($is_old_seller && $flag) {
                        if ($is_old_seller['expire_on'] >= $today) {
                            $plan_expire = 0;
                            $flag = 0;
                        }
                    }

                    $is_deactive_plan = $obj_mp_sellerplan_detail->getLastDeactivePlanBySellerId($mp_seller_id);
                    if ($is_deactive_plan && $flag) {
                        $plan_expire = 2;
                    }
                }

                if ($plan_expire == 1) {
                    $this->context->controller->errors[] = $this->l('This seller did not request any membership plan. First seller need to buy any membership plan after that you can assign product(s) to this seller.');
                } elseif ($plan_expire == 2) {
                    $this->context->controller->errors[] = $this->l('Membership plan of this seller has been expired.');
                } elseif ($plan_expire == 3) {
                    $this->context->controller->errors[] = $this->l('Marketplace membership plan of this seller is not approved. First you need to approve membership plan of this seller.');
                } elseif ($plan_expire == 4) {
                    $this->context->controller->errors[] = $this->l('This seller already has maximum number of active products according membership plan limit.');
                }
            }
        }
    }

    public function hookDisplayMPMyAccountMenu()
    {
        $id_customer = $this->context->customer->id;
        $obj_mp_seller = new WkMpSeller();
        $seller_info = $obj_mp_seller->getSellerDetailByCustomerId($id_customer);
        if ($seller_info && $seller_info['active'] && Configuration::get('MP_SELLER_MEMBERSHIP_IS_CONFIGURED')) {
            $this->context->smarty->assign('plandetail_link', $this->context->link->getModuleLink('mpsellermembership', 'mpsellerplans'));
            $this->context->smarty->assign('mpmenu', '0');

            return $this->fetch('module:mpsellermembership/views/templates/hook/userplandetail_link.tpl');
        }
    }

    public function hookDisplayMPMenuBottom()
    {
        $id_customer = $this->context->customer->id;
        $obj_mp_seller = new WkMpSeller();
        $seller_info = $obj_mp_seller->getSellerDetailByCustomerId($id_customer);
        if ($seller_info && $seller_info['active'] && Configuration::get('MP_SELLER_MEMBERSHIP_IS_CONFIGURED')) {
            $this->context->smarty->assign('plandetail_link', $this->context->link->getModuleLink('mpsellermembership', 'mpsellerplans'));
            $this->context->smarty->assign('mpmenu', '1');

            return $this->fetch('module:mpsellermembership/views/templates/hook/userplandetail_link.tpl');
        }
    }

    /**
     * Install process start
     */

    public function callInstallTab()
    {
        $this->installTab('AdminAddSellerMembershipPlan', 'Membership Plans', 'AdminMarketplaceManagement');
        $this->installTab('AdminSellerMembershipPlanRequest', 'Membership Plan Request', 'AdminMarketplaceManagement');
        $this->installTab('AdminSellerMembershipPlanDetail', 'Membership Plan Detail', 'AdminMarketplaceManagement');

        return true;
    }

    public function installTab($class_name, $tab_name, $tab_parent_name = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }

        if ($tab_parent_name) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tab_parent_name);
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;

        return $tab->add();
    }

    public function addGroup()
    {
        $obj_group = new Group();
        $obj_group->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $obj_group->name[$lang['id_lang']] = 'marketplaceselller';
        }

        $obj_group->reduction = 0.00;
        $obj_group->price_display_method = 0;
        $obj_group->add();
        $gp_id = $obj_group->id;
        $obj_mp_seller_plan = new MarketplaceSellerplan();
        $modules = $obj_mp_seller_plan->getAllModules();
        $shops = $obj_mp_seller_plan->getAllShops();
        $obj_group->addModulesRestrictions($gp_id, $modules, $shops);
        Configuration::updateValue('WK_MP_SELLER_GROUP', $gp_id);

        if (!Configuration::get('WK_MP_SELLER_GROUP') || Configuration::get('WK_MP_SELLER_GROUP') == 0) {
            //Assigning all categories to seller
            $root = Category::getRootCategory();
            $category_ids = Db::getInstance()->executeS('SELECT `id_category` FROM `'._DB_PREFIX_.'category` WHERE `id_category` <> '.$root->id);
            foreach ($category_ids as $id) {
                Db::getInstance()->insert('category_group', array('id_category' => $id['id_category'], 'id_group' => $gp_id));
            }
        }

        return $gp_id;
    }

    public function addCategory($gp_id)
    {
        $obj = new Category();
        $root = Category::getRootCategory();
        $obj->name = array();
        $obj->description = array();
        $obj->link_rewrite = array();
        foreach (Language::getLanguages(true) as $lang) {
            $obj->name[$lang['id_lang']] = 'seller';
            $obj->description[$lang['id_lang']] = $this->l('Products under this category are for sellers only');
            $obj->link_rewrite[$lang['id_lang']] = 'seller';
        }

        $obj->id_parent = $root->id;
        $obj->groupBox = $gp_id;
        $obj->active = 0;
        $obj->add();
        $cat_id = $obj->id;
        Configuration::updateValue('WK_MP_SELLER_CATEGORY', $cat_id);

        return $cat_id;
    }

    public function updateCustomerGroup()
    {
        $seller_group = Configuration::get('WK_MP_SELLER_GROUP');
        $mp_sellers = WkMpSeller::getAllSeller();
        if ($mp_sellers) {
            $obj_mp_seller_plan = new MarketplaceSellerplan();
            foreach ($mp_sellers as $seller) {
                $obj_mp_seller_plan->updateCustomerGroupByIdCustomerAndIdGroup($seller['seller_customer_id'], $seller_group);
            }
        }

        return true;
    }

    public function addSellerGroup()
    {
        $seller_group_id = array();
        $gp_id = $this->addGroup();
        $seller_group_id[] = $gp_id;
        $this->addCategory($seller_group_id);

        return true;
    }

    public function alterTable($table_name, $action)
    {
        if ($action == 'add') {
            $add = Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.$table_name." ADD COLUMN is_free_plan_taken tinyint(1) unsigned NOT NULL DEFAULT '0'");
            if (!$add) {
                return false;
            }

            return true;
        } elseif ($action == 'drop') {
            $drop = Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.$table_name.' DROP COLUMN is_free_plan_taken');
            if (!$drop) {
                return false;
            }

            return true;
        }
    }

    public function createTables()
    {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return false;
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return false;
        }
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);
        foreach ($sql as $query) {
            if ($query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }

        return true;
    }

    public function addConfigurationVariable()
    {
        Configuration::updateValue('WK_MP_SELLER_GROUP', 0);
        Configuration::updateValue('WK_MP_SELLER_CATEGORY', 0);
        Configuration::updateValue('MP_SELLER_MEMBERSHIP_WARN_DAYS', 3);
        Configuration::updateValue('MP_SELLER_MEMBERSHIP_FREE_PLAN', 0);
        Configuration::updateValue('MP_SELLER_MEMBERSHIP_FREE_PLAN_PRODUCTS', 0);
        Configuration::updateValue('MP_SELLER_MEMBERSHIP_FREE_PLAN_DURATION', 0);
        Configuration::updateValue('MP_SELLER_MEMBERSHIP_FREE_PLAN_DURATION_TYPE', 1);
        Configuration::updateValue('MP_SELLER_MEMBERSHIP_WARN_PRODUCTS', 5);
        Configuration::updateValue('MP_SELLER_MEMBERSHIP_IS_CONFIGURED', 0);
        Configuration::updateValue('MP_SELLER_MEMBERSHIP_OLD_SELLER_DAYS', 7);
        Configuration::updateValue('MP_SELLER_MEMBERSHIP_NO_OF_PLAN_DISPLAY', 6);
        Configuration::updateValue('MP_SELLER_MEMBERSHIP_MAIL_WARN_DAYS', 3);
        Configuration::updateValue(
            'MP_SELLER_MEMBERSHIP_CRON_URL',
            '5 0 * * * curl '.Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/mpsellermembership/updatedatabase.php'
        );

        return true;
    }

    public function hookDisplayAdminSellerDetailViewRightColumn($params)
    {
        $idSeller = Tools::getValue('id_seller');
        if ($idSeller) {
            $objMpSellerPlan = new MarketplaceSellerplanDetail();
            $sellerPlan = $objMpSellerPlan->getCurrentActivePlanBySellerId($idSeller);
            if ($sellerPlan) {
                $this->context->smarty->assign('sellerPlan', $sellerPlan);                    

                return $this->display(__FILE__, 'adminsellerplandetails.tpl');
            }
        }
    }

    public function registerPsAndMpHooks()
    {
        return $this->registerHook(
            array(
                'displayMPMyAccountMenu', 'displayMPMenuBottom', 'displayMpAddProductHeader',
                'actionBeforeAddMPProduct', 'actionAfterAddMPProduct', 'actionAfterAddSeller',
                'actionToogleSellerStatus', 'actionBeforeAssignMpProduct', 'actionMpSellerDelete',
                'actionBeforeToggleMPProductStatus', 'actionValidateOrder','actionProductDelete',
                'actionFrontControllerSetMedia', 'actionObjectLanguageAddAfter', 'displayAdminSellerDetailViewRightColumn')
        );
    }

    public function install()
    {
        if (!parent::install()
            || !$this->createTables()
            || !$this->addConfigurationVariable()
            || !$this->addSellerGroup()
            || !$this->callInstallTab()
            || !$this->updateCustomerGroup()
            || !$this->registerPsAndMpHooks()
            || !$this->alterTable('wk_mp_seller', 'add')
        ) {
            return false;
        }

        if (!WkMpSeller::getAllSeller()) {
            Configuration::updateValue('MP_SELLER_MEMBERSHIP_IS_CONFIGURED', 1);
        }

        return true;
    }

    /**
     * Uninstall process start
     */

    public function callUninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }

        return true;
    }

    public function dropTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'wk_mp_seller_plan`,
            `'._DB_PREFIX_.'wk_mp_seller_plan_lang`,
            `'._DB_PREFIX_.'wk_mp_seller_plan_detail`,
            `'._DB_PREFIX_.'wk_mp_old_seller_plan`'
        );
    }

    public function deleteConfig()
    {
        if (!Configuration::deleteByName('WK_MP_SELLER_GROUP')
            || !Configuration::deleteByName('WK_MP_SELLER_CATEGORY')
            || !Configuration::deleteByName('MP_SELLER_MEMBERSHIP_FREE_PLAN')
            || !Configuration::deleteByName('MP_SELLER_MEMBERSHIP_FREE_PLAN_PRODUCTS')
            || !Configuration::deleteByName('MP_SELLER_MEMBERSHIP_FREE_PLAN_DURATION')
            || !Configuration::deleteByName('MP_SELLER_MEMBERSHIP_FREE_PLAN_DURATION_TYPE')
            || !Configuration::deleteByName('MP_SELLER_MEMBERSHIP_WARN_DAYS')
            || !Configuration::deleteByName('MP_SELLER_MEMBERSHIP_WARN_PRODUCTS')
            || !Configuration::deleteByName('MP_SELLER_MEMBERSHIP_IS_CONFIGURED')
            || !Configuration::deleteByName('MP_SELLER_MEMBERSHIP_OLD_SELLER_DAYS')
            || !Configuration::deleteByName('MP_SELLER_MEMBERSHIP_NO_OF_PLAN_DISPLAY')
            || !Configuration::deleteByName('MP_SELLER_MEMBERSHIP_CRON_URL')
        ) {
            return false;
        }

        return true;
    }

    public function deleteGroup()
    {
        $group = new Group(Configuration::get('WK_MP_SELLER_GROUP'));
        if ($group->delete()) {
            return true;
        }

        return false;
    }

    public function deleteAllProduct()
    {
        $mpSellerPlan = new MarketplaceSellerplan();
        $plans = $mpSellerPlan->getAllPlan();
        if ($plans) {
            foreach ($plans as $plan) {
                $product = new Product($plan['id_product']);
                $product->delete();
            }
        }

        return true;
    }

    public function deleteCategory()
    {
        $category = new Category(Configuration::get('WK_MP_SELLER_CATEGORY'));
        if ($category->delete()) {
            return true;
        }

        return false;
    }

    public function uninstall()
    {
        if (!$this->deleteGroup()
            || !$this->deleteAllProduct()
            || !$this->deleteCategory()
            || !$this->dropTables()
            || !$this->deleteConfig()
            || !$this->callUninstallTab()
            || !$this->alterTable('wk_mp_seller', 'drop')
            || !parent::uninstall()
        ) {
            return false;
        }

        return true;
    }
}
