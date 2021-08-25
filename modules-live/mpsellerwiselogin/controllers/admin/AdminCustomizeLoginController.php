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

class AdminCustomizeLoginController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'marketplace_login_content';
        $this->className = 'LoginContent';
        parent::__construct();

        $objTheme = new LoginTheme();
        $activeTheme = $objTheme->getActiveTheme();
        $this->id_theme = $activeTheme['id'];

        $this->width = [
            ['id_value' => '1', 'name' => $this->l('1/12 Of Parent Width')],
            ['id_value' => '2', 'name' => $this->l('2/12 Of Parent Width')],
            ['id_value' => '3', 'name' => $this->l('3/12 Of Parent Width')],
            ['id_value' => '4', 'name' => $this->l('4/12 Of Parent Width')],
            ['id_value' => '5', 'name' => $this->l('5/12 Of Parent Width')],
            ['id_value' => '6', 'name' => $this->l('6/12 Of Parent Width')],
            ['id_value' => '7', 'name' => $this->l('7/12 Of Parent Width')],
            ['id_value' => '8', 'name' => $this->l('8/12 Of Parent Width')],
            ['id_value' => '9', 'name' => $this->l('9/12 Of Parent Width')],
            ['id_value' => '10', 'name' => $this->l('10/12 Of Parent Width')],
            ['id_value' => '11', 'name' => $this->l('11/12 Of Parent Width')],
            ['id_value' => '12', 'name' => $this->l('12/12 Of Parent Width')],
        ];

        $this->two_block_position = [
            ['id' => '1', 'name' => '1'],
            ['id' => '2', 'name' => '2'],
        ];

        $subblock_head = LoginParentBlock::getNoOfSubBlocks('header', $this->id_theme);
        $this->head_pos = [];
        for ($i = 1; $i <= $subblock_head; ++$i) {
            $this->head_pos[] = array(
				'id' => $i, 
				'name' => $i
			);
        }

        $subblock_reg = LoginParentBlock::getNoOfSubBlocks('registration', $this->id_theme);
        $this->reg_pos = [];

        for ($i = 1; $i <= $subblock_reg; ++$i) {
            $this->reg_pos[] = ['id' => $i, 'name' => $i];
        }

        $subBlockCont = LoginParentBlock::getNoOfSubBlocks('content', $this->id_theme);
        $this->content_pos = [];

        for ($i = 1; $i <= $subBlockCont; ++$i) {
            $this->content_pos[] = ['id' => $i, 'name' => $i];
        }
    }

    public function initContent()
    {
        $this->initTabModuleList();
        $this->initToolbar();
        $this->initPageHeaderToolbar();

        $this->renderList();

        $this->content = $this->renderForm();
        $this->context->smarty->assign(
            [
                'content' => $this->content,
                'url_post' => self::$currentIndex.'&token='.$this->token,
                'show_page_header_toolbar' => $this->show_page_header_toolbar,
                'page_header_toolbar_title' => $this->page_header_toolbar_title,
                'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
            ]
        );

        // return parent::initContent();
    }

    public function renderList()
    {
        Hook::exec('actionSellerWiseLoginRenderList');

        return parent::renderList();
    }

    public function renderForm()
    {
        $this->fields_form = [
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        $idShop = $this->context->shop->id;
        $smartyArr = [
            'width' => $this->width,
            'head_pos' => $this->head_pos,
            'reg_pos' => $this->reg_pos,
            'content_pos' => $this->content_pos,
            'two_block_position' => $this->two_block_position,
        ];

        $objLoginConf = new LoginConfigration();
        $themeConf = $objLoginConf->getShopThemeConfigration($idShop, $this->id_theme);
        $themeConfLangArr = $objLoginConf->getShopThemeConfigrationLangInfo($this->id_theme);
        if ($themeConfLangArr) {
            foreach ($themeConfLangArr as $themeConfLang) {
                $themeConf['meta_title'][$themeConfLang['id_lang']] = $themeConfLang['meta_title'];
                $themeConf['meta_description'][$themeConfLang['id_lang']] = $themeConfLang['meta_description'];
            }
        }
        $smartyArr['themeConfig'] = $themeConf;
        $objBlockPosition = new LoginBlockPosition();
        $objBlockContent = new LoginContent();

        $wk_logo_dir = _PS_MODULE_DIR_.$this->module->name.'/views/img/';
        $img_src = glob($wk_logo_dir.'logo.*');

        if ($img_src && file_exists($img_src[0])) {
            $ext = pathinfo($img_src[0], PATHINFO_EXTENSION);

            $wk_logo_url = _MODULE_DIR_.$this->module->name.'/views/img/logo.'.$ext;
            $smartyArr['wk_logo_url'] = $wk_logo_url;
        }

        $headerLogoDetails = $objBlockPosition->getBlockPositionDetailByBlockName($idShop, 'logo', $this->id_theme);
        $smartyArr['headerLogoDetails'] = $headerLogoDetails;

        $headerLoginDetails = $objBlockPosition->getBlockPositionDetailByBlockName($idShop, 'login', $this->id_theme);
        $smartyArr['headerLoginDetails'] = $headerLoginDetails;

        $contentPosition = LoginParentBlock::getParentBlockPosition('content', $this->id_theme);
        $smartyArr['contentPosition'] = $contentPosition;

        $contentPBlockActive = LoginParentBlock::isParentBlockActive('content', $this->id_theme);
        $smartyArr['contentPBlockActive'] = $contentPBlockActive;

        $blockFeatureDetail = $objBlockPosition->getBlockPositionDetailByBlockName($idShop, 'feature', $this->id_theme);
        $smartyArr['blockFeatureDetail'] = $blockFeatureDetail;
        if ($blockFeatureDetail) {
            $blockContent = $objBlockContent->getBlockContent($blockFeatureDetail['id'], $this->id_theme);
            $blockContentLang = $objBlockContent->getBlockLangContentById($blockContent['id']);
            $blockLangContent = array();
            foreach ($blockContentLang as $content) {
                $blockLangContent['content'][$content['id_lang']] = $content['content'];
            }
            // d($blockLangContent);
            $smartyArr['blockLangContent'] = $blockLangContent;
        }

        $regBannerPosition = LoginParentBlock::getParentBlockPosition('registration', $this->id_theme);
        $smartyArr['regBannerPosition'] = $regBannerPosition;

        $bannerImgUrl = _PS_MODULE_DIR_.$this->module->name.'/views/img/banner_img/'.$this->id_theme.'.jpg';
        if (file_exists($bannerImgUrl)) {
            $bannerImgUrl = _MODULE_DIR_.$this->module->name.'/views/img/banner_img/'.$this->id_theme.'.jpg';
            $smartyArr['bannerImgUrl'] = $bannerImgUrl;
        }

        $regPBlockActive = LoginParentBlock::isParentBlockActive('registration', $this->id_theme);
        $smartyArr['regPBlockActive'] = $regPBlockActive;

        $regBlockTitleDetails = $objBlockPosition->getBlockPositionDetailByBlockName($idShop, 'reg_title', $this->id_theme);
        if ($regBlockTitleDetails) {
            $regTitle = $objBlockContent->getBlockContent($regBlockTitleDetails['id'], $this->id_theme);
            if ($regTitle) {
                $regTitleLineLang = $objBlockContent->getBlockLangContentById($regTitle['id']);
                if ($regTitleLineLang) {
                    $regTitleLine = array();
                    foreach ($regTitleLineLang as $regTitle) {
                        $regTitleLine['content'][$regTitle['id_lang']] = $regTitle['content'];
                    }
                    $smartyArr['regTitleLine'] = $regTitleLine;
                }
            }
            $smartyArr['regBlockTitleDetails'] = $regBlockTitleDetails;
        }

        $regBlockDetails = $objBlockPosition->getBlockPositionDetailByBlockName($idShop, 'reg_block', $this->id_theme);
        $smartyArr['regBlockDetails'] = $regBlockDetails;

        $termsConditionDetails = $objBlockPosition->getBlockPositionDetailByBlockName($idShop, 'termscondition', $this->id_theme);

        if ($termsConditionDetails) {
            $tcBlock = $objBlockContent->getBlockContent($termsConditionDetails['id'], $this->id_theme);
            $tcBlockContentLang = $objBlockContent->getBlockLangContentById($tcBlock['id']);
            if ($tcBlockContentLang) {
                $tcBlockContent = array();
                foreach ($tcBlockContentLang as $regTitle) {
                    $tcBlockContent['content'][$regTitle['id_lang']] = $regTitle['content'];
                }
                $smartyArr['tcBlockContent'] = $tcBlockContent;
            }
            $smartyArr['termsConditionDetails'] = $termsConditionDetails;
        }

        $smartyArr['tinymce'] = true;
        $iso = $this->context->language->iso_code;
        $smartyArr['iso'] = file_exists(_PS_CORE_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en';
        $smartyArr['path_css'] = _THEME_CSS_DIR_;
        $smartyArr['ad'] = __PS_BASE_URI__.basename(_PS_ADMIN_DIR_);
        $smartyArr['languages'] = Language::getLanguages();
        $smartyArr['total_languages'] = count(Language::getLanguages());
        $smartyArr['current_lang'] = Language::getLanguage((int) Configuration::get('PS_LANG_DEFAULT'));
        $smartyArr['multi_lang'] = Configuration::get('MP_MULTILANG_ADMIN_APPROVE');
        $smartyArr['multi_def_lang_off'] = Configuration::get('MP_MULTILANG_DEFAULT_LANG');
        $this->context->smarty->assign($smartyArr);

        $this->multiple_fieldsets = true;

        return parent::renderForm();
    }

    public function postProcess()
    {
        $objParentBlock = new LoginParentBlock();
        if (Tools::isSubmit('submit_1')) {
            $headerBgColor = Tools::getValue('header_bg_color');
            $bodyBgColor = Tools::getValue('body_bg_color');

            if ($headerBgColor) {
                if (!Validate::isColor($headerBgColor)) {
                    $this->errors[] = $this->l('Header Background Color value is not valid.');
                }
            }

            if ($bodyBgColor) {
                if (!Validate::isColor($bodyBgColor)) {
                    $this->errors[] = $this->l('Body Background Color value is not valid.');
                }
            }

            if (empty($this->errors)) {
                $objLoginConf = new LoginConfigration();
                $themeConf = $objLoginConf->getShopThemeConfigration($this->context->shop->id, $this->id_theme);

                if ($themeConf) {
                    $objLoginConf = new LoginConfigration($themeConf['id']);
                } else {
                    $objLoginConf->id_shop = $this->context->shop->id;
                    $objLoginConf->id_theme = $this->id_theme;
                }

                $objLoginConf->header_bg_color = $headerBgColor;
                $objLoginConf->body_bg_color = $bodyBgColor;

                foreach (Language::getLanguages(false) as $language) {
                    $objLoginConf->meta_title[$language['id_lang']] = Tools::getValue('metaTitle_'.$language['id_lang']);

                    $objLoginConf->meta_description[$language['id_lang']] = Tools::getValue('metaDescription_'.$language['id_lang']);
                }
                $objLoginConf->save();
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        }

        if (Tools::isSubmit('submit_2')) {
            if ($_FILES['wk_logo']['size']) {
                $imginfo = getimagesize($_FILES['wk_logo']['tmp_name']);
                $imgWidth = $imginfo[0];
                $imgHeight = $imginfo[1];

                if (!ImageManager::isRealImage($_FILES['wk_logo']['tmp_name'], $_FILES['wk_logo']['type']) || !ImageManager::isCorrectImageFileExt($_FILES['wk_logo']['name']) || preg_match('/\%00/', $_FILES['wk_logo']['name'])) {
                    $this->errors[] = $this->l('Image format not recognized, allowed formats are: .gif, .jpg, .png');
                } elseif ($imgWidth > 350 || $imgHeight > 99) {
                    $this->errors[] = $this->l('Invalid Image Dimentions.');
                } else {
                    $ext = pathinfo($_FILES['wk_logo']['name'], PATHINFO_EXTENSION);
                    $wk_logo_dir = _PS_MODULE_DIR_.$this->module->name.'/views/img/';

                    $img_src = glob($wk_logo_dir.'logo.*');

                    if (file_exists($img_src[0])) {
                        unlink($img_src[0]);
                    }

                    $img_src = $wk_logo_dir.'logo'.'.'.$ext;
                    ImageManager::resize($_FILES['wk_logo']['tmp_name'], $img_src, 0, 0, $ext);
                }
            }

            if (empty($this->errors)) {
                $parentBlock = $objParentBlock->getBlockIdByThemeId('header', $this->id_theme);

                $this->saveBlockPositionDetails('logo', $parentBlock['id'], 'shopLogoPosition', 'shopLogoWidth', 0, 'hdblk_bg_color', 'hdBlockTextColor');

                $this->saveBlockPositionDetails('login', $parentBlock['id'], 'loginBlockPosition', 'loginWidth', 0, 'hdblk_bg_color', 'hdBlockTextColor');

                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        }

        if (Tools::isSubmit('submit_3')) {
            $parentBlockName = 'registration';
            $parentBlock = $objParentBlock->getBlockIdByThemeId($parentBlockName, $this->id_theme);
            $this->saveParentBlockSetting($parentBlockName, 'regBannerPosition', 'regPBlockActive');

            if ($_FILES['banner_img']['size']) {
                if (!ImageManager::isRealImage($_FILES['banner_img']['tmp_name'], $_FILES['banner_img']['type']) || !ImageManager::isCorrectImageFileExt($_FILES['banner_img']['name']) || preg_match('/\%00/', $_FILES['banner_img']['name'])) {
                    $this->errors[] = $this->l('Image format not recognized, allowed formats are: .gif, .jpg, .png');
                } else {
                    $img_src = _PS_MODULE_DIR_.$this->module->name.'/views/img/banner_img/'.$this->id_theme.'.jpg';
                    if (file_exists($img_src)) {
                        unlink($img_src);
                    }

                    ImageManager::resize($_FILES['banner_img']['tmp_name'], $img_src, 0, 0, 'png');
                }
            }

            if (empty($this->errors)) {
                $blockPositionId = $this->saveBlockPositionDetails('reg_title', $parentBlock['id'], 'regTitleBlockPos', 'regTitleBlockWidth', 'regTitleBlockActive', 'regBgColor', 'regTitleTextColor');
                $this->saveBlockContent($blockPositionId, 'regTitleLine_');

                $blockPositionId = $this->saveBlockPositionDetails('reg_block', $parentBlock['id'], 'regBlockPosition', 'regBlockWidth', 'regBlockActive', 'regBgColor', 'regBlockTextColor');

                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        }

        if (Tools::isSubmit('submit_4')) {
            $this->saveParentBlockSetting('content', 'contentPosition', 'contentPBlockActive');
            Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
        }

        if (Tools::isSubmit('submit_5')) {
            $parentBlock = $objParentBlock->getBlockIdByThemeId('content', $this->id_theme);
            $blockPositionId = $this->saveBlockPositionDetails('feature', $parentBlock['id'], 'featureBlockPosition', 'featureBlockWidth', 'featureBlockActive', 'featureBgColor', 'featureTextColor');
            $this->saveBlockContent($blockPositionId, 'featureContent_');
            Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
        }

        if (Tools::isSubmit('submit_6')) {
            $parentBlock = $objParentBlock->getBlockIdByThemeId('content', $this->id_theme);
            $blockPositionId = $this->saveBlockPositionDetails('termscondition', $parentBlock['id'], 'tcBlockPosition', 'tcBlockWidth', 'tcBlockActive', 'tcBgColor', 'tcTextColor');
            $this->saveBlockContent($blockPositionId, 'tcBlockContent_');

            Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
        }

        Hook::exec('actionBlockDataSave');
        parent::postProcess();
    }

    public function saveParentBlockSetting($parentBlockName, $parentBlockPosition, $parentBlockActive = 0)
    {
        $objParentBlock = new LoginParentBlock();
        $pblk_detail = $objParentBlock->getParentBlockDetails($parentBlockName, $this->id_theme);
        if ($pblk_detail) {
            $objParentBlock = new LoginParentBlock($pblk_detail['id']);
        }

        $objParentBlock->id_position = Tools::getValue($parentBlockPosition);

        if ($parentBlockActive) {
            $objParentBlock->active = Tools::getValue($parentBlockActive);
        }

        $objParentBlock->save();

        return $objParentBlock->id;
    }

    public function saveBlockPositionDetails($blockName, $idParent, $blockPosition, $blockWidth, $blockActive = 0, $blockBgColor, $blockTextColor)
    {
        $idShop = $this->context->shop->id;

        $objBlockPosition = new LoginBlockPosition();
        $blockDetail = $objBlockPosition->getBlockPositionDetailByBlockName($idShop, $blockName, $this->id_theme);

        if ($blockDetail) {
            $objBlockPosition = new LoginBlockPosition($blockDetail['id']);
        }

        $objBlockPosition->id_shop = $idShop;
        $objBlockPosition->id_parent = $idParent;
        $objBlockPosition->id_position = Tools::getValue($blockPosition);
        $objBlockPosition->block_name = $blockName;
        $objBlockPosition->width = Tools::getValue($blockWidth);
        $objBlockPosition->block_bg_color = Tools::getValue($blockBgColor);
        $objBlockPosition->block_text_color = Tools::getValue($blockTextColor);

        if ($blockActive) {
            $objBlockPosition->active = Tools::getValue($blockActive);
        }

        $objBlockPosition->save();

        return $objBlockPosition->id;
    }

    public function saveBlockContent($blockPositionId, $blockContentName)
    {
        $idShop = $this->context->shop->id;

        $objBlockContent = new LoginContent();
        $blockContent = $objBlockContent->getBlockContent($blockPositionId, $this->id_theme);

        if ($blockContent) {
            $objBlockContent = new LoginContent($blockContent['id']);
        }

        $objBlockContent->id_shop = $idShop;
        $objBlockContent->id_block = $blockPositionId;

        foreach (Language::getLanguages(false) as $language) {
            $objBlockContent->content[$language['id_lang']] = Tools::getValue($blockContentName.$language['id_lang']);
        }
        $objBlockContent->save();

        return $objBlockContent->id;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();

        $this->addJqueryPlugin('tagify');
        $this->addJqueryPlugin('colorpicker');

        $this->addJS(_PS_MODULE_DIR_.$this->module->name.'/views/js/admin_sellerlogin.js');

        $this->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
        if (version_compare(_PS_VERSION_, '1.6.0.11', '>')) {
            $this->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
        } else {
            $this->addJS(_PS_JS_DIR_.'tinymce.inc.js');
        }
    }
}
