<?php
/**
  *  @author    Inveo s.r.o. <inqueries@inveoglobal.com>
  *  @copyright 2009-2015 Inveo s.r.o.
  *  @license   EULA
  */

define('THEMEPROVIDER_DIRDEPTH', 2);

/** @class: ThemeProviderPlugin
  * @project: Theme Provider
  * @date: 2015-03-20
  * @compatibility: PHP 5 >= 5.0.0
  * @version: 3.1.00
  */
class ThemeProviderPlugin
	extends ThemeProviderPluginCore
		implements ThereProviderPluginInterface
{
	const PLUGIN_NAME = 'PrestaShop';

	public static function cacheIdSandboxed($apikey)
	{
		if(!self::validateApikey($apikey))
		{
			return false;
		}

		if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$cart = Context::getContext()->cart;
			$shop = Context::getContext()->shop->id;
		}
		else
		{
			global $cart;
			$shop = 1;
		}
		
		if(is_null($cart)) // in admin
		{
			return false;
		}
		
		return array(
				(int)$shop,
				(int)$cart->id_customer,
				(int)$cart->id,
				(int)$cart->id_lang,
				(int)$cart->id_currency,
				(int)$cart->id_address_invoice,
				(int)$cart->id_address_delivery,
				(int)$cart->id_carrier
			);
	}

	public static function cacheIdDirect($apikey)
	{
		require_once(dirname(__FILE__).'/../../config/config.inc.php');
		require_once(dirname(__FILE__).'/../../init.php');
		
		if(!self::validateApikey($apikey))
		{
			return false;
		}

		return self::cacheIdSandboxed($apikey);
	}

	public static function runtime($apikey)
	{
		global $useSSL;
		if (self::runTimeLoaded())
		{
			extract(
					ThemeProviderEnvironment::getSmallGlobals(),
					EXTR_OVERWRITE
			); // recovering globals
		}

		ThemeProviderConnectorListener::init();

		require_once(dirname(__FILE__).'/../../config/config.inc.php');
		$useSSL = false;
		if(
			(method_exists('Tools', 'usingSecureMode') && Tools::usingSecureMode())
				||
			(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')
		)
		{
			$useSSL = true;
		}
		require_once(dirname(__FILE__).'/../../init.php');

		if(!self::validateApikey($apikey))
		{
			return false;
		}

		ThemeProviderEnvironment::globalizeVars(get_defined_vars());
		
		if(version_compare(_PS_VERSION_, '1.7.0.0', '>='))
		{
			$_GET['module'] = 'psthemeprovider';
			require_once(_PS_MODULE_DIR_.'psthemeprovider/controllers/front/provider.php');
			$controller_class = 'PsthemeproviderproviderModuleFrontController';
			$controller = Controller::getController($controller_class);
			$controller->php_self = $controller->page_name = 'module-psthemeprovider-provider';
			$controller->theme_separator = ThemeProvider::themeSeparate();
			Hook::exec('actionDispatcher', array(
												'controller_type' => Dispatcher::FC_FRONT,
												'controller_class' => $controller_class,
												'is_module' => 1)
										);
			$controller->run();
			return ThemeProviderConnectorListener::shutdown();
		}
		else
		{
			if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				$controller->setMedia();
				$_GET['controller'] = basename(THEMEPROVIDER_FILENAME_PROVIDER, '.php');
				$smarty = Context::getContext()->smarty;
				
				if(version_compare(_PS_VERSION_, '1.6.0.0', '>=') && version_compare(_PS_VERSION_, '1.7.0.0', '<'))
				{
					$layout = $controller->getLayout();
					if($layout)
					{
						$c = file_get_contents($layout);
						if(Configuration::get('PS_THEMEPROVIDER_LEFTCOLUMN'))
						{
							if(preg_match('/\{\$left_column_size=([0-9]+)\}/', $c, $matches))
							{
								$smarty->assign('left_column_size', $matches[1]);
							}
							else
							{
								$smarty->assign('left_column_size', 3);
							}
						}

						if(Configuration::get('PS_THEMEPROVIDER_RIGHTCOLUMN'))
						{
							if(preg_match('/\{\$right_column_size=([0-9]+)\}/', $c, $matches))
							{
								$smarty->assign('right_column_size', $matches[1]);
							}
							else
							{
								$smarty->assign('right_column_size', 3);
							}
						}
					}
				}
				
				if(Configuration::get('PS_THEMEPROVIDER_LEFTCOLUMN'))
				{
					$controller->display_column_left = true;
				}
				if(Configuration::get('PS_THEMEPROVIDER_RIGHTCOLUMN'))
				{
					$controller->display_column_right = true;
				}
			}
			else
			{
				global $smarty;
				$_SERVER['PHP_SELF'] = _MODULE_DIR_.basename(dirname(__FILE__)).'/'.THEMEPROVIDER_FILENAME_PROVIDER;
			}
			$smarty->assign(array('page_name' => 'theme-provider'));

			if(version_compare(_PS_VERSION_, '1.6.0.0', '>=')) // FrontController::displayHeader() is buggy
			{
				$smarty->assign(array(
							'js_defer' => (bool)Configuration::get('PS_JS_DEFER'),
							'static_token' => Tools::getToken(false),
							'token' => Tools::getToken(),
							'priceDisplayPrecision' => _PS_PRICE_DISPLAY_PRECISION_,
							'content_only' => (int)Tools::getValue('content_only')
						));
				if(file_exists(_PS_THEME_DIR_.'global.tpl'))
				{
					$smarty->fetch(_PS_THEME_DIR_.'global.tpl'); // just process, do not send to browser
				}
			}
			
			if(defined('_PS_DISPLAY_COMPATIBILITY_WARNING_') && _PS_DISPLAY_COMPATIBILITY_WARNING_)
			{
				error_reporting(0);
			}

			if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
			{
				ob_start();
			}
			require_once(dirname(__FILE__).'/../../header.php');
			if(version_compare(_PS_VERSION_, '1.6.0.0', '>=')) // Controller::smartyOutputContent() is buggy
			{
				echo str_replace('</body></html>', '', ob_get_clean());
			}
			echo ThemeProvider::themeSeparate();
			require_once(dirname(__FILE__).'/../../footer.php');
			return ThemeProviderConnectorListener::shutdown();
		}
	}
	
	public static function cacheSettings()
	{
		if (!self::runTimeLoaded())
		{
			trigger_error(self::COMPONENT_NAME.': '.self::PLUGIN_NAME.' core is not loaded in '.__METHOD__, E_USER_ERROR);
			return false;
		}

		return parent::__validateSettings(
						array(
							'expire' => Configuration::get('PS_THEMEPROVIDER_CACHELIFE'),
							'compress' => Configuration::get('PS_THEMEPROVIDER_COMPRESSCACHE')
						)
			);
	}
	
	public static function cacheUser()
	{
		if (!self::runTimeLoaded())
		{
			trigger_error(self::COMPONENT_NAME.': '.self::PLUGIN_NAME.' core is not loaded in '.__METHOD__, E_USER_ERROR);
			return true;
		}
	
		if(!Configuration::get('PS_THEMEPROVIDER_NOUSERCACHE')) // cache everyone
		{
			return true;
		}
	
		// cache for users is disabled
		if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$user = Context::getContext()->cookie->logged;
		}
		else
		{
			global $cookie;
			$user = $cookie->logged;
		}
		if($user)
		{
			return false;
		}
		return true;
	}

	public static function runTimeLoaded()
	{
		return defined('_PS_VERSION_');
	}
	
	public static function validateApikey($apikey)
	{
		if (!self::runTimeLoaded())
		{
			trigger_error(self::COMPONENT_NAME.': '.self::PLUGIN_NAME.' core is not loaded in '.__METHOD__, E_USER_ERROR);
			return false;
		}
		return parent::__checkApikey(Configuration::get('PS_THEMEPROVIDER_APIKEY'), $apikey, Configuration::get('PS_THEMEPROVIDER_APIKEYREQ'));
	}

}

?>
