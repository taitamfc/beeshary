CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_pack_product` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `new_mp_product_id` int(10) unsigned NOT NULL,
  `mp_product_id` int(10) unsigned NOT NULL,
  `mp_product_id_attribute` int(10) unsigned NOT NULL,
  `quantity` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;