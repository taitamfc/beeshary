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

class MpBookingMpFeaturePricePlanModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->getTranslator()->trans('Marketplace', array(), 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        );
        $breadcrumb['links'][] = array(
            'title' => $this->getTranslator()->trans('Booking Price Plans', array(), 'Breadcrumb'),
            'url' => '',
        );
        return $breadcrumb;
    }

    public function initContent()
    {
        parent::initContent();
        if ($this->context->customer->isLogged(true)) {
            $idCustomer = $this->context->customer->id;
            $mpSellerDetails = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($mpSellerDetails && $mpSellerDetails['active']) {
                $idSeller = $mpSellerDetails['id_seller'];
                $dateFrom = date('d-m-Y');
                $dateTo = date('d-m-Y', strtotime("+1 day", strtotime($dateFrom)));
                $featurePriceInfo = array();
                $idFeaturePrice = Tools::getValue('id_feature_price_rule');
                $formActionParams = array();

                if ($idFeaturePrice) {
                    if (Validate::isLoadedObject(
                        $featurePriceInfo = new WkMpBookingProductFeaturePricing($idFeaturePrice)
                    )) {
                        if (Validate::isLoadedObject(
                            $objBookingProductInfo = new WkMpBookingProductInformation(
                                $featurePriceInfo->id_booking_product_info
                            )
                        )) {
                            if ($objBookingProductInfo->id_seller != $idSeller) {
                                Tools::redirect(
                                    $this->context->link->getModuleLink('mpbooking', 'mpfeaturepriceplanslist')
                                );
                            }
                            if ($objBookingProductInfo->id_mp_product) {
                                $mpProduct = WkMpSellerProduct::getSellerProductByIdProduct(
                                    $objBookingProductInfo->id_mp_product,
                                    $this->context->language->id
                                );
                                $smartyVars['productName'] = $mpProduct['product_name'];
                                $smartyVars['edit'] = 1;
                            }
                        }
                    }
                    if ($featurePriceInfo->special_days) {
                        $smartyVars['special_days'] = json_decode($featurePriceInfo->special_days, true);
                    }
                    $formActionParams['id_feature_price_rule'] = $idFeaturePrice;
                }
                $smartyVars['form_action'] = $this->context->link->getModuleLink(
                    'mpbooking',
                    'mpfeaturepriceplan',
                    $formActionParams
                );
                $objDefaultCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
                $smartyVars['id_seller'] = $idSeller;
                $smartyVars['module_dir'] = _MODULE_DIR_;
                $smartyVars['featurePriceInfo'] = $featurePriceInfo;
                $smartyVars['default_lang'] = $mpSellerDetails['default_lang'];
                $smartyVars['defaultCurrencySign'] = $objDefaultCurrency->sign;
                $smartyVars['logic'] = 'mpfeaturepriceplans';
                $smartyVars['logged'] = $this->context->customer->isLogged();
                $smartyVars['date_from'] = $dateFrom;
                $smartyVars['date_to'] = $dateTo;

                //assign default variables
                WkMpHelper::assignGlobalVariables();
                WkMpHelper::assignDefaultLang($idSeller);

                $objDefaultCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
                Media::addJsDef(
                    array(
                        'defaultcurrency_sign' => $objDefaultCurrency->sign,
                        'autocomplete_product_search_url' => $this->context->link->getModuleLink(
                            'mpbooking',
                            'mpfeaturepriceplan'
                        ),
                    )
                );

                $this->context->smarty->assign($smartyVars);
                $this->setTemplate('module:mpbooking/views/templates/front/mpfeaturepriceplan.tpl');
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('SubmitFeaturePricePlan') || Tools::isSubmit('StayFeaturePricePlan')) {
            $idBookingProductInfo = Tools::getValue('id_booking_product_info');
            if (Validate::isLoadedObject(
                $objBookingProductInfo = new WkMpBookingProductInformation($idBookingProductInfo)
            )) {
                $idMpProduct = $objBookingProductInfo->id_mp_product;
                $idPsProduct = $objBookingProductInfo->id_product;
                $idFeaturePrice = Tools::getValue('id_feature_price_rule');
                if (!isset($idFeaturePrice) || !$idFeaturePrice) {
                    $idFeaturePrice = 0;
                }
                $dateFrom = date('Y-m-d', strtotime(Tools::getValue('date_from')));
                $dateTo = date('Y-m-d', strtotime(Tools::getValue('date_to')));
                $isSpecialDaysExists = Tools::getValue('is_special_days_exists');
                $specialDays = Tools::getValue('special_days');
                $priceImpactWay = Tools::getValue('price_impact_way');
                $priceImpactType = Tools::getValue('price_impact_type');
                $impactValue = Tools::getValue('impact_value');
                $dateSelectionType = Tools::getValue('date_selection_type');
                $specificDate = date('Y-m-d', strtotime(Tools::getValue('specific_date')));
                $jsonSpecialDays = json_encode($specialDays);

                $sellerDefaultLanguage = Tools::getValue('default_lang');
                $defaultLang = WkMpHelper::getDefaultLanguageBeforeFormSave($sellerDefaultLanguage);

                if ($idFeaturePrice) {
                    $objFeaturePricing = new WkMpBookingProductFeaturePricing($idFeaturePrice);
                } else {
                    $objFeaturePricing = new WkMpBookingProductFeaturePricing();
                }

                $languages = Language::getLanguages(false);
                if (!Tools::getValue('feature_price_name_'.$defaultLang)) {
                    if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                        $sellerLang = Language::getLanguage((int) $defaultLang);
                        $this->errors[] = $this->module->l(
                            'Booking price rule name is required in ',
                            'mpfeaturepriceplan'
                        ).$sellerLang['name'];
                    } else {
                        $this->errors[] = $this->module->l('Booking price rule name is required', 'mpfeaturepriceplan');
                    }
                } else {
                    $isPlanTypeExists = 0;
                    if ($dateSelectionType == WkMpBookingProductFeaturePricing::WK_DATE_SELECTION_SPECIFIC_DATE) {
                        $isPlanTypeExists = $objFeaturePricing->checkBookingProductFeaturePriceExistance(
                            $idBookingProductInfo,
                            $specificDate,
                            date('Y-m-d', strtotime("+1 day", strtotime($specificDate))),
                            'specific_date',
                            false,
                            $idFeaturePrice
                        );
                    } elseif ($dateSelectionType == WkMpBookingProductFeaturePricing::WK_DATE_SELECTION_DATE_RANGE
                        && isset($isSpecialDaysExists)
                        && $isSpecialDaysExists == 'on'
                    ) {
                        if ($jsonSpecialDays != "false") {
                            $isPlanTypeExists = $objFeaturePricing->checkBookingProductFeaturePriceExistance(
                                $idBookingProductInfo,
                                $dateFrom,
                                $dateTo,
                                'special_day',
                                $jsonSpecialDays,
                                $idFeaturePrice
                            );
                        }
                    } elseif ($dateSelectionType == WkMpBookingProductFeaturePricing::WK_DATE_SELECTION_DATE_RANGE) {
                        $isPlanTypeExists = $objFeaturePricing->checkBookingProductFeaturePriceExistance(
                            $idBookingProductInfo,
                            $dateFrom,
                            $dateTo,
                            'date_range',
                            false,
                            $idFeaturePrice
                        );
                    }
                    if ($isPlanTypeExists) {
                        $this->errors[] = $this->module->l(
                            'A booking price rule already exists in which some dates are common with this plan.
                            Please select a different date range.',
                            'mpfeaturepriceplan'
                        );
                    } else {
                        if (!$idMpProduct) {
                            $this->errors[] = $this->module->l(
                                'Product is not selected. Please try again.',
                                'mpfeaturepriceplan'
                            );
                        }
                        $validateRules = call_user_func(
                            array('WkMpBookingProductFeaturePricing', 'getValidationRules'),
                            'WkMpBookingProductFeaturePricing'
                        );
                        foreach ($languages as $language) {
                            $languageName = '';
                            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                                $languageName = '('.$language['name'].')';
                            }
                            if (!Validate::isCatalogName(Tools::getValue('feature_price_name_'.$language['id_lang']))) {
                                 $this->errors[] = $this->module->l('Booking price rule name is invalid in ', 'mpfeaturepriceplan').
                                 $languageName;
                            } elseif (Tools::strlen(Tools::getValue('feature_price_name_'.$language['id_lang'])) > $validateRules['sizeLang']['feature_price_name']) {
                                sprintf(
                                    $this->module->l(
                                        'Booking price rule Name field is too long (%2$d chars max).',
                                        'mpfeaturepriceplan'
                                    ),
                                    $ValidateRules['sizeLang']['feature_price_name']
                                );
                            }
                        }

                        if ($dateSelectionType == 1) {
                            if ($dateFrom == '') {
                                $this->errors[] = $this->module->l(
                                    'Please choose Date from for the booking price rule.',
                                    'mpfeaturepriceplan'
                                );
                            } elseif (!Validate::isDate($dateFrom)) {
                                $this->errors[] = $this->module->l('Invalid Date From.', 'mpfeaturepriceplan');
                            }

                            if ($dateTo == '') {
                                $this->errors[] = $this->module->l(
                                    'Please choose Date to for the booking price rule.',
                                    'mpfeaturepriceplan'
                                );
                            } elseif (!Validate::isDate($dateTo)) {
                                $this->errors[] = $this->module->l('Invalid Date To.', 'mpfeaturepriceplan');
                            }
                            if (strtotime($dateTo) < strtotime($dateFrom)) {
                                $this->errors[] = $this->module->l(
                                    'Date To must be a date after Date From.',
                                    'mpfeaturepriceplan'
                                );
                            }
                            if (isset($isSpecialDaysExists) && $isSpecialDaysExists == 'on') {
                                $isSpecialDaysExists = 1;
                                if (!isset($specialDays) || !$specialDays) {
                                    $isSpecialDaysExists = 0;
                                    $this->errors[] = $this->module->l(
                                        'Please select at least one day for the special day selection.',
                                        'mpfeaturepriceplan'
                                    );
                                }
                            } else {
                                $isSpecialDaysExists = 0;
                            }
                        } else {
                            if ($specificDate == '') {
                                $this->errors[] = $this->module->l(
                                    'Please choose Date from for the booking price rule.',
                                    'mpfeaturepriceplan'
                                );
                            } elseif (!Validate::isDate($specificDate)) {
                                $this->errors[] = $this->module->l('Invalid Date From.', 'mpfeaturepriceplan');
                            }
                        }

                        if (!$impactValue) {
                            $this->errors[] = $this->module->l('Please enter a valid imapct value.', 'mpfeaturepriceplan');
                        } else if ($priceImpactType == 1 && $impactValue > 100) {
                            $this->errors[] = $this->module->l('Invalid precentage impact value.', 'mpfeaturepriceplan');
                        } elseif (!Validate::isPrice($impactValue)) {
                            $this->errors[] = $this->module->l('Invalid Impact Value.', 'mpfeaturepriceplan');
                        }

                        $mpSellerDetails = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
                        if ($mpSellerDetails) {
                            $idSeller = $mpSellerDetails['id_seller'];
                        }

                        if (empty($idSeller)) {
                            $this->errors[] = $this->module->l('Please select a seller first.', 'mpfeaturepriceplan');
                        }

                        if (!count($this->errors)) {
                            if ($idFeaturePrice) {
                                $objFeaturePricing = new WkMpBookingProductFeaturePricing($idFeaturePrice);
                            } else {
                                $objFeaturePricing = new WkMpBookingProductFeaturePricing();
                            }
                            $objFeaturePricing->id_booking_product_info = $idBookingProductInfo;

                            foreach ($languages as $language) {
                                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                                    if (Tools::getValue('feature_price_name_'.$language['id_lang'])) {
                                        $bookingPriceRuleName = Tools::getValue('feature_price_name_'.$language['id_lang']);
                                    } else {
                                        $bookingPriceRuleName = Tools::getValue('feature_price_name_'.$defaultLang);
                                    }
                                } else {
                                    $bookingPriceRuleName = Tools::getValue('feature_price_name_'.$defaultLang);
                                }
                                $objFeaturePricing->feature_price_name[$language['id_lang']] = $bookingPriceRuleName;
                            }
                            $objFeaturePricing->date_selection_type = $dateSelectionType;
                            if ($dateSelectionType == 1) {
                                $objFeaturePricing->date_from = $dateFrom;
                                $objFeaturePricing->date_to = $dateTo;
                            } else {
                                $objFeaturePricing->date_from = $specificDate;
                                $objFeaturePricing->date_to = date(
                                    'Y-m-d',
                                    strtotime("+1 day", strtotime($specificDate))
                                );
                            }
                            $objFeaturePricing->impact_way = $priceImpactWay;
                            $objFeaturePricing->is_special_days_exists = $isSpecialDaysExists;
                            $objFeaturePricing->special_days = $jsonSpecialDays;
                            $objFeaturePricing->impact_type = $priceImpactType;
                            $objFeaturePricing->impact_value = $impactValue;
                            $objFeaturePricing->active = 1;
                            if ($objFeaturePricing->save()) {
                                if ($idFeaturePrice) {
                                    $params = array('edited_conf' => 1);
                                } else {
                                    $params = array('created_conf' => 1);
                                }
                                if (Tools::isSubmit('StayFeaturePricePlan')) {
                                    $params['id_feature_price_rule'] = $objFeaturePricing->id;
                                    Tools::redirect(
                                        $this->context->link->getModuleLink('mpbooking', 'mpfeaturepriceplan', $params)
                                    );
                                } else {
                                    Tools::redirect(
                                        $this->context->link->getModuleLink('mpbooking', 'mpfeaturepriceplanslist', $params)
                                    );
                                }
                            }
                        }
                    }
                }
            } else {
                $this->errors[] = $this->module->l('Selected seller booking product not found', 'mpfeaturepriceplan');
            }
        }
        parent::postProcess();
    }

    public function displayAjaxSearchMpBookingProductByName()
    {
        $mpProductName = Tools::getValue('product_name');
        $idSeller = Tools::getValue('id_seller');
        if ($mpProductName) {
            $bookingProductsByName = WkMpBookingProductInformation::searchSellerBookingProductByName(
                $this->context->language->id,
                $mpProductName,
                $idSeller
            );
            if ($bookingProductsByName) {
                echo json_encode($bookingProductsByName, true);
                die;
            } else {
                die(
                    json_encode(
                        array(
                            'status' => 'failed',
                            'msg' => $this->module->l('No match found for entered product name.', 'mpfeaturepriceplan')
                        )
                    )
                );
            }
        } else {
            die(
                json_encode(
                    array(
                        'status' => 'failed',
                        'msg' => $this->module->l('No match found for entered product name.', 'mpfeaturepriceplan')
                    )
                )
            );
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryUI(array('ui.slider', 'ui.datepicker'));
        //data table file included
        $this->registerStylesheet('datatable_bootstrap', 'modules/marketplace/views/css/datatable_bootstrap.css');
        $this->registerJavascript('mp-jquery-dataTables', 'modules/marketplace/views/js/jquery.dataTables.min.js');
        $this->registerJavascript('mp-dataTables.bootstrap', 'modules/marketplace/views/js/dataTables.bootstrap.js');

        $this->registerJavascript('mp-change_multilang', 'modules/marketplace/views/js/change_multilang.js');
        //add marketplace module css
        $this->registerStylesheet('marketplace-account-css', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerStylesheet('mp-global-style-css', 'modules/marketplace/views/css/mp_global_style.css');
        $this->registerStylesheet('mp-header-style-css', 'modules/marketplace/views/css/mp_header.css');

        // mpbooking module css and js
        $this->registerStylesheet('wk-datepicker-custom-css', 'modules/mpbooking/views/css/wk-datepicker-custom.css');
        $this->registerStylesheet(
            'wk-featureprice-plans-css',
            'modules/mpbooking/views/css/front/wk-mp-feature-price-plans.css'
        );
        $this->registerStylesheet(
            'wk-global-style',
            'modules/'.$this->module->name.'/views/css/wk-booking-global-style.css'
        );
        $this->registerJavascript(
            'mpbooking-wk-feature-price-plans',
            'modules/mpbooking/views/js/front/wk-mp-feature-price-plans.js'
        );
    }
}
