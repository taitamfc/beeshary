<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo Bootstrap Menu
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class LeoBtmegamenuHelper
{

    public static function getCategories()
    {
        $children = self::getIndexedCategories();
        $list = array();
        self::treeCategory(1, $list, $children);
        return $list;
    }

    public static function treeCategory($id, &$list, $children, $tree = "")
    {
        if (isset($children[$id])) {
            if ($id != 0) {
                $tree = $tree." - ";
            }
            foreach ($children[$id] as $v) {
                $v["tree"] = $tree;
                $list[] = $v;
                self::treeCategory($v["id_category"], $list, $children, $tree);
            }
        }
    }

    public static function getIndexedCategories()
    {
        global $cookie;
        $id_lang = $cookie->id_lang;
        $id_shop = Context::getContext()->shop->id;

        $join = 'JOIN `'._DB_PREFIX_.'category_shop` cs ON(c.`id_category` = cs.`id_category` AND cs.`id_shop` = '.(int)$id_shop.')';

        $allCat = Db::getInstance()->ExecuteS('
        SELECT c.*, cl.id_lang, cl.name, cl.description, cl.link_rewrite, cl.meta_title, cl.meta_keywords, cl.meta_description
        FROM `'._DB_PREFIX_.'category` c
        '.$join.'
        LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.(int)$id_lang.' AND cl.`id_shop` = '.(int)$id_shop.')
        LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = c.`id_category`)
        WHERE `active` = 1
        GROUP BY c.`id_category`
        ORDER BY `id_category` ASC');
        $children = array();
        if ($allCat) {
            foreach ($allCat as $v) {
                $pt = $v["id_parent"];
                $list = @$children[$pt] ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
            return $children;
        }
        return array();
    }

    public static function getCMSCategories()
    {
        $children = self::getIndexedCMSCategories();
        $list = array();
        self::treeCMSCategory(1, $list, $children);
        return $list;
    }

    public static function treeCMSCategory($id, &$list, &$children, $tree = "")
    {
        if (isset($children[$id])) {
            if ($id != 0 && $id != 1) {
                $tree = $tree." - ";
            }
            foreach ($children[$id] as &$v) {
                $v['tree'] = $tree;
                $v['name'] = $tree . $v['name'];
                $list[] = $v;
                self::treeCMSCategory($v['id_cms_category'], $list, $children, $tree);
            }
        }
    }

    public static function getIndexedCMSCategories()
    {
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        
        $sql = ' SELECT m.*, md.*
                FROM '._DB_PREFIX_.'cms_category m
                LEFT JOIN '._DB_PREFIX_.'cms_category_lang md ON m.id_cms_category = md.id_cms_category AND md.id_lang = '.(int)$id_lang . ' AND md.id_shop = '.(int)$id_shop
                .' JOIN '._DB_PREFIX_.'cms_category_shop bs ON m.id_cms_category = bs.id_cms_category AND bs.id_shop = '.(int)($id_shop);
//        if ($active) {
//            $sql .= ' WHERE m.`active`=1 ';
//        }

//        if ($id_leoblogcat != null) {
//            # validate module
//            $sql .= ' WHERE id_parent='.(int)$id_leoblogcat;
//        }
        $sql .= ' ORDER BY `position` ';
        $allCat = Db::getInstance()->ExecuteS($sql);
        $children = array();
        if ($allCat) {
            foreach ($allCat as $v) {
                $pt = $v["id_parent"];
                $list = @$children[$pt] ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
            return $children;
        }
        return array();
    }

    public static function getFieldValue($obj, $key, $id_lang = NULL, $id_shop = null)
    {
        if (!$id_shop && $obj->isLangMultishop()) {
            $id_shop = Context::getContext()->shop->id;
        }

        if ($id_lang) {
            $defaultValue = ($obj->id && isset($obj->{$key}[$id_lang])) ? $obj->{$key}[$id_lang] : '';
        }
        else {
            $defaultValue = isset($obj->{$key}) ? $obj->{$key} : '';
        }

        return Tools::getValue($key.($id_lang ? '_'.$id_shop.'_'.$id_lang : ''), $defaultValue);
    }

    public static function getPost($keys = array(), $lang = false)
    {
        $post = array();
        if ($lang === false) {
            foreach ($keys as $key) {
                // get value from $_POST
                if ($key == 'icon_class') {
                    //remove single quote and double quote if fill class font icon
                    $icon_class = Tools::getValue($key);
                    if ($icon_class != strip_tags($icon_class)) {
                        $post[$key] = $icon_class;
                    } else {
                        $post[$key] = str_replace(array('\'', '"'), '', $icon_class);
                    }
                } else {
                    $post[$key] = Tools::getValue($key);
                }
            }
        }
        if ($lang === true) {
            foreach ($keys as $key) {
                // get value multi language from $_POST
                foreach (Language::getIDs(false) as $id_lang) {
                    $post[$key.'_'.(int)$id_lang] = Tools::getValue($key.'_'.(int)$id_lang);
                }
            }
        }
        return $post;
    }

    public static function getConfigKey($multi_lang = false)
    {
        if ($multi_lang == false) {
            return array(
                'saveleobootstrapmenu',
                'id_btmegamenu',
                'id_parent',
                'active',
                'show_title',
                'sub_with',
                'type',
                'product_type',
                'cms_type',
                'category_type',
                'manufacture_type',
                'supplier_type',
                'controller_type',
                'controller_type_parameter',
                'target',
                'menu_class',
                'icon_class',
                'filename',
                'is_group',
                'colums',
                'tab',
                'groupBox',
            );
        } else {
            return array(
                'title',
                'text',
                'url',
                'content_text',
            );
        }
    }

    public static function getBaseLink($id_shop = null, $ssl = null, $relative_protocol = false)
    {
        static $force_ssl = null;

        if ($ssl === null) {
            if ($force_ssl === null) {
                $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            }
            $ssl = $force_ssl;
        }
        $context = Context::getContext();
        if (!$id_shop) {
            $id_shop = (int)Context::getContext()->shop->id;
        }
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null) {
            $shop = new Shop($id_shop);
        } else {
            $shop = Context::getContext()->shop;
        }

        $ssl_enable = Configuration::get('PS_SSL_ENABLED');
        if ($relative_protocol) {
            $base = '//'.($ssl && $ssl_enable ? $shop->domain_ssl : $shop->domain);
        } else {
            $base = (($ssl && $ssl_enable) ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);
        }

        return $base.$shop->getBaseURI();
    }
    
    public static function getLangLink($id_lang = null, Context $context = null, $id_shop = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        if (!$id_shop) {
            $id_shop = $context->shop->id;
        }

        $allow = (int)Configuration::get('PS_REWRITING_SETTINGS');
        if ((!$allow && in_array($id_shop, array($context->shop->id,  null))) || !Language::isMultiLanguageActivated($id_shop) || !(int)Configuration::get('PS_REWRITING_SETTINGS', null, null, $id_shop)) {
            return '';
        }

        if (!$id_lang) {
            $id_lang = $context->language->id;
        }

        return Language::getIsoById($id_lang).'/';
    }

    public static function leoCreateColumn($table_name, $col_name, $data_type)
    {
        $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.bqSQL($table_name) .'` LIKE "'.bqSQL($col_name).'"';
        $column = Db::getInstance()->executeS($sql);

        if (empty($column)) {
            $sql = 'ALTER TABLE `'._DB_PREFIX_.bqSQL($table_name).'` ADD COLUMN `'.bqSQL($col_name).'` '.pSQL($data_type);
            $res = Db::getInstance()->execute($sql);
        }
    }
    
    /**
     * @param
     * 0 no multi_lang
     * 1 multi_lang follow id_lang
     * 2 multi_lnag follow code_lang
     * @return array
     */
    public static function getPostAdmin($keys = array(), $multi_lang = 0)
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
    
    public static function genKey()
    {
        return md5(time().rand());
    }
    
    static $id_shop;
    /**
     * FIX Install multi theme
     * LeoBtmegamenuHelper::getIDShop();
     */
    public static function getIDShop()
    {
        if ((int)self::$id_shop) {
            $id_shop = (int)self::$id_shop;
        } else {
            $id_shop = (int)Context::getContext()->shop->id;
        }
        return $id_shop;
    }
    
    public static function base64Decode($data)
    {
        return call_user_func('base64_decode', $data);
    }

    public static function base64Encode($data)
    {
        return call_user_func('base64_encode', $data);
    }
    
    public static function autoUpdateModule()
    {
        $module = Module::getInstanceByName('leobootstrapmenu');
        if (Configuration::get('LEOBOOTSTRAPMENU_CORRECT_MOUDLE') != $module->version) {
            # UPDATE LATEST VERSION
            Configuration::updateValue('LEOBOOTSTRAPMENU_CORRECT_MOUDLE', $module->version);
            self::processCorrectModule();
        }
    }
    
    public static function processCorrectModule($quickstart = false)
    {
        //update size of filed menu class
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'btmegamenu` MODIFY `menu_class` varchar(255) DEFAULT NULL');
        
        Configuration::updateValue('BTMEGAMENU_GROUP_DE', '');
        //change table leowidgets to btmegamenu_widgets
        $correct_widget_table = Db::getInstance()->executeS('SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE TABLE_SCHEMA = "'._DB_NAME_.'" AND TABLE_NAME = "'._DB_PREFIX_.'btmegamenu_widgets"');
        if (count($correct_widget_table) < 1) {
            $correct_old_widget_table = Db::getInstance()->executeS('SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE TABLE_SCHEMA = "'._DB_NAME_.'" AND TABLE_NAME = "'._DB_PREFIX_.'leowidgets"');
            if (count($correct_old_widget_table) == 1) {
                Db::getInstance()->execute('RENAME TABLE `'._DB_PREFIX_.'leowidgets` TO `'._DB_PREFIX_.'btmegamenu_widgets`');
                Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'btmegamenu_widgets` CHANGE `id_leowidgets` `id_btmegamenu_widgets` int(11) NOT NULL AUTO_INCREMENT');
            } else {
                Db::getInstance()->execute('
                    CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'btmegamenu_widgets`(
                      `id_btmegamenu_widgets` int(11) NOT NULL AUTO_INCREMENT,
                      `name` varchar(250) NOT NULL,
                      `type` varchar(250) NOT NULL,
                      `params` LONGTEXT,
                      `id_shop` int(11) unsigned NOT NULL,
                      `key_widget` int(11)  NOT NULL,
                      PRIMARY KEY (`id_btmegamenu_widgets`,`id_shop`)
                    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
            }
        }
        
        //change id_parent of root menu to 0
        $correct_root_id = Db::getInstance()->getRow('SELECT `id_btmegamenu` FROM `'._DB_PREFIX_.'btmegamenu` WHERE `id_group` = 0 AND `id_parent` = 0');
            
        if ($correct_root_id) {
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'btmegamenu` WHERE `id_btmegamenu` = '.(int)$correct_root_id['id_btmegamenu']);
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'btmegamenu` SET `id_parent` = 0 WHERE `id_parent` = '.(int)$correct_root_id['id_btmegamenu']);
        }
        
        //move params widget from group to menu
        $correct_params_widgets_group = Db::getInstance()->executeS('SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = "'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'btmegamenu_group" AND column_name="params_widget"');
        
        if (count($correct_params_widgets_group) == 1) {
            $correct_params_widgets_menu = Db::getInstance()->executeS('SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = "'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'btmegamenu" AND column_name="params_widget"');
            if (count($correct_params_widgets_menu) < 1) {
                Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'btmegamenu` ADD `params_widget` LONGTEXT');
                $list_group = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'btmegamenu_group`');
                
                foreach ($list_group as $list_group_item) {
                    $group_param_widget = Tools::jsonDecode(self::base64Decode($list_group_item['params_widget']), true);
                    
                    if (count($group_param_widget) > 0) {
                        foreach ($group_param_widget as $group_param_widget_item) {
                            $id_menu = $group_param_widget_item['id'];
                            unset($group_param_widget_item['id']);
                            $new_param_widget = self::base64Encode(Tools::jsonEncode($group_param_widget_item));
                            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'btmegamenu` SET `params_widget` = "'.pSQL($new_param_widget).'" WHERE `id_btmegamenu` = '.(int)$id_menu);
                        }
                    }
                }
            }
            Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'btmegamenu_group` DROP `params_widget`');
        }
        
        //correct randkey
        $correct_randkey = Db::getInstance()->executeS('SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = "'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'btmegamenu_group" AND column_name="randkey"');
        if (count($correct_randkey) < 1) {
            Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'btmegamenu_group` ADD `randkey` varchar(255) DEFAULT NULL');
        }
        
        //correct form id for appagebuilder
        $correct_form_id = Db::getInstance()->executeS('SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = "'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'btmegamenu_group" AND column_name="form_id"');
        if (count($correct_form_id) < 1) {
            Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'btmegamenu_group` ADD `form_id` varchar(255) DEFAULT NULL');
        }
        
        //auto add randkey for group
        BtmegamenuGroup::autoCreateKey();
        
        //correct group title, change to multilang
        $correct_group_title = Db::getInstance()->executeS('SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = "'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'btmegamenu_group" AND column_name="title"');
        $correct_group_lang_table = Db::getInstance()->executeS('SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE TABLE_SCHEMA = "'._DB_NAME_.'" AND TABLE_NAME = "'._DB_PREFIX_.'btmegamenu_group_lang"');
        
        if (count($correct_group_title) >= 1 && count($correct_group_lang_table) < 1) {
            $list_group_title = Db::getInstance()->executeS('SELECT `id_btmegamenu_group`, `title` FROM `'._DB_PREFIX_.'btmegamenu_group`');
            Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'btmegamenu_group_lang` (
                  `id_btmegamenu_group` int(11) NOT NULL,
                  `id_lang` int(11) NOT NULL,
                  `title` varchar(255) DEFAULT NULL,
                  PRIMARY KEY (`id_btmegamenu_group`,`id_lang`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
            $list_lang = Language::getLanguages(false);
            foreach ($list_group_title as $list_group_title_item) {
                $group_id = $list_group_title_item['id_btmegamenu_group'];
                $group_title = $list_group_title_item['title'];
                foreach ($list_lang as $list_lang_item) {
                    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'btmegamenu_group_lang`(`id_btmegamenu_group`,`id_lang`,`title`) VALUES('.(int)$group_id.', '.(int)$list_lang_item['id_lang'].', "'.pSQL($group_title).'")');
                }
            }
            Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'btmegamenu_group` DROP COLUMN `title`');
        }
        Configuration::updateValue('BTMEGAMENU_GROUP_AUTO_CORRECT', 1);
        //correct title_fo
        $correct_title_fo = Db::getInstance()->executeS('SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = "'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'btmegamenu_group_lang" AND column_name="title_fo"');
        if (count($correct_title_fo) < 1) {
            Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'btmegamenu_group_lang` ADD `title_fo` varchar(255) DEFAULT NULL');
            $list_lang = Language::getLanguages(false);
            $group_title_fo = '';
            foreach ($list_lang as $list_lang_item) {
                if ($list_lang_item['iso_code'] == 'en') {
                    $group_title_fo = 'Categories';
                }
                if ($list_lang_item['iso_code'] == 'es') {
                    $group_title_fo = 'Categorías';
                }
                if ($list_lang_item['iso_code'] == 'fr') {
                    $group_title_fo = 'Catégories';
                }
                if ($list_lang_item['iso_code'] == 'de') {
                    $group_title_fo = 'Kategorien';
                }
                if ($list_lang_item['iso_code'] == 'it') {
                    $group_title_fo = 'Categorie';
                }
                if ($list_lang_item['iso_code'] == 'ar') {
                    $group_title_fo = 'ال�?ئات';
                }
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'btmegamenu_group_lang` SET `title_fo` =  "'.pSQL($group_title_fo).'" WHERE `id_lang` = '.(int)$list_lang_item['id_lang']);
            }
        }
        Configuration::updateValue('BTMEGAMENU_GROUP_AUTO_CORRECT_TITLE_FO', 1);
        
        # ADD FIELD "URL" TO "BTMEGAMENU_LANG" TABLE - BEGIN
        $exist_column = Db::getInstance()->executeS(" SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '"._DB_NAME_."' AND TABLE_NAME='"._DB_PREFIX_."btmegamenu_lang' AND COLUMN_NAME ='url'");
        if (count($exist_column) < 1) {
            Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'btmegamenu_lang` ADD `url` varchar(255) DEFAULT NULL');
            $menus = Db::getInstance()->executeS('SELECT `id_btmegamenu`, `id_parent`, `url` FROM `'._DB_PREFIX_.'btmegamenu`');
            if ($menus) {
                foreach ($menus as $menu) {
                    if ($menu['id_parent'] != 0) {
                        $megamenu = new Btmegamenu((int)$menu['id_btmegamenu']);
                        foreach ($megamenu->url as &$url) {
                            $url = $menu['url'] ? $menu['url'] : '';
                            # validate module
                            $validate_module = $url;
                            unset($validate_module);
                        }
                        $megamenu->update();
                    }
                }
            }
            Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'btmegamenu` DROP `url`');
        }
        # ADD FIELD "URL" TO "BTMEGAMENU_LANG" TABLE - END
        
        if (!Configuration::hasKey('BTMEGAMENU_GROUP_AUTO_CORRECT')) {
            //correct group title, change to multilang
            $correct_group_title = Db::getInstance()->executeS('SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = "'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'btmegamenu_group" AND column_name="title"');
            $correct_group_lang_table = Db::getInstance()->executeS('SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE TABLE_SCHEMA = "'._DB_NAME_.'" AND TABLE_NAME = "'._DB_PREFIX_.'btmegamenu_group_lang"');
            
            if (count($correct_group_title) >= 1 && count($correct_group_lang_table) < 1) {
                $list_group_title = Db::getInstance()->executeS('SELECT `id_btmegamenu_group`, `title` FROM `'._DB_PREFIX_.'btmegamenu_group`');
                Db::getInstance()->execute('
                    CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'btmegamenu_group_lang` (
                      `id_btmegamenu_group` int(11) NOT NULL,
                      `id_lang` int(11) NOT NULL,
                      `title` varchar(255) DEFAULT NULL,
                      PRIMARY KEY (`id_btmegamenu_group`, `id_lang`)
                    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
                $list_lang = Language::getLanguages(false);
                foreach ($list_group_title as $list_group_title_item) {
                    $group_id = $list_group_title_item['id_btmegamenu_group'];
                    $group_title = $list_group_title_item['title'];
                    foreach ($list_lang as $list_lang_item) {
                        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'btmegamenu_group_lang`(`id_btmegamenu_group`, `id_lang`, `title`) VALUES('.(int)$group_id.', '.(int)$list_lang_item['id_lang'].', "'.pSQL($group_title).'")');
                    }
                }
                Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'btmegamenu_group` DROP COLUMN `title`');
            }
            Configuration::updateValue('BTMEGAMENU_GROUP_AUTO_CORRECT', 1);
        }
        if (!Configuration::hasKey('BTMEGAMENU_GROUP_AUTO_CORRECT_TITLE_FO')) {
            //correct title_fo
            $correct_title_fo = Db::getInstance()->executeS('SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = "'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'btmegamenu_group_lang" AND column_name="title_fo"');
            if (count($correct_title_fo) < 1) {
                Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'btmegamenu_group_lang` ADD `title_fo` varchar(255) DEFAULT NULL');
                $list_lang = Language::getLanguages(false);
                $group_title_fo = '';
                foreach ($list_lang as $list_lang_item) {
                    if ($list_lang_item['iso_code'] == 'en') {
                        $group_title_fo = 'Categories';
                    }
                    if ($list_lang_item['iso_code'] == 'es') {
                        $group_title_fo = 'Categorías';
                    }
                    if ($list_lang_item['iso_code'] == 'fr') {
                        $group_title_fo = 'Catégories';
                    }
                    if ($list_lang_item['iso_code'] == 'de') {
                        $group_title_fo = 'Kategorien';
                    }
                    if ($list_lang_item['iso_code'] == 'it') {
                        $group_title_fo = 'Categorie';
                    }
                    if ($list_lang_item['iso_code'] == 'ar') {
                        $group_title_fo = 'ال�?ئات';
                    }
                    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'btmegamenu_group_lang` SET `title_fo` =  "'.pSQL($group_title_fo).'" WHERE `id_lang` = '.(int)$list_lang_item['id_lang']);
                }
            }
            Configuration::updateValue('BTMEGAMENU_GROUP_AUTO_CORRECT_TITLE_FO', 1);
        }
        
    }
    
    public static function getModuleTabs()
    {
        $data = array(
            array(
                'class_name' => 'AdminLeoBootstrapMenuModule',
                'name' => 'Leo Megamenu Configuration',
                'id_parent' => Tab::getIdFromClassName('AdminParentModulesSf'),
            ),
            array(
                'class_name' => 'AdminLeoWidgets',
                'name' => 'Leo Widgets',
                'id_parent' => -1,
            ),
        );
        
        return $data;
    }
}
