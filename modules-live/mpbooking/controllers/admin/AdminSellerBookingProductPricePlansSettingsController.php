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

class AdminSellerBookingProductPricePlansSettingsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->className = 'WkMpBookingProductFeaturePricing';
        $this->table = 'wk_mp_booking_product_feature_pricing';
        $this->bootstrap = true;
        $this->identifier = 'id_feature_price_rule';
        parent::__construct();

        $this->_join .= 'JOIN `'._DB_PREFIX_.'wk_mp_booking_product_info` bpi
        ON (bpi.`id_booking_product_info` = a.`id_booking_product_info`)';
        $this->_join .= ' JOIN `'._DB_PREFIX_.'wk_mp_seller_product` msp ON
        (msp.`id_mp_product` = bpi.`id_mp_product`)';
        $this->_join .= ' JOIN `'._DB_PREFIX_.'wk_mp_seller_product_lang` mspl ON
        (mspl.`id_mp_product` = msp.`id_mp_product`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'wk_mp_booking_product_feature_pricing_lang` bfpl
        ON (a.id_feature_price_rule = bfpl.id_feature_price_rule)';
        $this->_join .= ' JOIN `'._DB_PREFIX_.'wk_mp_seller` mpsi ON (mpsi.`id_seller` = msp.`id_seller`)';

        $this->_select = 'bfpl.`feature_price_name`, bpi.`id_mp_product`, mspl.`product_name`, mpsi.`business_email`,
        mpsi.`id_seller`, CONCAT(mpsi.`seller_firstname`, " ", mpsi.`seller_lastname`) as seller_name';
        $this->_select .= ' ,IF(a.impact_type=1 , CONCAT(round(a.impact_value, 2), " ",  "%"), a.impact_value)
        AS impact_value';
        $this->_select .= ' ,IF(a.impact_type=1 , \''.$this->l('Percentage').'\', \''.$this->l('Fixed Amount').'\')
        AS impact_type';
        $this->_select .= ' ,IF(a.impact_way=1 , \''.$this->l('Decrease').'\', \''.$this->l('Increase').
        '\') AS impact_way';

        $this->_where = 'AND mspl.`id_lang` = '.(int) $this->context->language->id.
        ' AND bpi.`id_seller` != 0 AND bfpl.`id_lang` = '.(int) $this->context->language->id;;

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icosn-trash',
            ),
        );
        $impactWays = array(1 => $this->l('decrease'), 2 => $this->l('increase'));
        $impactTypes = array(1 => $this->l('Percentage'), 2 => $this->l('Fixed Price'));
        $this->fields_list = array(
            'id_feature_price_rule' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'product_name' => array(
                'title' => $this->l('Product Name'),
                'havingFilter' => true,
                'callback' => 'getBookingProductlink',
            ),
            'seller_name' => array(
                'title' => $this->l('Seller'),
                'havingFilter' => true,
                'callback' => 'getSellerDisplayData',
                'hint' => $this->l('Search seller with seller\'s name.'),
            ),
            'feature_price_name' => array(
                'title' => $this->l('Booking Rule Name'),
            ),
            'impact_way' => array(
                'title' => $this->l('Impact Way'),
                'type' => 'select',
                'list' => $impactWays,
                'filter_key' => 'a!impact_way',
            ),
            'impact_type' => array(
                'title' => $this->l('Impact Type'),
                'type' => 'select',
                'list' => $impactTypes,
                'filter_key' => 'a!impact_type',
            ),
            'impact_value' => array(
                'title' => $this->l('Impact Value'),
                'type' => 'price',
                'currency' => true,
                'callback' => 'setOrderCurrency',
            ),
            'date_from' => array(
                'title' => $this->l('Date From'),
                'type' => 'date',
            ),
            'date_to' => array(
                'title' => $this->l('Date To'),
                'type' => 'date',
            ),
            'active' => array(
                'align' => 'center',
                'title' => $this->l('Status'),
                'active' => 'status',
                'type' => 'bool',
            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
        );
        $this->list_no_link = true;
    }

    public function getBookingProductlink($productName, $row)
    {
        $displayData = '';
        if ($productName && $row['id_mp_product']) {
            $displayData .= '<a target="blank" href="'.
            $this->context->link->getAdminLink('AdminSellerBookingProductDetail').'&id_mp_product='.
            $row['id_mp_product'].'&updatewk_mp_seller_product">'.$productName.'(#'.$row['id_mp_product'].')'.'</a>';
        }
        return $displayData;
    }

    public function getSellerDisplayData($sellerName, $row)
    {
        $displayData = '';
        if ($sellerName && $row['business_email'] && $row['id_seller']) {
            $displayData .= $sellerName.' (#'.$row['id_seller'].')<br>';
            $displayData .= '<a target="blank" href="'.$this->context->link->getAdminLink('AdminSellerInfoDetail').
            '&id_seller='.$row['id_seller'].'&viewwk_mp_seller">'.$row['business_email'].'</a>';
        }
        return $displayData;
    }

    // [setOrderCurrency description] - A callback function for setting currency sign with amount.
    public function setOrderCurrency($column)
    {
        $currency_default = Configuration::get('PS_CURRENCY_DEFAULT');
        return Tools::displayPrice($column, (int) $currency_default);
    }

    public function initToolbar()
    {
        if (WkMpSeller::getAllSeller()) {
            parent::initToolbar();
            $this->page_header_toolbar_btn['new'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->l('Add booking price rule'),
                'imgclass' => 'new',
            );
        }
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
    }

    public function renderForm()
    {
        $objCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $currencySign = $objCurrency->sign;
        $dateFrom = date('d-m-Y');
        $dateTo = date('d-m-Y', strtotime("+1 day", strtotime($dateFrom)));
        $smartyVars['sellerInfoObj'] = array();
        $currentLangId = Configuration::get('PS_LANG_DEFAULT');
        $smartyVars['languages'] = Language::getLanguages(false);
        $smartyVars['currentLang'] = Language::getLanguage((int) $currentLangId);
        if ($this->display == 'edit') {
            if ($idFeaturePrice = Tools::getValue('id_feature_price_rule')) {
                if (Validate::isLoadedObject(
                    $featurePriceInfo = new WkMpBookingProductFeaturePricing($idFeaturePrice)
                )) {
                    if (Validate::isLoadedObject(
                        $objBookingProductInfo = new WkMpBookingProductInformation(
                            $featurePriceInfo->id_booking_product_info
                        )
                    )) {
                        if ($idMpProduct = $objBookingProductInfo->id_mp_product) {
                            $objSellerProduct = new WkMpSellerProduct($idMpProduct);
                            if (Validate::isLoadedObject($objSellerProduct)) {
                                $objSeller = new WkMpSeller($objSellerProduct->id_seller);
                                $smartyVars['sellerInfoObj'] = $objSeller;
                            }
                            if ($objBookingProductInfo->id_mp_product) {
                                $mpProduct = WkMpSellerProduct::getSellerProductByIdProduct(
                                    $objBookingProductInfo->id_mp_product,
                                    $this->context->language->id
                                );
                                $smartyVars['productName'] = $mpProduct['product_name'];
                            }
                        }
                    }
                }
                if ($featurePriceInfo->special_days) {
                    $smartyVars['special_days'] = json_decode($featurePriceInfo->special_days, true);
                }
            }
            $smartyVars['edit'] = 1;
            $smartyVars['featurePriceInfo'] = $featurePriceInfo;
        } elseif ($this->display == 'add') {
            $customerInfo = WkMpSeller::getAllSeller();
            if ($customerInfo) {
                $smartyVars['customer_info'] = $customerInfo;
                //get first seller from the list
                $firstSellerDetails = $customerInfo[0];
                $mpIdSeller = $firstSellerDetails['id_seller'];
            } else {
                $mpIdSeller = 0;
            }
        }
        $smartyVars['defaultcurrency_sign'] = $currencySign;
        $smartyVars['date_from'] = $dateFrom;
        $smartyVars['date_to'] = $dateTo;

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
        $idBookingProductInfo = Tools::getValue('id_booking_product_info');
        if (Validate::isLoadedObject(
            $objBookingProductInfo = new WkMpBookingProductInformation($idBookingProductInfo)
        )) {
            $idMpProduct = $objBookingProductInfo->id_mp_product;
            $idFeaturePriceRule = Tools::getValue('id_feature_price_rule');
            if (!isset($idFeaturePriceRule) || !$idFeaturePriceRule) {
                $idFeaturePriceRule = 0;
            }
            $idSeller = Tools::getValue('id_seller');
            $enableFeaturePrice = Tools::getValue('enable_feature_price');
            $featurePriceName = Tools::getValue('feature_price_name');
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
            $defaultLang = Configuration::get('PS_LANG_DEFAULT');

            if ($idFeaturePriceRule) {
                $objFeaturePricing = new WkMpBookingProductFeaturePricing($idFeaturePriceRule);
            } else {
                $objFeaturePricing = new WkMpBookingProductFeaturePricing();
            }
            $languages = Language::getLanguages(false);
            $objDefaultLang = new language($defaultLang);
            if (!Tools::getValue('feature_price_name_'.$defaultLang)) {
                $this->errors[] = sprintf(
                    $this->l('Booking price rule name is required at least in %s'),
                    $objDefaultLang->name
                );
            } else {
                $isPlanTypeExists = 0;
                if (!$idSeller) {
                    $this->errors[] = $this->l('Please select a seller first.');
                }

                if ($dateSelectionType == WkMpBookingProductFeaturePricing::WK_DATE_SELECTION_SPECIFIC_DATE) {
                    $isPlanTypeExists = $objFeaturePricing->checkBookingProductFeaturePriceExistance(
                        $idBookingProductInfo,
                        $specificDate,
                        date('Y-m-d', strtotime("+1 day", strtotime($specificDate))),
                        'specific_date',
                        false,
                        $idFeaturePriceRule
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
                            $idFeaturePriceRule
                        );
                    }
                } elseif ($dateSelectionType == WkMpBookingProductFeaturePricing::WK_DATE_SELECTION_DATE_RANGE) {
                    $isPlanTypeExists = $objFeaturePricing->checkBookingProductFeaturePriceExistance(
                        $idBookingProductInfo,
                        $dateFrom,
                        $dateTo,
                        'date_range',
                        false,
                        $idFeaturePriceRule
                    );
                }
                if ($isPlanTypeExists) {
                    $this->errors[] = $this->l('A booking price rule already exists in which some dates are common with
                     this plan. Please select a different date range.');
                } else {
                    if (!$idMpProduct) {
                        $this->errors[] = $this->l('Product is not selected. Please try again.');
                    }

                    $validateRules = call_user_func(
                        array('WkMpBookingProductFeaturePricing', 'getValidationRules'),
                        'WkMpBookingProductFeaturePricing'
                    );
                    foreach ($languages as $language) {
                        if (!Validate::isCatalogName(Tools::getValue('feature_price_name_'.$language['id_lang']))) {
                             $this->errors[] = $this->l('Feature price name is invalid in ').$language['name'];
                        } elseif (Tools::strlen(Tools::getValue('feature_price_name_'.$language['id_lang'])) > $validateRules['sizeLang']['feature_price_name']) {
                            sprintf(
                                $this->l(
                                    'Feature price Name field is too long (%2$d chars max).',
                                    'mpfeaturepriceplan'
                                ),
                                $ValidateRules['sizeLang']['feature_price_name']
                            );
                        }
                    }

                    if ($dateSelectionType == 1) {
                        if ($dateFrom == '') {
                            $this->errors[] = $this->l('Please choose Date from for the booking price rule.');
                        } elseif (!Validate::isDate($dateFrom)) {
                            $this->errors[] = $this->l('Invalid Date From.');
                        }

                        if ($dateTo == '') {
                            $this->errors[] = $this->l('Please choose Date to for the booking price rule.');
                        } elseif (!Validate::isDate($dateTo)) {
                            $this->errors[] = $this->l('Invalid Date To.');
                        }

                        if ($dateTo <= $dateFrom) {
                            $this->errors[] = $this->l('Date To must be a date after Date From.');
                        }
                        if (isset($isSpecialDaysExists) && $isSpecialDaysExists == 'on') {
                            $isSpecialDaysExists = 1;
                            if (!isset($specialDays) || !$specialDays) {
                                $isSpecialDaysExists = 0;
                                $this->errors[] = $this->l('Please select at least one day for the special day
                                selection.');
                            }
                        } else {
                            $isSpecialDaysExists = 0;
                        }
                    } else {
                        if ($specificDate == '') {
                            $this->errors[] = $this->l('Please choose Date from for the booking price rule.');
                        } elseif (!Validate::isDate($specificDate)) {
                            $this->errors[] = $this->l('Invalid Date From.');
                        }
                    }
                    if (!$impactValue) {
                        $this->errors[] = $this->l('Please enter a valid imapct value.');
                    } elseif ($priceImpactType == 1 && $impactValue > 100) {
                        $this->errors[] = $this->l('Invalid precentage impact value.');
                    } elseif (!Validate::isPrice($impactValue)) {
                        $this->errors[] = $this->l('Invalid Impact Value.');
                    }
                    if (!count($this->errors)) {
                        if ($idFeaturePriceRule) {
                            $objFeaturePricing = new WkMpBookingProductFeaturePricing($idFeaturePriceRule);
                        } else {
                            $objFeaturePricing = new WkMpBookingProductFeaturePricing();
                        }
                        $objFeaturePricing->id_booking_product_info = $idBookingProductInfo;

                        foreach ($languages as $language) {
                            if (Tools::getValue('feature_price_name_'.$language['id_lang'])) {
                                $bookingPriceRuleName = Tools::getValue('feature_price_name_'.$language['id_lang']);
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
                        $objFeaturePricing->active = $enableFeaturePrice;
                        if ($objFeaturePricing->save()) {
                            if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                                Tools::redirectAdmin(
                                    self::$currentIndex.'&id_feature_price_rule='.
                                    (int) $objFeaturePricing->id.'&update'.
                                    $this->table.'&conf=4&token='.$this->token
                                );
                            } else {
                                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                            }
                        }
                    }
                }
            }
        } else {
            $this->errors[] = $this->l('Selected booking product not found');
        }
        if (isset($idFeaturePriceRule) && $idFeaturePriceRule) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }
    }

    public function ajaxProcessSearchBookingProductByName()
    {
        $productName = Tools::getValue('product_name');
        $idSeller = Tools::getValue('id_seller');
        if ($productName && $idSeller) {
            $objBookingProductInfo = new WkMpBookingProductInformation();
            $productsByName = $objBookingProductInfo->searchSellerBookingProductByName(
                $this->context->language->id,
                $productName,
                $idSeller
            );
            if ($productsByName) {
                echo json_encode($productsByName, true);
                die;
            } else {
                die(
                    json_encode(
                        array(
                            'status' => 'failed',
                            'msg' => $this->l('No match found for entered product name.')
                        )
                    )
                );
            }
        } else {
            die(
                json_encode(
                    array(
                        'status' => 'failed',
                        'msg' => $this->l('No match found for entered product name.')
                    )
                )
            );
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme = false);
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/admin/wk-feature-price-setting.js');
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/admin/wk-feature-price-setting.css');
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/wk-datepicker-custom.css');
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/wk-booking-global-style.css');
    }
}
