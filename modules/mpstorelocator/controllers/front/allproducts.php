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
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
class mpstorelocatorallproductsModuleFrontController extends ModuleFrontController
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
	public function getProductSearch($params = []){
		
	}
	public function getProductActivities($id_seller = 0)
    {	
        $ids = [];
		$sql = 'SELECT id_product FROM `'._DB_PREFIX_.'wk_mp_booking_product_info`';
		$sql .= ' WHERE `id_product` > 0';
		$sql .= ' AND `active` = 1';
		if( $id_seller ){
			$sql .= ' AND `id_seller` = '.(int) $id_seller;
		}
		$productActivities = Db::getInstance()->executeS($sql);

        if ($productActivities && !empty($productActivities)) {
			foreach( $productActivities as $productActivity ){
				$ids[] = $productActivity['id_product'];
			}
            return $ids;
        }

        return false;
    }
	
    public function initContent()
    {
        parent::initContent();
		
        $idLang = $this->context->cookie->id_lang;
        $idProduct = Tools::getValue('id_product');
        $idStore = Tools::getValue('id_store');
        $stores = array();
        if (empty($idProduct)
            && empty($idStore)
            && !Tools::getValue('stores')
            && !Tools::getValue('ajax')
        ) {
            //Tools::redirect($this->context->link->getPageLink('nopagefound'));
        }

        $this->context->smarty->assign(
            array(
                'displayContactDetails' => Configuration::get('MP_STORE_CONTACT_DETAILS'),
                'displayFax' => Configuration::get('MP_STORE_DISPLAY_FAX'),
                'displayEmail' => Configuration::get('MP_STORE_DISPLAY_EMAIL'),
                'displayStoreTiming' => Configuration::get('MP_DISPLAY_STORE_TIMING'),
                'displayStorePage' => Configuration::get('MP_STORE_STORE_PAGE'),
				'modules_dir' => _MODULE_DIR_,
            )
        );
		/*
		$stores = MarketplaceStoreLocator::getAllStore(true);
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
        $stores = MarketplaceStoreLocator::getMoreStoreDetails($stores);

		if (count($stores)) {
			foreach( $stores as $key => $store ){
				$seller 			= WkMpSeller::getSeller($store['id_seller'],$this->context->language->id);

				$objReview = new WkMpSellerReview();
				$reviews = $objReview->getReviewsByConfiguration($store['id_seller']);
				$average_ratings 				= $reviews['avg_rating'];
				$total_review 					= ( $reviews ) ? count( $reviews['reviews'] ) : 0;
				$stores[$key]['average_ratings'] 	= ($average_ratings) ? $average_ratings : 0;
				$stores[$key]['left_ratings'] 		= 5 - $average_ratings;
				$stores[$key]['total_review'] 		= $total_review;
				$stores[$key]['seller'] 			= $seller;
					
			}
			$this->context->smarty->assign('store_locations', $stores);
		}
		
		$this->context->smarty->assign('store_locations', $stores);
		*/
		
		/* new */
		$module = APPageBuilder::getInstance();
		$form_atts = [];
		$form_atts['nb_products'] = 9;
        $n = (int)isset($form_atts['nb_products']) ? $form_atts['nb_products'] : 9;
        $p = (int)Tools::getIsset('p') ? Tools::getValue('p') : '1';
        $form_atts['page_number'] = $p;
        $form_atts['get_total'] = true;
        $form_atts['value_by_categories'] = true;
        $form_atts['categorybox'] = 13;
		
		
		$sellerProductIds = $this->getProductActivities();
	
		
		if( $sellerProductIds ){
			$form_atts['value_by_product_id'] = 1;
			$form_atts['value_by_categories'] = 0;
			$form_atts['product_id'] = implode(',',$sellerProductIds);
		}
		

        /*$total = $module->getProductsFont($form_atts);*/
		$total = count($sellerProductIds);
		$assign['formAtts']['use_showmore'] = 1;
        $form_atts['total_page'] = $total_page = ceil(($total / $n));

		$products = array();
        if ($p <= $total_page) {
            $form_atts['get_total'] = false;
            $products = $module->getProductsFont($form_atts);
            $products = $this->loadProductDetail($products, $module);
        }
        
        if(isset($assign['formAtts']['use_showmore']) && $assign['formAtts']['use_showmore']){
            if($p < $total_page){
                $assign['formAtts']['use_showmore'] = 1;    # show_more
            }else{
                $assign['formAtts']['use_showmore'] = 0;
            }
        }
		
		foreach( $products as $key => $product ){
			$objBookingProductInfo = new WkMpBookingProductInformation();
			$bookingProductInfo = $objBookingProductInfo->getBookingProductInfoByIdProduct( $product['id_product'] );
			
			if( !$bookingProductInfo ){
				unset( $products[$key] );
				continue;
			}

			$products[$key]['latitude'] = ( @$bookingProductInfo['latitude'] ) ? @$bookingProductInfo['latitude'] : $product['mp_seller_info']['store_locator']['latitude'];
			$products[$key]['longitude'] = ( @$bookingProductInfo['longitude'] ) ? @$bookingProductInfo['longitude'] : $product['mp_seller_info']['store_locator']['longitude'];
			$products[$key]['zip_code'] = (@$bookingProductInfo['activity_postcode']) ? @$bookingProductInfo['activity_postcode'] : $product['mp_seller_info']['store_locator']['zip_code'];
			$products[$key]['map_address_text'] = (@$bookingProductInfo['activity_addr']) ? @$bookingProductInfo['activity_addr'] : $product['mp_seller_info']['store_locator']['map_address_text'];
			$products[$key]['map_address'] = (@$bookingProductInfo['activity_addr']) ? @$bookingProductInfo['activity_addr'] : $product['mp_seller_info']['store_locator']['map_address_text'];
			$products[$key]['storeLink'] = $product['url'];
			$products[$key]['address1'] = (@$bookingProductInfo['activity_addr']) ? @$bookingProductInfo['activity_addr'] : $product['mp_seller_info']['store_locator']['address1'];
			$products[$key]['address2'] = $product['mp_seller_info']['store_locator']['address2'];
			$products[$key]['city_name'] = $product['mp_seller_info']['store_locator']['city_name'];
			$products[$key]['state_name'] = '';
			$products[$key]['country_name'] = '';
			$products[$key]['current_hours'] = '';
			$products[$key]['phone'] = $product['mp_seller_info']['store_locator']['phone'];
			$products[$key]['wk_store_distance'] = '';
			$products[$key]['id'] = '';


		}
		


        $assign['scolumn'] = 3;
        $assign['products'] = $products;
        $assign['p'] = $p + 1;
		$assign['productClassWidget'] = '';
		$assign['apAjax'] = '';
		
		
		$assign['formAtts']['profile'] = 'plist2837126972';
        if (isset($assign['formAtts']['profile']) && $assign['formAtts']['profile'] != 'default' && file_exists(apPageHelper::getConfigDir('theme_profiles').$assign['formAtts']['profile'] . '.tpl')) {
            $assign['product_item_path'] = apPageHelper::getConfigDir('theme_profiles') . $assign['formAtts']['profile'].'.tpl';
        } else {
            // Default load file in theme
            $assign['product_item_path'] = 'catalog/_partials/miniatures/product.tpl';
        }

		foreach( $assign as $a_key => $a_val ){
			$this->context->smarty->assign($a_key, $a_val);
		}

		$this->context->smarty->assign(
			array(
				'psModuleDir' => _MODULE_DIR_,
				'enableSearchProduct' => Configuration::get('MP_STORE_SEARCH_BY_PRODUCT')
			)
		);
		
		if ( Tools::getValue('is_ajax') ) {
			$this->context->smarty->assign('products', $products);
			die(
				json_encode(
					array(
						'html' => $this->context->smarty->fetch(
							_PS_MODULE_DIR_.'mpstorelocator/views/templates/front/filtered_product.tpl'
						),
						'products' 		=> $products,
						'stores' 		=> $products,
						'hasError' 		=> false,
						'total_page' 	=> $total_page,
						'p' 			=> $p,
					)
				)
			);
		}

		$this->setTemplate('module:mpstorelocator/views/templates/front/allproducts.tpl');


        Media::addJsDef(
            array(
                'storeLink' => $this->context->link->getModuleLink(
                    'mpstorelocator',
                    'storedetails'
                ),
                'storeLocationsJson' => Tools::jsonEncode($products),
                'storeLocate' => 1,
                'no_store_found' => $this->module->l('No Store Found'),
                'storeTiming' => $this->module->l('Store Timing'),
                'contactDetails' => $this->module->l('Contact'),
                'getDirections' => $this->module->l('Get directions'),
                'emailMsg' => $this->module->l('Email'),
                'closedMsg' => $this->module->l('Closed'),
                'ajaxurlStoreByKey' => $this->context->link->getModuleLink('mpstorelocator', 'allproducts'),
            )
        );
    }
	
	public function loadProductDetail($products, $module)
    {
        # 1.7
        $assembler = new ProductAssembler(Context::getContext());
        $presenterFactory = new ProductPresenterFactory(Context::getContext());
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                Context::getContext()->link
            ),
            Context::getContext()->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            Context::getContext()->getTranslator()
        );
        
        $products_for_template = array();
        foreach ($products as $rawProduct)
        {
            $product_temp = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                Context::getContext()->language
            );
            
            # FIX 1.7.5.0
            if(is_object($product_temp) && method_exists($product_temp, 'jsonSerialize'))
            {
                $product_temp = $product_temp->jsonSerialize();
            }

			
            # ADD SHORTCODE TO PRODUCT DESCRIPTION AND PRODUCT SHORT DESCRIPTION
			$sellerProduct = WkMpSellerProduct::getSellerProductByPsIdProduct($rawProduct['id_product']);
			$seller['shop_image'] 		= _MODULE_DIR_.'marketplace/views/img/shop_img/defaultshopimage.jpg';
			$seller['profile_image'] 	= _MODULE_DIR_.'marketplace/views/img/shop_img/defaultshopimage.jpg';
			$seller['shop_name']  = '';
			$seller['city_name']  = '';
			if( $sellerProduct ){
				$mpIdSeller = $sellerProduct['id_seller'];
				if ($seller = WkMpSeller::getSeller($mpIdSeller, Context::getContext()->language->id)) {
					$mpShopImage = $seller['shop_image'];
					if ($mpShopImage && file_exists(_PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$mpShopImage)) {
						$seller['shop_image'] = _MODULE_DIR_.'marketplace/views/img/shop_img/'.$mpShopImage;
					} else {
						$seller['shop_image'] = _MODULE_DIR_.'marketplace/views/img/shop_img/defaultshopimage.jpg';
					}
					$mpProfileImage = $seller['profile_image'];
					if ($mpShopImage && file_exists(_PS_MODULE_DIR_.'marketplace/views/img/seller_img/'.$mpProfileImage)) {
						$seller['profile_image'] = _MODULE_DIR_.'marketplace/views/img/seller_img/'.$mpProfileImage;
					} else {
						$seller['profile_image'] = _MODULE_DIR_.'marketplace/views/img/seller_img/defaultshopimage.jpg';
					}
					$objReview = new WkMpSellerReview();
					$reviews = $objReview->getReviewsByConfiguration($sellerProduct['id_seller']);
					$average_ratings 				= $reviews['avg_rating'];
					$total_review 					= ( $reviews ) ? count( $reviews['reviews'] ) : 0;
					$store_locator 					= MarketplaceStoreLocator::getSellerStore($sellerProduct['id_seller']);
					$seller['store_locator'] 		= ($store_locator) ? $store_locator[0] : '';
					$seller['city_name'] 			= ($store_locator) ? $store_locator[0]['city_name'] : '';
					$seller['average_ratings'] 		= ($average_ratings) ? $average_ratings : 0;
					$seller['left_ratings'] 		= 5 - $average_ratings;
					$seller['total_review'] 		= $total_review;
				}
			}
			$product_temp['mp_seller_info'] = $seller;
            $product_temp['description'] = $module->buildShortCode($product_temp['description']);
            $product_temp['description_short'] = $module->buildShortCode($product_temp['description_short']);
            $product_temp['date_add'] = date('H:s',strtotime($product_temp['date_add']));
			$product_temp['img_home_default'] = $product_temp['cover']['bySize']['home_default'];
            $product_temp['cover_image'] = ($product_temp['cover']['bySize']['booking_home_default']['url']) ? $product_temp['cover']['bySize']['booking_home_default']['url'] : $product_temp['cover']['large']['url'];
			
			$objBookingProductInfo 				= new WkMpBookingProductInformation();
			$bookingProductInfo 				= $objBookingProductInfo->getBookingProductInfoByIdProduct( $product_temp['id_product'] );
			$product_temp['bookingProductInfo'] = $bookingProductInfo;
			$product_temp['is_booking'] 		= ( $bookingProductInfo ) ? true : false;
			$product_temp['date_add'] 			= $bookingProductInfo['activity_period'];
			
			$products_for_template[] = $product_temp;
        }
        return $products_for_template;
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
            "https://maps.googleapis.com/maps/api/js?key=$MP_GEOLOCATION_API_KEY&libraries=places&language=$language->iso_code&region=$country->iso_code&v=3.5",
            [
              'server' => 'remote'
            ]
        );

        Media::addJsDef(
            array(
                'storeLogoImgPath' => _MODULE_DIR_.'marketplace/views/img/seller_img/',
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
        $this->registerJavascript('storedetails', 'modules/'.$this->module->name.'/views/js/front/allproducts.js');
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
        $module = APPageBuilder::getInstance();
		if (Tools::getIsset('id_store')) {
            $this->getStoreDetails();
        } else {
            $key = Tools::getValue('search_key');
            $idProduct = Tools::getValue('id_product');
            $currentLocation = Tools::getValue('current_location');
            $radius = Tools::getValue('radius');
			if( !$radius ){
				$radius = 25;
			}
            $category_id = Tools::getValue('category_id');
            $date_stay 	= Tools::getValue('date_stay');
            $date_end 	= Tools::getValue('date_end');
			
			$this->context->smarty->assign('modules_dir', _MODULE_DIR_);
			
			$form_atts = [];
			
			if( $category_id ){
				$form_atts['value_by_categories'] = 1;
				$form_atts['categorybox'] = $category_id;
			}else{
				$sellerProductIds = $this->getProductActivities();
				if( $sellerProductIds ){
					$form_atts['value_by_product_id'] 	= 1;
					$form_atts['value_by_categories'] 	= 0;
					$form_atts['nb_products'] 			= 1000;
					$form_atts['product_id'] = implode(',',$sellerProductIds);
				}
			}


			/*$total = $module->getProductsFont($form_atts);*/
			$total = 100;
			$total = (is_array($total) && count($total) > 0) ? count($total) : 0;
			$form_atts['total_page'] = $total_page = ceil(($total / $n));
			
			$products = array();
			if ($p <= $total_page) {
				//$form_atts['get_total'] = false;
				$products = $module->getProductsFont($form_atts);
				$products = $this->loadProductDetail($products, $module);
			}
			
			
			
			if(isset($assign['formAtts']['use_showmore']) && $assign['formAtts']['use_showmore']){
				if($p < $total_page){
					$assign['formAtts']['use_showmore'] = 1;    # show_more
				}else{
					$assign['formAtts']['use_showmore'] = 0;
				}
			}
			$distanceStore = array();
			
			if( $date_stay || $date_end ){
				$date_stay 	= ( $date_stay ) ? date( 'Y-m-d' , strtotime( $date_stay ) )  : '';
				$date_end 	= ( $date_end ) ? date( 'Y-m-d' , strtotime( $date_end ) ) : '';
				$sql = 'SELECT id_product FROM `'._DB_PREFIX_.'wk_mp_booking_product_info`
					WHERE active = 1 ';
					
				if( $date_stay ){
					$sql .= ' AND `date_upd` >= \''.pSql($date_stay).'\'';
				}
				if( $date_end ){
					$sql .= ' AND `date_upd` <= \''.pSql($date_end).'\'';
				}
				
				$sql .= ' GROUP BY id_product';
		
	

				$time_ids = Db::getInstance()->executeS( $sql );
				$new_time_ids = [];
				if( count( $time_ids ) ){
					foreach( $time_ids as $time_id ){
						$new_time_ids[] = $time_id['id_product'];
					} 
				}
			}
			
	
			
			foreach( $products as $key => $product ){
				if( $date_stay || $date_end ){
					if( !in_array( $product['id_product'], $new_time_ids ) ){
						unset( $products[$key] );
						continue;
					}
				}
				$objBookingProductInfo = new WkMpBookingProductInformation();
				$bookingProductInfo = $objBookingProductInfo->getBookingProductInfoByIdProduct( $product['id_product'] );

				$products[$key]['latitude'] = ( @$bookingProductInfo['latitude'] ) ? @$bookingProductInfo['latitude'] : $product['mp_seller_info']['store_locator']['latitude'];
				$products[$key]['longitude'] = ( @$bookingProductInfo['longitude'] ) ? @$bookingProductInfo['longitude'] : $product['mp_seller_info']['store_locator']['longitude'];
				$products[$key]['zip_code'] = (@$bookingProductInfo['activity_postcode']) ? @$bookingProductInfo['activity_postcode'] : $product['mp_seller_info']['store_locator']['zip_code'];
				$products[$key]['map_address_text'] = (@$bookingProductInfo['activity_addr']) ? @$bookingProductInfo['activity_addr'] : $product['mp_seller_info']['store_locator']['map_address_text'];
				$products[$key]['map_address'] = (@$bookingProductInfo['activity_addr']) ? @$bookingProductInfo['activity_addr'] : $product['mp_seller_info']['store_locator']['map_address_text'];
				$products[$key]['latitude'] = ( @$bookingProductInfo['latitude'] ) ? @$bookingProductInfo['latitude'] : $product['mp_seller_info']['store_locator']['latitude'];
				$products[$key]['longitude'] = ( @$bookingProductInfo['longitude'] ) ? @$bookingProductInfo['longitude'] : $product['mp_seller_info']['store_locator']['longitude'];
				$products[$key]['zip_code'] = (@$bookingProductInfo['activity_postcode']) ? @$bookingProductInfo['activity_postcode'] : $product['mp_seller_info']['store_locator']['zip_code'];
				$products[$key]['map_address_text'] = (@$bookingProductInfo['activity_addr']) ? @$bookingProductInfo['activity_addr'] : $product['mp_seller_info']['store_locator']['map_address_text'];
				$products[$key]['map_address'] = (@$bookingProductInfo['activity_addr']) ? @$bookingProductInfo['activity_addr'] : $product['mp_seller_info']['store_locator']['map_address_text'];
				$products[$key]['storeLink'] = $product['url'];
				$products[$key]['address1'] = (@$bookingProductInfo['activity_addr']) ? @$bookingProductInfo['activity_addr'] : $product['mp_seller_info']['store_locator']['address1'];
				$products[$key]['address2'] = $product['mp_seller_info']['store_locator']['address2'];
				$products[$key]['city_name'] = $product['mp_seller_info']['store_locator']['city_name'];
				$products[$key]['state_name'] = '';
				$products[$key]['country_name'] = '';
				$products[$key]['current_hours'] = '';
				$products[$key]['phone'] = $product['mp_seller_info']['store_locator']['phone'];
				$products[$key]['id'] = '';

				$distanceStore[] = $products[$key];
			}
			$products = $distanceStore;
			
			
			//currentLocation
			if (!empty($currentLocation)) {
				$distanceStore = [];
				foreach( $products as $key => $product ){
					$distance = $this->distance($currentLocation, $products[$key]['latitude'], $products[$key]['longitude']);
					if (empty($radius)) {
                        if (Configuration::get('MP_STORE_DISTANCE_UNIT') == "METRIC") {
                            $distance = $distance . 'Km';
                        } else {
                            $distance = $distance . 'Miles';
                        }
                        $products[$key]['distance'] = $distance;
						$distanceStore[] = $products[$key];
                    } else {
                        if ($distance <= $radius) {
                            if (Configuration::get('MP_STORE_DISTANCE_UNIT') == "METRIC") {
                                $distance = $distance . 'Km';
                            } else {
                                $distance = $distance . 'Miles';
                            }
                            $products[$key]['distance'] = $distance;
							$distanceStore[] = $products[$key];
                        }
                    }
				}
				$this->context->smarty->assign('currentLocation', $currentLocation);
				$products = $distanceStore;
			}
			
			//pp_theme
			if (!empty($pp_theme)) {
				$distanceStore = [];
				foreach( $products as $key => $product ){
					if( @$products[$key]['custom_fields']['pp_theme'] && $products[$key]['custom_fields']['pp_theme'] == $pp_theme ){
						$distanceStore[] = $products[$key];
					}
				}
				$products = $distanceStore;
			}

			
			
			if ($products) {
				$this->context->smarty->assign('products', $products);
				die(
					json_encode(
						array(
							'html' => $this->context->smarty->fetch(
								_PS_MODULE_DIR_.'mpstorelocator/views/templates/front/filtered_product.tpl'
							),
							'products' => $products,
							'stores' => $products,
							'hasError' => false
						)
					)
				);
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
            $radius = Tools::getValue('radius');
			if( !$radius ){
				$radius = 25;
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
