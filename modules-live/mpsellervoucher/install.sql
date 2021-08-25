CREATE TABLE IF NOT EXISTS `PREFIX_mp_cart_rule` (
  `id_mp_cart_rule` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_ps_cart_rule` int(10) unsigned NOT NULL DEFAULT '0',
  `id_seller` int(10) unsigned NOT NULL DEFAULT '0',
  `for_customer` int(10) unsigned NOT NULL DEFAULT '0',
  `date_from` datetime NOT NULL,
  `date_to` datetime NOT NULL,
  `description` text,
  `quantity` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity_per_user` int(10) unsigned NOT NULL DEFAULT '0',
  `priority` int(10) unsigned NOT NULL DEFAULT '1',
  `code` varchar(254) NOT NULL,
  `country_restriction` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `group_restriction` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cart_rule_restriction` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `product_restriction` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `reduction_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `reduction_amount` decimal(17,2) NOT NULL DEFAULT '0.00',
  `reduction_tax` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `reduction_currency` int(10) unsigned NOT NULL DEFAULT '0',
  `mp_reduction_product` int(10) NOT NULL DEFAULT '0',
  `highlight` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `admin_approval` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_mp_cart_rule`)
  ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_cart_rule_lang` (
  `id_mp_cart_rule` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(254) NOT NULL,
  PRIMARY KEY (`id_mp_cart_rule`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_cart_rule_country` (
  `id_mp_cart_rule` int(10) unsigned NOT NULL,
  `id_country` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_mp_cart_rule`,`id_country`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_cart_rule_group` (
  `id_mp_cart_rule` int(10) unsigned NOT NULL,
  `id_group` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_mp_cart_rule`,`id_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_cart_rule_product_rule_group` (
  `id_mp_product_rule_group` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_mp_cart_rule` int(10) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT '1',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_mp_product_rule_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_cart_rule_product_rule` (
  `id_mp_product_rule` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_mp_product_rule_group` int(10) unsigned NOT NULL,
  `type` enum('products') NOT NULL,
  PRIMARY KEY (`id_mp_product_rule`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_cart_rule_product_rule_value` (
  `id_mp_product_rule` int(10) unsigned NOT NULL,
  `id_mp_item` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_mp_product_rule`,`id_mp_item`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;