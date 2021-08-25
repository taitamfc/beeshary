<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Quick product search by category block
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

class AdminLeoProductSearchModuleController extends ModuleAdminControllerCore
{

    public function __construct()
    {
        parent::__construct();

        $url = 'index.php?controller=AdminModules&configure=leoproductsearch&token=' . Tools::getAdminTokenLite('AdminModules');
        Tools::redirectAdmin($url);
    }
}
