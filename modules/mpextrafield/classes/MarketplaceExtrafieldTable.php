<?php
/**
* 2010-2019 Webkul
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MarketplaceExtrafieldTable
{
    public static function create()
    {
        $queries = array(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'marketplace_extrafield_inputtype` (
            	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            	`inputtype_name` varchar(255) character set utf8 NOT NULL,
            	PRIMARY KEY (`id`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'mp_extrafield_custom_field_validation` (
            	`validation_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            	`validation_type` varchar(255) character set utf8 NOT NULL,
            	PRIMARY KEY (`validation_id`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'marketplace_extrafield` (
            	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            	`page` int(11) unsigned NOT NULL DEFAULT 0,
            	`inputtype` int(11) unsigned NOT NULL DEFAULT 0,
            	`asplaceholder` int(11) unsigned NOT NULL DEFAULT 0,
            	`validation_type` int(11) unsigned NOT NULL DEFAULT 0,
            	`char_limit` varchar(255) character set utf8 NOT NULL,
            	`multiple` tinyint(1) unsigned NOT NULL DEFAULT 0,
            	`file_type` varchar(255) character set utf8 NOT NULL DEFAULT 0,
            	`field_req` tinyint(1) unsigned NOT NULL DEFAULT 1,
            	`active` tinyint(1) unsigned NOT NULL DEFAULT 1,
            	`show_field` tinyint(1) unsigned NOT NULL DEFAULT 1,
            	PRIMARY KEY (`id`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'marketplace_extrafield_lang` (
            	`id` int(11) unsigned NOT NULL,
            	`id_lang` int(11) unsigned NOT NULL,
            	`label_name` varchar(255) character set utf8 NOT NULL,
            	`default_value` varchar(255) character set utf8 NOT NULL,
            	PRIMARY KEY (`id`, `id_lang`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'marketplace_extrafield_association` (
            	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            	`extrafield_id` int(11) unsigned NOT NULL,
            	`attribute_name` varchar(255) character set utf8 NOT NULL,
            	PRIMARY KEY (`id`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'marketplace_extrafield_options` (
            	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            	`extrafield_id` int(11) unsigned NOT NULL,
            	PRIMARY KEY (`id`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'marketplace_extrafield_options_lang` (
            	`id` int(11) unsigned NOT NULL,
            	`id_lang` int(11) unsigned NOT NULL,
            	`display_value` varchar(255) character set utf8 NOT NULL,
            	PRIMARY KEY (`id`, `id_lang`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'mp_extrafield_custom_field_options` (
            	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            	`extrafield_id` int(11) unsigned NOT NULL DEFAULT 0,
            	PRIMARY KEY (`id`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'mp_extrafield_custom_field_options_lang` (
            	`id` int(11) unsigned NOT NULL,
            	`id_lang` int(11) unsigned NOT NULL,
            	`left_value` varchar(255) character set utf8 NOT NULL,
            	`right_value` varchar(255) character set utf8 NOT NULL,
            	PRIMARY KEY (`id`, `id_lang`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'marketplace_extrafield_value` (
            	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            	`extrafield_id` int(11) unsigned NOT NULL,
            	`marketplace_product_id` int(11) unsigned NOT NULL,
            	`field_value` text character set utf8,
            	`mp_id_shop` int(11) unsigned NOT NULL DEFAULT 0,
            	`mp_id_seller` int(11) unsigned NOT NULL,
            	`is_for_shop` int(11) unsigned NOT NULL DEFAULT 0,
            	PRIMARY KEY (`id`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'marketplace_extrafield_value_lang` (
            	`id` int(11) unsigned NOT NULL,
            	`id_lang` int(11) unsigned NOT NULL,
            	`field_val` text character set utf8 NOT NULL,
            	PRIMARY KEY (`id`, `id_lang`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
        );

        foreach ($queries as $query) {
            if ($query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function drop()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'marketplace_extrafield`,
            `'._DB_PREFIX_.'marketplace_extrafield_lang`,
            `'._DB_PREFIX_.'marketplace_extrafield_association`,
            `'._DB_PREFIX_.'marketplace_extrafield_value`,
            `'._DB_PREFIX_.'marketplace_extrafield_value_lang`,
            `'._DB_PREFIX_.'marketplace_extrafield_inputtype`,
            `'._DB_PREFIX_.'mp_extrafield_custom_field_options`,
            `'._DB_PREFIX_.'mp_extrafield_custom_field_options_lang`,
            `'._DB_PREFIX_.'mp_extrafield_custom_field_validation`,
            `'._DB_PREFIX_.'marketplace_extrafield_options`,
            `'._DB_PREFIX_.'marketplace_extrafield_options_lang`
            ');
    }
}
