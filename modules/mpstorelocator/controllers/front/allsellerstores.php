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

class MpStoreLocatorallSellerStoresModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Store Locations', [], 'Breadcrumb'),
            'url' => ''
        ];

        return $breadcrumb;
    }

    public function initContent()
    {
        parent::initContent();

        if (Configuration::get('MP_STORE_ALL_SELLER')) {
            $id_lang = $this->context->language->id;
            $pp_theme = (string)Tools::getValue('pp_theme');
            $pp_city = (string)Tools::getValue('pp_place');
            $seller_stores = MarketplaceStoreLocator::getAllStore(true, $pp_city, $pp_theme);

            if ($seller_stores) {
                // get store location details
                foreach ($seller_stores as $key => $store) {
                    $obj_country = new Country($store['country_id'], $id_lang);
                    $obj_state = new State($store['state_id']);
                    $seller_stores[$key]['country_name'] = $obj_country->name;
                    $seller_stores[$key]['state_name'] = $obj_state->name;

                    if (file_exists(_PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$store['id'].'.jpg')) {
                        $seller_stores[$key]['img_exist'] = 1;
                    } else {
                        $seller_stores[$key]['img_exist'] = 0;
                    }
                    // assign store url
                    $mpSeller = WkMpSeller::getSeller($store['id_seller'], $id_lang);
                    $seller_stores[$key]['store_det_url'] = $this->context->link->getModulelink('marketplace', 'shopstore', array('mp_shop_name' => $mpSeller['link_rewrite']));
                }

                Media::addJsDef([
                    'storeLocationsJson' => Tools::jsonEncode($seller_stores),
                ]);

                $this->context->smarty->assign(array(
                    'manage_status' => Configuration::get('MP_STORE_LOCATION_ACTIVATION'),
                    'store_locations' => $seller_stores,
                ));
            }

            if (!empty($pp_theme)  && !count($seller_stores)) {
                $storesLatLng = array();
                $trackStores = array();
                $centerLatLng = '';
                $rel_seller_stores = MarketplaceStoreLocator::getAllStore(true, null, $pp_theme);
                foreach ($rel_seller_stores as &$_store) {
                    $obj_country = new Country($_store['country_id'], $id_lang);
                    $obj_state = new State($_store['state_id']);
                    $_store['country_name'] = $obj_country->name;
                    $_store['state_name'] = $obj_state->name;

                    if (file_exists(_PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$store['id'].'.jpg')) {
                        $_store['img_exist'] = 1;
                    } else {
                        $_store['img_exist'] = 0;
                    }
                    $latLng = trim($_store['latitude']) .','. trim($_store['longitude']);
                    $storesLatLng[$latLng] = $latLng;
                    $trackStores[$latLng] = $_store;

                    if (!empty($pp_city) && $pp_city == $_store['city_name']) {
                        $centerLatLng = $latLng;
                    }
                    // assign store url
                    $mpSeller = WkMpSeller::getSeller($store['id_seller'], $id_lang);
                    $seller_stores[$key]['store_det_url'] = $this->context->link->getModulelink('marketplace', 'shopstore', array('mp_shop_name' => $mpSeller['link_rewrite']));
                }

                $mp_geolocation_api_key = Configuration::get('MP_GEOLOCATION_API_KEY');
                // unset the center latlng from stores latlng
                unset($storesLatLng[$centerLatLng], $trackStores[$centerLatLng]);

                $getGMatrixInfos = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=". $centerLatLng ."&destinations=". urlencode(implode('|', $storesLatLng)) ."&key=". $mp_geolocation_api_key);
                $decodeData = json_decode($getGMatrixInfos, true);
                $getAvailableStores = array();

                if ($decodeData['status'] == 'OK') {
                    $elems = $decodeData['rows'][0]['elements'];
                    if (isset($elems) && count($elems)) {
                        sort($trackStores);
                        $track = 0;
                        foreach ($elems as $data) {
                            if ($data['status'] != 'OK') {
                                //|| $data['distance']['value'] > 50000) {
                                $track++;
                                continue;
                            }
                            $getAvailableStores[] = $trackStores[$track];
                            $track++;
                        }
                    }
                }

                if (!count($getAvailableStores)) {
                    $getAvailableStores = $seller_stores;
                }

                Media::addJsDef([
                    'storeLocationsJson' => Tools::jsonEncode($getAvailableStores),
                ]);

                $this->context->smarty->assign(array(
                    'manage_status' => Configuration::get('MP_STORE_LOCATION_ACTIVATION'),
                    'store_locations' => $getAvailableStores,
                ));
            }

            Media::addJsDef([
                'bee_icon' => _THEME_IMG_DIR_ .'bee-activite-g4.svg',
                'pp_theme_default_opt_name' => 'Toutes les thématiques',
                'pp_theme_default_city_name' => 'Selectionnez un lieu',
            ]);

            $country = $this->context->country;
            $MP_GEOLOCATION_API_KEY = Configuration::get('MP_GEOLOCATION_API_KEY');

            $this->context->smarty->assign(array(
                'title_text_color' => Configuration::get('MP_TITLE_TEXT_COLOR'),
                'title_bg_color' => Configuration::get('MP_TITLE_BG_COLOR'),
                'MP_GEOLOCATION_API_KEY' => Configuration::get('MP_GEOLOCATION_API_KEY'),
                'country' => $country,
                'modules_dir' => _MODULE_DIR_,
                'cravings' => MpStoreLocator::getCravingsAndThemes(),
            ));
			
			 //  if (isset($this->context->customer->id)) {
         //   $seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
			 $idLang = $this->context->language->id;
			  $sellerProduct = WkMpSellerProduct::getSellerProduct('', 'all', $idLang);
			    if ($sellerProduct) {
                    $sellerProduct = $this->getProductDetails($sellerProduct);
                    $objBookingProductInfo = new WkMpBookingProductInformation();
                    foreach ($sellerProduct as $key => &$product) {
                        $product['price'] = Tools::displayPrice($product['price']);
                        $bookingProductInfo = $objBookingProductInfo->getBookingProductInfoByMpIdProduct($product['id_mp_product']);
                        if ($bookingProductInfo) {
                            $product['booking_type'] = $bookingProductInfo['booking_type'];
                        } else {
                            unset($sellerProduct[$key]);
                        }
                    }
                }

				$this->context->smarty->assign(array(
                    'products_status' => Configuration::get('WK_MP_SELLER_PRODUCTS_SETTINGS'),
                    'imageediturl' => $this->context->link->getModuleLink('marketplace', 'productimageedit'),
                    'product_lists' => $sellerProduct,
                  //  'is_seller' => $seller['active'],
                    'logic' => 'mpbookingproduct',
                    'static_token' => Tools::getToken(false),
                ));
			 //  }

            $this->defineJSVars();
            $this->setTemplate('module:mpstorelocator/views/templates/front/allsellerstores.tpl');
        }
    }

    public function getProductDetails($productList)
    {
        //if multilang is OFF then current lang will be PS Default Lang
        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
            $idLang = $this->context->language->id;
        } else {
            $idLang = Configuration::get('PS_LANG_DEFAULT');
        }

        $language = Language::getLanguage((int) $idLang);

        foreach ($productList as &$product) {
            if ($product['id_ps_product']) { // if product activated
                
                $idPsProduct = $product['id_ps_product'];
                $link = new Link();
                $product['url'] = $link->getProductLink($idPsProduct);

                $objProduct = new Product($idPsProduct, false, $idLang);
                $cover = Product::getCover($idPsProduct);

                if ($cover) {
                    $objImage = new Image($cover['id_image']);
                   $product['image_path'] = _THEME_PROD_DIR_.$objImage->getExistingImgPath().'.jpg';
                    $product['cover_image'] = $idPsProduct.'-'.$cover['id_image'];
                }

                $product['id_product'] = $idPsProduct;
                $product['id_lang'] = $idLang;
                $product['lang_iso'] = $language['iso_code'];
                $product['obj_product'] = $objProduct;
            } else { //if product not active
                $unactiveImage = WkMpSellerProduct::getInactiveProductImageByIdProduct($product['id_mp_product']);
                // product is inactive so by default first image is taken because no one is cover image
                if ($unactiveImage) {
                    $product['unactive_image'] = $unactiveImage[0]['seller_product_image_name'];
                }
            }

            //convert price for multiple currency
            $product['price'] = Tools::convertPrice($product['price']);
        }
        return $productList;
    }

    public function setMedia()
    {
        parent::setMedia();

        // Register JS
        $this->registerJavascript('store-sellerstores', 'modules/'.$this->module->name.'/views/js/sellerstores.js', [
              'priority' => 250,
              'position' => 'bottom',
        ]);
        // $this->registerJavascript('store-details', 'modules/'.$this->module->name.'/views/js/storedetails.js');

        // Register CSS
        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerStylesheet('store-details', 'modules/'.$this->module->name.'/views/css/store_details.css');
    }

    public function defineJSVars()
    {
        $jsVars = [
            'url_getstore_by_product' => $this->context->link->getModulelink('mpstorelocator', 'getstorebyproduct'),
            'url_getstorebykey' => $this->context->link->getModulelink('mpstorelocator', 'getstorebykey'),
            'mpstore_ajax_url' => $this->context->link->getModulelink('mpstorelocator', 'ajax'),
            'no_store_msg' => $this->trans('No store found', [], 'Modules.MpStoreLocator'),
        ];
        return Media::addJsDef($jsVars);
    }
}
