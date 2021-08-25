<?php
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

class WkMpSellerInvoiceDb
{
    public function createTable()
    {
        $success = true;
        $db = Db::getInstance();
        $queries = $this->getDbTableQueries();

        foreach ($queries as $query) {
            $success &= $db->execute($query);
        }

        return $success;
    }

    protected function getDbTableQueries()
    {
        return array(
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."mp_seller_order_invoice` (
                `id_order_invoice` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `invoice_number` int(11) NOT NULL,
                `id_order` int(11) NOT NULL,
                `id_seller` int(11) NOT NULL,
                PRIMARY KEY  (`id_order_invoice`)
            ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."mp_seller_invoice_config` (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_seller` int(10) unsigned NOT NULL,
                `invoice_number` int(10) DEFAULT NULL,
                `invoice_vat` varchar(32) DEFAULT NULL,
                `invoice_based`  int(11) NOT NULL DEFAULT '1',
                `value` int(11) NOT NULL DEFAULT '0',
                `time_interval` ENUM('day', 'week', 'month', 'year') NOT NULL DEFAULT  'day',
                `last_generated` datetime NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY  (`id`)
            ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."mp_seller_invoice_config_lang` (
                `id` int(11) UNSIGNED NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `invoice_prefix` varchar(32) DEFAULT NULL,
                `invoice_legal_text` text,
                `invoice_footer_text` text,
                PRIMARY KEY (`id`, `id_lang`)
            ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."mp_admin_commission_invoice_history` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_seller` int(11) NOT NULL,
                `invoice_number` int(11) NOT NULL,
                `invoice_based`  int(11) NOT NULL DEFAULT '1',
                `from` datetime NOT NULL,
                `to` datetime NOT NULL,
                `orders` text,
                `is_send_to_seller`  int(11) NOT NULL DEFAULT '0',
                `last_notification` datetime NOT NULL DEFAULT '00-00-00 00:00:00',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY  (`id`)
            ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8"
        );
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'mp_seller_order_invoice`,
            `'._DB_PREFIX_.'mp_seller_invoice_config`,
            `'._DB_PREFIX_.'mp_seller_invoice_config_lang`,
            `'._DB_PREFIX_.'mp_admin_commission_invoice_history`;
        ');
    }
}
