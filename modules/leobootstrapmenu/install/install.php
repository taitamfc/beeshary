<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo Bootstrap Menu
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

$path = dirname(_PS_ADMIN_DIR_);

include_once($path.'/config/config.inc.php');
include_once($path.'/init.php');


$res = (bool)Db::getInstance()->execute('
    CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'btmegamenu` (
      `id_btmegamenu` int(11) NOT NULL AUTO_INCREMENT,
      `id_group` int(11) NOT NULL,
      `image` varchar(255) NOT NULL,
      `id_parent` int(11) NOT NULL,
      `sub_with` varchar(255) NOT NULL,
      `is_group` tinyint(1) NOT NULL,
      `width` varchar(255) DEFAULT NULL,
      `submenu_width` varchar(255) DEFAULT NULL,
      `submenu_colum_width` varchar(255) DEFAULT NULL,
      `item` varchar(255) DEFAULT NULL,
      `item_parameter` varchar(255) DEFAULT NULL,
      `colums` varchar(255) DEFAULT NULL,
      `type` varchar(255) NOT NULL,
      `is_content` tinyint(1) NOT NULL,
      `show_title` tinyint(1) NOT NULL,
      `level_depth` smallint(6) NOT NULL,
      `active` tinyint(1) NOT NULL,
      `position` int(11) NOT NULL,
      `submenu_content` text NOT NULL,
      `show_sub` tinyint(1) NOT NULL,
      `target` varchar(25) DEFAULT NULL,
      `privacy` smallint(6) DEFAULT NULL,
      `position_type` varchar(25) DEFAULT NULL,
      `menu_class` varchar(255) DEFAULT NULL,
      `content` text,
      `icon_class` varchar(255) DEFAULT NULL,
      `level` int(11) NOT NULL,
      `left` int(11) NOT NULL,
      `right` int(11) NOT NULL,
      `submenu_catids` text,
      `is_cattree` tinyint(1) DEFAULT \'1\',
      `date_add` datetime DEFAULT NULL,
      `date_upd` datetime DEFAULT NULL,
      `groupBox` varchar(255) DEFAULT "all",
      `params_widget` LONGTEXT,
      PRIMARY KEY (`id_btmegamenu`)
    ) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8;
');
$res &= (bool)Db::getInstance()->execute('
    CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'btmegamenu_lang` (
      `id_btmegamenu` int(11) NOT NULL,
      `id_lang` int(11) NOT NULL,
      `title` varchar(255) DEFAULT NULL,
      `text` varchar(255) DEFAULT NULL,
        `url` varchar(255) DEFAULT NULL,
      `description` text,
      `content_text` text,
      `submenu_content_text` text,
      PRIMARY KEY (`id_btmegamenu`,`id_lang`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
');

$res &= (bool)Db::getInstance()->execute('
    CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'btmegamenu_shop` (
      `id_btmegamenu` int(11) NOT NULL DEFAULT \'0\',
      `id_shop` int(11) NOT NULL DEFAULT \'0\',
      PRIMARY KEY (`id_btmegamenu`,`id_shop`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
');

$res &= (bool)Db::getInstance()->execute('
    CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'btmegamenu_widgets`(
      `id_btmegamenu_widgets` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(250) NOT NULL,
      `type` varchar(250) NOT NULL,
      `params` LONGTEXT,
      `id_shop` int(11) unsigned NOT NULL,
      `key_widget` int(11)  NOT NULL,
      PRIMARY KEY (`id_btmegamenu_widgets`,`id_shop`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
');

$res &= (bool)Db::getInstance()->execute('
    CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'btmegamenu_group`(
        `id_btmegamenu_group` int(11) NOT NULL AUTO_INCREMENT,
        `id_shop` int(10) unsigned NOT NULL,
        `hook` varchar(64) DEFAULT NULL,
        `position` int(11) NOT NULL,
        `active` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
        `params` text NOT NULL,
        
        `active_ap` tinyint(1) DEFAULT NULL,
        `randkey` varchar(255) DEFAULT NULL,
        `form_id` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id_btmegamenu_group`,`id_shop`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
');

$res &= (bool)Db::getInstance()->execute('
    CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'btmegamenu_group_lang` (
      `id_btmegamenu_group` int(11) NOT NULL,
      `id_lang` int(11) NOT NULL,
      `title` varchar(255) DEFAULT NULL,
      `title_fo` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id_btmegamenu_group`,`id_lang`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
');


/* install sample data */
$rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_btmegamenu FROM `'._DB_PREFIX_.'btmegamenu`');
if (count($rows) <= 0 && file_exists(_PS_MODULE_DIR_.'leobootstrapmenu/install/sample.php')) {
    include_once(_PS_MODULE_DIR_.'leobootstrapmenu/install/sample.php');
}
