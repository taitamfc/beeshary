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

class WkMpSellerPaymentRequest extends ObjectModel
{
    public $id_seller_payment_request;
    public $id_seller;
    public $id_currency;
    public $id_mp_transaction;
    public $request_amount;
    public $remark;
    public $status;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' =>  'wk_mp_seller_payment_request',
        'primary'   =>  'id_seller_payment_request',
        'fields'    =>  array(
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_mp_transaction' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'request_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'remark' => array('type' => self::TYPE_STRING),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
        )
    );

    public static function createTable()
    {
        return Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'wk_mp_seller_payment_request` (
              `id_seller_payment_request` int(10) unsigned NOT NULL auto_increment,
              `id_seller` int(10) unsigned DEFAULT 0,
              `id_currency` int(10) unsigned DEFAULT 0,
              `id_mp_transaction` int(10) unsigned DEFAULT 0,
              `remark` varchar(255) DEFAULT NULL,
              `request_amount` decimal(17,2) NOT NULL default "0.00",
              `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
              `date_add` datetime NOT NULL,
              `date_upd` datetime NOT NULL,
              PRIMARY KEY  (`id_seller_payment_request`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
        );
    }

    public static function deleteTable()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'wk_mp_seller_payment_request`');
    }

    public static function cancelByTransactionId($idMpTransaction)
    {
        return Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'wk_mp_seller_payment_request`
            SET `status` = 2, `remark` = "N/A"
            WHERE `id_mp_transaction` = '.(int)$idMpTransaction
        );
    }

    public static function getRequests($idSeller, $idCurrency = null)
    {
        $reqeusts = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_payment_request`
            WHERE `id_seller` = '.
            (int)$idSeller.($idCurrency ? ' AND id_currency = '.(int)$idCurrency : '').
            ' ORDER BY id_seller_payment_request DESC'
        );
        foreach ($reqeusts as &$reqeust) {
            $reqeust['request_amount_currency'] = Tools::displayPrice(
                $reqeust['request_amount'],
                new Currency($reqeust['id_currency'])
            );
        }
        return $reqeusts;
    }

    public static function getSellerLastRequest($idSeller, $idCurrency = null)
    {
        $seller = Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_payment_request`
            WHERE `id_seller` ='.(int) $idSeller .
            ($idCurrency ? ' AND id_currency = '.(int)$idCurrency : '').' ORDER BY id_seller_payment_request DESC'
        );
        return $seller;
    }

    public static function getSellers()
    {
        $sellers = Db::getInstance()->executeS(
            'SELECT id_seller, CONCAT(seller_firstname, " ", seller_lastname) as name FROM `'._DB_PREFIX_.'wk_mp_seller`
            WHERE `active` = 1'
        );
        $results = array();
        foreach ($sellers as $seller) {
            $results[$seller['id_seller']] = $seller['name'];
        }
        return $results;
    }

    public static function sellerTransactionTotal($idCustomerSeller, $idCurrency = false)
    {
        $orderTotal = WkMpSellerTransactionHistory::getSellerOrderTotalByIdCustomer($idCustomerSeller, $idCurrency);
        foreach ($orderTotal as $key => $detail) {
            $idCurrency = empty($detail['id_currency']) ? Configuration::get('PS_CURRENCY_DEFAULT') :
            $detail['id_currency'];
            $currency = new Currency($idCurrency);
            $sellerTotal = empty($detail['seller_total_earned']) ? '0' : $detail['seller_total_earned'];
            $sellerAmount = empty($detail['seller_amount']) ? '0' : $detail['seller_amount'];
            $sellerRecieve = empty($detail['seller_receive']) ? '0' : $detail['seller_receive'];
            $sellerTax = empty($detail['seller_tax']) ? '0' : $detail['seller_tax'];
            $sellerShipping = empty($detail['seller_shipping']) ? '0' : $detail['seller_shipping'];
            $sellerRefundedAmount = empty($detail['seller_refunded_amount']) ? '0' : $detail['seller_refunded_amount'];
            $adminTotalAmount = empty($detail['admin_total_earned']) ? '0' : $detail['admin_total_earned'];
            $adminCommission = empty($detail['admin_commission']) ? '0' : $detail['admin_commission'];
            $adminTax = empty($detail['admin_tax']) ? '0' : $detail['admin_tax'];
            $adminShipping = empty($detail['admin_shipping']) ? '0' : $detail['admin_shipping'];
            $adminRefundedAmount = empty($detail['admin_refunded_amount']) ? '0' : $detail['admin_refunded_amount'];
            $total_earning = $adminTotalAmount + $sellerTotal;
            $seller_shipping = 0;
            $admin_shipping = 0;
            $shippingInfo = false;
            if ($idCustomerSeller) {
                $sellerShippingInfo = WkMpAdminShipping::getTotalSellerShipping(
                    $idCustomerSeller,
                    $detail['id_currency']
                );
                if (!$sellerShippingInfo) {
                    $sellerShippingInfo = array(
                        'seller_shipping' => '0',
                        'id_currency' => $idCurrency
                    );
                }
                $adminShippingInfo = WkMpAdminShipping::getTotalAdminShipping(
                    $detail['id_currency'],
                    $idCustomerSeller
                );
                if (!$adminShippingInfo) {
                    $sellerShippingInfo = array(
                        'admin_shipping' => '0',
                        'id_currency' => $idCurrency
                    );
                }
                $shippingInfo = array_merge($sellerShippingInfo, $adminShippingInfo);
            } else {
                $shippingInfo = WkMpAdminShipping::getTotalShippingByIdCurrency($detail['id_currency']);
            }
            if ($shippingInfo) {
                if (isset($shippingInfo['seller_shipping'])) {
                    $seller_shipping = $shippingInfo['seller_shipping'];
                }
                if (isset($shippingInfo['admin_shipping'])) {
                    $admin_shipping = $shippingInfo['admin_shipping'];
                }
                $sellerTotal += $seller_shipping;
                $total_earning += $admin_shipping + $seller_shipping;
            }
            $orderTotal[$key]['no_prefix_admin_shipping'] = $admin_shipping;
            $orderTotal[$key]['no_prefix_seller_shipping'] = $seller_shipping;
            $orderTotal[$key]['admin_shipping'] = Tools::displayPrice($admin_shipping, $currency);
            $orderTotal[$key]['seller_shipping'] = Tools::displayPrice($seller_shipping, $currency);
            $orderTotal[$key]['no_prefix_seller_total'] = $sellerTotal;
            $orderTotal[$key]['no_prefix_seller_amount'] = $sellerAmount;
            $orderTotal[$key]['no_prefix_seller_recieve'] = $sellerRecieve;
            $orderTotal[$key]['no_prefix_seller_tax'] = $sellerTax;
            $orderTotal[$key]['no_prefix_seller_shipping'] = $sellerShipping;
            $orderTotal[$key]['no_prefix_seller_refund'] = $sellerRefundedAmount;
            $orderTotal[$key]['no_prefix_seller_due'] = $sellerTotal - $sellerRecieve;
            $orderTotal[$key]['no_prefix_admin_total'] = $adminTotalAmount;
            $orderTotal[$key]['no_prefix_admin_commission'] = $adminCommission;
            $orderTotal[$key]['no_prefix_admin_tax'] = $adminTax;
            $orderTotal[$key]['no_prefix_admin_shipping'] = $adminShipping;
            $orderTotal[$key]['no_prefix_admin_refund'] = $adminRefundedAmount;
            $orderTotal[$key]['no_prefix_total_earning'] = $adminTotalAmount + $sellerTotal;
            $orderTotal[$key]['seller_total'] = Tools::displayPrice($sellerTotal, $currency);
            $orderTotal[$key]['seller_amount'] = Tools::displayPrice($sellerAmount, $currency);
            $orderTotal[$key]['seller_recieve'] = Tools::displayPrice($sellerRecieve, $currency);
            $orderTotal[$key]['seller_tax'] = Tools::displayPrice($sellerTax, $currency);
            $orderTotal[$key]['seller_refund'] = Tools::displayPrice($sellerRefundedAmount, $currency);
            $orderTotal[$key]['seller_due'] = Tools::displayPrice($sellerTotal - $sellerRecieve, $currency);
            $orderTotal[$key]['admin_total'] = Tools::displayPrice($adminTotalAmount, $currency);
            $orderTotal[$key]['admin_commission'] = Tools::displayPrice($adminCommission, $currency);
            $orderTotal[$key]['admin_tax'] = Tools::displayPrice($adminTax, $currency);
            $orderTotal[$key]['admin_refund'] = Tools::displayPrice($adminRefundedAmount, $currency);
            $orderTotal[$key]['total_earning'] = Tools::displayPrice($total_earning, $currency);
            unset($currency);
        }
        return $orderTotal;
    }
}
