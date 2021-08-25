<?php
/**
 *  Leo Theme for Prestashop 1.6.x
 *
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
 */

class ApPageBuilderApPagebuilderHomeModuleFrontController extends FrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->display_column_left  = false;
        $this->display_column_right = false;
    }

    public function initContent()
    {
        parent::initContent();
        $this->addJS(_THEME_JS_DIR_.'index.js');

        $this->context->smarty->assign(array(
            'HOOK_HOME' => Hook::exec('displayHome'),
            'HOOK_HOME_TAB' => Hook::exec('displayHomeTab'),
            'HOOK_HOME_TAB_CONTENT' => Hook::exec('displayHomeTabContent')
        ));
        $this->display_column_left = false;
        $this->display_column_right = false;
        $this->context->smarty->assign(array(
            'page_name'           => 'index',
        ));
        $this->setTemplate('index.tpl');
    }

    /**
     * set html <body id="index"
     */
    public function getPageName()
    {
        $page_name = 'index';
        return $page_name;
    }
    
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        unset($page['body_classes']['page-']);
        $page['body_classes']['page-index'] = true;
        return $page;
    }
}
