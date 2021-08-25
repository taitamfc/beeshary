CREATE TABLE IF NOT EXISTS `PREFIX_mp_price_slots` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`id_specific_price` INT UNSIGNED NOT NULL,
	`mp_id_product` int(10) unsigned NOT NULL,
	`id_product_attribute` int(10) unsigned NOT NULL,
	`id_shop` int(10) unsigned NOT NULL,
	`id_currency` int(10) unsigned NOT NULL,
	`id_country` int(10) unsigned NOT NULL,
	`id_group` int(10) unsigned NOT NULL,
	`id_customer` INT UNSIGNED NOT NULL,
	`price` DECIMAL(20, 6) NOT NULL,
	`from_quantity` mediumint(8) UNSIGNED NOT NULL,
	`reduction` DECIMAL(20, 6) NOT NULL,
	`reduction_tax` tinyint(1) NOT NULL DEFAULT 1,
	`reduction_type` ENUM('amount', 'percentage') NOT NULL,
	`from` DATETIME NOT NULL,
	`to` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;