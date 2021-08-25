CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller` (
  `id_seller` int(10) unsigned NOT NULL auto_increment,
  `shop_name_unique` varchar(255) character set utf8 NOT NULL,
  `link_rewrite` varchar(255) character set utf8 NOT NULL,
  `seller_firstname` varchar(255) character set utf8 NOT NULL,
  `seller_lastname` varchar(255) character set utf8 NOT NULL,
  `business_email` varchar(128) NOT NULL, 
  `phone` varchar(32) DEFAULT NULL,
  `fax` varchar(32) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,  
  `id_country` int(10) unsigned NOT NULL DEFAULT '0',
  `id_state` int(10) unsigned NOT NULL DEFAULT '0',
  `default_lang` int(10) unsigned NOT NULL DEFAULT '0',
  `facebook_id` varchar(255) character set utf8 NOT NULL,
  `twitter_id` varchar(255) character set utf8 NOT NULL,
  `google_id` varchar(255) character set utf8 NOT NULL,
  `instagram_id` varchar(255) character set utf8 NOT NULL,
  `profile_image` varchar(15) NOT NULL,
  `profile_banner` varchar(15) NOT NULL,
  `shop_image` varchar(15) NOT NULL,
  `shop_banner` varchar(15) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shop_approved` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `seller_customer_id` int(10) unsigned NOT NULL,
  `seller_details_access` varchar(255) character set utf8 NOT NULL,
  `address` text,  
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id_seller`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_lang` (
  `id_seller` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,  
  `shop_name` varchar(255) character set utf8 NOT NULL,  
  `about_shop` text,
  PRIMARY KEY (`id_seller`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_product` (
  `id_mp_product` int(10) unsigned NOT NULL auto_increment,
  `id_seller` int(10) unsigned NOT NULL,
  `id_ps_product` int(10) unsigned default 0,
  `id_ps_shop` int(10) unsigned default 1,
  `id_category` int(10) unsigned default 1,
  `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `wholesale_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unity` varchar(255) character set utf8 NOT NULL,
  `unit_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `id_tax_rules_group` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity` int(10) NOT NULL DEFAULT '0',
  `minimal_quantity` int(10) NOT NULL DEFAULT '1',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `condition` ENUM('new', 'used', 'refurbished') NOT NULL DEFAULT 'new',
  `available_for_order` tinyint(1) NOT NULL DEFAULT '1',
  `show_price` tinyint(1) NOT NULL DEFAULT '1',
  `online_only` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `visibility` ENUM('both', 'catalog', 'search', 'none') NOT NULL DEFAULT 'both',
  `admin_assigned` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `width` decimal(20, 6) NOT NULL DEFAULT '0',
  `height` decimal(20, 6) NOT NULL DEFAULT '0',
  `depth` decimal(20, 6) NOT NULL DEFAULT '0',
  `weight` decimal(20, 6) NOT NULL DEFAULT '0',
  `reference` varchar(32) character set utf8 NOT NULL,
  `ean13` varchar(13) character set utf8 NOT NULL,
  `upc` varchar(12) character set utf8 NOT NULL,
  `isbn` varchar(13) character set utf8 NOT NULL,
  `out_of_stock` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `available_date` date NOT NULL,
  `ps_id_carrier_reference` varchar(255),
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id_mp_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_product_lang` (
  `id_mp_product` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `product_name` varchar(255) character set utf8 NOT NULL,
  `short_description` text,
  `description` text,
  `available_now` varchar(255) character set utf8 NOT NULL,
  `available_later` varchar(255) character set utf8 NOT NULL,
  `meta_title` varchar(255) character set utf8 NOT NULL,
  `meta_description` varchar(255) character set utf8 NOT NULL,
  `link_rewrite` varchar(255) character set utf8 NOT NULL, 
  PRIMARY KEY (`id_mp_product`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_product_feature` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ps_id_feature` int(10) unsigned NOT NULL,
  `mp_id_product` int(10) unsigned NOT NULL,
  `ps_id_feature_value` int(10) unsigned NOT NULL,
  `mp_id_feature_value` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_product_feature_value` (
  `mp_id_feature_value` int(10) unsigned NOT NULL auto_increment,
  `ps_id_feature` int(10) unsigned NOT NULL,
  `is_custom` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (`mp_id_feature_value`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_product_feature_lang` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ps_id_feature_value` int(10) unsigned NOT NULL,
  `mp_id_feature_value` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,  
  `value` varchar(255) character set utf8 NOT NULL,  
  PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_product_category` (
  `id_mp_category_product` int(10) unsigned NOT NULL auto_increment,
  `id_category` int(10) unsigned default 1,
  `id_seller_product` int(10) unsigned NOT NULL,
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY  (`id_mp_category_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_product_image` (
  `id_mp_product_image` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seller_product_id` int(10) NOT NULL,
  `seller_product_image_name` varchar(15) NOT NULL,
  `id_ps_image` int(10) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id_mp_product_image`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_product_attribute` (
  `id_mp_product_attribute` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_mp_product` int(10) unsigned NOT NULL,
  `id_ps_product_attribute` int(10) unsigned NOT NULL,
  `id_ps_product` int(10) unsigned NOT NULL,
  `mp_reference` varchar(32) DEFAULT NULL,
  `mp_ean13` varchar(13) DEFAULT NULL,
  `mp_upc` varchar(12) DEFAULT NULL,
  `mp_isbn` varchar(12) DEFAULT NULL,
  `mp_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `mp_wholesale_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `mp_unit_price_impact` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `mp_quantity` int(10) NOT NULL DEFAULT '0',
  `mp_weight` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `mp_default_on` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `mp_minimal_quantity` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_available_date` date NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_mp_product_attribute`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_product_attribute_shop` (
  `id_mp_product` int(10) unsigned NOT NULL,
  `id_mp_product_attribute` int(10) unsigned NOT NULL,
  `id_shop` int(10) unsigned NOT NULL,
  `mp_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `mp_wholesale_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `mp_unit_price_impact` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `mp_weight` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `mp_default_on` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `mp_minimal_quantity` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_available_date` date NOT NULL,
  PRIMARY KEY (`id_mp_product_attribute`,`id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_product_attribute_image` (
  `id_mp_product_attribute` int(10) unsigned NOT NULL,
  `id_image` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_mp_product_attribute`,`id_image`),
  KEY `id_image` (`id_image`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_product_attribute_combination` (
  `id_ps_attribute` int(10) unsigned NOT NULL,
  `id_mp_product_attribute` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_ps_attribute`,`id_mp_product_attribute`),
  KEY `id_mp_product_attribute` (`id_mp_product_attribute`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_stock_available` (
  `id_mp_stock_available` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_mp_product` int(11) unsigned NOT NULL,
  `id_mp_product_attribute` int(11) unsigned NOT NULL,
  `id_shop` int(11) unsigned NOT NULL,
  `id_shop_group` int(11) unsigned NOT NULL,
  `quantity` int(10) NOT NULL DEFAULT '0',
  `depends_on_stock` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `out_of_stock` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_mp_stock_available`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_order` (
  `id_mp_order` int(10) unsigned NOT NULL auto_increment,
  `seller_customer_id` int(10) unsigned NOT NULL,
  `seller_shop` varchar(255) character set utf8 NOT NULL,
  `total_earn_ti` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_earn_te` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_admin_commission` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_admin_tax` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_seller_amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_seller_tax` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id_mp_order`)
)ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_order_detail` (
  `id_mp_order_detail` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_seller_order` int(10) NOT NULL,
  `product_id` int(10) NOT NULL,
  `product_attribute_id` int(10) NOT NULL,
  `seller_customer_id` int(10) NOT NULL,
  `seller_name` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(10) NOT NULL,
  `price_ti` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `price_te` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `admin_commission` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `admin_tax` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `seller_amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `seller_tax` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `id_order` int(10) NOT NULL,
  `id_currency` int(10) NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY  (`id_mp_order_detail`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_order_status` (
  `id_order_status` int(10) unsigned NOT NULL auto_increment,
  `id_order` int(10) unsigned NOT NULL,
  `id_seller` int(10) unsigned NOT NULL,
  `current_state` int(10) unsigned NOT NULL,
  `tracking_number` varchar(64) DEFAULT NULL,
  `tracking_url` varchar(255) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id_order_status`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_order_history` (
  `id_order_history` int(10) unsigned NOT NULL auto_increment,
  `id_order` int(10) unsigned NOT NULL,
  `id_seller` int(10) unsigned NOT NULL,
  `id_order_state` int(10) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY  (`id_order_history`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_commision` (
  `id_wk_mp_commision` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `commision_rate` decimal(20,2) NOT NULL DEFAULT '0.000000',
  `seller_customer_id` int(10) NOT NULL,
  PRIMARY KEY  (`id_wk_mp_commision`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_payment_mode` (
  `id_mp_payment` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_mode` varchar(255) NOT NULL,
   PRIMARY KEY  (`id_mp_payment`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_customer_payment_detail` (
  `id_customer_payment` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seller_customer_id` int(100) unsigned NOT NULL,
  `payment_mode_id` int(100) unsigned NOT NULL,
  `payment_detail` varchar(255) NOT NULL,
   PRIMARY KEY  (`id_customer_payment`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_help_desk` (
  `id_mp_help_desk` int(10) unsigned NOT NULL auto_increment,
  `id_product` int(11),
  `id_customer` int(11),
  `id_seller` int(11),
  `subject` varchar(128) DEFAULT NULL,
  `description` text,
  `customer_email` varchar(128) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
 PRIMARY KEY  (`id_mp_help_desk`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_review` (
  `id_review` int(10) unsigned NOT NULL auto_increment,
  `id_seller` int(11),
  `id_customer` int(11),
  `customer_email` varchar(100),
  `rating` int(11),
  `review` text,
  `active` tinyint(1),
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id_review`)
)ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_shipping` (
  `id_mp_shipping` int(11) unsigned NOT NULL auto_increment,
  `order_id` int(11) NOT NULL,
  `shipping_description` text,
  `shipping_date` datetime,
  PRIMARY KEY  (`id_mp_shipping`)
)ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_delivery` (
  `id_mp_delivery` int(11) unsigned NOT NULL auto_increment,
  `order_id` int(11) NOT NULL,
  `delivery_date` datetime,
  `received_by` varchar(255),
  PRIMARY KEY  (`id_mp_delivery`)
)ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_admin_shipping` (
  `id_wk_mp_admin_shipping` int(11) unsigned NOT NULL auto_increment,
  `order_id` int(11) NOT NULL,
  `order_reference` VARCHAR(9),
  `shipping_amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id_wk_mp_admin_shipping`)
)ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_order_voucher` (
  `id_order_voucher` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL,
  `seller_id` int(10) NOT NULL,
  `voucher_name` varchar(255) NOT NULL,
  `voucher_value` decimal(20,6) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id_order_voucher`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

/* --------Seller Payment Transaction ------------ */

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_payment`(
`id_seller_payment` int(10) unsigned NOT NULL auto_increment,
`id_seller` int(10) unsigned NOT NULL,
`total_earning` decimal(20,6) NOT NULL DEFAULT '0.000000',
`total_pending` decimal(20,6) NOT NULL DEFAULT '0.000000',
`total_paid` decimal(20,6) NOT NULL DEFAULT '0.000000',
`total_due` decimal(20,6) NOT NULL DEFAULT '0.000000',
`id_currency` int(10) unsigned NOT NULL,
PRIMARY KEY  (`id_seller_payment`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_order_payment`(
`id_seller_order_payment` int(10) unsigned NOT NULL auto_increment,
`id_order` int(10) unsigned NOT NULL,
`id_seller` int(10) unsigned NOT NULL,
`current_state` int(10) unsigned NOT NULL,
`seller_total` decimal(20,6) NOT NULL DEFAULT '0.000000',
`id_currency` int(10) unsigned NOT NULL,
PRIMARY KEY  (`id_seller_order_payment`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_seller_payment_history`(
`id_seller_payment_history` int(10) unsigned NOT NULL auto_increment,
`id_seller` int(10) NOT NULL,
`id_currency` int(10) unsigned NOT NULL,
`amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
`payment_method` varchar(255) NOT NULL,
`payment_type` int(10) unsigned NOT NULL,
`payment_was` int(10) unsigned NOT NULL,
`id_transaction` VARCHAR(254) NULL,
`remark` varchar(255) DEFAULT NULL,
`status` int(10) unsigned NOT NULL,
`date_add` datetime NOT NULL,
PRIMARY KEY  (`id_seller_payment_history`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
