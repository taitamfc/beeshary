<?php
/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/classes/WkGdprRequiredClasses.php';

class WkGdpr extends Module
{
    public function __construct()
    {
        $this->name = 'wkgdpr';
        $this->tab = 'administration';
        $this->version = '4.0.1';
        $this->author = 'Webkul';
        $this->secure_key = Tools::hash($this->name);
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('GDPR Compliance | Data Protection & EU Cookie Law');
        $this->description = $this->l('This module allows you to fully comply with the General Data Protection Regulation (GDPR) and EU Cookie Policy.');
        $this->confirmUninstall = $this->l('Are you sure? All module data will be lost after uninstalling the module');
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminGdprConfiguration'));
    }

    public function install()
    {
        $objAgreementData = new WkGdprAgreementData();
        $objModuleDb = new WkGdprDb();
        if (!parent::install()
            || !$objModuleDb->createTables()
            || !$this->registerHooks()
            || !$this->callInstallTab()
            || !$this->insertDefaultModuleData()
            || !$objAgreementData->insertInstalledModulesGdprAgreementData()
        ) {
            return false;
        }
        return true;
    }

    public function insertDefaultModuleData()
    {
        Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_ENABLE', 1);
        Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_BG_COLOR', '#ffffff');
        Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_TEXT_COLOR', '#484848');
        Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_BORDER_COLOR', '#e1e1e1');
        Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_BUTTON_BG_COLOR', '#4074ea');
        Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_COLOR', '#ffffff');
        Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_COLOR', '#3d72e7');
        Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_BUTTON_BG_HOVER_COLOR', '#4074ea');
        Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_HOVER_COLOR', '#ffffff');
        Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_HOVER_COLOR', '#4074ea');
        Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_POSITION', 'left');

        Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_IMAGE_SHOW', 1);
        Configuration::updateValue(
            'WK_GDPR_COOKIE_BLOCK_CONTENT',
            $this->l('We use cookies to personalize your experience. By continuing to visit this website you agree to our use of cookies.')
        );

        Configuration::updateValue('WK_GDPR_CUSTOMER_DATA_DELETE_APPROVE', 1);
        Configuration::updateValue('WK_GDPR_ADMIN_MAIL_DATA_UPDATE_REQUEST', 1);
        Configuration::updateValue('WK_GDPR_ADMIN_MAIL_DATA_ERASURE_REQUEST', 1);
        Configuration::updateValue(
            'WK_GDPR_DEFAULT_AGREEMENT_CONTENT',
            $this->l('You must agree with terms and conditions as per GDPR compliances.')
        );
        return true;
    }

    public function callInstallTab()
    {
        $this->installTab('AdminGdprCompliance', 'GDPR Compliance');
        $this->installTab('AdminGdprManagement', 'Manage GDPR', 'AdminGdprCompliance');
        $this->installTab('AdminGdprConfiguration', 'Configurations', 'AdminGdprManagement');
        $this->installTab('AdminGdprAgreementDataManagement', 'Manage GDPR Agreement Data', 'AdminGdprManagement');
        $this->installTab('AdminGdprCustomerDataManagement', 'Manage Customer Data', 'AdminGdprManagement');

        return true;
    }

    public function installTab($className, $tabName, $tabParentName = false, $needTab = true)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();

