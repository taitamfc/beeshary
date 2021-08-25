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

class MpStoreProductAvailable extends ObjectModel
{
    public $id_product;
    public $availabe_store_pickup;

    public static $definition = array(
        'table' => 'mp_store_pickup_available',
        'primary' => 'id_mp_store_pickup_available',
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT),
            'availabe_store_pickup' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    public static function availableForStorePickup($idProduct)
    {
        return Db::getInstance()->getValue(
            'SELECT `availabe_store_pickup`
            FROM `'._DB_PREFIX_.'mp_store_pickup_available`
            WHERE `id_product` = '.(int)$idProduct
        );
    }

    public static function getAvailablePickupId($idProduct)
    {
        return Db::getInstance()->getValue(
            'SELECT `id_mp_store_pickup_available`
            FROM `'._DB_PREFIX_.'mp_store_pickup_available`
            WHERE `id_product` = '.(int)$idProduct
        );
    }

    public static function getIdCarrierByIdCart($idCart)
    {
        return Db::getInstance()->executeS(
            'SELECT `id_carrier`
            FROM `'._DB_PREFIX_.'orders`
            WHERE `id_cart` = '.(int)$idCart
        );
    }

    public static function deletePickUpProductAvailable($idProduct)
    {
        return Db::getInstance()->executeS(
            'DELETE FROM `'._DB_PREFIX_.'mp_store_pickup_available`
            WHERE `id_product` = '.(int)$idProduct
        );
    }

    public function getCarrierByIdProduct($product_list)
    {
        $warehouse_count_by_address = array();

        $stock_management_active = Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT');
        foreach ($product_list as &$product) {
            if (!isset($warehouse_count_by_address[$product['id_address_delivery']])) {
                $warehouse_count_by_address[$product['id_address_delivery']] = array();
            }

            $product['warehouse_list'] = array();

            if ($stock_management_active
                && (int)$product['advanced_stock_management'] == 1
            ) {
                $warehouse_list = Warehouse::getProductWarehouseList($product['id_product'], $product['id_product_attribute'], $this->context->cart->id_shop);
                if (count($warehouse_list) == 0) {
                    $warehouse_list = Warehouse::getProductWarehouseList(
                        $product['id_product'],
                        $product['id_product_attribute']
                    );
                }
                // Does the product is in stock ?
                // If yes, get only warehouse where the product is in stock

                $warehouse_in_stock = array();
                $manager = StockManagerFactory::getManager();

                foreach ($warehouse_list as $warehouse) {
                    $product_real_quantities = $manager->getProductRealQuantities(
                        $product['id_product'],
                        $product['id_product_attribute'],
                        array($warehouse['id_warehouse']),
                        true
                    );

                    if ($product_real_quantities > 0 || Pack::isPack((int)$product['id_product'])) {
                        $warehouse_in_stock[] = $warehouse;
                    }
                }

                if (!empty($warehouse_in_stock)) {
                    $warehouse_list = $warehouse_in_stock;
                } else {
                }
            } else {
                //simulate default warehouse
                $warehouse_list = array(0 => array('id_warehouse' => 0));
            }

            foreach ($warehouse_list as $warehouse) {
                $product['warehouse_list'][$warehouse['id_warehouse']] = $warehouse['id_warehouse'];
                if (!isset($warehouse_count_by_address[$product['id_address_delivery']][$warehouse['id_warehouse']])) {
                    $warehouse_count_by_address[$product['id_address_delivery']][$warehouse['id_warehouse']] = 0;
                }

                $warehouse_count_by_address[$product['id_address_delivery']][$warehouse['id_warehouse']]++;
            }
        }
        unset($product);

        arsort($warehouse_count_by_address);

        // Step 2 : Group product by warehouse
        $productCarrier = array();
        foreach ($product_list as &$product) {
            $product['carrier_list'] = array();
            $id_warehouse = 0;
            foreach ($warehouse_count_by_address[$product['id_address_delivery']] as $id_war => $val) {
                if (array_key_exists((int)$id_war, $product['warehouse_list'])) {
                    $carrierList =  Carrier::getAvailableCarrierList(new Product((int)$product['id_product']), $id_war);
                    if (!$id_warehouse) {
                        $id_warehouse = (int)$id_war;
                    }
                    $productCarrier[$product['id_product']] = $carrierList;
                }
            }
        }
        return $productCarrier;
    }

    public static function restrictStorePickUp($carrierList, $idProduct)
    {
        $context = Context::getContext();
        $storeConfiguration = MpStoreConfiguration::getStoreConfigurationByIdProduct($idProduct);
        if ($storeConfiguration && $storeConfiguration['enable_country']) {
            $countries = json_decode($storeConfiguration['countries']);
            $address = new Address($context->cart->id_address_delivery);
            $idCountry = $address->id_country;
            if (!in_array($idCountry, $countries)) {
                $carriers = array();
                foreach ($carrierList as $key => $carrier) {
                    if (Configuration::get('MP_STORE_ID_CARRIER') != $carrier) {
                        $carriers[$key] = $carrier;
                    }
                }
                $carrierList = $carriers;
            }
        }

        if (isset($context->cookie->mpForcePickUpCarrier)
            && $context->cookie->mpForcePickUpCarrier
        ) {
            foreach ($carrierList as $key => $carrier) {
                if (Configuration::get('MP_STORE_ID_CARRIER') == $carrier) {
                    $carriers[$key] = $carrier;
                    return $carriers;
                }
            }
        }

        return $carrierList;
    }
}
