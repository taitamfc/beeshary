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
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *   @author    Buy-addons <contact@buy-addons.com>
 *   @copyright 2007-2020 PrestaShop SA
 *   @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *   International Registered Trademark & Property of PrestaShop SA
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

require_once('./ba_importer.php');
$ba_importer = new Ba_importer();

error_reporting(E_ALL);
if (Tools::getValue('baimporter_token') != sha1(_COOKIE_KEY_ . 'baimporter')) {
    echo $ba_importer->l("You do not have permission to access it.");
    die;
}
set_time_limit(0);
require_once('./classes/ajaximport.php');

$cookiekey = $ba_importer->cookiekeymodule();
$batoken = Tools::getValue("batoken");

$cookie = new Cookie('psAdmin');
$id_employee = $cookie->id_employee;
if ($batoken == $cookiekey && !empty($id_employee)) {
    $a = new AjaxImport();
    $a->submitAddDb();
} else {
    echo $ba_importer->l("You do not have permission to access it.");
    die;
}