        if ($className =='AdminGdprManagement') { //Tab name for which you want to add icon
            $tab->icon = 'gavel'; //Material Icon name
        }

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }

        if ($tabParentName) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } elseif (!$needTab) {
            $tab->id_parent = -1;
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;

        return $tab->add();
    }

    public function registerHooks()
    {
        return $this->registerHook(
            array(
                'actionModuleRegisterHookAfter',
                'actionFrontControllerSetMedia',
                'displayGDPRConsent',
                'displayCustomerAccount',
                'additionalCustomerFormFields',
                'validateCustomerFormFields',
                'displayHeader',
                'displayFooter',
                'actionModuleUnRegisterHookAfter',
            )
        );
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerJavascript(
            'module-wkgdpr-gdpr-form-checkbox-js',
            'modules/'.$this->name.'/views/js/front/wk_gdpr_form_checkbox.js',
            array('position' => 'bottom', 'priority' => 999)
        );
        $this->context->controller->registerStylesheet(
            'module-wkgdpr-gdpr-form-checkbox-css',
            'modules/'.$this->name.'/views/css/front/wk_gdpr_form_checkbox.css'
        );

        if (Configuration::get('WK_GDPR_COOKIE_BLOCK_ENABLE')
            && empty($this->context->cookie->cookie_accepted_customer)
            && (Configuration::get('WK_GDPR_COOKIE_BLOCK_POSITION') == 'left'
            || Configuration::get('WK_GDPR_COOKIE_BLOCK_POSITION') == 'right')
        ) {
            Media::addJsDef(
                array(
                    'wkGdprControlsLink' => $this->context->link->getModuleLink($this->name, 'wkcustomergdprcontrols'),
                )
            );
            $this->context->controller->registerJavascript(
                'module-wkgdpr-gdpr-cookie-block-js',
                'modules/'.$this->name.'/views/js/front/wk_gdpr_cookie_block.js',
                array('position' => 'bottom', 'priority' => 999)
            );

            $this->context->controller->registerStylesheet(
                'module-wkgdpr-gdpr-cookie-block-css',
                'modules/'.$this->name.'/views/css/front/wk_gdpr_cookie_block.css'
            );
        }
    }

    public function hookDisplayHeader()
    {
        // if customer data is erased, check and remove from context
        if ($this->context->customer->isLogged()) {
            if (WkGdprAnonymousCustomer::customerDataErased($this->context->customer->id)) {
                $this->context->customer->logout();
                Tools::redirect($this->context->link->getPageLink('index'));
            }
        }

        $this->context->smarty->assign('isWkGdpr', 1);
    }

    public function hookDisplayFooter()
    {
        // cookie accept popup to accept as per cookie law
        if (Configuration::get('WK_GDPR_COOKIE_BLOCK_ENABLE')
            && empty($this->context->cookie->cookie_accepted_customer)
            && (Configuration::get('WK_GDPR_COOKIE_BLOCK_POSITION') == 'left'
            || Configuration::get('WK_GDPR_COOKIE_BLOCK_POSITION') == 'right')
        ) {
            $this->context->smarty->assign(
                array(
                    'WK_GDPR_COOKIE_BLOCK_BG_COLOR' => Configuration::get('WK_GDPR_COOKIE_BLOCK_BG_COLOR'),
                    'WK_GDPR_COOKIE_BLOCK_TEXT_COLOR' => Configuration::get('WK_GDPR_COOKIE_BLOCK_TEXT_COLOR'),
                    'WK_GDPR_COOKIE_BLOCK_BORDER_COLOR' => Configuration::get('WK_GDPR_COOKIE_BLOCK_BORDER_COLOR'),
                    'WK_GDPR_COOKIE_BLOCK_CONTENT' => Configuration::get('WK_GDPR_COOKIE_BLOCK_CONTENT'),
                    'WK_GDPR_COOKIE_BLOCK_IMAGE_SHOW' => Configuration::get('WK_GDPR_COOKIE_BLOCK_IMAGE_SHOW'),
                    'WK_GDPR_COOKIE_BLOCK_ENABLE' => Configuration::get('WK_GDPR_COOKIE_BLOCK_ENABLE'),
                    'WK_GDPR_COOKIE_BLOCK_BUTTON_BG_COLOR' => Configuration::get(
                        'WK_GDPR_COOKIE_BLOCK_BUTTON_BG_COLOR'
                    ),
                    'WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_COLOR' => Configuration::get(
                        'WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_COLOR'
                    ),
                    'WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_COLOR' => Configuration::get(
                        'WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_COLOR'
                    ),
                    'WK_GDPR_COOKIE_BLOCK_BUTTON_BG_HOVER_COLOR' => Configuration::get(
                        'WK_GDPR_COOKIE_BLOCK_BUTTON_BG_HOVER_COLOR'
                    ),
                    'WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_HOVER_COLOR' => Configuration::get(
                        'WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_HOVER_COLOR'
                    ),
                    'WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_HOVER_COLOR' => Configuration::get(
                        'WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_HOVER_COLOR'
                    ),
                    'wk_cookie_block_token' => Tools::getToken(false),
                    'WK_GDPR_COOKIE_BLOCK_POSITION' => Configuration::get('WK_GDPR_COOKIE_BLOCK_POSITION'),
                    'cookie_block_icon' => $this->_path.'views/img/uploads/wk_cookie_block_img.png',
                    'cookie_cross_icon' => $this->_path.'views/img/icons/cross-Icon.png'
                )
            );

            return $this->fetch('module:'.$this->name.'/views/templates/hook/wkCookieBlock.tpl');
        }
    }

    public function hookDisplayCustomerAccount()
    {
        return $this->fetch('module:'.$this->name.'/views/templates/hook/wkCustomerGdprLink.tpl');
    }

    public function hookActionModuleRegisterHookAfter($params)
    {
        $objModule = $params['object'];
        if (isset($params['hook_name']) && $params['hook_name'] == 'registerGDPRConsent') {
            if (Validate::isLoadedObject($objModule)) {
                if ($idModule = $objModule->id) {
                    $objAgreementData = new WkGdprAgreementData();
                    if (!$objAgreementData->getModulesGdprAgreementData($idModule)) {
                        $objAgreementData->id_module = $idModule;
                        $objAgreementData->active = 1;
                        foreach (Language::getLanguages(true) as $lang) {
                            $objAgreementData->agreement_content[$lang['id_lang']] = Configuration::get(
                                'WK_GDPR_DEFAULT_AGREEMENT_CONTENT'
                            );
                        }
                        $objAgreementData->save();
                    }
                }
            }
        }
    }

    public function hookDisplayGDPRConsent($params)
    {
        if (isset($params['id_module'])) {
            if ($idModule = $params['id_module']) {
                $objAgreementData = new WkGdprAgreementData();
                if ($gdprAgreementData = $objAgreementData->getModulesGdprAgreementData($idModule)) {
                    if ($gdprAgreementData['active']) {
                        $this->context->smarty->assign(
                            array(
                                'gdprAgreementContent' => $gdprAgreementData['agreement_content'],
                                'id_agreement_data' => $gdprAgreementData['id_agreement_data'],
                            )
                        );
                        return $this->fetch(
                            'module:'.$this->name.'/views/templates/hook/wkModuleGdprAgreementField.tpl'
                        );
                    }
                }
            }
        }
    }

    /**
     * Display hook after the customer registration form.
     * @return html
     */
    public function hookAdditionalCustomerFormFields($params)
    {
        $objAgreementData = new WkGdprAgreementData();
        if ($gdprAgreementData = $objAgreementData->getModulesGdprAgreementData(
            WkGdprAgreementData::WK_CUSTOMER_REGISTER_FORM
        )) {
            if ($gdprAgreementData['active']) {
                $formFields = array();
                $formFields['wk_gdpr_agreement'] = (new FormField())
                    ->setName('wk_gdpr_agreement')
                    ->setType('checkbox')
                    ->setRequired(true)
                    ->setLabel($gdprAgreementData['agreement_content']);

                return $formFields;
            }
        }
    }

    /**
     * Validate customer form before submit
     *
     * @param [type] $params
     * @return void
     */
    public function hookValidateCustomerFormFields($params)
    {
        // Validate referal code and referal email fields in the create account page..
        if ($params['fields']) {
            $wkGDPRAgreementFormCheckboxField = reset($params['fields']);
            if ($wkGDPRAgreementFormCheckboxField->getName() == 'wk_gdpr_agreement') {
                if (!$wkGDPRAgreementFormCheckboxField->getValue()) {
                    $wkGDPRAgreementFormCheckboxField->addError(
                        $this->l('You must agree to the GDPR terms and conditions before register')
                    );
                }
            }
        }
    }


    public function hookActionModuleUnRegisterHookAfter($params)
    {
        $objModule = $params['object'];
        if (isset($params['hook_name']) && $params['hook_name'] == 'registerGDPRConsent') {
            if (Validate::isLoadedObject($objModule)) {
                if ($idModule = $objModule->id) {
                    $objAgreementData = new WkGdprAgreementData();
                    if ($agreementData = $objAgreementData->getModulesGdprAgreementData($idModule)) {
                        $objAgreementData = new WkGdprAgreementData($agreementData['id_agreement_data']);
                        $objAgreementData->delete();
                    }
                }
            }
        }
    }

    public function uninstall()
    {
        $objModuleDb = new WkGdprDb();
        if (!parent::uninstall()
            || !$this->uninstallTab()
            || !$objModuleDb->dropTables()
            || !$this->deleteConfigurationKey()
        ) {
            return false;
        }
        return true;
    }

    public function deleteConfigurationKey()
    {
        $configKeys = array(
            'WK_GDPR_DEFAULT_AGREEMENT_CONTENT',
            'WK_CUSTOMER_DATA_DELETE_APPROVE',
            'WK_GDPR_ADMIN_MAIL_DATA_ERASURE_REQUEST',
            'WK_GDPR_ADMIN_MAIL_DATA_ERASURE_REQUEST',
            'WK_GDPR_COOKIE_BLOCK_ENABLE',
            'WK_GDPR_COOKIE_BLOCK_BG_COLOR',
            'WK_GDPR_COOKIE_BLOCK_TEXT_COLOR',
            'WK_GDPR_COOKIE_BLOCK_BORDER_COLOR',
            'WK_GDPR_COOKIE_BLOCK_BUTTON_BG_COLOR',
            'WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_COLOR',
            'WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_COLOR',
            'WK_GDPR_COOKIE_BLOCK_BUTTON_BG_HOVER_COLOR',
            'WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_HOVER_COLOR',
            'WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_HOVER_COLOR',
            'WK_GDPR_COOKIE_BLOCK_POSITION',
            'WK_GDPR_COOKIE_BLOCK_IMAGE_SHOW',
            'WK_GDPR_COOKIE_BLOCK_CONTENT'
        );
        foreach ($configKeys as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }
        return true;
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }
        return true;
    }
}
