<?php
/**
* 2010-2017 Webkul.
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

class LoginTheme extends ObjectModel
{
    public $id;
    public $name;
    public $active;

    public static $definition = array(
        'table' => 'marketplace_login_theme',
        'primary' => 'id',
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    public function getAllThemes()
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_login_theme`'
        );
    }

    public function getActiveTheme()
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_login_theme`
            WHERE `active` = 1'
        );
    }

    public function deActivateAllThemes()
    {
        return Db::getInstance()->executeS(
            'UPDATE `'._DB_PREFIX_.'marketplace_login_theme`
            SET `active`= 0'
        );
    }
}
