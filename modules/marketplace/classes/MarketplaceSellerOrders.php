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

class MarketplaceSellerOrders extends ObjectModel
{
    public $id;
    public $seller_customer_id; /** @var id_customer of marketplace seller */
    public $seller_shop;
    public $total_earn_ti; /** @var total earn of shop with tax */
    public $total_earn_te; /** @var  total earn of shop without tax */
    public $total_admin_commission; /** @var total admin commission */
    public $total_admin_tax; /** @var total admin tax */
    public $total_seller_amount; /** @var  total seller amount */
    public $total_seller_tax; /** @var total seller tax */
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'marketplace_seller_orders',
        'primary' => 'id',
        'fields' => array(
            'seller_customer_id' => array('type' => self::TYPE_INT),
            'seller_shop' => array('type' => self::TYPE_STRING),
            'total_earn_ti' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_earn_te' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_admin_commission' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_admin_tax' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_seller_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_seller_tax' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public static function updateMarketplaceSellerOrder($seller_customer_id, $all_product_info)
    {
        $order_details = self::isSellerOrderExist($seller_customer_id);
        if ($order_details) {
            $obj_mpsellerorder = new self($order_details['id']);
            $obj_mpsellerorder->total_earn_ti = round(($all_product_info['total_earn_ti'] + $order_details['total_earn_ti']), 6);
            $obj_mpsellerorder->total_earn_te = round(($all_product_info['total_earn_te'] + $order_details['total_earn_te']), 6);
            $obj_mpsellerorder->total_admin_commission = round(($all_product_info['total_admin_commission'] + $order_details['total_admin_commission']), 6);
            $obj_mpsellerorder->total_admin_tax = round(($all_product_info['total_admin_tax'] + $order_details['total_admin_tax']), 6);
            $obj_mpsellerorder->total_seller_amount = round(($all_product_info['total_seller_amount'] + $order_details['total_seller_amount']), 6);
            $obj_mpsellerorder->total_seller_tax = round(($all_product_info['total_seller_tax'] + $order_details['total_seller_tax']), 6);
            $obj_mpsellerorder->save();
            if ($obj_mpsellerorder->id) {
                return $obj_mpsellerorder->id;
            }
        } else {
            $obj_mp_seller = new SellerInfoDetail();
            $mp_seller_info = $obj_mp_seller->getSellerDetailsByCustomerId($seller_customer_id);
            $obj_mpsellerorder = new self();
            $obj_mpsellerorder->seller_customer_id = $seller_customer_id;
            $obj_mpsellerorder->seller_shop = $mp_seller_info['shop_name_unique'];
            $obj_mpsellerorder->total_earn_ti = round($all_product_info['total_earn_ti'], 6);
            $obj_mpsellerorder->total_earn_te = round($all_product_info['total_earn_te'], 6);
            $obj_mpsellerorder->total_admin_commission = round($all_product_info['total_admin_commission'], 6);
            $obj_mpsellerorder->total_admin_tax = round($all_product_info['total_admin_tax'], 6);
            $obj_mpsellerorder->total_seller_amount = round($all_product_info['total_seller_amount'], 6);
            $obj_mpsellerorder->total_seller_tax = round($all_product_info['total_seller_tax'], 6);
            $obj_mpsellerorder->save();
            if ($obj_mpsellerorder->id) {
                return $obj_mpsellerorder->id;
            }
        }

        return false;
    }

    /**
     * changed as per marketplace commission calc table for preventing order data even
     * if seller delete anyproduct which has ordered by any buyer.
     *
     * @param [type] $id_lang     [description]
     * @param [type] $id_customer [description]
     * @param bool   $limit       [got getting 5 top most order for dashboard page]
     *
     * @return [type] [description]
     */
    public function mpSellerOrders($id_lang, $id_customer, $top_five = false)
    {
        return Db::getInstance()->executeS('SELECT ordd.`id_order_detail`AS `id_order_detail`,
			ordd.`product_name` AS `ordered_product_name`,
			ordd.`product_price` AS product_price,
			ordd.`product_quantity` AS qty,
			ordd.`id_order` AS id_order,
			ord.`id_customer` AS buyer_id_customer,
			ord.`total_paid` AS total_paid,
			ord.`payment` AS payment_mode,
			ord.`reference` AS reference,
			cus.`firstname` AS seller_firstname,
			cus.`lastname` AS seller_lastname,
			cus.`email` AS seller_email,
			ord.`date_add`,ords.`name` AS order_status,
			ord.`id_currency` AS `id_currency`
			FROM `'._DB_PREFIX_.'marketplace_seller_orders_details` msod
			JOIN `'._DB_PREFIX_.'order_detail` ordd ON (ordd.`product_id` = msod.`product_id` AND ordd.`id_order` = msod.`id_order`)
			JOIN `'._DB_PREFIX_.'orders` ord ON (ordd.`id_order` = ord.`id_order`)
			JOIN `'._DB_PREFIX_.'marketplace_seller_info` msi ON (msi.`seller_customer_id` = msod.`seller_customer_id`)
			JOIN `'._DB_PREFIX_.'customer` cus ON (msi.`seller_customer_id` = cus.`id_customer`)
			JOIN `'._DB_PREFIX_.'order_state_lang` ords ON (ord.`current_state` = ords.`id_order_state`)
			WHERE ords.id_lang = '.$id_lang.' AND cus.`id_customer` = '.$id_customer.'
			GROUP BY ordd.`id_order` ORDER BY ordd.`id_order` DESC '.($top_five ? 'LIMIT 5' : ''));
    }

    public function getTotalOrder($id_order, $id_customer_seller)
    {
        return Db::getInstance()->getValue(
            'SELECT SUM(price_ti) as `totalorder`
            FROM `'._DB_PREFIX_.'marketplace_seller_orders_details`
            WHERE `id_order` = '.(int) $id_order.'
            AND `seller_customer_id` = '.(int) $id_customer_seller
        );
    }

    public static function getSellerTotalEarn($id_customer_seller)
    {
        return Db::getInstance()->getValue(
            'SELECT `total_earn_ti` FROM `'._DB_PREFIX_.'marketplace_seller_orders`
            WHERE `seller_customer_id`='.(int) $id_customer_seller
        );
    }

    public static function getAdminTotalCommissionBySeller($id_customer_seller)
    {
        return Db::getInstance()->getValue(
            'SELECT `total_admin_commission` FROM `'._DB_PREFIX_.'marketplace_seller_orders`
            WHERE `seller_customer_id`='.(int) $id_customer_seller
        );
    }
    
    public static function getSellerTotalAmountBySeller($id_customer_seller)
    {
        return Db::getInstance()->getValue(
            'SELECT `total_seller_amount` FROM `'._DB_PREFIX_.'marketplace_seller_orders`
            WHERE `seller_customer_id`='.(int) $id_customer_seller
        );
    }

    public static function isSellerOrderExist($id_customer_seller)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_orders`
            WHERE `seller_customer_id` = '.(int) $id_customer_seller
        );
    }

    public static function getCurrencyConversionRate($id_currency_from, $id_currency_to)
    {
        $conversionRate = 1;
        if ($id_currency_to != $id_currency_from) {
            $currencyFrom = new Currency((int) $id_currency_from);
            $conversionRate /= $currencyFrom->conversion_rate;
            $currencyTo = new Currency((int) $id_currency_to);
            $conversionRate *= $currencyTo->conversion_rate;
        }

        return $conversionRate;
    }

    /**
     * [if seller update their unique shop name then that name will also update in seller orders seller_shop field]
     * @param  [type] $seller_customer_id      [seller customer id]
     * @param  [type] $shop_name_unique [seller unique shop name]
     * @return [type]                   [description]
     */
    public static function updateOrderShopUniqueBySellerCustomerId($seller_customer_id, $shop_name_unique)
    {
        $order_details = self::isSellerOrderExist($seller_customer_id);
        if ($order_details) {
            return Db::getInstance()->update('marketplace_seller_orders', array('seller_shop' => $shop_name_unique), 'seller_customer_id = '. (int) $seller_customer_id);
        } else {
            return true;
        }
    }
}
