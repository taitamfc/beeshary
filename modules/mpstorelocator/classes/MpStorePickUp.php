<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MpStorePickUp extends ObjectModel
{
    public $id_cart;
    public $id_order;

    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'mp_store_pickup',
        'primary' => 'id_store_pickup',
        'fields' => array(
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'copy_post' => false),
        ),
    );

    public static function getStorePickUpId($idCart)
    {
        if ($idCart) {
            $sql = 'SELECT `id_store_pickup`
                FROM `'._DB_PREFIX_.'mp_store_pickup`
                WHERE `id_cart` = '.(int)$idCart;
            return Db::getInstance()->getValue($sql);
        }
    }
}
