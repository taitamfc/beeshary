<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo Prestashop Blockleoblogs for Prestashop 1.6.x
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

use PrestaShop\PrestaShop\Adapter\ObjectPresenter;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Blockgrouptop extends Module implements WidgetInterface
{

    public function __construct()
    {
        $this->name = 'blockgrouptop';
        $this->tab = 'front_office_features';
        $this->version = '1.3.4';
        $this->author = 'LeoTheme';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Block Group Top');
        $this->description = $this->l('Adds a block allowing customers to select a language for your stores content.');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if (!parent::install() || !$this->registerLeoHook() || !Configuration::updateValue('LEO_BLOCKGROUPTOP_USEINFO', 0)) {
            return false;
        }
        $this->_installDataSample();
        Configuration::updateValue('AP_INSTALLED_BLOCKGROUPTOP', '1');
        return true;
    }
    
    public function uninstall()
    {
        return Configuration::deleteByName('LEO_BLOCKGROUPTOP_USEINFO') &&
            parent::uninstall() && $this->unregisterLeoHook();
    }

    private function _installDataSample()
    {
        if (!file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php')) {
            return false;
        }
        require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');

        $sample = new Datasample(1);
        return $sample->processImport($this->name);
    }

    public function getWidgetVariables($hookName, array $params)
    {
        # module validation
        // unset($params);
        $languages = Language::getLanguages(true, $this->context->shop->id);

        foreach ($languages as &$lang) {
            $lang['name_simple'] = $this->getNameSimple($lang['name']);
        }
        
        if (!count($languages)) {
            return false;
        }
        // $link = new Link();
        // $list_variable = array();
        // if ((int)Configuration::get('PS_REWRITING_SETTINGS')) {
            // $default_rewrite = array();
            // if (Dispatcher::getInstance()->getController() == 'product' && ($id_product = (int)Tools::getValue('id_product'))) {
                // $rewrite_infos = Product::getUrlRewriteInformations((int)$id_product);
                // foreach ($rewrite_infos as $infos) {
                    // $default_rewrite[$infos['id_lang']] = $link->getProductLink((int)$id_product, $infos['link_rewrite'], $infos['category_rewrite'], $infos['ean13'], (int)$infos['id_lang']);
                // }
            // }

            // if (Dispatcher::getInstance()->getController() == 'category' && ($id_category = (int)Tools::getValue('id_category'))) {
                // $rewrite_infos = Category::getUrlRewriteInformations((int)$id_category);
                // foreach ($rewrite_infos as $infos) {
                    // $default_rewrite[$infos['id_lang']] = $link->getCategoryLink((int)$id_category, $infos['link_rewrite'], $infos['id_lang']);
                // }
            // }

            // if (Dispatcher::getInstance()->getController() == 'cms' && (($id_cms = (int)Tools::getValue('id_cms')) || ($id_cms_category = (int)Tools::getValue('id_cms_category')))) {
                // $rewrite_infos = (isset($id_cms) && !isset($id_cms_category)) ? CMS::getUrlRewriteInformations($id_cms) : CMSCategory::getUrlRewriteInformations($id_cms_category);
                // foreach ($rewrite_infos as $infos) {
                    // $arr_link = (isset($id_cms) && !isset($id_cms_category)) ?
                            // $link->getCMSLink($id_cms, $infos['link_rewrite'], null, $infos['id_lang']) :
                            // $link->getCMSCategoryLink($id_cms_category, $infos['link_rewrite'], $infos['id_lang']);
                    // $default_rewrite[$infos['id_lang']] = $arr_link;
                // }
            // }
            // $list_variable['lang_rewrite_urls'] = $default_rewrite;
        // }

        // FIX error http://screencast.com/t/yFeNH4BnBWkL
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $countries = Country::getCountries($this->context->language->id, true);
        }
        
        $current_currency = null;
        $serializer = new ObjectPresenter;
        $currencies = array_map(
            function ($currency) use ($serializer, &$current_currency) {
                $currencyArray = $serializer->present($currency);

                // serializer doesn't see 'sign' because it is not a regular
                // ObjectModel field.
                $currencyArray['sign'] = $currency->sign;

                $url = $this->context->link->getLanguageLink($this->context->language->id);

                $extraParams = array(
                    'SubmitCurrency' => 1,
                    'id_currency' => $currency->id
                );

                $partialQueryString = http_build_query($extraParams);
                $separator = empty(parse_url($url)['query']) ? '?' : '&';

                $url .= $separator . $partialQueryString;

                $currencyArray['url'] = $url;

                if ($currency->id === $this->context->currency->id) {
                    $currencyArray['current'] = true;
                    $current_currency = $currencyArray;
                } else {
                    $currencyArray['current'] = false;
                }

                return $currencyArray;
            },
            Currency::getCurrencies(true, true)
        );
        
        $list_variable = array();
        $list_variable['lang_iso'] = $this->context->language->iso_code;
        $list_variable['countries'] = $countries;
        $list_variable['img_lang_url'] = _THEME_LANG_DIR_;
        $list_variable['currencies'] = $currencies;
        $list_variable['current_currency'] = $current_currency;
        $list_variable['cookie'] = $this->context->cookie;
        $list_variable['languages'] = $languages;
        $list_variable['current_language'] = array(
            'id_lang' => $this->context->language->id,
            'name' => $this->context->language->name,
            'name_simple' => $this->getNameSimple($this->context->language->name)
        );
        $list_variable['blockcurrencies_sign'] = $this->context->currency->sign;
        $list_variable['catalog_mode'] = Configuration::get('PS_CATALOG_MODE');
        
        //DONGND:: add parameters for user info
        $enable_userinfo = Configuration::get('LEO_BLOCKGROUPTOP_USEINFO');
        $list_variable['enable_userinfo'] = $enable_userinfo;
        if ($enable_userinfo == 1) {
            $logged = $this->context->customer->isLogged();

            if ($logged) {
                $customerName = $this->context->customer->firstname.' '.$this->context->customer->lastname;
            } else {
                $customerName = '';
            }

            $link = $this->context->link;
            
            $list_variable['logged'] = $logged;
            $list_variable['customerName'] = $customerName;
            $list_variable['logout_url'] = $link->getPageLink('index', true, null, 'mylogout');
            $list_variable['my_account_url'] = $link->getPageLink('my-account', true);
        }
        return $list_variable;
    }
    
    private function getNameSimple($name)
    {
        return preg_replace('/\s\(.*\)$/', '', $name);
    }

    /**
     * Returns module content for header
     *
     * @param array $params Parameters
     * @return string Content
     */
    // public function hookDisplayTop($params)
    // {

        
    // }
    
    public function renderWidget($hookName, array $params)
    {
       
        $this->smarty->assign($this->getWidgetVariables($hookName, $params));
        return $this->fetch('module:blockgrouptop/views/templates/hook/blockgrouptop.tpl');
    }

    // public function hookDisplayNav2($params)
    // {
        // if (!$this->_prepareHook($params)) {
            // return;
        // }
        // return $this->display(__FILE__, 'views/templates/hook/blockgrouptop.tpl');
    // }
    
    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submitBlockGroupTop')) {
            Configuration::updateValue('LEO_BLOCKGROUPTOP_USEINFO', (int)(Tools::getValue('LEO_BLOCKGROUPTOP_USEINFO')));
            $output .= $this->displayConfirmation($this->l('Settings updated.'));
        }
        return $this->renderForm();
    }
    
    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Block User Info'),
                        'name' => 'LEO_BLOCKGROUPTOP_USEINFO',
                        'is_bool' => true,
                        'desc' => $this->l('Enable/Disable Block User Info.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table =  $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitBlockGroupTop';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab
        .'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'LEO_BLOCKGROUPTOP_USEINFO' => (bool)Tools::getValue('LEO_BLOCKGROUPTOP_USEINFO', Configuration::get('LEO_BLOCKGROUPTOP_USEINFO')),
        );
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->registerStylesheet('modules-blockgrouptop-blockgrouptop', $this->getMediaDir().'css/blockgrouptop.css', array('media' => 'all', 'priority' => 150));
        $this->context->controller->registerJavascript('modules-blockgrouptop-blockgrouptop', $this->getMediaDir().'js/blockgrouptop.js', array('position' => 'bottom', 'priority' => 150));
    }
	
	/**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookActionAdminControllerSetMedia()
    {       
		$this->autoRestoreSampleData();
    }
    
    /**
     * Run only one when install/change Theme_of_Leo
     */
    public function hookActionAdminBefore($params)
    {
        if (isset($params) && isset($params['controller']) && isset($params['controller']->theme_manager)) {
            // Validate : call hook from theme_manager
        } else {
            // Other module call this hook -> duplicate data
            return;
        }
        
        $this->unregisterHook('actionAdminBefore');
        
        # FIX : update Prestashop by 1-Click module -> NOT NEED RESTORE DATABASE
        $ap_version = Configuration::get('AP_CURRENT_VERSION');
        if ($ap_version != false) {
            $ps_version = Configuration::get('PS_VERSION_DB');
            $versionCompare =  version_compare($ap_version, $ps_version);
            if ($versionCompare != 0) {
                // Just update Prestashop
                Configuration::updateValue('AP_CURRENT_VERSION', $ps_version);
                return;
            }
        }
        
        # WHENE INSTALL THEME, INSERT HOOK FROM DATASAMPLE IN THEME
        $hook_from_theme = false;
        if (file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php')) {
            require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');
            $sample = new Datasample();
            if ($sample->processHook($this->name)) {
                $hook_from_theme = true;
            }
        }
        
        # INSERT HOOK FROM MODULE_DATASAMPLE
        if ($hook_from_theme == false) {
            $this->registerLeoHook();
        }
        
        # WHEN INSTALL MODULE, NOT NEED RESTORE DATABASE IN THEME
        $install_module = (int)Configuration::get('AP_INSTALLED_BLOCKGROUPTOP', 0);
        if ($install_module) {
            Configuration::updateValue('AP_INSTALLED_BLOCKGROUPTOP', '0');    // next : allow restore sample
            return;
        }
    }
    
    /**
     * Common method
     * Resgister all hook for module
     */
    public function registerLeoHook()
    {
        $res = true;
        $res &= $this->registerHook('displayHeader');
        $res &= $this->registerHook('displayNav2');
        $res &= $this->registerHook('actionAdminControllerSetMedia');                       
        return $res;
    }
	
	/**
     * Common method
     * Unresgister all hook for module
     */
    public function unregisterLeoHook()
    {
        $res = true;
        $res &= $this->unregisterHook('displayHeader');
        $res &= $this->unregisterHook('displayNav2');
        $res &= $this->unregisterHook('actionAdminControllerSetMedia');       
        return $res;
    }
	
    
    // public function isCached($template, $cache_id = null, $compile_id = null)
    // {
        // if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {            
            // return false;
        // }
        // return parent::isCached($template, $cache_id, $compile_id);
    // }
	
	/**
     * FIX BUG 1.7.3.3 : install theme lose hook displayHome, displayLeoProfileProduct
     * because ajax not run hookActionAdminBefore();
     */
    public function autoRestoreSampleData()
    {		
        if(Hook::isModuleRegisteredOnHook($this, 'actionAdminBefore', (int)Context::getContext()->shop->id)) {			
            $theme_manager = new stdclass();
            $theme_manager->theme_manager = 'theme_manager';
            $this->hookActionAdminBefore(array(
                'controller' => $theme_manager,
            ));
        }
    }
	
    //DONGND:: update direction css, js, img for 1.7.4.0
    public function getMediaDir()
    {
        $media_dir = '';
        if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {            
                $media_dir = 'modules/'.$this->name.'/views/';
        }else{           
                $media_dir = 'modules/'.$this->name.'/';
        }
        return $media_dir;
    }
}
