CREATE TABLE IF NOT EXISTS `PREFIX_marketplace_login_block_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_shop` int(11) NOT NULL,
  `id_parent` int(11) NOT NULL,
  `id_position` int(11) NOT NULL,
  `id_theme` int(11) NOT NULL,
  `block_name` text NOT NULL,
  `width` int(11) NOT NULL,
  `block_bg_color` text NOT NULL,
  `block_text_color` text NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_marketplace_login_configration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_shop` int(11) NOT NULL,
  `id_theme` int(11) NOT NULL,
  `header_bg_color` text NOT NULL,
  `body_bg_color` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_marketplace_login_configration_lang` (
  `id` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,  
  `meta_title` text,  
  `meta_description` text,
  PRIMARY KEY (`id`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_marketplace_login_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_shop` int(11) NOT NULL,
  `id_block` int(11) NOT NULL,
  `id_theme` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_marketplace_login_content_lang` (
  `id` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,  
  `content` text NOT NULL,
  PRIMARY KEY (`id`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_marketplace_login_parent_block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_position` int(11) NOT NULL,
  `id_theme` int(11) NOT NULL,
  `name` text NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_marketplace_login_theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_marketplace_login_block_position` (`id`, `id_shop`, `id_parent`, `id_position`, `id_theme`, `block_name`, `width`, `block_bg_color`, `block_text_color`, `active`) VALUES
(1, 1, 3, 2, 1, 'termscondition', 12, '#ffffff', '#555555', 1),
(2, 1, 3, 1, 1, 'feature', 12, '#ffffff', '#555555', 1),
(3, 1, 1, 1, 1, 'logo', 3, '', '#ffffff', 1),
(4, 1, 1, 2, 1, 'login', 9, '', '#ffffff', 1),
(5, 1, 2, 2, 1, 'reg_block', 4, '#ffffff', '#555555', 1),
(6, 1, 2, 1, 1, 'reg_title', 8, '#ffffff', '#ffffff', 1),
(7, 1, 6, 2, 2, 'termscondition', 12, '#ffffff', '#555555', 1),
(8, 1, 6, 1, 2, 'feature', 12, '#ffffff', '#555555', 1),
(9, 1, 4, 1, 2, 'logo', 3, '', '#ffffff', 1),
(10, 1, 4, 2, 2, 'login', 9, '', '#ffffff', 1),
(11, 1, 5, 2, 2, 'reg_block', 4, '#ffffff', '#555555', 1),
(12, 1, 5, 1, 2, 'reg_title', 8, '#ffffff', '#ffffff', 1),
(13, 1, 9, 2, 3, 'termscondition', 12, '#ffffff', '#555555', 1),
(14, 1, 9, 1, 3, 'feature', 12, '#ffffff', '#555555', 1),
(15, 1, 7, 1, 3, 'logo', 3, '', '#252525', 1),
(16, 1, 7, 2, 3, 'login', 9, '', '#252525', 1),
(17, 1, 8, 2, 3, 'reg_block', 4, '#ffffff', '#555555', 1),
(18, 1, 8, 1, 3, 'reg_title', 8, '#ffffff', '#ffffff', 1);

INSERT INTO `PREFIX_marketplace_login_configration` (`id`, `id_shop`, `id_theme`, `header_bg_color`, `body_bg_color`) VALUES
(1, 1, 1, '#252525', '#ffffff'),
(2, 1, 2, '#252525', '#ffffff'),
(3, 1, 3, '#F2F2F2', '#ffffff');

INSERT INTO `PREFIX_marketplace_login_content` (`id`, `id_shop`, `id_block`, `id_theme`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 1),
(3, 1, 6, 1),
(4, 1, 7, 2),
(5, 1, 8, 2),
(6, 1, 12, 2),
(7, 1, 13, 3),
(8, 1, 14, 3),
(9, 1, 18, 3);

INSERT INTO `PREFIX_marketplace_login_parent_block` (`id`, `id_position`, `id_theme`, `name`, `active`) VALUES
(1, 0, 1, 'header', 1),
(2, 1, 1, 'registration', 1),
(3, 2, 1, 'content', 1),
(4, 0, 2, 'header', 1),
(5, 1, 2, 'registration', 1),
(6, 2, 2, 'content', 1),
(7, 0, 3, 'header', 1),
(8, 1, 3, 'registration', 1),
(9, 2, 3, 'content', 1);

INSERT INTO `PREFIX_marketplace_login_theme` (`id`, `name`, `active`) VALUES
(1, 'theme1', 1),
(2, 'theme2', 0),
(3, 'theme3', 0);