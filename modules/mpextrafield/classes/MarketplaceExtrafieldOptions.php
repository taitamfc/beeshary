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

class MarketplaceExtrafieldOptions extends ObjectModel
{
    public $id;
    public $extrafield_id;

    public $display_value;

    public static $definition = array(
        'table' => 'marketplace_extrafield_options',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'extrafield_id' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'display_value' => array('type' => self::TYPE_STRING),

            /* Lang fields */
            'display_value' => array('type' => self::TYPE_STRING, 'lang' => true),
            ),
    );

    public function insertExtraFieldOptions($extrafield_id, $display_value)
    {
        $is_insert = Db::getInstance()->insert('marketplace_extrafield_options', array(
            'extrafield_id' => (int) $extrafield_id,
            'display_value' => pSQL($display_value),
            ));
        if ($is_insert) {
            return true;
        } else {
            return false;
        }
    }

    public function findExtraFieldOptions($extrafield_id, $id_lang)
    {
        $extrafieldoptions = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'marketplace_extrafield_options mpeo
            LEFT JOIN '._DB_PREFIX_.'marketplace_extrafield_options_lang as mpeol ON (mpeol.`id`=mpeo.`id` AND mpeol.id_lang = '.(int) $id_lang.')
            where `extrafield_id` ='.(int) $extrafield_id);

        if (empty($extrafieldoptions)) {
            return false;
        } else {
            return $extrafieldoptions;
        }
    }

    public function deleteExtraFieldOptions($extrafield_id)
    {
        $delete = Db::getInstance()->delete('marketplace_extrafield_options', 'extrafield_id='.$extrafield_id);
        if ($delete) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteExtraFieldOptionsById($id)
    {
        $extrafieldoption = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'marketplace_extrafield_options WHERE extrafield_id='.$id);
        if ($extrafieldoption) {
            foreach ($extrafieldoption as $option) {
                $delete = Db::getInstance()->delete('marketplace_extrafield_options_lang', 'id = '.(int) $option['id']);
            }
            $delete = Db::getInstance()->delete('marketplace_extrafield_options', 'extrafield_id = '.(int) $id);
            if ($delete) {
                return true;
            }
        }
        return false;
    }

    public function findExtrafieldOptionById($id)
    {
        $extrafieldoption = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'marketplace_extrafield_options WHERE id='.$id);
        if (empty($extrafieldoption)) {
            return false;
        } else {
            return $extrafieldoption;
        }
    }

    public function updateExtraFieldOptions($display_val_exit, $key)
    {
        $is_update = Db::getInstance()->update(
            'marketplace_extrafield_options',
            array(
            'display_value' => pSQL($display_val_exit),
            'id='.(int) $key,
            )
        );
        if ($is_update) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * [insertCustomDropDownField -> inserting dropdown values into table].
     *
     * @param [type] $extrafield_id  [Field_id]
     * @param [type] $dropdown_value [dropdown_values which will appear in dropdown list]
     *
     * @return [type] [true,false]
     */
    public function insertCustomDropDownField($extrafield_id, $dropdown_value)
    {
        $custom_fields = Db::getInstance()->insert('marketplace_extrafield_options', array(
            'extrafield_id' => (int) $extrafield_id,
            'display_value' => pSQL($dropdown_value),
            ));
        if (empty($custom_fields)) {
            return false;
        } else {
            return $custom_fields;
        }
    }

    /**
     * [getCustomDropdownOptions -> selecting dropdown values].
     *
     * @param [type] $extrafield_id [field_id]
     *
     * @return [type] [array containing all values of dropdown list]
     */
    public function getCustomDropdownOptions($extrafield_id)
    {
        $custom_fields = Db::getInstance()->executeS('SELECT `id` FROM `'._DB_PREFIX_.'marketplace_extrafield_options` WHERE extrafield_id = '.$extrafield_id);
        if (empty($custom_fields)) {
            return false;
        } else {
            foreach ($custom_fields as $key => $field) {
                $options = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_extrafield_options_lang` WHERE id = '.$field['id']);
                if ($options) {
                    foreach ($options as $option) {
                        $custom_fields[$key]['display_value'][$option['id_lang']] = $option['display_value'];
                    }
                }
            }
            return $custom_fields;
        }
    }

    public static function insertIntoOptionLang($id, $id_lang, $dropdown_value)
    {
        $custom_fields = Db::getInstance()->insert('marketplace_extrafield_options', array(
            'id' => $id,
            'id_lang' => (int) $id_lang,
            'display_value' => pSQL($dropdown_value),
        ));

        if ($custom_fields) {
            return $custom_fields;
        } else {
            return false;
        }
    }
}
