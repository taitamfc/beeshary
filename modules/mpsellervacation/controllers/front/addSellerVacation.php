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

class MpSellerVacationAddSellerVacationModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
       
        if ($this->context->customer->isLogged()) {
			parent::initContent();
            $customer_id = $this->context->customer->id;
            $obj_marketplace_seller = new WkMpSeller();
            $seller_info = $obj_marketplace_seller->getSellerDetailByCustomerId($customer_id);
            $id_seller = $seller_info['id_seller'];
            if ($id_seller) {
                $obj_seller_vacation_detail = new SellerVacationDetail();
                $vacation_id = Tools::getValue('id');
                if ($vacation_id) {
                    //edit page
                    $previous_vacation_detail = $obj_seller_vacation_detail->checkPreviousVcationDetail($id_seller, $vacation_id);
                    if ($previous_vacation_detail) {
                        $disable_dates = SellerVacationDetail::disableDates($previous_vacation_detail);
                        $this->context->smarty->assign('dates_array', Tools::jsonEncode($disable_dates));
                        Media::addJsDef(array('dates_array' => Tools::jsonEncode($disable_dates)));
                    }

                    $vacation_info = $obj_seller_vacation_detail->getMpsVacationDetailByEditId($vacation_id);
                    if ($vacation_info) {
                        $vacation_lang_info = $obj_seller_vacation_detail->getMpsVacationLangDetailByEditId($vacation_id);
                        if ($vacation_lang_info) {
                            foreach ($vacation_lang_info as $vacation_lang) {
                                $vacation_info['description'][$vacation_lang['id_lang']] = $vacation_lang['description'];
                            }
                        }
                    }

                    $this->context->smarty->assign('id', $vacation_id);
                    $this->context->smarty->assign('vacation_info', $vacation_info);
                } else {
                    $previous_vacation_detail = $obj_seller_vacation_detail->checkPreviousVcationDetail($id_seller);
                    $result = array();
                    if ($previous_vacation_detail) {
                        $disable_dates = SellerVacationDetail::disableDates($previous_vacation_detail);
                        $this->context->smarty->assign('dates_array', Tools::jsonEncode($disable_dates));
                        Media::addJsDef(array('dates_array' => Tools::jsonEncode($disable_dates)));
                    }
                }
				$objMpSeller = new WkMpSeller();
						//setMedia();
						if ($this->context->customer->isLogged()) {
						$smartyVar = array();
						$seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);

						}
						parent::initContent();
						$this->context->smarty->assign(array(
						'mp_seller_info' => $seller));

                // Set default lang at every form according to configuration multi-language
                WkMpHelper::assignDefaultLang($id_seller);

                $this->context->smarty->assign(array(
					'logic' => 'vac_details',
					'title_text_color' => Configuration::get('MP_TITLE_TEXT_COLOR'),
					'title_bg_color' => Configuration::get('MP_TITLE_BG_COLOR'),
				));

                $this->setTemplate('module:mpsellervacation/views/templates/front/add_seller_vacation.tpl');
            }
        } else {
            Tools::redirect('index.php?controller=authentication');
			//$this->context->link->redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('mp_vac_submit_btn')) {
            if (isset($this->context->customer->id)) {
                $customer_id = $this->context->customer->id;
                $obj_marketplace_seller = new WkMpSeller();
                $obj_seller_vacation_detail = new SellerVacationDetail();
                $seller_info = $obj_marketplace_seller->getSellerDetailByCustomerId($customer_id);
                $seller_id = $seller_info['id_seller'];

                $from_date = Tools::getValue('from');
                $to_date = Tools::getValue('to');
                $add_to_cart = Tools::getValue('addtocart');
                $vacation_id = Tools::getValue('id');
				
				$from_date 	= str_replace('/','-',$from_date);
				$to_date 	= str_replace('/','-',$to_date);
				$from_date  = ($from_date) ? date( 'Y-m-d', strtotime($from_date) ) : '';
				$to_date  	= ($to_date) ? date( 'Y-m-d', strtotime($to_date) ) : '';

                //If multi-lang is OFF then PS default lang will be default lang for seller
                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    $default_lang = Tools::getValue('selected_lang');
                } else {
                    if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') { //Admin default lang
                        $default_lang = Configuration::get('PS_LANG_DEFAULT');
                    } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') { //Seller default lang
                        $default_lang = Tools::getValue('selected_lang');
                    }
                }

                if (empty($from_date)) {
                    $this->errors[] = Tools::displayError($this->module->l('Starting Date is required.'));
                } elseif (empty($to_date)) {
                    $this->errors[] = Tools::displayError($this->module->l('Ending Date is required.'));
                } elseif ($from_date < date('Y-m-d')) {
                    $this->errors[] = Tools::displayError($this->module->l('Starting Date can not be less than from Today Date.'));
                } elseif ($to_date < date('Y-m-d')) {
                        $this->errors[] = Tools::displayError($this->module->l('Ending Date can not be less than from Today Date.'));
                } elseif ($to_date < $from_date) {
                    $this->errors[] = Tools::displayError($this->module->l('Starting Date must be less than Ending Date.'));
                }
                if (!$vacation_id) {
                    if ($vacation_details = SellerVacationDetail::getValidVacationDetailsBySellerId($seller_id)) {
                        //$current_ramge_dates = $this->disableDates($vacation_details);
                        $first = strtotime($from_date);
                        $last = strtotime($to_date);
                        $current_range_dates = SellerVacationDetail::getDateRange($first, $last, 'Y-n-j');
                        $previous_vacation_detail = $obj_seller_vacation_detail->checkPreviousVcationDetail($seller_id);
                        if ($previous_vacation_detail) {
                            $previous_range_dates = SellerVacationDetail::disableDates($previous_vacation_detail);
                        }
                        $f = 0;
                        foreach ($current_range_dates as $current_date) {
                            if (in_array($current_date, $previous_range_dates)) {
                                $f = 1;
                                break;
                            }
                        }
                        if ($f) {
                            $this->errors[] = Tools::displayError($this->module->l('Vacation(s) are already exists in which you have selected date range. Please choose different date range.'));
                        }
                    }
                }

                if (!Tools::getValue('description_'.$default_lang)) {
                    if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                        $default_lang_arr = Language::getLanguage((int) $default_lang);
                        $this->errors[] = Tools::displayError($this->module->l('Description is required in '.$default_lang_arr['name']));
                    } else {
                        $this->errors[] = Tools::displayError($this->module->l('Description is required'));
                    }
                }

                if (!count($this->errors)) {
                    $obj_seller_vacation_detail = new SellerVacationDetail();
                    $seller_product_detail = $obj_seller_vacation_detail->getMpSellerProductDetail($seller_id);
                    $from_date_check = SellerVacationDetail::mpSellerVacationCheckFromDate($from_date);
                    $first = strtotime($from_date);
                    $last = strtotime($to_date);
                    $date_range = SellerVacationDetail::getDateRange($first, $last);
                    $is_in_vacation = in_array(date('Y-m-d'), $date_range);

                    if ('on' == $add_to_cart) {
                        $add_to_cart = 1;
                    } else {
                        $add_to_cart = 0;
                    }

                    if ($is_in_vacation) {
                        $obj_seller_vacation_detail->mpSellerVacationEnableDisableAddToCart($seller_product_detail, $add_to_cart);
                    } else {
                        $obj_seller_vacation_detail->mpSellerVacationEnableDisableAddToCart($seller_product_detail, 1);
                    }

                    if ($vacation_id) {
                        $obj_seller_vacation_detail = new SellerVacationDetail($vacation_id);
                        $obj_seller_vacation_detail->id_seller = $seller_id;
                        $obj_seller_vacation_detail->from = $from_date;
                        $obj_seller_vacation_detail->to = $to_date;

                        foreach (Language::getLanguages(true) as $language) {
                            $desc_lang_id = $language['id_lang'];
                            //ifdescription in other language is not available then fill with context language
                            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                                //if product name in other language is not available then fill with seller language same for others
                                if (!Tools::getValue('description_'.$language['id_lang'])) {
                                    $desc_lang_id = $default_lang;
                                }
                            } else {
                                //if multilang is OFF then all fields will be filled as default lang content
                                $desc_lang_id = $default_lang;
                            }

                            $obj_seller_vacation_detail->description[$language['id_lang']] = Tools::getValue('description_'.$desc_lang_id);
                        }

                        $obj_seller_vacation_detail->addtocart = $add_to_cart;
                        $obj_seller_vacation_detail->save();

                        Tools::redirect($this->context->link->getModuleLink('mpsellervacation', 'sellerVacationDetail', array('updated' => 1)));
                    } else {
                        $obj_seller_vacation_detail->id_seller = $seller_id;
                        $obj_seller_vacation_detail->from = $from_date;
                        $obj_seller_vacation_detail->to = $to_date;

                        foreach (Language::getLanguages(true) as $language) {
                            $desc_lang_id = $language['id_lang'];
                            //ifdescription in other language is not available then fill with context language
                            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                                //if product name in other language is not available then fill with seller language same for others
                                if (!Tools::getValue('description_'.$language['id_lang'])) {
                                    $desc_lang_id = $default_lang;
                                }
                            } else {
                                //if multilang is OFF then all fields will be filled as default lang content
                                $desc_lang_id = $default_lang;
                            }

                            $obj_seller_vacation_detail->description[$language['id_lang']] = Tools::getValue('description_'.$desc_lang_id);
                        }

                        $obj_seller_vacation_detail->addtocart = $add_to_cart;
                        $obj_seller_vacation_detail->save();

                        Tools::redirect($this->context->link->getModuleLink('mpsellervacation', 'sellerVacationDetail', array('added' => 1)));
                    }
                }
            }
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->defineJSVars();
        $this->registerstylesheet('mpseller_vacation_css', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerstylesheet('mp_global_style.css', 'modules/marketplace/views/css/mp_global_style.css');
        $this->registerJavascript('mpseller_vacation_js', 'modules/'.$this->module->name.'/views/js/mpsvacationvalidation.js');
        $this->registerJavascript('mp-change_multilang', 'modules/marketplace/views/js/change_multilang.js');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Marketplace', array(), 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard')
        ];
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Seller Vacation Details', array(), 'Breadcrumb'),
            'url' => ''
        ];
        return $breadcrumb;
    }

    public function defineJSVars()
    {
        $jsVars = [
                'startdate_error' => $this->trans('From date is required.'),
                'enddate_error' => $this->trans('To date is required.'),
                'description_error' => $this->trans('Description is required in '),
                'description_error_other' => $this->trans('Description is required.'),
                'date_must_less' => $this->trans('From date must be less than from To date.'),
            ];
        Media::addJsDef($jsVars);
    }
}