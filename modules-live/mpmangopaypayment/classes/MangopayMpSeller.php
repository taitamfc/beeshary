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

class MangopayMpSeller extends ObjectModel
{
    public $mgp_clientid;
    public $mgp_userid;
    public $mgp_walletid;
    public $id_seller;
    public $currency_iso;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_mp_mangopay_seller',
        'primary' => 'id',
        'fields' => array(
            'mgp_clientid' => array('type' => self::TYPE_STRING),
            'mgp_userid' => array('type' => self::TYPE_INT),
            'mgp_walletid' => array('type' => self::TYPE_INT),
            'id_seller' => array('type' => self::TYPE_INT),
            'currency_iso' => array('type' => self::TYPE_STRING),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * Delete the mangopay sellers detail by userid
     * @param int $mgp_userid
     * @return bool
     */
    public function deleteSellerMangopaydetailsByMangopayUserId($mgp_userid)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->delete(
            'wk_mp_mangopay_seller',
            '`mgp_userid`= \''.pSQL($mgp_userid).'\''
        );
    }

    /**
     * Check whether the mangopay detials available of the seller.
     * @param int $client_id
     * @param int $id_seller
     * @param int $currency_iso
     * @return array
     */
    public static function checkSellerMangopayDetailsAvailable($client_id, $id_seller, $currency_iso)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_mangopay_seller`
            WHERE `id_seller` = '.(int) $id_seller.'
            AND `mgp_clientid` = \''.pSQL($client_id).'\''.'
            AND `currency_iso`= \''.pSQL($currency_iso).'\''
        );
    }

    /**
     * Get the mangopay seller detail by idseller.
     * @param int $id_seller
     * @return array
     */
    public static function sellerMangopayDetails($id_seller)
    {
        $client_id = Configuration::get('WK_MP_MANGOPAY_CLIENTID');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_mangopay_seller`
            WHERE `id_seller` = '.(int) $id_seller.' AND `mgp_clientid` = \''.pSQL($client_id).'\''
        );
    }

    /**
     * Get the mangopay seller detail by idseller and currency.
     * @param int $id_seller
     * @param int $currency_iso
     * @return array
     */
    public static function sellerMangopayDetailsByCurrency($id_seller, $currency_iso)
    {
        $client_id = Configuration::get('WK_MP_MANGOPAY_CLIENTID');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_mangopay_seller`
            WHERE `id_seller` = '.(int) $id_seller.'
            AND `mgp_clientid` = \''.pSQL($client_id).'\''.'
            AND `currency_iso`= \''.pSQL($currency_iso).'\''
        );
    }
}
