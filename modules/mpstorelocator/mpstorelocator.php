<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once 'classes/MpStoreLocatorClassIncluded.php';
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class MpStoreLocator extends CarrierModule
{
    const INSTALL_SQL_FILE = 'install.sql';
    private $_html = '';
    private $_postErrors = array();
    public function __construct()
    {
        $this->name = 'mpstorelocator';
        $this->tab = 'front_office_features';
        $this->version = '5.1.0';
        $this->author = 'Webkul';
        $this->dependencies = array('marketplace');
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Marketplace Store Locator');
        $this->description = $this->l('Marketplace Store Location Detector');
        $this->dayOfWeek = array(
            $this->l('Sunday'),
            $this->l('Monday'),
            $this->l('Tuesday'),
            $this->l('Wednesday'),
            $this->l('Thursday'),
            $this->l('Friday'),
            $this->l('Saturday'),
        );
    }

    //for configuration page
    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminMpStoreConfiguration'));
    }

    public function hookDisplayProductButtons()
    {
        $idProduct = Tools::getValue('id_product');
        $objMpProduct = new WkMpSellerProduct();
        $mpProduct = $objMpProduct->getSellerProductByPsIdProduct($idProduct);

        // Visible only if marketplace product
        if ($mpProduct) {
            // button will display if any store exist
            $stores = MarketplaceStoreProduct::getProductStore($idProduct, true);
            if ($stores) {
                $this->context->smarty->assign(
                    'storeLink',
                    $this->context->link->getModuleLink(
                        'mpstorelocator',
                        'storedetails',
                        array('id_product' => $idProduct)
                    )
                );

                return $this->fetch('module:'.$this->name.'/views/templates/hook/link.tpl');
            }
        }
    }

    public function hookDisplayProductAdditionalInfo()
    {
        return $this->hookDisplayProductButtons();
    }

    public function hookActionToogleSellerStatus($params)
    {
        if ($params['is_seller']) {
            $objStore = new MarketplaceStoreLocator();
            $objStore->deactivateAllSellerStores($params['id_seller']);
        }
    }

    public function hookDisplayMPMyAccountMenu()
    {
        $idCustomer = $this->context->customer->id;
        $objMpSeller = new WkMpSeller();
        $mpSeller = $objMpSeller->getSellerDetailByCustomerId($idCustomer);
        if ($mpSeller && $mpSeller['active']) {
            $this->context->smarty->assign('mpmenu', 0);
            return $this->fetch('module:'.$this->name.'/views/templates/hook/add_store.tpl');
        }
    }

    public function hookDisplayMPMenuBottom()
    {
        $idCustomer = $this->context->customer->id;
        $objMpSeller = new WkMpSeller();
        $mpSeller = $objMpSeller->getSellerDetailByCustomerId($idCustomer);
        if ($mpSeller && $mpSeller['active']) {
            $this->context->smarty->assign('mpmenu', 1);
            return $this->fetch('module:'.$this->name.'/views/templates/hook/add_store.tpl');
        }
    }

    public function hookDisplayFooterBefore($params)
    {
        $stores = MarketplaceStoreLocator::getAllStore(true);
        if (Configuration::get('MP_STORE_ALL_SELLER') && $stores) {
            return $this->fetch('module:'.$this->name.'/views/templates/hook/viewsellerstores.tpl');
        }
    }

    public function hookModuleRoutes()
    {
        // $storeDetails = 'storedetails/';
        // $allSellerStores = 'allsellerstores/';
        $allproducts = 'allproducts/';

        // return array(
        //     'module-mpstorelocator-storedetails' => array(
        //         'controller' => 'storedetails',
        //         'rule' => array(
        //             "$storeDetails{:id_product}",
        //             "$storeDetails{:id_store}",
        //             "$storeDetails{:stores}"
        //         ),
        //         'keywords' => array(
        //             'id_product' => array(
        //             'regexp' => '[_a-zA-Z0-9_-]+',
        //             'param' => 'id_product',
        //             ),
        //         ),
        //         'params' => array(
        //             'fc' => 'module',
        //             'module' => 'mpstorelocator',
        //             'controller' => 'storedetails',
        //         ),
        //     ),
        //     'module-mpstorelocator-allsellerstores' => array(
        //         'controller' => 'allsellerstores',
        //         'rule' => "$allSellerStores",
        //         'keywords' => array(),
        //         'params' => array(
        //             'fc' => 'module',
        //             'module' => 'mpstorelocator',
        //             'controller' => 'allsellerstores',
        //         ),
        //     ),
        // );
		
		return array(
            'module-mpstorelocator-allproducts' => array(
                'controller' => 'allproducts',
                'rule' => "$allproducts",
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'mpstorelocator',
                    'controller' => 'allproducts',
                ),
            ),
        );
    }

    public function callInstallTab()
    {
        $this->installTab('AdminMpStoreLocatorConfiguration', 'Manage Store Configuration', 'AdminMarketplaceManagement');
        $this->installTab('AdminMarketplaceStoreLocator', 'Manage Store Locations', 'AdminMarketplaceManagement');

        $this->installTab('AdminMpStoreConfiguration', 'Store', 'AdminMpStoreLocatorConfiguration');
        $this->installTab('AdminMpStorePickUpConfiguration', 'Store Pickup', 'AdminMpStoreLocatorConfiguration');
        $this->installTab(
            'AdminMpStorePickUpPaymentConfiguration',
            'Store Pickup Payment',
            'AdminMpStoreLocatorConfiguration'
        );
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

    /**
     * Module Installation Process.
     */
    public function createTable()
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

        return true;
    }

    public function registerMpHook()
    {
        return $this->registerHook(
            array(
                'displayProductAdditionalInfo',
                'actionFrontControllerSetMedia',
                'displayBackOfficeHeader',
                'displayMpProductNavTab',
                'displayMpProductTabContent',
                'actionAfterAddMPProduct',
                'actionAfterUpdateMPProduct',
                'displayProductButtons',
                'displayMPMyAccountMenu',
                'displayMPMenuBottom',
                'displayFooterBefore',
                'moduleRoutes',
                'actionToogleSellerStatus',
                'displayFooterProduct',
                'displayHome',
                'displayNav',
                'displayBeforeCarrier',
                'advancedPaymentOptions',
                'displayPaymentTop',
                'actionOrderStatusPostUpdate',
                'displayAdminOrder',
                'displayOrderDetail',
                'displayOrderConfirmation2',
                'actionCartSave',
                'actionCarrierUpdate',
                'actionObjectWkMpSellerProductDeleteAfter'
            )
        );
    }

    public function hookActionObjectWkMpSellerProductDeleteAfter($params)
    {
        if ($params && $params['id_ps_product']) {
            MarketplaceStoreProduct::deleteStoreProduct($params['id_ps_product']);
            MpStorePickUpProduct::deletePickUpProduct($params['id_ps_product']);
            MpStoreProductAvaialable::deletePickUpProductAvailable($params['id_ps_product']);
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        if ($this->context->controller instanceof AdminStoreConfigurationController) {
            $this->context->controller->addJS(_MODULE_DIR_.'mpstorelocator/views/js/admin/mpstoreconfig.js');
        }
        if ($this->context->controller instanceof AdminOrdersController
            || $this->context->controller instanceof AdminStorePickUpPaymentConfigurationController
        ) {
            $this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/views/css/front/orderdetail.css');
        }
    }

    public function hookActionFrontControllerSetMedia()
    {
        if ($this->context->controller->php_self == "order") {
            if (Tools::getValue('mpApplyStorePickUp') == 1) {
                $this->context->cookie->mpForcePickUpCarrier = 1;
            } elseif (Tools::getValue('mpApplyStorePickUp') == 2) {
                $this->context->cookie->mpForcePickUpCarrier = 0;
            }
        }

        if ($this->context->controller->php_self == "order") {
            if (Tools::getValue('mpRemoveProductFrom') == 1) {
                $this->removeProductFromCart();
                $this->context->cookie->mpForcePickUpCarrier = 0;
            }
        }
        if ($this->context->controller->php_self == 'order-detail'
            || $this->context->controller->php_self == 'order-confirmation'
        ) {
            $this->context->controller->registerStylesheet(
                'order-detail-css',
                'modules/'.$this->name.'/views/css/front/orderdetail.css'
            );
        }

        $stores = array();
        if ($this->context->controller->php_self == "product") {
            $idProduct = Tools::getValue('id_product');
            $product_stores = MarketplaceStoreProduct::getProductStore($idProduct, true);
            if ($product_stores) {
                //store list
                foreach ($product_stores as $value) {
                    $stores[] = MarketplaceStoreLocator::getStoreById($value['id_store'], true);
                }
                $stores = MarketplaceStoreLocator::getMoreStoreDetails($stores);
            }
        } elseif ($this->context->controller->php_self == "index") {
            $stores = MarketplaceStoreLocator::getAllStore(true);
            if ($stores) {
                $stores = MarketplaceStoreLocator::getMoreStoreDetails($stores);
            }
        }
        if ($this->context->controller->php_self == "product" || $this->context->controller->php_self == "index") {
            Media::addJsDef(
                array(
                    'storeLocationsJson' => json_encode($stores),
                    'idProductLoad' => Tools::getValue('id_product'),
                    'idStoreLoad' => Tools::getValue('id_store')
                )
            );
        }
        if ($this->context->controller->php_self == "order"
            || ($this->context->controller->php_self == "index"
            && Configuration::get('MP_STORE_HOME_PAGE'))
            || ($this->context->controller->php_self == "product"
            && (Configuration::get('MP_STORE_DISPLAY_PRODUCT_MAP')
            || Configuration::get('MP_STORE_PRODUCT_TAB')))
        ) {
            $googleMapKey = Configuration::get('MP_GEOLOCATION_API_KEY');
            $this->context->controller->registerJavascript(
                'cluster-js',
                'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js',
                array(
                    'priority' => 100,
                    'server' => 'remote'
                )
            );
            $this->context->controller->registerJavascript(
                'wk-google-map-js',
                "https://maps.googleapis.com/maps/api/js?key=$googleMapKey&libraries=places",
                array(
                    'priority' => 101,
                    'server' => 'remote'
                )
            );
            $this->context->controller->registerJavascript(
                'storedetails',
                'modules/'.$this->name.'/views/js/front/storedetails.js',
                array('priority' => 200)
            );
            $this->context->controller->registerStylesheet(
                'store_details',
                'modules/'.$this->name.'/views/css/front/store_details.css'
            );
            $storeConfiguration = MpStoreConfiguration::getStoreConfiguration();
            Media::addJsDef(
                array(
                    // 'allStore' => $allStore,
                    'no_store_found' => $this->l('No Store Found'),
                    'storeLink' => $this->context->link->getModuleLink(
                        'mpstorelocator',
                        'storedetails'
                    ),
                    'storeTiming' => $this->l('Store Timing'),
                    'contactDetails' => $this->l('Contact'),
                    'getDirections' => $this->l('Get directions'),
                    'closedMsg' => $this->l('Closed'),
                    'storeConfiguration' => $storeConfiguration,
                    'emailMsg' => $this->l('Email'),
                    'storeLocate' => 1,
                    'controller' => $this->context->controller->php_self,
                    'storeLogoImgPath' => _MODULE_DIR_.'mpstorelocator/views/img/store_logo/',
                    'autoLocate' => Configuration::get('MP_AUTO_LOCATE'),
                    'displayCluster' => Configuration::get('MP_STORE_CLUSTER'),
                    'distanceType' => Configuration::get('MP_STORE_DISTANCE_UNIT'),
                    'markerIcon' => _MODULE_DIR_.'mpstorelocator/views/img/'.Configuration::get('MP_STORE_MARKER_NAME'),
                    'displayCustomMarker' => Configuration::get('MP_STORE_MARKER_ICON_ENABLE'),
                    'displayContactDetails' => Configuration::get('MP_STORE_CONTACT_DETAILS'),
                    'displayFax' => Configuration::get('MP_STORE_DISPLAY_FAX'),
                    'displayEmail' => Configuration::get('MP_STORE_DISPLAY_EMAIL'),
                    'displayStoreTiming' => Configuration::get('MP_DISPLAY_STORE_TIMING'),
                    'displayStorePage' => Configuration::get('MP_STORE_STORE_PAGE'),
                    'maxZoomLevel' => Configuration::get('MP_STORE_MAP_ZOOM'),
                    'maxZoomLevelEnable' => Configuration::get('MP_STORE_MAP_ZOOM_ENABLE'),
                    'ajaxurlStoreByKey' => $this->context->link->getModuleLink(
                        'mpstorelocator',
                        'storedetails'
                    ),
                )
            );
        }

        if ($this->context->controller->php_self == "order") {
            $this->context->controller->addJqueryPlugin('growl', null, false);
            $this->context->controller->registerStylesheet('growl-css', 'js/jquery/plugins/growl/jquery.growl.css');

            $this->context->controller->registerJavascript(
                'mpstore-pick-up',
                'modules/'.$this->name.'/views/js/front/mpstore_pickup.js',
                array('priority' => 250)
            );
            $this->context->controller->registerJavascript(
                'mpstore-payment-restrict',
                'modules/'.$this->name.'/views/js/front/mpstore_payment_restrict.js'
            );
            $this->context->controller->registerJavascript(
                'mpstore-picker-js',
                'modules/'.$this->name.'/views/js/front/bootstrap-datetimepicker.min.js'
            );
            $this->context->controller->registerStylesheet(
                'store-font',
                'https://fonts.googleapis.com/css?family=Open+Sans:400,600,700'
            );
            $this->context->controller->registerStylesheet(
                'mpstore-picker-css',
                'modules/'.$this->name.'/views/css/front/bootstrap-datetimepicker.min.css'
            );
            $this->context->controller->registerStylesheet(
                'mpstore-pickup-css',
                'modules/'.$this->name.'/views/css/front/store_pickup.css'
            );

            $stores = MarketplaceStoreLocator::getAllAvailableStore(true);
            if ($stores) {
                $stores = MarketplaceStoreLocator::getMoreStoreDetails($stores);
            }

            Media::addJsDef(
                array(
                    'mpMinimumHours' => Configuration::get('MP_STORE_MINIMUM_HOURS'),
                    'mpMaximumDays' => Configuration::get('MP_STORE_MAXIMUM_DAYS'),
                    'mpMinimumDays' => Configuration::get('MP_STORE_MINIMUM_DAYS'),
                    'dayOfWeek' => $this->dayOfWeek,
                    'idSelectError' => $this->l('Please select the store'),
                    'timeError' => $this->l('Please select the time'),
                    'dateError' => $this->l('Please select the date'),
                    'distanceMsg' => $this->l('Distance'),
                    'storeLocationsJson' => json_encode($stores),
                    'MP_STORE_PICKUP_DATE' => Configuration::get('MP_STORE_PICKUP_DATE'),
                    'MP_STORE_TIME' => Configuration::get('MP_STORE_TIME'),
                    'storeCarrierId' => Configuration::get('MP_STORE_ID_CARRIER'),
                )
            );
        }
    }

    public function hookDisplayHome()
    {
        $stores = MarketplaceStoreLocator::getAllStore(true);
        if ($stores) {
            if (Configuration::get('MP_STORE_HOME_PAGE')) {
                return $this->fetch('module:mpstorelocator/views/templates/hook/store_map.tpl');
            }
        }
    }

    public function hookDisplayFooterProduct()
    {
        $idProduct = Tools::getValue('id_product');
        $stores = MarketplaceStoreProduct::getProductStore($idProduct, true);
        if ($stores) {
            $html = '';
            if (Configuration::get('MP_STORE_DISPLAY_PRODUCT_MAP')
                || Configuration::get('MP_STORE_PRODUCT_TAB')
            ) {
                $html .= '<h3 class="page-product-heading">'.$this->l('Store Info').'</h3>';
            }
            if (Configuration::get('MP_STORE_DISPLAY_PRODUCT_MAP')) {
                $html .= $this->fetch('module:mpstorelocator/views/templates/hook/store_map.tpl');
            }
            if (Configuration::get('MP_STORE_PRODUCT_TAB')) {
                $html .= $this->getStoreProductWs($stores);
            }
            return $html;
        }
    }

    public function getStoreProductWs($productStores)
    {
        $this->context->smarty->assign('modules_dir', _MODULE_DIR_);

        if ($productStores) {
            $allproductStore = array();
            if ($productStores) {
                foreach ($productStores as $pStore) {
                    $allproductStore[] = MarketplaceStoreLocator::getStoreById($pStore['id_store'], true);
                }
                $pStores = MarketplaceStoreLocator::getMoreStoreDetails(
                    $allproductStore
                );
                $this->context->smarty->assign(
                    array(
                        'filtered_stores' => $pStores,
                        'displayContactDetails' => Configuration::get('MP_STORE_CONTACT_DETAILS'),
                        'displayFax' => Configuration::get('MP_STORE_DISPLAY_FAX'),
                        'displayEmail' => Configuration::get('MP_STORE_DISPLAY_EMAIL'),
                        'displayStoreTiming' => Configuration::get('MP_DISPLAY_STORE_TIMING'),
                        'displayStorePage' => Configuration::get('MP_STORE_STORE_PAGE'),
                    )
                );
                return $this->fetch('module:mpstorelocator/views/templates/front/filtered_store.tpl');
            }
        }
    }

    public function setModuleDefaultConfiguration()
    {
        Configuration::updateValue('MP_STORE_STORE_PAGE', 1);
        Configuration::updateValue('MP_STORE_CONTACT_DETAILS', 1);
        Configuration::updateValue('MP_STORE_DISPLAY_FAX', 1);
        Configuration::updateValue('MP_STORE_DISPLAY_EMAIL', 1);
        Configuration::updateValue('MP_DISPLAY_STORE_TIMING', 1);
        Configuration::updateValue('MP_STORE_HOME_PAGE', 1);
        Configuration::updateValue('MP_STORE_DISPLAY_PRODUCT_MAP', 1);

        Configuration::updateValue('MP_AUTO_LOCATE', 1);
        Configuration::updateValue('MP_STORE_CLUSTER', 1);
        Configuration::updateValue('MP_STORE_PRODUCT_TAB', 1);
        Configuration::updateValue('MP_STORE_SEARCH_BY_PRODUCT', 1);
        Configuration::updateValue('MP_STORE_DISTANCE_UNIT', 'METRIC');
        Configuration::updateValue('MP_STORE_MAP_ZOOM', 17);

        //Store pick up configuration
        Configuration::updateValue('MP_STORE_PICKUP_DATE', 1);
        Configuration::updateValue('MP_STORE_PICK_UP', 1);
        Configuration::updateValue('MP_STORE_COUNTRIES', json_encode(array()));
        Configuration::updateValue('MP_STORE_COUNTRY_ENABLE', 0);

        //Store pick up configuration
        Configuration::updateValue('MP_STORE_PICK_UP_PAYMENT', 1);
        Configuration::updateValue('MP_PICK_UP_PAYMENT_RESTRICT', 1);

        if (!Configuration::updateValue('MP_STORE_LOCATION_ACTIVATION', 0)
            || !Configuration::updateValue('MP_STORE_ALL_SELLER', 1)
        ) {
            return false;
        }

        return true;
    }

    public function install()
    {
        if (!parent::install()
            || !$this->createTable()
            || !$this->registerMpHook()
            || !$this->callInstallTab()
            || !$this->setModuleDefaultConfiguration()
            || !$this->installCarrier()
            || !$this->installOrderState()
        ) {
            return false;
        }
        return true;
    }

    // public function disable($force_all = false)
    // {
    //     if ($force_all) {
    //     }
    //     if (parent::disable() && $this->enableCarrier(false)) {
    //         return true;
    //     }
    //     return false;
    // }

    // public function enable($force_all = false)
    // {
    //     if (parent::enable() && $this->enableCarrier(true)) {
    //         return true;
    //     }
    //     return false;
    // }

    // public function enableCarrier($enable = false)
    // {
    //     $idCarrier = (int)Configuration::get('MP_STORE_ID_CARRIER');
    //     $carrier = new Carrier($idCarrier);
    //     if ($carrier) {
    //         if ($enable) {
    //             $carrier->active = (int)1;
    //         } else {
    //             $carrier->active = (int)0;
    //         }
    //         $carrier->save();
    //         Configuration::updateValue('MP_STORE_ID_CARRIER', $carrier->id);
    //     }
    //     return true;
    // }

    public function installOrderState()
    {
        if (!Configuration::get('MP_STORE_OS_WAITING')
            || !Validate::isLoadedObject(new OrderState(Configuration::get('MP_STORE_OS_WAITING')))
        ) {
            $order_state = new OrderState();
            $order_state->name = array();
            foreach (Language::getLanguages() as $language) {
                $order_state->name[$language['id_lang']] = $this->l('Awaiting payment in store');
            }
            $order_state->send_email = false;
            $order_state->color = '#4169E1';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            if ($order_state->add()) {
                Configuration::updateValue('MP_STORE_OS_WAITING', (int) $order_state->id);
            }
        }
        return true;
    }

    public function deleteConfigKeys()
    {
        $var = array(
            'MP_GEOLOCATION_API_KEY',
            'MP_STORE_LOCATION_ACTIVATION',
            'MP_STORE_ALL_SELLER',
            'MP_STORE_STORE_PAGE',
            'MP_STORE_CONTACT_DETAILS',
            'MP_STORE_DISPLAY_FAX',
            'MP_STORE_DISPLAY_EMAIL',
            'MP_DISPLAY_STORE_TIMING',
            'MP_STORE_HOME_PAGE',
            'MP_STORE_DISPLAY_PRODUCT_MAP',
            'MP_AUTO_LOCATE',
            'MP_STORE_CLUSTER',
            'MP_STORE_PRODUCT_TAB',
            'MP_STORE_SEARCH_BY_PRODUCT',
            'MP_STORE_DISTANCE_UNIT',
            'MP_STORE_MAP_ZOOM',
            'MP_STORE_PICKUP_DATE',
            'MP_STORE_PICK_UP',
            'MP_STORE_PICK_UP_PAYMENT',
            'MP_STORE_COUNTRIES',
            'MP_STORE_COUNTRY_ENABLE'

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
        $tables = array(
            'marketplace_store_locator',
            'marketplace_store_products',
            'mp_store_pickup_products',
            'mp_store_pickup',
            'mpstore_pay',
            'mpstore_pay_lang',
            'mp_store_pickup_available',
            'mp_store_configuration'
        );
        foreach ($tables as $table) {
            $drop = Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$table);
            if (!$drop) {
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

    public function uninstall($keep = true)
    {
        if (!parent::uninstall()
            || ($keep && !$this->deleteTables())
            || !$this->uninstallTab()
            || !$this->deleteConfigKeys()
            || !$this->uninstallCarrier()
        ) {
            return false;
        }
        return true;
    }


    public function uninstallOrderState()
    {
        $orderState = new OrderState((int)Configuration::get('MP_STORE_OS_WAITING'));
        $orderState->delete();
        return true;
    }

    /* Store pick up */

    public function getOrderShippingCost($cart, $shippingCost)
    {
        return $shippingCost;
    }

    public function getOrderShippingCostExternal($params)
    {
        $this->getOrderShippingCost($params, 0);
    }

    public function removeProductFromCart()
    {
        $products = MpStorePickUpProduct::getProductByCartId($this->context->cart->id);
        $idProducts = array_column($products, 'id_product');
        $idProductAttributes = array_column($products, 'id_product_attribute');
        $cartProducts = $this->context->cart->getProducts();
        if ($cartProducts) {
            foreach ($cartProducts as $product) {
                $key = array_search($product['id_product'], $idProducts);
                if (is_bool($key) || $product['id_product_attribute'] != $idProductAttributes[$key]
                ) {
                    $this->context->cart->deleteProduct($product['id_product'], $product['id_product_attribute']);
                }
            }
        }
    }

    public function hookDisplayMpProductNavTab()
    {
        $idMpProduct = Tools::getvalue('id_mp_product');
        $idProduct = (MarketplaceStoreProduct::getIdProductByMpIdProducts((int)$idMpProduct));
        if ($idProduct) {
            return $this->display(__FILE__, 'mp_product_tab.tpl');
        }
    }

    public function hookDisplayMpProductTabContent()
    {
        $idMpProduct = Tools::getvalue('id_mp_product');
        $enablePickUp = 0;
        $idProduct = (MarketplaceStoreProduct::getIdProductByMpIdProducts((int)$idMpProduct));
        if ($idProduct) {
            if (MpStoreProductAvailable::availableForStorePickup($idProduct)) {
                $enablePickUp = 1;
            }
            $this->context->smarty->assign(
                array(
                    'enable_pickup' => $enablePickUp,
                )
            );
            return $this->display(__FILE__, 'enable_product_pickup.tpl');
        }
    }

    /**
     * [hookActionProductSave - save the auction product in admin product controller]
     *
     */
    public function hookActionAfterUpdateMPProduct($params)
    {
        $this->updateAvailableProductStore($params);
    }

    public function updateAvailableProductStore($params)
    {
        if ($params['id_mp_product']) {
            $idProduct = (MarketplaceStoreProduct::getIdProductByMpIdProducts((int)$params['id_mp_product']));
            if ($idProduct) {
                $idAvailableStore = MpStoreProductAvailable::getAvailablePickupId($idProduct);
                if (empty($idAvailableStore)) {
                    $objStoreProductAvailable = new MpStoreProductAvailable();
                } else {
                    $objStoreProductAvailable = new MpStoreProductAvailable($idAvailableStore);
                }
                $objStoreProductAvailable->availabe_store_pickup = (int)Tools::getValue('enableStorePickUp');
                $objStoreProductAvailable->id_product = (int)$idProduct;
                $objStoreProductAvailable->save();
            }
        }
    }

    public function hookActionAfterAddMPProduct($params)
    {
        $this->updateAvailableProductStore($params);
    }

    public function hookActionCartSave($params)
    {
        if (Tools::getIsset('delete') && Tools::getValue('delete')) {
            $idProduct = Tools::getValue('id_product');
            $idProductAttribute = Tools::getValue('id_product_attribute');
            MpStorePickUpProduct::deleteProductsFromCart(
                $this->context->cart->id,
                $idProduct,
                $idProductAttribute
            );
        }
    }


    public function hookDisplayPaymentTop($params)
    {
        $deliveryOptions = $this->context->cart->getDeliveryOption();
        $deliveryOptions = $deliveryOptions[$this->context->cart->id_address_delivery];
        $deliveryOptions = rtrim($deliveryOptions, ',');
        $deliveryOptions = explode(',', $deliveryOptions);
        $pickUpAvailable = $this->checkAvailableForStorePayment();
        if (Configuration::get('MP_STORE_PICK_UP_PAYMENT')
            && $pickUpAvailable[0]
            && (!$pickUpAvailable[1] ? $pickUpAvailable[2] : $pickUpAvailable[1])
            && in_array(Configuration::get('MP_STORE_ID_CARRIER'), $deliveryOptions)
            && (Configuration::get('MP_PICK_UP_PAYMENT_RESTRICT') && count($deliveryOptions) >= 1)
        ) {
            $removeProduct = 0;
            $someProduct = 0;
            foreach ($this->context->cart->getProducts() as $product) {
                $storeConfig = MpStoreConfiguration::getSelectedStoreConfiguration(
                    $this->context->cart->id,
                    $product['id_product']
                );
                if (!MpStoreProductAvailable::availableForStorePickup($product['id_product'])
                    || ($storeConfig && !$storeConfig['store_payment']
                    || !(MarketplaceStoreProduct::getProductStore($product['id_product'], true)))
                ) {
                    $removeProduct = 1;
                } else {
                    $someProduct = 1;
                }
            }
            if ($removeProduct && $someProduct) {
                $this->context->smarty->assign(
                    array(
                        'deliveryOptionCount' => count($deliveryOptions),
                        'formSubmit' => $this->context->link->getPageLink(
                            $this->context->controller->php_self,
                            null,
                            null,
                            array(
                                'mpRemoveProductFrom' => 1
                            )
                        )
                    )
                );
                return $this->fetch(
                    'module:mpstorelocator/views/templates/hook/wk_storepay_before.tpl'
                );
            }
        }
    }

    /**
     * display hook for including payment option during checkout.
     *
     * @return html
     */
    public function hookAdvancedPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }
        if ($params['cart']->id_carrier == 0) {
            $deliveryOptions = $params['cart']->getDeliveryOption();
            $deliveryOptions = $deliveryOptions[$params['cart']->id_address_delivery];
            $deliveryOptions = rtrim($deliveryOptions, ',');
            $deliveryOptions = explode(',', $deliveryOptions);
        } else {
            $deliveryOptions = array($params['cart']->id_carrier);
        }
        $pickUpAvailable = $this->checkAvailableForStorePayment();
        if (Configuration::get('MP_STORE_PICK_UP_PAYMENT')
            && $pickUpAvailable[0] && !$pickUpAvailable[1]
            && in_array(Configuration::get('MP_STORE_ID_CARRIER'), $deliveryOptions)
            && count($deliveryOptions) == 1
        ) {
            $payment_options = array($this->getOfflinePaymentOption());
            return $payment_options;
        }
        return null;
    }

    public function checkAvailableForStorePayment()
    {
        $availableForPickUp = array();
        $availableForPickUp[0] = 0;
        $availableForPickUp[1] = 0;
        $availableForPickUp[2] = 0;
        foreach ($this->context->cart->getProducts() as $product) {
            $storeConfig = MpStoreConfiguration::getSelectedStoreConfiguration(
                $this->context->cart->id,
                $product['id_product']
            );
            if ($storeConfig) {
                if ($storeConfig['store_payment']) {
                    $availableForPickUp[0] = 1;
                } else {
                    $availableForPickUp[1] = 1;
                }
            } else {
                $availableForPickUp[2] = 1;
            }
        }
        return $availableForPickUp;
    }

    /**
     * Payment return hook for showing payment detail after order generation.
     *
     * @param  Array $params - for Order Details
     *
     * @return html
     */
    public function hookDisplayOrderConfirmation2($params)
    {
        $idOrder = Tools::getValue('id_order');
        if ($idOrder) {
            $order = new Order($idOrder);
            $displayOrderConfirmation = $this->displayOrderStoreDetails($order);
            if ($displayOrderConfirmation) {
                return $this->fetch('module:mpstorelocator/views/templates/hook/mpstorelocator_order_detail.tpl');
            }
        }
    }

    public function getOfflinePaymentOption()
    {
        $offlineOption = new PaymentOption();
        $stores = MpStorePickUpProduct::getIdStoreByIdCart($this->context->cart->id);
        $offlineOption->setCallToActionText($this->l('Marketplace Pay in Store'))
                        ->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true));

        if (count($stores) == '1') {
            $paymentOptions =  MarketplaceStoreLocator::getStoreById($stores[0]['id_store'], true);
            $paymentOptions = MpStorePay::getPaymentOptionDetails(
                json_decode($paymentOptions['payment_option']),
                $this->context->language->id
            );
            $this->context->smarty->assign(
                array(
                    'paymentOptions' => $paymentOptions,
                    'imagePath' => _MODULE_DIR_.'mpstorelocator/views/img/payment_logo/'
                )
            );
            $offlineOption->setAdditionalInformation(
                $this->fetch('module:mpstorelocator/views/templates/front/payment_infos.tpl')
            );
        }
        return $offlineOption;
    }


    /**
     * Display hook for store details related to order
     *
     * @param  Array $params - for Order details
     *
     * @return html
     */
    public function hookDisplayOrderDetail($params)
    {
        if ($params['order']) {
            $displayOrderConfirmation = $this->displayOrderStoreDetails($params['order']);
            if ($displayOrderConfirmation) {
                return $this->fetch('module:mpstorelocator/views/templates/hook/mpstorelocator_order_detail.tpl');
            }
        }
    }

    public function hookDisplayAdminOrder()
    {
        $idOrder = Tools::getValue('id_order');
        if ($idOrder) {
            $displayOrderConfirmation = $this->displayOrderStoreDetails(new Order($idOrder));
            if ($displayOrderConfirmation) {
                return $this->display(__FILE__, 'admin-order-view-store-order.tpl');
            }
        }
    }

    public function displayOrderStoreDetails($order)
    {
        $stores = MpStorePickUpProduct::getDistinctStoresByIdOrder($order->id);
        if (empty($stores)) {
            return 0;
        } else {
            $orderedProducts = $order->getProducts();
            $key = 0;
            $orderDetail = array();
            $storePickUp = array();
            foreach ($stores as $store) {
                $storeDetails = MpStorePickUpProduct::getDistinctStoresByOrder(
                    $order->id,
                    $store['pickup_date'],
                    $store['id_store']
                );
                $i = 0;
                $detail = array();
                foreach ($storeDetails as $storeDetail) {
                    $detail[$i] = $storeDetail['id_product'].'_'.$storeDetail['id_product_attribute'];
                    $storePickUp[$detail[$i]] = $storeDetail['id_store_pickup'];
                    $i++;
                }
                $orderDetail[$key]['store_pickup'] = $storePickUp;
                $orderDetail[$key]['products'] = $detail;
                $orderDetail[$key]['count'] = count($detail);
                $orderDetail[$key]['id_store'] = $store['id_store'];
                $dateTime = explode(' ', $store['pickup_date']);
                $orderDetail[$key]['pickup_date'] = $dateTime[0];
                $orderDetail[$key]['pickup_time'] = $dateTime[1];

                $storeConfig = MpStoreConfiguration::getStoreConfigurationByIdStore($store['id_store']);
                $orderDetail[$key]['enablePickUpTime'] = $storeConfig['enable_time'];
                $orderDetail[$key]['enablePickUpDate'] = $storeConfig['enable_date'];
                $orderDetail[$key]['enablePaymentOptions'] = $storeConfig['store_payment'];
                $key++;
            }

            $products = array();
            foreach ($orderedProducts as $product) {
                $productName = explode(' - ', $product['product_name']);
                $index = $product['id_product'].'_'.$product['product_attribute_id'];
                if ($productName[0]) {
                    $products[$index]['product_name'] = $productName[0];
                }
                if (isset($productName[1])) {
                    $products[$index]['product_attr_name'] = $productName[1];
                }
                $newProducts = new Product((int)$product['product_id'], false, (int)$order->id_lang);
                $products[$index]['imageLink'] = $this->context->link->getImageLink(
                    $newProducts->link_rewrite,
                    $product['image']->id,
                    ImageType::getFormattedName('small')
                );
            }
            $storesDetail = array();
            foreach ($stores as $store) {
                $idStore = $store['id_store'];
                $storesData = MarketplaceStoreLocator::getStoreById($idStore, true);
                $storesDetail[$idStore] = (MarketplaceStoreLocator::getMoreStoreDetails(
                    array($storesData)
                ))[0];
                $paymentOptions =  json_decode((MarketplaceStoreLocator::getStoreById($idStore, true))['payment_option']);
                if (empty($paymentOptions)) {
                    $storesDetail[$idStore]['payment_options'] = array();
                } else {
                    $storesDetail[$idStore]['payment_options'] = MpStorePay::getPaymentOptionDetails(
                        $paymentOptions,
                        $this->context->language->id
                    );
                }
            }

            $this->context->smarty->assign(
                array(
                    'products' => $products,
                    'stores' => $storesDetail,
                    'orderedProducts' => $orderDetail,
                    'imagePath' => _MODULE_DIR_.'mpstorelocator/views/img/payment_logo/',
                )
            );

            if (count($storesDetail) == 1) {
                $this->context->smarty->assign('numOfStores', 1);
            }
            return 1;
        }
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        $order = new Order($params['id_order']);
        $orderCarrier = array_column(MpStoreProductAvailable::getIdCarrierByIdCart($order->id_cart), 'id_carrier');
        if (in_array(Configuration::get('MP_STORE_ID_CARRIER'), $orderCarrier)) {
            if (Configuration::get('MP_STORE_ID_CARRIER') == $order->id_carrier) {
                MpStorePickUpProduct::updateIdOrderByIdCart($order->id_cart, $params['id_order']);

                $this->sendMailNotificationToStore($order);
                $this->sendMailNotificationToSeller($order);
                $data = $this->getMailOrderDetails($order);
                if ($data) {
                    $this->sendMailNotificationToCustomer($order, $data);
                    $this->sendMailNotificationToAdmin($order, $data);
                }
            }
            $this->context->cookie->mpForcePickUpCarrier = 0;
        } else {
            MpStorePickUpProduct::deleteStoreProductByCartId($order->id_cart);
        }
    }

    public function getMailOrderDetails($order, $idSeller = false)
    {
        $cartRule = $order->getCartRules();
        if ($idSeller) {
            $stores = MpStorePickUpProduct::getStoresByIdOrder($order->id, $idSeller);
        } else {
            $stores = MpStorePickUpProduct::getStoresByIdOrder($order->id);
        }
        $orderedProducts = $order->getProducts();
        $productsDetail = array();
        $objCurrency = new Currency($order->id_currency);
        foreach ($orderedProducts as $product) {
            $index = $product['id_product'].'_'.$product['product_attribute_id'];
            $productsDetail[$index]['product_name'] = $product['product_name'];
            $productsDetail[$index]['product_quantity'] = $product['product_quantity'];
            $productsDetail[$index]['product_reference'] = $product['product_reference'];
            $productsDetail[$index]['total_price_tax_incl'] = $product['total_price_tax_incl'];
        }
        $this->context->smarty->assign(
            array(
                'orderedProducts' => $productsDetail,
            )
        );
        $key = 0;
        $orderDetail = array();
        foreach ($stores as $store) {
            $storesData = MpStorePickUpProduct::getDistinctStoresByIdOrder($order->id, $store['id_store']);
            $totalProductPrice = 0;
            $storeInfo = MarketplaceStoreLocator::getStoreById($store['id_store'], true);
            $storeDetailsInfo = MarketplaceStoreLocator::getMoreStoreDetails(array($storeInfo));
            $storeInfo = $storeDetailsInfo[0];
            $products = array();
            $count = array();
            $pickUpDate = array();
            $pickUpTime = array();
            foreach ($storesData as $storeData) {
                $storeDetails = MpStorePickUpProduct::getDistinctStoresByOrder(
                    $order->id,
                    $storeData['pickup_date'],
                    $storeData['id_store']
                );
                $i = 0;
                $detail = array();
                foreach ($storeDetails as $storeDetail) {
                    $detail[$i] = $storeDetail['id_product'].'_'.$storeDetail['id_product_attribute'];
                    $i++;
                }
                foreach ($detail as $product) {
                    $totalProductPrice += $productsDetail[$product]['total_price_tax_incl'];
                }
                $products[] = $detail;
                $count[] = count($detail);
                $dateTime = explode(' ', $storeData['pickup_date']);
                $pickUpDate[] = $dateTime[0];
                $pickUpTime[] = $dateTime[1];
            }
            $orderDetail[$key]['products'] = $products;
            $orderDetail[$key]['count'] = $count;
            $orderDetail[$key]['pickup_date'] = $pickUpDate;
            $orderDetail[$key]['pickup_time'] = $pickUpTime;
            $orderDetail[$key]['id_store'] = $store['id_store'];
            $paymentOptions = json_decode($storeInfo['payment_option']);

            // $paymentOptions =  json_decode((MarketplaceStoreLocator::getStoreById($store['id_store'], true))['payment_option']);
            if (empty($paymentOptions)) {
                $storeInfo['payment_options'] = array();
            } else {
                $storeInfo['payment_options'] = MpStorePay::getPaymentOptionDetails(
                    $paymentOptions,
                    $this->context->language->id
                );
            }
            $orderDetail[$key]['storeDetails'] = $storeInfo;
            $orderDetail[$key]['store_total'] = Tools::displayPrice($totalProductPrice, $objCurrency, false);
            $discount = ($totalProductPrice / (float)$order->total_products_wt) * $order->total_discounts_tax_incl;
            $orderDetail[$key]['store_discount'] = Tools::displayPrice($discount, $objCurrency, false);
            $orderDetail[$key]['totalPaid'] = Tools::displayPrice($totalProductPrice - $discount, $objCurrency, false);
            $key++;
        }

        $product_list_html = '';
        if (count($orderDetail) > 0) {
            // $product_list_txt = $this->getModuleEmailTemplateContent('store_order_product_list', (int) $order->id_lang, $this->name, 'txt', $orderDetail[0]);
            $product_list_html = $this->getModuleEmailTemplateContent(
                'store_order_conf_product',
                (int) $order->id_lang,
                $this->name,
                'tpl',
                $orderDetail
            );
        }
        $voucherDetail = array();
        $voucherDetail = $this->getModuleEmailTemplateContent('order_voucher', (int) $order->id_lang, $this->name, 'tpl', $cartRule);

        $customer = new Customer($order->id_customer);
        $orderStatus = (new OrderState($order->current_state, (int)$order->id_lang))->name;
        $data = array(
            '{order_name}' => $order->reference,
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{email}' => $customer->email,
            '{order_name}' => $order->reference,
            '{date}' => Tools::displayDate(date('Y-m-d H:i:s'), null, 1),
            '{payment}' => Tools::substr($order->payment, 0, 255),
            '{payment_status}' => Tools::substr($orderStatus, 0, 255),
            '{store_details}' => $product_list_html,
            // '{products_txt}' => $product_list_txt,
            '{voucher_html}' => $voucherDetail,
            // '{discounts_txt}' => $product_list_txt,
            // '{total_paid}' => Tools::displayPrice($totalProductPrice - $discount, $objCurrency, false),
            // '{total_products}' => Tools::displayPrice($totalProductPrice, $objCurrency, false),
            // '{total_discounts}' => Tools::displayPrice($discount, $objCurrency, false),
        );
        return $data;
    }

    public static function getModuleEmailTemplateContent(
        $emailFileName,
        $idLang = 0,
        $moduleName = 0,
        $fileExt = 'html',
        $tplVars = array()
    ) {
        $context = Context::getContext();
        if (!$idLang) {
            $idLang = $context->language->id;
        }
        $langIso = Language::getIsoById($idLang);
        if ($moduleName) {
            $emailTemplatePaths = array(
                _PS_MODULE_DIR_.'/'.$moduleName.'/mails/'.$langIso.'/'.$emailFileName.'.'.$fileExt,
                _PS_MODULE_DIR_.'/'.$moduleName.'/mails/en/'.$emailFileName.'.'.$fileExt
            );
        } else {
            $emailTemplatePaths = array(
                _PS_MAIL_DIR_.'/'.$langIso.'/'.$emailFileName.'.'.$fileExt,
                _PS_MAIL_DIR_.'/en/'.$emailFileName.'.'.$fileExt
            );
        }
        foreach ($emailTemplatePaths as $path) {
            if (Tools::file_exists_cache($path)) {
                if ($fileExt == 'tpl') {
                    $context->smarty->assign('list', $tplVars);
                    return $context->smarty->fetch($path);
                } else {
                    return Tools::file_get_contents($path);
                }
            }
        }
        return '';
    }

    public function sendMailNotificationToStore($order)
    {
        $stores = MpStorePickUpProduct::getStoresByIdOrder($order->id);
        $orderedProducts = $order->getProducts();
        $productsDetail = array();
        $objCurrency = new Currency($order->id_currency);
        foreach ($orderedProducts as $product) {
            $index = $product['id_product'].'_'.$product['product_attribute_id'];
            $productsDetail[$index]['product_name'] = $product['product_name'];
            $productsDetail[$index]['product_quantity'] = $product['product_quantity'];
            $productsDetail[$index]['unit_price_tax_incl'] = Tools::displayPrice(
                $product['unit_price_tax_incl'],
                $objCurrency
            );
            $productsDetail[$index]['unit_price_tax_excl'] = Tools::displayPrice(
                $product['unit_price_tax_excl'],
                $objCurrency
            );
            $productsDetail[$index]['totalPrice'] = Tools::displayPrice(
                $product['total_price_tax_incl'],
                $objCurrency
            );
            $productsDetail[$index]['total_price_tax_incl'] = $product['total_price_tax_incl'];
        }
        $this->context->smarty->assign(
            array(
                'orderedProducts' => $productsDetail,
            )
        );

        foreach ($stores as $store) {
            $storesData = MpStorePickUpProduct::getDistinctStoresByIdOrder($order->id, $store['id_store']);
            $totalProductPrice = 0;
            $orderDetail = array();
            $key = 0;
            foreach ($storesData as $storeData) {
                $storeDetails = MpStorePickUpProduct::getDistinctStoresByOrder(
                    $order->id,
                    $storeData['pickup_date'],
                    $storeData['id_store']
                );
                $i = 0;
                $detail = array();
                foreach ($storeDetails as $storeDetail) {
                    $detail[$i] = $storeDetail['id_product'].'_'.$storeDetail['id_product_attribute'];
                    $i++;
                }
                foreach ($detail as $product) {
                    $totalProductPrice += $productsDetail[$product]['total_price_tax_incl'];
                }
                $orderDetail[$key]['products'] = $detail;
                $orderDetail[$key]['count'] = count($detail);
                $dateTime = explode(' ', $storeData['pickup_date']);
                $orderDetail[$key]['pickup_date'] = $dateTime[0];
                $orderDetail[$key]['pickup_time'] = $dateTime[1];
                $key++;
            }

            $storeConfiguration = MpStoreConfiguration::getStoreConfigurationByIdStore($store['id_store']);
            if ($storeConfiguration) {
                $this->context->smarty->assign(
                    array(
                        'enableDatePicker' => $storeConfiguration['enable_date'],
                        'enableTimePicker' => $storeConfiguration['enable_time']
                    )
                );
            }

            $discount = ($totalProductPrice / $order->total_products_wt)  * $order->total_discounts_tax_incl;
            $product_list_html = '';
            if (count($orderDetail) > 0) {
                // $product_list_txt = $this->getModuleEmailTemplateContent('store_order_product_list', (int) $order->id_lang, $this->name, 'txt', $orderDetail[0]);
                $product_list_html = $this->getModuleEmailTemplateContent('store_order_product_list', (int) $order->id_lang, $this->name, 'tpl', $orderDetail);
            }

            $customer = new Customer($order->id_customer);
            $orderStatus = (new OrderState($order->current_state, (int)$order->id_lang))->name;
            $data = array(
                '{order_name}' => $order->reference,
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
                '{order_name}' => $order->reference,
                '{date}' => Tools::displayDate(date('Y-m-d H:i:s'), null, 1),
                '{payment}' => Tools::substr($order->payment, 0, 255),
                '{payment_status}' => Tools::substr($orderStatus, 0, 255),
                '{products}' => $product_list_html,
                // '{products_txt}' => $product_list_txt,
                // '{discounts}' => $product_list_html,
                // '{discounts_txt}' => $product_list_txt,
                '{total_paid}' => Tools::displayPrice($totalProductPrice - $discount, $objCurrency, false),
                '{total_products}' => Tools::displayPrice($totalProductPrice, $objCurrency, false),
                '{total_discounts}' => Tools::displayPrice($discount, $objCurrency, false),
            );
            $storesData = MarketplaceStoreLocator::getStoreById($store['id_store'], true);
            $storeConfiguration = MpStoreConfiguration::getStoreConfiguration($storesData['id_seller']);
            if ($storeConfiguration['enable_store_notification'] && Validate::isEmail($storesData['email'])) {
                Mail::Send(
                    (int)$order->id_lang,
                    'store_notify',
                    Mail::l('Store Order Notification ', (int)$order->id_lang),
                    $data,
                    $storesData['email'],
                    null,
                    null,
                    null,
                    null,
                    null,
                    _PS_MODULE_DIR_.'mpstorelocator/mails/'
                );
            }
        }
    }

    public function sendMailNotificationToSeller($order)
    {
        $sellers = MpStorePickUpProduct::getSellersByIdOrder($order->id);
        foreach ($sellers as $seller) {
            $data = $this->getMailOrderDetails($order, $seller['id_seller']);
            $sellerDetail = new WkMpSeller($seller['id_seller']);
            if (Validate::isEmail($sellerDetail->business_email)) {
                Mail::Send(
                    (int)$order->id_lang,
                    'admin_notify',
                    Mail::l('Order Created On Store', (int)$order->id_lang),
                    $data,
                    $sellerDetail->business_email,
                    null,
                    null,
                    null,
                    null,
                    null,
                    _PS_MODULE_DIR_.'mpstorelocator/mails/'
                );
            }
        }
    }

    public function sendMailNotificationToCustomer($order, $data)
    {
        $customer = new Customer($order->id_customer);

        if (Validate::isEmail($customer->email)) {
            Mail::Send(
                (int)$order->id_lang,
                'customer_notify',
                Mail::l('Store Confirmation ', (int)$order->id_lang),
                $data,
                $customer->email,
                null,
                null,
                null,
                null,
                null,
                _PS_MODULE_DIR_.'mpstorelocator/mails/'
            );
        }
    }

    public function sendMailNotificationToAdmin($order, $data)
    {
        $employeesList = Employee::getEmployees();
        foreach ($employeesList as $value) {
            $employee = new Employee($value['id_employee']);
            if ($employee) {
                if ($employee->isSuperAdmin()) {
                    $adminEmail =  $employee->email;
                    break;
                }
            }
        }
        if (Validate::isEmail($adminEmail)) {
            Mail::Send(
                (int)$order->id_lang,
                'admin_notify',
                Mail::l('Order Created On Store', (int)$order->id_lang),
                $data,
                $adminEmail,
                null,
                null,
                null,
                null,
                null,
                _PS_MODULE_DIR_.'mpstorelocator/mails/'
            );
        }
    }

    public function hookDisplayBeforeCarrier()
    {
        if (Configuration::get('MP_STORE_PICK_UP')) {
            $carriersList = ($this->context->cart->getDeliveryOptionList());
            if (isset($carriersList[$this->context->cart->id_address_delivery])) {
                $pickUpProductFound = 0;
                $pickUpProductFoundDetails = array();
                $storeDetails = new MPStoreProductAvailable();
                $carrierList = $storeDetails->getCarrierByIdProduct($this->context->cart->getProducts());
                foreach ($carrierList as $carrier) {
                    if (in_array(Configuration::get('MP_STORE_ID_CARRIER'), $carrier)) {
                        $pickUpProductFoundDetails[] = 0;
                    } else {
                        $pickUpProductFoundDetails[] = 1;
                    }
                }
                if (count($pickUpProductFoundDetails) == 1 && $pickUpProductFoundDetails[0]) {
                    $pickUpProductFound = 0;
                } else {
                    foreach ($pickUpProductFoundDetails as $productDetails) {
                        $pickUpProductFound = (int)($pickUpProductFound ^ $productDetails);
                    }
                }

                if ($pickUpProductFound) {
                    foreach (array_keys($carriersList[$this->context->cart->id_address_delivery]) as $carrier) {
                        $carriers = explode(',', $carrier);
                        if (in_array(Configuration::get('MP_STORE_ID_CARRIER'), $carriers)) {
                            $pickUpProductFound = 0;
                        }
                    }
                }

                $resetStoreProduct = 0;
                if (isset($this->context->cookie->mpForcePickUpCarrier)
                    && $this->context->cookie->mpForcePickUpCarrier
                    // && $pickUpProductFound == 1
                ) {
                    $resetStoreProduct = 1;
                    $pickUpProductFound = 2;
                }

                $this->context->smarty->assign(
                    array(
                        'MP_GEOLOCATION_API_KEY' => Configuration::get('MP_GEOLOCATION_API_KEY'),
                        'formSubmitUrl' => $this->context->link->getPageLink(
                            'order',
                            null,
                            null,
                            array(
                                'mpApplyStorePickUp' => $pickUpProductFound
                            )
                        ),
                        'pickUpProductFound' => $pickUpProductFound,
                        'resetStoreProduct' => $resetStoreProduct
                    )
                );
                return $this->fetch('module:mpstorelocator/views/templates/hook/pickup_store_map.tpl');
            }
        }
    }

    public function hookActionCarrierUpdate($params)
    {
        if ((int) Configuration::get('MP_STORE_ID_CARRIER') === (int) $params['id_carrier']) {
            Configuration::updateValue('MP_STORE_ID_CARRIER', $params['carrier']->id);
        }
    }

    public function uninstallCarrier()
    {
        $carrier = new Carrier((int) Configuration::get('MP_STORE_ID_CARRIER'));
        $carrier->delete();
        return true;
    }


    /**
     *
     * @return boolean
     */
    public function installCarrier()
    {
        $carrier = new Carrier((int) Configuration::get('MP_STORE_ID_CARRIER'));
        if (Validate::isLoadedObject($carrier)) {
            if ($carrier->deleted) {
                $carrier->deleted = 0;
                $carrier->update();
            }
            return true;
        }
        $languages = Language::getLanguages(true);
        $carrier->name = $this->l('Marketplace Store pick up');
        $carrier->is_module = 1;
        $carrier->is_free = 1;
        $carrier->active = 1;
        $carrier->deleted = 0;
        $carrier->shipping_handling = 0;
        $carrier->range_behavior = 0;
        $carrier->shipping_external = 0;
        $carrier->external_module_name = $this->name;
        $carrier->need_range = 1;
        $carrier->id_tax_rules_group = 0;
        foreach ($languages as $language) {
            $carrier->delay[(int) $language['id_lang']] = $this->l('Pick Up In Store');
        }

        if ($carrier->add()) {
            Configuration::updateValue('MP_STORE_ID_CARRIER', $carrier->id);
            return (
                $this->insertCarrierGroup($carrier)
                && $this->addToZones($carrier)
                && $this->addPriceRange($carrier)
                && $this->addWeightRange($carrier)
            );
        } else {
            return false;
        }
    }

    /**
     *
     * @param Carrier $carrier
     * @return boolean
     */
    protected function insertCarrierGroup($carrier)
    {
        $success = array();
        $groups = Group::getGroups(true);
        foreach ($groups as $group) {
            $carrier_group_data = array(
                'id_carrier' => (int) $carrier->id,
                'id_group' => (int) $group['id_group']
            );
            $success[] = Db::getInstance()->insert('carrier_group', $carrier_group_data);
        }
        return array_sum($success) >= count($success);
    }

    /**
     *
     * @param Carrier $carrier
     * @return boolean
     */
    protected function addToZones($carrier)
    {
        $success = array();
        $zones = Zone::getZones();
        $rangeWeight = new RangeWeight();
        $rangePrice = new RangePrice();
        foreach ($zones as $zone) {
            $carrierZoneData = array(
                'id_carrier' => (int) $carrier->id,
                'id_zone' => (int) $zone['id_zone']
            );
            $success[] = Db::getInstance()->insert('carrier_zone', $carrierZoneData);
            $deliveryPriceRange = array(
                'id_carrier' => (int) $carrier->id,
                'id_range_price' => (int) $rangePrice->id,
                'id_range_weight' => null,
                'id_zone' => (int) $zone['id_zone'],
                'price' => '0'
            );
            $success[] = Db::getInstance()->insert('delivery', $deliveryPriceRange, true);

            $deliveryWeightRange = array(
                'id_carrier' => (int) $carrier->id,
                'id_range_price' => null,
                'id_range_weight' => (int) $rangeWeight->id,
                'id_zone' => (int) $zone['id_zone'],
                'price' => '0'
            );
            $success[] = Db::getInstance()->insert('delivery', $deliveryWeightRange, true);
        }
        return array_sum($success) >= count($success);
    }

    /**
     *
     * @param Carrier $carrier
     * @return boolean
     */
    protected function addPriceRange($carrier)
    {
        $rangePrice = new RangePrice();
        $rangePrice->id_carrier = $carrier->id;
        $rangePrice->delimiter1 = '0';
        $rangePrice->delimiter2 = '10000';
        return $rangePrice->add();
    }

    /**
     *
     * @param Carrier $carrier
     * @return boolean
     */
    protected function addWeightRange($carrier)
    {
        $rangeWeight = new RangeWeight();
        $rangeWeight->id_carrier = $carrier->id;
        $rangeWeight->delimiter1 = '0';
        $rangeWeight->delimiter2 = '10000';
        return $rangeWeight->add();
    }
	public static function getCravingsAndThemes()
    {
        $rencontrer_un_artisan = [];
        foreach (WkMpSellerProductCategory::getSimpleCategories(Context::getContext()->language->id, self::getSearchEngineCategoriesIDs()) as $cat) {
            $rencontrer_un_artisan[(int)$cat['id_category']] = $cat['name'];
        }

    	return array(
            'acheter_local_et_artisanal' => array(
                'name' => 'Acheter local',
                'list' => array(
                    'Epicerie salée',
                    'Gourmandise sucrée',
                    'Vins, bières et spiritueux',
                    'Boissons et jus de fruits',
                    'Art et peinture',
                    'Livres et papeterie',
                    'Beauté et bien-être',
                    'Maison et déco',
                    'Mode et accessoires',
                    'Bijoux et créations',
                    'Jeux et jouets',
                    'Instruments de musique',
                    // 'Nature et médecine',
                )
            ),
            'decouvrir_une_production_locale' => array(
                'name' => 'Trouver une activité',
                'list' => array(
                    'A la rencontre d\'un éleveur',
                    'Découverte d\'un producteur',
                    'Métiers de bouche et gastronomie',
                    'Brasserie / Oenologie / Apéritif',
                    'Modelage',
                    'Activités créatives et artistiques',
                    'Dessin / Peinture / Photo',
                    'Confection mode',
                    'Orfèvrerie / bijoux',
                    'Prendre soin de soi',
                    'Histoire et Patrimoine',
                    'Mode et accessoires',
                    'En musique',
                    'Insolite'
                )
            ),
    		'rencontrer_un_artisan' => array(
    			'name' => 'Géolocaliser un artisan',
    			'list' => $rencontrer_un_artisan
    		)
    	);
    }

    public static function getSearchEngineCategoriesIDs()
    {
        return [105, 106, 109, 110, 118, 117, 107, 114, 119, 138, 120, 121, 122, 113, 111, 108, 115, 112, 116];
    }
}
