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

class MpExtrafieldCustomFieldValidation extends ObjectModel
{
    public $validation_id;
    public $validation_type;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'mp_extrafield_custom_field_validation',
        'primary' => 'validation_id',
        'fields' => array(
            'validation_type' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
        ),
    );
    /**
     * [getCustomFieldValidation -> selecting all validation type for display in validation type field].
     *
     * @return [type] [array containg validation type data]
     */
    public function getCustomFieldValidation()
    {
        $custom_fields_type = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'mp_extrafield_custom_field_validation`');
        if (empty($custom_fields_type)) {
            return false;
        } else {
            return $custom_fields_type;
        }
    }
}
