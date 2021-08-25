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

class MarketplaceAdminTotalShipping extends ObjectModel
{
    public $id;
    public $order_id;
    public $order_reference;
    public $shipping_amount;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'marketplace_admin_total_shipping',
        'primary' => 'id',
        'fields' => array(
            'order_id' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'order_reference' => array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 9),
            'shipping_amount' => array('type' => self::TYPE_FLOAT,'validate' => 'isPrice', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
        ),
    );

    public function getOrderByOrderId($id_order)
    {
        $result = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'marketplace_admin_total_shipping where `order_id` = '.(int) $id_order);
        if ($result) {
            return $result;
        }

        return false;
    }

    public static function getTotalShippingCost()
    {
        $result = Db::getInstance()->getValue('SELECT SUM(`shipping_amount`) as shipping FROM '._DB_PREFIX_.'marketplace_admin_total_shipping where 1');
        if ($result) {
            return $result;
        }

        return false;
    }

    public static function getTotalShippingCostWithPaymentAccepted()
    {
        $result = Db::getInstance()->getValue('SELECT SUM(mstshp.`shipping_amount`) as shipping FROM '._DB_PREFIX_.'marketplace_admin_total_shipping mstshp 
            LEFT JOIN '._DB_PREFIX_.'orders ordr on (mstshp.`order_id` = ordr.`id_order`) 
            where ordr.`invoice_number` > 0');
        if ($result) {
            return $result;
        }

        return false;
    }
}
