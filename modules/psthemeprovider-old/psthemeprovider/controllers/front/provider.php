<?php
/*
* 2007-2015 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class PsthemeproviderproviderModuleFrontController extends ModuleFrontController
{
	public $theme_separator = '';
    
	/*public function __construct()
	{
		$this->module = Module::getInstanceByName(Tools::getValue('module'));
		if (!$this->module->active)
		{
			Tools::redirect('index');
		}

		$this->page_name = 'module-psthemeprovider-provider';

		FrontController::__construct();

		$this->controller_type = 'modulefront';
    }*/

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign('theme_separator', $this->theme_separator);
        $this->setTemplate('module:psthemeprovider/views/templates/front/provider.tpl');
    }
}
