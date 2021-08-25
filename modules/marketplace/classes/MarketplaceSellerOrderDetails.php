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

class MarketplaceSellerOrderDetails extends ObjectModel
{
    public $id;
    public $id_seller_order;  /** @var id of marketplace_seller_orders */
    public $product_id;
    public $product_attribute_id;
    public $seller_customer_id; /** @var id_customer of marketplace seller */
    public $seller_name;
    public $product_name;
    public $quantity;
    public $price_ti; /* product price with tax */
    public $price_te; /* product price without tax */
    public $admin_commission; /* admin commission */
    public $admin_tax; /* admin tax */
    public $seller_amount; /* seller amount */
    public $seller_tax;    /* seller tax*/
    public $id_order;
    public $id_currency; /* order currency id */
    public $date_add;

    public static $definition = array(
        'table' => 'marketplace_seller_orders_details',
        'primary' => 'id',
        'fields' => array(
            'product_id' => array('type' => self::TYPE_INT, 'required' => true),
            'product_attribute_id' => array('type' => self::TYPE_INT, 'required' => true),
            'id_seller_order' => array('type' => self::TYPE_INT, 'required' => true),
            'seller_customer_id' => array('type' => self::TYPE_INT, 'required' => true),
            'seller_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'product_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'quantity' => array('type' => self::TYPE_INT, 'required' => true),
            'price_ti' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'price_te' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'admin_commission' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'admin_tax' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'seller_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'seller_tax' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'id_order' => array('type' => self::TYPE_INT, 'required' => true),
            'id_currency' => array('type' => self::TYPE_INT, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public function getOrderCommissionDetails($id_order)
    {
        $details = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_orders_details` WHERE `id_order` = '.(int) $id_order);
        if ($details) {
            return $details;
        }

        return false;
    }

    public function getSellerOrderedProductDetails($id_order, $lang_id = false)
    {
        if (!$lang_id) {
            $lang_id = Configuration::get('PS_LANG_DEFAULT');
        }
        $details = Db::getInstance()->executeS(
            'SELECT ordd.* , msi.*, msil.*, c.*, mssp.`id` as `mp_id_product`, mssp.`id_seller`, mssp.`id_ps_product` 
            FROM `'._DB_PREFIX_.'order_detail` ordd
            LEFT JOIN `'._DB_PREFIX_.'marketplace_seller_product` mssp ON (mssp.`id_ps_product` = ordd.`product_id`)
            LEFT JOIN `'._DB_PREFIX_.'marketplace_seller_info` msi ON (msi.`id` = mssp.`id_seller`)
            LEFT JOIN `'._DB_PREFIX_.'marketplace_seller_info_lang` msil ON (msi.`id` = msil.`id` AND msil.`id_lang` = '.(int) $lang_id.')
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = msi.`seller_customer_id`)
            WHERE ordd.`id_order`= '.(int) $id_order
        );
        if ($details) {
            return $details;
        }

        return false;
    }

    public function getSellerProductsInOrder($id_order)
    {
        $order_detail = Db::getInstance()->executeS(
            'SELECT mp_com_calc.`product_id`, mp_com_calc.`product_name`, mp_sel_ord.`seller_shop`, mp_sel_info.`id`, mp_sel_info.`seller_name`, mp_sel_info.`business_email`
        	FROM `'._DB_PREFIX_.'marketplace_seller_orders_details` mp_com_calc
        	INNER JOIN  `'._DB_PREFIX_.'marketplace_seller_orders` mp_sel_ord ON (mp_com_calc.`id_seller_order` = `mp_sel_ord`.id) 
        	INNER JOIN `'._DB_PREFIX_.'marketplace_seller_info` mp_sel_info ON (mp_sel_ord.`seller_shop` = mp_sel_info.`shop_name_unique`) 
        	WHERE `mp_com_calc`.id_order='.$id_order
        );
        if ($order_detail) {
            return $order_detail;
        }

        return false;
    }

    public static function updateMpCombinationQtyByPsIdAttribute($idAttribute, $quantity)
    {
        $mpCombinationMap = Db::getInstance()->getRow('SELECT *
            FROM `'._DB_PREFIX_.'mp_combination_map`
            WHERE `id_ps_product_attribute` = '.(int) $idAttribute);

        if ($mpCombinationMap) {
            $mpIdProductAttribute = $mpCombinationMap['mp_id_product_attribute'];
            $currentCombinationQty = Db::getInstance()->getValue('SELECT `mp_quantity`
                FROM `'._DB_PREFIX_.'mp_product_attribute`
                WHERE `mp_id_product_attribute` = '.(int) $mpIdProductAttribute);

            $combinationQty = $currentCombinationQty - $quantity;
            return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'mp_product_attribute`
                SET `mp_quantity` = '.$combinationQty.'
                WHERE `mp_id_product_attribute` = '.(int) $mpIdProductAttribute);
        }

        return false;
    }

    public function getAllOrders()
    {
        $details = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_orders_details`');
        if ($details) {
            return $details;
        }

        return false;
    }
}
