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

class MarketplaceShippingInfo extends ObjectModel
{
    public $id;
    public $order_id;
    public $shipping_description;
    public $shipping_date;

    public static $definition = array(
        'table' => 'marketplace_shipping',
        'primary' => 'id',
        'fields' => array(
            'order_id' => array('type' => self::TYPE_INT,'required' => true),
            'shipping_description' => array('type' => self::TYPE_STRING),
            'shipping_date' => array('type' => self::TYPE_DATE),
        ),
    );

    public function getShippingDetailsByOrderId($idOrder)
    {
        if (isset($idOrder)) {
            $mpShipping = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_shipping`
																	WHERE `order_id`='.$idOrder);
            if ($mpShipping) {
                return $mpShipping;
            }
        }

        return false;
    }
}
