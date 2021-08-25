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

class MpshippingAddMpShippingModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $link = new Link();
        $idCustomer = $this->context->customer->id;
        if ($idCustomer) {
            $mpCustomerInfo = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($mpCustomerInfo && $mpCustomerInfo['active']) {
                $mpIdSeller = $mpCustomerInfo['id_seller'];

                //get total zone available in prestashop
                $zoneDetail = Zone::getZones(true);
                $this->context->smarty->assign('zones', $zoneDetail);

                //Get customer group
                if ($customerAllGroups = Group::getGroups($this->context->language->id)) {
                    $this->context->smarty->assign('customerAllGroups', $customerAllGroups);
                }

                $mpShippingId = Tools::getValue('mpshipping_id');
                if ($mpShippingId) { //Edit shipping
                    $objMpShippingMethod = new MpShippingMethod($mpShippingId);
                    if ($mpIdSeller == $objMpShippingMethod->mp_id_seller) {
                        $this->context->smarty->assign('mp_shipping_id', $mpShippingId);
                        //tax option for seller shipping
                        $this->context->smarty->assign('tax_rules', TaxRulesGroup::getTaxRulesGroups(true));
                        $this->context->smarty->assign('id_tax_rule_group', $objMpShippingMethod->id_tax_rule_group);
                        $this->context->smarty->assign('range_behavior', $objMpShippingMethod->range_behavior);
                        //end
                        $this->context->smarty->assign('mp_shipping_name', $objMpShippingMethod->mp_shipping_name);
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

                        //Get Shipping group
                        if ($shippingGroup = MpShippingMethod::getShippingGroup($mpShippingId)) {
                            $this->context->smarty->assign('shippingGroup', array_column($shippingGroup, 'id_group'));
                        }

                        //@shippingMethod==1 billing accroding to weight
                        //@shippingMethod==2 billing accroding to price
                        $shippingMethod = $objMpShippingMethod->shipping_method;
                        $ranges = array();
                        if ($shippingMethod == 1) {
                            //find all range according to weight available for this shipping method
                            $objRangeWeight = new MpRangeWeight();
                            $objRangeWeight->mp_shipping_id = $mpShippingId;
                            $differentRange = $objRangeWeight->getAllRangeAccordingToShippingId();
                            if ($differentRange) {
                                $ranges = $differentRange;
                            } else {
                                $this->context->smarty->assign('different_range', -1);
                            }
                        } elseif ($shippingMethod == 2) {
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
                        $mpShippingProcess = $link->getModuleLink('mpshipping', 'addmpshipping', array('submitUpdateshipping' => 1, 'mp_id_seller' => $mpIdSeller, 'mp_shipping_id' => $mpShippingId));
                    } else {
                        Tools::redirect($this->context->link->getModuleLink('mpshipping', 'addmpshipping'));
                    }
                } else {
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
                    //tax option for seller shipping
                    $this->context->smarty->assign('tax_rules', TaxRulesGroup::getTaxRulesGroups(true));
                    Media::addJsDef(array('mp_shipping_id' => ''));

                    $mpShippingProcess = $link->getModuleLink('mpshipping', 'addmpshipping', array('submitAddshipping' => 1, 'mp_id_seller' => $mpIdSeller));
                }

                // Set default lang at every form according to configuration multi-language
                WkMpHelper::assignDefaultLang($mpIdSeller);

                $this->context->smarty->assign('title_text_color', Configuration::get('WK_MP_TITLE_TEXT_COLOR'));
                $this->context->smarty->assign('title_bg_color', Configuration::get('WK_MP_TITLE_BG_COLOR'));
                $this->context->smarty->assign('mpshippingprocess', $mpShippingProcess);
                $this->context->smarty->assign('self', dirname(__FILE__));
                $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
                $this->context->smarty->assign('currency_sign', $currency->sign);
                $this->context->smarty->assign('PS_WEIGHT_UNIT', Configuration::get('PS_WEIGHT_UNIT'));
                $this->context->smarty->assign('logic', 'mp_carriers');
                /*$this->context->smarty->assign('title_text_color', Configuration::get('MP_TITLE_TEXT_COLOR'));
                $this->context->smarty->assign('title_bg_color', Configuration::get('MP_TITLE_BG_COLOR'));*/

                $jsDefVar = array(
                    'currency_sign' => $currency->sign,
                    'PS_WEIGHT_UNIT' => Configuration::get('PS_WEIGHT_UNIT'),
                    'string_price' => $this->module->l('Will be applied when the price is', 'addmpshipping'),
                    'string_weight' => $this->module->l('Will be applied when the weight is', 'addmpshipping'),
                    'invalid_range' => $this->module->l('This range is not valid', 'addmpshipping'),
                    'need_to_validate' => $this->module->l('Please validate the last range before create a new one.', 'addmpshipping'),
                    'delete_range_confirm' => $this->module->l('Are you sure to delete this range ?', 'addmpshipping'),
                    'labelDelete' => $this->module->l('Delete', 'addmpshipping'),
                    'labelValidate' => $this->module->l('Validate', 'addmpshipping'),
                    'range_is_overlapping' => $this->module->l('Ranges are overlapping', 'addmpshipping'),
                    'finish_error' => $this->module->l('You need to go through all step', 'addmpshipping'),
                    'shipping_name_error' => $this->module->l('Carrier name is required field', 'addmpshipping'),
                    'transit_time_error' => $this->module->l('Transit time is required in', 'addmpshipping'),
                    'transit_time_error_other' => $this->module->l('Transit time is required in', 'addmpshipping'),
                    'speedgradeinvalid' => $this->module->l('Speed grade must be integer', 'addmpshipping'),
                    'speedgradevalue' => $this->module->l('Speed grade must be from 0 to ', 'addmpshipping'),
                    'invalid_logo_file_error' => $this->module->l('Invalid logo file!', 'addmpshipping'),
                    'shipping_charge_error_message' => $this->module->l('Shipping charge is not valid.', 'addmpshipping'),
                    'shipping_charge_lower_limit_error1' => $this->module->l('Shipping charge lower limit must be numeric.', 'addmpshipping'),
                    'shipping_charge_lower_limit_error2' => $this->module->l('Shipping charge lower limit should not negative', 'addmpshipping'),
                    'shipping_charge_upper_limit_error1' => $this->module->l('Shipping charge upper limit must be numeric', 'addmpshipping'),
                    'shipping_charge_upper_limit_error2' => $this->module->l('Shipping charge upper limit should not negative', 'addmpshipping'),
                    'shipping_charge_limit_error' => $this->module->l('Shipping charge upper limit must be greater than lower limit', 'addmpshipping'),
                    'shipping_charge_limit_equal_error' => $this->module->l('Shipping charge lower limit and upper limit should not equal', 'addmpshipping'),
                    'invalid_logo_size_error' => $this->module->l('Invalid logo size', 'addmpshipping'),
                    'invalid_range_value' => $this->module->l('Ranges upper and lower values should not clash to one another.', 'addmpshipping'),
                    'shipping_select_zone_err' => $this->module->l('Select atleast one zone', 'addmpshipping'),
                    'impact_price_text' => $this->module->l('Impact Price', 'addmpshipping'),
                    'interger_price_text' => $this->module->l('Enter price should be an integer', 'addmpshipping'),
                    'confirm_msg' => $this->module->l('Are you sure?', 'addmpshipping'),
                );

                Media::addJsDef($jsDefVar);

                if (Tools::getValue('addmpshipping_step4') == 1) {
                    $mpShippingId = Tools::getValue('mpshipping_id');

                    if ($mpShippingId) {
                        $objMpShippingMethod = new MpShippingMethod($mpShippingId);
                        if ($objMpShippingMethod->is_free) {
                            Tools::redirect($link->getModuleLink('mpshipping', 'mpshippinglist'));
                        } else {
                            if (Tools::getValue('updateimpact')) {
                                //Delete impact price
                                if (Tools::getValue('impact_id')) {
                                    $impactId = Tools::getValue('impact_id');
                                    $objMpShipImpact = new MpShippingImpact($impactId);
                                    $objMpShippingNew = new MpShippingMethod($objMpShipImpact->mp_shipping_id);
                                    if ($objMpShippingNew->mp_id_seller == $mpIdSeller) {
                                        $objMpShipImpact->delete();
                                        $this->context->smarty->assign('deleteimpact', 1);
                                    }
                                }

                                $shippingMethod = $objMpShippingMethod->shipping_method;
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
                                $this->context->smarty->assign('updateimpact', 1);
                            } else {
                                $this->context->smarty->assign('addmpshipping_success', 1);
                            }
                            $this->context->smarty->assign('mpshipping_id', $mpShippingId);
                            $this->context->smarty->assign('mpshipping_name', $objMpShippingMethod->mp_shipping_name);
                            $shippingAjaxLink = $link->getModuleLink('mpshipping', 'shippingajax');
                            $this->context->smarty->assign('shipping_ajax_link', $shippingAjaxLink);
                            $this->context->smarty->assign('modules_dir', _MODULE_DIR_);
                            $updateImpactLink = $link->getModuleLink('mpshipping', 'addmpshipping', ['mpshipping_id'=>$mpShippingId, 'addmpshipping_step4'=>1, 'updateimpact' => 1]);
                            $lastJsDef = [
                                'img_ps_dir' => _MODULE_DIR_.'marketplace/views/img/',
                                'shipping_ajax_link' => $shippingAjaxLink,
                                'select_country' => $this->module->l('Select country', 'addmpshipping'),
                                'select_state' => $this->module->l('All', 'addmpshipping'),
                                'zone_error' => $this->module->l('Select Zone', 'addmpshipping'),
                                'no_range_available_error' => $this->module->l('No Range Available', 'addmpshipping'),
                                'ranges_info' => $this->module->l('Ranges', 'addmpshipping'),
                                'message_impact_price_error' => $this->module->l('Price should be numeric', 'addmpshipping'),
                                'message_impact_price' => $this->module->l('Impact added sucessfully', 'addmpshipping'),
                                'update_impact_link' => $updateImpactLink,
                                'currency_sign' => $currency->sign,
                            ];

                            Media::addJsDef($lastJsDef);

                            if ($shippingMethod == 2) {
                                Media::addJsDef(array('range_sign' => $currency->sign));
                            } else {
                                Media::addJsDef(array('range_sign' => Configuration::get('PS_WEIGHT_UNIT')));
                            }

                            $this->setTemplate('module:mpshipping/views/templates/front/addshippingstep4.tpl');
                        }
                    }
                } else {
                    $this->setTemplate('module:mpshipping/views/templates/front/addmpshipping.tpl');
                }
            } else {
                Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('mpshipping', 'addmpshipping')));
        }
    }

    public function postProcess()
    {
        if (Tools::getValue('submitAddshipping') || Tools::getValue('submitUpdateshipping')) {
            $shippingName = Tools::getValue('shipping_name');
            $isValidShippingName = Validate::isCarrierName($shippingName);
            $grade = Tools::getValue('grade');
            $isValidGrade = Validate::isUnsignedInt($grade);
            $shippingMethod = Tools::getValue('shipping_method');
            $trackingUrl = Tools::getValue('tracking_url');
            //$transit_time = Tools::getValue('transit_time');
            $isValidTrackingUrl = Validate::isAbsoluteUrl($trackingUrl);

            //If multi-lang is OFF then PS default lang will be default lang for seller

            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $defaultLang = Tools::getValue('current_lang');
            } else {
                if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') { //Admin default lang
                    $defaultLang = Configuration::get('PS_LANG_DEFAULT');
                } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') { //Seller default lang
                    $defaultLang = Tools::getValue('current_lang');
                }
            }
            $mpIdSeller = Tools::getValue('mp_id_seller');
            if (!$shippingName) {
                $this->errors[] = $this->module->l('Carrier name is required.', 'addmpshipping');
            } elseif (!$isValidShippingName) {
                $this->errors[] = $this->module->l('Carrier name must not have Invalid characters /^[^<>;=#{}]*$/u', 'addmpshipping');
            } elseif (Tools::strlen($shippingName) > 64) {
                $this->errors[] = $this->module->l('Carrier name field is too long (64 chars max).', 'addmpshipping');
            }

            if (!Tools::getValue('transit_time_'.$defaultLang)) {
                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    $defLangArr = Language::getLanguage((int) $defaultLang);
                    $this->errors[] = $this->module->l('Transit time is required in '.$defLangArr['name'], 'addmpshipping');
                } else {
                    $this->errors[] = $this->module->l('Transit time is required', 'addmpshipping');
                }
            } else {
                foreach (Language::getLanguages() as $language) {
                    $languageName = '';
                    if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                        $languageName = $language['name'];
                    }

                    if (!Validate::isCatalogName(Tools::getValue('transit_time_'.$language['id_lang']))) {
                        $this->errors[] = $this->module->l('Transit time must not have invalid characters /^[^<>={}]*$/u in '.$languageName, 'addmpshipping');
                    }
                }
            }

            if (!$isValidGrade) {
                $this->errors[] = $this->module->l('Speed grade must be numeric', 'addmpshipping');
            } elseif ($grade < 0 || $grade > 9) {
                $this->errors[] = $this->module->l('Speed grade must be from 0 to 9', 'addmpshipping');
            }

            if (!$isValidTrackingUrl) {
                $this->errors[] = $this->module->l('Invalid Tracking Url', 'addmpshipping');
            }

            //if shipping is not free then range is mandatory
            if (Tools::getValue('is_free') == 0) {
                $rangeInf = Tools::getValue('range_inf');
                $rangeSup = Tools::getValue('range_sup');
                if (isset($rangeInf[0]) && $rangeInf[0] == '') {
                    $this->errors[] = $this->module->l('Shipping charge lower limit should not blank', 'addmpshipping');
                }
                if (isset($rangeSup[0]) && $rangeSup[0] == '') {
                    $this->errors[] = $this->module->l('Shipping charge upper limit should not blank', 'addmpshipping');
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
                        $this->errors[] = $this->module->l('Only jpg,png,jpeg image formats are allowed and image size should not exceed 125*125', 'addmpshipping');
                    } else {
                        list($width, $height) = getimagesize($_FILES['shipping_logo']['tmp_name']);
                        if ($width > 125 ||  $height > 125) {
                            $this->errors[] = $this->module->l('Only jpg,png,jpeg image formats are allowed and image size should not exceed 125*125', 'addmpshipping');
                        }
                        $isNewImage = true;
                    }
                }
            }
            if (empty($this->errors)) {
                $mpShippingId = Tools::getValue('mp_shipping_id');
                if ($mpShippingId) {
                    $objMpShippingMethod = new MpShippingMethod($mpShippingId); //Edit shipping
                } else {
                    $objMpShippingMethod = new MpShippingMethod(); //Add shipping
                }
                $objMpShippingMethod->mp_shipping_name = $shippingName;
                $objMpShippingMethod->grade = $grade;
                $objMpShippingMethod->tracking_url = $trackingUrl;
                $objMpShippingMethod->shipping_method = $shippingMethod;

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

                $objMpShippingMethod->deleted = 0;
                $objMpShippingMethod->mp_id_seller = $mpIdSeller;

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
    }

    public function addMpShippingStep2($mpShippingId)
    {
        $zoneDetail = Zone::getZones(true);

        $isFree = Tools::getValue('is_free');
        $rangeInf = Tools::getValue('range_inf');
        $rangeSup = Tools::getValue('range_sup');
        $shippingHandling = Tools::getValue('shipping_handling');
        //for tax option in seller shipping
        $idTaxRuleGroup = Tools::getValue('id_tax_rule_group');
        $rangeBehavior = Tools::getValue('range_behavior');

        if ($isFree == 0) {
            if (isset($rangeInf[0]) && $rangeInf[0] == '') {
                $this->errors[] = $this->module->l('Shipping charge lower limit should not blank', 'addmpshipping');
            }
            if (isset($rangeSup[0]) && $rangeSup[0] == '') {
                $this->errors[] = $this->module->l('Shipping charge upper limit should not blank', 'addmpshipping');
            }
        }
        if (empty($this->errors)) {
            $objMpShippingMethod = new MpShippingMethod($mpShippingId);
            $objMpShippingMethod->is_free = $isFree;
            //for tax option in seller shipping
            $objMpShippingMethod->id_tax_rule_group = $idTaxRuleGroup;
            $objMpShippingMethod->range_behavior = $rangeBehavior;
            $objMpShippingMethod->shipping_handling = $shippingHandling;
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
                    if ($isFeeSet) {
                        /*if ($isFeeSet) {
                            $range_enter = 1;
                        }*/
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
            $this->errors[] = $this->module->l('The max height field is invalid', 'addmpshipping');
        }

        if ($maxWidth == '') {
            $maxWidth = (int) 0;
        } elseif (!Validate::isUnsignedInt($maxWidth)) {
            $this->errors[] = $this->module->l('The max width field is invalid', 'addmpshipping');
        }

        if ($maxDepth == '') {
            $maxDepth = (int) 0;
        } elseif (!Validate::isUnsignedInt($maxDepth)) {
            $this->errors[] = $this->module->l('The max depth field is invalid', 'addmpshipping');
        }

        if ($maxWeight == '') {
            $maxWeight = (float) 0;
        } elseif (!Validate::isFloat($maxWeight)) {
            $this->errors[] = $this->module->l('The max weight field is invalid', 'addmpshipping');
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
                if (Tools::getValue('submitAddshipping')) {
                    if (Configuration::get('MP_SHIPPING_ADMIN_APPROVE') == 0) {
                        $objMpShippingMethod->active = 1;
                        $objMpShippingMethod->save();
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

                    //Mail to Admin if configuration set to "YES".
                    if (Configuration::get('MP_MAIL_ADMIN_SHIPPING_ADDED') == 1) {
                        $objMpShippingMethod->mailToAdminShippingAdded($mpIdSeller, $mpShippingId);
                    }
                }
            }

            $link = new Link();
            if ($updateShipping || $objMpShippingMethod->is_free) {
                $addMpShippingListLink = $link->getModuleLink('mpshipping', 'mpshippinglist', array('updatempshipping_success' => 1));
            } else {
                $addMpShippingListLink = $link->getModuleLink('mpshipping', 'addmpshipping', array('mpshipping_id' => $mpShippingId, 'addmpshipping_step4' => 1));
            }

            Tools::redirect($addMpShippingListLink);
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Marketplace', 'addmpshipping'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        );

        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Carriers', 'addmpshipping'),
            'url' => '',
        );

        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->registerJavascript('addmpshipping', 'modules/'.$this->module->name.'/views/js/addmpshipping.js');
        $this->registerJavascript('mpshippinglistjs', 'modules/'.$this->module->name.'/views/js/mpshippinglist.js');
        $this->registerStylesheet('mpshippinglistcss', 'modules/'.$this->module->name.'/views/css/mpshippinglist.css');
        $this->registerStylesheet('addmpshippingcss', 'modules/'.$this->module->name.'/views/css/addmpshipping.css');
        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');

        //If admin allow to use custom css on Marketplace theme
        if (Configuration::get('WK_MP_ALLOW_CUSTOM_CSS')) {
            $this->registerStylesheet('mp-custom_style-css', 'modules/marketplace/views/css/mp_custom_style.css');
        }
    }
}
