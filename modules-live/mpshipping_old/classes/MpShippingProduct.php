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

class Mpshippingproduct extends ObjectModel
{
    public $id;
    public $mp_product_id;
    public $width;
    public $height;
    public $depth;
    public $weight;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'mp_shipping_product',
        'primary' => 'id',
        'fields' => array(
            'mp_product_id' => array('type' => self::TYPE_INT ,'validate' => 'isUnsignedInt', 'required' => true),
            'width' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'height' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'depth' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'weight' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public function findWeightInfoByMpProID($mp_product_id)
    {
        $mp_product_wet_detail = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_product` WHERE `mp_product_id` = '.(int) $mp_product_id);

        if (empty($mp_product_wet_detail)) {
            return false;
        } else {
            return $mp_product_wet_detail;
        }
    }

    public static function deletePsProductCarrier($ps_product_id)
    {
        return Db::getInstance()->delete('product_carrier', 'id_product = '.(int) $ps_product_id);
    }
}
