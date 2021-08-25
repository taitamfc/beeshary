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

class AdminGdprAgreementDataManagementController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->table = 'wk_gdpr_agreement_data';
        $this->className = 'WkGdprAgreementData';
        $this->identifier = 'id_agreement_data';
        $this->display = 'add';
        parent::__construct();
        $this->page_header_toolbar_title = $this->l('Manage GDPR Agreement Data');
    }

    public function renderForm()
    {
        $smartyVars = array();
        $objAgreementData = new WkGdprAgreementData();
        $gdprModulesData = $objAgreementData->getModulesGdprAgreementData(
            0,
            2,
            0,
            WkGdprAgreementData::WK_ONLY_MODULE_AGREEMENT_DATA,
            1
        );
        $gdprPsFormsData = $objAgreementData->getModulesGdprAgreementData(
            0,
            2,
            0,
            WkGdprAgreementData::WK_ONLY_PS_CORE_AGREEMENT_DATA,
            1
        );
        $smartyVars['active_tab'] = Tools::getValue('tab');
        $smartyVars['psGDPRForms'] = $gdprPsFormsData;
        $smartyVars['gdprModulesAgreementData'] = $gdprModulesData;
        $currentLangId = Configuration::get('PS_LANG_DEFAULT');
        $smartyVars['languages'] = Language::getLanguages(false);
        $smartyVars['currentLang'] = Language::getLanguage((int) $currentLangId);

        $this->context->smarty->assign($smartyVars);
        $this->fields_form = array(
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );
        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitModulesAgreementContents')) {
            $languages = Language::getLanguages(true);
            $enableModuleAgreement = Tools::getValue('enable_gdpr_agreement');
            $defaultAgreementContent = Tools::getValue('gdpr_agreement_content');
            $defaultLang = Configuration::get('PS_LANG_DEFAULT');
            if ($defaultAgreementContent) {
                $objDefaultLang = new language($defaultLang);
                foreach ($defaultAgreementContent as $content) {
                    if (!trim($content[$defaultLang])) {
                        $this->errors[] = sprintf(
                            $this->l('Please enter content in all the agreement Content fields atleast in %s'),
                            $objDefaultLang->name
                        );
                        break;
                    }
                }
            }
            if (empty($this->errors)) {
                foreach ($defaultAgreementContent as $idAgreementData => $content) {
                    $objAgreementData = new WkGdprAgreementData($idAgreementData);
                    $objAgreementData->active = $enableModuleAgreement[$idAgreementData];
                    foreach ($languages as $lang) {
                        if (isset($content[$lang['id_lang']]) && $content[$lang['id_lang']]) {
                            $agreementContent = $content[$lang['id_lang']];
                        } else {
                            $agreementContent = $content[$defaultLang];
                        }
                        $objAgreementData->agreement_content[$lang['id_lang']] = $agreementContent;
                    }
                    $objAgreementData->save();
                }
                Tools::redirectAdmin(
                    self::$currentIndex.'&conf=4&token='.$this->token.'&tab='.Tools::getValue('active_tab')
                );
            }
        }
        parent::postProcess();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $jsVars = array(
            'path_css' => _THEME_CSS_DIR_,
            'ad' => __PS_BASE_URI__.basename(_PS_ADMIN_DIR_),
            'autoload_rte' => true,
            'lang' => true,
            'iso' => $this->context->language->iso_code,
        );
        Media::addJsDef($jsVars);
        //tinymce
        $this->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
        if (version_compare(_PS_VERSION_, '1.6.0.11', '>')) {
            $this->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
        } else {
            $this->addJS(_PS_JS_DIR_.'tinymce.inc.js');
        }

        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/admin/wk_gdpr_module_data.js');
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/admin/wk_gdpr_module_data.css');
    }
}
