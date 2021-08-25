<?php
/**
 * Redis Cache powered by Vopster
 *
 *    @author    Vopster.com
 *    @copyright 2017 Vopster.com
 *    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 *    @link      https://addons.prestashop.com/en/contact-us?id_product=26866
 */

class AdminRediscacheController extends ModuleAdminController
{
    public function ajaxProcessGetStatus()
    {
        $response = array();
        $response['status'] = RedisHelper::getConfig('PS_REDIS_STATUS');
        die(Tools::jsonEncode($response));
    }
}
