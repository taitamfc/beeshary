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

class MarketplaceExtrafield extends ObjectModel
{
    public $id;
    public $page;
    public $inputtype;
    public $validation_type;
    public $char_limit;
    public $multiple;
    public $file_type;
    public $field_req;
    public $asplaceholder;
    public $active;

    public $default_value;
    public $label_name;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'marketplace_extrafield',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'page' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'inputtype' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'validation_type' => array('type' => self::TYPE_STRING),
            'char_limit' => array('type' => self::TYPE_STRING),
            'multiple' => array('type' => self::TYPE_STRING),
            'file_type' => array('type' => self::TYPE_STRING),
            'field_req' => array('type' => self::TYPE_STRING),
            'asplaceholder' => array('type' => self::TYPE_INT,'validate' => 'isInt'),
            'active' => array('type' => self::TYPE_BOOL,'validate' => 'isBool'),

            /* Lang fields */
            'label_name' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true),
            'default_value' => array('type' => self::TYPE_STRING, 'lang' => true),
        ),
    );

    public function delete()
    {
        MarketplaceExtrafieldAssociation::deleteExtraFieldAssoc($this->id);
        //delete associate value
        $obj_extrafield_value = new MarketplaceExtrafieldValue();
        $obj_extrafield_value->deleteExtraFieldValue($this->id);


        $obj_extrafield_option = new MarketplaceExtrafieldOptions();
        $obj_extrafield_option->deleteExtraFieldOptionsById($this->id);

        $obj_mp_extrafield_option = new MpExtrafieldOptions();
        $obj_mp_extrafield_option->deleteExtraFieldRadioOptionsById($this->id);

        Db::getInstance()->delete('marketplace_extrafield_lang', '`id` = '.(int)$this->id);
        if (!parent::delete()) {
            return false;
        }

        return true;
    }

    public function insertMarketplaceExtrafield($page, $inputtype, $default_value, $label_name, $asplaceholder = 0)
    {
        $is_insert = Db::getInstance()->insert('marketplace_extrafield', array(
                'page' => (int) $page,
                'inputtype' => (int) $inputtype,
                'default_value' => pSQL($default_value),
                'label_name' => pSQL($label_name),
                'asplaceholder' => (int) $asplaceholder,
        ));
        if ($is_insert) {
            $inserted_id = Db::getInstance()->Insert_ID();

            return $inserted_id;
        } else {
            return false;
        }
    }

    public function isAttributeNameRegister($page, $attribute_name)
    {
        $isregister = Db::getInstance()->executeS('SELECT mpef.`id` FROM '._DB_PREFIX_.'marketplace_extrafield AS mpef LEFT JOIN '._DB_PREFIX_."marketplace_extrafield_association AS mpefa ON (mpef.`id`=mpefa.`extrafield_id`) WHERE mpefa.`attribute_name`='".$attribute_name."' AND mpef.`page`=".$page);
        if (empty($isregister)) {
            return false;
        } else {
            return true;
        }
    }

    public function findExtraAttributeDetailById($id)
    {
        $extrafielddetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT mpef.* ,mpefa.`attribute_name` FROM '._DB_PREFIX_.'marketplace_extrafield as mpef 
			LEFT JOIN '._DB_PREFIX_.'marketplace_extrafield_association as mpefa ON (mpef.`id`= mpefa.`extrafield_id`) 
			WHERE mpef.`id`= '.(int) $id);
        if (empty($extrafielddetail)) {
            return false;
        } else {
            return $extrafielddetail;
        }
    }

    public function getLabelAndDefaultValueDetailById($id)
    {
        $extrafielddetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'marketplace_extrafield_lang WHERE `id`= '.(int) $id);
        if (empty($extrafielddetail)) {
            return false;
        } else {
            return $extrafielddetail;
        }
    }

    public static function getExtraFieldDetailById($id)
    {
        $extrafielddetail = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'marketplace_extrafield
            where `id`='.(int) $id);

        if (empty($extrafielddetail)) {
            return false;
        } else {
            return $extrafielddetail;
        }
    }

    public function findActiveExtraAttributeDetailByPage($page, $id_lang)
    {
        $extrafielddetail = Db::getInstance()->executeS('SELECT mpef.*, mpefl.`label_name` as label_name, mpefl.`default_value` as default_value ,mpefa.`attribute_name` FROM '._DB_PREFIX_.'marketplace_extrafield as mpef 
            LEFT JOIN '._DB_PREFIX_.'marketplace_extrafield_association as mpefa ON (mpef.`id`=mpefa.`extrafield_id`) 
			LEFT JOIN '._DB_PREFIX_.'marketplace_extrafield_lang as mpefl ON (mpefl.`id`=mpef.`id` AND mpefl.id_lang = '.(int) $id_lang.') 
			where mpef.`page`='.$page.' AND mpef.`active`= 1');

        if (empty($extrafielddetail)) {
            return false;
        } else {
            return $extrafielddetail;
        }
    }
}
