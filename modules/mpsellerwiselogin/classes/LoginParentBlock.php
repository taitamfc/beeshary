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

class LoginParentBlock extends ObjectModel
{
    public $id;
    public $id_position;
    public $id_theme;
    public $name;
    public $active;

    public static $definition = array(
        'table' => 'marketplace_login_parent_block',
        'primary' => 'id',
        'fields' => array(
            'id_position' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_theme' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'name' => array('type' => self::TYPE_STRING),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    public static function getNoOfSubBlocks($blockName, $idTheme)
    {
        return Db::getInstance()->getValue(
            'SELECT COUNT(mlbp.id) AS noofblock
			FROM '._DB_PREFIX_.'marketplace_login_parent_block AS mlpb
			INNER JOIN '._DB_PREFIX_.'marketplace_login_block_position AS mlbp 
			ON (mlbp.id_parent = mlpb.id AND mlbp.id_theme='.(int) $idTheme.')
			WHERE mlpb.`name` = \''.pSQL($blockName).'\''
        );
    }

    public static function getParentBlockPosition($blockName, $idTheme)
    {
        return Db::getInstance()->getValue(
            'SELECT `id_position` FROM `'._DB_PREFIX_.'marketplace_login_parent_block` 
			WHERE `name` = \''.pSQL($blockName).'\'
            AND `id_theme`='.(int) $idTheme
        );
    }

    public static function isParentBlockActive($blockName, $idTheme)
    {
        return Db::getInstance()->getValue(
            'SELECT `active` FROM `'._DB_PREFIX_.'marketplace_login_parent_block` 
			WHERE `name` = \''.pSQL($blockName).'\'
            AND `id_theme`='.(int) $idTheme
        );
    }

    public function getParentBlockDetails($blockName, $idTheme)
    {
        return Db::getInstance()->getRow(
            'SELECT * from `'._DB_PREFIX_.'marketplace_login_parent_block` 
			WHERE `name` = \''.pSQL($blockName).'\'
            AND `id_theme`='.(int) $idTheme
        );
    }

    public function getActiveParentBlock($idTheme)
    {
        $temp = 'header';
        return Db::getInstance()->executeS(
            'SELECT * from `'._DB_PREFIX_.'marketplace_login_parent_block` 
			WHERE `active` = 1 
			AND `name` != \''.pSQL($temp).'\'
            AND `id_theme`='.(int) $idTheme.'
            ORDER BY id_position'
        );
    }

    public function getBlockIdByThemeId($blockName, $idTheme)
    {
        return Db::getInstance()->getRow(
            'SELECT * from `'._DB_PREFIX_.'marketplace_login_parent_block` 
			WHERE `name` = \''.pSQL($blockName).'\'
            AND `id_theme`='.(int) $idTheme
        );
    }
}
