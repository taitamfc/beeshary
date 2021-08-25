<?php
/**
* 2017-2018 PHPIST.
*
*  @author    Yassine Belkaid <yassine.belkaid87@gmail.com>
*  @copyright 2017-2018 PHPIST
*  @license   MIT
*/

class WkMpSellerBank extends ObjectModel
{
    public $id;
    public $id_seller;
    public $bank_type;
    public $beneficiary;
    public $establishment;
    public $iban_code;
    public $code_bic;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_mp_seller_bank',
        'primary' => 'id_ps_wk_mp_seller_bank',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT,'required' => true),
            'bank_type' => array('type' => self::TYPE_STRING),
            'beneficiary' => array('type' => self::TYPE_STRING),
            'establishment' => array('type' => self::TYPE_STRING),
            'iban_code' => array('type' => self::TYPE_STRING),
            'code_bic' => array('type' => self::TYPE_STRING),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),
        ),
    );

    public static function getSellerBankDataByIdSeller($id_seller)
    {
        return Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'wk_mp_seller_bank WHERE id_seller = '. (int)$id_seller);
    }
}
