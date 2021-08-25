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

class MarketplaceEditStoreModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        if (!$this->context->customer->isLogged()) {
            $this->redirectMyAccount();
        }

        $smartyVar = array();
        $seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
        $idSeller = $seller['id_seller'];
        $smartyVar['logic'] = 2;

        $mpSellerLang = (new WkMpSeller())->getSellerShopLang($idSeller);
        if ($mpSellerLang) {
            foreach ($mpSellerLang as $sellerLang) {
                $seller['shop_name'][$sellerLang['id_lang']] = $sellerLang['shop_name'];
                $seller['about_shop'][$sellerLang['id_lang']] = $sellerLang['about_shop'];
            }
        }

        if (Tools::getValue('updated')) {
            $smartyVar['updated'] = 1;
        }

        $this->context->smarty->assign($smartyVar);

        // Set default lang at every form according to configuration multi-language
        WkMpHelper::assignDefaultLang($idSeller);
                
        //Settings (Permission) for seller to display selected details of seller
        $selectedDetailsByAdmin = array();
        $sellerDetailsAccess = Tools::jsonDecode(Configuration::get('WK_MP_SELLER_DETAILS_ACCESS'));
        if ($sellerDetailsAccess) {
            $objMarketplace = new Marketplace();
            if ($objMarketplace->sellerDetailsView) {
                foreach ($objMarketplace->sellerDetailsView as $sellerDetailsVal) {
                    if ($sellerDetailsAccess && in_array($sellerDetailsVal['id_group'], $sellerDetailsAccess)) {
                        $selectedDetailsByAdmin[] = array(
                            'id_group' => $sellerDetailsVal['id_group'],
                            'name' => $sellerDetailsVal['name']
                        );
                    }
                }
            }
        }

        if ($seller['seller_details_access']) {
            $this->context->smarty->assign('selectedDetailsBySeller', Tools::jsonDecode($seller['seller_details_access']));
        }

        $sellerBankData = WkMpSellerBank::getSellerBankDataByIdSeller($idSeller);
        $sellerDeliveryData = WkMpSellerDelivery::geDeliveryInfostBySellerId($idSeller);
        $shipping_days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $first_shipping_day = 'xx';

        if ($sellerDeliveryData) {
            $sellerDeliveryData['delivery_method'] = explode(' et ', $sellerDeliveryData['delivery_method']);
            $sellerDeliveryData['shipping_days'] = explode(', ', $sellerDeliveryData['shipping_days']);
            foreach ($shipping_days as $day) {
                if (in_array($day, $sellerDeliveryData['shipping_days'])) {
                    $first_shipping_day = $day;
                    break;
                }
            }
        }

        $this->context->smarty->assign(array(
            'mp_seller_info' => $seller,
            'active_tab' => Tools::getValue('tab'),
            'static_token' => Tools::getToken(false),
            'selectedDetailsByAdmin' => $selectedDetailsByAdmin,
            'country' => Country::getCountries($this->context->language->id, true),
            'seller_country_need' => Configuration::get('WK_MP_SELLER_COUNTRY_NEED'),
            'link' => $this->context->link,
            'mpSellerShopSettings' => Configuration::get('WK_MP_SELLER_SHOP_SETTINGS'),
            'timestamp' => WkMpHelper::getTimestamp(),
            'shop_default_img_path' => _MODULE_DIR_.$this->module->name.'/views/img/shop_img/defaultshopimage.jpg',
            'seller_default_img_path' => _MODULE_DIR_.$this->module->name.'/views/img/seller_img/defaultimage.jpg',
            'no_image_path' => _MODULE_DIR_.$this->module->name.'/views/img/home-default.jpg',
            'ps_img_dir' => _PS_IMG_.'l/',
            'max_phone_digit' => Configuration::get('WK_MP_PHONE_DIGIT'),
            'marketplace_address' => trim($seller['address']),
            'seller_bank_obj' => $sellerBankData,
            'seller_delivery_obj' => $sellerDeliveryData,
            'delivery_methods' => ['Colissimo', 'Transporteur express', 'Point relais Mondial Relay', 'Livraison libre'],
            'delivery_delays' => ['3 jours', '7 jours', '1 semaine', '2 semaines', '3 semaines'],
            'shipping_days' => $shipping_days,
            'option_free_deliverys' => [30 =>'Livraison offerte à partir de 30€', 50 => 'Livraison offerte à partir de 50€', 90 => 'Livraison offerte à partir de 90€', 0 => 'Non offerte'],
            'first_shipping_day' => $first_shipping_day,
        ));

        Media::addJsDef(array(
            'id_country' => $seller['id_country'],
            'id_state' => $seller['id_state'],
        ));

        $this->defineJSVars($idSeller);
        $this->setTemplate('module:marketplace/views/templates/front/shop/editstore.tpl');        
    }

    public function postProcess()
    {
        if (Tools::isSubmit('updateStore')) {
            if (isset($this->context->customer->id) && ($id_seller = (int)Tools::getValue('mp_seller_id'))) {
                $seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
                if ($seller && $seller['active']) {
                    $shopNameUnique = Tools::getValue('shop_name_unique');
                    $sellerFirstName = trim(Tools::getValue('seller_firstname'));
                    $sellerLastName = trim(Tools::getValue('seller_lastname'));
                    $businessEmail = Tools::getValue('business_email');
                    $phone = Tools::getValue('phone');
                    $fax = Tools::getValue('fax');
                    $post_code = Tools::getValue('post_code');
                    $sellerDefaultLanguage = Tools::getValue('current_lang_id');
                    $defaultLang = WkMpHelper::getDefaultLanguageBeforeFormSave($sellerDefaultLanguage);

                    if (!$defaultLang) {
                        $defaultLang = $sellerDefaultLanguage;
                    }

                    // Validate data
                    $this->validateSellerForm($defaultLang, $id_seller);

                    if (empty($this->errors)) {
                        $sellerCity = '';
                        $sellerCountryId = 8;
                        $sellerStateId = 0;
                        if (Tools::getValue('city')) {
                            $sellerCity = trim(Tools::getValue('city'));
                        }

                        if (Tools::getValue('id_country')) {
                            $sellerCountryId = Tools::getValue('id_country');
                        }

                        if (Tools::getValue('id_state')) {
                            $sellerStateId = Tools::getValue('id_state');
                        }

                        //update seller details
                        $objSeller = new WkMpSeller($id_seller);
                        $objSeller->shop_name_unique = $shopNameUnique;
                        $objSeller->link_rewrite = Tools::link_rewrite($shopNameUnique);
                        $objSeller->business_email = $businessEmail;
                        $objSeller->phone = $phone;
                        $objSeller->fax = $fax;
                        $objSeller->city = $sellerCity;
                        $objSeller->id_country = $sellerCountryId;
                        $objSeller->id_state = $sellerStateId;
                        $objSeller->post_code = $post_code;
                        $objSeller->default_lang = $sellerDefaultLanguage;

                        foreach (Language::getLanguages(false) as $language) {
                            $shopLangId = $language['id_lang'];
                            $aboutShopLangId = $language['id_lang'];

                            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                                //if shop name in other language is not available then fill with seller language same for others
                                if (!Tools::getValue('shop_name_'.$language['id_lang'])) {
                                    $shopLangId = $defaultLang;
                                }
                                if (!Tools::getValue('about_shop_'.$language['id_lang'])) {
                                    $aboutShopLangId = $defaultLang;
                                }
                            } else {
                                //if multilang is OFF then all fields will be filled as default lang content
                                $shopLangId = $defaultLang;
                                $aboutShopLangId = $defaultLang;
                            }

                            $objSeller->shop_name[$language['id_lang']] = Tools::getValue('shop_name_'.$shopLangId);

                            $objSeller->about_shop[$language['id_lang']] = Tools::getValue('about_shop_'.$aboutShopLangId);
                        }
                        $objSeller->address = Tools::getValue('address');
                        $objSeller->save();

                        // bank infos
                        $bank_type = Tools::getValue('bank_type');
                        $bank_beneficiary = Tools::getValue('bank_beneficiary');
                        $bank_establishment = Tools::getValue('bank_establishment');
                        $bank_iban_code = Tools::getValue('bank_iban_code');
                        $bank_code_bic = Tools::getValue('bank_code_bic');

                        if ($bank_type && $bank_establishment && $bank_iban_code && $bank_code_bic) {
                            if ($id_bank = (int)Tools::getValue('id_ps_wk_mp_seller_bank'))
                                $sellerBankObj = new WkMpSellerBank($id_bank);
                            else 
                                $sellerBankObj = new WkMpSellerBank();
                            $sellerBankObj->id_seller = $id_seller;
                            $sellerBankObj->bank_type = $bank_type;
                            $sellerBankObj->establishment = $bank_establishment;
                            $sellerBankObj->beneficiary = $bank_beneficiary;
                            $sellerBankObj->code_bic = $bank_code_bic;
                            $sellerBankObj->iban_code = 'FR'. $bank_iban_code;
                            $sellerBankObj->save();
                        }

                        // shipping infos
                        $delivery_method = Tools::getValue('delivery_method');
                        $delivery_delay = Tools::getValue('delivery_delay');
                        $shipping_days = Tools::getValue('shipping_days');
                        $option_free_delivery = Tools::getValue('option_free_delivery');

                        // add seller shipping info
                        if ($delivery_method && $shipping_days) {
                            if ($id_delivery = (int)Tools::getValue('id_ps_wk_mp_seller_delivery'))
                                $wkmpSellerDeliveryObj = new WkMpSellerDelivery($id_delivery);
                            else
                                $wkmpSellerDeliveryObj = new WkMpSellerDelivery();
                            $wkmpSellerDeliveryObj->id_seller = $id_seller;
                            $wkmpSellerDeliveryObj->delivery_method = implode(' et ', $delivery_method);
                            $wkmpSellerDeliveryObj->delivery_delay = $delivery_delay;
                            $wkmpSellerDeliveryObj->shipping_days = implode(', ', $shipping_days);
                            $wkmpSellerDeliveryObj->option_free_delivery = $option_free_delivery;
                            $wkmpSellerDeliveryObj->save();
                        }

                        //update shop name unique in seller order table
                        WkMpSellerOrder::updateOrderShopUniqueBySellerCustomerId($this->context->customer->id, $shopNameUnique);
                        
                        // Hook::exec('actionAfterUpdateSeller', array('id_seller' => $id_seller));
                        Tools::redirect($this->context->link->getModuleLink('marketplace', 'editstore', array('updated' => 1, 'tab' => Tools::getValue('active_tab'))));
                    }
                } else {
                    Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
                }
            } else {
                Tools::redirect($this->context->link->getPageLink('my-account'));
            }
        }
    }

    public function validateSellerForm($defaultLang, $idSeller)
    {
        $shopNameUnique = Tools::getValue('shop_name_unique');
        $businessEmail = Tools::getValue('business_email');
        $phone = Tools::getValue('phone');
        $shopName = trim(Tools::getValue('shop_name_'.$defaultLang));
        $sellerLang = Language::getLanguage((int) $defaultLang);

        $languages = Language::getLanguages();
        foreach ($languages as $language) {
            if (!Validate::isCatalogName(Tools::getValue('shop_name_'.$language['id_lang']))) {
                $invalidShopName = 1;
            }
        }

        if ($shopNameUnique == '') {
            $this->errors[] = $this->module->l('Unique name for shop is required field.', 'editprofile');
        } elseif (!Validate::isCatalogName($shopNameUnique)) {
            $this->errors[] = $this->module->l('Invalid Unique name for shop');
        } elseif (WkMpSeller::isShopNameExist(Tools::link_rewrite($shopNameUnique), $idSeller)) {
            $this->errors[] = $this->module->l('Unique name for shop is already taken. Try another.', 'editprofile');
        }

        if ($shopName == '') {
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $this->errors[] = sprintf($this->module->l('Shop name is required in %s', 'editprofile'), $sellerLang['name']);
            } else {
                $this->errors[] = $this->module->l('Shop name is required', 'editprofile');
            }
        } elseif (isset($invalidShopName)) {
            $this->errors[] = $this->module->l('Invalid Shop name', 'editprofile');
        }

        //Validate data
        $languages = Language::getLanguages();
        foreach ($languages as $language) {
            if (Tools::getValue('about_shop_'.$language['id_lang'])) {
                if (!Validate::isCleanHtml(Tools::getValue('about_shop_'.$language['id_lang']))) {
                    $invalidAboutShop = 1;
                }
            }
        }

        if (isset($invalidAboutShop)) {
            $this->errors[] = $this->module->l('Shop description have not valid data.', 'editprofile');
        }

        if ($phone == '') {
            $this->errors[] = $this->module->l('Phone is required field.', 'editprofile');
        } elseif (!Validate::isPhoneNumber($phone)) {
            $this->errors[] = $this->module->l('Invalid phone number.', 'editprofile');
        }

        if ($businessEmail == '') {
            $this->errors[] = $this->module->l('Email ID is required field.', 'editprofile');
        } elseif (!Validate::isEmail($businessEmail)) {
            $this->errors[] = $this->module->l('Invalid Email ID.', 'editprofile');
        } elseif (WkMpSeller::isSellerEmailExist($businessEmail, $idSeller)) {
            $this->errors[] = $this->module->l('Email ID already exist.', 'editprofile');
        }

        if (Configuration::get('WK_MP_SELLER_COUNTRY_NEED')) {
            $sellerCity = Tools::getValue('city');
            if (!$sellerCity) {
                $this->errors[] = $this->module->l('City is required field.', 'editprofile');
            } elseif (!Validate::isName($sellerCity)) {
                $this->errors[] = $this->module->l('Invalid city name.', 'editprofile');
            }

            if (!Tools::getValue('id_country')) {
                $this->errors[] = $this->module->l('Country is required field.', 'editprofile');
            }

            //if state available in selected country
            if (Tools::getValue('state_available')) {
                if (!Tools::getValue('id_state')) {
                    $this->errors[] = $this->module->l('State is required field.', 'editprofile');
                }
            }
        }

        $cp = Tools::getValue('post_code');
        if (Tools::isEmpty($cp) || !Validate::isPostCode($cp)) {
            $this->errors[] = $this->module->l('Code post is required field.', 'editprofile');
        }
    }
    
    public function defineJSVars($mpIdSeller)
    {
        $jsDef = array(
            'terms_and_condition_active' => 0,
            'actionIdForUpload' => $mpIdSeller,
            'actionpage' => 'seller',
            'deleteaction' => '',
            'adminupload' => 0,
            'upload_single' => 1, //assigned in 'jquery.filer.js' for differenciate seller page and product page
            'seller_country_need' => Configuration::get('WK_MP_SELLER_COUNTRY_NEED'),
            'multi_lang' => Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE'),
            'iso' => $this->context->language->iso_code,
            'path_sellerdetails' => $this->context->link->getModuleLink('marketplace', 'editprofile'),
            'path_uploader' => $this->context->link->getModulelink('marketplace', 'uploadimage'),
            'seller_default_img_path' => _MODULE_DIR_.$this->module->name.'/views/img/seller_img/defaultimage.jpg',
            'shop_default_img_path' => _MODULE_DIR_.$this->module->name.'/views/img/shop_img/defaultshopimage.jpg',
            'no_image_path' => _MODULE_DIR_.$this->module->name.'/views/img/home-default.jpg',
            'mp_tinymce_path' => _MODULE_DIR_.$this->module->name.'/libs',
            'img_module_dir' => _MODULE_DIR_.$this->module->name.'/views/img/',
            'req_shop_name_lang' => $this->module->l('Shop name is required in Default Language -', 'editprofile'),
            'shop_name_exist_msg' => $this->module->l('Shop Unique name already taken. Try another.', 'editprofile'),
            'shop_name_error_msg' => $this->module->l('Shop name can not contain any special character except underscore. Try another.', 'editprofile'),
            'seller_email_exist_msg' => $this->module->l('Email Id alreay exist.', 'editprofile'),
            'confirm_deactivate_msg' => $this->module->l('Are you sure want to deactivate your shop?', 'editprofile'),
            'selectstate' => $this->module->l('Select State', 'editprofile'),
            'checkCustomerAjaxUrl' => $this->context->link->getModulelink('mpsellerwiselogin', 'checkcustomerajax'),
            'moduledir' => '/modules/',
        );
        
        Media::addJsDef($jsDef);
    }

    public function redirectMyAccount()
    {
        Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('marketplace', 'editprofile')));
    }

    public function displayAjaxCheckUniqueShopName()
    {
        if (!$this->isTokenValid()) {
            die('Something went wrong!');
        }
        //check unique shop name and compare to other existing shop name unique
        WkMpSeller::validateSellerUniqueShopName();
    }

    public function displayAjaxCheckUniqueSellerEmail()
    {
        if (!$this->isTokenValid()) {
            die('Something went wrong!');
        }
        //check seller email and compare to other existing seller email
        WkMpSeller::validateSellerEmail();
    }

    public function displayAjaxGetSellerState()
    {
        if (!$this->isTokenValid()) {
            die('Something went wrong!');
        }
        //Get state by choosing country
        WkMpSeller::displayStateByCountryId();
    }

    public function displayAjaxValidateMpSellerForm()
    {
        if (!$this->isTokenValid()) {
            die('Something went wrong!');
        }

        $params = array();
        parse_str(Tools::getValue('formData'), $params);
        if (!empty($params)) {
            WkMpSeller::validationSellerFormField($params);
        } else {
            die('1');
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->registerStylesheet('mp-marketplace_account', 'modules/'.$this->module->name.'/views/css/marketplace_account.css');
        $this->registerStylesheet('mp_global_style-css', 'modules/'.$this->module->name.'/views/css/mp_global_style.css');
        $this->registerStylesheet('mp_global_style-css', 'modules/mpsellerwiselogin/views/css/seller_creation.css');

        $this->registerJavascript('chosen-jquery', 'js/jquery/plugins/jquery.chosen.js', ['priority' => 100, 'position' => 'bottom']);
        $this->registerStylesheet('chosen-jquery', 'themes/beeshary/assets/css/jquery.chosen.css');

        $this->registerJavascript('mp-mp_form_validation', 'modules/'.$this->module->name.'/views/js/mp_form_validation.js');
        $this->registerJavascript('mp-change_multilang', 'modules/'.$this->module->name.'/views/js/change_multilang.js');
        $this->registerJavascript('mp-raty', 'modules/'.$this->module->name.'/views/js/raty.js');
        // $this->registerJavascript('mp-getstate', 'modules/'.$this->module->name.'/views/js/getstate.js');
        $this->registerJavascript('seller-profile', 'modules/'.$this->module->name.'/views/js/sellerprofile.js', ['priority' => 200, 'position' => 'bottom']);
        $this->registerJavascript('ps-validate-js', 'js/validate.js');
    }
}
