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

class LoginBlockPosition extends ObjectModel
{
    public $id;
    public $id_shop;
    public $id_parent;
    public $id_position;
    public $id_theme;
    public $block_name;
    public $width;
    public $block_bg_color;
    public $block_text_color;
    public $active;

    public static $definition = array(
        'table' => 'marketplace_login_block_position',
        'primary' => 'id',
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_parent' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_position' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_theme' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'block_name' => array('type' => self::TYPE_STRING),
            'width' => array('type' => self::TYPE_FLOAT),
            'block_bg_color' => array('type' => self::TYPE_STRING),
            'block_text_color' => array('type' => self::TYPE_STRING),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    public function getBlockPositionDetail($psShopId, $blockName)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_login_block_position`
			WHERE `id_shop` = '.(int) $psShopId.'
            AND `block_name` = \''.pSQL($blockName).'\' AND `active`=1'
        );
    }

    public function getBlockPositionDetailByBlockName($psShopId, $blockName, $idTheme)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_login_block_position`
			WHERE `id_shop` = '.(int) $psShopId.'
			AND `block_name` = \''.pSQL($blockName).'\'
            AND `id_theme` = '.(int) $idTheme
        );
    }

    public function getPositionDetailByIdParent($idParent, $idTheme)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_login_block_position`
			WHERE `id_parent` = '.(int) $idParent.' 
			AND `active`=1
            AND `id_theme`='.(int) $idTheme.'
            ORDER BY id_position'
        );
    }

    public function getAllBlockPositionDetail()
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_login_block_position`
            ORDER BY id_position'
        );
    }
}
