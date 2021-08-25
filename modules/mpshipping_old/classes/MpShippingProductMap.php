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

class Mpshippingproductmap extends ObjectModel
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

    public function getMpShippingProductMapDetails($mp_product_id)
    {
        $mp_shipping_product_map_details = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_product_map` WHERE `mp_product_id` = '.(int) $mp_product_id.'
			');

        if (empty($mp_shipping_product_map_details)) {
            return false;
        } else {
            return $mp_shipping_product_map_details;
        }
    }

    public function deleteMpShippingProductMapOnDeactivate($mp_shipping_id)
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'mp_shipping_product_map`
			WHERE `mp_shipping_id` = '.(int) $mp_shipping_id.'');
    }

    public function deleteMpShippingProductMapDetails($mp_product_id)
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'mp_shipping_product_map`
			WHERE `mp_product_id` = '.(int) $mp_product_id.'');
    }

    public function checkMpProduct($id_product)
    {
        $mp_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_mp_product` FROM `'._DB_PREFIX_.'wk_mp_seller_product` WHERE `id_ps_product` = '.(int) $id_product);
        if ($mp_product) {
            return $mp_product;
        }

        return false;
    }

    public function setProductCarrier($id_product, $carr_ref)
    {
        $obj_prod = new Product($id_product);
        //$ps_carriers = $obj_prod->getCarriers();
        //if (empty($ps_carriers)) {
        $obj_prod->setCarriers($carr_ref);
        //}
    }

    public function getMpShippingForProducts($id_mp_shipping)
    {
        $mp_ship = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_shipping_product_map` WHERE `mp_shipping_id` = '.(int) $id_mp_shipping);
        if ($mp_ship) {
            return $mp_ship;
        } else {
            return false;
        }
    }

    public function assignCarriersToSellerProduct($id_product, $id_lang)
    {
        $carr_ref = array();
        $AllPs_Carriers_Only = Mpshippingmethod::getOnlyPrestaCarriers($id_lang);
        if ($AllPs_Carriers_Only) {
            foreach ($AllPs_Carriers_Only as $ps_carriers) {
                $carr_ref[] = $ps_carriers['id_reference'];
            }

            $this->setProductCarrier($id_product, $carr_ref);
        }

        return true;
    }

    public function assignAdminDefaultCarriersToSellerProduct($id_product)
    {
        $admin_def_shipping = unserialize(Configuration::get('MP_SHIPPING_ADMIN_DEFAULT'));
        if ($admin_def_shipping) {
            $this->setProductCarrier($id_product, $admin_def_shipping);
        }

        return true;
    }

    public function updateNewShippingWithOldShippingOnProducts($new_shipping_id, $old_shipping_id)
    {
        return Db::getInstance()->update('mp_shipping_product_map', array('mp_shipping_id' => $new_shipping_id), 'mp_shipping_id = '.(int) $old_shipping_id);
    }
}
