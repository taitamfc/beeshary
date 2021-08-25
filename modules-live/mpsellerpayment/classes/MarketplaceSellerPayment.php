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

class MarketplaceSellerPayment extends ObjectModel
{
    public $id_seller;
    public $total_earning;
    public $total_paid;
    public $total_due;
    public $id_currency;

    public static $definition = array(
        'table' => 'marketplace_seller_payment',
        'primary' => 'id',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'required' => true),
            'total_earning' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'total_paid' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'total_due' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'id_currency' => array('type' => self::TYPE_INT, 'required' => true),
        ),
    );

    /**
     * [getDetailsByIdSeller - Get seller payment details according to seller id]
     * @param  [type] $id_seller [description]
     * @return [type]            [description]
     */
    public static function getDetailsByIdSeller($id_seller)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_payment` WHERE `id_seller`='.(int) $id_seller);
    }

    /**
     * [getDetailsByIdSellerAndIdCurrency - Get seller payment details according to seller id & currency id]
     * @param  [type] $id_seller   [description]
     * @param  [type] $id_currency [description]
     * @return [type]              [description]
     */
    public function getDetailsByIdSellerAndIdCurrency($id_seller, $id_currency)
    {
        $result = false;
        if (isset($id_seller) && isset($id_currency)) {
            $details = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_payment` WHERE `id_seller`= '.(int)$id_seller.' AND `id_currency` = '.(int)$id_currency);
            if ($details) {
                $result = $details;
            }
        }

        return $result;
    }

    /**
     * [updateEarningAndDueByIdSellerAndIdCurrency - Update total earning and total due of any seller]
     * @param  [type] $earning     [description]
     * @param  [type] $due         [description]
     * @param  [type] $id_seller   [description]
     * @param  [type] $id_currency [description]
     * @return [type]              [description]
     */
    public function updateEarningAndDueByIdSellerAndIdCurrency($earning, $due, $id_seller, $id_currency)
    {
        $result = false;
        if (isset($earning) && isset($due) && isset($id_seller) && isset($id_currency)) {
            $is_updated = Db::getInstance()->update('marketplace_seller_payment', array('total_earning' => (float)$earning, 'total_due' => (float)$due), '`id_seller` = '.(int)$id_seller.' AND `id_currency` = '.(int)$id_currency);
            if ($is_updated) {
                $result = $is_updated;
            }
        }

        return $result;
    }

    /**
     * [getCustomerPaymentDetails - Get customer payment details according to customer id]
     * @param  [type] $id_customer [description]
     * @return [type]              [description]
     */
    public function getCustomerPaymentDetails($id_customer)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_customer_payment_detail` WHERE `seller_customer_id` = '.(int) $id_customer);
    }

    public function getPaymentModeById($payment_mode_id)
    {
        return Db::getInstance()->getValue('SELECT `payment_mode` FROM `'._DB_PREFIX_.'marketplace_payment_mode` WHERE `id` = '.(int) $payment_mode_id);
    }

    public function getSellerPaymentBySellerId($id_seller, $id_currency)
    {
        return Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'marketplace_seller_payment WHERE `id_seller` = '.(int) $id_seller.' AND `id_currency` = '.(int) $id_currency);
    }

    public function updateSellerPayment($total_paid, $total_due, $id_seller, $id_currency)
    {
        return Db::getInstance()->update('marketplace_seller_payment', array('total_paid' => $total_paid, 'total_due' => $total_due), '`id_seller` = '.(int) $id_seller.' AND `id_currency` = '.(int) $id_currency);

    }
}
