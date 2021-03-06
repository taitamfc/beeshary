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

class mpstorelocatorstoredetailsModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $idProduct = Tools::getValue('id_product');
        $idStore = Tools::getValue('id_store');
        $breadcrumb = parent::getBreadcrumbLinks();
        if ($idProduct) {
            $objProduct = new Product($idProduct, false, $this->context->language->id);
            $breadcrumb['links'][] = [
                'title' => $this->getTranslator()->trans($objProduct->name, [], 'Breadcrumb'),
                'url' => $objProduct->getLink()
            ];

            $breadcrumb['links'][] = [
                'title' => $this->getTranslator()->trans('Product Stores', [], 'Breadcrumb'),
                'url' => ''
            ];
        }

        if ($idStore || Tools::getValue('stores')) {
            $objStore = new MarketplaceStoreLocator($idStore);
            if ($idStore) {
                $url = $this->context->link->getModuleLink(
                    'mpstorelocator',
                    'storedetails',
                    array('stores' => 1)
                );
            } else {
                $url = '';
            }
            $breadcrumb['links'][] = [
                'title' => $this->getTranslator()->trans('Stores', [], 'Breadcrumb'),
                'url' => $url
            ];

            if ($idStore) {
                $breadcrumb['links'][] = [
                    'title' => $this->getTranslator()->trans($objStore->name, [], 'Breadcrumb'),
                    'url' => ''
                ];
            }
        }
        return $breadcrumb;
    }

    public function initContent()
    {

        parent::initContent();
        $idLang = $this->context->cookie->id_lang;
        $idProduct = Tools::getValue('id_product');
        $idStore = Tools::getValue('id_store');
        $idBadge = Tools::getValue('badge');
        $stores = array();
        if (empty($idProduct)
            && empty($idStore)
            && !Tools::getValue('stores')
            && !Tools::getValue('ajax')
        ) {
            Tools::redirect($this->context->link->getPageLink('nopagefound'));
        }

        $pp_badges = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_badges` WHERE active = 1 ORDER BY badge_name ASC');



        $this->context->smarty->assign(
            array(
                'displayContactDetails' => Configuration::get('MP_STORE_CONTACT_DETAILS'),
                'displayFax' => Configuration::get('MP_STORE_DISPLAY_FAX'),
                'displayEmail' => Configuration::get('MP_STORE_DISPLAY_EMAIL'),
                'displayStoreTiming' => Configuration::get('MP_DISPLAY_STORE_TIMING'),
                'displayStorePage' => Configuration::get('MP_STORE_STORE_PAGE'),
                'pp_badges' => $pp_badges,
                'idBadge' => $idBadge,
            )
        );
        if ($idProduct || Tools::getValue('stores')) {
            if ($idProduct) {
                if (empty((new Product($idProduct))->id)) {
                    Tools::redirect($this->context->link->getPageLink('nopagefound'));
                }
                $product_stores = MarketplaceStoreProduct::getProductStore($idProduct, true);
                if ($product_stores) {
                    //store list
                    foreach ($product_stores as $value) {
                        $stores[] = MarketplaceStoreLocator::getStoreById($value['id_store'], true);
                    }
                }
                $stores = MarketplaceStoreLocator::getMoreStoreDetails($stores);

                $this->context->smarty->assign(
                    array(
                        'active_product_id' => $idProduct,
                        'selectedProductName' => Product::getProductName($idProduct)
                    )
                );
                $obj_mpproduct = new WkMpSellerProduct();
                $mp_product = $obj_mpproduct->getSellerProductByPsIdProduct($idProduct);
                $id_seller = $mp_product['id_seller'];
                if ($id_seller) {
                    $all_products = $obj_mpproduct->getSellerProduct($id_seller, true, $this->context->language->id);
                    if ($all_products) {
                        foreach ($all_products as &$product) {
                            $obj_product = new Product($product['id_ps_product'], false, $idLang);
                            $product['product_name'] = $obj_product->name;
                        }
                        $this->context->smarty->assign('all_products', $all_products);

                        // get store location details
                        foreach ($stores as &$store) {
                            $obj_country = new Country($store['country_id'], $idLang);
                            $obj_state = new State($store['state_id']);
                            $store['country_name'] = $obj_country->name;
                            $store['state_name'] = $obj_state->name;

                            if (file_exists(_PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$store['id'].'.jpg')) {
                                $store['img_exist'] = 1;
                            } else {
                                $store['img_exist'] = 0;
                            }
                        }

                        if (count($stores)) {
                            Media::addJsDef([
                                'storeLocationsJson' => Tools::jsonEncode($stores),
                            ]);

                            $this->context->smarty->assign(array(
                                'store_locations' => $stores,
                            ));
                        }

                        $this->context->smarty->assign(
                            array(
                                'active_product_id' => $idProduct,
                                'modules_dir' => _MODULE_DIR_,
                            )
                        );

                        $this->defineJSVars();
                        // $this->setTemplate('module:'.$this->module->name.'/views/templates/front/storedetails.tpl');
                    }
                }
            } else {
				$p 		= ( Tools::getValue('p') ) ? Tools::getValue('p') : 1;
                $stores = MarketplaceStoreLocator::getAllStore(true,['pagi'=>true,'limit'=>12,'page'=>$p]);
				if( $stores ){
					$total_page = 100;
				}else{
					$total_page = $p;
				}
				
            }
            
            $stores = MarketplaceStoreLocator::getMoreStoreDetails($stores);
            
            /*10km from Nice */
            // $currentLocation['lat'] = '43.7032932';
            // $currentLocation['lng'] = '7.1827765';
            // $radius = 10;
            
            $currentLocation = false;
            $radius = 0;
            $pp_theme   = Tools::getValue('savoir');
            $pp_badge   = Tools::getValue('badge');

            //currentLocation
            if (!empty($currentLocation)) {
                $distanceStore = [];
                foreach ($stores as $key => $store) {
                    $distance = $this->distance($currentLocation, $store['latitude'], $store['longitude']);
                    if (empty($radius)) {
                        if (Configuration::get('MP_STORE_DISTANCE_UNIT') == "METRIC") {
                            $distance = $distance . 'Km';
                        } else {
                            $distance = $distance . 'Miles';
                        }
                        $store['distance'] = $distance;
                        $distanceStore[] = $store;
                    } else {
                        if ($distance <= $radius) {
                            if (Configuration::get('MP_STORE_DISTANCE_UNIT') == "METRIC") {
                                $distance = $distance . 'Km';
                            } else {
                                $distance = $distance . 'Miles';
                            }
                            $store['distance'] = $distance;
                            $distanceStore[] = $store;
                        }
                    }
                }
                $stores = $distanceStore;
                $this->context->smarty->assign('currentLocation', $currentLocation);
            }

            if (count($stores) && $pp_theme) {
                $distanceStore = [];
                foreach( $stores as $key => $store ){
                    //PAUL : search Multi   
                    if( $store['custom_fields']['pp_theme'] == $pp_theme || in_array($pp_theme, explode(',', $store['custom_fields']['pp_theme'])) ){
                    //PAUL
                        $distanceStore[] = $store;
                    }
                }
                $stores = $distanceStore;
            }

            if (count($stores) && $pp_badge) {
                $distanceStore = [];
                foreach( $stores as $key => $store ){
                    $badges = $store['custom_fields']['badges'];
                    if( $badges ){
                        if( in_array( $pp_badge, $badges ) ){
                            $distanceStore[] = $store;
                        }
                    }
                }
                $stores = $distanceStore;
            }


            $extra_field_value_obj = new MarketplaceExtrafieldValue();
            if (count($stores)) {
                foreach( $stores as $key => $store ){
                    $seller             = WkMpSeller::getSeller($store['id_seller'],$this->context->language->id);

                    $extra_fields = $extra_field_value_obj->findExtrafieldValues($store['id_seller']);
                    if( count($extra_fields) ){
                        foreach( $extra_fields as $extra_field ){
                            switch ($extra_field['extrafield_id']) {
                                case 1:
                                    $seller['profession'] = $extra_field['field_value'];
                                    break;
                                case 2:
                                    $seller['quisuisje'] = $extra_field['field_value'];
                                    break;
                                case 3:
                                    $seller['mapassion'] = $extra_field['field_value'];
                                    break;
                                case 4:
                                    $seller['unproverbe'] = $extra_field['field_value'];
                                    $seller['unproverbe'] = str_replace('\\','',$seller['unproverbe']);
                                    break;
                                case 5:
                                    $seller['labels'] = $extra_field['field_value'];
                                    break;
                                case 6:
                                    $seller['siret'] = $extra_field['field_value'];
                                    break;
                                case 10:
                                    $seller['spoken_langs'] = $extra_field['field_value'];
                                    $seller['spoken_langs'] = explode(',',$seller['spoken_langs']);
                                    break;

                                default:
                                    # code...
                                    break;
                            }
                        }
                    }

                    $average_ratings    = WkMpSellerReview::getSellerAvgRating($store['id_seller']);

                    $objReview = new WkMpSellerReview();
                    $reviews = $objReview->getReviewsByConfiguration($store['id_seller']);
                    $average_ratings                = $reviews['avg_rating'];
                    $total_review                   = ( $reviews ) ? count( $reviews['reviews'] ) : 0;
                    $stores[$key]['average_ratings']    = ($average_ratings) ? $average_ratings : 0;
                    $stores[$key]['left_ratings']       = 5 - $average_ratings;
                    $stores[$key]['total_review']       = $total_review;
                    $stores[$key]['seller']             = $seller;

                    $sellerBannerPath = WkMpSeller::getSellerBannerLink($seller);
                    $stores[$key]['seller_banner_path'] = $sellerBannerPath;

                    $mpSellerProfileImage = $seller['profile_image'];
                    if ($mpSellerProfileImage && file_exists(_PS_MODULE_DIR_.$this->module->name.'/views/img/seller_img/'.$mpSellerProfileImage)) {
                        $seller_img_path =  _MODULE_DIR_.$this->module->name.'/views/img/seller_img/'.$mpSellerProfileImage;
                    } else {
                        $seller_img_path = _MODULE_DIR_.$this->module->name.'/views/img/seller_img/defaultimage.jpg';
                    }
                    $stores[$key]['seller_img_path'] = $seller_img_path;
                }
                $this->context->smarty->assign('store_locations', $stores);
            }

            $this->context->smarty->assign(
                array(
                    'psModuleDir' => _MODULE_DIR_,
                    'enableSearchProduct' => Configuration::get('MP_STORE_SEARCH_BY_PRODUCT')
                )
            );
			
			if ( Tools::getValue('is_ajax') ) {
				$this->context->smarty->assign('filtered_stores', $stores);
				$this->context->smarty->assign('stores', $stores);
				$this->context->smarty->assign('store_locations', $stores);
				
				$the_html = '';
				if( $stores ){
					$the_html = $this->context->smarty->fetch(
						_PS_MODULE_DIR_.'mpstorelocator/views/templates/front/filtered_store.tpl'
					);
				}
				
				die(
					json_encode(
						array(
							'html' => $the_html,
							'stores' => $stores,
							'filtered_stores' 	=> $stores,
							'store_locations' 	=> $stores,
							'p' 				=> $p,
							'total_page' 		=> $total_page,
							'hasError' 			=> false
						)
					)
				);
			}




            $this->setTemplate('module:mpstorelocator/views/templates/front/storedetails.tpl');
        } elseif ($idStore) {
            if (Configuration::get('MP_STORE_STORE_PAGE') == 0 && !Tools::getValue('ajax')) {
                Tools::redirect($this->context->link->getPageLink('nopagefound'));
            }
            $store = MarketplaceStoreLocator::getStoreById($idStore, true);
            if (!empty($store)) {
                $stores[] = $store;
                $stores = MarketplaceStoreLocator::getMoreStoreDetails($stores);
                Media::addJsDef(
                    array(
                        'displayInfoWindowOnStore' => 1
                    )
                );
            }

            $productDetails = $this->getStoreProductsDetails($idStore);
            if (Configuration::get('MP_STORE_PICK_UP_PAYMENT')) {
                $paymentOptions =  MarketplaceStoreLocator::getStoreById($idStore, true);
                // $paymentOptions = MpStorePay::getPaymentOptionDetails(json_decode($paymentOptions['payment_option']));
                $this->context->smarty->assign(
                    array(
                        'paymentOptions' => array(),
                        'imagePath' => _MODULE_DIR_.'mpstorelocator/views/img/payment_logo/'
                    )
                );
            }
            $this->context->smarty->assign(
                array(
                    'storeLogoImgPath' => _MODULE_DIR_.'mpstorelocator/views/img/store_logo/',
                    'directionImg' => _MODULE_DIR_.'mpstorelocator/views/img/direction-icon.png',
                    'storeLocationsJson' => Tools::jsonEncode($stores),
                    'store_locations' => $stores,
                    'products' => $productDetails,
                )
            );
            $this->setTemplate('module:mpstorelocator/views/templates/front/store.tpl');
        }


        Media::addJsDef(
            array(
                'storeLink' => $this->context->link->getModuleLink(
                    'mpstorelocator',
                    'storedetails'
                ),
                'storeLocationsJson' => Tools::jsonEncode($stores),
                'storeLocate' => 1,
                'no_store_found' => $this->module->l('No Store Found'),
                'storeTiming' => $this->module->l('Store Timing'),
                'contactDetails' => $this->module->l('Contact'),
                'getDirections' => $this->module->l('Get directions'),
                'emailMsg' => $this->module->l('Email'),
                'closedMsg' => $this->module->l('Closed'),
                'ajaxurlStoreByKey' => $this->context->link->getModuleLink('mpstorelocator', 'storedetails'),
            )
        );
    }

    public function displayAjaxSearchProduct()
    {
        //Search seller product for auction
        $html = '';
        if (!empty(Tools::getValue('search_key'))) {
            $products = MarketplaceStoreProduct::getProducts(Tools::getValue('search_key'));
            if ($products) {
                $html .= '<ul>';
                foreach ($products as $product) {
                    $html .= '<li id_product="'.$product['id_product'].'">'.$product['name'].'</li>';
                }
            } else {
                $html .= '<li>'.$this->module->l('No Product Found').'</li>';
            }
            $html .= '</ul>';
        }
        die(
        json_encode(
            array('html' => $html)
        )
        );
    }

    public function getStoreProductsDetails($idStore)
    {
        $productDetails = MarketplaceStoreProduct::getStoreProducts($idStore);
        $productDetails = $this->setWkPagination($productDetails);
        foreach ($productDetails as $key => $product) {
            if (empty($product['id_image'])) {
                $productDetails[$key]['image'] = Tools::getShopDomainSsl(true, true)
                    .__PS_BASE_URI__.'/img/p/'.$this->context->language->iso_code.'.jpg';
            } else {
                $productDetails[$key]['image'] = str_replace(
                    'https://',
                    Tools::getShopProtocol(),
                    $this->context->link->getImageLink(
                        $product['link_rewrite'],
                        $product['id_image'],
                        ImageType::getFormattedName('home')
                    )
                );
            }
            $productDetails[$key]['link'] = $this->context->link->getProductLink($product['id_product']);
        }
        return $productDetails;
    }

    public function displayAjaxGetProducts()
    {
        $page = 1;
        $productLimit = Tools::getValue('n');
        $idStore = Tools::getValue('id_store');
        $startLimit = ($page-1) * $productLimit;
        $this->context->smarty->assign(
            array(
                'products' => $this->getStoreProductsDetails($idStore),
                'class' => 'wkstore-products tab-pane',
                'id' => 'wk_store_products',
                'current_url' => $this->context->link->getModuleLink(
                    'mpstorelocator',
                    'storedetails',
                    array('id_store' => $idStore)
                )
            )
        );
        die(
			json_encode(
				array(
					'html' => $this->context->smarty->fetch(
						_PS_MODULE_DIR_.'mpstorelocator/views/templates/front/store_product_list.tpl'
					)
				)
			)
        );
    }

    public function setMedia()
    {
        parent::setMedia();

        // Google Map Library
        $language = $this->context->language;
        $country = $this->context->country;
        $MP_GEOLOCATION_API_KEY = Configuration::get('MP_GEOLOCATION_API_KEY');
        $storeConfiguration = MpStoreConfiguration::getStoreConfiguration();
        $this->registerJavascript(
            'google-map-lib',
            "https://maps.googleapis.com/maps/api/js?key=$MP_GEOLOCATION_API_KEY&libraries=places&language=$language->iso_code&region=$country->iso_code",
            [
                'server' => 'remote'
            ]
        );

        Media::addJsDef(
            array(
                'storeLogoImgPath' => _MODULE_DIR_.'mpstorelocator/views/img/store_logo/',
                'autoLocate' => Configuration::get('MP_AUTO_LOCATE'),
                'displayCluster' => Configuration::get('MP_STORE_CLUSTER'),
                'openInfoWindowEvent' => Configuration::get('MP_INFO_WINDOW'),
                'distanceType' => Configuration::get('MP_STORE_DISTANCE_UNIT'),
                'markerIcon' => _MODULE_DIR_.'mpstorelocator/views/img/'.Configuration::get(
                        'MP_STORE_MARKER_NAME'
                    ),
                'idProductLoad' => Tools::getValue('id_product'),
                'idStoreLoad' => Tools::getValue('id_store'),
                'storeConfiguration' => $storeConfiguration,
                'displayCustomMarker' => Configuration::get('MP_STORE_MARKER_ICON_ENABLE'),
                'displayContactDetails' => Configuration::get('MP_STORE_CONTACT_DETAILS'),
                'displayFax' => Configuration::get('MP_STORE_DISPLAY_FAX'),
                'displayEmail' => Configuration::get('MP_STORE_DISPLAY_EMAIL'),
                'displayStoreTiming' => Configuration::get('MP_DISPLAY_STORE_TIMING'),
                'displayStorePage' => Configuration::get('MP_STORE_STORE_PAGE'),
                'maxZoomLevel' => Configuration::get('MP_STORE_MAP_ZOOM'),
                'maxZoomLevelEnable' => Configuration::get('MP_STORE_MAP_ZOOM_ENABLE'),
                'controller' => 'storedetails'
            )
        );
        // Register JS
        $this->registerJavascript('storedetails', 'modules/'.$this->module->name.'/views/js/front/storedetails.js');
        $this->registerJavascript(
            'cluster-js',
            'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js',
            array(
                'priority' => 100,
                'server' => 'remote'
            )
        );
        // Register CSS
        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerStylesheet('store_details', 'modules/'.$this->module->name.'/views/css/front/store_details.css');
    }

    public function defineJSVars()
    {
        $jsVars = [
            'url_getstore_by_product' => $this->context->link->getModulelink('mpstorelocator', 'getstorebyproduct'),
            'url_getstorebykey' => $this->context->link->getModulelink('mpstorelocator', 'getstorebykey'),
            'no_store_msg' => $this->trans('No store found', [], 'Modules.MpStoreLocator'),
        ];
        return Media::addJsDef($jsVars);
    }

    public function setWkPagination($storeProduct)
    {
        $p = Tools::getValue('p');
        $n = Configuration::get('PS_PRODUCTS_PER_PAGE');
        // default page number
        if (!$p) {
            $p = 1;
        }

        $default_products_per_page = max(1, (int)Configuration::get('PS_PRODUCTS_PER_PAGE'));
        $nArray = array($default_products_per_page, $default_products_per_page * 2, $default_products_per_page * 5);

        $total_products = count($storeProduct);
        if ((int)Tools::getValue('n') && (int)$total_products > 0) {
            $nArray[] = $total_products;
        }
        // Retrieve the current number of products per page
        // (either the default, the GET parameter or the one in the cookie)
        $n = $default_products_per_page;
        if (isset($this->context->cookie->nb_item_per_page)
            && in_array($this->context->cookie->nb_item_per_page, $nArray)
        ) {
            $n = (int)$this->context->cookie->nb_item_per_page;
        }
        if ((int)Tools::getValue('n') && in_array((int)Tools::getValue('n'), $nArray)) {
            $n = (int)Tools::getValue('n');
        }

        if ($n != $default_products_per_page || isset($this->context->cookie->nb_item_per_page)) {
            $this->context->cookie->nb_item_per_page = $n;
        }

        $planCount = count($storeProduct);
        $this->context->smarty->assign(
            array(
                'nb_products' => $planCount,
                'p' => $p,
                'n' => $n,
                'nArray' => $nArray,
                'page_count' => (int) ceil($planCount/$n),
                'wk_controller_page' => $this->context->link->getModuleLink(
                    'mpstorelocator',
                    'storedetails',
                    array('id_store' => Tools::getValue('id_store'))
                ),
            )
        );

        // get plan by page
        $storeProduct = $this->filterPlanByPage($storeProduct, $p, $n);

        return $storeProduct;
    }

    public function filterPlanByPage($storeProduct, $p, $n)
    {
        $result = array();
        if ($storeProduct) {
            $start = ($p - 1) * $n;
            $end = $start + $n;
            for ($i = $start; $i < $end; $i++) {
                if (array_key_exists($i, $storeProduct)) {
                    $result[] = $storeProduct[$i];
                }
            }
        }

        return $result;
    }

    public function displayAjaxGetStoreDetails()
    {
        if (Tools::getIsset('id_store')) {
            $this->getStoreDetails();
        } else {
            $key = Tools::getValue('search_key');
            $idProduct = Tools::getValue('id_product');
            $currentLocation = Tools::getValue('current_location');
            $radius     = Tools::getValue('radius');
            if( !empty($currentLocation) && !$radius ){
                $radius = 15;
            }
            $pp_theme   = Tools::getValue('pp_theme');
            $pp_badge   = Tools::getValue('pp_badge');

            if (!empty($key) || !empty($idProduct)) {
                $this->context->smarty->assign('modules_dir', _MODULE_DIR_);
                if (empty($idProduct)) {
                    $stores = MarketplaceStoreLocator::getStoreByCity($key);
                } else {
                    if (empty($key)) {
                        $productStores = MarketplaceStoreProduct::getProductStore($idProduct, true, false);
                    } else {
                        $productStores = MarketplaceStoreProduct::getProductStore($idProduct, true, $key);
                    }
                    if ($productStores) {
                        foreach ($productStores as $pStore) {
                            $stores[] = MarketplaceStoreLocator::getStoreById($pStore['id_store'], true);
                        }
                    }
                }
            } else {
                $stores = MarketplaceStoreLocator::getAllStore(true);
            }
            if (isset($stores) && $stores) {
                $this->context->smarty->assign(
                    array(
                        'displayContactDetails' => Configuration::get('MP_STORE_CONTACT_DETAILS'),
                        'displayFax' => Configuration::get('MP_STORE_DISPLAY_FAX'),
                        'displayEmail' => Configuration::get('MP_STORE_DISPLAY_EMAIL'),
                        'displayStoreTiming' => Configuration::get('MP_DISPLAY_STORE_TIMING')
                    )
                );

                $allstore = MarketplaceStoreLocator::getMoreStoreDetails($stores);

                $stores = $allstore;
                
                
                
                //currentLocation
                if (!empty($currentLocation)) {
                    $distanceStore = [];
                    foreach ($stores as $key => $store) {
                        $distance = $this->distance($currentLocation, $store['latitude'], $store['longitude']);
                        if (empty($radius)) {
                            if (Configuration::get('MP_STORE_DISTANCE_UNIT') == "METRIC") {
                                $distance = $distance . 'Km';
                            } else {
                                $distance = $distance . 'Miles';
                            }
                            $store['distance'] = $distance;
                            $distanceStore[] = $store;
                        } else {
                            if ($distance <= $radius) {
                                if (Configuration::get('MP_STORE_DISTANCE_UNIT') == "METRIC") {
                                    $distance = $distance . 'Km';
                                } else {
                                    $distance = $distance . 'Miles';
                                }
                                $store['distance'] = $distance;
                                $distanceStore[] = $store;
                            }
                        }
                    }
                    $stores = $distanceStore;
                    $this->context->smarty->assign('currentLocation', $currentLocation);
                }
                
                if (count($stores) && $pp_theme) {
                    $distanceStore = [];
                    foreach( $stores as $key => $store ){
                        //PAUL : search Multi
                        if( $store['custom_fields']['pp_theme'] == $pp_theme || in_array($pp_theme, explode(',', $store['custom_fields']['pp_theme'])) ){
                        //PAUL
                            $distanceStore[] = $store;
                        }
                    }
                    $stores = $distanceStore;
                }
                
        

                if (count($stores) && $pp_badge) {
                    $distanceStore = [];
                    foreach( $stores as $key => $store ){
                        $badges = $store['custom_fields']['badges'];
                        if( $badges ){
                            if( in_array( $pp_badge, $badges ) ){
                                $distanceStore[] = $store;
                            }
                        }
                    }
                    $stores = $distanceStore;
                }

                if (count($stores)) {
                    foreach( $stores as $key => $store ){
                        $seller             = WkMpSeller::getSeller($store['id_seller'],$this->context->language->id);
                        $average_ratings    = WkMpSellerReview::getSellerAvgRating($store['id_seller']);

                        $objReview = new WkMpSellerReview();
                        $reviews = $objReview->getReviewsByConfiguration($store['id_seller']);
                        $average_ratings                = $reviews['avg_rating'];
                        $total_review                   = ( $reviews ) ? count( $reviews['reviews'] ) : 0;
                        $stores[$key]['average_ratings']    = ($average_ratings) ? $average_ratings : 0;
                        $stores[$key]['left_ratings']       = 5 - $average_ratings;
                        $stores[$key]['total_review']       = $total_review;
                        $stores[$key]['seller']             = $seller;

                        $sellerBannerPath = WkMpSeller::getSellerBannerLink($seller);
                        $stores[$key]['seller_banner_path'] = $sellerBannerPath;

                        $mpSellerProfileImage = $seller['profile_image'];
                        if ($mpSellerProfileImage && file_exists(_PS_MODULE_DIR_.$this->module->name.'/views/img/seller_img/'.$mpSellerProfileImage)) {
                            $seller_img_path =  _MODULE_DIR_.$this->module->name.'/views/img/seller_img/'.$mpSellerProfileImage;
                        } else {
                            $seller_img_path = _MODULE_DIR_.$this->module->name.'/views/img/seller_img/defaultimage.jpg';
                        }
                        $stores[$key]['seller_img_path'] = $seller_img_path;
                    }
                }
                $distanceStore = $stores;

                if ($distanceStore) {
                    $this->context->smarty->assign('filtered_stores', $distanceStore);
                    $this->context->smarty->assign('stores', $distanceStore);
                    $this->context->smarty->assign('store_locations', $distanceStore);
                    die(
						json_encode(
							array(
								'html' => $this->context->smarty->fetch(
									_PS_MODULE_DIR_.'mpstorelocator/views/templates/front/filtered_store.tpl'
								),
								'stores' => $distanceStore,
								'filtered_stores' => $distanceStore,
								'store_locations' => $distanceStore,
								'pp_theme' => $pp_theme,
								'pp_badge' => $pp_badge,
								'hasError' => false
							)
						)
                    );
                }
            }
        }
        die(json_encode(array('hasError' => true))); //ajax close
    }

    public function getStoreDetails()
    {
        $idStore = Tools::getValue('id_store');
        $idLang = $this->context->language->id;
        if ($idStore) {
            $stores[] = MarketplaceStoreLocator::getStoreById($idStore, true);
            if ($stores) {
                $stores = MarketplaceStoreLocator::getMoreStoreDetails($stores);
            }

            Media::addJsDef(
                array(
                    'storeLocationsJson' => Tools::jsonEncode($stores),
                    'storeLocate' => 1,
                )
            );
            $distanceStore = array();
            $currentLocation = Tools::getValue('current_location');
            $radius     = Tools::getValue('radius');
            if( !empty($currentLocation) && !$radius ){
                $radius = 15;
            }
            foreach ($stores as $store) {
                if (!empty($currentLocation)) {
                    $distance = round($this->distance($currentLocation, $store['latitude'], $store['longitude']));
                    if (empty($radius)) {
                        if (Configuration::get('MP_STORE_DISTANCE_UNIT') == "METRIC") {
                            $distance = $distance . 'Km';
                        } else {
                            $distance = $distance . 'Miles';
                        }
                        $store['distance'] = $distance;
                        $distanceStore[] = $store;
                    } else {
                        if ($distance <= $radius) {
                            if (Configuration::get('MP_STORE_DISTANCE_UNIT') == "METRIC") {
                                $distance = $distance . 'Km';
                            } else {
                                $distance = $distance . 'Miles';
                            }
                            $store['distance'] = $distance;
                            $distanceStore[] = $store;
                        }
                    }
                } else {
                    $distanceStore[] = $store;
                }
            }
            $this->context->smarty->assign(
                array(
                    'storeLogoImgPath' => _MODULE_DIR_.'mpstorelocator/views/img/store_logo/',
                    'directionImg' => _MODULE_DIR_.'mpstorelocator/views/img/direction-icon.png',
                    'storeLocationsJson' => Tools::jsonEncode($distanceStore),
                    'storeLocations' => $distanceStore,
                )
            );
            die(
				json_encode(
					array(
						'html' => $this->context->smarty->fetch(
							_PS_MODULE_DIR_.'mpstorelocator/views/templates/front/store_detail.tpl'
						),
						'stores' => $distanceStore,
						'hasError' => false
					)
				)
            );
        }
        die(
        json_encode(
            array(
                'hasError' => false
            )
        )
        );
    }

    public function distance($currentLocation, $lat2, $lon2)
    {
        $theta = $currentLocation['lng'] - $lon2;
        $dist = sin(deg2rad($currentLocation['lat'])) * sin(deg2rad($lat2));
        $dist +=  cos(deg2rad($currentLocation['lat'])) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = Configuration::get('MP_STORE_DISTANCE_UNIT');
        if ($unit == "METRIC") {
            return round(($miles * 1.609344), 2);
        } else {
            return round($miles, 2);
        }
    }


    public function displayAjaxGetStoreProductHtml()
    {
        $storeDetails = new MPStoreProductAvailable();
        $carrierList = $storeDetails->getCarrierByIdProduct($this->context->cart->getProducts());

        $products = $this->context->cart->getProducts();
        $otherPickupProducts = 0;
        $carrierName = array();
        foreach ($products as $key => $product) {
            $products[$key]['imageLink'] = $this->context->link->getImageLink(
                $product['link_rewrite'],
                $product['id_image'],
                ImageType::getFormattedName('small')
            );
            $availableForPickUp = MPStoreProductAvailable::availableForStorePickup($product['id_product']);
            $stores = MarketplaceStoreProduct::getAvailableProductStore($product['id_product'], true);

            if (empty($stores)
                || empty($availableForPickUp)
                || !in_array(Configuration::get('MP_STORE_ID_CARRIER'), $carrierList[$product['id_product']])
            ) {
                $products[$key]['available_store'] = false;
                $otherPickupProducts = 1;
                $selectedCarriers = json_decode(Tools::getValue('idCarriers'));
                foreach ($selectedCarriers as $carrier) {
                    if ($carrier != Configuration::get('MP_STORE_ID_CARRIER')) {
                        $carrierName[$carrier]= (new Carrier((int)$carrier))->name;
                    }
                }
            } else {
                $idStores = array_column($stores, 'id_store');
                $products[$key]['id_seller'] = MarketplaceStoreLocator::getIdSellerByIdStore($idStores);
                if ($products[$key]['id_seller']) {
                    $storeConfiguration = MpStoreConfiguration::getStoreConfiguration($products[$key]['id_seller']);
                    $products[$key]['enable_date'] = $storeConfiguration['enable_date'];
                    $products[$key]['enable_time'] = $storeConfiguration['enable_time'];
                }
                $products[$key]['available_store'] = true;
            }
            $storePickupDetails = MpStorePickUpProduct::getStorePickUpDetails(
                $this->context->cart->id,
                $product['id_product'],
                $product['id_product_attribute']
            );
            if ($storePickupDetails) {
                $cartProducts = ($this->context->cart->getProducts());
                $idProducts = array_column($cartProducts, 'id_product');
                $storeProducts = MarketplaceStoreProduct::checkStoreProducts($storePickupDetails[0]['id_store']);
                $applyForAll = 1;
                if ($storeProducts) {
                    $storeProducts = array_column($storeProducts, 'id_product');
                    foreach ($idProducts as $idProduct) {
                        if (!$availableForPickUp
                            || !in_array($idProduct, $storeProducts)
                            || !in_array(Configuration::get('MP_STORE_ID_CARRIER'), $carrierList[$idProduct])
                        ) {
                            $applyForAll = 0;
                        }
                    }
                } else {
                    $applyForAll = 0;
                }

                $products[$key]['id_store_pickup'] = $storePickupDetails[0]['id_store_pickup'];
                $products[$key]['id_store_pickup_product'] = $storePickupDetails[0]['id_store_pickup_product'];
                $products[$key]['id_store'] = $storePickupDetails[0]['id_store'];
                $products[$key]['store_details'] = MarketplaceStoreLocator::getStoreById(
                    $storePickupDetails[0]['id_store'],
                    true
                );
                $products[$key]['store_details'] = (MarketplaceStoreLocator::getMoreStoreDetails(
                    array($products[$key]['store_details'])
                ))[0];
                $dateTime = explode(' ', $storePickupDetails[0]['pickup_date']);
                $products[$key]['store_pickup_date'] = $dateTime[0];
                $products[$key]['store_pickup_time'] = $dateTime[1];
                $products[$key]['apply_for_all'] = $applyForAll;

                if (Configuration::get('MP_STORE_PICK_UP_PAYMENT')) {
                    $storeDetails =  MarketplaceStoreLocator::getStoreById($storePickupDetails[0]['id_store'], true);
                    $paymentOptions = MpStorePay::getPaymentOptionDetails(
                        json_decode($storeDetails['payment_option']),
                        $this->context->language->id
                    );
                    $products[$key]['paymentOptions'] = $paymentOptions;
                }
            }
        }
        $carrierName = implode(',', $carrierName);
        $this->context->smarty->assign(
            array(
                'imagePath' => _MODULE_DIR_.'mpstorelocator/views/img/payment_logo/',
                'products' => $products,
                'MP_GEOLOCATION_API_KEY' => Configuration::get('MP_GEOLOCATION_API_KEY'),
                'MP_STORE_PICKUP_DATE' => Configuration::get('MP_STORE_PICKUP_DATE'),
                'MP_STORE_TIME' => Configuration::get('MP_STORE_TIME'),
                'otherPickupProducts' => $otherPickupProducts,
                'carrierName' => $carrierName
            )
        );
        die(
        json_encode(
            array(
                'html' => $this->context->smarty->fetch(
                    'module:mpstorelocator/views/templates/hook/product_store.tpl'
                )
            )
        )
        );
    }

    public function displayAjaxGetStoreDetailsByIdProduct()
    {
        $idProduct = Tools::getValue('id_product');
        if ($idProduct) {
            $productStores = MarketplaceStoreProduct::getAvailableProductStore($idProduct, true);
            $stores = array();
            if ($productStores) {
                foreach ($productStores as $pStore) {
                    $stores[] = MarketplaceStoreLocator::getStoreById($pStore['id_store'], true);
                }
            }
            $response = array();
            if ($idStore = Tools::getValue('id_store')) {
                $disabledDates = MpStorePickUpProduct::getDisabledDates($idStore);
                $disabledDates = array_column($disabledDates, 'pickup_datetime');
                $disabledDates = array_filter($disabledDates, 'strlen');
                if ($disabledDates) {
                    $response['disabledDates'] = $disabledDates;
                } else {
                    $response['disabledDates'] = array();
                }
            } else {
                $response['disabledDates'] = array();
            }

            if (isset($stores) && $stores) {
                $allstore = MarketplaceStoreLocator::getMoreStoreDetails($stores);
                if ($allstore) {
                    $response['stores'] = $allstore;
                    $response['hasError'] = false;
                }
            }
            die(
            json_encode($response)
            );
        }
        die(json_encode(array('hasError' => true))); //ajax close
    }

    public function displayAjaxGetStoreProductDetails()
    {
        $idStore = (int)Tools::getValue('id_store');
        if ($idStore) {
            $storeDetails = new MPStoreProductAvailable();
            $carrierList = $storeDetails->getCarrierByIdProduct($this->context->cart->getProducts());
            $applyForAll = 1;
            $products = $this->context->cart->getProducts();
            $idProducts = array_column($products, 'id_product');
            $storeProducts = MarketplaceStoreProduct::checkStoreProducts($idStore);
            $count = 0;
            if ($storeProducts) {
                $storeProducts = array_column($storeProducts, 'id_product');
                foreach ($idProducts as $idProduct) {
                    $availableForPickUp = MPStoreProductAvailable::availableForStorePickup($idProduct);
                    if (!$availableForPickUp
                        || !in_array($idProduct, $storeProducts)
                        || !in_array(Configuration::get('MP_STORE_ID_CARRIER'), $carrierList[$idProduct])
                    ) {
                        $applyForAll = 0;
                    } else {
                        $count += 1;
                    }
                }
            }
            if ($count == 1) {
                $applyForAll = 0;
            }
            $response = array();
            $response['applyForAll'] = $applyForAll;
            if (Configuration::get('MP_STORE_PICK_UP_PAYMENT')) {
                $storeDetails =  json_decode(MarketplaceStoreLocator::getStoreById($idStore)['payment_option'], true);
                if ($storeDetails) {
                    $paymentOptions = MpStorePay::getPaymentOptionDetails(
                        $storeDetails,
                        $this->context->language->id
                    );
                    $this->context->smarty->assign(
                        array(
                            'paymentOptions' => $paymentOptions,
                            'imagePath' => _MODULE_DIR_.'mpstorelocator/views/img/payment_logo/'
                        )
                    );
                    $response['html'] = $this->context->smarty->fetch(
                        _PS_MODULE_DIR_.'mpstorelocator/views/templates/front/partials/store_payment_options.tpl'
                    );
                } else {
                    $response['html'] = '';
                }
            } else {
                $response['html'] = '';
            }
            $disabledDates = MpStorePickUpProduct::getDisabledDates($idStore);
            $disabledDates = array_column($disabledDates, 'pickup_datetime');
            $disabledDates = array_filter($disabledDates, 'strlen');
            if ($disabledDates) {
                $response['disabledDates'] = $disabledDates;
            } else {
                $response['disabledDates'] = array();
            }
            die(
            json_encode($response)
            );
        }
        die(json_encode(array('hasError' => true))); //ajax close
    }

    public function saveStoreDetails($idProduct, $idProductAttr)
    {
        $storePickUpId = MpStorePickUpProduct::getStorePickUpId(
            $this->context->cart->id
        );
        $storePickUpProductId = MpStorePickUpProduct::getStorePickUpProductId(
            $this->context->cart->id,
            $idProduct,
            $idProductAttr
        );
        if ($storePickUpId) {
            $objStoreCart = new MpStorePickUp(
                (int)$storePickUpId
            );
        } else {
            $objStoreCart = new MpStorePickUp();
        }
        if ($storePickUpProductId) {
            $objStoreProductCart = new MpStorePickUpProduct(
                (int)$storePickUpProductId
            );
        } else {
            $objStoreProductCart = new MpStorePickUpProduct();
        }

        $idStore = Tools::getValue('wk_id_store');
        $objStoreCart->id_cart = (int)$this->context->cart->id;
        $objStoreCart->id_order = 0;
        $objStoreCart->save();

        if ($objStoreCart->id) {
            $objStoreProductCart->id_store_pickup = (int)$objStoreCart->id;
            $objStoreProductCart->id_store = (int)$idStore;
            $objStoreProductCart->id_product = (int)$idProduct;
            $objStoreProductCart->id_product_attribute = (int)$idProductAttr;
            $idSeller = (int)Tools::getValue('wk_id_seller');

            $storeConfiguration = MpStoreConfiguration::getStoreConfiguration($idSeller);
            if ($storeConfiguration && $storeConfiguration['enable_date']) {
                $wkPickUpDate = Tools::getValue('wk_pickup_date');
                $wkPickUpTime = Tools::getValue('wk_pickup_time');
                $dateTime = $wkPickUpDate;

                if ($storeConfiguration['enable_time']
                    && $wkPickUpTime
                ) {
                    $dateTime .= ' ' . $wkPickUpTime;
                }
                $objStoreProductCart->pickup_date = pSQl($dateTime);
            }
            $objStoreProductCart->save();
        }
    }

    public function displayAjaxSaveStoreDetails()
    {
        if (empty(Tools::getValue('apply_for_all'))) {
            $idProduct = Tools::getValue('id_product');
            $stores = MarketplaceStoreProduct::getAvailableProductStore($idProduct, true);
            if ($stores) {
                $idProductAttr = Tools::getValue('id_product_attr');
                $this->saveStoreDetails($idProduct, $idProductAttr);
                die('1');
            }
        } else {
            $storeDetails = new MpStoreProductAvailable();
            $carrierList = $storeDetails->getCarrierByIdProduct($this->context->cart->getProducts());
            $products = $this->context->cart->getProducts();
            $idProducts = array_column($products, 'id_product');
            foreach ($products as $product) {
                $stores = MarketplaceStoreProduct::getAvailableProductStore($product['id_product'], true);
                $availableForPickUp = MpStoreProductAvailable::availableForStorePickup($product['id_product']);
                if ($stores
                    && $availableForPickUp
                    && in_array(Configuration::get('MP_STORE_ID_CARRIER'), $carrierList[$product['id_product']])
                ) {
                    $idProduct = $product['id_product'];
                    $idProductAttr = $product['id_product_attribute'];
                    $this->saveStoreDetails($idProduct, $idProductAttr);
                }
            }
            die('1');
        }
        die('0');
    }

    public function displayAjaxCheckStoreDetails()
    {
        $products = $this->context->cart->getProducts();
        $storeProducts = MpStorePickUpProduct::getProductByCartId($this->context->cart->id);
        if (empty($storeProducts)) {
            die(
            json_encode(
                array(
                    'hasError' => 1,
                    'error' => $this->l(
                        'Please select the store on products'
                    ),
                )
            )
            );
        }
        $idProducts = array_column($storeProducts, 'id_product');
        $idProductAttributes = array_column($storeProducts, 'id_product_attribute');
        $errors = array();
        foreach ($products as $product) {
            $objProduct = new Product($product['id_product']);
            $carriers = $objProduct->getCarriers();
            $carriers = array_column($carriers, 'id_carrier');
            $availableForPickUp = MpStoreProductAvailable::availableForStorePickup($product['id_product']);
            $storeDetails = new MPStoreProductAvailable();
            $carrierList = $storeDetails->getCarrierByIdProduct($this->context->cart->getProducts());
            if (in_array(Configuration::get('MP_STORE_ID_CARRIER'), $carrierList[$product['id_product']])
                && (!$availableForPickUp || !in_array($product['id_product'], $idProducts)
                    || !in_array($product['id_product_attribute'], $idProductAttributes)
                    || empty(MarketplaceStoreProduct::getAvailableProductStore($product['id_product'], true))
                )
            ) {
                $errors[] = $this->module->l(
                    'Please select the store on "'.
                    Product::getProductName(
                        $product['id_product'],
                        $product['id_product_attribute']
                    ).'"'
                );
            }
        }
        if ($errors) {
            die(
            json_encode(
                array(
                    'hasError' => 1,
                    'errors' => $errors
                )
            )
            );
        }
        die(
        json_encode(
            array(
                'hasError' => 0
            )
        )
        );
    }

    public function displayAjaxCheckAllProducts()
    {
        $products = $this->context->cart->getProducts();
        $idProducts = array_column($products, 'id_product');
        $productAttributes = array_column($products, 'id_product_attribute');
        $storeProducts = MpStorePickUpProduct::getProductByCartId($this->context->cart->id);
        foreach ($storeProducts as $storeProduct) {
            $key = array_search($storeProduct['id_product'], $idProducts);
            if ($key == -1 || $storeProduct['id_product_attribute'] != $productAttributes[$key]) {
                MpStorePickUpProduct::deleteProductsFromCart(
                    $this->context->cart->id,
                    $storeProduct['id_product'],
                    $storeProduct['id_product_attribute']
                );
            }
        }
        $this->displayAjaxCheckStoreDetails();
    }
}
