<?php
/*
* 2010-2016 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

$current_dir = dirname(__FILE__).'/';
require_once ($current_dir.'ps_curl.php');
require_once '../../config/config.inc.php';

$page_link = Context::getContext()->link->getModuleLink('mpsellervacation', 'vacationProcessByCron');
$request = new PS_CURL();
$request->setCookiFileLocation($current_dir.'PScookie.txt');
$request->call($page_link);
echo $request->getHttpStatus();

//   cron - */1 * * * * /usr/bin/php /home/prince/public_html/prestashop_1.6.1.5_db13/modules/mpsellervacation/ps_cron.php