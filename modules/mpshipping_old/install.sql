/**
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

CREATE TABLE IF NOT EXISTS `PREFIX_mp_shipping_method` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`mp_shipping_name` varchar(255) character set utf8 NOT NULL,
	`shipping_method` int(11) unsigned NOT NULL DEFAULT '1',
	`mp_id_seller` int(11) unsigned NOT NULL,
	`id_ps_reference` int(11) unsigned NOT NULL,
	`id_tax_rule_group` int(11) unsigned NOT NULL,
	`range_behavior` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`tracking_url` varchar(255) DEFAULT NULL,
	`max_width` int(10) DEFAULT '0',
	`max_height` int(10) DEFAULT '0',
	`max_depth` int(10) DEFAULT '0',
	`max_weight` decimal(20,6) DEFAULT '0.000000',
	`grade` int(10) DEFAULT '0',
	`is_free` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`active` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`is_done` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`shipping_handling` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`is_default_shipping` tinyint(1) unsigned NOT NULL,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_shipping_method_lang` (
  `id` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,  
  `transit_delay` varchar(255) character set utf8 NOT NULL,
  PRIMARY KEY (`id`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_shipping_delivery` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`mp_shipping_id` int(11) unsigned NOT NULL,
	`id_zone` int(11) unsigned NOT NULL,
	`mp_id_range_price` int(11) unsigned NOT NULL,
	`mp_id_range_weight` int(11) unsigned NOT NULL,
	`base_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_range_price` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`mp_shipping_id` int(11) unsigned NOT NULL,
	`delimiter1` decimal(20,6) NOT NULL DEFAULT '0.000000',
	`delimiter2` decimal(20,6) NOT NULL DEFAULT '0.000000',
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_range_weight` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`mp_shipping_id` int(11) unsigned NOT NULL,
	`delimiter1` decimal(20,6) NOT NULL DEFAULT '0.000000',
	`delimiter2` decimal(20,6) NOT NULL DEFAULT '0.000000',
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_shipping_impact` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`mp_shipping_id` int(11) unsigned NOT NULL,
	`shipping_delivery_id` int(11) unsigned NOT NULL,
	`id_zone` int(11) unsigned NOT NULL,
	`id_country` int(11) unsigned NOT NULL,
	`id_state` int(11) unsigned NOT NULL DEFAULT '0',
	`impact_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_shipping_product_map` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`mp_shipping_id` int(11) unsigned NOT NULL,
	`id_ps_reference` int(11) unsigned NOT NULL,
	`mp_product_id` int(11) unsigned NOT NULL,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_shipping_cart` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`id_ps_cart` int(11) unsigned NOT NULL,
	`id_ps_carrier` int(11) unsigned NOT NULL,
	`extra_cost` decimal(20,6) DEFAULT '0.000000',
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;