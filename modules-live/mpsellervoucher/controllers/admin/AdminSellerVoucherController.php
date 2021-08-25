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

class AdminSellerVoucherController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'mp_cart_rule';
        $this->className = 'MpCartRule';
        $this->bootstrap = true;

        $this->identifier = 'id_mp_cart_rule';
        parent::__construct();

        $this->context = Context::getContext();

       $this->_join .= "INNER JOIN `"._DB_PREFIX_."mp_cart_rule_lang` mcrl ON (a.`id_mp_cart_rule`= mcrl.`id_mp_cart_rule` AND mcrl.`id_lang` = ".(int)$this->context->language->id.")
                        INNER JOIN `"._DB_PREFIX_."wk_mp_seller` msi ON (a.`id_seller` = msi.`id_seller`)";
        $this->_select .="mcrl.`name`, msi.`shop_name_unique`";

        $this->fields_list = array(
                'id_mp_cart_rule' => array(
                    'title' => $this->l('ID') ,
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                ),
                'id_ps_cart_rule' => array(
                    'title' => $this->l('Prestashop Cart Rule Id') ,
                    'align' => 'center',
                    'hint' => $this->l('Generated Prestashop Id in Cart Rules'),
                    'callback' => 'prestashopDisplayId',
                    'class' => 'fixed-width-xs',
                ),
                'shop_name_unique' => array(
                    'title' => $this->l('Unique Shop name') ,
                    'align' => 'center'
                ),
                'name' => array(
                    'title' => $this->l('Name') ,
                    'align' => 'center'
                ),
                'code' => array(
                    'title' => $this->l('Code') ,
                    'align' => 'center',
                ),
                'quantity' => array(
                    'title' => $this->l('Quantity') ,
                    'align' => 'center',
                ),
                'date_to' => array(
                    'title' => $this->l('Expiration date') ,
                    'align' => 'center',
                    'type' => 'datetime',
                ),
                'active' => array(
                    'title' =>  $this->l('Status'),
                    'active' => 'status',
                    'align' => 'center',
                    'type' => 'bool',
                ),
                'date_add' => array(
                    'title' => $this->l('Add Date') ,
                    'align' => 'center',
                    'type' => 'datetime',
                    'filter_key' => 'a!date_add'
                ),
            );


        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
            'enableSelection' => array(
                'text' => $this->l('Enable selection'),
                'icon' => 'icon-power-off text-success',
            ),
            'disableSelection' => array(
                'text' => $this->l('Disable selection'),
                'icon' => 'icon-power-off text-danger',
            ),
        );


        if (!$this->module->active)
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new voucher'),
        );
    }

    public function prestashopDisplayId($id_ps_cart_rule)
    {
        if ($id_ps_cart_rule) {
            return $id_ps_cart_rule;
        } else {
            return '-';
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
        // Generate countries list
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $countries = Country::getCountries($this->context->language->id, true, false, false);
        }
        $groups = Group::getGroups($this->context->language->id, true);
        $currencies = Currency::getCurrenciesByIdShop($this->context->shop->id);
        $obj_mp_cart_rule = new MpCartRule();

        if ($this->display == 'add') {
            $obj_mp_seller = new WkMpSeller();
            $seller_list = $obj_mp_seller->getAllSeller();
            if ($seller_list) {
                $this->context->smarty->assign('seller_list', $seller_list);

                //get first seller from the list
                $first_seller_details = $seller_list[0];
                $mp_id_seller = $first_seller_details['id_seller'];
            } else {
                $mp_id_seller = 0;
            }

            $this->context->smarty->assign(
                array(
                    'groups' => $groups,
                    'countries' => $countries,
                    'defaultDateFrom' => date('Y-m-d H:00:00'),
                    'defaultDateTo' => date('Y-m-d H:00:00', strtotime('+1 month')),
                )
            );
        } elseif ($this->display == 'edit') {
            $id_mp_cart_rule = Tools::getValue("id_mp_cart_rule");
            $obj_mp_cart_rule = new MpCartRule($id_mp_cart_rule);

            $mp_id_seller = $obj_mp_cart_rule->id_seller;
            $voucher_detail = $obj_mp_cart_rule->getVoucherDetailById($id_mp_cart_rule, Configuration::get('PS_LANG_DEFAULT'));
            if ($voucher_detail) {
            	// Code for Separating selected and unselected countries
                if (!$voucher_detail['country_restriction']) {
                    //$voucher_detail['countries']['selected'] = $countries;
                    $voucher_detail['countries']['unselected'] = array();
                }
                else {
                    if (isset($voucher_detail['countries'])) {
                        $selected_countries = $voucher_detail['countries'];
                        unset($voucher_detail['countries']);
                        foreach ($selected_countries as $id_country => $country_dtl) {
                            $selected_countries[$id_country] = $countries[$id_country];
                            unset($countries[$id_country]);
                        }
                        //$voucher_detail['countries']['selected'] = $selected_countries;
                        //$voucher_detail['countries']['unselected'] = $countries;
                    } else {
                        $voucher_detail['countries']['selected'] = array();
                        //$voucher_detail['countries']['unselected'] = $countries;
                    }
                }

                // Code for Separating selected and unselected groups
                if (!$voucher_detail['group_restriction']) {
                    //$voucher_detail['groups']['selected'] = $groups;
                    //$voucher_detail['groups']['unselected'] = array();
                }
                else {
                    if (isset($voucher_detail['groups'])) {
                        $selected_groups = $voucher_detail['groups'];
                        unset($voucher_detail['groups']);
                        foreach ($groups as $key => $group_dtl) {
                            if (isset($selected_groups[$group_dtl['id_group']])) {
                                $selected_groups[$group_dtl['id_group']] = $group_dtl;
                                unset($groups[$key]);
                            }
                        }
                        //$voucher_detail['groups']['selected'] = $selected_groups;
                        //$voucher_detail['groups']['unselected'] = $groups;
                    } else {
                        //$voucher_detail['groups']['selected'] = array();
                        //$voucher_detail['groups']['unselected'] = $groups;
                    }
                }
                $this->context->smarty->assign('voucher_detail', $voucher_detail);
            }
        }

        // NOTE : Only 'id_ps_product' > 0 :  seller products are listed because at the time of uploading data to ps table we need "id_ps_product" in "item" column of "ps_cart_rule_product_rule_value" table
        $sellerProducts = $obj_mp_cart_rule->getSellerProductByIdSeller($mp_id_seller, false, 1, Configuration::get('PS_LANG_DEFAULT'));

        // Set default lang at every form according to configuration multi-language
        WkMpHelper::assignDefaultLang($mp_id_seller);

        $this->context->smarty->assign(
            array(
                'currencies' => $currencies,
                'sellerProducts' => $sellerProducts,
                'PS_CURRENCY_DEFAULT' => Configuration::get('PS_CURRENCY_DEFAULT'),
                'MP_SELLER_CUSTOMER_VOUCHER_ALLOW' => Configuration::get('MP_SELLER_CUSTOMER_VOUCHER_ALLOW'),
            )
        );

        $this->fields_form = array(
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button',
            ),
        );

        return parent::renderForm();
    }

    public function processSave()
    {
        $mp_seller_id = Tools::getValue('id_seller');
        $obj_mp_sellerinfo = new WkMpSeller((int)$mp_seller_id);
        $seller_default_lang = $obj_mp_sellerinfo->default_lang;

        // Get Default Lang
        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
            $default_lang = $seller_default_lang;
        } else {
            if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') { // Admin default lang
                $default_lang = Configuration::get('PS_LANG_DEFAULT');
            } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') { // Seller default lang
                $default_lang = $seller_default_lang;
            }
        }

        $languages = Language::getLanguages(false);
        $className = 'MpCartRule';
        $rules = call_user_func(array($className, 'getValidationRules'), $className);

        /*==== Validations ====*/
        if (!Tools::getValue('name_'.$default_lang)) {
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $seller_lang = Language::getLanguage((int) $default_lang);
                $this->errors[] = Tools::displayError($this->l('Voucher name is required in ').$seller_lang['name']);
            } else {
                $this->errors[] = Tools::displayError($this->l('Voucher name is required'));
            }
        }
        else {
            foreach ($languages as $language) {
                $value = Tools::getValue('name_'.$language['id_lang']);
                if ($value && Tools::strlen($value) > $rules['sizeLang']['name']) {
                    $this->errors[] = sprintf(Tools::displayError('Voucher Name field is too long (%2$d chars max).'), $rules['sizeLang']['name']);
                }
                if (!Validate::isCleanHtml(Tools::getValue('name_'.$language['id_lang']))) {
                    $lang = Language::getLanguage((int)$language['id_lang']);
                    $this->errors[] = Tools::displayError($this->l('Voucher name is not valid in ').$lang['name']);
                }
            }

            if (Tools::getValue('for_customer')) {
                if (!Validate::isUnsignedId(Tools::getValue('for_customer'))) {
                    $this->errors[] = Tools::displayError($this->l('Please select a valid customer.'));
                }
            }

            if (!Tools::getValue('date_from') || !Tools::getValue('date_to')) {
                $this->errors[] = Tools::displayError($this->l('please enter valid date range.'));
            } else {
                if (strtotime(Tools::getValue('date_from')) > strtotime(Tools::getValue('date_to'))) {
                    $this->errors[] = Tools::displayError($this->l('The voucher cannot end before it begins.'));
                }
            }

            if (!Tools::getValue('reduction_type')) {
                $this->errors[] = Tools::displayError($this->l('Please select reduction type.'));
            } else {
                if (!Validate::isInt(Tools::getValue('reduction_type'))) {
                    $this->errors[] = Tools::displayError($this->l('Some thing went wrong, please try again.'));
                }
                else {
                    if (Tools::getValue('reduction_type') == 1) {
                        if (!Validate::isPercentage(Tools::getValue('reduction_percent'))) {
                            $this->errors[] = Tools::displayError($this->l('Percentage value is not valid.'));
                        }
                    }
                    elseif (Tools::getValue('reduction_type') == 2) {
                        if (!Validate::isFloat(Tools::getValue('reduction_amount'))) {
                            $this->errors[] = Tools::displayError($this->l('Amount is not valid.'));
                        }
                    }
                }
            }

            if (!Tools::getValue('reduction_for')) {
                $this->errors[] = Tools::displayError($this->l('Please select a value in which discount will be applied.'));
            } else {
                if (!Validate::isInt(Tools::getValue('reduction_for'))) {
                    $this->errors[] = Tools::displayError($this->l('Some thing went wrong, please try again.'));
                } else {
                    if (Tools::getValue('reduction_for') == 1) {
                        if (!Tools::getValue('mp_reduction_product')) {
                            $this->errors[] = Tools::displayError($this->l('Please select a product for which you are creating voucher.'));
                        } else {
                            if (!Validate::isInt(Tools::getValue('mp_reduction_product'))) {
                                $this->errors[] = Tools::displayError($this->l('Please select a valid product.'));
                            }
                            else {
                                $mp_id_prod = Tools::getValue('mp_reduction_product');
                                $obj_mp_product = new WkMpSellerProduct($mp_id_prod);
                                $id_seller = $obj_mp_product->id_seller;

                                if ($id_seller != $mp_seller_id) {
                                    $this->errors[] = Tools::displayError($this->l('Please select your product from dropdown.'));
                                }
                            }
                        }
                    } elseif (Tools::getValue('reduction_for') == 2) {
                        if (Tools::getValue('reduction_type') == 2) {
                            $this->errors[] = Tools::displayError($this->l('Something went wrong, please try again later.'));
                        } else {
                            if (!Tools::getValue('multiple_reduction_product')) {
                                $this->errors[] = Tools::displayError($this->l('Select products in which voucher will be applied.'));
                            } else {
                                $reductionProducts = Tools::getValue('multiple_reduction_product');
                                foreach ($reductionProducts as $mpIdProduct) {
                                    if (!Validate::isInt($mpIdProduct)) {
                                        $this->errors[] = Tools::displayError($this->l('Please select a valid product.'));
                                    } else {
                                        $obj_mp_product = new WkMpSellerProduct($mpIdProduct);
                                        $id_seller = $obj_mp_product->id_seller;

                                        if ($id_seller != $mp_seller_id) {
                                            $this->errors[] = Tools::displayError($this->l('Something went wrong in selected products, please try again later.'));
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        /*==== Validations ====*/

        if (!count($this->errors)) {
            if (Tools::getValue("id_mp_cart_rule")) {
                $id_mp_cart_rule = Tools::getValue("id_mp_cart_rule");
                $obj_mp_cart_rule = new MpCartRule((int)$id_mp_cart_rule);
            }
            else
                $obj_mp_cart_rule = new MpCartRule();

            $obj_mp_prod_rule = new MpCartRuleProductRule();
            $obj_mp_prod_rule_grp = new MpCartRuleProductRuleGroup();

            $_POST['product_restriction'] = 1;

            foreach ($languages as $language) {
                $voucher_lang_id = $language['id_lang'];
                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    if (!Tools::getValue('name_'.$voucher_lang_id)) {
                        $voucher_lang_id = $default_lang;
                    }
                } else {
                    $voucher_lang_id = $default_lang;
                }

                $obj_mp_cart_rule->name[$language['id_lang']] = Tools::getValue('name_'.$voucher_lang_id);
            }

            $_POST['admin_approval'] = Tools::getValue('active');

            if (Tools::getValue("id_mp_cart_rule")) { // For Update
                $prod_upd = true;
                $old_id_mp_prod = $obj_mp_cart_rule->mp_reduction_product;
                if (Tools::getValue('reduction_for') == 1) {
                    $new_id_mp_prod = Tools::getValue("mp_reduction_product");
                    if ($new_id_mp_prod == $old_id_mp_prod) {
                        $prod_upd = false;
                    }
                }
            }

            $this->errors = $obj_mp_cart_rule->validateController();
            $obj_mp_cart_rule->description = Tools::getValue('description');
            $obj_mp_cart_rule->code = Tools::getValue('code');
            $obj_mp_cart_rule->priority = Tools::getValue('priority');
            $obj_mp_cart_rule->for_customer = trim(Tools::getValue('for_customer'));
            $obj_mp_cart_rule->quantity = Tools::getValue('quantity');
            $obj_mp_cart_rule->quantity_per_user = Tools::getValue('quantity_per_user');
            $obj_mp_cart_rule->country_restriction = Tools::getValue('country_restriction');
            $obj_mp_cart_rule->group_restriction = Tools::getValue('group_restriction');
            $obj_mp_cart_rule->cart_rule_restriction = Tools::getValue('cart_rule_restriction');
            $obj_mp_cart_rule->product_restriction = Tools::getValue('product_restriction');
            $obj_mp_cart_rule->active = Tools::getValue('active');
            $obj_mp_cart_rule->admin_approval = Tools::getValue('admin_approval');

            if (Tools::getValue('reduction_type') == 1)
                $obj_mp_cart_rule->reduction_amount = 0;
            elseif (Tools::getValue('reduction_type') == 2)
                $obj_mp_cart_rule->reduction_percent = 0;

            if (Tools::getValue('reduction_for') == 1) {
                $obj_mp_cart_rule->mp_reduction_product = Tools::getValue('mp_reduction_product');
            } elseif (Tools::getValue('reduction_for') == 2) {
                $obj_mp_cart_rule->mp_reduction_product = -2;
            }

            if (!count($this->errors)) {
                if ($obj_mp_cart_rule->save()) {
                    $id_mp_cart_rule = $obj_mp_cart_rule->id;

                    // Insert Into "mp_cart_rule_country" and "mp_cart_rule_group" tables
                    if ($obj_mp_cart_rule->country_restriction || $obj_mp_cart_rule->group_restriction) {
                        $country_select = Tools::getValue('country_select');
                        $group_select = Tools::getValue('group_select');

                        $upd_data = Tools::getValue("id_mp_cart_rule") ? 1 : 0 ;

                        if ($obj_mp_cart_rule->country_restriction || $obj_mp_cart_rule->group_restriction)
                            $obj_mp_cart_rule->insertIntoMpCartRuleCountryGroupTables($country_select, $group_select, false, $upd_data);
                    }

                    $mpVoucherItems = false;
                    if (Tools::getValue('reduction_for') == 1) {
                        $mpVoucherItems = $obj_mp_cart_rule->mp_reduction_product;
                    } elseif (Tools::getValue('reduction_for') == 2) {
                        $mpVoucherItems = Tools::getValue('multiple_reduction_product');
                    }

                    // NOTE : @TODO if more options are added instead of only specific product then for differentiation use mp_reduction_product like it is done in prestashop with reduction_product variable
                    if (Tools::getValue("id_mp_cart_rule")) { // For update
                        if ($prod_upd)
                            $obj_mp_prod_rule_grp->updateMpIdProdByIdMpCartRule($id_mp_cart_rule, $mpVoucherItems);
                    }
                    else {
                        if ($obj_mp_cart_rule->product_restriction > 0) {  // for specific product
                            $obj_mp_prod_rule_grp->id_mp_cart_rule = $id_mp_cart_rule;
                            $obj_mp_prod_rule_grp->quantity = 1;
                            $obj_mp_prod_rule_grp->save();

                            $id_mp_product_rule_group = $obj_mp_prod_rule_grp->id;

                            $obj_mp_prod_rule->id_mp_product_rule_group = $id_mp_product_rule_group;
                            $obj_mp_prod_rule->type = 'products';
                            $obj_mp_prod_rule->save();

                            $id_mp_product_rule = $obj_mp_prod_rule->id;

                            $obj_mp_prod_rule->insertIntoMpCartRuleProductRuleValue($id_mp_product_rule, $mpVoucherItems);
                        }
                    }

                    if (Tools::getValue("id_mp_cart_rule")) { // For update
                        if ($obj_mp_cart_rule->admin_approval) {
                            if ($obj_mp_cart_rule->id_ps_cart_rule) {
                                $ps_id_cart_rule = $obj_mp_cart_rule->insertUpdateDataIntoPsTables(false, 1);
                            } else {
                                $ps_id_cart_rule = $obj_mp_cart_rule->insertUpdateDataIntoPsTables();
                            }
                        } else {
                            if ($obj_mp_cart_rule->id_ps_cart_rule) {
                                $cart_rule = new CartRule($obj_mp_cart_rule->id_ps_cart_rule);
                                $cart_rule->active = 0;
                                $cart_rule->save();
                            }
                        }
                    } else {
                        if ($obj_mp_cart_rule->admin_approval) {
                            $ps_id_cart_rule = $obj_mp_cart_rule->insertUpdateDataIntoPsTables();
                        }
                    }

                    if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                        if (Tools::getValue("id_mp_cart_rule")) {
                            Tools::redirectAdmin(self::$currentIndex.'&id_mp_cart_rule='.(int)$id_mp_cart_rule.'&update'.$this->table.'&conf=4&token='.$this->token);
                        } else {
                            Tools::redirectAdmin(self::$currentIndex.'&id_mp_cart_rule='.(int)$id_mp_cart_rule.'&update'.$this->table.'&conf=3&token='.$this->token);
                        }
                    } else {
                        if (Tools::getValue("id_mp_cart_rule")) {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                        } else {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                        }
                    }
                }
            }
            else {
                if (Tools::getValue("id_mp_cart_rule"))
                    $this->display = 'edit';
                else
                    $this->display = 'add';
            }
        }
        else {
            if (Tools::getValue("id_mp_cart_rule"))
                $this->display = 'edit';
            else
                $this->display = 'add';
        }
    }

    public function ajaxProcessCustomerSearch()
    {
        $customer_word = Tools::getValue('word');
        $id_seller = Tools::getValue('id_seller');
        $obj_mp_seller = new WkMpSeller((int)$id_seller);
        $seller_customer_id = $obj_mp_seller->seller_customer_id;

        if ($customer_word) {
            if (Configuration::get('MP_SELLER_CUSTOMER_VOUCHER_ALLOW')) {
                $obj_mp_cart_rule = new MpCartRule();
                if (Configuration::get('MP_VOUCHER_CUSTOMER_TYPE') == 1)
                    $customers = $obj_mp_cart_rule->getSellerProductOrderedCustomers($seller_customer_id, $customer_word);
                elseif (Configuration::get('MP_VOUCHER_CUSTOMER_TYPE') == 2)
                    $customers = $obj_mp_cart_rule->getPsCustomers($customer_word);

                die(Tools::jsonEncode($customers));
            }
            else
                die(false);
        }
        else
            die(false);
    }

    public function ajaxProcessProductSearch()
    {
        $prod_word = Tools::getValue('word');
        $id_seller = Tools::getValue('id_seller');
        $obj_mp_cart_rule = new MpCartRule();

        if ($prod_word) {
            $mp_product = $obj_mp_cart_rule->getSellerProductByIdSeller($id_seller, $prod_word, 1);
            die(Tools::jsonEncode($mp_product));
        }
        else
            die(false);
    }

    public function ajaxProcessGetSellerLang()
    {
        $id_seller = Tools::getValue('id_seller');
        $obj_mp_seller = new WkMpSeller((int)$id_seller);
        $obj_mp_cart_rule = new MpCartRule();
        if ($obj_mp_seller->id) {
            $seller_lang = Language::getLanguage((int)$obj_mp_seller->default_lang);
            $sellerProducts = $obj_mp_cart_rule->getSellerProductByIdSeller($id_seller, false, 1, Configuration::get('PS_LANG_DEFAULT'));
            $response = array('seller_lang' => $seller_lang, 'products' => $sellerProducts);
            die(Tools::jsonEncode($response)); //close ajax
        } else {
            die(false);
        }
    }

    public function processDelete()
    {
        if (Tools::isSubmit("deletemp_cart_rule")) {
            $id_mp_cart_rule = Tools::getValue('id_mp_cart_rule');
            $obj_mp_cart_rule = new MpCartRule();
            $obj_mp_cart_rule->deleteVoucher($id_mp_cart_rule);
            Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='. $this->token);
        }
    }

    protected function processBulkDelete()
    {
        if ($this->tabAccess['delete'] === '1') {
            $success = 1;
            if (is_array($this->boxes) && !empty($this->boxes)) {
                $deleteData = Tools::getValue($this->table.'Box');
                $obj_mp_cart_rule = new MpCartRule();
                $obj_mp_cart_rule->deleteVoucher($deleteData);
                Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='. $this->token);
            }
            else
                $this->errors[] = Tools::displayError('You must select at least one element to delete.');
        }
        else
            $this->errors[] = Tools::displayError('You do not have permission to delete this.');
    }

    public function processStatus()
    {
        if (!($obj = $this->loadObject(true)))
            return;

        if (Tools::isSubmit('statusmp_cart_rule')) {
            $id_mp_cart_rule = Tools::getValue('id_mp_cart_rule');
            $obj_mp_cart_rule = new MpCartRule();
            $obj_mp_cart_rule->changeStatus($id_mp_cart_rule);
            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=5');
        }
    }

    protected function processBulkEnableSelection()
    {
        return $this->processBulkStatusSelection(1);
    }

    protected function processBulkDisableSelection()
    {
        return $this->processBulkStatusSelection(0);
    }

    protected function processBulkStatusSelection($status)
    {
        $obj_mp_cart_rule = new MpCartRule();
        if (is_array($this->boxes) && !empty($this->boxes)) {
            return $obj_mp_cart_rule->changeStatus($this->boxes, $status);
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_MODULE_DIR_.'mpsellervoucher/views/js/sellerVoucherAdmin.js');
        $this->addCSS(_MODULE_DIR_.'mpsellervoucher/views/css/sellerVoucherAdmin.css');
    }
}