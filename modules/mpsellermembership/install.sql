CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_plan` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`id_product` int(11) NOT NULL ,
	`plan_price` decimal(20,6) unsigned NOT NULL,
	`plan_duration` int(11) unsigned NOT NULL,
	`num_products_allow` int(11) unsigned NOT NULL,
	`sequence_number` int(11) unsigned NOT NULL,
	`active` tinyint(1) unsigned NOT NULL DEFAULT '1',
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_plan_lang` (
  `id` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,  
  `plan_name` varchar(128) character set utf8 NOT NULL,
  PRIMARY KEY (`id`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_plan_detail` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`id_plan` int(11) unsigned NOT NULL,
	`id_order` int(11) unsigned NOT NULL,
	`mp_id_seller` int(11) unsigned NOT NULL,
	`num_products_allow` int(11) unsigned NOT NULL,
	`plan_duration` int(11) unsigned NOT NULL,
	`active_from` date,
	`expire_on` date,
	`is_this_current_plan` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`active` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`date_add` datetime,
	`date_upd` datetime,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_old_seller_plan` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`id_seller` int(11) NOT NULL ,
	`active_from` date NOT NULL,
	`expire_on` date NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;