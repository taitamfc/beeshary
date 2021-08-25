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

class CarrierProductMap extends ObjectModel
{
    public $id;
    public $id_cart;
    public $id_carrier;
    public $id_seller;
    public $id_product;
    public $id_product_attribute;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'mp_carrierproduct_map',
        'primary' => 'id',
        'fields' => array(
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_carrier' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function getCarrierDetailByIdCart($id_cart)
    {
        if ($id_cart) {
            $sql = 'SELECT `id_carrier`, `id_product`, `id_product_attribute` FROM `'._DB_PREFIX_.'mp_carrierproduct_map` WHERE `id_cart` = '.$id_cart;
            $carrierProducts = array();

            $result = Db::getInstance()->executeS($sql);
            if ($result) {
                foreach ($result as $value) {
                    $carrierProducts[$value['id_carrier']][$value['id_product']][] = $value['id_product_attribute'];
                }

                return $carrierProducts;
            }
        }

        return false;
    }

    public function carrierUsed($id_cart)
    {
        $sql = 'SELECT DISTINCT `id_carrier` FROM `'._DB_PREFIX_.'mp_carrierproduct_map` WHERE `id_cart` = '.$id_cart;

        $result = Db::getInstance()->executeS($sql);
        if ($result) {
            return $result;
        }

        return false;
    }

    public function getCarrierProducts($id_carrier, $id_cart)
    {
        $sql = 'SELECT `id_product`, `id_product_attribute` FROM `'._DB_PREFIX_.'mp_carrierproduct_map` WHERE `id_carrier` = '.$id_carrier.' AND `id_cart` = '.$id_cart;

        $result = Db::getInstance()->executeS($sql);
        if ($result) {
            return $result;
        }

        return false;
    }

    public function getIdCarrierByProduct($id_product, $id_prod_attr, $id_cart)
    {
        $sql = 'SELECT `id_carrier` FROM `'._DB_PREFIX_.'mp_carrierproduct_map` WHERE `id_product` = '.$id_product.' AND `id_product_attribute` = '.$id_prod_attr.' AND `id_cart` = '.$id_cart;

        $result = Db::getInstance()->getValue($sql);
        if ($result) {
            return $result;
        }

        return false;
    }

    public function deleteDataBycartId($id_cart)
    {
        $result = Db::getInstance()->delete('mp_carrierproduct_map', '`id_cart` = '.(int) $id_cart);
        if ($result) {
            return $result;
        }

        return false;
    }

    public function getSellerIdByIdProd($id_product)
    {
        $sql = 'SELECT `id_seller` FROM `'._DB_PREFIX_.'wk_mp_seller_product` WHERE `id_ps_product` = '.(int) $id_product;

        $id_seller = Db::getInstance()->getValue($sql);
        if ($id_seller) {
            return $id_seller;
        }

        return false;
    }

    public function getSellerDetailByIdProd($id_product, $id_lang)
    {
        $sql = 'SELECT msp.`id_seller`, CONCAT(msi.`seller_firstname`, \' \', msi.`seller_lastname`) AS `seller_name`,
                msil.`shop_name`, msi.`link_rewrite` AS `shop_link_rewrite`
                FROM `'._DB_PREFIX_.'wk_mp_seller_product` AS msp
                INNER JOIN `'._DB_PREFIX_.'wk_mp_seller` AS msi ON (msi.id_seller = msp.id_seller)
                INNER JOIN `'._DB_PREFIX_.'wk_mp_seller_lang` AS msil ON (msil.id_seller = msi.id_seller AND msil.id_lang = '.$id_lang.')
                WHERE msp.id_ps_product = '.$id_product;

        $sellerDtl = Db::getInstance()->getRow($sql);
        if ($sellerDtl) {
            return $sellerDtl;
        }

        return false;
    }

    public function getSellerShopNameByIdProduct($id_product)
    {
        $sql = 'SELECT msi.`link_rewrite`
                FROM '._DB_PREFIX_.'wk_mp_seller_product AS msp
                INNER JOIN `'._DB_PREFIX_.'wk_mp_seller` AS msi ON (msi.id_seller = msp.id_seller)
                WHERE msp.id_ps_product='.$id_product;

        $linkRewrite = Db::getInstance()->getValue($sql);
        if ($linkRewrite) {
            return $linkRewrite;
        }

        return false;
    }

    public function insertSelectedDeliveryOption($delivery_option_list)
    {
        $delivery_option = array();
        foreach ($delivery_option_list as $id_address => $options) {
            foreach ($options as $key => $option) {
                if (Configuration::get('PS_CARRIER_DEFAULT') == -1 && $option['is_best_price']) {
                    $delivery_option[$id_address] = $key;
                    break;
                } elseif (Configuration::get('PS_CARRIER_DEFAULT') == -2 && $option['is_best_grade']) {
                    $delivery_option[$id_address] = $key;
                    break;
                } elseif ($option['unique_carrier'] && in_array(Configuration::get('PS_CARRIER_DEFAULT'), array_keys($option['carrier_list']))) {
                    $delivery_option[$id_address] = $key;
                    break;
                }
            }
            reset($options);
            if (!isset($delivery_option[$id_address])) {
                $delivery_option[$id_address] = key($options);
            }
        }

        $context = Context::getContext();
        foreach ($delivery_option_list as $id_address => $selected_delivery_option) {
            foreach ($selected_delivery_option[$delivery_option[$id_address]]['carrier_list'] as $id_carrier => $carrier_data) {
                foreach ($carrier_data['product_list'] as $product) {
                    $id_seller = $this->getSellerIdByIdProd($product['id_product']);
                    $id_seller = $id_seller ? $id_seller : 0;
                    $obj_cp_map = new self();
                    $obj_cp_map->id_cart = $context->cart->id;
                    $obj_cp_map->id_carrier = $id_carrier;
                    $obj_cp_map->id_seller = $id_seller;
                    $obj_cp_map->id_product = $product['id_product'];
                    $obj_cp_map->id_product_attribute = $product['id_product_attribute'];
                    $obj_cp_map->save();
                }
            }
        }

        return true;
    }

    public function createFinalDeliveryOptionList($delivery_option_list, $productInfo, $carrierInstance, $free_carriers_rules, $default_country)
    {
        if (Module::isInstalled('marketplace')) {
            require_once _PS_MODULE_DIR_.'mpcartordersplit/classes/CarrierProductMap.php';
        }

        $dummy_option_list = array();
        $carrier_list = array();
        $context = Context::getContext();

        $carrierUsed = $this->carrierUsed($context->cart->id);
        foreach ($delivery_option_list as $id_address => $delivery_option) {
            if ($id_address) {
                $address = new Address($id_address);
                $country = new Country($address->id_country);
            } else {
                $country = $default_country;
            }

            $total_price_with_tax = 0;
            $total_price_without_tax = 0;
            $total_price_without_tax_with_rules = 0;
            $primary_key = '';
            $position = 0;

            foreach ($carrierUsed as $carrier_cont) {
                $primary_key .= $carrier_cont['id_carrier'].',';
                $carrier_products = $this->getCarrierProducts($carrier_cont['id_carrier'], $context->cart->id);
                foreach ($carrier_products as $product_cont) {
                    if (isset($productInfo[$product_cont['id_product']]) &&
                        isset($productInfo[$product_cont['id_product']][$product_cont['id_product_attribute']])
                    ) {
                        $carrier_list[$carrier_cont['id_carrier']]['product_list'][] = $productInfo[$product_cont['id_product']][$product_cont['id_product_attribute']];
                    } else {
                        $this->deleteDataBycartId($context->cart->id);
                        $this->insertSelectedDeliveryOption($delivery_option_list);

                        Tools::redirect($context->link->getPageLink($context->controller->php_self));
                    }
                }

                if (!isset($carrierInstance[$carrier_cont['id_carrier']])) {
                    $carrierInstance[$carrier_cont['id_carrier']] = new Carrier($carrier_cont['id_carrier']);
                    if (file_exists(_PS_SHIP_IMG_DIR_.$carrier_cont['id_carrier'].'.jpg')) {
                        $carrierInstance[$carrier_cont['id_carrier']]->logo = _THEME_SHIP_DIR_.$carrier_cont['id_carrier'].'.jpg';
                    } else {
                        $carrierInstance[$carrier_cont['id_carrier']]->logo = false;
                    }

                    $carrierInstance[$carrier_cont['id_carrier']]->package_list[] = 0;
                }

                $carrier_list[$carrier_cont['id_carrier']]['instance'] = $carrierInstance[$carrier_cont['id_carrier']];
                $carrier_list[$carrier_cont['id_carrier']]['logo'] = $carrierInstance[$carrier_cont['id_carrier']]->logo;
                $carrier_list[$carrier_cont['id_carrier']]['package_list'] = $carrierInstance[$carrier_cont['id_carrier']]->package_list;

                $price_with_tax = $context->cart->getPackageShippingCost((int) $carrier_cont['id_carrier'], true, $country, $carrier_list[$carrier_cont['id_carrier']]['product_list']);
                $price_without_tax = $context->cart->getPackageShippingCost((int) $carrier_cont['id_carrier'], false, $country, $carrier_list[$carrier_cont['id_carrier']]['product_list']);

                $carrier_list[$carrier_cont['id_carrier']]['price_with_tax'] = $price_with_tax;
                $carrier_list[$carrier_cont['id_carrier']]['price_without_tax'] = $price_without_tax;

                $total_price_with_tax += $price_with_tax;
                $total_price_without_tax += $price_without_tax;
                $position += $carrierInstance[$carrier_cont['id_carrier']]->position;

                if (!$total_price_without_tax_with_rules) {
                    $total_price_without_tax_with_rules = (in_array($carrier_cont['id_carrier'], $free_carriers_rules)) ? 0 : $price_without_tax;
                }
            }

            $dummy_option_list[$id_address][$primary_key]['carrier_list'] = $carrier_list;
            $dummy_option_list[$id_address][$primary_key]['total_price_with_tax'] = $total_price_with_tax;
            $dummy_option_list[$id_address][$primary_key]['total_price_without_tax'] = $total_price_without_tax;
            $dummy_option_list[$id_address][$primary_key]['unique_carrier'] = (count($carrierUsed) <= 1);
            $dummy_option_list[$id_address][$primary_key]['position'] = $position / count($carrierUsed);
            $dummy_option_list[$id_address][$primary_key]['is_free'] = !$total_price_without_tax_with_rules ? true : false;
            $dummy_option_list[$id_address][$primary_key]['is_best_grade'] = 1;
            $dummy_option_list[$id_address][$primary_key]['is_best_price'] = 1;
        }

        unset($delivery_option_list);
        unset($carrier_list);

        return $dummy_option_list;
    }

    public function setCarrierToSellerProd($id_cart, $id_carrier, $id_seller)
    {
        $sql = 'UPDATE `'._DB_PREFIX_.'mp_carrierproduct_map`
                SET `id_carrier`= '.(int) $id_carrier.'
                WHERE `id_seller` = '.(int) $id_seller.' AND `id_cart` = '.(int) $id_cart.' AND `id_carrier` !='.(int) $id_carrier;

        $result = Db::getInstance()->execute($sql);

        return $result;
    }

    public function displayDataOfCartSplit($delivery_option_list = false)
    {
        $context = Context::getContext();
        if (!$delivery_option_list) {
            $delivery_option_list = $context->cart->getDeliveryOptionList();
        }

        $id_lang = $context->language->id;
        $id_cart = $context->cart->id;

        $carrierInstance = array();
        $sellerData = array();
        $commonSellerCarrier = array();
        $seller_list = array();
        $PS_CARRIER_DEFAULT = Configuration::get('PS_CARRIER_DEFAULT');
        $dbEntryChange = 0;

        foreach ($delivery_option_list as $id_address => &$addressDeliveryList) {
            $address = new Address($id_address);
            $country = new Country($address->id_country);
            foreach ($addressDeliveryList as &$selected_carriers) {
                foreach ($selected_carriers['carrier_list'] as $id_carrier => $carrier_detail) {
                    foreach ($carrier_detail['product_list'] as $product) {
                        $id_seller = isset($product['id_seller']) ? $product['id_seller'] : 0;
                        if ($id_seller && !isset($seller_list[$id_seller]['seller_detail'])) {
                            $seller_list[$id_seller]['seller_detail'] = array(
                                    'id_seller' => $product['id_seller'],
                                    'shop_name' => $product['shop_name'],
                                    'shopcollection' => $product['seller_products_link'],
                                );
                        }
                        $seller_list[$id_seller]['product_carrier_list'][$id_carrier]['product_list'][] = $product;
                        $sellerData[$id_seller]['product_list'][] = $product;
                        $sellerData[$id_seller]['carrierWise'][$id_carrier][] = $product;
                        $sellerData[$id_seller]['selectedCarriers'][$id_carrier] = $id_carrier;

                        if (!isset($seller_list[$id_seller]['product_carrier_list'][$id_carrier]['instance'])
                            || !isset($seller_list[$id_seller]['product_carrier_list'][$id_carrier]['logo'])
                            || !isset($seller_list[$id_seller]['product_carrier_list'][$id_carrier]['package_list'])) {
                            $seller_list[$id_seller]['product_carrier_list'][$id_carrier]['instance'] = $carrier_detail['instance'];
                            $seller_list[$id_seller]['product_carrier_list'][$id_carrier]['logo'] = $carrier_detail['logo'];
                            $seller_list[$id_seller]['product_carrier_list'][$id_carrier]['package_list'] = $carrier_detail['package_list'];
                        }
                        if (!isset($commonSellerCarrier[$id_seller]['commonCarrier'])) {
                            $commonSellerCarrier[$id_seller]['commonCarrier'] = $product['carrier_list'];
                        } else {
                            if (count($commonSellerCarrier[$id_seller]['commonCarrier'])) {
                                $commonSellerCarrier[$id_seller]['commonCarrier'] = array_intersect($commonSellerCarrier[$id_seller]['commonCarrier'], $product['carrier_list']);
                            }
                        }
                    }
                }

                foreach ($commonSellerCarrier as $id_seller => &$sellerCarrier) {
                    $sellerData[$id_seller]['commonCarrier'] = $sellerCarrier['commonCarrier'];
                    if (count($sellerCarrier['commonCarrier'])) {
                        // Common carrier
                        $bestCarrier = array();
                        foreach ($sellerCarrier['commonCarrier'] as $id_carrier) {
                            if (!isset($carrierInstance[$id_carrier])) {
                                $carrierInstance[$id_carrier] = new Carrier($id_carrier);
                                if (file_exists(_PS_SHIP_IMG_DIR_.$id_carrier.'.jpg')) {
                                    $carrierInstance[$id_carrier]->logo = _THEME_SHIP_DIR_.$id_carrier.'.jpg';
                                } else {
                                    $carrierInstance[$id_carrier]->logo = false;
                                }
                            }

                            $price_with_tax = $context->cart->getPackageShippingCost((int) $id_carrier, true, $country, $sellerData[$id_seller]['product_list']);
                            $price_without_tax = $context->cart->getPackageShippingCost((int) $id_carrier, false, $country, $sellerData[$id_seller]['product_list']);

                            $sellerCarrierDetail = &$sellerData[$id_seller]['commonCarrierDetail'][$id_carrier];

                            $sellerCarrierDetail['price_with_tax'] = $price_with_tax;
                            $sellerCarrierDetail['displayPriceWithTax'] = Tools::displayPrice($price_with_tax);
                            $sellerCarrierDetail['price_without_tax'] = $price_without_tax;
                            $sellerCarrierDetail['displayPriceWithoutTax'] = Tools::displayPrice($price_without_tax);
                            $sellerCarrierDetail['best_price'] = 0;
                            $sellerCarrierDetail['best_grade'] = 0;
                            $sellerCarrierDetail['name'] = $carrierInstance[$id_carrier]->name;
                            $sellerCarrierDetail['logo'] = $carrierInstance[$id_carrier]->logo;
                            $sellerCarrierDetail['delay'] = $carrierInstance[$id_carrier]->delay[$id_lang];
                            $sellerCarrierDetail['position'] = $carrierInstance[$id_carrier]->position;
                            $sellerCarrierDetail['grade'] = $carrierInstance[$id_carrier]->grade;
                            $sellerCarrierDetail['is_free'] = $price_without_tax <= 0 ? 1 : '';

                            if (!isset($bestCarrier['best_price'])) {
                                $bestCarrier['best_price']['price'] = $price_with_tax;
                                $bestCarrier['best_price']['id_carrier'] = $id_carrier;
                            } else {
                                if ($price_with_tax < $bestCarrier['best_price']['price']) {
                                    $bestCarrier['best_price']['price'] = $price_with_tax;
                                    $bestCarrier['best_price']['id_carrier'] = $id_carrier;
                                }
                            }

                            if (!isset($bestCarrier['best_grade'])) {
                                $bestCarrier['best_grade']['grade'] = $sellerCarrierDetail['grade'];
                                $bestCarrier['best_grade']['id_carrier'] = $id_carrier;
                            } else {
                                if ($sellerCarrierDetail['grade'] > $bestCarrier['best_grade']['grade']) {
                                    $bestCarrier['best_grade']['grade'] = $sellerCarrierDetail['grade'];
                                    $bestCarrier['best_grade']['id_carrier'] = $id_carrier;
                                }
                            }
                        }

                        // Set best price and grade carrier
                        $sellerData[$id_seller]['commonCarrierDetail'][$bestCarrier['best_price']['id_carrier']]['best_price'] = 1;
                        $sellerData[$id_seller]['commonCarrierDetail'][$bestCarrier['best_grade']['id_carrier']]['best_grade'] = 1;

                        if (count($sellerData[$id_seller]['carrierWise']) > 1) {
                            // Make all seller product have same carrier in DB
                            $selected_id_carrier = 0;
                            $best_price_carrier = 0;

                            foreach ($sellerCarrier['commonCarrier'] as $id_carrier) {
                                $option = $sellerData[$id_seller]['commonCarrierDetail'][$id_carrier];
                                if ($PS_CARRIER_DEFAULT == -1 && $option['best_price']) {
                                    $selected_id_carrier = $id_carrier;
                                    break;
                                } elseif ($PS_CARRIER_DEFAULT == -2 && $option['best_grade']) {
                                    $selected_id_carrier = $id_carrier;
                                    break;
                                } elseif ($PS_CARRIER_DEFAULT == $id_carrier) {
                                    $selected_id_carrier = $id_carrier;
                                    break;
                                } else {
                                    if ($option['best_price'] && !$best_price_carrier) {
                                        $best_price_carrier = $id_carrier;
                                    }
                                }
                            }
                            $selected_id_carrier = $selected_id_carrier ? $selected_id_carrier : $best_price_carrier;
                            $this->setCarrierToSellerProd($id_cart, $selected_id_carrier, $id_seller);
                            if (!$dbEntryChange) {
                                $dbEntryChange = 1;
                            }
                        }
                    } else {
                        // Different Carrier
                        foreach ($sellerData[$id_seller]['carrierWise'] as $id_carrier => $product_list) {
                            if (!isset($carrierInstance[$id_carrier])) {
                                $carrierInstance[$id_carrier] = new Carrier($id_carrier);
                                if (file_exists(_PS_SHIP_IMG_DIR_.$id_carrier.'.jpg')) {
                                    $carrierInstance[$id_carrier]->logo = _THEME_SHIP_DIR_.$id_carrier.'.jpg';
                                } else {
                                    $carrierInstance[$id_carrier]->logo = false;
                                }
                            }

                            $price_with_tax = $context->cart->getPackageShippingCost((int) $id_carrier, true, $country, $product_list);
                            $price_without_tax = $context->cart->getPackageShippingCost((int) $id_carrier, false, $country, $product_list);

                            if (!isset($sellerData[$id_seller]['diffCarrierTotal'])) {
                                $sellerData[$id_seller]['diffCarrierTotal'] = array();
                                $sellerData[$id_seller]['diffCarrierTotal']['price_with_tax'] = 0;
                                $sellerData[$id_seller]['diffCarrierTotal']['price_without_tax'] = 0;
                            }
                            $sellerData[$id_seller]['diffCarrierTotal']['price_with_tax'] += $price_with_tax;
                            $sellerData[$id_seller]['diffCarrierTotal']['price_without_tax'] += $price_without_tax;

                            $sellerData[$id_seller]['diffCarrierTotal']['displayPriceWithTax'] = Tools::displayPrice($sellerData[$id_seller]['diffCarrierTotal']['price_with_tax']);
                            $sellerData[$id_seller]['diffCarrierTotal']['displayPriceWithoutTax'] = Tools::displayPrice($sellerData[$id_seller]['diffCarrierTotal']['price_without_tax']);

                            $sellerCarrierDetail = &$sellerData[$id_seller]['diffCarrierDetail'][$id_carrier];
                            $sellerCarrierDetail['price_with_tax'] = $price_with_tax;
                            $sellerCarrierDetail['displayPriceWithTax'] = Tools::displayPrice($price_with_tax);
                            $sellerCarrierDetail['price_without_tax'] = $price_without_tax;
                            $sellerCarrierDetail['displayPriceWithoutTax'] = Tools::displayPrice($price_without_tax);
                            $sellerCarrierDetail['name'] = $carrierInstance[$id_carrier]->name;
                            $sellerCarrierDetail['logo'] = $carrierInstance[$id_carrier]->logo;
                            $sellerCarrierDetail['delay'] = $carrierInstance[$id_carrier]->delay[$id_lang];
                            $sellerCarrierDetail['position'] = $carrierInstance[$id_carrier]->position;
                            $sellerCarrierDetail['grade'] = $carrierInstance[$id_carrier]->grade;
                            $sellerCarrierDetail['is_free'] = $price_without_tax <= 0 ? 1 : '';
                        }
                    }
                }

                if ($dbEntryChange) {
                    Tools::redirect($context->link->getPageLink($context->controller->php_self));
                }

                foreach ($seller_list as $id_seller => &$cartSellerDtl) {
                    foreach ($cartSellerDtl['product_carrier_list'] as $id_carrier => &$carrierProd) {
                        $price_with_tax = $context->cart->getPackageShippingCost((int) $id_carrier, true, $country, $carrierProd['product_list']);
                        $price_without_tax = $context->cart->getPackageShippingCost((int) $id_carrier, false, $country, $carrierProd['product_list']);

                        $carrierProd['price_with_tax'] = $price_with_tax;
                        $carrierProd['displayPriceWithTax'] = Tools::displayPrice($price_with_tax);
                        $carrierProd['price_without_tax'] = $price_without_tax;
                        $carrierProd['displayPriceWithoutTax'] = Tools::displayPrice($price_without_tax);
                    }

                    $cartSellerDtl['commonCarrier'] = $sellerData[$id_seller]['commonCarrier'];
                    if (count($sellerData[$id_seller]['commonCarrier'])) {
                        $cartSellerDtl['commonCarrierDetail'] = $sellerData[$id_seller]['commonCarrierDetail'];
                    } else {
                        $cartSellerDtl['diffCarrierDetail'] = $sellerData[$id_seller]['diffCarrierDetail'];
                        $cartSellerDtl['diffCarrierTotal'] = $sellerData[$id_seller]['diffCarrierTotal'];
                    }
                    $cartSellerDtl['selectedCarriers'] = $sellerData[$id_seller]['selectedCarriers'];
                }

                ksort($seller_list);
                $selected_carriers['carrier_list'] = $seller_list;
            }
        }

        unset($carrierInstance);
        unset($sellerData);
        unset($commonSellerCarrier);
        unset($seller_list);

        return $delivery_option_list;
    }
}
