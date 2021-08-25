<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_5_2_0($object)
{
    $object->registerHook('displayOrderDetailsExtraTab');
    $object->registerHook('displayOrderDetailsExtraTabContent');
    $object->registerHook('displayAdminPsSellerOrderViewHead');
    $object->registerHook('displayAdminPsSellerOrderViewBody');
    $object->registerHook('actionBeforeAddMPProduct');
    $object->registerHook('actionBeforeUpdateMPProduct');

    return Db::getInstance()->execute(
        'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'mp_shipping_method_group` (
            `mp_shipping_id` int(10) unsigned NOT NULL,
            `id_group` int(10) unsigned NOT NULL,
            PRIMARY KEY (`mp_shipping_id`,`id_group`),
            KEY `id_group` (`id_group`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'
    );
}
