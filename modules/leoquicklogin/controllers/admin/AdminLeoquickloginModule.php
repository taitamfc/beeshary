<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo Quick Login And Social Login
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

class AdminLeoQuickLoginModuleController extends ModuleAdminControllerCore
{

    public function __construct()
    {
        parent::__construct();

        $url = 'index.php?controller=AdminModules&configure=leoquicklogin&token=' . Tools::getAdminTokenLite('AdminModules');
        Tools::redirectAdmin($url);
    }
}
