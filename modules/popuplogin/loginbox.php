<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2016 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
include_once('../../config/config.inc.php');
$fb_url = '';
$gg_url = '';

include_once('popuplogin.php');
$popuplogin = new popuplogin();
if ($popuplogin->psversion() == 5 || $popuplogin->psversion() == 6 || $popuplogin->psversion() == 7)
{
    if (isset(Context::getContext()->controller))
    {
        $controller = Context::getContext()->controller;
    }
    else
    {
        $controller = new FrontController();
        if (isset($_SERVER['HTTPS']))
        {
            $controller->ssl = true;
        }
        $controller->init();
    }
}
else
{
    include_once('../../init.php');
}

$popuplogin = new popuplogin();
$link = new Link();
include(Configuration::get('ppl_design') . '.php');
?>