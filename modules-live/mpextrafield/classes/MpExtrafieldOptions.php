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

class MpExtrafieldOptions extends ObjectModel
{
    public $id;
    public $extrafield_id;

    public $left_value;
    public $right_value;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'mp_extrafield_custom_field_options',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'extrafield_id' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'left_value' => array('type' => self::TYPE_STRING),
            'right_value' => array('type' => self::TYPE_STRING),

            /* Lang fields */
            'left_value' => array('type' => self::TYPE_STRING, 'lang' => true),
            'right_value' => array('type' => self::TYPE_STRING, 'lang' => true),
        ),
    );
    /**
     * [getCustomFieldOptions -> seleting values of values of radiobutton and checkbox].
     *
     * @param [type] $extrafield_id [field_id]
     *
     * @return [type] [array containing checkbox and radiobutton values]
     */
    public function getCustomFieldOptions($extrafield_id)
    {
        $custom_fields = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_extrafield_custom_field_options` WHERE extrafield_id = '.$extrafield_id);
        if (empty($custom_fields)) {
            return false;
        } else {
            foreach ($custom_fields as $key => $field) {
                $options = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_extrafield_custom_field_options_lang` WHERE id = '.$field['id']);
                if ($options) {
                    foreach ($options as $option) {
                        $custom_fields[$key]['left_value'][$option['id_lang']] = $option['left_value'];
                        $custom_fields[$key]['right_value'][$option['id_lang']] = $option['right_value'];
                    }
                }
            }
            return $custom_fields;
        }
    }

    public function getCustomFieldRadioOptions($extrafield_id, $id_lang)
    {
        $custom_fields = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_extrafield_custom_field_options` mpecfo
            LEFT JOIN '._DB_PREFIX_.'mp_extrafield_custom_field_options_lang as mpecfol ON (mpecfol.`id`=mpecfo.`id` AND mpecfol.id_lang = '.(int) $id_lang.')
            WHERE extrafield_id = '.$extrafield_id);
        if (empty($custom_fields)) {
            return false;
        } else {
            return $custom_fields;
        }
    }

    public function deleteExtraFieldRadioOptionsById($id)
    {
        $extrafieldoption = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'mp_extrafield_custom_field_options WHERE extrafield_id='.(int) $id);
        if ($extrafieldoption) {
            foreach ($extrafieldoption as $option) {
                $delete = Db::getInstance()->delete('mp_extrafield_custom_field_options_lang', 'id = '.(int) $option['id']);
            }
            $delete = Db::getInstance()->delete('mp_extrafield_custom_field_options', 'extrafield_id = '.(int) $id);
            if ($delete) {
                return true;
            }
        }
        return false;
    }

    public function getCustomFieldOptionsJoin($extrafield_id)
    {
        $custom_fields = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'wk_registration_custom_field` AS wrcf
			JOIN `'._DB_PREFIX_.'mp_extrafield_custom_field_options` AS wrcfo ON wrcf.id = wrcfo.extrafield_id 
			WHERE extrafield_id = '.$extrafield_id);
        if (empty($custom_fields)) {
            return false;
        } else {
            return $custom_fields;
        }
    }

    /**
     * [updateCustomFieldOptions -> updating radiobutton and checkbox value].
     *
     * @param [type] $extrafield_id [field_id]
     * @param [type] $left_value    [first value]
     * @param [type] $right_value   [second value]
     *
     * @return [type] [true , false]
     */
    public function updateCustomFieldOptions($extrafield_id, $left_value, $right_value)
    {
        $custom_update_fields = Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'mp_extrafield_custom_field_options`
			SET 
			`left_value` = "'.pSQL($left_value).'",
			`right_value` = "'.pSQL($right_value).'" WHERE extrafield_id = '.$extrafield_id);
        if (empty($custom_update_fields)) {
            return false;
        } else {
            return $custom_update_fields;
        }
    }
}
