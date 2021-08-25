<?php
/**
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminMpsellershippingController extends ModuleAdminController
{
    public function __construct()
    {

        $this->bootstrap = true;
        $this->context = Context::getContext();

        $this->table = 'mp_shipping_method';
        $this->className = 'Mpshippingmethod';
        $this->list_no_link = true;
        $this->identifier = 'id';

        parent::__construct();

        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'mp_shipping_method_lang` mpshl ON (mpshl.`id` = a.`id`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller` mpsi ON (a.`mp_id_seller`=mpsi.id_seller)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_lang` mpsil ON (mpsil.`id_seller` = mpsi.`id_seller` AND mpsil.`id_lang` = '.$this->context->language->id.')';
        $this->_select .= 'mpsi.`seller_firstname`, mpsi.`seller_lastname`, mpsil.`shop_name`, a.`id` as `ship_id`, mpshl.`transit_delay`';
        $this->_where .= 'AND a.`is_done` = 1 AND a.`deleted` = 0 AND mpshl.`id_lang` = '.(int) $this->context->language->id;

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
                            'delete' => array(
                                'text' => $this->trans('Delete selected', array(), 'Modules.MpShipping'),
                                'confirm' => $this->trans('Delete selected items?', array(), 'Modules.MpShipping'),
                                'icon' => 'icon-trash', ),
                        );

        $this->fields_list = array(
                'id' => array(
                    'title' => $this->trans('Id', array(), 'Modules.MpShipping') ,
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                ),
                'mp_shipping_name' => array(
                    'title' => $this->trans('Shipping Name', array(), 'Modules.MpShipping') ,
                    'align' => 'center',
                ),
                'transit_delay' => array(
                    'title' => $this->trans('Transit Delay', array(), 'Modules.MpShipping') ,
                    'align' => 'center',
                ),
                'seller_firstname' => array(
                    'title' => $this->trans('Seller Name', array(), 'Modules.MpShipping') ,
                    'align' => 'center',
                ),
                'shop_name' => array(
                    'title' => $this->trans('Shop Name', array(), 'Modules.MpShipping') ,
                    'align' => 'center',
                ),
                'active' => array(
                    'title' => $this->trans('Status', array(), 'Modules.MpShipping'),
                    'active' => 'status',
                    'align' => 'center',
                    'type' => 'bool',
                    'orderby' => false,
                ),
                'ship_id' => array(
                    'title' => $this->trans('Assign Impact Price', array(), 'Modules.MpShipping'),
                    'width' => 35,
                    'align' => 'center',
                    'callback' => 'assignimpact',
                    'orderby' => false,
                    'search' => false,
                    'remove_onclick' => true,
                ),
            );

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
    }

    public function assignimpact($mp_shipping_id)
    {
        $obj_mpshipping_method = new Mpshippingmethod($mp_shipping_id);
        if ($obj_mpshipping_method->is_free) {
            $html = $this->trans('Free', array(), 'Modules.MpShipping');
        } else {
            $html = '<a class="edit btn btn-default" title="'.$this->trans('Assign', array(), 'Modules.MpShipping').'" href="'.self::$currentIndex.'&id='.$mp_shipping_id.'&updatemp_shipping_method&updateimpact=1&token='.$this->token.'"><i class="icon-pencil"></i>'.$this->trans('Assign', array(), 'Modules.MpShipping').'</a>';
        }

        return $html;
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->trans('Add New', array(), 'Modules.MpShipping')
        );
    }

    public function renderForm()
    {
        $link = new Link();
        //$id_lang = $this->context->language->id;
        $obj_mp_sellerinfo = new WkMpSeller();
        //get total zone available in prestashop
        $zone_detail = Zone::getZones();
        $this->context->smarty->assign('zones', $zone_detail);

        $mp_shipping_id = Tools::getValue('id');
        if ($mp_shipping_id) { //Edit shipping
            $obj_mpshipping_method = new Mpshippingmethod($mp_shipping_id);
            $this->context->smarty->assign('mp_shipping_id', $mp_shipping_id);
            $this->context->smarty->assign('mp_shipping_name', $obj_mpshipping_method->mp_shipping_name);
            //tax options for seller shipping
            $this->context->smarty->assign('tax_rules', TaxRulesGroup::getTaxRulesGroups(true));
            $this->context->smarty->assign('id_tax_rule_group', $obj_mpshipping_method->id_tax_rule_group);
            //end
            $this->context->smarty->assign('range_behavior', $obj_mpshipping_method->range_behavior);
            $this->context->smarty->assign('transit_delay', $obj_mpshipping_method->transit_delay);
            $this->context->smarty->assign('shipping_method', $obj_mpshipping_method->shipping_method);
            $this->context->smarty->assign('tracking_url', $obj_mpshipping_method->tracking_url);
            $this->context->smarty->assign('grade', $obj_mpshipping_method->grade);
            $this->context->smarty->assign('shipping_handling', $obj_mpshipping_method->shipping_handling);
            $this->context->smarty->assign('shipping_handling_charge', Configuration::get('PS_SHIPPING_HANDLING'));
            $this->context->smarty->assign('is_free', $obj_mpshipping_method->is_free);
            $this->context->smarty->assign('max_width', $obj_mpshipping_method->max_width);
            $this->context->smarty->assign('max_height', $obj_mpshipping_method->max_height);
            $this->context->smarty->assign('max_depth', $obj_mpshipping_method->max_depth);
            $this->context->smarty->assign('max_weight', $obj_mpshipping_method->max_weight);

            $mp_id_seller = $obj_mpshipping_method->mp_id_seller;
            $seller_customer_id = $obj_mp_sellerinfo->getCustomerIdBySellerId($mp_id_seller);
            $this->context->smarty->assign('seller_customer_id', $seller_customer_id);

            //@shipping_method==1 billing accroding to weight
            //@shipping_method==2 billing accroding to price
            $shipping_method = $obj_mpshipping_method->shipping_method;
            $ranges = array();
            if ($obj_mpshipping_method->shipping_method == 1) {
                //find all range according to weight available for this shipping method
                $obj_range_weight = new Mprangeweight();
                $obj_range_weight->mp_shipping_id = $mp_shipping_id;
                $different_range = $obj_range_weight->getAllRangeAccordingToShippingId();
                if ($different_range) {
                    $ranges = $different_range;
                } else {
                    $this->context->smarty->assign('different_range', -1);
                }
            } elseif ($obj_mpshipping_method->shipping_method == 2) {
                // find range by price available for shipping method
                $obj_range_price = new Mprangeprice();
                $obj_range_price->mp_shipping_id = $mp_shipping_id;
                $different_range = $obj_range_price->getAllRangeAccordingToShippingId();
                if ($different_range) {
                    $ranges = $different_range;
                } else {
                    $this->context->smarty->assign('different_range', -1);
                }
            }

            if (!count($ranges)) {
                $ranges[] = array('id_range' => 0, 'delimiter1' => 0, 'delimiter2' => 0);
            }

            $this->context->smarty->assign('ranges', $ranges);

            Media::addJsDef(array('mp_shipping_id' => $mp_shipping_id, 'is_free' => $obj_mpshipping_method->is_free, 'shipping_handling' => $obj_mpshipping_method->shipping_handling, 'shipping_method' => $shipping_method));

            //find zone where shipping method deliver product
            $obj_mp_delivery = new Mpshippingdelivery();
            $id_zone_detail = $obj_mp_delivery->getIdZoneByShiipingId($mp_shipping_id);
            if ($id_zone_detail) {
                $fields_value = array();
                foreach ($id_zone_detail as $id_zo_det) {
                    $fields_value['zones'][$id_zo_det['id_zone']] = 1;
                }

                $this->context->smarty->assign('fields_val', $fields_value);

                //get delivery details by shipping id its provide price for different range
                $delivery_shipping_detail = $obj_mp_delivery->getDeliveryDetailByShiipingId($mp_shipping_id);

                if ($delivery_shipping_detail) {
                    $price_by_range = array();
                    foreach ($delivery_shipping_detail as $delivery_shipping) {
                        if ($shipping_method == 2) {
                            $price_by_range[$delivery_shipping['mp_id_range_price']][$delivery_shipping['id_zone']] = round($delivery_shipping['base_price'], 2);
                        } else {
                            $price_by_range[$delivery_shipping['mp_id_range_weight']][$delivery_shipping['id_zone']] = round($delivery_shipping['base_price'], 2);
                        }
                    }

                    $this->context->smarty->assign('price_by_range', $price_by_range);
                }
            }

            if (Tools::getValue('updateimpact') == '1') {
                if ($obj_mpshipping_method->is_free) {
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminMpsellershipping'));
                } else {
                    $getimpactprice_arr = Mpshippingimpact::getAllImpactPriceByMpshippingid($mp_shipping_id);
                    if ($getimpactprice_arr) {
                        $impactprice_arr = array();
                        foreach ($getimpactprice_arr as $key => $getimpactprice) {
                            $zone_arr = Mpshippingimpact::getZonenameByZoneid($getimpactprice['id_zone']);
                            $impactprice_arr[$key]['id_zone'] = $zone_arr['name'];

                            $countryname = CountryCore::getNameById($this->context->language->id, $getimpactprice['id_country']);
                            $impactprice_arr[$key]['id_country'] = $countryname;

                            if ($getimpactprice['id_state']) {
                                $statename = StateCore::getNameById($getimpactprice['id_state']);
                                $impactprice_arr[$key]['id_state'] = $statename;
                            } else {
                                $impactprice_arr[$key]['id_state'] = 'All';
                            }

                            $impactprice_arr[$key]['shipping_delivery_id'] = $getimpactprice['shipping_delivery_id'];
                            $impactprice_arr[$key]['impact_price'] = $getimpactprice['impact_price'];
                            $impactprice_arr[$key]['id'] = $getimpactprice['id'];
                            $impactprice_arr[$key]['mp_shipping_id'] = $mp_shipping_id;
                            /*Range Or weight for the impact*/
                            $obj_mpshipping_deliv = new Mpshippingdelivery($getimpactprice['shipping_delivery_id']);
                            if ($shipping_method == 2) {
                                $obj_range = new Mprangeprice($obj_mpshipping_deliv->mp_id_range_price);
                                $impactprice_arr[$key]['price_range'] = Tools::ps_round($obj_range->delimiter1, 2).'-'.Tools::ps_round($obj_range->delimiter2, 2);
                            } else {
                                $obj_weight = new Mprangeweight($obj_mpshipping_deliv->mp_id_range_weight);
                                $impactprice_arr[$key]['weight_range'] = Tools::ps_round($obj_weight->delimiter1, 2).'-'.Tools::ps_round($obj_weight->delimiter2, 2);
                            }
                            /*END*/
                        }
                        $this->context->smarty->assign('ship_method', $shipping_method);
                        $this->context->smarty->assign('impactprice_arr', $impactprice_arr);
                    }

                    $shipping_ajax_link = $link->getModuleLink('mpshipping', 'shippingajax');
                    $this->context->smarty->assign('mpshipping_id', $mp_shipping_id);
                    $this->context->smarty->assign('shipping_ajax_link', $shipping_ajax_link);
                    $this->context->smarty->assign('updateimpact', 1);

                    $jsDefVar = [
                        'shipping_ajax_link' => $shipping_ajax_link,
                        'img_ps_dir' => _MODULE_DIR_.'marketplace/views/img/',
                        'select_country' => $this->trans('Select country', array(), 'Modules.MpShipping'),
                        'select_state' => $this->trans('All', array(), 'Modules.MpShipping'),
                        'zone_error' => $this->trans('Select Zone', array(), 'Modules.MpShipping'),
                        'no_range_available_error' => $this->trans('No Range Available', array(), 'Modules.MpShipping'),
                        'ranges_info' => $this->trans('Ranges', array(), 'Modules.MpShipping'),
                        'message_impact_price_error' => $this->trans('Price should be numeric', array(), 'Modules.MpShipping'),
                        'message_impact_price' => $this->trans('Impact added sucessfully', array(), 'Modules.MpShipping'),
                    ];

                    Media::addJsDef($jsDefVar);
                }
            }
        } else {
            $shipping_method = 2;
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
            //for tax options seller shipping
            $this->context->smarty->assign('tax_rules', TaxRulesGroup::getTaxRulesGroups(true));
            //seller customer information
            $customer_info = WkMpSeller::getAllSeller();
            if ($customer_info) {
                $this->context->smarty->assign('customer_info', $customer_info);

                //get first seller from the list
                $first_seller_details = $customer_info[0];
                $mp_id_seller = $first_seller_details['id_seller'];
            } else {
                $mp_id_seller = 0;
            }
        }

        // Multi-lang start
        $adminproducturl = $this->context->link->getAdminLink('AdminSellerProductDetail');
        $this->context->smarty->assign('adminproducturl', $adminproducturl);
        // Set default lang at every form according to configuration multi-language
        WkMpHelper::assignDefaultLang($mp_id_seller);
        // Multilang end

        $update_impact_link = $this->context->link->getAdminLink('AdminMpsellershipping').'&id='.$mp_shipping_id.'&updatemp_shipping_method&updateimpact=1';
        $this->context->smarty->assign('update_impact_link', $update_impact_link);
        $this->context->smarty->assign('self', dirname(__FILE__));
        $this->context->smarty->assign('PS_WEIGHT_UNIT', Configuration::get('PS_WEIGHT_UNIT'));

        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $this->context->smarty->assign('currency_sign', $currency->sign);

        $jsDefVariable = [
            'adminproducturl' => $adminproducturl,
            'update_impact_link' => $update_impact_link,
            'currency_sign' => $currency->sign,
        ];

        Media::addJsDef($jsDefVariable);

        if (!Tools::getValue('updateimpact')) {

            $jsDefVar = [
                'PS_WEIGHT_UNIT' => Configuration::get('PS_WEIGHT_UNIT'),
                'shipping_method' => $shipping_method,
                'string_price' => $this->trans('Will be applied when the price is', array(), 'Modules.MpShipping'),
                'string_weight' => $this->trans('Will be applied when the weight is', array(), 'Modules.MpShipping'),
                'invalid_range' => $this->trans('This range is not valid', array(), 'Modules.MpShipping'),
                'need_to_validate' => $this->trans('Please validate the last range before create a new one.', array(), 'Modules.MpShipping'),
                'delete_range_confirm' => $this->trans('Are you sure to delete this range ?', array(), 'Modules.MpShipping'),
                'labelDelete' => $this->trans('Delete', array(), 'Modules.MpShipping'),
                'labelValidate' => $this->trans('Validate', array(), 'Modules.MpShipping'),
                'range_is_overlapping' => $this->trans('Ranges are overlapping', array(), 'Modules.MpShipping'),
                'finish_error' => $this->trans('You need to go through all step', array(), 'Modules.MpShipping'),
                'shipping_name_error' => $this->trans('Carrier name is required field', array(), 'Modules.MpShipping'),
                'transit_time_error' => $this->trans('Transit time is required atleast in ', array(), 'Modules.MpShipping'),
                'transit_time_error_other' => $this->trans('Transit time is required atleast in ', array(), 'Modules.MpShipping'),
                'speedgradeinvalid' => $this->trans('Speed grade must be integer', array(), 'Modules.MpShipping'),
                'speedgradevalue' => $this->trans('Speed grade must be from 0 to ', array(), 'Modules.MpShipping'),
                'invalid_logo_file_error' => $this->trans('Invalid logo file!', array(), 'Modules.MpShipping'),
                'shipping_charge_error_message' => $this->trans('Shipping charge is not valid.', array(), 'Modules.MpShipping'),
                'shipping_charge_lower_limit_error1' => $this->trans('Shipping charge lower limit must be numeric.', array(), 'Modules.MpShipping'),
                'shipping_charge_lower_limit_error2' => $this->trans('Shipping charge lower limit should not negative', array(), 'Modules.MpShipping'),
                'shipping_charge_upper_limit_error1' => $this->trans('Shipping charge upper limit must be numeric', array(), 'Modules.MpShipping'),
                'shipping_charge_upper_limit_error2' => $this->trans('Shipping charge upper limit should not negative', array(), 'Modules.MpShipping'),
                'shipping_charge_limit_error' => $this->trans('Shipping charge upper limit must be greater than lower limit', array(), 'Modules.MpShipping'),
                'shipping_charge_limit_equal_error' => $this->trans('Shipping charge lower limit and upper limit should not equal', array(), 'Modules.MpShipping'),
                'invalid_logo_size_error' => $this->trans('Invalid logo size', array(), 'Modules.MpShipping'),
                'invalid_range_value' => $this->trans('Ranges upper and lower values should not clash to one another.', array(), 'Modules.MpShipping'),
                'shipping_select_zone_err' => $this->trans('Select atleast one zone.', array(), 'Modules.MpShipping'),
            ];

            Media::addJsDef($jsDefVar);
        }

        if ($shipping_method == 2) {
            Media::addJsDef(array('range_sign' => $currency->sign));
        } else {
            Media::addJsDef(array('range_sign' => Configuration::get('PS_WEIGHT_UNIT')));
        }

        $this->fields_form = array(
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Modules.MpShipping'),
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
            $mp_shipping_id = Tools::getValue('id');
            $impact_id = Tools::getValue('impact_id');
            if ($impact_id) {
                $obj_mp_ship_impact = new Mpshippingimpact($impact_id);
                $obj_mp_ship_impact->delete();
                //Db::getInstance()->delete('mp_shipping_impact','id='.$impact_id);
            }
            Tools::redirectAdmin(self::$currentIndex.'&id='.$mp_shipping_id.'&updatemp_shipping_method&updateimpact=1&conf=1&token='.$this->token);
        }

        if (Tools::isSubmit('FinishButtonclick')) {
            $shipping_name = Tools::getValue('shipping_name');
            $is_valid_shipping_name = Validate::isCarrierName($shipping_name);
            $grade = Tools::getValue('grade');
            $is_valid_grade = Validate::isUnsignedInt($grade);
            $shipping_method = Tools::getValue('shipping_method');
            $tracking_url = Tools::getValue('tracking_url');
            $is_valid_tracking_url = Validate::isAbsoluteUrl($tracking_url);

            //If multi-lang is OFF then PS default lang will be default lang for seller from Marketplace Configuration page
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $default_lang = Tools::getValue('current_lang');
            } else {
                if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') { //Admin Default lang
                    $default_lang = Configuration::get('PS_LANG_DEFAULT');
                } elseif (Configuration::get('MP_MULTILANG_DEFAULT_LANG') == '2') { //Seller Default lang
                    $default_lang = Tools::getValue('current_lang');
                } else {
                  $default_lang = Tools::getValue('current_lang');
                  
                }
            }

            $seller_customer_id = Tools::getValue('seller_customer_id');
            /*$obj_seller_info = new WkMpSeller();*/
            $mp_customer_info = WkMpSeller::getSellerDetailByCustomerId($seller_customer_id);
            if ($mp_customer_info['id_seller']) {
                $mp_id_seller = $mp_customer_info['id_seller'];
            } else {
                $mp_id_seller = 0;
            }

            if (!$mp_id_seller) {
                $this->errors[] = Tools::displayError($this->trans('Selected Customer is not a Seller', array(), 'Modules.MpShipping'));
            }

            if (!$shipping_name) {
                $this->errors[] = Tools::displayError($this->trans('Carrier name is required.', array(), 'Modules.MpShipping'));
            } elseif (!$is_valid_shipping_name) {
                $this->errors[] = Tools::displayError('Carrier name must not have Invalid characters /^[^<>;=#{}]*$/u');
            } elseif (Tools::strlen($shipping_name) > 64) {
                $this->errors[] = Tools::displayError($this->trans('Carrier name field is too long (64 chars max).', array(), 'Modules.MpShipping'));
            }

            if (!Tools::getValue('transit_time_'.$default_lang)) {
                if (Configuration::get('MP_MULTILANG_ADMIN_APPROVE')) {
                    $seller_lang_arr = Language::getLanguage((int) $default_lang);
                    $this->errors[] = Tools::displayError($this->trans('Transit time is required in '.$seller_lang_arr['name']));
                } else {
                    $this->errors[] = Tools::displayError($this->trans('Transit time is required', array(), 'Modules.MpShipping'));
                }
            } elseif (!Validate::isGenericName(Tools::getValue('transit_time_'.$default_lang))) {
                $this->errors[] = Tools::displayError($this->trans('Transit time must not have Invalid characters /^[^<>={}]*$/u', array(), 'Modules.MpShipping'));
            }

            if (!$is_valid_grade) {
                $this->errors[] = Tools::displayError($this->trans('Speed grade must be numeric', array(), 'Modules.MpShipping'));
            } elseif ($grade < 0 || $grade > 9) {
                $this->errors[] = Tools::displayError($this->trans('Speed grade must be from 0 to 9', array(), 'Modules.MpShipping'));
            }

            if (!$is_valid_tracking_url) {
                $this->errors[] = Tools::displayError($this->trans('Invalid Tracking Url', array(), 'Modules.MpShipping'));
            }

            $is_new_image = false;
            if (isset($_FILES['shipping_logo'])) {
                if ($_FILES['shipping_logo']['size'] > 0 && $_FILES['shipping_logo']['tmp_name'] != '') {
                    $image_type = array('jpg','jpeg','png');
                    $extention = explode('.', $_FILES['shipping_logo']['name']);
                    $ext = Tools::strtolower($extention['1']);
                    if (!in_array($ext, $image_type)) {
                        $this->errors[] = Tools::displayError($this->trans('Only jpg,png,jpeg image allow and image size should not exceed 125*125', array(), 'Modules.MpShipping'));
                    } else {
                        list($width, $height) = getimagesize($_FILES['shipping_logo']['tmp_name']);
                        if ($width > 125 ||  $height > 125) {
                            $this->errors[] = Tools::displayError($this->trans('Only jpg,png,jpeg image allow and image size should not exceed 125*125', array(), 'Modules.MpShipping'));
                        }
                        $is_new_image = true;
                    }
                }
            }

            if (empty($this->errors)) {
                $mpshipping_id = Tools::getValue('id');
                if ($mpshipping_id) {
                    $obj_mpshipping_method = new Mpshippingmethod($mpshipping_id); //Edit shipping
                } else {
                    $obj_mpshipping_method = new Mpshippingmethod(); //Add shipping
                }

                $obj_mpshipping_method->mp_shipping_name = $shipping_name;
                $obj_mpshipping_method->grade = $grade;
                $obj_mpshipping_method->shipping_method = $shipping_method;
                $obj_mpshipping_method->deleted = 0;
                $obj_mpshipping_method->mp_id_seller = $mp_id_seller;

                foreach (Language::getLanguages(true) as $language) {
                    $transit_lang_id = $language['id_lang'];

                    if (Configuration::get('MP_MULTILANG_ADMIN_APPROVE')) {
                        //if product name in other language is not available then fill with seller language same for others
                        if (!Tools::getValue('transit_time_'.$language['id_lang'])) {
                            $transit_lang_id = $default_lang;
                        }
                    } else {
                        //if multilang is OFF then all fields will be filled as default lang content
                        $transit_lang_id = $default_lang;
                    }

                    $obj_mpshipping_method->transit_delay[$language['id_lang']] = Tools::getValue('transit_time_'.$transit_lang_id);
                }

                if ($mpshipping_id) {
                    $obj_mpshipping_method->save();
                    $updateshipping = 1;
                } else {
                    $obj_mpshipping_method->id_ps_reference = 0;
                    $obj_mpshipping_method->save();
                    $mpshipping_id = $obj_mpshipping_method->id;
                    $updateshipping = 0;
                }

                if ($is_new_image == true) {
                    $dir = _PS_MODULE_DIR_.'mpshipping/views/img/logo/'.$mpshipping_id.'.jpg';
                    ImageManager::resize($_FILES['shipping_logo']['tmp_name'], $dir);
                }
                $this->addMpShippingStep2($mpshipping_id);
                $this->addMpShippingStep3($mpshipping_id, $updateshipping, $is_new_image, $mp_id_seller);
            }
        }
        parent::postProcess();
    }

    public function addMpShippingStep2($mpshipping_id)
    {
        $zone_detail = Zone::getZones();

        $is_free = Tools::getValue('is_free');
        $range_inf = Tools::getValue('range_inf');
        $range_sup = Tools::getValue('range_sup');
        $shipping_handling = Tools::getValue('shipping_handling');
        //for tax options
        $id_tax_rule_group = Tools::getValue('id_tax_rule_group');
        $range_behavior = Tools::getValue('range_behavior');
        //end

        if ($is_free == 0) {
            if (!$range_inf) {
                $this->errors[] = Tools::displayError($this->trans('Shipping charge lower limit should not blank', array(), 'Modules.MpShipping'));
            }
            if (!$range_sup) {
                $this->errors[] = Tools::displayError($this->trans('Shipping charge upper limit should not blank', array(), 'Modules.MpShipping'));
            }
        }
        if (empty($this->errors)) {
            $obj_mpshipping_method = new Mpshippingmethod($mpshipping_id);
            $obj_mpshipping_method->is_free = $is_free;
            $obj_mpshipping_method->shipping_handling = $shipping_handling;
            //for tax options
            $obj_mpshipping_method->id_tax_rule_group = $id_tax_rule_group;
            $obj_mpshipping_method->range_behavior = $range_behavior;
            //end
            $obj_mpshipping_method->save();

            $shipping_method = $obj_mpshipping_method->shipping_method;
            if ($shipping_method == 2) {
                $range_type = 2;
                $obj_range_obj = new Mprangeprice(); //obj for price
            } elseif ($shipping_method == 1) {
                $range_type = 1;
                $obj_range_obj = new Mprangeweight(); //obj for weight
            }
            if ($is_free) {
                $save_num = 1;
                $range_count = 1;
                foreach ($zone_detail as $zone) {
                    $obj_mpshipping_del = new Mpshippingdelivery();
                    $zone_id = $zone['id_zone'];
                    $post_name = 'zone_'.$zone_id;
                    $is_fee_set = Tools::getValue($post_name);
                    /*if ($is_fee_set) {
                        $range_enter = 1;
                    }*/
                    if ($is_fee_set) {
                        $obj_mpshipping_del->mp_shipping_id = $mpshipping_id;
                        $obj_mpshipping_del->id_zone = $zone_id;
                        $obj_mpshipping_del->mp_id_range_price = 0;
                        $obj_mpshipping_del->mp_id_range_weight = 0;
                        $obj_mpshipping_del->base_price = (float) 0;
                        if ($range_count == 1) {
                            $obj_range_obj->delimiter1 = (float) 0;
                            $obj_range_obj->delimiter2 = (float) 0;

                            $obj_range_obj->mp_shipping_id = $mpshipping_id;
                            //$is_available = $obj_range_obj->isRangeInTableByShippingId();
                            $obj_range_obj->add();
                            $first_range_id = $obj_range_obj->id;
                            ++$range_count;
                        }
                        $obj_mpshipping_del->mp_id_range_price = $obj_range_obj->id;
                        $obj_mpshipping_del->save();

                        if ($save_num == 1) {
                            $first_del_id = $obj_mpshipping_del->id;
                            ++$save_num;
                        }
                    }
                }

                if (!isset($first_del_id)) {
                    $first_del_id = Mpshippingdelivery::getShippingDeliveryLastId($mpshipping_id);
                }
                if (!isset($first_range_id)) {
                    if ($range_type == 2) {
                        $first_range_id = Mprangeweight::getWeightRangeLastId($mpshipping_id);
                    } else {
                        $first_range_id = Mprangeprice::getPriceRangeLastId($mpshipping_id);
                    }
                }
                Mpshippingimpact::updateImpactAfterUpdateShipping($mpshipping_id, $first_del_id, $first_range_id, $range_type);
            } else {
                if ($obj_range_obj) {
                    $range_count = 1;
                    $save_num = 1;
                    foreach ($range_inf as $key => $value) {
                        if ($range_inf[$key] != '') {
                            $obj_range_obj->delimiter1 = $value;

                            if ($range_sup[$key] == '') {
                                $obj_range_obj->delimiter2 = (float) 0;
                            } else {
                                $obj_range_obj->delimiter2 = $range_sup[$key];
                            }

                            $obj_range_obj->mp_shipping_id = $mpshipping_id;

                            $obj_range_obj->add();
                            if ($range_count == 1) {
                                $first_range_id = $obj_range_obj->id;
                                ++$range_count;
                            }
                            foreach ($zone_detail as $zone) {
                                $obj_mpshipping_deliv = new Mpshippingdelivery();
                                $zone_id = $zone['id_zone'];
                                $post_name = 'zone_'.$zone_id;
                                $is_fee_set = Tools::getValue($post_name);
                                if ($is_fee_set) {
                                    $obj_mpshipping_deliv->mp_shipping_id = $mpshipping_id;
                                    $obj_mpshipping_deliv->id_zone = $zone_id;
                                    if ($shipping_method == 2) {
                                        $obj_mpshipping_deliv->mp_id_range_price = $obj_range_obj->id;
                                        $obj_mpshipping_deliv->mp_id_range_weight = 0;
                                    } elseif ($shipping_method == 1) {
                                        $obj_mpshipping_deliv->mp_id_range_weight = $obj_range_obj->id;
                                        $obj_mpshipping_deliv->mp_id_range_price = 0;
                                    }
                                    $zone_fees = Tools::getValue('fees');
                                    $obj_mpshipping_deliv->base_price = (float) $zone_fees[$zone_id][$key];
                                    if ($obj_mpshipping_deliv->base_price == 'on' || $obj_mpshipping_deliv->base_price == '') {
                                        $obj_mpshipping_deliv->base_price = 0;
                                    }
                                    $obj_mpshipping_deliv->save();
                                    if ($save_num == 1) {
                                        $first_del_id = $obj_mpshipping_deliv->id;
                                        ++$save_num;
                                    }
                                }
                            }
                        }
                    }
                }
                Mpshippingimpact::updateImpactAfterUpdateShipping($mpshipping_id, $first_del_id, $first_range_id, $range_type);
            }
        }
    }

    public function addMpShippingStep3($mpshipping_id, $updateshipping, $is_new_image, $mp_id_seller)
    {
        $max_height = Tools::getValue('max_height');
        $max_width = Tools::getValue('max_width');
        $max_depth = Tools::getValue('max_depth');
        $max_weight = Tools::getValue('max_weight');

        if ($max_height == '') {
            $max_height = (int) 0;
        } elseif (!Validate::isUnsignedInt($max_height)) {
            $this->errors[] = Tools::displayError('The max height field is invalid');
        }

        if ($max_width == '') {
            $max_width = (int) 0;
        } elseif (!Validate::isUnsignedInt($max_width)) {
            $this->errors[] = Tools::displayError('The max width field is invalid');
        }

        if ($max_depth == '') {
            $max_depth = (int) 0;
        } elseif (!Validate::isUnsignedInt($max_depth)) {
            $this->errors[] = Tools::displayError('The max depth field is invalid');
        }

        if ($max_weight == '') {
            $max_weight = (float) 0;
        } elseif (!Validate::isFloat($max_weight)) {
            $this->errors[] = Tools::displayError('The max weight field is invalid');
        }

        if (empty($this->errors)) {
            $obj_mpshipping_method = new Mpshippingmethod($mpshipping_id);
            $obj_mpshipping_method->max_height = $max_height;
            $obj_mpshipping_method->max_width = $max_width;
            $obj_mpshipping_method->max_depth = $max_depth;
            $obj_mpshipping_method->max_weight = $max_weight;
            $obj_mpshipping_method->is_done = 1;
            $obj_mpshipping_method->save();

            $id_ps_reference = Mpshippingmethod::getReferenceByMpShippingId($mpshipping_id);
            if ($id_ps_reference) {
                $obj_mpshipping = new Mpshippingmethod();
                $id_ps_carriers = $obj_mpshipping->updateToCarrier($mpshipping_id, $id_ps_reference);
                if ($is_new_image && $id_ps_carriers) {
                    $img_dir = _PS_MODULE_DIR_.'mpshipping/views/img/logo/';
                    if (file_exists($img_dir.$mpshipping_id.'.jpg')) {
                        copy($img_dir.$mpshipping_id.'.jpg', _PS_IMG_DIR_.'s/'.$id_ps_carriers.'.jpg');
                    }
                }
            } else {
                //Autoapprove of shipping method.
                if (Configuration::get('MP_SHIPPING_ADMIN_APPROVE') == 0 && !Tools::getValue('id')) {
                    $id_ps_reference_added = $obj_mpshipping_method->addToCarrier($mpshipping_id);
                    $obj_mpshipping_method->enableShipping($mpshipping_id, $id_ps_reference_added);

                    if (Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL') == 1) {
                        $obj_mpshipping_method->mailToSeller($mp_id_seller, $mpshipping_id, 1);
                    }
                }
                //Mail to admin when new shipping method added.
                if (Configuration::get('MP_MAIL_ADMIN_SHIPPING_ADDED') == 1 && !Tools::getValue('id')) {
                    $obj_mpshipping_method->mailToAdminShippingAdded($mp_id_seller, $mpshipping_id);
                }
            }

            if ($updateshipping) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        }
    }

    public function processBulkStatusSelection($status)
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $bulk_id) {
                $this->toggleStatus($bulk_id);
            }
            Tools::redirectAdmin(self::$currentIndex.'&conf=5&token='.$this->token);
        }

        parent::processBulkStatusSelection($status);
    }

    public function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $bulk_id) {
                $this->deleteShipping($bulk_id);
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

        $id_lang = $this->context->language->id;
        $obj_carr = new Carrier();
        $carr_detials = $obj_carr->getCarriers($id_lang, true);
        if (empty($carr_detials)) {
            $json = array('status' => 'ko', 'msg' => 'No Carriers available');
            echo Tools::jsonEncode($json);
        } else {
            $this->assignCarriersToMainProduct($id_lang);
            $json = array('status' => 'ok', 'msg' => 'Carriers assigned successfully.');
            echo Tools::jsonEncode($json);
        }
        die; //ajax close
    }

    public function assignCarriersToMainProduct($id_lang)
    {
        $obj_shipmap = new Mpshippingproductmap();

        $start = 0;
        $limit = 0;
        $order_by = 'id_product';
        $order_way = 'ASC';

        $carr_ref = array();
        $AllPs_Carriers_Only = Mpshippingmethod::getOnlyPrestaCarriers($id_lang);
        if ($AllPs_Carriers_Only) {
            foreach ($AllPs_Carriers_Only as $ps_carriers) {
                $carr_ref[] = $ps_carriers['id_reference'];
            }
        }

        $ps_prod_info = Product::getProducts($id_lang, $start, $limit, $order_by, $order_way, false, true);
        foreach ($ps_prod_info as $product) {
            if (!$obj_shipmap->checkMpProduct($product['id_product'])) {
                $obj_shipmap->setProductCarrier($product['id_product'], $carr_ref);
            }
        }
    }

    public function toggleStatus($bulk_id = false)
    {
        if ($bulk_id) {
            $mp_shipping_id = $bulk_id;
        } else {
            $mp_shipping_id = Tools::getValue('id');
        }

        $obj_mp_shipping_met = new Mpshippingmethod($mp_shipping_id);

        $id_ps_reference = Mpshippingmethod::getReferenceByMpShippingId($mp_shipping_id);
        if ($id_ps_reference) {
            if ($obj_mp_shipping_met->active == 1) { //going to deactive
                $obj_mp_shipping_met->active = 0;
                $obj_mp_shipping_met->save();

                //remove from default shipping of seller
                Mpshippingmethod::updateDefaultShipping($mp_shipping_id, 0);

                $obj_carrier = Carrier::getCarrierByReference($id_ps_reference);
                $obj_carrier->active = 0;
                if ($obj_mp_shipping_met->is_free) {
                    $obj_carrier->is_free = 1;
                }
                $obj_carrier->save();

                $obj_mp_ship_prod_map = new Mpshippingproductmap();
                $obj_mp_ship_prod_map->deleteMpShippingProductMapOnDeactivate($mp_shipping_id);

                if (Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL') == 1) {
                    $obj_mp_shipping_met->mailToSeller($obj_mp_shipping_met->mp_id_seller, $mp_shipping_id, 0);
                }

                /*When deactivate any seller shipping method then check if only this shipping method is applied on the sellers product or all deactive shippings are applied on the product then default chosen shippings by admin should be applied on those products*/
                $obj_mp_shipping_met = new Mpshippingmethod();
                $obj_mp_shipping_met->updateCarriersOnDeactivateOrDelete();
                /*END*/
            } else { //going to active
                $obj_mp_shipping_met->active = 1;
                $obj_mp_shipping_met->save();

                $obj_carrier = Carrier::getCarrierByReference($id_ps_reference);
                if ($obj_mp_shipping_met->is_free) {
                    $obj_carrier->is_free = 1;
                }
                $obj_carrier->active = 1;
                $obj_carrier->save();

                if (Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL') == 1) {
                    $obj_mp_shipping_met->mailToSeller($obj_mp_shipping_met->mp_id_seller, $mp_shipping_id, 1);
                }
            }
        } else { //going to first time active
            $obj_mp_shipping_met->active = 1;
            $obj_mp_shipping_met->save();

            $id_ps_reference_added = $obj_mp_shipping_met->addToCarrier($mp_shipping_id);
            if ($id_ps_reference_added) {
                $obj_mp_shipping = new Mpshippingmethod($mp_shipping_id);
                $obj_mp_shipping->id_ps_reference = $id_ps_reference_added;
                $obj_mp_shipping->save();

                if (Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL') == 1) {
                    $obj_mp_shipping_met->mailToSeller($obj_mp_shipping_met->mp_id_seller, $mp_shipping_id, 1);
                }

                $img_dir = _PS_MODULE_DIR_.'mpshipping/views/img/logo/';
                if (file_exists($img_dir.$mp_shipping_id.'.jpg')) {
                    copy($img_dir.$mp_shipping_id.'.jpg', _PS_IMG_DIR_.'s/'.$id_ps_reference_added.'.jpg');
                }
            }
        }
    }

    public function deleteShipping($bulk_id = false)
    {
        if ($bulk_id) {
            $mp_shipping_id = $bulk_id;
        } else {
            $mp_shipping_id = Tools::getValue('id');
        }

        //$obj_shipping_prod = new Mpshippingproductmap();
        //$mpprod_map = $obj_shipping_prod->getMpShippingForProducts($mp_shipping_id);

        //delete shipping all data
        $obj_mp_shipping = new Mpshippingmethod();
        $obj_mp_shipping->deleteMpShipping($mp_shipping_id);

        /*Assign new selected shipping methods to the seller produccts which have no seller shipping methods*/
        $obj_mp_shipping_met = new Mpshippingmethod();
        $obj_mp_shipping_met->updateCarriersOnDeactivateOrDelete();
        /*END*/
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addCSS(array(_MODULE_DIR_.$this->module->name.'/views/css/style.css'));
        $this->addJS(array(_MODULE_DIR_.$this->module->name.'/views/js/fieldform.js'));
        $this->addCSS(_MODULE_DIR_.'mpshipping/views/css/mpshippinglist.css');
        $this->addJs(_MODULE_DIR_.'mpshipping/views/js/addmpshipping.js');
    }
}
