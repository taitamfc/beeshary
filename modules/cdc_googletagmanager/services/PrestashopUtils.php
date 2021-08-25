<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SAS Comptoir du Code
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SAS Comptoir du Code is strictly forbidden.
 * In order to obtain a license, please contact us: contact@comptoirducode.com
 *
 * @author    Vincent - Comptoir du Code
 * @copyright Copyright(c) 2015-2016 SAS Comptoir du Code
 * @license   Commercial license
 * @package   cdc_googletagmanager
 */

class PrestashopUtils {

	/**
     * Get an order by its cart id
     *
     * @param int $id_cart Cart id
     * @return array Order details
     */
    public static function getOrdersByCartId($id_cart)
    {
        $sql = 'SELECT `id_order`
                FROM `'._DB_PREFIX_.'orders`
                WHERE `id_cart` = '.(int)$id_cart
                    .Shop::addSqlRestriction();
        $result = Db::getInstance()->executeS($sql);
        return $result;
    }


    /**
     * Return the group of order associated to an order
     * (useful in case of multiwarehouse and splitted orders)
     */
    public static function getOrdersGroup($order_id) {
        // get cart
        $order = new Order($order_id);
        $id_cart = $order->id_cart;

        // get all orders associated
        $orders = PrestashopUtils::getOrdersByCartId($id_cart);

        return $orders;
    }

    /**
     * Return the number of orders from a client, different from the order
     * in parameter
     */
    public static function countCustomerOtherOrders($id_customer, $exclude_order_reference) {
      return (int) Db::getInstance()->getValue("
          SELECT count(DISTINCT `reference`) FROM `"._DB_PREFIX_."orders` WHERE `id_customer` = '".(int)$id_customer."' and `reference` <> '".pSQL($exclude_order_reference)."'
      ");
    }

    /**
     * Get attribute small name
     */
    public static function getAttributeSmall($id_product_attribute, $id_lang) {
        $result = Db::getInstance()->executeS('
            SELECT pac.`id_product_attribute`, al.`name` AS attribute_name
            FROM `'._DB_PREFIX_.'product_attribute_combination` pac
            LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
            LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
            LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (
                a.`id_attribute` = al.`id_attribute`
                AND al.`id_lang` = '.(int)$id_lang.'
            )
            WHERE pac.`id_product_attribute` = '.(int) $id_product_attribute
        );

        $attributeSmall = "";
        if(count($result)) {
            foreach ($result as $attribute) {
                if(!empty($attributeSmall)) {
                    $attributeSmall .= ', ';
                }
                $attributeSmall .= $attribute['attribute_name'];
            }
        }

        return $attributeSmall;
    }
}
