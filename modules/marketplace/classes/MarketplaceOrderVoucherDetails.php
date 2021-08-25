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

class MarketplaceOrderVoucherDetails extends ObjectModel
{
    public $id;
    public $order_id;
    public $seller_id;
    public $voucher_name;
    public $voucher_value;

    public static $definition = array(
        'table' => 'marketplace_order_voucher_details',
        'primary' => 'id',
        'fields' => array(
            'order_id' => array('type' => self::TYPE_INT, 'required' => true),
            'seller_id' => array('type' => self::TYPE_INT, 'required' => true),
            'voucher_name' => array('type' => self::TYPE_STRING, 'required' => true),
            'voucher_value' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
        ),
    );

    public static function getVoucherDetailsForSeller($order_id, $seller_id)
    {
        $seller_info = Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'marketplace_order_voucher_details` WHERE `order_id` = '.(int) $order_id.' AND `seller_id` = '. (int) $seller_id);

        if ($seller_info) {
            return $seller_info;
        }

        return false;
    }

}