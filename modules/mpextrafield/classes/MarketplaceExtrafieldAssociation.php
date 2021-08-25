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

class MarketplaceExtrafieldAssociation extends ObjectModel
{
    public $id;
    public $extrafield_id;
    public $attribute_name;
    public static $definition = array(
        'table' => 'marketplace_extrafield_association',
        'primary' => 'id',
        'fields' => array(
            'extrafield_id' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'attribute_name' => array('type' => self::TYPE_STRING),
        ),
    );

    public static function insertMpExtraFieldAssoc($extrafield_id, $attribute_name)
    {
        $is_insert = Db::getInstance()->insert('marketplace_extrafield_association', array(
            'extrafield_id' => (int) $extrafield_id,
            'attribute_name' => pSQL($attribute_name),
            ));
        if ($is_insert) {
            return true;
        } else {
            return false;
        }
    }

    public static function deleteExtraFieldAssoc($extrafield_id)
    {
        $delete = Db::getInstance()->delete('marketplace_extrafield_association', 'extrafield_id='.$extrafield_id);
        if ($delete) {
            return true;
        } else {
            return false;
        }
    }

    public static function updateMpExtraFieldAssoc($extrafield_id, $attribute_name)
    {
        $is_update = Db::getInstance()->update('marketplace_extrafield_association', array('attribute_name' => pSQL($attribute_name)), 'extrafield_id='.(int) $extrafield_id);
        if ($is_update) {
            return true;
        } else {
            return false;
        }
    }

    public function isExistRecord($id)
    {
        $is_exist = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'marketplace_extrafield_association where extrafield_id ='.(int) $id);
        if ($is_exist) {
            return $is_exist;
        } else {
            return false;
        }
    }
}
