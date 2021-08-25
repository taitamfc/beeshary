<?php
/**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Invisiblegrecaptcha extends Module
{
    public static $recaptcha_js_api;

    public static $captcha_config = array();

    public static $file_path = null;

    public static $error_messages = array();

    public function __construct()
    {
        $this->name = 'invisiblegrecaptcha';
        $this->tab = 'front_office_features';
        $this->version = '2.0.5';
        $this->author = 'Gofenice Technologies';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->module_key = '0efa56d948ca17e0784c2d5cf9498706';
        parent::__construct();
        $this->displayName = $this->l('Google - No CAPTCHA reCAPTCHA - Manual (V2), Invisible (V3)');
        $this->description = $this->l('Secure your shop from spam & abuse while real people pass through with ease');
        self::$recaptcha_js_api = 'https://www.google.com/recaptcha/api.js';
        self::$recaptcha_js_api .= '?hl='.$this->context->language->iso_code;
        $this->loadConfig();
    }

    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');
        return parent::install()
            && $this->registerHook('header')
            && $this->registerHook('displayCustomerAccountForm');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function getContent()
    {
        $this->context->controller->addJqueryPlugin('tagify');
        $this->context->controller->addJS(self::$recaptcha_js_api);

        if (Tools::getIsset('action')) {
            switch (Tools::getValue('action')) {
                case 'add-domain':
                    Db::getInstance()->delete(
                        'grecaptcha_domains',
                        'domain = "'.pSQL(Tools::getValue('domain')).'"'
                    );

                    Db::getInstance()->insert(
                        'grecaptcha_domains',
                        array(
                            'domain'    =>  pSQL(Tools::getValue('domain'))
                        )
                    );
                    break;

                case 'update-domain':
                    Db::getInstance()->update(
                        'grecaptcha_domains',
                        array(
                            'domain'    =>  pSQL(Tools::getValue('domain'))
                        ),
                        'id_grecaptcha_domain = '.(int)Tools::getValue('id_domain')
                    );
                    break;

                case 'delete-domain':
                    Db::getInstance()->delete(
                        'grecaptcha_domains',
                        'id_grecaptcha_domain = '.(int)Tools::getValue('id_domain')
                    );
                    break;
            }

            echo Tools::jsonEncode($this->getAllDomains());
            exit;
        }

        $this->context->smarty->assign(
            array(
                'domains'   =>  $this->getAllDomains(),
                'ajax_action_link'  =>  $this->context->link->getAdminLink('AdminModules', true).'&configure='.
                $this->name.'&tab_module='.$this->tab.'&module_name='.$this->name
            )
        );

        $output = '';

        if (((bool)Tools::isSubmit('submitGrecaptchaModule')) == true) {
            $output .= $this->postProcess();
            $this->loadConfig();
        }

        $spam_keys = $this->renderForm('spam-keys');
        $domain_restrictions = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm().$spam_keys.$domain_restrictions;
    }

    protected function renderForm($form = 'default')
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table.'_'.$form;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitGrecaptchaModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => self::$captcha_config,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm($form)));
    }
    
    protected function getConfigForm($form = 'default')
    {
        $config_feilds = array();

        switch ($form) {
            case 'default':
                $config_feilds = array(
                    'form' => array(
                        'legend' => array(
                            'title' => $this->l('reCAPTCHA Settings'),
                            'icon' => 'icon-cogs',
                        ),
                        'input' => array(
                            array(
                                'col' => 3,
                                'type' => 'text',
                                'prefix' => '<i class="icon icon-key"></i>',
                                'name' => 'GRECAPTCHA_SITE_KEY',
                                'label' => $this->l('Site Key'),
                            ),
                            array(
                                'col' => 3,
                                'type' => 'text',
                                'prefix' => '<i class="icon icon-key"></i>',
                                'name' => 'GRECAPTCHA_SECRET_KEY',
                                'label' => $this->l('Secret Key'),
                            ),
                            array(
                                'col' => 3,
                                'type' => 'select',
                                'name' => 'GRECAPTCHA_VERSION',
                                'label' => $this->l('reCAPTCHA Version'),
                                'options' => array(
                                    'query' => array(
                                        array(
                                            'name' => 'V2 / Manual Verification',
                                            'value' => '2'
                                        ),
                                        array(
                                            'name' => $this->l('V3 / Invisible'),
                                            'value' => '3'
                                        )
                                    ),
                                    'id' => 'value',
                                    'name' => 'name'
                                )
                            ),
                            array(
                                'col' => 3,
                                'type' => 'switch',
                                'name' => 'GRECAPTCHA_IN_REG_FORM',
                                'label' => $this->l('Enable for registration form'),
                                'values' => array(
                                    array(
                                        'id' => 'active_on',
                                        'value' => 1,
                                        'label' => $this->l('Enabled')
                                    ),
                                    array(
                                        'id' => 'active_off',
                                        'value' => 0,
                                        'label' => $this->l('Disabled')
                                    )
                                ),
                                'is_bool' => true
                            )
                        ),
                        'submit' => array(
                            'title' => $this->l('Save'),
                        ),
                    ),
                );

                if (self::$captcha_config['GRECAPTCHA_VERSION'] == 2 && self::$captcha_config['GRECAPTCHA_SITE_KEY']) {
                    $config_feilds['form']['input'][] = array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'GRECAPTCHA_PREVIEW',
                        'label' => $this->l('Preview')
                    );
                } elseif (self::$captcha_config['GRECAPTCHA_VERSION'] == 3) {
                    $config_feilds['form']['input'][] = array(
                        'col' => 3,
                        'type' => 'select',
                        'name' => 'GRECAPTCHA_POSITION',
                        'label' => $this->l('reCAPTCHA badge position'),
                        'hint' => $this->l('Reposition the reCAPTCHA badge. \'Custom\' allows you to control the CSS'.
                            ' - write your own CSS for the badge in the theme css file.'),
                        'options' => array(
                            'query' => array(
                                array(
                                    'name' => $this->l('Bottom Right'),
                                    'value' => 'bottomright'
                                ),
                                array(
                                    'name' => $this->l('Bottom Left'),
                                    'value' => 'bottomleft'
                                ),
                                array(
                                    'name' => $this->l('Custom'),
                                    'value' => 'inline'
                                )
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        )
                    );
                }
                break;
            
            case 'spam-keys':
                $config_feilds = array(
                    'form' => array(
                        'legend' => array(
                            'title' => $this->l('Add spam words'),
                            'icon' => 'icon-plus',
                        ),
                        'input' => array(
                            array(
                                'col' => 6,
                                'type' => 'tags',
                                'name' => 'GRECAPTCHA_SPAM_KEYS',
                                'label' => $this->l('Spam Words'),
                                'desc' => $this->l('Click in the field, write something, and then press "Enter."')
                            ),
                        ),
                        'submit' => array(
                            'title' => $this->l('Save'),
                        ),
                    )
                );
                break;
        }

        return $config_feilds;
    }

    protected function getConfigFormValues()
    {
        $site_key = Tools::getValue('GRECAPTCHA_SITE_KEY', Configuration::get('GRECAPTCHA_SITE_KEY'));
        $secret_key = Tools::getValue('GRECAPTCHA_SECRET_KEY', Configuration::get('GRECAPTCHA_SECRET_KEY'));
        $enable_reg_form = Tools::getValue('GRECAPTCHA_IN_REG_FORM', Configuration::get('GRECAPTCHA_IN_REG_FORM'));
        $position = Tools::getValue('GRECAPTCHA_POSITION', Configuration::get('GRECAPTCHA_POSITION'));
        $version = Tools::getValue('GRECAPTCHA_VERSION', Configuration::get('GRECAPTCHA_VERSION'));
        $spam_keys = Tools::getValue('GRECAPTCHA_SPAM_KEYS', Configuration::get('GRECAPTCHA_SPAM_KEYS'));

        return array(
            'GRECAPTCHA_SITE_KEY' => $site_key,
            'GRECAPTCHA_SECRET_KEY' => $secret_key,
            'GRECAPTCHA_IN_REG_FORM' => $enable_reg_form,
            'GRECAPTCHA_POSITION' => $position,
            'GRECAPTCHA_VERSION' => $version,
            'GRECAPTCHA_PREVIEW' => 1,
            'GRECAPTCHA_SPAM_KEYS' => $spam_keys
        );
    }

    protected function postProcess()
    {
        $failed = 0;

        $form_values = self::$captcha_config;

        foreach (array_keys($form_values) as $key) {
            if (Tools::getIsset($key)) {
                $processed = Configuration::updateValue($key, Tools::getValue($key));
                if (!$processed) {
                    $failed++;
                }
            }
        }

        if ($failed) {
            return $this->displayError($this->l('Update failed'));
        } else {
            return $this->displayConfirmation($this->l('Update successful'));
        }
    }

    public function hookHeader()
    {
        $can_load_captcha = false;
        if ($this->context->controller instanceof ContactController) {
            $can_load_captcha = true;
        } elseif ((bool)self::$captcha_config['GRECAPTCHA_IN_REG_FORM']
            && ($this->context->controller instanceof AuthController
                || $this->context->controller instanceof OrderController
                || $this->context->controller instanceof OrderOpcController)
        ) {
            $can_load_captcha = true;
        }

        if ($can_load_captcha) {
            if (self::$captcha_config['GRECAPTCHA_SITE_KEY'] && self::$captcha_config['GRECAPTCHA_SECRET_KEY']) {
                if (version_compare(_PS_VERSION_, '1.6', '>=')) {
                    Media::addJsDef(
                        array(
                            'captcha_site_key'  =>  self::$captcha_config['GRECAPTCHA_SITE_KEY'],
                            'captcha_position'  =>  self::$captcha_config['GRECAPTCHA_POSITION']
                        )
                    );
                }

                $this->context->smarty->assign(
                    array(
                        'captcha_site_key' => self::$captcha_config['GRECAPTCHA_SITE_KEY'],
                        'captcha_position' => self::$captcha_config['GRECAPTCHA_POSITION'],
                        'is_17' => (bool)version_compare(_PS_VERSION_, '1.7', '>=')
                    )
                );

                $this->context->smarty->assign('grecaptcha_tag', $this->display(__FILE__, 'recaptcha-tag.tpl'));

                if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                    $this->context->controller->registerJavascript(
                        'recaptcha-js',
                        self::$recaptcha_js_api,
                        array(
                            'server' => 'remote',
                            'position'  =>  'bottom'
                        )
                    );
                    $this->context->controller->addJS($this->_path.'views/js/front-ps17'.self::$file_path.'.js');
                } elseif (version_compare(_PS_VERSION_, '1.6', '>=')) {
                    $this->context->controller->addJS(self::$recaptcha_js_api);
                    $this->context->controller->addJS($this->_path.'views/js/front-ps16'.self::$file_path.'.js');
                } elseif (version_compare(_PS_VERSION_, '1.5', '>=')) {
                    $this->context->controller->addJS(self::$recaptcha_js_api);
                    $this->context->controller->addJS($this->_path.'views/js/front-ps15'.self::$file_path.'.js');
                    return $this->display(__FILE__, 'recaptcha-script.tpl');
                }
            }
        }
    }

    public function hookDisplayCustomerAccountForm($params)
    {
        if ((bool)self::$captcha_config['GRECAPTCHA_IN_REG_FORM']
            && !($this->context->controller instanceof OrderController)
        ) {
            $this->context->smarty->assign(
                array(
                    'recaptcha_site_key' => self::$captcha_config['GRECAPTCHA_SITE_KEY'],
                    'is_ajax' => (bool)Tools::getIsset('ajax'),
                    'is_17' => (bool)version_compare(_PS_VERSION_, '1.7', '>=')
                )
            );

            return $this->display(__FILE__, 'recaptcha-widget.tpl');
        }
    }

    public function getConfigurations()
    {
        return array_merge(self::$captcha_config, self::$error_messages);
    }

    public function hasSpamWordsFound($message)
    {
        $words = explode(',', self::$captcha_config['GRECAPTCHA_SPAM_KEYS']);
        foreach ($words as $word) {
            if (!empty($word) && strpos($message, $word) !== false) {
                return true;
            }
        }

        return false;
    }

    public function isEmailDomainAllowed($email)
    {
        $domain_name = Tools::strtolower(Tools::substr(strrchr($email, "@"), 1));
        $sql = 'SELECT id_grecaptcha_domain FROM '.
                _DB_PREFIX_.'grecaptcha_domains WHERE LOWER(domain) = "'.pSQL(Tools::strtolower($domain_name)).'"';
        return !(bool)Db::getInstance()->getValue($sql);
    }

    protected function getAllDomains()
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'grecaptcha_domains ORDER BY id_grecaptcha_domain';
        return Db::getInstance()->executeS($sql);
    }

    private function loadConfig()
    {
        self::$captcha_config = $this->getConfigFormValues();
        if (self::$captcha_config['GRECAPTCHA_VERSION'] == 3) {
            $render = null;
            if (in_array($this->context->controller->controller_type, array('front', 'modulefront'))) {
                $render = 'explicit';
            } else {
                $render = self::$captcha_config['GRECAPTCHA_SITE_KEY'];
            }

            self::$recaptcha_js_api .= '&render='.$render;
            self::$file_path = '-v3';
        }

        self::$error_messages = array(
            'MESSAGE_BLOCKED' => $this->l('You are not allowed to send a message, please contact our customer support'),
            'EMAIL_DOMAIN_BLOCKED' => $this->l('This email domain is blocked'),
            'CAPTCHA_FAILED' => $this->l('Please complete the captcha'),
        );

        if (self::$captcha_config['GRECAPTCHA_VERSION'] == 3) {
            self::$error_messages['CAPTCHA_FAILED'] = $this->l('Invalid captcha response, please try again');
        }
    }

    public function verifyCaptcha($response)
    {
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array(
            'secret' => self::$captcha_config['GRECAPTCHA_SECRET_KEY'],
            'response' => $response
        );
        $options = array(
            'http' => array (
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $verify = Tools::file_get_contents($url, false, $context);
        $captcha_success = json_decode($verify);
        return $captcha_success->success;
    }
}
