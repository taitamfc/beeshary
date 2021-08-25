<?php
/**
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once 'classes/SearchBlockHelperClass.php';
class MpSearchBlock extends Module
{
    public function __construct()
    {
        $this->name = 'mpsearchblock';
        $this->author = 'Webkul';
        $this->tab = 'front_office_features';
        $this->version = '5.0.2';
        $this->context = Context::getContext();
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => '1.7');

        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->dependencies = array('marketplace');

        parent::__construct();

        $this->displayName = $this->l('Marketplace Search Block');
        $this->description = $this->l('User can now search, based on the different categories. The search has been
        enhanced as now the user can search by Products, Product Categories, Seller Shops, Sellers Name or the shop by
        location');
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !$this->enableByName('ps_searchbar')) {            //enable prestashop default 'blocksearch' module
            return false;
        }

        return true;
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('top') ||
            !$this->registerHook('header') ||
            !$this->registerHook('actionFrontControllerSetMedia') ||
            !$this->disableByName('ps_searchbar') ||
            !$this->setPosition() ||
            !Configuration::updateValue('PS_SEARCH_MINWORDLEN', 1)
            ) {
            return false;
        }

        return true;
    }

    /**
     * [hookHeader add css and js file used in this modules ].
     */
    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'module-mpsearchblock-style',
            'modules/'.$this->name.'/views/css/mpsearchblock.css'
        );

        $this->context->controller->registerJavascript(
            'module-mpsearchblock-js',
            'modules/'.$this->name.'/views/js/mpsearchblock.js'
        );

        $this->context->controller->addJqueryUI('ui.autocomplete');

        $jsVars = [
                'ajaxsearch_url' => $this->context->link->getModuleLink('mpsearchblock', 'ajaxsearch'),
                'more' => $this->trans('More.', [], 'Modules.MpSearchBlock'),
                'hide' => $this->trans('Hide.', [], 'Modules.MpSearchBlock'),
            ];

        Media::addJsDef($jsVars);
    }

    /**
     * [hookTop : display search box in top and send controller link].
     */
    public function hookTop()
    {
        return $this->fetch('module:'.$this->name.'/views/templates/hook/mpsearchblock.tpl');
    }

    /**
     * [setPosition set module(mpsearchblock) position to 2].
     */
    public function setPosition()
    {
        $id_hook = Hook::getIdByName('displayTop');
        $update_position = $this->updatePosition($id_hook, 0, 2);

        if ($update_position) {
            return true;
        }

        return false;
    }
}
