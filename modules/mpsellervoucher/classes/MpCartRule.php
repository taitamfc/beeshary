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

class MpCartRule extends ObjectModel
{
    public $id_mp_cart_rule;
    public $name;
    public $id_ps_cart_rule;
    public $id_seller;
    public $for_customer;
    public $date_from;
    public $date_to;
    public $description;
    public $quantity;
    public $quantity_per_user;
    public $priority;
    public $code;
    public $country_restriction;
    public $group_restriction;
    public $product_restriction;
    public $reduction_percent;
    public $reduction_amount;
    public $reduction_tax;
    public $reduction_currency;
    public $mp_reduction_product;               // idProducts | -2 (multiple products)
    public $highlight;
    public $active;
    public $admin_approval;
    public $date_add;
    public $date_upd;


    public static $definition = array(
        'table' => 'mp_cart_rule',
        'primary' => 'id_mp_cart_rule',
        'multilang' => true,
        'fields' => array(
            'id_ps_cart_rule' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_seller' =>                  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'for_customer' =>                   array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_from' =>                  array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'date_to' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'description' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 65534),
            'quantity' =>                   array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'quantity_per_user' =>                  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'priority' =>                   array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'code' =>                   array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 254),
            'country_restriction' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'group_restriction' =>                  array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'product_restriction' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'reduction_percent' =>                  array('type' => self::TYPE_FLOAT, 'validate' => 'isPercentage'),
            'reduction_amount' =>                   array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'reduction_tax' =>                  array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'reduction_currency' =>                 array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'mp_reduction_product' =>                   array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'highlight' =>                  array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' =>                 array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'admin_approval' =>                 array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>                   array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>                   array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

