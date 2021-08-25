<?php
/**
* 2010-2021 Webkul
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
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/../marketplace/marketplace.php';
require_once dirname(__FILE__).'/../marketplace/classes/WkMpRequiredClasses.php';
require_once dirname(__FILE__).'/../mpbooking/classes/WkMpBookingRequiredClasses.php';
require_once dirname(__FILE__).'/classes/MpWsRequiredClass.php';

class MpWebservice extends Module
{
    public function __construct()
    {
        $this->name = 'mpwebservice';
        $this->tab = 'market_place';
        $this->version = '6.1.0';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => '1.7');
        $this->bootstrap = true;
        $this->dependencies = array('marketplace');
        parent::__construct();
        $this->displayName = $this->l('Marketplace Webservice');
        $this->description = $this->l('Marketplace Seller, Product and Order Webservice API');
        $this->confirmUninstall = $this->l('Are you sure?');
    }

    public function callInstallTab()
    {
        $this->installTab('AdminMpWebservice', 'Webservice', 'AdminMarketplaceManagement');
        return true;
    }

    public function installTab($className, $tabName, $tabParentName = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();
        foreach (Language::getLanguages(false) as $lang) {
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
        $mpWsDb = new WkMpWebserviceDb();
        if (!parent::install()
            || !$mpWsDb->createTable()
            || !$this->callInstallTab()
            || !$this->registerHook('displayMPMenuBottom')
            || !$this->registerHook('displayMpMyAccountMenuActiveSeller')
            || !$this->registerHook('addWebserviceResources')) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        $mpWsDb = new WkMpWebserviceDb();
        if (!parent::uninstall()
            || !$mpWsDb->deleteTable()) {
            return false;
        }
        return true;
    }

    public function hookAddWebserviceResources()
    {
        $mpResources = array(
            'mpimages' => array('description' => 'Marketplace Images', 'specific_management' => true),
            'seller' => array('description' => 'Marketplace Sellers', 'specific_management' => true),
        );
        ksort($mpResources);

        return $mpResources;
    }

    /**
     * Display menu link in Marketplace Menu bottom
     * @return html
     */
    public function hookDisplayMPMenuBottom()
    {
        if (Configuration::get('WK_WS_SELLER_WEBSERVICE')) {
            $idCustomer = $this->context->cookie->id_customer;
            if ($idCustomer) {
                $mpSeller = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
                if ($mpSeller) {
                    $this->context->smarty->assign(
                        'webservice',
                        $this->context->link->getModuleLink('mpwebservice', 'webservice')
                    );
                    return $this->display(__FILE__, 'mpwebservice_link.tpl');
                }
            }
        }
    }

    /**
     * Display menu link in My Account Mp Menu bottom
     * @return html
     */
    public function hookdisplayMpMyAccountMenuActiveSeller()
    {
        $this->context->smarty->assign('mpmyaccountmenu', 1);
        return $this->hookDisplayMPMenuBottom();
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitMpWsConfig')) {
            Configuration::updateValue('WK_WS_SELLER_WEBSERVICE', Tools::getValue('WK_WS_SELLER_WEBSERVICE'));
            Configuration::updateValue('WK_WS_KEY_ADMIN_APPROVE', Tools::getValue('WK_WS_KEY_ADMIN_APPROVE'));
            Configuration::updateValue('WK_WS_KEY_SELLER_STATUS', Tools::getValue('WK_WS_KEY_SELLER_STATUS'));
            Configuration::updateValue('MP_ADMIN_WS_KEY', Tools::getValue('MP_ADMIN_WS_KEY'));
        }
        $wkShopURL = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'api/';
        $mpResources = WebserviceSpecificManagementSeller::$allowedMethods;
        $this->context->smarty->assign(
            array(
                'mp_resources' => $mpResources,
                'wk_shop_url' => $wkShopURL,
                'admin_webservice_link' => $this->context->link->getAdminLink('AdminWebservice'),
                'WK_WS_SELLER_WEBSERVICE' => Configuration::get('WK_WS_SELLER_WEBSERVICE'),
                'WK_WS_KEY_SELLER_STATUS' => Configuration::get('WK_WS_KEY_SELLER_STATUS'),
                'WK_WS_KEY_ADMIN_APPROVE' => Configuration::get('WK_WS_KEY_ADMIN_APPROVE'),
                'MP_ADMIN_WS_KEY' => Configuration::get('MP_ADMIN_WS_KEY'),
            )
        );

        return $this->display(__FILE__, 'views/templates/admin-config.tpl');
    }
}
