<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SAS Comptoir du Code
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SAS Comptoir du Code is strictly forbidden.
 * In order to obtain a license, please contact us: contact@comptoirducode.com
 *
 * @author    Vincent - Comptoir du Code
 * @copyright Copyright(c) 2015-2016 SAS Comptoir du Code
 * @license   Commercial license
 * @package   cdc_googletagmanager
 *
 * Project Name : Google Tag Manager Enhanced Ecommerce (UA) Tracking
 * Created By  : Comptoir du Code
 * Created On  : 2016-06-02
 * Support : https://addons.prestashop.com/contact-community.php?id_product=23806
 *
 * Based on Google recommendations
 *  - https://developers.google.com/tag-manager/devguide
 *  - https://developers.google.com/tag-manager/enhanced-ecommerce
 */

if (!defined('_CDCGTM_DIR_'))
    define('_CDCGTM_DIR_', dirname(__FILE__));

include_once(_CDCGTM_DIR_.'/classes/gtm/includes.php');
include_once(_CDCGTM_DIR_.'/classes/CdcGtmOrderLog.php');
include_once(_CDCGTM_DIR_.'/services/CdcTools.php');
include_once(_CDCGTM_DIR_.'/services/PrestashopUtils.php');

define('_CDCGTM_PRICE_DECIMAL_', 2);

// fix json old PHP version
if (version_compare(phpversion(), '5.4.0', '<')) {
    define('JSON_UNESCAPED_SLASHES',      64);   // Since PHP 5.4.0
    define('JSON_PRETTY_PRINT',           128);  // Since PHP 5.4.0
}

class cdc_googletagmanager extends Module
{

    // <config>
    private $order_id_field = "id"; // [id,reference]
    // </config>

	const PAGE_PRODUCT = 'product';
    const PAGE_HOME = 'index';
    const PAGE_SEARCH = 'search';
    const PAGE_CATEGORY = 'category';
    const PAGE_CART = 'order';
    const PAGE_CART_17 = 'cart';
    const PAGE_OPC = 'orderopc';
    const PAGE_OPC_SUPERCHECKOUT = 'supercheckout';
    const PAGE_PAYMENT = 'payment';
    const PAGE_ORDERCONFIRMATION = 'orderconfirmation';

    private $debug_enabled = null;
    private $debug_stack = array();
    private $shop_id = null;

    // override page values (controller, id_category ...)
    private $override_page_values = array();

    // maximum products added in datalayer in a given page (0 to disable)
    private $max_products_datalayer = null; 

    private $async_user_info = false;
    private $userid_enable = null;

    private $gtm_enable = null;
    private $gtm_id = null;
    private $dataLayer = null;
    private $cartProducts = array();
    private $opc_enabled = false;
    private $ee_generated = false;
    private $displayOrderConfirmation = false;
    private $displayOrderConfirmationOrderObj = null;

    private $remarketing_enable = null;
    private $remarketing_generated = false;
    private $product_identifier = null;
    private $product_id_prefix = null;
    private $category_hierarchy = false;
    private $product_name_field = null;
    private $product_id_field = null;
    private $category_name_field = null;
    private $data_language = null;

    // order validation page
    private $isOrderValidationPage;
    private $orderValidationDetails;

    // event sent when datalayer ready
    private $eventDatalayerReady = "datalayer_ready";

    // mod test to check if hooks are present
    private $mod_test_hooks_enabled = false;

    // configuration variables Google Customer Reviews
    private $greviews_config = array(
        'GCR_ENABLE', 'GCR_BADGE_CODE', 'GCR_MERCHANT_ID',
        'GCR_ORDER_CODE', 'GCR_DELIVERY_DAYS', 'GCR_BADGE_POSITION');


    // configs are set in construct
    private $conf = array();

	/* Initialiaze Default Settings */
	public function __construct()
	{
        $this->name = 'cdc_googletagmanager';
        $this->tab = 'analytics_stats';
        $this->author = 'Comptoir du Code';
        $this->version = '4.9.5';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.7');
        $this->module_key = '5d5819d54f9765c68428b1cf8b357338';
        $this->author_address = '0xb1c5eebc8be3c1aa53e8953238bb8f04c55b287f';

        // configs
        $this->conf = array(
            // available id types
            'id_types' => array('id', 'reference', 'ean13', 'upc'),

            // available display for variant id
            'display-variant-id' => array(
                'when-set' => $this->l('Display variant ID only when set'),
                'always' => $this->l('Always display variant ID (0 if no variant)'),
                'never' => $this->l('Never display variant ID')
            )
        );


        parent::__construct();
        $this->displayName = $this->l('CdC Google Tag Manager Enhanced Ecommerce');
        $this->description = $this->l('Integration of Google Tag Manager - Enhanced Ecommerce (UA) Tracking and Google Customer reviews');

        // load config
        $this->gtm_id = self::getConfigValue('GTMID');
        $this->gtm_enable = self::getConfigValue('ENABLE');
        $this->remarketing_enable = self::getConfigValue('REMARKETING_ENABLE');
        $this->product_identifier = self::getConfigValue('REMARKETING_PRODUCTID');
        $this->product_id_prefix = self::getConfigValue('REMARKETING_PRODUCTPREF');
        $this->display_variant_id = self::getConfigValue('DISPLAY_VARIANT_ID');
        $this->max_products_datalayer = self::getConfigValue('MAX_CAT_ITEMS');
        $this->category_hierarchy = (bool) self::getConfigValue('CATEGORY_HIERARCHY', false);
        $this->product_id_field = self::getConfigValue('PRODUCT_ID_FIELD');
        $this->product_name_field = self::getConfigValue('PRODUCT_NAME_FIELD');
        $this->category_name_field = self::getConfigValue('CATEGORY_NAME_FIELD');
        $this->data_language = (int) self::getConfigValue('DATA_LANGUAGE');

        $this->userid_enable = self::getConfigValue('ENABLE_USERID');
        if($this->userid_enable) {
            $this->async_user_info = (int) self::getConfigValue('ASYNC_USER_INFO');
        }

        $this->dataLayer = new Gtm_DataLayer();

        $this->shop_id = (int)Context::getContext()->shop->id;
        //Configuration::deleteByName(self::getConfigName('CUSTOM_HOOKS'));

        // init debugger
        $this->debugManager();
        if($this->debug_enabled) {
            $this->addDebug("v".$this->version." | GTM ". ($this->gtm_enable ? 'enabled' : 'disabled') ." | ID: ".$this->gtm_id);
        }

        // debug hook installation
        if(Tools::getIsset('cdcgtm_debug_hooks')) {
            $this->debugHooksMod();
        }

        // test if order validation page (for performance, only one call / page)
        $this->isOrderValidationPage = $this->isOrderValidationPage();
	}

	/* Installing module */
	public function install()
	{
        $result = parent::install()
            && CdcGtmOrderLog::createTable()
            && $this->registerHook('header')
            && $this->registerHook('displayAfterTitle')
            && $this->registerHook('displayAfterBodyOpeningTag')
            && $this->registerHook('displayBeforeBodyClosingTag')
            && $this->registerHook('actionObjectOrderDetailUpdateAfter')
            && $this->registerHook('backOfficeHeader')
            && $this->registerHook('updateOrderStatus')
            && $this->registerHook('displayOrderConfirmation');

            // set default configuration
            $this->setDefaultConfiguration();

        // version specific
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $result &= $this->addTab("AdminCdcGoogletagmanagerOrders", "GTM Orders", "AdminParentStats");
        } else {
            $result &= $this->addTab("AdminCdcGoogletagmanagerOrders", "GTM Orders", "AdminAdvancedParameters");
        }

        // reset custom hooks check
        Configuration::deleteByName(self::getConfigName('CUSTOM_HOOKS'));

