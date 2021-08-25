/**
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

CREATE TABLE IF NOT EXISTS `PREFIX_marketplace_seller_payment`(
`id` int(10) unsigned NOT NULL auto_increment,
`id_seller` int(10) unsigned NOT NULL,
`total_earning` decimal(20,6) NOT NULL DEFAULT '0.000000',
`total_paid` decimal(20,6) NOT NULL DEFAULT '0.000000',
`total_due` decimal(20,6) NOT NULL DEFAULT '0.000000',
`id_currency` int(10) unsigned NOT NULL,
PRIMARY KEY  (`id`)
)ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_marketplace_seller_payment_transactions`(
`id` int(10) unsigned NOT NULL auto_increment,
`id_seller` int(10) NOT NULL,
`id_currency` int(10) unsigned NOT NULL,
`amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
`date_add` datetime NOT NULL,
`type` varchar(100),
`status` int(10) unsigned NOT NULL,
PRIMARY KEY  (`id`)
)ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
