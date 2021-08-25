<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo Bootstrap Menu
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class AdminLeoBootstrapMenuModuleController extends ModuleAdminControllerCore
{

    public function __construct()
    {
        parent::__construct();
        if (!Tools::getValue('exportgroup') && !Tools::getValue('exportwidgets')) {
            if (Configuration::get('BTMEGAMENU_GROUP_DE') && Configuration::get('BTMEGAMENU_GROUP_DE') != '') {
                $url = 'index.php?controller=AdminModules&configure=leobootstrapmenu&editgroup=1&id_group='.Configuration::get('BTMEGAMENU_GROUP_DE').'&tab_module=front_office_features&module_name=leobootstrapmenu&token='.Tools::getAdminTokenLite('AdminModules');
            } else {
                $url = 'index.php?controller=AdminModules&configure=leobootstrapmenu&tab_module=front_office_features&module_name=leobootstrapmenu&token='.Tools::getAdminTokenLite('AdminModules');
            }
            Tools::redirectAdmin($url);
        }
    }
    
    public function postProcess()
    {
        /**
         * Task permission
         * MOVE 2 export to getContent
         */
        parent::postProcess();
    }
}
