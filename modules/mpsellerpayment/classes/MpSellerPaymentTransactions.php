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

class MpSellerPaymentTransactions extends ObjectModel
{
    public $id_seller;
    public $id_currency;
    public $amount;
    public $date_add;
    public $type;
    public $status;

    public static $definition = array(
        'table' => 'marketplace_seller_payment_transactions',
        'primary' => 'id',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'required' => true),
            'id_currency' => array('type' => self::TYPE_INT, 'required' => true),
            'amount' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE),
            'type' => array('type' => self::TYPE_STRING),
            'status' => array('type' => self::TYPE_INT),
        ),
    );

    /**
     * [getDetailsByIdSeller - Get seller payment transactions details according to seller id]
     * @param  [type] $id_seller [description]
     * @return [type]            [description]
     */
    public static function getDetailsByIdSeller($id_seller)
    {
        $result = false;
        if (isset($id_seller)) {
            $payment = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_payment_transactions` WHERE `id_seller` = '.(int) $id_seller);
            if ($payment) {
                $result = $payment;
            }
        }

        return $result;
    }

    /**
     * [getDeatilsByIdSellerAndOrderBy - Get seller payment transactions details according to seller id with order by]
     * @param  [type] $id_seller [description]
     * @param  [type] $order_by  [description]
     * @return [type]            [description]
     */
    public static function getDeatilsByIdSellerAndOrderBy($id_seller, $order_by)
    {
        $result = false;
        if (isset($id_seller) && isset($order_by)) {
            $payment_transactions = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_payment_transactions` WHERE `id_seller` = '.(int) $id_seller.' ORDER BY '.pSQL($order_by));
            if ($payment_transactions) {
                $result = $payment_transactions;
            }
        }

        return $result;
    }

    /**
     * [getSellerTransactionById - Get seller payment transactions details according to transaction id]
     * @param  [type] $id_transaction [description]
     * @return [type]                 [description]
     */
    public function getSellerTransactionById($id_transaction)
    {
        return Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'marketplace_seller_payment_transactions WHERE `id` = '.(int) $id_transaction);
    }

    /**
     * [updateTransactionStatus - Update seller payment transactions status according to transaction id]
     * @param  [type] $id_transaction [description]
     * @param  [type] $status         [description]
     * @return [type]                 [description]
     */
    public function updateTransactionStatus($id_transaction, $status)
    {
        return Db::getInstance()->update('marketplace_seller_payment_transactions', array('status' => $status), '`id` = '.(int) $id_transaction);
    }
}
