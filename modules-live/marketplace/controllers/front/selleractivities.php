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

class MarketplaceSellerActivitiesModuleFrontController extends ModuleFrontController
{
    const ACTIVITY_DEFAULT_CATEGORY = 13;
 
    public function initContent()
    {
        parent::initContent();

     
		if (!isset($this->context->customer->id) || !$this->context->customer->isLogged()) {
            Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('marketplace', 'sellerpurchases')));
        }

        $seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
        if (!$seller || !$seller['active']) {
            Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
        }
        
        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
            $idLang = $this->context->language->id;
        } else {
            if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') {
                $idLang = Configuration::get('PS_LANG_DEFAULT');
            } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') {
                $idLang = $seller['default_lang'];
            }
        }

        $this->defineJSVars();
        $this->context->smarty->assign(array(
            'logic' => 4,
            'is_seller' => $seller['active'],
            'id_seller' => $seller['id_seller'],
            'mp_seller_info' => $seller,
            'products_status' => Configuration::get('WK_MP_SELLER_PRODUCTS_SETTINGS'),
            'imageediturl' => $this->context->link->getModuleLink('marketplace', 'productimageedit'),
            'static_token' => Tools::getToken(false),
        ));

        if (Tools::isSubmit('updated') || Tools::isSubmit('added')) {
            $this->activityProcess($seller);
        } elseif (Tools::getValue('action') == 'edit' && ($idMpProduct = (int)Tools::getValue('id_mp_product'))) {
            $this->editProduct($seller);
        } elseif (Tools::getValue('action') == 'add') {
            $this->addProduct($seller);
        } elseif (Tools::getValue('action') == 'uploadimage') {
            $this->ajaxProcessUploadimage();
        } elseif (Tools::getValue('action') == 'deleteimage') {
            $this->ajaxProcessDeleteimage();
        } elseif (Tools::getValue('action') == 'remove' && ($idMpProduct = (int)Tools::getValue('id_mp_product'))) {
            $this->deleteProduct($idMpProduct);
        } elseif (Tools::getValue('mp_product_status')) {
            $this->changeProductStatus();
        } else {
            $sellerActivities = WkMpSellerProduct::getSellerActivities($seller['id_seller']);

            if ($sellerActivities) {
                $sellerActivities = $this->getProductDetails($sellerActivities, $idLang);
                foreach ($sellerActivities as &$product) {
                    $product['price'] = Tools::displayPrice($product['price']);
                }
                unset($product);
            }

            $this->context->smarty->assign(array(
                'product_lists' => $sellerActivities,
            ));

            $this->setTemplate('module:marketplace/views/templates/front/product/selleractivities.tpl');
        }
    }
    
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => 'Journal de bord',
            'url' => '',
        );

        $breadcrumb['links'][] = array(
            'title' => 'Mes activités',
            'url' => '',
        );

        return $breadcrumb;
    }

    public function changeProductStatus()
    {
        $idProduct = Tools::getValue('id_product');
        $objMpProduct = new WkMpSellerProduct();
        $sellerProduct = WkMpSellerProduct::getSellerProductByPsIdProduct($idProduct);
        if ($sellerProduct) {
            $mpIdProduct = $sellerProduct['id_mp_product'];
            Hook::exec('actionBeforeToggleMPProductStatus', array('id_mp_product' => $mpIdProduct));
            if (!count($this->errors)) {
                $objMpProduct = new WkMpSellerProduct($mpIdProduct);
                if ($objMpProduct->active) {
                    $objMpProduct->active = 0;
                    $objMpProduct->save();
                    $product = new Product($idProduct);
                    $product->active = 0;
                    $product->save();

                    //Update id_image as mp_image_id when product is going to deactivate
                    WkMpProductAttributeImage::setCombinationImagesAsMp($mpIdProduct);
                } else {
                    $objMpProduct->active = 1;
                    $objMpProduct->save();

                    $objMpProduct->updateSellerProductToPs($mpIdProduct, 1);
                    $objMpProductAttribute = new WkMpProductAttribute();
                    $objMpProductAttribute->updateMpProductCombinationToPs($mpIdProduct, $idProduct);

                    Hook::exec('actionToogleMPProductActive', array('id_mp_product' => $mpIdProduct, 'active' => $objMpProduct->active));
                }

                Hook::exec('actionAfterToggleMPProductStatus', array('id_product' => $idProduct, 'active' => $objMpProduct->active));
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'selleractivities', array('status_updated' => 1)));
            }
        }
    }

    public function deleteProduct($mpIdProduct)
    {
        $mpDelete = false;
        $objMpProduct = new WkMpSellerProduct($mpIdProduct);
        if ($objMpProduct->delete()) {
            $mpDelete = true;
        }

        if ($mpDelete) {
            Tools::redirect($this->context->link->getModuleLink('marketplace', 'selleractivities', array('deleted' => 1)));
        }
    }

    public function editProduct($seller)
    {
        if ($idMpProduct = (int)Tools::getValue('id_mp_product')) {
            $id_lang = (int)$this->context->language->id;
            $mpSellerProduct = new WkMpSellerProduct($idMpProduct);
            $product = (array) $mpSellerProduct;
            $objBookingProductInfo = new WkMpBookingProductInformation();
            $bookingProductInfo = $objBookingProductInfo->getBookingProductInfoByMpIdProduct($idMpProduct);
            $product_cats = WkMpSellerProductCategory::getSellerCategories($idMpProduct, [self::ACTIVITY_DEFAULT_CATEGORY]);

            //Assign and display product active/inactive images
            WkMpSellerProductImage::getProductImageDetails($idMpProduct);
			
	
            if ($bookingProductInfo) {
                $product['booking_type'] = $bookingProductInfo['booking_type'];
                $this->context->smarty->assign([
                    'idBookingProductInformation' => $bookingProductInfo['id'],
                    'booking_info' => $bookingProductInfo,
                    'activity_curious_opts' => ($bookingProductInfo['activity_curious'] ? explode('#~#', $bookingProductInfo['activity_curious']) : []),
                ]);
            } else {
                $this->context->smarty->assign([
                    'idBookingProductInformation' => 0,
                    'booking_info' => null,
                    'activity_curious_opts' => [],
                ]);
            }

            $this->defineJSVars();
            $this->context->smarty->assign(array(
                'id_mp_product' => $idMpProduct,
                'product_info' => $product,
                'id' => $idMpProduct,
                'defaultCurrencySign' => $this->context->currency->sign,
                'bookingProductTimeSlots' => null,
                'action' => 'update',
                'submit_url' => $this->context->link->getModuleLink('marketplace', 'selleractivities', ['updated' => 1]),
                'mpb_categories' => WkMpSellerProductCategory::getSimpleCategories((int)$this->context->language->id,  MpStoreLocator::getSearchEngineCategoriesIDs()),
                'MP_GEOLOCATION_API_KEY' => Configuration::get('MP_GEOLOCATION_API_KEY'),
                'product_cats' => ($product_cats ? explode(',', $product_cats) : []),
                'id_lang' => $id_lang,
                'activity_periods' => $this->getActivityPeriods(),
                'activity_participants' => $this->getActivityParticipants(),
                'activity_curious' => $this->getActivityCurious(),
            ));

            $this->setTemplate('module:marketplace/views/templates/front/product/activityform.tpl');
        }
    }

    public function addProduct($seller)
    {
        $id_lang = (int)$this->context->language->id;

        $this->defineJSVars();
        $this->context->smarty->assign(array(
            'idBookingProductInformation' => 0,
            'booking_info' => null,
            'activity_curious_opts' => [],
            'id_mp_product' => 0,
            'product_info' => null,
            'id' => 0,
            'defaultCurrencySign' => $this->context->currency->sign,
            'bookingProductTimeSlots' => null,
            'action' => 'add',
            'submit_url' => $this->context->link->getModuleLink('marketplace', 'selleractivities', ['added' => 1]),
            'mpb_categories' => WkMpSellerProductCategory::getSimpleCategories((int)$this->context->language->id,  MpStoreLocator::getSearchEngineCategoriesIDs()),
            'MP_GEOLOCATION_API_KEY' => Configuration::get('MP_GEOLOCATION_API_KEY'),
            'product_cats' => [],
            'id_lang' => $id_lang,
            'activity_periods' => $this->getActivityPeriods(),
            'activity_participants' => $this->getActivityParticipants(),
            'activity_curious' => $this->getActivityCurious(),
        ));

        $this->setTemplate('module:marketplace/views/templates/front/product/activityform.tpl');
    }

    public function getProductDetails($selleractivities, $idLang)
    {
        $language = Language::getLanguage((int) $idLang);

        foreach ($selleractivities as &$product) {
            if ($product['id_ps_product']) { // if product activated
                
                $idPsProduct = $product['id_ps_product'];
                
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

        return $selleractivities;
    }

    public function activityProcess($sellerInfo)
    {
        $wkErrors = array();
        $participants = (int)Tools::getValue('activity_participants');
        $categories = Tools::getValue('mpb_categories');
        $defaultLang = (int) $sellerInfo['default_lang'];
        $shortDesc = strip_tags(Tools::getValue('activity_short_desc'));
        $activityDesc = strip_tags(Tools::getValue('activity_desc'));
        $activityName = strip_tags(Tools::getValue('activity_name'));
        $activity_addr = strip_tags(Tools::getValue('activity_addr'));
        $activity_city = strip_tags(Tools::getValue('activity_city'));
        $activity_postcode = strip_tags(Tools::getValue('activity_postcode'));
        $activity_period = strip_tags(Tools::getValue('activity_period'));
        $activity_curious = Tools::getValue('activity_curious');
        $latitude = Tools::getValue('latitude');
        $longitude = Tools::getValue('longitude');
        $video_link = Tools::getValue('activity_video');
        $id_mp_product = (int)Tools::getValue('id_mp_product');
        $id_booking_product_info = (int)Tools::getValue('idTable');

        array_unshift($categories, self::ACTIVITY_DEFAULT_CATEGORY);

        if (!Validate::isCleanHtml($shortDesc) || Tools::isEmpty($shortDesc)) {
            $wkErrors[] = 'Le contenu de la phrase est invalide';
        } elseif (Tools::strlen($shortDesc) > 500) {
            $wkErrors[] = 'Le longeur de la phrase est trop long';
        }

        if (!Validate::isCleanHtml($activityDesc) || Tools::isEmpty($activityDesc)) {
            $wkErrors[] = 'Le contenu de la description est invalide';
        }

        if (!Validate::isGenericName($activityName) || Tools::isEmpty($activityName)) {
            $wkErrors[] = 'Le nom de l\'activite est invalide';
        } elseif (Tools::strlen($activityName) > 100) {
            $wkErrors[] = 'Le nom de l\'activite est trop long';
        }

        if ($participants == '') {
            $wkErrors[] = 'Le nombre de participants est requis';
        } elseif (!Validate::isInt($participants)) {
            $wkErrors[] = 'Le nombre de participants est invalide';
        }

        if (Tools::isEmpty($activity_addr)) {
            $wkErrors[] = 'Vous devez fournir une adresse de votre activité';
        }

        if (Tools::isEmpty($activity_city)) {
            $wkErrors[] = 'Vous devez fournir une ville de votre activité';
        } elseif (!Validate::isCityName($activity_city)) {
            $wkErrors[] = 'La ville n\'est pas valide';
        }

        if (Tools::isEmpty($activity_postcode)) {
            $wkErrors[] = 'Vous devez fournir un code postal de votre activité';
        } elseif (!Validate::isPostCode($activity_postcode)) {
            $wkErrors[] = 'Le code postal n\'est pas valide';
        }

        if (Tools::isEmpty($activity_period)) {
            $wkErrors[] = 'Il manque la durée de l\'activité';
        }

        if (Tools::isEmpty($activity_curious) || !count($activity_curious)) {
            $wkErrors[] = 'Veuillez selectionnez un ou plusieurs curieux concernés';
        }

        if (!Tools::isEmpty($video_link) && !Validate::isUrl($video_link)) {
            $wkErrors[] = 'Veuillez saisir un lien de la video valide';
        }

        if (count($wkErrors)) {
            $this->context->smarty->assign(array(
                'errors' => $wkErrors,
            ));

            if ($id_mp_product) {
                $this->editProduct($sellerInfo);
            } else {
                $this->addProduct($sellerInfo);
            }
        } else {
            $is_seller = (int)$sellerInfo['id_seller'];
            $updated = false;
            if ($id_mp_product) {
                $objSellerProduct = new WkMpSellerProduct($id_mp_product);
                $updated = true;
            } else {
                $objSellerProduct = new WkMpSellerProduct();
            }

            $objSellerProduct->quantity = $participants;
            $objSellerProduct->minimal_quantity = 1;
            $objSellerProduct->id_category = self::ACTIVITY_DEFAULT_CATEGORY;
            $objSellerProduct->condition = 'new';
            $objSellerProduct->price = 0;
            $objSellerProduct->id_seller = $is_seller;
            $objSellerProduct->id_ps_shop = $this->context->shop->id;
            $objSellerProduct->id_ps_product = ($updated ? $objSellerProduct->id_ps_product : 0);
            $objSellerProduct->active = ($updated ? $objSellerProduct->active : 0);
            $langs = Language::getLanguages(false);

            foreach ($langs as $language) {
                $productIdLang = $language['id_lang'];
                $shortDescIdLang = $language['id_lang'];
                $descIdLang = $language['id_lang'];
                $availableNowIdLang = $language['id_lang'];
                $availableLaterIdLang = $language['id_lang'];
                $metaTitleIdLang = $language['id_lang'];
                $metaDescriptionIdLang = $language['id_lang'];
                $objSellerProduct->product_name[$language['id_lang']] = $activityName;
                $objSellerProduct->short_description[$language['id_lang']] = $shortDesc;
                $objSellerProduct->description[$language['id_lang']] = $activityDesc;
                //Friendly URL
                $objSellerProduct->link_rewrite[$language['id_lang']] = Tools::link_rewrite($activityName);
            }
            $objSellerProduct->save();

            $sellerProductId = $objSellerProduct->id;

            //Add into category table
            $this->assignMpProductCategory($categories, $sellerProductId, self::ACTIVITY_DEFAULT_CATEGORY, $updated);

            if ($updated) {
                $objSellerProduct->updateSellerProductToPs($sellerProductId, $objSellerProduct->active);
                $psProductId = (int)$objSellerProduct->id_ps_product;
            } else {
                // creating ps_product when admin setting is default
                $psProductId = $objSellerProduct->addSellerProductToPs($sellerProductId, 1);
                if ($psProductId) {
                    $objSellerProduct->id_ps_product = $psProductId;
                    $objSellerProduct->save();
                    $objPsProduct = new Product($psProductId);
                    $objPsProduct->is_virtual = 1;
                    $objPsProduct->save();
                }
                
                WkMpSellerProduct::sendMail($sellerProductId, $objSellerProduct->active, $objSellerProduct->active);

                // adding product feature into marketplace table
                if (Configuration::get('WK_MP_MAIL_ADMIN_PRODUCT_ADD')) {
                    $sellerDetail = WkMpSeller::getSeller($is_seller, Configuration::get('PS_LANG_DEFAULT'));
                    if ($sellerDetail) {
                        $sellerName = $sellerDetail['seller_firstname'].' '.$sellerDetail['seller_lastname'];
                        $shopName = $sellerDetail['shop_name'];
                        $objSellerProduct->mailToAdminOnProductAdd($activityName, $sellerName, $sellerDetail['phone'], $shopName, $sellerDetail['business_email']);
                    }
                }
            }


            if ($sellerProductId) {
                if ($id_booking_product_info) {
                   $objBookingProductInfo = new WkMpBookingProductInformation($id_booking_product_info);
                } else {
                   $objBookingProductInfo = new WkMpBookingProductInformation();
                }
                // save booking product Info in our table
                $objBookingProductInfo->id_seller = $objSellerProduct->id_seller;
                $objBookingProductInfo->id_product = $objSellerProduct->id_ps_product;
                $objBookingProductInfo->id_mp_product = $sellerProductId;
                $objBookingProductInfo->quantity = $participants;
                $objBookingProductInfo->booking_type = 2;
                $objBookingProductInfo->activity_addr = $activity_addr;
                $objBookingProductInfo->activity_city = $activity_city;
                $objBookingProductInfo->activity_postcode = $activity_postcode;
                $objBookingProductInfo->activity_period = $activity_period;
                $objBookingProductInfo->activity_curious = implode('#~#', $activity_curious);
                $objBookingProductInfo->activity_material = strip_tags(Tools::getValue('activity_material'));
                $objBookingProductInfo->latitude = $latitude;
                $objBookingProductInfo->longitude = $longitude;
                $objBookingProductInfo->video_link = $video_link;
                $objBookingProductInfo->active = $objSellerProduct->active;
                $objBookingProductInfo->save();
            }

            if ($updated) {
                // create Activity slots
                $this->createActivityTimeSlots($sellerProductId);

                Tools::redirect($this->context->link->getModuleLink('marketplace', 'selleractivities', ['edited_conf' => 1]));
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'selleractivities', ['created_conf' => 1]));
            }
        }
    }

    public function assignMpProductCategory($categories, $mpIdProduct, $defaultCategory, $updated = false)
    {
        if (!is_array($categories)) {
            return false;
        }

        if ($updated) {
            $prodCats = WkMpSellerProductCategory::getSellerCategories($mpIdProduct, [$defaultCategory]);
            unset($categories[array_search($defaultCategory, $categories)]);
            unset($categories[array_search(106, $categories)]);
            $diffCats = [];

            if ($prodCats) {
                $prodCats = explode(',', $prodCats);
                $commonCats = array_intersect($prodCats, $categories);
                $diffCats = array_diff($categories, $prodCats);

                foreach ($prodCats as $cat) {
                    if (in_array($cat, $commonCats)) {
                        continue;
                    }

                    WkMpSellerProductCategory::deleteCategoryByCatIdByProdId($mpIdProduct, $cat);
                }
            }

            if ($diffCats) {
                $objSellerProductCategory = new WkMpSellerProductCategory();
                $objSellerProductCategory->id_seller_product = $mpIdProduct;
                foreach ($diffCats as $categoryVal) {
                    $objSellerProductCategory->id_category = $categoryVal;
                    $objSellerProductCategory->is_default = 0;
                    $objSellerProductCategory->add();
                }
            }
        } else {
            $objSellerProductCategory = new WkMpSellerProductCategory();
            $objSellerProductCategory->id_seller_product = $mpIdProduct;

            foreach ($categories as $categoryVal) {
                $objSellerProductCategory->id_category = $categoryVal;

                if ($categoryVal == $defaultCategory) {
                    $objSellerProductCategory->is_default = 1;
                } else {
                    $objSellerProductCategory->is_default = 0;
                }
                $objSellerProductCategory->add();
            }
        }
    }

    public function ajaxProcessUploadimage()
    {
        if (Tools::getValue('actionIdForUpload')) {
            $actionIdForUpload = Tools::getValue('actionIdForUpload'); //it will be Product Id OR Seller Id
            $adminupload = Tools::getValue('adminupload'); //if uploaded by Admin from backend
            $finalData = WkMpSellerProductImage::uploadImage($_FILES, $actionIdForUpload, $adminupload);
            echo Tools::jsonEncode($finalData);
        }

        die; //ajax close
    }

    public function ajaxProcessDeleteimage()
    {
        //Delete product image
        if (Tools::getValue('actionpage') == 'product') {
            $imageName = Tools::getValue('image_name');
            if ($imageName) {
                WkMpSellerProductImage::deleteProductImage($imageName);
            }
        }

        die; //ajax close
    }

    public function createActivityTimeSlots($mpIdProduct)
    {
        $slotingDateFrom = Tools::getValue('sloting_date_from');
        $slotingDateTo = Tools::getValue('sloting_date_to');
        $idProduct = Tools::getValue('id_booking_product');
        $errors = [];

        if (!$slotingDateFrom || !$slotingDateTo) {
            $errors[] = $this->l('Please select at least one valid date range for time slots.');
        }
        if (!$mpIdProduct) {
            $errors[] = $this->l('Booking product id is missing.');
        }

        if (!count($errors)) {
            WkMpBookingProductTimeSlotPrices::deleteBookingProductTimeSlotsByIdMpProductByPsProduct($mpIdProduct, $idProduct);
            foreach ($slotingDateFrom as $keyDateFrom => $dateFrom) {
                // validate date range duplicacy...
                $errors = $this->validateTimeSlotsDateRangesDuplicacy($dateFrom, $slotingDateTo[$keyDateFrom], $keyDateFrom);
                if (!count($errors)) {
                    $bookingTimeFrom = Tools::getValue('booking_time_from'.$keyDateFrom);
                    $bookingTimeTo = Tools::getValue('booking_time_to'.$keyDateFrom);
                    $slotRangePrice = Tools::getValue('slot_range_price'.$keyDateFrom);

                    if ($bookingTimeFrom && $bookingTimeTo && $slotRangePrice) {
                        foreach ($bookingTimeFrom as $keyTimeFrom => $timeFrom) {
                            $dipSlotsFound = false;
                            //validate time slots duplicacy
                            foreach ($bookingTimeFrom as $keyTime => $timeSlotFrom) {
                                $checkTimeTo = $bookingTimeTo[$keyTime];
                                if ($keyTimeFrom == $keyTime) {
                                    break;
                                } else {
                                    if (strtotime($timeFrom) <= strtotime($checkTimeTo) && strtotime($bookingTimeTo[$keyTimeFrom]) >= strtotime($timeSlotFrom)) {
                                        $dipSlotsFound = true;
                                    }
                                }
                            }
                            if ($dipSlotsFound) {
                                continue;// if duplicate time slot, dont proceed
                            }
                            if ($timeFrom && $bookingTimeTo[$keyTimeFrom] && Validate::isPrice($slotRangePrice[$keyTimeFrom])) {
                                if ($timeFrom < $bookingTimeTo[$keyTimeFrom]) {
                                    $wkTimeSlotPrices = new WkMpBookingProductTimeSlotPrices();
                                    $wkTimeSlotPrices->id_product = $idProduct;
                                    $wkTimeSlotPrices->id_mp_product = $mpIdProduct;
                                    $wkTimeSlotPrices->date_from = date('Y-m-d', strtotime($dateFrom));
                                    $wkTimeSlotPrices->date_to = date('Y-m-d', strtotime($slotingDateTo[$keyDateFrom]));
                                    $wkTimeSlotPrices->time_slot_from = $timeFrom;
                                    $wkTimeSlotPrices->time_slot_to = $bookingTimeTo[$keyTimeFrom];
                                    $wkTimeSlotPrices->price = $slotRangePrice[$keyTimeFrom];
                                    $wkTimeSlotPrices->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->registerStylesheet('marketplace_account', 'modules/'.$this->module->name.'/views/css/marketplace_account.css');
        $this->registerStylesheet('marketplace_global', 'modules/'.$this->module->name.'/views/css/mp_global_style.css');

        if (Tools::getValue('action') == 'edit') {
            $this->addJqueryUI('timepicker');
            //Upload images
            $this->addJS(_MODULE_DIR_.'mpbooking/views/js/mp-seller-booking-product.js');
            $this->addCSS(_MODULE_DIR_.'marketplace/views/css/uploadimage-css/jquery.filer.css');
            //$this->addCSS(_MODULE_DIR_.'marketplace/views/css/uploadimage-css/jquery.filer-dragdropbox-theme.css');
            //$this->registerStylesheet('mp-cropper', 'modules/'.$this->module->name.'/views/css/uploadimage-css/cropper.min.css');

            //$this->addCSS(_MODULE_DIR_.'marketplace/views/css/uploadimage-css/uploadphoto.css');

            //$this->registerJavascript('mp-cropper', 'modules/'.$this->module->name.'/views/js/uploadimage-js/cropper.min.js');
            //$this->addJS(_MODULE_DIR_.'marketplace/views/js/uploadimage-js/jquery.filer.js');
            //$this->addJS(_MODULE_DIR_.'marketplace/views/js/uploadimage-js/uploadimage.js');
            $this->addJS(_MODULE_DIR_.'marketplace/views/js/imageedit.js');
            $this->addJS(_MODULE_DIR_.'marketplace/views/js/managecombination.js');
            $this->registerJavascript('chosen-jquery', 'js/jquery/plugins/jquery.chosen.js', ['priority' => 100, 'position' => 'bottom']);
            $this->registerStylesheet('chosen-jquery', 'themes/beeshary/assets/css/jquery.chosen.css');
            $this->registerStylesheet('mpbooking-activity', 'modules/mpbooking/views/css/front/mpbooking_activity.css');
            $this->registerJavascript('mpbooking-activity', 'modules/mpbooking/views/js/front/activities.js', ['priority' => 250,'position' => 'bottom']);

            /* crop & upload */
            $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/js/image-uploader/css/cropper.css'); 
            $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/image-uploader/js/cropper.js'); 
            $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/image-uploader/js/upload-cropped-image.js');              
        } elseif (Tools::getValue('action') == "add") {
            $this->registerJavascript('chosen-jquery', 'js/jquery/plugins/jquery.chosen.js', ['priority' => 100, 'position' => 'bottom']);
            $this->registerStylesheet('chosen-jquery', 'themes/beeshary/assets/css/jquery.chosen.css');
            $this->registerStylesheet('mpbooking-activity', 'modules/mpbooking/views/css/front/mpbooking_activity.css');
            $this->registerJavascript('mpbooking-activity', 'modules/mpbooking/views/js/front/activities.js', ['priority' => 250,'position' => 'bottom']);
        }
    }

    public function defineJSVars()
    {
        $jsVars = [];
        if ($idMpProduct = (int)Tools::getValue('id_mp_product')) {
            $jsVars = [
                // 'path_uploader' => $this->context->link->getModulelink('marketplace', 'selleractivities'),
                'ajax_urlpath' => $this->context->link->getModuleLink('marketplace', 'productimageedit'),
                'path_uploader' => $this->context->link->getModulelink('marketplace', 'uploadimage'),
                'actionpage' => 'product',
                'adminupload' => 0,
                'deleteaction' => 'jFiler-item-trash-action',
                'actionIdForUpload' => $idMpProduct,
                'choosefile' => 'Selectionnez une image',
                'choosefiletoupload' => 'Selectionnez une image pour uploader',
                'imagechoosen' => 'Les images ont ete choisi',
                'dragdropupload' => 'Drop file here to Upload',
                'confirm_delete_msg' => 'Are you sure want to delete this image?',
                'confirm_delete_msg' => 'Are you sure want to delete this image?',
                'only' => 'Only',
                'imagesallowed' => 'Images are allowed to be uploaded',
                'onlyimagesallowed' => 'Images are allowed to be uploaded.',
                'imagetoolarge' => 'is too large! Please upload image up to',
                'imagetoolargeall' => 'Images you have choosed are too large! Please upload images up to',
                'mp_theme_dir' => _THEME_IMG_DIR_,
                'adminController' => 0,
            ];
        }
        $jsVars['checkCustomerAjaxUrl'] = $this->context->link->getModulelink('mpsellerwiselogin', 'checkcustomerajax');
        $jsVars['defaultCurrencySign'] = $this->context->currency->sign;

        return Media::addJsDef($jsVars);
    }

    /**
     * [validateTimeSlotsDateRangesDuplicacy check date range duplicacy of the time slots type products]
     * @param  [date] $currentDateFrom [description]
     * @param  [date] $currentDateTo   [description]
     * @param  [int] $keyDateFrom     [description]
     * @return [type]                  [description]
     */
    private function validateTimeSlotsDateRangesDuplicacy($currentDateFrom, $currentDateTo, $keyDateFrom)
    {
        $slotingDateFrom = Tools::getValue('sloting_date_from');
        $slotingDateTo = Tools::getValue('sloting_date_to');
        $errors = [];

        foreach ($slotingDateFrom as $key => $dateFrom) {
            $checkDateTo = $slotingDateTo[$key];
            if ($key == $keyDateFrom) {
                break;
            } else {
                if (!$currentDateFrom || !$checkDateTo || !$currentDateTo || !$dateFrom) {
                    $errors[] = $this->module->l('Dates can not be empty in the date ranges.', 'mpbookingproduct');
                } else {
                    if (strtotime($currentDateFrom) <= strtotime($checkDateTo) && strtotime($currentDateTo) >= strtotime($dateFrom)) {
                        $errors[] = $this->module->l('Duplicate date ranges data not saved.', 'mpbookingproduct');
                    }
                }
            }
        }
        return $errors;
    }

    public function getActivityPeriods()
    {
        return ['< 15 minutes', '30 minutes', '1 heure', '1h30', '> 2h'];
    }

    public function getActivityParticipants()
    {
        return array_merge(range(1, 12), ['+ 12 nous consulter']);
    }

    public function getActivityCurious()
    {
        return ['Tout public', 'Enfants', 'Personnes à mobilité'];
    }
}
