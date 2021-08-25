
CREATE TABLE IF NOT EXISTS `PREFIX_anywhere` (
  `id_anywhere` int(11) NOT NULL AUTO_INCREMENT,
  `hook` varchar(128) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_anywhere`)
) ENGINE=_ENGINE_ DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `PREFIX_anywhere_lang` (
  `id_anywhere` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id_anywhere`,`id_lang`)
) ENGINE=_ENGINE_ DEFAULT CHARSET=utf8;

