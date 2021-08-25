<?php
/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkMpBookingOrder extends ObjectModel
{
    public $id_cart;
    public $id_order;
    public $id_mp_product;
    public $id_product;
    public $booking_type;
    public $quantity;
    public $date_from;
    public $date_to;
    public $time_from;
    public $time_to;
    public $product_real_price_tax_excl;
    public $product_real_price_tax_incl;
    public $range_feature_price_tax_incl;
    public $range_feature_price_tax_excl;
    public $total_order_tax_excl;
    public $total_order_tax_incl;
    public $consider_last_date;

    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_mp_booking_order',
        'primary' => 'id_booking_order',
        'fields' => array(
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_mp_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'booking_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'quantity' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_from' => array('type' => self::TYPE_DATE),
            'date_to' => array('type' => self::TYPE_DATE),
            'time_from' => array('type' => self::TYPE_STRING),
            'time_to' => array('type' => self::TYPE_STRING),
            'product_real_price_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'product_real_price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'range_feature_price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'range_feature_price_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_order_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_order_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'consider_last_date' => array('type' => self::TYPE_INT),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function getBookingProductOrderInfo($id_product, $id_order)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_order`
            WHERE `id_product`='.(int) $id_product.' AND `id_order`='.(int) $id_order
        );
    }

    public function getOrderDetailsProductInfo($id_order, $id_product)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'order_detail`
            WHERE `product_id`='.(int) $id_product.' AND `id_order`='.(int) $id_order
        );
    }

    public function updatePsOrderDetailsColumns($id_order, $id_product, $update_info = array())
    {
        return Db::getInstance()->update(
            'order_detail',
            $update_info,
            '`product_id`='.(int) $id_product.' AND `id_order`='.(int) $id_order
        );
    }

    public function getProductOrderedQuantityInDateRange($id_product, $date_from, $date_to, $incl_customer_cart = 0)
    {
        $orderedBookings = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_order`
            WHERE `id_product`='.(int) $id_product.'
            AND IF(consider_last_date=1, `date_from` <= \''.pSql($date_to).'\', `date_from` < \''.pSql($date_to).'\')
            AND IF(consider_last_date=1, `date_to`>= \''.pSql($date_from).'\', `date_to` > \''.pSql($date_from).'\')'
        );
        if ($incl_customer_cart) {
            if (isset(Context::getContext()->cart->id) && ($idCart = Context::getContext()->cart->id)) {
                $totalDaySeconds = 24 * 60 * 60;
                if (!Configuration::get('WK_CONSIDER_DATE_TO')) {
                    $date_to = date('Y-m-d', strtotime($date_to) - $totalDaySeconds);
                }
                if ($cartBookings = Db::getInstance()->executeS(
                    'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_cart` bc
                    WHERE `id_cart`='.(int) $idCart.' AND `id_product`='.(int) $id_product.' AND `date_from` <= \''.pSql($date_to).'\'
                    AND IF(bc.consider_last_date = 1, `date_to` >= \''.pSql($date_from).'\', `date_to` > \''.
                    pSql($date_from).'\')'
                )) {
                    $orderedBookings = array_merge($orderedBookings, $cartBookings);
                }
            }
        }

        $totalDaySeconds = 24 * 60 * 60;
        $bookedCount = 0;
        if ($orderedBookings) {
            $dateCovered = array();
            foreach ($orderedBookings as $booking) {
                $dateClashed = 0;
                if ($booking['consider_last_date']) {
                    $traverseToDate = strtotime($booking['date_to']);
                } else {
                    $traverseToDate = strtotime($booking['date_to']) - $totalDaySeconds;
                }
                for ($date = strtotime($booking['date_from']); $date <= $traverseToDate; $date = ($date + $totalDaySeconds)) {
                    if (!count($dateCovered)) {
                        $bookedCount = $booking['quantity'];
                    }
                    if (!in_array($date, $dateCovered)) {
                        $dateCovered[] = $date;
                    } else {
                        $dateClashed = 1;
                    }
                }
                if ($dateClashed) {
                    $bookedCount += $booking['quantity'];
                } elseif ($booking['quantity'] > $bookedCount) {
                    $bookedCount = $booking['quantity'];
                }
            }
        }
        return $bookedCount;
    }

    public function getProductTimeSlotOrderedQuantity($id_product, $date, $time_from, $time_to, $incl_customer_cart = 0)
    {
        $date = date('Y-m-d', strtotime($date));
        $orderedQty = Db::getInstance()->getValue(
            'SELECT SUM(`quantity`) FROM `'._DB_PREFIX_.'wk_mp_booking_order`
            WHERE `id_product`='.(int) $id_product.' AND `date_from` = \''.pSql($date).'\'
            AND `time_from` = \''.pSql($time_from).'\'
            AND `time_to` = \''.pSql($time_to).'\''
        );
        if ($incl_customer_cart) {
            if (isset(Context::getContext()->cart->id) && ($idCart = Context::getContext()->cart->id)) {
                $cartQty = Db::getInstance()->getValue(
                    'SELECT SUM(`quantity`) FROM `'._DB_PREFIX_.'wk_mp_booking_cart`
                    WHERE `id_cart`='.(int) $idCart.'
                    AND `id_product`='.(int) $id_product.'
                    AND `date_from` = \''.pSql($date).'\'
                    AND `time_from` = \''.pSql($time_from).'\'
                    AND `time_to` = \''.pSql($time_to).'\''
                );
                $orderedQty += $cartQty;
            }
        }
        return $orderedQty;
    }

    public function getSellerProductDetailsByIdOrder($idOrder, $idProduct, $sellerCustomerId = 0)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_order_detail` WHERE `id_order` = '.(int) $idOrder.
        ' AND product_id = '.(int) $idProduct;
        if ($sellerCustomerId) {
            $sql .= ' AND seller_customer_id = '.(int) $sellerCustomerId;
        }
        return Db::getInstance()->getRow($sql);
    }
}
