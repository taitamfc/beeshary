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

class MarketplaceStoreProduct extends ObjectModel
{
    public $id_product;
    public $id_store;

    public static $definition = array(
        'table' => 'marketplace_store_products',
        'primary' => 'id',
        'fields' => array(
                 'id_product' => array('type' => self::TYPE_INT),
                 'id_store' => array('type' => self::TYPE_INT),
                ),
        );

    /**
     * couldn't found any function in marketplace.
     *
     * @param [int] $id_seller
     *
     * @return [array/false]
     */
    public static function getMpSellerActiveProducts($id_seller, $id_lang = false)
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        $sql = 'SELECT msp.*, mspl.*, msp.`id_ps_product` AS id_product
                FROM `'._DB_PREFIX_.'wk_mp_seller_product`  msp
                INNER JOIN `'._DB_PREFIX_.'wk_mp_seller_product_lang` mspl ON (mspl.`id_mp_product` = msp.`id_mp_product`)
                WHERE msp.`id_seller` = '.(int) $id_seller.' AND msp.`active` = 1 AND mspl.`id_lang` = '.(int) $id_lang;

        $mp_products = Db::getInstance()->executeS($sql);

        if ($mp_products) {
            return $mp_products;
        }

        return false;
    }

    /**
     * [getProductStore get only active store products].
     *
     * @param [int] $id_product [prestashop product id]
     *
     * @return [array/false] [array]
     */
    public static function getProductStore($id_product, $active = false, $cityName = false)
    {
        $sql = 'SELECT sp.`id_store`
                FROM `'._DB_PREFIX_.'marketplace_store_products` AS sp
                LEFT JOIN `'._DB_PREFIX_.'marketplace_store_locator` AS sl ON (sp.`id_store` = sl.`id`)
                WHERE sp.`id_product` = '.(int) $id_product;

        if ($active) {
            $sql .= ' AND sl.`active` = 1';
        }
        if ($cityName) {
            $sql .= ' AND sl.`city_name` LIKE \'%'.pSQL($cityName).'%\' ';
        }

        $mpStores = Db::getInstance()->executeS($sql);
        if ($mpStores) {
            return $mpStores;
        }

        return false;
    }

    public static function getProducts($key)
    {
        $context = Context::getContext();
        $sql = 'SELECT p.`id_product`, pl.`name`
		    FROM `'._DB_PREFIX_.'product` p
		    '.Shop::addSqlAssociation('product', 'p').'
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
            ON (pl.id_product = p.id_product AND pl.id_lang = '.
            (int)$context->language->id.Shop::addSqlRestrictionOnLang('pl').')
            WHERE p.`active` = 1
            AND pl.`name` LIKE \'%'.pSQL($key).'%\'';
        return Db::getInstance()->executeS($sql);
    }

    public static function getSellerProducts($id_store)
    {
        $store_products = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_store_products` WHERE `id_store` = '.(int) $id_store);

        if ($store_products) {
            return $store_products;
        }

        return false;
    }

    public static function getStoreProducts($idStore, $startLimit = 0, $endLimit = 0)
    {
        $context = Context::getContext();
        $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, pl.`name`,
            image_shop.`id_image` id_image, il.`legend`,  p.`price`
		    FROM `'._DB_PREFIX_.'product` p
		    '.Shop::addSqlAssociation('product', 'p').'
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
            ON (pl.id_product = p.id_product AND pl.id_lang = '.
            (int)$context->language->id.Shop::addSqlRestrictionOnLang('pl').')
		    LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
            ON (image_shop.`id_product` = p.`id_product`
            AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
            LEFT JOIN `'._DB_PREFIX_.'image_lang` il
            ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$context->language->id.')
            LEFT JOIN `'._DB_PREFIX_.'marketplace_store_products` msp ON (msp.`id_product` = p.`id_product`)
            WHERE p.`active` = 1 AND msp.`id_store` = '.(int)$idStore;
        if ($endLimit) {
            $sql .= ' LIMIT '.(int)$startLimit.',' .(int)$endLimit;
        }

        return Db::getInstance()->executeS($sql);
    }

    public static function deleteStoreProductByStoreId($id_store)
    {
        return Db::getInstance()->delete('marketplace_store_products', 'id_store = '.(int) $id_store);
    }

    public static function deleteStoreProduct($idProduct)
    {
        return Db::getInstance()->delete('marketplace_store_products', 'id_product = '.(int) $idProduct);
    }

    public static function getAvailableProductStore($idProduct, $active = false)
    {
        $sql = 'SELECT msp.`id_store`
            FROM `'._DB_PREFIX_.'marketplace_store_products` AS msp
            LEFT JOIN `'._DB_PREFIX_.'marketplace_store_locator` AS msl ON (msp.`id_store` = msl.`id`)
            WHERE msp.`id_product` = '.(int) $idProduct .
            ' AND store_pickup_available = 1';
        if ($active) {
            $sql .= ' AND msl.`active` = 1';
        }
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function checkStoreProducts($idStore)
    {
        $sql = 'SELECT `id_product`
            FROM `'._DB_PREFIX_.'marketplace_store_products`
            WHERE `id_store` = '.(int) $idStore;
        return Db::getInstance()->executeS($sql);
    }

    public static function getIdProductByMpIdProducts($idMpProduct)
    {
        return Db::getInstance()->getValue(
            'SELECT id_ps_product FROM `'._DB_PREFIX_.'wk_mp_seller_product`'.
            ' WHERE `id_mp_product` = '.(int) $idMpProduct
        );
    }
}
