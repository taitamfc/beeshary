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

class LeobootstrapmenuWidgetModuleFrontController extends ModuleFrontController
{
    public $php_self;

    public function init()
    {
        parent::init();

        require_once($this->module->getLocalPath().'leobootstrapmenu.php');
    }

    public function initContent()
    {
        $this->php_self = 'widget';
        parent::initContent();

        $module = new leobootstrapmenu();

        echo $module->renderLeoWidget();
        die;
    }
}
