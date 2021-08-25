CREATE TABLE IF NOT EXISTS `PREFIX_marketplace_store_locator` (

`id` int(10) unsigned NOT NULL auto_increment,
`name` varchar(1000) NOT NULL,
`id_seller` int(11) NOT NULL,
`country_id` int(10) unsigned NOT NULL,
`state_id` int(10) unsigned default NULL,
`city_name` varchar(64) NOT NULL,
`address1` varchar(128) NOT NULL,
`address2` varchar(128) NOT NULL,
`map_address` text,
`map_address_text` text,
`latitude` decimal(13,8) DEFAULT NULL,
`longitude` decimal(13,8) DEFAULT NULL,
`zip_code` varchar(12) default NULL,
`phone` varchar(16) default NULL,
`fax` varchar(16) default NULL,
`email` varchar(128) default NULL,

`payment_option` text default NULL,
`pickup_start_time` text default NULL,
`pickup_end_time` text default NULL,

`store_open_days` text default NULL,
`opening_time` text default NULL,
`closing_time` text default NULL,

`store_pickup_available` tinyint(1) NOT NULL,
`active` tinyint(1) NOT NULL,
`date_add` datetime NOT NULL,
`date_upd` datetime NOT NULL,

PRIMARY KEY  (`id`)

) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_marketplace_store_products` (

`id` int(10) unsigned NOT NULL auto_increment,
`id_product` int(10) NOT NULL,
`id_store` int(10) NOT NULL,
PRIMARY KEY  (`id`)

) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_store_pickup_products` (
`id_store_pickup_product` int(10) unsigned NOT NULL auto_increment,
`id_store_pickup` int(10) unsigned NOT NULL,
`id_store` int(10) NOT NULL,
`id_product` int(10) NOT NULL,
`id_product_attribute` int(10) NOT NULL,
`pickup_date` datetime NOT NULL,
PRIMARY KEY  (`id_store_pickup_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_store_pickup` (
`id_store_pickup` int(10) unsigned NOT NULL auto_increment,
`id_cart` int(10) NOT NULL,
`id_order` int(10) NOT NULL,
`date_add` datetime NOT NULL,
`date_upd` datetime NOT NULL,
PRIMARY KEY  (`id_store_pickup`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mpstore_pay` (
`id_mp_store_pay` int(10) unsigned NOT NULL auto_increment,
`active` tinyint(1) NOT NULL,
`date_add` datetime NOT NULL,
`date_upd` datetime NOT NULL,
PRIMARY KEY  (`id_mp_store_pay`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mpstore_pay_lang` (
  `id_mp_store_pay` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,  
  `payment_name` varchar(255) character set utf8 NOT NULL,  
  PRIMARY KEY (`id_mp_store_pay`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_store_pickup_available` (
  `id_mp_store_pickup_available` int(10) unsigned NOT NULL auto_increment,
  `id_product` int(10) unsigned NOT NULL,
  `availabe_store_pickup` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_mp_store_pickup_available`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `PREFIX_mp_store_configuration` (
  `id_store_configuration` int(10) unsigned NOT NULL auto_increment,
  `id_seller` int(10) unsigned NOT NULL,
  `minimum_days` int(10) unsigned,
  `maximum_days` int(10) unsigned,
  `minimum_hours` int(10) unsigned,
  `max_pick_ups` int(10) unsigned,
  `store_payment` tinyint(1) NOT NULL,
  `countries` text default NULL,
  `enable_marker` tinyint(1) NOT NULL,
  `enable_country` tinyint(1) NOT NULL,
  `enable_date` tinyint(1) NOT NULL,
  `enable_time` tinyint(1) NOT NULL,
  `enable_store_notification` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_store_configuration`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;