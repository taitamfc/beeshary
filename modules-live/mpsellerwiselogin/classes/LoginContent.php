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

class LoginContent extends ObjectModel
{
    public $id;
    public $id_shop;
    public $id_block;
    public $id_theme;
    public $content;

    public static $definition = array(
        'table' => 'marketplace_login_content',
        'multilang' => true,
        'primary' => 'id',
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_block' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_theme' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            /* Lang fields */
            'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
        ),
    );

    public function getBlockLangContentById($id)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_login_content_lang`
			WHERE `id` = '.(int) $id
        );
    }

    public function getBlockContent($idBlock, $idTheme, $idLang = false)
    {
        if ($idLang) {
            return Db::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'marketplace_login_content` mpln
                LEFT JOIN `'._DB_PREFIX_.'marketplace_login_content_lang` mplnl
                ON (mpln.`id` = mplnl.`id`)
                WHERE mpln.`id_block` = '.(int) $idBlock.'
                AND mpln.`id_theme` = '.(int) $idTheme.'
                AND mplnl.`id_lang` = '.(int) $idLang
            );
        } else {
            return Db::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'marketplace_login_content`
                WHERE `id_block` = '.(int) $idBlock.'
                AND `id_theme` = '.(int) $idTheme
            );
        }
    }

    public function getBlockContentByName($block_name, $idTheme)
    {
        return Db::getInstance()->getRow(
            'SELECT mlc.content AS content
			FROM `'._DB_PREFIX_.'marketplace_login_block_position` AS mlbp
			INNER JOIN `'._DB_PREFIX_.'marketplace_login_content` AS mlc
            ON (mlbp.id = mlc.id_block AND mlc.id_theme = '.(int) $idTheme.')
			WHERE mlbp.block_name = \''.$block_name.'\''
        );
    }

    public function getAllLoginContent()
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'marketplace_login_content`'
        );
    }
}
