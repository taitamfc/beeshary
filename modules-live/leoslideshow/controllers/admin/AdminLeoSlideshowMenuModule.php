<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Adds image, text or video to your homepage.
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

class AdminLeoSlideshowMenuModuleController extends ModuleAdminControllerCore
{

    public function __construct()
    {
        parent::__construct();
        if (Configuration::get('LEOSLIDESHOW_GROUP_DE') && Configuration::get('LEOSLIDESHOW_GROUP_DE') != '') {
            $url = 'index.php?controller=adminmodules&configure=leoslideshow&editgroup=1&id_group='.Configuration::get('LEOSLIDESHOW_GROUP_DE').'&tab_module=front_office_features&module_name=leoslideshow&token='.Tools::getAdminTokenLite('AdminModules');
        } else {
            $url = 'index.php?controller=adminmodules&configure=leoslideshow&tab_module=front_office_features&module_name=leoslideshow&token='.Tools::getAdminTokenLite('AdminModules');
        }
        Tools::redirectAdmin($url);
    }
}
