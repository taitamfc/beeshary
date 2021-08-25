<?php
/**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkMpSellerPaymentHistory extends ObjectModel
{
    public $id_seller;
    public $id_currency;
    public $amount;
    public $payment_method; // eg -stripe, paypal, etc
    public $payment_type;   // 1. Real, 2. Manually by admin
    public $payment_was;    // 1. Paid, 2. Due
    public $id_transaction; // transaction id of payment geteway
    public $remark;
    public $date_add;
    public $status;

    public static $definition = array(
        'table' => 'wk_mp_seller_payment_history',
        'primary' => 'id_seller_payment_history',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'required' => true),
            'id_currency' => array('type' => self::TYPE_INT, 'required' => true),
            'amount' =>  array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'required' => true),
            'payment_method' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'payment_type' => array('type' => self::TYPE_INT),
            'payment_was' => array('type' => self::TYPE_INT),
            'id_transaction' => array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 254),
            'remark' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'date_add' => array('type' => self::TYPE_DATE),
            'status' => array('type' => self::TYPE_INT),
        ),
    );

    /**
     * Get seller payment transactions details according to seller id
     *
     * @param  int $id_seller
     * @return bool/array
     */
    public static function getDetailsByIdSeller($idSeller, $paid = false)
    {
        if (isset($idSeller)) {
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_payment_history` WHERE `id_seller` = '.(int) $idSeller;
            
            if ($paid) {
                $sql .= ' AND `payment_was` = 1';
            }
            $payment = Db::getInstance()->executeS($sql);
            if ($payment) {
                return $payment;
            }
        }

        return false;
    }
}
