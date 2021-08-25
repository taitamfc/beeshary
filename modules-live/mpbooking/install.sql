/**
* 2010-2019 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_booking_product_info` (
  `id_booking_product_info` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(10) unsigned NOT NULL,
  `id_mp_product` int(10) unsigned NOT NULL,
  `id_seller` int(10) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT '0',
  `booking_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_booking_product_info`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_booking_time_slots_prices` (
  `id_time_slots_price` int(11) NOT NULL AUTO_INCREMENT,
  `id_booking_product_info` int(10) unsigned NOT NULL,
  `date_from` datetime NOT NULL,
  `date_to` datetime NOT NULL,
  `time_slot_from` varchar(255) DEFAULT NULL,
  `time_slot_to` varchar(255) DEFAULT NULL,
  `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_time_slots_price`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_booking_product_feature_pricing` (
  `id_feature_price_rule` int(11) NOT NULL AUTO_INCREMENT,
  `id_booking_product_info` int(11) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `is_special_days_exists` tinyint(1) NOT NULL,
  `date_selection_type` tinyint(1) NOT NULL,
  `special_days` text,
  `impact_way` tinyint(1) NOT NULL,
  `impact_type` tinyint(1) NOT NULL,
  `impact_value` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_feature_price_rule`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_booking_product_feature_pricing_lang` (
  `id_feature_price_rule` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `feature_price_name` varchar(255) character set utf8 NOT NULL,
  PRIMARY KEY (`id_feature_price_rule`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_booking_cart` (
  `id_booking_cart` int(11) NOT NULL AUTO_INCREMENT,
  `id_cart` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `booking_type` tinyint(4) NOT NULL,
  `date_from` datetime NOT NULL,
  `date_to` datetime NOT NULL,
  `time_from` varchar(255) DEFAULT NULL,
  `time_to` varchar(255) DEFAULT NULL,
  `consider_last_date` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_booking_cart`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_booking_order` (
  `id_booking_order` int(11) NOT NULL AUTO_INCREMENT,
  `id_cart` int(11) NOT NULL,
  `id_order` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_mp_product` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `booking_type` tinyint(4) NOT NULL,
  `date_from` datetime NOT NULL,
  `date_to` datetime NOT NULL,
  `time_from` varchar(255) DEFAULT NULL,
  `time_to` varchar(255) DEFAULT NULL,
  `product_real_price_tax_excl` decimal(20,6) NOT NULL,
  `product_real_price_tax_incl` decimal(20,6) NOT NULL,
  `range_feature_price_tax_incl` decimal(20,6) NOT NULL,
  `range_feature_price_tax_excl` decimal(20,6) NOT NULL,
  `total_order_tax_excl` decimal(20,6) NOT NULL,
  `total_order_tax_incl` decimal(20,6) NOT NULL,
  `consider_last_date` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_booking_order`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_booking_product_disabled_dates` (
  `id_disabled_dates` int(11) NOT NULL AUTO_INCREMENT,
  `id_booking_product_info` int(10) unsigned NOT NULL,
  `disable_special_days_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `disabled_dates_slots_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `disabled_special_days` text,
  `disabled_dates_slots` text,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_disabled_dates`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;