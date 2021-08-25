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

class LoginNoOfBlock extends ObjectModel
{
    public $id;
    public $id_theme;
    public $block_name;

    public static $definition = array(
        'table' => 'marketplace_noofblock',
        'primary' => 'id',
        'fields' => array(
            'id_theme' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'block_name' => array('type' => self::TYPE_STRING, 'validate' => 'isName'),
        ),
    );

    public static function getNoOfBlock($idTheme)
    {
        return Db::getInstance()->getValue(
            'SELECT COUNT(*) AS no_of_rows FROM `'._DB_PREFIX_.'marketplace_noofblock` 
			WHERE `id_theme`='.(int) $idTheme
        );
    }
}
