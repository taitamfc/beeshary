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

class MpStoreLocatorStoreConfigurationModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if ($this->context->customer->id) {
            $idCustomer = $this->context->customer->id;
            $idLang = $this->context->language->id;

            $objSeller = new WkMpSeller();
            $mpSeller = $objSeller->getSellerDetailByCustomerId($idCustomer);

            if ($mpSeller && $mpSeller['active']) {
                $idSeller = $mpSeller['id_seller'];
                $countries = MpStoreConfiguration::getCountries($idLang, true);
                $country = $this->context->country;
                $objConfig = new MpStoreConfiguration();
                $storeConfiguration = $objConfig->getStoreConfiguration($idSeller);

                if ($storeConfiguration) {
                    $storeConfiguration['countries'] = json_decode($storeConfiguration['countries']);
                    $this->context->smarty->assign(
                        'storeConfiguration',
                        $storeConfiguration
                    );
                }
                $dirName = _PS_MODULE_DIR_.'mpstorelocator/views/img/mp_store_marker_icon/';
                $filePrefix = $idSeller.'_'.$storeConfiguration['id_store_configuration'].'_';
                $fileName = glob($dirName."$filePrefix*.jpg");

                if ($fileName) {
                    $fileName = explode('/', $fileName[0]);
                    $fileName = $fileName[count($fileName) - 1];
                    $this->context->smarty->assign(
                        array(
                            'storeImage' => $fileName
                        )
                    );
                }

                $this->context->smarty->assign(
                    array(
                        'logic' => 'manage_store_configuration',
                        'mpIdSeller' => $idSeller,
                        'modules_dir' => _MODULE_DIR_,
                        'countries' => $countries,
                        'country' => $country,
                        'activeTab' => Tools::getValue('tab'),
                        'addConfig' => Tools::getValue('addConfig'),
                        'MP_STORE_COUNTRY_ENABLE' => Configuration::get('MP_STORE_COUNTRY_ENABLE'),
                        'MP_STORE_PICKUP_DATE' => Configuration::get('MP_STORE_PICKUP_DATE'),
                        'MP_STORE_PICK_UP_PAYMENT' => Configuration::get('MP_STORE_PICK_UP_PAYMENT')
                    )
                );

                $this->setTemplate('module:mpstorelocator/views/templates/front/storeconfiguration.tpl');
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('btnSubmitStoreConfig')) {
            $this->configStorePickUpDataValidation();
            if (!count($this->errors)) {
                $this->configStorePickUpDataProcess();
            }
        }
    }

    private function configStorePickUpDataProcess()
    {
        if (Tools::isSubmit('btnSubmitStoreConfig')) {
            if (Tools::getValue('mp_id_store_configuration')) {
                $objStoreConfiguration = new MpStoreConfiguration((int)Tools::getValue('mp_id_store_configuration'));
            } else {
                $objStoreConfiguration = new MpStoreConfiguration();
            }

            $objStoreConfiguration->id_seller = (int)Tools::getValue('mp_id_seller');
            $objStoreConfiguration->enable_store_notification = (int)Tools::getValue('enableStoreNotification');

            if (Configuration::get('MP_STORE_PICK_UP_PAYMENT')) {
                $objStoreConfiguration->store_payment = (int)Tools::getValue('enableStorePayment');
            }

            if (Configuration::get('MP_STORE_PICKUP_DATE')) {
                $objStoreConfiguration->enable_date = (int)Tools::getValue('enableDateSelection');
                $objStoreConfiguration->max_pick_ups = (int)Tools::getValue('mp_max_pickup');
                if (Tools::getValue('enableDateSelection')) {
                    $objStoreConfiguration->minimum_days = (int)Tools::getValue('mp_minimum_days');
                    $objStoreConfiguration->maximum_days = (int)Tools::getValue('mp_maximum_days');
                    $objStoreConfiguration->enable_time = (int)Tools::getValue('enableTimeSelection');
                    if (Tools::getValue('enableTimeSelection')) {
                        $objStoreConfiguration->minimum_hours = (int)Tools::getValue('mp_minimum_hours');
                    }
                }
            }

            $objStoreConfiguration->enable_country = (int)Tools::getValue('enableCountryRestriction');
            $objStoreConfiguration->enable_marker = (int)Tools::getValue('enableCustomMarker');

            if (Configuration::get('MP_STORE_COUNTRY_ENABLE')) {
                if (Tools::getValue('enableCountryRestriction')) {
                    $objStoreConfiguration->countries = json_encode(Tools::getValue('mp_countries'));
                }
            }
            $objStoreConfiguration->save();
            if ($objStoreConfiguration->id) {
                if (Tools::getValue('enableCustomMarker')) {
                    $dirName = _PS_MODULE_DIR_.'mpstorelocator/views/img/mp_store_marker_icon/';
                    $filePrefix = $objStoreConfiguration->id_seller.'_'.$objStoreConfiguration->id.'_';
                    $fileName = glob($dirName."$filePrefix*.jpg");
                    if ($_FILES['mp_marker_icon']['name']) {
                        if ($fileName && !@unlink($fileName[0])) {
                        } else {
                            //upload default image
                            $logoName = $filePrefix.'mp_marker_icon'.strtotime("now").'.jpg';
                            $storeLogoPath = $dirName.$logoName;
                            ImageManager::resize($_FILES['mp_marker_icon']['tmp_name'], $storeLogoPath, 27, 42);
                            Configuration::updateValue('MP_STORE_MARKER_NAME', $logoName);
                        }
                    }
                }
            }
            $moduleConfig = $this->context->link->getModuleLink(
                'mpstorelocator',
                'storeconfiguration',
                array('success' => 1, 'tab' => Tools::getValue('active_tab'))
            );
            Tools::redirectAdmin(
                $moduleConfig
            );
        }
    }

    private function configStorePickUpDataValidation()
    {
        if (Tools::isSubmit('btnSubmitStoreConfig')) {
            $idStoreConfiguration = 0;
            if ($idStoreConfiguration = Tools::getValue('mp_id_store_configuration')) {
                $idCustomer = $this->context->customer->id;
                $idLang = $this->context->language->id;
                $objSeller = new WkMpSeller();
                $mpSeller = $objSeller->getSellerDetailByCustomerId($idCustomer);
                if ($mpSeller['id_seller'] != Tools::getValue('mp_id_seller')) {
                    Tools::redirect(
                        $this->context->link->getModuleLink(
                            'mpstorelocator',
                            'storeconfiguration',
                            array(
                                'mperror' => 1
                            )
                        )
                    );
                }
            }

            if (Tools::getValue('enableCustomMarker')) {
                if ($idStoreConfiguration) {
                    $objStoreConfiguration = new MpStoreConfiguration($idStoreConfiguration);
                    $dirName = _PS_MODULE_DIR_.'mpstorelocator/views/img/mp_store_marker_icon/';
                    $filePrefix = $objStoreConfiguration->id_seller.'_'.$objStoreConfiguration->id.'_';
                    $fileName = glob($dirName."$filePrefix*.jpg");
                    if (empty($_FILES['mp_marker_icon']['name']) && !$fileName) {
                        $this->errors[] = $this->module->l('Marker icon required');
                    }
                } else {
                    if (empty($_FILES['mp_marker_icon']['name'])) {
                        $this->errors[] = $this->module->l('Marker icon required');
                    }
                }
                if ($_FILES['mp_marker_icon']['size'] != 0) {
                    list($shopWidth, $shopHeight) = getimagesize($_FILES['mp_marker_icon']['tmp_name']);
                    if (800 < $shopWidth || 800 < $shopHeight) {
                        $this->errors[] = $this->l('File size must be less than 800 x 800 px.');
                    } else {
                        if (0 == $_FILES['mp_marker_icon']['error']) {
                            if (!ImageManager::isCorrectImageFileExt($_FILES['mp_marker_icon']['name'])) {
                                $this->errors[] = $this->l(
                                    'Invalid image extension. Only jpg, jpeg, gif file can be uploaded.'
                                );
                            }
                        }
                    }
                }
            }

            if (Configuration::get('MP_STORE_PICKUP_DATE')
                && Tools::getValue('enableDateSelection')
            ) {
                if (!Validate::isUnsignedInt(Tools::getValue('mp_minimum_days'))) {
                    $this->errors[] = $this->l('Minimum days field is invalid.');
                }
                if (!Validate::isUnsignedInt(Tools::getValue('mp_maximum_days'))) {
                    $this->errors[] = $this->l('Maximum days field is invalid.');
                }
                if (Tools::getValue('enableTimeSelection')) {
                    if (!Validate::isUnsignedInt(Tools::getValue('mp_minimum_hours'))) {
                        $this->errors[] = $this->l('Minimum hours field is invalid.');
                    }
                }
                if (!Validate::isUnsignedInt(Tools::getValue('mp_max_pickup'))) {
                    $this->errors[] = $this->l('Maximum pick ups field is invalid.');
                }
            }

            if (Configuration::get('MP_STORE_COUNTRY_ENABLE')) {
                if (Tools::getValue('enableCountryRestriction') && empty(Tools::getValue('mp_countries'))) {
                    $this->errors[] = $this->l('Select atleast 1 country');
                }
            }
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Marketplace', 'storelist'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard')
        );

        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Store Configuration', 'storeconfiguration'),
            'url' => ''
        );
        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();

        // Register JS
        $this->registerJavascript('mutiselect-js', 'modules/'.$this->module->name.'/views/js/admin/bootstrap-multiselect.js');

        $this->registerStylesheet('mutiselect-css', 'modules/'.$this->module->name.'/views/css/admin/bootstrap-multiselect.css');
        $this->registerStylesheet('store_config-css', 'modules/'.$this->module->name.'/views/css/front/store_config.css');

        $this->registerJavascript(
            'store-configuration-js',
            'modules/'.$this->module->name.'/views/js/front/mp_storeconfiguration.js'
        );


        // Register CSS
        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');
    }
}
