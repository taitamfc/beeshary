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

class AdminSellerMembershipPlanDetailController extends ModuleAdminController
{
    public function __construct()
    {
        $mp_seller_id = Tools::getValue('mp_seller_id');
        if (!$mp_seller_id) {
            $mp_seller_id = 0;
        }

        $this->bootstrap = true;
        $this->table = 'wk_mp_seller_plan_detail';
        $this->className = 'MarketplaceSellerplanDetail';
        $this->context = Context::getContext();
        $this->addRowAction('delete');
        $this->list_no_link = true;
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller` mpsi on  (a.`mp_id_seller` = mpsi.`id_seller`)';
        $this->_select .= 'a.`id` AS `id`, a.`id_plan` AS `plan_name`, mpsi.`seller_firstname`, a.`is_this_current_plan` as `plan_status_check`';
        $this->_where .= ' AND a.`mp_id_seller` = '.(int) $mp_seller_id;
        $this->_orderBy = 'id';
        $this->_orderWay = 'DESC';
        $this->identifier = 'id';
        parent::__construct();

        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('Id'),
                'align' => 'center',
            ),
            'mp_id_seller' => array(
                'title' => $this->l('Seller Id'),
                'align' => 'center',
            ),
            'seller_firstname' => array(
                'title' => $this->l('Seller First Name'),
                'align' => 'center',
                'filter_key' => 'mpsi!seller_firstname'
            ),
            'plan_name' => array(
                'title' => $this->l('Plan Name'),
                'align' => 'center',
                'callback' => 'getPlanName',
                'search' => false
            ),
            'date_add' => array(
                'title' => $this->l('Requested date'),
                'align' => 'center',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            ),
            'active_from' => array(
                'title' => $this->l('Active From'),
                'align' => 'center',
                'type' => 'date',
            ),
            'expire_on' => array(
                'title' => $this->l('Expire On'),
                'align' => 'center',
                'type' => 'date',
            ),
            'plan_status_check' => array(
                'title' => $this->l('Plan Status'),
                'align' => 'center',
                'callback' => 'checkMembershipPlanStatus',
                'search' => false
            ),
        );
    }

    public function checkMembershipPlanStatus($is_this_current_plan, $arr_this_row)
    {
        if ($is_this_current_plan && $arr_this_row['active']) {
            return $this->l('Active');
        } elseif (!$is_this_current_plan && !$arr_this_row['active']) {
            return $this->l('Pending');
        } else {
            return $this->l('Expired');
        }
    }

    public function getPlanName($id)
    {
        $is_plan_exist = MarketplaceSellerplan::getPlanLangInfoByIdAndLangId($id, $this->context->language->id);
        if ($is_plan_exist) {
            return $is_plan_exist;
        } else {
            return $this->l('Free Plan');
        }
    }

    public function initToolBar()
    {
        parent::initToolBar();
        unset($this->toolbar_btn['new']);
    }

    public function renderForm()
    {
        $mp_seller_id = Tools::getValue('mp_seller_id');
        if (!$mp_seller_id) {
            $mp_seller_id = 0;
        }

        $adminlink = $this->context->link->getAdminLink('AdminSellerMembershipPlanDetail');
        $sellers = WkMpSeller::getAllSeller();
        $sellerList = array(
                array('id' => 0, 'name' => $this->l('Choose Seller')),
            );
        if ($sellers) {
            foreach ($sellers as $seller) {
                $sellerList[] = array(
                    'id' => $seller['id_seller'],
                    'name' => $seller['seller_firstname'].' '.$seller['seller_lastname'],
                );
            }
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Seller Plan Detail'),
            ),
            'input' => array(
                array(
                    'label' => $this->l('Select Seller'),
                    'type' => 'select',
                    'name' => 'seller_detail',
                    'options' => array(
                        'query' => $sellerList,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'seller_detail_link',
                ),
            ),
        );

        $this->fields_value = array(
            'seller_detail_link' => $adminlink,
            'seller_detail' => $mp_seller_id,
        );

        return parent::renderForm();
    }

    public function initContent()
    {
        if (!$this->ajax) {
            $this->content .= $this->renderForm();
        }

        $this->context->smarty->assign(
            array(
                'current' => self::$currentIndex,
                'token' => $this->token,
                'content' => $this->content,
            )
        );

        parent::initContent();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_MODULE_DIR_.'mpsellermembership/views/js/renderform.js');
    }
}
