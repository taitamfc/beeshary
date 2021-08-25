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

class MpStorePay extends ObjectModel
{
    public $payment_name;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'mpstore_pay',
        'primary' => 'id_mp_store_pay',
        'multilang' => true,
        'fields' => array(
            'payment_name' => array(
                'type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true
            ),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'copy_post' => false),
        ),
    );

    public static function getPaymentOption($active = false)
    {
        $sql = 'SELECT wkp.`id_mp_store_pay`, wkpl.`payment_name`
            FROM `'._DB_PREFIX_.'mpstore_pay` AS wkp
            LEFT JOIN `'._DB_PREFIX_.'mpstore_pay_lang` AS wkpl'.
            ' ON (wkp.`id_mp_store_pay` = wkpl.`id_mp_store_pay`)';
        if ($active) {
            $sql .= ' WHERE wkp.`active` = 1';
        }
        $sql .= ' GROUP BY wkp.`id_mp_store_pay`';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function delete()
    {
        $image_path_logo = _PS_MODULE_DIR_.'mpstorelocator/views/img/payment_logo/'.(int)$this->id.'.jpg';
        @unlink($image_path_logo);

        if (!parent::delete()) {
            return false;
        }

        return true;
    }

    public static function getPaymentOptionDetails($paymentOptions, $idLang)
    {
        if ($paymentOptions) {
            $sql = 'SELECT wkp.`id_mp_store_pay`, wkpl.`payment_name`
                FROM `'._DB_PREFIX_.'mpstore_pay` AS wkp
                LEFT JOIN `'._DB_PREFIX_.'mpstore_pay_lang` AS wkpl'.
                ' ON (wkp.`id_mp_store_pay` = wkpl.`id_mp_store_pay`)'.
                ' WHERE wkp.`active` = 1'.
                ' AND wkp.`id_mp_store_pay` IN ('.pSQL(implode(',', $paymentOptions)).')'.
                ' AND wkpl.`id_lang` = '.(int)$idLang;
    
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }
}
