<?php
/**
  *  @author    Inveo s.r.o. <inqueries@inveoglobal.com>
  *  @copyright 2009-2015 Inveo s.r.o.
  *  @license   EULA
  */

if (!defined('_PS_VERSION_'))
	exit;

if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'ThemeProvider.php'))
{
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'ThemeProvider.php');
}
elseif(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'ThemeProviderFree.php'))
{
	define('THEMEPROVIDER_FREE', true);
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'ThemeProviderFree.php');
}

class Psthemeprovider extends Module
{
	private $_handledHooks = array(
					'cart',
					'actionCartSave',
					'backOfficeTop',
					'displayBackOfficeTop'
				);
	private $_confKeys = array(
					'PS_THEMEPROVIDER_CACHELIFE',
					'PS_THEMEPROVIDER_NOUSERCACHE',
					'PS_THEMEPROVIDER_APIKEY',
					'PS_THEMEPROVIDER_APIKEYREQ',
					'PS_THEMEPROVIDER_COMPRESSCACHE',
					'PS_THEMEPROVIDER_LEFTCOLUMN',
					'PS_THEMEPROVIDER_RIGHTCOLUMN'
				);
	
	private $_tpFile = '';

	public function __construct()
	{
		$this->name = 'psthemeprovider';
		$this->tab = 'others';
		$this->version = '1.6.00';
		$this->author = 'Inveo s.r.o.';
		$this->need_instance = 0;
		$this->displayName = $this->l('PrestaShop Theme Provider');
		$this->description = $this->l('Provides API to integrate a 3rd party app with PrestaShop.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete all settings?');
		if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
		{
			$this->bootstrap = true;
		}
		if(version_compare(_PS_VERSION_, '1.7.0.0', '>='))
		{
			$this->controllers = array('provider');
		}

		if(
			defined('THEMEPROVIDER_INIT') // eval vs. __FILE__
			&&
			defined('THEMEPROVIDER_PLUGIN_LOADED')
		)
			if(
				($cacheId = ThemeProviderPlugin::cacheIdSandboxed(Configuration::get('PS_THEMEPROVIDER_APIKEY')))
					&&
				($settings = ThemeProviderPlugin::cacheSettings())
			)
				ThemeProvider::initStatic(
							$cacheId,
							$settings['expire'],
							$settings['compress']
				);

		parent::__construct();
	}
	
	public function install()
	{
		if(version_compare(_PS_VERSION_, '1.3', '<') || version_compare(_PS_VERSION_, '1.8', '>='))
		{
			return $this->_returnError($this->displayName.' '.$this->version.' '.$this->l('supports only PrestaShop 1.3, 1.4, 1.5 and 1.6.'));
		}

		Configuration::updateValue('PS_THEMEPROVIDER_CACHELIFE', 300);
	
		if(!parent::install())
		{
			return false;
		}
	
		if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$cookie = $this->context->cookie;
			$hooksAr = array(
				'actionCartSave',
				'actionProductUpdate',
				'actionProductDelete',
				'displayBackOfficeTop'
			);
			$link = 'index.php?controller=AdminModules&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)Tab::getCurrentTabId().(int)$cookie->id_employee);
		}
		else
		{
			global $cookie;
			$hooksAr = array(
				'cart',
				'updateproduct',
				'deleteproduct',
				'backOfficeTop'
			);
			$link = 'index.php?tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)Tab::getCurrentTabId().(int)$cookie->id_employee);
		}

		foreach($hooksAr as $hook)
		{
			if(!$this->registerHook($hook))
			{
				return false;
			}
		}

