<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MpShippingFreeShipping extends ObjectModel
{
    public $free_shipping_start_price;
    public $free_shipping_start_weight;
    public $id_seller;

    public static $definition = array(
        'table' => 'mp_shipping_free_shipping',
        'primary' => 'id_mp_shipping_free_shipping',
        'fields' => array(
            'free_shipping_start_price' => array('type' => self::TYPE_FLOAT,),
            'free_shipping_start_weight' => array('type' => self::TYPE_FLOAT,),
            'id_seller' => array('type' => self::TYPE_INT),
        ),
    );

    public function getFreeShippingInfoByIdSeller($idSeller, $full = false) {
        $sql = 'SELECT `id_mp_shipping_free_shipping` FROM `'._DB_PREFIX_.'mp_shipping_free_shipping`
        WHERE `id_seller` = '.(int) $idSeller;
        if ($full) {
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'mp_shipping_free_shipping` WHERE `id_seller` = '.(int) $idSeller;
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        if (!empty($result)) {
            return $result;
        }

        return false;
    }

    public function deleteFreeShippingInfoByIdSeller($idSeller) {
        $sql = 'DELETE FROM `'._DB_PREFIX_.'mp_shipping_free_shipping`
        WHERE `id_seller` = '.(int) $idSeller;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
    }

    public static function getSellerIdByIdProd($id_product)
    {
        $sql = 'SELECT `id_seller` FROM `'._DB_PREFIX_.'wk_mp_seller_product` WHERE `id_ps_product` = '.(int) $id_product;

        $id_seller = Db::getInstance()->getValue($sql);
        if ($id_seller) {
            return $id_seller;
        }

        return false;
    }
}