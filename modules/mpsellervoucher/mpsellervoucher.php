<?php
/**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__).'/define.php';

class MpSellerVoucher extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    private $_html = '';
    public function __construct()
    {
        $this->name = 'mpsellervoucher';
        $this->version = '5.1.0';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->dependencies = array('marketplace');
        $this->controllers = array(
            'managevoucher',
            'sellercartrule',
        );

        parent::__construct();

        $this->displayName = $this->l('Marketplace Seller Voucher');
        $this->description = $this->l('Seller can create vouches for its products');
        $this->confirmUninstall = $this->l('Are you sure? All module data will be lost after uninstalling the module');
    }

    public function hookActionValidateOrder($params)
    {
        $obj_mp_cart_rule = new MpCartRule();
        $order = $params['order'];

        $order_cart_rules = $order->getCartRules();
        foreach ($order_cart_rules as $key => $cart_rule) {
            $mp_cart_rule = $obj_mp_cart_rule->getVoucherDetailByPsIdCartRule($cart_rule['id_cart_rule']);

            if ($mp_cart_rule) {
                $obj_mp_cart_rule = new MpCartRule($mp_cart_rule['id_mp_cart_rule']);
                $obj_mp_cart_rule->quantity = max(0, $obj_mp_cart_rule->quantity - 1);
                $obj_mp_cart_rule->update();
            }
        }
    }

    public function hookDisplayMPMyAccountMenu()
    {
        $customer_id = $this->context->customer->id;
        if ($customer_id) {
            $obj_marketplace_seller = new WkMpSeller();
            $mp_seller = $obj_marketplace_seller->getSellerDetailByCustomerId($customer_id);
            if ($mp_seller && $mp_seller['active']) {
                $this->context->smarty->assign('mpmenu', 0);

                return $this->display(__FILE__, 'seller_voucher_link.tpl');
            }
        }
    }

    public function hookDisplayMPMenuBottom()
    {
        $customer_id = $this->context->customer->id;
        if ($customer_id) {
            $obj_marketplace_seller = new WkMpSeller();
            $mp_seller = $obj_marketplace_seller->getSellerDetailByCustomerId($customer_id);
            if ($mp_seller && $mp_seller['active']) {
                $this->context->smarty->assign('mpmenu', 1);

                return $this->display(__FILE__, 'seller_voucher_link.tpl');
            }
        }
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue('MP_SELLER_VOUCHER_ADMIN_APPROVE', Tools::getValue('MP_SELLER_VOUCHER_ADMIN_APPROVE'));
            Configuration::updateValue('MP_SELLER_VOUCHER_UPDATE_ADMIN_APPROVE', Tools::getValue('MP_SELLER_VOUCHER_UPDATE_ADMIN_APPROVE'));
            Configuration::updateValue('MP_SELLER_CUSTOMER_VOUCHER_ALLOW', Tools::getValue('MP_SELLER_CUSTOMER_VOUCHER_ALLOW'));
            Configuration::updateValue('MP_VOUCHER_CUSTOMER_TYPE', Tools::getValue('MP_VOUCHER_CUSTOMER_TYPE'));
        }

        $module_config = $this->context->link->getAdminLink('AdminModules');
        Tools::redirectAdmin($module_config.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&conf=4');
    }

    public function getContent()
    {
        $this->context->controller->addJs($this->_path.'views/js/sellerVoucherAdmin.js');

        if (Tools::isSubmit('btnSubmit'))
            $this->_postProcess();
        else
            $this->_html .= '<br />';

        $this->_html .= $this->renderForm();

        return $this->_html;
    }

    public function renderForm()
    {
        //Get default language
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $cust_type = array($this->l('Seller Ordered Product Customer'), $this->l('All Prestashop Customers'));
        $customer_type_list = array();
        foreach ($cust_type as $key => $customer) {
            $type_val = $key + 1;
            $customer_type_list[$key]['id'] = 'cust_type'.$type_val;
            $customer_type_list[$key]['value'] = $type_val;
            $customer_type_list[$key]['label'] = $customer;
        }

        $fields_form = array();
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Configuration'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Newly added voucher need to be approved'),
                    'name' => 'MP_SELLER_VOUCHER_ADMIN_APPROVE',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                    'hint' => $this->l('If No, all marketplace seller\'s Voucher request is automatically approved'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Voucher update need to be approved'),
                    'name' => 'MP_SELLER_VOUCHER_UPDATE_ADMIN_APPROVE',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                    'hint' => $this->l('If No, all marketplace seller\'s Voucher request is automatically approved'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Seller can create vouchers for particular customer'),
                    'name' => 'MP_SELLER_CUSTOMER_VOUCHER_ALLOW',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                    'hint' => $this->l('If Yes, Seller can create voucher for particular customer'),
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Choose Customer Type'),
                    'name' => 'MP_VOUCHER_CUSTOMER_TYPE',
                    'required' => false,
                    'class' => 'mp_voucher_customer_type',
                    'values' => $customer_type_list,
                    'desc' => $this->l('Note : If you select "Seller Ordered Product Customer" then Sellers can view the list of only those customers who has ordered the products from that seller, but if you select "All Prestashop Customers" then all your prestashop customers list will be displayed to seller.'),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->submit_action = 'btnSubmit';
        $helper->table = $this->table;
        $helper->identifier = $this->identifier;
        //Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        //$this->fields_form = array();
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm($fields_form);
    }

    public function getConfigFieldsValues()
    {
        $config_vars = array(
            'MP_SELLER_VOUCHER_ADMIN_APPROVE' => Tools::getValue('MP_SELLER_VOUCHER_ADMIN_APPROVE', Configuration::get('MP_SELLER_VOUCHER_ADMIN_APPROVE')),
            'MP_SELLER_VOUCHER_UPDATE_ADMIN_APPROVE' => Tools::getValue('MP_SELLER_VOUCHER_UPDATE_ADMIN_APPROVE', Configuration::get('MP_SELLER_VOUCHER_UPDATE_ADMIN_APPROVE')),
            'MP_SELLER_CUSTOMER_VOUCHER_ALLOW' => Tools::getValue('MP_SELLER_CUSTOMER_VOUCHER_ALLOW', Configuration::get('MP_SELLER_CUSTOMER_VOUCHER_ALLOW')),
            'MP_VOUCHER_CUSTOMER_TYPE' => Tools::getValue('MP_VOUCHER_CUSTOMER_TYPE', Configuration::get('MP_VOUCHER_CUSTOMER_TYPE')),
        );

        return $config_vars;
    }

    public function install()
    {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return false;
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return false;
        }

        $sql = str_replace(array('PREFIX_',  'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($sql as $query) {
            if ($query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }

        if (!parent::install()
            || !$this->registerHook('displayMPMyAccountMenu')
            || !$this->registerHook('displayMPMenuBottom')
            || !$this->registerHook('actionValidateOrder')
            || !$this->callInstallTab()
        ) {
            return false;
        }

        // Set default config variable
        Configuration::updateValue('MP_SELLER_VOUCHER_ADMIN_APPROVE', 1);
        Configuration::updateValue('MP_SELLER_VOUCHER_UPDATE_ADMIN_APPROVE', 1);
        Configuration::updateValue('MP_SELLER_CUSTOMER_VOUCHER_ALLOW', 0);
        Configuration::updateValue('MP_VOUCHER_CUSTOMER_TYPE', 1);

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

    public function callInstallTab()
    {
        $this->installTab('AdminSellerVoucher', 'Manage Seller Vouchers', 'AdminMarketplaceManagement');
        return true;
    }

    public function deleteConfigKeys()
    {
        $var = array(
            'MP_SELLER_VOUCHER_ADMIN_APPROVE','MP_SELLER_VOUCHER_UPDATE_ADMIN_APPROVE'
            ,'MP_SELLER_CUSTOMER_VOUCHER_ALLOW','MP_VOUCHER_CUSTOMER_TYPE',
        );
        foreach ($var as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'mp_cart_rule`,
            `'._DB_PREFIX_.'mp_cart_rule_lang`,
            `'._DB_PREFIX_.'mp_cart_rule_group`,
            `'._DB_PREFIX_.'mp_cart_rule_country`,
            `'._DB_PREFIX_.'mp_cart_rule_product_rule`,
            `'._DB_PREFIX_.'mp_cart_rule_product_rule_group`,
            `'._DB_PREFIX_.'mp_cart_rule_product_rule_value`');
    }

    public function callUninstallTab()
    {
        $this->uninstallTab('AdminSellerVoucher');
        return true;
    }

    public function uninstallTab($class_name)
    {
        $id_tab = (int) Tab::getIdFromClassName($class_name);
        if ($id_tab) {
            $tab = new Tab($id_tab);

            return $tab->delete();
        } else {
            return false;
        }
    }

    public function uninstall($keep = true)
    {
        if (!parent::uninstall()
            || ($keep && !$this->deleteTables())
            || ($keep && !$this->deleteConfigKeys())
            || !$this->callUninstallTab()
            ) {
            return false;
        }

        return true;
    }

    public function reset()
    {
        if (!$this->uninstall(false)) {
            return false;
        }
        if (!$this->install(false)) {
            return false;
        }

        return true;
    }
}