		if(version_compare(_PS_VERSION_, '1.7.0.0', '<'))
		{
			Tools::redirectAdmin($link);
		}
		return true;
	}
	
	public function uninstall()
	{
		foreach($this->_confKeys as $key)
		{
			Configuration::deleteByName($key);
		}

		return parent::uninstall();
	}

	public function getContent()
	{
		@header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		@header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		@header('Cache-Control: no-store, no-cache, must-revalidate');
		@header('Cache-Control: post-check=0, pre-check=0', false);
		@header('Pragma: no-cache');
	
		if(defined('THEMEPROVIDER_LOADED') && THEMEPROVIDER_ADVANCE)
		{
			$this->displayName = $this->l('PrestaShop Theme Provider');
			$this->_tpFile = 'ThemeProvider.php';
		}
		else
		{
			$this->displayName = $this->l('PrestaShop Theme Provider Free');
			$this->_tpFile = 'ThemeProviderFree.php';
		}

		$html = '';
		
		if(version_compare(_PS_VERSION_, '1.6.0.0', '<'))
		{
			$html .= '<h2>'.$this->displayName.'</h2>';
		}
		
		if(version_compare(_PS_VERSION_, '1.4.0.0', '>='))
		{
			$shopHost = Tools::getShopDomain(false, true);
		}
		else
		{
			$shopHost = Tools::getHttpHost(false, true);
		}

		$actLink = '<a href="http://'.$shopHost.__PS_BASE_URI__.'modules/'.$this->name.'/'.$this->_tpFile.'?inveoActivation&amp;deliverBack='.urlencode($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'" style="text-decoration: underline !important">'.$this->l('Click here to activate').' &raquo;</a>';
		
		if(!defined('THEMEPROVIDER_LOADED'))
		{
			$html .= $this->displayError($this->displayName.' '.$this->l('core was not loaded.'));
		}
		elseif(!defined('THEMEPROVIDER_INIT'))
		{
			$html .= $this->displayError($this->displayName.' '.$this->l('is not activated.').' '.$actLink);
		}
		elseif(version_compare(_PS_VERSION_, '1.4.0.0', '>=') && Tools::getHttpHost(false, false) != Tools::getShopDomain(false, false))
		{
			$html .= $this->displayWarning($this->l('Please make sure').' '.$this->displayName.' '.$this->l('is activated').' '.$this->l('at').' '.$shopHost.': '.$actLink);
		}

		if(defined('THEMEPROVIDER_LOADED') && defined('THEMEPROVIDER_INIT'))
		{
			// initializing API key
			if(Configuration::get('PS_THEMEPROVIDER_APIKEY') === false) // we can use ThemeProviderTools class when TP is loaded
			{
				Configuration::updateValue('PS_THEMEPROVIDER_APIKEY', ThemeProviderTools::keyGen());
			}

			// upgrading setttings and hooks...
			if(Configuration::get('PS_THEMEPROVIDER_APIKEYREQ') === false)
			{
				Configuration::updateValue('PS_THEMEPROVIDER_APIKEYREQ', 0);
			}

			if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				$hookMaintenance = 'displayBackOfficeTop';
			}
			else
			{
				$hookMaintenance = 'backOfficeTop';
			}
			if(!$this->isRegisteredInHook($hookMaintenance))
			{
				$this->registerHook($hookMaintenance);
			}

			// give an offer
			if(!THEMEPROVIDER_ADVANCE)
			{
				$html .= $this->displayConfirmation($this->l('Give your customers even better experience and').' <a href="http://www.inveostore.com/theme-providers-advanced" style="text-decoration: underline !important">'.$this->l('upgrade to the paid PrestaShop Theme Provider module').' &raquo;</a>');
			}

			$columKeysAr = array('PS_THEMEPROVIDER_LEFTCOLUMN', 'PS_THEMEPROVIDER_RIGHTCOLUMN');
			/* Save API settings */
			if(Tools::isSubmit('submitPsthemeproviderApiSettings'))
			{
				if(THEMEPROVIDER_ADVANCE)
				{
					$columKeysAr = array('PS_THEMEPROVIDER_LEFTCOLUMN', 'PS_THEMEPROVIDER_RIGHTCOLUMN');
					foreach($columKeysAr as $columKey)
					{
						if(Configuration::get($columKey) != Tools::getValue($columKey))
						{
							ThemeProvider::cacheReset();
							break;
						}
					}

					foreach($columKeysAr as $c)
					{
						Configuration::updateValue($c, Tools::getValue($c));
					}
				}
				Configuration::updateValue('PS_THEMEPROVIDER_APIKEYREQ', Tools::getValue('PS_THEMEPROVIDER_APIKEYREQ'));
				$html .= $this->displayConfirmation($this->l('API settings updated'));
			}
			
			/* Save settings */
			if(Tools::isSubmit('submitPsthemeproviderSettings'))
			{
				foreach($this->_confKeys as $c)
				{
					if($c != 'PS_THEMEPROVIDER_APIKEY' && !in_array($c, $columKeysAr))
					{
						Configuration::updateValue($c, Tools::getValue($c));
					}
				}
				$html .= $this->displayConfirmation($this->l('Settings updated'));
			}
			
			/* Run clean-up */
			if(Tools::isSubmit('submitPsthemeproviderMaintenance'))
			{
				if(Tools::getValue('PS_THEMEPROVIDER_CLEANCACHE'))
				{
					$nbFiles = ThemeProvider::cacheClean();
					$html .= $this->displayConfirmation($this->l('Cache was cleaned ('.$nbFiles.' files deleted)'));
				}
				if(Tools::getValue('PS_THEMEPROVIDER_RESETCACHE'))
				{
					$nbFiles = ThemeProvider::cacheReset();
					$html .= $this->displayConfirmation($this->l('Cache was reset ('.$nbFiles.' files deleted)'));
				}
			}
			
			/* Save settings */
			if(Tools::isSubmit('submitPsthemeproviderAdvancedSettings'))
			{
				$hooks = $this->_getHooks();
					foreach($hooks as $hook)
					{
						if(Tools::getIsset('PS_THEMEPROVIDER_CLEANONHOOKS_'.$hook['id_hook']))
						{
							if(!$this->isRegisteredInHook($hook['name']))
								$this->registerHook($hook['name']);
						}
						else
						{
							$this->unregisterHook($hook['id_hook']);
						}
					}
				$html .= $this->displayConfirmation($this->l('Advanced settings updated'));
			}

			if(!is_writable(THEMEPROVIDER_CACHE))
			{
				$html .= $this->displayError($this->l('Cache directory is not writable.'));
			}

			$cleanPeriod = array(
				array('id' => 0, 'name' => $this->l('No cache (slow)')),
				array('id' => (60 * 1), 'name' => $this->l('1 minutes')),
				array('id' => (60 * 2), 'name' => $this->l('2 minutes')),
				array('id' => (60 * 3), 'name' => $this->l('3 minutes')),
				array('id' => (60 * 4), 'name' => $this->l('4 minutes')),
				array('id' => (60 * 5), 'name' => $this->l('5 minutes (default)')),
				array('id' => (60 * 6), 'name' => $this->l('6 minutes')),
				array('id' => (60 * 7), 'name' => $this->l('7 minutes')),
				array('id' => (60 * 8), 'name' => $this->l('8 minutes')),
				array('id' => (60 * 9), 'name' => $this->l('9 minutes')),
				array('id' => (60 * 10), 'name' => $this->l('10 minutes')),
				array('id' => (3600 / 4), 'name' => $this->l('15 minutes')),
				array('id' => (3600 / 2), 'name' => $this->l('30 minutes')),
				array('id' => 3600, 'name' => $this->l('1 hour')),
				array('id' => (3600 * 2), 'name' => $this->l('2 hours')),
				array('id' => (3600 * 4), 'name' => $this->l('4 hours')),
				array('id' => (3600 * 5), 'name' => $this->l('5 hours')),
				array('id' => (3600 * 6), 'name' => $this->l('6 hours'))
			);

			$hooks = $this->_getHooks();
			$hooksOptions = array();
			foreach($hooks as $hook)
			{
				$hooksOptions[] = array('id_hook' => $hook['id_hook'], 'name' => $hook['name'].(!empty($hook['description']) ? ' ('.$hook['description'].')' : '' ));
			}

			if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
			{
				$helper = new HelperForm();
				$helper->show_toolbar = false;
				$helper->table =  $this->table;
				$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
				$helper->default_form_language = $lang->id;
				$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
				$helper->identifier = $this->identifier;
				$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
				$helper->token = Tools::getAdminTokenLite('AdminModules');
				$helper->tpl_vars = array(
					'fields_value' => $this->getConfigFieldsValues(),
					'languages' => $this->context->controller->getLanguages(),
					'id_language' => $this->context->language->id
				);

				$fields_form_api = array(
					'form' => array(
						'legend' => array(
							'title' => $this->displayName.' '.$this->l('API'),
							'icon' => 'icon-cogs'
						),
						'input' => array(
							array(
								'type' => 'text',
								'label' => $this->l('Security key:'),
								'name' => 'PS_THEMEPROVIDER_APIKEY',
								'desc' => stripslashes($this->l('(please enter this API security key in connector\'s settings)')).' | <a href="http://www.inveostore.com/theme-connectors">'.$this->l('Download the Theme Connector').'</a> &raquo;',
								'readonly' => true
							),
							array(
								'type' => 'switch',
								'label' => $this->l('Protect API:'),
								'name' => 'PS_THEMEPROVIDER_APIKEYREQ',
								'values' => array(
											array(
												'id' => 'active_on',
												'value' => 1,
												'label' => $this->l('Yes')
											),
											array(
												'id' => 'active_off',
												'value' => 0,
												'label' => $this->l('No')
											)
										),
								'desc' => '('.$this->l('require security key').')'
							)
						),
						'submit' => array(
								'title' => $this->l('Save')
							)
					)
				);
				if(version_compare(_PS_VERSION_, '1.7.0.0', '<'))
				{
					$fields_form_api['form']['input'][] = array(
									'type' => 'switch',
									'label' => $this->l('Display left column:'),
									'name' => 'PS_THEMEPROVIDER_LEFTCOLUMN',
									'values' => array(
												array(
													'id' => 'active_on',
													'value' => 1,
													'label' => $this->l('Yes')
												),
												array(
													'id' => 'active_off',
													'value' => 0,
													'label' => $this->l('No')
												)
											),
									'desc' => '('.$this->l('displays left column').((!THEMEPROVIDER_ADVANCE) ? ' '.$this->l('- upgrade of the module is required') : '').')',
									'disabled' => ((!THEMEPROVIDER_ADVANCE) ? true : false)
								);
					$fields_form_api['form']['input'][] = array(
									'type' => 'switch',
									'label' => $this->l('Display right column:'),
									'name' => 'PS_THEMEPROVIDER_RIGHTCOLUMN',
									'values' => array(
												array(
													'id' => 'active_on',
													'value' => 1,
													'label' => $this->l('Yes')
												),
												array(
													'id' => 'active_off',
													'value' => 0,
													'label' => $this->l('No')
												)
											),
									'desc' => '('.$this->l('displays right column').((!THEMEPROVIDER_ADVANCE) ? ' '.$this->l('- upgrade of the module is required') : '').')',
									'disabled' => ((!THEMEPROVIDER_ADVANCE) ? true : false)
								);
				}

				// api key
				$helper->submit_action = 'submitPsthemeproviderApiSettings';
				$html .= $helper->generateForm(array($fields_form_api));

				$fields_form_settings = array(
					'form' => array(
						'legend' => array(
							'title' => $this->l('Cache settings'),
							'icon' => 'icon-cogs'
						),
						'input' => array(
							array(
								'type' => 'select',
								'label' => $this->l('Lifetime:'),
								'name' => 'PS_THEMEPROVIDER_CACHELIFE',
								'options' => array(
										'query' => $cleanPeriod,
										'id' => 'id',
										'name' => 'name'
									),
								'desc' => ((!THEMEPROVIDER_ADVANCE) ? ' ('.$this->l('upgrade of the module is required').')' : ''),
								'disabled' => !THEMEPROVIDER_ADVANCE
							),
							array(
								'type' => 'switch',
								'label' => $this->l('Compress:'),
								'name' => 'PS_THEMEPROVIDER_COMPRESSCACHE',
								'values' => array(
											array(
												'id' => 'active_on',
												'value' => 1,
												'label' => $this->l('Yes')
											),
											array(
												'id' => 'active_off',
												'value' => 0,
												'label' => $this->l('No')
											)
										),
								'desc' => '('.$this->l('use gzip compression for files').((!THEMEPROVIDER_ADVANCE) ? ' '.$this->l('- upgrade of the module is required') : '').')',
								'disabled' => !THEMEPROVIDER_ADVANCE
							),
							array(
								'type' => 'switch',
								'label' => $this->l('Exclude users:'),
								'name' => 'PS_THEMEPROVIDER_NOUSERCACHE',
								'values' => array(
											array(
												'id' => 'active_on',
												'value' => 1,
												'label' => $this->l('Yes')
											),
											array(
												'id' => 'active_off',
												'value' => 0,
												'label' => $this->l('No')
											)
										),
								'desc' => '('.$this->l('use cache only for visitors').((!THEMEPROVIDER_ADVANCE) ? ' '.$this->l('- upgrade of the module is required') : '').')',
								'disabled' => !THEMEPROVIDER_ADVANCE
							)
						),
						'submit' => array(
								'title' => $this->l('Save')
							)
					)
				);
				
				// settings
				$helper->submit_action = 'submitPsthemeproviderSettings';
				$html .= $helper->generateForm(array($fields_form_settings));

				$fields_form_maintenance = array(
					'form' => array(
						'legend' => array(
							'title' => $this->l('Cache maintenance'),
							'icon' => 'icon-cogs'
						),
						'input' => array(
							array(
								'type' => 'switch',
								'label' => $this->l('Clean:'),
								'name' => 'PS_THEMEPROVIDER_CLEANCACHE',
								'values' => array(
											array(
												'id' => 'active_on',
												'value' => 1,
												'label' => $this->l('Enabled')
											),
											array(
												'id' => 'active_off',
												'value' => 0,
												'label' => $this->l('Disabled')
											)
										),
								'desc' => '('.((!THEMEPROVIDER_ADVANCE) ? $this->l('upgrade of the module is required') : ThemeProvider::cacheStatsExpired().' '.$this->l('expired records in total')).')',
								'disabled' => !THEMEPROVIDER_ADVANCE
							),
							array(
								'type' => 'switch',
								'label' => $this->l('Reset:'),
								'name' => 'PS_THEMEPROVIDER_RESETCACHE',
								'values' => array(
											array(
												'id' => 'active_on',
												'value' => 1,
												'label' => $this->l('Enabled')
											),
											array(
												'id' => 'active_off',
												'value' => 0,
												'label' => $this->l('Disabled')
											)
										),
								'desc' => '('.((!THEMEPROVIDER_ADVANCE) ? $this->l('upgrade of the module is required') : ThemeProvider::cacheStatsTotal().' '.$this->l('records in total')).')',
								'disabled' => !THEMEPROVIDER_ADVANCE
							)
						),
						'submit' => array(
								'title' => $this->l('Apply')
							)
					)
				);
				
				// maintenance
				$helper->submit_action = 'submitPsthemeproviderMaintenance';
				$html .= $helper->generateForm(array($fields_form_maintenance));
				
				if(THEMEPROVIDER_ADVANCE)
				{
					$fields_form_adv_settings = array(
						'form' => array(
							'legend' => array(
								'title' => $this->l('Advanced settings'),
								'icon' => 'icon-cogs'
							),
							'input' => array(
								array(
									'type' => 'checkbox',
									'label' => $this->l('Reset cache on hooks:'),
									'name' => 'PS_THEMEPROVIDER_CLEANONHOOKS',
									'values' => array(
										'query' => $hooksOptions,
										'id' => 'id_hook',
										'name' => 'name'
									),
									'hint' => $this->l('Here you can force the cache resetting on certain hooks. We recommend that only developers make changes.').'<br />'.$this->l('Note: Cart hooks never require resetting of cache and are excluded from the list. Custom hooks (manually added to the PrestaShop) are supported and appear here automatically.')
								)
							),
							'submit' => array(
									'title' => $this->l('Save')
								)
						)
					);
					
					// advanced settings
					$helper->submit_action = 'submitPsthemeproviderAdvancedSettings';
					$html .= $helper->generateForm(array($fields_form_adv_settings));
				}
			}
			else
			{
				$conf = $this->getConfigFieldsValues();
				$html =
					'<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">'.
						'<fieldset><legend><img src="../img/admin/prefs.gif" />'.$this->displayName.' '.$this->l('API').'</legend>'.
						'<label for="apikey" class="clear pointer">'.$this->l('Security key:').'</label>'.
						'<div class="margin-form">'.
						'<input type="text" name="PS_THEMEPROVIDER_APIKEY" id="apikey" value="'.$conf['PS_THEMEPROVIDER_APIKEY'].'" size="25" readonly /> '.$this->l('(please enter this API security key in connector\'s settings)').' | <a href="http://www.inveostore.com/theme-connectors">'.$this->l('Download the Theme Connector').'</a> &raquo;'.
						'</div>'.
						'<label for="apikey-required" class="clear pointer">'.$this->l('Protect API').':</label>'.
						'<div class="margin-form" style="padding-top: 5px;">'.
						'<input type="checkbox" style="vertical-align: middle;" name="PS_THEMEPROVIDER_APIKEYREQ" id="apikey-required" value="1" '.(Configuration::get('PS_THEMEPROVIDER_APIKEYREQ') ? 'checked="checked"' : '').' />'.
						'&nbsp;('.$this->l('require security key').')'.
						'</div>';

				if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				{
					$html .=
						'<label for="left-column" class="clear pointer">'.$this->l('Display left column').':</label>'.
						'<div class="margin-form" style="padding-top: 5px;">'.
						'<input type="checkbox" style="vertical-align: middle;" name="PS_THEMEPROVIDER_LEFTCOLUMN" id="left-column" value="1" '.((!THEMEPROVIDER_ADVANCE) ? 'disabled="disabled"' : '').' '.(Configuration::get('PS_THEMEPROVIDER_LEFTCOLUMN') ? 'checked="checked"' : '').' />'.
						'&nbsp;('.$this->l('displays left column').((!THEMEPROVIDER_ADVANCE) ? ' '.$this->l('- upgrade of the module is required') : '').')'.
						'</div>'.
						'<label for="right-column" class="clear pointer">'.$this->l('Display right column').':</label>'.
						'<div class="margin-form" style="padding-top: 5px;">'.
						'<input type="checkbox" style="vertical-align: middle;" name="PS_THEMEPROVIDER_RIGHTCOLUMN" id="right-column" value="1" '.((!THEMEPROVIDER_ADVANCE) ? 'disabled="disabled"' : '').' '.(Configuration::get('PS_THEMEPROVIDER_RIGHTCOLUMN') ? 'checked="checked"' : '').' />'.
						'&nbsp;('.$this->l('displays right column').((!THEMEPROVIDER_ADVANCE) ? ' '.$this->l('- upgrade of the module is required') : '').')'.
						'</div>';
				}

				$html .=
						'<p class="center"><input type="submit" value="   '.$this->l('Save').'   " name="submitPsthemeproviderApiSettings" class="button" /></p>'.
						'</fieldset>'.
						'<div class="clear"></div>'.
					'</form>';

				$html .= 
					'<br />'.
					'<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">'.
					'<fieldset><legend><img src="../img/admin/statsettings.gif" />'.$this->l('Cache settings').'</legend>'.
					'<label for="carts-period" class="clear pointer">'.$this->l('Lifetime:').'</label>'.
					'<div class="margin-form">'.
						'<select id="carts-period" class="pointer" name="PS_THEMEPROVIDER_CACHELIFE" '.(!THEMEPROVIDER_ADVANCE ? ' disabled="disabled"' : '').'>';
							foreach ($cleanPeriod as $k => $value)
							{
								$html .= '<option value="'.$value['id'].'"'.($value['id'] == $conf['PS_THEMEPROVIDER_CACHELIFE'] ? ' selected="selected"' : '').'>'.$value['name'].'&nbsp;</option>';
							}
						$html .= '</select>'.
					((!THEMEPROVIDER_ADVANCE) ? ' ('.$this->l('upgrade of the module is required').')' : '').
					'</div>'.
					'<label for="compress-cache" class="clear pointer">'.$this->l('Compress').':</label>'.
					'<div class="margin-form" style="padding-top: 5px;">'.
					'<input type="checkbox" style="vertical-align: middle;" name="PS_THEMEPROVIDER_COMPRESSCACHE" id="compress-cache" value="1" '.(Configuration::get('PS_THEMEPROVIDER_COMPRESSCACHE') ? 'checked="checked"' : '').' '.(!THEMEPROVIDER_ADVANCE ? ' disabled="disabled"' : '').' />'.
					'&nbsp;('.$this->l('use gzip compression for files').((!THEMEPROVIDER_ADVANCE) ? ' '.$this->l('- upgrade of the module is required') : '').')'.
					'</div>'.
					'<label for="visitors-cache" class="clear pointer">'.$this->l('Exclude users').':</label>'.
					'<div class="margin-form" style="padding-top: 5px;">'.
					'<input type="checkbox" style="vertical-align: middle;" name="PS_THEMEPROVIDER_NOUSERCACHE" id="visitors-cache" value="1" '.(Configuration::get('PS_THEMEPROVIDER_NOUSERCACHE') ? 'checked="checked"' : '').' '.(!THEMEPROVIDER_ADVANCE ? ' disabled="disabled"' : '').' />'.
					'&nbsp;('.$this->l('use cache only for visitors').((!THEMEPROVIDER_ADVANCE) ? ' '.$this->l('- upgrade of the module is required') : '').')'.
					'</div>'.
					'<p class="center"><input type="submit" value="   '.$this->l('Save').'   " name="submitPsthemeproviderSettings" class="button" /></p>'.
					'</fieldset>'.
					'<div class="clear"></div>'.
					'</form>';

				$html .=
					'<br /><form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">'.
					'<fieldset><legend><img src="../img/admin/database_gear.gif" />'.$this->l('Cache maintenance').'</legend>'.
					'<label for="clear-cache" class="clear pointer">'.$this->l('Clean').':</label>'.
					'<div class="margin-form" style="padding-top: 5px;">'.
					'<input type="checkbox" style="vertical-align: middle;" name="PS_THEMEPROVIDER_CLEANCACHE" id="clear-cache" value="1" '.(!THEMEPROVIDER_ADVANCE ? ' disabled="disabled"' : '').' />'.
					'&nbsp;('.((!THEMEPROVIDER_ADVANCE) ? $this->l('upgrade of the module is required') : ThemeProvider::cacheStatsExpired().' '.$this->l('expired records in total')).')'.
					'</div>'.
					'<label for="reset-cache" class="clear pointer">'.$this->l('Reset').':</label>'.
					'<div class="margin-form" style="padding-top: 5px;">'.
					'<input type="checkbox" style="vertical-align: middle;" name="PS_THEMEPROVIDER_RESETCACHE" id="reset-cache" value="1" '.(!THEMEPROVIDER_ADVANCE ? ' disabled="disabled"' : '').' />'.
					'&nbsp;('.((!THEMEPROVIDER_ADVANCE) ? $this->l('upgrade of the module is required') : ThemeProvider::cacheStatsTotal().' '.$this->l('records in total')).')'.
					'</div>'.
					'<p class="center"><input type="submit" value="   '.$this->l('Apply').'   " name="submitPsthemeproviderMaintenance" class="button" /></p>'.
					'</fieldset>'.
					'<div class="clear"></div>'.
				'</form>';
				if(THEMEPROVIDER_ADVANCE)
				{
					$html .=
						'<br /><form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">'.
						'<fieldset><legend><img src="../img/admin/exchangesrate.gif" />'.$this->l('Advanced settings').'</legend>'.
						'<span>'.$this->l('Reset cache on hooks:').'</span><br /><br />'.
						'<span style="font-size: .8em">'.
						$this->l('Here you can force the cache resetting on certain hooks. We recommend that only developers make changes.').'<br />'.$this->l('Note: Cart hooks never require resetting of cache and are excluded from the list. Custom hooks (manually added to the PrestaShop) are supported and appear here automatically.').
						'</span>'.
						'<br /><br />'.
						'<table cellspacing="0" cellpadding="0" class="table" style="width: 98%">'.
							'<thead>'.
								'<tr>'.
									'<th></th>'.
									'<th>'.$this->l('Name').'</th>'.
									'<th>'.$this->l('Description').'</th>'.
								'</tr>'.
							'</thead>'.
						'<tbody>';
					foreach($hooks as $hook)
					{
						$html .= '<tr><td><input type="checkbox" style="vertical-align: middle;" class="noborder" value="1" name="PS_THEMEPROVIDER_CLEANONHOOKS_'.$hook['id_hook'].'" id="checkbox_'.$hook['id_hook'].'" '.((array_key_exists('PS_THEMEPROVIDER_CLEANONHOOKS_'.$hook['id_hook'], $conf) && $conf['PS_THEMEPROVIDER_CLEANONHOOKS_'.$hook['id_hook']] == 1) ? 'checked="checked"' : '').' /></td><td><label for="checkbox_'.$hook['id_hook'].'" class="pointer" style="text-align: left">'.$hook['name'].'</label></td><td>'.$hook['description'].'</td></tr>';
					}
					$html .=
						'</tbody>'.
						'</table>'.
						'<div class="clear"></div>'.
						'<p class="center"><input type="submit" value="   '.$this->l('Save').'   " name="submitPsthemeproviderAdvancedSettings" class="button" /></p>'.
						'</fieldset>'.
					'</form>';
				}
			}
		}
	
		if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
		{
			$this->context->controller->addCSS($this->_path.'css/inveocopyright.css');
			$this->smarty->assign(array(
						'mod_name' => $this->displayName,
						'mod_ver' => $this->version,
						'mod_copy_date' => date('Y')
						)
					);
			$html .= $this->display(__FILE__, 'views/templates/admin/intro.tpl');
		}
		else
		{
			$html .=
				'<div class="clear"></div>'.
				'<div style="text-align: right; margin-top: 1em">'.
				$this->displayName.' '.$this->version.'<br /><br />'.
				'copyright &copy; 2012-'.date('Y').' Inveo<br />'.$this->l('PrestaShop Modules:').' <a href="http://www.inveostore.com" style="color:blue;text-decoration:underline">www.inveostore.com</a> | '.$this->l('eCommerce Services:').' <a href="http://www.inveo.us" style="color:blue;text-decoration:underline">www.inveo.us</a>'.
				'</div>';
		}
		return $html;
	}

	public function hookCart($params)
	{
		if(isset($params['cart']) && is_object($params['cart']) && (int)$params['cart']->id)
		{
			$shop = 1;
			if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				$shop = $this->context->shop->id;
			}
			ThemeProvider::cacheCleanTree(
								array(
									$shop,
									(int)$params['cart']->id_customer,
									(int)$params['cart']->id
								)
							);
		}
	}
	public function hookActionCartSave($params)
	{
		return $this->hookCart($params);
	}
	
	public function hookBackOfficeTop($params)
	{
		if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$cookie = $this->context->cookie;
			$link = 'index.php?controller=AdminMaintenance&amp;token='.Tools::getAdminToken('AdminMaintenance'.(int)Tab::getIdFromClassName('AdminMaintenance').(int)$cookie->id_employee);
		}
		else
		{
			global $cookie;
			$link = 'index.php?tab=AdminPreferences&amp;token='.Tools::getAdminToken('AdminPreferences'.(int)Tab::getIdFromClassName('AdminPreferences').(int)$cookie->id_employee);
		}
		if(!Configuration::get('PS_SHOP_ENABLE'))
		{
			return
				'<div style="color: #000; background-color: #FF0033; border: 1px solid #fff; padding: 5px; width: 100%; position: fixed; left: 0px !important; bottom: 0px !important; z-index: 99 !important">'.
				'<img src="../img/admin/warning.gif" alt="" title="">&nbsp;'.$this->l('PrestaShop Theme Provider module may not work correctly when shop maintenance mode is enabled.').
				' <a href="'.$link.'" style="color: #000; text-decoration: underline !important">'.$this->l('Enable shop').' &raquo;</a></div>';
		}
	}
	
	public function hookDisplayBackOfficeTop($params)
	{
		return $this->hookBackOfficeTop($params);
	}

	private function _getHooks()
	{
		$hooks = Hook::getHooks();
		$hooksOptions = array();
		foreach($hooks as $k => $v)
		{
			if(!in_array($v['name'], $this->_handledHooks))
			{
				$hooksOptions[] = $hooks[$k];
			}
		}
		return $hooksOptions;
	}

	public function __call($name, $args)
	{
		if(strpos($name, 'hook') === 0)
		{
			ThemeProvider::cacheReset();
		}
	}
	
	private function _returnError($msg)
	{
		if(version_compare(_PS_VERSION_, '1.5', '>='))
		{
			$this->_errors[] = $msg;
		}
		else
		{
			echo $this->displayError($msg);
		}
	
		return false;
	}

	public function getConfigFieldsValues()
	{
		$fields = array();
		$fields = Configuration::getMultiple($this->_confKeys);
		$fields['PS_THEMEPROVIDER_RESETCACHE'] = $fields['PS_THEMEPROVIDER_CLEANCACHE'] = '0';
		if(!THEMEPROVIDER_ADVANCE)
		{
			$fields['PS_THEMEPROVIDER_CACHELIFE'] = 0;
		}

		$hooks = $this->_getHooks();
			foreach($hooks as $hook)
			{
				$fields['PS_THEMEPROVIDER_CLEANONHOOKS_'.$hook['id_hook']] = '0';
				if($this->isRegisteredInHook($hook['name']))
				{
					$fields['PS_THEMEPROVIDER_CLEANONHOOKS_'.$hook['id_hook']] = '1';
				}
			}
		return $fields;
	}
}

?>