        return $result;
	}

	/* Un installing module */
	public function uninstall()
	{
        $result = parent::uninstall()
            && CdcGtmOrderLog::deleteTable()
            && $this->unregisterHook('displayAfterTitle')
            && $this->unregisterHook('displayAfterBodyOpeningTag')
            && $this->unregisterHook('displayBeforeBodyClosingTag')
            && $this->unregisterHook('header')
            && $this->unregisterHook('actionObjectOrderDetailUpdateAfter')
            && $this->unregisterHook('backOfficeHeader')
            && $this->unregisterHook('updateOrderStatus')
            && $this->unregisterHook('displayOrderConfirmation')
            && $this->deleteTab("AdminCdcGoogletagmanagerOrders")
            && $this->deleteConfiguration();

        // dynamic remarketing
        $result &= Configuration::deleteByName(self::getConfigName('REMARKETING_ENABLE'))
            && Configuration::deleteByName(self::getConfigName('REMARKETING_PRODUCTID'))
            && Configuration::deleteByName(self::getConfigName('REMARKETING_PRODUCTPREF'));

        // google customer reviews
        foreach ($this->greviews_config as $greviews_config_var) {
            $result &= Configuration::deleteByName(self::getConfigName($greviews_config_var));
        }

        return $result;
	}

    /**
     * Delete module configuration
     */
    public function deleteConfiguration() {
        return Configuration::deleteByName(self::getConfigName('CUSTOM_HOOKS'))
            && Configuration::deleteByName(self::getConfigName('REFUNDS_Q'))
            && Configuration::deleteByName(self::getConfigName('ENABLE'))
            && Configuration::deleteByName(self::getConfigName('ENABLE_RESEND'))
            && Configuration::deleteByName(self::getConfigName('RESEND_DAYS'))
            && Configuration::deleteByName(self::getConfigName('ENABLE_USERID'))
            && Configuration::deleteByName(self::getConfigName('ENABLE_GUESTID'))
            && Configuration::deleteByName(self::getConfigName('ASYNC_USER_INFO'))
            && Configuration::deleteByName(self::getConfigName('DISPLAY_VARIANT_ID'))
            && Configuration::deleteByName(self::getConfigName('CATEGORY_HIERARCHY'))
            && Configuration::deleteByName(self::getConfigName('PRODUCT_NAME_FIELD'))
            && Configuration::deleteByName(self::getConfigName('PRODUCT_ID_FIELD'))
            && Configuration::deleteByName(self::getConfigName('DATA_LANGUAGE'))
            && Configuration::deleteByName(self::getConfigName('CATEGORY_NAME_FIELD'));
    }

    /**
     * Set default values to configuration
     * If force = false : do not override current values
     * If force = true : override current values
     */
    public function setDefaultConfiguration($force = false) {
        $updatedValues = 0;
        $defaultConfig = array(
            'ENABLE' => 1,
            'ENABLE_RESEND' => 1,
            'RESEND_DAYS' => 7,
            'ENABLE_USERID' => 0,
            'ENABLE_GUESTID' => 0,
            'ASYNC_USER_INFO' => 0,
            'MAX_CAT_ITEMS' => 30
        );

        foreach($defaultConfig as $key => $defaultValue) {
            if($force || (Configuration::get($this->getConfigName($key)) === false)) {
                Configuration::updateValue( self::getConfigName($key), $defaultValue);
                $updatedValues++;
            }
        }

        return $updatedValues;
    }

    /**
     * add tab
     */
    public function addTab($className, $name, $parentClassName) {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();
        $tab->name[(int)(Configuration::get('PS_LANG_DEFAULT'))] = $this->l($name);
        $tab->module = $this->name;
        $tab->id_parent = (int)Tab::getIdFromClassName($parentClassName);
        return $tab->add();
    }

    /**
     * Delete tab
     */
    protected function deleteTab($className) {
        $id_tab = (int)Tab::getIdFromClassName($className);
        $allTableDeleted = true;
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $allTableDeleted = $tab->delete();
        } else {
            return false;
        }
        return $allTableDeleted;
    }


	/* Handle module settings in admin panel */
	public function getContent()
	{
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->context->controller->addCss($this->_path.'views/css/admin-theme.css');
            $this->context->controller->addCss($this->_path.'views/css/bootsrap.extend.css');
            $this->context->controller->addCss($this->_path.'views/css/font-awesome.min.css');
            $this->context->controller->addJS("https://code.jquery.com/jquery-1.12.4.min.js");
            $this->context->controller->addJS("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js");
        }

		$output = null;

        // set global smarty variable
        $this->smarty->assign(array(
            'form_action' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules')
        ));

    	/**
    	 * Manage form submit
    	 */
        if (Tools::isSubmit('submit'.$this->name))
        {
            // General settings
            $this->saveValueToConfiguration("GTMID");
            $this->gtm_id = self::getConfigValue('GTMID');
            $this->saveValueToConfiguration("ENABLE");
            $this->gtm_enable = (int) self::getConfigValue('ENABLE');
            $this->saveValueToConfiguration("ENABLE_RESEND");
            $this->saveValueToConfiguration("RESEND_DAYS");
            $this->saveValueToConfiguration("MAX_CAT_ITEMS");
            $this->saveValueToConfiguration("DISPLAY_VARIANT_ID");
            $this->saveValueToConfiguration("CATEGORY_HIERARCHY");
            $this->saveValueToConfiguration("PRODUCT_NAME_FIELD");
            $this->saveValueToConfiguration("PRODUCT_ID_FIELD");
            $this->saveValueToConfiguration("CATEGORY_NAME_FIELD");
            $this->saveValueToConfiguration("ENABLE_USERID");
            $this->saveValueToConfiguration("ENABLE_GUESTID");
            $this->saveValueToConfiguration("DATA_LANGUAGE");
            $this->saveValueToConfiguration("ASYNC_USER_INFO");

            // remarketing config
            $this->saveValueToConfiguration("REMARKETING_ENABLE");
            $this->remarketing_enable = (int) self::getConfigValue('REMARKETING_ENABLE');
            $this->saveValueToConfiguration("REMARKETING_PRODUCTID");
            $this->saveValueToConfiguration("REMARKETING_PRODUCTPREF");

            // Google customer reviews settings
            foreach ($this->greviews_config as $greviews_config_var) {
                $this->saveValueToConfiguration($greviews_config_var);
            }

            $output .= $this->displayConfirmation($this->l($this->displayName.' module settings have been Updated'));
        }


        // install custom hooks
        elseif(Tools::getIsset('install_hooks')) {
            if($this->installCustomHooks()) {
                $output .= $this->displayConfirmation($this->l('Hooks are correctly installed!'));
            } else {
                $output .= $this->displayError($this->l('Hooks are not correctly installed :-('));
                $this->smarty->assign(array(
                    'troubleshooting' => true
                ));
            }
        }

        // user force installed hooks (done manually)
        elseif(Tools::getIsset('force_installed_hooks')) {
            Configuration::updateValue( self::getConfigName('CUSTOM_HOOKS'), 1);
        }

        // user force installed hooks (done manually)
        elseif(Tools::getIsset('force_check_hooks')) {
            Configuration::updateValue( self::getConfigName('CUSTOM_HOOKS'), 0);
        }


        // if variable custom hook false, redirect to install page
        if(!self::getConfigValue('CUSTOM_HOOKS')) {
            // url to check if hooks are presents
            /*$check_url = "";
            if(Tools::usingSecureMode()) {
                // we use contact page instead of homepage to have the https version of the page
                // if the site is in mixed mod (http + https)
                $check_url = $this->context->link->getPageLink('contact',null,null,null,false,null,true);
            } else {
                $check_url = $this->context->link->getPageLink('index',null,null,null,false,null,true);
            }*/
            $check_url = $this->context->link->getPageLink('index',null,null,null,false,null,true);
            $check_url .= '?cdcgtm_debug_hooks&nocache='.time();
            

            $this->smarty->assign(array(
                'multishop' => Shop::isFeatureActive(),
                'check_url' => $check_url
            ));
            return $output.$this->display(__FILE__, 'views/templates/admin/install.tpl');
        }


    	return $output.$this->displayForm();
	}


	/**
	 * Get a post / get value and save it to Configuration
	 */
	protected function saveValueToConfiguration($name) {
		Configuration::updateValue( self::getConfigName($name), trim( (string)Tools::getValue(self::getConfigName($name)) ) );
	}

    public static function getConfigName($name) {
        return 'CDC_GTM_'.Tools::strtoupper($name);
    }

    public static function getConfigValue($name) {
        return Configuration::get(self::getConfigName($name));
    }

    /**
     * Display the configuration form
     */
    public function displayForm()
    {
        $tpl_vars = array(
            'CDC_GTM_ENABLE' => (int) self::getConfigValue('ENABLE'),
            'CDC_GTM_GTMID' => self::getConfigValue('GTMID'),
            'CDC_GTM_ENABLE_RESEND' => (int) self::getConfigValue('ENABLE_RESEND'),
            'CDC_GTM_RESEND_DAYS' => (int) self::getConfigValue('RESEND_DAYS'),
            'CDC_GTM_MAX_CAT_ITEMS' => (int) self::getConfigValue('MAX_CAT_ITEMS'),
            'CDC_GTM_DISPLAY_VARIANT_ID_LIST' => $this->conf['display-variant-id'],
            'CDC_GTM_DISPLAY_VARIANT_ID' => self::getConfigValue('DISPLAY_VARIANT_ID'),
            'CDC_GTM_CATEGORY_HIERARCHY' => self::getConfigValue('CATEGORY_HIERARCHY'),
            'CDC_GTM_PRODUCT_NAME_FIELD' => self::getConfigValue('PRODUCT_NAME_FIELD'),
            'CDC_GTM_PRODUCT_ID_FIELD' => self::getConfigValue('PRODUCT_ID_FIELD'),
            'CDC_GTM_CATEGORY_NAME_FIELD' => self::getConfigValue('CATEGORY_NAME_FIELD'),
            'CDC_GTM_DATA_LANGUAGES' => $this->getLanguages(),
            'CDC_GTM_DATA_LANGUAGE' => self::getConfigValue('DATA_LANGUAGE'),
            'CDC_GTM_ENABLE_USERID' => (int) self::getConfigValue('ENABLE_USERID'),
            'CDC_GTM_ENABLE_GUESTID' => (int) self::getConfigValue('ENABLE_GUESTID'),
            'CDC_GTM_ASYNC_USER_INFO' => (int) self::getConfigValue('ASYNC_USER_INFO'),
            'CDC_GTM_REMARKETING_ENABLE' => self::getConfigValue('REMARKETING_ENABLE'),
            'CDC_GTM_REMARKETING_PRODUCTID' => self::getConfigValue('REMARKETING_PRODUCTID'),
            'CDC_GTM_REMARKETING_PRODUCTPREF' => self::getConfigValue('REMARKETING_PRODUCTPREF'),
            'CDC_PS_VERSION' => _PS_VERSION_,
            'CDC_GTM_ID_TYPES' => $this->conf['id_types']
        );

        // G customer reviews configuration
        foreach ($this->greviews_config as $greviews_config_var) {
            $tpl_vars[self::getConfigName($greviews_config_var)] = self::getConfigValue($greviews_config_var);
        }

        $this->smarty->assign($tpl_vars);
        return $this->display(__FILE__, 'views/templates/admin/config.tpl');
    }



    /***************************************************************************
     * HOOK
     */

    public function hookHeader()
    {
        // Ajax Cart
        if($this->gtm_enable && !empty($this->gtm_id)) {
            $ajaxCartEnabled = Configuration::get('PS_BLOCK_CART_AJAX', 0);
            $this->context->smarty->assign(array(
                    'ajaxcartEnabled' => $ajaxCartEnabled,
                    'ajaxGetProductUrl' => Context::getContext()->link->getModuleLink('cdc_googletagmanager', 'async', array('obj' => 'cart-action'), null, null, null, true),
            ));

            if($ajaxCartEnabled) {
                $this->context->controller->addJS($this->_path.'views/js/ajaxcart.js');
            }
        }
    }

    /**
     * hook immediately after <title></title>
     * add gtm tag
     */
    public function hookDisplayAfterTitle($params)
    {
        // test if hook is enabled
        if($this->mod_test_hooks_enabled) {
            $this->displayHookPresent('displayAfterTitle');
        }

        // Google tag manager main Tag
        if($this->gtm_enable && !empty($this->gtm_id)) {
            $this->generateEnhancedEcommerce();

            if($this->remarketing_enable) {
                $this->generateRemarketingParameters();
            }

            $this->generateGtmTag();

            return $this->display(__FILE__, 'gtm_tag.tpl');
        }
    }



    /**
     * hook after opening body
     */
    public function hookDisplayAfterBodyOpeningTag($params)
    {
        // test if hook is enabled
        if($this->mod_test_hooks_enabled) {
            $this->displayHookPresent('displayAfterBodyOpeningTag');
        }

        $content = "";

        // Google tag manager no script
        if($this->gtm_enable && !empty($this->gtm_id)) {
            $this->context->smarty->assign(array('gtm_id' => $this->gtm_id));
            $content .= $this->display(__FILE__, 'gtm_tag_noscript.tpl');
        }

        // return content
        if(!empty($content)) {
            return $content;
        }
    }


    /**
     * hook before </body> closing tag
     */
    public function hookDisplayBeforeBodyClosingTag($params)
    {
        // test if hook is enabled
        if($this->mod_test_hooks_enabled) {
            $this->displayHookPresent('displayBeforeBodyClosingTag');
        }

        $content = "";

        // add JS with datalayer for OPC
        if($this->gtm_enable && !empty($this->gtm_id)) {
            if($this->opc_enabled) {
                if($this->generateEnhancedEcommerce()) {
                    $this->generateGtmTag();
                }
                $content .= $this->display(__FILE__, 'opc.tpl');
            }
        }


        // Google Customer Reviews
        $enable_greviews = (int) self::getConfigValue('GCR_ENABLE');
        if($enable_greviews) {

            // Google Customer Reviews Order confirmation
            $enable_greviews_order = (int) self::getConfigValue('GCR_ORDER_CODE');
            if($enable_greviews_order && $this->isOrderValidationPage) {
                $this->greviewsGenerateOrderConfirmationCode();
                $content .= $this->display(__FILE__, 'greviews_order_confirmation_code.tpl');
            }

            else {
                // Google Customer Reviews Badge
                $enable_greviews_code = (int) self::getConfigValue('GCR_BADGE_CODE');
                if($enable_greviews_code) {
                    $tpl_vars = array();
                    foreach ($this->greviews_config as $greviews_config_var) {
                        $tpl_vars[$greviews_config_var] = self::getConfigValue($greviews_config_var);
                    }
                    $this->context->smarty->assign($tpl_vars);
                    $content .= $this->display(__FILE__, 'greviews_badge_tag.tpl');
                }
            }
        }


        // display debug
        if($this->debug_enabled) {
            // add POST and GET variables
            $this->addDebug("POST");
            $this->addDebug(print_r($_POST, true));
            $this->addDebug("GET");
            $this->addDebug(print_r($_GET, true));
            
            $content .= $this->displayDebug();
        }

         if(!empty($content)) {
            return $content;
        }
    }

    /**
     * Hook for partial refunds
     */
    public function hookActionObjectOrderDetailUpdateAfter($params)
    {
        $orderDetail = $params['object'];

         // if no quantity refunded, no action
        if (empty($orderDetail->product_quantity_refunded)) {
            return;
        }

        $order_id = $orderDetail->id_order;
        $product = new Product($orderDetail->product_id);
        $variant = new Combination($orderDetail->product_attribute_id);
        $product_full_identifier = $this->getProductId($product, $variant);

        $gtmOrderLog = CdcGtmOrderLog::getByOrderId($order_id);
        if(Validate::isLoadedObject($gtmOrderLog)) {
            if(CdcGtmOrderLog::isRefundable($order_id, $gtmOrderLog->id_shop, $product_full_identifier)) {
                $this->addRefund($order_id, $gtmOrderLog->id_shop, $product_full_identifier, $orderDetail->product_quantity_refunded);
            }
        }
    }


    /**
     * HOOK order state change
     * Used for full refund
     * @param type $params : array containing order details
     */
    public function hookUpdateOrderStatus($params)
    {
        $order_id = $params['id_order'];
        $newOrderStatus = $params['newOrderStatus']->id;

        $refund_state_id = array(
            Configuration::get('PS_OS_CANCELED'),
            Configuration::get('PS_OS_REFUND'),
            Configuration::get('PS_OS_ERROR')
        );

        $gtmOrderLog = CdcGtmOrderLog::getByOrderId($order_id);
        if(Validate::isLoadedObject($gtmOrderLog)) {
            if(in_array($newOrderStatus, $refund_state_id)) {
                if(CdcGtmOrderLog::isRefundable($order_id, $gtmOrderLog->id_shop)) {
                    $this->addRefund($order_id, $gtmOrderLog->id_shop);
                }
            }
        }
    }


    /**
     * Display GTM tag in backoffice for :
     *  - order refund
     */
    public function hookBackOfficeHeader()
    {
        // Google tag manager
        if($this->gtm_enable && !empty($this->gtm_id)) {
            // disable async loading. useless in backoffice, no full cache
            $this->async_user_info = 0;

            // check if some orders has to be resent
            if(Configuration::get($this->getConfigName('ENABLE_RESEND'))) {
                $this->resendNotSentOrders();
            }

            // if refund or order to resend, display GTM Tag in backoffice header
            if($this->generateEnhancedEcommerceRefund() // refund
                || $this->generateEnhancedEcommerceOrderResend() // order resend
                ) {
                // change name for event when datalayer is ready
                // to avoid to trigger pageviews in Back Office
                $this->eventDatalayerReady = "bo_".$this->eventDatalayerReady;

                $this->generateGtmTag();
                return $this->display(__FILE__, 'gtm_tag.tpl');
            }


        }
    }


    /**
     * hookDisplayOrderConfirmation
     *
     */
    public function hookDisplayOrderConfirmation(array $params)
    {
        $this->displayOrderConfirmation = true;
        try {
            if(!empty($params['objOrder'])) {
                $this->displayOrderConfirmationOrderObj = json_decode(json_encode($params['objOrder']), true);
            }
        } catch (Exception $e) {
            // nothing
        }
    }



    /***************************************************************************
     * GENERATE GTM TAG
     */


    /**
     * Take a dataLayer object in param
     * Return JSON equivalent for the webpage
     */
    public function dataLayerToJson($dataLayer) {
        $dataLayerJs = "";

        if(count((array)$dataLayer)) {
            $eventCallback = false;
            if(!empty($dataLayer->eventCallback)) {
                // remove double quotes before encoding
                $dataLayer->eventCallback = str_replace('"', '@@', $dataLayer->eventCallback);
                // add function delimiter
                $dataLayer->eventCallback = "!##".$dataLayer->eventCallback."##!";
                $eventCallback = true;
                if(empty($dataLayer->event)) {
                    // create random event to fire callback
                    $dataLayer->event = "eventForCallback";
                }
            }

            // encode datalayer
            $json_options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
            if($this->debug_enabled) {
                $json_options = $json_options | JSON_PRETTY_PRINT;
            }
            $dataLayerJs = json_encode($dataLayer, $json_options);

            if($eventCallback) {
                // remove double quotes
                $dataLayerJs = str_replace('##!"','', str_replace('"!##','', $dataLayerJs));
                // add double quote back
                $dataLayerJs = str_replace('@@', '"', $dataLayerJs);
            }
        }

        return $dataLayerJs;
    }


    public function generateGtmTag()
    {
        if(!$this->gtm_enable || empty($this->gtm_id)) {
            return "";
        }

        // generate datalayer JSON
        $dataLayerJs = $this->dataLayerToJson($this->dataLayer);

        // debug
        if($this->debug_enabled) {
            $this->addDebug($dataLayerJs);

            if($this->async_user_info) {
                $this->addDebug('<div>[ASYNC USER INFOS]</div><div id="cdcgtm_debug_asynccall">Loading user infos datalayer...</div>');
            }
        }

        // if order validation, save datalayer in log
        if($this->isOrderValidationPage) {
            $this->saveDataLayerInLog($dataLayerJs);
        }

        // model
        $model = array(
            'gtm_id' => $this->gtm_id,
            'dataLayer' => $dataLayerJs,
            'async_user_info' => $this->async_user_info,
            'async_url' => Context::getContext()->link->getModuleLink('cdc_googletagmanager', 'async', array('obj' => 'user'), null, null, null, true),
            'event_datalayer_ready' => $this->eventDatalayerReady,
            'gtm_debug' => $this->debug_enabled
        );
        $this->context->smarty->assign($model);

        return $model;
    }



    /***************************************************************************
     * ENHANCED ECOMMERCE FUNCTIONS
     */

	public function generateEnhancedEcommerce() {
        if(!$this->ee_generated) {
    		// general informations
    		$controller = $this->getValue('controller');
    		$this->dataLayer->pageCategory = $controller;
    		$this->dataLayer->ecommerce = new Gtm_Ecommerce($this->context->currency->iso_code);

            // add user informations
            if(!$this->async_user_info) {
                $this->dataLayer = $this->addUserInfosToDatalayer($this->dataLayer);
            }

            // order confirmation page
            if($this->isOrderValidationPage) {
                $this->generateEnhancedEcommerceOrderConfirmation();
            }
            // other pages
            else {
                // test is OPC
                if($this->isOnePageCheckout()) {
                    $this->opc_enabled = true;
                    $controller = self::PAGE_OPC;
                }

                switch ($controller) {
                    case self::PAGE_HOME:
                        $this->generateEnhancedEcommerceHomepage();
                        break;

                    case self::PAGE_PRODUCT:
                        $this->generateEnhancedEcommerceProduct();
                        break;

                    case self::PAGE_PAYMENT:
                    case self::PAGE_CART:
                    case self::PAGE_CART_17:
                    case self::PAGE_OPC:
                        $this->generateEnhancedEcommerceCart();
                        break;

                    case self::PAGE_CATEGORY:
                        $this->generateEnhancedEcommerceProductList($controller);
                        break;

                    case self::PAGE_SEARCH:
                        $this->datalayerPageSearch();
                        $this->generateEnhancedEcommerceProductList($controller);
                        break;
                        
                }
            }

            $this->ee_generated = true;
            return true;
        }
        return false;
	}

    /**
     * add user id and guest id informations to datalayer
     *
     * return Gtm_DataLayer with user informations
     */
    public function addUserInfosToDatalayer($dataLayer) {
        // user information
        if($this->userid_enable) {

            if(!empty($this->context->cookie->id_customer)) {
                $dataLayer->userLogged = 1;
                $dataLayer->userId = (string) $this->context->cookie->id_customer;

                // get customer groups
                $customer = new Customer($dataLayer->userId);
                if(Validate::isLoadedObject($customer)) {
                    $groups = $customer->getGroups();
                    $groupsName = array();
                    foreach ($groups as $group_id) {
                        $group = new Group($group_id);
                        if(Validate::isLoadedObject($group)) {
                            $groupsName[] = $group->name[(int)(Configuration::get('PS_LANG_DEFAULT'))];
                        }
                    }
                    $dataLayer->userGroups = $groupsName;
                }
                
            } elseif(Configuration::get($this->getConfigName('ENABLE_GUESTID'))) {
                $dataLayer->userLogged = 0;

                if(!empty($this->context->cookie->id_guest)) {
                    $dataLayer->userId = (string) "guest_".$this->context->cookie->id_guest;
                } else {
                    if(empty($this->context->cookie->user_rand_id)) {
                        $this->context->cookie->user_rand_id = rand(100000,999999);
                    }
                    $dataLayer->userId = (string) "guest_".$this->context->cookie->user_rand_id;
                }
            }
        }

        return $dataLayer;
    }

	/**
	 * Enhanced Ecommerce
	 * Order Confirmation
	 * https://developers.google.com/tag-manager/enhanced-ecommerce#purchases
	 */
	protected function generateEnhancedEcommerceOrderConfirmation($id_cart = null, $force_resend = false) {

        // get orders from cart
        if(!$id_cart) {
          $id_cart = $this->getCartFromOrderConfirmation();
        }

        $orders = PrestashopUtils::getOrdersByCartId($id_cart);

        // create action field
        $actionField = new Gtm_ActionField();
        $revenue = 0.0;
        $tax = 0.0;
        $shipping = 0.0;

        // products array
        $order_products = array();

        // store last handled order
        $lastOrder = null;

        $orders_list = array();
        foreach ($orders as $id_order) {
            $id_order = $id_order['id_order'];
            $order = new Order($id_order);

            if(Validate::isLoadedObject($order)) {
                // test if order is not in error and not already sent ?
                if($order->current_state != Configuration::get('PS_OS_ERROR')
                    && ($force_resend || !CdcGtmOrderLog::isSent($id_order, $this->shop_id))) {
                    $lastOrder = $order;

                    $orders_list[] = $id_order;
                    // create log
                    $this->createGtmOrderLog($id_order, $this->shop_id);
                    $this->orderValidationDetails[] = array('id_order' => $id_order);

                    // build id if multi order
                    $order_id_field = $this->order_id_field;
                    $current_ref_order = $order->$order_id_field;
                    if(empty($actionField->id)) {
                        // reference empty, set reference
                        $actionField->id = (string) $current_ref_order;
                    } else {
                        // reference not empty, add it if not yet added
                        $array_ids = explode(',', $actionField->id);
                        if(!in_array($current_ref_order, $array_ids)) {
                            $array_ids[] = $current_ref_order;
                            $actionField->id = implode(',', $array_ids);
                        }
                    }

                    $revenue += $order->total_paid_tax_incl; // total transaction inc tax and shipping
                    $tax += $order->total_paid_tax_incl - $order->total_paid_tax_excl;
                    $shipping += $order->total_shipping_tax_incl;

                    // discount - only one
                    if(empty($actionField->coupon)) {
                        $discounts = $order->getCartRules();
                        if(is_array($discounts) && count($discounts) > 0) {
                            $actionField->coupon = $discounts[0]['id_cart_rule'].'-'.$discounts[0]['name'];
                        }
                    }

                    // products
                    $products = $order->getProducts();
                    foreach ($products as $product) {
                        $dataLayerProduct = $this->getDataLayerProduct($product);
                        if($dataLayerProduct) {
                            $dataLayerProduct->quantity = (int) $product['product_quantity'];
                            $dataLayerProduct->price = (string) round($product['unit_price_tax_incl'], _CDCGTM_PRICE_DECIMAL_);
                            $dataLayerProduct->price_tax_exc = (string) round($product['unit_price_tax_excl'], _CDCGTM_PRICE_DECIMAL_);
                            $dataLayerProduct->removeNull();
                            $order_products[] = $dataLayerProduct;
                        }
                    }

                }
            }
        }

        // fill computed data in actionField
        $actionField->revenue = (string) $revenue;
        $actionField->tax = (string) $tax;
        $actionField->shipping = (string) $shipping;

        // get customer informations
        if(Validate::isLoadedObject($lastOrder)) {
            $past_orders = PrestashopUtils::countCustomerOtherOrders($lastOrder->id_customer, $lastOrder->reference);
            $customer_infos = new stdClass();
            $customer_infos->new = $past_orders > 0 ? 0 : 1;
            $customer_infos->past_orders = $past_orders;
            $this->dataLayer->customer = $customer_infos;
        }

        // add informations to layer
        if(count($orders_list) > 0) {
            $this->dataLayer->ecommerce->purchase = new Gtm_Purchase();
            $this->dataLayer->ecommerce->purchase->actionField = $actionField;
            $this->dataLayer->ecommerce->purchase->products = $order_products;

            $callback_name = $force_resend ? 'orderresend' : 'orderconfirmation';
            $this->dataLayer->eventCallback = $this->getCallback($callback_name, array(
                'orders' => $orders_list,
                'id_shop' => $this->shop_id
            ));
            $this->dataLayer->event = "order_confirmation";
        }

    }

    /**
     * Return cart ID
     */
    protected function getCartFromOrderConfirmation() {
        $id_cart = null;

        // get cart from hook displayOrderConfirmation
        if(isset($this->displayOrderConfirmationOrderObj['id_cart'])) {
            $id_cart = (int) $this->displayOrderConfirmationOrderObj['id_cart'];
        }

        // get cart from params
        if(!$id_cart) {
            $id_cart = (int)(Tools::getValue('id_cart'));
        }

        // get cart from param order
        if(!$id_cart) {
            $order = new Order((int)(Tools::getValue('id_order')));
            if(Validate::isLoadedObject($order)) {
                $id_cart = $order->id_cart;
            }
        }

        return $id_cart;
    }


    /***************************************************************************
     * GOOGLE REMARKETING
     * Official documentation: https://developers.google.com/adwords-remarketing-tag/parameters#retail
     */

    protected function generateRemarketingParameters() {
        if(!$this->remarketing_generated) {
            // general informations
            $controller = $this->getValue('controller');
            $this->dataLayer->google_tag_params = new Gtm_GoogleTagParams();
            $pagetype = $controller; // home,searchresults,category,product,cart,purchase,other

            // order confirmation page
            if($this->isOrderValidationPage) {
                $pagetype = 'purchase';
                $this->dataLayer->google_tag_params->ecomm_prodid = $this->remarketingGetProductListIdsFromCart();
                $this->dataLayer->google_tag_params->ecomm_totalvalue = (float) round($this->getTotalUnitPriceFromCart(), _CDCGTM_PRICE_DECIMAL_);
                $this->dataLayer->google_tag_params->ecomm_totalvalue_tax_exc = (float) round($this->getTotalUnitPriceFromCart(false), _CDCGTM_PRICE_DECIMAL_);
            }
            // other pages
            else {
                // test is OPC
                if($this->isOnePageCheckout()) {
                    $this->opc_enabled = true;
                    $controller = self::PAGE_OPC;
                }

                switch ($controller) {
                    case self::PAGE_HOME:
                        $pagetype = 'home';
                        break;

                    case self::PAGE_PRODUCT:
                        $product = $this->getDisplayedProduct();
                        if(Validate::isLoadedObject($product)) {
                            $this->dataLayer->google_tag_params->ecomm_prodid = $this->remarketingGetProductIdentifier($product);
                            $this->dataLayer->google_tag_params->ecomm_totalvalue = (float) round((float) Product::getPriceStatic($product->id, true, $product->id_product_attribute), _CDCGTM_PRICE_DECIMAL_);
                            $this->dataLayer->google_tag_params->ecomm_totalvalue_tax_exc = (float) round((float) Product::getPriceStatic($product->id, false, $product->id_product_attribute), _CDCGTM_PRICE_DECIMAL_);
                            $this->dataLayer->google_tag_params->ecomm_category = $this->getCategoryName($product->id_category_default);
                        }
                        break;

                    case self::PAGE_PAYMENT:
                    case self::PAGE_CART:
                    case self::PAGE_CART_17:
                    case self::PAGE_OPC:
                        $pagetype = 'cart';
                        $this->dataLayer->google_tag_params->ecomm_prodid = $this->remarketingGetProductListIdsFromCart();
                        $this->dataLayer->google_tag_params->ecomm_totalvalue = (float) round($this->getTotalUnitPriceFromCart(), _CDCGTM_PRICE_DECIMAL_);
                        $this->dataLayer->google_tag_params->ecomm_totalvalue_tax_exc = (float) round($this->getTotalUnitPriceFromCart(false), _CDCGTM_PRICE_DECIMAL_);
                        break;

                    case self::PAGE_CATEGORY:
                        $this->dataLayer->google_tag_params->ecomm_category = $this->getCategoryName((int) Tools::getValue('id_category'));
                        break;
                    case self::PAGE_SEARCH:
                        $pagetype = 'searchresults';
                        break;
                    default:
                        $pagetype = 'other';
                        break;
                }
            }

            $this->dataLayer->google_tag_params->ecomm_pagetype = $pagetype;
            $this->dataLayer->google_tag_params->removeNull();
            $this->remarketing_generated = true;
        }
    }


    /**
     * Return a list of product id from the cart
     */
    protected function remarketingGetProductListIdsFromCart() {
        $products_list = array();

        // get products list from cart
        $products = $this->getProductListFromCart();
        if(is_array($products) && count($products)) {
            foreach ($products as $product) {
                $product_identifier = $this->remarketingGetProductIdentifier($product);
                if($product_identifier) {
                    $products_list[] = $product_identifier;
                }
            }
        }

        return $products_list;
    }


    /**
     * Return the product identifier for GA EE datalayer
     * 
     * @param   $product              
     * @param   $id_product_attribute 
     * @return                        
     */
    protected function getProductId($product, $combination) {
        return $this->getProductIdentifier($product, $combination, $this->product_id_field);
    }


    /**
     * Return the product identifier for remarketing
     * 
     * @param  $product
     * @return
     */
    protected function remarketingGetProductIdentifier($product) {
        $id_product_attribute = $this->getProductAttributeId($product);
        $combination = null;
        if($id_product_attribute) {
            $combination = new Combination($id_product_attribute);
        }

        return $this->getProductIdentifier($product, $combination, $this->product_identifier, $this->product_id_prefix);
    }


    /**
     * Return the product identifier of the given product
     * 
     * @param  array $product
     * @return string product identifier
     */
    protected function getProductIdentifier($product, $combination = null, $id_type = null, $product_id_prefix = null) {
        $product = (array) $product;
        $product_identifier = null;

        if(empty($id_type) || !in_array($id_type, $this->conf['id_types'])) {
            $id_type = 'id';
        }

        // combination
        if(!is_null($combination) && Validate::isLoadedObject($combination)) {
            $id_product_attribute = $combination->id;
        } else {
            $combination = null;
            $id_product_attribute = null;
        }

        // if id product is "id_product" instead of "id"
        if(empty($product['id']) && !empty($product['id_product'])) {
            $product['id'] = $product['id_product'];
        }

        // get product identifier
        switch ($id_type) {
            case 'id':
                $product_id = !empty($product['id']) ? $product['id'] : 0;
                $product_identifier = null;

                // display product variant
                if($this->display_variant_id != 'never') {
                    if(!$id_product_attribute && !empty($product['id_product_attribute'])) {
                        $id_product_attribute = $product['id_product_attribute'];
                    }

                    if($id_product_attribute) {
                        $product_identifier = $product_id.'-'.$id_product_attribute;
                    } elseif($this->display_variant_id == 'always') {
                        $product_identifier = $product_id.'-'.Product::getDefaultAttribute($product_id);
                    }
                }

                $product_identifier = $product_identifier ? $product_identifier : ''.$product_id;
                break;
            
            case 'reference':
            case 'ean13':
            case 'upc':
                if($combination && !empty($combination->$id_type)) {
                    $product_identifier = $combination->$id_type;
                } elseif(!empty($product[$id_type])) {
                    $product_identifier = $product[$id_type];
                }

                break;

            default:
                $product_identifier = 'ERROR';
                break;
        }

        // add prefix
        if(!empty($product_id_prefix)) {
            $product_id_prefix = (string) $product_id_prefix;
            $product_id_prefix = str_replace('{lang}', $this->context->language->iso_code, $product_id_prefix);
            $product_id_prefix = str_replace('{LANG}', strtoupper($this->context->language->iso_code), $product_id_prefix);

            $product_identifier = $product_id_prefix.$product_identifier;
        }


        return $product_identifier;
    }


    /**
     * Return the product name
     * 
     * @param  $product
     * @param  $id_lang
     * @return String
     */
    protected function getProductName($product, $product_name_field = null) {
        $base_product = new Product($product->id);
        $product_name_field = empty($product_name_field) ? $this->product_name_field : $product_name_field;
        $id_lang = $this->getDataLanguage();

        switch ($product_name_field) {
            case 'link_rewrite':
                if(!empty($base_product->link_rewrite)) {
                    if(is_array($base_product->link_rewrite)) {
                        $product_name = !empty($base_product->link_rewrite[$id_lang]) ? $base_product->link_rewrite[$id_lang] : $base_product->link_rewrite[0];
                    } else {
                        $product_name = $base_product->link_rewrite;
                    }
                }
                break;

            case 'id':
                $product_name = $base_product->id;
                break;
            
            default:
                $product_name = $this->cleanString(Product::getProductName($base_product->id, 0, $id_lang));
                break;
        }

        $product_name = trim($product_name);

        if(empty($product_name)) {
            $product_name = 'Unknown product name';
        }
        return $product_name;
    }


    /**
     * Return the sum of each product found on cart
     */
    protected function getTotalUnitPriceFromCart($include_tax = true) {
        $product_total = 0.0;

        // price tax exc. or inc.
        $price_field = 'price';
        if($include_tax) {
            $price_field = 'price_wt';
        }

        // get products list from smarty
        $products = $this->getProductListFromCart();
        if(is_array($products) && count($products)) {
            foreach ($products as $p) {
                if(isset($p[$price_field])) {
                    $product_total += (float) $p[$price_field];
                }
            }
        }

        return $product_total;
    }


    /**
     * Return the name of the category
     * 
     * @param  Category $category
     * @return String category nale
     */
    protected function getCategoryName($id_category) {
        $categoryName = '';
        $id_lang = $this->getDataLanguage();
        $category = new Category($id_category, $id_lang);
        switch (
            $this->category_name_field) {
            case 'link_rewrite':
                $categoryName = !empty($category->link_rewrite) ? $category->link_rewrite : null;
                break;

            case 'id':
                $categoryName = $category->id;
                break;
            
            default:
                if(Validate::isLoadedObject($category)) {
                    if($this->category_hierarchy) {
                        // get parents
                        $c_parents = $category->getParentsCategories($id_lang);
                        $c_parents_names = array();
                        foreach ($c_parents as $c_parent) {
                            if(!$c_parent['is_root_category']) {
                                $c_parents_names[] = $this->cleanString($c_parent['name']);
                            }
                        }
                        $c_parents_names = array_reverse($c_parents_names);
                        $categoryName = implode('/', $c_parents_names);
                    }
                    else {
                        $categoryName = $this->cleanString($category->name);
                    }
                }
                break;
        }

        $categoryName = trim($categoryName);

        if(empty($categoryName)) {
            $categoryName = 'Unknown category name';
        }
        
        return $categoryName;
    }

    /**
     * Google Customer Reviews
     * Generate Order confirmation code
     * https://support.google.com/merchants/answer/7106244
     */
    protected function greviewsGenerateOrderConfirmationCode() {
        // get order from cart
        $id_cart = (int)(Tools::getValue('id_cart'));
        $orders = PrestashopUtils::getOrdersByCartId((int)($id_cart));

        // create GCR order vars
        $greviews_order_vars = array();
        $greviews_order_vars['merchant_id'] = self::getConfigValue('GCR_MERCHANT_ID');
        $greviews_order_vars['order_id'] = "";
        $greviews_order_vars['customer_email'] = "";
        $greviews_order_vars['delivery_country'] = "";

        $delivery_days = (int) self::getConfigValue('GCR_DELIVERY_DAYS');
        $greviews_order_vars['estimated_delivery_date'] = date ( 'Y-m-d' , strtotime ( $delivery_days.' weekdays' ) );


        $orders_list = array();
        foreach ($orders as $id_order) {
            $id_order = $id_order['id_order'];
            $order = new Order($id_order);

            if(Validate::isLoadedObject($order)) {
                // test if order is not in error and not already sent ?
                if($order->current_state != Configuration::get('PS_OS_ERROR')) {
                    $orders_list[] = $id_order;
                    // first order, get customer info
                    if(count($orders_list) == 1) {
                        $customer = $order->getCustomer();
                        if(Validate::isLoadedObject($customer)) {
                            $greviews_order_vars['customer_email'] = $customer->email;
                        }

                        $delivery_address = new Address($order->id_address_delivery);
                        if(Validate::isLoadedObject($delivery_address)) {
                            $greviews_order_vars['delivery_country'] = Country::getIsoById($delivery_address->id_country);
                        }
                    }

                    // build id if multi order
                    $order_id_field = 'reference';
                    $current_ref_order = $order->$order_id_field;
                    if(empty($greviews_order_vars['order_id'])) {
                        // reference empty, set reference
                        $greviews_order_vars['order_id'] = $current_ref_order;
                    } else {
                        // reference not empty, add it if not yet added
                        $array_ids = explode(',', $greviews_order_vars['order_id']);
                        if(!in_array($current_ref_order, $array_ids)) {
                            $array_ids[] = $current_ref_order;
                            $greviews_order_vars['order_id'] = implode(',', $array_ids);
                        }
                    }

                }
            }
        }

        // return view
        $this->context->smarty->assign(array('greviews' => $greviews_order_vars));
    }


    /**
     * Create GtmOrderLog if not already created
     */
    protected function createGtmOrderLog($id_order, $id_shop) {
        if(CdcGtmOrderLog::countByOrderId($id_order, $id_shop) == 0) {
            $order_log = new CdcGtmOrderLog();
            $order_log->id_order = $id_order;
            $order_log->id_shop = $id_shop;
            $order_log->sent = 0;
            $order_log->resent = 0;
            $order_log->save();
        }
    }

    /**
     * Save datalayer in log
     */
    protected function saveDataLayerInLog($dataLayerjs) {
        if(is_array($this->orderValidationDetails) && count($this->orderValidationDetails)) {
            foreach ($this->orderValidationDetails as $order) {
               $order_log = CdcGtmOrderLog::getByOrderId($order['id_order'], $this->shop_id);
               if(Validate::isLoadedObject($order_log)) {
                    $order_log->datalayer = $dataLayerjs;
                    $order_log->save();
               }
            }
        }
    }

	/**
	 * Enhanced Ecommerce
	 * Cart
	 * https://developers.google.com/tag-manager/enhanced-ecommerce#checkout
     *
     * @param $opc : True if One Page Checkout is enabled
	 */
	protected function generateEnhancedEcommerceCart() {
        $step = $this->getCheckoutStep();
        $actionField = new Gtm_ActionField();
        $actionField->step = $step;

        $this->dataLayer->event = "checkout";
        $this->dataLayer->ecommerce->checkout = new Gtm_Checkout();
        $this->dataLayer->ecommerce->checkout->actionField = $actionField;

        // products
        if($this->context->cart->id) {
        	$cart = new Cart($this->context->cart->id);
        	if(Validate::isLoadedObject($cart)) {
        		$cartProducts = $cart->getProducts();
        		foreach ($cartProducts as $product) {
        			$dataLayerProduct = $this->getDataLayerProduct($product);
                    if($dataLayerProduct) {
                        $dataLayerProduct->quantity = (int) $product['cart_quantity'];
                        $dataLayerProduct->removeNull();
                        $this->dataLayer->ecommerce->checkout->products[] = $dataLayerProduct;
                    }
                }
        	}
        }
    }

    /**
     * Return checkout step
     *  1 : cart summary
     *  2 : address
     *  3 : shipping
     *  4 : payment
     *  5 : payment options
     */
    protected function getCheckoutStep() {
        $step = 0;

        // Prestashop >= 1.7
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $checkout_process = $this->context->smarty->getTemplateVars('checkout_process');
            if($checkout_process) {
                $checkout_process_reflection = new ReflectionObject($checkout_process);
                $renderable = $checkout_process_reflection->getProperty('renderable');
                $renderable->setAccessible(true);
                $checkout_process_access = $renderable->getValue($checkout_process);
                $reflectionMethod = new ReflectionMethod('CheckoutProcess', 'getSteps');
                $checkout_steps = $reflectionMethod->invoke($checkout_process_access);
                foreach ($checkout_steps as $step_k => $step_val) {
                    if($step_val->isCurrent()) {
                        $step = $step_k;
                        break;
                    }
                }
            }
        }

        // Prestashop <= 1.7
        else {
            if(!$this->opc_enabled) {
                $step = $this->getValue('controller') == 'payment' ? 4 : Tools::getValue('step', 0);
            }
        }

        // transform step [0..n] to [1..n+1]
        $step++;

        // To keep consistancy accross multiple payment methods,
        // regroup payment options in step payment (4)
        if($step > 4) {
          $step = 4;
        }

        return $step;
    }

    /**
     * Enhanced Ecommerce
     * Product Impressions
     * Page Homepage
     * https://developers.google.com/tag-manager/enhanced-ecommerce#product-impressions
     */
    protected function generateEnhancedEcommerceHomepage() {
        $page_name = 'homepage';

        // Home featured products
        if ($this->isModuleEnabled('homefeatured') || $this->isModuleEnabled('homefeaturedslider')) {
            $category = new Category($this->context->shop->getCategory(), $this->context->language->id);
            $home_featured_products = $category->getProducts((int)Context::getContext()->language->id, 1, (Configuration::get('HOME_FEATURED_NBR') ? (int)Configuration::get('HOME_FEATURED_NBR') : 8), 'position');
            $this->addProductListToDatalayer($home_featured_products, $page_name.'-homefeatured');
        }

        // New products
        if ($this->isModuleEnabled('blocknewproducts') && (Configuration::get('PS_NB_DAYS_NEW_PRODUCT')
                || Configuration::get('PS_BLOCK_NEWPRODUCTS_DISPLAY'))) {
            $new_products = Product::getNewProducts((int)$this->context->language->id, 0, (int)Configuration::get('NEW_PRODUCTS_NBR'));
            $this->addProductListToDatalayer($new_products, $page_name.'-blocknewproducts');
        }

        // Best Sellers
        if ($this->isModuleEnabled('blockbestsellers') && (!Configuration::get('PS_CATALOG_MODE')
                || Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY'))) {
            $ga_homebestsell_product_list = ProductSale::getBestSalesLight((int)$this->context->language->id, 0, 8);
            $this->addProductListToDatalayer($ga_homebestsell_product_list, $page_name.'-blockbestsellers');
        }

    }


    /**
     * Enhanced Ecommerce
     * Product Impressions
     * Page Category / Page Search
     * https://developers.google.com/tag-manager/enhanced-ecommerce#product-impressions
     */
    protected function generateEnhancedEcommerceProductList($page) {
        $c_name = null;
        $page_name = '';

        switch ($page) {
            case self::PAGE_CATEGORY:
                $c_name = $this->getCategoryName((int) Tools::getValue('id_category'));
                $page_name = "category";
                break;
            case self::PAGE_SEARCH:
                $page_name = "search";
                break;
            default:
                break;
        }

        // get products list from smarty
        $products = $this->getProductListFromSmarty();
        $this->addProductListToDatalayer($products, $page_name, $c_name);
    }


    /**
     * Enhanced Ecommerce
     * Product View
     * Page Product Detail
     * https://developers.google.com/tag-manager/enhanced-ecommerce#details
     */
    protected function generateEnhancedEcommerceProduct() {
        $product = $this->getDisplayedProduct();
        $dataLayerProduct = $this->getDataLayerProduct($product);
        if($dataLayerProduct) {
            $dataLayerProduct->removeNull();
            $this->dataLayer->ecommerce->detail->products[] = $dataLayerProduct;
        }
    }


    /**
     * Enhanced Ecommerce
     * Refund Order / Products
     * Backoffice refund
     *   - Partial refund
     *   - Update status to "REFUND" OR "CANCELED"
     * https://developers.google.com/tag-manager/enhanced-ecommerce#refunds
     */
    protected function generateEnhancedEcommerceRefund() {
        $refundsQueue = Configuration::get('CDC_GTM_REFUNDS_Q', null, null, $this->shop_id);
        if(!empty($refundsQueue)) {

            $ordersToRefund = Tools::jsonDecode($refundsQueue, true);

            // refund orders 1 at a time
            if(is_array($ordersToRefund) && count($ordersToRefund)) {
                $this->dataLayer->ecommerce = new Gtm_Ecommerce($this->context->currency->iso_code);
                $this->dataLayer->ecommerce->refund = new Gtm_Refund();

                $products = reset($ordersToRefund); // get first elem and reset pointer
                $order_id = key($ordersToRefund);

                // refund full order
                $this->dataLayer->ecommerce->refund->actionField->id = (string) $order_id;

                // add products in case of partial refund
                if($products !== "all" && is_array($products) && count($products)) {
                    foreach ($products as $product_full_identifier => $qtity) {
                        $dataLayerProduct = new Gtm_DataLayerProduct();
                        $dataLayerProduct->id = (string) $product_full_identifier;
                        $dataLayerProduct->quantity = (int) $qtity;
                        $dataLayerProduct->removeNull();
                        $this->dataLayer->ecommerce->refund->products[] = $dataLayerProduct;
                    }
                }

                $this->dataLayer->eventCallback = $this->getCallback("orderrefund", array('order' => $order_id, 'id_shop' => $this->shop_id));
                $this->dataLayer->event = "order_refund";
                return true;
            }
        }

        // no refund to send
        return false;
    }


    /**
     * Generate datalayer to resend order
     */
    protected function generateEnhancedEcommerceOrderResend() {
        $cartsResendQueue = Configuration::get('CDC_GTM_ORDER_RESEND_Q', null, null, $this->shop_id);
        if(!empty($cartsResendQueue)) {

            $cartsToResend = Tools::jsonDecode($cartsResendQueue, true);
            // resend orders 1 at a time
            if(is_array($cartsToResend) && count($cartsToResend)) {
                $this->dataLayer->ecommerce = new Gtm_Ecommerce($this->context->currency->iso_code);

                reset($cartsToResend); // set pointer to first elem of array
                $cart_id = key($cartsToResend);

                $this->generateEnhancedEcommerceOrderConfirmation($cart_id, true);

                $this->dataLayer->event = "order_resend";
                return true;
            }
        }

        // no refund to send
        return false;
    }


    /**
     * Add search informations to datalayer
     * @return [type] [description]
     */
    protected function datalayerPageSearch() {
        $this->dataLayer->search = new Gtm_Search();

        // find the search term
        $term = '';
        if(version_compare(_PS_VERSION_, '1.7', '>=')) {
            $term = $this->context->smarty->getTemplateVars('search_string');
        } else {
            $term = $this->context->smarty->getTemplateVars('search_query');
        }

        // code injection protection
        $term = htmlspecialchars($term);

        $term = $this->cleanString($term);

        $this->dataLayer->search->term = $term;
    }

    /**
     * Create DataLayerProduct from Product
     * Product can come from
     *   - Cart ($cart->getProducts())
     *   - Order ($order->getProducts())
     *   - Category ($category->getProducts())
     *   - Product (new Product())
     */
    protected function getDataLayerProduct($product, $category_name = null) {
    	$dataLayerProduct = new Gtm_DataLayerProduct();
    	$product = $product instanceof Product ? $product : (object)$product;
        $variant = null;

        // set correct ID
        if(empty($product->id)) {
            if(empty($product->id_product)) {
                // error no product id
                return null;
            }
            $product->id = (int) $product->id_product;
        }

        $id_product_attribute = $this->getProductAttributeId($product);

        if($id_product_attribute) {
            $variant = new Combination($id_product_attribute);
            if(!Validate::isLoadedObject($variant)) {
                $variant = null;
                $id_product_attribute = 0;
            }
        }

    	// product ID
        $dataLayerProduct->id = (string) $this->getProductId($product, $variant);


        // reference
        if(!empty($variant->reference)) {
            $dataLayerProduct->reference = (string) $variant->reference;
        } else {
            $dataLayerProduct->reference = (string) $product->reference;
        }

        // product name
        $dataLayerProduct->name = $this->getProductName($product);
        $dataLayerProduct->link = $this->getProductName($product, "link_rewrite");

        // variant name
        if($id_product_attribute) {
            $dataLayerProduct->variant = $this->cleanString(PrestashopUtils::getAttributeSmall($id_product_attribute, $this->getDataLanguage()));
        }

        // product price
        $dataLayerProduct->price = (string) round((float) Product::getPriceStatic($product->id, true, $id_product_attribute), _CDCGTM_PRICE_DECIMAL_);
        $dataLayerProduct->price_tax_exc = (string) round((float) Product::getPriceStatic($product->id, false, $id_product_attribute), _CDCGTM_PRICE_DECIMAL_);

        // wholesale price
        //die(print_r($product));
        $wholesale_price = $product->wholesale_price;
        if($variant && $variant->wholesale_price > 0) {
            $wholesale_price = $variant->wholesale_price;
        }

        $dataLayerProduct->wholesale_price = (string) round((float) $wholesale_price, _CDCGTM_PRICE_DECIMAL_);

        // category
        if(!empty($category_name)) {
            $dataLayerProduct->category = $category_name;
        } else {
            $dataLayerProduct->category = $this->getCategoryName($product->id_category_default);
        }

        // manufacturer
        if($product->id_manufacturer) {
            $manufacturer_name = $this->cleanString(Manufacturer::getNameById((int)$product->id_manufacturer));
            $dataLayerProduct->brand = $manufacturer_name;
        }

        return $dataLayerProduct;
    }


    /**
     * Add a list of products to datalayer
     * @param $products : product list
     * @param $page_name : name of the page
     * @param $category_name : name of category to override product main category
     */
    protected function addProductListToDatalayer($products, $page_name, $category_name = null) {
        $position = 1;
        if(is_array($products) && !empty($products)) {
            foreach ($products as $p) {
                $dataLayerProduct = $this->getDataLayerProduct($p, $category_name);
                if($dataLayerProduct) {
                    $dataLayerProduct->list = $page_name;
                    $dataLayerProduct->position = $position++;

                    $dataLayerProduct->removeNull();
                    $this->dataLayer->ecommerce->impressions[] = $dataLayerProduct;
                }

                // stop loop when max_products_datalayer is reached
                if($this->max_products_datalayer && $position > $this->max_products_datalayer) {
                    break;
                }
            }
        }
    }

    /**
     * Add a refund (full or partial) to be sent as soon as possible
     */
    protected function addRefund($order_id, $shop_id, $product_id_with_attribute = null, $quantity = null) {
        $gtmOrderLog = CdcGtmOrderLog::getByOrderId($order_id, $shop_id);

        // get refunds queue
        $refundsQueue = Configuration::get('CDC_GTM_REFUNDS_Q', null, null, $shop_id);
        if(empty($refundsQueue)) {
            $refundsQueue = array();
        } else {
            $refundsQueue = Tools::jsonDecode($refundsQueue, true);
        }

        // full refund
        if(is_null($product_id_with_attribute)) {
            $gtmOrderLog->refund = "all";
            $refundsQueue[$order_id] = "all";
        }
        // partial refund
        else {
            $productsRefunded = array();
            if($gtmOrderLog->refund && $gtmOrderLog->refund != "all") {
                $productsRefunded = explode(',', $gtmOrderLog->refund);
            }
            $productsRefunded[] = $product_id_with_attribute;
            $gtmOrderLog->refund = implode(',', $productsRefunded);

            if(!isset($refundsQueue[$order_id])) {
                $refundsQueue[$order_id] = array();
            }
            if($refundsQueue[$order_id] != "all") {
                $refundsQueue[$order_id][$product_id_with_attribute] = $quantity;
            }
        }

        // save
        Configuration::updateValue('CDC_GTM_REFUNDS_Q', Tools::jsonEncode($refundsQueue), false, null, $shop_id);
        $gtmOrderLog->save();
    }


    /**
     * Add a order to the resend queue, to be sent as soon as possible
     * ORDER_RESEND_Q format :
     * [
     *   CART_ID1:[ORDER_ID1,ORDER_ID2],
     *   CART_ID2:[ORDER_ID3],
     * ]
     */
    public static function addOrderResend($order_id, $shop_id) {
        $order = new Order($order_id);
        if(Validate::isLoadedObject($order)) {
            $id_cart = $order->id_cart;
            if(!$id_cart) {
                return false;
            }

            // get refunds queue
            $cartResendQueue = Configuration::get('CDC_GTM_ORDER_RESEND_Q', null, null, $shop_id);
            if(empty($cartResendQueue)) {
                $cartResendQueue = array();
            } else {
                $cartResendQueue = Tools::jsonDecode($cartResendQueue, true);
            }

            // create array of orders
            if(!isset($cartResendQueue[$id_cart]) || !is_array($cartResendQueue[$id_cart])) {
                $cartResendQueue[$id_cart] = array();
            }

            // add order in the queue
            if(!in_array($order_id, $cartResendQueue[$id_cart])) {
                $cartResendQueue[$id_cart][] = $order_id;
            }

            // save
            Configuration::updateValue('CDC_GTM_ORDER_RESEND_Q', Tools::jsonEncode($cartResendQueue), false, null, $shop_id);
        }
    }

    /**
     * Check if some orders has to be resent
     * and add them to the resend queue
     */
    protected function resendNotSentOrders() {
        $numberOfDaysMaxToResendOrders = (int)Configuration::get($this->getConfigName('RESEND_DAYS'));
        if($numberOfDaysMaxToResendOrders < 1) {
            $numberOfDaysMaxToResendOrders = 1;
        }
        $orders = CdcGtmOrderLog::getNotSent(null, $numberOfDaysMaxToResendOrders, 10);
        foreach($orders as $order) {
            cdc_googletagmanager::addOrderResend($order['id_order'], $order['id_shop']);
        }
    }
            


    /**
     * Test if the page is the order validation page
     */
    protected function isOrderValidationPage() {
        $ordervalidationpage = false;
        $controller = Tools::getValue("controller");
        $module = Tools::getValue('module');
        if($this->debug_enabled) {
            $this->addDebug("[isOrderValidationPage] controller = $controller / module = $module");
        }

        // hook displayOrderConfirmation called
        // every new order should call this
        if($this->displayOrderConfirmation) {
            $ordervalidationpage = true;
        }
        // main order validation page
        // every payment module should redirect to it
        elseif($controller == self::PAGE_ORDERCONFIRMATION) {
            $ordervalidationpage = true;
        }

        // paypal module
        elseif ($module == 'paypal' && $controller == 'submit') {
            $ordervalidationpage = true;
        }

        // paypalplus module
        elseif ($module == 'paypalplus' && $controller == 'confirmation') {
            $ordervalidationpage = true;
        }

        // stripejs module
        elseif ($module == 'stripejs' && $controller == 'confirmation') {
            $ordervalidationpage = true;
        }

        // firstdatapayment module
        elseif ($module == 'firstdatapayment' && $controller == 'success') {
            $ordervalidationpage = true;
        }

        // przelewy24 module
        elseif ($module == 'przelewy24' && $controller == 'paymentSuccessful') {
            $ordervalidationpage = true;
            // get cart id from url, and put it in displayOrderConfirmationOrderObj
            $this->displayOrderConfirmationOrderObj['id_cart'] = (int) Tools::getValue('ga_cart_id');
        }

        // mollie module
        elseif ($module == 'mollie' && $controller == 'return') {
            $ordervalidationpage = true;
            // get cart id from url, and put it in displayOrderConfirmationOrderObj
            $this->displayOrderConfirmationOrderObj['id_cart'] = (int) Tools::getValue('cart_id');
        }

        // klarna checkout
        elseif ($module == 'klarnaofficial' && $controller == 'thankyou') {
            $ordervalidationpage = true;
        }

        // sveacheckout
        elseif ($module == 'sveacheckout' && $controller == 'confirmation') {
            $ordervalidationpage = true;
        }

        // paysoncheckout2
        elseif ($module == 'paysoncheckout2' && $controller == 'confirmation') {
            $ordervalidationpage = true;
        }

        // g2apay
        elseif ($module == 'g2apay' && $controller == 'success') {
            $ordervalidationpage = true;
        }

        // iyzico module
        elseif ($module == 'iyzicocheckoutform' && $controller == 'result') {
            $ordervalidationpage = true;
            $this->displayOrderConfirmationOrderObj['id_cart'] = (int) $this->context->cart->id;
        }

        // payu module (waiting validation)
        elseif ($module == 'payu' && $controller == 'success') {
            $ordervalidationpage = true;
        }

        // ecm_checkout
        elseif ($module == 'ecm_checkout' && $controller == 'checkout_end') {
            $ordervalidationpage = true;
        }

        if($ordervalidationpage && !is_array($this->orderValidationDetails)) {
            $this->orderValidationDetails = array();
        }
        return $ordervalidationpage;
    }

    /**
     * test if the checkout page is one page checkout
     */
    protected function isOnePageCheckout() {
        $opc = false;

        $controller = $this->getValue('controller');
        $module = Tools::getValue('module');

        // test on controller name
        if($controller == self::PAGE_OPC
            || $controller == self::PAGE_OPC_SUPERCHECKOUT) {
            $opc = true;
        }

        // test on module name
        elseif($module == "onepagecheckoutps") {
            $opc = true;
        }

        // klarna checkout
        elseif($module == "klarnaofficial" && $controller == "checkoutklarna") {
            $opc = true;
        }
        elseif($module == "klarnaofficial" && $controller == "checkoutklarnakco") {
            $opc = true;
        }

        // st easy checkout
        elseif($module == "steasycheckout" && $controller == "default") {
            $opc = true;
        }
        

        return $opc;
    }


    /**
     * Return the current displayed product
     * @return Product
     */
    protected function getDisplayedProduct() {
        $product_id = (int) $this->getValue('id_product');
        if(!$product_id) {
            $smarty_product = $this->context->smarty->getTemplateVars('product');
            if(Validate::isLoadedObject($smarty_product)) {
                $product_id = (int) $smarty_product->id;
            }
        }
        $product = new Product($product_id);

        // add attribute
        if(Tools::getValue('id_product_attribute')) {
            $product->id_product_attribute = (int) Tools::getValue('id_product_attribute');
        } elseif(!isset($product->id_product_attribute)) {
            $product->id_product_attribute = 0;
        }

        return $product;
    }


    /**
     * Return product list displayed on page
     * Get smarty variable
     */
    protected function getProductListFromSmarty() {
        // get products list from smarty
        $products = $this->context->smarty->getTemplateVars('products');

        // prestashop >= 1.7
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            // get in listing
            if(!$products || !is_array($products)) {
                $listing = $this->context->smarty->getTemplateVars('listing');
                if(isset($listing['products'])) {
                    $products = $listing['products'];
                }
            }

            // get in cart
            if(!$products || !is_array($products)) {
                $cart = $this->context->smarty->getTemplateVars('cart');
                if(isset($cart['products'])) {
                    $products = $cart['products'];
                }
            }
        }

        return $products;
    }

    /**
     * Return product list on cart
     */
    protected function getProductListFromCart() {
        $products = array();

        // search in context
        $cart = $this->context->cart;

        // search in parameter
        if(!Validate::isLoadedObject($cart) && Tools::getIsset('id_cart')) {
            $cart = new Cart((int) Tools::getValue('id_cart'));
        }

        // get products from cart
        if(Validate::isLoadedObject($cart)) {
            $products = $cart->getProducts();
        }
        return $products;
    }


    /**
     * Return id product attribute
     * 
     * @param   $product
     * @return int          id_product_attribute
     */
    protected function getProductAttributeId($product) {
        $id_product_attribute = null;

        $product = (array) $product;
        if(empty($product['id']) && !empty($product['id_product'])) {
            $product['id'] = $product['id_product'];
        }

        if(!empty($product['id'])) {
            // get product variant
            if (!empty($product['product_attribute_id'])) {
                $id_product_attribute = (int)$product['product_attribute_id'];
            } elseif (!empty($product['id_product_attribute'])) {
                $id_product_attribute = (int)$product['id_product_attribute'];
            }

            // force default variant whenever possible
            if ($this->display_variant_id && !$id_product_attribute) {
                $id_product_attribute = Product::getDefaultAttribute($product['id']);
            }
        }

        return $id_product_attribute;
    }

    /**
     * Generate datalayer for cart action (add or remove)
     *
     * @param $id_product
     * @param $id_product_attribute
     * @param $action
     * @param $qtity
     */
    public function getDataLayerCartAction($id_product, $id_product_attribute, $action, $qtity) {
        if($action != 'add' && $action != 'remove') {
            return null;
        }

        // get product
        $product = new Product($id_product);
        $product->id_product_attribute = $id_product_attribute;
        $dlProduct = $this->getDataLayerProduct($product);

        // add quantity
        $dlProduct->quantity = (int) $qtity;

        // generate datalayer
        $dataLayer = new Gtm_DataLayer();
        $dataLayer->event = $action == 'add' ? 'addToCart' : 'removeFromCart';
        $dataLayer->ecommerce = new Gtm_Ecommerce($this->context->currency->iso_code);
        $dataLayer->ecommerce->$action = (object) ['products' => [$dlProduct]];

        return $dataLayer;
    }


    /**
     * Return list of languages
     */
    private function getLanguages()
    {
        $languages = array();
        $languages[0] = $this->l('Default - User language');
        $psLangs = Language::getLanguages();
        foreach ($psLangs as $psLang) {
            $languages[$psLang['id_lang']] = $psLang['name'];
        }
        return $languages;
    }

    /**
     * Datalayer language id
     * @return int
     */
    private function getDataLanguage() {
        return $this->data_language ? $this->data_language : (int) $this->context->language->id;
    }

    /**
     * Remove accents from string
     * @see https://stackoverflow.com/a/10790734
     * 
     * @param  string $string
     * @return string
     */
    public function remove_accents($string) {
        if ( !preg_match('/[\x80-\xff]/', $string) )
            return $string;

        $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
            chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
            chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
            chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
            chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
            chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
            chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
            chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
            chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
            chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
            chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
            chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
            chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
            chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
            chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
            chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
            chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
            chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
            chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
            chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
            chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
            chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
            chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
            chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
            chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
            chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
            chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
            chr(195).chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
            chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
            chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
            chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
            chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
            chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
            chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
            chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
            chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
            chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
            chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
            chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
            chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
            chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
            chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
            chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
            chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
            chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
            chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
            chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
            chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
            chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
            chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
            chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
            chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
            chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
            chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
            chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
            chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
            chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
            chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
            chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
            chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
            chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
            chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
            chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
            chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
            chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
            chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
            chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
            chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
            chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
            chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
            chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
            chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
            chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
            chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
            chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
            chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
            chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
            chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
            chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
            chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
            chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
            chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
            chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
            chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
            chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
            chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
            chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
            chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
            chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
            chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
            chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
        );

        $string = strtr($string, $chars);

        return $string;
    }

    /**
     * Replace accented characters
     * 
     * @param  string $string
     * @return string
     */
    public function cleanString($string) {
        $string = $this->remove_accents($string);

        // keep only 7 bit printable ASCII chars; remove others
        // not used anymore since we can use unicode with json_encode
        //$string = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $string);

        return $string;
    }

    /**
     * Generate a callback function
     * depending on the type and the params
     */
    protected function getCallback($type, $params = array()) {
        $callbackFn = "";
        //$callbackUrl = $this->context->link->getModuleLink($this->name, 'callback', array(), null, null, null, true);
        // the old url style works in any cases
        $baseurl = $this->context->shop->getBaseURI();
        if(empty($baseurl) || substr($baseurl, -1) != '/') {
            $baseurl = $baseurl.'/';
        }
        $callbackUrl = $baseurl.'index.php?fc=module&module=cdc_googletagmanager&controller=callback';
        $callbackParams = array();

        // create callback params
        $callbackParams['type'] = $type;
        if(!is_null($params) && is_array($params) && count($params)) {
            $callbackParams['params'] = $params;
        }

        // if params are not empty, create ajax callback function
        if(count($callbackParams)) {
            $callbackParams = urlencode(base64_encode(Tools::jsonEncode($callbackParams)));
            $callbackUrl .= '&p='.$callbackParams;
            $callbackFn = 'function() {
                console.log("cdcgtm callback: '.$type.'");
                var x = new XMLHttpRequest();
                x.open("GET", "'.$callbackUrl.'", true);
                x.send();
            }';

            // remove new lines
            $callbackFn = preg_replace( "/\r|\n/", "", $callbackFn);
            // remove white spaces
            $callbackFn = preg_replace('/\s+/', ' ', $callbackFn);
        }

        return $callbackFn;
    }

    /**
     * hook home to display generate the product list associated to home featured, news products and best sellers Modules
     */
    public function isModuleEnabled($name)
    {
        if (version_compare(_PS_VERSION_, '1.5', '>='))
            if(Module::isEnabled($name))
            {
                $module = Module::getInstanceByName($name);
                if(!$module) {
                    return false;
                }
                return $module->isRegisteredInHook('home');
            }
            else
                return false;
        else
        {
            $module = Module::getInstanceByName($name);
            return ($module && $module->active === true);
        }
    }


    /**
     * Return value associated to the key
     * search in :
     *   - override values
     *   - POST values
     *   - GET values
     */
    public function getValue($key) {
        if(isset($this->override_page_values[$key])) {
            return $this->override_page_values[$key];
        }
        return Tools::getValue($key);
    }

    /**
     * Override page values
     * index / id_category / id_product ...
     */
    public function setValues($values) {
        foreach ($values as $key => $value) {
            $this->override_page_values[$key] = $value;
        }
    }



    /**
     * Install custom hooks in template files
     */
    public function installCustomHooks() {
        $success = true;

        // Prestashop 1.7
        if(version_compare(_PS_VERSION_, '1.7', '>=')) {
            // displayAfterTitle
            $filename = _PS_THEME_DIR_.'templates/_partials/head.tpl';
            if(!CdcTools::stringInFile('{hook h="displayAfterTitle"}', $filename)) {
                $file_content = Tools::file_get_contents($filename);
                if(!empty($file_content)) {
                    $matches = preg_split('/(<\/title>)/is', $file_content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                    if(count($matches) == 3) {
                        $new_content = $matches[0] . $matches[1] . "\n{hook h=\"displayAfterTitle\"}" . $matches[2];
                        if(!file_put_contents($filename, $new_content)) {
                            $success = false;
                        }
                    } else {
                        $success = false;
                    }
                } else {
                    $success = false;
                }
            }

        }

        // Prestashop 1.6 / 1.5
        else {
            // displayAfterTitle
            $filename = _PS_THEME_DIR_.'header.tpl';
            if(!CdcTools::stringInFile('{hook h="displayAfterTitle"}', $filename)) {
                $file_content = Tools::file_get_contents($filename);
                $matches = preg_split('/(<\/title>)/is', $file_content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                if(count($matches) == 3) {
                    $new_content = $matches[0] . $matches[1] . "\n{hook h=\"displayAfterTitle\"}" . $matches[2];
                    if(!file_put_contents($filename, $new_content)) {
                        $success = false;
                    }
                } else {
                    $success = false;
                }
            }

            // displayAfterBodyOpeningTag
            if(!CdcTools::stringInFile('{hook h="displayAfterBodyOpeningTag"}', _PS_THEME_DIR_.'header.tpl')) {
                $file_content = Tools::file_get_contents(_PS_THEME_DIR_.'header.tpl');
                $matches = preg_split('/(<body.*?>)/is', $file_content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                /* ' Fix bug syntax coloration */
                if(count($matches) == 3) {
                    $new_content = $matches[0] . $matches[1] . "\n{hook h=\"displayAfterBodyOpeningTag\"}" . $matches[2];
                    if(!file_put_contents(_PS_THEME_DIR_.'header.tpl', $new_content)) {
                        $success = false;
                    }
                } else {
                    $success = false;
                }
            }

            // displayBeforeBodyClosingTag
            if(!CdcTools::stringInFile('{hook h="displayBeforeBodyClosingTag"}', _PS_THEME_DIR_.'footer.tpl')) {
                $file_content = Tools::file_get_contents(_PS_THEME_DIR_.'footer.tpl');
                $matches = preg_split('/(<\/body>)/is', $file_content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                if(count($matches) == 3) {
                    $new_content = $matches[0] . "{hook h=\"displayBeforeBodyClosingTag\"}\n" . $matches[1] . $matches[2];
                    if(!file_put_contents(_PS_THEME_DIR_.'footer.tpl', $new_content)) {
                        $success = false;
                    }
                } else {
                    $success = false;
                }
            }

        }

        return $success;
    }

    /**
     * Enable or disable debug
     * Return true if debug is enabled
     */
    protected function debugManager() {
        // set debug by param
        if(Tools::getIsset('cdcgtm_debug')) {
            $param_debug = (int) Tools::getValue('cdcgtm_debug');
            unset($_GET['cdcgtm_debug']);

            if($param_debug == 1) {
                // enable debug
                $this->debug_enabled = true;
                Context::getContext()->cookie->cdcgtmd = 1;
                $this->addDebug("[debugManager] Set debug ON from param");
            } else {
                // disable debug
                $this->debug_enabled = false;
                Context::getContext()->cookie->cdcgtmd = 0;
                $this->addDebug("[debugManager] Set debug OFF from param");
            }
        }

        // read cookie
        if(is_null($this->debug_enabled)) {
            if(Context::getContext()->cookie->cdcgtmd == 1) {
                $this->debug_enabled = true;
                $this->addDebug("[debugManager] Set debug ON from cookie");
            } else {
                $this->debug_enabled = false;
            }
        }

        return $this->debug_enabled;
    }



    /**
     * Add debug message
     */
    public function addDebug($msg) {
        $this->debug_stack[] = $msg;
    }

    /**
     * Display all debug messages
     */
    public function displayDebug() {
        $html = '<style>';
        $html .= '#cdcgtm_debug {position: fixed; top: 0; right: 0; width: 500px; max-height: 420px; overflow-y: scroll; z-index: 9999; opacity: 0.7; margin-right: -470px; transition: margin 700ms;}';
        $html .= '#cdcgtm_debug:hover {opacity: 1; margin-right: 0;}';
        $html .= '</style>';
        $html .= '<div id="cdcgtm_debug">';
        $html .= '<div style="position: absolute; top: 0; bottom: 0; left: 0; width: 30px; padding: 5px; font-weight: bold; font-size: 2em; line-height: 100%; background: #000000; color: #00ff00; text-align: center; border-right: 1px solid #00ff00;">&laquo; <span style="font-size: 70%;">D E B U G</span><div style="position: absolute; bottom: 0; text-align: center; font-size: 80%;"><a href="?cdcgtm_debug=0">x</a></div></div>';
        $html .= '<div id="cdcgtm_debug_content" style="margin-left: 30px;">';
        if(!CdcTools::isXmlHttpRequest()) {
            foreach ($this->debug_stack as $text) {
                $html .= '<pre style="background: #000; color: #0f0; font-size: 12px; font-family: monospace; margin: 0px; border: 0; border-bottom: 1px dashed #0f0;">';
                $html .= "$text".PHP_EOL;
                $html .= '</pre>';
            }
        }
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }


    /**
     * Display custom html tag in the page to mention that the hook 
     * is present
     * 
     * @return [type] [description]
     */
    private function displayHookPresent($hookName) {
        $hookKey = 'cdcgtm_'.$hookName;
        $html = '<div data-hookkey="'.$hookKey.'">'.$hookName.': OK</div>';

        echo $html;
    }


    /**
     * set debug hooks mod on
     */
    private function debugHooksMod() {
        $this->mod_test_hooks_enabled = true;
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }


}
?>
