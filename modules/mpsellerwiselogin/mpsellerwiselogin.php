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

require_once _PS_MODULE_DIR_.'/marketplace/classes/WkMpRequiredClasses.php';
require_once 'classes/LoginBlockPosition.php';
require_once 'classes/LoginConfigration.php';
require_once 'classes/LoginParentBlock.php';
require_once 'classes/LoginNoOfBlock.php';
require_once 'classes/LoginContent.php';
require_once 'classes/LoginTheme.php';

class MpSellerWiseLogin extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    private $_html = '';
    private $_postErrors = array();
    public function __construct()
    {
        $this->name = 'mpsellerwiselogin';
        $this->tab = 'front_office_features';
        $this->version = '5.0.0';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->dependencies = array('marketplace');

        parent::__construct();
        $this->displayName = $this->l('Marketplace Seller Wise Login');
        $this->description = $this->l('seller can login separately');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
    }

    private function _postValidation()
    {
        if (Tools::isSubmit('btnSubmit')) {
            if (Tools::getValue('MPS_URL_REWRITE_ADMIN_APPROVE')) {
                if (Tools::getValue('MP_SELLER_LOGIN_PREFIX') == '') {
                    $this->_postErrors[] = $this->l('Seller\'s login page url is required field.');
                } elseif (!Tools::link_rewrite(Tools::getValue('MP_SELLER_LOGIN_PREFIX'))) {
                    $this->_postErrors[] = $this->l('Seller\'s login page url is invalid.');
                }
            }
        }
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue('MPS_URL_REWRITE_ADMIN_APPROVE', Tools::getValue('MPS_URL_REWRITE_ADMIN_APPROVE'));
            Configuration::updateValue('MP_SELLER_LOGIN_PREFIX', Tools::getValue('MP_SELLER_LOGIN_PREFIX'));

            $module_config = $this->context->link->getAdminLink('AdminModules');
            Tools::redirectAdmin($module_config.'&configure='.$this->name.'&module_name='.$this->name.'&conf=4');
        }
    }

    public function getContent()
    {
        $this->_html = '';

        if (Tools::isSubmit('btnSubmit')) {
            $this->_postValidation();
            if (!count($this->_postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        } else {
            $this->_html .= '<br />';
        }

        $this->_html .= $this->renderForm();

        return $this->_html;
    }

    public function renderForm()
    {
        //Get default language
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $fields_form = array();

        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('URL Settings'),
                'icon' => 'icon-cogs',
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->l('Seller SEO URL'),
                    'name' => 'MPS_URL_REWRITE_ADMIN_APPROVE',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                    'hint' => $this->l('If Yes, Seller\'s Login page url will be fully seo compatible'),
                ],
                [
                    'type' => 'text',
                    'class' => 'mp_url_rewrite',
                    'label' => $this->l('Seller Login '),
                    'name' => 'MP_SELLER_LOGIN_PREFIX',
                    'hint' => $this->l('URL prefix for seller\'s Login page'),
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        //Generate Render Form in Configuration page
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

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm($fields_form);
    }

    public function getConfigFieldsValues()
    {
        return [
            'MPS_URL_REWRITE_ADMIN_APPROVE' => Configuration::get('MPS_URL_REWRITE_ADMIN_APPROVE'),
            'MP_SELLER_LOGIN_PREFIX' => Configuration::get('MP_SELLER_LOGIN_PREFIX'),
        ];
    }

    /**
     * [hookModuleRoutes - Make user pages url friendly].
     *
     * @return [type] [description]
     */
    public function hookModuleRoutes()
    {
        if (Configuration::get('MPS_URL_REWRITE_ADMIN_APPROVE')) {
            $sellerLogin = Tools::link_rewrite(Configuration::get('MP_SELLER_LOGIN_PREFIX'));

            return [
                'module-mpsellerwiselogin-sellerlogin' => [
                    'controller' => 'sellerlogin',
                    'rule' => "$sellerLogin",
                    'keywords' => [
                    ],
                    'params' => [
                        'fc' => 'module',
                        'module' => 'mpsellerwiselogin',
                        'controller' => 'sellerlogin',
                    ],
                ],
                'module-mpsellerwiselogin-sellercreation' => [
                    'controller' => 'sellercreation',
                    'rule' => "inscription",
                    'keywords' => [
                    ],
                    'params' => [
                        'fc' => 'module',
                        'module' => 'mpsellerwiselogin',
                        'controller' => 'sellercreation',
                    ],
                ],
            ];
        }
    }

    public function callInstallTab()
    {
        $this->installTab('AdminSelectTheme', 'Seller Login Page Theme', 'AdminMarketplaceManagement');
        $this->installTab('AdminCustomizeLogin', 'Manage Seller Login Page', 'AdminMarketplaceManagement');

        return true;
    }

    public function installTab($className, $tabName, $tabParentName = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }

        if ($tabParentName) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;

        return $tab->add();
    }

    public function install()
    {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return false;
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return false;
        }

        $sql = str_replace(
            ['PREFIX_', 'ENGINE_TYPE'],
            [_DB_PREFIX_, _MYSQL_ENGINE_],
            $sql
        );
        $sql = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($sql as $query) {
            if ($query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }

        for ($i = 1; $i <= 3; ++$i) {
            for ($j = 1; $j <= 4; ++$j) {
                Tools::copy(
                    _PS_MODULE_DIR_.$this->name.'/views/img/slices/theme'.$i.'/icon-'.$j.'.png',
                    _PS_IMG_DIR_.'cms/wktheme'.$i.'-icon'.$j.'.png'
                );
            }
        }
        $this->updateDbValues();

        if (!parent::install()
            || !$this->registerHook('displayTop')
            || !$this->registerHook('moduleRoutes')
            || !$this->callInstallTab()) {
            return false;
        }

        Configuration::updateValue('MPS_URL_REWRITE_ADMIN_APPROVE', 1);
        Configuration::updateValue('MP_SELLER_LOGIN_PREFIX', 'sellerlogin');

        return true;
    }

    public function updateDbValues()
    {
        $loginCofig = new LoginConfigration();
        $configValues = $loginCofig->getAllConfigration();
        if ($configValues) {
            foreach ($configValues as $config) {
                $loginCofig = new LoginConfigration($config['id']);
                foreach (Language::getLanguages(false) as $language) {
                    $loginCofig->meta_title[$language['id_lang']] = 'Seller Login';

                    $loginCofig->meta_description[$language['id_lang']] = 'If seller Want to sell your products online then from this page you can login, upload products and sell online';
                }
                $loginCofig->save();
            }
        }

        $loginContent = new LoginContent();
        $contentValues = $loginContent->getAllLoginContent();
        if ($contentValues) {
            foreach ($contentValues as $content) {
                $loginContent = new LoginContent($content['id']);
                foreach (Language::getLanguages(false) as $language) {
                    if ($content['id_block'] == 1 || $content['id_block'] == 7 || $content['id_block'] == 13) {
                        $loginContent->content[$language['id_lang']] = '<hr class="hr_style" /><p class="tc_cont">By sending the seller request you agree to abide by all the terms and conditions laid by us.</p>';
                    } elseif ($content['id_block'] == 2 || $content['id_block'] == 8 || $content['id_block'] == 14) {
                        $loginContent->content[$language['id_lang']] = '<p class="ftr_heading">Features</p><p class="ftr_desc">Doing Business With Us Is Really Easy</p><div class="row ftr_detail"><div class="col-sm-3"><div class="row"><div class="col-sm-12"><img src="'._PS_IMG_.'cms/wktheme1-icon1.png" alt="" width="155" height="155" /></div><div class="col-sm-12"><p class="ftr_subhead">Register Online as a Seller</p><p class="ftr_subdesc">Just fill the registration form and create your own online shop. Start selling</p></div></div></div> <div class="col-sm-3"> <div class="row"> <div class="col-sm-12"><img src="'._PS_IMG_.'cms/wktheme1-icon2.png" alt="" width="155" height="155" /></div> <div class="col-sm-12"> <p class="ftr_subhead">Add your products</p> <p class="ftr_subdesc">Upload your products with images and have your own attractive collection page</p> </div> </div> </div> <div class="col-sm-3"> <div class="row"> <div class="col-sm-12"><img src="'._PS_IMG_.'cms/wktheme1-icon3.png" alt="" width="155" height="155" /></div> <div class="col-sm-12"> <p class="ftr_subhead">Process the Orders</p> <p class="ftr_subdesc">Timely order processing will help you gain more customers</p> </div> </div> </div> <div class="col-sm-3"> <div class="row"> <div class="col-sm-12"><img src="'._PS_IMG_.'cms/wktheme1-icon4.png" alt="" width="155" height="155" /></div> <div class="col-sm-12"> <p class="ftr_subhead">Start Earning BIG</p> <p class="ftr_subdesc">Grow big and earn big by selling with us</p> </div> </div> </div> </div>';
                    } elseif ($content['id_block'] == 6 || $content['id_block'] == 12 || $content['id_block'] == 18) {
                        $loginContent->content[$language['id_lang']] = 'Take Your First Step Towards Online Selling';
                    }
                }
                $loginContent->save();
            }
        }
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'marketplace_login_theme`,
            `'._DB_PREFIX_.'marketplace_login_content`,
            `'._DB_PREFIX_.'marketplace_login_content_lang`,
            `'._DB_PREFIX_.'marketplace_login_configration`,
            `'._DB_PREFIX_.'marketplace_login_configration_lang`,
            `'._DB_PREFIX_.'marketplace_login_parent_block`,
            `'._DB_PREFIX_.'marketplace_login_block_position`'
        );
    }

    public function deleteModulesIcons()
    {
        for ($i = 1; $i <= 3; ++$i) {
            for ($j = 1; $j <= 4; ++$j) {
                unlink(_PS_IMG_DIR_.'cms/wktheme'.$i.'-icon'.$j.'.png');
            }
        }

        return true;
    }

    public function callUninstallTab()
    {
        $this->uninstallTab('AdminSelectTheme');
        $this->uninstallTab('AdminCustomizeLogin');

        return true;
    }

    public function uninstallTab($class_name)
    {
        $idTab = (int) Tab::getIdFromClassName($class_name);
        if ($idTab) {
            $tab = new Tab($idTab);

            return $tab->delete();
        } else {
            return false;
        }
    }

    public function uninstall($keep = true)
    {
        if (!parent::uninstall()
            || ($keep && !$this->deleteTables())
            || !$this->callUninstallTab()
            || !$this->deleteModulesIcons()
        ) {
            return false;
        }

        return true;
    }

    public function hookDisplayTop()
    {
        return $this->fetch('module:'.$this->name.'/views/templates/hook/Sellerloginbtn.tpl');
    }
}
