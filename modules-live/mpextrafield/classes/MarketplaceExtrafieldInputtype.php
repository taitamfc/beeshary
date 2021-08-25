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

class MarketplaceExtrafieldInputtype extends ObjectModel
{
    public $id;
    public $inputtype_name;

    public static $definition = array(
            'table' => 'marketplace_extrafield_inputtype',
            'primary' => 'id',
            'fields' => array(
                'inputtype_name	' => array('type' => self::TYPE_STRING),
            ),
        );

    public function insertMpExtraFieldAssoc($inputtype_name)
    {
        $is_insert = Db::getInstance()->insert(
            'marketplace_extrafield_inputtype',
            array('inputtype_name' => pSQL($inputtype_name),
            )
        );

        if ($is_insert) {
            return true;
        } else {
            return false;
        }
    }

    public function insertMpExtraFieldValidation($inputtype_name)
    {
        $is_insert = Db::getInstance()->insert(
            'mp_extrafield_custom_field_validation',
            array('validation_type' => pSQL($inputtype_name),
            )
        );
        if ($is_insert) {
            return true;
        } else {
            return false;
        }
    }

    public function findExtraFieldInputtype()
    {
        $extrafieldinputtype = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_extrafield_inputtype`');
        if (empty($extrafieldinputtype)) {
            return false;
        } else {
            return $extrafieldinputtype;
        }
    }

    public function findExtraFieldInputtypeValidation()
    {
        $extrafieldinputtypevalidation = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'mp_extrafield_custom_field_validation`');
        if (empty($extrafieldinputtypevalidation)) {
            return false;
        } else {
            return $extrafieldinputtypevalidation;
        }
    }

    public function findInputTypeName()
    {
        $inputtypename = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('select `inputtype_name` from '._DB_PREFIX_.'marketplace_extrafield_inputtype where id='.$this->id);

        if (empty($inputtypename)) {
            return false;
        } else {
            return $inputtypename;
        }
    }

    public function findInputTypeIdByname($inputtype_name)
    {
        $inputtypeid = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('select `id` from '._DB_PREFIX_."marketplace_extrafield_inputtype where inputtype_name='".$inputtype_name."'");

        if (empty($inputtypeid)) {
            return false;
        } else {
            return $inputtypeid;
        }
    }
}
