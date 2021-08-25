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

class MarketplaceCustomerPaymentDetail extends ObjectModel
{
    public $id;
    public $seller_customer_id;
    public $payment_mode_id;
    public $payment_detail;

    public static $definition = array(
        'table' => 'marketplace_customer_payment_detail',
        'primary' => 'id',
        'fields' => array(
            'seller_customer_id' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'payment_mode_id' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'payment_detail' => array('type' => self::TYPE_STRING),
        ),
    );

    public function getPaymentDetailByCustomerId($id_customer)
    {
        return Db::getInstance()->getRow(
            'SELECT mcpd.*, mpm.`payment_mode` FROM `'._DB_PREFIX_.'marketplace_customer_payment_detail` mcpd
            LEFT JOIN  `'._DB_PREFIX_.'marketplace_payment_mode` mpm ON (mcpd.`payment_mode_id`=mpm.`id`)
            WHERE mcpd.seller_customer_id = '.(int) $id_customer
        );
    }

    public static function getPaymentDetailById($id)
    {
        $get_dtl =  Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_customer_payment_detail` WHERE `id` = '.(int) $id);

        if ($get_dtl) {
            return $get_dtl;
        } else {
            return false;
        }
    }

    /**
     * From PaymentDetails class V2.1.1
     */
    public function getSellerPaymentDetails($id_customer)
    {
        $seller_payment = Db::getInstance()->getRow(
            'SELECT mcpd.`id`, mcpd.`seller_customer_id`, mcpd.`payment_mode_id`, mcpd.`payment_detail`, mpm.`payment_mode`
            FROM `'._DB_PREFIX_.'marketplace_customer_payment_detail` mcpd
            JOIN `'._DB_PREFIX_.'marketplace_payment_mode` mpm ON (`mcpd`.payment_mode_id = `mpm`.id)
            WHERE `seller_customer_id`='.(int) $id_customer
        );

        if ($seller_payment) {
            return $seller_payment;
        }

        return false;
    }

    /**
     * From PaymentDetails class V2.1.1
     */
    public function getSellerPaymentMode($id_payment_mode)
    {
        if ($payment_mode = Db::getInstance()->getValue(
            'SELECT `payment_mode`
            FROM `'._DB_PREFIX_.'marketplace_payment_mode`
            WHERE `id` = '.$id_payment_mode
        )) {
            return $payment_mode;
        }

        return false;
    }

    /**
     * From PaymentDetails class V2.1.1
     */
    public function getAdminPaymentOption()
    {
        if (!empty($payment_option = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_payment_mode`'))) {
            return $payment_option;
        }

        return false;
    }

    /**
     * From PaymentDetails class V2.1.1
     */
    public function deleteSellerPayment($id)
    {
        if (Db::getInstance()->delete('marketplace_customer_payment_detail', 'id = '.$id)) {
            return true;
        }

        return false;
    }
}
