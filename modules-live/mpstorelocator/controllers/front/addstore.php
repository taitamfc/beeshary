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

class mpstorelocatoraddstoreModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Marketplace', [], 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard')
        ];

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Add Store', [], 'Breadcrumb'),
            'url' => ''
        ];

        return $breadcrumb;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submit_store') || Tools::isSubmit('submit_and_stay_store')) {
            $idSeller = trim(Tools::getValue('id_seller'));
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

            // Check fields sizes
            $className = 'MarketplaceStoreLocator';
            // @todo : the call_user_func seems to contains only statics values
            $rules = call_user_func(array($className, 'getValidationRules'), $className);
            if (Tools::strlen($shopName) > $rules['size']['name']) {
                $this->errors[] = $this->module->l('Store name field is too long ('.$rules['size']['name'].' Chars max).');
            } elseif (Tools::strlen($address1) > $rules['size']['address1']) {
                $this->errors[] = $this->module->l('Address1 field is too long ('.$rules['size']['address1'].' Chars max).');
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


            if ($idSeller == 0) {
                $this->errors[] = $this->l('Seller name is required');
            }
            if (empty($latitude)) {
                $this->errors[] = $this->l('Latitude required');
            }
            if (empty($longitude)) {
                $this->errors[] = $this->l('Longitude required');
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
                } elseif (Tools::strlen($phone) > 16) {
                    $this->errors[] = $this->l('Phone number length must be 0 to 16');
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
                                'Invalid image extension. Only jpg, jpeg, gif file can be uploaded.'
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
                            'Closing slot time is not given.'
                        );
                    }
                } else {
                    $this->errors[] = $this->module->l(
                        'Opening slot time is not given.'
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
                            if ($opening_time[$i] > $closing_time[$i]) {
                                $this->errors[] = $this->module->l(
                                    sprintf(
                                        'For \'%s\' store opening time is greater than closing time.',
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

            if (!count($this->errors)) {
                $id_store = Tools::getValue('id_store');
                if ($id_store) {
                    $edit = 1;
                    $objStore = new MarketplaceStoreLocator($id_store);
                    //deleting the previous store products
                    MarketplaceStoreProduct::deleteStoreProductByStoreId($id_store);

                    if (Configuration::get('MP_STORE_LOCATION_ACTIVATION')) {
                        $objStore->active = $storeStatus;
                    }
                } else {
                    $edit = 0;
                    $objStore = new MarketplaceStoreLocator();

                    if (Configuration::get('MP_STORE_LOCATION_ACTIVATION')) {
                        $objStore->active = $storeStatus;
                    } else {
                        $objStore->active = 0;
                    }
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
                $objStore->save();

                $idInsert = $objStore->id;

                if ($idInsert) {
                    // Save store products if provided
                    if ($storeProducts) {
                        foreach ($storeProducts as $idProducts) {
                            $objStoreProduct = new MarketplaceStoreProduct();
                            $objStoreProduct->id_product = $idProducts;
                            $objStoreProduct->id_store = $idInsert;
                            $objStoreProduct->add();
                        }
                    }

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

                    if ($edit) { // if edit store
                        $param = array('success' => 2, 'tab' => Tools::getValue('active_tab'));
                    } else {
                        $param = array('success' => 1, 'tab' => Tools::getValue('active_tab'));
                    }

                    if (Tools::isSubmit('submit_store')) {
                        Tools::redirect($this->context->link->getModuleLink('mpstorelocator', 'storelist', $param));
                    } elseif (Tools::isSubmit('submit_and_stay_store')) {
                        $param['id_store'] = $idInsert;
                        // dump($this->context->link->getModuleLink('mpstorelocator', 'addstore', $param).'&updatemarketplace_store_locator');die;
                        Tools::redirect($this->context->link->getModuleLink('mpstorelocator', 'addstore', $param).'&updatemarketplace_store_locator');
                    }
                } else {
                    //'Some problem error occured while updating records.Please try after some time.';
                    $this->errors[] = $this->module->l('Some problem error occured while updating records. Please try later.');
                }
            }
        }
    }

    public function init()
    {
        parent::init();
        $this->weekDays = array();
        $this->weekDays[0] = $this->l('Sun');
        $this->weekDays[1] = $this->l('Mon');
        $this->weekDays[2] = $this->l('Tue');
        $this->weekDays[3] = $this->l('Wed');
        $this->weekDays[4] = $this->l('Thu');
        $this->weekDays[5] = $this->l('Fri');
        $this->weekDays[6] = $this->l('Sat');
    }

    public function initContent()
    {
        parent::initContent();
        if ($this->context->customer->id) {
            $this->context->smarty->assign('logic', 'add_new_store');
            $id_customer = $this->context->customer->id;
            $id_lang = $this->context->language->id;

            $obj_marketplace_seller = new WkMpSeller();
            $mp_seller = $obj_marketplace_seller->getSellerDetailByCustomerId($id_customer);
            if ($mp_seller && $mp_seller['active']) {
                $id_seller = $mp_seller['id_seller'];
                $seller_name = $mp_seller['seller_firstname'].' '.$mp_seller['seller_lastname'];

                $id_store = Tools::getValue('id_store');
                if ($id_store) {
                    $store = MarketplaceStoreLocator::getStoreById($id_store);
                    if (isset($store['id_seller']) && $store['id_seller'] == $id_seller) {
                        // Delete Store
                        if (Tools::getValue('delete')) {
                            $delete_prod = MarketplaceStoreProduct::deleteStoreProductByStoreId($id_store);
                            $delete_store = MarketplaceStoreLocator::deleteStoreLocationByStoreId($id_store);

                            if ($delete_prod && $delete_store) {
                                Tools::redirect($this->context->link->getModuleLink('mpstorelocator', 'storelist', array('deleted' => 1)));
                            } else {
                                Tools::redirect($this->context->link->getModuleLink('mpstorelocator', 'storelist', array('deleted' => 2)));
                            }
                        }
                        // close

                        //Delete Store Logo
                        if (Tools::getValue('id_delete_logo')) {
                            $id_delete_logo = Tools::getValue('id_delete_logo');
                            if (!@unlink(_PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$id_delete_logo.'.jpg')) {
                                $delete_logo_msg = 2;
                            } else {
                                //upload default image
                                /*$store_logo_path = _PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$id_delete_logo .".jpg";
                                $default_image_path = _PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/default.jpg';
                                ImageManager::resize($default_image_path, $store_logo_path, 50, 50);*/
                                $delete_logo_msg = 1;
                            }

                            if (isset($delete_logo_msg) && $delete_logo_msg) {
                                $param = array('delete_logo_msg' => $delete_logo_msg);
                                Tools::redirect($this->context->link->getModuleLink('mpstorelocator', 'storelist', $param));
                            }
                        }

                        $store = MarketplaceStoreLocator::getStoreById($id_store);
                        if ($store) {
                            // $store = MarketplaceStoreLocator::getMoreStoreDetails(array($store), $this->context->language->id);
                            $obj_country = new Country($store['country_id'], $id_lang);
                            $obj_state = new State($store['state_id']);
                            $store['country_name'] = $obj_country->name;
                            $store['state_name'] = $obj_state->name;
                            $store['products'] = MarketplaceStoreProduct::getSellerProducts($id_store);
                            $store['store_opening_days'] = json_decode($store['store_open_days']);
                            $store['opening_time'] = json_decode($store['opening_time']);
                            $store['closing_time'] = json_decode($store['closing_time']);
                            $paymentOption = json_decode($store['payment_option']);
                            if (empty($paymentOption)) {
                                $store['payment_option'] = array();
                            } else {
                                $store['payment_option'] = $paymentOption;
                            }

                            if (file_exists(_PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$id_store.'.jpg')) {
                                $this->context->smarty->assign('img_exist', 1);
                            }
                            $this->context->smarty->assign('store', $store);
                        }

                        $this->context->smarty->assign('id_store', $id_store);

                        Media::addJsDef(array(
                            'id_state' => $store['state_id'],
                            'id_store' => $store['id'],
                            'lat' => $store['latitude'],
                            'lng' => $store['longitude'],
                            'map_address' => $store['map_address'],
                            'country_id' => $store['country_id'],
                        ));
                    } else {
                        Tools::redirectAdmin(
                            $this->context->link->getModuleLink(
                                'mpstorelocator',
                                'storelist',
                                array(
                                    'mperror' => 1
                                )
                            )
                        );
                    }
                } else {
                    Media::addJsDef(array(
                        'id_state' => 0,
                    ));
                }

                $countries = Country::getCountries($id_lang, true);

                $mp_products = MarketplaceStoreProduct::getMpSellerActiveProducts($id_seller, $id_lang);
                if ($mp_products) {
                    $this->context->smarty->assign('mp_products', $mp_products);
                }

                $country = $this->context->country;
                $storePaymentOptions = MpStorePay::getPaymentOption(true);
                $this->context->smarty->assign(array(
                    'manage_status' => Configuration::get('MP_STORE_LOCATION_ACTIVATION'),
                    'countries' => $countries,
                    'id_customer' => $id_customer,
                    'id_seller' => $id_seller,
                    'seller_name' => $seller_name,
                    'modules_dir' => _MODULE_DIR_,
                    'country' => $country,
                    'link' => $this->context->link,
                    'weekdays' => $this->weekDays,
                    'activeTab' => Tools::getValue('tab'),
                    'storePaymentOptions' => $storePaymentOptions
                ));

                $this->defineJSVars();
                $this->setTemplate('module:'.$this->module->name.'/views/templates/front/addsellerstore.tpl');
            } else {
                Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('mpstorelocator', 'addstore')));
            }
        } else {
            Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('mpstorelocator', 'addstore')));
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        // Google Map Library
        $language = $this->context->language;
        $country = $this->context->country;
        $MP_GEOLOCATION_API_KEY = Configuration::get('MP_GEOLOCATION_API_KEY');
        $this->registerJavascript(
            'google-map-lib',
            "https://maps.googleapis.com/maps/api/js?key=$MP_GEOLOCATION_API_KEY&libraries=places&language=$language->iso_code&region=$country->iso_code",
            [
              'server' => 'remote'
            ]
        );

        // Register JS
        $this->registerJavascript('addstorelocation', 'modules/'.$this->module->name.'/views/js/admin/addstorelocation.js');
        $this->registerJavascript('filterstate', 'modules/'.$this->module->name.'/views/js/front/filterstate.js');

        // Register CSS
        $this->registerStylesheet('addstorelocation', 'modules/'.$this->module->name.'/views/css/admin/addstorelocation.css');
        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerStylesheet('marketplace_style', 'modules/marketplace/views/css/mp_global_style.css');
        $this->registerJavascript('mutiselect-js', 'modules/'.$this->module->name.'/views/js/admin/bootstrap-multiselect.js');

        $this->registerStylesheet('mutiselect-css', 'modules/'.$this->module->name.'/views/css/admin/bootstrap-multiselect.css');

        $this->addJqueryUI(array('ui.slider', 'ui.datepicker'));
        $this->registerJavascript(
            'addstore_timepicker-js',
            'js/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js',
            array(
                'position' => 'bottom',
                'priority' => 999
            )
        );

        $this->registerStylesheet('bootstrap-datetimepicker-css', 'js/jquery/plugins/timepicker/jquery-ui-timepicker-addon.css');

        $this->addJqueryPlugin('growl', null, false);
        $this->registerStylesheet('growl-css', 'js/jquery/plugins/growl/jquery.growl.css');
    }

    public function defineJSVars()
    {
        $jsVars = [
                'url_filestate' => $this->context->link->getModulelink('mpstorelocator', 'filterstate'),
                'req_shop_name' => $this->module->l('Seller name is required', [], 'Modules.MpStoreLocator'),
                'req_seller_name' => $this->module->l('Store name is required', [], 'Modules.MpStoreLocator'),
                'inv_shop_name' => $this->module->l('Store name is invalid', [], 'Modules.MpStoreLocator'),
                'req_street' => $this->module->l('Street is required', [], 'Modules.MpStoreLocator'),
                'req_city_name' => $this->module->l('City name is required', [], 'Modules.MpStoreLocator'),
                'inv_city_name' => $this->module->l('City name is invalid', [], 'Modules.MpStoreLocator'),
                'req_countries' => $this->module->l('Country is required', [], 'Modules.MpStoreLocator'),
                'req_zip_code' => $this->module->l('Zip/Postal code is required', [], 'Modules.MpStoreLocator'),
                'inv_zip_code' => $this->module->l('Zip/Postal code is inavlid', [], 'Modules.MpStoreLocator'),
                'req_latitude' => $this->module->l('Please select location on map', [], 'Modules.MpStoreLocator'),
                'select_country' => $this->module->l('Please select a country', [], 'Modules.MpStoreLocator'),
            ];
        Media::addJsDef($jsVars);
    }
}
