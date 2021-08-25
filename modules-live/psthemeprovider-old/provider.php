<?php
/**
  *  @author    Inveo s.r.o. <inqueries@inveoglobal.com>
  *  @copyright 2009-2015 Inveo s.r.o.
  *  @license   EULA
  *  @date: 2015-03-16
  *  @compatibility: PHP 5 >= 5.0.0
  *  @version: 1.0.17
  */

require_once(dirname(__FILE__).'/../../config/config.inc.php');
$useSSL = false;
if(
	(method_exists('Tools', 'usingSecureMode') && Tools::usingSecureMode())
		||
	(isset($_SERVER['HTTPS']) && in_array(strtolower($_SERVER['HTTPS']), array(1, 'on')))
)
	$useSSL = true;
require_once(dirname(__FILE__).'/../../init.php');
require_once(dirname(__FILE__).'/'.basename(dirname(__FILE__)).'.php');

if(!defined('THEMEPROVIDER_INIT'))
	exit();

$key = Tools::getValue('apikey');
if(ThemeProviderPlugin::validateApikey($key))
{
	$pse = new Psthemeprovider();
	if ($pse->active)
	{
		ThemeProvider::initPreProcess();
		if(
			ThemeProvider::cacheNeedUpdate()
				||
			ThemeProvider::cacheNoCache()
				||
			!ThemeProviderPlugin::cacheUser()
		)
		{
			ThemeProvider::initPostProcess();
			echo ThemeProviderPlugin::runtime($key);
		}
		else
		{
			echo ThemeProvider::cacheRead();
		}
	}
}


?>
