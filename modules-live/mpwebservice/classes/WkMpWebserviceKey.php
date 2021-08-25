<?php
/**
* 2010-2021 Webkul.
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
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkMpWebserviceKey extends ObjectModel
{
    public $id_seller;
    public $key;
    public $description;
    public $mpresource;
    public $active;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'wk_mp_webservice_key',
        'primary' => 'id_wk_mp_webservice_key',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'key' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 32),
            'description' => array('type' => self::TYPE_STRING),
            'mpresource' => array('type' => self::TYPE_STRING),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
        ),
    );

    public static function getMpWebserviceKey($idSeller, $idMpWebservice = false)
    {
        if ($idMpWebservice) {
            return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'wk_mp_webservice_key`
            WHERE id_wk_mp_webservice_key = '.(int) $idMpWebservice.' AND id_seller = '.(int)$idSeller);
        } else {
            return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'wk_mp_webservice_key`
            WHERE id_seller = '.(int) $idSeller);
        }
    }

    public static function getKeyDetails($key)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'wk_mp_webservice_key`
            WHERE `key` =\''.pSQL($key).'\' AND active = 1');
    }
}
