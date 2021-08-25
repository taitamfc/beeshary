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

class MpStoreConfiguration extends ObjectModel
{
    public $id_seller;
    public $enable_marker;
    public $enable_country;
    public $countries;
    public $enable_date;
    public $enable_time;
    public $minimum_days;
    public $maximum_days;
    public $minimum_hours;
    public $max_pick_ups;
    public $enable_store_notification;
    public $store_payment;

    public static $definition = array(
        'table' => 'mp_store_configuration',
        'primary' => 'id_store_configuration',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'minimum_days' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'maximum_days' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'minimum_hours' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'max_pick_ups' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'countries' => array('type' => self::TYPE_STRING, 'size' => 65000),
            'enable_marker' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'enable_country' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'enable_date' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'enable_time' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'enable_store_notification' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'store_payment' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    public static function getStoreConfiguration($idMpSeller = false)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'mp_store_configuration`';
        if ($idMpSeller) {
            $sql .= 'WHERE `id_seller` = '. (int)$idMpSeller;
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        }
        $storeConfiguration = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($result as $row) {
            $storeConfiguration[$row['id_seller']] = $row;
        }
        return $storeConfiguration;
    }

    public static function getCountries($idLang, $active = false)
    {
        $countries = array();

        $sql = 'SELECT c.`id_country`, cl.`name` country
            FROM `'._DB_PREFIX_.'country` c '.Shop::addSqlAssociation('country', 'c').'
            LEFT JOIN `'._DB_PREFIX_.'country_lang` cl
            ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '.(int) $idLang.')
            WHERE 1'.($active ? ' AND c.active = 1' : '');

        if (Configuration::get('MP_STORE_COUNTRY_ENABLE')) {
            $sql .= ' AND c.`id_country` IN ('.pSQL(
                implode(',', json_decode(Configuration::get('MP_STORE_COUNTRIES')))
            ).')';
        }

        $sql .= ' ORDER BY cl.name ASC';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($result as $row) {
            $countries[$row['id_country']] = $row['country'];
        }
        return $countries;
    }

    public static function getSelectedStoreConfiguration($idCart, $idProduct)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT msc.*'.
            ' FROM `'._DB_PREFIX_.'mp_store_configuration` AS msc'.
            ' LEFT JOIN `'._DB_PREFIX_.'marketplace_store_locator` AS msl'.
            ' ON (msl.`id_seller` = msc.`id_seller`)'.
            ' LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup_products` AS mspp'.
            ' ON (msl.`id` = mspp.`id_store`)'.
            ' LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup` AS msp'.
            ' ON (mspp.`id_store_pickup` = msp.`id_store_pickup`)'.
            ' WHERE mspp.`id_product` = '.(int)$idProduct.
            ' AND msp.`id_cart` = '.(int)$idCart
        );
    }

    public static function getStoreConfigurationByIdProduct($idProduct)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT msc.*'.
            ' FROM `'._DB_PREFIX_.'mp_store_configuration` AS msc'.
            ' LEFT JOIN `'._DB_PREFIX_.'marketplace_store_locator` AS msl'.
            ' ON (msl.`id_seller` = msc.`id_seller`)'.
            ' LEFT JOIN `'._DB_PREFIX_.'marketplace_store_products` AS mspp'.
            ' ON (msl.`id` = mspp.`id_store`)'.
            ' WHERE mspp.`id_product` = '.(int)$idProduct
        );
    }

    public static function getStoreConfigurationByIdStore($idStore)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT msc.*'.
            ' FROM `'._DB_PREFIX_.'mp_store_configuration` AS msc'.
            ' LEFT JOIN `'._DB_PREFIX_.'marketplace_store_locator` AS msl'.
            ' ON (msl.`id_seller` = msc.`id_seller`)'.
            ' WHERE msl.`id` = '.(int)$idStore
        );
    }
}
