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

class Mpshippingimpact extends ObjectModel
{
    public $id;
    public $mp_shipping_id;
    public $shipping_delivery_id;
    public $id_zone;
    public $id_country;
    public $id_state;
    public $impact_price;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'mp_shipping_impact',
        'primary' => 'id',
        'fields' => array(
            'mp_shipping_id' => array('type' => self::TYPE_INT, 'required' => true),
            'shipping_delivery_id' => array('type' => self::TYPE_INT, 'required' => true),
            'id_zone' => array('type' => self::TYPE_INT, 'required' => true),
            'id_country' => array('type' => self::TYPE_INT, 'required' => true),
            'id_state' => array('type' => self::TYPE_INT),
            'impact_price' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public function getImpactPriceWithRange($total_order_price_wt, $mp_shipping_id, $id_zone, $id_country, $id_state, $by_price = 0, $no_state = 0)
    {
        if ($by_price) {
            $ship_del_id = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'mp_shipping_delivery` WHERE `mp_shipping_id`='.$mp_shipping_id.' AND `id_zone`='.$id_zone.' AND `mp_id_range_price`= (SELECT `id` FROM `'._DB_PREFIX_.'mp_range_price` WHERE `mp_shipping_id`='.$mp_shipping_id.' AND `delimiter1`<='.$total_order_price_wt.' AND `delimiter2`>'.$total_order_price_wt.')');
        } else {
            $ship_del_id = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'mp_shipping_delivery` WHERE `mp_shipping_id`='.$mp_shipping_id.' AND `id_zone`='.$id_zone.' AND `mp_id_range_weight`= (SELECT `id` FROM `'._DB_PREFIX_.'mp_range_weight` WHERE `mp_shipping_id`='.$mp_shipping_id.' AND `delimiter1`<='.$total_order_price_wt.' AND `delimiter2`>'.$total_order_price_wt.')');
        }

        if ($no_state) {
            $sql = 'SELECT `impact_price` FROM `'._DB_PREFIX_.'mp_shipping_impact` WHERE `mp_shipping_id`='.$mp_shipping_id.' AND `id_zone`='.$id_zone.' AND `id_country`='.(int) $id_country.' AND `shipping_delivery_id`='.(int) $ship_del_id;
        } else {
            $cur_id_state = Db::getInstance()->getValue('SELECT `id_state` FROM `'._DB_PREFIX_.'mp_shipping_impact` WHERE `mp_shipping_id`='.$mp_shipping_id.' AND `id_zone`='.$id_zone.' AND `id_country`='.(int) $id_country.' AND `id_state`='.(int) $id_state.' AND `shipping_delivery_id`='.(int) $ship_del_id);

            if ($cur_id_state && $cur_id_state == $id_state) {
                $sql = 'SELECT `impact_price` FROM `'._DB_PREFIX_.'mp_shipping_impact` WHERE `mp_shipping_id`='.$mp_shipping_id.' AND `id_zone`='.$id_zone.' AND `id_country`='.(int) $id_country.' AND `id_state`='.(int) $id_state.' AND `shipping_delivery_id`='.(int) $ship_del_id;
            } else {
                $sql = 'SELECT `impact_price` FROM `'._DB_PREFIX_.'mp_shipping_impact` WHERE `mp_shipping_id`='.$mp_shipping_id.' AND `id_zone`='.$id_zone.' AND `id_country`='.(int) $id_country.' AND `id_state`=0 AND `shipping_delivery_id`='.(int) $ship_del_id;
            }
        }
        $result = Db::getInstance()->getValue($sql);
        if ($result) {
            return $result;
        }

