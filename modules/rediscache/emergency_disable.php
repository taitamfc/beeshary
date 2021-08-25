<?php
/**
 * Redis Cache powered by Vopster
 *
 *    @author    Vopster
 *    @copyright 2017 Vopster
 *    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 *    @link      https://addons.prestashop.com/en/contact-us?id_product=26866
 */

define('PS_ADMIN_DIR', getcwd());

require_once PS_ADMIN_DIR . '/../../config/config.inc.php';
require_once dirname(__FILE__) . '/classes/BackwardCompatibility.php';
require_once dirname(__FILE__) . '/classes/RedisHelper.php';

$cookie = new Cookie('psAdmin');

if ($cookie->id_employee) {
    RedisHelper::overrideDefaultCaching(0);
    RedisHelper::saveConfig('PS_REDIS_STATUS', 0);
    RedisHelper::saveConfig('PS_REDIS_SESSION_CACHE_STATUS', 0);
    echo 'Caching was disabled as an emergency measure. Please contact support.';
} else {
    echo 'You are not authorized to access this file. Please log in first.';
}
