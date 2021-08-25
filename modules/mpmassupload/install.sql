CREATE TABLE IF NOT EXISTS `PREFIX_marketplace_mass_upload` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`mass_upload_category` int(11) unsigned NOT NULL,
	`request_id` varchar(255) character set utf8 NOT NULL,
	`id_seller` int(11) unsigned NOT NULL,
	`total_records` int(11) unsigned NOT NULL,
	`is_approve` tinyint(1) unsigned NOT NULL,
	`status` varchar(255) character set utf8 NOT NULL,
	`is_csv_product_added` tinyint(1) unsigned NOT NULL DEFAULT 0,
	`csv_type` tinyint(1) unsigned NOT NULL,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;