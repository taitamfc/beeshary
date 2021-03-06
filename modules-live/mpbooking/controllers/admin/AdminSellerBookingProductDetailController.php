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

class AdminSellerBookingProductDetailController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->table = 'wk_mp_seller_product';
        $this->className = 'WkMpSellerProduct';
        $this->bootstrap = true;

        $this->_join .= ' JOIN `'._DB_PREFIX_.'wk_mp_booking_product_info` mpbi ON
        (mpbi.`id_mp_product` = a.`id_mp_product`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_product_lang` mspl ON
        (mspl.`id_mp_product` = a.`id_mp_product`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller` mpsi ON (mpsi.`id_seller` = a.`id_seller`)';
        $this->_select = '
            CONCAT(mpsi.`seller_firstname`, " ", mpsi.`seller_lastname`) as seller_name,
            a.`id_mp_product` as `seller_product_id`,
            mpsi.`shop_name_unique`,
            mspl.`id_lang`,
            mspl.`product_name`,
            a.`id_ps_product` as temp_ps_id, mpbi.`booking_type`';
        $this->_where = 'AND mspl.`id_lang` = '.(int) $this->context->language->id;

        $this->_group = 'GROUP BY mspl.`id_mp_product`';
        $this->identifier = 'id_mp_product';

        parent::__construct();

        $this->fields_list = array();
        $this->fields_list['id_mp_product'] = array(
            'title' => $this->l('ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
        );

        $this->fields_list['id_ps_product'] = array(
            'title' => $this->l('Prestashop Product ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'hint' => $this->l('Generated Prestashop ID in Catalog'),
            'callback' => 'prestashopDisplayId',
        );

        $this->fields_list['seller_product_id'] = array(
            'title' => $this->l('Image'),
            'callback' => 'displayProductImage',
            'search' => false,
            'havingFilter' => true,
        );

        $this->fields_list['product_name'] = array(
            'title' => $this->l('Product Name'),
        );

        $this->fields_list['seller_name'] = array(
            'title' => $this->l('Seller Name'),
            'havingFilter' => true,
        );

        $this->fields_list['shop_name_unique'] = array(
            'title' => $this->l('Unique Shop Name'),
            'havingFilter' => true,
        );

        $productBookingType = array(1 => 'Date Range', 2 => 'Time Slots');
        $this->fields_list['booking_type'] = array(
            'title' => $this->l('Product Booking Type'),
            'align' => 'center',
            'type' => 'select',
            'list' => $productBookingType,
            'filter_key' => 'mpbi!booking_type',
            'callback' => 'getBookingType',
        );

        $this->fields_list['active'] = array(
            'title' => $this->l('Status'),
            'active' => 'status',
            'type' => 'bool',
            'orderby' => false,
        );

        $this->fields_list['temp_ps_id'] = array(
            'title' => $this->l('Preview'),
            'align' => 'center',
            'search' => false,
            'remove_onclick' => true,
            'hint' => $this->l('Preview Activated Products Only'),
            'callback' => 'previewProduct',
            'orderby' => false,
        );

        $hookColumn = Hook::exec('addBkColumnSellerProductList');

        $i = 0;
        if ($hookColumn) {
            $column = explode('-', $hookColumn);
            $numColums = count($column);
            for ($i = 0; $i < $numColums; $i = $i + 2) {
                $this->fields_list[$column[$i]] = array(
                    'title' => $this->l($column[$i + 1]),
                    'align' => 'center',
                );
            }
        }

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
        );

        if ($wkErrorCode = Tools::getValue('wk_error_code')) {
            if ($wkErrorCode == 1) {
                $this->errors[] = Tools::displayError($this->l('Their is some error to map marketplace product.'));
            } elseif ($wkErrorCode == 2) {
                $this->errors[] = Tools::displayError($this->l('Can not able to create product in prestashop catalog.'));
            }
        }
    }

    public function getBookingType($echo)
    {
        if ($echo == 1) {
            $return = $this->l('Date Range');
        } else {
            $return = $this->l('Time Slots');
        }

        return $return;
    }

    public function prestashopDisplayId($idPsProduct)
    {
        if ($idPsProduct) {
            return $idPsProduct;
        } else {
            return '-';
        }
    }

    public function displayProductImage($idMpProduct, $rowData)
    {
        if ($rowData['id_ps_product']) { // if product activated
            $idPsProduct = $rowData['id_ps_product'];
            $objProduct = new Product($idPsProduct, false, $this->context->language->id);
            $cover = Product::getCover($idPsProduct);

            $imagesTypes = ImageType::getImagesTypes('products');
            if ($cover) {
                $coverImage = $idPsProduct.'-'.$cover['id_image'];
                foreach ($imagesTypes as $imageType) {
                    $src = $this->context->link->getImageLink(
                        $objProduct->link_rewrite,
                        $coverImage,
                        $imageType['name']
                    );
                    if ($src) {
                        return '<img class="img-thumbnail" width="45" height="45" src="'.
                        $this->context->link->getImageLink($objProduct->link_rewrite, $coverImage, $imageType['name']).
                        '">';
                    }
                }
            }
        } else { //if product not active
            $unactiveImage = WkMpSellerProduct::getInactiveProductImageByIdProduct($idMpProduct);
            if ($unactiveImage) {
                return '<img class="img-thumbnail" width="45" height="45" src="'._MODULE_DIR_.
                'marketplace/views/img/product_img/'.$unactiveImage[0]['seller_product_image_name'].'">';
            }
        }

        return '<img class="img-thumbnail" width="45" height="45" src="'._MODULE_DIR_.
        '/marketplace/views/img/home-default.jpg">';
    }

    public function previewProduct($id, $rowData)
    {
        if ($id && $rowData['active']) {
            $productLink = $this->context->link->getProductLink(
                (int) $id,
                null,
                null,
                null,
                (int) $this->context->language->id
            );
            return '<span class="btn-group-action"><span class="btn-group">
                        <a target="_blank" class="btn btn-default" href="'.$productLink.'">
                        <i class="icon-eye"></i>&nbsp;'.$this->l('Preview').'</a>
                    </span>
                </span>';
        }
    }

    public function initToolbar()
    {
        if (WkMpSeller::getAllSeller()) {
            parent::initToolbar();
            $this->page_header_toolbar_btn['new'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->l('Add new product'),
            );
        }
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function postProcess()
    {
        if (!$this->loadObject(true)) {
            return;
        }
        // send reason for deactivating product
        if ($idProductForReason = Tools::getValue('actionId_for_reason')) {
            $msg = trim(Tools::getValue('reason_text'));
            if (!$msg) {
                $msg = $this->l('Admin has deactivated your product.');
            }
            $this->activeSellerProduct($idProductForReason, $msg);
            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=5');
        }

        if (Tools::isSubmit('status'.$this->table)) {
            $this->activeSellerProduct();
        }

        parent::postProcess();
    }

    public function renderForm()
    {
        $dateFrom = date('d-m-Y');
        $dateTo = date('d-m-Y', strtotime("+1 day", strtotime($dateFrom)));

        //tinymce setup
        $jsDef = array();
        $smartyVars['path_css'] =  _THEME_CSS_DIR_;
        $smartyVars['ad'] =  __PS_BASE_URI__.basename(_PS_ADMIN_DIR_);
        $smartyVars['autoload_rte'] =  true;
        $smartyVars['lang'] =  true;
        $smartyVars['iso'] =  $this->context->language->iso_code;

        $objMpProduct = new WkMpSellerProduct();
        $mpIdSeller = 0;
        $idCategory = array(Category::getRootCategory()->id); //home category id
        $defaultCategory = Category::getCategoryInformations($idCategory, $this->context->language->id);
        if ($this->display == 'add') {
            $customerInfo = WkMpSeller::getAllSeller();
            if ($customerInfo) {
                $this->context->smarty->assign('customer_info', $customerInfo);
                //get first seller from the list
                $firstSellerDetails = $customerInfo[0];
                $mpIdSeller = $firstSellerDetails['id_seller'];
            } else {
                $mpIdSeller = 0;
            }
            $idCategory = array(Category::getRootCategory()->id); //home category id
            $defaultCategory = Category::getCategoryInformations($idCategory, $this->context->language->id);
        } elseif ($this->display == 'edit') {
            $idMpProduct = Tools::getValue('id_mp_product');
            // dump($_POST);die;
            if ($idMpProduct) {
                if (Validate::isLoadedObject($objMpProduct = new WkMpSellerProduct($idMpProduct))) {
                    if ($mpProductInfo = (array) $objMpProduct) {
                        $mpIdSeller = $mpProductInfo['id_seller'];
                        // Category tree
                        $objMpProductCategory = new WkMpSellerProductCategory();
                        $defaultIdCategory = $objMpProductCategory->getSellerProductDefaultCategory($idMpProduct);

                        $idCategory = array();
                        $checkedProductCategory = $objMpProduct->getSellerProductCategories($idMpProduct);
                        if ($checkedProductCategory) {
                            // Default category
                            foreach ($checkedProductCategory as $checkIdCategory) {
                                $idCategory[] = $checkIdCategory['id_category'];
                            }

                            $catIdsJoin = implode(',', $idCategory);
                            $smartyVars['catIdsJoin'] =  $catIdsJoin;
                        }

                        $defaultCategory = Category::getCategoryInformations($idCategory, $this->context->language->id);

                        //Assign and display product active/inactive images
                        WkMpSellerProductImage::getProductImageDetails($idMpProduct);
                        $objBookingProductInfo = new WkMpBookingProductInformation();
                        if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(
                            $idMpProduct,
                            $mpProductInfo['id_ps_product']
                        )) {
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

                            //set calendar data
                            $calendarHelper = new HelperCalendar();
                            $calendarHelper->setDateFrom(date('Y-m-d')); // current date
                            $calendarHelper->setDateTo(date('Y-m-d', strtotime("+1 day", strtotime(date('Y-m-d'))))); // next date
                            // So that compare dates options do not show on the calendar
                            $calendarHelper->setCompareDateFrom(null);
                            $calendarHelper->setCompareDateTo(null);
                            $calendarHelper->setCompareOption(null);

                            $smartyVars['calendar'] = $calendarHelper->generate(); // send calendar view to the tpl
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
                                    $this->errors[] = $this->l('Date From is required field.');
                                }
                                if ($availablityDateTo == '') {
                                    $this->errors[] = $this->l('Date To is required field.');
                                }
                                if ($availablityDateTo < $availablityDateFrom) {
                                    $this->errors[] = $this->l('Date To should be greater than Date From.');
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
                            $smartyVars['productBookingType'] = $bookingProductInfo['booking_type'];
                            $jsDef['booking_type'] = $bookingProductInfo['booking_type'];
                            $jsDef['bookingCalendarData'] = $bookingCalendarData;
                            $jsDef['calendarDate'] = date("d-m-Y", strtotime($availablityDateFrom));
                            //End (Availability & Rates Tab)
                        }
                        //set calendar data
                        $calendarHelper = new HelperCalendar();
                        $calendarHelper->setDateFrom(date('Y-m-d')); // current date
                        $calendarHelper->setDateTo(date('Y-m-d', strtotime("+1 day", strtotime(date('Y-m-d'))))); // next date

                        // So that compare dates options do not show on the calendar
                        $calendarHelper->setCompareDateFrom(null);
                        $calendarHelper->setCompareDateTo(null);
                        $calendarHelper->setCompareOption(null);
                        $smartyVars['calendar'] = $calendarHelper->generate(); //send calendar view to the tpl
                        $smartyVars['product_info'] = $mpProductInfo;
                        $smartyVars['id_tax_rules_group'] = $mpProductInfo['id_tax_rules_group'];
                        $smartyVars['idMpProduct'] = $idMpProduct;
                        $smartyVars['edit'] = 1;
                        $smartyVars['defaultCategory'] = $defaultCategory;
                        $smartyVars['defaultIdCategory'] = $defaultIdCategory;
                    }
                }
            }
        }
        Media::addJsDef($jsDef);

        // Set default lang at every form according to configuration multi-language
        WkMpHelper::assignDefaultLang($mpIdSeller);

        WkMpHelper::defineGlobalJSVariables(); // Define global js variable on js file

        //show tax rule group on add product page
        $taxRuleGroups = TaxRulesGroup::getTaxRulesGroups(true);
        if ($taxRuleGroups) {
            $smartyVars['tax_rules_groups'] = $taxRuleGroups;
        }

        $objProduct = new Product();
        $objDefaultCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $smartyVars['defaultTaxRuleGroup'] = $objProduct->getIdTaxRulesGroup();
        $smartyVars['mp_seller_applied_tax_rule'] = 1;
        $smartyVars['defaultCurrencySign'] = $objDefaultCurrency->sign;
        $smartyVars['ps_modules_dir'] = _PS_MODULE_DIR_;
        $smartyVars['modules_dir'] = _MODULE_DIR_;
        $smartyVars['ps_img_dir'] = _PS_IMG_.'l/';
        $smartyVars['mp_image_dir'] = _MODULE_DIR_.'marketplace/views/img/';
        $smartyVars['defaultCategory'] = $defaultCategory;
        $smartyVars['self'] = dirname(__FILE__);
        $smartyVars['date_from'] = $dateFrom;
        $smartyVars['date_to'] = $dateTo;
        $smartyVars['booking_type_time_slot'] = WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT;
        $smartyVars['booking_type_date_range'] = WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE;

        $this->context->smarty->assign($smartyVars);
        $this->fields_form = array(
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        return parent::renderForm();
    }

    public function processSave()
    {
        $productQuantity = Tools::getValue('quantity');
        $minimalQuantity = Tools::getValue('minimal_quantity');
        $productCondition = Tools::getValue('condition');

        $productPrice = Tools::getValue('price');

        $defaultCategory = Tools::getValue('default_category');
        $productCategory = Tools::getValue('product_category');

        $idMpProduct = Tools::getValue('id_mp_product'); //if edit
        $idTaxRulesGroup = Tools::getValue('id_tax_rules_group');
        $sellerDefaultLanguage = Tools::getValue('seller_default_lang');
        $defaultLang = WkMpHelper::getDefaultLanguageBeforeFormSave($sellerDefaultLanguage);
        $reference = Tools::getValue('reference');
		
		/* adding new fields */
		$activity_addr = strip_tags(Tools::getValue('activity_addr'));
		$activity_city = strip_tags(Tools::getValue('activity_city'));
		$activity_postcode = strip_tags(Tools::getValue('activity_postcode'));
		$activity_period = strip_tags(Tools::getValue('activity_period'));
		$activity_curious = Tools::getValue('activity_curious');
		$activity_participants = Tools::getValue('activity_participants');
		$latitude = Tools::getValue('latitude');
		$longitude = Tools::getValue('longitude');
		$video_link = Tools::getValue('video_link');
		/* adding new fields */
		
        //Product Name Validate
        if (!Tools::getValue('product_name_'.$defaultLang)) {
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $sellerLang = Language::getLanguage((int) $defaultLang);
                $this->errors[] = $this->l('Product name is required in ').$sellerLang['name'];
            } else {
                $this->errors[] = $this->l('Product name is required');
            }
        } else {
            // Validate form
            $this->errors = WkMpSellerProduct::validateMpProductForm();
            if (!$this->errors) {
                $this->errors = array();
            }
        }

        if ($idMpProduct) {
            Hook::exec('actionBkBeforeUpdateMPProduct', array('id_mp_product' => $idMpProduct));
        } else {
            $idSeller = Tools::getValue('id_seller');
            Hook::exec('actionBkBeforeAddMPProduct', array('id_seller' => $idSeller));
        }

        if (empty($this->errors)) {
            $psDefaultLangProName = '';
            $productCategory = explode(',', $productCategory);

            if ($idMpProduct) { //if update product
                $objSellerProduct = new WkMpSellerProduct($idMpProduct);
            } else { //if add new product
                $objSellerProduct = new WkMpSellerProduct();
            }

            $objSellerProduct->quantity = '99999999999';
            $objSellerProduct->minimal_quantity = $minimalQuantity;
            $objSellerProduct->id_category = $defaultCategory;
            $objSellerProduct->condition = $productCondition;

            //Pricing
            $objSellerProduct->price = $productPrice;
            $objSellerProduct->id_tax_rules_group = $idTaxRulesGroup;

            $objSellerProduct->reference = $reference ? $reference : '';

            if (!$idMpProduct) { //add product
                $objSellerProduct->id_seller = $idSeller;
                $objSellerProduct->id_ps_shop = $this->context->shop->id;
                $objSellerProduct->id_ps_product = 0;
                $objSellerProduct->active = Tools::getValue('product_active');
                $objSellerProduct->admin_approved = Tools::getValue('product_active');
            }

            foreach (Language::getLanguages(false) as $language) {
                $productIdLang = $language['id_lang'];
                $shortDescIdLang = $language['id_lang'];
                $descIdLang = $language['id_lang'];
                $availableNowIdLang = $language['id_lang'];
                $availableLaterIdLang = $language['id_lang'];
                $metaTitleIdLang = $language['id_lang'];
                $metaDescriptionIdLang = $language['id_lang'];

                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    //if product name in other language is not available then fill with seller language same for others
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

                if (!$idMpProduct && Configuration::get('PS_LANG_DEFAULT') == $language['id_lang']) { //add product
                    $psDefaultLangProName = Tools::getValue('product_name_'.$productIdLang);
                }

                $objSellerProduct->product_name[$language['id_lang']] = Tools::getValue('product_name_'.$productIdLang);

                $objSellerProduct->short_description[$language['id_lang']] = Tools::getValue(
                    'short_description_'.$shortDescIdLang
                );

                $objSellerProduct->description[$language['id_lang']] = Tools::getValue('description_'.$descIdLang);

                //Friendly URL
                if (Tools::getValue('link_rewrite_'.$language['id_lang'])) {
                    $objSellerProduct->link_rewrite[$language['id_lang']] = Tools::link_rewrite(
                        Tools::getValue('link_rewrite_'.$language['id_lang'])
                    );
                } else {
                    $objSellerProduct->link_rewrite[$language['id_lang']] = Tools::link_rewrite(
                        Tools::getValue('product_name_'.$productIdLang)
                    );
                }
            }
            if ($objSellerProduct->save()) {
                $psProductId = $objSellerProduct->id_ps_product;
                if ($idMpProduct) { //update product
                    $objMpCategory = new WkMpSellerProductCategory();
                    // for Updating new categories first delete previous category
                    $objMpCategory->deleteProductCategory($idMpProduct);

                    //Add new category into table
                    $this->assignMpProductCategory($productCategory, $idMpProduct, $defaultCategory);

                    if ($objSellerProduct->active && $objSellerProduct->id_ps_product) {
                        if ($psProductId = $objSellerProduct->updateSellerProductToPs($idMpProduct, 1)) {
                            $objPsProduct = new Product($psProductId);
                            $objPsProduct->is_virtual = 1;
                            $objPsProduct->save();
                        }
                    }
                    $objBookingProductInfo = new WkMpBookingProductInformation();
                    $bookingProductInfo = $objBookingProductInfo->getBookingProductInfo($idMpProduct, $psProductId);
                    if ($bookingProductInfo) {
                        $objBookingProductInfo = new WkMpBookingProductInformation(
                            $bookingProductInfo['id_booking_product_info']
                        );
                    }
                    Hook::exec(
                        'actionBkAfterUpdateMPProduct',
                        array('id_mp_product' => $idMpProduct, 'id_mp_product_attribute' => 0)
                    );
                } else { //add product
                    $idMpProduct = $objSellerProduct->id;

                    //Add into category table
                    $this->assignMpProductCategory($productCategory, $idMpProduct, $defaultCategory);

                    //if default approval on, then entry of a product in ps_product table
                    if (Tools::getValue('product_active')) {
                        // creating ps_product when admin setting is default
                        $psProductId = $objSellerProduct->addSellerProductToPs($idMpProduct, 1);
                        if ($psProductId) {
                            $objSellerProduct->id_ps_product = $psProductId;
                            $objSellerProduct->save();
                            $objPsProduct = new Product($psProductId);
                            $objPsProduct->is_virtual = 1;
                            $objPsProduct->save();
                        }
                        WkMpSellerProduct::sendMail($idMpProduct, 1, 1);
                    }
                    // adding product feature into marketplace table
                    if (Configuration::get('WK_MP_MAIL_ADMIN_PRODUCT_ADD')) {
                        $sellerDetail = WkMpSeller::getSeller($idSeller, Configuration::get('PS_LANG_DEFAULT'));
                        if ($sellerDetail) {
                            $sellerName = $sellerDetail['seller_firstname'].' '.$sellerDetail['seller_lastname'];
                            $shopName = $sellerDetail['shop_name'];
                            $objSellerProduct->mailToAdminOnProductAdd(
                                $psDefaultLangProName,
                                $sellerName,
                                $sellerDetail['phone'],
                                $shopName,
                                $sellerDetail['business_email']
                            );
                        }
                    }
                    Hook::exec('actionBkAfterAddMPProduct', array('id_mp_product' => $idMpProduct));
                    $objBookingProductInfo = new WkMpBookingProductInformation();
                }
                if ($idMpProduct) {
                    // save booking product Info in our table
                    $objBookingProductInfo->id_seller = $objSellerProduct->id_seller;
                    $objBookingProductInfo->id_product = $objSellerProduct->id_ps_product;
                    $objBookingProductInfo->id_mp_product = $idMpProduct;
                    $objBookingProductInfo->quantity = $productQuantity;
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
                                $this->errors[] = $this->l('Please select at least one valid date range for time slots.');
                            }
                            if (!$idMpProduct) {
                                $this->errors[] = $this->l('Booking product id is missing to create time slots.');
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
                                                                            $this->errors[] = $this->l('Duplicate time
                                                                            slots data not saved.');
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
                                                                            $this->errors[] = $this->l('Time Slot ').$timeFrom.$this->l(' to ').$bookingTimeTo[$keyTimeFrom].$this->l(' for the date range ').date('Y-m-d', strtotime($dateFrom)).$this->l(' To ').date('Y-m-d', strtotime($slotingDatesTo[$keyDateFrom])).$this->l(' not saved because of invalid price : ').$slotRangePrice[$keyTimeFrom];
                                                                        }
                                                                    } else {
                                                                        $this->errors[] = $this->l('Time Slot ').$timeFrom.$this->l(' to ').$bookingTimeTo[$keyTimeFrom].$this->l(' for the date range ').date('Y-m-d', strtotime($dateFrom)).$this->l(' To ').date('Y-m-d', strtotime($slotingDatesTo[$keyDateFrom])).$this->l(' not saved because of invalid time slots');
                                                                    }
                                                                } else {
                                                                    $this->errors[] = $this->l('Time Slot not saved because
                                                                    of missing info of time slots');
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    $this->errors[] = $this->l('Date from can not be after date to while
                                                    adding time slots.');
                                                }
                                            } else {
                                                $invalidRange = 1;
                                            }
                                        }
                                    }
                                } else {
                                    $this->errors[] = $this->l('Some error occurred while saving time slots info.');
                                }
                                if ($invalidRange) {
                                    $this->errors[] = $this->l('Invalid date ranges were not saved.');
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
                                $this->errors[] = $this->l('if Disable Special Days option is active, Please select at
                                least one special day to disable.');
                            }
                        }
                        if ($toDisableDates) {
                            if (!$disabledSpecificDatesJson || !count(json_decode($disabledSpecificDatesJson, true))) {
                                $this->errors[] = $this->l('if Disable Specific Dates option is active, Please select
                                at least one date to disable.');
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
                                    $this->errors[] = $this->l('Some error has been occurred while saving disable dates info.');
                                }
                            }
                        }
                    }
                }
                if (empty($this->errors)) {
                    if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                        if ($idMpProduct) {
                            Tools::redirectAdmin(
                                self::$currentIndex.'&id_mp_product='.(int) $idMpProduct.'&update'.
                                $this->table.'&conf=4&tab='.Tools::getValue('active_tab').'&token='.$this->token
                            );
                        } else {
                            Tools::redirectAdmin(
                                self::$currentIndex.'&id_mp_product='.(int) $sellerProductId.'&update'.$this->table.
                                '&conf=3&tab='.Tools::getValue('active_tab').'&token='.$this->token
                            );
                        }
                    } else {
                        if ($idMpProduct) {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                        } else {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                        }
                    }
                } else {
                    // if product is saved but some errors are occurred while saving time slots information
                    $this->confirmations[] = $this->l('Product has been saved successfully. But above errors were
                    occurred while saving the booking information of the product.');
                }
            }
        }
        if ($idMpProduct) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }
    }

    private function defineJSVars()
    {
        $objDefaultCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $jsVars = array(
            'back_end' => 1,
            'is_need_reason' => Configuration::get('WK_MP_SELLER_PRODUCTS_DEACTIVATE_REASON'),
            'path_addfeature' => $this->context->link->getAdminlink('AdminSellerBookingProductDetail'),
            'path_sellerproduct' => $this->context->link->getAdminLink('AdminSellerBookingProductDetail'),
            'defaultCurrencySign' => $objDefaultCurrency->sign,
            'adminController' => 1,
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
                    'wk_image_dir' => _MODULE_DIR_.$this->module->name.'/views/img/',
                    'actionpage' => 'product',
                    'defaultIdCategory' => $defaultIdCategory,
                    'adminupload' => 1,
                    'backend_controller' => 1,
                    'actionIdForUpload' => $idMpProduct,
                    'deleteaction' => 'jFiler-item-trash-action',
                    'imageUploader' => $this->context->link->getAdminlink('AdminSellerProductDetail'),
                    'path_uploader' => $this->context->link->getAdminLink('AdminSellerProductDetail'),
                    'ajax_urlpath' => $this->context->link->getAdminLink('AdminSellerProductDetail'),
                    'confirm_delete_msg' => $this->l('Are you sure you want to delete this image?'),
                    'drag_drop' => $this->l('Drag & Drop to Upload'),
                    'or' => $this->l('or'),
                    'pick_img' => $this->l('Pick Image'),
                    'delete_msg' => $this->l('Deleted.'),
                    'error_msg' => $this->l('An error occurred.'),
                    'choosefile' => $this->l('Choose Images'),
                    'choosefiletoupload' => $this->l('Choose Images To Upload'),
                    'imagechoosen' => $this->l('Images were chosen'),
                    'dragdropupload' => $this->l('Drop file here to Upload'),
                    'only' => $this->l('Only'),
                    'imagesallowed' => $this->l('Images are allowed to be uploaded.'),
                    'onlyimagesallowed' => $this->l('Only Images are allowed to be uploaded.'),
                    'imagetoolarge' => $this->l('is too large! Please upload image up to'),
                    'imagetoolargeall' => $this->l('Images you have choosed are too large! Please upload images up to'),

                    // vars for booking product actions
                    'slot_text' => $this->l('Slot'),
                    'avl_qty_txt' => $this->l('Avail qty'),
                    'price_txt' => $this->l('Price'),
                    'booked_qty_txt' => $this->l('Booked'),
                    'status_txt' => $this->l('Status'),
                    'day_text' => $this->l('day'),
                    'add_more_slots_txt' => $this->l('Add More Slots'),
                    'no_info_found_txt' => $this->l('No Information Found.'),
                    'no_slots_avail_txt' => $this->l('No time slot available.'),
                    'date_to_more_date_from_err' => $this->l('date to must be greater than date from.'),
                    'date_from_req' => $this->module->l('Date from is missing.', 'mpbookingproduct'),
                    'date_to_req' => $this->l('Date to is missing.'),
                    'to_txt' => $this->l('To'),
                    'date_range_already_added' => $this->module->l('Disable date range already added.'),
                    'all_slots_disable_warning' => $this->l('In selected date range time slots of more than one date
                    ranges are there. So in this case all the time slots will be disabled in this date range.'),
                    'date_from_less_current_date_err' => $this->l('date from can not be before current date.'),
                    'no_slot_selected_err' => $this->l('No slots selected. Please select at least one slot.'),
                    'update_success' => $this->l('Updated Successfully'),
                    'success_msg' => $this->l('Success'),
                )
            );
        }
        Media::addJsDef($jsVars);
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

    public function assignMpProductCategory($productCategory, $mpIdProduct, $defaultCategory)
    {
        if (!is_array($productCategory)) {
            return false;
        }

        $objSellerProductCategory = new WkMpSellerProductCategory();
        $objSellerProductCategory->id_seller_product = $mpIdProduct;

        foreach ($productCategory as $categoryVal) {
            $objSellerProductCategory->id_category = $categoryVal;

            if ($categoryVal == $defaultCategory) {
                $objSellerProductCategory->is_default = 1;
            } else {
                $objSellerProductCategory->is_default = 0;
            }
            $objSellerProductCategory->add();
        }
    }

    public function processStatus()
    {
        if (empty($this->errors)) {
            parent::processStatus();
        }
    }

    public function activeSellerProduct($mpProductId = false, $reasonText = false)
    {
        $psProductId = 0;
        if (!$mpProductId) {
            $mpProductId = Tools::getValue('id_mp_product');
        }
        Hook::exec('actionBkBeforeToggleMPProductStatus', array('id_mp_product' => $mpProductId));
        if (!count($this->errors)) {
            $objMpProduct = new WkMpSellerProduct($mpProductId);

            if ($objMpProduct->active) { // going to be deactive
                //product created but deactive now
                $objMpProduct->active = 0;
                $objMpProduct->status_before_deactivate = 0;
                $objMpProduct->save();

                //Update id_image as mp_image_id when product is going to deactivate
                WkMpProductAttributeImage::setCombinationImagesAsMp($mpProductId);

                if ($objMpProduct->id_ps_product) {
                    $psProductId = $objMpProduct->id_ps_product;
                    $product = new Product($psProductId);
                    $product->active = 0;
                    $product->is_virtual = 1;
                    $product->save();
                }
                WkMpSellerProduct::sendMail($mpProductId, 2, 2, $reasonText);
            } else {
                $objMpSeller = new WkMpSeller($objMpProduct->id_seller);
                if ($objMpSeller->active) { //if seller is active
                    // going to be active
                    if ($objMpProduct->id_ps_product) {
                        //product created but dactivated right now, need to active
                        $objMpProduct->active = 1;
                        $objMpProduct->status_before_deactivate = 1;
                        $objMpProduct->admin_approved = 1;
                        $objMpProduct->save();
                        $objMpProduct->updateSellerProductToPs($mpProductId, 1);
                        $psProductId = $objMpProduct->id_ps_product;

                        $objMpProductAttribute = new WkMpProductAttribute();
                        $objMpProductAttribute->updateMpProductCombinationToPs($mpProductId, $psProductId);
                    } else {
                        //not yet product created, first time activated
                        $idProduct = $objMpProduct->addSellerProductToPs($mpProductId, 1);
                        if ($idProduct) {
                            $psProductId = $idProduct;
                            $product = new Product($psProductId);
                            $product->is_virtual = 1;
                            $product->save();

                            $objMpProduct->active = 1;
                            $objMpProduct->status_before_deactivate = 1;
                            $objMpProduct->admin_approved = 1;
                            $objMpProduct->id_ps_product = $psProductId;
                            $objMpProduct->save();

                            $objBookingProductInfo = new WkMpBookingProductInformation();
                            if ($bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(
                                $mpProductId,
                                0
                            )) {
                                $objBookingProductInfo = new WkMpBookingProductInformation(
                                    $bookingProductInfo['id_booking_product_info']
                                );
                                // save booking product Info in our table
                                $objBookingProductInfo->id_seller = $objMpProduct->id_seller;
                                $objBookingProductInfo->id_product = $objMpProduct->id_ps_product;
                                $objBookingProductInfo->id_mp_product = $mpProductId;
                                $objBookingProductInfo->active = $objMpProduct->active;
                                $objBookingProductInfo->save();
                            }
                            Hook::exec('actionBkToogleMPProductCreateStatus', array('id_product' => $idProduct, 'active' => 1));
                        } else {
                            Tools::redirectAdmin(self::$currentIndex.'&wk_error_code=2&token='.$this->token);
                        }
                    }
                    Hook::exec('actionBkToogleMPProductActive', array('id_mp_product' => $mpProductId, 'active' => $objMpProduct->active));
                    WkMpSellerProduct::sendMail($mpProductId, 1, 1);
                } else {
                    $this->context->controller->errors[] = sprintf($this->l('You can not activate this product because shop %s is not active right now.'), $objMpSeller->shop_name_unique);
                }
            }
            Hook::exec('actionBkAfterToggleMPProductStatus', array('id_product' => $psProductId, 'active' => $objMpProduct->active));
        }
    }

    protected function processBulkEnableSelection()
    {
        return $this->processBulkStatusSelection(1);
    }

    protected function processBulkDisableSelection()
    {
        return $this->processBulkStatusSelection(0);
    }

    protected function processBulkStatusSelection($status)
    {
        if ($status == 1) {
            if (is_array($this->boxes) && !empty($this->boxes)) {
                foreach ($this->boxes as $id) {
                    $objSellerProduct = new WkMpSellerProduct($id);
                    if ($objSellerProduct->active == 0) {
                        $this->activeSellerProduct($id);
                    }
                }
            }
        } elseif ($status == 0) {
            if (is_array($this->boxes) && !empty($this->boxes)) {
                foreach ($this->boxes as $id) {
                    $objSellerProduct = new WkMpSellerProduct($id);
                    if ($objSellerProduct->active == 1) {
                        $this->activeSellerProduct($id);
                    }
                }
            }
        }
    }

    public function ajaxProcessDeleteUnactiveImage()
    {
        //Delete unactive images
        $idImage = Tools::getValue('id_image');
        $imageName = Tools::getValue('img_name');

        $delete = WkMpSellerProductImage::deleteProductImageByMpProductImageName($idImage, $imageName);
        if ($delete) {
            $productImgPath = _PS_MODULE_DIR_.$this->module->name.'/views/img/product_img/';
            @unlink($productImgPath.$imageName);
            die('1');
        } else {
            die('2');
        }
    }

    public function ajaxProcessDeleteActiveImage()
    {
        //Delete active images from the images
        $idImage = Tools::getValue('id_image');
        $idProduct = Tools::getValue('id_pro');
        $isCover = Tools::getValue('is_cover');
        $objImage = new Image($idImage);
        if ($objImage->delete()) {
            WkMpSellerProduct::deleteSellerProductImage(false, false, $idImage);

            Product::cleanPositions($idProduct);
            // if cover image deleting, make first image as a cover
            if ($isCover) {
                $images = Image::getImages($this->context->language->id, $idProduct);
                if ($images) {
                    $objImage = new Image($images[0]['id_image']);
                    $objImage->cover = 1;
                    $objImage->save();
                }
                die('2');
            } else {
                die('1');
            }
        } else {
            die('3');
        }
    }

    public function ajaxProcessChangeImageCover()
    {
        //Change cover image in product images
        $idImage = Tools::getValue('id_image');
        if (isset($idImage) && $idImage) {
            $idProduct = Tools::getValue('id_pro');
            Image::deleteCover((int) $idProduct);
            $img = new Image((int) $idImage);
            $img->cover = 1;
            // unlink existing cover image in temp folder
            @unlink(_PS_TMP_IMG_DIR_.'product_'.(int) $img->id_product);
            @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int) $img->id_product.'_'.$this->context->shop->id);
            if ($img->update()) {
                die('1');
            } else {
                die('0');
            }
        } else {
            die('2');
        }
    }

    public function ajaxProcessfindSellerDefaultLang()
    {
        //Get seller default langauge
        $mpIdCustomer = Tools::getValue('customer_id');
        $mpSellerInfo = WkMpSeller::getSellerDetailByCustomerId($mpIdCustomer);
        if ($mpSellerInfo) {
            $sellerLanguageData = Language::getLanguage((int) $mpSellerInfo['default_lang']);
            die(Tools::jsonEncode($sellerLanguageData)); //close ajax
        }
    }

    public function ajaxProcessUploadimage()
    {
        //Update product image
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

    public function ajaxProcessChangeTaxRule()
    {
        //When seller or admin change tax rule or price from add/update product page then display product price with Tax included (Final price after Tax Incl.)
        WkMpSellerProduct::getMpProductTaxIncludedPrice();
    }

    public function ajaxProcessProductCategory()
    {
        //Load Prestashop category with ajax load of plugin jstree
        WkMpSellerProduct::getMpProductCategory();
    }

    public function ajaxProcessUpdateDefaultAttribute()
    {
        //Update default combination for seller product
        WkMpProductAttribute::updateMpProductDefaultAttribute();
    }

    public function ajaxProcessDeleteMpCombination()
    {
        //Delete Product combination from combination list at edit product page
        WkMpProductAttribute::deleteMpProductAttribute();
    }

    public function ajaxProcessChangeCombinationStatus()
    {
        //Change combination status through ajaxProcess if combination activate/deactivate module is enabled
        WkMpProductAttribute::changeCombinationStatus();
    }

    public function ajaxProcessAddMoreFeature()
    {
        $mpSeller = WkMpSeller::getSellerDetailByCustomerId(Tools::getValue('idSeller'));
        WkMpHelper::assignDefaultLang($mpSeller['id_seller']);
        $sellerDefaultLanguage = Tools::getValue('sellerDefaultLang');
        if ($sellerDefaultLanguage) {
            $defaultLang = WkMpHelper::getDefaultLanguageBeforeFormSave($sellerDefaultLanguage);
            $this->context->smarty->assign(
                array(
                    'current_lang' => Language::getLanguage((int) $defaultLang),
                    'default_lang' => $defaultLang,
                )
            );
        }
        $this->context->smarty->assign(
            array(
                'ps_img_dir' => _PS_IMG_.'l/',
                'controller' => 'admin',
                'fieldrow' => Tools::getValue('fieldrow'),
                'choosedLangId' => Tools::getValue('choosedLangId'),
                'available_features' => Feature::getFeatures($this->context->language->id, (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP)),
            )
        );
        die(
            $this->context->smarty->fetch(
                _PS_MODULE_DIR_.'marketplace/views/templates/front/product/_partials/more-product-feature.tpl'
            )
        );
    }

    public function ajaxProcessGetFeatureValue()
    {
        $featuresValue = FeatureValue::getFeatureValuesWithLang(
            $this->context->language->id,
            (int) Tools::getValue('idFeature')
        );
        if (!empty($featuresValue)) {
            die(Tools::jsonEncode($featuresValue));
        }
        die(false);
    }

    public function ajaxProcessValidateMpForm()
    {
        $data = array('status' => 'ok');
        $params = array();
        parse_str(Tools::getValue('formData'), $params);
        if (!empty($params)) {
            WkMpSellerProduct::validationProductFormField($params);

            // if features are enable or seller is trying to add features
            if (isset($params['wk_feature_row'])) {
                WkMpProductFeature::checkFeatures($params);
            }
        }
        die(Tools::jsonEncode($data));
    }

    public function ajaxProcessGetDateRangeAvailableBookingSlots()
    {
        $dateFrom = Tools::getValue('date_from');
        $dateTo = Tools::getValue('date_to');
        $idBookingProductInfo = Tools::getValue('id_booking_product_info');
        $result = array();
        if (!$dateFrom) {
            $this->errors[] = $this->l('Invalid Date From.');
        }
        if (!$dateTo) {
            $this->errors[] = $this->l('Invalid Date To.');
        } elseif (strtotime($dateTo) < strtotime($dateFrom)) {
            $this->errors[] = $this->l('Date To must be date after Date From.');
        }
        if (!$idBookingProductInfo) {
            $this->errors[] = $this->l('Product Id not found.');
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

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme = false);
        $this->addJS(_MODULE_DIR_.'marketplace/views/js/sellerprofile.js');
        if (isset($this->display)) {
            $this->addJqueryPlugin(array('fancybox', 'tablednd'));

            $this->addCSS(_MODULE_DIR_.'marketplace/views/css/mp_global_style.css');
            $this->addJS(_MODULE_DIR_.'marketplace/views/js/mp_form_validation.js');
            $this->addJS(_MODULE_DIR_.'marketplace/views/js/change_multilang.js');

            //tinymce
            $this->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
            if (version_compare(_PS_VERSION_, '1.6.0.11', '>')) {
                $this->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
            } else {
                $this->addJS(_PS_JS_DIR_.'tinymce.inc.js');
            }

            //Category tree
            $this->addCSS(_MODULE_DIR_.'marketplace/views/js/categorytree/themes/default/style.min.css');
            $this->addJS(_MODULE_DIR_.'marketplace/views/js/categorytree/jstree.min.js');
            $this->addJS(_MODULE_DIR_.'marketplace/views/js/categorytree/wk_jstree.js');
        }

        $this->defineJSVars();
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/wk-mp-seller-booking-product.js');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/wk-mpbooking-global.js');
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/admin/wk-booking-product.css');

        if ($this->display == 'edit' && Tools::getValue('id_mp_product')) {
            //Upload images
            $this->addCSS(_MODULE_DIR_.'marketplace/views/css/uploadimage-css/jquery.filer.css');
            $this->addCSS(_MODULE_DIR_.'marketplace/views/css/uploadimage-css/jquery.filer-dragdropbox-theme.css');
            $this->addCSS(_MODULE_DIR_.'marketplace/views/css/uploadimage-css/uploadphoto.css');
            $this->addJS(_MODULE_DIR_.'marketplace/views/js/uploadimage-js/jquery.filer.js');
            $this->addJS(_MODULE_DIR_.'marketplace/views/js/uploadimage-js/uploadimage.js');
            $this->addJS(_MODULE_DIR_.'marketplace/views/js/imageedit.js');
            //End
            $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/wk-datepicker-custom.css');
            $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/wk-booking-global-style.css');
        }
    }
}