            /* Lang fields */
            'name' =>                   array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'size' => 254),
        ),
    );

    public function getVoucherDetailByPsIdCartRule($id_ps_cart_rule)
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."mp_cart_rule` WHERE `id_ps_cart_rule` = ".(int)$id_ps_cart_rule;
        $voucher = Db::getInstance()->getRow($sql);
        if ($voucher) {
            return $voucher;
        }
        return false;
    }

    public function getVoucherDetailById($id_mp_cart_rule, $seller_default_lang)
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."mp_cart_rule` WHERE `id_mp_cart_rule` = ".(int)$id_mp_cart_rule;
        $voucher = Db::getInstance()->getRow($sql);
        if ($voucher) {
            if ($voucher['reduction_percent'] > '0.00')
                $voucher['reduction_type'] = 1;
            else
                $voucher['reduction_type'] = 2;

            $voucher['currency'] = Currency::getCurrency($voucher['reduction_currency']);

            // Code For getting customer details
            if ($voucher['for_customer']) {
                $customer = new Customer((int)$voucher['for_customer']);
                $voucher['customer'] = array('id_customer' => $customer->id,
                                            'email' => $customer->email,
                                            'firstname' => $customer->firstname,
                                            'lastname' => $customer->lastname);
            }

            // Code For Voucher Name
            $sql = "SELECT `name`, `id_lang` FROM `"._DB_PREFIX_."mp_cart_rule_lang` WHERE `id_mp_cart_rule` = ".(int)$id_mp_cart_rule;
            $voucher_name = Db::getInstance()->executeS($sql);
            $name_arr = array();
            foreach ($voucher_name as $key => $name)
                $name_arr[$name['id_lang']] = $name['name'];
            $voucher['name'] = $name_arr;

            if ($voucher['country_restriction']) {
                $countries = $this->getCountriesByIdMpCartRule($id_mp_cart_rule);
                if ($countries)
                    foreach ($countries as $key => $country)
                        $voucher['countries'][$country['id_country']] = array('id_country' => $country['id_country']);
            }

            if ($voucher['group_restriction']) {
                $groups = $this->getGroupsByIdMpCartRule($id_mp_cart_rule);
                if ($groups)
                    foreach ($groups as $key => $group)
                        $voucher['groups'][$group['id_group']] = array('id_group' => $group['id_group']);
            }

            if ($voucher['mp_reduction_product'] > 0) {
                $voucher['reduction_for'] = 1;
            } elseif ($voucher['mp_reduction_product'] == -2) {
                $voucher['reduction_for'] = 2;
            }


            // IMPORTANT NOTE
            // As Specific Product Marketplace Id is stored in "mp_reduction_product" column. So, for now we are taking it directly from that column but for future when we use other restriction like "Product selection" take data from product restriction tables.
            $voucher['specific_prod'] = array();
            $voucher['multiple_prod'] = array();

            if ($voucher['reduction_for'] == 1) {
               $voucher['specific_prod'] = WkMpSellerProduct::getSellerProductByIdProduct($voucher['mp_reduction_product'], $seller_default_lang);
            } elseif ($voucher['reduction_for'] == 2) {
                $obj_mp_prod_rule_grp = new MpCartRuleProductRuleGroup();
                $multiSelectedProducts = $obj_mp_prod_rule_grp->getVoucherProductRuleInfo($id_mp_cart_rule);
                $id_mp_products = array_column($multiSelectedProducts, 'id_mp_item');
                $voucher['multiple_prod'] = $id_mp_products;
            }

            return $voucher;
        }
        return false;
    }

    public function getSellerVoucherByIdSeller($id_seller, $id_lang, $active = false, $admin_approval = false)
    {
        $sql = "SELECT mcr.*, mcrl.`name`
                FROM `"._DB_PREFIX_."mp_cart_rule` AS mcr
                INNER JOIN `"._DB_PREFIX_."mp_cart_rule_lang` AS mcrl ON (mcrl.`id_mp_cart_rule` = mcr.`id_mp_cart_rule` AND mcrl.`id_lang` = ".(int)$id_lang.")
                WHERE mcr.`id_seller` = ".(int)$id_seller;

        if ($active !== false)
            $sql .= " mcr.`active` = ".(int)$active;

        if ($admin_approval !== false)
            $sql .= " mcr.`admin_approval` = ".(int)$admin_approval;


        $result = Db::getInstance()->executeS($sql);
        if ($result)
            return $result;

        return false;
    }

    public function getSellerProductByIdSeller($id_seller, $search_query = false, $isPsProduct = false, $default_lang = false, $active = false)
    {
        if (!$default_lang) {
            $default_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        $sql = "SELECT msp.`id_mp_product` AS mp_id_prod, mspl.`product_name`
                FROM `"._DB_PREFIX_."wk_mp_seller_product` AS msp
                INNER JOIN `"._DB_PREFIX_."wk_mp_seller_product_lang` AS mspl ON(mspl.`id_mp_product` = msp.`id_mp_product` AND mspl.id_lang = ".$default_lang.")
                WHERE msp.`id_seller` =".(int)$id_seller;

        if ($search_query) {
            $sql .= " AND (msp.`id_mp_product` = ".(int)$search_query." OR mspl.`product_name` LIKE '%".$search_query."%')";
        }

        if ($isPsProduct !== false) {
            if ($isPsProduct) {
                $sql .= " AND msp.`id_ps_product` > 0";
            } else {
                $sql .= " AND msp.`id_ps_product` <= 0";
            }
        }

        if ($active !== false) {
            $sql .= " AND msp.`active` =".(int)$active;
        }

        $result = Db::getInstance()->executeS($sql);
        if ($result)
            return $result;
        return false;
    }

    public function getSellerProductOrderedCustomers($seller_customer_id, $search_query = false)
    {
    	$sql = 'SELECT psc.`id_customer`, psc.`email`, psc.`firstname`, psc.`lastname`
    			FROM `'._DB_PREFIX_.'wk_mp_seller_order_detail` AS msod
    			INNER JOIN `'._DB_PREFIX_.'orders` AS pso ON (pso.`id_order` = msod.`id_order`)
    			INNER JOIN `'._DB_PREFIX_.'customer` AS psc ON (psc.`id_customer` = pso.`id_customer`)
    			WHERE msod.`seller_customer_id` = '.(int)$seller_customer_id.' AND psc.`deleted` = 0 AND psc.`is_guest` = 0 AND psc.`active` = 1';

        if ($search_query) {
            $sql .= ' AND (psc.`id_customer` = '.(int)$search_query.'
                OR psc.`email` LIKE "%'.pSQL($search_query).'%"
                OR psc.`firstname` LIKE "%'.pSQL($search_query).'%"
                OR psc.`lastname` LIKE "%'.pSQL($search_query).'%")';
        }

        $sql .= ' GROUP BY psc.`id_customer`';

        $result = Db::getInstance()->executeS($sql);
        if ($result)
            return $result;
        return false;
    }

    public function getPsCustomers($search_query = false)
    {
        if ($search_query) {
            $customers = Db::getInstance()->executeS('
                SELECT `id_customer`, `email`, `firstname`, `lastname`
                FROM `'._DB_PREFIX_.'customer`
                WHERE `deleted` = 0 AND is_guest = 0 AND active = 1
                AND (
                    `id_customer` = '.(int)$search_query.'
                    OR `email` LIKE "%'.pSQL($search_query).'%"
                    OR `firstname` LIKE "%'.pSQL($search_query).'%"
                    OR `lastname` LIKE "%'.pSQL($search_query).'%"
                )
                ORDER BY `firstname`, `lastname` ASC
                LIMIT 50');
        }
        else
            $customers = Customer::getCustomers();

        if ($customers)
            return $customers;
        return false;
    }

    public function getCountriesByIdMpCartRule($id_mp_cart_rule)
    {
        $sql = "SELECT id_country FROM `"._DB_PREFIX_."mp_cart_rule_country` WHERE id_mp_cart_rule = ".(int)$id_mp_cart_rule;

        $result = Db::getInstance()->executeS($sql);
        if ($result)
            return $result;
        return false;
    }

    public function getGroupsByIdMpCartRule($id_mp_cart_rule)
    {
        $sql = "SELECT id_group FROM `"._DB_PREFIX_."mp_cart_rule_group` WHERE id_mp_cart_rule = ".(int)$id_mp_cart_rule;

        $result = Db::getInstance()->executeS($sql);
        if ($result)
            return $result;
        return false;
    }

    public function insertIntoMpCartRuleCountryGroupTables($country_select = false, $group_select = false, $id_mp_cart_rule = false, $upd_data = 0)
    {
        if (!$id_mp_cart_rule) {
            $id_mp_cart_rule = $this->id;
        }

        if ($upd_data) {
            $this->deleteMpCartRuleCountryByIdMpCartRule($id_mp_cart_rule);
            $this->deleteMpCartRuleGroupByIdMpCartRule($id_mp_cart_rule);
        }

        $obj_mp_cart_rule = new MpCartRule($id_mp_cart_rule);

        $restriction_key = array();
        $context = Context::getContext();

        if ($obj_mp_cart_rule->country_restriction && $country_select) {
            if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
                $countries = Carrier::getDeliveredCountries($context->language->id, true, true);
            } else {
                $countries = Country::getCountries($context->language->id, true);
            }

            $ps_num_country = count($countries);
            $num_seleced_country = count($country_select);

            if ($num_seleced_country == $ps_num_country) {
                $obj_mp_cart_rule->country_restriction = 0;
                $obj_mp_cart_rule->save();
            }
            else
                $restriction_key[] = 'country';
        }

        if ($obj_mp_cart_rule->group_restriction && $group_select) {
            $groups = Group::getGroups($context->language->id, true);
            $ps_num_group = count($groups);
            $num_seleced_group = count($group_select);

            if ($ps_num_group == $num_seleced_group) {
                $obj_mp_cart_rule->group_restriction = 0;
                $obj_mp_cart_rule->save();
            }
            else
                $restriction_key[] = 'group';
        }

        if (count($restriction_key) && ($country_select || $group_select)) {
            foreach ($restriction_key as $type) {
                if (is_array($array = ($type == 'country' ? $country_select : $group_select)) && count($array)) {
                    $values = array();
                    foreach ($array as $id)
                        $values[] = '('.(int)$id_mp_cart_rule.','.(int)$id.')';

                    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'mp_cart_rule_'.$type.'` (`id_mp_cart_rule`, `id_'.$type.'`) VALUES '.implode(',', $values));
                }
            }
            return true;
        }
        return false;
    }

    public function getSellerCartRule($id_seller, $default_lang = false, $exclude_id = false, $active = false, $admin_approval = false)
    {
        $sql = "SELECT mcr.`id_mp_cart_rule`";

        if ($default_lang)
            $sql .= ", mcrl.`name`";

        $sql .= " FROM `"._DB_PREFIX_."mp_cart_rule` AS mcr";

        if ($default_lang)
            $sql .= " INNER JOIN `"._DB_PREFIX_."mp_cart_rule_lang` AS mcrl ON (mcr.id_mp_cart_rule = mcrl.id_mp_cart_rule AND mcrl.id_lang = ".(int)$default_lang.")";

        $sql .= " WHERE mcr.`id_seller` = ".$id_seller;

        if ($exclude_id != false)
            $sql .= " AND mcr.`id_mp_cart_rule` NOT IN (".$exclude_id.")";

        if ($active != false)
            $sql .= " AND active = ".(int)$active;

        if ($admin_approval != false)
            $sql .= " AND active = ".(int)$admin_approval;

        $result = Db::getInstance()->executeS($sql);
        if ($result)
            return $result;

        return false;
    }

    public function insertUpdateDataIntoPsTables($id_mp_cart_rule = false, $update_data = 0)
    {
        if (!$id_mp_cart_rule) {
            $id_mp_cart_rule = $this->id;
        }

        $obj_mp_cart_rule = new MpCartRule($id_mp_cart_rule);

        if ($update_data) {
            $id_cart_rule = $obj_mp_cart_rule->id_ps_cart_rule;
            $cart_rule = new CartRule($id_cart_rule);
        } else {
            $cart_rule = new CartRule();
        }

        $ps_id_prod = 0;
        if ($obj_mp_cart_rule->mp_reduction_product != -2) {
            $mp_id_prod = $obj_mp_cart_rule->mp_reduction_product;
            $obj_mp_product = new WkMpSellerProduct((int)$mp_id_prod);
            $ps_id_prod = $obj_mp_product->id_ps_product;
        }

        $reduction_product = ($obj_mp_cart_rule->mp_reduction_product != -2) ? (int)$ps_id_prod : $obj_mp_cart_rule->mp_reduction_product ;

        $cart_rule->id_customer = $obj_mp_cart_rule->for_customer;
        $cart_rule->date_from = $obj_mp_cart_rule->date_from;
        $cart_rule->date_to = $obj_mp_cart_rule->date_to;
        $cart_rule->description = $obj_mp_cart_rule->description;
        $cart_rule->quantity = $obj_mp_cart_rule->quantity;
        $cart_rule->quantity_per_user = $obj_mp_cart_rule->quantity_per_user;
        $cart_rule->priority = $obj_mp_cart_rule->priority;
        $cart_rule->code = $obj_mp_cart_rule->code;
        $cart_rule->country_restriction = $obj_mp_cart_rule->country_restriction;
        $cart_rule->group_restriction = $obj_mp_cart_rule->group_restriction;
        $cart_rule->product_restriction = $obj_mp_cart_rule->product_restriction;
        $cart_rule->reduction_percent = $obj_mp_cart_rule->reduction_percent;
        $cart_rule->reduction_amount = $obj_mp_cart_rule->reduction_amount;
        $cart_rule->reduction_tax = $obj_mp_cart_rule->reduction_tax;
        $cart_rule->reduction_currency = $obj_mp_cart_rule->reduction_currency;
        $cart_rule->reduction_product = $reduction_product;
        $cart_rule->highlight = $obj_mp_cart_rule->highlight;
        $cart_rule->active = $obj_mp_cart_rule->active;
        $cart_rule->name = $obj_mp_cart_rule->name;
        $cart_rule->partial_use = 0;
        $cart_rule->minimum_amount_tax = Configuration::get('PS_CURRENCY_DEFAULT'); //
        $cart_rule->save();

        $id_cart_rule = $cart_rule->id;
        if ($id_cart_rule) {
            if (!$update_data) {
                $obj_mp_cart_rule->id_ps_cart_rule = $id_cart_rule;
                $obj_mp_cart_rule->save();
            }

            // NOTE : If EDIT delete data form Prestashop Cart Rule Associated Tables
            if ($update_data) {
                $this->deleteDataFromPsCartRuleAssociatedTables($id_cart_rule);
            }

            // Insert data into ps_cart_rule_country table
            if ($cart_rule->country_restriction) {
                $cart_rule_countries = $this->getCountriesByIdMpCartRule($id_mp_cart_rule);
                if ($cart_rule_countries) {
                    $insert_data = array();
                    foreach ($cart_rule_countries as $country){
                        $insert_data[] = '('.(int)$id_cart_rule.','.(int)$country["id_country"].')';
                    }

                    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_country` (`id_cart_rule`, `id_country`) VALUES '.implode(',', $insert_data));
                }
            }

            // Insert data into ps_cart_rule_group table
            if ($cart_rule->group_restriction) {
                $cart_rule_groups = $this->getGroupsByIdMpCartRule($id_mp_cart_rule);
                if ($cart_rule_groups) {
                    $insert_data = array();
                    foreach ($cart_rule_groups as $group)
                        $insert_data[] = '('.(int)$id_cart_rule.','.(int)$group['id_group'].')';

                    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_group` (`id_cart_rule`, `id_group`) VALUES '.implode(',', $insert_data));
                }
            }

            // Insert data into product restriction Tables
            if ($cart_rule->product_restriction) {
                $obj_mp_prod_rule_grp = new MpCartRuleProductRuleGroup();
                $obj_mp_prod_rule = new MpCartRuleProductRule();

                // Insert Into "cart_rule_product_rule_group" table
                $mp_product_rule_grp_dtl = $obj_mp_prod_rule_grp->getDataByIdMpCartRule($id_mp_cart_rule);
                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`)
                VALUES ('.(int)$id_cart_rule.', '.(int)$mp_product_rule_grp_dtl["quantity"].')');
                $id_product_rule_group = Db::getInstance()->Insert_ID();


                // Insert Into "cart_rule_product_rule" table
                $mp_product_rule_dtl = $obj_mp_prod_rule->getDataByIdMpProductRuleGroup($mp_product_rule_grp_dtl['id_mp_product_rule_group']);
                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule` (`id_product_rule_group`, `type`) VALUES ('.(int)$id_product_rule_group.', "'.$mp_product_rule_dtl["type"].'")');
                $id_product_rule = Db::getInstance()->Insert_ID();

                // Insert Into "cart_rule_product_rule_value" table
                $rowLists = array();
                if ($reduction_product != -2) {
                    $rowLists[] = array('id_product_rule' => (int)$id_product_rule, 'id_item' => (int)$ps_id_prod);
                } else {
                    $mpProductRuleValues = $obj_mp_prod_rule->getDataFromMpCartRuleProductRuleValueTable($mp_product_rule_dtl['id_mp_product_rule']);
                    if (count($mpProductRuleValues) > 0) {
                        foreach ($mpProductRuleValues as $mpCartRuleProductValue) {
                            $obj_mp_product = new WkMpSellerProduct((int)$mpCartRuleProductValue['id_mp_item']);
                            $ps_id_prod = $obj_mp_product->id_ps_product;
                            $rowLists[] = array('id_product_rule' => (int)$id_product_rule, 'id_item' => (int)$ps_id_prod);
                        }
                    }
                }

                Db::getInstance()->insert('cart_rule_product_rule_value', $rowLists);
            }

            // Code From Prestashop
            // If the new rule has no cart rule restriction, then it must be added to the white list of the other cart rules that have restrictions
            if (!$cart_rule->cart_rule_restriction) {
                Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`) (
                    SELECT id_cart_rule, '.(int)$id_cart_rule.' FROM `'._DB_PREFIX_.'cart_rule` WHERE cart_rule_restriction = 1
                )');
            }

            return $id_cart_rule;
        }

        return false;
    }

    public function changeStatus($mp_cart_rule, $force_status = false, $by_admin = 0)
    {
        if (!is_array($mp_cart_rule))
            $mp_cart_rule = array($mp_cart_rule);

        foreach ($mp_cart_rule as $key => $id_mp_cart_rule) {
            $obj_mp_cart_rule = new MpCartRule($id_mp_cart_rule);

            if ($force_status === false) {
                if ($obj_mp_cart_rule->active) {
                    $final_status = 0;
                }
                else {
                    $final_status = 1;
                }
            }
            else {
                $final_status = (int)$force_status;
            }

            if (!$final_status) {
                $obj_mp_cart_rule->active = $final_status;
                $obj_mp_cart_rule->admin_approval = $final_status;
                $obj_mp_cart_rule->save();

                if ($obj_mp_cart_rule->id_ps_cart_rule) {
                    $id_cart_rule = $obj_mp_cart_rule->id_ps_cart_rule;
                    $cart_rule = new CartRule($id_cart_rule);
                    $cart_rule->active = $final_status;
                    $cart_rule->save();
                }
            }
            else {
                $obj_mp_cart_rule->active = $final_status;
                $obj_mp_cart_rule->admin_approval = $final_status;
                $obj_mp_cart_rule->save();

                if ($obj_mp_cart_rule->id_ps_cart_rule) {
                    $id_cart_rule = $obj_mp_cart_rule->insertUpdateDataIntoPsTables(false, 1);
                }
                else {
                    $id_cart_rule = $obj_mp_cart_rule->insertUpdateDataIntoPsTables();
                }
            }
        }

        return true;
    }

    public function deleteVoucher($mp_cart_rule)
    {
        if (!is_array($mp_cart_rule))
            $mp_cart_rule = array($mp_cart_rule);

        $obj_mp_prod_rule_grp = new MpCartRuleProductRuleGroup();

        foreach ($mp_cart_rule as $key => $id_mp_cart_rule) {
            if ($id_mp_cart_rule) {
                $obj_mp_cart_rule = new MpCartRule($id_mp_cart_rule);

                if ($obj_mp_cart_rule->country_restriction) {
                    $this->deleteMpCartRuleCountryByIdMpCartRule($id_mp_cart_rule);
                }

                if ($obj_mp_cart_rule->group_restriction) {
                    $this->deleteMpCartRuleGroupByIdMpCartRule($id_mp_cart_rule);
                }

                if ($obj_mp_cart_rule->product_restriction) {
                    $obj_mp_prod_rule_grp->deleteProductRestrictionByIdMpCartRule($id_mp_cart_rule);
                }

                if ($obj_mp_cart_rule->id_ps_cart_rule) {
                    $id_cart_rule = $obj_mp_cart_rule->id_ps_cart_rule;
                    $this->deleteDataFromPsCartRuleAssociatedTables($id_cart_rule);

                    $cart_rule = new CartRule($id_cart_rule);
                    $cart_rule->delete();
                }

                $obj_mp_cart_rule->delete();
            }
        }

        return true;
    }

    // delete data from PS cart rule associated tables
    public function deleteMpCartRuleCountryByIdMpCartRule($id_mp_cart_rule)
    {
        return Db::getInstance()->delete('mp_cart_rule_country', '`id_mp_cart_rule` = '.(int)$id_mp_cart_rule);
    }
    public function deleteMpCartRuleGroupByIdMpCartRule($id_mp_cart_rule)
    {
        return Db::getInstance()->delete('mp_cart_rule_group', '`id_mp_cart_rule` = '.(int)$id_mp_cart_rule);
    }

    // delete data from PS cart rule associated tables
    public function deleteDataFromPsCartRuleAssociatedTables($id_cart_rule)
    {
        $this->deletePsCartRuleCountryByIdCartRule($id_cart_rule);
        $this->deletePsCartRuleGroupByIdCartRule($id_cart_rule);
        $this->deletePsCartRuleCombinationByIdCartRule($id_cart_rule);
        $this->deleteDataFromCartRuleProdRestrictionTabels($id_cart_rule);

        return true;
    }
    public function deletePsCartRuleCountryByIdCartRule($id_cart_rule)
    {
        return Db::getInstance()->delete('cart_rule_country', '`id_cart_rule` = '.(int)$id_cart_rule);
    }
    public function deletePsCartRuleGroupByIdCartRule($id_cart_rule)
    {
        return Db::getInstance()->delete('cart_rule_group', '`id_cart_rule` = '.(int)$id_cart_rule);
    }
    public function deletePsCartRuleCombinationByIdCartRule($id_cart_rule)
    {
        return Db::getInstance()->delete('cart_rule_combination', '`id_cart_rule_1` = '.(int)$id_cart_rule.' OR `id_cart_rule_2` = '.(int)$id_cart_rule);
    }
    public function deleteDataFromCartRuleProdRestrictionTabels($id_cart_rule)
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."cart_rule_product_rule_group` WHERE `id_cart_rule` = ".(int)$id_cart_rule;
        $prod_rule_grp = Db::getInstance()->getRow($sql);
        if ($prod_rule_grp) {
            $sql = "SELECT * FROM `"._DB_PREFIX_."cart_rule_product_rule` WHERE `id_product_rule_group` = ".(int)$prod_rule_grp['id_product_rule_group'];
            $prod_rule = Db::getInstance()->getRow($sql);
            if ($prod_rule) {
                Db::getInstance()->delete('cart_rule_product_rule_value', '`id_product_rule` = '.(int)$prod_rule['id_product_rule']);
                Db::getInstance()->delete('cart_rule_product_rule', '`id_product_rule_group` = '.(int)$prod_rule_grp['id_product_rule_group']);
                Db::getInstance()->delete('cart_rule_product_rule_group', '`id_cart_rule` = '.(int)$id_cart_rule);

                return true;
            }
        }
        return false;
    }

    // Not using this function
    // This functionality is not present in this module
    public function insertIntoMpCartRuleCombinationTable($cart_rule_selected, $id_mp_cart_rule = false)
    {
        if (!$id_mp_cart_rule) {
            $id_mp_cart_rule = $this->id;
        }

        $obj_mp_cart_rule = new MpCartRule($id_mp_cart_rule);
        $id_seller = $obj_mp_cart_rule->id_seller;

        if ($obj_mp_cart_rule->cart_rule_restriction) {
            $seller_cart_rule = $this->getSellerCartRule($id_seller, false, $id_mp_cart_rule);

            $num_seller_cart_rule = count($seller_cart_rule);
            $num_selected_cart_rule = count($cart_rule_selected);

            if ($num_selected_cart_rule == $num_seller_cart_rule ) {
                $obj_mp_cart_rule->cart_rule_restriction = 0;
                $obj_mp_cart_rule->save();
            }
            else {
                if (is_array($array = $cart_rule_selected) && count($array)) {
                    $values = array();
                    foreach ($array as $id) {
                        $values[] = '('.(int)$id_mp_cart_rule.','.(int)$id.')';
                    }
                    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'mp_cart_rule_combination` (`id_mp_cart_rule_1`, `id_mp_cart_rule_2`) VALUES '.implode(',', $values));
                }
            }
        }

        // NOTE : Code From Prestashop
        // If the new rule has no cart rule restriction, then it must be added to the white list of the other cart rules that have restrictions
        if (!$obj_mp_cart_rule->cart_rule_restriction) {
            Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'mp_cart_rule_combination` (`id_mp_cart_rule_1`, `id_mp_cart_rule_2`) (
                SELECT id_mp_cart_rule, '.(int)$id_mp_cart_rule.' FROM `'._DB_PREFIX_.'mp_cart_rule` WHERE cart_rule_restriction = 1
            )');
        }
        // And if the new cart rule has restrictions, previously unrestricted cart rules may now be restricted (a mug of coffee is strongly advised to understand this sentence)
        else {
            $ruleCombinations = Db::getInstance()->executeS('
            SELECT cr.id_mp_cart_rule
            FROM '._DB_PREFIX_.'mp_cart_rule cr
            WHERE cr.id_mp_cart_rule != '.(int)$id_mp_cart_rule.'
            AND cr.cart_rule_restriction = 0
            AND NOT EXISTS (
                SELECT 1
                FROM '._DB_PREFIX_.'mp_cart_rule_combination
                WHERE cr.id_mp_cart_rule = '._DB_PREFIX_.'mp_cart_rule_combination.id_mp_cart_rule_2 AND '.(int)$id_mp_cart_rule.' = id_mp_cart_rule_1
            )
            AND NOT EXISTS (
                SELECT 1
                FROM '._DB_PREFIX_.'mp_cart_rule_combination
                WHERE cr.id_mp_cart_rule = '._DB_PREFIX_.'mp_cart_rule_combination.id_mp_cart_rule_1 AND '.(int)$id_mp_cart_rule.' = id_mp_cart_rule_2
            )
            ');
            foreach ($ruleCombinations as $incompatibleRule) {
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'mp_cart_rule` SET cart_rule_restriction = 1 WHERE id_mp_cart_rule = '.(int)$incompatibleRule['id_mp_cart_rule'].' LIMIT 1');
                Db::getInstance()->execute('
                INSERT IGNORE INTO `'._DB_PREFIX_.'mp_cart_rule_combination` (`id_mp_cart_rule_1`, `id_mp_cart_rule_2`) (
                    SELECT id_mp_cart_rule, '.(int)$incompatibleRule['id_mp_cart_rule'].' FROM `'._DB_PREFIX_.'mp_cart_rule`
                    WHERE active = 1
                    AND id_mp_cart_rule != '.(int)$id_mp_cart_rule.'
                    AND id_mp_cart_rule != '.(int)$incompatibleRule['id_mp_cart_rule'].'
                )');
            }
        }

        return true;
    }
}