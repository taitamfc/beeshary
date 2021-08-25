<?php
/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminGdprConfigurationController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->table = 'configuration';
        $this->display = 'add';
        parent::__construct();
        $this->page_header_toolbar_title = $this->l('GDPR Configuration');
    }

    public function renderForm()
    {
        $this->context->smarty->assign(
            array(
                'WK_GDPR_DEFAULT_AGREEMENT_CONTENT' => Configuration::get('WK_GDPR_DEFAULT_AGREEMENT_CONTENT'),
                'WK_GDPR_CUSTOMER_DATA_DELETE_APPROVE' => Configuration::get('WK_GDPR_CUSTOMER_DATA_DELETE_APPROVE'),
                'WK_GDPR_COOKIE_BLOCK_BG_COLOR' => Configuration::get('WK_GDPR_COOKIE_BLOCK_BG_COLOR'),
                'WK_GDPR_COOKIE_BLOCK_TEXT_COLOR' => Configuration::get('WK_GDPR_COOKIE_BLOCK_TEXT_COLOR'),
                'WK_GDPR_COOKIE_BLOCK_BORDER_COLOR' => Configuration::get('WK_GDPR_COOKIE_BLOCK_BORDER_COLOR'),
                'WK_GDPR_COOKIE_BLOCK_BUTTON_BG_COLOR' => Configuration::get('WK_GDPR_COOKIE_BLOCK_BUTTON_BG_COLOR'),
                'WK_GDPR_COOKIE_BLOCK_CONTENT' => Configuration::get('WK_GDPR_COOKIE_BLOCK_CONTENT'),
                'WK_GDPR_COOKIE_BLOCK_IMAGE_SHOW' => Configuration::get('WK_GDPR_COOKIE_BLOCK_IMAGE_SHOW'),
                'WK_GDPR_COOKIE_BLOCK_ENABLE' => Configuration::get('WK_GDPR_COOKIE_BLOCK_ENABLE'),
                'WK_GDPR_COOKIE_BLOCK_POSITION' => Configuration::get('WK_GDPR_COOKIE_BLOCK_POSITION'),
                'WK_GDPR_COOKIE_BLOCK_BUTTON_BG_HOVER_COLOR' => Configuration::get(
                    'WK_GDPR_COOKIE_BLOCK_BUTTON_BG_HOVER_COLOR'
                ),
                'WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_HOVER_COLOR' => Configuration::get(
                    'WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_HOVER_COLOR'
                ),
                'WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_HOVER_COLOR' => Configuration::get(
                    'WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_HOVER_COLOR'
                ),
                'WK_GDPR_ADMIN_MAIL_DATA_ERASURE_REQUEST' => Configuration::get(
                    'WK_GDPR_ADMIN_MAIL_DATA_ERASURE_REQUEST'
                ),
                'WK_GDPR_ADMIN_MAIL_DATA_UPDATE_REQUEST' => Configuration::get(
                    'WK_GDPR_ADMIN_MAIL_DATA_UPDATE_REQUEST'
                ),
                'WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_COLOR' => Configuration::get(
                    'WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_COLOR'
                ),
                'WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_COLOR' => Configuration::get(
                    'WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_COLOR'
                ),
                'moduleDir' => _MODULE_DIR_,
                'psModuleDir' => _PS_MODULE_DIR_,
            )
        );
        $this->fields_form = array(
            'submit' => array(
                'class' => 'button',
            ),
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitGDPRGeneralConfig')) {
            $defaultAgreementContent = Tools::getValue('WK_GDPR_DEFAULT_AGREEMENT_CONTENT');

            if (!trim($defaultAgreementContent)) {
                $this->errors[] = $this->l('Default GDPR agreement content is required.');
            }

            if (!count($this->errors)) {
                Configuration::updateValue(
                    'WK_GDPR_CUSTOMER_DATA_DELETE_APPROVE',
                    Tools::getValue('WK_GDPR_CUSTOMER_DATA_DELETE_APPROVE')
                );
                Configuration::updateValue('WK_GDPR_DEFAULT_AGREEMENT_CONTENT', $defaultAgreementContent, true);

                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            }
        } elseif (Tools::isSubmit('submitGDPRMailConfig')) {
            Configuration::updateValue(
                'WK_GDPR_ADMIN_MAIL_DATA_ERASURE_REQUEST',
                Tools::getValue('wk_gdpr_admin_mail_data_erasure_request')
            );
            Configuration::updateValue(
                'WK_GDPR_ADMIN_MAIL_DATA_UPDATE_REQUEST',
                Tools::getValue('wk_gdpr_admin_mail_data_update_request')
            );

            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
        } elseif (Tools::isSubmit('submitCookieLawConfig')) {
            $cookieBlockBgColor = Tools::getValue('WK_GDPR_COOKIE_BLOCK_BG_COLOR');
            $cookieBlockTextColor = Tools::getValue('WK_GDPR_COOKIE_BLOCK_TEXT_COLOR');
            $cookieBlockBorderColor = Tools::getValue('WK_GDPR_COOKIE_BLOCK_BORDER_COLOR');
            $cookieBlockButtonBgColor = Tools::getValue('WK_GDPR_COOKIE_BLOCK_BUTTON_BG_COLOR');
            $cookieBlockButtonTextColor = Tools::getValue('WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_COLOR');
            $cookieBlockButtonBorderColor = Tools::getValue('WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_COLOR');

            $cookieBlockButtonBgHoverColor = Tools::getValue('WK_GDPR_COOKIE_BLOCK_BUTTON_BG_HOVER_COLOR');
            $cookieBlockButtonTextHoverColor = Tools::getValue('WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_HOVER_COLOR');
            $cookieBlockButtonBorderHoverColor = Tools::getValue('WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_HOVER_COLOR');
            $cookieBlockContent = Tools::getValue('WK_GDPR_COOKIE_BLOCK_CONTENT');

            if ($cookieBlockBgColor == '') {
                $this->errors[] = $this->l('Please select cookie block background color.');
            } elseif (!Validate::isColor($cookieBlockBgColor)) {
                $this->errors[] = $this->l('Invalid value entered in cookie block background color.');
            }

            if ($cookieBlockTextColor == '') {
                $this->errors[] = $this->l('Please select cookie block text color.');
            } elseif (!Validate::isColor($cookieBlockTextColor)) {
                $this->errors[] = $this->l('Invalid value entered in cookie block text color.');
            }

            if ($cookieBlockBorderColor == '') {
                $this->errors[] = $this->l('Please select cookie block border color.');
            } elseif (!Validate::isColor($cookieBlockBorderColor)) {
                $this->errors[] = $this->l('Invalid value entered in cookie block border color.');
            }

            if ($cookieBlockButtonBgColor == '') {
                $this->errors[] = $this->l('Please select cookie block\'s button background color.');
            } elseif (!Validate::isColor($cookieBlockButtonBgColor)) {
                $this->errors[] = $this->l('Invalid value entered in cookie block\'s button background color.');
            }

            if ($cookieBlockButtonTextColor == '') {
                $this->errors[] = $this->l('Please select cookie block\'s button text color.');
            } elseif (!Validate::isColor($cookieBlockButtonTextColor)) {
                $this->errors[] = $this->l('Invalid value entered in cookie block\'s button text color.');
            }

            if ($cookieBlockButtonBorderColor == '') {
                $this->errors[] = $this->l('Please select cookie block\'s button border color.');
            } elseif (!Validate::isColor($cookieBlockButtonBorderColor)) {
                $this->errors[] = $this->l('Invalid value entered in cookie block\'s button border color.');
            }

            if ($cookieBlockButtonBgHoverColor == '') {
                $this->errors[] = $this->l('Please select cookie block\'s button background color on hover.');
            } elseif (!Validate::isColor($cookieBlockButtonBgHoverColor)) {
                $this->errors[] = $this->l('Invalid value entered in cookie block\'s button background color
                on hover.');
            }

            if ($cookieBlockButtonTextHoverColor == '') {
                $this->errors[] = $this->l('Please select cookie block\'s button text color on hover.');
            } elseif (!Validate::isColor($cookieBlockButtonTextHoverColor)) {
                $this->errors[] = $this->l('Invalid value entered in cookie block\'s button text color on hover.');
            }

            if ($cookieBlockButtonBorderHoverColor == '') {
                $this->errors[] = $this->l('Please select cookie block\'s button border color hover.');
            } elseif (!Validate::isColor($cookieBlockButtonBorderHoverColor)) {
                $this->errors[] = $this->l('Invalid value entered in cookie block\'s button border color hover.');
            }

            $cleanHtmlContent = Tools::getDescriptionClean(
                Tools::getValue('WK_GDPR_COOKIE_BLOCK_CONTENT')
            );
            //Remove TinyMCE's Non-Breaking Spaces
            $cleanHtmlContent = str_replace(chr(0xC2).chr(0xA0), "", $cleanHtmlContent);
            // END
            if (!trim($cleanHtmlContent)) {
                $this->errors[] = $this->l('Cookie block content is required');
            } else {
                if (!Validate::isCleanHtml(
                    $cookieBlockContent,
                    (int) Configuration::get('PS_ALLOW_HTML_IFRAME')
                )) {
                    $this->errors[] = $this->l('Cookie block content is not valid');
                }
            }

            $cookieBlockImg = $_FILES['wk_cookie_block_img'];
            if (isset($cookieBlockImg)
                && $cookieBlockImg['name']
            ) {
                if ($error = ImageManager::validateUpload(
                    $cookieBlockImg,
                    Tools::getMaxUploadSize()
                )) {
                    $this->errors[] = $error;
                }
            }

            if (!count($this->errors)) {
                if (isset($cookieBlockImg['tmp_name'])) {
                    ImageManager::resize(
                        $cookieBlockImg['tmp_name'],
                        _PS_MODULE_DIR_.'wkgdpr/views/img/uploads/wk_cookie_block_img.png'
                    );
                }
                Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_BG_COLOR', $cookieBlockBgColor);
                Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_TEXT_COLOR', $cookieBlockTextColor);
                Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_BORDER_COLOR', $cookieBlockBorderColor);
                Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_BUTTON_BG_COLOR', $cookieBlockButtonBgColor);
                Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_COLOR', $cookieBlockButtonTextColor);
                Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_COLOR', $cookieBlockButtonBorderColor);
                Configuration::updateValue(
                    'WK_GDPR_COOKIE_BLOCK_BUTTON_BG_HOVER_COLOR',
                    $cookieBlockButtonBgHoverColor
                );
                Configuration::updateValue(
                    'WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_HOVER_COLOR',
                    $cookieBlockButtonTextHoverColor
                );
                Configuration::updateValue(
                    'WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_HOVER_COLOR',
                    $cookieBlockButtonBorderHoverColor
                );

                Configuration::updateValue('WK_GDPR_COOKIE_BLOCK_CONTENT', $cookieBlockContent, true);
                Configuration::updateValue(
                    'WK_GDPR_COOKIE_BLOCK_IMAGE_SHOW',
                    Tools::getValue('WK_GDPR_COOKIE_BLOCK_IMAGE_SHOW')
                );
                Configuration::updateValue(
                    'WK_GDPR_COOKIE_BLOCK_ENABLE',
                    Tools::getValue('WK_GDPR_COOKIE_BLOCK_ENABLE')
                );
                Configuration::updateValue(
                    'WK_GDPR_COOKIE_BLOCK_POSITION',
                    Tools::getValue('WK_GDPR_COOKIE_BLOCK_POSITION')
                );

                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            }
        }
        parent::postProcess();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $jsVars = array(
            'path_css' => _THEME_CSS_DIR_,
            'baseDir' => __PS_BASE_URI__,
            'ad' => __PS_BASE_URI__.basename(_PS_ADMIN_DIR_),
            'autoload_rte' => true,
            'lang' => true,
            'iso' => $this->context->language->iso_code,
            'maxSizeAllowed' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')
        );
        Media::addJsDef($jsVars);

        $this->addJqueryPlugin('colorpicker');
        //tinymce
        $this->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
        if (version_compare(_PS_VERSION_, '1.6.0.11', '>')) {
            $this->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
        } else {
            $this->addJS(_PS_JS_DIR_.'tinymce.inc.js');
        }

        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/admin/wk_gdpr_configuration.js');
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/admin/wk_gdpr_configuration.css');
    }
}
