CREATE TABLE IF NOT EXISTS `PREFIX_mp_badges` (
`id` int(10) unsigned NOT NULL auto_increment,
`badge_name` varchar(255) character set utf8 NOT NULL,
`badge_desc` varchar(255) character set utf8 NOT NULL,
`active` tinyint(1) unsigned NOT NULL DEFAULT '0',
`date_add` datetime NOT NULL,
`date_upd` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `PREFIX_mp_seller_badges` (
`id` int(10) unsigned NOT NULL auto_increment,
`badge_id` int(10) unsigned NOT NULL,
`mp_seller_id` int(10) unsigned NOT NULL,
`date_add` datetime NOT NULL,
`date_upd` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_seller_badges_configuration` (
`id` int(10) unsigned NOT NULL auto_increment,
`id_seller` int(10) unsigned NOT NULL,
`active` tinyint(1) unsigned NOT NULL DEFAULT '0',
PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
