<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@buy-addons.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Buy-addons <contact@buy-addons.com>
 *  @copyright 2007-2020 Buy-addons
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

header('Content-Type: text/html; charset=ISO-8859-1');
require_once('../../config/config.inc.php');
// if maintaince mode enable
$remote_ip = Tools::getRemoteAddr();
if (!(int)Configuration::get('PS_SHOP_ENABLE')) {
    if (!in_array($remote_ip, explode(',', Configuration::get('PS_MAINTENANCE_IP')))) {
        if (!Configuration::get('PS_MAINTENANCE_IP')) {
            Configuration::updateValue('PS_MAINTENANCE_IP', $remote_ip);
        } else {
            Configuration::updateValue('PS_MAINTENANCE_IP', Configuration::get('PS_MAINTENANCE_IP') . ',' . $remote_ip);
        }
    }
}

require_once('../../init.php');
require_once('./ba_importer.php');
@set_time_limit(0);
require_once('./classes/autoimport.php');

$ba_importer = new Ba_importer();
$cookiekey = $ba_importer->cookiekeymodule();
$batoken = Tools::getValue("batoken");
$id_queue = Configuration::get('baautoimpor_id_queue');

if ($batoken == $cookiekey) {
    $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
    // check if a setting crashed and can not finished
    $sql = 'SELECT count(*) FROM ' . _DB_PREFIX_ . 'ba_cronjobs_importer a';
    $sql .= ' INNER JOIN ' . _DB_PREFIX_ . 'ba_importer_config b';
    $sql .= ' ON  a.id_importer_config = b.id_importer_config';
    $sql .= ' WHERE b.import_local !=1 AND a.status_imported = 2 ';
    $sql .= ' AND DATE_ADD(update_at, INTERVAL 2 hour) < NOW()';
    $pending = $db->getValue($sql, false);
    if ($pending > 0) {
        $sql2 = 'UPDATE ' . _DB_PREFIX_ . 'ba_cronjobs_importer';
        $sql2 .= ' SET imported = 0, status_imported = 1, products_imported = NULL ';
        $db->query($sql2);
        Configuration::updateGlobalValue('baautoimpor_is_run', 0);
    }
    //  It is okay, process autoimport
    $id_importer_config = Tools::getValue('id_importer_config');
    $id_importer_config = trim($id_importer_config);
    $id_importer_config = trim($id_importer_config, ",");
    $sl_ba_cronjobs = 'SELECT a.* FROM ' . _DB_PREFIX_ . 'ba_cronjobs_importer a';
    $sl_ba_cronjobs .= ' INNER JOIN ' . _DB_PREFIX_ . 'ba_importer_config b';
    $sl_ba_cronjobs .= ' ON  a.id_importer_config = b.id_importer_config';
    $sl_ba_cronjobs .= ' WHERE b.import_local !=1 AND a.imported = 0';
    if ($id_importer_config != false) {
        if (!empty($id_queue)) {
            $array_tmp = explode(',', $id_importer_config);
            $array_queue = explode(',', $id_queue);
            $array_result = array_unique(array_merge($array_queue, $array_tmp));
            $array_result = $ba_importer->deleteElement('', $array_result); // remove empty
            $array_result = implode(',', $array_result);
            Configuration::updateGlobalValue('baautoimpor_id_queue', $array_result);
        } else {
            Configuration::updateGlobalValue('baautoimpor_id_queue', $id_importer_config);
        }
        $id_queue = Configuration::get('baautoimpor_id_queue');
        $sl_ba_cronjobs .= ' AND a.id_importer_config IN ('.pSQL($id_queue).')
            ORDER BY FIELD(a.id_importer_config,'.pSQL($id_queue).')';
    }
    $sl_ba_cronjobs .= ' LIMIT 0,1';
    $list_ba_cron = $db->ExecuteS($sl_ba_cronjobs, true, false);
    $product_end = Tools::getValue('product_end');
    $autoimport = new AutoImport();
    $autoimport->funcAutoImport($list_ba_cron, $product_end);
} else {
    echo $ba_importer->l("You do not have permission to access it.");
    die;
}
