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
require_once _PS_MODULE_DIR_.'marketplace/classes/WkMpProductAttribute.php';

class WkMpPackProduct extends ObjectModel
{
    public $id;
    public $new_mp_product_id;
    public $mp_product_id;
    public $mp_product_id_attribute;
    public $quantity;

    public static $definition = array(
        'table' => 'wk_mp_pack_product',
        'primary' => 'id',
        'fields' => array(
            'new_mp_product_id' => array('type' => self::TYPE_INT),
            'mp_product_id' => array('type' => self::TYPE_INT),
            'mp_product_id_attribute' => array('type' => self::TYPE_INT),
            'quantity' => array('type' => self::TYPE_INT),
        ),
    );

    public function isPackProductFieldUpdate($mp_id_prod, $is_pack_product)
    {
        return Db::getInstance()->update('wk_mp_seller_product', array('is_pack_product' => $is_pack_product), 'id_mp_product = '.(int) $mp_id_prod);
    }

    public function getProductByProdName($prod_letter, $id_seller, $prev_id_prod_str = false, $id_lang = false)
    {
        $virtual_pro_ids = '';
        if (Module::isInstalled('mpvirtualproduct') && Module::isEnabled('mpvirtualproduct')) {
            $virtual_pro = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_virtual_product`');
            if ($virtual_pro) {
                foreach ($virtual_pro as $value) {
                    $virtual_pro_ids .= "'".$value['mp_product_id']."',";
                }
            }
        }

        $sql = 'SELECT msp.`id_mp_product` AS mp_id_prod, msp.`id_seller`, mspl.`product_name`, msp.`id_ps_product` AS ps_id_prod FROM `'._DB_PREFIX_.'wk_mp_seller_product` AS msp 
			JOIN `'._DB_PREFIX_.'wk_mp_seller_product_lang` mspl ON (msp.`id_mp_product` = mspl.`id`)
			WHERE msp.`id_seller` = '.(int) $id_seller.' AND mspl.`id_lang` = '.(int) $id_lang." AND msp.`active` = 1 AND msp.`is_pack_product` = 0 AND `product_name` LIKE '%".$prod_letter."%'";

        if ($prev_id_prod_str) {
            $sql .= 'AND msp.id NOT IN ('.$prev_id_prod_str.')';
        }

        if ($virtual_pro_ids) {
            $new_virtual_pro_ids = Tools::substr($virtual_pro_ids, 0, -1);
            $sql .= 'AND msp.id NOT IN ('.$new_virtual_pro_ids.')';
        }

        return Db::getInstance()->executeS($sql);
    }

    /**
     * [getPackedProducts description]
     *
     * @param  int $newproductid
     * @return bool/array
     */
    public function getPackedProducts($newproductid)
    {
        if ($newproductid) {
            return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'wk_mp_pack_product` WHERE `new_mp_product_id` = '.(int) $newproductid);
        }

        return false;
    }

    /**
     * Get not packed marketplace seller products by $sellerid
     *
     * @param  int $sellerid
     * @return bool/array
     */
    public function getMpSellerProductsBySellerID($sellerid)
    {
        if ($sellerid) {
            return Db::getInstance()->executeS(
                'SELECT * FROM `'._DB_PREFIX_.'wk_mp_seller_product`
                WHERE `id_seller`='.(int) $sellerid.' AND `active` = 1 AND `is_pack_product` = 0'
            );
        }

        return false;
    }

    public function deletePrevMpPackedProduct($mpproductid)
    {
        if ($mpproductid) {
            return Db::getInstance()->delete('wk_mp_pack_product', 'new_mp_product_id = '.(int) $mpproductid);
        }

        return false;
    }

    public function deletePrevPsPackedProduct($psproductid)
    {
        if ($psproductid) {
            return Pack::deleteItems($psproductid);
        }

        return false;
    }

    public function isPackProduct($id)
    {
        if ($id) {
            return Db::getInstance()->getValue('SELECT `is_pack_product` FROM `'._DB_PREFIX_.'wk_mp_seller_product` WHERE `id_mp_product` = '.(int) $id);
        }

        return false;
    }

    public function addToPsPack($mpprodid, $psproductid)
    {
        $packedproduct = $this->getPackedProducts($mpprodid);
        $i = 0;
        $pspackproducts = array();
        foreach ($packedproduct as $packprod) {
            $mpSellerProduct = new WkMpSellerProduct($packprod['mp_product_id']);
            $ps_id_prod = $mpSellerProduct->id_ps_product;
            if ($ps_id_prod) {
                $ps_prod_attr = 0;
                if ($packprod['mp_product_id_attribute']) {
                    $ps_prod_attr = $this->getPsAttributeIdByMpAttributeId($packprod['mp_product_id_attribute']);
                }
                $pspackproducts[$i] = array(
                    'id_mp_pack' => $packprod['id'],
                    'id' => $ps_id_prod,
                    'qty' => $packprod['quantity'],
                    'ps_product_id_attribute' => $ps_prod_attr,
                    );
                ++$i;
            }
        }
        foreach ($pspackproducts as $pspackprod) {
            $params = array('for' => 'ps', 'pack_product_id' => $psproductid, 'item_product_id' => $pspackprod['id'], 'item_product_id_attribute' => $pspackprod['ps_product_id_attribute']);
            $is_duplicate = $this->checkIfDuplicateEntry($params);
            if (!$is_duplicate) {
                Pack::addItem($psproductid, $pspackprod['id'], $pspackprod['qty'], $pspackprod['ps_product_id_attribute']);
            } else {
                $obj_pack_mp = new self($pspackprod['id_mp_pack']);
                $obj_pack_mp->delete();
            }
        }
    }

