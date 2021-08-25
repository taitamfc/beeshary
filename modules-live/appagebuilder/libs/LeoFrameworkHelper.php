<?php
/**
 * 2007-2015 Apollotheme
 *
 * NOTICE OF LICENSE
 *
 * ApPageBuilder is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Apollotheme <apollotheme@gmail.com>
 *  @copyright 2007-2015 Apollotheme
 *  @license   http://apollotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}
if (!class_exists("LeoFrameworkHelper")) {

    /**
     * LeoFrameworkHelper Class
     */
    class LeoFrameworkHelper
    {
        /**
         * @var Array $overrideHooks;
         *
         * @access protected
         */
        protected $overrideHooks = array();
        /**
         * @var String $activedTheme
         *
         * @access protected
         */
        protected $activedTheme = '';
        /**
         * @var boolean $isLangRTL
         *
         * @access protected
         */
        protected $isLangRTL = false;
        protected $cparams = array();
        protected $fonts = array();

        /**
         * get instance of current object
         */
        public static function getInstance()
        {
            static $_instance;
            if (!$_instance) {
                $_instance = new LeoFrameworkHelper();
            }
            return $_instance;
        }

        public function __construct()
        {
            
        }

//        public static function getHookPositions()
//        {
//
//            $hookspos = array(
//                'displayNav',
//                'displayTop',
//                'displayHeaderRight',
//                'displaySlideshow',
//                'topNavigation',
//                'displayTopColumn',
//                'displayRightColumn',
//                'displayLeftColumn',
//                'displayHome',
//                'displayFooter',
//                'displayBottom',
//                'displayContentBottom',
//                'displayFootNav',
//                'displayFooterTop',
//                'displayFooterBottom'
//            );
//            return $hookspos;
//        }

        /**
         * Set actived theme and language direction
         */
        public function setActivedTheme($theme, $isRTL = false)
        {
            $this->activedTheme = $theme;
            $this->isLangRTL = $isRTL;
            return $this;
        }

        public function developmentMode($dDevMode, $skin)
        {
            global $cookie;

            $cssFiles = array();
            $theme = $this->activedTheme;
            /* export direct to stylesheet folder of  the theme */

            $themeDir = _PS_ALL_THEMES_DIR_.$theme;
            $cssFolder = _PS_ALL_THEMES_DIR_.$theme.'/css/';
            $lessDevURL = __PS_BASE_URI__.'cache/'.$theme.'/';
            $themeURL = '';
            require_once(_PS_MODULE_DIR_.'leotempcp/libs/lessparser.php');
            $lessparser = new LeoLessParser($themeDir.'/development/', $themeDir, $lessDevURL, $themeURL, $cssFolder);

            if ($dDevMode == 'compile-export') {
                if (Tools::isSubmit('exportless')) {
                    $lessparser->setLastTimeChanged(time())->compileLess();
                } else {
                    $lessparser->compileLess();
                }
            } else {
                /* export direct to stylesheet to cache folder */
                $lessDevDir = _PS_CACHE_DIR_.$theme.'/';
                if (!is_dir($lessDevDir)) {
                    mkdir($lessDevDir, 0755, true);
                }
                $cssFiles = $lessparser->setDevelopmentMode($lessDevDir)->compileLessDevelopment($skin, $this->isLangRTL);
            }
            return $cssFiles;
        }

        public static function getIntelnalModule($theme)
        {
            $xml = _PS_ALL_THEMES_DIR_.$theme.'/development/customize/module.xml';
            $output = array();


            if (file_exists($xml)) {
                libxml_use_internal_errors(true);
                $xml = simplexml_load_file($xml, null, LIBXML_NOCDATA);

                if (isset($xml->module)) {
                    $xml = get_object_vars($xml);


                    if (is_array($xml['module'])) {
                        foreach ($xml['module'] as $module) {
                            $tmp = get_object_vars($module);
                            $output[$tmp['key']] = $tmp;
                        }
                    } else {
                        $module = get_object_vars($xml['module']);
                        $output[trim($module['key'])] = $module;
                    }
                }
            }
            return $output;
        }

        /**
         * save data into framework
         */
        public static function writeToCache($folder, $file, $value, $e = 'css')
        {
            $file = $folder.preg_replace('/[^A-Z0-9\._-]/i', '', $file).'.'.$e;
            $handle = fopen($file, 'w+');
            fwrite($handle, ($value));
            fclose($handle);
        }

        /**
         *  auto load all css file local folder
         */
        public function loadLocalCss()
        {
            return $this->getFileList(_PS_ALL_THEMES_DIR_.$this->activedTheme.'/css/local/', '.css');
        }

        /**
         *  auto load all js file local folder
         */
        public function loadLocalJs()
        {
            return $this->getFileList(_PS_ALL_THEMES_DIR_.$this->activedTheme.'/js/local/', '.js');
        }

        public static function getThemeInfo($theme)
        {
            $xml = _PS_ALL_THEMES_DIR_.$theme.'/config.xml';

            $output = array();

            if (file_exists($xml)) {
                $output = simplexml_load_file($xml);
            }

            return $output;
        }

        public static function getLayoutSettingByTheme($theme)
        {
            $xml = _PS_ALL_THEMES_DIR_.$theme.'/development/customize/layout.xml';

            $output = array();

            if (file_exists($xml)) {
                $info = simplexml_load_file($xml);


                if (isset($info->layout)) {
                    foreach ($info->layout as $layouts) {
                        $vars = get_object_vars($layouts);

                        if (is_object($vars['item'])) {

                            $tmp = get_object_vars($vars['item']);
                            $block = $tmp['block'];
                            if (is_object($tmp['option'])) {
                                $options = $tmp['option'];
                                $tmp['option'] = array();
                                $tmp['option'][] = get_object_vars($options);
                            } else {
                                foreach ($tmp['option'] as $key => $o) {
                                    $tmp['option'][$key] = get_object_vars($o);
                                }
                            }
                            unset($tmp['block']);
                            $vars['layout'][$block] = $tmp;
                        } else {
                            foreach ($vars['item'] as $selector) {
                                $tmp = get_object_vars($selector);


                                if (is_array($tmp) && !empty($tmp)) {
                                    $block = $tmp['block'];
                                    unset($tmp['block']);
                                    if (is_object($tmp['option'])) {
                                        $options = $tmp['option'];
                                        $tmp['option'] = array();
                                        $tmp['option'][] = get_object_vars($options);
                                    } else {
                                        foreach ($tmp['option'] as $key => $o) {
                                            $tmp['option'][$key] = get_object_vars($o);
                                        }
                                    }

                                    $vars['layout'][$block] = $tmp;
                                }
                            }
                        }
                        unset($vars['item']);
                        $output = $vars;
                    }
                }
            }

            return $output;
        }

        public static function getPanelConfigByTheme($fileName, $theme)
        {
            $xml = _PS_ALL_THEMES_DIR_.$theme.'/development/customize/'.$fileName.'.xml';
            $output = array();
            if (file_exists($xml)) {
                $info = simplexml_load_file($xml);
                if (isset($info->configs)) {
                    foreach ($info->configs as $header) {
                        $vars = get_object_vars($header);
                        if (is_object($vars['item'])) {
                            $tmp = get_object_vars($vars['item']);
                            $block = $tmp['block'];
                            if (is_object($tmp['option'])) {
                                $options = $tmp['option'];
                                $tmp['option'] = array();
                                $tmp['option'][] = get_object_vars($options);
                            } else {
                                foreach ($tmp['option'] as $key => $o) {
                                    $tmp['option'][$key] = get_object_vars($o);
                                }
                            }
                            unset($tmp['block']);
                            $vars['configs'][$block] = $tmp;
                        } else {
                            foreach ($vars['item'] as $selector) {
                                $tmp = get_object_vars($selector);
                                if (is_array($tmp) && !empty($tmp)) {
                                    $block = $tmp['block'];
                                    unset($tmp['block']);
                                    if (is_object($tmp['option'])) {
                                        $options = $tmp['option'];
                                        $tmp['option'] = array();
                                        $tmp['option'][] = get_object_vars($options);
                                    } else {
                                        foreach ($tmp['option'] as $key => $o) {
                                            $tmp['option'][$key] = get_object_vars($o);
                                        }
                                    }
                                    $vars['configs'][$block] = $tmp;
                                }
                            }
                        }
                        unset($vars['item']);
                        $output = $vars;
                    }
                }
            }

            return $output;
        }

        public function getParam($key, $value = "")
        {
            return $this->cparams[$this->activedTheme."_".$key];
        }

        /**
         * trigger to process user paramters using for demostration
         */
        public function triggerUserParams($params)
        {
            if (Tools::getIsset('btn-leo-reset')) {
                foreach ($params as $param) {
                    $kc = $this->activedTheme."_".$param;
                    $this->cparams[$kc] = null;
                    setcookie($kc, null, 0, '/');
                    if (isset($_COOKIE[$kc])) {
                        $this->cparams[$kc] = null;
                        $_COOKIE[$kc] = null;
                    }
                }
            }

            $data = Tools::getValue('userparams');

            $exp = time() + 60 * 60 * 24 * 355;
            foreach ($params as $param) {
                $kc = $this->activedTheme."_".$param;
                $this->cparams[$kc] = '';
                if ($data) {
                    if (isset($data[$param])) {
                        setcookie($kc, $data[$param], $exp, '/');
                        $this->cparams[$kc] = $data[$param];
                    }
                }
                if (isset($_COOKIE[$kc])) {
                    $this->cparams[$kc] = $_COOKIE[$kc];
                }
            }

            if (isset($data['user_setting']) && $data['user_setting'] == 1) {
                Tools::redirect($this->getURI());
            }
        }

        public function loadLocalFont()
        {
            $this->fonts = array(
                'Verdana' => 'Verdana, Geneva, sans-serif',
                'Georgia' => 'Georgia, "Times New Roman", Times, serif',
                'Arial' => 'Arial, Helvetica, sans-serif',
                'Impact' => 'Impact, Arial, Helvetica, sans-serif',
                'Tahoma' => 'Tahoma, Geneva, sans-serif',
                'Trebuchet' => '"Trebuchet MS", Arial, Helvetica, sans-serif',
                'Arial' => '"Arial Black", Gadget, sans-serif',
                'Times' => 'Times, "Times New Roman", serif',
                'Palatino' => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
                'Lucida' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
                'MS' => '"MS Serif", "New York", serif',
                'Comic' => '"Comic Sans MS", cursive',
                'Courier' => '"Courier New", Courier, monospace',
                'Lucida' => '"Lucida Console", Monaco, monospace'
            );
            return $this;
        }

        public function renderFontTagHeader($engine, $lfont, $glink, $gfont, $selector)
        {
            $output = '';
            if ($engine == 'google') {
                if (!empty($glink) && !empty($gfont)) {
                    $output = '<link rel="stylesheet" type="text/css" href="'.trim($glink).'" media="screen" />';
                    $output .= '<style type="text/css">'.trim($selector)." { font-family:".trim($gfont)." } </style> ";
                }
            } else {
                $fontfamily = isset($this->fonts[trim($lfont)]) ? $this->fonts[trim($lfont)] : $lfont;
                $output .= '<style type="text/css">'.trim($selector)." { font-family:".$fontfamily." } </style> ";
            }
            return $output;
        }

        /**
         * Generate into file css
         */
        public function renderFontTagHeaderCSS($engine, $lfont, $glink, $gfont, $selector)
        {
            $output = '';

            if ($engine == 'google') {
                if (!empty($glink) && !empty($gfont)) {
                    $output = '@import url("'.trim($glink).'");' ."\n";
                    $output .= trim($selector)." { font-family:".trim($gfont)." }\n\n";
                }
            } else {
                $fontfamily = isset($this->fonts[trim($lfont)]) ? $this->fonts[trim($lfont)] : $lfont;
                $output .= trim($selector)." { font-family:".$fontfamily." }\n\n";
            }
            return $output;
        }
        
        /**
         * get URI with http or https
         */
        public function getURI()
        {

            $useSSL = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
            $protocol_content = ($useSSL) ? 'https://' : 'http://';

            return $protocol_content.Tools::getHttpHost().__PS_BASE_URI__;
        }


        /**
         * load override Hooks following actived theme
         */
        public function loadOverridedHooks($shopId)
        {

            $overrideHooks = array();

            $sql = 'SELECT * FROM `'._DB_PREFIX_.'leohook` WHERE theme="'.pSQL($this->activedTheme).'" AND id_shop='.(int)$shopId;
            $result = Db::getInstance()->executeS($sql);
            if ($result)
                foreach ($result as $row) {
                    $overrideHooks[$row['id_module']] = $row['name_hook'];
                }
            $this->overrideHooks = $overrideHooks;

            return $this;
        }

        /**
         * get list of filename inside folder
         */
        public static function getFileList($path, $e = null, $nameOnly = false)
        {
            $output = array();
            $directories = glob($path.'*'.$e);
            if ($directories)
                foreach ($directories as $dir) {
                    $dir = basename($dir);
                    if ($nameOnly) {
                        $dir = str_replace($e, '', $dir);
                    }

                    $output[$dir] = $dir;
                }

            return $output;
        }

        public static function getUserProfiles($theme)
        {

            $folder = _PS_ALL_THEMES_DIR_.$theme.'/css/customize/*.css';
            $dirs = glob($folder);
            $output = array();
            if ($dirs)
                foreach ($dirs as $dir) {
                    $file = str_replace(".css", "", basename($dir));
                    $output[] = array("skin" => $file, "name" => (Tools::ucfirst($file)));
                }

            return $output;
        }

        public static function getLayoutDirections($theme)
        {
            $folder = _PS_ALL_THEMES_DIR_.$theme.'/layout/*';
            $dirs = glob($folder, GLOB_ONLYDIR);
            $output = array();
            foreach ($dirs as $dir) {
                $file = str_replace(".scss", "", basename($dir));
                $output[] = array("id" => $file, "name" => (Tools::ucfirst($file)));
            }

            return $output;
        }

        public static function getSkins()
        {
            $folders = array();
            if (!apPageHelper::isRelease()) {
                $folders[] = _PS_THEME_DIR_.'assets/css/'.apPageHelper::getCssDir().'skins/*';
            }
            $folders[] = _PS_THEME_DIR_.apPageHelper::getCssDir().'skins/*';
            $output = array();
            foreach ($folders as $folder) {
                $dirs = glob($folder, GLOB_ONLYDIR);
                $output = array();
                if ($dirs) {
                    $i = 0;
                    foreach ($dirs as $dir) {
                        $output[$i]['id'] = basename($dir);
                        $output[$i]['name'] = Tools::ucfirst(basename($dir));
                        $skinFileUrl = apPageHelper::getUriFromPath($dir).'/';

                        if (file_exists($dir.'/icon.png')) {
                            $output[$i]['icon'] = $skinFileUrl.'icon.png';
                        }
                        $output[$i]['css'] = $skinFileUrl;

                        $isRTL = Context::getContext()->language->is_rtl;
                        if ($isRTL && file_exists($dir.'/custom-rtl.css')) {
                            $output[$i]['rtl'] = 1;
                        } else {
                            $output[$i]['rtl'] = 0;
                        }
                        $i++;
                    }
                }
                if (!empty($output)) {
                    break;
                }
            }
            return $output;
        }

        /**
         *
         */
        public static function renderEdtiorThemeForm($theme)
        {
            $customizeXML = _PS_ALL_THEMES_DIR_.$theme.'/development/customize/themeeditor.xml';

            $output = array('selectors' => array(), 'elements' => array());
            if (file_exists($customizeXML)) {
                $info = simplexml_load_file($customizeXML);
                if (isset($info->selectors->items)) {
                    foreach ($info->selectors->items as $item) {
                        $vars = get_object_vars($item);
                        if (is_object($vars['item'])) {
                            $tmp = get_object_vars($vars['item']);
                            $vars['selector'][] = $tmp;
                        } else {
                            foreach ($vars['item'] as $selector) {
                                $tmp = get_object_vars($selector);
                                if (is_array($tmp) && !empty($tmp)) {
                                    $vars['selector'][] = $tmp;
                                }
                            }
                        }
                        unset($vars['item']);
                        $output['selectors'][$vars['match']] = $vars;
                    }
                }

                if (isset($info->elements->items)) {
                    foreach ($info->elements->items as $item) {
                        $vars = get_object_vars($item);
                        if (is_object($vars['item'])) {
                            $tmp = get_object_vars($vars['item']);
                            $vars['selector'][] = $tmp;
                        } else {
                            foreach ($vars['item'] as $selector) {
                                $tmp = get_object_vars($selector);
                                if (is_array($tmp) && !empty($tmp)) {
                                    $vars['selector'][] = $tmp;
                                }
                            }
                        }
                        unset($vars['item']);
                        $output['elements'][$vars['match']] = $vars;
                    }
                }
            }

            return $output;
        }

        /**
         * Execute modules for specified hook
         *
         * @param string $hook_name Hook Name
         * @param array $hook_args Parameters for the functions
         * @param int $id_module Execute hook for this module only
         * @return string modules output
         */
        public function exec($hook_name, $hook_args = array(), $id_module = null)
        {

            // Check arguments validity
            if (($id_module && !is_numeric($id_module)) || !Validate::isHookName($hook_name)) {
                throw new PrestaShopException('Invalid id_module or hook_name');
            }

            // If no modules associated to hook_name or recompatible hook name, we stop the function

            if (!$module_list = Hook::getHookModuleExecList($hook_name)) {
                return '';
            }

            // Check if hook exists
            if (!$id_hook = Hook::getIdByName($hook_name)) {
                return false;
            }

            // Store list of executed hooks on this page
            Hook::$executed_hooks[$id_hook] = $hook_name;

            $live_edit = false;
            $context = Context::getContext();
            if (!isset($hook_args['cookie']) || !$hook_args['cookie']) {
                $hook_args['cookie'] = $context->cookie;
            }
            if (!isset($hook_args['cart']) || !$hook_args['cart']) {
                $hook_args['cart'] = $context->cart;
            }

            $retro_hook_name = Hook::getRetroHookName($hook_name);

            // Look on modules list
            $altern = 0;
            $output = '';
            foreach ($module_list as $array) {

                // Check errors
                if ($id_module && $id_module != $array['id_module'])
                    continue;
                if (!($moduleInstance = Module::getInstanceByName($array['module'])))
                    continue;


                // Check permissions
                $exceptions = $moduleInstance->getExceptions($array['id_hook']);
                if (in_array(Dispatcher::getInstance()->getController(), $exceptions)) {
                    continue;
                }
                if (Validate::isLoadedObject($context->employee) && !$moduleInstance->getPermission('view', $context->employee)) {
                    continue;
                }

                // Check which / if method is callable

                $hook_callable = is_callable(array($moduleInstance, 'hook'.$hook_name));
                $ohook = $orhook = "";
                $hook_retro_callable = is_callable(array($moduleInstance, 'hook'.$retro_hook_name));
                if (array_key_exists($moduleInstance->id, $this->overrideHooks)) {
                    $ohook = Hook::getRetroHookName($this->overrideHooks[$moduleInstance->id]);
                    $orhook = ($this->overrideHooks[$moduleInstance->id]);
                    $hook_callable = is_callable(array($moduleInstance, 'hook'.$orhook));
                    $hook_retro_callable = is_callable(array($moduleInstance, 'hook'.$ohook));
                }

                if (($hook_callable || $hook_retro_callable)) {
                    $hook_args['altern'] = ++$altern;
                    if (array_key_exists($moduleInstance->id, $this->overrideHooks)) {
                        if ($hook_callable) {
                            $display = $moduleInstance->{'hook'.$orhook}($hook_args);
                        } else if ($hook_retro_callable) {
                            $display = $moduleInstance->{'hook'.$ohook}($hook_args);
                        }
                    } else {
                        // Call hook method
                        if ($hook_callable) {
                            $display = $moduleInstance->{'hook'.$hook_name}($hook_args);
                        } else if ($hook_retro_callable) {
                            $display = $moduleInstance->{'hook'.$retro_hook_name}($hook_args);
                        }
                    }
                    // Live edit
                    if (isset($array['live_edit']) && $array['live_edit'] && Tools::isSubmit('live_edit') && Tools::getValue('ad') && Tools::getValue('liveToken') == Tools::getAdminToken('AdminModulesPositions'.(int)Tab::getIdFromClassName('AdminModulesPositions').(int)Tools::getValue('id_employee'))) {
                        $live_edit = true;
                        $output .= self::wrapLiveEdit($display, $moduleInstance, $array['id_hook']);
                    } else {
                        $output .= $display;
                    }
                }
            }

            // Return html string
            return ($live_edit ? '<script type="text/javascript">hooks_list.push(\''.$hook_name.'\'); </script>
						<div id="'.$hook_name.'" class="dndHook" style="min-height:50px">' : '').$output.($live_edit ? '</div>' : '');
        }

        public static function getPattern($theme)
        {
            $output = array();

            $path = _PS_THEME_DIR_.'assets/img/patterns/';
            if ($theme && is_dir($path)) {
                $files = glob($path.'*');
                $i = 0;
                foreach ($files as $dir) {
                    if (preg_match("#.png|.jpg|.gif#", $dir)) {
                        $output[$i]['img_name'] = basename($dir);
                        $output[$i]['img_url'] = _THEME_IMG_DIR_.'patterns/'.basename($dir);
                        $i++;
                    }
                }
            }
            return $output;
        }

        /**
         * wrap html Live Edit
         */
        public static function wrapLiveEdit($display, $moduleInstance, $id_hook)
        {
            return '';
        }

        /**
         * get array languages
         * @param : id_lang, name, active, iso_code, language_code, date_format_lite, date_format_full, is_rtl, id_shop, shops (array)
         * return array (
         * 		1 => en,
         * 		2 => vn,
         * )
         */
        public static function getLangAtt($attribute = 'iso_code')
        {
            $languages = array();
            foreach (Language::getLanguages(false, false, false) as $lang) {
                $languages[] = $lang[$attribute];
            }
            return $languages;
        }

        public static function getCookie()
        {
            $data = $_COOKIE;
            return $data;
        }

        /**
         * @param
         * 0 no multi_lang
         * 1 multi_lang follow id_lang
         * 2 multi_lnag follow code_lang
         * @return array
         */
        public static function getPost($keys = array(), $multi_lang = 0)
        {
            $post = array();
            if ($multi_lang == 0) {
                foreach ($keys as $key) {
                    // get value from $_POST
                    $post[$key] = Tools::getValue($key);
                }
            } elseif ($multi_lang == 1) {

                foreach ($keys as $key) {
                    // get value multi language from $_POST
                    if (method_exists('Language', 'getIDs')) {
                        foreach (Language::getIDs(false) as $id_lang)
                            $post[$key.'_'.(int)$id_lang] = Tools::getValue($key.'_'.(int)$id_lang);
                    }
                }
            } elseif ($multi_lang == 2) {
                $languages = self::getLangAtt();
                foreach ($keys as $key) {
                    // get value multi language from $_POST
                    foreach ($languages as $id_code)
                        $post[$key.'_'.$id_code] = Tools::getValue($key.'_'.$id_code);
                }
            }

            return $post;
        }

        /**
         * Name of special char
         * http://www.computerhope.com/keys.htm
         */
        public static function ConvertSpecialChar($str = '')
        {
            $result = '';

            if (is_string($str)) {
                $result = str_replace('LEO_BACKSLASH', '\\', $str);
            }
            return $result;
        }

        public static function leoExitsDb($type='', $table_name='', $col_name='')
        {
            if ($type == 'table') {
                # EXITS TABLE
                $sql = 'SELECT COUNT(*) FROM information_schema.tables
                            WHERE table_schema = "'._DB_NAME_.'"
                            AND table_name = "'._DB_PREFIX_.pSQL($table_name).'"';
                $table = Db::getInstance()->getValue($sql);
                if (empty($table)) {
                    return false;
                }
                return true;
                
            } else if ($type == 'column') {
                # EXITS COLUMN
                $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.pSQL($table_name) .'` LIKE "'.pSQL($col_name).'"';
                $column = Db::getInstance()->executeS($sql);
                if (empty($column)) {
                    return false;
                }
                return true;
            }
            
            return false;
        }

        public static function leoCreateColumn($table_name, $col_name, $data_type)
        {
            $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.pSQL($table_name) .'` LIKE "'.pSQL($col_name).'"';
            $column = Db::getInstance()->executeS($sql);

            if (empty($column)) {
                $sql = 'ALTER TABLE `'._DB_PREFIX_.pSQL($table_name).'` ADD COLUMN `'.pSQL($col_name).'` '.pSQL($data_type);
                $res = Db::getInstance()->execute($sql);
            }
        }
        
        public static function leoEditColumn($table_name, $col_name, $data_type)
        {
            $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.pSQL($table_name) .'` LIKE "'.pSQL($col_name).'"';
            $column = Db::getInstance()->executeS($sql);

            if (!empty($column)) {
                $sql = 'ALTER TABLE `'._DB_PREFIX_.pSQL($table_name).'` MODIFY `'.pSQL($col_name).'` '.pSQL($data_type);
                $res = Db::getInstance()->execute($sql);
            }
        }
        
        /*
         * Copy from leotemcp module
         * Do not write more in this class, please write to 'class apPageHelper' in helper.php file
         */
    }

}
