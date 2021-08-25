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

class Mpshippingmethod extends ObjectModel
{
    public $id;
    public $mp_shipping_name;
    public $shipping_method;
    public $mp_id_seller;
    public $id_tax_rule_group;
    public $id_ps_reference;
    public $range_behavior;
    public $tracking_url;
    public $max_width;
    public $max_height;
    public $max_depth;
    public $max_weight;
    public $grade;
    public $is_free;
    public $deleted;
    public $active;
    public $is_done = 0;
    public $shipping_handling;
    public $is_default_shipping;
    public $date_add;
    public $date_upd;

    public $transit_delay;

    public static $definition = array(
        'table' => 'mp_shipping_method',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'mp_shipping_name' => array('type' => self::TYPE_STRING, 'validate' => 'isCarrierName', 'required' => true, 'size' => 64),
            'shipping_method' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'mp_id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_ps_reference' => array('type' => self::TYPE_INT),
            'id_tax_rule_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'range_behavior' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'tracking_url' => array('type' => self::TYPE_STRING, 'validate' => 'isAbsoluteUrl'),
            'max_width' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'max_height' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'max_depth' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'max_weight' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'grade' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'size' => 1),
            'is_free' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'deleted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' => array('type' => self::TYPE_BOOL),
            'is_done' => array('type' => self::TYPE_BOOL),
            'shipping_handling' => array('type' => self::TYPE_BOOL),
            'is_default_shipping' => array('type' => self::TYPE_BOOL),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),

            'transit_delay' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
        ),
    );

    public function deleteMpShipping($id_shipping)
    {
        $obj_mp_shipping = new self($id_shipping);
        $obj_mp_ship_product_map = new Mpshippingproductmap();

        $id_ps_reference = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_ps_reference` FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `id` = '.(int) $id_shipping);
        if ($id_ps_reference) {
            $del = Db::getInstance()->update('carrier', array('deleted' => 1), 'id_reference = '.$id_ps_reference);
            if ($del) {
                $obj_mp_shipping->delete();
            }
        } else {
            $obj_mp_shipping->delete();
        }

        $mpshipping_prod_map = $obj_mp_ship_product_map->getMpShippingForProducts($id_shipping);
        if ($mpshipping_prod_map) {
            Db::getInstance()->delete('mp_shipping_product_map', 'mp_shipping_id = '.$id_shipping);
        }
    }

    public function getAllShippingMethodNotDelete($mp_id_seller, $delete, $is_done = 1)
    {
        $mp_shipping_detail = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `mp_id_seller` = '.(int) $mp_id_seller.' AND `deleted` = '.(int) $delete.' AND `is_done` = '.(int) $is_done);

        if (empty($mp_shipping_detail)) {
            return false;
        } else {
            return $mp_shipping_detail;
        }
    }

    public function addToCarrier($mp_shipping_id)
    {
        $obj_mp_shipping = new self($mp_shipping_id);

        $obj_carrier = new Carrier();
        $obj_carrier->name = $obj_mp_shipping->mp_shipping_name;
        $obj_carrier->active = $obj_mp_shipping->active;
        $obj_carrier->url = $obj_mp_shipping->tracking_url;
        $obj_carrier->range_behavior = $obj_mp_shipping->range_behavior;
        $obj_carrier->position = Carrier::getHigherPosition() + 1;
        $obj_carrier->shipping_method = $obj_mp_shipping->shipping_method;
        $obj_carrier->max_width = $obj_mp_shipping->max_width;
        $obj_carrier->max_height = $obj_mp_shipping->max_height;
        $obj_carrier->max_depth = $obj_mp_shipping->max_depth;
        $obj_carrier->max_weight = $obj_mp_shipping->max_weight;
        $obj_carrier->grade = $obj_mp_shipping->grade;
        $obj_carrier->shipping_handling = $obj_mp_shipping->shipping_handling;
        $obj_carrier->shipping_external = true;
        $obj_carrier->external_module_name = 'mpshipping';
        $obj_carrier->is_module = 1;
        $obj_carrier->need_range = 1;

        if ($obj_mp_shipping->is_free) {
            $obj_carrier->is_free = 1;
        }
        $mpship_info = array();
        $mpship_lang_info_arr = $this->getMarketPlaceShippingLangInfo($mp_shipping_id);
        foreach ($mpship_lang_info_arr as $mpship_lang_info) {
            $mpship_info['delay'][$mpship_lang_info['id_lang']] = $mpship_lang_info['transit_delay'];
        }

        foreach (Language::getLanguages(true) as $lang) {
            $obj_carrier->delay[$lang['id_lang']] = $mpship_info['delay'][$lang['id_lang']];
        }

        $obj_carrier->save();
        $id_ps_carrier = $obj_carrier->id; //First time id_ps_carrier and id_reference both are same

        if ($obj_mp_shipping->shipping_method == 2) {
            $this->addRangePrice($mp_shipping_id, $id_ps_carrier); //range price
        } else {
            $this->addRangeWeight($mp_shipping_id, $id_ps_carrier); //range weight
        }

        $this->addZone($mp_shipping_id, $id_ps_carrier);
        $this->updateZoneShop($id_ps_carrier);
        $this->addCarrierTaxRule($id_ps_carrier, $obj_mp_shipping->id_tax_rule_group);
        $this->addCarrierGroup($id_ps_carrier);

        return $id_ps_carrier;
    }

    public function getMarketPlaceShippingLangInfo($id)
    {
        $marketplaceshippinginfo = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_method_lang` WHERE `id` = '.$id);

        if (!empty($marketplaceshippinginfo)) {
            return $marketplaceshippinginfo;
        } else {
            return false;
        }
    }

    public function updateToCarrier($mp_shipping_id, $id_reference)
    {
        //Delete Carrier
        Db::getInstance()->update('carrier', array('deleted' => 1), 'id_reference='.$id_reference);

        $obj_mp_shipping = new self($mp_shipping_id);
        $obj_carrier = new Carrier();
        $obj_carrier->name = $obj_mp_shipping->mp_shipping_name;
        $obj_carrier->active = $obj_mp_shipping->active;
        $obj_carrier->url = $obj_mp_shipping->tracking_url;
        $obj_carrier->range_behavior = $obj_mp_shipping->range_behavior;
        $obj_carrier->position = Carrier::getHigherPosition() + 1;
        $obj_carrier->shipping_method = $obj_mp_shipping->shipping_method;
        $obj_carrier->max_width = $obj_mp_shipping->max_width;
        $obj_carrier->max_height = $obj_mp_shipping->max_height;
        $obj_carrier->max_depth = $obj_mp_shipping->max_depth;
        $obj_carrier->max_weight = $obj_mp_shipping->max_weight;
        $obj_carrier->grade = $obj_mp_shipping->grade;
        $obj_carrier->shipping_handling = $obj_mp_shipping->shipping_handling;
        $obj_carrier->shipping_external = true;
        $obj_carrier->external_module_name = 'mpshipping';
        $obj_carrier->is_module = 1;
        $obj_carrier->need_range = 1;

        if ($obj_mp_shipping->is_free) {
            $obj_carrier->is_free = 1;
        } else {
            $obj_carrier->is_free = 0;
        }

        $mpship_lang_info_arr = $this->getMarketPlaceShippingLangInfo($mp_shipping_id);
        $mpship_info = array();
        foreach ($mpship_lang_info_arr as $mpship_lang_info) {
            $mpship_info['delay'][$mpship_lang_info['id_lang']] = $mpship_lang_info['transit_delay'];
        }

        foreach (Language::getLanguages(true) as $lang) {
            $obj_carrier->delay[$lang['id_lang']] = $mpship_info['delay'][$lang['id_lang']];
        }

        $obj_carrier->save();
        $id_ps_carriers = $obj_carrier->id;

        if ($obj_mp_shipping->shipping_method == 2) {
            //range price
            Db::getInstance()->delete('range_price', 'id_carrier = '.(int) $id_ps_carriers);
            $this->addRangePrice($mp_shipping_id, $id_ps_carriers);
        } else {
            //range weight
            Db::getInstance()->delete('range_weight', 'id_carrier = '.(int) $id_ps_carriers);
            $this->addRangeWeight($mp_shipping_id, $id_ps_carriers);
        }

        //Delete data from table before adding new data
        Db::getInstance()->delete('carrier_zone', 'id_carrier='.(int) $id_ps_carriers);
        $this->addZone($mp_shipping_id, $id_ps_carriers);

        $this->updateZoneShop($id_ps_carriers);

        Db::getInstance()->delete('carrier_tax_rules_group_shop', 'id_carrier = '.(int) $id_ps_carriers);
        $this->addCarrierTaxRule($id_ps_carriers, $obj_mp_shipping->id_tax_rule_group);

        Db::getInstance()->delete('carrier_group', 'id_carrier = '.(int) $id_ps_carriers);
        $this->addCarrierGroup($id_ps_carriers);

        if ($id_reference) {
            $new_obj_carrier = new Carrier($id_ps_carriers);
            $new_obj_carrier->id_reference = $id_reference;
            $new_obj_carrier->save();
        }

        return $id_ps_carriers;
    }

    public function addRangePrice($mp_shipping_id, $id_carrier)
    {
        $obj_mp_range = new Mprangeprice();
        $obj_mp_range->mp_shipping_id = $mp_shipping_id;
        $range_detail_info = $obj_mp_range->getAllRangeAccordingToShippingId();
        if ($range_detail_info) {
            foreach ($range_detail_info as $range_detail) {
                Db::getInstance()->insert('range_price', array(
                                        'id_carrier' => (int) $id_carrier,
                                        'delimiter1' => $range_detail['delimiter1'],
                                        'delimiter2' => $range_detail['delimiter2'],
                                    ));
                $range_price_insert_id = Db::getInstance()->Insert_ID();
                $this->addDelivery($id_carrier, $mp_shipping_id, $range_price_insert_id, $range_detail['id_range'], true);
            }
        }

        return true;
    }

    public function addRangeWeight($mp_shipping_id, $id_carrier)
    {
        $obj_mp_range = new Mprangeweight();
        $obj_mp_range->mp_shipping_id = $mp_shipping_id;
        $range_detail_info = $obj_mp_range->getAllRangeAccordingToShippingId();
        if ($range_detail_info) {
            foreach ($range_detail_info as $range_detail) {
                Db::getInstance()->insert('range_weight', array(
                                        'id_carrier' => (int) $id_carrier,
                                        'delimiter1' => $range_detail['delimiter1'],
                                        'delimiter2' => $range_detail['delimiter2'],
                                    ));
                $range_weight_insert_id = Db::getInstance()->Insert_ID();
                $this->addDelivery($id_carrier, $mp_shipping_id, $range_weight_insert_id, $range_detail['id_range'], false);
            }
        }

        return true;
    }

    public function addZone($mp_shipping_id, $id_carrier)
    {
        $obj_mp_del = new Mpshippingdelivery();
        $id_zone_detail = $obj_mp_del->getIdZoneByShiipingId($mp_shipping_id);

        if ($id_zone_detail) {
            foreach ($id_zone_detail as $id_zo_det) {
                Db::getInstance()->insert('carrier_zone', array(
                                    'id_carrier' => (int) $id_carrier,
                                    'id_zone' => (int) $id_zo_det['id_zone'],
                                ));
            }
        }

        return true;
    }

    public function updateZoneShop($id_carrier)
    {
        return Db::getInstance()->update('carrier_shop', array('id_shop' => (int) Context::getContext()->shop->id), 'id_carrier ="'.(int) $id_carrier.'" ');
    }

    public function addCarrierTaxRule($id_carrier, $id_tax_rule_group)
    {
        return Db::getInstance()->insert('carrier_tax_rules_group_shop', array(
                                    'id_carrier' => (int) $id_carrier,
                                    'id_tax_rules_group' => $id_tax_rule_group,
                                    'id_shop' => (int) Context::getContext()->shop->id,
                                ));
    }

    public function addCarrierGroup($id_carrier)
    {
        $group_detail = Group::getGroups(1);
        foreach ($group_detail as $group_det) {
            Db::getInstance()->insert('carrier_group', array(
                                    'id_carrier' => (int) $id_carrier,
                                    'id_group' => $group_det['id_group'],
                                ));
        }

        return true;
    }

    public function addDelivery($id_carrier, $mp_shipping_id, $id_range, $mp_id_range, $is_price_range = false)
    {
        $obj_mpshipping_del = new Mpshippingdelivery();
        if ($is_price_range) {
            $delivery_detail_info = $obj_mpshipping_del->getDeliveryBySIdAndRpId($mp_shipping_id, $mp_id_range);
            if ($delivery_detail_info) {
                foreach ($delivery_detail_info as $delivery_detail) {
                    Db::getInstance()->insert('delivery', array(
                                        'id_carrier' => $id_carrier,
                                        'id_range_price' => $id_range,
                                        'id_zone' => $delivery_detail['id_zone'],
                                        'price' => $delivery_detail['base_price'],
                                    ));
                }
            }
        } else {
            $delivery_detail_info = $obj_mpshipping_del->getDeliveryBySIdAndRwId($mp_shipping_id, $mp_id_range);
            if ($delivery_detail_info) {
                foreach ($delivery_detail_info as $delivery_detail) {
                    Db::getInstance()->insert('delivery', array(
                                        'id_carrier' => $id_carrier,
                                        'id_range_weight' => $id_range,
                                        'id_zone' => $delivery_detail['id_zone'],
                                        'price' => $delivery_detail['base_price'],
                                    ));
                }
            }
        }

        return true;
    }

    public function getMpShippingMethods($mp_id_seller)
    {
        $mp_shipping_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `mp_id_seller` = '.(int) $mp_id_seller.' AND `deleted` = 0 AND `active` = 1 AND `is_done` = 1');

        if (empty($mp_shipping_data)) {
            return false;
        } else {
            return $mp_shipping_data;
        }
    }

    public static function updateDefaultShipping($mp_shipping_id, $is_default_shipping)
    {
        return Db::getInstance()->update('mp_shipping_method', array('is_default_shipping' => (int) $is_default_shipping), '`id` = '.(int) $mp_shipping_id);
    }

    public function getDefaultMpShippingMethods($mp_id_seller)
    {
        $mp_shipping_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `mp_id_seller` = '.(int) $mp_id_seller.' AND `deleted` = 0 AND `active` = 1 AND `is_done` = 1 AND `is_default_shipping` = 1');

        if (empty($mp_shipping_data)) {
            return false;
        } else {
            return $mp_shipping_data;
        }
    }

    public function getMpShippingPsShopId($mp_shipping_id)
    {
        $ps_shop_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_ps_shop` FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `id` = '.(int) $mp_shipping_id.'');

        if (empty($ps_shop_id)) {
            return false;
        } else {
            return $ps_shop_id;
        }
    }

    public function insertProductCarrierDetails($id_product, $id_carrier_reference, $id_shop)
    {
        $ps_product_carrier_details = Db::getInstance()->insert('product_carrier', array(
                                            'id_product' => (int) $id_product,
                                            'id_carrier_reference' => (int) $id_carrier_reference,
                                            'id_shop' => (int) $id_shop,

                                        ));

        return $ps_product_carrier_details;
    }

    public function getAdminShippingMethods()
    {
        $ps_carriers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'carrier` WHERE `deleted` = 0 and `active` = 1');

        if (empty($ps_carriers)) {
            return false;
        } else {
            return $ps_carriers;
        }
    }

    public function getAllProducts($mp_id_seller)
    {
        $mp_products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_product` 
		WHERE `id_seller` = '.$mp_id_seller);

        if (empty($mp_products)) {
            return false;
        } else {
            return $mp_products;
        }
    }

    /* ----- After delete mp_shipping_map table -----*/

    public function getAllReferenceId()
    {
        $id_ps_reference = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id_ps_reference` FROM `'._DB_PREFIX_.'mp_shipping_method`');

        if (empty($id_ps_reference)) {
            return false;
        } else {
            return $id_ps_reference;
        }
    }

    public static function getMpShippingId($id_ps_reference)
    {
        $mp_shipping_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id` FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE id_ps_reference = '.(int) $id_ps_reference);

        if (empty($mp_shipping_id)) {
            return false;
        } else {
            return $mp_shipping_id;
        }
    }

    public static function getReferenceByMpShippingId($mp_shipping_id)
    {
        $id_ps_reference = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_ps_reference` FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `id` = '.(int) $mp_shipping_id);

        if (empty($id_ps_reference)) {
            return false;
        } else {
            return $id_ps_reference;
        }
    }

    public static function getCarrierIdByReference($id_ps_reference)
    {
        $id_carrier = Db::getInstance()->getValue('SELECT `id_carrier` FROM `'._DB_PREFIX_.'carrier`
			WHERE `id_reference` = '.(int) $id_ps_reference.' AND deleted = 0 ORDER BY id_carrier DESC');

        if (empty($id_carrier)) {
            return false;
        } else {
            return $id_carrier;
        }
    }

    public static function DeletePriceWeightDeliveryById($mpshipping_id)
    {
        Db::getInstance()->delete('mp_range_price', 'mp_shipping_id = '.(int) $mpshipping_id);
        Db::getInstance()->delete('mp_range_weight', 'mp_shipping_id = '.(int) $mpshipping_id);
        Db::getInstance()->delete('mp_shipping_delivery', 'mp_shipping_id = '.(int) $mpshipping_id);

        return true;
    }

    public static function getOnlyPrestaCarriers($id_lang)
    {
        $obj_carr = new Carrier();
        $carr_detials = $obj_carr->getCarriers($id_lang, true);

        if (!$carr_detials) {
            return false;
        }

        $carr_details_mod = $obj_carr->getCarriers($id_lang, true, false, false, null, 2);
        if ($carr_details_mod) {
            $carr_detials_final = array_merge($carr_detials, $carr_details_mod);
        } else {
            $carr_detials_final = $carr_detials;
        }

        $only_ps_carriers = array();
        if ($carr_detials_final) {
            foreach ($carr_detials_final as $carr_val) {
                $mp_carrier = Db::getInstance()->getRow('SELECT `id` FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `id_ps_reference` = '.(int) $carr_val['id_reference']);

                if (empty($mp_carrier)) {
                    $only_ps_carriers[] = $carr_val;
                }
            }
        }

        return $only_ps_carriers;
    }

    public function isSellerShippingByIdReference($ps_id_ref)
    {
        $is_seller_shipping = Db::getInstance()->getRow('SELECT `id` FROM `'._DB_PREFIX_.'mp_shipping_method` WHERE `id_ps_reference` = '.(int) $ps_id_ref);
        if ($is_seller_shipping) {
            return true;
        }

        return false;
    }

    public function updateCarriersOnDeactivateOrDelete()
    {
        $admin_def_shipping = Configuration::get('MP_SHIPPING_ADMIN_DEFAULT');
        /*Assign new selected shipping methods to the seller produccts which have no seller shipping methods*/
        $all_seller_admin_products = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_product` WHERE `id_ps_product`!= 0');
        if ($all_seller_admin_products) {
            $obj_mp_shp_mthod = new self();
            foreach ($all_seller_admin_products as $val_prod) {
                $prod_obj = new Product($val_prod['id_ps_product']);
                $carriers_lst = $prod_obj->getCarriers();
                $to_change = 1;
                $to_assign = 1;
                foreach ($carriers_lst as $val_crr) {
                    $is_seller_crr = $obj_mp_shp_mthod->isSellerShippingByIdReference($val_crr['id_reference']);
                    if ($is_seller_crr) {
                        $to_change = 0;
                        if ($val_crr['active'] == 1) {
                            $to_assign = 0;
                        }
                    }
                }
                if ($to_change || $to_assign) {
                    //set carrier using carrier reference
                    $prod_obj->setCarriers(unserialize($admin_def_shipping));
                }
            }
        }
        /*END*/
    }

    public function enableShipping($mpshipping_id, $id_ps_reference_added)
    {
        $obj_mp_shipping = new self($mpshipping_id);
        $obj_mp_shipping->id_ps_reference = $id_ps_reference_added;
        $obj_mp_shipping->active = 1;
        $obj_mp_shipping->save();

        $img_dir = _PS_MODULE_DIR_.'mpshipping/views/img/logo/';
        if (file_exists($img_dir.$mpshipping_id.'.jpg')) {
            copy($img_dir.$mpshipping_id.'.jpg', _PS_IMG_DIR_.'s/'.$id_ps_reference_added.'.jpg');
        }
    }

    public function mailToAdminShippingAdded($mp_id_seller, $mpshipping_id)
    {
        /*$obj_seller_info = new WkMpSeller();*/
        $seller_info = WkMpSeller::getSeller($mp_id_seller);
        $id_lang = $seller_info['default_lang']; // Seller default lang


        $obj_seller = new WkMpSeller($mp_id_seller, $id_lang);
        $mp_seller_name = $obj_seller->seller_name;
        $business_email = $obj_seller->business_email;
        $mp_shop_name = $obj_seller->shop_name;
        $phone = $obj_seller->phone;

        if ($business_email == '') {
            $id_customer = $obj_seller->seller_customer_id;
            $obj_cus = new Customer($id_customer);
            $business_email = $obj_cus->email;
        }

        $shipping_info = $this->getMpShippingInfo($mpshipping_id, $id_lang);

        if ($shipping_info['is_free'] == 0) {
            $free_shipping = 'No';
        } else {
            $free_shipping = 'Yes';
        }

        if ($shipping_info['shipping_handling'] == 0) {
            $handling = 'No';
        } else {
            $handling = 'Yes';
        }

        if ($shipping_info['active'] == 0) {
            $status = 'Pending';
        } else {
            $status = 'Approved';
        }

        $templateVars = array(
            '{seller_name}' => $mp_seller_name,
            '{mp_shop_name}' => $mp_shop_name,
            '{business_email}' => $business_email,
            '{phone}' => $phone,
            '{shipping_name}' => $shipping_info['mp_shipping_name'],
            '{transit_delay}' => $shipping_info['transit_delay'],
            '{free_shipping}' => $free_shipping,
            '{handling_cost}' => $handling,
            '{status}' => $status,
        );

        $temp_path = _PS_MODULE_DIR_.'mpshipping/mails/';

        Mail::Send(
            $id_lang,
            'shipping_added',
            Mail::l('New Shipping method added', $id_lang),
            $templateVars,
            Configuration::get('PS_SHOP_EMAIL'),
            null,
            null,
            'Mpshipping',
            null,
            null,
            $temp_path,
            false,
            null,
            null
        );
    }

    public function mailToSeller($mp_id_seller, $mpshipping_id, $approve)
    {
        /*$obj_seller_info = new WkMpSeller();*/
        $seller_info = WkMpSeller::getSeller($mp_id_seller);
        $id_lang = $seller_info['default_lang']; // Seller default lang

        $obj_seller = new WkMpSeller($mp_id_seller, $id_lang);
        $mp_seller_name = $obj_seller->seller_name;
        $business_email = $obj_seller->business_email;
        $mp_shop_name = $obj_seller->shop_name;
        $phone = $obj_seller->phone;

        $business_email = $seller_info['business_email'];
        if ($business_email == '') {
            $id_customer = $obj_seller->seller_customer_id;
            $obj_cus = new Customer($id_customer);
            $business_email = $obj_cus->email;
        }
        $shipping_info = $this->getMpShippingInfo($mpshipping_id, $id_lang);

        if ($shipping_info['is_free'] == 0) {
            $free_shipping = 'No';
        } else {
            $free_shipping = 'Yes';
        }

        if ($shipping_info['shipping_handling'] == 0) {
            $handling = 'No';
        } else {
            $handling = 'Yes';
        }

        if ($shipping_info['active'] == 0) {
            $status = 'Pending';
        } else {
            $status = 'Approved';
        }

        $templateVars = array(
            '{seller_name}' => $mp_seller_name,
            '{mp_shop_name}' => $mp_shop_name,
            '{business_email}' => $business_email,
            '{phone}' => $phone,
            '{shipping_name}' => $shipping_info['mp_shipping_name'],
            '{transit_delay}' => $shipping_info['transit_delay'],
            '{free_shipping}' => $free_shipping,
            '{handling_cost}' => $handling,
            '{status}' => $status,

        );

        $temp_path = _PS_MODULE_DIR_.'mpshipping/mails/';

        if ($approve == 1) {
            Mail::Send(
                $id_lang,
                'shipping_active',
                Mail::l('Shipping method activated', $id_lang),
                $templateVars,
                $business_email,
                null,
                null,
                'Mpshipping',
                null,
                null,
                $temp_path,
                false,
                null,
                null
            );
        }
        if ($approve == 0) {
            Mail::Send(
                $id_lang,
                'shipping_deactive',
                Mail::l('Shipping method deactivated', $id_lang),
                $templateVars,
                $business_email,
                null,
                null,
                'Mpshipping',
                null,
                null,
                $temp_path,
                false,
                null,
                null
            );
        }
    }

    public function getMpShippingInfo($mp_id_shipping, $id_lang)
    {
        $shipping_info = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_method` mp_shp_mthd LEFT JOIN `'._DB_PREFIX_.'mp_shipping_method_lang` mp_shp_mthd_lang ON (mp_shp_mthd.id = mp_shp_mthd_lang.id AND mp_shp_mthd_lang.`id_lang`='.$id_lang.') WHERE mp_shp_mthd.id='.$mp_id_shipping);

        if ($shipping_info) {
            return $shipping_info;
        } else {
            return false;
        }
    }
}
