<?php
/**
* 2010-2021 Webkul.
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
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkMpWebserviceDb
{
    public function createTable()
    {
        $mpSuccess = true;
        $mpDatabaseInstance = Db::getInstance();
        if ($tableQueries = $this->getMpTableQueries()) {
            foreach ($tableQueries as $mpQuery) {
                $mpSuccess &= $mpDatabaseInstance->execute(trim($mpQuery));
            }
        }

        return $mpSuccess;
    }

    private function getMpTableQueries()
    {
        return array(
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."wk_mp_webservice_key` (
                `id_wk_mp_webservice_key` int(11) NOT NULL AUTO_INCREMENT,
                `id_seller` int(10) unsigned NOT NULL,
                `key` varchar(32) NOT NULL,
                `description` text NULL,
                `mpresource` text NOT NULL,
                `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_wk_mp_webservice_key`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8",
        );
    }

    public function deleteTable()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'wk_mp_webservice_key`'
        );
    }
}
