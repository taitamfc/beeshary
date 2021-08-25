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

class MpStorePickUpProduct extends ObjectModel
{
    public $id_product;
    public $id_product_attribute;
    public $id_store_pickup;
    public $id_store;
    public $pickup_date;

    public static $definition = array(
        'table' => 'mp_store_pickup_products',
        'primary' => 'id_store_pickup_product',
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_store' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_store_pickup' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'pickup_date' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'copy_post' => false)
        ),
    );

    public static function deleteStoreProductByCartId($idCart)
    {
        if ($idCart) {
            $idStorePickUp = MpStorePickUp::getStorePickUpId($idCart);
            if ($idStorePickUp) {
                Db::getInstance()->delete('mp_store_pickup_products', 'id_store_pickup = '.(int)$idStorePickUp);
                Db::getInstance()->delete('mp_store_pickup', 'id_cart = '.(int)$idCart);
            }
        }
    }

    public static function getStorePickUpDetails($idCart, $idProduct, $idProductAttribute)
    {
        $sql = 'SELECT spp.`id_store_pickup`, spp.`pickup_date`, spp.`id_store`, spp.`id_store_pickup_product`
            FROM `'._DB_PREFIX_.'mp_store_pickup_products` AS spp
            LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup` AS sp'.
            ' ON (spp.`id_store_pickup` = sp.`id_store_pickup`)
            WHERE sp.`id_cart` = '.(int)$idCart.
            ' AND spp.`id_product` = '.(int)$idProduct.
            ' AND spp.`id_product_attribute` = '.(int)$idProductAttribute;
        return Db::getInstance()->executeS($sql);
    }

    public static function getStoreId($idCart, $idProduct)
    {
        $sql = 'SELECT msl.`active`
            FROM `'._DB_PREFIX_.'mp_store_pickup_products` AS spp
            LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup` AS sp'.
            ' ON (spp.`id_store_pickup` = sp.`id_store_pickup`)
            LEFT JOIN `'._DB_PREFIX_.'marketplace_store_locator` AS msl'.
            ' ON (spp.`id_store` = msl.`id`)
            WHERE sp.`id_cart` = '.(int)$idCart.
            ' AND spp.`id_product` = '.(int)$idProduct;
        return Db::getInstance()->getValue($sql);
    }

    public static function getStorePickUpDetailsByIdOrder($idOrder, $idProduct, $idProductAttribute)
    {
        $sql = 'SELECT spp.`pickup_date`, spp.`id_store`
            FROM `'._DB_PREFIX_.'mp_store_pickup_products AS spp`
            LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup` AS sp'.
            ' ON (spp.`id_store_pickup` = sp.`id_store_pickup`)
            WHERE sp.`id_order` = '.(int)$idOrder.
            ' AND spp.`id_product` = '.(int)$idProduct.
            ' AND spp.`id_product_attribute` = '.(int)$idProductAttribute;
        return Db::getInstance()->getRow($sql);
    }

    public static function updateIdOrderByIdCart($idCart, $idOrder)
    {
        if ($idCart) {
            Db::getInstance()->update(
                'mp_store_pickup',
                array('id_order' => (int)$idOrder),
                'id_cart = '.(int)$idCart
            );
        }
    }

    public static function getIdStoreByIdCart($idCart)
    {
        $sql = 'SELECT spp.`id_store`
            FROM `'._DB_PREFIX_.'mp_store_pickup_products` AS spp
            LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup` AS sp'.
            ' ON (spp.`id_store_pickup` = sp.`id_store_pickup`)
            WHERE sp.`id_cart` = '.(int)$idCart.
            ' GROUP BY spp.`id_store`';
        return Db::getInstance()->executeS($sql);
    }

    public static function getStoresByIdOrder($idOrder, $idSeller = false)
    {
        $sql = 'SELECT spp.`id_store`
            FROM `'._DB_PREFIX_.'mp_store_pickup_products` AS spp
            LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup` AS sp'.
            ' ON (spp.`id_store_pickup` = sp.`id_store_pickup`)';

        if ($idSeller) {
            $sql .= ' LEFT JOIN `'._DB_PREFIX_.'marketplace_store_locator` AS msl'.
            ' ON (msl.`id` = spp.`id_store`)
            WHERE sp.`id_order` = '.(int)$idOrder.
            ' AND msl.`id_seller` = '.(int)$idSeller;
        } else {
            $sql .= ' WHERE sp.`id_order` = '.(int)$idOrder;
        }
        $sql .= ' GROUP BY spp.`id_store`';
        return Db::getInstance()->executeS($sql);
    }

    public static function getSellersByIdOrder($idOrder)
    {
        $sql = 'SELECT msl.`id_seller`
            FROM `'._DB_PREFIX_.'mp_store_pickup_products` AS spp
            LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup` AS sp'.
            ' ON (spp.`id_store_pickup` = sp.`id_store_pickup`)
            LEFT JOIN `'._DB_PREFIX_.'marketplace_store_locator` AS msl'.
            ' ON (msl.`id` = spp.`id_store`)
            WHERE sp.`id_order` = '.(int)$idOrder.
            ' GROUP BY msl.`id_seller`';
        return Db::getInstance()->executeS($sql);
    }

    public static function getDistinctStoresByIdOrder($idOrder, $idStore = false)
    {
        $sql = 'SELECT DISTINCT spp.`id_store`, spp.`pickup_date`
            FROM `'._DB_PREFIX_.'mp_store_pickup_products` AS spp
            LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup` AS sp'.
            ' ON (spp.`id_store_pickup` = sp.`id_store_pickup`)
            WHERE sp.`id_order` = '.(int)$idOrder;
        if ($idStore) {
            $sql .= ' AND spp.`id_store` = '.(int)$idStore;
        }
        return Db::getInstance()->executeS($sql);
    }

    public static function getDistinctStoresByOrder($idOrder, $pickUpDate, $idStore)
    {
        $sql = 'SELECT  spp.`id_product`, spp.`id_product_attribute`, spp.`id_store_pickup`
            FROM `'._DB_PREFIX_.'mp_store_pickup_products` AS spp
            LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup` AS sp'.
            ' ON (spp.`id_store_pickup` = sp.`id_store_pickup`)
            WHERE sp.`id_order` = '.(int)$idOrder.
            ' AND spp.`id_store` = '.(int)$idStore.
            ' AND spp.`pickup_date` = "'.pSQL($pickUpDate).'"';
        return Db::getInstance()->executeS($sql);
    }

    public static function getDisabledDates($idStore)
    {
        $sql = 'SELECT CASE
            WHEN COUNT(sp.`id_order`) >= msc.`max_pick_ups`'.
            ' THEN DATE_FORMAT(spp.`pickup_date`, "%Y-%m-%d")
            END AS `pickup_datetime`
            FROM `'._DB_PREFIX_.'mp_store_pickup_products` AS spp'.
            ' LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup` AS sp'.
            ' ON (spp.`id_store_pickup` = sp.`id_store_pickup`)'.
            ' LEFT JOIN `'._DB_PREFIX_.'marketplace_store_locator` AS msl'.
            ' ON (spp.`id_store` = msl.`id`)'.
            ' LEFT JOIN `'._DB_PREFIX_.'mp_store_configuration` AS msc'.
            ' ON (msl.`id_seller` = msc.`id_seller`)'.
            ' WHERE spp.`id_store` = '.(int)$idStore.
            ' GROUP BY DATE(spp.`pickup_date`)';
        return Db::getInstance()->executeS($sql);
    }

    public static function chekDisabledDates($idStore, $date)
    {
        $sql = 'SELECT CASE
            WHEN COUNT(sp.`id_order`) >= msc.`max_pick_ups`'.
            ' THEN DATE_FORMAT(spp.`pickup_date`, "%Y-%m-%d")
            END AS `pickup_datetime`
            FROM `'._DB_PREFIX_.'mp_store_pickup_products` AS spp'.
            ' LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup` AS sp'.
            ' ON (spp.`id_store_pickup` = sp.`id_store_pickup`)'.
            ' LEFT JOIN `'._DB_PREFIX_.'marketplace_store_locator` AS msl'.
            ' ON (spp.`id_store` = msl.`id`)'.
            ' LEFT JOIN `'._DB_PREFIX_.'mp_store_configuration` AS msc'.
            ' ON (msl.`id_seller` = msc.`id_seller`)'.
            ' WHERE spp.`id_store` = '.(int)$idStore.
            ' AND DATE_FORMAT(spp.`pickup_date`, "%Y-%m-%d") = "'.pSQL($date).'"'.
            ' GROUP BY DATE(spp.`pickup_date`)';
        return Db::getInstance()->executeS($sql);
    }

    public static function getStoreProducts($idOrder, $idStore)
    {
        $sql = 'SELECT DISTINCT spp.`id_product`, spp.`id_product_attribute`, spp.`pickup_date`
            FROM `'._DB_PREFIX_.'mp_store_pickup_products` AS spp'.
            ' LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup` AS sp'.
            ' ON (spp.`id_store_pickup` = sp.`id_store_pickup`)'.
            ' WHERE sp.`id_order` = '.(int)$idOrder.
            ' AND spp.`id_store` = '.(int)$idStore;
        return Db::getInstance()->executeS($sql);
    }

    public static function getProductByCartId($idCart)
    {
        $sql = 'SELECT spp.`id_product`, spp.`id_product_attribute`
            FROM `'._DB_PREFIX_.'mp_store_pickup_products` AS spp'.
            ' LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup` AS sp'.
            ' ON (spp.`id_store_pickup` = sp.`id_store_pickup`)'.
            ' WHERE sp.`id_cart` = '.(int) $idCart;
        return Db::getInstance()->executeS($sql);
    }

    public static function getStorePickUpId($idCart)
    {
        $sql = 'SELECT sp.`id_store_pickup`
            FROM `'._DB_PREFIX_.'mp_store_pickup` AS sp'.
            ' WHERE sp.`id_cart` = '.(int) $idCart;
        return Db::getInstance()->getValue($sql);
    }

    public static function getStorePickUpProductId($idCart, $idProduct, $idProductAttribute)
    {
        $sql = 'SELECT spp.`id_store_pickup_product`
            FROM `'._DB_PREFIX_.'mp_store_pickup_products` AS spp'.
            ' LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup` AS sp'.
            ' ON (spp.`id_store_pickup` = sp.`id_store_pickup`)'.
            ' WHERE sp.`id_cart` = '.(int) $idCart.
            ' AND spp.`id_product` = '.(int) $idProduct.
            ' AND spp.`id_product_attribute` = '.(int) $idProductAttribute;
        return Db::getInstance()->getValue($sql);
    }


    public static function deleteProductsFromCart($idCart, $idProduct, $idProductAttribute)
    {
        if ($idCart) {
            $idStorePickUp = MpStorePickUp::getStorePickUpId($idCart);
            if ($idStorePickUp) {
                Db::getInstance()->delete(
                    'mp_store_pickup_products',
                    'id_store_pickup = '.(int)$idStorePickUp.
                    ' AND id_product = '.(int)$idProduct.
                    ' AND id_product_attribute = '.(int)$idProductAttribute
                );
            }
        }
    }

    public static function getIdOrderByCartId($idCart)
    {
        if ($idCart) {
            $sql = 'SELECT `id_order`
            FROM `'._DB_PREFIX_.'mp_store_pickup`
            WHERE `id_cart` = '.(int) $idCart;
            return Db::getInstance()->getValue($sql);
        }
    }

    public static function deletePickUpProduct($idProduct, $idProductAttribute = false)
    {
        $sql = 'DELETE FROM `'._DB_PREFIX_.'mp_store_pickup_products` AS mspp'.
            ' LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup` AS msp ON (mspp.`id_store_pickup` = msp.`id_store_pickup`)'.
            ' WHERE mspp.`id_product` = '.(int) $idProduct.
            ' AND `id_order` != 0';
        if ($idProductAttribute) {
            $sql .= ' AND mspp.`id_product_attribute ` = '.(int)$idProductAttribute;
        }
        return Db::getInstance()->executeS($sql);
    }

    public static function deletePickUpProductByIDStore($idStore)
    {
        return Db::getInstance()->executeS(
            'DELETE FROM `'._DB_PREFIX_.'mp_store_pickup_products` AS mspp'.
            ' LEFT JOIN `'._DB_PREFIX_.'mp_store_pickup` AS msp ON (mspp.`id_store_pickup` = msp.`id_store_pickup`)'.
            ' WHERE mspp.`id_store` = '.(int) $idStore.
            ' AND `id_order` != 0'
        );
    }
}
