<?php
/**
 * 2007-2015 Apollotheme
 *
 * NOTICE OF LICENSE
 *
 * ApPageBuilder is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Apollotheme <apollotheme@gmail.com>
 *  @copyright 2007-2015 Apollotheme
 *  @license   http://apollotheme.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
include_once(dirname(__FILE__).'/appagebuilder.php');

$module = APPageBuilder::getInstance();

//DONGND:: get product link for demo multi product detail
if (Tools::getValue('action') == 'get-list-shortcode') {
    $result = '';
    $result = $module->getListShortCodeForEditor();
    die($result);
}
