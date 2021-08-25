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

class MpBookingMpBookingProductModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Marketplace', array(), 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        ];
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Marketplace Booking Product', array(), 'Breadcrumb'),
            'url' => '',
        ];
        return $breadcrumb;
    }

    public function initContent()
    {
        parent::initContent();
        $idCustomer = $this->context->customer->id;
		
        if ($idCustomer) {
            $sellerInfo = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($sellerInfo && $sellerInfo['active']) {
                $smartyVars = array();
				$smartyVars['idsCategory'] = [];
                $dateFrom = date('d-m-Y');
                // $dateTo = date('d-m-Y', strtotime("+1 day", strtotime($dateFrom)));
                $dateTo = date('d-m-Y');
                $idSeller = $sellerInfo['id_seller'];
                // show admin commission on product base price for seller
                if (Configuration::get('WK_MP_SHOW_ADMIN_COMMISSION')) {
                    if ($adminCommission = WkMpCommission::getCommissionBySellerCustomerId($idCustomer)) {
                        $smartyVars['admin_commission'] = $adminCommission;
                    }
                }
                $jsDef = array();

                if ($idMpProduct = Tools::getValue('id_mp_product')) {
                    if (Validate::isLoadedObject($mpProductInfo = new WkMpSellerProduct($idMpProduct))) {
                        $mpProductInfo = (array) $mpProductInfo;
                        // If seller of current product and current seller customer is match
                        if ($mpProductInfo['id_seller'] == $idSeller) {
                            Hook::exec(
                                'actionBkBeforeShowUpdatedProduct',
                                array('mp_product_details' => $mpProductInfo)
                            );
                            // Category tree
                            $objMpCategory = new WkMpSellerProductCategory();
                            $defaultIdCategory = $objMpCategory->getSellerProductDefaultCategory($idMpProduct);
                            $smartyVars['defaultIdCategory'] = $defaultIdCategory;
                            $objSellerProduct = new WkMpSellerProduct($idMpProduct);
                            $checkedProductCategory = $objSellerProduct->getSellerProductCategories($idMpProduct);
                            $idsCategory = array();
                            if ($checkedProductCategory) {
                                // Default category
                                foreach ($checkedProductCategory as $checkIdCategory) {
                                    $idsCategory[] = $checkIdCategory['id_category'];
                                }
                                $catIdsJoin = implode(',', $idsCategory);
                                $smartyVars['catIdsJoin'] = $catIdsJoin;
                                $smartyVars['idsCategory'] = $idsCategory;
                            }
                            $defaultCategory = Category::getCategoryInformations(
                                $idsCategory,
                                $this->context->language->id
                            );
                            // Category data send END

                            //Assign and display product active/inactive images
                            WkMpSellerProductImage::getProductImageDetails($idMpProduct);
                            $objBookingProductInfo = new WkMpBookingProductInformation();
							
							

                            if ( $bookingProductInfo = $objBookingProductInfo->getBookingProductInfo( $idMpProduct  ) ) {
                                $idBookingProductInfo = $bookingProductInfo['id_booking_product_info'];
                                $smartyVars['bookingProductInfo'] =  $bookingProductInfo;
                                $smartyVars['idBookingProductInfo'] =  $idBookingProductInfo;

                                // Booking Product Information send
                                $mpProductInfo['booking_type'] = $bookingProductInfo['booking_type'];
                                $mpProductInfo['quantity'] = $bookingProductInfo['quantity'];//set Qty as bookking Qty

                                // Send Time Slots information is time slot type product
                                if ($bookingProductInfo['booking_type'] == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT) {
                                    $objBookingTimeSlots = new WkMpBookingProductTimeSlotPrices();
                                    $bookingTimeSlots = $objBookingTimeSlots->getBookingProductAllTimeSlotsFormatted(
                                        $idBookingProductInfo
                                    );
                                    $smartyVars['bookingProductTimeSlots'] =  $bookingTimeSlots;
                                }

                                // Data to show Disables dates (Disable dates/slots tab)
                                $objBookingDisableDates = new WkMpBookingProductDisabledDates();
                                // get booking product disable dates
                                $bookingDisableDates = $objBookingDisableDates->getBookingProductDisableDates(
                                    $idBookingProductInfo
                                );
                                if ($bookingDisableDates) {
                                    if ($bookingDisableDates['disabled_special_days']) {
                                        $bookingDisableDates['disabled_special_days'] = json_decode(
                                            $bookingDisableDates['disabled_special_days'],
                                            true
                                        );
                                    }
                                    if ($bookingDisableDates['disabled_dates_slots']) {
                                        $bookingDisableDates['disabled_dates_slots_array'] = json_decode(
                                            $bookingDisableDates['disabled_dates_slots'],
                                            true
                                        );
                                    }
                                    $bookingDisableDatesInfo = $objBookingDisableDates->getBookingProductDisableDatesInfoFormatted(
                                        $idBookingProductInfo
                                    );
                                    if ($bookingDisableDatesInfo) {
                                        $jsDef['disabledDays'] = $bookingDisableDatesInfo['disabledDays'];
                                        $jsDef['disabledDates'] = $bookingDisableDatesInfo['disabledDates'];
                                        $smartyVars['disabledDays'] = $bookingDisableDatesInfo['disabledDays'];
                                        $smartyVars['disabledDates'] = $bookingDisableDatesInfo['disabledDates'];
                                    }
                                    $smartyVars['DISABLE_SPECIAL_DAYS_ACTIVE'] = $bookingDisableDates['disable_special_days_active'];
                                    $smartyVars['DISABLE_SPECIFIC_DAYS_ACTIVE'] = $bookingDisableDates['disabled_dates_slots_active'];

                                }
                                $smartyVars['bookingDisableDates'] = $bookingDisableDates;
                                // End (Disable dates/slots tab)

                                // Send rates/Availability information on the calendar(Availability & Rates Tab)
                                $objBookingCart = new WkMpBookingCart();
                                $bookingCalendarData = array();
                                if (Tools::isSubmit('availability-search-submit')) {
                                    $availablityDateFrom = Tools::getValue('availability_date_from');
                                    $availablityDateTo = Tools::getValue('availability_date_to');

                                    $availablityDateFrom = date("Y-m-d", strtotime($availablityDateFrom));
                                    $availablityDateTo = date("Y-m-d", strtotime($availablityDateTo));
                                    if ($availablityDateFrom == '') {
                                        $this->errors[] = $this->module->l(
                                            'Date From is required field.',
                                            'mpbookingproduct'
                                        );
                                    }
                                    if ($availablityDateTo == '') {
                                        $this->errors[] = $this->module->l(
                                            'Date To is required field.',
                                            'mpbookingproduct'
                                        );
                                    }
                                    if ($availablityDateTo < $availablityDateFrom) {
                                        $this->errors[] = $this->module->l(
                                            'Date To should be greater than Date From.',
                                            'mpbookingproduct'
                                        );
                                    }
                                    $tab = Tools::getValue('active_tab');
                                    if (!count($this->errors)) {
                                        $dateStart = $availablityDateFrom;
                                        while (strtotime($dateStart) <= strtotime($availablityDateTo)) {
                                            $tempDateTo = date('Y-m-d', strtotime("+1 day", strtotime($dateStart)));
                                            $bookingCalendarData[$dateStart] = $objBookingCart->getBookingProductDateWiseAvailabilityAndRates(
                                                $idBookingProductInfo,
                                                $dateStart,
                                                $tempDateTo
                                            );
                                            $dateStart = date('Y-m-d', strtotime("+1 day", strtotime($dateStart)));
                                        }
                                    }
                                    $smartyVars['active_tab'] = Tools::getValue('active_tab');
                                } else {
                                    // assign booking info for today on the page
                                    $availablityDateFrom = date("Y-m-d");
                                    $availablityDateTo = date("Y-m-t", strtotime("$availablityDateFrom +1 month"));
                                    $dateStart = $availablityDateFrom;
                                    while (strtotime($dateStart) <= strtotime($availablityDateTo)) {
                                        $tempDateTo = date('Y-m-d', strtotime("+1 day", strtotime($dateStart)));
                                        $bookingCalendarData[$dateStart] = $objBookingCart->getBookingProductDateWiseAvailabilityAndRates(
                                            $idBookingProductInfo,
                                            $dateStart,
                                            $tempDateTo
                                        );
                                        $dateStart = date('Y-m-d', strtotime("+1 day", strtotime($dateStart)));
                                    }
                                    $smartyVars['active_tab'] = Tools::getValue('tab');
                                }
                                $smartyVars['availablity_date_to'] = $availablityDateTo;
                                $smartyVars['availablity_date_from'] = $availablityDateFrom;
                                $smartyVars['bookingCalendarData'] = $bookingCalendarData;

                                $jsDef['booking_type'] = $bookingProductInfo['booking_type'];
                                $jsDef['bookingCalendarData'] = $bookingCalendarData;
                                $jsDef['calendarDate'] = date("d-m-Y", strtotime($availablityDateFrom));
                                //End (Availability & Rates Tab)
                            } else {
                                Tools::redirect($this->context->link->getModuleLink('mpbooking', 'mpbookingproductslist'));
                            }
                        } else {
                            Tools::redirect($this->context->link->getModuleLink('mpbooking', 'mpbookingproductslist'));
                        }

                        $smartyVars['product_info'] = $mpProductInfo;
                        $smartyVars['id_tax_rules_group'] = $mpProductInfo['id_tax_rules_group'];
                        $smartyVars['id_mp_product'] = $idMpProduct;
                    } else {
                        Tools::redirect($this->context->link->getModuleLink('mpbooking', 'mpbookingproductslist'));
                    }
                } else {
                    $idCategory = array(Category::getRootCategory()->id); //home category id
                    $defaultCategory = Category::getCategoryInformations($idCategory, $this->context->language->id);
                }

                // Set default lang at every form according to configuration multi-language
                WkMpHelper::assignDefaultLang($idSeller);
                Media::addJsDef($jsDef);
                //show tax rule group on add product page
                $taxRuleGroups = TaxRulesGroup::getTaxRulesGroups(true);
                if ($taxRuleGroups && Configuration::get('WK_MP_SELLER_APPLIED_TAX_RULE')) {
                    $smartyVars['tax_rules_groups'] = $taxRuleGroups;
                    $smartyVars['mp_seller_applied_tax_rule'] = 1;
                }

                $objDefaultCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
                $smartyVars['defaultCurrencySign'] = $objDefaultCurrency->sign;
                $smartyVars['controller'] = 'mpbookingproduct';
                $smartyVars['static_token'] = Tools::getToken(false);
                $smartyVars['module_dir'] = _MODULE_DIR_;
                $smartyVars['ps_img_dir'] = _PS_IMG_.'l/';
                $smartyVars['defaultCategory'] = $defaultCategory;
                $smartyVars['is_seller'] = 1;
                $smartyVars['logic'] = 'mpbookingproduct';
                $smartyVars['default_lang'] = $sellerInfo['default_lang'];
                $smartyVars['mp_seller_info'] = $sellerInfo;
                $smartyVars['date_from'] = $dateFrom;
                $smartyVars['date_to'] = $dateTo;
                $smartyVars['booking_type_time_slot'] = WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT;
                $smartyVars['booking_type_date_range'] = WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE;

                $this->defineJSVars();

                $this->context->smarty->assign($smartyVars);
                $this->setTemplate('module:mpbooking/views/templates/front/mpbookingproduct.tpl');
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect(
                'index.php?controller=authentication&back='.
                urlencode($this->context->link->getModuleLink('marketplace', 'mpbookingproduct'))
            );
        }
    }
	
	private function mpbk_get_lat_lng($address){
		$address = urlencode($address);
		$gg_key = 'AIzaSyCOmT3Bc4gFK4b-ZzOYgTCcvAnp4mV6uEw';
		$url = "https://maps.google.com/maps/api/geocode/json?address=$address&key=".$gg_key;
		$result = @file_get_contents($url);
		$result = @json_decode($result);
		$lat 	= @$result->results[0]->geometry->location->lat;
		$lng 	= @$result->results[0]->geometry->location->lng;
		
		$return = [
			'lat' => $lat,
			'lng' => $lng,
		];
		return $return;
	}

    public function postProcess()
    {
        if (Tools::isSubmit('StayMpBookingProduct') || Tools::isSubmit('SubmitMpBookingProduct')) {
            $sellerInfo = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if ($sellerInfo && $sellerInfo['active']) {
                $idSeller = $sellerInfo['id_seller'];
                $objSellerProduct = new WkMpSellerProduct();
                $edit = 0;
                $idMpProduct = Tools::getValue('id_mp_product');
                if ($idMpProduct
                    && Validate::isLoadedObject($objSellerProduct = new WkMpSellerProduct($idMpProduct))
                ) {
                    $edit = 1;
                }
                //get data from add product form
                $quantity = Tools::getValue('quantity');
                $minimalQuantity = Tools::getValue('minimal_quantity');
                $condition = Tools::getValue('condition');

                $price = Tools::getValue('price');
                $idTaxRulesGroup = Tools::getValue('id_tax_rules_group');

                $defaultCategory = Tools::getValue('default_category');
                $categories = Tools::getValue('product_categories');
				$categories[] = 13;
			

                $reference = trim(Tools::getValue('reference'));
                $sellerDefaultLanguage = Tools::getValue('default_lang');
                $defaultLang = WkMpHelper::getDefaultLanguageBeforeFormSave($sellerDefaultLanguage);
				
				$activity_addr = strip_tags(Tools::getValue('activity_addr'));
				$activity_city = strip_tags(Tools::getValue('activity_city'));
				$activity_postcode = strip_tags(Tools::getValue('activity_postcode'));
				$activity_period = strip_tags(Tools::getValue('activity_period'));
				$activity_curious = Tools::getValue('activity_curious');
				$activity_participants = Tools::getValue('activity_participants');
				$latitude = Tools::getValue('latitude');
				$longitude = Tools::getValue('longitude');
				$video_link = Tools::getValue('video_link');
				
				if( $activity_city && $activity_postcode ){
					$lat_lng = $this->mpbk_get_lat_lng($activity_postcode.' '.$activity_city);
					$latitude = $lat_lng['lat'];
					$longitude = $lat_lng['lng'];
				}

                if (Configuration::get('WK_MP_SELLER_PRODUCT_VISIBILITY')) {
                    //Product Visibility
                    $availableForOrder = trim(Tools::getValue('available_for_order'));
                    $showPrice = $availableForOrder ? 1 : trim(Tools::getValue('show_price'));
                    $onlineOnly = trim(Tools::getValue('online_only'));
                    $visibility = trim(Tools::getValue('visibility'));
                }
                if (!Tools::getValue('product_name_'.$defaultLang)) {
                    if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                        $sellerLang = Language::getLanguage((int) $defaultLang);
                        $this->errors[] = sprintf(
                            $this->module->l('Product name is required in %s', 'mpbookingproduct'),
                            $sellerLang['name']
                        );
                    } else {
                        $this->errors[] = $this->module->l('Product name is required', 'mpbookingproduct');
                    }
                } else {
                    // Validate form
                    $mpProductErrors = WkMpSellerProduct::validateMpProductForm();
                    $this->errors = $mpProductErrors ? $mpProductErrors : array();
                    if (!Tools::getValue('booking_type')) {
                        $this->errors[] = $this->module->l(
                            'Please select type of booking of this product.',
                            'mpbookingproduct'
                        );
                    }

                    if ($edit) {
                        Hook::exec('actionBkBeforeUpdateMPProduct', array('id_mp_product' => $idMpProduct));
                    } else {
                        Hook::exec('actionBkBeforeAddMPProduct', array('id_seller' => $idSeller));
                    }

                    if (empty($this->errors)) {
                        $objSellerProduct->id_seller = $idSeller;
                        $objSellerProduct->quantity = '99999999999'; // set maximum qty for booking product
                        $objSellerProduct->minimal_quantity = $minimalQuantity;
                        $objSellerProduct->id_ps_product = $edit ? $objSellerProduct->id_ps_product : 0;
                        $objSellerProduct->id_category = $defaultCategory;
                        $objSellerProduct->id_ps_shop = $this->context->shop->id;
                        $objSellerProduct->condition = $condition;
                        //Pricing
                        $objSellerProduct->price = $price;
                        $objSellerProduct->id_tax_rules_group = $idTaxRulesGroup;;

                        if (Configuration::get('WK_MP_SELLER_PRODUCT_REFERENCE')) {
                            $objSellerProduct->reference = $reference;
                        }

                        //control product approval setting while adding product
                        if (!$edit) {
                            if (Configuration::get('WK_MP_PRODUCT_ADMIN_APPROVE')) {
                                $objSellerProduct->active = 0;
                                $objSellerProduct->admin_approved = 0;
                            } else {
                                $objSellerProduct->active = 1;
                                $objSellerProduct->admin_approved = 1;
                            }
                        }

                        foreach (Language::getLanguages(false) as $language) {
                            $productIdLang = $language['id_lang'];
                            $shortDescIdLang = $language['id_lang'];
                            $descIdLang = $language['id_lang'];

                            //if product name in other language is not available
                            //then fill with seller language same for others
                            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                                if (!Tools::getValue('product_name_'.$language['id_lang'])) {
                                    $productIdLang = $defaultLang;
                                }
                                if (!Tools::getValue('short_description_'.$language['id_lang'])) {
                                    $shortDescIdLang = $defaultLang;
                                }
                                if (!Tools::getValue('description_'.$language['id_lang'])) {
                                    $descIdLang = $defaultLang;
                                }
                            } else {
                                //if multilang is OFF then all fields will be filled as default lang content
                                $productIdLang = $defaultLang;
                                $shortDescIdLang = $defaultLang;
                                $descIdLang = $defaultLang;
                            }

                            if (Configuration::get('PS_LANG_DEFAULT') == $language['id_lang']) {
                                $nameDefaultLang = Tools::getValue('product_name_'.$productIdLang);
                            }
                            $objSellerProduct->product_name[$language['id_lang']] = Tools::getValue(
                                'product_name_'.$productIdLang
                            );
                            $objSellerProduct->short_description[$language['id_lang']] = Tools::getValue(
                                'short_description_'.$shortDescIdLang
                            );
                            $objSellerProduct->description[$language['id_lang']] = Tools::getValue(
                                'description_'.$descIdLang
                            );
                            $objSellerProduct->link_rewrite[$language['id_lang']] = Tools::link_rewrite(
                                Tools::getValue('product_name_'.$productIdLang)
                            );
                        }
					
                        if ($objSellerProduct->save()) {
                            $idPsProduct = $objSellerProduct->id_ps_product;
                            $idMpProduct = $objSellerProduct->id;
                            if ($idMpProduct) {
                                //Add into category table
                                $objMpCategory = new WkMpSellerProductCategory();
                                // for Updating new categories first delete previous category
                                $objMpCategory->deleteProductCategory($idMpProduct);
                                $objMpCategory->id_seller_product = $idMpProduct;
                                if ($categories) {
                                    //set if more than one category selected
                                    foreach ($categories as $pCategory) {
                                        $objMpCategory->id_category = $pCategory;
                                        if ($pCategory == $defaultCategory) {
                                            $objMpCategory->is_default = 1;
                                        } else {
                                            $objMpCategory->is_default = 0;
                                        }

                                        $objMpCategory->add();
                                    }
                                }
                                //control product approval setting
                                if ($edit) {
                                    if ($objSellerProduct->active) {
                                        $deactivateAfterUpdate = WkMpSellerProduct::deactivateProductAfterUpdate(
                                            $idMpProduct,
                                            1
                                        );
                                        if (!Configuration::get('WK_MP_PRODUCT_UPDATE_ADMIN_APPROVE')) {
                                            // Update also in prestashop if product is active
                                            $idPsProduct = $objSellerProduct->updateSellerProductToPs($idMpProduct, 1);
                                        }
                                    }
                                } else {
                                    //if (!Configuration::get('WK_MP_PRODUCT_ADMIN_APPROVE')) {
                                        // creating ps_product when admin setting is default
                                        $idPsProduct = $objSellerProduct->addSellerProductToPs($idMpProduct, 1);
                                        //save ps_product
                                        $objSellerProduct->id_ps_product = $idPsProduct;
                                        $objSellerProduct->save();
                                        Hook::exec(
                                            'actionBkToogleMPProductCreateStatus',
                                            array('id_product' => $idPsProduct, 'active' => 1)
                                        );
                                    //}
                                }

                                if (!$edit) {
									/* ttfc disable sending email */
                                    WkMpSellerProduct::sendMail($idMpProduct, 1, 1);
                                }
                                if ($idPsProduct) {
                                    $objPsProduct = new Product($idPsProduct);
                                    $objPsProduct->is_virtual = 1;
                                    $objPsProduct->save();
                                }
                                $objBookingProductInfo = new WkMpBookingProductInformation();
                                if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(
                                    $idMpProduct
                                )) {
                                    $objBookingProductInfo = new WkMpBookingProductInformation(
                                        $bookingProductInfo['id_booking_product_info']
                                    );
                                }
                                // save booking product Info in our table
                                $objBookingProductInfo->id_product = $idPsProduct;
                                $objBookingProductInfo->id_mp_product = $idMpProduct;
                                $objBookingProductInfo->id_seller = $idSeller;
                                $objBookingProductInfo->quantity = $quantity;
                                $objBookingProductInfo->booking_type = Tools::getValue('booking_type');
                                $objBookingProductInfo->active = $objSellerProduct->active;
								
								// adding new fields
								$objBookingProductInfo->activity_addr = $activity_addr;
								$objBookingProductInfo->activity_city = $activity_city;
								$objBookingProductInfo->activity_postcode = $activity_postcode;
								$objBookingProductInfo->activity_period = $activity_period;
								$objBookingProductInfo->activity_curious = $activity_curious;
								$objBookingProductInfo->activity_participants = $activity_participants;
								$objBookingProductInfo->activity_material = strip_tags(Tools::getValue('activity_material'));
								$objBookingProductInfo->latitude = $latitude;
								$objBookingProductInfo->longitude = $longitude;
								$objBookingProductInfo->video_link = $video_link;
								// end adding new fields
			
                                if ($objBookingProductInfo->save()) {
                                    $idBookingProductInfo = $objBookingProductInfo->id;
                                    $invalidRange = 0;
                                    // if product is successfully saved the save the time slot information if available
                                    $saveTimeSlotInfo = Tools::getValue('time_slots_data_save');
                                    if (isset($saveTimeSlotInfo) && $saveTimeSlotInfo) {
                                        $slotingDatesFrom = Tools::getValue('sloting_date_from');
                                        $slotingDatesTo = Tools::getValue('sloting_date_to');
                                        // check  if at least one time slot is available to process
                                        if ((isset($slotingDatesFrom[0]) && $slotingDatesFrom[0] && !$slotingDatesTo)) {
                                            $this->errors[] = $this->module->l('Please select at least one valid date range for time slots.', 'mpbookingproduct');
                                        }
                                        if (!$idMpProduct) {
                                            $this->errors[] = $this->module->l('Booking product id is missing to create time slots.', 'mpbookingproduct');
                                        }
                                        if (!count($this->errors)) {
                                            $wkTimeSlotPrices = new WkMpBookingProductTimeSlotPrices();
                                            if ($wkTimeSlotPrices->deleteTimeSlotsByIdBookingProductInfo(
                                                $idBookingProductInfo
                                            )) {
                                                if (isset($slotingDatesFrom[0])) {
                                                    foreach ($slotingDatesFrom as $keyDateFrom => $dateFrom) {
                                                        if ($dateFrom && $slotingDatesTo[$keyDateFrom]) {
                                                            if (strtotime($dateFrom) <= strtotime($slotingDatesTo[$keyDateFrom])) {
                                                                if (!count($this->errors)) {
                                                                    $bookingTimeFrom = Tools::getValue(
                                                                        'booking_time_from'.$keyDateFrom
                                                                    );
                                                                    $bookingTimeTo = Tools::getValue('booking_time_to'.$keyDateFrom);
                                                                    $slotRangePrice = Tools::getValue('slot_range_price'.$keyDateFrom);
                                                                    $slotRangeId = Tools::getValue('time_slot_id'.$keyDateFrom);
                                                                    $slotActive = Tools::getValue('slot_active'.$keyDateFrom);

                                                                    if (isset($bookingTimeFrom[0])
                                                                        && $bookingTimeFrom[0]
                                                                        && $bookingTimeTo
                                                                        && $slotRangePrice
                                                                    ) {
                                                                        foreach ($bookingTimeFrom as $keyTimeFrom => $timeFrom) {
                                                                            //validate time slots duplicacy
                                                                            foreach ($bookingTimeFrom as $keyTime => $timeSlotFrom) {
                                                                                $checkTimeTo = $bookingTimeTo[$keyTime];
                                                                                if ($keyTimeFrom == $keyTime) {
                                                                                    break;
                                                                                } else {
                                                                                    if (strtotime($timeFrom) <= strtotime($checkTimeTo)
                                                                                        && strtotime($bookingTimeTo[$keyTimeFrom]) >= strtotime($timeSlotFrom)
                                                                                    ) {
                                                                                        $this->errors[] = $this->module->l('Duplicate time slots data not saved.', 'mpbookingproduct');
                                                                                    }
                                                                                }
                                                                            }
                                                                            $validateError = $wkTimeSlotPrices->validateTimeSlotsDuplicacyInOtherDateRanges(
                                                                                $idBookingProductInfo,
                                                                                $dateFrom,
                                                                                $slotingDatesTo[$keyDateFrom],
                                                                                $timeFrom,
                                                                                $bookingTimeTo[$keyTimeFrom]
                                                                            );
                                                                            if ($validateError) {
                                                                                $this->errors[] = $validateError;
                                                                            }
                                                                            if (count($this->errors)) {
                                                                                continue;// if duplicate time slot dont proceed
                                                                            }
                                                                            if ($timeFrom
                                                                                && $bookingTimeTo[$keyTimeFrom]
                                                                                && Validate::isPrice($slotRangePrice[$keyTimeFrom])
                                                                            ) {
                                                                                if ($timeFrom < $bookingTimeTo[$keyTimeFrom]) {
                                                                                    if (Validate::isPrice(
                                                                                        $slotRangePrice[$keyTimeFrom]
                                                                                    )) {
                                                                                        if (isset($slotRangeId[$keyTimeFrom])
                                                                                            && $slotRangeId[$keyTimeFrom]
                                                                                        ) {
                                                                                            $wkTimeSlotPrices = new WkMpBookingProductTimeSlotPrices($slotRangeId[$keyTimeFrom]);
                                                                                        } else {
                                                                                            $wkTimeSlotPrices = new WkMpBookingProductTimeSlotPrices();
                                                                                        }
                                                                                        $wkTimeSlotPrices->id_booking_product_info = $idBookingProductInfo;
                                                                                        $wkTimeSlotPrices->date_from = date('Y-m-d', strtotime($dateFrom));
                                                                                        $wkTimeSlotPrices->date_to = date('Y-m-d', strtotime($slotingDatesTo[$keyDateFrom]));
                                                                                        $wkTimeSlotPrices->time_slot_from = $timeFrom;
                                                                                        $wkTimeSlotPrices->time_slot_to = $bookingTimeTo[$keyTimeFrom];
                                                                                        $wkTimeSlotPrices->price = $slotRangePrice[$keyTimeFrom];
                                                                                        $wkTimeSlotPrices->active = $slotActive[$keyTimeFrom];
                                                                                        $wkTimeSlotPrices->save();
                                                                                    } else {
                                                                                        $this->errors[] = $this->module->l('Time Slot ', 'mpbookingproduct').$timeFrom.$this->module->l(' to ', 'mpbookingproduct').$bookingTimeTo[$keyTimeFrom].$this->module->l(' for the date range ', 'mpbookingproduct').date('Y-m-d', strtotime($dateFrom)).$this->module->l(' To ', 'mpbookingproduct').date('Y-m-d', strtotime($slotingDatesTo[$keyDateFrom])).$this->module->l(' not saved because of invalid price : ', 'mpbookingproduct').$slotRangePrice[$keyTimeFrom];
                                                                                    }
                                                                                } else {
                                                                                    $this->errors[] = $this->module->l('Time Slot ', 'mpbookingproduct').$timeFrom.$this->module->l(' to ', 'mpbookingproduct').$bookingTimeTo[$keyTimeFrom].$this->module->l(' for the date range ', 'mpbookingproduct').date('Y-m-d', strtotime($dateFrom)).$this->module->l(' To ', 'mpbookingproduct').date('Y-m-d', strtotime($slotingDatesTo[$keyDateFrom])).$this->module->l(' not saved because of invalid time slots', 'mpbookingproduct');
                                                                                }
                                                                            } else {
                                                                                $this->errors[] = $this->module->l('Time Slot not saved because of missing info of time slots', 'mpbookingproduct');
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            } else {
                                                                $this->errors[] = $this->module->l('Date from can not be after date to while adding time slots.', 'mpbookingproduct');
                                                            }
                                                        } else {
                                                            $invalidRange = 1;
                                                        }
                                                    }
                                                }
                                            } else {
                                                $this->errors[] = $this->module->l('Some error occurred while saving time slots info.', 'mpbookingproduct');
                                            }
                                            if ($invalidRange) {
                                                $this->errors[] = $this->module->l('Invalid date ranges were not saved.', 'mpbookingproduct');
                                            }
                                        }
                                    }

                                    // save the disable dates and time slots info
                                    $toDisableSpecialDays = Tools::getValue('disable_special_days_active');
                                    $toDisableDates = Tools::getValue('disable_specific_days_active');
                                    // Data to show Disables dates (Disable dates/slots tab)
                                    $disabledSpecialDays = Tools::getValue('disabled_special_days');
                                    $disabledSpecificDatesJson = Tools::getValue('disabled_specific_dates_json');
                                    if ($toDisableSpecialDays) {
                                        if (!$disabledSpecialDays | !count($disabledSpecialDays)) {
                                            $this->errors[] = $this->module->l('if Disable Special Days is active, Please select at least one special day to disable.', 'mpbookingproduct');
                                        }
                                    }
                                    if ($toDisableDates) {
                                        if (!$disabledSpecificDatesJson || !count(json_decode($disabledSpecificDatesJson, true))) {
                                            $this->errors[] = $this->module->l('if Disable Specific Dates is active, Please select at least one date to disable.', 'mpbookingproduct');
                                        }
                                    }
                                    if (empty($this->errors)) {
                                        $objBookingDisableDates = new WkMpBookingProductDisabledDates();
                                        $bookingDisableDates = $objBookingDisableDates->getBookingProductDisableDates(
                                            $idBookingProductInfo
                                        );
                                        if ($bookingDisableDates
                                            || ($toDisableSpecialDays && $disabledSpecialDays)
                                            || ($toDisableDates && $disabledSpecificDatesJson)
                                        ) {
                                            if ($bookingDisableDates) {
                                                $objBookingDisableDates = new WkMpBookingProductDisabledDates(
                                                    $bookingDisableDates['id_disabled_dates']
                                                );
                                            }
                                            $objBookingDisableDates->id_booking_product_info = $idBookingProductInfo;
                                            $objBookingDisableDates->disable_special_days_active = $toDisableSpecialDays;
                                            $objBookingDisableDates->disabled_dates_slots_active = $toDisableDates;
                                            $objBookingDisableDates->disabled_special_days = isset($disabledSpecialDays) && $disabledSpecialDays ? json_encode($disabledSpecialDays) : 0;
                                            $objBookingDisableDates->disabled_dates_slots = isset($disabledSpecificDatesJson) && $disabledSpecificDatesJson ? $disabledSpecificDatesJson : 0;
                                            if (!$objBookingDisableDates->save()) {
                                                $this->errors[] = $this->module->l('Some error has been occurred while saving disable dates info.', 'mpbookingproduct');
                                            }
                                        }
                                    }
                                }
                                if (!$edit && Configuration::get('WK_MP_MAIL_ADMIN_PRODUCT_ADD')) {
                                    $sellerDetail = WkMpSeller::getSeller(
                                        $idSeller,
                                        Configuration::get('PS_LANG_DEFAULT')
                                    );
                                    if ($sellerDetail) {
                                        $sellerName = $sellerDetail['seller_firstname'].' '.
                                        $sellerDetail['seller_lastname'];
                                        $shopName = $sellerDetail['shop_name'];
                                        $objSellerProduct->mailToAdminOnProductAdd(
                                            $nameDefaultLang,
                                            $sellerName,
                                            $sellerDetail['phone'],
                                            $shopName,
                                            $sellerDetail['business_email']
                                        );
                                    }
                                }
                            }
                        }

                        if ($edit) {
                            Hook::exec(
                                'actionBkAfterUpdateMPProduct',
                                array('id_mp_product' => $idMpProduct, 'id_mp_product_attribute' => 0)
                            );
                        } else {
                            Hook::exec('actionBkAfterAddMPProduct', array('id_mp_product' => $idMpProduct));
                        }
                        if (empty($this->errors)) {
                            if (isset($edit)) {
                                if (isset($deactivateAfterUpdate) && $deactivateAfterUpdate) {
                                    $params = array('edited_withdeactive' => 1);
                                } else {
                                    $params = array('edited_conf' => 1);
                                }
                            } else {
                                $params = array('created_conf' => 1);
                            }
                            if (Tools::isSubmit('StayMpBookingProduct')) {
                                $params['id_mp_product'] = $idMpProduct;
                                $params['tab'] = Tools::getValue('active_tab');
                                Tools::redirect($this->context->link->getModuleLink('mpbooking', 'mpbookingproduct', $params));
                            } else {
                                Tools::redirect($this->context->link->getModuleLink('mpbooking', 'mpbookingproductslist', $params));
                            }
                        } else {
                            $this->warning[] = $this->module->l('Product has been saved successfully. But above errors were occurred while saving the time slots information of the booking product-', 'mpbookingproduct');
                        }
                    }
                }
            }
        }
        parent::postProcess();
    }

    /**
     * Load Prestashop category with ajax load of plugin jstree.
     */
    public function displayAjaxProductCategory()
    {
        if (!$this->isTokenValid()) {
            die('Something went wrong!');
        }
        WkMpSellerProduct::getMpProductCategory();
    }

    private function defineJSVars()
    {
        $objDefaultCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $jsVars = array(
            'path_sellerproduct' => $this->context->link->getModuleLink('mpbooking', 'mpbookingproduct'),
            'defaultCurrencySign' => $objDefaultCurrency->sign,
            'adminController' => 0,
            'booking_type_time_slot' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT,
            'booking_type_date_range' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE,
        );
        if ($idMpProduct = Tools::getValue('id_mp_product')) {
            $objMpCategory = new WkMpSellerProductCategory();
            $defaultIdCategory = $objMpCategory->getSellerProductDefaultCategory($idMpProduct);
            $jsVars = array_merge(
                $jsVars,
                array(
                    'image_drag_drop' => 1,
                    'actionpage' => 'product',
                    'wk_image_dir' => _MODULE_DIR_.'mpbooking/views/img/',
                    'defaultIdCategory' => $defaultIdCategory,
                    'adminupload' => 0,
                    'static_token' => Tools::getToken(false),
                    'actionIdForUpload' => $idMpProduct,
                    'deleteaction' => 'jFiler-item-trash-action',
                    'path_uploader' => $this->context->link->getModulelink('marketplace', 'uploadimage'),
                    'ajax_urlpath' => $this->context->link->getModuleLink('marketplace', 'productimageedit'),
                    'confirm_delete_msg' => $this->module->l(
                        'Are you sure you want to delete this image?',
                        'mpbookingproduct'
                    ),
                    'drag_drop' => $this->module->l('Drag & Drop to Upload', 'mpbookingproduct'),
                    'or' => $this->module->l('or', 'mpbookingproduct'),
                    'pick_img' => $this->module->l('Pick Image', 'mpbookingproduct'),
                    'delete_msg' => $this->module->l('Deleted.', 'mpbookingproduct'),
                    'error_msg' => $this->module->l('An error occurred.', 'mpbookingproduct'),
                    'choosefile' => $this->module->l('Choose Images', 'mpbookingproduct'),
                    'choosefiletoupload' => $this->module->l('Choose Images To Upload', 'mpbookingproduct'),
                    'imagechoosen' => $this->module->l('Images were chosen', 'mpbookingproduct'),
                    'dragdropupload' => $this->module->l('Drop file here to Upload', 'mpbookingproduct'),
                    'only' => $this->module->l('Only', 'mpbookingproduct'),
                    'imagesallowed' => $this->module->l('Images are allowed to be uploaded.', 'mpbookingproduct'),
                    'onlyimagesallowed' => $this->module->l('Only Images are allowed to be uploaded.', 'mpbookingproduct'),
                    'imagetoolarge' => $this->module->l('is too large! Please upload image up to', 'mpbookingproduct'),
                    'imagetoolargeall' => $this->module->l('Images you have choosed are too large! Please upload images up to', 'mpbookingproduct'),
                    // vars for booking product actions
                    'slot_text' => $this->module->l('Slot', 'mpbookingproduct'),
                    'avl_qty_txt' => $this->module->l('Avail qty', 'mpbookingproduct'),
                    'price_txt' => $this->module->l('Price', 'mpbookingproduct'),
                    'booked_qty_txt' => $this->module->l('Booked', 'mpbookingproduct'),
                    'status_txt' => $this->module->l('Status', 'mpbookingproduct'),
                    'day_text' => $this->module->l('day', 'mpbookingproduct'),
                    'add_more_slots_txt' => $this->module->l('Add More Slots', 'mpbookingproduct'),
                    'no_info_found_txt' => $this->module->l('No Information Found.', 'mpbookingproduct'),
                    'no_slots_avail_txt' => $this->module->l('No time slot available.', 'mpbookingproduct'),
                    'date_to_more_date_from_err' => $this->module->l(
                        'date to must be greater than date from.',
                        'mpbookingproduct'
                    ),
                    'date_from_req' => $this->module->l('Date from is missing.', 'mpbookingproduct'),
                    'date_to_req' => $this->module->l('Date to is missing.', 'mpbookingproduct'),
                    'to_txt' => $this->module->l('To', 'mpbookingproduct'),
                    'date_range_already_added' => $this->module->l(
                        'Disable date range already added.',
                        'mpbookingproduct'
                    ),
                    'all_slots_disable_warning' => $this->module->l(
                        'In selected date range time slots of more than one date ranges are there. So in this case all
                        the time slots will be disabled in this date range.',
                        'mpbookingproduct'
                    ),
                    'date_from_less_current_date_err' => $this->module->l(
                        'date from can not be before current date.',
                        'mpbookingproduct'
                    ),
                    'no_slot_selected_err' => $this->module->l(
                        'No slots selected. Please select at least one slot.',
                        'mpbookingproduct'
                    ),
                    'update_success' => $this->module->l('Updated Successfully', 'mpbookingproduct'),
                    'success_msg' => $this->module->l('Success', 'mpbookingproduct'),
                )
            );
        }
        Media::addJsDef($jsVars);
    }

    /**
     * [validateBookingProductDetails validate booking product submitted information]
     * @return [type] [description]
     */
    public function validateBookingProductDetails()
    {
        $productName = Tools::getValue('name');
        $productPrice = Tools::getValue('price');
        $productQuantity = Tools::getValue('product_quantity');
        $productDescription = Tools::getValue('description');
        $productCategory = Tools::getValue('product_category');
        $shortDescription = Tools::getValue('description_short');
        //$productCondition = Tools::getValue('condition');
        if ($productName == '') {
            $this->errors[] = $this->module->l('Product name is required field.', 'mpbookingproduct');
        } elseif (!Validate::isGenericName($productName)) {
            $this->errors[] = $this->module->l('Product name must not have Invalid characters <>;=#{}', 'mpbookingproduct');
        }
        if ($shortDescription) {
            $limit = (int) Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
            if ($limit <= 0) {
                $limit = 400;
            }
            if (!Validate::isCleanHtml($shortDescription)) {
                $this->errors[] = Tools::displayError($this->module->l('Invalid short description'), 'mpbookingproduct');
            }

            if (Tools::strlen(strip_tags($shortDescription)) > $limit) {
                $this->errors[] = sprintf(
                    $this->module->l('Short description field is too long: %1$d chars max (current count %2$d).', 'mpbookingproduct'),
                    $limit,
                    Tools::strlen(strip_tags($shortDescription))
                );
            }
        }
        if ($productDescription) {
            if (!Validate::isCleanHtml($productDescription, (int) Configuration::get('PS_ALLOW_HTML_IFRAME'))) {
                $this->errors[] = $this->module->l('Invalid product description', 'mpbookingproduct');
            }
        }
        if ($productPrice == '') {
            $this->errors[] = $this->module->l('Product price is required field.', 'mpbookingproduct');
        } elseif (!Validate::isPrice($productPrice)) {
            $this->errors[] = $this->module->l('Invalid product price', 'mpbookingproduct');
        }

        if ($productQuantity == '') {
            $this->errors[] = $this->module->l('Product quantity required field.', 'mpbookingproduct');
        } elseif (!Validate::isInt($productQuantity)) {
            $this->errors[] = $this->module->l('Invalid product quantity.', 'mpbookingproduct');
        }

        if (!$productCategory) {
            $this->errors[] = $this->module->l('Please select at least one category.', 'mpbookingproduct');
        }
    }

    // checks date range duplicacy of the time slots type products
    private function validateTimeSlotsDateRangesDuplicacy($currentDateFrom, $currentDateTo, $keyDateFrom)
    {
        $slotingDateFrom = Tools::getValue('sloting_date_from');
        $slotingDateTo = Tools::getValue('sloting_date_to');
        foreach ($slotingDateFrom as $key => $dateFrom) {
            $checkDateTo = $slotingDateTo[$key];
            if ($key == $keyDateFrom) {
                break;
            } else {
                if (!$currentDateFrom || !$checkDateTo || !$currentDateTo || !$dateFrom) {
                    $this->errors[] = $this->module->l('Dates can not be empty in the date ranges.', 'mpbookingproduct');
                } else {
                    if (strtotime($currentDateFrom) <= strtotime($checkDateTo) && strtotime($currentDateTo) >= strtotime($dateFrom)) {
                        $this->errors[] = $this->module->l('Duplicate date ranges data not saved.', 'mpbookingproduct');
                    }
                }
            }
        }
    }

    //When seller or admin change tax rule or price from add/update product page then display product price with Tax
    //included (Final price after Tax Incl.).
    public function displayAjaxChangeTaxRule()
    {
        if (!$this->isTokenValid()) {
            die('Something went wrong!');
        }
        WkMpSellerProduct::getMpProductTaxIncludedPrice();
    }

    public function displayAjaxGetDateRangeAvailableBookingSlots()
    {
        $dateFrom = Tools::getValue('date_from');
        $dateTo = Tools::getValue('date_to');
        $idBookingProductInfo = Tools::getValue('id_booking_product_info');
        $result = array();
        if (!$dateFrom) {
            $this->errors[] = $this->module->l('Invalid Date From.', 'mpbookingproduct');
        }
        if (!$dateTo) {
            $this->errors[] = $this->module->l('Invalid Date To.', 'mpbookingproduct');
        } elseif (strtotime($dateTo) < strtotime($dateFrom)) {
            $this->errors[] = $this->module->l('Date To must be date after Date From.', 'mpbookingproduct');
        }
        if (!$idBookingProductInfo) {
            $this->errors[] = $this->module->l('Product Id not found.', 'mpbookingproduct');
        }
        if (!count($this->errors)) {
            $objBookingSlots = new WkMpBookingProductTimeSlotPrices();
            $slotsInDateFrom = $objBookingSlots->getBookingProductTimeSlotsOnDate($idBookingProductInfo, $dateFrom);
            $slotsInDateTo = $objBookingSlots->getBookingProductTimeSlotsOnDate($idBookingProductInfo, $dateTo);
            if ($slotsInDateFrom && $slotsInDateTo) {
                if ($slotsInDateTo
                    && ($slotsInDateTo[0]['id_time_slots_price'] == $slotsInDateFrom[0]['id_time_slots_price'])
                ) {
                    $result['status'] = 'success';
                    $result['slots'] = $slotsInDateFrom;
                } else {
                    $result['status'] = 'success';
                    $result['slots'] = 'all';
                }
            } elseif ($slotsInDateFrom || $slotsInDateTo) {
                $result['status'] = 'success';
                $result['slots'] = 'all';
            } else {
                $result['status'] = 'success';
                $result['slots'] = 'no_slot';
            }
        } else {
            $result['status'] = 'failed';
            $result['errors'] = $this->errors;
        }
        die(json_encode($result));
    }

    public function displayAjaxValidateMpBookingProductForm()
    {
        $data = array('status' => 'ok');
        if (!$this->isTokenValid()) {
            die('Something went wrong!');
        }
        $params = array();
        parse_str(Tools::getValue('formData'), $params);
        // validate booking product information fields
        if (!empty($params)) {
            $objBookingProductInfo = new WkMpBookingProductInformation();
            $objBookingProductInfo->validationBookingProductFormFieldJs($params);
            if (isset($params['disable_special_days_active'])) {
                $objBookingDisableDates = new WkMpBookingProductDisabledDates();
                $objBookingDisableDates->validationDisableDatesFieldJs($params);
            }
        }
        die(Tools::jsonEncode($data));
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryUI('ui.datepicker');
        $this->addJqueryPlugin('tablednd');
        $this->addjQueryPlugin('growl', null, false);

        $this->context->controller->registerJavascript('mpbooking_timepicker-js', 'js/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js', ['position' => 'bottom', 'priority' => 1000]);
        //add marketplace module css
        $this->registerStylesheet('marketplace_accountcss', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerStylesheet('mp_header_style-css', 'modules/marketplace/views/css/mp_header.css');
        $this->registerStylesheet('mp_global_style-css', 'modules/marketplace/views/css/mp_global_style.css');

        $this->registerJavascript('mp-mp_form_validation', 'modules/marketplace/views/js/mp_form_validation.js');
        $this->registerJavascript('mp-change_multilang', 'modules/marketplace/views/js/change_multilang.js');
        if (Tools::getValue('id_mp_product')) {
            //Upload images
            $this->registerStylesheet('mp-filer-css', 'modules/marketplace/views/css/uploadimage-css/jquery.filer.css');
            $this->registerStylesheet('mp-filer-dragdropbox-theme-css', 'modules/marketplace/views/css/uploadimage-css/jquery.filer-dragdropbox-theme.css');
            $this->registerStylesheet('mp-uploadphoto-css', 'modules/marketplace/views/css/uploadimage-css/uploadphoto.css');
            $this->registerJavascript('mp-filer-js', 'modules/marketplace/views/js/uploadimage-js/jquery.filer.js');
            $this->registerJavascript('mp-uploadimage-js', 'modules/marketplace/views/js/uploadimage-js/uploadimage.js');
            //mpbooking module css and js
            $this->registerJavascript('mp-imageedit-js', 'modules/marketplace/views/js/imageedit.js');
            $this->registerStylesheet('wk-mp-booking-products', 'modules/'.$this->module->name.'/views/css/front/wk-mp-booking-products.css');
            $this->registerStylesheet('wk-timepicker-custom', 'modules/'.$this->module->name.'/views/css/front/wk-datetimepicker-custom.css');
            //Global Css
            $this->registerStylesheet(
                'wk-global-style',
                'modules/'.$this->module->name.'/views/css/wk-booking-global-style.css',
                array('position' => 'bottom', 'priority' => 0)
            );
            $this->registerStylesheet('wk-datepicker-custom', 'modules/'.$this->module->name.'/views/css/wk-datepicker-custom.css');
        }
        //mpbooking module css and js
        $this->registerJavascript('wk-mp-seller-booking-product', 'modules/'.$this->module->name.'/views/js/wk-mp-seller-booking-product.js');
        $this->registerJavascript('wk-mp-seller-booking-global', 'modules/'.$this->module->name.'/views/js/wk-mpbooking-global.js');

        //Category tree
        // $this->registerStylesheet('mp-categorytree-css', 'modules/marketplace/views/js/categorytree/themes/default/style.min.css');
        // $this->registerJavascript('mp-jstree-js', 'modules/marketplace/views/js/categorytree/jstree.min.js');
        // $this->registerJavascript('mp-wk_jstree-js', 'modules/marketplace/views/js/categorytree/wk_jstree.js');
		
		 /* crop & upload */
		 
        $this->addCSS( 'modules/marketplace/views/js/image-uploader/css/cropper.css'); 
        $this->addJS( 'modules/marketplace/views/js/image-uploader/js/cropper.js'); 
        $this->addJS( 'modules/marketplace/views/js/image-uploader/js/upload-cropped-image.js');   
    }
}
