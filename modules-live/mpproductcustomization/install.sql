CREATE TABLE IF NOT EXISTS `PREFIX_mp_product_customization` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `id_mp_product` int(10) unsigned NOT NULL,
    `type` tinyint(1) NOT NULL,
    `required` tinyint(1) NOT NULL,
    PRIMARY KEY (`id`) 
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_product_customization_lang` (
    `id` int(10) unsigned NOT NULL,
    `id_lang` int(10) unsigned NOT NULL,
    `id_ps_shop` int(10) unsigned NOT NULL DEFAULT '1',
    `name` varchar(255) character set utf8 NOT NULL,
    PRIMARY KEY (`id`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
