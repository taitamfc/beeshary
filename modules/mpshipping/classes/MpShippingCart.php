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

class MpShippingCart extends ObjectModel
{
    public $id;
    public $id_ps_cart;
    public $id_ps_carrier;
    public $extra_cost;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'mp_shipping_cart',
        'primary' => 'id',
        'fields' => array(
            'id_ps_cart' => array('type' => self::TYPE_INT, 'required' => true),
            'id_ps_carrier' => array('type' => self::TYPE_INT, 'required' => true),
            'extra_cost' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public function isAvailable($idPsCarrier, $idPsCart)
    {
        $isAvailable = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_cart` WHERE `id_ps_carrier` = '.(int) $idPsCarrier.' AND id_ps_cart = '.(int) $idPsCart);

        if (empty($isAvailable)) {
            return false;
        } else {
            return $isAvailable;
        }
    }
}
