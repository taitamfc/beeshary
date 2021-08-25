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

class MpSellerWiseLoginSellerLoginModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $this->display_header = false;
        $this->display_footer = false;
        $smartyArr = array();
        $idCustomer = $this->context->customer->id;

        if ($idCustomer) {
            $objMpSeller = new WkMpSeller();
            $sellerDetail = $objMpSeller->getSellerDetailByCustomerId($idCustomer);
        }

        if ($this->context->cookie->logged && $sellerDetail) {
            Tools::redirect($this->context->link->getModuleLink('marketplace', 'dashboard'));
        } else {
            $objLoginConf = new LoginConfigration();
            $objBlockPosition = new LoginBlockPosition();
            $objBlockContent = new LoginContent();
            $objParentBlock = new LoginParentBlock();
            $objTheme = new LoginTheme();

            $activeTheme = $objTheme->getActiveTheme();
            $idTheme = $activeTheme['id'];
            $smartyArr['idTheme'] = $idTheme;
            $smartyArr['validateJs'] = _PS_JS_DIR_.'validate.js';
            $smartyArr['mpSellerWiseLoginJs'] = _MODULE_DIR_.'mpsellerwiselogin/views/js/mpsellerwiselogin.js';

            $error = Tools::getValue('error');
            if (!empty($error)) {
                $smartyArr['error'] = $error;
            }
            $content_arr = array('reg_title', 'termscondition', 'feature');

            $themeConf = $objLoginConf->getShopThemeConfigration($this->context->shop->id, $idTheme, $this->context->language->id);

            if ($themeConf) {
                $smartyArr['themeConf'] = $themeConf;
            }

            $wk_logo_dir = _PS_MODULE_DIR_.$this->module->name.'/views/img/';
            $img_src = glob($wk_logo_dir.'logo.*');

            if ($img_src && file_exists($img_src[0])) {
                $ext = pathinfo($img_src[0], PATHINFO_EXTENSION);

                $wk_logo_url = _MODULE_DIR_.$this->module->name.'/views/img/logo.'.$ext;
                $smartyArr['wk_logo_url'] = $wk_logo_url;
            }

            $headerBlock = $objParentBlock->getBlockIdByThemeId('header', $idTheme);
            $headerBlockPosition = $objBlockPosition->getPositionDetailByIdParent($headerBlock['id'], $idTheme);
            $loginBlockPosition = $objBlockPosition->getBlockPositionDetailByBlockName($this->context->shop->id, 'login', $idTheme);
            $smartyArr['loginBlockPosition'] = $loginBlockPosition;

            if ($headerBlockPosition) {
                $smartyArr['headerBlock'] = $headerBlockPosition;
            }

            $parentBlock = $objParentBlock->getActiveParentBlock($idTheme);
            if ($parentBlock) {
                foreach ($parentBlock as $key => $value) {
                    $parentBlock[$key]['sub_block'] = $objBlockPosition->getPositionDetailByIdParent($value['id'], $idTheme);
                    if ($parentBlock[$key]['sub_block']) {
                        foreach ($parentBlock[$key]['sub_block'] as $sub_k => $sub_v) {
                            if (in_array($sub_v['block_name'], $content_arr)) {
                                $parentBlock[$key]['sub_block'][$sub_k]['data'] = $objBlockContent->getBlockContent($sub_v['id'], $idTheme, $this->context->language->id);
                            }
                        }
                    }
                }
                $smartyArr['parentBlock'] = $parentBlock;
            }

            $bannerImg = _MODULE_DIR_.$this->module->name.'/views/img/banner_img/'.$idTheme.'.jpg';
            $smartyArr['bannerImg'] = $bannerImg;

            $modImgDir = _MODULE_DIR_.$this->module->name.'/views/img/';
            $smartyArr['modImgDir'] = $modImgDir;

            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $smartyArr['allow_multilang'] = 1;
                $curr_lang = $this->context->language->id;
            } else {
                $smartyArr['allow_multilang'] = 0;
                if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') {
                    //Admin default lang
                    $curr_lang = Configuration::get('PS_LANG_DEFAULT');
                } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') {
                    //Seller default lang
                    $curr_lang = $this->context->language->id;
                }
            }
            $current_lang = Language::getLanguage((int) $curr_lang);

            $smartyArr['MP_SELLER_COUNTRY_NEED'] = Configuration::get('WK_MP_SELLER_COUNTRY_NEED');
            $smartyArr['max_phone_digit'] = Configuration::get('WK_MP_PHONE_DIGIT');
            $smartyArr['languages'] = Language::getLanguages();
            $smartyArr['total_languages'] = count(Language::getLanguages());
            $smartyArr['current_lang'] = $current_lang;
            $smartyArr['multi_lang'] = Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE');
            $smartyArr['active_languages'] = Language::getLanguages(true, $this->context->shop->id);
            $smartyArr['countries'] = Country::getCountries($this->context->language->id, true);

            $this->context->smarty->assign($smartyArr);

            Media::addJsDef(['modImgDir' => $modImgDir, 
                'lang' => $current_lang['id_lang']
            ]);

            $this->defineJSVars();
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/sellerlogin.tpl');
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        $objTheme = new LoginTheme();
        $activeTheme = $objTheme->getActiveTheme();
        $idTheme = $activeTheme['id'];
        $this->registerStylesheet('add_product', 'modules/'.$this->module->name.'/views/css/theme'.$idTheme.'.css');
        $this->registerJavascript('ps-validate-js', 'js/validate.js');
        $this->registerJavascript(
            'seller-wise-login-js', 
            'modules/'.$this->module->name.'/views/js/mpsellerwiselogin.js',
            [
                'media' => 'all',
                'priority' => 900,
                'position' => 'bottom',
            ]
        );
    }

    public function defineJSVars()
    {
        $jsVars = [
            'emailIdError' => $this->module->l('Please Change your Email-Id to continue from here.', 'sellerlogin'),
            'allFieldMandatoryError' => $this->module->l('All Fields Are Mandatory.', 'sellerlogin'),
            'firstNameError' => $this->module->l('First name is not valid.', 'sellerlogin'),
            'lastNameError' => $this->module->l('Last name is not valid.', 'sellerlogin'),
            'invalidEmailIdError' => $this->module->l('Please Enter Valid Email-Id.', 'sellerlogin'),
            'passwordLengthError' => $this->module->l('Password Length Must Be More Than 4 digit.', 'sellerlogin'),
            'invalidPasswordError' => $this->module->l('Please enter valid Password', 'sellerlogin'),
            'invalidUniqueShopNameError' => $this->module->l('Invalid unique shop name.', 'sellerlogin'),
            'shopNameRequiredLang' => $this->module->l('Shop name is required in Default Language', 'sellerlogin'),
            'shopNameRequired' => $this->module->l('Shop name is required.', 'sellerlogin'),
            'invalidShopNameError' => $this->module->l('Invalid shop name', 'sellerlogin'),
            'phoneNumberError' => $this->module->l('Phone number is not valid.', 'sellerlogin'),
            'emailAlreadyExist' => $this->module->l('This email is already registered as Seller, Please Login.', 'sellerlogin'),
            'shopNameAlreadyExist' => $this->module->l('Unique Shop name already taken. Try another.', 'sellerlogin'),
            'shopNameError' => $this->module->l('Shop name can not contain any special character except underscrore. Try another.', 'sellerlogin'),
            'checkCustomerAjaxUrl' => $this->context->link->getModulelink('mpsellerwiselogin', 'checkcustomerajax'),
            'validateUniquenessAjaxUrl' => $this->context->link->getModulelink('marketplace', 'validateuniqueshop'),
        ];

        Media::addJsDef($jsVars);
    }
}
