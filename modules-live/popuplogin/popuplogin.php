<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2016 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

if (!defined('_PS_VERSION_'))
{
    exit;
}

class popuplogin extends Module
{
    public function __construct()
    {
        $this->name = 'popuplogin';
        $this->tab = 'front_office_features';
        $this->version = '1.5.1';
        $this->author = 'MyPresta.eu';
        parent::__construct();
        $this->trusted();
        $this->displayName = $this->l('PopUp Login Block');
        $this->description = $this->l('Nice looking popup login block for your prestashop');

        $this->mkey = "nlc";
        $this->checkforupdates();

        //ajax variables
        $this->lemailaddress = $this->l('E-mail address');
        $this->lpassword = $this->l('Password');
        $this->llogin = $this->l('Login');
        $this->lletmein = $this->l('Let me in');
        $this->fpassword = $this->l('Forgot your password?');
        $this->raccount = $this->l('Register');
    }

    public function checkforupdates($display_msg = 0, $form = 0)
    {
        // ---------- //
        // ---------- //
        // VERSION 12 //
        // ---------- //
        // ---------- //
        if (@file_exists('../modules/' . $this->name . '/key.php'))
        {
            @require_once('../modules/' . $this->name . '/key.php');
        }
        else
        {
            if (@file_exists(dirname(__FILE__) . $this->name . '/key.php'))
            {
                @require_once(dirname(__FILE__) . $this->name . '/key.php');
            }
            else
            {
                if (@file_exists('modules/' . $this->name . '/key.php'))
                {
                    @require_once('modules/' . $this->name . '/key.php');
                }
            }
        }
        if ($form == 1)
        {
            return '
            <div class="panel" id="fieldset_myprestaupdates" style="margin-top:20px;">
            ' . ($this->psversion() == 6 || $this->psversion() == 7 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('MyPresta updates') . '</div>' : '') . '
			<div class="form-wrapper" style="padding:0px!important;">
            <div id="module_block_settings">
                    <fieldset id="fieldset_module_block_settings">
                         ' . ($this->psversion() == 5 ? '<legend style="">' . $this->l('MyPresta updates') . '</legend>' : '') . '
                        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <label>' . $this->l('Check updates') . '</label>
                            <div class="margin-form">' . (Tools::isSubmit('submit_settings_updates_now') ? ($this->inconsistency(0) ? '' : '') . $this->checkforupdates(1) : '') . '
                                <button style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" class="button btn btn-default" />
                                <i class="process-icon-update"></i>
                                ' . $this->l('Check now') . '
                                </button>
                            </div>
                            <label>' . $this->l('Updates notifications') . '</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">' . $this->l('-- select --') . '</option>
                                    <option value="1" ' . ((int)(Configuration::get('mypresta_updates') == 1) ? 'selected="selected"' : '') . '>' . $this->l('Enable') . '</option>
                                    <option value="0" ' . ((int)(Configuration::get('mypresta_updates') == 0) ? 'selected="selected"' : '') . '>' . $this->l('Disable') . '</option>
                                </select>
                                <p class="clear">' . $this->l('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.') . '</p>
                            </div>
                            <label>' . $this->l('Module page') . '</label>
                            <div class="margin-form">
                                <a style="font-size:14px;" href="' . $this->mypresta_link . '" target="_blank">' . $this->displayName . '</a>
                                <p class="clear">' . $this->l('This is direct link to official addon page, where you can read about changes in the module (changelog)') . '</p>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="submit_settings_updates"class="button btn btn-default pull-right" />
                                <i class="process-icon-save"></i>
                                ' . $this->l('Save') . '
                                </button>
                            </div>
                        </form>
                    </fieldset>
                    <style>
                    #fieldset_myprestaupdates {
                        display:block;clear:both;
                        float:inherit!important;
                    }
                    </style>
                </div>
            </div>
            </div>';
        }
        else
        {
            if (defined('_PS_ADMIN_DIR_'))
            {
                if (Tools::isSubmit('submit_settings_updates'))
                {
                    Configuration::updateValue('mypresta_updates', Tools::getValue('mypresta_updates'));
                }
                if (Configuration::get('mypresta_updates') != 0 || (bool)Configuration::get('mypresta_updates') == false)
                {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200))
                    {
                        $actual_version = popuploginUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (popuploginUpdate::version($this->version) < popuploginUpdate::version(Configuration::get('updatev_' . $this->name)))
                    {
                        $this->warning = $this->l('New version available, check http://MyPresta.eu for more informations');
                    }
                }
                if ($display_msg == 1)
                {
                    if (popuploginUpdate::version($this->version) < popuploginUpdate::version(popuploginUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version)))
                    {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    }
                    else
                    {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public function trusted()
    {
        if (_PS_VERSION_ >= "1.6.0.8")
        {
            if (isset($_GET['controller']))
            {
                if ($_GET['controller'] == "AdminModules")
                {
                    if (_PS_VERSION_ >= "1.6.0.8")
                    {
                        if (isset($_GET['controller']))
                        {
                            if ($_GET['controller'] == "AdminModules")
                            {
                                $this->context->controller->addJS(($this->_path) . 'trusted.js', 'all');
                            }
                        }
                    }
                }
            }
        }
        if (defined('_PS_HOST_MODE_'))
        {
            if (isset($_GET['controller']))
            {
                if ($_GET['controller'] == "AdminModules")
                {
                    if (defined('self::CACHE_FILE_TRUSTED_MODULES_LIST') == true)
                    {
                        $context = Context::getContext();
                        $theme = new Theme($context->shop->id_theme);
                        $xml = simplexml_load_string(file_get_contents(_PS_ROOT_DIR_ . self::CACHE_FILE_TRUSTED_MODULES_LIST));
                        if ($xml)
                        {
                            $css = $xml->modules->addChild('module');
                            $css->addAttribute('name', $this->name);
                            $xmlcode = $xml->asXML();
                            if (!strpos(file_get_contents(_PS_ROOT_DIR_ . self::CACHE_FILE_TRUSTED_MODULES_LIST), $this->name))
                            {
                                if (file_exists(_PS_ROOT_DIR_ . self::CACHE_FILE_TRUSTED_MODULES_LIST))
                                {
                                    file_put_contents(_PS_ROOT_DIR_ . self::CACHE_FILE_TRUSTED_MODULES_LIST, $xmlcode);
                                }
                            }
                        }
                    }
                    if (defined('self::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST') == true)
                    {
                        $xml = simplexml_load_string(file_get_contents(_PS_ROOT_DIR_ . self::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST));
                        //$xml=new SimpleXMLElement('<modules/>');
                        //$cs=$xml->addChild('modules');
                        if ($xml)
                        {
                            $css = $xml->addChild('module');
                            $css->addChild('id', 0);
                            $css->addChild('name', "<![CDATA[" . $this->name . "]]>");
                            $xmlcode = $xml->asXML();
                            $xmlcode = str_replace('&lt;', "<", $xmlcode);
                            $xmlcode = str_replace('&gt;', ">", $xmlcode);
                            if (!strpos(file_get_contents(_PS_ROOT_DIR_ . self::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST), $this->name))
                            {
                                if (file_exists(_PS_ROOT_DIR_ . self::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST))
                                {
                                    file_put_contents(_PS_ROOT_DIR_ . self::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST, $xmlcode);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function install()
    {
        if (!parent::install() || ($this->psversion() != 4 && $this->psversion() != 5 ? !$this->registerHook('dashboardZoneOne'):true) || !Configuration::updateValue('ppl_design', '1') || !$this->registerHook('header') || !$this->registerNewHook('popuplogin') || !Configuration::updateValue('mypresta_updates', 1))
        {
            return false;
        }
        return true;
    }

    public function registerNewHook($hook)
    {
        if ($this->psversion() == 5 || $this->psversion() == 6 || $this->psversion() == 7)
        {
            if (Hook::getIdByName(preg_replace("/[^\da-z]/i", '', trim($hook))) == false)
            {
                $newhook = new Hook();
                $newhook->name = preg_replace("/[^\da-z]/i", '', trim(preg_replace("/[^\da-z]/i", '', trim($hook))));
                $newhook->live_edit = 1;
                $newhook->position = 1;
                $newhook->add();
                $this->registerHook(preg_replace("/[^\da-z]/i", '', trim(preg_replace("/[^\da-z]/i", '', trim($hook)))));
            }
        }
        elseif ($this->psversion() == 4)
        {
            if ($this->getIdByName(preg_replace("/[^\da-z]/i", '', trim($hook))) == false)
            {
                $newhook = new Hook();
                $name = preg_replace("/[^\da-z]/i", '', trim(preg_replace("/[^\da-z]/i", '', trim($hook))));
                $newhook->name = $name;
                $newhook->title = $name;
                $newhook->live_edit = 1;
                $newhook->position = 1;
                $newhook->add();
                $this->registerHook(preg_replace("/[^\da-z]/i", '', trim(preg_replace("/[^\da-z]/i", '', trim($hook)))));
            }
        }
        return true;
    }

    public static function getIdByName($hook_name)
    {
        $hook_name = strtolower($hook_name);
        if (!Validate::isHookName($hook_name))
        {
            return false;
        }

        $cache_id = 'hook_idsbyname';
        $hook_ids = array();
        $result = Db::getInstance()->ExecuteS('
			SELECT `id_hook`, `name`
			FROM `' . _DB_PREFIX_ . 'hook`');
        foreach ($result as $row)
        {
            return (isset($hook_ids[$hook_name]) ? $hook_ids[$hook_name] : false);
        }
    }

    public function runhook($hook)
    {
        if ($this->psversion() == 5 || $this->psversion() == 6)
        {
            return Hook::exec($hook);
        }
        elseif ($this->psversion() == 4)
        {
            return Module::Hookexec($hook);
        }
    }

    public function psversion()
    {
        $version = _PS_VERSION_;
        $exp = $explode = explode(".", $version);
        return $exp[1];
    }

    public function getContent()
    {
        $output = "";
        if (isset($_POST['popupdesign']))
        {
            Configuration::updateValue('ppl_design', Tools::getValue('popupdesign'));
        }

        if (Tools::isSubmit('register_settings'))
        {
            Configuration::updateValue('popuplogin_return', Tools::getValue('popuplogin_return'));
            Configuration::updateValue('popuplogin_register', Tools::getValue('popuplogin_register'));
        }
        return $output . $this->displayForm();
    }

    public function displayForm()
    {

        $form = '
        <fieldset id="myfields" style="display:block; width:650px; vertical-align:top;">
            <legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />' . $this->l('Select your login block design') . '</legend>
            <form id="selectlogindesign" name="selectlogindesign"  method="post"/>
            <table style="width:100%; text-align:center;">
                <tr>
                    <td><img onclick="$(\'#popupdesign1\').attr(\'checked\',true)" class="imagedropshadow" src="' . $this->_path . 'img/login-block-1.png" /><br/><input type="radio" name="popupdesign" id="popupdesign1" value="1" ' . (Configuration::get('ppl_design') == 1 ? 'checked="yes"' : '') . '/></td>
                    <td></td>
                    <td><img onclick="$(\'#popupdesign2\').attr(\'checked\',true)" class="imagedropshadow" src="' . $this->_path . 'img/login-block-2.png" /><br/><input type="radio" name="popupdesign" id="popupdesign2" value="2" ' . (Configuration::get('ppl_design') == 2 ? 'checked="yes"' : '') . '/></td>
                </tr>
                <tr>
                    <td><img onclick="$(\'#popupdesign3\').attr(\'checked\',true)" class="imagedropshadow" src="' . $this->_path . 'img/login-block-3.png" /><br/><input type="radio" name="popupdesign" id="popupdesign3" value="3" ' . (Configuration::get('ppl_design') == 3 ? 'checked="yes"' : '') . '/></td>
                    <td></td>
                    <td><img onclick="$(\'#popupdesign4\').attr(\'checked\',true)" class="imagedropshadow" src="' . $this->_path . 'img/login-block-4.png" /><br/><input type="radio" name="popupdesign" id="popupdesign4" value="4" ' . (Configuration::get('ppl_design') == 4 ? 'checked="yes"' : '') . '/></td>
                </tr>
                <tr>
                    <td><img onclick="$(\'#popupdesign5\').attr(\'checked\',true)" class="imagedropshadow" src="' . $this->_path . 'img/login-block-5.png" /><br/><input type="radio" name="popupdesign" id="popupdesign5" value="5" ' . (Configuration::get('ppl_design') == 5 ? 'checked="yes"' : '') . '/></td>
                    <td></td>
                    <td><img onclick="$(\'#popupdesign6\').attr(\'checked\',true)" class="imagedropshadow" src="' . $this->_path . 'img/login-block-6.png" /><br/><input type="radio" name="popupdesign" id="popupdesign6" value="6" ' . (Configuration::get('ppl_design') == 6 ? 'checked="yes"' : '') . '/></td>
                </tr>
                <tr>
                    <td><img onclick="$(\'#popupdesign7\').attr(\'checked\',true)" class="imagedropshadow" src="' . $this->_path . 'img/login-block-7.png" /><br/><input type="radio" name="popupdesign" id="popupdesign7" value="7" ' . (Configuration::get('ppl_design') == 7 ? 'checked="yes"' : '') . '/></td>
                    <td></td>
                    <td><img onclick="$(\'#popupdesign8\').attr(\'checked\',true)" class="imagedropshadow" src="' . $this->_path . 'img/login-block-8.png" /><br/><input type="radio" name="popupdesign" id="popupdesign8" value="8" ' . (Configuration::get('ppl_design') == 8 ? 'checked="yes"' : '') . '/></td>
                </tr>
            </table>
                <a onclick="selectlogindesign.submit();" style="margin:auto; margin-bottom:15px;" href="#" class="push_button red">' . $this->l('Save changes!') . '</a>
            </form>
            <div class="mywarning alert-box">
            ' . $this->l('By default PrestaShop for each "log in" link uses class="login". If you use non default theme - make sure that your login buttons have got class="login" param') . '
            </div>      
        </fieldset>';

        $form.='    <div style="display:block; width:650px; vertical-align:top;">
                        <form action="' . Tools::safeOutput($_SERVER['REQUEST_URI']) . '" method="post">
                            <fieldset style="margin-top:10px;">
                                <legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />' . $this->l('Login settings') . '</legend>
                                <div style="clear:both;">
                                    <label>' . $this->l('After login redirect customer to:') . '</label>
                    			    <div class="margin-form">
                                        <select name="popuplogin_return">
                                            <option>' . $this->l('-- SELECT --') . '</option>
                                            <option value="1" ' . (Configuration::get('popuplogin_return') == 1 ? 'selected' : '') . '>' . $this->l('My account') . '</option>
                                            <option value="2" ' . (Configuration::get('popuplogin_return') == 2 ? 'selected' : '') . '>' . $this->l('Homepage') . '</option>
                                            <option value="3" ' . (Configuration::get('popuplogin_return') == 3 ? 'selected' : '') . '>' . $this->l('Stay on the same page') . '</option>
                                        </select>
                    				</div>
                                </div>
                                <div style="clear:both; margin-bottom:20px;">
                                    <label>' . $this->l('Display register link:') . '</label>
                    			    <div class="margin-form">
                                        <select name="popuplogin_register">
                                            <option>' . $this->l('-- SELECT --') . '</option>
                                            <option value="1" ' . (Configuration::get('popuplogin_register') == 1 ? 'selected' : '') . '>' . $this->l('Yes') . '</option>
                                            <option value="0" ' . (Configuration::get('popuplogin_register') == 0 ? 'selected' : '') . '>' . $this->l('No') . '</option>
                                        </select>
                    				</div>
                                </div>
                				<center><input type="submit" name="register_settings" value="' . $this->l('Save') . '" class="button" /></center>
                            </fieldset>
                        </form>
                    </div>';

        return "
        <div id='cssmenu'>
            <ul>
               <li class='active'><a><span>" . $this->displayName . " " . $this->version . "</span></a></li>
               <li class=''><a href='http://mypresta.eu/contact.html' target='_blank'><span>" . $this->l('Support') . "</span></a></li>
               <li class=''><a href='http://mypresta.eu/modules/front-office-features/popup-login-pro.html' target='_blank'><span>" . $this->l('Updates') . "</span></a></li>
               <li style='position:relative; display:inline-block; float:right; '><a href='http://mypresta.eu' target='_blank' title='prestashop modules'><img src='../modules/popuplogin/logo-white.png' alt='prestashop modules' style=\"position:absolute; top:17px; right:16px;\"/></a></li>
            </ul>
        </div>" . '
        <link href="//fonts.googleapis.com/css?family=Oswald" rel="stylesheet" type="text/css">
        <link href="../modules/' . $this->name . '/css.css" rel="stylesheet" type="text/css" />' . $form;
    }

    public function hookpopuplogin()
    {
        return $this->display(__FILE__, 'displayPopUpLogin.tpl');
    }

    public function hookHeader()
    {
        Media::addJsDef(array('signUpCgMsg' => $this->l('You have to accept the general terms of use and the privacy policy')));

        if ($this->psversion() == 4)
        {
            Tools::addCSS(_PS_CSS_DIR_ . 'jquery.fancybox-1.3.4.css', 'screen');
            Tools::addJS(_PS_JS_DIR_ . 'jquery/jquery.fancybox-1.3.4.js');
            Tools::addJS(($this->_path) . 'popuplogin.js', 'all');
        }

        if ($this->psversion() == 5 || $this->psversion() == 6 || $this->psversion() == 7)
        {
            $this->context->controller->addCSS(_PS_CSS_DIR_ . 'jquery.fancybox-1.3.4.css', 'screen');
            $this->context->controller->addJqueryPlugin('fancybox');
            $this->context->controller->addJS(($this->_path) . 'popuplogin.js', 'all');
            //$this->context->controller->addCSS(($this->_path).'popuplogin.css', 'all');
        }

        if ($this->psversion() == 7)
        {
            $this->context->smarty->assign(array('ps_version' => $this->psversion()));
            return $this->display(__file__, 'header.tpl');
        }
    }

    public function hookdashboardZoneOne($params)
    {
        $this->checkforupdates(0,0);
        $this->context->smarty->assign('update_availablility', (isset($this->warning) ? $this->warning:false));
        return $this->display(__file__, 'dashboardZoneOne.tpl');
    }

}


class popuploginUpdate extends popuplogin
{
    public static function version($version)
    {
        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 3)
        {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 2)
        {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 1)
        {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 0)
        {
            $version = (int)$version . "0000";
        }
        return (int)$version;
    }

    public static function encrypt($string)
    {
        return base64_encode($string);
    }

    public static function verify($module, $key, $version)
    {
        if (ini_get("allow_url_fopen"))
        {
            if (function_exists("file_get_contents"))
            {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module=' . $module . "&version=" . self::encrypt($version) . "&lic=$key&u=" . self::encrypt(_PS_BASE_URL_ . __PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_" . $module, date("U"));
        Configuration::updateValue("updatev_" . $module, $actual_version);
        return $actual_version;
    }
}