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

class AdminMarketplaceStoreLocatorController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'marketplace_store_locator';
        $this->className = 'MarketplaceStoreLocator';
        $this->list_no_link = true;

        $this->identifier = 'id';
        parent::__construct();

        $this->_join .= 'INNER JOIN `'._DB_PREFIX_.'wk_mp_seller` ms ON (ms.`id_seller` = a.`id_seller`)';
        $this->_select = 'CONCAT(ms.`seller_firstname`, \' \', ms.`seller_lastname`) AS `seller_name`, ms.`business_email` AS seller_email, a.`id` as id_image';

        $this->fieldImageSettings = array(
            'name' => 'logo',
            'dir' => 'store_logo',
        );

        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'id_image' => array(
                'title' => $this->l('Logo'),
                'align' => 'center',
                // 'image' => 'store_logo',
                'callback' => 'callStoreLogo',
                'orderby' => false,
                'search' => false,
            ),
            'seller_name' => array(
                'title' => $this->l('Seller Name'),
                'align' => 'center',
                'havingFilter' => true
            ),
            'seller_email' => array(
                'title' => $this->l('Seller Email'),
                'align' => 'center',
                'havingFilter' => true
            ),
            'name' => array(
                'title' => $this->l('Store Name'),
                'align' => 'center',
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
            ),
        );

        $this->bulk_actions = array('delete' => array(
            'text' => $this->l('Delete selected'),
            'icon' => 'icon-trash',
            'confirm' => $this->l('Delete selected items?'), ),
        );
        $this->weekDays = array();
        $this->weekDays[0] = $this->l('Sun');
        $this->weekDays[1] = $this->l('Mon');
        $this->weekDays[2] = $this->l('Tue');
        $this->weekDays[3] = $this->l('Wed');
        $this->weekDays[4] = $this->l('Thu');
        $this->weekDays[5] = $this->l('Fri');
        $this->weekDays[6] = $this->l('Sat');
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new store'),
        );
    }

    public function callStoreLogo($idLogo)
    {
        return '<img src="'._MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$idLogo.'.jpg"/>';
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        // $ps_img_dir = _PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo';
        // $this->context->smarty->assign('ps_img_dir', $ps_img_dir);

        // $mod_img_dir = _MODULE_DIR_.'mpstorelocator/views/img/store_logo';
        // $this->context->smarty->assign('mod_img_dir', $mod_img_dir);

        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new store'),
        );

        return parent::renderList();
    }

    public function postProcess()
    {
        $this->addCSS(_MODULE_DIR_.'mpstorelocator/views/css/admin/addstorelocation.css');

        if (Tools::isSubmit('submit_store') || Tools::isSubmit('submit_and_stay_store')) {
            $id_store = Tools::getValue('id_store');
            if ($id_store) {  //if edit store
                $this->saveStoreLocation($id_store);
            } else { // add store
                $this->saveStoreLocation();
            }
        }

        if (Tools::isSubmit('Arraystore_locator')) {
            $this->changeStatus();
        }

        return parent::postProcess();
    }

    public function changeStatus()
    {
        $id = Tools::getValue('id');
        $obj_store_locator = new MarketplaceStoreLocator();
        $storeStatus = $obj_store_locator->getStoreLocatorStatus($id);
        if ($storeStatus) {
            $status = 0;
        } else {
            $status = 1;
        }

        Hook::exec('actionStoreLocationToggle', array('id_store' => $id, 'current_status' => $storeStatus));
        $obj_store_locator->activeStoreLocator($status, $id);

        Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
    }

    public function saveStoreLocation($id_store = false)
    {
        $idSeller = trim(Tools::getValue('seller_name'));
        $shopName = trim(Tools::getValue('shop_name'), ' ');
        $address1 = trim(Tools::getValue('address1'), ' ');
        $address2 = trim(Tools::getValue('address2'), ' ');
        $countryId = trim(Tools::getValue('countries'), ' ');
        $stateId = trim(Tools::getValue('state'), ' ');
        $cityName = trim(Tools::getValue('city_name'), ' ');
        $latitude = trim(Tools::getValue('latitude'), ' ');
        $longitude = trim(Tools::getValue('longitude'), ' ');
        $mapAddress = trim(Tools::getValue('map_address'), ' ');
        $phone = trim(Tools::getValue('phone'), ' ');
        $fax = trim(Tools::getValue('fax'), ' ');
        $email = trim(Tools::getValue('email'), ' ');
        $zipCode = trim(Tools::getValue('zip_code'), ' ');
        
        $slotStartTime = trim(Tools::getValue('pickup_start_time'), ' ');
        $slotEndTime = trim(Tools::getValue('pickup_end_time'), ' ');
        
        $storeProducts = Tools::getValue('store_products');
        $storeStatus = trim(Tools::getValue('store_status'), ' ');
        $storePickupAvailable = trim(Tools::getValue('store_pickup_available'), ' ');
        $mapAddressText = trim(Tools::getValue('map_address_text'), ' ');
        $wkStorePaymentOption = Tools::getValue('wk_store_payment_option');


        if ($idSeller == 0) {
            $this->errors[] = $this->l('Seller name is required');
        }
        if (!$shopName) {
            $this->errors[] = $this->l('Shop name is required');
        } else {
            if (!Validate::isGenericName($shopName)) {
                $this->errors[] = $this->l('Invalid shop name');
            }
        }
        if (!$countryId) {
            $this->errors[] = $this->l('Country is required');
        }
        if (!$address1) {
            $this->errors[] = $this->l('Address1 is required');
        } elseif (Tools::strlen($address1) > 128) {
            $this->errors[] = $this->l('Address1 length must be 0 to 128');
        }
        if ($address2 && Tools::strlen($address2) > 128) {
            $this->errors[] = $this->l('Address2 length must be 0 to 128');
        }
        if ($cityName) {
            if (!Validate::isCityName($cityName)) {
                $this->errors[] = $this->l('Invalid city name');
            }
        }

        $country = new Country($countryId);
        if ($country && !(int)$country->contains_states && $stateId) {
            $this->errors[] = $this->l('You have selected a state for a country that does not contain states.');
        }

        /* If the selected country contains states, then a state have to be selected */
        if ((int)$country->contains_states && !$stateId) {
            $this->errors[] = $this->l('An address located in a country containing states must have a state selected.');
        }
        /* Check zip code format */
        if ($country->zip_code_format && !$country->checkZipCode($zipCode)) {
            $this->errors[] = $this->l('Your Zip/postal code is incorrect.')
            .'<br />'.$this->l('It must be entered as follows:').
            ' '.str_replace(
                'C',
                $country->iso_code,
                str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))
            );
        } elseif (empty($zipCode) && $country->need_zip_code) {
            $this->errors[] = $this->l('A Zip/postal code is required.');
        } elseif ($zipCode && !Validate::isPostCode($zipCode)) {
            $this->errors[] = $this->l('The Zip/postal code is invalid.');
        }

        if ($phone) {
            if (!Validate::isPhoneNumber($phone)) {
                $this->errors[] = $this->l('Invalid phone number');
            }
        }
        if ($email) {
            if (!Validate::isEmail($email)) {
                $this->errors[] = $this->l('Invalid Email Id');
            } elseif (Tools::strlen($email) > 128) {
                $this->errors[] = $this->l('Email length must be 0 to 128');
            }
        }
        if ($fax) {
            if (!Validate::isPhoneNumber($fax)) {
                $this->errors[] = $this->l('Invalid fax number');
            }
        }


        if ($_FILES['store_logo']['size'] != 0) {
            list($shopWidth, $shopHeight) = getimagesize($_FILES['store_logo']['tmp_name']);
            if (800 < $shopWidth || 800 < $shopHeight) {
                $this->errors[] = $this->l('File size must be less than 800 x 800 px.');
            } else {
                if (0 == $_FILES['store_logo']['error']) {
                    if (!ImageManager::isCorrectImageFileExt($_FILES['store_logo']['name'])) {
                        $this->errors[] = $this->l(
                            'Invalid image extension. Only jpg, png, jpeg, gif file can be uploaded.'
                        );
                    }
                }
            }
        }

        if (Tools::getValue('store_pickup_available')) {
            if (!empty($slotStartTime)) {
                if (!empty($slotEndTime)) {
                    if ($slotStartTime > $slotEndTime) {
                        $this->errors[] = $this->module->l(
                            'Opening slot time is greater than closing time.'
                        );
                    }
                } else {
                    $this->errors[] = $this->module->l(
                        'Pick up slot close time is missing'
                    );
                }
            } else {
                $this->errors[] = $this->module->l(
                    'Pick up slot start time is missing'
                );
            }
        }

        $store_opening_days = array();
        $opening_time = array();
        $closing_time = array();

        for ($i = 0; $i < 7; $i++) {
            $store_opening_days[$i] = Tools::getValue('store_opening_days_'.$i);
            $opening_time[$i] = Tools::getValue('opening_time_'.$i);
            $closing_time[$i] = Tools::getValue('closing_time_'.$i);

            if (1 == $store_opening_days[$i]) {
                $store_opening_days[$i] = (int) 1;
                if (!empty($opening_time[$i])) {
                    if (!empty($closing_time[$i])) {
                        if ($opening_time[$i] >= $closing_time[$i]) {
                            $this->errors[] = $this->module->l(
                                sprintf(
                                    'For \'%s\' store opening time is greater than or equal to closing time.',
                                    $this->weekDays[$i]
                                )
                            );
                        }
                    } else {
                        $this->errors[] = $this->module->l(
                            sprintf('For \'%s\' store closing time is not given.', $this->weekDays[$i])
                        );
                    }
                } else {
                    $this->errors[] = $this->module->l(
                        sprintf('For \'%s\' store opening time is not given.', $this->weekDays[$i])
                    );
                }
            } else {
                $store_opening_days[$i] = 0;
            }
        }

        // Check fields sizes
        // @todo : the call_user_func seems to contains only statics values
        $rules = call_user_func(array($this->className, 'getValidationRules'), $this->className);
        if (Tools::strlen($shopName) > $rules['size']['name']) {
            $this->errors[] = $this->module->l('Store name field is too long ('.$rules['size']['name'].' Chars max).');
        } elseif (Tools::strlen($address1) > $rules['size']['address1']) {
            $this->errors[] = $this->module->l('Address field is too long ('.$rules['size']['address1'].' Chars max).');
        } elseif (Tools::strlen($mapAddress) > $rules['size']['map_address']) {
            $this->errors[] = $this->module->l('Map Address field is too long ('.$rules['size']['map_address'].' Chars max).');
        } elseif (Tools::strlen($mapAddressText) > $rules['size']['map_address_text']) {
            $this->errors[] = $this->module->l('Map Address text field is too long ('.$rules['size']['map_address_text'].' Chars max).');
        } elseif (Tools::strlen($cityName) > $rules['size']['city_name']) {
            $this->errors[] = $this->module->l('City name field is too long ('.$rules['size']['city_name'].' Chars max).');
        } elseif (Tools::strlen($zipCode) > $rules['size']['zip_code']) {
            $this->errors[] = $this->module->l('Zipcode field is too long ('.$rules['size']['zip_code'].' Chars max).');
        } elseif (Tools::strlen($phone) > $rules['size']['phone']) {
            $this->errors[] = $this->module->l('Phone field is too long ('.$rules['size']['phone'].' Chars max).');
        }
        // Validation

        if (empty($this->errors)) {
            if ($id_store) {
                $objStore = new MarketplaceStoreLocator($id_store);
                //deleting the previous store products
                MarketplaceStoreProduct::deleteStoreProductByStoreId($id_store);
            } else {
                $objStore = new MarketplaceStoreLocator();
            }

            $objStore->name = $shopName;
            $objStore->id_seller = $idSeller;
            $objStore->country_id = $countryId;
            $objStore->state_id = $stateId;
            $objStore->city_name = $cityName;
            $objStore->address1 = $address1;
            $objStore->address2 = $address2;
            $objStore->latitude = $latitude;
            $objStore->longitude = $longitude;
            $objStore->map_address = $mapAddress;
            $objStore->map_address_text = $mapAddressText;
            $objStore->zip_code = $zipCode;
            $objStore->phone = $phone;
            $objStore->fax = $fax;
            $objStore->email = $email;

            $objStore->pickup_start_time = $slotStartTime;
            $objStore->pickup_end_time = $slotEndTime;
            // $objStore->hours = $hours;
            $objStore->store_open_days = json_encode($store_opening_days);
            $objStore->opening_time = json_encode($opening_time);
            $objStore->closing_time = json_encode($closing_time);
            $objStore->payment_option = json_encode($wkStorePaymentOption);

            $objStore->store_pickup_available = $storePickupAvailable;
            $objStore->active = $storeStatus;
            $objStore->save();
            $idInsert = $objStore->id;

            if ($idInsert) {
                $width = 50;
                $height = 50;
                $storeLogoPath = _PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$idInsert.'.jpg';
                if ($id_store) {  // if edit store
                    if (0 != $_FILES['store_logo']['size']) {
                        ImageManager::resize($_FILES['store_logo']['tmp_name'], $storeLogoPath, $width, $height);
                    }
                } else {
                    if (0 != $_FILES['store_logo']['size']) {
                        ImageManager::resize($_FILES['store_logo']['tmp_name'], $storeLogoPath, $width, $height);
                    } else {
                        $defaultImagePath = _PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/default.jpg';
                        ImageManager::resize($defaultImagePath, $storeLogoPath, $width, $height);
                    }
                }

                // Save store products if provided
                if ($storeProducts) {
                    foreach ($storeProducts as $idProducts) {
                        $objStoreProduct = new MarketplaceStoreProduct();
                        $objStoreProduct->id_product = $idProducts;
                        $objStoreProduct->id_store = $idInsert;
                        $objStoreProduct->add();
                    }
                }
                if (Tools::getValue('submit_store')) {
                    $redirect = self::$currentIndex.'&conf=4&token='.$this->token;
                } elseif (Tools::getValue('submit_and_stay_store')) {
                    $redirect = self::$currentIndex.'&conf=4&id='
                    .$idInsert.'&updatemarketplace_store_locator&token='.$this->token;
                }
                Tools::redirectAdmin($redirect);
            } else {
                $this->errors[] = $this->l('Some problem occured while updating records. Please try after some time.');
            }
        }
    }

    public function renderForm()
    {
        $link = new Link();
        $id_lang = $this->context->language->id;

        // get only active country and country that have states(admin can manage it)
        $countries = Country::getCountries($id_lang, true);
        $autocomplete_link = $link->getModuleLink('mpstorelocator', 'frontautocomplete');
        $this->tpl_form_vars = array(
            'countries' => $countries,
            'modules_dir' => _MODULE_DIR_,
            'autocomplete_link' => $autocomplete_link,
        );

        $obj_seller = new WkMpSeller();
        $seller_info = $obj_seller->getAllSeller();
        if ($seller_info) {
            $this->tpl_form_vars['seller_info'] = $seller_info;
        }

        // if edit store
        $id_store = Tools::getValue('id');
        if ($id_store) {
            $obj_store = new MarketplaceStoreLocator();
            $store = $obj_store->getStoreById($id_store);
            if ($store) {
                $obj_country = new Country($store['country_id'], $id_lang);
                $obj_state = new State($store['state_id']);
                $store['country_name'] = $obj_country->name;
                $store['state_name'] = $obj_state->name;
                //jsonEncode bcz using this in js
                $store['products'] = Tools::jsonEncode(MarketplaceStoreProduct::getSellerProducts($id_store));
                $store['store_opening_days'] = json_decode($store['store_open_days']);
                $store['opening_time'] = json_decode($store['opening_time']);
                $store['closing_time'] = json_decode($store['closing_time']);
                $paymentOption = json_decode($store['payment_option']);
                if (empty($paymentOption)) {
                    $store['payment_option'] = array();
                } else {
                    $store['payment_option'] = $paymentOption;
                }
                $this->context->smarty->assign('store', $store);

                if (file_exists(_PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$id_store.'.jpg')) {
                    $this->context->smarty->assign('img_exist', 1);
                }

                $this->context->smarty->assign('logo_path', _MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$store['id'].'.jpg');
            }
        }
        $storePaymentOptions = MpStorePay::getPaymentOption(true);
        $this->context->smarty->assign(
            array(
                'default_logo_path' => _MODULE_DIR_.'mpstorelocator/views/img/store_logo/default.jpg',
                'weekdays' => $this->weekDays,
                'storePaymentOptions' => $storePaymentOptions
            )
        );

        $this->fields_form = array(
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'button',
                    ),
                );

        return parent::renderForm();
    }

    public function ajaxProcessFilterStates()
    {
        $id_country = Tools::getValue('id_country');
        $has_states = Country::containsStates($id_country);
        if ($has_states) {
            $states = State::getStatesByIdCountry((int) $id_country);
            if ($states) {
                $jsondata = Tools::jsonEncode($states);
            } else {
                $jsondata = Tools::jsonEncode(array('failed'));
            }
        } else {
            $jsondata = Tools::jsonEncode(array('no_states'));
        }
        die($jsondata);
    }

    public function ajaxProcessGetSellerProducts()
    {
        $id_seller = Tools::getValue('id_seller');
        $mp_products = MarketplaceStoreProduct::getMpSellerActiveProducts($id_seller);
        if ($mp_products) {
            $jsondata = Tools::jsonEncode($mp_products);
        } else {
            $jsondata = Tools::jsonEncode(array('failed'));
        }

        die($jsondata);
    }

    public function ajaxProcessDeleteStoreLogo()
    {
        $id_store = Tools::getValue('id_store');
        if (!@unlink(_PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$id_store.'.jpg')) {
            $data = array('status' => 'failed', 'msg' => 'Error while deleting file.');
        } else {
            $data = array('status' => 'success', 'msg' => 'Image successfully deleted.');
        }

        die(Tools::jsonEncode($data));
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
                    $objStore = new MarketplaceStoreLocator($id);
                    if (!$objStore->active) {
                        $objMpSeller = new WkMpSeller($objStore->id_seller);
                        if (!$objMpSeller->active) {
                            $this->errors[] = $this->l('You can not activate this store because shop '.$objMpSeller->shop_name_unique.' is not active right now.');
                        } else {
                            parent::processBulkStatusSelection($status);
                        }
                    } else {
                        parent::processBulkStatusSelection($status);
                    }
                }
            }
        } else {
            parent::processBulkStatusSelection($status);
        }
    }

    public function processStatus()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            if ($object->id) {
                $objStore = new MarketplaceStoreLocator($object->id);
                if (!$objStore->active) {
                    $objMpSeller = new WkMpSeller($objStore->id_seller);
                    if (!$objMpSeller->active) {
                        $this->errors[] = $this->l('You can not activate this product because shop '.$objMpSeller->shop_name_unique.' is not active right now.');
                    } else {
                        parent::processStatus();
                    }
                } else {
                    parent::processStatus();
                }
            }
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();

        // Google Map Library
        $language = $this->context->language;
        $country = $this->context->country;
        $getLocationKey = Configuration::get('MP_GEOLOCATION_API_KEY');
        if ($getLocationKey) {
        }
        $this->addJs("https://maps.googleapis.com/maps/api/js?key=$getLocationKey&libraries=places&language=$language->iso_code&region=$country->iso_code");

        $this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/admin/addstorelocation.js');
        $this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/admin/adminstore.js');
        $this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/admin/bootstrap-multiselect.js');
        
        $this->addCSS(_MODULE_DIR_.'mpstorelocator/views/css/admin/bootstrap-multiselect.css');
    }
}
