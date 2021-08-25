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

class WkGdprDb
{
    public function getModuleSql()
    {
        return array(
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."wk_gdpr_agreement_data` (
                `id_agreement_data` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `id_module` int(11) NOT NULL,
                `active` tinyint(1) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_agreement_data`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."wk_gdpr_agreement_data_lang` (
                `id_agreement_data` int(11) NOT NULL,
                `id_lang` int(11) NOT NULL,
                `agreement_content` text NOT NULL,
                PRIMARY KEY (`id_agreement_data`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."wk_gdpr_customer_requests` (
                `id_request` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `id_customer` text NOT NULL,
                `request_type` text NOT NULL,
                `request_reason` text NOT NULL,
                `status` text NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_request`)
            ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 ;",
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."wk_gdpr_anonymous_customer` (
                `id_gdpr_anonymous_customer` int(11) NOT NULL AUTO_INCREMENT,
                `id_customer` int(11) NOT NULL,
                `date_add` datetime NOT NULL,
                PRIMARY KEY (`id_gdpr_anonymous_customer`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;"
        );
    }

    public function createTables()
    {
        if ($sql = $this->getModuleSql()) {
            $objDb = Db::getInstance();
            foreach ($sql as $query) {
                if ($query) {
                    if (!$objDb->execute(trim($query))) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function dropTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'wk_gdpr_agreement_data`,
            `'._DB_PREFIX_.'wk_gdpr_agreement_data_lang`,
            `'._DB_PREFIX_.'wk_gdpr_customer_requests`,
            `'._DB_PREFIX_.'wk_gdpr_anonymous_customer`'
        );
    }
}
