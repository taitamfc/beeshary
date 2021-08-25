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
require_once(_PS_MODULE_DIR_.'appagebuilder/libs/Helper.php');
require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoFrameworkHelper.php');
require_once(_PS_MODULE_DIR_.'appagebuilder/classes/ApPageSetting.php');
require_once(_PS_MODULE_DIR_.'appagebuilder/classes/ApPageBuilderModel.php');
require_once(_PS_MODULE_DIR_.'appagebuilder/classes/ApPageBuilderProfilesModel.php');
require_once(_PS_MODULE_DIR_.'appagebuilder/classes/ApPageBuilderProductsModel.php');
require_once(_PS_MODULE_DIR_.'appagebuilder/classes/ApPageBuilderShortcodeModel.php');

class APPageBuilder extends Module
{
    protected $default_language;
    protected $languages;
    protected $theme_name;
    protected $data_index_hook;
    protected $profile_data;
    protected $hook_index_data;
    protected $profile_param;
    protected $path_resource;
    protected $product_active;
    protected $backup_dir;
    protected $header_content;
    
    protected $_confirmations = array();
    protected $_errors = array();
    protected $_warnings = array();
    private $templateFile;

    public function __construct()
    {
        $this->name = 'appagebuilder';
        $this->module_key = '9da746af2f0aa356120277ab2a148484';
        $this->tab = 'front_office_features';
        $this->version = '2.2.5';
        $this->author = 'ApolloTheme';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->need_instance = 0;
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = true;
        parent::__construct();
        
        $this->displayName = $this->l('Apollo Page Builder');
        $this->description = $this->l('Apollo Page Builder build content for your site.');
        $this->theme_name = _THEME_NAME_;
        $this->default_language = Language::getLanguage(Context::getContext()->language->id);
        $this->languages = Language::getLanguages();
        $this->path_resource = $this->_path.'views/';
        $this->templateFile = 'module:appagebuilder/views/templates/hook/appagebuilder.tpl';
        $this->redirectFriendUrl();
    }
    
    public function redirectFriendUrl()
    {
//        if (Context::getContext()->controller === null || (isset(Context::getContext()->controller->controller_type) && in_array(Context::getContext()->controller->controller_type, array('front', 'modulefront')))) {
        // NEED HOOK TO MODULEROUTES
        $ap_live_edit = Tools::getValue('ap_live_edit');
        if (empty($ap_live_edit) &&  (Context::getContext()->controller === null || Context::getContext()->controller->controller_type != 'admin')) {
            $id_appagebuilder_profiles = Tools::getValue('id_appagebuilder_profiles');
            if (Configuration::get('PS_REWRITING_SETTINGS') && Tools::getIsset('id_appagebuilder_profiles') && $id_appagebuilder_profiles) {
                $profile_data = ApPageBuilderProfilesModel::getActiveProfile('index');

                if (isset($profile_data['friendly_url']) && !empty($profile_data['friendly_url'])) {
                    require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoFriendlyUrl.php');
                    $leo_friendly_url = LeoFriendlyUrl::getInstance();
                    
                    $link = Context::getContext()->link;
                    $idLang = Context::getContext()->language->id;
                    $idShop = null;
                    $relativeProtocol = false;
                    
                    $url = $link->getBaseLink($idShop, null, $relativeProtocol).$leo_friendly_url->getLangLink($idLang, null, $idShop).$profile_data['friendly_url'].'.html';
                    $leo_friendly_url->canonicalRedirection($url);
                }
            }
        }
    }
    
    public static function getInstance()
    {
        static $_instance;
        if (!$_instance) {
            $_instance = new APPageBuilder();
        }
        return $_instance;
    }

    public function install()
    {
        require_once(_PS_MODULE_DIR_.$this->name.'/libs/setup.php');
        
		//DONGND:: build shortcode, create folder for override
        apPageHelper::createShortCode();
		
        if (!parent::install() || !ApPageSetup::installConfiguration() || !ApPageSetup::createTables() || !ApPageSetup::installModuleTab() || !$this->registerLeoHook()) {
            return false;
        }

        # NOT LOAD DATASAMPLE AGAIN
        Configuration::updateValue('AP_INSTALLED_APPAGEBUILDER', '1');
        
        # REMOVE FILE INDEX.PHP FOR TRANSLATE
        ApPageSetup::processTranslateTheme();
               
        Configuration::updateValue('APPAGEBUILDER_OVERRIDED', 1);

        return true;
    }

    public function uninstall()
    {
        require_once(_PS_MODULE_DIR_.$this->name.'/libs/setup.php');
        
        #shortcode to tinymce :  rollback default file config for tinymce
        Tools::copy(_PS_MODULE_DIR_.$this->name.'/views/js/shortcode/backup/tinymce.inc.js', _PS_ROOT_DIR_.'/js/admin/tinymce.inc.js');
        
        #shortcode to tinymce : CATEGORY IN ADMIN
        Tools::copy(_PS_MODULE_DIR_.'appagebuilder/views/js/shortcode/backup/category.bundle.js', _PS_ADMIN_DIR_.'/themes/new-theme/public/category.bundle.js');
        #shortcode to tinymce : CMS PAGE IN ADMIN
        Tools::copy(_PS_MODULE_DIR_.'appagebuilder/views/js/shortcode/backup/cms_page_form.bundle.js', _PS_ADMIN_DIR_.'/themes/new-theme/public/cms_page_form.bundle.js');

        if (!parent::uninstall()|| !ApPageSetup::uninstallModuleTab() || !ApPageSetup::deleteTables() || !ApPageSetup::uninstallConfiguration()) {
            return false;
        }
		
		//DONGND:: remove overrider folder
		// $this->uninstallOverrides();
		Configuration::updateValue('APPAGEBUILDER_OVERRIDED', 0);
        
        return true;
    }
    
    public function hookActionModuleRegisterHookAfter($params)
    {
        if (isset($params['hook_name']) && ($params['hook_name'] == 'header' || $params['hook_name']=='displayheader')) {
            if ($this->getConfig('MOVE_END_HEADER')) {
                $hook_name = 'header';
                $id_hook = Hook::getIdByName($hook_name);
                $id_module = $this->id;
                $id_shop = Context::getContext()->shop->id;
                
                // Get module position in hook
                $sql = 'SELECT MAX(`position`) AS position
                    FROM `'._DB_PREFIX_.'hook_module`
                    WHERE `id_hook` = '.(int)$id_hook.' AND `id_shop` = '.(int)$id_shop . ' AND id_module != '.(int) $id_module;
                if (!$position = Db::getInstance()->getValue($sql)) {
                    $position = 0;
                }

                $sql = 'UPDATE `'._DB_PREFIX_.'hook_module'.'` SET `position` =' . (int)($position + 1) . ' WHERE '
                                . '`id_module` = '.(int) $id_module
                                . ' AND `id_hook` = '.(int) $id_hook
                                . ' AND `id_shop` = '.(int) $id_shop;
                Db::getInstance()->execute($sql);
            }
        }
    }
    
    public function postProcess()
    {
        if (count($this->errors) > 0) {
            return;
        }
        
        if (Tools::isSubmit('installdemo')) {
            require_once(_PS_MODULE_DIR_.$this->name.'/libs/setup.php');
            ApPageSetup::installSample();
        } else if (Tools::isSubmit('resetmodule')) {
            require_once(_PS_MODULE_DIR_.$this->name.'/libs/setup.php');
            ApPageSetup::createTables(1);
        } else if (Tools::isSubmit('deleteposition')) {
            apPageHelper::processDeleteOldPosition();
            $this->_confirmations[] = 'POSITIONS NOT USE have been deleted successfully.';
        } else if (Tools::isSubmit('submitUpdateModule')) {
            apPageHelper::processCorrectModule();
            $this->_confirmations[] = 'Update and Correct Module is successful';
        } else if (Tools::isSubmit('backup')) {
            $this->processBackup();
            $this->_confirmations[] = 'Back-up to PHP file is successful';
        } else if (Tools::isSubmit('restore')) {
            $this->processRestore();
            $this->_confirmations[] = 'Restore Back-up File is successful';
        } else if (Tools::isSubmit('submitApPageBuilder')) {
            $load_owl = Tools::getValue('APPAGEBUILDER_LOAD_OWL');
            $header_hook = Tools::getValue('APPAGEBUILDER_HEADER_HOOK');
            $content_hook = Tools::getValue('APPAGEBUILDER_CONTENT_HOOK');
            $footer_hook = Tools::getValue('APPAGEBUILDER_FOOTER_HOOK');
            $product_hook = Tools::getValue('APPAGEBUILDER_PRODUCT_HOOK');
            Configuration::updateValue('APPAGEBUILDER_LOAD_OWL', (int)$load_owl);
            Configuration::updateValue('APPAGEBUILDER_LOAD_SWIPER', Tools::getValue('APPAGEBUILDER_LOAD_SWIPER'));
            Configuration::updateValue('APPAGEBUILDER_LOAD_SLICK', Tools::getValue('APPAGEBUILDER_LOAD_SLICK'));
            Configuration::updateValue('APPAGEBUILDER_COOKIE_PROFILE', Tools::getValue('APPAGEBUILDER_COOKIE_PROFILE'));
            Configuration::updateValue('APPAGEBUILDER_HEADER_HOOK', $header_hook);
            Configuration::updateValue('APPAGEBUILDER_CONTENT_HOOK', $content_hook);
            Configuration::updateValue('APPAGEBUILDER_FOOTER_HOOK', $footer_hook);
            Configuration::updateValue('APPAGEBUILDER_PRODUCT_HOOK', $product_hook);
//            Configuration::updateValue('APPAGEBUILDER_LOAD_AJAX', Tools::getValue('APPAGEBUILDER_LOAD_AJAX'));
            Configuration::updateValue('APPAGEBUILDER_LOAD_STELLAR', Tools::getValue('APPAGEBUILDER_LOAD_STELLAR'));
            Configuration::updateValue('APPAGEBUILDER_LOAD_WAYPOINTS', Tools::getValue('APPAGEBUILDER_LOAD_WAYPOINTS'));
            Configuration::updateValue('APPAGEBUILDER_LOAD_INSTAFEED', Tools::getValue('APPAGEBUILDER_LOAD_INSTAFEED'));
            Configuration::updateValue('APPAGEBUILDER_LOAD_IMAGE360', Tools::getValue('APPAGEBUILDER_LOAD_IMAGE360'));
            Configuration::updateValue('APPAGEBUILDER_LOAD_IMAGEHOTPOT', Tools::getValue('APPAGEBUILDER_LOAD_IMAGEHOTPOT'));
            Configuration::updateValue('APPAGEBUILDER_LOAD_HTML5VIDEO', Tools::getValue('APPAGEBUILDER_LOAD_HTML5VIDEO'));
            Configuration::updateValue('APPAGEBUILDER_SAVE_MULTITHREARING', Tools::getValue('APPAGEBUILDER_SAVE_MULTITHREARING'));
            Configuration::updateValue('APPAGEBUILDER_SAVE_SUBMIT', Tools::getValue('APPAGEBUILDER_SAVE_SUBMIT'));
            Configuration::updateValue('APPAGEBUILDER_LOAD_PRODUCTZOOM', Tools::getValue('APPAGEBUILDER_LOAD_PRODUCTZOOM'));

            Configuration::updateValue('APPAGEBUILDER_LOAD_FULLPAGEJS', Tools::getValue('APPAGEBUILDER_LOAD_FULLPAGEJS'));
            Configuration::updateValue('APPAGEBUILDER_LOAD_PN', Tools::getValue('APPAGEBUILDER_LOAD_PN'));
            Configuration::updateValue('APPAGEBUILDER_LOAD_TRAN', Tools::getValue('APPAGEBUILDER_LOAD_TRAN'));
            Configuration::updateValue('APPAGEBUILDER_LOAD_IMG', Tools::getValue('APPAGEBUILDER_LOAD_IMG'));
            Configuration::updateValue('APPAGEBUILDER_LOAD_COUNT', Tools::getValue('APPAGEBUILDER_LOAD_COUNT'));
//            Configuration::updateValue('APPAGEBUILDER_LOAD_COLOR', Tools::getValue('APPAGEBUILDER_LOAD_COLOR'));
            Configuration::updateValue('APPAGEBUILDER_COLOR', Tools::getValue('APPAGEBUILDER_COLOR'));
//            Configuration::updateValue('APPAGEBUILDER_LOAD_ACOLOR', Tools::getValue('APPAGEBUILDER_LOAD_ACOLOR'));
            Configuration::updateValue('APPAGEBUILDER_PRODUCT_MAX_RANDOM', Tools::getValue('APPAGEBUILDER_PRODUCT_MAX_RANDOM'));
            Configuration::updateValue('APPAGEBUILDER_LOAD_COOKIE', Tools::getValue('APPAGEBUILDER_LOAD_COOKIE'));

            //nghiatd - update extrafield
            $this->saveConfigExtrafields('APPAGEBUILDER_PRODUCT_TEXTEXTRA', 'APPAGEBUILDER_PRODUCT_EDITOREXTRA', 'product');
            $this->saveConfigExtrafields('APPAGEBUILDER_CATEGORY_TEXTEXTRA', 'APPAGEBUILDER_CATEGORY_EDITOREXTRA', 'category');
        }
    }


    public function getContent()
    {
        $this->errors = array();
        if (!$this->access('configure')) {
            $this->errors[] = $this->trans('You do not have permission to configure this.', array(), 'Admin.Notifications.Error');
            $this->context->smarty->assign('errors', $this->errors);
        }
        
        $this->postProcess();
        
        $output = '';
        $this->backup_dir = str_replace('\\', '/', _PS_CACHE_DIR_.'backup/modules/appagebuilder/');
        
        $create_profile_link = $this->context->link->getAdminLink('AdminApPageBuilderProfiles').'&addappagebuilder_profiles';
        $profile_link = $this->context->link->getAdminLink('AdminApPageBuilderProfiles');
        $position_link = $this->context->link->getAdminLink('AdminApPageBuilderPositions');
        $product_link = $this->context->link->getAdminLink('AdminApPageBuilderProducts');
        $path_guide = _PS_MODULE_DIR_.$this->name.'/views/templates/admin/guide.tpl';
        $guide_box = ApPageSetting::buildGuide($this->context, $path_guide, 1);

        $module_link = $this->context->link->getAdminLink('AdminModules', false)
                .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules');
        $back_up_file = @Tools::scandir($this->backup_dir, 'php');
        arsort($back_up_file);
        $this->context->smarty->assign(array(
            'guide_box' => $guide_box,
            'create_profile_link' => $create_profile_link,
            'profile_link' => $profile_link,
            'position_link' => $position_link,
            'product_link' => $product_link,
            'module_link' => $module_link,
            'back_up_file' => $back_up_file,
            'backup_dir' => $this->backup_dir,
        ));
        $output = $this->generateLeoHtmlMessage();
        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        Media::addJsDef(array('js_ap_controller' => 'module_configuration'));
        return $output.$this->renderForm();
    }

    public function saveConfigExtrafields($ft, $fe, $type)
    {
        $field_text = array_unique(explode(',', Tools::getValue($ft)));
        $field_default = array('name', 'description', 'description_short', 'link_rewrite',  'meta_description', 'meta_keywords',  'meta_title', 'name', 'available_now', 'available_later', 'online_only', 'ean13', 'isbn', 'upc', 'ecotax', 'quantity', 'minimal_quantity', 'price', 'wholesale_price', 'unity', 'unit_price_ratio', 'additional_shipping_cost', 'reference', 'supplier_reference', 'location', 'width', 'height', 'depth', 'weight', 'out_of_stock', 'quantity_discount', 'customizable', 'uploadable_files', 'text_fields', 'active', 'redirect_type', 'id_type_redirected', 'available_date', 'show_condition', 'condition', 'show_price', 'indexed', 'cache_is_pack', 'cache_has_attachments', 'is_virtual', 'cache_default_attribute', 'date_add', 'date_upd', 'advanced_stock_management', 'pack_stock_type', 'state');
        $field_text_valid = array();
        foreach ($field_text as $k => $v) {
            // validate module
            unset($k);
            
            $v = str_replace(' ', '_', trim($v));
            $v = preg_replace('/[^A-Za-z0-9\_]/', '', $v);
            if ($v && !in_array($v, $field_default)) {
                $field_text_valid[] = $v;
            }
        }

        Configuration::updateValue($ft, implode(',', $field_text_valid));
        $this->processExtrafield($field_text_valid, $type, 'varchar(255)');

        $field_editor = array_unique(explode(',', Tools::getValue($fe)));
        $field_editor_valid = array();
        foreach ($field_editor as $k => $v) {
            // validate module
            unset($k);
            
            $v = str_replace(' ', '_', trim($v));
            $v = preg_replace('/[^A-Za-z0-9\_]/', '', $v);
            if ($v && !in_array($v, $field_text) && !in_array($v, $field_default)) {
                $field_editor_valid[] = $v;
            }
        }
            
        Configuration::updateValue($fe, implode(',', $field_editor_valid));
        $this->processExtrafield($field_editor_valid, $type, 'text');
    }

