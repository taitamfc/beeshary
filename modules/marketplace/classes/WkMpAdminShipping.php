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

class WkMpAdminShipping extends ObjectModel
{
    public $order_id;
    public $order_reference;
    public $shipping_amount;
    public $admin_earn;
    public $seller_earn;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_mp_admin_shipping',
        'primary' => 'id_wk_mp_admin_shipping',
        'fields' => array(
            'order_id' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'order_reference' => array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 9),
            'shipping_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'admin_earn' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'seller_earn' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
        ),
    );

    /**
     * Get Order Shipping Detail by using order id
     *
     * @param int $idOrder Order ID
     *
     * @return array
     */
    public function getOrderByIdOrder($idOrder)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_admin_shipping`
            WHERE `order_id` = ' . (int) $idOrder
        );
    }

    /**
     * Get Total Shipping Cost by adding all shipping cost (Only seller's orders)
     *
     * @return float
     */
    public static function getTotalShippingCost()
    {
        $result = Db::getInstance()->getValue(
            'SELECT SUM(`shipping_amount`) as shipping FROM `' . _DB_PREFIX_ . 'wk_mp_admin_shipping`'
        );
        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Get Total Shipping Cost by adding all shipping cost (Only seller's orders)
     *
     * @param int $idCurrency Currency ID
     *
     * @return array
     */
    public static function getTotalShippingByIdCurrency($idCurrency)
    {
        $sql = 'SELECT
                    o.`id_currency`,
                    SUM(`admin_earn`) as admin_shipping,
                    SUM(`seller_earn`) as seller_shipping
                    FROM ' . _DB_PREFIX_ . 'wk_mp_admin_shipping wkshp
                    LEFT JOIN ' . _DB_PREFIX_ . 'orders o on (o.`id_order` = wkshp.`order_id`)
                    WHERE o.`id_currency` = ' . (int) $idCurrency . '
                    AND o.`id_order` IN (SELECT wkt.`id_transaction` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_transaction_history` wkt WHERE wkt.`status`= ' . (int) WkMpSellerTransactionHistory::MP_SELLER_ORDER_STATUS . ')';

        if (Configuration::get('WK_MP_COMMISSION_DISTRIBUTE_ON') == 1) {
            // Payment accepted
            $sql .= ' AND (SELECT `id_order_state` FROM `' . _DB_PREFIX_ . 'order_history` oh WHERE oh.`id_order` = o.`id_order` AND oh.`id_order_state`=' . (int) Configuration::get('PS_OS_PAYMENT') . ' LIMIT 1)';
        }

        $totalShipping = Db::getInstance()->getRow($sql);
        if ($totalShipping) {
            //Deduct refunded shipping amount
            $refundedShipping = Db::getInstance()->getRow(
                'SELECT SUM(wkt.`admin_shipping`) as admin_shipping, SUM(wkt.`seller_shipping`) as seller_shipping
                FROM `' . _DB_PREFIX_ . 'wk_mp_seller_transaction_history` wkt
                WHERE wkt.`id_currency` = ' . (int) $idCurrency . '
                AND wkt.`status`= ' . (int) WkMpSellerTransactionHistory::MP_ORDER_REFUND_STATUS . ''
            );
            if ($refundedShipping) {
                $totalShipping['admin_shipping'] = $totalShipping['admin_shipping'] - $refundedShipping['admin_shipping'];
                $totalShipping['seller_shipping'] = $totalShipping['seller_shipping'] - $refundedShipping['seller_shipping'];
            }
        }

        return $totalShipping;
    }

    /**
     * Get Total Shipping cost by adding all shipping cost only those order
     * which are payment accepted status (Only Seller's Orders)
     *
     * @return float
     */
    public static function getTotalShippingCostWithPaymentAccepted()
    {
        //This function is deprecated
        $result = Db::getInstance()->getValue(
            'SELECT SUM(mstshp.`shipping_amount`) as shipping
            FROM `' . _DB_PREFIX_ . 'wk_mp_admin_shipping` mstshp
            LEFT JOIN ' . _DB_PREFIX_ . 'orders ordr on (mstshp.`order_id` = ordr.`id_order`)
            WHERE ordr.`id_order` IN (SELECT wkt.`id_transaction` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_transaction_history` wkt WHERE wkt.`status`= ' . (int) WkMpSellerTransactionHistory::MP_SELLER_ORDER_STATUS . ')
            AND (SELECT `id_order_state` FROM `' . _DB_PREFIX_ . 'order_history` oh WHERE oh.`id_order` = ordr.`id_order` AND oh.`id_order_state`=' . (int) Configuration::get('PS_OS_PAYMENT') . ' LIMIT 1)'
        );
        if ($result) {
            return $result;
        }

        return false;
    }

    public static function checkSellerShippingDistributionExist()
    {
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping_distribution`');
    }

    public static function addingAdminShipping($idOrder, $sellerSplitAmount = false, $cart = false)
    {
        $isSeller = false;
        $order = new Order($idOrder);

        $products = $order->getProducts();
        foreach ($products as $product) {
            if (WkMpSellerProduct::getSellerProductByPsIdProduct($product['product_id'])) {
                $totalShipping = $order->total_shipping_tax_incl;
                $isSeller = true;
            }
        }

        if (Db::getInstance()->getValue('SELECT `free_shipping` FROM `' . _DB_PREFIX_ . 'order_cart_rule` WHERE `id_order` = ' . (int) $idOrder)) {
            $totalShipping = 0;
        }

        if ($isSeller) {
            $adminEarning = $totalShipping;
            $sellerEarning = 0;
            $distributorShippingCost = Hook::exec('actionShippingDistribution', array('seller_splitDetail' => $sellerSplitAmount, 'order' => $order), null, true);
            if ($distributorShippingCost) {
                foreach ($distributorShippingCost as $module) {
                    $adminEarning = 0;
                    if ($module) {
                        foreach ($module as $sellerIdCustomer => $shippingAmount) {
                            if ($sellerIdCustomer == 'admin') {
                                $adminEarning = $shippingAmount;
                            } else {
                                Db::getInstance()->insert(
                                    'wk_mp_seller_shipping_distribution',
                                    array(
                                        'order_id' => (int) $idOrder,
                                        'order_reference' => $order->reference,
                                        'seller_customer_id' => (int) $sellerIdCustomer,
                                        'seller_earn' => Tools::ps_round($shippingAmount, 6),
                                    )
                                );
                                $sellerEarning += $shippingAmount;
                            }
                        }
                    }
                }
            } else {
                if (Module::isEnabled('mpshipping') && $sellerSplitAmount && $cart && $order) {
                    require_once _PS_MODULE_DIR_ . '/mpshipping/classes/MpShippingInclude.php';
                    $distributorShippingCost = MpShippingMethod::getShippingDistributionData($sellerSplitAmount, $cart, $order);
                    if ($distributorShippingCost) {
                        $adminEarning = 0;
                        foreach ($distributorShippingCost as $sellerIdCustomer => $shippingAmount) {
                            if ($sellerIdCustomer == 'admin') {
                                $adminEarning = $shippingAmount;
                            } else {
                                Db::getInstance()->insert(
                                    'wk_mp_seller_shipping_distribution',
                                    array(
                                        'order_id' => (int) $idOrder,
                                        'order_reference' => pSQL($order->reference),
                                        'seller_customer_id' => (int) $sellerIdCustomer,
                                        'seller_earn' => (float) Tools::ps_round($shippingAmount, 6),
                                    )
                                );

                                $sellerEarning += $shippingAmount;
                            }
                        }
                    }
                }
            }

            // adding shipping to marketplace admin shipping table
            $objAdminShipping = new self();
            $objAdminShipping->order_id = $idOrder;
            $objAdminShipping->order_reference = $order->reference;
            $objAdminShipping->shipping_amount = Tools::ps_round($totalShipping, 6);
            $objAdminShipping->admin_earn = Tools::ps_round($adminEarning, 6);
            $objAdminShipping->seller_earn = Tools::ps_round($sellerEarning, 6);
            $objAdminShipping->save();
        }
    }

    public static function getSellerShippingByIdOrder($idOrder, $idSellerCustomer)
    {
        return Db::getInstance()->getValue(
            'SELECT `seller_earn` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping_distribution`
            WHERE `order_id` = ' . (int) $idOrder . ' AND `seller_customer_id` = ' . (int) $idSellerCustomer
        );
    }

    public static function getTotalSellerShipping($idSellerCustomer, $idCurrency)
    {
        $sql = 'SELECT SUM(wkshpdist.`seller_earn`) as seller_shipping,
                o.`id_currency` FROM `' . _DB_PREFIX_ . 'wk_mp_admin_shipping` wkshp
                INNER JOIN ' . _DB_PREFIX_ . 'orders o on (o.`id_order` = wkshp.`order_id`)
                INNER JOIN ' . _DB_PREFIX_ . 'wk_mp_seller_shipping_distribution wkshpdist on (wkshp.`order_id` = wkshpdist.`order_id`)
                WHERE wkshpdist.`seller_customer_id` = ' . (int) $idSellerCustomer . '
                AND o.`id_currency` = ' . (int) $idCurrency . '
                AND o.`id_order` IN (SELECT wkt.`id_transaction` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_transaction_history` wkt WHERE wkt.`status`= ' . (int) WkMpSellerTransactionHistory::MP_SELLER_ORDER_STATUS . '
                AND wkt.`id_customer_seller`= '.(int) $idSellerCustomer.')';

        if (Configuration::get('WK_MP_COMMISSION_DISTRIBUTE_ON') == 1) {
            // Payment accepted
            $sql .= ' AND (SELECT `id_order_state` FROM `' . _DB_PREFIX_ . 'order_history` oh WHERE oh.`id_order` = o.`id_order` AND oh.`id_order_state`=' . (int) Configuration::get('PS_OS_PAYMENT') . ' LIMIT 1)';
        }
        $sql .= ' group by o.`id_currency`';

        $totalSellerShipping = Db::getInstance()->getRow($sql);
        if ($totalSellerShipping) {
            //Deduct refunded shipping amount
            $refundedSellerShipping = Db::getInstance()->getValue(
                'SELECT SUM(wkt.`seller_shipping`) FROM `' . _DB_PREFIX_ . 'wk_mp_seller_transaction_history` wkt
                WHERE wkt.`id_customer_seller` = ' . (int) $idSellerCustomer . '
                AND wkt.`id_currency` = ' . (int) $idCurrency . '
                AND wkt.`status`= ' . (int) WkMpSellerTransactionHistory::MP_ORDER_REFUND_STATUS . ''
            );
            if ($refundedSellerShipping) {
                $totalSellerShipping['seller_shipping'] = $totalSellerShipping['seller_shipping'] - $refundedSellerShipping;
            }
        }

        return $totalSellerShipping;
    }

    public static function getTotalAdminShipping($idCurrency, $idSellerCustomer)
    {
        $sql = 'SELECT SUM(wkshp.`admin_earn`) as admin_shipping,
                o.`id_currency` FROM `' . _DB_PREFIX_ . 'wk_mp_admin_shipping` wkshp
                INNER JOIN ' . _DB_PREFIX_ . 'orders o on (o.`id_order` = wkshp.`order_id`)
                INNER JOIN (SELECT DISTINCT id_order, seller_customer_id FROM ' . _DB_PREFIX_ . 'wk_mp_seller_order_detail) wksod ON (o.id_order = wksod.id_order)
                WHERE wksod.`seller_customer_id` = ' . (int) $idSellerCustomer . '
                AND o.`id_currency` = ' . (int) $idCurrency . '
                AND o.`id_order` IN (SELECT wkt.`id_transaction` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_transaction_history` wkt WHERE wkt.`status`= ' . (int) WkMpSellerTransactionHistory::MP_SELLER_ORDER_STATUS . '
                AND wkt.`id_customer_seller`= '.(int) $idSellerCustomer.')';

        if (Configuration::get('WK_MP_COMMISSION_DISTRIBUTE_ON') == 1) {
            // Payment accepted
            $sql .= ' AND (SELECT `id_order_state` FROM `' . _DB_PREFIX_ . 'order_history` oh WHERE oh.`id_order` = o.`id_order` AND oh.`id_order_state`=' . (int) Configuration::get('PS_OS_PAYMENT') . ' LIMIT 1)';
        }

        $totalAdminShipping = Db::getInstance()->getRow($sql);
        if ($totalAdminShipping) {
            //Deduct refunded shipping amount
            $refundedAdminShipping = Db::getInstance()->getValue(
                'SELECT SUM(wkt.`admin_shipping`) FROM `' . _DB_PREFIX_ . 'wk_mp_seller_transaction_history` wkt
                WHERE wkt.`id_customer_seller` = ' . (int) $idSellerCustomer . '
                AND wkt.`id_currency` = ' . (int) $idCurrency . '
                AND wkt.`status`= ' . (int) WkMpSellerTransactionHistory::MP_ORDER_REFUND_STATUS . ''
            );
            if ($refundedAdminShipping) {
                $totalAdminShipping['admin_shipping'] = $totalAdminShipping['admin_shipping'] - $refundedAdminShipping;
            }
        }

        return $totalAdminShipping;
    }
}
