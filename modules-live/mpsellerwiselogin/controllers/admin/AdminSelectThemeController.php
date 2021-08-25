<?php
/**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminSelectThemeController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'marketplace_login_theme';
        $this->className = 'LoginTheme';

        parent::__construct();
    }

    public function initContent()
    {
        $this->initTabModuleList();
        $this->initToolbar();
        $this->initPageHeaderToolbar();

        $this->content = $this->renderForm();
        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
        ));
    }

    public function renderForm()
    {
        $this->initFirstForm();

        return parent::renderForm();
    }

    public function initFirstForm()
    {
        $smartyVars = array();
        $objTheme = new LoginTheme();
        $allTheme = $objTheme->getAllThemes();
        $active_theme = $objTheme->getActiveTheme();
        $smartyVars['all_theme'] = $allTheme;
        $active_theme_id = $active_theme['id'];

        $preview_img_dir = _MODULE_DIR_.$this->module->name.'/views/img/theme_preview/';
        $smartyVars['preview_img_dir'] = $preview_img_dir;

        $prevImg = $preview_img_dir.'theme'.$active_theme_id.'.jpg';
        $smartyVars['prev_img'] = $prevImg;
        $this->context->smarty->assign($smartyVars);

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Select Theme'),
                'icon' => 'icon-cogs',
            ),
        );

        return true;
    }

    public function processSave()
    {
        if (Tools::isSubmit('submit_login_theme')) {
            $this->saveTheme();
            Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
        }

        if (Tools::isSubmit('edit_login_theme')) {
            $this->saveTheme();
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminCustomizeLogin'));
        }
    }

    public function saveTheme()
    {
        $objTheme = new LoginTheme();
        $activeTheme = $objTheme->getActiveTheme();
        $objTheme = new LoginTheme($activeTheme['id']);
        $objTheme->active = 0;
        $objTheme->save();

        $idTheme = Tools::getValue('login_theme');
        $objTheme = new LoginTheme($idTheme);
        $objTheme->active = 1;
        $objTheme->save();

        return $objTheme->id;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();
        $this->addJS(_PS_MODULE_DIR_.$this->module->name.'/views/js/admin_sellerlogin.js');
    }
}
