<?php
/**
* 2010-2017 Webkul
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MarketplaceExtrafieldValue extends ObjectModel
{
    public $id;
    public $extrafield_id;
    public $marketplace_product_id;
    public $mp_id_shop;
    public $mp_id_seller;
    public $is_for_shop;
    public $field_value;

    public $field_val;

    public static $definition = array(
        'table' => 'marketplace_extrafield_value',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'extrafield_id' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'marketplace_product_id' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'mp_id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'mp_id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'field_value' => array('type' => self::TYPE_STRING),
            'is_for_shop' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),

            /* Lang fields */
            'field_val' => array('type' => self::TYPE_STRING, 'lang' => true),
        ),
    );

    //@marketplace_product_id = 0 for shop edit and add
    public function insertExtraFieldValue($extrafield_id, $field_value, $mp_id_shop, $mp_id_seller, $marketplace_product_id = 0, $is_for_shop = 0)
    {
        $is_insert = Db::getInstance()->insert(
            'marketplace_extrafield_value',
            array(
            'extrafield_id' => (int) $extrafield_id,
            'marketplace_product_id' => (int) $marketplace_product_id,
            'field_value' => pSQL($field_value),
            'mp_id_seller' => (int) $mp_id_seller,
            'mp_id_shop' => (int) $mp_id_shop,
            'is_for_shop' => (int) $is_for_shop, )
        );
        if ($is_insert) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteExtraFieldValue($extrafield_id)
    {
        $extrafieldvalue = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'marketplace_extrafield_value WHERE extrafield_id='.(int) $extrafield_id);
        if ($extrafieldvalue) {
            foreach ($extrafieldvalue as $value) {
                Db::getInstance()->delete('marketplace_extrafield_value_lang', '`id` = '.(int)$value['id']);
            }
            $delete = Db::getInstance()->delete('marketplace_extrafield_value', 'extrafield_id='.$extrafield_id);
            if ($delete) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function deleteExtraFieldValueByFieldValue($field_value)
    {
        $delete = Db::getInstance()->delete('marketplace_extrafield_value', 'field_value='.$field_value);
        if ($delete) {
            return true;
        } else {
            return false;
        }
    }

    public function findExtrafieldValue($extrafield_id, $mp_id_shop, $mp_id_seller, $marketplace_product_id = 0, $is_for_shop = 0)
    {
        $extrafieldvalue = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM '._DB_PREFIX_.'marketplace_extrafield_value WHERE `marketplace_product_id` ='.(int) $marketplace_product_id.' AND extrafield_id='.(int) $extrafield_id.' AND mp_id_shop='.(int) $mp_id_shop.' AND mp_id_seller='.(int) $mp_id_seller.' AND is_for_shop='.(int) $is_for_shop);

        if (empty($extrafieldvalue)) {
            return false;
        } else {
            $fieldvalue = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'marketplace_extrafield_value_lang WHERE `id` ='.(int) $extrafieldvalue['id']);
            if ($fieldvalue) {
                foreach ($fieldvalue as $value) {
                    $extrafieldvalue['field_val'][$value['id_lang']] = $value['field_val'];
                }
                return $extrafieldvalue;
            } return false;
        }
    }

    public function findExtrafieldValues($mp_id_shop, $marketplace_product_id = 0)
    {
        $extrafield_values = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'marketplace_extrafield_value WHERE mp_id_shop='.$mp_id_shop.' AND marketplace_product_id='.$marketplace_product_id);
        if (empty($extrafield_values)) {
            return false;
        } else {
            return $extrafield_values;
        }
    }

    public function updateExtrafieldValue($extrafield_id, $field_value, $mp_id_shop, $mp_id_seller, $marketplace_product_id = 0, $is_for_shop = 0)
    {
        $is_update = Db::getInstance()->execute('update '._DB_PREFIX_."marketplace_extrafield_value set field_value='".$field_value."' where extrafield_id=".(int) $extrafield_id.' AND mp_id_shop='.(int) $mp_id_shop.' AND marketplace_product_id='.(int) $marketplace_product_id.' and is_for_shop='.$is_for_shop, ' and mp_id_seller='.$mp_id_seller);
        if ($is_update) {
            return true;
        } else {
            return false;
        }
    }

    public function updateExtrafieldValueByid($extrafield_value_id, $field_value)
    {
        $is_update = Db::getInstance()->update('marketplace_extrafield_value', array('field_value' => $field_value), 'id='.(int) $extrafield_value_id);
        if ($is_update) {
            return true;
        } else {
            return false;
        }
    }

    public function getExtrafieldValueId($extraFieldId, $page, $idSeller)
    {
        if ($page == 2 && !$idSeller) {
            return false;
        } elseif ($page == 1 && !Tools::getIsset('id_mp_product')) {
            return false;
        }

        $sql = 'SELECT `field_value` FROM '._DB_PREFIX_.'marketplace_extrafield_value WHERE `extrafield_id` = '.$extraFieldId;
        if ($idSeller) {
            $sql .= ' AND `is_for_shop` = 1 AND `mp_id_shop` = '.(int) $idSeller;
        } elseif (Tools::getIsset('id_mp_product') && Tools::getValue('id_mp_product')) {
            $sql .= ' AND `is_for_shop` = 0 AND `marketplace_product_id` = '.(int) Tools::getValue('id_mp_product');
        } else {

        }

        $id = Db::getInstance()->getValue($sql);

        if ($id) {
            return $id;
        }
        return false;
    }
}
