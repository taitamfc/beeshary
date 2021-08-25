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

class MpShippingDelivery extends ObjectModel
{
    public $id;
    public $mp_shipping_id;
    public $id_zone;
    public $mp_id_range_price;
    public $mp_id_range_weight;
    public $base_price = 0;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'mp_shipping_delivery',
        'primary' => 'id',
        'fields' => array(
            'mp_shipping_id' => array('type' => self::TYPE_INT, 'required' => true),
            'id_zone' => array('type' => self::TYPE_INT, 'required' => true),
            'mp_id_range_price' => array('type' => self::TYPE_INT, 'required' => true),
            'mp_id_range_weight' => array('type' => self::TYPE_INT, 'required' => true),
            'base_price' => array('type' => self::TYPE_FLOAT,'validate' => 'isPrice', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public function getDliveryMethodForPriceRange($idZone, $mpShippingId)
    {
        $deliveryMethod = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT msd.`id`,round(mrp.`delimiter1`,2) as delimiter1,round(mrp.`delimiter2`,2) as delimiter2,mrp.`id` as id_range FROM `'._DB_PREFIX_.'mp_shipping_delivery` msd
			LEFT JOIN `'._DB_PREFIX_.'mp_range_price` mrp on (mrp.`id`=msd.`mp_id_range_price`)
			WHERE msd.`mp_shipping_id` = '.(int) $mpShippingId.' AND msd.`id_zone` = '.(int) $idZone.' AND msd.mp_id_range_weight = 0');

        if (empty($deliveryMethod)) {
            return false;
        } else {
            return $deliveryMethod;
        }
    }

    public static function getShippingDeliveryLastId($mpShippingId)
    {
        $idDel = Db::getInstance()->getValue('SELECT `id` FROM '._DB_PREFIX_.'mp_range_weight where mp_shipping_id = '.(int) $mpShippingId.' ORDER BY `id` DESC');
        if ($idDel) {
            return $idDel;
        }

        return 0;
    }

    public function getDliveryMethodForWeightRange($idZone, $mpShippingId)
    {
        $deliveryMethod = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT msd.`id`,round(mrw.`delimiter1`,2) as delimiter1,round(mrw.`delimiter2`,2) as delimiter2,mrw.`id` as id_range FROM `'._DB_PREFIX_.'mp_shipping_delivery` msd
			LEFT JOIN `'._DB_PREFIX_.'mp_range_weight` mrw on (mrw.`id`= msd.`mp_id_range_weight`)
			WHERE msd.`mp_shipping_id` = '.(int) $mpShippingId.' AND msd.`id_zone` = '.(int) $idZone.' AND msd.mp_id_range_price = 0');

        if (empty($deliveryMethod)) {
            return false;
        } else {
            return $deliveryMethod;
        }
    }

    public function getIdZoneByShiipingId($mpShippingId)
    {
        $idZoneDetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT distinct(`id_zone`) FROM `'._DB_PREFIX_.'mp_shipping_delivery` WHERE mp_shipping_id = '.(int) $mpShippingId);

        if (empty($idZoneDetail)) {
            return false;
        } else {
            return $idZoneDetail;
        }
    }

    public function getDeliveryDetailByShiipingId($mpShippingId)
    {
        $deliveryDetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_delivery` WHERE `mp_shipping_id` = '.(int) $mpShippingId);

        if (empty($deliveryDetail)) {
            return false;
        } else {
            return $deliveryDetail;
        }
    }

    public function getDeliveryBySIdAndRpId($mpShippingId, $mpIdRangePrice)
    {
        $deliveryDetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_delivery` WHERE `mp_shipping_id` = '.(int) $mpShippingId.' AND `mp_id_range_price` = '.(int) $mpIdRangePrice);

        if (empty($deliveryDetail)) {
            return false;
        } else {
            return $deliveryDetail;
        }
    }

    public function getDeliveryBySIdAndRwId($mpShippingId, $mpIdRangeWeight)
    {
        $deliveryDetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_delivery` WHERE `mp_shipping_id` = '.(int) $mpShippingId.' AND `mp_id_range_weight` = '.(int) $mpIdRangeWeight);

        if (empty($deliveryDetail)) {
            return false;
        } else {
            return $deliveryDetail;
        }
    }

    public function getDeliveryId($idZone, $mpShippingId, $idRange, $shippingMethod)
    {
        if ($shippingMethod == 1) {
            $deliverIdDetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT `id`  FROM `'._DB_PREFIX_.'mp_shipping_delivery` WHERE mp_shipping_id = '.(int) $mpShippingId.' AND mp_id_range_weight = '.(int) $idRange.' AND id_zone = '.(int) $idZone);
        } else {
            $deliverIdDetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT `id`  FROM `'._DB_PREFIX_.'mp_shipping_delivery` WHERE mp_shipping_id = '.(int) $mpShippingId.' AND mp_id_range_price = '.(int) $idRange.' AND id_zone = '.(int) $idZone);
        }

        if (empty($deliverIdDetail)) {
            return false;
        } else {
            return $deliverIdDetail['id'];
        }
    }
}
