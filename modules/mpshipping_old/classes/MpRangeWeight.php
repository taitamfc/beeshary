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

class Mprangeweight extends ObjectModel
{
    public $id;
    public $mp_shipping_id;
    public $delimiter1;
    public $delimiter2;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'mp_range_weight',
        'primary' => 'id',
        'fields' => array(
            'mp_shipping_id' => array('type' => self::TYPE_INT, 'required' => true),

            'delimiter1' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'delimiter2' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public function getAllRangeAccordingToShippingId()
    {
        $is_range = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id` as id_range,delimiter1,delimiter2 FROM '._DB_PREFIX_.'mp_range_weight WHERE `mp_shipping_id` = '.(int) $this->mp_shipping_id);

        if (empty($is_range)) {
            return false;
        } else {
            return $is_range;
        }
    }

    public static function getWeightRangeLastId($mp_shipping_id)
    {
        $id_range = Db::getInstance()->getValue('SELECT `id` FROM '._DB_PREFIX_.'mp_range_weight where mp_shipping_id = '.(int) $mp_shipping_id.' ORDER BY `id` DESC');
        if ($id_range) {
            return $id_range;
        }

        return 0;
    }

    public function isRangeInTableByShippingId()
    {
        $is_range = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'mp_range_weight WHERE mp_shipping_id = '.(int) $this->mp_shipping_id.' AND delimiter1 = '.$this->delimiter1.' AND delimiter2 = '.$this->delimiter2);

        if (empty($is_range)) {
            return false;
        } else {
            return $is_range;
        }
    }

    public function findRangeIdBetweenDelimetr($mp_shipping_id, $weight)
    {
        $mp_range_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT `id` FROM `'._DB_PREFIX_.'mp_range_weight` WHERE mp_shipping_id = '.(int) $mp_shipping_id.' AND '.$weight.' >= `delimiter1` AND '.$weight.' < `delimiter2` ');

        if (empty($mp_range_id)) {
            return false;
        } else {
            return $mp_range_id['id'];
        }
    }

    public function deleteRangeByMpshippingId($mp_shipping_id)
    {
        return Db::getInstance()->delete('mp_range_weight', 'mp_shipping_id='.$mp_shipping_id);
    }
}