    public function packItemDetails($mp_product_id)
    {
        Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'wk_mp_pack_product` WHERE `mp_product_id` = '.(int) $mp_product_id);
    }

    public static function isPackItem($mp_product_id)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'wk_mp_pack_product` WHERE `mp_product_id` = '.(int) $mp_product_id);
    }

    /**
     * Modified for MP3 version from 'mp_combination_map' table (which is removed) to 'wk_mp_product_attribute'
     *
     * @param int $ps_attr_id
     * @return array/bool
     */
    public function getMpAttributeIdByPsAttributeId($ps_attr_id)
    {
        return Db::getInstance()->getValue('SELECT `id_mp_product_attribute` FROM `'._DB_PREFIX_.'wk_mp_product_attribute` WHERE `id_ps_product_attribute` = '.(int) $ps_attr_id);
    }

    public function getPsAttributeIdByMpAttributeId($mp_attr_id)
    {
        $ps_prod_attr_id = Db::getInstance()->getValue('SELECT `id_ps_product_attribute` FROM `'._DB_PREFIX_.'wk_mp_product_attribute` WHERE `id_mp_product_attribute` = '.(int) $mp_attr_id);
        if (isset($ps_prod_attr_id) && $ps_prod_attr_id) {
            return $ps_prod_attr_id;
        } else {
            return 0;
        }
    }

    public static function checkProductCombination($mp_id_prod, $id_lang)
    {
        $combi_exist = 0;
        //if product has combination then seller can not make that product pack
        $obj_mp_combi = new WkMpProductAttribute();
        $prod_combination = $obj_mp_combi->getMpProductCombinations(1, $mp_id_prod, $id_lang);
        if ($prod_combination) {
            $combi_exist = 1;
        }

        return $combi_exist;
    }

    public static function changePackProductsStatus($active)
    {
        $all_pack_prod = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT DISTINCT `new_mp_product_id` FROM '._DB_PREFIX_.'wk_mp_pack_product');
        if ($all_pack_prod) {
            foreach ($all_pack_prod as $pack_prod) {
                $mpSellerProduct = new WkMpSellerProduct($pack_prod['new_mp_product_id']);
                $ps_id_prod = $mpSellerProduct->id_ps_product;
                if ($ps_id_prod) {
                    if ($active == 1) {
                        $product = new Product($ps_id_prod);
                        $product->active = 1;
                        $product->save();
                    } elseif ($active == 0) {
                        $product = new Product($ps_id_prod);
                        $product->active = 0;
                        $product->save();
                    }
                }
            }
        }

        return true;
    }

    /**
     * Changes product type to standard.
     * Send 1 for value of the variable $product_type_to to change into standard product
     *
     * @param int $product_type_to
     * @return bool/array
     */
    public static function changePsProductsType($product_type_to)
    {
        $all_pack_prod = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT DISTINCT `new_mp_product_id` FROM '._DB_PREFIX_.'wk_mp_pack_product');
        if ($all_pack_prod) {
            foreach ($all_pack_prod as $pack_prod) {
                $delete_pack = 0;
                $mpSellerProduct = new WkMpSellerProduct($pack_prod['new_mp_product_id']);
                $ps_id_prod = $mpSellerProduct->id_ps_product;
                if ($ps_id_prod) {
                    if ($product_type_to == 1) {
                        $product = new Product($ps_id_prod);
                        $delete_pack = $product->deletePack();
                        if ($delete_pack) {
                            Db::getInstance()->update('product', array('cache_is_pack' => 0), 'id_product = '.(int) $ps_id_prod);

                            return true;
                        }
                    }
                }
            }

            return false;
        }

        return true;
    }

    /**
     * Delete product pack details.
     *
     * @return array Deletion result
     */
    public function deleteMpPack($deleted_prod_id)
    {
        return Db::getInstance()->execute(
            'DELETE FROM `'._DB_PREFIX_.'wk_mp_pack_product`
			WHERE `new_mp_product_id` = '.(int) $deleted_prod_id.'
			OR `mp_product_id` = '.(int) $deleted_prod_id
        );
    }

    public function isPsCombinationExists($ps_prod_attr_id)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product_attribute` = '.(int) $ps_prod_attr_id);
    }

    /**
     * Checks if the current entry is duplicate or not.
     *
     * @param array $params new_mp_product_id, mp_product_id, mp_product_id_attribute
     * @return bool/array
     */
    public function checkIfDuplicateEntry($params)
    {
        if ($params['for'] == 'mp') {
            return Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'wk_mp_pack_product` WHERE `new_mp_product_id` = '.(int) $params['pack_product_id'].' AND `mp_product_id` = '.(int) $params['item_product_id'].' AND `mp_product_id_attribute` = '.(int) $params['item_product_id_attribute']);
        } elseif ($params['for'] == 'ps') {
            return Db::getInstance()->getValue('SELECT `id_product_pack` FROM `'._DB_PREFIX_.'pack` WHERE `id_product_pack` = '.(int) $params['pack_product_id'].' AND `id_product_item` = '.(int) $params['item_product_id'].' AND `id_product_attribute_item` = '.(int) $params['item_product_id_attribute']);
        } else {
            return true;
        }
    }

    public function saveMpPackProductData($params)
    {
        $obj_mppack = new self();
        $obj_mppack->new_mp_product_id = $params['pack_product_id'];
        $obj_mppack->mp_product_id = $params['item_product_id'];
        $obj_mppack->mp_product_id_attribute = $params['item_product_id_attribute'];
        $obj_mppack->quantity = $params['quantity'];
        $obj_mppack->save();
    }
}
