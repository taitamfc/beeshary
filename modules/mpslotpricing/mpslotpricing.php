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

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__).'/../marketplace/classes/WkMpRequiredClasses.php';
include_once 'classes/MpPriceSlots.php';

class mpslotpricing extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';

    public function __construct()
    {
        $this->name = 'mpslotpricing';
        $this->version = '5.0.0';
        $this->author = 'Webkul';
        $this->tab = 'front_office_features';
        $this->need_instance = 1;
        parent::__construct();
        $this->displayName = $this->l('Marketplace Slot Pricing');
        $this->description = $this->l('Allows seller to set different price slots for his products.');
    }

    public function hookActionMpProductDelete($params)
    {
        $mp_id_product = $params['id_mp_product'];
        $obj_mp_slot_price = new MpPriceSlots();
        $all_slot = $obj_mp_slot_price->getAllProductSlots($mp_id_product);
        if (!empty($all_slot)) {
            foreach ($all_slot as $value) {
                $id_specific_price = $value['id_specific_price'];
                if ($id_specific_price) {
                    $obj_specific_price = new SpecificPrice($id_specific_price);
                    $obj_specific_price->delete();
                }
                $obj_mp_slot_price = new MpPriceSlots($value['id']);
                $obj_mp_slot_price->delete();
            }
        }
    }
    public function hookActionFrontControllerSetMedia($params)
    {
        $controller = Tools::getValue('controller');
        if ('addproduct' === $controller || 'updateproduct' === $controller) {
            $this->assignJSVaribales();
            $this->context->controller->registerStylesheet(
                'timepicker-css',
                'modules/marketplace/views/js/jquerydatepicker/jquery-ui-timepicker-addon.css'
            );

            if (Configuration::get('WK_MP_ALLOW_CUSTOM_CSS')) {
                $this->context->controller->registerStylesheet('mp-custom_style-css', 'modules/marketplace/views/css/mp_custom_style.css');
            }

            $this->context->controller->registerStylesheet(
                'slotpricing-style',
                'modules/mpslotpricing/views/css/slotpricing.css'
            );

            $this->context->controller->registerJavascript(
                'timepicker',
                'modules/marketplace/views/js/jquerydatepicker/jquery-ui-timepicker-addon.js',
                ['position' => 'bottom', 'priority' => 1000]

            );

            $this->context->controller->registerJavascript(
                'module-mpslotprice-js',
                'modules/'.$this->name.'/views/js/slotpricing.js',
                ['position' => 'bottom', 'priority' => 1000]
            );
        }
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        $controller = Tools::getValue('controller');
        if ('AdminSellerProductDetail' === $controller) {
            $this->assignJSVaribales();
            $this->context->controller->addJS(_MODULE_DIR_.$this->name.'/views/js/slotpricing.js');
            $this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/views/css/slotpricing.css');
        }
    }

    public function assignJSVaribales()
    {
        Media::addJsDef(array(
            'modules_dir' => _MODULE_DIR_,
            'Choose' => $this->l('Choose'),
            'invalid_qty' => $this->l('Invalid quantity'),
            'wrong_id' => $this->l('Something went wrong'),
            'invalid_range' => $this->l('Invalid date range'),
            'success' => $this->l('Price Slot added successfully'),
            'sp_quantity_empty' => $this->l('Please set quantity.'),
            'no_customers_found' => $this->l('No customers found.'),
            'date_invalid' => $this->l('The from/to date is invalid'),
            'invalid_price' => $this->l('Invalid price/discount amount'),
            'sp_reduction_err' => $this->l('Please set reduction amount.'),
            'delete_successs' => $this->l('Slot price delete successfully'),
            'no_reduction' => $this->l('No reduction value has been submitted'),
            'reduction_range' => $this->l('Submitted reduction value (0-100) is out-of-range'),
            'already_exist' => $this->l('A specific price already exists for these parameters'),
            'select_dis_type' => $this->l('Please select a discount type (amount or percentage)'),
            'delete_err' => $this->l('An error occurred while attempting to delete the specific price'),
            'conf_delete' => $this->l('This will delete the specific price. Do you wish to proceed?'),
        ));
    }

    /**
     * [hookDisplayMpAddProductFooter -> display add slot price button below marketplace add product].
     *
     * @return [type] [description]
     */
    public function hookDisplayMpAddProductFooter()
    {
        $currencies = Currency::getCurrencies();
        $countries = Country::getCountries($this->context->language->id);
        $groups = Group::getGroups($this->context->language->id);
        $this->context->smarty->assign(array(
            'controller' => Tools::getValue('controller'),
            'currencies' => $currencies,
            'currency' => new Currency(Configuration::get('PS_CURRENCY_DEFAULT')),
            'countries' => $countries,
            'groups' => $groups,
            'multi_shop' => Shop::isFeatureActive(),
            'link' => new Link(),
            'pack' => new Pack(),
            'country_display_tax_label' => $this->context->country->display_tax_label,
            'modules_dir' => _MODULE_DIR_,
        ));
        if ('addproduct' == Tools::getValue('controller')) {
            return $this->fetch('module:mpslotpricing/views/templates/hook/addslots.tpl');
        } elseif ('AdminSellerProductDetail' == Tools::getValue('controller')) {
            return $this->display(__FILE__, 'addslots.tpl');
        }
    }

    public function hookActionAfterAddMPProduct($params)
    {
        $mp_id_product = $params['id_mp_product'];
        if ($mp_id_product && Tools::getValue('showTpl')) {
            $leave_bprice = 0;
            if (1 == Tools::getValue('leave_bprice')) {
                $leave_bprice = Tools::getValue('leave_bprice');
            }
            $sp_price = -1;
            if (Tools::getValue('sp_price')) {
                $sp_price = Tools::getValue('sp_price');
            }
            $price = $leave_bprice ? '-1' : $sp_price;
            $from_quantity = Tools::getValue('sp_from_quantity');
            $reduction = (float) Tools::getValue('sp_reduction');
            $reduction_tax = Tools::getValue('sp_reduction_tax');
            $specificPrice = Tools::getValue('sp_price');
            $reduction_type = !$reduction ? 'amount' : Tools::getValue('sp_reduction_type');
            $reduction_type = $reduction_type == '-' ? 'amount' : $reduction_type;
            $from = Tools::getValue('sp_from');
            if (!$from) {
                $from = '0000-00-00 00:00:00';
            }
            $to = Tools::getValue('sp_to');
            if (!$to) {
                $to = '0000-00-00 00:00:00';
            }
            $id_shop = 1;       // hardcoded id_shop = 1
            $id_product_attribute = 0;  // hardcoded id_product_attribute = 0
            $id_currency = Tools::getValue('sp_id_currency');
            $id_country = Tools::getValue('sp_id_country');
            $id_group = Tools::getValue('sp_id_group');
            $id_customer = Tools::getValue('sp_id_customer');

            $product_detail = WkMpSellerProduct::getSellerProductByIdProduct($mp_id_product);
            $objMpPriceSlots = new MpPriceSlots();
            $id_specific_price = 0;
            // product is created in prestashop
            if ($product_detail && $product_detail['id_ps_product']) {
                //adding specific price to prestashop
                $id_specific_price = $objMpPriceSlots->addSpecificProductPriceToPs($product_detail['id_ps_product'], $id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_tax, $reduction_type, $from, $to, $id_product_attribute);
            }
            // saving record to mp slot price table
            $objMpPriceSlots->addSpecificProductPriceToMp($mp_id_product, $id_specific_price, $id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_tax, $reduction_type, $from, $to, $id_product_attribute);
        }
    }

    //actionBeforeAddMPProduct
    public function hookActionBeforeAddMPProduct()
    {
        if (Tools::getValue('showTpl')) {
            $showTpl = 0;

            $leave_bprice = 0;
            if (1 == Tools::getValue('leave_bprice')) {
                $leave_bprice = Tools::getValue('leave_bprice');
            }
            $sp_price = -1;
            if (Tools::getValue('sp_price')) {
                $sp_price = Tools::getValue('sp_price');
            }
            $price = $leave_bprice ? '-1' : $sp_price;
            $from_quantity = Tools::getValue('sp_from_quantity');
            $reduction = (float) Tools::getValue('sp_reduction');
            $reduction_tax = Tools::getValue('sp_reduction_tax');
            $specificPrice = Tools::getValue('sp_price');
            $reduction_type = !$reduction ? 'amount' : Tools::getValue('sp_reduction_type');
            $reduction_type = $reduction_type == '-' ? 'amount' : $reduction_type;
            $from = Tools::getValue('sp_from');
            if (!$from) {
                $from = '0000-00-00 00:00:00';
            }
            $to = Tools::getValue('sp_to');
            if (!$to) {
                $to = '0000-00-00 00:00:00';
            }
            $id_shop = 1;       // hardcoded id_shop = 1
            $id_product_attribute = 0;  // hardcoded id_product_attribute = 0

            if (($price == '-1') && ((float)$reduction == '0')) {
                $this->context->controller->errors[] = $this->l('No reduction value has been submitted');
            } elseif ($to != '0000-00-00 00:00:00' && strtotime($to) < strtotime($from)) {
                $this->context->controller->errors[] = $this->l('Invalid date range');
            } elseif ($reduction_type == 'percentage' && ((float)$reduction <= 0 || (float)$reduction > 100)) {
                $this->context->controller->errors[] = $this->l('Submitted reduction value (0-100) is out-of-range');
            } elseif ((!isset($price) && !isset($reduction)) || (isset($price) && !Validate::isNegativePrice($price)) || (isset($reduction) && !Validate::isPrice($reduction))) {
                $this->context->controller->errors[] = $this->l('Invalid price/discount amount');
            } elseif (!Validate::isUnsignedInt($from_quantity)) {
                $this->context->controller->errors[] = $this->l('Invalid quantity');
            } elseif ($reduction && !Validate::isReductionType($reduction_type)) {
                $this->context->controller->errors[] = $this->l('Please select a discount type (amount or percentage).');
            } elseif ($from && $to && (!Validate::isDateFormat($from) || !Validate::isDateFormat($to))) {
                $this->context->controller->errors[] = $this->l('The from/to date is invalid.');
            } else {
                $showTpl = 1;
            }
        }
    }


    /**
     * [hookDisplayMpUpdateProductFooter -> display add slot price button below marketplace update product].
     *
     * @return [type] [description]
     */
    public function hookDisplayMpUpdateProductFooter()
    {
        $link = new Link();
        $mp_product_id = Tools::getValue('id_mp_product');
        if ($mp_product_id) {
            $product_detail = new WkMpSellerProduct($mp_product_id);
            $obj_mp_slot_price = new MpPriceSlots();
            $price_slots = $obj_mp_slot_price->getAllProductSlots($mp_product_id);
            $currencies = Currency::getCurrencies();
            $countries = Country::getCountries($this->context->language->id);
            $groups = Group::getGroups($this->context->language->id);
            $tmp = array();
            foreach ($currencies as $currency) {
                $tmp[$currency['id_currency']] = $currency;
            }
            $currencies = $tmp;

            $tmp = array();
            foreach ($countries as $country) {
                $tmp[$country['id_country']] = $country;
            }
            $countries = $tmp;

            $tmp = array();
            foreach ($groups as $group) {
                $tmp[$group['id_group']] = $group;
            }
            $groups = $tmp;
            if ($price_slots) {
                $i = 0;
                foreach ($price_slots as $key => $specific_price) {
                    $id_currency = $specific_price['id_currency'] ? $specific_price['id_currency'] : Configuration::get('PS_CURRENCY_DEFAULT');
                    $current_specific_currency = $currencies[$id_currency];
                    if ($specific_price['reduction_type'] == 'percentage') {
                        $impact = '- '.($specific_price['reduction'] * 100).' %';
                    } elseif ($specific_price['reduction'] > 0) {
                        $impact = '- '.Tools::displayPrice(Tools::ps_round($specific_price['reduction'], 2), $current_specific_currency).' ';
                        if ($specific_price['reduction_tax']) {
                            $impact .= '('.$this->l('Tax incl.').')';
                        } else {
                            $impact .= '('.$this->l('Tax excl.').')';
                        }
                    } else {
                        $impact = '--';
                    }

                    if ($specific_price['id_customer']) {
                        $customer = new Customer((int) $specific_price['id_customer']);
                        if ($customer) {
                            $customer_full_name = $customer->firstname.' '.$customer->lastname;
                            unset($customer);
                        }
                    }

                    if ($specific_price['from'] == '0000-00-00 00:00:00' && $specific_price['to'] == '0000-00-00 00:00:00') {
                        $period = $this->l('Unlimited');
                    } else {
                        $period = $this->l('From').' '.($specific_price['from'] != '0000-00-00 00:00:00' ? $specific_price['from'] : '0000-00-00 00:00:00').'<br />'.$this->l('To').' '.($specific_price['to'] != '0000-00-00 00:00:00' ? $specific_price['to'] : '0000-00-00 00:00:00');
                    }
                    $price = Tools::ps_round($specific_price['price'], 2);
                    $fixed_price = ($price == Tools::ps_round($product_detail->price, 2) || $specific_price['price'] == -1) ? '--' : Tools::displayPrice($price, $current_specific_currency);
                    $slot_price[$key]['id'] = $specific_price['id'];
                    $slot_price[$key]['id_currency'] = $specific_price['id_currency'] ? $currencies[$specific_price['id_currency']]['name'] : $this->l('All currencies');
                    $slot_price[$key]['id_country'] = $specific_price['id_country'] ? $countries[$specific_price['id_country']]['name'] : $this->l('All countries');
                    $slot_price[$key]['id_group'] = $specific_price['id_group'] ? $groups[$specific_price['id_group']]['name'] : $this->l('All groups');
                    $slot_price[$key]['id_customer'] = isset($customer_full_name) ? $customer_full_name : $this->l('All customers');
                    $slot_price[$key]['price'] = $fixed_price;
                    $slot_price[$key]['impact'] = $impact;
                    $slot_price[$key]['period'] = $period;
                    $slot_price[$key]['from_quantity'] = $specific_price['from_quantity'];
                    unset($customer_full_name);
                }
                $this->context->smarty->assign(array(
                    'price_slots' => $slot_price,
                    ));
            }
            $obj_seller_product = new WkMpSellerProduct();
            $ps_product = $obj_seller_product->getSellerProduct($mp_product_id);
            $product_detail = WkMpSellerProduct::getSellerProductByIdProduct($mp_product_id);
            if ($product_detail) {
                $this->context->smarty->assign('product_detail', $product_detail);
            }
            $this->context->smarty->assign(array(
                'updateProduct' => 1,
                'controller' => Tools::getValue('controller'),
                'currencies' => $currencies,
                'currency' => new Currency(Configuration::get('PS_CURRENCY_DEFAULT')),
                'countries' => $countries,
                'groups' => $groups,
                'multi_shop' => Shop::isFeatureActive(),
                'link' => new Link(),
                'pack' => new Pack(),
                'country_display_tax_label' => $this->context->country->display_tax_label,
                'mp_product_id' => $mp_product_id,
                'modules_dir' => _MODULE_DIR_,
                ));
            if ('updateproduct' == Tools::getValue('controller')) {
                return $this->fetch('module:mpslotpricing/views/templates/hook/addslots.tpl');
            } elseif ('AdminSellerProductDetail' == Tools::getValue('controller')) {
                return $this->display(__FILE__, 'addslots.tpl');
            }
        }
    }

    /**
     * [hookActionToogleMPProductCreateStatus -> adding specific price to prestashop in case of Mp product activated by admin at first time].
     *
     * @param [type] $params [product information]
     *
     * @return [type] [description]
     */
    public function hookActionToogleMPProductCreateStatus($params)
    {
        $mp_product_id = Tools::getValue('id_mp_product');
        $ps_product_id = $params['id_product'];
        $obj_mp_slot_price = new MpPriceSlots();
        $price_slots = $obj_mp_slot_price->getAllProductSlots($mp_product_id);
        if (!empty($price_slots)) {
            foreach ($price_slots as $slots) {
                $id_specific_price = $slots['id_specific_price'];
                $obj_specific_price = new SpecificPrice($id_specific_price);
                if (empty($obj_specific_price->id)) {
                    $specificPrice = new SpecificPrice();
                    $specificPrice->id_product = (int) $ps_product_id;
                    $specificPrice->id_product_attribute = $slots['id_product_attribute'];
                    $specificPrice->id_shop = $slots['id_shop'];
                    $specificPrice->id_currency = $slots['id_currency'];
                    $specificPrice->id_country = $slots['id_country'];
                    $specificPrice->id_group = $slots['id_group'];
                    $specificPrice->id_customer = $slots['id_customer'];
                    $specificPrice->price = $slots['price'];
                    $specificPrice->from_quantity = $slots['from_quantity'];
                    $specificPrice->reduction = $slots['reduction'];
                    $specificPrice->reduction_tax = $slots['reduction_tax'];
                    $specificPrice->reduction_type = $slots['reduction_type'];
                    $specificPrice->from = $slots['from'];
                    $specificPrice->to = $slots['to'];
                    if ($specificPrice->add()) {
                        $obj_mp_slot_price = new MpPriceSlots($slots['id']);
                        $obj_mp_slot_price->id_specific_price = $specificPrice->id;
                        $obj_mp_slot_price->save();
                    }
                }
            }
        }
    }

    public function install()
    {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return (false);
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return (false);
        }
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);
        foreach ($sql as $query) {
            if ($query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }

        if (!parent::install()
            || !$this->registerHook('actionMpProductDelete')
            || !$this->registerHook('actionBeforeAddMPProduct')
            || !$this->registerHook('actionToogleMPProductCreateStatus')
            || !$this->registerHook('actionAfterAddMPProduct')
            || !$this->registerHook('actionFrontControllerSetMedia')
            || !$this->registerHook('actionAdminControllerSetMedia')
            || !$this->registerHook('displayMpAddProductFooter')
            || !$this->registerHook('displayMpUpdateProductFooter')
            ) {
            return false;
        }

        return true;
    }

    public function dropSpecificPrice()
    {
        $slot_dtl_arr = MpPriceSlots::getAllPsSlotsId();
        if ($slot_dtl_arr) {
            foreach ($slot_dtl_arr as $slot_dtl) {
                $specific_price_id = $slot_dtl['id_specific_price'];
                $obj_specific_price = new SpecificPrice($specific_price_id);
                $obj_specific_price->delete();
            }
        }

        return true;
    }

    public function dropTable()
    {
        return Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'mp_price_slots');
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->dropSpecificPrice()
            || !$this->dropTable()
            ) {
            return false;
        }

        return true;
    }
}
