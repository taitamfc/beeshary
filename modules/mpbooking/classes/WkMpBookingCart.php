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

class WkMpBookingCart extends ObjectModel
{
    public $id_cart;
    public $id_product;
    public $booking_type;
    public $quantity;
    public $date_from;
    public $date_to;
    public $time_from;
    public $time_to;
    public $consider_last_date;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_mp_booking_cart',
        'primary' => 'id_booking_cart',
        'fields' => array(
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'booking_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'quantity' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_from' => array('type' => self::TYPE_DATE),
            'date_to' => array('type' => self::TYPE_DATE),
            'time_from' => array('type' => self::TYPE_STRING),
            'time_to' => array('type' => self::TYPE_STRING),
            'consider_last_date' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function getBookingProductCartInfo($id_product, $id_cart)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_cart`
            WHERE `id_product`='.(int) $id_product.' AND `id_cart`='.(int) $id_cart
        );
    }

    public function getProductLastEnteredCartRow($id_product, $id_cart)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_cart`
            WHERE `id_product`='.(int) $id_product.' AND `id_cart`='.(int) $id_cart.' ORDER BY `id_booking_cart` DESC'
        );
    }

    public function getCartInfoByProduct($id_product, $id_cart)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_cart`
            WHERE `id_product`='.(int) $id_product.' AND `id_cart`='.(int) $id_cart.' ORDER BY `id_booking_cart` DESC'
        );
    }

    public function getProductBookingInfoInDateRange($id_product, $date_from, $date_to)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_cart`
            WHERE `id_product`='.(int) $id_product.' AND `date_from` <= \''.pSql($date_to).'\' AND `date_to` >= \''.
            pSql($date_from).'\''
        );
    }

    public function getProductBookingQuantityInDateRange($id_product, $date_from, $date_to)
    {
        $totalDaySeconds = 24 * 60 * 60;
        if (!Configuration::get('WK_MP_CONSIDER_DATE_TO')) {
            $date_to = date('Y-m-d', strtotime($date_to) - $totalDaySeconds);
        }
        $cartBookings = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_cart` bc
            WHERE `id_product`='.(int) $id_product.' AND `date_from` <= \''.pSql($date_to).'\'
            AND IF(bc.consider_last_date = 1, `date_to` >= \''.pSql($date_from).'\', `date_to` > \''.
            pSql($date_from).'\')'
        );
        $bookedCount = 0;
        if ($cartBookings) {
            $dateCovered = array();
            foreach ($cartBookings as $booking) {
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

    public function cartProductEntryExistsForDateRange($id_cart, $id_product, $date_from, $date_to)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_cart`
            WHERE `id_cart`='.(int) $id_cart.' AND `id_product`='.(int) $id_product.'
            AND `date_from` = \''.pSql($date_from).'\' AND `date_to` = \''.pSql($date_to).'\''
        );
    }

    public function cartProductEntryExistsForTimeSlot($id_cart, $id_product, $date, $time_from, $time_to)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_cart`
            WHERE `id_cart`='.(int) $id_cart.' AND `id_product`='.(int) $id_product.'
            AND `date_from` = \''.pSql(date('Y-m-d', strtotime($date))).'\'
            AND `time_from` = \''.pSql($time_from).'\'
            AND `time_to` = \''.pSql($time_to).'\''
        );
    }

    public function getProductTimeSlotBookedQuantity($id_product, $date, $time_from, $time_to)
    {
        $date = date('Y-m-d', strtotime($date));
        return Db::getInstance()->getValue(
            'SELECT SUM(`quantity`) FROM `'._DB_PREFIX_.'wk_mp_booking_cart`
            WHERE `id_product`='.(int) $id_product.'
            AND `date_from` = \''.pSql($date).'\'
            AND `time_from` = \''.pSql($time_from).'\'
            AND `time_to` = \''.pSql($time_to).'\''
        );
    }

    public function getCartInfo($id_cart)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_booking_cart` WHERE `id_cart`='.(int) $id_cart
        );
    }

    public function deleteBookingProductCartByIdProductIdCart($id_product, $id_cart)
    {
        return Db::getInstance()->delete(
            'wk_mp_booking_cart',
            '`id_product`='.(int) $id_product.' AND `id_cart`='.(int) $id_cart
        );
    }

    public function getBookingProductDateWiseAvailabilityAndRates($id_booking_product_info, $date)
    {
        $bookingProductInformation = (array)new WkMpBookingProductInformation($id_booking_product_info);
        $bookingInfo = array();
        if ($bookingProductInformation) {
            $adminCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
            $bookingInfo['booking_type'] = $bookingProductInformation['booking_type'];
            $bookingInfo['booking_info'] = array();
            $objBookingOrders = new WkMpBookingOrder();

            if ($bookingProductInformation['booking_type'] == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE) {
                $dateTo = date('Y-m-d', strtotime("+1 day", strtotime($date)));
                $bookedQuantity = $objBookingOrders->getProductOrderedQuantityInDateRange(
                    $bookingProductInformation['id_mp_product'],
                    $bookingProductInformation['id_product'],
                    $date,
                    $dateTo
                );
                $maxAvailableQuantity = $bookingProductInformation['quantity'] - $bookedQuantity;

                $bookingPrice = WkMpBookingProductFeaturePricing::getBookingProductTotalPrice(
                    $id_booking_product_info,
                    $date,
                    $date
                );
                $bookingInfo['booking_info']['price'] = $bookingPrice;
                $bookingInfo['booking_info']['available_qty'] = ($maxAvailableQuantity > 0) ? $maxAvailableQuantity : 0;
                $bookingInfo['booking_info']['booked_qty'] = $bookedQuantity;
                $bookingInfo['booking_info']['price']['total_price_tax_incl_formatted'] = Tools::displayPrice(
                    $bookingInfo['booking_info']['price']['total_price_tax_incl'],
                    $adminCurrency
                );
                $bookingInfo['booking_info']['price']['total_price_tax_excl_formatted'] = Tools::displayPrice(
                    $bookingInfo['booking_info']['price']['total_price_tax_excl'],
                    $adminCurrency
                );
                if (isset($bookingInfo['booking_info']['available_qty'])
                    && $bookingInfo['booking_info']['available_qty']
                ) {
                    $bookingInfo['calendarCssClass'] = 'booking_available';
                } else {
                    $bookingInfo['calendarCssClass'] = 'booking_unavailable';
                }
            } elseif ($bookingProductInformation['booking_type'] == WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_TIME_SLOT) {
                $objBookingTimeSlots = new WkMpBookingProductTimeSlotPrices();
                $bookingTimeSlots = $objBookingTimeSlots->getBookingProductTimeSlotsOnDate(
                    $id_booking_product_info,
                    $date,
                    true
                );
                $anySlotAvail = false;

                if ($bookingTimeSlots) {
                    foreach ($bookingTimeSlots as &$slot) {
                        $slotBookedQty = $objBookingOrders->getProductTimeSlotOrderedQuantity(
                            $bookingProductInformation['id_product'],
                            $date,
                            $slot['time_slot_from'],
                            $slot['time_slot_to']
                        );
                        if (!$slotBookedQty) {
                            $slotBookedQty = 0;
                        }
                        $slotAvailQty = $bookingProductInformation['quantity'] - $slotBookedQty;
                        $slot['available_qty'] = $slotAvailQty > 0 ? $slotAvailQty : 0;
                        $slot['booked_qty'] = $slotBookedQty;
                        $slot['price_formatted'] = Tools::displayPrice($slot['price'], $adminCurrency);
                        if ($slot['available_qty']) {
                            $anySlotAvail = 1;
                        }
                    }
                    $bookingInfo['booking_info'] = $bookingTimeSlots;
                }
                if ($anySlotAvail) {
                    $bookingInfo['calendarCssClass'] = 'booking_available';
                } else {
                    $bookingInfo['calendarCssClass'] = 'booking_unavailable';
                }
            }
        }
        return $bookingInfo;
    }

    public function deleteCurrentCustomerCarts()
    {
        $conditionBookingCart = '`id_cart` NOT IN (SELECT `id_cart` FROM `'._DB_PREFIX_.'wk_mp_booking_order`)';
        $conditionPrestashopCart = '`id_cart` NOT IN (SELECT `id_cart` FROM `'._DB_PREFIX_.'orders`)
        AND `id_product` IN (SELECT `id_product` FROM `'._DB_PREFIX_.'wk_mp_booking_product_info`)';

        $deleteModuleBookingCartProducts =  Db::getInstance()->delete(
            'wk_mp_booking_cart',
            $conditionBookingCart
        );

        $deleteCoreBookingCartProducts =  Db::getInstance()->delete(
            'cart_product',
            $conditionPrestashopCart
        );
        return $deleteCoreBookingCartProducts && $deleteModuleBookingCartProducts;
    }
}
