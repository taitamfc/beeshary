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

class LoginConfigration extends ObjectModel
{
    public $id;
    public $id_shop;
    public $id_theme;
    public $header_bg_color;
    public $body_bg_color;

    public $meta_title;
    public $meta_description;

    public static $definition = array(
        'table' => 'marketplace_login_configration',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'id_theme' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'header_bg_color' => array('type' => self::TYPE_STRING, 'required' => true),
            'body_bg_color' => array('type' => self::TYPE_STRING, 'required' => true),
            /* Lang fields */
            'meta_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'),
        ),
    );

    public function getShopThemeConfigration($psShopId, $idTheme, $idLang = false)
    {
        if ($idLang) {
            return Db::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'marketplace_login_configration` mplc
                LEFT JOIN `'._DB_PREFIX_.'marketplace_login_configration_lang` mplcl
                on (mplcl.`id` = mplc.`id`)
                WHERE mplc.`id_shop` = '.(int) $psShopId.'
                AND mplc.`id_theme`='.(int) $idTheme.'
                AND mplcl.`id_lang`='.(int) $idLang
            );
        } else {
            return Db::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'marketplace_login_configration`
                WHERE `id_shop` = '.(int) $psShopId.'
                AND `id_theme`='.(int) $idTheme
            );
        }
    }

    public function getShopThemeConfigrationLangInfo($id)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_login_configration_lang`
            WHERE `id` = '.(int) $id
        );
    }

    public function getAllConfigration()
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_login_configration`'
        );
    }
}
