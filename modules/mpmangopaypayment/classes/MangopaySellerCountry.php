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

class MangopaySellerCountry extends ObjectModel
{
    public $id_customer;
    public $id_seller;
    public $id_country;

    public static $definition = array(
        'table' => 'wk_mp_mangopay_seller_country',
        'primary' => 'id',
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT),
            'id_seller' => array('type' => self::TYPE_INT),
            'id_country' => array('type' => self::TYPE_INT),
        ),
    );

    /**
     * Get the seller country id.
     * @param int $id_seller
     * @return array
     */
    public static function sellerCountryID($id_seller)
    {
        return Db::getInstance()->getValue(
            'SELECT `id_country` FROM `'._DB_PREFIX_.'wk_mp_mangopay_seller_country`
            WHERE  `id_seller` = '.(int) $id_seller
        );
    }

    /**
     * Get the seller country detail.
     * @param int $id_seller
     * @return array
     */
    public static function sellerCountryDetails($id_seller)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'wk_mp_mangopay_seller_country` WHERE  `id_seller` = '.(int) $id_seller
        );
    }
}
