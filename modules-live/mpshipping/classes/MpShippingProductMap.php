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

class MpShippingProductMap extends ObjectModel
{
    public $id;
    public $mp_shipping_id;
    public $id_ps_reference;
    public $mp_product_id;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'mp_shipping_product_map',
        'primary' => 'id',
        'fields' => array(
            'mp_shipping_id' => array('type' => self::TYPE_INT ,'validate' => 'isUnsignedInt', 'required' => true),
            'id_ps_reference' => array('type' => self::TYPE_INT ,'validate' => 'isUnsignedInt', 'required' => true),
            'mp_product_id' => array('type' => self::TYPE_INT ,'validate' => 'isUnsignedInt', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public function getMpShippingProductMapDetails($mpProductId)
    {
        $MpShippingProductMapDetails = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_product_map` WHERE `mp_product_id` = '.(int) $mpProductId.'
			');

        if (empty($MpShippingProductMapDetails)) {
            return false;
        } else {
            return $MpShippingProductMapDetails;
        }
    }

    public function deleteMpShippingProductMapOnDeactivate($mpShippingId)
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'mp_shipping_product_map`
			WHERE `mp_shipping_id` = '.(int) $mpShippingId.'');
    }

    public function deleteMpShippingProductMapDetails($mpProductId)
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'mp_shipping_product_map`
			WHERE `mp_product_id` = '.(int) $mpProductId.'');
    }

    public function checkMpProduct($idProduct)
    {
        $mpProduct = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_mp_product` FROM `'._DB_PREFIX_.'wk_mp_seller_product` WHERE `id_ps_product` = '.(int) $idProduct);
        if ($mpProduct) {
            return $mpProduct;
        }

        return false;
    }

    public function setProductCarrier($idProduct, $carrRef)
    {
        $objProd = new Product($idProduct);
        //$psCarriers = $objProd->getCarriers();
        //if (empty($psCarriers)) {
        $objProd->setCarriers($carrRef);
        //}
    }

    public function getMpShippingForProducts($idMpShipping)
    {
        $mpShip = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_product_map` WHERE `mp_shipping_id` = '.(int) $idMpShipping);
        if ($mpShip) {
            return $mpShip;
        } else {
            return false;
        }
    }

    public function assignCarriersToSellerProduct($idProduct, $idLang)
    {
        $carrRef = array();
        $allPsCarriersOnly = MpShippingMethod::getOnlyPrestaCarriers($idLang);
        if ($allPsCarriersOnly) {
            foreach ($allPsCarriersOnly as $psCarriers) {
                $carrRef[] = $psCarriers['id_reference'];
            }

            $this->setProductCarrier($idProduct, $carrRef);
        }

        return true;
    }

    public function assignAdminDefaultCarriersToSellerProduct($idProduct)
    {
        $adminDefShipping = array();
        if (Configuration::get('MP_SHIPPING_ADMIN_DEFAULT')) {
            $adminDefShipping = unserialize(Configuration::get('MP_SHIPPING_ADMIN_DEFAULT'));
        }
        if ($adminDefShipping) {
            $this->setProductCarrier($idProduct, $adminDefShipping);
        }

        return true;
    }

    public function updateNewShippingWithOldShippingOnProducts($newShippingId, $oldShippingId)
    {
        return Db::getInstance()->update('mp_shipping_product_map', array('mp_shipping_id' => $newShippingId), 'mp_shipping_id = '.(int) $oldShippingId);
    }
}