        return 0;
    }

    public function getCountriesByZoneId($id_zone, $id_lang)
    {
        $sql = 'SELECT DISTINCT c.id_country, cl.name
				FROM `'._DB_PREFIX_.'country` c
				'.Shop::addSqlAssociation('country', 'c', false).'
				LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_country` = c.`id_country`)
				LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country`)
				WHERE (c.`id_zone` = '.(int) $id_zone.' OR s.`id_zone` = '.(int) $id_zone.')
				AND `id_lang` = '.(int) $id_lang;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function getStatesByIdCountry($id_country)
    {
        if (empty($id_country)) {
            die(Tools::displayError());
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT s.`id_state`, s.`name` FROM `'._DB_PREFIX_.'state` s WHERE s.`id_country` = '.(int) $id_country.' AND s.`active` = 1');
    }

    public function isAllReadyInImpact($mp_shipping_id, $shipping_delivery_id, $id_zone, $id_country, $id_state)
    {
        $is_exist_impact = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM '._DB_PREFIX_.'mp_shipping_impact WHERE mp_shipping_id = '.(int) $mp_shipping_id.' AND shipping_delivery_id = '.(int) $shipping_delivery_id.' AND id_zone='.$id_zone.' AND id_country = '.(int) $id_country.' AND id_state = '.(int) $id_state);

        if (empty($is_exist_impact)) {
            return false;
        } else {
            return $is_exist_impact;
        }
    }

    public static function getAllImpactPriceByMpshippingid($mp_shipping_id)
    {
        $allimpactprice = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'mp_shipping_impact WHERE mp_shipping_id = '.(int) $mp_shipping_id);

        if ($allimpactprice) {
            return $allimpactprice;
        } else {
            return false;
        }
    }

    public static function getZonenameByZoneid($id_zone)
    {
        $allzone = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM '._DB_PREFIX_.'zone WHERE id_zone = '.(int) $id_zone);

        if ($allzone) {
            return $allzone;
        } else {
            return false;
        }
    }

    public static function updateImpactAfterUpdateShipping($mpshipping_id, $first_del_id, $first_range_id, $range_type)
    {
        $all_impc = self::getAllImpactPriceByMpshippingid($mpshipping_id);
        if ($all_impc) {
            foreach ($all_impc as $val_ipc) {
                $obj_shp_del = new Mpshippingdelivery($val_ipc['shipping_delivery_id']);
                $mp_range_weight_id = $obj_shp_del->mp_id_range_weight;
                $mp_range_price_id = $obj_shp_del->mp_id_range_price;

                if ($mp_range_price_id) {
                    if ($range_type != 2) {
                        Db::getInstance()->delete('mp_range_price', 'mp_shipping_id = '.(int) $mpshipping_id);
                        Db::getInstance()->delete('mp_shipping_impact', 'mp_shipping_id = '.(int) $mpshipping_id);
                        break;
                    }
                    $obj_rng_prc = new Mprangeprice($mp_range_price_id);
                    if (isset($obj_rng_prc->delimiter1)) {
                        $delm1 = $obj_rng_prc->delimiter1;
                        $delm2 = $obj_rng_prc->delimiter2;
                        $nw_rng_prc_id = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'mp_range_price` WHERE `delimiter2` != 0 AND  `delimiter1`>='.$delm1.' AND `delimiter2`<='.$delm2.' AND `id`>='.$first_range_id);
                    }
                    if (isset($nw_rng_prc_id) && $nw_rng_prc_id) {
                        $nw_shp_del_id = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'mp_shipping_delivery` WHERE `mp_shipping_id`='.$mpshipping_id.' AND `id_zone`='.$val_ipc['id_zone'].' AND `mp_id_range_price`='.$nw_rng_prc_id);
                        if ($nw_shp_del_id) {
                            $allUPDATE = Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'mp_shipping_impact SET `shipping_delivery_id` = '.(int) $nw_shp_del_id.' WHERE `id` = '.(int) $val_ipc['id']);
                        } else {
                            $obj_impc = new self($val_ipc['id']);
                            $obj_impc->delete();
                        }
                    } else {
                        $obj_impc = new self($val_ipc['id']);
                        $obj_impc->delete();
                    }
                } elseif ($mp_range_weight_id) {
                    if ($range_type != 1) {
                        Db::getInstance()->delete('mp_range_weight', 'mp_shipping_id = '.(int) $mpshipping_id);
                        Db::getInstance()->delete('mp_shipping_impact', 'mp_shipping_id = '.(int) $mpshipping_id);
                        break;
                    }
                    $obj_rng_prc = new Mprangeweight($mp_range_weight_id);
                    $delm1 = $obj_rng_prc->delimiter1;
                    $delm2 = $obj_rng_prc->delimiter2;
                    $nw_rng_weight_id = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'mp_range_weight` WHERE `delimiter2` != 0 AND `delimiter1`>='.$delm1.' AND `delimiter2`<='.$delm2.' AND `id`>='.$first_range_id);
                    if ($nw_rng_weight_id) {
                        $nw_shp_del_id = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'mp_shipping_delivery` WHERE `mp_shipping_id`='.$mpshipping_id.' AND `id_zone`<='.$val_ipc['id_zone'].' AND `mp_id_range_weight`='.$nw_rng_weight_id);
                        if ($nw_shp_del_id) {
                            $allUPDATE = Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'mp_shipping_impact SET `shipping_delivery_id` = '.(int) $nw_shp_del_id.' WHERE `id` = '.(int) $val_ipc['id']);
                        } else {
                            $obj_impc = new self($val_ipc['id']);
                            $obj_impc->delete();
                        }
                    } else {
                        $obj_impc = new self($val_ipc['id']);
                        $obj_impc->delete();
                    }
                } else {
                    $obj_impc = new self($val_ipc['id']);
                    $obj_impc->delete();
                }
            }
        }
        if ($range_type == 2) {
            Db::getInstance()->delete('mp_range_price', 'mp_shipping_id = '.(int) $mpshipping_id.' AND id < '.(int) $first_range_id);
        } elseif ($range_type == 1) {
            Db::getInstance()->delete('mp_range_weight', 'mp_shipping_id = '.(int) $mpshipping_id.' AND id < '.(int) $first_range_id);
        }
        Db::getInstance()->delete('mp_shipping_delivery', 'mp_shipping_id = '.(int) $mpshipping_id.' AND id < '.(int) $first_del_id);
        if (isset($allUPDATE) && $allUPDATE) {
            return true;
        } else {
            return false;
        }
    }
}
