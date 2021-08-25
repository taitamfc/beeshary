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

class AdminSellerMembershipPlanRequestController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'wk_mp_seller_plan_detail';
        $this->className = 'MarketplaceSellerplanDetail';
        $this->context = Context::getContext();
        $this->list_no_link = true;
        $this->addRowAction('delete');
        $this->_join .= 'Left JOIN `'._DB_PREFIX_.'wk_mp_seller_plan` mpsp ON  (a.`id_plan` = mpsp.`id`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_plan_lang` msla ON (msla.`id` = mpsp.`id`)';
        $this->_join .= 'Left JOIN `'._DB_PREFIX_.'wk_mp_seller` mpsi ON  (a.`mp_id_seller`= mpsi.`id_seller`)';
        $this->_where .= ' AND a.`active` = 0 AND msla.`id_lang` = '.(int) $this->context->language->id;
        $this->_select = 'mpsi.`seller_firstname`, msla.`plan_name` as `plan_name`, a.`active` as `active`';
        $this->identifier = 'id';
        parent::__construct();

        $this->fields_list = array(
            'mp_id_seller' => array(
                'title' => $this->l('Seller Id') ,
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'seller_firstname' => array(
                'title' => $this->l('Seller First Name') ,
                'align' => 'center',
                'filter_key' => 'mpsi!seller_firstname'
            ),
            'plan_name' => array(
                'title' => $this->l('Plan Name') ,
                'align' => 'center',
            ),
            'id_order' => array(
                'title' => $this->l('Prestashop Order Id') ,
                'align' => 'center',
            ),
            'date_add' => array(
                'title' => $this->l('Plan Purchase Date') ,
                'align' => 'center',
                'filter_key' => 'a!date_add',
                'type' => 'datetime'
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'type' => 'bool',
                'active' => 'status',
            ),
        );

        $this->_conf[100] = $this->l('Membership Plan Activated Successfully.');
        $this->_conf[101] = $this->l('Selected Membership Plan Activated Successfully.');
    }

    public function initToolBar()
    {
        parent::initToolBar();
        unset($this->toolbar_btn['new']);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitBulkenableSelection'.$this->table)) {
            $selected_box = Tools::getValue($this->table.'Box');
            if (is_array($selected_box) && !empty($selected_box)) {
                foreach ($selected_box as $id) {
                    $this->activeSellerMemberShipPlan($id);
                }

                if (empty($this->errors)) {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=101&token='.$this->token);
                }
            }
        }

        parent::postProcess();
    }

    public function processStatus()
    {
        $id = Tools::getValue('id');
        if ($id) {
            $this->activeSellerMemberShipPlan($id);
        }

        if (empty($this->errors)) {
            Tools::redirectAdmin(self::$currentIndex.'&conf=100&token='.$this->token);
        }
    }

    public function activeSellerMemberShipPlan($id)
    {
        $obj_mp_seller_plan_detail = new MarketplaceSellerplanDetail($id);
        $id_seller = $obj_mp_seller_plan_detail->mp_id_seller;
        $num_products_allow = $obj_mp_seller_plan_detail->num_products_allow;
        $active_products = 0;
        $seller_products = MarketplaceSellerplanDetail::getSellerProduct($id_seller, true);
        if ($seller_products) {
            $active_products = count($seller_products);
        }
        if ($active_products > $num_products_allow) {
            $this->errors[] = $this->l('Active products are greater than the plan limit. First deactive some product of this seller.');
        } else {
            $duration = $obj_mp_seller_plan_detail->plan_duration - 1;
            if ($duration < 0) {
                $duration = 0;
            }

            $active_from = date('Y-m-d');
            $expire_on = date('Y-m-d', strtotime(' + '.$duration.' day'));
            $active_plan = $obj_mp_seller_plan_detail->getCurrentActivePlanBySellerId($id_seller);
            if ($active_plan) {
                if (($active_plan['id_plan'] == $obj_mp_seller_plan_detail->id_plan) && ($active_plan['expire_on'] >= $active_from)) {
                    $expire_on = date('Y-m-d', strtotime($active_plan['expire_on'].' + '.($duration + 1).' day'));
                }
            }
            $obj_mp_seller_plan_detail->updateCurrentPlanBySellerId($id_seller, 0);

            $obj_mp_seller_plan_detail->active_from = $active_from;
            $obj_mp_seller_plan_detail->expire_on = $expire_on;
            $obj_mp_seller_plan_detail->is_this_current_plan = 1;
            $obj_mp_seller_plan_detail->active = 1;
            $obj_mp_seller_plan_detail->save();

            if ($obj_mp_seller_plan_detail->id_plan) {
                $obj_mp_seller_info = new WkMpSeller($id_seller);
                $obj_mp_seller_plan = new MarketplaceSellerplan($obj_mp_seller_plan_detail->id_plan);
                $seller_name = $obj_mp_seller_info->seller_firstname.' '.$obj_mp_seller_info->seller_firstname;
                $plan_var = array(
                    '{seller_name}' => $seller_name,
                    '{plan_name}' => $obj_mp_seller_plan->plan_name,
                    '{plan_price}' => $obj_mp_seller_plan->plan_price,
                    '{product_allow}' => $obj_mp_seller_plan_detail->num_products_allow,
                    '{active_from}' => $active_from,
                    '{expire_on}' => $expire_on,
                );
                $id_lang = Configuration::get('PS_LANG_DEFAULT');
                $template_path = _PS_MODULE_DIR_.'mpsellermembership/mails/';
                Mail::Send(
                    (int) $id_lang,
                    'seller_plan_active',
                    Mail::l('Membership Plan Activation', (int) $id_lang),
                    $plan_var,
                    $obj_mp_seller_info->business_email,
                    $seller_name,
                    null,
                    null,
                    null,
                    null,
                    $template_path,
                    false,
                    null,
                    null
                );
            }

            $is_warn_time_left = MarketplaceOldSellerPlan::getInfoByIdSeller($id_seller);
            if ($is_warn_time_left) {
                $obj_old_seller_temp = new MarketplaceOldSellerPlan($is_warn_time_left['id']);
                $obj_old_seller_temp->delete();
            }
        }
    }
}
