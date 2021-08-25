<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminMpSellerShippingController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();

        $this->table = 'mp_shipping_method';
        $this->className = 'MpShippingMethod';
        $this->list_no_link = true;
        $this->identifier = 'id';

        parent::__construct();

        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'mp_shipping_method_lang` mpshl ON (mpshl.`id` = a.`id`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller` mpsi ON (a.`mp_id_seller`=mpsi.id_seller)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_lang` mpsil ON (mpsil.`id_seller` = mpsi.`id_seller` AND mpsil.`id_lang` = '.$this->context->language->id.')';
        $this->_select .= 'CONCAT(mpsi.`seller_firstname`, " ", mpsi.`seller_lastname`) as seller_name, mpsi.`shop_name_unique`, a.`id` as `ship_id`, mpshl.`transit_delay`';
        $this->_where .= 'AND a.`is_done` = 1 AND a.`deleted` = 0 AND mpshl.`id_lang` = '.(int) $this->context->language->id;

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
                            'delete' => array(
                                'text' => $this->l('Delete selected'),
                                'confirm' => $this->l('Delete selected items?'),
                                'icon' => 'icon-trash', ),
                        );

        $this->fields_list = array(
                'id' => array(
                    'title' => $this->l('ID') ,
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                ),
                'id_ps_reference' => array(
                    'title' => $this->l('Prestashop Carrier ID'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                    'hint' => $this->l('Generated Prestashop Carrier ID in Carriers'),
                    'callback' => 'displayPsCarrierIdByReference',
                ),
                'mp_shipping_name' => array(
                    'title' => $this->l('Shipping Name') ,
                    'align' => 'center',
                    'havingFilter' => true,
                ),
                'seller_name' => array(
                    'title' => $this->l('Seller Name') ,
                    'align' => 'center',
                    'havingFilter' => true,
                ),
                'shop_name_unique' => array(
                    'title' => $this->l('Unique Shop Name') ,
                    'align' => 'center',
                    'havingFilter' => true,

                ),
                'active' => array(
                    'title' => $this->l('Status'),
                    'active' => 'status',
                    'align' => 'center',
                    'type' => 'bool',
                    'orderby' => false,
                ),
                'ship_id' => array(
                    'title' => $this->l('Assign Impact Price'),
                    'width' => 35,
                    'align' => 'center',
                    'callback' => 'assignImpact',
                    'orderby' => false,
                    'search' => false,
                    'remove_onclick' => true,
                ),
            );

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
    }

    public function displayPsCarrierIdByReference($idPsReference)
    {
        if ($idPsReference) {
            if ($objCarrier = Carrier::getCarrierByReference($idPsReference)) {
                return $objCarrier->id;
            }
        }

        return '-';
    }

    public function assignImpact($mpShippingId)
    {
        $objMpShippingMethod = new MpShippingMethod($mpShippingId);
        if ($objMpShippingMethod->is_free) {
            $html = $this->l('Free');
        } else {
            $html = '<a class="edit btn btn-default" title="'.$this->l('View').'" href="'.self::$currentIndex.'&id='.$mpShippingId.'&updatemp_shipping_method&updateimpact=1&token='.$this->token.'"><i class="icon-search-plus"></i>'.$this->l('View').'</a>';
        }

        return $html;
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add New')
        );
    }

    public function renderForm()
    {
        $link = new Link();
        //$idLang = $this->context->language->id;
        $objMpSellerInfo = new WkMpSeller();
        //get total zone available in prestashop
        $zoneDetail = Zone::getZones(true);
        $this->context->smarty->assign('zones', $zoneDetail);
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

        //Get customer group
        if ($customerAllGroups = Group::getGroups($this->context->language->id)) {
            $this->context->smarty->assign('customerAllGroups', $customerAllGroups);
        }

        $mpShippingId = Tools::getValue('id');
        if ($mpShippingId) { //Edit shipping
            $objMpShippingMethod = new MpShippingMethod($mpShippingId);
            $this->context->smarty->assign('mp_shipping_id', $mpShippingId);
            $this->context->smarty->assign('mp_shipping_name', $objMpShippingMethod->mp_shipping_name);
            //tax options for seller shipping
            $this->context->smarty->assign('tax_rules', TaxRulesGroup::getTaxRulesGroups(true));
            $this->context->smarty->assign('id_tax_rule_group', $objMpShippingMethod->id_tax_rule_group);
            //end
            $this->context->smarty->assign('range_behavior', $objMpShippingMethod->range_behavior);
            $this->context->smarty->assign('transit_delay', $objMpShippingMethod->transit_delay);
            $this->context->smarty->assign('shipping_method', $objMpShippingMethod->shipping_method);
            $this->context->smarty->assign('tracking_url', $objMpShippingMethod->tracking_url);
            $this->context->smarty->assign('grade', $objMpShippingMethod->grade);
            $this->context->smarty->assign('shipping_handling', $objMpShippingMethod->shipping_handling);
            $this->context->smarty->assign('shipping_handling_charge', Configuration::get('PS_SHIPPING_HANDLING'));
            $this->context->smarty->assign('is_free', $objMpShippingMethod->is_free);
            $this->context->smarty->assign('max_width', $objMpShippingMethod->max_width);
            $this->context->smarty->assign('max_height', $objMpShippingMethod->max_height);
            $this->context->smarty->assign('max_depth', $objMpShippingMethod->max_depth);
            $this->context->smarty->assign('max_weight', $objMpShippingMethod->max_weight);
            $this->context->smarty->assign('mpShippingActive', $objMpShippingMethod->active);

            $mpIdSeller = $objMpShippingMethod->mp_id_seller;
            $sellerCustomerId = $objMpSellerInfo->getCustomerIdBySellerId($mpIdSeller);
            $this->context->smarty->assign('seller_customer_id', $sellerCustomerId);

            //Get Shipping group
            if ($shippingGroup = MpShippingMethod::getShippingGroup($mpShippingId)) {
                $this->context->smarty->assign('shippingGroup', array_column($shippingGroup, 'id_group'));
            }

            //@shippingMethod==1 billing accroding to weight
            //@shippingMethod==2 billing accroding to price
            $shippingMethod = $objMpShippingMethod->shipping_method;
            $ranges = array();
            if ($objMpShippingMethod->shipping_method == 1) {
                //find all range according to weight available for this shipping method
                $objRangeWeight = new MpRangeWeight();
                $objRangeWeight->mp_shipping_id = $mpShippingId;
                $differentRange = $objRangeWeight->getAllRangeAccordingToShippingId();
                if ($differentRange) {
                    $ranges = $differentRange;
                } else {
                    $this->context->smarty->assign('different_range', -1);
                }
            } elseif ($objMpShippingMethod->shipping_method == 2) {
                // find range by price available for shipping method
                $objRangePrice = new MpRangePrice();
                $objRangePrice->mp_shipping_id = $mpShippingId;
                $differentRange = $objRangePrice->getAllRangeAccordingToShippingId();
                if ($differentRange) {
                    $ranges = $differentRange;
                } else {
                    $this->context->smarty->assign('different_range', -1);
                }
            }

            if (!count($ranges)) {
                $ranges[] = array('id_range' => 0, 'delimiter1' => 0, 'delimiter2' => 0);
            }

            $this->context->smarty->assign('ranges', $ranges);

            Media::addJsDef(array('mp_shipping_id' => $mpShippingId, 'is_free' => $objMpShippingMethod->is_free, 'shipping_handling' => $objMpShippingMethod->shipping_handling, 'shipping_method' => $shippingMethod));

            //find zone where shipping method deliver product
            $objMpDelivery = new MpShippingDelivery();
            $idZoneDetails = $objMpDelivery->getIdZoneByShiipingId($mpShippingId);
            if ($idZoneDetails) {
                $fieldsValue = array();
                foreach ($idZoneDetails as $idZoneDetail) {
                    $fieldsValue['zones'][$idZoneDetail['id_zone']] = 1;
                }

                $this->context->smarty->assign('fields_val', $fieldsValue);

                //get delivery details by shipping id its provide price for different range
                $deliveryShippingDetail = $objMpDelivery->getDeliveryDetailByShiipingId($mpShippingId);

                if ($deliveryShippingDetail) {
                    $priceByRange = array();
                    foreach ($deliveryShippingDetail as $deliveryShipping) {
                        if ($shippingMethod == 2) {
                            $priceByRange[$deliveryShipping['mp_id_range_price']][$deliveryShipping['id_zone']] = round($deliveryShipping['base_price'], 2);
                        } else {
                            $priceByRange[$deliveryShipping['mp_id_range_weight']][$deliveryShipping['id_zone']] = round($deliveryShipping['base_price'], 2);
                        }
                    }

                    $this->context->smarty->assign('price_by_range', $priceByRange);
                }
            }

            if (Tools::getValue('updateimpact') == '1') {
                if ($objMpShippingMethod->is_free) {
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminMpSellerShipping'));
                } else {
                    $getImpactPriceArr = MpShippingImpact::getAllImpactPriceByMpshippingid($mpShippingId);
                    if ($getImpactPriceArr) {
                        $impactPriceArr = array();
                        foreach ($getImpactPriceArr as $key => $getImpactPrice) {
                            $zoneArr = MpShippingImpact::getZonenameByZoneid($getImpactPrice['id_zone']);
                            $impactPriceArr[$key]['id_zone'] = $zoneArr['name'];

                            $countryName = CountryCore::getNameById($this->context->language->id, $getImpactPrice['id_country']);
                            $impactPriceArr[$key]['id_country'] = $countryName;

                            if ($getImpactPrice['id_state']) {
                                $stateName = StateCore::getNameById($getImpactPrice['id_state']);
                                $impactPriceArr[$key]['id_state'] = $stateName;
                            } else {
                                $impactPriceArr[$key]['id_state'] = 'All';
                            }

                            $impactPriceArr[$key]['shipping_delivery_id'] = $getImpactPrice['shipping_delivery_id'];
                            $impactPriceArr[$key]['impact_price'] = $getImpactPrice['impact_price'];
                            $impactPriceArr[$key]['impact_price_display'] = Tools::displayPrice(
                                $getImpactPrice['impact_price'],
                                $currency
                            );
                            $impactPriceArr[$key]['id'] = $getImpactPrice['id'];
                            $impactPriceArr[$key]['mp_shipping_id'] = $mpShippingId;
                            /*Range Or weight for the impact*/
                            $objMpShippingDeliv = new MpShippingDelivery($getImpactPrice['shipping_delivery_id']);
                            if ($shippingMethod == 2) {
                                $objRange = new MpRangePrice($objMpShippingDeliv->mp_id_range_price);
                                $impactPriceArr[$key]['price_range'] = Tools::ps_round($objRange->delimiter1, 2).'-'.Tools::ps_round($objRange->delimiter2, 2);
                            } else {
                                $objWeight = new MpRangeWeight($objMpShippingDeliv->mp_id_range_weight);
                                $impactPriceArr[$key]['weight_range'] = Tools::ps_round($objWeight->delimiter1, 2).'-'.Tools::ps_round($objWeight->delimiter2, 2);
                            }
                            /*END*/
                        }
                        $this->context->smarty->assign('ship_method', $shippingMethod);
                        $this->context->smarty->assign('impactprice_arr', $impactPriceArr);
                    }

                    $shippingAjaxLink = $link->getModuleLink('mpshipping', 'shippingajax');
                    $this->context->smarty->assign('mpshipping_id', $mpShippingId);
                    $this->context->smarty->assign('shipping_ajax_link', $shippingAjaxLink);
                    $this->context->smarty->assign('updateimpact', 1);

                    $jsDefVar = [
                        'shipping_ajax_link' => $shippingAjaxLink,
                        'img_ps_dir' => _MODULE_DIR_.'marketplace/views/img/',
                        'select_country' => $this->l('Select country'),
                        'select_state' => $this->l('All'),
                        'zone_error' => $this->l('Select Zone'),
                        'no_range_available_error' => $this->l('No Range Available'),
                        'ranges_info' => $this->l('Ranges'),
                        'message_impact_price_error' => $this->l('Price should be numeric'),
                        'message_impact_price' => $this->l('Impact added sucessfully'),
                    ];

                    Media::addJsDef($jsDefVar);
                }
            }
        } else {
            $shippingMethod = 2;
            $this->context->smarty->assign('shipping_method', 2);
            $this->context->smarty->assign('mp_shipping_name', '');
            $this->context->smarty->assign('transit_delay', '');
            $this->context->smarty->assign('tracking_url', '');
            $this->context->smarty->assign('grade', 0);
            $this->context->smarty->assign('shipping_handling_charge', Configuration::get('PS_SHIPPING_HANDLING'));
            $this->context->smarty->assign('max_width', 0);
            $this->context->smarty->assign('max_height', 0);
            $this->context->smarty->assign('max_depth', 0);
            $this->context->smarty->assign('max_weight', 0);
            $this->context->smarty->assign('mpShippingActive', 0);
            //for tax options seller shipping
            $this->context->smarty->assign('tax_rules', TaxRulesGroup::getTaxRulesGroups(true));
            //seller customer information
            $customerInfo = WkMpSeller::getAllSeller();
            if ($customerInfo) {
                $this->context->smarty->assign('customer_info', $customerInfo);

                //get first seller from the list
                $firstSellerDetails = $customerInfo[0];
                $mpIdSeller = $firstSellerDetails['id_seller'];
            } else {
                $mpIdSeller = 0;
            }
        }

        // Multi-lang start
        $adminProductUrl = $this->context->link->getAdminLink('AdminSellerProductDetail');
        $this->context->smarty->assign('adminproducturl', $adminProductUrl);
        // Set default lang at every form according to configuration multi-language
        WkMpHelper::assignDefaultLang($mpIdSeller);
        // Multilang end

        $updateImpactLink = $this->context->link->getAdminLink('AdminMpSellerShipping').'&id='.$mpShippingId.'&updatemp_shipping_method&updateimpact=1';
        $this->context->smarty->assign('update_impact_link', $updateImpactLink);
        $this->context->smarty->assign('modules_dir', _MODULE_DIR_);
        $this->context->smarty->assign('img_ps_dir', _MODULE_DIR_.'mpshipping/views/img/');
        $this->context->smarty->assign('self', dirname(__FILE__));
        $this->context->smarty->assign('currency_sign', $currency->sign);
        $this->context->smarty->assign('PS_WEIGHT_UNIT', Configuration::get('PS_WEIGHT_UNIT'));
        $this->context->smarty->assign('isAdminAddCarrier', 1);
        $this->fields_form = array(
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('statusmp_shipping_method')) {
            $this->toggleStatus();
            Tools::redirectAdmin(self::$currentIndex.'&conf=5&token='.$this->token);
        } elseif (Tools::isSubmit('deletemp_shipping_method')) {
            $this->deleteShipping();
            Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.$this->token);
        }
        //Delete impact price
        if (Tools::getValue('deleteimpact')) {
            $mpShippingId = Tools::getValue('id');
            $impactId = Tools::getValue('impact_id');
            if ($impactId) {
                $objMpShipImpact = new MpShippingImpact($impactId);
                $objMpShipImpact->delete();
                //Db::getInstance()->delete('mp_shipping_impact','id='.$impactId);
            }
            Tools::redirectAdmin(self::$currentIndex.'&id='.$mpShippingId.'&updatemp_shipping_method&updateimpact=1&conf=1&token='.$this->token);
        }

        if (Tools::isSubmit('FinishButtonclick')) {
            $shippingName = Tools::getValue('shipping_name');
            $isValidShippingName = Validate::isCarrierName($shippingName);
            $grade = Tools::getValue('grade');
            $isValidGrade = Validate::isUnsignedInt($grade);
            $shippingMethod = Tools::getValue('shipping_method');
            $trackingUrl = Tools::getValue('tracking_url');
            $isValidTrackingUrl = Validate::isAbsoluteUrl($trackingUrl);

            //If multi-lang is OFF then PS default lang will be default lang for seller from Marketplace Configuration page
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $defaultLang = Tools::getValue('current_lang');
            } else {
                if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') { //Admin Default lang
                    $defaultLang = Configuration::get('PS_LANG_DEFAULT');
                } elseif (Configuration::get('MP_MULTILANG_DEFAULT_LANG') == '2') { //Seller Default lang
                    $defaultLang = Tools::getValue('current_lang');
                }
            }

            $sellerCustomerId = Tools::getValue('seller_customer_id');
            /*$obj_seller_info = new WkMpSeller();*/
            $mpCustomerInfo = WkMpSeller::getSellerDetailByCustomerId($sellerCustomerId);
            if ($mpCustomerInfo['id_seller']) {
                $mpIdSeller = $mpCustomerInfo['id_seller'];
            } else {
                $mpIdSeller = 0;
            }

            if (!$mpIdSeller) {
                $this->errors[] = $this->l('Selected Customer is not a Seller');
            }

            if (!$shippingName) {
                $this->errors[] = $this->l('Carrier name is required.');
            } elseif (!$isValidShippingName) {
                $this->errors[] = $this->l('Carrier name must not have Invalid characters /^[^<>;=#{}]*$/u');
            } elseif (Tools::strlen($shippingName) > 64) {
                $this->errors[] = $this->l('Carrier name field is too long (64 chars max).');
            }

            if (!Tools::getValue('transit_time_'.$defaultLang)) {
                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    $sellerLangArr = Language::getLanguage((int) $defaultLang);
                    $this->errors[] = $this->l('Transit time is required in '.$sellerLangArr['name']);
                } else {
                    $this->errors[] = $this->l('Transit time is required');
                }
            } else {
                foreach (Language::getLanguages() as $language) {
                    $languageName = '';
                    if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                        $languageName = $language['name'];
                    }

                    if (!Validate::isCatalogName(Tools::getValue('transit_time_'.$language['id_lang']))) {
                        $this->errors[] = $this->l('Transit time must not have invalid characters /^[^<>={}]*$/u in '.$languageName);
                    }
                }
            }

            if (!$isValidGrade) {
                $this->errors[] = $this->l('Speed grade must be numeric');
            } elseif ($grade < 0 || $grade > 9) {
                $this->errors[] = $this->l('Speed grade must be from 0 to 9');
            }

            if (!$isValidTrackingUrl) {
                $this->errors[] = $this->l('Invalid Tracking Url');
            }

            //if shipping is not free then range is mandatory
            if (Tools::getValue('is_free') == 0) {
                $rangeInf = Tools::getValue('range_inf');
                $rangeSup = Tools::getValue('range_sup');
                if (isset($rangeInf[0]) && $rangeInf[0] == '') {
                    $this->errors[] = $this->l('Shipping charge lower limit should not blank');
                }
                if (isset($rangeSup[0]) && $rangeSup[0] == '') {
                    $this->errors[] = $this->l('Shipping charge upper limit should not blank');
                }
            }

            $isNewImage = false;
            if (isset($_FILES['shipping_logo'])) {
                if ($_FILES['shipping_logo']['size'] > 0 && $_FILES['shipping_logo']['tmp_name'] != '') {
                    if ($error = ImageManager::validateUpload($_FILES['shipping_logo'])) {
                        $this->errors[] = $error;
                    }
                    $imageType = array('jpg','jpeg','png');
                    $extention = explode('.', $_FILES['shipping_logo']['name']);
                    $ext = Tools::strtolower($extention['1']);
                    if (!in_array($ext, $imageType)) {
                        $this->errors[] = $this->l('Only jpg,png,jpeg image allow and image size should not exceed 125*125');
                    } else {
                        list($width, $height) = getimagesize($_FILES['shipping_logo']['tmp_name']);
                        if ($width > 125 ||  $height > 125) {
                            $this->errors[] = $this->l('Only jpg,png,jpeg image allow and image size should not exceed 125*125');
                        }
                        $isNewImage = true;
                    }
                }
            }

            if (empty($this->errors)) {
                $mpShippingId = Tools::getValue('id');
                $mpShippingActive = Tools::getValue('mpShippingActive');
                if ($mpShippingId) {
                    $objMpShippingMethod = new MpShippingMethod($mpShippingId); //Edit shipping
                } else {
                    $objMpShippingMethod = new MpShippingMethod(); //Add shipping
                }

                $objMpShippingMethod->mp_shipping_name = $shippingName;
                $objMpShippingMethod->grade = $grade;
                $objMpShippingMethod->tracking_url = $trackingUrl;
                $objMpShippingMethod->shipping_method = $shippingMethod;
                $objMpShippingMethod->deleted = 0;
                $objMpShippingMethod->mp_id_seller = $mpIdSeller;
                $objMpShippingMethod->active = $mpShippingActive;

                foreach (Language::getLanguages(true) as $language) {
                    $transitLangId = $language['id_lang'];

                    if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                        //if product name in other language is not available then fill with seller language same for others
                        if (!Tools::getValue('transit_time_'.$language['id_lang'])) {
                            $transitLangId = $defaultLang;
                        }
                    } else {
                        //if multilang is OFF then all fields will be filled as default lang content
                        $transitLangId = $defaultLang;
                    }

                    $objMpShippingMethod->transit_delay[$language['id_lang']] = Tools::getValue('transit_time_'.$transitLangId);
                }

                if ($mpShippingId) {
                    $objMpShippingMethod->save();
                    $updateShipping = 1;
                } else {
                    $objMpShippingMethod->id_ps_reference = 0;
                    $objMpShippingMethod->save();
                    $mpShippingId = $objMpShippingMethod->id;
                    $updateShipping = 0;
                }

                if ($isNewImage == true) {
                    $dir = _PS_MODULE_DIR_.'mpshipping/views/img/logo/'.$mpShippingId.'.jpg';
                    ImageManager::resize($_FILES['shipping_logo']['tmp_name'], $dir);
                }
                $this->addMpShippingStep2($mpShippingId);
                $this->addMpShippingStep3($mpShippingId, $updateShipping, $isNewImage, $mpIdSeller);
            }
        }
        parent::postProcess();
    }

    public function addMpShippingStep2($mpShippingId)
    {
        $zoneDetail = Zone::getZones(true);

        $isFree = Tools::getValue('is_free');
        $rangeInf = Tools::getValue('range_inf');
        $rangeSup = Tools::getValue('range_sup');
        $shippingHandling = Tools::getValue('shipping_handling');
        //for tax options
        $idTaxRuleGroup = Tools::getValue('id_tax_rule_group');
        $rangeBehavior = Tools::getValue('range_behavior');
        //end

        if ($isFree == 0) {
            if (isset($rangeInf[0]) && $rangeInf[0] == '') {
                $this->errors[] = $this->l('Shipping charge lower limit should not blank');
            }
            if (isset($rangeSup[0]) && $rangeSup[0] == '') {
                $this->errors[] = $this->l('Shipping charge upper limit should not blank');
            }
        }
        if (empty($this->errors)) {
            $objMpShippingMethod = new MpShippingMethod($mpShippingId);
            $objMpShippingMethod->is_free = $isFree;
            $objMpShippingMethod->shipping_handling = $shippingHandling;
            //for tax options
            $objMpShippingMethod->id_tax_rule_group = $idTaxRuleGroup;
            $objMpShippingMethod->range_behavior = $rangeBehavior;
            //end
            $objMpShippingMethod->save();

            $shippingMethod = $objMpShippingMethod->shipping_method;
            if ($shippingMethod == 2) {
                $rangeType = 2;
                $objRangeObj = new MpRangePrice(); //obj for price
            } elseif ($shippingMethod == 1) {
                $rangeType = 1;
                $objRangeObj = new MpRangeWeight(); //obj for weight
            }
            $firstDelId = 0;
            $firstRangeId = 0;
            if ($isFree) {
                $saveNum = 1;
                $rangeCount = 1;
                foreach ($zoneDetail as $zone) {
                    $objMpShippingDel = new MpShippingDelivery();
                    $zoneId = $zone['id_zone'];
                    $postName = 'zone_'.$zoneId;
                    $isFeeSet = Tools::getValue($postName);
                    /*if ($isFeeSet) {
                        $range_enter = 1;
                    }*/
                    if ($isFeeSet) {
                        $objMpShippingDel->mp_shipping_id = $mpShippingId;
                        $objMpShippingDel->id_zone = $zoneId;
                        $objMpShippingDel->mp_id_range_price = 0;
                        $objMpShippingDel->mp_id_range_weight = 0;
                        $objMpShippingDel->base_price = (float) 0;
                        if ($rangeCount == 1) {
                            $objRangeObj->delimiter1 = (float) 0;
                            $objRangeObj->delimiter2 = (float) 0;

                            $objRangeObj->mp_shipping_id = $mpShippingId;
                            //$is_available = $objRangeObj->isRangeInTableByShippingId();
                            $objRangeObj->add();
                            $firstRangeId = $objRangeObj->id;
                            ++$rangeCount;
                        }
                        $objMpShippingDel->mp_id_range_price = $objRangeObj->id;
                        $objMpShippingDel->save();

                        if ($saveNum == 1) {
                            $firstDelId = $objMpShippingDel->id;
                            ++$saveNum;
                        }
                    }
                }

                if (!isset($firstDelId)) {
                    $firstDelId = MpShippingDelivery::getShippingDeliveryLastId($mpShippingId);
                }
                if (!isset($firstRangeId)) {
                    if ($rangeType == 2) {
                        $firstRangeId = MpRangeWeight::getWeightRangeLastId($mpShippingId);
                    } else {
                        $firstRangeId = MpRangePrice::getPriceRangeLastId($mpShippingId);
                    }
                }
                MpShippingImpact::updateImpactAfterUpdateShipping($mpShippingId, $firstDelId, $firstRangeId, $rangeType);
            } else {
                if ($objRangeObj) {
                    $rangeCount = 1;
                    $saveNum = 1;
                    foreach ($rangeInf as $key => $value) {
                        if ($rangeInf[$key] != '') {
                            $objRangeObj->delimiter1 = $value;

                            if ($rangeSup[$key] == '') {
                                $objRangeObj->delimiter2 = (float) 0;
                            } else {
                                $objRangeObj->delimiter2 = (float) $rangeSup[$key];
                            }

                            $objRangeObj->mp_shipping_id = $mpShippingId;

                            $objRangeObj->add();
                            if ($rangeCount == 1) {
                                $firstRangeId = $objRangeObj->id;
                                ++$rangeCount;
                            }
                            foreach ($zoneDetail as $zone) {
                                $objMpShippingDeliv = new MpShippingDelivery();
                                $zoneId = $zone['id_zone'];
                                $postName = 'zone_'.$zoneId;
                                $isFeeSet = Tools::getValue($postName);
                                if ($isFeeSet) {
                                    $objMpShippingDeliv->mp_shipping_id = $mpShippingId;
                                    $objMpShippingDeliv->id_zone = $zoneId;
                                    if ($shippingMethod == 2) {
                                        $objMpShippingDeliv->mp_id_range_price = $objRangeObj->id;
                                        $objMpShippingDeliv->mp_id_range_weight = 0;
                                    } elseif ($shippingMethod == 1) {
                                        $objMpShippingDeliv->mp_id_range_weight = $objRangeObj->id;
                                        $objMpShippingDeliv->mp_id_range_price = 0;
                                    }
                                    $zoneFees = Tools::getValue('fees');
                                    $objMpShippingDeliv->base_price = (float) $zoneFees[$zoneId][$key];
                                    if ($objMpShippingDeliv->base_price == 'on' || $objMpShippingDeliv->base_price == '') {
                                        $objMpShippingDeliv->base_price = 0;
                                    }
                                    $objMpShippingDeliv->save();
                                    if ($saveNum == 1) {
                                        $firstDelId = $objMpShippingDeliv->id;
                                        ++$saveNum;
                                    }
                                }
                            }
                        }
                    }
                }
                MpShippingImpact::updateImpactAfterUpdateShipping($mpShippingId, $firstDelId, $firstRangeId, $rangeType);
            }
        }
    }

    public function addMpShippingStep3($mpShippingId, $updateShipping, $isNewImage, $mpIdSeller)
    {
        $maxHeight = Tools::getValue('max_height');
        $maxWidth = Tools::getValue('max_width');
        $maxDepth = Tools::getValue('max_depth');
        $maxWeight = Tools::getValue('max_weight');
        $shippingGroup = Tools::getValue('shipping_group');

        if ($maxHeight == '') {
            $maxHeight = (int) 0;
        } elseif (!Validate::isUnsignedInt($maxHeight)) {
            $this->errors[] = $this->l('The max height field is invalid');
        }

        if ($maxWidth == '') {
            $maxWidth = (int) 0;
        } elseif (!Validate::isUnsignedInt($maxWidth)) {
            $this->errors[] = $this->l('The max width field is invalid');
        }

        if ($maxDepth == '') {
            $maxDepth = (int) 0;
        } elseif (!Validate::isUnsignedInt($maxDepth)) {
            $this->errors[] = $this->l('The max depth field is invalid');
        }

        if ($maxWeight == '') {
            $maxWeight = (float) 0;
        } elseif (!Validate::isFloat($maxWeight)) {
            $this->errors[] = $this->l('The max weight field is invalid');
        }

        if (empty($this->errors)) {
            $objMpShippingMethod = new MpShippingMethod($mpShippingId);
            $objMpShippingMethod->max_height = $maxHeight;
            $objMpShippingMethod->max_width = $maxWidth;
            $objMpShippingMethod->max_depth = $maxDepth;
            $objMpShippingMethod->max_weight = $maxWeight;
            $objMpShippingMethod->is_done = 1;
            $objMpShippingMethod->save();

            //Set Shipping group
            $deletePrevGroup = MpShippingMethod::deleteShippingGroup($mpShippingId);
            if ($deletePrevGroup) {
                MpShippingMethod::setShippingGroup($mpShippingId, $shippingGroup);
            }

            $idPsReference = MpShippingMethod::getReferenceByMpShippingId($mpShippingId);
            if ($idPsReference) {
                $objMpShipping = new MpShippingMethod();
                $idPsCarriers = $objMpShipping->updateToCarrier($mpShippingId, $idPsReference);
                if ($idPsCarriers) {
                    $imgDir = _PS_MODULE_DIR_.'mpshipping/views/img/logo/';
                    if (file_exists($imgDir.$mpShippingId.'.jpg')) {
                        copy($imgDir.$mpShippingId.'.jpg', _PS_IMG_DIR_.'s/'.$idPsCarriers.'.jpg');
                    }
                }
            } else {
                //Autoapprove of shipping method.
                if (Tools::getValue('mpShippingActive') == 1) {
                    $idPsReferenceAdded = $objMpShippingMethod->addToCarrier($mpShippingId);
                    $objMpShippingMethod->enableShipping($mpShippingId, $idPsReferenceAdded);

                    //Assign distribute as Seller for seller shipping
                    $newObjMpShipping = new MpShipping();
                    if ($newObjMpShipping->checkMarketplaceVersion()) {
                        MpShippingMethod::updatePsShippingDistributionType($idPsReferenceAdded, 'seller');
                    }

                    if (Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL') == 1) {
                        $objMpShippingMethod->mailToSeller($mpIdSeller, $mpShippingId, 1);
                    }
                }
            }

            if ($updateShipping) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        }
    }

    public function processBulkStatusSelection($status)
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $bulkId) {
                $this->bulkUpdate($status, $bulkId);
            }
            Tools::redirectAdmin(self::$currentIndex.'&conf=5&token='.$this->token);
        }

        parent::processBulkStatusSelection($status);
    }

    public function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $bulkId) {
                $this->deleteShipping($bulkId);
            }
            Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='.$this->token);
        }

        parent::processBulkDelete();
    }

    public function renderView()
    {
        return parent::renderView();
    }

    public function ajaxProcessUpdateCarrierToMainProducts()
    {
        $idLang = $this->context->language->id;
        $objCarr = new Carrier();
        $carrDetails = $objCarr->getCarriers($idLang, true);
        if (empty($carrDetails)) {
            $json = array('status' => 'ko', 'msg' => $this->l('No Carriers available'));
            echo Tools::jsonEncode($json);
        } else {
            $this->assignCarriersToMainProduct($idLang);
            $json = array('status' => 'ok', 'msg' => $this->l('Carriers assigned successfully.'));
            echo Tools::jsonEncode($json);
        }
        die; //ajax close
    }

    public function assignCarriersToMainProduct($idLang)
    {
        $objShipMap = new MpShippingProductMap();

        $start = 0;
        $limit = 0;
        $orderBy = 'id_product';
        $orderWay = 'ASC';

        $carrRef = array();
        $allPsCarriersOnly = MpShippingMethod::getOnlyPrestaCarriers($idLang);
        if ($allPsCarriersOnly) {
            foreach ($allPsCarriersOnly as $psCarriers) {
                $carrRef[] = $psCarriers['id_reference'];
            }
        }

        $psProdInfo = Product::getProducts($idLang, $start, $limit, $orderBy, $orderWay, false, true);
        foreach ($psProdInfo as $product) {
            if (!$objShipMap->checkMpProduct($product['id_product'])) {
                $objShipMap->setProductCarrier($product['id_product'], $carrRef);
            }
        }
    }

    public function bulkUpdate($status, $bulkId)
    {
        if ($bulkId) {
            $mpShippingId = $bulkId;
        } else {
            $mpShippingId = Tools::getValue('id');
        }

        $objMpShippingMet = new MpShippingMethod($mpShippingId);

        $idPsReference = MpShippingMethod::getReferenceByMpShippingId($mpShippingId);
        if ($idPsReference) {
            if ($objMpShippingMet->active == 1 && $status == 0) { //going to deactive
                $objMpShippingMet->active = 0;
                $objMpShippingMet->save();

                //remove from default shipping of seller
                MpShippingMethod::updateDefaultShipping($mpShippingId, 0);

                $objCarrier = Carrier::getCarrierByReference($idPsReference);
                $objCarrier->active = 0;
                if ($objMpShippingMet->is_free) {
                    $objCarrier->is_free = 1;
                }
                $objCarrier->save();

                $objMpShipProdMap = new MpShippingProductMap();
                $objMpShipProdMap->deleteMpShippingProductMapOnDeactivate($mpShippingId);

                if (Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL') == 1) {
                    $objMpShippingMet->mailToSeller($objMpShippingMet->mp_id_seller, $mpShippingId, 0);
                }

                /*When deactivate any seller shipping method then check if only this shipping method is applied on the sellers product or all deactive shippings are applied on the product then default chosen shippings by admin should be applied on those products*/
                $objMpShippingMet = new MpShippingMethod();
                $objMpShippingMet->updateCarriersOnDeactivateOrDelete();
            /*END*/
            } else { //going to active
                if ($objMpShippingMet->active == 0 && $status == 1) {
                    $objMpShippingMet->active = $status;
                    $objMpShippingMet->save();

                    $objCarrier = Carrier::getCarrierByReference($idPsReference);
                    if ($objMpShippingMet->is_free) {
                        $objCarrier->is_free = 1;
                    }
                    $objCarrier->active = 1;
                    $objCarrier->save();

                    if (Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL') == 1) {
                        $objMpShippingMet->mailToSeller($objMpShippingMet->mp_id_seller, $mpShippingId, 1);
                    }
                }
            }
        } else { //going to first time active
            $objMpShippingMet->active = 1;
            $objMpShippingMet->save();

            $idPsReferenceAdded = $objMpShippingMet->addToCarrier($mpShippingId);
            if ($idPsReferenceAdded) {
                $objMpShipping = new MpShippingMethod($mpShippingId);
                $objMpShipping->id_ps_reference = $idPsReferenceAdded;
                $objMpShipping->save();

                //Assign distribute as Seller for seller shipping
                $newObjMpShipping = new MpShipping();
                if ($newObjMpShipping->checkMarketplaceVersion()) {
                    MpShippingMethod::updatePsShippingDistributionType($idPsReferenceAdded, 'seller');
                }

                if (Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL') == 1) {
                    $objMpShippingMet->mailToSeller($objMpShippingMet->mp_id_seller, $mpShippingId, 1);
                }

                $imgDir = _PS_MODULE_DIR_.'mpshipping/views/img/logo/';
                if (file_exists($imgDir.$mpShippingId.'.jpg')) {
                    copy($imgDir.$mpShippingId.'.jpg', _PS_IMG_DIR_.'s/'.$idPsReferenceAdded.'.jpg');
                }
            }
        }
    }

    public function toggleStatus($bulkId = false)
    {
        if ($bulkId) {
            $mpShippingId = $bulkId;
        } else {
            $mpShippingId = Tools::getValue('id');
        }

        $objMpShippingMet = new MpShippingMethod($mpShippingId);

        $idPsReference = MpShippingMethod::getReferenceByMpShippingId($mpShippingId);
        if ($idPsReference) {
            if ($objMpShippingMet->active == 1) { //going to deactive
                $objMpShippingMet->active = 0;
                $objMpShippingMet->save();

                //remove from default shipping of seller
                MpShippingMethod::updateDefaultShipping($mpShippingId, 0);

                $objCarrier = Carrier::getCarrierByReference($idPsReference);
                $objCarrier->active = 0;
                if ($objMpShippingMet->is_free) {
                    $objCarrier->is_free = 1;
                }
                $objCarrier->save();

                $objMpShipProdMap = new MpShippingProductMap();
                $objMpShipProdMap->deleteMpShippingProductMapOnDeactivate($mpShippingId);

                if (Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL') == 1) {
                    $objMpShippingMet->mailToSeller($objMpShippingMet->mp_id_seller, $mpShippingId, 0);
                }

                /*When deactivate any seller shipping method then check if only this shipping method is applied on the sellers product or all deactive shippings are applied on the product then default chosen shippings by admin should be applied on those products*/
                $objMpShippingMet = new MpShippingMethod();
                $objMpShippingMet->updateCarriersOnDeactivateOrDelete();
            /*END*/
            } else { //going to active
                $objMpShippingMet->active = 1;
                $objMpShippingMet->save();

                $objCarrier = Carrier::getCarrierByReference($idPsReference);
                if ($objMpShippingMet->is_free) {
                    $objCarrier->is_free = 1;
                }
                $objCarrier->active = 1;
                $objCarrier->save();

                if (Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL') == 1) {
                    $objMpShippingMet->mailToSeller($objMpShippingMet->mp_id_seller, $mpShippingId, 1);
                }
            }
        } else { //going to first time active
            $objMpShippingMet->active = 1;
            $objMpShippingMet->save();

            $idPsReferenceAdded = $objMpShippingMet->addToCarrier($mpShippingId);
            if ($idPsReferenceAdded) {
                $objMpShipping = new MpShippingMethod($mpShippingId);
                $objMpShipping->id_ps_reference = $idPsReferenceAdded;
                $objMpShipping->save();

                //Assign distribute as Seller for seller shipping

                $newObjMpShipping = new MpShipping();
                if ($newObjMpShipping->checkMarketplaceVersion()) {
                    MpShippingMethod::updatePsShippingDistributionType($idPsReferenceAdded, 'seller');
                }

                if (Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL') == 1) {
                    $objMpShippingMet->mailToSeller($objMpShippingMet->mp_id_seller, $mpShippingId, 1);
                }

                $imgDir = _PS_MODULE_DIR_.'mpshipping/views/img/logo/';
                if (file_exists($imgDir.$mpShippingId.'.jpg')) {
                    copy($imgDir.$mpShippingId.'.jpg', _PS_IMG_DIR_.'s/'.$idPsReferenceAdded.'.jpg');
                }
            }
        }
    }

    public function deleteShipping($bulkId = false)
    {
        if ($bulkId) {
            $mpShippingId = $bulkId;
        } else {
            $mpShippingId = Tools::getValue('id');
        }

        //$obj_shipping_prod = new MpShippingProductMap();
        //$mpprod_map = $obj_shipping_prod->getMpShippingForProducts($mpShippingId);

        //delete shipping all data
        $objMpShipping = new MpShippingMethod();
        $objMpShipping->deleteMpShipping($mpShippingId);

        /*Assign new selected shipping methods to the seller produccts which have no seller shipping methods*/
        $objMpShippingMet = new MpShippingMethod();
        $objMpShippingMet->updateCarriersOnDeactivateOrDelete();
        /*END*/
    }

    public function ajaxProcessChangeShippingDistributionType()
    {
        if ($idPsReference = Tools::getValue('id_ps_reference')) {
            $shippingDistributeType = Tools::getValue('shipping_distribute_type');
            //Change shipping distribution type for Ps Carriers Controller
            $newObjMpShipping = new MpShipping();
            if ($newObjMpShipping->checkMarketplaceVersion()) {
                if (MpShippingMethod::updatePsShippingDistributionType($idPsReference, $shippingDistributeType)) {
                    die('1');
                }
            }
        }

        die('0');
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/style.css');
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/mpshippinglist.css');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/fieldform.js');
        $this->addJs(_MODULE_DIR_.$this->module->name.'/views/js/addmpshipping.js');
    }
}
