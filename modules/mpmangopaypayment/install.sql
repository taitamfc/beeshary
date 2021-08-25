/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_mangopay_config` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_employee` int(10) unsigned NOT NULL,
  `mgp_clientid` varchar(64) NOT NULL,
  `mgp_passphrase` varchar(255) character set utf8 NOT NULL,
  `mgp_userid` varchar(32) character set utf8 NOT NULL,
  `mgp_walletid` varchar(32) character set utf8 NOT NULL,
  `user_type` varchar(255) character set utf8 NOT NULL,
  `user_email` varchar(128) character set utf8 NOT NULL,
  `currency_iso` varchar(11) character set utf8 NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_mangopay_seller` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `mgp_clientid` varchar(64) NOT NULL,
  `mgp_userid` varchar(32) character set utf8 NOT NULL,
  `mgp_walletid` varchar(32) character set utf8 NOT NULL,
  `id_seller` int(10) unsigned NOT NULL,
  `currency_iso` varchar(11) character set utf8 NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_mangopay_buyer` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_customer` int(10) unsigned NOT NULL,
  `mgp_clientid` varchar(64) NOT NULL,
  `mgp_userid` varchar(32) character set utf8 NOT NULL,
  `mgp_walletid` varchar(32) character set utf8 NOT NULL,
  `user_type` varchar(255) character set utf8 NOT NULL,
  `user_email` varchar(128) character set utf8 NOT NULL,
  `currency_iso` varchar(11) character set utf8 NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_mangopay_seller_country` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_customer` int(10) unsigned NOT NULL,
  `id_seller` int(10) unsigned NOT NULL,
  `id_country` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_mangopay_transaction` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_cart` int(11) unsigned NOT NULL,
  `transaction_id` int(10) unsigned NOT NULL,
  `order_reference` varchar(9) NOT NULL,
  `mgp_clientid` varchar(64) NOT NULL,
  `buyer_mgp_userid` varchar(32) character set utf8 NOT NULL,
  `credited_mgp_userid` varchar(32) character set utf8 NOT NULL,
  `credited_mgp_walletid` varchar(32) character set utf8 NOT NULL,
  `payment_type` varchar(255) character set utf8 NOT NULL,
  `amount_paid` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `credited_amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `currency` varchar(11) NOT NULL,
  `fees` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `mandate_id` varchar(32) character set utf8 NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL,
  `creation_date` int(20) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_mangopay_bankwire_details` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `id_mangopay_transaction` int(11) unsigned NOT NULL,
  `mgp_wire_reference` varchar(32) character set utf8 NOT NULL,
  `mgp_account_type` varchar(32) character set utf8 NOT NULL,
  `mgp_account_owner_name` varchar(32) character set utf8 NOT NULL,
  `mgp_account_iban` varchar(255) character set utf8 NOT NULL,
  `mgp_account_bic` varchar(255) character set utf8 NOT NULL,
  `declared_amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_mangopay_transfer_details` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `order_reference` varchar(9) NOT NULL,
  `mgp_clientid` varchar(64) NOT NULL,
  `buyer_id_customer` int(10) unsigned NOT NULL,
  `id_seller` int(10) unsigned NOT NULL,
  `currency` varchar(11) NOT NULL,
  `amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `fees` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `transfer_id` int(10) unsigned NOT NULL,
  `refund_transfer_id` int(10) unsigned NOT NULL,
  `is_refunded` tinyint(1) unsigned NOT NULL,
  `refunded_by` varchar(10),
  `send_to_card` tinyint(1) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wk_mp_mangopay_payin_refund` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `payin_id` int(10) unsigned NOT NULL,
  `refund_id` int(10) unsigned NOT NULL,
  `amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;