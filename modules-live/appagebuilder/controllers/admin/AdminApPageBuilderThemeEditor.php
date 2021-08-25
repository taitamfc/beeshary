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

class AdminApPageBuilderThemeEditorController extends ModuleAdminControllerCore
{
    public $themeName = '';
    public $css_patterns;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'appagebuilder_themeeditor';
        $this->lang = false;
        $this->context = Context::getContext();
        parent::__construct();
        $this->themeName = _THEME_NAME_;
        $this->js_patterns = _PS_THEME_DIR_.apPageHelper::getJsDir().'patterns/';
        $this->css_patterns = _PS_THEME_DIR_.apPageHelper::getCssDir().'patterns/';
    }

    public function postProcess()
    {
        if (count($this->errors) > 0) {
            return;
        }
        
        if (Tools::getValue('action') && Tools::getValue('action') == 'savedata' && Tools::getValue('customize')) {
            $data = LeoFrameworkHelper::getPost(array('action-mode', 'saved_file', 'newfile', 'customize', 'customize_match', 'active'), 0);
            
            $selectors = $data['customize'];
            $matches = $data['customize_match'];

            $output = '';

            $cache = array();
            foreach ($selectors as $match => $customizes) {
                $output .= "\r\n/* customize for $match */ \r\n";
                foreach ($customizes as $key => $customize) {
                    if (isset($matches[$match]) && isset($matches[$match][$key])) {
                        $tmp = explode('|', $matches[$match][$key]);
                        $attribute = Tools::strtolower(trim($tmp[1]));
                        if (trim($customize)) {
                            $output .= $tmp[0].' { ';
                            if ($attribute == 'background-image') {
                                $output .= $attribute.':url('.$customize.')';
                            } elseif ($attribute == 'font-size') {
                                $output .= $attribute.':'.$customize.'px';
                            } else if (strpos($attribute, 'color') !== false) {
                                $output .= $attribute.':#'.$customize;
                            } else if ($attribute == 'background') {
                                $output .= $attribute.':#'.$customize;
                            } else {
                                $output .= $attribute.':'.$customize;
                            }
                            $output .= "} \r\n";
                        }
                        $cache[$match][] = array('val' => $customize, 'selector' => $tmp[0], 'attr' => $tmp[1]);
                    }
                }
            }
            
            # RENAME
            if (!empty($data['saved_file']) && !empty($data['newfile'])) {
                # DELETE PATTERN
                if (isset($data['saved_file']) && $data['saved_file'] && file_exists($this->css_patterns.$data['saved_file'].'.css')) {
                    unlink($this->css_patterns.$data['saved_file'].'.css');
                }
                if (isset($data['saved_file']) && $data['saved_file'] && file_exists($this->css_patterns.$data['saved_file'].'.json')) {
                    unlink($this->css_patterns.$data['saved_file'].'.json');
                }
            }
            
            
            if (empty($data['newfile'])) {
                # EDIT PATTERN
                $nameFile = $data['saved_file'] ? $data['saved_file'] : 'profile-'.time();
            } else {
                # CREATE PATTERN
                $nameFile = preg_replace('#\s+#', '-', trim($data['newfile']));
            }
            
            if ($data['action-mode'] != 'save-delete') {
                # CREATE + EDIT
                if (!is_dir($this->css_patterns)) {
                    mkdir($this->css_patterns, 0755, true);
                }

                if (!empty($output)) {
                    LeoFrameworkHelper::writeToCache($this->css_patterns, $nameFile, $output);
                }
                if (!empty($cache)) {
                    LeoFrameworkHelper::writeToCache($this->css_patterns, $nameFile, Tools::jsonEncode($cache), 'json');
                }
                
                if (isset($data['active']) && $data['active']) {
                    # SET ACTIVE - YES
                    apPageHelper::setConfig('C_PROFILE', $nameFile);
                } elseif (isset($data['active']) && empty($data['active'])) {
                    # SET ACTIVE - NO
                    $pattern_active = apPageHelper::getConfig('C_PROFILE');
                    if ($nameFile == $pattern_active) {
                        apPageHelper::setConfig('C_PROFILE', '');
                    }
                }
            } else {
                # SET ACTIVE - NO
                $pattern_active = apPageHelper::getConfig('C_PROFILE');
                if ($data['saved_file'] == $pattern_active) {
                    apPageHelper::setConfig('C_PROFILE', '');
                }
                # DELETE PATTERN
                if (isset($data['saved_file']) && $data['saved_file'] && file_exists($this->css_patterns.$data['saved_file'].'.css')) {
                    unlink($this->css_patterns.$data['saved_file'].'.css');
                }
                if (isset($data['saved_file']) && $data['saved_file'] && file_exists($this->css_patterns.$data['saved_file'].'.json')) {
                    unlink($this->css_patterns.$data['saved_file'].'.json');
                }
            }
            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
        }
    }

    /**
     * get list of files inside folder path.
     */
    private function getFileList($path, $e = null, $nameOnly = false)
    {
        $output = array();
        $directories = glob($path.'*'.$e);
        if ($directories) {
            foreach ($directories as $dir) {
                $dir = basename($dir);
                if ($nameOnly) {
                    $dir = str_replace($e, '', $dir);
                }
                $output[$dir] = $dir;
            }
        }
        return $output;
    }

    /**
     * render list of modules following positions in the layout editor.
     */
    public function renderList()
    {
        $filePath = _PS_ALL_THEMES_DIR_.$this->themeName.'';
        
        $xml = simplexml_load_file($filePath.'/config.xml');

        if (!isset($xml->theme_key) || empty($xml->theme_key)) {
            return '<div class="panel"><div class="panel-content"><div class="alert alert-warning">'.'This function is only avariable using for Theme from <b><a href=http://www.leotheme.com/ target=_blank>leotheme.com</a></b> or using theme built-in <b>Leo Framework</b>'.'</div></div></div>';
        }

        $tpl = $this->createTemplate('themeeditor.tpl');
        Context::getContext()->controller->addCss(apPageHelper::getCssAdminDir().'admin/themeeditor.css');
        Context::getContext()->controller->addCss(apPageHelper::getCssAdminDir().'colorpicker/css/colorpicker.css');
        Context::getContext()->controller->addCss(apPageHelper::getCssAdminDir().'paneltool.css');
        
        Context::getContext()->controller->addJs(apPageHelper::getJsAdminDir().'colorpicker/js/colorpicker.js');
        Context::getContext()->controller->addJs(apPageHelper::getJsAdminDir().'admin/themeeditor.js');
        
        

        $output = LeoFrameworkHelper::renderEdtiorThemeForm($this->themeName);

        $profiles = $this->getFileList($this->css_patterns, '.css', true);
        $patterns = $this->getFileList(_PS_ALL_THEMES_DIR_.$this->themeName.'/assets/img/patterns/', '.png');
        $patternsjpg = $this->getFileList(_PS_ALL_THEMES_DIR_.$this->themeName.'/assets/img/patterns/', '.jpg');

        $patterns = array_merge($patterns, $patternsjpg);
        $backGroundValue = array(
            'attachment' => array('scroll', 'fixed', 'local', 'initial', 'inherit'),
            'repeat' => array('repeat', 'repeat-x', 'repeat-y', 'no-repeat', 'initial', 'inherit'),
            'position' => array('left top', 'left center', 'left bottom', 'right top', 'right center', 'right bottom', 'center top', 'center center', 'center bottom')
        );
        $siteURL = _PS_BASE_URL_.__PS_BASE_URI__;
        $imgLink = Context::getContext()->link->getAdminLink('AdminApPageBuilderImages').'&leo_controller=live_theme_edit';    // URL LOAD IMAGE BUTTON
        $backgroundImageURL = _PS_BASE_URL_._THEME_DIR_.'assets/img/patterns/';

        $ssl_enable = Configuration::get('PS_SSL_ENABLED');
        if ($ssl_enable) {
            $siteURL = str_replace('http:', 'https:', $siteURL);
            $imgLink = str_replace('http:', 'https:', $imgLink);
            $backgroundImageURL = str_replace('http:', 'https:', $backgroundImageURL);
        }

        $tpl->assign(array(
            'actionURL' => 'index.php?tab=AdminApPageBuilderThemeEditor&token='.Tools::getAdminTokenLite('AdminApPageBuilderThemeEditor').'&action=savedata',
            'text_layout' => $this->l('Layout'),
            'text_elements' => $this->l('Elements'),
            'profiles' => $profiles,
            'profiles_active' => apPageHelper::getConfig('c_profile'),
            'xmlselectors' => $output,
            'apPageHelper' => apPageHelper::getInstance(),
            'themeName' => $this->themeName,
            'patterns' => $patterns,
            'backgroundImageURL' => $backgroundImageURL,
            'siteURL' => $siteURL,
            'customizeFolderURL' => _PS_THEME_URI_.apPageHelper::getCssDir().'patterns/',
            'backLink' => 'index.php?controller=AdminModules&configure=appagebuilder&token='.Tools::getAdminTokenLite('AdminModules'),
            'imgLink' => $imgLink,
            'backGroundValue' => $backGroundValue
        ));

        return $tpl->fetch();
    }
}
