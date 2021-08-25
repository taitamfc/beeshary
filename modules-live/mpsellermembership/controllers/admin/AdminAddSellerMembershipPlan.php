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

class AdminAddSellerMembershipPlanController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'wk_mp_seller_plan';
        $this->className = 'MarketplaceSellerplan';
        $this->context = Context::getContext();
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->list_no_link = true;
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_plan_lang` mspl ON (mspl.`id` = a.`id`)';
        $this->_select = 'mspl.`plan_name`';
        $this->_where = 'AND mspl.`id_lang` = '.(int) $this->context->language->id;
        $this->identifier = 'id';
        parent::__construct();

        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('Id') ,
                'align' => 'center',
            ),
            'id_product' => array(
                'title' => $this->l('Prestashop Id Product') ,
                'align' => 'center',
            ),
            'plan_name' => array(
                'title' => $this->l('Plan Name') ,
                'align' => 'center',
            ),
            'plan_price' => array(
                'title' => $this->l('Plan Price') ,
                'align' => 'center',
                'type' => 'price',
            ),
            'plan_duration' => array(
                'title' => $this->l('Plan Duration') ,
                'align' => 'center',
                'callback' => 'planDurationAppendText',
            ),
            'num_products_allow' => array(
                'title' => $this->l('Number Of Product') ,
                'align' => 'center',
            ),
            'sequence_number' => array(
                'title' => $this->l('Sequence Number') ,
                'align' => 'center',
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'type' => 'bool',
                'active' => 'status',
                'orderby' => false,
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
        );
    }

    public function planDurationAppendText($value)
    {
        return $value.$this->l(' Days');
    }

    public function renderForm()
    {
        if ($this->display == 'edit') {
            $plan_id = Tools::getValue('id');
            if ($plan_id) {
                $image = file_exists(_PS_MODULE_DIR_.'mpsellermembership/views/img/'.$plan_id.'.jpg') ? $plan_id.'.jpg' : 'default-plan.png';
                $image = __PS_BASE_URI__.'modules/mpsellermembership/views/img/'.$image;
                $this->context->smarty->assign('plan_logo', $image);

                $obj_mp_seller_plan = new MarketplaceSellerplan();
                $plan_info = $obj_mp_seller_plan->getPlanInfoById($plan_id);
                if ($plan_info) {
                    $plan_name_arr = array();
                    $plan_langinfo = $obj_mp_seller_plan->getPlanLangInfoById($plan_id);
                    if ($plan_langinfo) {
                        foreach ($plan_langinfo as $plan) {
                            $plan_name_arr[$plan['id_lang']] = $plan['plan_name'];
                        }
                    }
                    $plan_info['plan_name'] = $plan_name_arr;
                }
                $this->context->smarty->assign('plan_info', $plan_info);
            }
        }

        $this->context->smarty->assign(
            array(
                'countryinfo' => Country::getCountries($this->context->language->id),
                'path_css' => _THEME_CSS_DIR_,
                'ad' => __PS_BASE_URI__.basename(_PS_ADMIN_DIR_),
                'autoload_rte' => true,
                'lang' => true,
                'iso' => $this->context->language->iso_code,
                'mp_module_dir' => _MODULE_DIR_,
                'ps_module_dir' => _PS_MODULE_DIR_,
                'ps_img_dir' => _PS_IMG_.'l/',
                'self' => dirname(__FILE__),
                'allow_multilang' => 1,
                'languages' => Language::getLanguages(),
                'total_languages' => count(Language::getLanguages()),
                'current_lang' => Language::getLanguage((int) $this->context->language->id),
                'multi_lang' => Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE'),
                'multi_def_lang_off' => Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG'),
            )
        );

        WkMpHelper::defineGlobalJSVariables(); // Define global js variable on js file

        $this->fields_form = array(
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            );

        return parent::renderForm();
    }

    public function initToolBar()
    {
        parent::initToolBar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new plan'),
        );
    }

    public function processSave()
    {
        $plan_id = Tools::getValue('id'); // edit
        $plan_price = Tools::getValue('plan_price');
        $plan_duration = Tools::getValue('plan_duration');
        $plan_duration_type = Tools::getValue('plan_duration_type');
        $num_products_allow = Tools::getValue('num_products_allow');
        $sequence_number = Tools::getValue('sequence_number');
        $default_lang = Configuration::get('PS_LANG_DEFAULT');
        $plan_active = Tools::getValue('plan_active');

        // data validation
        if (Tools::getValue('plan_name_'.$default_lang)) {
            $languages = Language::getLanguages();
            $plan_name_error = 0;
            foreach ($languages as $language) {
                if (!Validate::isCatalogName(Tools::getValue('plan_name_'.$language['id_lang']))) {
                    $plan_name_error = 1;
                }
            }
            //Product Name Validate
            if ($plan_name_error) {
                $this->errors[] = $this->module->l('Plan name must not have Invalid characters').' <>;=#{}';
            }
        } else {
            $default_lang_arr = Language::getLanguage((int) $default_lang);
            $this->errors[] = $this->module->l('Plan name is required in '.$default_lang_arr['name']);
        }

        if ($plan_price == '') {
            $this->errors[] = $this->l('Plan price is required field.');
        } elseif (!Validate::isPrice($plan_price)) {
            $this->errors[] = $this->l('Plan price is invalid.');
        }

        if ($plan_duration == '') {
            $this->errors[] = $this->l('Plan duration is required field.');
        } elseif ($plan_duration == 0) {
            $this->errors[] = $this->l('Plan duration must be greater than 0.');
        } elseif (!Validate::isUnsignedInt($plan_duration)) {
            $this->errors[] = $this->l('Plan duration is invalid(duration must be numeric and greater than 0).');
        }

        if ($num_products_allow == '') {
            $this->errors[] = $this->l('Number of products is required field.');
        } elseif ($num_products_allow == 0) {
            $this->errors[] = $this->l('Number of products must be greater than 0.');
        } elseif (!Validate::isUnsignedInt($num_products_allow)) {
            $this->errors[] = $this->l('Number of products is invalid(products must be numeric and greater than 0).');
        }

        if ($sequence_number == '') {
            $this->errors[] = $this->l('Sequence Number is required field.');
        } elseif ($sequence_number == 0) {
            $this->errors[] = $this->l('Sequence Number must be greater than 0.');
        } elseif (!Validate::isUnsignedInt($sequence_number)) {
            $this->errors[] = $this->l('Sequence Number is invalid(Sequence Number must be numeric and greater than 0).');
        }

        if (!empty($_FILES['plan_logo']['name'])) {
            if ($_FILES['plan_logo']['size'] > 0) {
                if ($_FILES['plan_logo']['tmp_name'] != '') {
                    if (!ImageManager::isCorrectImageFileExt($_FILES['plan_logo']['name'])) {
                        $this->errors[] = $this->l('Invalid image extensions, only jpg, jpeg and png are allowed.');
                    }
                }
            } else {
                $this->errors[] = $this->l('Invalid image size.');
            }
        }

        if (!$plan_id) {
            $obj_mp_seller_plan = new MarketplaceSellerplan();
            $is_plan_exist = $obj_mp_seller_plan->getPlanDetailsByPriceAndDurationAndAllowProducts($plan_price, $plan_duration, $num_products_allow);
            if ($is_plan_exist) {
                $this->errors[] = $this->l('Plan for this combination already exist(plan price, plan duration and number of products allow).');
            }
        }

        if (!count($this->errors)) {
            $plan_duration *= $plan_duration_type;
            $quantity = (int) 50000; // quantity define 500 becaouse we are define product as virtual so quantity don't matter
            if ($plan_id) {
                //edit plan
                $obj_mp_seller_plan = new MarketplaceSellerplan($plan_id);
                $id_product = $obj_mp_seller_plan->id_product;
                if ($id_product) {
                    $obj_product = new Product($id_product);
                    $obj_product->name = array();
                    $obj_product->description = array();
                    $obj_product->description_short = array();
                    $obj_product->link_rewrite = array();
                    $obj_product->price = $plan_price;
                    $obj_product->quantity = $quantity;

                    foreach (Language::getLanguages(true) as $language) {
                        $plan_lang_id = $language['id_lang'];
                        if (!Tools::getValue('plan_name_'.$plan_lang_id)) {
                            $plan_lang_id = $default_lang;
                        }

                        $obj_product->name[$language['id_lang']] = Tools::getValue('plan_name_'.$plan_lang_id);
                        $obj_product->link_rewrite[$language['id_lang']] = Tools::link_rewrite(Tools::getValue('plan_name_'.$plan_lang_id));
                        $obj_product->description_short[$language['id_lang']] = $this->l('Plan - ').Tools::getValue('plan_name_'.$plan_lang_id).$this->l(', Price - ').$plan_price.$this->l(', No. of products allowed - ').$num_products_allow.$this->l(', Valid - ').$plan_duration.$this->l(' days');
                        $obj_product->description[$language['id_lang']] = $this->l('This product is for seller only');

                        $obj_mp_seller_plan->plan_name[$language['id_lang']] = Tools::getValue('plan_name_'.$plan_lang_id);
                    }

                    $obj_product->save();
                    $ps_product_id = $obj_product->id;
                    Search::indexation(Tools::link_rewrite(Tools::getValue('plan_name_'.$default_lang)));

                    $obj_mp_seller_plan->id_product = $ps_product_id;
                    $obj_mp_seller_plan->plan_price = $plan_price;
                    $obj_mp_seller_plan->plan_duration = $plan_duration;
                    $obj_mp_seller_plan->num_products_allow = $num_products_allow;
                    $obj_mp_seller_plan->sequence_number = $sequence_number;
                    $obj_mp_seller_plan->save();
                }

                MarketplaceSellerplan::uploadMpImage($_FILES['plan_logo'], $plan_id);
                if (!empty($_FILES['plan_logo']['name']) && $_FILES['plan_logo']['size'] > 0 && $id_product) {
                    $logo_src = $_FILES['plan_logo']['tmp_name'];
                    MarketplaceSellerplan::uploadPsImage($logo_src, $id_product, $this->context->shop->id);
                }

                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            } else {
                //add new plan
                $category_id = (int) Configuration::get('WK_MP_SELLER_CATEGORY');
                $obj_product = new Product();
                $obj_product->name = array();
                $obj_product->description = array();
                $obj_product->description_short = array();
                $obj_product->link_rewrite = array();

                foreach (Language::getLanguages(true) as $language) {
                    $plan_lang_id = $language['id_lang'];
                    if (!Tools::getValue('plan_name_'.$plan_lang_id)) {
                        $plan_lang_id = $default_lang;
                    }

                    $obj_product->name[$language['id_lang']] = Tools::getValue('plan_name_'.$plan_lang_id);
                    $obj_product->link_rewrite[$language['id_lang']] = Tools::link_rewrite(Tools::getValue('plan_name_'.$plan_lang_id));
                    $obj_product->description_short[$language['id_lang']] = $this->l('Plan - ').Tools::getValue('plan_name_'.$plan_lang_id).$this->l(', Price - ').$plan_price.$this->l(', No. of products allowed - ').$num_products_allow.$this->l(', Valid - ').$plan_duration.$this->l(' days');
                    $obj_product->description[$language['id_lang']] = $this->l('This product is for seller only');
                }
                $obj_product->quantity = $quantity;
                $obj_product->id_category_default = $category_id;
                $obj_product->price = $plan_price;
                $obj_product->active = $plan_active;
                $obj_product->indexed = 1;
                $obj_product->condition = 'new';
                $obj_product->visibility = 'none';
                $obj_product->is_virtual = 1;
                $obj_product->id_tax_rules_group = 0;
                $obj_product->save();
                $ps_product_id = $obj_product->id;
                Search::indexation(Tools::link_rewrite(Tools::getValue('plan_name_'.$default_lang)));
                if ($ps_product_id > 0) {
                    if ($category_id > 0) {
                        $obj_product->addToCategories(array($category_id));
                    }

                    if ($quantity > 0) {
                        StockAvailable::updateQuantity($ps_product_id, null, $quantity);
                    }
                }

                $obj_mp_seller_plan = new MarketplaceSellerplan();
                $obj_mp_seller_plan->id_product = $ps_product_id;

                foreach (Language::getLanguages(true) as $language) {
                    $plan_lang_id = $language['id_lang'];
                    if (!Tools::getValue('plan_name_'.$plan_lang_id)) {
                        $plan_lang_id = $default_lang;
                    }

                    $obj_mp_seller_plan->plan_name[$language['id_lang']] = Tools::getValue('plan_name_'.$plan_lang_id);
                }
                $obj_mp_seller_plan->plan_price = $plan_price;
                $obj_mp_seller_plan->plan_duration = $plan_duration;
                $obj_mp_seller_plan->num_products_allow = $num_products_allow;
                $obj_mp_seller_plan->sequence_number = $sequence_number;
                $obj_mp_seller_plan->active = $plan_active;
                $obj_mp_seller_plan->save();

                MarketplaceSellerplan::uploadMpImage($_FILES['plan_logo'], $obj_mp_seller_plan->id);
                if (!empty($_FILES['plan_logo']['name']) && $_FILES['plan_logo']['size'] > 0) {
                    $logo_src = $_FILES['plan_logo']['tmp_name'];
                } else {
                    $logo_src = _PS_MODULE_DIR_.'mpsellermembership/views/img/default-plan.png';
                }

                MarketplaceSellerplan::uploadPsImage($logo_src, $ps_product_id, $this->context->shop->id);

                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        } else {
            if ($plan_id) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
    }

    protected function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                $to_delete = new $this->className($id);
                $obj_product = new Product($to_delete->id_product);
                $obj_product->delete();
            }
        }
        parent::processBulkDelete();
    }

    protected function processBulkEnableSelection()
    {
        $this->bulkStatusAction(1);

        return parent::processBulkEnableSelection();
    }

    protected function processBulkDisableSelection()
    {
        $this->bulkStatusAction(0);

        return parent::processBulkDisableSelection();
    }

    protected function bulkStatusAction($status)
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                $object = new $this->className((int) $id);
                $obj_product = new Product($object->id_product);
                $obj_product->active = (int) $status;
                $obj_product->save();
            }
        }
    }
}
