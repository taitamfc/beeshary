<?php
/**
* 2010-2019 Webkul
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class RelatedProductInfo
{
    public static function getProductsByCatId($id_category, $p = 0, $n = 0, $seller_id = false)
    {
        if ($seller_id) {
            $sql = 'SELECT mspc.`id_seller_product` FROM `'._DB_PREFIX_.'wk_mp_seller_product` AS msp'.
            ' LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_product_category` AS mspc'.
            ' ON (msp.`id_mp_product` = mspc.`id_seller_product`)'.
            ' WHERE msp.`id_seller` = '.(int)$seller_id.
            ' AND mspc.`id_category` = '.(int) $id_category;
        } else {
            $sql = 'SELECT `id_seller_product` FROM `'._DB_PREFIX_.'wk_mp_seller_product_category`'.
            ' where id_category ='.(int) $id_category;
        }
        if ($p) {
            $sql .= ' LIMIT '.(((int) $p - 1) * (int) $n).','.(int) $n;
        }
        $seller_product_ids = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($seller_product_ids) {
            return $seller_product_ids;
        }

        return false;
    }

    public static function getOrderDetailsByPsId($id_product)
    {
        $order_detail = Db::getInstance()->executeS(
            'SELECT * FROM '._DB_PREFIX_.'order_detail where product_id ='.(int) $id_product
        );
        if ($order_detail) {
            return $order_detail;
        }
        return false;
    }
}
