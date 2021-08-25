<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MangopayPayInRefund extends ObjectModel
{
    public $id;
    public $payin_id;
    public $refund_id;
    public $amount;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_mp_mangopay_payin_refund',
        'primary' => 'id',
        'fields' => array(
            'payin_id' => array('type' => self::TYPE_STRING),
            'refund_id' => array('type' => self::TYPE_STRING),
            'amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * Get the payin refund amount.
     * @param int $id_payin
     * @return int
     */
    public function getRefundSumByPayIn($id_payin)
    {
        if ($refunded_amt = Db::getInstance()->getValue(
            'SELECT SUM(`amount`) FROM `'._DB_PREFIX_.'wk_mp_mangopay_payin_refund`
            WHERE `payin_id` = \''.pSQL($id_payin).'\''
        )) {
            return $refunded_amt;
        } else {
            return 0;
        }
    }
}