    //add by nghiatd
    public function processExtrafield($submit_fields, $type, $field_type)
    {
        $table = ($type == 'product')?_DB_PREFIX_.'appagebuilder_extrapro':_DB_PREFIX_.'appagebuilder_extracat';
        $id = ($type == 'product')?'id_product':'id_category';
        //$field_type = ($field_type == 'varchar(255)')?'VARCHAR(255) NULL;':'TEXT;';
        $files = array();
        //check table exist and return field
        $sql = 'SELECT * FROM information_schema.tables
                    WHERE table_schema = "'._DB_NAME_.'"
                    AND table_name = "'.pSQL($table).'"';
        $result = Db::getInstance()->getValue($sql);

        if (!empty($result)) {
            $sql = 'SHOW FIELDS FROM `'.pSQL($table) .'`';
            $result = Db::getInstance()->executeS($sql);
            foreach ($result as $value) {
                $files[$value['Field']] = $value['Type'];
            }
        } else {
            #select product layout
            $this->registerHook('actionObjectProductUpdateAfter');
            $this->registerHook('displayAdminProductsExtra');
            $this->registerHook('filterProductContent');
            #select category layout
            $this->registerHook('actionObjectCategoryUpdateAfter');
            $this->registerHook('displayBackOfficeCategory');
            $this->registerHook('filterCategoryContent');

            Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `'.pSQL($table).'` (
                  `'.pSQL($id).'` int(11) unsigned NOT NULL,
                  `id_shop` int(11) unsigned NOT NULL DEFAULT \'1\',
                  `id_lang` int(10) unsigned NOT NULL,
                  PRIMARY KEY (`'.pSQL($id).'`, `id_shop`, `id_lang`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
        }

        //add field
        foreach ($submit_fields as $value) {
            if ($value && !array_key_exists($value, $files)) {
                $sql = 'ALTER TABLE `'.pSQL($table).'` ADD `'.pSQL($value).'` '.pSQL($field_type);
                //echo $sql.'<br>';
                Db::getInstance()->execute($sql);
                $files[$value] = $field_type;
            }
        }

        //delete field
        foreach ($files as $key => $value) {
            if (!in_array($key, $submit_fields) && $key != $id && $key != 'id_shop' && $key != 'id_lang' && $value == $field_type) {
                $sql = 'ALTER TABLE `'.pSQL($table).'` DROP `'.pSQL($key).'`';
                //echo $sql.'<br>';
                Db::getInstance()->execute($sql);
                unset($files[$key]);
            }
        }
        unset($files['id_product']);
        unset($files['id_category']);
        unset($files['id_shop']);
        unset($files['id_lang']);
        return implode(',', array_keys($files));
    }

    public function processRestore()
    {
        $file = Tools::getValue('backupfile');
        if (file_exists($this->backup_dir.$file)) {
            $query = $dataLang = '';
            require_once($this->backup_dir.$file);
            if (isset($query) && !empty($query)) {
                $query = str_replace('_DB_PREFIX_', _DB_PREFIX_, $query);
                $query = str_replace('_MYSQL_ENGINE_', _MYSQL_ENGINE_, $query);
                $query = str_replace('LEO_ID_SHOP', (int)Context::getContext()->shop->id, $query);
                $query = str_replace("\\'", "\'", $query);

                $db_data_settings = preg_split("/;\s*[\r\n]+/", $query);
                foreach ($db_data_settings as $query) {
                    $query = trim($query);
                    if (!empty($query)) {
                        if (!Db::getInstance()->Execute($query)) {
                            $this->_html['error'][] = 'Can not restore for '.$this->name;
                            return false;
                        }
                    }
                }

                if (isset($dataLang) && !empty($dataLang)) {
                    $languages = Language::getLanguages(true, Context::getContext()->shop->id);
                    foreach ($languages as $lang) {
                        if (isset($dataLang[Tools::strtolower($lang['iso_code'])])) {
                            $query = str_replace('_DB_PREFIX_', _DB_PREFIX_, $dataLang[Tools::strtolower($lang['iso_code'])]);
                            //if not exist language in list, get en
                        } else {
                            if (isset($dataLang['en'])) {
                                $query = str_replace('_DB_PREFIX_', _DB_PREFIX_, $dataLang['en']);
                            } else {
                                //firt item in array
                                foreach (array_keys($dataLang) as $key) {
                                    $query = str_replace('_DB_PREFIX_', _DB_PREFIX_, $dataLang[$key]);
                                    break;
                                }
                            }
                        }
                        $query = str_replace('_MYSQL_ENGINE_', _MYSQL_ENGINE_, $query);
                        $query = str_replace('LEO_ID_SHOP', (int)Context::getContext()->shop->id, $query);
                        $query = str_replace('LEO_ID_LANGUAGE', (int)$lang['id_lang'], $query);
                        $query = str_replace("\\\'", "\'", $query);

                        $db_data_settings = preg_split("/;\s*[\r\n]+/", $query);
                        foreach ($db_data_settings as $query) {
                            $query = trim($query);
                            if (!empty($query)) {
                                if (!Db::getInstance()->Execute($query)) {
                                    $this->_html['error'][] = 'Can not restore for data lang '.$this->name;
                                    return false;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function processBackup()
    {
        $install_folder = $this->backup_dir;
        
        if (!is_dir($install_folder)) {
            mkdir($install_folder, 0755, true);
        }
        $list_table = Db::getInstance()->executeS("SHOW TABLES LIKE '%appagebuilder%'");

        $create_table = '';
        $data_with_lang = '';
        $backup_file = $install_folder.$this->name.date('_Y_m_d_H_i_s').'.php';
        $fp = @fopen($backup_file, 'w');
        if ($fp === false) {
            die('Unable to create backup file '.addslashes($backup_file));
        }

        fwrite($fp, '<?php');
        fwrite($fp, "\n/* back up for module ".$this->name." */\n");

        $data_language = array();
        $list_lang = array();
        $languages = Language::getLanguages(true, Context::getContext()->shop->id);
        foreach ($languages as $lang) {
            $list_lang[$lang['id_lang']] = $lang['iso_code'];
        }

        foreach ($list_table as $table) {
            $table = current($table);
            $table_name = str_replace(_DB_PREFIX_, '_DB_PREFIX_', $table);
            // Skip tables which do not start with _DB_PREFIX_
            if (Tools::strlen($table) < Tools::strlen(_DB_PREFIX_) || strncmp($table, _DB_PREFIX_, Tools::strlen(_DB_PREFIX_)) != 0) {
                continue;
            }
            $schema = Db::getInstance()->executeS('SHOW CREATE' . ' TABLE `'.pSQL($table).'`');
            if (count($schema) != 1 || !isset($schema[0]['Table']) || !isset($schema[0]['Create Table'])) {
                fclose($fp);
                die($this->l('An error occurred while backing up. Unable to obtain the schema of').' '.$table);
            }
            $create_table .= 'DROP TABLE IF EXISTS `'.$table_name."`;\n".$schema[0]['Create Table'].";\n";

            if (strpos($schema[0]['Create Table'], '`id_shop`')) {
                $data = Db::getInstance()->query('SELECT * FROM `'.pSQL($schema[0]['Table']).'` WHERE `id_shop`='.(int)Context::getContext()->shop->id, false);
            } else {
                $data = Db::getInstance()->query('SELECT * FROM `'.pSQL($schema[0]['Table']).'`', false);
            }

            $sizeof = DB::getInstance()->NumRows();
            $lines = explode("\n", $schema[0]['Create Table']);

            if ($data && $sizeof > 0) {
                //if table is language
                $id_language = 0;
                if (strpos($schema[0]['Table'], 'lang') !== false) {
                    $data_language[$schema[0]['Table']] = array();
                    $i = 1;
                    while ($row = DB::getInstance()->nextRow($data)) {
                        $s = '(';
                        foreach ($row as $field => $value) {
                            if ($field == 'id_lang') {
                                $id_language = $value;
                                $tmp = "'".pSQL('LEO_ID_LANGUAGE', true)."',";
                            } else if ($field == 'ID_SHOP') {
                                $tmp = "'".pSQL('ID_SHOP', true)."',";
                            } else {
                                $tmp = "'".pSQL($value, true)."',";
                            }

                            if ($tmp != "'',") {
                                $s .= $tmp;
                            } else {
                                foreach ($lines as $line) {
                                    if (strpos($line, '`'.$field.'`') !== false) {
                                        if (preg_match('/(.*NOT NULL.*)/Ui', $line)) {
                                            $s .= "'',";
                                        } else {
                                            $s .= 'NULL,';
                                        }
                                        break;
                                    }
                                }
                            }
                        }

                        if (!isset($list_lang[$id_language])) {
                            continue;
                        }

                        if (!isset($data_language[$schema[0]['Table']][Tools::strtolower($list_lang[$id_language])])) {
                            $data_language[$schema[0]['Table']][Tools::strtolower($list_lang[$id_language])] = 'INSERT INTO `'.$table_name."` VALUES\n";
                        }

                        $s = rtrim($s, ',');
                        if ($i % 200 == 0 && $i < $sizeof) {
                            $s .= ");\nINSERT INTO `".$table_name."` VALUES\n";
                        } else {
                            $s .= "),\n";
                        }
                        $data_language[$schema[0]['Table']][Tools::strtolower($list_lang[$id_language])] .= $s;
                    }
                } else {
                    //normal table
                    $create_table .= $this->createInsert($data, $table_name, $lines, $sizeof);
                }
            }
        }

        $create_table = str_replace('$', '\$', $create_table);
        $create_table = '$query = "'.$create_table;
        //foreach by table
        $tpl = array();

        fwrite($fp, $create_table."\";\n");
        if ($data_language) {
            foreach ($data_language as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    if (!isset($tpl[$key1])) {
                        $tpl[$key1] = Tools::substr($value1, 0, -2).";\n";
                    } else {
                        $tpl[$key1] .= Tools::substr($value1, 0, -2).";\n";
                    }
                }
            }
            foreach ($tpl as $key => $value) {
                if ($data_with_lang) {
                    $data_with_lang .= ',"'.$key.'"=>'.'"'.$value.'"';
                } else {
                    $data_with_lang .= '"'.$key.'"=>'.'"'.$value.'"';
                }
            }

            //delete base uri when export
            $data_with_lang = str_replace('$', '\$', $data_with_lang);
            $data_with_lang = '$dataLang = Array('.$data_with_lang;

            fwrite($fp, $data_with_lang.');');
        }
        fclose($fp);
    }
    
    /**
     * sub function of back-up database
     */
    public function createInsert($data, $table_name, $lines, $sizeof)
    {
        $data_no_lang = 'INSERT INTO `'.$table_name."` VALUES\n";
        $i = 1;
        while ($row = DB::getInstance()->nextRow($data)) {
            $s = '(';
            foreach ($row as $field => $value) {
                if ($field == 'ID_SHOP') {
                    $tmp = "'".pSQL('ID_SHOP', true)."',";
                } else {
                    $tmp = "'".pSQL($value, true)."',";
                }
                if ($tmp != "'',") {
                    $s .= $tmp;
                } else {
                    foreach ($lines as $line) {
                        if (strpos($line, '`'.$field.'`') !== false) {
                            if (preg_match('/(.*NOT NULL.*)/Ui', $line)) {
                                $s .= "'',";
                            } else {
                                $s .= 'NULL,';
                            }
                            break;
                        }
                    }
                }
            }
            $s = rtrim($s, ',');
            if ($i % 200 == 0 && $i < $sizeof) {
                $s .= ");\nINSERT INTO `".$table_name."` VALUES\n";
            } elseif ($i < $sizeof) {
                $s .= "),\n";
            } else {
                $s .= ");\n";
            }
            $data_no_lang .= $s;

            ++$i;
        }
        return $data_no_lang;
    }

    public function renderForm()
    {
        $list_all_hooks = $this->renderListAllHook(ApPageSetting::getHook('all'));
        $list_header_hooks = (Configuration::get('APPAGEBUILDER_HEADER_HOOK')) ?
                Configuration::get('APPAGEBUILDER_HEADER_HOOK') : implode(',', ApPageSetting::getHook('header'));
        $list_content_hooks = (Configuration::get('APPAGEBUILDER_CONTENT_HOOK')) ?
                Configuration::get('APPAGEBUILDER_CONTENT_HOOK') : implode(',', ApPageSetting::getHook('content'));
        $list_footer_hooks = (Configuration::get('APPAGEBUILDER_FOOTER_HOOK')) ?
                Configuration::get('APPAGEBUILDER_FOOTER_HOOK') : implode(',', ApPageSetting::getHook('footer'));
        $list_product_hooks = (Configuration::get('APPAGEBUILDER_PRODUCT_HOOK')) ?
                Configuration::get('APPAGEBUILDER_PRODUCT_HOOK') : implode(',', ApPageSetting::getHook('product'));
        $form_general = array(
            'legend' => array(
                'title' => $this->l('General Settings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'name' => 'dump_name',
                    'html_content' => '<div class="alert alert-info ap-html-full">'
                    .$this->l('Loading JS and CSS library').'</div>',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Load Jquery Stellar Library'),
                    'name' => 'APPAGEBUILDER_LOAD_STELLAR',
                    'desc' => $this->l('This script is use for parallax. If you load it in other plugin please turn it off'),
                    'is_bool' => true,
                    'values' => ApPageSetting::returnYesNo(),
                    'default' => '0',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Load Owl Carousel Library'),
                    'name' => 'APPAGEBUILDER_LOAD_OWL',
                    'desc' => $this->l('This script is use for Carousel. If you load it in other plugin please turn it off'),
                    'is_bool' => true,
                    'values' => ApPageSetting::returnYesNo(),
                    'default' => '0',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Load Swiper Carousel Library'),
                    'name' => 'APPAGEBUILDER_LOAD_SWIPER',
                    'desc' => $this->l('This script is use for Carousel. If you load it in other plugin please turn it off'),
                    'is_bool' => true,
                    'values' => ApPageSetting::returnYesNo(),
                    'default' => '0',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Load Waypoints Library'),
                    'name' => 'APPAGEBUILDER_LOAD_WAYPOINTS',
                    'desc' => $this->l('This script is use for Animated. If you load it in other plugin please turn it off'),
                    'is_bool' => true,
                    'values' => ApPageSetting::returnYesNo(),
                    'default' => '0',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Load Instafeed Library'),
                    'name' => 'APPAGEBUILDER_LOAD_INSTAFEED',
                    'default' => 0,
                    'values' => ApPageSetting::returnYesNo(),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Load Video HTML5 Library'),
                    'name' => 'APPAGEBUILDER_LOAD_HTML5VIDEO',
                    'default' => 0,
                    'values' => ApPageSetting::returnYesNo(),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Load Full Page Library'),
                    'name' => 'APPAGEBUILDER_LOAD_FULLPAGEJS',
                    'default' => 0,
                    'values' => ApPageSetting::returnYesNo(),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Load Magic360 Library'),
                    'name' => 'APPAGEBUILDER_LOAD_IMAGE360',
                    'default' => 0,
                    'values' => ApPageSetting::returnYesNo(),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Load Image Hotpot Library'),
                    'name' => 'APPAGEBUILDER_LOAD_IMAGEHOTPOT',
                    'default' => 0,
                    'values' => ApPageSetting::returnYesNo(),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Load Cookie Library'),
                    'name' => 'APPAGEBUILDER_LOAD_COOKIE',
                    'default' => 0,
                    'values' => ApPageSetting::returnYesNo(),
                    'desc' => $this->l('Yes : Load library JS jquery.cooki-plugin.js'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Load Product Zoom Library'),
                    'name' => 'APPAGEBUILDER_LOAD_PRODUCTZOOM',
                    'default' => 1,
                    'values' => ApPageSetting::returnYesNo(),
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'name' => 'dump_name',
                    'html_content' => '<div class="alert alert-info ap-html-full">'
                    .$this->l('Functions').'</div>',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Save Profile Multithrearing'),
                    'name' => 'APPAGEBUILDER_SAVE_MULTITHREARING',
                    'default' => 0,
                    'values' => ApPageSetting::returnYesNo(),
                    'desc' => $this->l('Yes - use AJAX SUBMIT, not load page again, keep your data safe'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Save Profile Submit'),
                    'name' => 'APPAGEBUILDER_SAVE_SUBMIT',
                    'default' => 0,
                    'values' => ApPageSetting::returnYesNo(),
                    'desc' => $this->l('Yes - use Normal SUBMIT and load page again. No - use AJAX SUBMIT, not load page again.'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Save profile and postion id to cookie'),
                    'name' => 'APPAGEBUILDER_COOKIE_PROFILE',
                    'default' => 0,
                    'desc' => $this->l('That is only for demo, please turn off it in live site'),
                    'values' => ApPageSetting::returnYesNo(),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Random Product'),
                    'desc' => $this->l('Number of time create random product when using Prestashop_CACHED and showing product carousel has order by RANDOM'),
                    'name' => 'APPAGEBUILDER_PRODUCT_MAX_RANDOM',
                    'class' => '',
                    'default' => 2,
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'name' => 'dump_name',
                    'html_content' => '<div class="alert alert-info ap-html-full">'
                    .$this->l('AJAX SETTINGS').
                    '<input type="hidden" id="position-hook-select"/></div>',
                ),
//                array(
//                    'type' => 'switch',
//                    'label' => $this->l('Use Ajax Feature'),
//                    'name' => 'APPAGEBUILDER_LOAD_AJAX',
//                    'default' => 1,
//                    'values' => ApPageSetting::returnYesNo(),
//                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('AJAX Show Category Qty - Enable'),
                    'name' => 'APPAGEBUILDER_LOAD_PN',
                    'default' => 0,
                    'values' => ApPageSetting::returnYesNo(),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('AJAX Show Category Qty - Code'),
                    'name' => 'APPAGEBUILDER_LOAD_TPN',
                    'cols' => 100,
                    'hint' => $this->l('Add this CODE to THEME_NAME/modules/ps_categorytree/views/templates/hook/ ps_categorytree.tpl file'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('AJAX Show More Product Image - Enable'),
                    'name' => 'APPAGEBUILDER_LOAD_TRAN',
                    'default' => 0,
                    'values' => ApPageSetting::returnYesNo(),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('AJAX Show More Product Image - Code'),
                    'name' => 'APPAGEBUILDER_LOAD_TTRAN',
                    'cols' => 100,
                    'hint' => $this->l('Add this CODE to THEME_NAME/templates/catalog/_partials/miniatures/product.tpl file'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('AJAX Show Multiple Product Images - Enable'),
                    'name' => 'APPAGEBUILDER_LOAD_IMG',
                    'default' => 0,
                    'values' => ApPageSetting::returnYesNo(),
                ),
                array(
                    'type' => 'textarea',
//                    'label' => $this->l('You can add this code in tpl file of module you want to show Multiple Product Image'),
                    'label' => $this->l('AJAX Show Multiple Product Images - Code'),
                    'name' => 'APPAGEBUILDER_LOAD_TIMG',
                    'cols' => 100,
                    'hint' => $this->l('Add this CODE to THEME_NAME/templates/catalog/_partials/miniatures/product.tpl file'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('AJAX Show Count Down Product - Enable'),
                    'name' => 'APPAGEBUILDER_LOAD_COUNT',
                    'default' => 0,
                    'values' => ApPageSetting::returnYesNo(),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('AJAX Show Count Down Product  - Code'),
                    'name' => 'APPAGEBUILDER_LOAD_TCOUNT',
                    'cols' => 100,
                    'hint' => $this->l('Add this CODE to THEME_NAME/templates/catalog/_partials/miniatures/product.tpl file'),
                ),
//                array(
//                    'type' => 'switch',
//                    'label' => $this->l('Show Discount Color'),
//                    'name' => 'APPAGEBUILDER_LOAD_ACOLOR',
//                    'default' => 1,
//                    'values' => ApPageSetting::returnYesNo(),
//                ),
//                array(
//                    'type' => 'textarea',
//                    'label' => $this->l('You can add this code in tpl file of module you want to show color discount'),
//                    'name' => 'APPAGEBUILDER_LOAD_TCOLOR',
//                    'cols' => 100,
//                ),
//                array(
//                    'type' => 'textarea',
//                    'label' => $this->l('For color (Ex: 10:#ff0000,20:#152ddb,40:#ffee001) '),
//                    'name' => 'APPAGEBUILDER_LOAD_COLOR',
//                    'cols' => 100
//                ),
//                array(
//                    'type' => 'textarea',
//                    'label' => $this->l('If you want my script run fine with Layered navigation block.
//                                Please copy to override file modules/blocklayered/blocklayered.js to folder
//                                themes/TEMPLATE_NAME/js/modules/blocklayered/blocklayered.js.
//                                Then find function reloadContent(params_plus).'),
//                    'name' => 'APPAGEBUILDER_LOAD_RTN',
//                    'cols' => 100
//                ),
//                array(
//                    'type' => 'textarea',
//                    'label' => $this->l('For color (Ex: 10:#ff0000,20:#152ddb,40:#ffee001)'),
//                    'name' => 'APPAGEBUILDER_COLOR',
//                    'default' => '',
//                    'cols' => 100
//                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'name' => 'dump_name',
                    'html_content' => '<br/><div class="alert alert-info ap-html-full">'
                    .$this->l('Setting hook in positions (This setting will apply for all profiles).')
                    .'<div class="list-all-hooks">'.$this->l('Default all hooks: [').$list_all_hooks.' ]</div>'
                    .'</div>',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Hooks in header'),
                    'name' => 'APPAGEBUILDER_HEADER_HOOK',
                    'class' => '',
                    'default' => $list_header_hooks
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Hooks in content'),
                    'name' => 'APPAGEBUILDER_CONTENT_HOOK',
                    'class' => '',
                    'default' => $list_content_hooks
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Hooks in footer'),
                    'name' => 'APPAGEBUILDER_FOOTER_HOOK',
                    'class' => '',
                    'default' => $list_footer_hooks
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Hooks in product-footer'),
                    'name' => 'APPAGEBUILDER_PRODUCT_HOOK',
                    'class' => '',
                    'default' => $list_product_hooks
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'name' => 'html_content_de',
                    'html_content' => '<input type="hidden" name="hook_header_old" id="hook_header_old"/>
                                                <input type="hidden" name="hook_content_old" id="hook_content_old"/>
                                                <input type="hidden" name="hook_footer_old" id="hook_footer_old"/>
                                                <input type="hidden" name="hook_product_old" id="hook_product_old"/>
                                                <input type="hidden" name="is_change" id="is_change" value=""/>
                                                <input type="hidden" id="message_confirm" value="'
                    .$this->l('The hook is changing. Click OK will save new config hooks and will
                                                                    REMOVE ALL current data widget. Are you sure?').'"/>',
                ),
            ),
            'submit' => array(
                'id' => 'btn-save-appagebuilder',
                'title' => $this->l('Save'),
            )
        );
     
        $form_extrafield = array(

            'legend' => array(
                'title' => $this->l('Extrafield Settings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'name' => 'dump_name',
                    'html_content' => '<br/><div class="alert alert-info ap-html-full">'
                    .$this->l('This config is only apply for advance user.')
                    .'<div class="list-all-hooks">We will create new fileds for category and product, then you can use it to show in layout</div>'
                    .'<div class="list-all-hooks">Have 2 type of database: 1. text and 2. editor</div>'
                    .'<div class="list-all-hooks">When you change data of this field, all data of extrafied will lost</div>'
                    .'</div>',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Product Extra Text Field'),
                    'name' => 'APPAGEBUILDER_PRODUCT_TEXTEXTRA',
                    'class' => '',
                    'desc'  => $this->l('Do not contain space and special charactor. Example: sub-name, sub-title'),
                    'default' => ''
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Product Extra Editor Field'),
                    'name' => 'APPAGEBUILDER_PRODUCT_EDITOREXTRA',
                    'class' => '',
                    'desc'  => $this->l('Do not contain space and special charactor. Example: sub-name, sub-title'),
                    'default' => ''
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Category Extra Editor Field'),
                    'name' => 'APPAGEBUILDER_CATEGORY_TEXTEXTRA',
                    'class' => '',
                    'desc'  => $this->l('Do not contain space and special charactor. Example: sub-name, sub-title'),
                    'default' => ''
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Category Extra Editor Field'),
                    'name' => 'APPAGEBUILDER_CATEGORY_EDITOREXTRA',
                    'class' => '',
                    'desc'  => $this->l('Do not contain space and special charactor. Example: sub-name, sub-title'),
                    'default' => ''
                )
            ),
            'submit' => array(
                'id' => 'btn-save-extrafield',
                'title' => $this->l('Save'),
            )
        );
        $fields_form = array(
            'form' => $form_general
        );
        $fields_form1 = array(
            'form' => $form_extrafield
        );
        $data = $this->getConfigFieldsValues($form_general, $form_extrafield);
        // Check existed the folder root store resources of module
        $path_img = apPageHelper::getImgThemeDir();
        if (!file_exists($path_img)) {
            mkdir($path_img, 0755, true);
        }
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
                Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitApPageBuilder';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
                .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $data,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form, $fields_form1));
    }

    private function renderListAllHook($arr)
    {
        $html = '';
        if ($arr) {
            foreach ($arr as $item) {
                $html .= "<a href='javascript:;'>$item</a>";
            }
        }
        return $html;
    }

    public function hookPagebuilderConfig($param)
    {
        $cf = $param['configName'];
        if ($cf == 'profile' || $cf == 'header' || $cf == 'footer' || $cf == 'content' || $cf == 'product' || $cf == 'product_builder') {
            #GET DETAIL PROFILE
            $cache_name = 'pagebuilderConfig'.'|'.$param['configName'];
            $cache_id = $this->getCacheId($cache_name);
            if (!$this->isCached('module:appagebuilder/views/templates/hook/config.tpl', $cache_id)) {
                $ap_type = $cf;

                if ($cf == 'profile') {
                    $ap_type = 'id_appagebuilder_profiles';
                } else if ($cf == 'product_builder') {
                    $ap_type = 'plist_key';
                }
                $this->smarty->assign(
                    array(
                        'ap_cfdata' => $this->getConfigData($cf),
                        'ap_cf' => $cf,
                        'ap_type' => $ap_type,
                        'ap_controller' => apPageHelper::getPageName(),
                        'ap_current_url' => Context::getContext()->link->getPageLink('index', true),
                    )
                );
            }
            return $this->display(__FILE__, 'config.tpl', $cache_id);
        }

        if (!$this->product_active) {
            $this->product_active = ApPageBuilderProductsModel::getActive();
        }
        if ($cf == 'productClass') {
            // validate module
            return $this->product_active['class'];
        }
        if ($cf == 'productKey') {
            $tpl_file = apPageHelper::getConfigDir('theme_profiles') . $this->product_active['plist_key'].'.tpl';
            if (is_file($tpl_file)) {
                return $this->product_active['plist_key'];
            }
            return;
        }
        //nghiatd
        if ($cf == 'productLayout') {
            $id_product = Tools::getValue('id_product');
            if ($id_product) {
                $id_shop = Context::getContext()->shop->id;
                $sql = 'SELECT page from `'._DB_PREFIX_.'appagebuilder_page` where id_product = \''.(int)$id_product.'\' AND id_shop = \''.(int)$id_shop.'\'';
                $layout = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                return $layout;
            } else {
                return '';
            }
        }
        if ($cf == 'categoryLayout') {
            $id_category = Tools::getValue('id_category');
            if ($id_category) {
                $id_shop = Context::getContext()->shop->id;
                $sql = 'SELECT page from `'._DB_PREFIX_.'appagebuilder_page` where id_category = \''.(int)$id_category.'\' AND id_shop = \''.(int)$id_shop.'\'';
                $layout = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                return $layout;
            } else {
                return '';
            }
        }
        //DONGND:: get class of category layout
        if ($cf == 'classCategoryLayout') {
            $id_category = Tools::getValue('id_category');
            if ($id_category) {
                $id_shop = Context::getContext()->shop->id;
                $sql = 'SELECT pr.`class` from `'._DB_PREFIX_.'appagebuilder_page` pa INNER JOIN `'._DB_PREFIX_.'appagebuilder_products` pr ON (pa.`page` = pr.`plist_key`) where pa.id_category = \''.(int)$id_category.'\' AND pa.id_shop = \''.(int)$id_shop.'\'';
                $class_category_layout = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                return $class_category_layout;
            } else {
                return '';
            }
        }
    }

    public function getConfigData($cf)
    {
        if ($cf == 'profile') {
            $id_lang = (int)Context::getContext()->language->id;
            $sql = 'SELECT p.`id_appagebuilder_profiles` AS `id`, p.`name`, ps.`active`, pl.friendly_url FROM `'._DB_PREFIX_.'appagebuilder_profiles` p '
                    .' INNER JOIN `'._DB_PREFIX_.'appagebuilder_profiles_shop` ps ON (ps.`id_appagebuilder_profiles` = p.`id_appagebuilder_profiles`)'
                    .' INNER JOIN `'._DB_PREFIX_.'appagebuilder_profiles_lang` pl ON (pl.`id_appagebuilder_profiles` = p.`id_appagebuilder_profiles`) AND pl.id_lang='.(int)$id_lang
                    .' WHERE ps.id_shop='.(int)Context::getContext()->shop->id;
        } else if ($cf == 'product_builder') {
            $sql = 'SELECT p.`plist_key` AS `id`, p.`name`, ps.`active`'
                    .' FROM `'._DB_PREFIX_.'appagebuilder_products` p '
                    .' INNER JOIN `'._DB_PREFIX_.'appagebuilder_products_shop` ps '
                    .' ON (ps.`id_appagebuilder_products` = p.`id_appagebuilder_products`)'
                    .' WHERE ps.id_shop='.(int)Context::getContext()->shop->id;
        } else {
            $sql = 'SELECT p.`id_appagebuilder_positions` AS `id`, p.`name`'
                    .' FROM `'._DB_PREFIX_.'appagebuilder_positions` p '
                    .' INNER JOIN `'._DB_PREFIX_.'appagebuilder_positions_shop` ps '
                    .' ON (ps.`id_appagebuilder_positions` = p.`id_appagebuilder_positions`)'
                    .' WHERE p.`position` = \''.PSQL($cf).'\' AND ps.id_shop='.(int)Context::getContext()->shop->id;
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($result as &$val) {
            if ($cf == 'profile') {
                $val['active'] = 0;
                if ($val['id'] == $this->profile_data['id_appagebuilder_profiles']) {
                    $val['active'] = 1;
                }
            } else if ($cf == 'product_builder') {
                if (Tools::getIsset('plist_key')) {
                    $val['active'] = 0;
                    if ($val['id'] == Tools::getValue('plist_key')) {
                        $val['active'] = 1;
                    }
                }
            } else {
                $val['active'] = 0;
                if (Tools::getIsset($cf)) {
                    if ($val['id'] == Tools::getValue($cf)) {
                        $val['active'] = 1;
                    }
                } else {
                    if ($val['id'] == $this->profile_data[$cf]) {
                        $val['active'] = 1;
                    }
                }
            }
        }
        return $result;
    }

    public function getConfigFieldsValues($form_general, $form_extrafield)
    {
        $this->context->controller->addCss(apPageHelper::getCssDir().'style.css');
        $result = array();
       
        foreach ($form_general['input'] as $form) {
            //$form['name'] = isset($form['name']) ? $form['name'] : '';
            if (Configuration::hasKey($form['name'])) {
                $result[$form['name']] = Tools::getValue($form['name'], Configuration::get($form['name']));
            } else {
                $result[$form['name']] = Tools::getValue($form['name'], isset($form['default']) ? $form['default'] : '');
            }
        }
       
        foreach ($form_extrafield['input'] as $form) {
            //$form['name'] = isset($form['name']) ? $form['name'] : '';
            if (Configuration::hasKey($form['name'])) {
                $result[$form['name']] = Configuration::get($form['name']);
            } else {
                $result[$form['name']] = Tools::getValue($form['name'], isset($form['default']) ? $form['default'] : '');
            }
        }

        $result['APPAGEBUILDER_LOAD_TCOUNT'] = '<div class="leo-more-cdown" data-idproduct="{$product.id_product}"></div>';
        $result['APPAGEBUILDER_LOAD_TTRAN'] = '                <!-- case 1 get second image of product except image_cover -->'."\n";
        $result['APPAGEBUILDER_LOAD_TTRAN'] .= '<span class="second-image-style product-additional" data-idproduct="{$product.id_product}"></span>';
        $result['APPAGEBUILDER_LOAD_TTRAN'] .= "\n\n". '                <!-- case 2 get second image of product_attribute ( if product not attribute get second image of product ). -->'."\n";
        $result['APPAGEBUILDER_LOAD_TTRAN'] .= '<span class="second-image-style product-attribute-additional" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" data-id-image="{$product.cover.id_image}"></span>';
        $result['APPAGEBUILDER_LOAD_TTRAN'] .= "\n\n". '                <!-- case 3 get second image of product except image_showed. -->'."\n";
        $result['APPAGEBUILDER_LOAD_TTRAN'] .= '<span class="second-image-style product-all-additional" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" data-id-image="{$product.cover.id_image}"></span>';
        $result['APPAGEBUILDER_LOAD_TIMG'] = '<div class="leo-more-info" data-idproduct="{$product.id_product}"></div>';
        $result['APPAGEBUILDER_LOAD_TPN'] = '<span id="leo-cat-{$node.id}" style="display:none" class="leo-qty leo-cat-{$node.id} badge pull-right" data-str="{l s=\' item(s)\' d=\'Shop.Theme.Catalog\'}"></span>';

        return $result;
    }

    /**
     * Widget ApTab
     */
    public function fontContent($assign, $tpl_name)
    {
        // Setting live edit mode
        $assign['apLiveEdit'] = '';
        $assign['apLiveEditEnd'] = '';
        $is_live = Tools::getIsset('ap_live_edit') ? Tools::getValue('ap_live_edit') : '';
        if ($is_live) {
            $live_token = Tools::getIsset('liveToken') ? Tools::getValue('liveToken') : '';
            if (!$this->checkLiveEditAccess($live_token)) {
                Tools::redirect('index.php?controller=404');
            }
            $cookie = new Cookie('url_live_back');
            $url_edit_profile = $cookie->variable_name;

            $id_profile = ApPageBuilderProfilesModel::getIdProfileFromRewrite();
            $cookie = new Cookie('ap_id_profile');
            if (!$id_profile) {
                if ($cookie->variable_name) {
                    $url_edit_profile .= '&id_appagebuilder_profiles='.$cookie->variable_name;
                }
            } else {
                $id_profile = '';
                // Restor id_profile to cookie
                $cookie = new Cookie('ap_id_profile');
                $cookie->setExpire(time() + 60 * 60);
                $cookie->variable_name = $id_profile;
                $cookie->write();
                $url_edit_profile .= '&id_appagebuilder_profiles='.$id_profile;
            }
            $assign['urlEditProfile'] = $url_edit_profile;
            $assign['isLive'] = $is_live;
            $assign['apLiveEdit'] = '<div class="cover-live-edit"><a class="link-to-back-end" href="';
            if (isset($assign['formAtts']) && isset($assign['formAtts']['form_id']) && $assign['formAtts']['form_id']) {
                // validate module
                $assign['apLiveEdit'] .= $url_edit_profile.'#'.$assign['formAtts']['form_id'];
            } else {
                $assign['apLiveEdit'] .= $url_edit_profile;
            }
            $assign['apLiveEdit'] .= '" target="_blank"><i class="icon-pencil"></i> <span>Edit</span></a>';
            $assign['apLiveEditEnd'] = '</div>';
        }
        
        if ($assign) {
            foreach ($assign as $key => $ass) {
                $this->smarty->assign(array($key => $ass));
            }
        }
		$override_folder = '';
		if (isset($assign['formAtts']['override_folder']) && $assign['formAtts']['override_folder'] != '')
		{
			$override_folder = $assign['formAtts']['override_folder'];
		}
        $tpl_file = apPageHelper::getTemplate($tpl_name, $override_folder);
        $content = $this->display(__FILE__, $tpl_file);
        return $content;
    }

    public function checkLiveEditAccess($live_token)
    {
        $cookie = new Cookie('ap_token');
        return $live_token === $cookie->variable_name;
    }

    /**
     * $page_number = 0, $nb_products = 10, $count = false, $order_by = null, $order_way = null
     */
    public function getProductsFont($params)
    {
        $id_lang = $this->context->language->id;
        $context = Context::getContext();
        //assign value from params
        $p = isset($params['page_number']) ? $params['page_number'] : 1;
        if ($p < 0) {
            $p = 1;
        }
        $n = isset($params['nb_products']) ? $params['nb_products'] : 10;
        if ($n < 1) {
            $n = 10;
        }
        $order_by = isset($params['order_by']) ? Tools::strtolower($params['order_by']) : 'position';
        $order_way = isset($params['order_way']) ? $params['order_way'] : 'ASC';
        $random = false;
        if ($order_way == 'random') {
            $random = true;
        }
        $get_total = isset($params['get_total']) ? $params['get_total'] : false;
        $order_by_prefix = false;
        if ($order_by == 'id_product' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'product_shop';
        } else if ($order_by == 'reference') {
            $order_by_prefix = 'p';
        } else if ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'manufacturer') {
            $order_by_prefix = 'm';
            $order_by = 'name';
        } elseif ($order_by == 'position') {
            $order_by = 'date_add';
            $order_by_prefix = 'product_shop';
//            $order_by_prefix = 'cp';
        } elseif ($order_by == 'quantity') {
            $order_by_prefix = 'ps';    // ps_product_sale
        }
        if ($order_by == 'price') {
            $order_by = 'orderprice';
        }
        $active = 1;
        if (!Validate::isBool($active) || !Validate::isOrderBy($order_by)) {
            die(Tools::displayError());
        }
        //build where
        $where = '';
        $sql_join = '';
        $sql_group = '';

        $value_by_categories = isset($params['value_by_categories']) ? $params['value_by_categories'] : 0;
        if ($value_by_categories) {
            $id_categories = isset($params['categorybox']) ? $params['categorybox'] : '';
            $id_categories = apPageHelper::addonValidInt( $id_categories );         # We validate id_categories in apPageHelper::addonValidInt function . This function is used at any where
            
            if (isset($params['category_type']) && $params['category_type'] == 'default') {
                $where .= ' AND product_shop.`id_category_default` IN ('.pSQL($id_categories).')';
            } else {
                $sql_join .= ' INNER JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product= p.`id_product` )';
                
                $where .= ' AND cp.`id_category` IN ('.pSQL($id_categories).')';
                $sql_group = ' GROUP BY p.id_product';
            }
        }
        $value_by_supplier = isset($params['value_by_supplier']) ? $params['value_by_supplier'] : 0;
        if ($value_by_supplier && isset($params['supplier'])) {
            $id_suppliers = apPageHelper::addonValidInt( $params['supplier'] );          # We validate id_categories in apPageHelper::addonValidInt function. This function is used at any where
            $where .= ' AND p.id_supplier IN ('.pSQL($id_suppliers).')';
        }
        $value_by_product_id = isset($params['value_by_product_id']) ? $params['value_by_product_id'] : 0;
        if ($value_by_product_id && isset($params['product_id'])) {
            $temp = explode(',', $params['product_id']);
            foreach ($temp as $key => $value) {
                // validate module
                $temp[$key] = (int)$value;
            }

            $product_id = implode(',', array_map('intval', $temp));
            $where .= ' AND p.id_product '.(strpos($product_id, ',') === false ? '= '.(int)$product_id : 'IN ('.pSQL($product_id).')');
	
        }

        $value_by_manufacture = isset($params['value_by_manufacture']) ? $params['value_by_manufacture'] : 0;
        if ($value_by_manufacture && isset($params['manufacture'])) {
            $id_manufactures = apPageHelper::addonValidInt( $params['manufacture'] );          # We validate id_categories in apPageHelper::addonValidInt function. This function is used at any where
            $where .= ' AND p.id_manufacturer IN ('.pSQL($id_manufactures).')';
        }
        $product_type = isset($params['product_type']) ? $params['product_type'] : '';
        $value_by_product_type = isset($params['value_by_product_type']) ? $params['value_by_product_type'] : 0;
        if ($value_by_product_type && $product_type == 'new_product') {
            $where .= ' AND product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ?
                                            (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'"';
        }
        //home feature
        if ($value_by_product_type && $product_type == 'home_featured') {
            $ids = array();
            $category = new Category(Context::getContext()->shop->getCategory(), (int)Context::getContext()->language->id);
            $result = $category->getProducts((int)Context::getContext()->language->id, 1, $n*($p+1), 'position');   // Load more product $n*$p, hidden
            foreach ($result as $product) {
                $ids[$product['id_product']] = 1;
            }
            $ids = array_keys($ids);
            sort($ids);
            $ids = count($ids) > 0 ? implode(',', $ids) : 'NULL';
            $where .= ' AND p.`id_product` IN ('.$ids.')';
        }
        //special or price drop
        if ($value_by_product_type && $product_type == 'price_drop') {
            $current_date = date('Y-m-d H:i:s');
            $id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
            $ids = Address::getCountryAndState($id_address);
            $id_country = $ids['id_country'] ? $ids['id_country'] : Configuration::get('PS_COUNTRY_DEFAULT');
            $id_country = (int)$id_country;
            $ids_product = SpecificPrice::getProductIdByDate($context->shop->id, $context->currency->id, $id_country, $context->customer->id_default_group, $current_date, $current_date, 0, false);
            $tab_id_product = array();
            foreach ($ids_product as $product) {
                if (is_array($product)) {
                    $tab_id_product[] = (int)$product['id_product'];
                } else {
                    $tab_id_product[] = (int)$product;
                }
            }
            $where .= ' AND p.`id_product` IN ('.((is_array($tab_id_product) && count($tab_id_product)) ? implode(', ', $tab_id_product) : 0).')';
        }
        
        $sql = 'SELECT p.*, product_shop.*, p.`reference`, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
                product_attribute_shop.id_product_attribute,
                product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity,
                pl.`description`, pl.`description_short`, pl.`available_now`,
                pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`,
                image_shop.`id_image`,
                il.`legend`, m.`name` AS manufacturer_name, cl.`name` AS category_default,
                DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
                INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
                DAY)) > 0 AS new, product_shop.price AS orderprice';

        if ($value_by_product_type && $product_type == 'best_sellers') {
            $sql .= ' FROM `'._DB_PREFIX_.'product_sale` ps';
            $sql .= ' LEFT JOIN `'._DB_PREFIX_.'product` p ON ps.`id_product` = p.`id_product`';
        } else {
            $sql .= ' FROM `'._DB_PREFIX_.'product` p';
        }
        
        $sql .= ' INNER JOIN '._DB_PREFIX_.'product_shop product_shop ON (product_shop.id_product = p.id_product AND product_shop.id_shop = '.(int)$context->shop->id.')
          LEFT JOIN '._DB_PREFIX_.'product_attribute_shop product_attribute_shop        ON p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.'
            '.ProductCore::sqlStock('p', 'product_attribute_shop', false, $context->shop).'
          LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (product_shop.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
          LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
          LEFT JOIN '._DB_PREFIX_.'image_shop image_shop    ON (image_shop.id_product = p.id_product AND image_shop.id_shop = '.(int)$context->shop->id.' AND image_shop.cover=1)
          LEFT JOIN '._DB_PREFIX_.'image_lang il    ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
          LEFT JOIN '._DB_PREFIX_.'manufacturer m   ON m.`id_manufacturer` = p.`id_manufacturer`';
        $sql .= $sql_join;

        $sql .= ' WHERE product_shop.`id_shop` = '.(int)$context->shop->id.'
                AND product_shop.`active` = 1
                AND product_shop.`visibility` IN ("both", "catalog")'
                .$where;

        $sql .= $sql_group;

        if ($random === true) {
    //            $sql .= ' ORDER BY product_shop.date_add '.(!$get_total ? ' LIMIT '.(int)$n : '');
            $sql .= ' ORDER BY RAND() '.(!$get_total ? ' LIMIT '.(int)$n : '');
        } else {
            $order_way = Validate::isOrderWay($order_way) ? Tools::strtoupper($order_way) : 'ASC';      // $order_way Validate::isOrderWay()
            $sql .= ' ORDER BY '.(!empty($order_by_prefix) ? '`'.pSQL($order_by_prefix).'`.' : '').'`'.bqSQL($order_by).'` '.pSQL($order_way)
                    .(!$get_total ? ' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n : '');
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($order_by == 'orderprice') {
            Tools::orderbyPrice($result, $order_way);
        }
        if (!$result) {
            return array();
        }
        /* Modify SQL result */
        $this->context->controller->addColorsToProductList($result);
        return Product::getProductsProperties($id_lang, $result);
    }

    //DONGND:: register hook back-end
    public function hookActionAdminControllerSetMedia()
    {
        Media::addJsDef(
            array(
                'appagebuilder_module_dir' => $this->_path,
                'appagebuilder_listshortcode_url' => $this->context->link->getAdminLink('AdminApPageBuilderShortcode').'&get_listshortcode=1',
            )
        );
        Configuration::updateValue('shortcode_url_add', $this->context->link->getAdminLink('AdminApPageBuilderShortcode'));
        
        $this->autoRestoreSampleData();
        
        if (Tools::getValue('configure') == 'appagebuilder') {
            Media::addJsDef(array('autoload_func_name' => 'SetButonSaveToHeader'));
            $this->context->controller->addJs(apPageHelper::getJsAdminDir().'admin/autoload.js');
            
            Media::addJsDef(array('TopSaveAndStay_Name' => 'submitApPageBuilder'));
            $this->context->controller->addJs(apPageHelper::getJsAdminDir().'admin/function.js');
        }
    }

    public function hookdisplayHeader()
    {
        apPageHelper::autoUpdateModule();
        
        if (isset(Context::getContext()->controller->controller_type) && in_array(Context::getContext()->controller->controller_type, array('front', 'modulefront'))) {
            # WORK AT FRONTEND
            apPageHelper::loadShortCode(_PS_THEME_DIR_);

            $this->profile_data = ApPageBuilderProfilesModel::getActiveProfile('index');
            $this->profile_param = Tools::jsonDecode($this->profile_data['params'], true);
            $this->setFullwidthHook();

            # FIX 1.7
            apPageHelper::setGlobalVariable($this->context);
        }
        
        
        if (Configuration::get('APPAGEBUILDER_LOAD_WAYPOINTS')) {
            $uri = apPageHelper::getCssDir().'animate.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            
            $uri = apPageHelper::getJsDir().'waypoints.min.js';
            $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));
        }
        if (Configuration::get('APPAGEBUILDER_LOAD_INSTAFEED')) {
            $uri = apPageHelper::getJsDir().'instafeed.min.js';
            $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));
        }
        if (Configuration::get('APPAGEBUILDER_LOAD_STELLAR')) {
            $uri = apPageHelper::getJsDir().'jquery.stellar.js';
            $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));
        }
        if (Configuration::get('APPAGEBUILDER_LOAD_OWL')) {
            $uri = apPageHelper::getCssDir().'owl.carousel.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            $uri = apPageHelper::getCssDir().'owl.theme.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            
            $uri = apPageHelper::getJsDir().'owl.carousel.js';
            $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));
        }

        if (Configuration::get('APPAGEBUILDER_LOAD_SWIPER')) {
            $uri = apPageHelper::getCssDir().'swiper.min.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            
            $uri = apPageHelper::getJsDir().'swiper.min.js';
            $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));
        }

        //DONGND:: add jquery plugin images loaded
        $uri = apPageHelper::getJsDir().'imagesloaded.pkgd.min.js';
        $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));

        //slick
        $uri = apPageHelper::getJsDir().'slick.js';
        $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));

        $uri = apPageHelper::getCssDir().'slick-theme.css';
        $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));

        $uri = apPageHelper::getCssDir().'slick.css';
        $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));

        // zoom
        if (Configuration::get('APPAGEBUILDER_LOAD_PRODUCTZOOM')) {
            $uri = apPageHelper::getJsDir().'jquery.elevateZoom-3.0.8.min.js';
            $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));
        }

        $product_list_image = Configuration::get('APPAGEBUILDER_LOAD_IMG');
        $product_one_img = Configuration::get('APPAGEBUILDER_LOAD_TRAN');
        $category_qty = Configuration::get('APPAGEBUILDER_LOAD_PN');
        $productCdown = Configuration::get('APPAGEBUILDER_LOAD_COUNT');
//        $productColor = Configuration::get('APPAGEBUILDER_LOAD_ACOLOR');
        $productColor = false;
        $ajax_enable = $product_list_image || $product_one_img || $category_qty || $productCdown || $productColor;
        $this->smarty->assign(array(
            'ajax_enable' => $ajax_enable,
            'product_list_image' => $product_list_image,
            'product_one_img' => $product_one_img,
            'category_qty' => $category_qty,
            'productCdown' => $productCdown,
            'productColor' => $productColor
        ));
        $this->context->controller->addJqueryPlugin('fancybox');
        if ($productCdown) {
            $uri = apPageHelper::getJsDir().'countdown.js';
            $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 80));
        }
        if ($product_list_image) {
            $this->context->controller->addJqueryPlugin(array('scrollTo', 'serialScroll'));
        }
            
        // add js for html5 youtube video
        if (Configuration::get('APPAGEBUILDER_LOAD_HTML5VIDEO')) {
            $uri = apPageHelper::getCssDir().'mediaelementplayer.min.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            
            $uri = apPageHelper::getJsDir().'mediaelement-and-player.js';
            $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));
        }
        //add js,css for full page js
        if (Configuration::get('APPAGEBUILDER_LOAD_FULLPAGEJS')) {
            $uri = apPageHelper::getCssDir().'jquery.fullPage.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            
            $uri = apPageHelper::getJsDir().'jquery.fullPage.js';
            $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));
        }
        // add js, css for Magic360
        if (Configuration::get('APPAGEBUILDER_LOAD_IMAGE360')) {
            $uri = apPageHelper::getCssDir().'magic360.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            $uri = apPageHelper::getCssDir().'magic360.module.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            
            $uri = apPageHelper::getJsDir().'magic360.js';
            $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));
        }
        // add js, css for ImageHotPot
        if (Configuration::get('APPAGEBUILDER_LOAD_IMAGEHOTPOT')) {
            $uri = apPageHelper::getCssDir().'ApImageHotspot.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            
            $uri = apPageHelper::getJsDir().'ApImageHotspot.js';
            $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));
        }
        // add js Cookie : jquery.cooki-plugin.js
        if (Configuration::get('APPAGEBUILDER_LOAD_COOKIE')) {
            $this->context->controller->addJqueryPlugin('cooki-plugin');
        }

        $uri = apPageHelper::getCssDir().'styles.css';
        $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
		
		//DONGND:: add unique css file, css of module for all theme, no need override		
		$uri = apPageHelper::getCssDir().'unique.css';
        $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            
        $uri = apPageHelper::getJsDir().'script.js';
        $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));
        if (!$this->product_active) {
            $this->product_active = ApPageBuilderProductsModel::getActive();
        }
        $this->smarty->smarty->assign(array('productClassWidget' => $this->product_active['class']));
        
        $tpl_file = apPageHelper::getConfigDir('theme_profiles') . $this->product_active['plist_key'].'.tpl';
        
        if (is_file($tpl_file)) {
            $this->smarty->smarty->assign(array('productProfileDefault' => $this->product_active['plist_key']));
        }
        // In the case not exist: create new cache file for template
        if (!$this->isCached('module:appagebuilder/views/templates/hook/header.tpl', $this->getCacheId('displayHeader'))) {
            if (!$this->hook_index_data) {
                $model = new ApPageBuilderModel();
                $this->hook_index_data = $model->getAllItems($this->profile_data, 1, $this->default_language['id_lang']);
            }
            $this->smarty->assign(array(
                'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
                'mediumSize' => Image::getSize(ImageType::getFormattedName('medium'))
            ));
        }
        
        # LEOTEMCP
        $isRTL = $this->context->language->is_rtl;
        $leoRTL = $this->context->language->is_rtl;
        if ($leoRTL && version_compare(Configuration::get('PS_VERSION_DB'), '1.7.3.0', '>=')) {
            $directory = _PS_ALL_THEMES_DIR_._THEME_NAME_;
            $allFiles = Tools::scandir($directory, 'css', '', true);
            $rtl_file = false;
            foreach ($allFiles as $key => $file) {
                if (Tools::substr(rtrim($file, '.css'), -4) == '_rtl') {
                    $rtl_file = true;
                    break;
                }
            }
            
            if ($rtl_file) {
                $leoRTL = false; // to remove class RTL
//                $this->context->controller->unregisterStylesheet('theme-rtl');
                @unlink(_PS_ALL_THEMES_DIR_._THEME_NAME_.'/assets/css/rtl_rtl.css');  // Remove file rtl_rtl.css
                $this->context->controller->registerStylesheet('theme-rtl', '/assets/css/rtl.css', array('media' => 'all', 'priority' => 900));
            }
        }
//        $id_shop = $this->context->shop->id;
//        $helper = LeoFrameworkHelper::getInstance();
        
        $this->themeCookieName = $this->getConfigName('PANEL_CONFIG');
        $panelTool = $this->getConfig('PANELTOOL');
        $backGroundValue = '';
        
        //DONGND:: get product detail layout
        $list_productdetail_layout = array();

        if ($panelTool) {
            # ENABLE PANEL TOOL
            $uri = apPageHelper::getCssDir().'colorpicker/css/colorpicker.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            $uri = apPageHelper::getCssDir().'paneltool.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            
            $uri = apPageHelper::getJsDir().'colorpicker/js/colorpicker.js';
            $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));
            $uri = apPageHelper::getJsDir().'paneltool.js';
            $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));
            $this->context->controller->addJqueryPlugin('cooki-plugin');
            
            $skin = $this->getPanelConfig('default_skin');
            $layout_mode = $this->getPanelConfig('layout_mode');
            $enable_fheader = (int)$this->getPanelConfig('enable_fheader');
            
            $backGroundValue = array(
                'attachment' => array('scroll', 'fixed', 'local', 'initial', 'inherit'),
                'repeat' => array('repeat', 'repeat-x', 'repeat-y', 'no-repeat', 'initial', 'inherit'),
                'position' => array('left top', 'left center', 'left bottom', 'right top', 'right center', 'right bottom', 'center top', 'center center', 'center bottom')
            );

            //DONGND:: check table exist
            $check_table = Db::getInstance()->executeS('SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE TABLE_SCHEMA = "'._DB_NAME_.'" AND TABLE_NAME = "'._DB_PREFIX_.'appagebuilder_details"');

            if (count($check_table) > 0) {
                if (Tools::getValue('id_product')) {
                        $id_product = Tools::getValue('id_product');
                } else {
                    $sql = 'SELECT p.`id_product`
                            FROM `'._DB_PREFIX_.'product` p
                            '.Shop::addSqlAssociation('product', 'p').'
                    AND product_shop.`visibility` IN ("both", "catalog")
                    AND product_shop.`active` = 1
                    ORDER BY p.`id_product` ASC';
                    $first_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
                    $id_product = $first_product['id_product'];
                }
                $sql = 'SELECT a.*
                        FROM `'._DB_PREFIX_.'appagebuilder_details` as a
                        INNER JOIN `'._DB_PREFIX_.'appagebuilder_details_shop` ps ON (ps.`id_appagebuilder_details` = a.`id_appagebuilder_details`) WHERE ps.id_shop='.(int)$this->context->shop->id;
                $list_productdetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

                if ($id_product && count($list_productdetail) > 1) {
                    foreach ($list_productdetail as $key => $list_productdetail_item) {
                        $product_layout_link = '';
                        $product_layout_link = $this->context->link->getProductLink($id_product, null, null, null, null, null, (int)Product::getDefaultAttribute((int)$id_product));
                        $product_layout_link = str_replace('.html', '.html?layout='.$list_productdetail_item['plist_key'], $product_layout_link);
                        $list_productdetail[$key]['product_layout_link'] = $product_layout_link;
                    }
                    $list_productdetail_layout = $list_productdetail;
                }
            }
        } else {
            $skin = $this->getConfig('default_skin');
            $layout_mode = $this->getConfig('layout_mode');
            $enable_fheader = $this->getConfig('enable_fheader');
            if (apPageHelper::getPageName() == 'category') {
                $this->context->controller->addJqueryPlugin('cooki-plugin');
            }
        }
        
//        if ($this->getConfig('ENABLE_CUSTOMFONT')) {
//            # CUSTOM FONT
//            $uri = apPageHelper::getCssDir().'fonts-cuttom.css';
//            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
//        }
        if ($this->getConfig('ENABLE_LOADFONT')) {
            # CUSTOM FONT
            $uri = apPageHelper::getCssDir().'fonts-cuttom2.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
        }
        
        $layout_width_val = '';
        $layout_width = $this->getConfig('layout_width');
        if (trim($layout_width) != 'auto' && trim($layout_width) != '') {
            $layout_width = (int)$layout_width;
            $layout_width_val = '<style type="text/css">.container{max-width:'.$layout_width.'px}</style>';
            if (is_numeric($layout_width)) {
                # validate module
                $layout_width_val .= '<script type="text/javascript">layout_width = '.$layout_width.';</script>';
            }
        }
        
        $load_css_type = $this->getConfig('load_css_type');
        $css_skin = array();
        $css_custom = array();
        if ($load_css_type) {
            # Load Css With Prestashop Standard - YES
            if (!$this->getConfig('enable_responsive')) {
                $uri = apPageHelper::getCssDir().'non-responsive.css';
                $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            }
            
            # LOAD SKIN CSS IN MODULE
            $uri = apPageHelper::getCssDir().'skins/'.$skin.'/skin.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            $uri = apPageHelper::getCssDir().'skins/'.$skin.'/custom-rtl.css';
            $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            
            # LOAD CUSTOM CSS
            if ($this->context->getMobileDevice() != false && !$this->getConfig('enable_responsive')) {
                $uri = apPageHelper::getCssDir().'mobile.css';
                $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            }
            
            # LOAD POSITIONS AND PROFILES
            $this->loadResouceForProfile();

            # LOAD PATTERN
            if ($profile = $this->getConfig('c_profile')) {
                $uri = apPageHelper::getCssDir().'patterns/'.$profile.'.css';
                $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
            }
        } else {
            # Load Css With Prestashop Standard - NO
            if (!$this->getConfig('enable_responsive')) {
                $uri = apPageHelper::getCssDir().'non-responsive.css';
                $skinFileUrl = apPageHelper::getFullPathCss($uri);
                if ($skinFileUrl !== false) {
                    $css_skin[] = '<link rel="stylesheet" href="'.apPageHelper::getUriFromPath($skinFileUrl).'" type="text/css" media="all" />';
                }
            }
            
            # LOAD SKIN CSS IN TPL
            $uri = apPageHelper::getCssDir().'skins/'.$skin.'/skin.css';
            $skinFileUrl = apPageHelper::getFullPathCss($uri);
            if ($skinFileUrl !== false) {
                $css_skin[] = '<link rel="stylesheet" id="leo-dynamic-skin-css" href="'.apPageHelper::getUriFromPath($skinFileUrl).'" type="text/css" media="all" />';
            }
            $uri = apPageHelper::getCssDir().'skins/'.$skin.'/custom-rtl.css';
            $skinFileUrl = apPageHelper::getFullPathCss($uri);
            if ($leoRTL && $skinFileUrl !== false) {
                $css_skin[] = '<link rel="stylesheet" id="leo-dynamic-skin-css-rtl" href="'.apPageHelper::getUriFromPath($skinFileUrl).'" type="text/css" media="all" />';
            }
            
            # LOAD CUSTOM CSS
            if ($this->context->getMobileDevice() != false && !$this->getConfig('enable_responsive')) {
                $uri = apPageHelper::getCssDir().'mobile.css';
                $skinFileUrl = apPageHelper::getFullPathCss($uri);
                if ($skinFileUrl !== false) {
                    $css_skin[] = '<link rel="stylesheet" href="'.apPageHelper::getUriFromPath($skinFileUrl).'" type="text/css" media="all" />';
                }
            }
            
            # LOAD POSITIONS AND PROFILES
            $this->loadResouceForProfile();
            
            # LOAD PATTERN
            if ($profile = $this->getConfig('c_profile')) {
                $uri = apPageHelper::getCssDir().'patterns/'.$profile.'.css';
                $skinFileUrl = apPageHelper::getFullPathCss($uri);
                if ($skinFileUrl !== false) {
                    $css_skin[] = '<link rel="stylesheet" href="'.apPageHelper::getUriFromPath($skinFileUrl).'" type="text/css" media="all" />';
                }
            }
        }
        
        if ($this->context->language->is_rtl) {
            # OVERRIDE CORE, LOAD RTL.CSS FILE AT BOTTOM
            $this->context->controller->registerStylesheet('theme-rtl', '/assets/css/rtl.css', ['media' => 'all', 'priority' => 9000]);
        }

        $ps = array(
            'LEO_THEMENAME' => _THEME_NAME_,
            'LEO_PANELTOOL' => $panelTool,
            'LEO_SUBCATEGORY' => $this->getConfig('subcategory'),
            'LEO_DEFAULT_SKIN' => $skin,
            'LEO_LAYOUT_MODE' => $layout_mode,
            'BACKGROUNDVALUE' => $backGroundValue,
            'LAYOUT_WIDTH' => $layout_width_val,
            'LOAD_CSS_TYPE' => $load_css_type,
            'LEO_CSS' => $css_custom,
            'LEO_SKIN_CSS' => $css_skin,
            'IS_RTL' => $isRTL,
            'LEO_RTL' => $leoRTL,
            'USE_PTABS' => $this->getConfig('ENABLE_PTAB'),
            'USE_FHEADER' => $enable_fheader,
            'LEO_COOKIE_THEME' => $this->themeCookieName,
            'LEO_BACKTOP' => $this->getConfig('backtop'),
            'apPageHelper' => apPageHelper::getInstance(),
            'leoConfiguration' => new Configuration(),
            'list_productdetail_layout' => $list_productdetail_layout,
        );

        Media::addJsDefL('LEO_COOKIE_THEME', $this->themeCookieName);
        $this->context->smarty->assign($ps);

        $page_name = apPageHelper::getPageName();
        $page = $this->smarty->smarty->getVariable('page')->value;
        if (isset($this->profile_data['meta_title']) && $this->profile_data['meta_title'] && $page_name == 'index') {
            $page['meta']['title'] = $this->profile_data['meta_title'];
        }
        if (isset($this->profile_data['meta_description']) && $this->profile_data['meta_description'] && $page_name == 'index') {
            $page['meta']['description'] = $this->profile_data['meta_description'];
        }
        if (isset($this->profile_data['meta_keywords']) && $this->profile_data['meta_keywords'] && $page_name == 'index') {
            $page['meta']['keywords'] = $this->profile_data['meta_keywords'];
        }
        $this->smarty->smarty->assign('page', $page);

        # REPLACE LINK FOR MULILANGUAGE
        $controller = Dispatcher::getInstance()->getController();
        if ($controller == 'appagebuilderhome') {
            Media::addJsDef(array('approfile_multilang_url' => ApPageBuilderProfilesModel::getAllProfileRewrite($this->profile_data['id_appagebuilder_profiles'])));
        }

        if ($controller == 'sitemap') {
            $profile_model = new ApPageBuilderProfilesModel();
            $profiles = $profile_model->getAllProfileByShop();
            foreach ($profiles as $key => $profile) {
                if (!isset($profile['friendly_url']) || !$profile['friendly_url']) {
                    unset($profiles[$key]);
                }
            }
            $this->smarty->smarty->assign('simap_ap_profiles', $profiles);
        }

        $this->header_content = $this->display(__FILE__, 'header.tpl');
        return $this->header_content;
    }

    //DONGND:: build shortcode by hook
    public function hookDisplayApSC($params)
    {
        if (isset($params['sc_key']) && $params['sc_key'] != '') {
            return $this->processShortCode($params['sc_key']);
        }
    }

    //DONGND:: build shortcode by embedded in content
    public function buildShortCode($content)
    {
        //DONGND:: validate module
        $result = preg_replace_callback(
            '~\[ApSC(.*?)\[\/ApSC\]~',
            function ($matches_tmp) {
                preg_match_all("~sc_key=(.*?)\]~", $matches_tmp[1], $tmp);
                return self::processShortCode($tmp[1][0]);
            },
            $content
        );
        return $result;
    }

    //DONGN:: get list short code for tinymce
    public function getListShortCodeForEditor()
    {
        $this->smarty->smarty->assign(array(
            'js_dir' => _PS_JS_DIR_,
            'appagebuilder_module_dir' => $this->_path,
            'shortcode_url_add' => Configuration::get('shortcode_url_add').'&addappagebuilder_shortcode',
            'shortcode_url' => Configuration::get('shortcode_url_add'),
            'list_shortcode' => ApPageBuilderShortcodeModel::getListShortCode(),
        ));
        return $this->display(__FILE__, 'list_shortcode.tpl');
    }

    private function processShortCode($shortcode_key)
    {
        $disable_cache = false;
        if (!Configuration::get('PS_SMARTY_CACHE')) {
            $disable_cache = true;
        }

        $cache_id = $this->getCacheId('apshortcode', $shortcode_key);
        if ($disable_cache || !$this->isCached($this->templateFile, $cache_id)) {
            $shortcode_html = '';
            $shortcode_obj = ApPageBuilderShortcodeModel::getShortCode($shortcode_key);
            if (isset($shortcode_obj['id_appagebuilder']) && $shortcode_obj['id_appagebuilder'] != '' && $shortcode_obj['id_appagebuilder'] != 0) {
                $shortcode_code = ApPageBuilderShortcodeModel::getAllItems($shortcode_obj['id_appagebuilder'], 1);
                
                if (!empty($shortcode_code)) {
                    if (empty(ApShortCodesBuilder::$shortcode_tags)) {
                        apPageHelper::loadShortCode(_PS_THEME_DIR_);
                    }
                    
                    apPageHelper::setGlobalVariable($this->context);
                    
                    // ApShortCodesBuilder::$is_front_office = 1;
                    // ApShortCodesBuilder::$is_gen_html = 1;
                    // ApShortCodesBuilder::$profile_param = array();
                    $ap_helper = new ApShortCodesBuilder();
                    // ApShortCodesBuilder::$hook_name = 'apshortcode';
                    
                    $shortcode_html = $ap_helper->parse($shortcode_code['apshortcode']);
                }
            }
            $this->smarty->assign(array('apContent' => $shortcode_html));
        }
        return $this->display(__FILE__, 'appagebuilder.tpl', $cache_id);
    }

    private function processHook($hook_name, $params = 'null')
    {
        $disable_cache_hook = isset($this->profile_param['disable_cache_hook']) ? $this->profile_param['disable_cache_hook'] : ApPageSetting::getCacheHook(3);
        $disable_cache = false;
        if (isset($disable_cache_hook[$hook_name]) && $disable_cache_hook[$hook_name]) {
            $disable_cache = true;
        }
        if (Tools::isSubmit('submitNewsletter')) {
            $disable_cache = true;
        }
        if (!Configuration::get('PS_SMARTY_CACHE')) {
            $disable_cache = true;
        }
        $is_live = Tools::getIsset('ap_live_edit') ? Tools::getValue('ap_live_edit') : '';
        if ($is_live) {
            $disable_cache = true;
        }

        $cache_id = $this->getCacheId($hook_name);
        $cover_hook_live = '';
        if ($disable_cache || !$this->isCached($this->templateFile, $cache_id)) {
            if ($disable_cache) {
                $cache_id = null;
            }
            if ($is_live) {
                $token = Tools::getIsset('ap_edit_token') ? Tools::getValue('ap_edit_token') : '';
                $admin_dir = Tools::getIsset('ad') ? Tools::getValue('ad') : '';
                $controller = 'AdminApPageBuilderHome';
                $id_lang = Context::getContext()->language->id;
                $id_profile = ApPageBuilderProfilesModel::getIdProfileFromRewrite();
                $params = array('token' => $token, 'id_appagebuilder_profiles' => $id_profile);
                $current_link = _PS_BASE_URL_.__PS_BASE_URI__;
                $url_design_layout = $current_link.$admin_dir.'/'.Dispatcher::getInstance()->createUrl($controller, $id_lang, $params, false);
                $cover_hook_live = '<div class="cover-hook"><a title="'.$this->l('Click to edit').'" class="lnk-hook" href="'
                        .$url_design_layout.'#'.$hook_name.'" target="_blank">Hook: '.Tools::strtoupper($hook_name).'</a></div>';
            }
            $model = new ApPageBuilderModel();
            if (!$this->hook_index_data) {
                $this->hook_index_data = $model->getAllItems($this->profile_data, 1, $this->default_language['id_lang']);
            }
            if (!isset($this->hook_index_data[$hook_name]) || trim($this->hook_index_data[$hook_name]) == '') {
                # NOT DATA BUT SET VARIABLE TO SET CACHE
                $this->smarty->assign(array('apContent' => ''));
                return $cover_hook_live.$this->fetch( $this->templateFile, $cache_id);
            }
            $ap_content = $model->parseData($hook_name, $this->hook_index_data[$hook_name], $this->profile_param);
            if ($is_live) {
                $ap_content = '<div class="ap-cover-hook">'.$ap_content.'</div>';
            }
            $this->smarty->assign(array('apContent' => $ap_content));
        }
        return $cover_hook_live.$this->fetch( $this->templateFile, $cache_id);
    }
    
    public function hookDisplayBanner($params)
    {
        return $this->processHook('displayBanner', $params);
    }

    public function hookDisplayNav1($params)
    {
        return $this->processHook('displayNav1', $params);
    }

    public function hookDisplayNav2($params)
    {
        return $this->processHook('displayNav2', $params);
    }

    public function hookDisplayNavFullWidth($params)
    {
        return $this->processHook('displayNavFullWidth', $params);
    }

    public function hookDisplayTop($params)
    {
        return $this->processHook('displayTop', $params);
    }

    public function hookDisplaySlideshow($params)
    {
        return $this->processHook('displaySlideshow', $params);
    }

    public function hookDisplayRightColumn($params)
    {
        return $this->processHook('displayRightColumn', $params);
    }

    public function hookDisplayLeftColumn($params)
    {
        return $this->processHook('displayLeftColumn', $params);
    }

    public function hookDisplayHome($params)
    {
        return $this->processHook('displayHome', $params);
    }

    public function hookDisplayFooterBefore($params)
    {
        return $this->processHook('displayFooterBefore', $params);
    }

    public function hookDisplayFooter($params)
    {
        return $this->processHook('displayFooter', $params);//.$this->header_content;
    }

    public function hookDisplayFooterAfter($params)
    {
        return $this->processHook('displayFooterAfter', $params);
    }

    public function hookDisplayFooterProduct($params)
    {
        return $this->processHook('displayFooterProduct', $params);
    }

    public function hookDisplayRightColumnProduct($params)
    {
        return $this->processHook('displayRightColumnProduct', $params);
    }

    public function hookDisplayLeftColumnProduct($params)
    {
        return $this->processHook('displayLeftColumnProduct', $params);
    }
    
    public function hookdisplayProductButtons($params)
    {
        return $this->processHook('displayProductButtons', $params);
    }
    
    public function hookDisplayReassurance($params)
    {
        return $this->processHook('displayReassurance', $params);
    }
    
    public function hookDisplayApCustom($params)
    {
        return $this->processHook('displayApCustom', $params);
    }
    
    public function hookDisplayLeoProfileProduct($params)
    {
        apPageHelper::setGlobalVariable($this->context);
        $html = '';
        $tpl_file = '';
        
        if (isset($params['ony_global_variable'])) {
            # {hook h='displayLeoProfileProduct' ony_global_variable=true}
            return $html;
        }

        if (!isset($params['product'])) {
            return 'Not exist product to load template';
        } else if (isset($params['profile'])) {
            # {hook h='displayLeoProfileProduct' product=$product profile=$productProfileDefault}
            $tpl_file = apPageHelper::getConfigDir('theme_profiles') . $params['profile'].'.tpl';
        } else if (isset($params['load_file'])) {
            # {hook h='displayLeoProfileProduct' product=$product load_file='templates/catalog/_partials/miniatures/product.tpl'}
            $tpl_file = _PS_ALL_THEMES_DIR_._THEME_NAME_.'/' . $params['load_file'];
        } else if (isset($params['typeProduct'])) {
            //DONGND:: load default product tpl when do not have product profile
            if ($params['product']['productLayout'] != '') {
                $tpl_file = apPageHelper::getConfigDir('theme_details') . $params['product']['productLayout'].'.tpl';
            } else {
                $tpl_file = _PS_ALL_THEMES_DIR_._THEME_NAME_.'/templates/catalog/product.tpl';
            }
        }

        if (empty($tpl_file)) {
            return 'Not exist profile to load template';
        }
		

		
		$sellerProduct = WkMpSellerProduct::getSellerProductByPsIdProduct($params['product']['id_product']);
		$seller['shop_image'] 		= _MODULE_DIR_.'marketplace/views/img/shop_img/defaultshopimage.jpg';
		$seller['profile_image'] 	= _MODULE_DIR_.'marketplace/views/img/shop_img/defaultshopimage.jpg';
		$seller['shop_name']  = '';
		$seller['city_name']  = '';
		if( $sellerProduct ){
			$mpIdSeller = $sellerProduct['id_seller'];
			if ($seller = WkMpSeller::getSeller($mpIdSeller, Context::getContext()->language->id)) {
				$mpShopImage = $seller['shop_image'];
				if ($mpShopImage && file_exists(_PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$mpShopImage)) {
					$seller['shop_image'] = _MODULE_DIR_.'marketplace/views/img/shop_img/'.$mpShopImage;
				} else {
					$seller['shop_image'] = _MODULE_DIR_.'marketplace/views/img/shop_img/defaultshopimage.jpg';
				}
				$mpProfileImage = $seller['profile_image'];
				if ($mpShopImage && file_exists(_PS_MODULE_DIR_.'marketplace/views/img/seller_img/'.$mpProfileImage)) {
					$seller['profile_image'] = _MODULE_DIR_.'marketplace/views/img/seller_img/'.$mpProfileImage;
				} else {
					$seller['profile_image'] = _MODULE_DIR_.'marketplace/views/img/seller_img/defaultshopimage.jpg';
				}
				$objReview = new WkMpSellerReview();
				$reviews = $objReview->getReviewsByConfiguration($sellerProduct['id_seller']);
				$average_ratings 				= $reviews['avg_rating'];
				$total_review 					= ( $reviews ) ? count( $reviews['reviews'] ) : 0;
				$store_locator 					= MarketplaceStoreLocator::getSellerStore($sellerProduct['id_seller']);
				$seller['store_locator'] 		= ($store_locator) ? $store_locator[0] : '';
				$seller['city_name'] 			= ($store_locator) ? $store_locator[0]['city_name'] : '';
				$seller['average_ratings'] 	= ($average_ratings) ? $average_ratings : 0;
				$seller['left_ratings'] 		= 5 - $average_ratings;
				$seller['total_review'] 		= $total_review;
			}
		}
		
		$objBookingProductInfo = new WkMpBookingProductInformation();
		$bookingProductInfo = $objBookingProductInfo->getBookingProductInfoByIdProduct( $params['product']['id_product'] );
		$params['product']['bookingProductInfo'] = $bookingProductInfo;
		$params['product']['is_booking'] = ( $bookingProductInfo ) ? true : false;
		
		$params['product']['mp_seller_info'] = $seller;

		$product_temp_price = $params['product']['price'];
		$product_temp_price = str_replace(',','',$product_temp_price);
		$product_temp_price = str_replace('€','',$product_temp_price);
		$product_temp_price = trim($product_temp_price);
		if( (float)$product_temp_price == 0){
			$params['product']['price'] = 'gratuit';
		}
		
        Context::getContext()->smarty->assign(array(
            'product' => $params['product'],
        ));
        $html .= Context::getContext()->smarty->fetch($tpl_file);
        return $html;
    }

    public function hookActionShopDataDuplication()
    {
        $this->clearHookCache();
    }

    /**
     * Register hook again to after install/change any theme
     */
    public function hookActionObjectShopUpdateAfter()
    {
        // Retrieve hooks used by the module
//        $sql = 'SELECT `id_hook` FROM `'._DB_PREFIX_.'hook_module` WHERE `id_module` = '.(int)$this->id;
//        $result = Db::getInstance()->executeS($sql);
//        foreach ($result as $row) {
//            $this->unregisterHook((int)$row['id_hook']);
//            $this->unregisterExceptions((int)$row['id_hook']);
//        }
    }
    
    /**
     * FIX BUG 1.7.3.3 : install theme lose hook displayHome, displayLeoProfileProduct
     * because ajax not run hookActionAdminBefore();
     */
    public function autoRestoreSampleData()
    {
        if(Hook::isModuleRegisteredOnHook($this, 'actionAdminBefore', (int)Context::getContext()->shop->id)) {
            $theme_manager = new stdclass();
            $theme_manager->theme_manager = 'theme_manager';
            $this->hookActionAdminBefore(array(
                'controller' => $theme_manager,
            ));
        }
    }
    
    /**
     * Run only one when install/change Theme_of_Leo
     */
    public function hookActionAdminBefore($params)
    {
        if (isset($params) && isset($params['controller']) && isset($params['controller']->theme_manager)) {
            // Validate : call hook from theme_manager
        } else {
            // Other module call this hook -> duplicate data
            return;
        }
        
        $this->unregisterHook('actionAdminBefore');
        
        # FIX THEME_CHILD NOT EXIST TPL FILE -> AUTO COPY TPL FILE FROM THEME_PARENT
        $assets = Context::getContext()->shop->theme->get('assets');
        $theme_parent = Context::getContext()->shop->theme->get('parent');
        if( is_array($assets) && isset($assets['use_parent_assets']) && $assets['use_parent_assets'] && $theme_parent )
        {
            $from = _PS_ALL_THEMES_DIR_.$theme_parent.'/modules/appagebuilder';
            $to =   _PS_ALL_THEMES_DIR_.apPageHelper::getInstallationThemeName().'/modules/appagebuilder';
            apPageHelper::createDir($to);
            Tools::recurseCopy($from, $to);
        }
        
        # FIX : update Prestashop by 1-Click module -> NOT NEED RESTORE DATABASE
        $ap_version = Configuration::get('AP_CURRENT_VERSION');
        if ($ap_version != false) {
            $ps_version = Configuration::get('PS_VERSION_DB');
            $versionCompare =  version_compare($ap_version, $ps_version);
            if ($versionCompare != 0) {
                // Just update Prestashop
                Configuration::updateValue('AP_CURRENT_VERSION', $ps_version);
                return;
            }
        }
        
        # WHENE INSTALL THEME, INSERT HOOK FROM DATASAMPLE IN THEME
        $hook_from_theme = false;
        if (file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php')) {
            require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');
            $sample = new Datasample();
            if ($sample->processHook($this->name)) {
                $hook_from_theme = true;
            };
        }
        
        # INSERT HOOK FROM MODULE_DATASAMPLE
        if ($hook_from_theme == false) {
            $this->registerLeoHook();
        }
        
        # WHEN INSTALL MODULE, NOT NEED RESTORE DATABASE IN THEME
        $install_module = (int)Configuration::get('AP_INSTALLED_APPAGEBUILDER', 0);
        if ($install_module) {
            Configuration::updateValue('AP_INSTALLED_APPAGEBUILDER', '0');    // next : allow restore sample
            return;
        }
        
        # INSERT DATABASE FROM THEME_DATASAMPLE
        if (file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php')) {
            require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');
            $sample = new Datasample();
            $sample->processImport($this->name);
        }
        
        # REMOVE FILE INDEX.PHP FOR TRANSLATE
        if (file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/setup.php')) {
            require_once(_PS_MODULE_DIR_.'appagebuilder/libs/setup.php');
            ApPageSetup::processTranslateTheme();
        }
    }
    
    protected function getCacheId($hook_name = null, $shortcode_key = null)
    {
        $cache_array = array();
        $cache_array[] = $this->name;
        if (ApPageBuilderProfilesModel::getIdProfileFromRewrite() && !$shortcode_key) {
            $cache_array[] = 'profile_'.ApPageBuilderProfilesModel::getIdProfileFromRewrite();
        }

        $cache_array[] = $hook_name;
        if ($this->profile_param && isset($this->profile_param[$hook_name]) && $this->profile_param[$hook_name]) {
            //$cache_array[] = $hook_name;
            $current_page = apPageHelper::getPageName();
            //show ocurrentPagenly in controller
            if (isset($this->profile_param[$hook_name][$current_page])) {
                $cache_array[] = $current_page;
                if ($current_page != 'index' && $cache_id = ApPageSetting::getControllerId($current_page, $this->profile_param[$hook_name][$current_page])) {
                    $cache_array[] = $cache_id;
                }
            } elseif (isset($this->profile_param[$hook_name]['productCarousel'])) {
                $random = round(rand(1, max(Configuration::get('APPAGEBUILDER_PRODUCT_MAX_RANDOM'), 1)));
                $cache_array[] = "p_carousel_$random";
            } else if (isset($this->profile_param[$hook_name]['exception']) && in_array($cache_array, $this->profile_param[$hook_name]['exception'])) {
                //show but not in controller
                $cache_array[] = $current_page;
            }
        }
        if (Configuration::get('PS_SSL_ENABLED')) {
            $cache_array[] = 'SSL_'.(int)Tools::usingSecureMode();
        }
        if (Shop::isFeatureActive()) {
            $cache_array[] = 'shop_'.(int)$this->context->shop->id;
        }
        if (Group::isFeatureActive()) {
            $cache_array[] = 'c_group_'.(int)GroupCore::getCurrent()->id;
        }
        if (Language::isMultiLanguageActivated()) {
            $cache_array[] = 'la_'.(int)$this->context->language->id;
        }
        if (Currency::isMultiCurrencyActivated()) {
            $cache_array[] = 'curcy_'.(int)$this->context->currency->id;
        }
        $cache_array[] = 'ctry_'.(int)$this->context->country->id;
        if (Tools::getValue('plist_key')&& Tools::getIsset('leopanelchange')) {
            $cache_array[] = 'plist_key_'.Tools::getValue('plist_key');
        }
        if (Tools::getValue('header') && Tools::getIsset('leopanelchange') && (in_array($hook_name, ApPageSetting::getHook('header')) || $hook_name == 'pagebuilderConfig|header')) {
            $cache_array[] = 'header_'.Tools::getValue('header');
        }
        if (Tools::getValue('content')&& Tools::getIsset('leopanelchange') && (in_array($hook_name, ApPageSetting::getHook('content')) || $hook_name == 'pagebuilderConfig|content')) {
            $cache_array[] = 'content_'.Tools::getValue('content');
        }
        if (Tools::getValue('product')&& Tools::getIsset('leopanelchange') && (in_array($hook_name, ApPageSetting::getHook('product')) || $hook_name == 'pagebuilderConfig|product')) {
            $cache_array[] = 'product_'.Tools::getValue('product');
        }
        if (Tools::getValue('footer') && Tools::getIsset('leopanelchange') && (in_array($hook_name, ApPageSetting::getHook('footer')) || $hook_name == 'pagebuilderConfig|footer')) {
            $cache_array[] = 'footer_'.Tools::getValue('footer');
        }
        
        //DONGND:: update cache for shortcode
        if ($shortcode_key) {
            $cache_array[] = 'shortcodekey_'.$shortcode_key;
        }
        
        return implode('|', $cache_array);
    }

    /**
     * Overide function isCached of Module.php
     * @param type $template
     * @param type $cache_id
     * @param type $compile_id
     * @return boolean
     */
//    public function isCached($template, $cache_id = null, $compile_id = null)
//    {
//        if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {
//            // FIX TEMP
//            return false;
//        }
//        if (Tools::getIsset('live_edit')) {
//            return false;
//        }
//        Tools::enableCache();
//        $is_cached = $this->getCurrentSubTemplate($this->getTemplatePath($template), $cache_id, $compile_id);
//        $is_cached = $is_cached->isCached($this->getTemplatePath($template), $cache_id, $compile_id);
//        Tools::restoreCacheSettings();
//        return $is_cached;
//    }

    /**
     * This function base on the function getCurrentSubTemplate of Module.php (not overide)
     * @param type $template
     * @param type $cache_id
     * @param type $compile_id
     * @return type
     */
//    protected function getCurrentSubTemplate($template, $cache_id = null, $compile_id = null)
//    {
//        if (!isset($this->current_subtemplate[$template.'_'.$cache_id.'_'.$compile_id])) {
//            $this->current_subtemplate[$template.'_'.$cache_id.'_'.$compile_id] = $this->context->smarty->createTemplate($this->getTemplatePath($template), $cache_id, $compile_id, $this->smarty);
//        }
//        return $this->current_subtemplate[$template.'_'.$cache_id.'_'.$compile_id];
//    }

    /**
     * Overide function display of Module.php
     * @param type $file
     * @param type $template
     * @param null $cache_id
     * @param type $compile_id
     * @return type
     */
    public function display($file, $template, $cache_id = null, $compile_id = null)
    {
        if (($overloaded = Module::_isTemplateOverloadedStatic(basename($file, '.php'), $template)) === null) {
            return sprintf($this->l('No template found "%s"'), $template);
        } else {
            if (Tools::getIsset('live_edit')) {
                $cache_id = null;
            }
            $this->smarty->assign(array(
                'module_dir' => __PS_BASE_URI__.'modules/'.basename($file, '.php').'/',
                'module_template_dir' => ($overloaded ? _THEME_DIR_ : __PS_BASE_URI__).'modules/'.basename($file, '.php').'/',
                'allow_push' => $this->allow_push
            ));
            if ($cache_id !== null) {
                Tools::enableCache();
            }
            $result = $this->getCurrentSubTemplate($template, $cache_id, $compile_id)->fetch();
            if ($cache_id !== null) {
                Tools::restoreCacheSettings();
            }
            $this->resetCurrentSubTemplate($template, $cache_id, $compile_id);
            return $result;
        }
    }

    public function clearHookCache()
    {
//        $this->_clearCache('appagebuilder.tpl', $this->name);
        $this->_clearCache($this->templateFile);    # CLEAR CACHE ALL HOOKS
    }
    
    //DONGND:: add clear cache for shortcode
    public function clearShortCodeCache($shortcode_key)
    {
        $cache_id = $this->getCacheId('apshortcode', $shortcode_key);
        
        $this->_clearCache('appagebuilder.tpl', $cache_id);
    }

    public function hookCategoryAddition()
    {
        $this->clearHookCache();
    }

    public function hookCategoryUpdate()
    {
        $this->clearHookCache();
    }

    public function hookCategoryDeletion()
    {
        $this->clearHookCache();
    }

    public function hookAddProduct()
    {
        $this->clearHookCache();
    }

    public function hookUpdateProduct()
    {
        $this->clearHookCache();
    }

    public function hookDeleteProduct()
    {
        $this->clearHookCache();
    }

    public function hookDisplayBackOfficeHeader()
    {
        apPageHelper::autoUpdateModule();
        if (method_exists($this->context->controller, 'addJquery')) {
            // validate module
            $this->context->controller->addJquery();
        }
		//DONGND:: fix home page config with theme of leotheme, redirect to profile page
		if (get_class($this->context->controller) == 'AdminPsThemeCustoConfigurationController' && _THEME_NAME_ != 'default') {
			Media::addJsDef(
				array(
					'ap_profile_url' => $this->context->link->getAdminLink('AdminApPageBuilderProfiles'),
					'ap_profile_txt_redirect' => $this->l('You are using a theme from Leotheme. Please access this link to use this feature easily'),
					'ap_check_theme_name' => _THEME_NAME_
				)
			);
		}
        $this->context->controller->addCss(apPageHelper::getCssAdminDir().'admin/style.css');
        $this->context->controller->addJS(apPageHelper::getJsAdminDir().'admin/function.js');
        if('AdminLegacyLayoutControllerCore' != get_class($this->context->controller))
        {
            $this->context->controller->addJS(apPageHelper::getJsAdminDir().'admin/setting.js');
        }
        if (!apPageHelper::isRelease()) {
            Media::addJsDef(array('js_ap_dev' => 1));
        }
    }

    public function loadResouceForProfile()
    {
        $profile = $this->profile_data;
        $arr = array();
        if ($profile['header']) {
            $arr[] = $profile['header'];
        }
        if ($profile['content']) {
            $arr[] = $profile['content'];
        }
        if ($profile['footer']) {
            $arr[] = $profile['footer'];
        }
        if ($profile['product']) {
            $arr[] = $profile['product'];
        }
        if (count($arr) > 0) {
            $model = new ApPageBuilderProfilesModel();
            $list_positions = $model->getPositionsForProfile($arr);
            if ($list_positions) {
                foreach ($list_positions as $item) {
                    $name = $item['position'].$item['position_key'];
                    
                    $uri = apPageHelper::getCssDir().'positions/'.$name.'.css';
                    $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
                    
                    $uri = apPageHelper::getJsDir().'positions/'.$name.'.js';
                    $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));
                }
            }
        }
        
        $uri = apPageHelper::getCssDir().'profiles/'.$profile['profile_key'].'.css';
        $this->context->controller->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 8000));
        
        $uri = apPageHelper::getJsDir().'profiles/'.$profile['profile_key'].'.js';
        $this->context->controller->registerJavascript(sha1($uri), $uri, array('position' => 'bottom', 'priority' => 8000));
    }

    public function getProfileData()
    {
        return $this->profile_data;
    }

    public function setFullwidthHook()
    {
        if (isset(Context::getContext()->controller->controller_type) && in_array(Context::getContext()->controller->controller_type, array('front', 'modulefront'))) {
            # frontend
            $page_name = apPageHelper::getPageName();
            if ($page_name == 'index' || $page_name == 'appagebuilderhome') {
                $this->context->smarty->assign(array(
                    'fullwidth_hook' => isset($this->profile_param['fullwidth_index_hook']) ? $this->profile_param['fullwidth_index_hook'] : ApPageSetting::getIndexHook(3),
                ));
            } else {
                $this->context->smarty->assign(array(
                    'fullwidth_hook' => isset($this->profile_param['fullwidth_other_hook']) ? $this->profile_param['fullwidth_other_hook'] : ApPageSetting::getOtherHook(3),
                ));
            }
        }
    }

    /**
     * Get Grade By product
     *
     * @return array Grades
     */
    public static function getGradeByProducts($list_product)
    {
        $list_product = apPageHelper::addonValidInt( $list_product );         # We validate id_categories in apPageHelper::addonValidInt function . This function is used at any where
        $validate = Configuration::get('PRODUCT_COMMENTS_MODERATE');
        $id_lang = (int)Context::getContext()->language->id;

        return (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT pc.`id_product_comment`, pcg.`grade`, pccl.`name`, pcc.`id_product_comment_criterion`, pc.`id_product`
        FROM `'._DB_PREFIX_.'product_comment` pc
        LEFT JOIN `'._DB_PREFIX_.'product_comment_grade` pcg ON (pcg.`id_product_comment` = pc.`id_product_comment`)
        LEFT JOIN `'._DB_PREFIX_.'product_comment_criterion` pcc ON (pcc.`id_product_comment_criterion` = pcg.`id_product_comment_criterion`)
        LEFT JOIN `'._DB_PREFIX_.'product_comment_criterion_lang` pccl ON (pccl.`id_product_comment_criterion` = pcg.`id_product_comment_criterion`)
        WHERE pc.`id_product` in ('.pSQL($list_product).')
        AND pccl.`id_lang` = '.(int)$id_lang.
                        ($validate == '1' ? ' AND pc.`validate` = 1' : '')));
    }

    /**
     * Return number of comments and average grade by products
     *
     * @return array Info
     */
    public static function getGradedCommentNumber($list_product)
    {
        $list_product = apPageHelper::addonValidInt( $list_product );         # We validate id_categories in apPageHelper::addonValidInt function . This function is used at any where
        $validate = (int)Configuration::get('PRODUCT_COMMENTS_MODERATE');

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT COUNT(pc.`id_product`) AS nbr, pc.`id_product`
        FROM `'._DB_PREFIX_.'product_comment` pc
        WHERE `id_product` in ('.pSQL($list_product).')'.($validate == '1' ? ' AND `validate` = 1' : '').'
        AND `grade` > 0 GROUP BY pc.`id_product`');
        return $result;
    }

    public static function getByProduct($id_product)
    {
        $id_lang = (int)Context::getContext()->language->id;

        if (!Validate::isUnsignedId($id_product) || !Validate::isUnsignedId($id_lang)) {
            die(Tools::displayError());
        }
        $alias = 'p';
        $table = '';
        // check if version > 1.5 to add shop association
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $table = '_shop';
            $alias = 'ps';
        }
        return Db::getInstance()->executeS('
            SELECT pcc.`id_product_comment_criterion`, pccl.`name`
            FROM `'._DB_PREFIX_.'product_comment_criterion` pcc
            LEFT JOIN `'._DB_PREFIX_.'product_comment_criterion_lang` pccl
                ON (pcc.id_product_comment_criterion = pccl.id_product_comment_criterion)
            LEFT JOIN `'._DB_PREFIX_.'product_comment_criterion_product` pccp
                ON (pcc.`id_product_comment_criterion` = pccp.`id_product_comment_criterion` AND pccp.`id_product` = '.(int)$id_product.')
            LEFT JOIN `'._DB_PREFIX_.'product_comment_criterion_category` pccc
                ON (pcc.`id_product_comment_criterion` = pccc.`id_product_comment_criterion`)
            LEFT JOIN `'._DB_PREFIX_.'product'.bqSQL($table).'` '.bqSQL($alias).'
                ON ('.bqSQL($alias).'.id_category_default = pccc.id_category AND '.bqSQL($alias).'.id_product = '.(int)$id_product.')
            WHERE pccl.`id_lang` = '.(int)$id_lang.'
            AND (
                pccp.id_product IS NOT NULL
                OR ps.id_product IS NOT NULL
                OR pcc.id_product_comment_criterion_type = 1
            )
            AND pcc.active = 1
            GROUP BY pcc.id_product_comment_criterion
        ');
    }

    public function hookProductMoreImg($list_pro)
    {
        $id_lang = Context::getContext()->language->id;
        //get product info
        $product_list = $this->getProducts($list_pro, $id_lang);

        $this->smarty->assign(array(
            'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
            'mediumSize' => Image::getSize(ImageType::getFormattedName('medium'))
        ));

        $obj = array();
        foreach ($product_list as $product) {
            $this->smarty->assign('product', $product);
            $obj[] = array('id' => $product['id_product'], 'content' => ($this->display(__FILE__, 'product.tpl')));
        }
        return $obj;
    }

    public function hookProductOneImg($list_pro)
    {
        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $use_ssl = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($use_ssl) ? 'https://' : 'http://';
        $link = new Link($protocol_link, $protocol_content);

        $id_lang = Context::getContext()->language->id;
        $where = ' WHERE i.`id_product` IN ('.$list_pro.') AND (ish.`cover`=0 OR ish.`cover` IS NULL) AND ish.`id_shop` = '.Context::getContext()->shop->id;
        $order = ' ORDER BY i.`id_product`,`position`';
        $limit = ' LIMIT 0,1';
        //get product info
        $list_img = $this->getAllImages($id_lang, $where, $order, $limit);
        $saved_img = array();
        $obj = array();
        $this->smarty->assign(array(
            'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
            'mediumSize' => Image::getSize(ImageType::getFormattedName('medium')),
            'smallSize' => Image::getSize(ImageType::getFormattedName('small'))
        ));

        $image_name = 'home';
        $image_name .= '_default';
        foreach ($list_img as $product) {
            if (!in_array($product['id_product'], $saved_img)) {
                $obj[] = array(
                    'id' => $product['id_product'],
                    'content' => ($link->getImageLink($product['link_rewrite'], $product['id_image'], $image_name)),
                    'name' => $product['name'],
                    );
            }
            $saved_img[] = $product['id_product'];
        }
        return $obj;
    }
    public function hookProductAllOneImg($list_pro)
    {
        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $use_ssl = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($use_ssl) ? 'https://' : 'http://';
        $link = new Link($protocol_link, $protocol_content);

        $id_lang = Context::getContext()->language->id;
        $image_product = Tools::getValue('image_product');
        $where = ' WHERE i.`id_product` IN ('.$list_pro.') AND i.`id_image` NOT IN ('.$image_product.') AND ish.`id_shop` = '.Context::getContext()->shop->id;
        $order = ' ORDER BY i.`id_product`,`position`';
        $limit = ' LIMIT 0,1';
        //get product info
        $list_img = $this->getAllImages($id_lang, $where, $order, $limit);
        $saved_img = array();
        $obj = array();
        $this->smarty->assign(array(
            'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
            'mediumSize' => Image::getSize(ImageType::getFormattedName('medium')),
            'smallSize' => Image::getSize(ImageType::getFormattedName('small'))
        ));

        $image_name = 'home';
        $image_name .= '_default';
        foreach ($list_img as $product) {
            if (!in_array($product['id_product'], $saved_img)) {
                $obj[] = array(
                    'id' => $product['id_product'],
                    'content' => ($link->getImageLink($product['link_rewrite'], $product['id_image'], $image_name)),
                    'name' => $product['name'],
                    );
            }
            $saved_img[] = $product['id_product'];
        }
        return $obj;
    }
    
    public function hookProductAttributeOneImg($list_pro)
    {
        $list_all = explode(',', $list_pro);
        $str_id_product = '';
        $str_id_product_attribute = '';
        
        $data = array(
            'product' => array(
                'arr_id_product' => array(),
                'str_id_product' => '',
            ),
            'attribute' => array(
                'arr_id_product' => array(),
                'str_id_product' => '',
                'str_id_attribute' => '',
            ),
        );
        foreach ($list_all as $key => $item) {
            $temp = explode('-', $item);
            if( (int)$temp[1] < 1 ){
                # product
                $str_id_product .= (int)$temp[0] . ',';
                $data['product']['str_id_product'] .= (int)$temp[0] . ',';
                $data['product']['arr_id_product'][(int)$temp[0]] = (int)$temp[1];
            }else{
                # attribute
                $str_id_product_attribute .= (int)$temp[0] . ',';
                $data['attribute']['str_id_product'] .= (int)$temp[0] . ',';
                $data['attribute']['str_id_attribute'] .= (int)$temp[1] . ',';
                $data['attribute']['arr_id_product'][(int)$temp[0]] = (int)$temp[1];
            }
        }
        $data['product']['str_id_product'] = rtrim($data['product']['str_id_product'], ',');
        $data['attribute']['str_id_product'] = rtrim($data['attribute']['str_id_product'], ',');
        $data['attribute']['str_id_attribute'] = rtrim($data['attribute']['str_id_attribute'], ',');
        

        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $use_ssl = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($use_ssl) ? 'https://' : 'http://';
        $link = new Link($protocol_link, $protocol_content);

        $saved_img = array();
        $obj = array();
        $this->smarty->assign(array(
            'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
            'mediumSize' => Image::getSize(ImageType::getFormattedName('medium')),
            'smallSize' => Image::getSize(ImageType::getFormattedName('small'))
        ));

        $image_name = 'home';
        $image_name .= '_default';
        
        
        if($data['product']['str_id_product'])
        {
            # GET IMAGE OF PRODUCT
            $id_lang = Context::getContext()->language->id;
            $where = ' WHERE i.`id_product` IN ('.$data['product']['str_id_product'].') AND (ish.`cover`=0 OR ish.`cover` IS NULL) AND ish.`id_shop` = '.Context::getContext()->shop->id;
            $order = ' ORDER BY i.`id_product`,`position`';
            $limit = ' LIMIT 0,1';

            $id_shop = Context::getContext()->shop->id;
            
            
            $sql = 'SELECT DISTINCT i.`id_product`, ish.`cover`, i.`id_image`, il.`legend`, i.`position`,pl.`link_rewrite`, pl.`name`
                                    FROM `'._DB_PREFIX_.'image` i
                                    LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (i.`id_product` = pl.`id_product`) AND pl.`id_lang` = '.(int)$id_lang.'
                                    LEFT JOIN `'._DB_PREFIX_.'image_shop` ish ON (ish.`id_image` = i.`id_image` AND ish.`id_shop` = '.(int)$id_shop.')
                                    LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')'.pSql($where).' '.pSQL($order);
            
            $image_product =  Db::getInstance()->executeS($sql);
            
            if($image_product)
            {
                foreach ($image_product as $product) {
                    if (!in_array($product['id_product'], $saved_img)) {
                        $obj[] = array(
                            'id' => $product['id_product'],
                            'id_product_attribute' => $product['id_product']. '-0',
                            'content' => ($link->getImageLink($product['link_rewrite'], $product['id_image'], $image_name)),
                            'name' => $product['name'],
                        );
                        $saved_img[] = $product['id_product'];
                    }
                }
            }
        }
        
        if($data['attribute']['str_id_product'] && $data['attribute']['str_id_attribute'])
        {
            # GET IMAGE OF ATTRIBUTE
            $sql = 'SELECT DISTINCT i.`id_product`, pai.`id_product_attribute`, ish.`cover`, i.`id_image`, il.`legend`, i.`position`,pl.`link_rewrite`, pl.`name` 
                    FROM `'._DB_PREFIX_.'image` i
                    INNER JOIN `'._DB_PREFIX_.'image_shop` ish ON (i.`id_image` = ish.`id_image` AND ish.`id_shop` = '.(int)$id_shop.')
                    INNER JOIN `'._DB_PREFIX_.'image_lang` il  ON (i.`id_image` = il.`id_image`  AND il.`id_lang` = '.(int)$id_lang.')
                    INNER JOIN `'._DB_PREFIX_.'product_attribute_image` pai ON (i.`id_image` = pai.`id_image` AND pai.`id_product_attribute` IN ('.$data['attribute']['str_id_attribute'].'))
                    INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (i.`id_product` = pl.`id_product` AND pl.`id_lang` = 1 AND pl.`id_product` IN('.$data['attribute']['str_id_product'].'))
                    ORDER BY pai.`id_product_attribute` ASC, i.`position` ASC';
            $image_attribute =  Db::getInstance()->executeS($sql);
            
            if($image_attribute)
            {
                $index = array();
                foreach ($image_attribute as $product) {
                    if(isset($index[$product['id_product']])){
                        $index[$product['id_product']] += 1;
                    }else{
                        $index[$product['id_product']] = 1;
                    }
                    
                    if (!in_array($product['id_product'], $saved_img)) {
                        if($index[$product['id_product']] == 2){
                            $obj[] = array(
                                'id' => $product['id_product'],
                                'id_product_attribute' => $product['id_product'] . '-' . $product['id_product_attribute'],
                                'content' => ($link->getImageLink($product['link_rewrite'], $product['id_image'], $image_name)),
                                'name' => $product['name'],
                            );
                            $saved_img[] = $product['id_product'];
                        }
                    }
                }
            }
        }
        
        
        return $obj;
    }

    public function hookProductCdown($leo_pro_cdown)
    {
        $id_lang = Context::getContext()->language->id;
        $product_list = $this->getProducts($leo_pro_cdown, $id_lang);
        $obj = array();
        foreach ($product_list as $product) {
            $this->smarty->assign('product', $product);
            $obj[] = array('id' => $product['id_product'], 'content' => ($this->display(__FILE__, 'cdown.tpl')));
        }
        return $obj;
    }

    public function hookProductColor($leo_pro_color)
    {
        $id_lang = Context::getContext()->language->id;
        $colors = array();
        $leo_customajax_color = Configuration::get('APPAGEBUILDER_COLOR');
        if ($leo_customajax_color) {
            $arrs = explode(',', $leo_customajax_color);
            foreach ($arrs as $arr) {
                $items = explode(':', $arr);
                $colors[$items[0]] = $items[1];
            }
        }
        $this->smarty->assign(array(
            'colors' => $colors,
        ));
        $product_list = $this->getProducts($leo_pro_color, $id_lang);
        $obj = array();
        foreach ($product_list as $product) {
            $this->smarty->assign('product', $product);
            $obj[] = array('id' => $product['id_product'], 'content' => ($this->display(__FILE__, 'color.tpl')));
        }
        return $obj;
    }
    
    public function hookModuleRoutes($params)
    {
        $routes = array();
        $model = new ApPageBuilderProfilesModel();
        $allProfileArr = $model->getAllProfileByShop();
        foreach ($allProfileArr as $allProfileItem) {
            if (isset($allProfileItem['friendly_url']) && $allProfileItem['friendly_url']) {
                $routes['module-appagebuilder-'.$allProfileItem['friendly_url']] = array(
                    'controller' => 'appagebuilderhome',
                    'rule' => $allProfileItem['friendly_url'].'.html',
                    'keywords' => array(
                    ),
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'appagebuilder'
                    )
                );
            }
        }
        return $routes;
    }

    public function getProducts($product_list, $id_lang, $colors = array())
    {
        $product_list = apPageHelper::addonValidInt( $product_list );         # We validate id_categories in apPageHelper::addonValidInt function . This function is used at any where
        $context = Context::getContext();
        $id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        $ids = Address::getCountryAndState($id_address);
        $id_country = ($ids['id_country'] ? $ids['id_country'] : Configuration::get('PS_COUNTRY_DEFAULT'));
        $sql = 'SELECT p.*, product_shop.*, pl.* , m.`name` AS manufacturer_name, s.`name` AS supplier_name,sp.`id_specific_price`
                                FROM `'._DB_PREFIX_.'product` p
                                '.Shop::addSqlAssociation('product', 'p').'
                                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
                                LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
                                LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)
                                LEFT JOIN `'._DB_PREFIX_.'specific_price` sp ON (sp.`id_product` = p.`id_product`
                                                AND sp.`id_shop` IN(0, '.(int)$context->shop->id.')
                                                AND sp.`id_currency` IN(0, '.(int)$context->currency->id.')
                                                AND sp.`id_country` IN(0, '.(int)$id_country.')
                                                AND sp.`id_group` IN(0, '.(int)$context->customer->id_default_group.')
                                                AND sp.`id_customer` IN(0, '.(int)$context->customer->id.')
                                                AND sp.`reduction` > 0
                                        )
                                WHERE pl.`id_lang` = '.(int)$id_lang.
                ' AND p.`id_product` in ('.pSQL($product_list).')';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($product_list) {
            $tmp_img = array();
            $cover_img = array();
            $where = ' WHERE i.`id_product` IN ('.pSQL($product_list).') AND ish.`id_shop` = '.Context::getContext()->shop->id;
            $order = ' ORDER BY i.`id_product`,`position`';

            switch (Configuration::get('LEO_MINFO_SORT')) {
                case 'position2':
                    break;
                case 'random':
                    $order = ' ORDER BY RAND()';
                    break;
                default:
                    $order = ' ORDER BY i.`id_product`,`position` DESC';
            }

            $list_img = $this->getAllImages($id_lang, $where, $order);
            foreach ($list_img as $val) {
                $tmp_img[$val['id_product']][$val['id_image']] = $val;
                if ($val['cover'] == 1) {
                    $cover_img[$val['id_product']] = $val['id_image'];
                }
            }
        }
        $now = date('Y-m-d H:i:s');
        $finish = $this->l('Expired');
        foreach ($result as &$val) {
            $time = false;
            if (isset($tmp_img[$val['id_product']])) {
                $val['images'] = $tmp_img[$val['id_product']];
                $val['id_image'] = $cover_img[$val['id_product']];
            } else {
                $val['images'] = array();
            }

            $val['specific_prices'] = self::getSpecificPriceById($val['id_specific_price']);
            if (isset($val['specific_prices']['from']) && $val['specific_prices']['from'] > $now) {
                $time = strtotime($val['specific_prices']['from']);
                $val['finish'] = $finish;
                $val['check_status'] = 0;
                $val['lofdate'] = Tools::displayDate($val['specific_prices']['from']);
            } elseif (isset($val['specific_prices']['to']) && $val['specific_prices']['to'] > $now) {
                $time = strtotime($val['specific_prices']['to']);
                $val['finish'] = $finish;
                $val['check_status'] = 1;
                $val['lofdate'] = Tools::displayDate($val['specific_prices']['to']);
            } elseif (isset($val['specific_prices']['to']) && $val['specific_prices']['to'] == '0000-00-00 00:00:00') {
                $val['js'] = 'unlimited';
                $val['finish'] = $this->l('Unlimited');
                $val['check_status'] = 1;
                $val['lofdate'] = $this->l('Unlimited');
            } else if (isset($val['specific_prices']['to'])) {
                $time = strtotime($val['specific_prices']['to']);
                $val['finish'] = $finish;
                $val['check_status'] = 2;
                $val['lofdate'] = Tools::displayDate($val['specific_prices']['from']);
            }
            if ($time) {
                $val['js'] = array(
                    'month' => date('m', $time),
                    'day' => date('d', $time),
                    'year' => date('Y', $time),
                    'hour' => date('H', $time),
                    'minute' => date('i', $time),
                    'seconds' => date('s', $time)
                );
            }
        }
        unset($colors);
        return Product::getProductsProperties($id_lang, $result);
    }

    public static function getSpecificPriceById($id_specific_price)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return array();
        }

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                        SELECT *
                        FROM `'._DB_PREFIX_.'specific_price` sp
                        WHERE `id_specific_price` ='.(int)$id_specific_price);

        return $res;
    }

    public function getAllImages($id_lang, $where, $order)
    {
        $id_shop = Context::getContext()->shop->id;
        $sql = 'SELECT DISTINCT i.`id_product`, ish.`cover`, i.`id_image`, il.`legend`, i.`position`,pl.`link_rewrite`, pl.`name`
                                FROM `'._DB_PREFIX_.'image` i
                                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (i.`id_product` = pl.`id_product`) AND pl.`id_lang` = '.(int)$id_lang.'
                                LEFT JOIN `'._DB_PREFIX_.'image_shop` ish ON (ish.`id_image` = i.`id_image` AND ish.`id_shop` = '.(int)$id_shop.')
                                LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')'.pSql($where).' '.pSQL($order);
        return Db::getInstance()->executeS($sql);
    }

    // show category and tags of product
    public function hookdisplayProductInformation($params)
    {
        $return = '';
        $product_id = $params['product']->id;
        $category_id = $params['product']->id_category_default;
        $cat = new Category($category_id, $this->context->language->id);
        $product_tags = Tag::getProductTags($product_id);
        $product_tags = $product_tags[(int)$this->context->cookie->id_lang];
        $return .= '<div class =category>Category: <a href="'.$this->context->link->getCategoryLink($category_id, $cat->link_rewrite).'">'.$cat->name.'</a>.</div>';
        $return .= '<div class="producttags clearfix">';
        $return .= 'Tag: ';
        if ($product_tags && count($product_tags) > 1) {
            $count = 0;
            foreach ($product_tags as $tag) {
                $return .= '<a href="'.$this->context->link->getPageLink('search', true, null, "tag=$tag").'">'.$tag.'</a>';
                if ($count < count($product_tags) - 1) {
                    $return .= ',';
                } else {
                    $return .= '.';
                }
                $count++;
            }
        }
        $return .= '</div>';
        return $return;
    }
    
    /**
     * alias from apPageHelper::getConfig()
     */
    public function getConfigName($name)
    {
        return apPageHelper::getConfigName($name);
    }
    
    /**
     * alias from apPageHelper::getConfig()
     */
    public function getConfig($name)
    {
        return apPageHelper::getConfig($name);
    }
    
    /**
     * get Value of configuration based on actived theme
     */
    public function getPanelConfig($key, $default = '', $id_lang = null)
    {
        if (Tools::getIsset($key)) {
            # validate module
            return Tools::getValue($key);
        }

        $cookie = LeoFrameworkHelper::getCookie();
        
        if (isset($cookie[$this->themeCookieName.'_'.$key])) {
            return $cookie[$this->themeCookieName.'_'.$key];
        }

        unset($default);
        return Configuration::get($this->getConfigName($key), $id_lang);
    }

    public function generateLeoHtmlMessage()
    {
        $html = '';
        if (count($this->_confirmations)) {
            foreach ($this->_confirmations as $string) {
                $html .= $this->displayConfirmation($string);
            }
        }
        if (count($this->_errors)) {
            $html .= $this->displayError($this->_errors);
        }
        if (count($this->_warnings)) {
            $html .= $this->displayWarning($this->_warnings);
        }
        return $html;
    }

    /**
     * Common method
     * Resgister all hook for module
     */
    public function registerLeoHook()
    {
        $res = true;
        $res &= $this->registerHook('header');
        $res &= $this->registerHook('actionShopDataDuplication');
        $res &= $this->registerHook('displayBackOfficeHeader');
        $res &= $this->registerHook('moduleroutes');
        foreach (ApPageSetting::getHook('all') as $value) {
            $res &= $this->registerHook($value);
        }
        # register hook to show when paging
        $this->registerHook('pagebuilderConfig');
        
        # register hook to show category and tags of product
        $this->registerHook('displayProductInformation');
        
        # register hook again to after install/change theme
        $this->registerHook('actionObjectShopUpdateAfter');
        
        # Multishop create new shop
        $this->registerHook('actionAdminShopControllerSaveAfter');
        
        $this->registerHook('displayProductButtons');
        $this->registerHook('displayReassurance');
        $this->registerHook('displayLeoProfileProduct');
        # MoveEndHeader
        $this->registerHook('actionModuleRegisterHookAfter');
        #select product layout
        $this->registerHook('actionObjectProductUpdateAfter');
        $this->registerHook('displayAdminProductsExtra');
        #select category layout
        $this->registerHook('actionObjectCategoryUpdateAfter');
        $this->registerHook('displayBackOfficeCategory');

        # register hook for apshortcode
        $this->registerHook('displayApSC');
        $this->registerHook('actionAdminControllerSetMedia');
        $this->registerHook('displayMaintenance');              // ApShortCode for maintain page
        $this->registerHook('actionOutputHTMLBefore');          // ApShortCode for maintain page
	$this->registerHook('displayApCustom');
        return $res;
    }
    
    /**
     * @Action Create new shop, choose theme then auto restore datasample.
     */
    public function hookActionAdminShopControllerSaveAfter($param)
    {
        if (Tools::getIsset('controller') !== false && Tools::getValue('controller') == 'AdminShop'
                && Tools::getIsset('submitAddshop') !== false && Tools::getValue('submitAddshop')
                && Tools::getIsset('theme_name') !== false && Tools::getValue('theme_name')) {
            $shop = $param['return'];
            
            if (file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php')) {
                require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');
                $sample = new Datasample();
                apPageHelper::$id_shop = $shop->id;
                $sample->_id_shop = $shop->id;
                $sample->processImport('appagebuilder');
            }
        }
    }

    public function hookDisplayBackOfficeCategory($params)
    {
        // PS 1760 not support this hook.
        // change to hookDisplayAdminEndContent
//        if (Validate::isLoadedObject($category = new Category((int)Tools::getValue('id_category')))) {
//            // validate module
//            unset($category);
//            
//            $id_shop = Context::getContext()->shop->id;
//            
//            $category_layouts = array();
//            $id_category = Tools::getValue('id_category');
//            
//            if (is_dir(apPageHelper::getConfigDir('theme_profiles'))) {
//                //DONGND:: fix get list product list via database
//                $sql = 'SELECT * FROM '._DB_PREFIX_.'appagebuilder_products p
//                        INNER JOIN '._DB_PREFIX_.'appagebuilder_products_shop ps on(p.id_appagebuilder_products = ps.id_appagebuilder_products) WHERE ps.id_shop='.(int)$id_shop;
//                $category_layouts = Db::getInstance()->executeS($sql);
//            }
//
//            $extrafied = array();
//            $data_fields = array();
//
//            if (Configuration::get('APPAGEBUILDER_CATEGORY_TEXTEXTRA') || Configuration::get('APPAGEBUILDER_CATEGORY_EDITOREXTRA')) {
//                $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.'appagebuilder_extracat' .'`';
//                $result = Db::getInstance()->executeS($sql);
//
//                $rows = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'appagebuilder_extracat' .'` WHERE id_category="'.(int)$id_category.'" AND id_shop="'.(int)$id_shop.'"');
//
//                foreach ($result as $value) {
//                    if ($value['Field'] != 'id_category' && $value['Field'] != 'id_shop' && $value['Field'] != 'id_lang') {
//                        $extrafied[$value['Field']] = $value['Type'];
//                        foreach ($rows as $row) {
//                            $data_fields[$value['Field']][$row['id_lang']] = $row[$value['Field']];
//                        }
//                    }
//                }
//            }
//
//            $this->context->smarty->assign(array(
//                'category_layouts' => $category_layouts,
//                'apextras' => $extrafied,
//                'id_lang_default' => $this->default_language['id_lang'],
//                'languages' => $this->languages,
//                'data_fields' => $data_fields,
//                'current_layout' => Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT page from `'._DB_PREFIX_.'appagebuilder_page` where id_category = \''.(int)$id_category.'\' AND id_shop = \''.(int)$id_shop.'\'')
//            ));
//            die('ab');
//            return $this->display(__FILE__, 'categoryExtra.tpl');
//        }
    }
    
    public function hookDisplayAdminEndContent($params)
    {
        $id_category = Tools::getValue('id_category');
        if ($id_category == false && preg_match('#/sell/catalog/categories/(?P<id_category>[a-zA-Z0-9_.-]+)/edit#sD', $_SERVER['REQUEST_URI'], $matches) ) {
            if(isset($matches['id_category']) && $matches['id_category']) {
                $id_category = $matches['id_category'];
            }
        }
        if (Validate::isLoadedObject($category = new Category($id_category))) {
            
            // validate module
            unset($category);
            
            $id_shop = Context::getContext()->shop->id;
            
            $category_layouts = array();
//            $id_category = Tools::getValue('id_category');
            
            if (is_dir(apPageHelper::getConfigDir('theme_profiles'))) {
                //DONGND:: fix get list product list via database
                $sql = 'SELECT * FROM '._DB_PREFIX_.'appagebuilder_products p
                        INNER JOIN '._DB_PREFIX_.'appagebuilder_products_shop ps on(p.id_appagebuilder_products = ps.id_appagebuilder_products) WHERE ps.id_shop='.(int)$id_shop;
                $category_layouts = Db::getInstance()->executeS($sql);
            }

            $extrafied = array();
            $data_fields = array();

            if (Configuration::get('APPAGEBUILDER_CATEGORY_TEXTEXTRA') || Configuration::get('APPAGEBUILDER_CATEGORY_EDITOREXTRA')) {
                $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.'appagebuilder_extracat' .'`';
                $result = Db::getInstance()->executeS($sql);

                $rows = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'appagebuilder_extracat' .'` WHERE id_category="'.(int)$id_category.'" AND id_shop="'.(int)$id_shop.'"');

                foreach ($result as $value) {
                    if ($value['Field'] != 'id_category' && $value['Field'] != 'id_shop' && $value['Field'] != 'id_lang') {
                        $extrafied[$value['Field']] = $value['Type'];
                        foreach ($rows as $row) {
                            $data_fields[$value['Field']][$row['id_lang']] = $row[$value['Field']];
                        }
                    }
                }
            }

            $this->context->smarty->assign(array(
                'category_layouts' => $category_layouts,
                'apextras' => $extrafied,
                'id_lang_default' => $this->default_language['id_lang'],
                'languages' => $this->languages,
                'data_fields' => $data_fields,
                'current_layout' => Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT page from `'._DB_PREFIX_.'appagebuilder_page` where id_category = \''.(int)$id_category.'\' AND id_shop = \''.(int)$id_shop.'\'')
            ));

            return $this->display(__FILE__, 'categoryExtra.tpl');
        }
    }

    public function hookActionObjectCategoryUpdateAfter($params)
    {
        $id_category = Tools::getValue('id_category');
        
        # FIX 1760 dont have params id_category
        if ($id_category == false &&  preg_match('#/sell/catalog/categories/(?P<id_category>[a-zA-Z0-9_.-]+)/edit#sD', $_SERVER['REQUEST_URI'], $matches) ) {
            if(isset($matches['id_category']) && $matches['id_category']) {
                $id_category = $matches['id_category'];
            }
        }
        
        //DONGND:: fix when change status category at category list (BO)
        if (isset($id_category) && $id_category) {
            $aplayout = Tools::getValue('aplayout');
            $id_shop = Context::getContext()->shop->id;
            $sql = 'SELECT page from `'._DB_PREFIX_.'appagebuilder_page` where id_category = \''.(int)$id_category.'\' AND id_shop = \''.(int)$id_shop.'\'';
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
           
            if ($result) {
                if ($aplayout == 'default') {
                    Db::getInstance()->execute('DELETE from `'._DB_PREFIX_.'appagebuilder_page` where id_category = \''.(int)$id_category.'\' and id_shop=\''.(int)$id_shop.'\'');
                } else {
                    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'appagebuilder_page` set page = \''.pSQL($aplayout).'\' where id_category = \''.(int)$id_category.'\' and id_shop=\''.(int)$id_shop.'\'');
                }
            } else {
                if ($aplayout != 'default') {
                    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'appagebuilder_page` (`id_product`,`id_category`,`page`,`id_shop`) VALUES (0,'.(int)$id_category.',\''.pSQL($aplayout).'\','.(int)$id_shop.')');
                }
            }
            if (Configuration::get('APPAGEBUILDER_CATEGORY_TEXTEXTRA') || Configuration::get('APPAGEBUILDER_CATEGORY_EDITOREXTRA')) {
                //save for extrafield
                $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.'appagebuilder_extracat' .'`';
                $result = Db::getInstance()->executeS($sql);
                $extrafied = array();
                foreach ($result as $value) {
                    if ($value['Field'] != 'id_category' && $value['Field'] != 'id_shop' && $value['Field'] != 'id_lang') {
                        $extrafied[] = $value['Field'];
                    }
                }
                if ($extrafied) {
                    foreach ($this->languages as $lang) {
                        $checkExist =  Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'appagebuilder_extracat' .'` WHERE id_shop="'.(int)$id_shop.'" AND id_category = "'.(int)$id_category.'" AND id_lang="'.(int)$lang['id_lang'].'"');
                        if ($checkExist) {
                            $sql = '';
                            foreach ($extrafied as $value) {
                                $sql .= (($sql=='')?'':','). '`'.bqSQL($value).'`="'.pSQL(Tools::getValue($value.'_'.$lang['id_lang'])).'"';
                            }
                            $sql = 'UPDATE `'._DB_PREFIX_.'appagebuilder_extracat' .'` SET '.$sql.' WHERE id_shop="'.(int)$id_shop.'" AND id_category = "'.(int)$id_category.'" AND id_lang="'.(int)$lang['id_lang'].'"';
                        } else {
                            $sql = 'INSERT INTO `'._DB_PREFIX_.'appagebuilder_extracat' .'` (`id_category`, `id_shop`, `id_lang`';
                            $sql1 = ' VALUES ("'.(int)$id_category.'","'.(int)$id_shop.'","'.(int)$lang['id_lang'].'"';
                            foreach ($extrafied as $value) {
                                $sql .= ',`'.bqSQL($value).'`';
                                $sql1 .= ',"'.((Tools::getValue($value.'_'.$lang['id_lang'])=='')? pSQL(Tools::getValue($value.'_'.$this->default_language['id_lang'])) : pSQL(Tools::getValue($value.'_'.$lang['id_lang']))).'"';
                            }
                            $sql = $sql.')'.$sql1.')';
                        }
                        //echo $sql.'<br/>';
                        Db::getInstance()->execute($sql);
                    }
                }
            }
        }
        //die;
    }

    public function hookfilterCategoryContent($params)
    {
        $params['object']['description'] = $this->buildShortCode($params['object']['description']);
        
        $id_category = Tools::getValue('id_category');
        $id_shop = Context::getContext()->shop->id;

        if (Configuration::get('APPAGEBUILDER_CATEGORY_TEXTEXTRA') || Configuration::get('APPAGEBUILDER_CATEGORY_EDITOREXTRA')) {
            $rows = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'appagebuilder_extracat' .'` WHERE id_category="'.(int)$id_category.'" AND id_shop="'.(int)$id_shop.'" AND id_lang="'.(int)$this->default_language['id_lang'].'"');
            foreach ($rows as $value) {
                foreach ($value as $k => $v) {
                    if ($k != 'id_category' && $k != 'id_shop' && $k != 'id_lang') {
                        $params['object'][$k] = $v;
                    }
                }
            }
        }

        //layout
        $sql = 'SELECT page from `'._DB_PREFIX_.'appagebuilder_page` where id_category = \''.(int)$id_category.'\' AND id_shop = \''.(int)$id_shop.'\'';
        $layout = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        $params['object']['categoryLayout'] = $layout;

        return $params;
    }

    public function hookActionObjectProductUpdateAfter($params)
    {
        $id_product = Tools::getValue('id_product');

        //DONGND:: fix when change status product at product list (BO)
        if (isset($id_product) && $id_product) {
            $aplayout = Tools::getValue('aplayout');
            $id_shop = Context::getContext()->shop->id;
            $sql = 'SELECT * from `'._DB_PREFIX_.'appagebuilder_page` WHERE id_product = '.(int)$id_product.' AND id_shop = '.(int)$id_shop;
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            
            if ($result) {
                if ($aplayout == 'default') {
                    Db::getInstance()->execute('DELETE from `'._DB_PREFIX_.'appagebuilder_page` WHERE id_product = '.(int)$id_product.' and id_shop='.(int)$id_shop);
                } else {
                    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'appagebuilder_page` SET page = "'.pSQL($aplayout).'" WHERE id_product = '.(int)$id_product.' and id_shop='.(int)$id_shop);
                }
            } else {
                if ($aplayout != 'default') {
                    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'appagebuilder_page` (`id_product`,`id_category`,`page`,`id_shop`) VALUES ('.(int)$id_product.',0,\''.pSQL($aplayout).'\','.(int)$id_shop.')');
                }
            }
        
            if (Configuration::get('APPAGEBUILDER_PRODUCT_TEXTEXTRA') || Configuration::get('APPAGEBUILDER_PRODUCT_EDITOREXTRA')) {
                //save for extrafield
                $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.'appagebuilder_extrapro' .'`';
                $result = Db::getInstance()->executeS($sql);
                $extrafied = array();
                foreach ($result as $value) {
                    if ($value['Field'] != 'id_product' && $value['Field'] != 'id_shop' && $value['Field'] != 'id_lang') {
                        $extrafied[] = $value['Field'];
                    }
                }
                if ($extrafied) {
                    $form = Tools::getValue('form');
                    $tab_hooks = $form['tab_hooks'];
                    foreach ($this->languages as $lang) {
                        $checkExist =  Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'appagebuilder_extrapro' .'` WHERE id_shop="'.(int)$id_shop.'" AND id_product = "'.(int)$id_product.'" AND id_lang="'.(int)$lang['id_lang'].'"');
                        if ($checkExist) {
                            $sql = '';
                            foreach ($extrafied as $value) {
                                $sql .= (($sql=='')?'':',').'`'.bqSQL($value).'`="'.pSQL($tab_hooks[$value][$lang['id_lang']]).'"';
                            }
                            $sql = 'UPDATE `'._DB_PREFIX_.'appagebuilder_extrapro' .'` SET '.$sql.' WHERE id_shop="'.(int)$id_shop.'" AND id_product = "'.(int)$id_product.'" AND id_lang="'.(int)$lang['id_lang'].'"';
                        } else {
                            $sql = 'INSERT INTO `'._DB_PREFIX_.'appagebuilder_extrapro' .'` (`id_product`, `id_shop`, `id_lang`';
                            $sql1 = ' VALUES ("'.(int)$id_product.'","'.(int)$id_shop.'","'.(int)$lang['id_lang'].'"';
                            foreach ($extrafied as $value) {
                                $sql .= ',`'.bqSQL($value).'`';
                                $sql1 .= ',"'.pSQL(($tab_hooks[$value][$lang['id_lang']]=='')?pSQL($tab_hooks[$value][$this->default_language['id_lang']]) : pSQL($tab_hooks[$value][$lang['id_lang']])).'"';
                            }
                            $sql = $sql.')'.$sql1.')';
                        }
                        //echo $sql;
                        Db::getInstance()->execute($sql);
                    }
                }
            }
        }
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        if (Validate::isLoadedObject($product = new Product((int)$params['id_product']))) {
            // validate module
            unset($product);
            
            $id_shop = Context::getContext()->shop->id;
            $extrafied = array();
            $data_fields = array();

            if (Configuration::get('APPAGEBUILDER_PRODUCT_TEXTEXTRA') || Configuration::get('APPAGEBUILDER_PRODUCT_EDITOREXTRA')) {
                $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.'appagebuilder_extrapro' .'`';
                $result = Db::getInstance()->executeS($sql);
                
                $rows = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'appagebuilder_extrapro' .'` WHERE id_product="'.(int)$params['id_product'].'" AND id_shop="'.(int)$id_shop.'"');

                foreach ($result as $value) {
                    if ($value['Field'] != 'id_product' && $value['Field'] != 'id_shop' && $value['Field'] != 'id_lang') {
                        $extrafied[$value['Field']] = $value['Type'];
                        foreach ($rows as $row) {
                            $data_fields[$value['Field']][$row['id_lang']] = $row[$value['Field']];
                        }
                    }
                }
            }
//            $product_layouts = array();
            $sql = 'SELECT a.`plist_key`, a.`name` FROM `'._DB_PREFIX_.'appagebuilder_details` AS a INNER JOIN `'._DB_PREFIX_.'appagebuilder_details_shop` AS b ON a.`id_appagebuilder_details` = b.`id_appagebuilder_details' .'` WHERE b.id_shop= "'.(int)$id_shop.'"';
            $list = Db::getInstance()->executeS($sql);

            $this->context->smarty->assign(array(
                'product_layouts' => $list,
//                'default_plist' => $default_plist,
                'id_product' => (int)Tools::getValue('id_product'),
                'apextras' => $extrafied,
                'languages' => $this->languages,
                'data_fields' => $data_fields,
                'default_language' => $this->default_language,
                'current_layout' => Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT page from `'._DB_PREFIX_.'appagebuilder_page` where id_product = \''.(int)$params['id_product'].'\' AND id_shop = \''.(int)$id_shop.'\'')
            ));
            
            return $this->display(__FILE__, 'productExtra.tpl');
        }
    }

    public function hookfilterProductContent($params)
    {
        $params['object']['description'] = $this->buildShortCode($params['object']['description']);
        $params['object']['description_short'] = $this->buildShortCode($params['object']['description_short']);
                
        $id_product = Tools::getValue('id_product');
        $id_shop = Context::getContext()->shop->id;
        //extra fields
        if (Configuration::get('APPAGEBUILDER_PRODUCT_TEXTEXTRA') || Configuration::get('APPAGEBUILDER_PRODUCT_EDITOREXTRA')) {
            $rows = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'appagebuilder_extrapro' .'` WHERE id_product="'.(int)$id_product.'" AND id_shop="'.(int)$id_shop.'" AND id_lang="'.(int)$this->default_language['id_lang'].'"');

            foreach ($rows as $value) {
                foreach ($value as $k => $v) {
                    if ($k != 'id_product' && $k != 'id_shop' && $k != 'id_lang') {
                        $params['object'][$k] = $v;
                    }
                }
            }
        }
        //DONGND:: get layout of product detail by parameter in URL
        if (Tools::getValue('layout')) {
            $sql = 'SELECT a.`plist_key` FROM `'._DB_PREFIX_.'appagebuilder_details` AS a INNER JOIN `'._DB_PREFIX_.'appagebuilder_details_shop` AS b ON a.`id_appagebuilder_details` = b.`id_appagebuilder_details' .'` WHERE b.`id_shop` = "'.(int)$id_shop.'" AND a.`plist_key` = "'.pSQL(Tools::getValue('layout')).'"';
            $layout = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            if ($layout) {
                $params['object']['productLayout'] = $layout;
                return $params;
            }
        }
        //layout
        $sql = 'SELECT page from `'._DB_PREFIX_.'appagebuilder_page` where id_product = \''.(int)$id_product.'\' AND id_shop = \''.(int)$id_shop.'\'';
        $layout = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        //get default
        if (!$layout) {
            $sql = 'SELECT a.`plist_key` FROM `'._DB_PREFIX_.'appagebuilder_details` AS a INNER JOIN `'._DB_PREFIX_.'appagebuilder_details_shop` AS b ON a.`id_appagebuilder_details` = b.`id_appagebuilder_details' .'` WHERE b.`id_shop` = "'.(int)$id_shop.'" AND b.`active` = 1';
            $layout = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }
        $params['object']['productLayout'] = $layout;

        return $params;
    }
    
    public function hookfilterCmsContent($params)
    {
        $params['object']['content'] = $this->buildShortCode($params['object']['content']);
        return $params;
    }
    
    public function hookfilterHtmlContent($params)
    {
        if($params['type'] == 'manufacturer')
        {
            $params['object']['description'] = $this->buildShortCode($params['object']['description']);
            $params['object']['short_description'] = $this->buildShortCode($params['object']['short_description']);
            return $params;
            
        }
        if($params['type'] == 'supplier')
        {
            $params['object']['description'] = $this->buildShortCode($params['object']['description']);
            return $params;
            
        }
    }
    
    public $is_maintain = false;
    public function hookDisplayMaintenance()
    {
        $this->is_maintain = true;
    }
    
    public function hookActionOutputHTMLBefore(&$params)
    {
        if($this->is_maintain){
            $params['html'] = $this->buildShortCode($params['html']);
        }
    }


    /**
     * PERMISSION ACCOUNT demo@demo.com
     */
    public function getPermission($variable, $employee = null)
    {
        if($variable == 'configure')
        {
            // Allow see form if permission is : configure, view
            $configure = Module::getPermissionStatic($this->id, 'configure', $employee);
            $view = Module::getPermissionStatic($this->id, 'view', $employee);
            return ($configure || $view);
        }
        
        return Module::getPermissionStatic($this->id, $variable, $employee);
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     */
    public function access($action)
    {
        $employee = null;
        return Module::getPermissionStatic($this->id, $action, $employee);
    }
    
    /**
     * Thanh_Phuong need show product_attribute at product_list
     * insert this code in tpl : {hook h='displayLeoProductAtribute' leoproduct=$product}
     * https://www.screencast.com/t/qxmmeaKk
     */
    public function hookDisplayLeoProductAtribute($params)
    {
        $cfg_show_color = 0;
        if (!Combination::isFeatureActive()) {
            return array();
        }
        
        $id_lang = Context::getContext()->language->id;
        $id_product = $params['leoproduct']['id'];
        
        $sql = 'SELECT ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name,
                    a.`id_attribute`, al.`name` AS attribute_name, a.`color` AS attribute_color, product_attribute_shop.`id_product_attribute`,
                    IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`price`, product_attribute_shop.`ecotax`, product_attribute_shop.`weight`,
                    product_attribute_shop.`default_on`, pa.`reference`, product_attribute_shop.`unit_price_impact`,
                    product_attribute_shop.`minimal_quantity`, product_attribute_shop.`available_date`, ag.`group_type`
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                ' . Product::sqlStock('pa', 'pa') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group`)
                ' . Shop::addSqlAssociation('attribute', 'a') . '
                WHERE pa.`id_product` = ' . (int) $id_product . '
                    AND al.`id_lang` = ' . (int) $id_lang . '
                    AND agl.`id_lang` = ' . (int) $id_lang . '
                GROUP BY id_attribute_group, id_product_attribute
                ORDER BY ag.`position` ASC, a.`position` ASC, agl.`name` ASC';

        $attributes_groups = Db::getInstance()->executeS($sql);
        
        if (is_array($attributes_groups) && $attributes_groups)
        {
            $groups = array();
            foreach ($attributes_groups as $k => $row)
            {
                if($cfg_show_color == 0 && $row['group_type']=='color'){
                    # Not show color
                    continue;
                }
                if (!isset($groups[$row['id_attribute_group']]))
                    $groups[$row['id_attribute_group']] = array(
                        'name' => $row['public_group_name'],
                        'group_type' => $row['group_type'],
                        'default' => -1,
                    );
                $groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = $row['attribute_name'];
                if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']]))
                    $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
                $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int)$row['quantity'];
                if($row['group_type']=='color'){
                    $texture = '';
                    if (Tools::isEmpty($row['attribute_color']) && @filemtime(_PS_COL_IMG_DIR_.$row['id_attribute'].'.jpg')) {
                        $texture = $this->context->link->getMediaLink(_THEME_COL_DIR_.$row['id_attribute'].'.jpg');
                    }
                    $groups[$row['id_attribute_group']]['colors'][$row['id_attribute']] = array(
                        'type' => $texture ? 1 : 0,
                        'value' => $texture?:$row['attribute_color'],
                    );
                }
            }
            
            $this->context->smarty->assign(array(
                'st_att_list_groups' => $groups,
                'st_att_list_show' => 1,
                'st_att_list_color' => 0,   // show color = text or corlor
                'st_att_list_center' => 0,
            ));
            return $this->fetch('module:appagebuilder/views/templates/hook/displayLeoProductAtribute.tpl');
        }
        
        
    }
}
