<?php
/**
 * 2007-2020 PrestaShop
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
 *   @author    Buy-addons <contact@buy-addons.com>
 *   @copyright 2007-2020 PrestaShop SA
 *   @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *   International Registered Trademark & Property of PrestaShop SA
 */

class Ba_importer extends Module
{
    const EACH = -1;
    public $webservice_url = 'http://webcron.prestashop.com/crons';
    public $shop_id;
    public $helperlist_id = 'importer_config';
    public function __construct()
    {
        $this->name = "ba_importer";
        $this->tab = "quick_bulk_update";
        $this->version = "1.1.13";
        $this->author = "buy-addons";
        $this->need_instance = 0;
        $this->secure_key = Tools::encrypt($this->name);
        $this->module_key = '53cbc72d58a88d2b87124fd4ced20701';
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Prestashop Importer - import product from csv, xls, xlsx');
        $this->description = $this->l('Author: buy-addons');
        if ($this->_path == null) {
            $this->_path = __PS_BASE_URI__ . 'modules/' . $this->name . '/';
        }
    }

    public function install()
    {
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        
        $ct_importer_config = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'ba_importer_config (
            `id_importer_config` int(11) unsigned NOT NULL auto_increment,
            `id_shop` int(11) unsigned DEFAULT NULL,
            `ba_name_setting` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
            `ba_name_file` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
            `import_local` int(11) unsigned DEFAULT NULL,
            `ba_step1` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
            `ba_step2` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
            `ba_step3` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
            `date_add` datetime DEFAULT NULL,
            `date_up` datetime DEFAULT NULL,
            PRIMARY KEY (`id_importer_config`)
        )';
        $db->query($ct_importer_config);
        
        $ct_ba_cronjobs = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'ba_cronjobs_importer (
            `id_cronjob` int(11) unsigned NOT NULL auto_increment,
            `id_importer_config` int(11) unsigned NOT NULL,
            `ba_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
            `hour` int(11) DEFAULT NULL,
            `day` int(11) DEFAULT NULL,
            `month` int(11) DEFAULT NULL,
            `day_of_week` int(11) DEFAULT NULL,
            `update_at` datetime DEFAULT NULL,
            `products_imported` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
            `status_imported` int(11) unsigned DEFAULT 1,
            `id_shop` int(11) unsigned DEFAULT NULL,
            `id_shop_group` int(11) unsigned DEFAULT NULL,
            `CONFIGN_DATA_POST` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
            `imported` int(11) unsigned DEFAULT NULL,
            PRIMARY KEY (`id_cronjob`, `id_importer_config`)
        )';
        $db->query($ct_ba_cronjobs);
        // since 1.1.0 add new table_name ps_productsinfile
        $productsinfile = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'ba_importer_productsinfile (
            `id_setting` int(11) unsigned NOT NULL auto_increment,
            `id_shop` int(11) unsigned NOT NULL,
            `id_product` int(11) unsigned NOT NULL,
            `id_product_attribute` int(11) unsigned NOT NULL,
            `date_add` datetime DEFAULT NULL,
            PRIMARY KEY (`id_setting`, `id_product`, `id_product_attribute`)
        )';
        $db->query($productsinfile);
        $list_id_shop = Shop::getCompleteListOfShopsID();
        foreach ($list_id_shop as $key_list => $value) {
            $value;
            $insert_importer_config = 'INSERT INTO ' . _DB_PREFIX_ . 'ba_importer_config (
                `id_shop`,`ba_name_setting`,`import_local`,`ba_step1`,`date_add`,`date_up`
            ) VALUES (
                \''. (int) $list_id_shop[$key_list].'\',
                \'Setting 1\',
                \'1\',
                \'{"select_settings":"1","name_settings":"Setting 1","import_local":"1","ftp_server":"",'
                   .'"ftp_user_name":"","ftp_user_port":"","ftp_user_transfer_mode":"",'
                   .'"ftp_user_pass":"","ftp_link_excel":"","url_excel":"","new_items":"Add",'
                   .'"existing_items":"Update","identify_existing_items":"- None -","import_items":"All",'
                   .'"product_start":"1","product_end":"1000","characters_csv":",","characters_category":"/",'
                   .'"import_header":"0","multi_lang":"1","combi_quanti":"1","cate_exist":"1",'
                   .'"manu_exist":"1","sup_exist":"1","fea_exist":"0","identify_existing_items_combi":"Attributes",'
                   .'"productsnotinfile":"- None -",'
                   .'"baencode":"utf8","product_type":"product_standard","submitimport":"","tab":"AdminModules"}\',
                NOW(),NOW()
            )';
            $db->query($insert_importer_config);
        }
        Configuration::updateGlobalValue('baautoimpor_is_run', 0);
        Configuration::updateValue('CRONJOBS_MODE', 'webservice');
        Configuration::updateValue('CRONJOB_BA_IMPORT_WEBSERVICE_ID', 0);
        $token = Tools::encrypt(Tools::getShopDomainSsl() . time());
        Configuration::updateGlobalValue('CRONJOBS_EXECUTION_TOKEN', $token);
        $confign_bc1 = '{"select_settings":"1","name_settings":"Setting 1","import_local":"1",'
                           .'"ftp_server":"","ftp_user_name":"","ftp_user_pass":"","ftp_user_port":"",'
                           .'"ftp_user_transfer_mode":"","ftp_link_excel":"","url_excel":"","new_items":"Add",'
                           .'"existing_items":"Update","identify_existing_items":"- None -","import_items":"All",'
                           .'"product_start":"1","product_end":"1000","characters_csv":",","characters_category":"/",'
                           .'"import_header":"0","multi_lang":"1","combi_quanti":"1","cate_exist":"1",'
                           .'"manu_exist":"1","sup_exist":"1","fea_exist":"0",'
                           .'"identify_existing_items_combi":"Attributes",'
                           .'"baencode":"utf8","product_type":"product_standard",'
                           .'"productsnotinfile":"- None -",'
                           .'"submitimport":"","tab":"AdminModules"}';
        
        $list_id_shop = Shop::getCompleteListOfShopsID();
        foreach ($list_id_shop as $key_list => $value) {
            Configuration::updateValue('CONFIGN_IMPORTER_BC1', $confign_bc1, false, '', $list_id_shop[$key_list]);
            Configuration::updateValue('CONFIG_SELECT_IMPORTER', null, false, '', $list_id_shop[$key_list]);
            Configuration::updateValue('CONFIGN_CRONJOB', null, false, '', $list_id_shop[$key_list]);
        }
        $id_shop = $this->context->shop->id;
        Configuration::updateValue('ba_id_shop', $id_shop, false, '', '');
        $this->saveDefaultConfig();
        $this->installTab();
        if (parent::install() == false || !$this->registerHook('DisplayBackOfficeHeader')) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        $this->uninstallTab();
        $sql = "DROP TABLE IF EXISTS " . _DB_PREFIX_ . "ba_abandoned_img;";
        Db::getInstance()->query($sql);
        $sql2 = "DROP TABLE IF EXISTS " . _DB_PREFIX_ . "ba_importer_config";
        Db::getInstance()->query($sql2);
        $sql3 = "DROP TABLE IF EXISTS " . _DB_PREFIX_ . "ba_cronjobs_importer";
        Db::getInstance()->query($sql3);
        $sql4 = "DROP TABLE IF EXISTS " . _DB_PREFIX_ . "ba_importer_productsinfile";
        Db::getInstance()->query($sql4);
        if (parent::uninstall() == false) {
            return false;
        }
        return true;
    }

    public function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->name = array();
        $tab->class_name = 'AdminBaCronJobs';
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Test Cron Jobs';
        }
        $tab->id_parent = -1;
        $tab->module = $this->name;

        return $tab->add();
    }

    public function uninstallTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminBaCronJobs');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return false;
    }

    public function saveDefaultConfig()
    {
        $db = Db::getInstance();
        $sql = "
            CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "cronjobs` (
                `id_cronjob` int(10) NOT NULL AUTO_INCREMENT,
                `id_module` int(10) DEFAULT NULL,
                `description` text,
                `task` text,
                `hour` int(11) DEFAULT '-1',
                `day` int(11) DEFAULT '-1',
                `month` int(11) DEFAULT '-1',
                `day_of_week` int(11) DEFAULT '-1',
                `updated_at` datetime DEFAULT NULL,
                `one_shot` tinyint(1) NOT NULL DEFAULT '0',
                `active` tinyint(1) DEFAULT '0',
                `id_shop` int(11) DEFAULT '0',
                `id_shop_group` int(11) DEFAULT '0',
                PRIMARY KEY (`id_cronjob`),
                KEY `id_module` (`id_module`)
            );
            CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ba_abandoned_img` (
              `id_img` int(11) NOT NULL,
              `name_img` varchar(255) NOT NULL,
              `id_product` int(11) NOT NULL,
              `id_shop` int(11) NOT NULL,
               UNIQUE KEY `id_img` (`id_img`,`name_img`,`id_product`, `id_shop`)
            );
        ";
        $db->query($sql);
        $sql = 'ALTER TABLE '._DB_PREFIX_.'cronjobs ADD COLUMN IF NOT EXISTS `updated_at` datetime DEFAULT NULL;';
        $db->query($sql);
        // Insert to table cronjobs
        $baseUrl = _PS_BASE_URL_ . __PS_BASE_URI__;
        $task = urlencode($baseUrl . 'modules/ba_importer/autoimport.php?batoken='.$this->cookiekeymodule().'');
        $sql = 'SELECT id_cronjob FROM ' . _DB_PREFIX_ . 'cronjobs WHERE `task` = \''
                . $task . '\' AND `hour` = \'-1\' AND `day` = \'-1\' AND `month` = \'-1\' AND `day_of_week` = \'-1\'';
        $resultCronjob = Db::getInstance()->getValue($sql, false);
        if ($resultCronjob == false) {
            $id_shop = (int) Context::getContext()->shop->id;
            $id_shop_group = (int) Context::getContext()->shop->id_shop_group;

            $query = 'INSERT INTO ' . _DB_PREFIX_ . 'cronjobs
                (`description`, `task`, `hour`, `day`, `month`, `day_of_week`,
                `updated_at`, `active`, `id_shop`, `id_shop_group`)
                VALUES (\'Ba_importer.\', \'' . $task . '\', \'-1\', \'-1\', \'-1\', \'-1\', NULL, TRUE, '
                    . $id_shop . ', ' . $id_shop_group . ')';

            $db->execute($query, false);
        }
        $id_cronjob = Db::getInstance()->Insert_ID();
        Configuration::updateValue('BA_IMPORTER_ID_CRONJOB', $id_cronjob);
        $this->updateWebservice(true);
    }

    public function hookDisplayBackOfficeHeader()
    {
        $token = Tools::getAdminTokenLite('AdminModules');
        $advance = AdminController::$currentIndex;
        $out = '<script>var baimporter_ajax_url = "' . $this->_path . 'ajax.php' . '";</script>';
        $out .= '<script>var baimporter_token = "' . sha1(_COOKIE_KEY_ . 'baimporter') . '";</script>';
        $out .= '<script>var batoken = "' . $this->cookiekeymodule() . '";</script>';
        $out .= '<script>var alert_name_setting = "' . $this->l('Name Setting is required') . '";</script>';
        $out .= '<script>var list_setting_url = "'.$advance.'&token='.$token.'&configure='.$this->name.'";</script>';
        return $out;
    }

    public $html = "";
    public $demo_mode = false;
    public $number_add_product = 20;

    public function getContent()
    {
        $this->selectSettings();
        $this->updateConfigSelect();
        $id_shop = $this->context->shop->id;
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $this->updateWebservice(true);
        $token = Tools::getAdminTokenLite('AdminModules');
        $advance = AdminController::$currentIndex;
        $this->smarty->assign('list_setting_url', $advance.'&token='.$token.'&configure='.$this->name);
        $this->smarty->assign('cronjob_date_now', date('l, Y-m-d H:i'));
        $this->context->controller->addCSS($this->_path . 'views/css/style.css');
        $this->context->controller->addJS($this->_path . 'views/js/style.js');
        $this->context->controller->addJS($this->_path . 'views/js/ajax.js');
        
        $buttonDemoArr = array(
            Tools::getValue('deleteba_importer'),
            Tools::getValue('submitBulkdeleteba_importer'),
            Tools::getValue('submitBulkdeleteimporter_config'),
        );
        if ($this->demo_mode==true) {
            foreach ($buttonDemoArr as $buttonDemo) {
                if ($buttonDemo === '') {
                    Tools::redirectAdmin($advance.'&token='.$token.'&configure='.$this->name.'&demoMode=1');
                }
            }
        }
        $demoMode=0;
        if (Tools::getValue('demoMode') == "1") {
            $demoMode=Tools::getValue('demoMode');
        }
        $this->smarty->assign('demoMode', $demoMode);
        $this->html = $this->display(__FILE__, 'views/templates/admin/hook/demomoderror.tpl');
        if (Tools::isSubmit("deleteCronjob")) {
            $id_cronjob = (int) Tools::getValue('id_cronjob');
            $sqln = 'DELETE FROM '._DB_PREFIX_.'ba_cronjobs_importer';
            $sqln .= ' WHERE id_cronjob = '.(int) $id_cronjob;
            $db->query($sqln);
            $this->smarty->assign('success', $this->l('Successful remove'));
            $this->html .= $this->display(__FILE__, 'views/templates/admin/hook/msg.tpl');
        }
        if (Tools::isSubmit("stopcronjobba_importer")) {
            Configuration::updateGlobalValue('baautoimpor_is_run', 0);
            Configuration::updateGlobalValue('baautoimpor_id_queue', null);
            $m = $this->l('All automatic import settings that are waiting in the Queue are stopped.');
            $m .= $this->l('However, they will still work the next time.');
            $this->smarty->assign('success', $m);
            $this->html .= $this->display(__FILE__, 'views/templates/admin/hook/msg.tpl');
        }
        if (Tools::getValue("msg_success") == 1) {
            $this->smarty->assign('success', $this->l('Successful update'));
            $this->html .= $this->display(__FILE__, 'views/templates/admin/hook/msg.tpl');
        }
        if (Tools::getValue("uploaderror") == 1) {
            $this->html .= "<div class='module_error alert alert-danger'>";
            $this->html .= $this->l = 'Update file error' . "</div>";
        }

        if (Tools::getValue("notexcel") == 1) {
            $this->html .= "<div class='module_error alert alert-danger'>";
            $this->html .= $this->l = 'Invalid File(Excel/CSV)' . "</div>";
        }
        if (Tools::getValue("notzip") == 1) {
            $this->html .= "<div class='module_error alert alert-danger'>";
            $this->html .= $this->l = 'Invalid Images(Zip file)' . "</div>";
        }
        
        
        if (Tools::getValue("notftp") != null) {
            $this->html .= "<div class='module_error alert alert-danger'>";
            $this->html .= $this->l = "Couldn't connect to " . Tools::getValue("notftp")."</div>";
        }
        if (Tools::getValue("notdowload") != null) {
            $this->html .= "<div class='module_error alert alert-danger'>";
            $this->html .= $this->l = "File not found: Please check your path to file "
                         ."(this path alway set from ROOT directory of FTP account)</div>";
        }
        $id_shop = $this->context->shop->id;
        $id_shop_group = $this->context->shop->id_shop_group;
        $id_group = $this->context->shop->id_shop_group;
        Configuration::updateValue('ba_id_shop', $id_shop, false, '', '');
        if (Configuration::get('CONFIGN_CHARACTERS_CSV', null, $id_shop_group, $id_shop) == false) {
            Configuration::updateValue('CONFIGN_CHARACTERS_CSV', ';', false, $id_group, $id_shop);
        }
        if (Configuration::get('CONFIGN_CHARACTERS_CATEGORY', null, $id_shop_group, $id_shop) == false) {
            Configuration::updateValue('CONFIGN_CHARACTERS_CATEGORY', '/', false, $id_group, $id_shop);
        }
        $arr_lang =array();
        $lang = Language::getLanguages();
        foreach ($lang as $value_lang) {
            $arr_lang[$value_lang["id_lang"]] = $value_lang["iso_code"];
        }
        $lang = Language::getLanguages(false);
        foreach ($lang as $value_lang) {
            $arr_lang[$value_lang["id_lang"]] = $value_lang["iso_code"];
        }
        $lang2 = Language::getLanguages();
        $lang2 = Language::getLanguages(false);
        $this->smarty->assign('arr_lang', $arr_lang);
        $this->smarty->assign('lang2', $lang2);
        $this->smarty->assign('multi_lang', Tools::getValue('multi_lang'));
        $characters_csv = Configuration::get('CONFIGN_CHARACTERS_CSV', null, $id_shop_group, $id_shop);
        $this->smarty->assign('characters_csv', $characters_csv);
        $characters_category = Configuration::get('CONFIGN_CHARACTERS_CATEGORY', null, $id_shop_group, $id_shop);
        $this->smarty->assign('characters_category', $characters_category);
        $confign_bc1 = Configuration::get('CONFIGN_IMPORTER_BC1', null, $id_shop_group, $id_shop);

        if ($confign_bc1 == false) {
            $confign_bc1 = '{"select_settings":"1","name_settings":"Setting 1","import_local":"1",'
                           .'"ftp_server":"","ftp_user_name":"","ftp_user_port":"","ftp_user_transfer_mode":"",'
                           .'"ftp_user_pass":"","ftp_link_excel":"","url_excel":"","new_items":"Add",'
                           .'"existing_items":"Update","identify_existing_items":"- None -",'
                           .'"quantity":"new_quantity",'
                           .'"update_categories":"more_categories","update_images":"more_images",'
                           .'"import_items":"All","product_start":"1","product_end":"1000",'
                           .'"characters_csv":",","characters_category":"/","import_header":"0",'
                           .'"multi_lang":"1","combi_quanti":"1","cate_exist":"1",'
                           .'"manu_exist":"1","sup_exist":"1","fea_exist":"0",'
                           .'"identify_existing_items_combi":"Attributes",'
                           .'"baencode":"utf8","product_type":"product_standard",'
                           .'"submitimport":"","tab":"AdminModules"}';
        }
        $arr_confign_bc1 = Tools::jsonDecode($confign_bc1, true);
        if (!empty($arr_confign_bc1)) {
            if (!isset($arr_confign_bc1['quantity'])) {
                $arr_confign_bc1['quantity'] = "new_quantity";
            }
            if (!isset($arr_confign_bc1['update_categories'])) {
                $arr_confign_bc1['update_categories'] = "more_categories";
            }
            if (!isset($arr_confign_bc1['update_images'])) {
                $arr_confign_bc1['update_images'] = "more_images";
            }
            $this->smarty->assign('arr_confign_bc1', $arr_confign_bc1);
        }
        $id_shop = $this->context->shop->id;

        $viewba_importer = Tools::getValue('viewimporter_config');
        $addba_importer = Tools::getValue('addba_importer');
        if ($viewba_importer === false && $addba_importer === false) {
            // since 1.1.0 fix duplicate in 1.1.0
            $this->html .= $this->initList();
        }
        if ($viewba_importer === false && $addba_importer === '') {
            $this->html .= $this->display(__FILE__, 'views/templates/admin/hook/form.tpl');
        }
        if ($viewba_importer !== false && $addba_importer === false) {
            $this->html .= $this->display(__FILE__, 'views/templates/admin/hook/form.tpl');
        }
        $duplicateba_importer = Tools::getValue('duplicateba_importer');
        // since 1.1.0 fix duplicate in 1.6.0.x
        if ($duplicateba_importer === false) {
            $duplicateba_importer = Tools::getValue('duplicateimporter_config');
        }
        if ($viewba_importer === false && $addba_importer === false && $duplicateba_importer === '') {
            $bamodule = AdminController::$currentIndex;
            $token = Tools::getAdminTokenLite('AdminModules');
            $duplicate_id_importer_config = (int) Tools::getValue('id_importer_config');
            $create_table = 'CREATE TABLE IF NOT EXISTS
                ' . _DB_PREFIX_ .'ba_importer_data_'.$duplicate_id_importer_config.'
                (Sample text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL)';
            $db->query($create_table);
            $sql = 'INSERT INTO '._DB_PREFIX_.'ba_importer_config (id_shop, ba_name_setting, ba_name_file,
                import_local, ba_step1, ba_step2, ba_step3, date_add, date_up)
                SELECT id_shop, ba_name_setting, ba_name_file, import_local, ba_step1, ba_step2, ba_step3,
                date_add, date_up
                FROM  '._DB_PREFIX_.'ba_importer_config
                WHERE id_importer_config = ' . (int) $duplicate_id_importer_config;
            $db->query($sql);
            $id_dupli = $db->Insert_ID();
            $sql = 'SELECT id_cronjob FROM '._DB_PREFIX_.'ba_cronjobs_importer WHERE id_importer_config
                = '.(int) $duplicate_id_importer_config.' AND id_shop ='.(int)$id_shop.' AND
                id_shop_group ='.(int)$id_shop_group.'';

            $id_cronjob_dupli = $db->getValue($sql, false);
            if (!empty($id_cronjob_dupli)) {
                $sql = 'INSERT INTO '._DB_PREFIX_.'ba_cronjobs_importer (id_importer_config, ba_name, hour, day,
                    month, day_of_week, update_at, products_imported, id_shop, id_shop_group,
                    CONFIGN_DATA_POST, imported)
                    SELECT '.(int) $id_dupli.', ba_name, hour, day, month,
                    day_of_week, update_at, products_imported, id_shop,
                    id_shop_group, CONFIGN_DATA_POST, imported
                    FROM  '._DB_PREFIX_.'ba_cronjobs_importer
                    WHERE id_cronjob = ' . (int) $id_cronjob_dupli;
                $db->query($sql);
            }
            $sql = 'DROP TABLE IF EXISTS '. _DB_PREFIX_ .'ba_importer_data_'.(int)$id_dupli.'';
            $db->query($sql);
            $sql = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'ba_importer_data_'.(int)$id_dupli.'
                AS SELECT * FROM '._DB_PREFIX_.'ba_importer_data_'.(int) $duplicate_id_importer_config;
            $db->query($sql);
            Tools::redirectAdmin($bamodule.'&token='.$token.'&configure='.$this->name.'&msg_success=1');
        }
        $deleteba_importer = Tools::getValue('deleteimporter_config');
        if ($viewba_importer === false && $addba_importer === false && $deleteba_importer !== false) {
            $bamodule = AdminController::$currentIndex;
            $token = Tools::getAdminTokenLite('AdminModules');
            $delete_id_importer_config = (int) Tools::getValue('id_importer_config');
            $sql = 'DELETE FROM '._DB_PREFIX_.'ba_importer_config WHERE ';
            $sql .= 'id_importer_config='. $delete_id_importer_config;
            $db->query($sql);
            // xoa tu bang ba_cronjobs_importer
            $id_remove = (int) $delete_id_importer_config;
            $sql = 'DELETE FROM '._DB_PREFIX_.'ba_cronjobs_importer WHERE id_importer_config='.$id_remove;
            $db->query($sql);
            $table_name = 'ba_importer_data_'.$id_remove;
            $sql_drop_table = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . $table_name;
            $db->query($sql_drop_table);
            Tools::redirectAdmin($bamodule.'&token='.$token.'&configure='.$this->name.'&msg_success=1');
        }
        // remove All
        if (Tools::isSubmit("submitBulkdeleteimporter_config")) {
            $ids = Tools::getValue('importer_configBox');
            if (!empty($ids)) {
                $bamodule = AdminController::$currentIndex;
                $token = Tools::getAdminTokenLite('AdminModules');
                $delete_ids = implode(',', $ids);
                $sql = 'DELETE FROM '._DB_PREFIX_.'ba_importer_config WHERE ';
                $sql .= 'id_importer_config IN ('. pSQL($delete_ids).')';
                $db->query($sql);
                // xoa tu bang ba_cronjobs_importer
                $id_remove = 'id_importer_config IN ('.pSQL($delete_ids).')';
                $sql = 'DELETE FROM '._DB_PREFIX_.'ba_cronjobs_importer WHERE '.$id_remove;
                $db->query($sql);
                foreach ($ids as $value) {
                    $table_name = 'ba_importer_data_'.$value;
                    $sql_drop_table = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . $table_name;
                    $db->query($sql_drop_table);
                }
                Tools::redirectAdmin($bamodule.'&token='.$token.'&configure='.$this->name.'&msg_success=1');
            }
        }
        if (Tools::isSubmit("cancelAddDb")) {
            $settingchoose = Tools::getValue('id_importer_config');
            if ($settingchoose !== false) {
                $id_importer_configg = '&id_importer_config=' . $settingchoose;
            }
            if ($settingchoose === false) {
                $select = 'SELECT * FROM ' . _DB_PREFIX_ . 'ba_importer_config ORDER BY id_importer_config DESC';
                $a = $db->getRow($select, false);
                $id_importer_configg = '&id_importer_config=' . $a['id_importer_config'];
            }
            $src = $advance . '&token=' . $token
                            . '&configure=ba_importer&viewba_importer'. $id_importer_configg;
                    Tools::redirectAdmin($src);
        }
        $is_sc = Tools::isSubmit("submit_cronjob");
        if (Tools::isSubmit("submitimport")) {
            $get_file_name = '';
            $id_shop = $this->context->shop->id;
            $id_group = $this->context->shop->id_shop_group;
            $characters = Tools::getValue("characters_csv");
            Configuration::updateValue('CONFIGN_CHARACTERS_CSV', $characters, false, $id_group, $id_shop);
            $characters_cat = Tools::getValue("characters_category");
            Configuration::updateValue('CONFIGN_CHARACTERS_CATEGORY', $characters_cat, false, $id_group, $id_shop);
            $arr_post = Tools::jsonEncode($_POST);
            $confign_bc1 = Tools::jsonEncode($_POST);
            
            $settingchoose = Tools::getValue('id_importer_config');
            Configuration::updateValue('get_id_config', $settingchoose);
            $this->smarty->assign('get_id_config', $settingchoose);
            $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
            if ($settingchoose !== false) {
                $a = Tools::getValue('select_settings');
                $confign_bc1 = $_POST;
                $confign_bc1['select_settings'] = $settingchoose;
                $confign_bc1 = Tools::jsonEncode($confign_bc1);
                $im_lo = Tools::getValue('import_local');
                $im_name_file = $this->getNameFileCsvOrExcel($im_lo);
                $update = 'UPDATE ' . _DB_PREFIX_ . 'ba_importer_config SET ';
                $update .= 'ba_name_setting=\''.trim($a).'\', ba_name_file=\''.$im_name_file.'\', ';
                $update .= 'import_local=\''.$im_lo.'\', ba_step1=\''.pSQL($confign_bc1).'\', ';
                $update .= 'date_up=NOW() ';
                $update .= 'WHERE id_importer_config=' . (int) $settingchoose . ' AND id_shop=' . (int) $id_shop;
                $db->query($update);
            }
            if ($settingchoose === false) {
                $name_new = Tools::getValue('select_settings');
                $insert = 'INSERT INTO ' . _DB_PREFIX_ . 'ba_importer_config (id_shop,ba_name_setting,ba_step1) ';
                $insert .= 'VALUES (\''.(int) $id_shop.'\',\''.pSQL(trim($name_new)).'\',\''.pSQL($confign_bc1).'\')';
                $db->query($insert);
                $select = 'SELECT * FROM ' . _DB_PREFIX_ . 'ba_importer_config ORDER BY id_importer_config DESC';
                $a = $db->getRow($select, false);
                $confign_bc1 = $_POST;
                $confign_bc1['select_settings'] = $a['id_importer_config'];
                $confign_bc1 = Tools::jsonEncode($confign_bc1);
                $im_lo = Tools::getValue('import_local');
                $im_name_file = $this->getNameFileCsvOrExcel($im_lo);
                $update = 'UPDATE ' . _DB_PREFIX_ . 'ba_importer_config SET ';
                $update .= 'ba_name_file=\''.$im_name_file.'\', import_local=\''.$im_lo.'\', ';
                $update .= 'ba_step1=\''.pSQL($confign_bc1).'\', date_add=NOW(), ';
                $update .= 'date_up=NOW() ';
                $update .= 'WHERE id_importer_config=' . (int) $a['id_importer_config'] . ' ';
                $update .= ' AND id_shop=' . (int) $id_shop;
                $db->query($update);
                Configuration::updateValue('CONFIG_SELECT_IMPORTER', null, false, '', $id_shop);
            }
            Configuration::updateValue('CONFIGN_IMPORTER_BC1', $confign_bc1, false, '', $id_shop);
            
            $id_importer_configg = Tools::getValue('id_importer_config');
            $table_name = 'ba_importer_data_';
            if ($id_importer_configg !== false) {
                $id_importer_configg = '&id_importer_config=' . $id_importer_configg;
                $table_name .= Tools::getValue('id_importer_config');
            } else {
                $select = 'SELECT * FROM ' . _DB_PREFIX_ . 'ba_importer_config ORDER BY id_importer_config DESC';
                $a = $db->getRow($select, false);
                $id_importer_configg = '&id_importer_config=' . $a['id_importer_config'];
                $table_name .= $a['id_importer_config'];
            }
            
            if (isset($_FILES['img'])) {
                $this->saveFileImageZip($_FILES['img'], $id_importer_configg);
            }
            if (isset($_FILES['exampleFile'])) {
                $this->saveFileImageZip($_FILES['exampleFile'], $id_importer_configg);
            }
            if (isset($_FILES['attachmentsFile'])) {
                $this->saveFileImageZip($_FILES['attachmentsFile'], $id_importer_configg);
            }
            
            if (Tools::getValue("import_local") == 0) {
                $url = Tools::getValue("url_excel");
                $link_exits = $this->urlExists($url);
                if ($link_exits === true) {
                    $post_file = array();
                    $post_file[] = strpos(Tools::strtolower($url), ".csv");
                    $post_file[] = strpos(Tools::strtolower($url), ".xls");
                    $post_file[] = strpos(Tools::strtolower($url), ".xlsx");
                    $ext = 0;
                    foreach ($post_file as $ktfile) {
                        if ($ktfile == true) {
                            $ext = 1;
                        }
                    }
                    if ($ext == 0) {
                        $src = $advance . '&token=' . $token . '&configure=ba_importer&tab_module=others&';
                        $src .= 'module_name=ba_importer&notexcel=1&viewba_importer'. $id_importer_configg;
                        Tools::redirectAdmin($src);
                    }
                    $arr = explode("/", $url);
                    $fileName = trim(end($arr));
                    $saveto = dirname(__FILE__) . '/stories/' . $fileName;
                    $this->getImageFromUrl($url, $saveto);
                    $get_file_name = $fileName;
                } else {
                    $src = $advance . '&token=' . $token . '&configure=ba_importer&tab_module=others&';
                    $src .= 'module_name=ba_importer&notexcel=1&viewba_importer'. $id_importer_configg;
                    Tools::redirectAdmin($src);
                }
            }
            if (Tools::getValue("import_local") == 1) {
                $file = $_FILES["filexls"];
                if ($file['error'] == 0) {
                    move_uploaded_file($file['tmp_name'], dirname(__FILE__) . "/stories/" . $file['name']);
                    $fileName = $file['name'];
                } else {
                    $src = $advance . '&token=' . $token . '&configure=ba_importer&tab_module=others&';
                    $src .= 'module_name=ba_importer&uploaderror=1&viewba_importer'. $id_importer_configg;
                    Tools::redirectAdmin($src);
                }
                if ($_FILES["filexls"]["size"] == 0) {
                    $src = $advance . '&token=' . $token . '&configure=ba_importer&tab_module=others&';
                    $src .= 'module_name=ba_importer&notexcel=1&viewba_importer'. $id_importer_configg;
                    Tools::redirectAdmin($src);
                } else {
                    $post_file = array();
                    $post_file[] = strpos(Tools::strtolower($file["name"]), ".csv");
                    $post_file[] = strpos(Tools::strtolower($file["name"]), ".xls");
                    $post_file[] = strpos(Tools::strtolower($file["name"]), ".xlsx");
                    $ext = 0;
                    foreach ($post_file as $ktfile) {
                        if ($ktfile >0) {
                            $ext = 1;
                        }
                    }
                    if ($ext == 0) {
                        $src = $advance . '&token=' . $token . '&configure=ba_importer&tab_module=others&';
                        $src .= 'module_name=ba_importer&notexcel=1&viewba_importer'. $id_importer_configg;
                        Tools::redirectAdmin($src);
                    }
                }
                $get_file_name = $file['name'];
            }
            if (Tools::getValue("import_local") == 2) {
                $fp = Tools::getValue("ftp_link_excel");
                $arr = explode("/", $fp);
                $fileName = trim(end($arr));
                $post_file = array();
                $post_file[] = strpos(Tools::strtolower($fileName), ".csv");
                $post_file[] = strpos(Tools::strtolower($fileName), ".xls");
                $post_file[] = strpos(Tools::strtolower($fileName), ".xlsx");
                $ext = 0;
                foreach ($post_file as $ktfile) {
                    if ($ktfile >0) {
                        $ext = 1;
                    }
                }
                if ($ext == 0) {
                    $src = $advance . '&token=' . $token . '&configure=ba_importer&tab_module=others&';
                    $src .= 'module_name=ba_importer&notexcel=1&viewba_importer'. $id_importer_configg;
                    Tools::redirectAdmin($src);
                }
                //-- Connection Settings
                $ftp_server = Tools::getValue("ftp_server");
                $ftp_user_name = Tools::getValue("ftp_user_name");
                $ftp_user_pass = Tools::getValue("ftp_user_pass");
                $ftp_user_port = (int) Tools::getValue("ftp_user_port");
                if (empty($ftp_user_port)) {
                    $ftp_user_port = 21;
                }
                $ftp_user_transfer_mode = Tools::getValue("ftp_user_transfer_mode");
                if ($ftp_user_transfer_mode == 'active') {
                    $ftp_user_transfer_mode = false;
                } else {
                    $ftp_user_transfer_mode = true;
                }
                $conn_id = ftp_connect($ftp_server, $ftp_user_port);
                $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
                if ($login_result===true) {
                    ftp_pasv($conn_id, $ftp_user_transfer_mode);
                    $download=ftp_get($conn_id, dirname(__FILE__) . '/stories/' . $fileName, $fp, FTP_BINARY);
                    // try to download $server_file and save to $local_file
                    if ($download === false) {
                        $src = $advance . '&token=' . $token . '&configure=ba_importer&tab_module=others&';
                        $src .= 'module_name=ba_importer&notdowload=1&viewba_importer'. $id_importer_configg;
                        Tools::redirectAdmin($src);
                    }
                    ftp_close($conn_id);
                } else {
                    $src = $advance . '&token=' . $token
                            . '&configure=ba_importer&tab_module=others&module_name=ba_importer&notftp='
                            .Tools::getValue("ftp_server").'&viewba_importer'. $id_importer_configg;
                    Tools::redirectAdmin($src);
                }
                $get_file_name = $fileName;
            }
            $this->saveDataCsvToDatabase($get_file_name, $table_name);
            $this->html = $this->import($arr_post, $fileName);
        } elseif (Tools::isSubmit("cancelimport")) {
            $bamodule = AdminController::$currentIndex;
            $token = Tools::getAdminTokenLite('AdminModules');
            Tools::redirectAdmin($bamodule.'&token='.$token.'&configure='.$this->name.'');
        } elseif (Tools::isSubmit("back_bc2")) {
            $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
            $confign_bc1 = Configuration::get('CONFIGN_IMPORTER_BC1', null, $id_shop_group, $id_shop);
            $get_id_config = Configuration::get('get_id_config');
            if ($get_id_config != null) {
                $sql_get_name_file = 'SELECT ba_name_file FROM ' . _DB_PREFIX_ . 'ba_importer_config ';
                $sql_get_name_file .= 'WHERE id_importer_config = ' . (int) $get_id_config;
                $get_name_file = $db->ExecuteS($sql_get_name_file, true, false);
                $arr_link1 = explode("/", $get_name_file['0']['ba_name_file']);
                $fileName = trim(end($arr_link1));
            }
            if ($get_id_config == null) {
                $select = 'SELECT ba_name_file FROM ' . _DB_PREFIX_ . 'ba_importer_config ';
                $select .= 'ORDER BY id_importer_config DESC';
                $get_name_file = $db->getRow($select, false);
                $arr_link1 = explode("/", $get_name_file['ba_name_file']);
                $fileName = trim(end($arr_link1));
            }
            if ($confign_bc1 != false) {
                $arr_confign_bc1 = Tools::jsonDecode($confign_bc1, true);
                if (!empty($arr_confign_bc1)) {
                    $url = $arr_confign_bc1["url_excel"];
                    $link_exits = $this->urlExists($url);
                    if ($link_exits === true) {
                        $post_file = array();
                        $post_file[] = strpos(Tools::strtolower($url), ".csv");
                        $post_file[] = strpos(Tools::strtolower($url), ".xls");
                        $post_file[] = strpos(Tools::strtolower($url), ".xlsx");
                        $ext = 0;
                        foreach ($post_file as $ktfile) {
                            if ($ktfile == true) {
                                $ext = 1;
                            }
                        }
                        if ($ext == 0) {
                            $src = $advance . '&token=' . $token . '&configure=ba_importer&tab_module=others';
                            $src .= '&module_name=ba_importer&notexcel=1&viewba_importer';
                            Tools::redirectAdmin($src);
                        }
                        $arr_link = explode("/", $url);
                        $fileName = trim(end($arr_link));
                        $saveto = dirname(__FILE__) . '/stories/' . $fileName;
                        $this->getImageFromUrl($url, $saveto);
                    }
                    $this->html = $this->import($confign_bc1, $fileName);
                }
            }
        } elseif (Tools::isSubmit("btnNextStep") || Tools::isSubmit("submit_reminder") || $is_sc) {
            Configuration::updateValue('CONFIGN_AUTO_IMPORT', 1);
            $this->html = "";
            $submit_reminder = 0;
            $submit_cronjob = 0;
            $id_shop = $this->context->shop->id;
            $id_shop_group = $this->context->shop->id_shop_group;
            if (Tools::isSubmit("btnNextStep")) {
                $select_column = Tools::getValue("select");
                $array_config = array();
                $array_config = Tools::jsonEncode($select_column);
                $id_shop_group = $this->context->shop->id_shop_group;
                Configuration::updateValue('CONFIG_SELECT_IMPORTER', $array_config, false, $id_shop_group, $id_shop);
                Configuration::updateValue('baautoimpor_is_run', '0', false, $id_shop_group, $id_shop);
                
                $id_shop = (int) Context::getContext()->shop->id;
                $settingchoose = Tools::getValue('get_id_config');
                $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
                if ($settingchoose !== "") {
                    $update = 'UPDATE ' . _DB_PREFIX_ . 'ba_importer_config SET ';
                    $update .= 'ba_step2=\''.pSQL($array_config).'\', ';
                    $update .= 'date_up=NOW() ';
                    $update .= 'WHERE id_importer_config=' . (int) $settingchoose . ' ';
                    $update .= 'AND id_shop=' . (int) $id_shop;
                    $db->query($update);
                }
                if ($settingchoose === "") {
                    $select = 'SELECT * FROM ' . _DB_PREFIX_ . 'ba_importer_config ORDER BY id_importer_config DESC';
                    $a = $db->getRow($select, false);
                    $update = 'UPDATE ' . _DB_PREFIX_ . 'ba_importer_config SET ';
                    $update .= 'ba_step2=\''.pSQL($array_config).'\', ';
                    $update .= 'date_up=NOW() ';
                    $update .= 'WHERE id_importer_config=' . (int) $a['id_importer_config'];
                    $db->query($update);
                    $select = 'SELECT * FROM ' . _DB_PREFIX_ . 'ba_importer_config ORDER BY id_importer_config DESC';
                    $b = $db->getRow($select, false);
                    Configuration::updateValue('CONFIG_SELECT_IMPORTER', $b['ba_step2']);
                }
                
                $data_post = Tools::jsonEncode($_POST);
                Configuration::updateValue('CONFIGN_DATA_POST', $data_post, false, $id_shop_group, $id_shop);
                $config_auto_cronjob = ('* * * * * php ' . _PS_MODULE_DIR_ . $this->name . '/autoimport.php?batoken='
                                        .$this->cookiekeymodule().'  >> '
                                        . _PS_MODULE_DIR_ . $this->name . '/cronjob/log_cronjob.txt 2>&1');
                $this->smarty->assign('config_auto_cronjob', $config_auto_cronjob);
            }
            if (Tools::isSubmit("submit_reminder")) {
                $config_auto_cronjob = ('* * * * * php ' . _PS_MODULE_DIR_ . $this->name . '/autoimport.php?batoken='
                                        .$this->cookiekeymodule().'  >> '
                                        . _PS_MODULE_DIR_ . $this->name . '/cronjob/log_cronjob.txt 2>&1');
                $this->smarty->assign('config_auto_cronjob', $config_auto_cronjob);
                if ($this->demo_mode === true) {
                    $this->html .= "<div class='alert alert-danger'>" . $this->l = 'You are use' . " <strong>"
                            . $this->l = 'Demo Mode' . "</strong>, "
                            . $this->l = 'so some buttons, functions will be disabled because of security.' . "  <br />"
                            . $this->l = 'You can use them in Live mode after you puchase our module.' . " <br />"
                            . $this->l = 'Thanks !' . "</div>";
                } else {
                    $this->configncronjob();
                    $submit_reminder = 1;
                }
            }
            if (Tools::isSubmit("submit_cronjob")) {
                $config_auto_cronjob = ('* * * * * php ' . _PS_MODULE_DIR_ . $this->name . '/autoimport.php?batoken='
                                        .$this->cookiekeymodule().'  >> '
                                        . _PS_MODULE_DIR_ . $this->name . '/cronjob/log_cronjob.txt 2>&1');
                $this->smarty->assign('config_auto_cronjob', $config_auto_cronjob);
                Configuration::updateValue('submit_cronjob', '1');
                $submit_cronjob = 1;
            }
            if (Configuration::get('submit_cronjob') != 1) {
                $ba_url = Tools::getShopProtocol() . Tools::getHttpHost() . __PS_BASE_URI__;
                $id_importer_config = Tools::getValue('id_importer_config');

                $this->html .= "<div class='alert alert-danger'><form method='post' enctype='multipart/form-data' >"
                        . $this->l('You need set up cron job in your hosting with command ') . "<br />
                            <strong>0 * * * * curl \"".$ba_url."modules/".$this->name."/autoimport.php?batoken="
                            .$this->cookiekeymodule()."\"</strong><br />
                            ".$this->l('Or you can set cron job for the specific setting you want
                            in your hosting with command')."<br />
                            <strong>0 * * * * curl \"".$ba_url."modules/".$this->name."/autoimport.php?batoken="
                            .$this->cookiekeymodule()."&id_importer_config=".$id_importer_config."\"</strong><br />
                            <button type='submit' class='btn btn-default' name='submit_cronjob' value='1'>"
                        . $this->l('Yes, I did') . "</button></form></div>";
            }
            $config_cronjob = Configuration::get('CONFIGN_CRONJOB', false, $id_shop_group, $id_shop);
            $arr_config_cronjob = array("hour" => "-1", "day" => "-1", "month" => "-1", "day_of_week" => "-1");
            if ($config_cronjob != false) {
                $arr_config_cronjob = Tools::jsonDecode($config_cronjob, true);
            }
            $this->smarty->assign('link_referer', $_SERVER["HTTP_REFERER"]);
            $this->smarty->assign('arr_config_cronjob', $arr_config_cronjob);

            if ($submit_reminder == 1 || $submit_cronjob == 1) {
                $this->html .= "<div class='alert alert-success'>";
                $this->html .= '<button type="button" class="close" data-dismiss="alert">×</button>';
                $this->html .= $this->l('Update successfull') . "</div>";
            }
            $this->html .= $this->display(__FILE__, 'views/templates/admin/hook/configncronjob.tpl');
        }

        return $this->html;
    }

    public function configncronjob()
    {
        $id_shop = (int) $this->context->shop->id;
        $id_shop_group = $this->context->shop->id_shop_group;
        $post = $_POST;
        $arr = Tools::jsonEncode($post);
        Configuration::updateValue('CONFIGN_CRONJOB', $arr, false, $id_shop_group, $id_shop);
        Configuration::updateValue('baautoimpor_is_run', '0', false, $id_shop_group, $id_shop);
        
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $a = Configuration::get('CONFIGN_IMPORTER_BC1', null, $id_shop_group, $id_shop);
        $b = Tools::jsonDecode($a);
        $update = 'UPDATE ' . _DB_PREFIX_ . 'ba_importer_config ';
        $update .= 'SET ba_step3=\''.pSQL($arr).'\', date_up=NOW() ';
        $update .= 'WHERE id_importer_config=' . (int) $b->select_settings . ' AND id_shop=' . (int) $id_shop;
        $db->query($update);
        
        $baseUrl = _PS_BASE_URL_ . __PS_BASE_URI__;
        $task = urlencode($baseUrl . 'modules/ba_importer/autoimport.php?batoken='.$this->cookiekeymodule().'');
        $sql = 'SELECT id_cronjob FROM ' . _DB_PREFIX_ . 'cronjobs WHERE `task` = \''
                . $task . '\'';
        $id_cronjob = Db::getInstance()->getValue($sql, false);
        if ($id_cronjob != false) {
            $id_shop = (int) Context::getContext()->shop->id;
            $id_shop_group = (int) Context::getContext()->shop->id_shop_group;
            $query = 'REPLACE INTO ' . _DB_PREFIX_ . 'cronjobs
                (`id_cronjob`, `description`, `task`, `hour`, `day`, `month`, `day_of_week`,
                `updated_at`, `active`, `id_shop`, `id_shop_group`)
                VALUES (\'' . $id_cronjob . '\', \'Ba_importer.\', \'' . $task . '\', \''
                    . $post["hour"] . '\', \'' . $post["day"] . '\', \'' . $post["month"] . '\', \''
                    . $post["day_of_week"] . '\', NULL, TRUE, '
                    . (int) $id_shop . ', ' . (int) $id_shop_group . ')';
            Db::getInstance()->execute($query, false);
        }
        $id_shop = (int) Context::getContext()->shop->id;
        $id_shop_group = (int) Context::getContext()->shop->id_shop_group;
        $sql2 = 'SELECT id_cronjob FROM ' . _DB_PREFIX_ . 'ba_cronjobs_importer ';
        $sql2 .= 'WHERE `id_importer_config` = \''. $b->select_settings . '\'';
        $id_cronjob2 = Db::getInstance()->getValue($sql2, false);
        if ($id_cronjob2 == false) {
            $query2 = 'INSERT INTO ' . _DB_PREFIX_ . 'ba_cronjobs_importer
                (`id_importer_config`, `ba_name`, `hour`, `day`, `month`, `day_of_week`,
                `id_shop`, `id_shop_group`, `CONFIGN_DATA_POST`, `imported`)
                VALUES (\'' . $b->select_settings . '\', \'Ba_importer\', \''
                    . $post["hour"] . '\', \'' . $post["day"] . '\', \'' . $post["month"] . '\', \''
                    . $post["day_of_week"] . '\', '
                    . (int) $id_shop . ', ' . (int) $id_shop_group . ', \''
                    . pSQL(Configuration::get('CONFIGN_DATA_POST', null, '', $id_shop)) . '\', 0)';
            $a = Db::getInstance()->execute($query2, false);
        }
        if ($id_cronjob2 != false) {
            $query2 = 'REPLACE INTO ' . _DB_PREFIX_ . 'ba_cronjobs_importer
                (`id_cronjob`, `id_importer_config`, `ba_name`, `hour`, `day`, `month`, `day_of_week`,
                `id_shop`, `id_shop_group`, `CONFIGN_DATA_POST`, `imported`)
                VALUES (\'' . $id_cronjob2 . '\', \'' . $b->select_settings . '\', \'Ba_importer\', \''
                    . $post["hour"] . '\', \'' . $post["day"] . '\', \'' . $post["month"] . '\', \''
                    . $post["day_of_week"] . '\', '
                    . (int) $id_shop . ', ' . (int) $id_shop_group . ', \''
                    . pSQL(Configuration::get('CONFIGN_DATA_POST', null, '', $id_shop)) . '\', 0)';
            $a = Db::getInstance()->execute($query2, false);
        }
    }
    public function import($arr, $fileName)
    {
        $db = Db::getInstance();
        $this->html = '';
        $array = $this->readFileXls($fileName);
        $row_excel = count($array);
        $so_hang = $row_excel - 1;
        $arr_post = Tools::jsonDecode($arr, true);
        $start=2;
        if (Tools::getValue("import_header") == 1) {
            $start = 1;
        }
        $product_start = (int) @$arr_post["product_start"];
        if ($arr_post["import_items"] == "Range" && $product_start>0 && $start<$product_start) {
            $start = $product_start;
        }
        $product_end_range = (int) $arr_post["product_end"];
        if ($arr_post["import_items"] == "Range" && $product_end_range>0 && $product_end_range<$row_excel) {
            $row_excel = $product_end_range;
        }
        //
        $so_hang = $row_excel-$start+1;
        $iso = Language::getIsoIds();
        $isoLang = array();
        foreach ($iso as $value) {
            $isoLang[] = $value["iso_code"];
        }
        $isoLang = implode(",", $isoLang);
        $this->smarty->assign('isoLang', $isoLang);
        $iso = Currency::getCurrencies();
        $isoCur = array();
        foreach ($iso as $value) {
            $isoCur[] = $value["iso_code"];
        }
        $isoCur = implode(", ", $isoCur);
        $this->smarty->assign('isoCur', $isoCur);
        // feature
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'feature_lang GROUP BY id_feature';
        $advance_feature = $db->executeS($sql, true, false);
        $this->smarty->assign('advance_feature', $advance_feature);
        // Combination
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'attribute_group_lang WHERE id_lang = ' . $this->context->language->id;
        $ba_combination = $db->executeS($sql, true, false);
        $this->smarty->assign('ba_combination', $ba_combination);
        // Warehouses
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'warehouse';
        $ba_warehouse = $db->executeS($sql, true, false);
        $this->smarty->assign('ba_warehouse', $ba_warehouse);
        // CONFIG_SELECT_IMPORTER
        $id_shop = $this->context->shop->id;
        $id_shop_group = $this->context->shop->id_shop_group;
        $config_select_importer = Configuration::get('CONFIG_SELECT_IMPORTER', null, $id_shop_group, $id_shop);
        if ($config_select_importer != false) {
            $arr_config_select_importer = Tools::jsonDecode($config_select_importer, true);
        }
        
        $config_importer_bc1 = Configuration::get('CONFIGN_IMPORTER_BC1', null, $id_shop_group, $id_shop);
        if ($config_importer_bc1 != false) {
            $arr_config_importer_bc1 = Tools::jsonDecode($config_importer_bc1, true);
        }
        $this->smarty->assign('multi_lang', $arr_config_importer_bc1['multi_lang']);
        
        $mapping_select = "";
        $since170 = $this->since170();
        $this->smarty->assign('since170', $since170);
        foreach ($array[1] as $key => $header) {
            $mapping_select .="<div class='form-group'>";
            $this->smarty->assign('config_select_importer', @$arr_config_select_importer[$key]);
            $this->smarty->assign('key', $key);
            $select = $this->display(__FILE__, 'views/templates/admin/hook/select.tpl');
            if (Tools::getValue("import_header") == 1) {
                $mapping_select .="<label class='control-label advance-label'> Column "
                        . $key . "</label>" . $select . "";
            } else {
                $mapping_select .="<label class='control-label advance-label'>" . $header . "</label>" . $select . "";
            }

            $mapping_select .="</div>";
        }
        $this->smarty->assign('mapping_select', $mapping_select);
        $this->smarty->assign('base_uri', __PS_BASE_URI__);
        $this->smarty->assign('product_start_import', $start);
        $this->smarty->assign('ba_arr', $arr);
        $array_check = (array) Tools::jsonDecode($arr);
        $this->smarty->assign('identify_existing_items', $array_check["identify_existing_items"]);
        $this->smarty->assign('identify_existing_items_combi', $array_check["identify_existing_items_combi"]);
        $this->smarty->assign('manu_exist', $array_check["manu_exist"]);
        $this->smarty->assign('sup_exist', $array_check["sup_exist"]);
        $this->smarty->assign('fea_exist', $array_check["fea_exist"]);
        $this->smarty->assign('baencode', $array_check["baencode"]);
        $this->smarty->assign('product_type', $array_check["product_type"]);
        $this->smarty->assign('select_settings', $array_check["select_settings"]);
        $settingchoose = Configuration::get('get_id_config');
        $this->smarty->assign('get_id_config', $settingchoose);
        $this->smarty->assign('multi_lang', $array_check["multi_lang"]);
        $this->smarty->assign('import_header', $array_check["import_header"]);
        $this->smarty->assign('import_local', $array_check["import_local"]);
        if ($so_hang < 0) {
            $so_hang = 0;
        }
        $this->smarty->assign('ba_so_hang', $so_hang);
        $this->smarty->assign('ba_demo_mode', $this->demo_mode);
        $this->smarty->assign('ba_file_name', $fileName);
        $tokenProducts = Tools::getAdminTokenLite('AdminProducts');
        $this->smarty->assign('tokenProducts', $tokenProducts);
        $this->smarty->assign('employee_id', $this->context->employee->id);
        $this->smarty->assign('shop_id', $this->context->shop->id);
        $this->smarty->assign('shop_id_group', $this->context->shop->id_shop_group);
        $this->html = $this->display(__FILE__, 'views/templates/admin/hook/mapping.tpl');
        return $this->html;
    }
    
    public function readFileXls($filename, $delimiter = null)
    {
        if (defined("JPATH_COMPONENT") == false) {
            define("JPATH_COMPONENT", dirname(__FILE__));
        }
        if (defined("DS") == false) {
            define("DS", DIRECTORY_SEPARATOR);
        }
        @ini_set("auto_detect_line_endings", true);
        $path_xls = JPATH_COMPONENT . DS . 'stories';
        $path_parts = pathinfo($path_xls . DS . $filename);
        $ext = Tools::strtolower($path_parts['extension']);
        $baencode = Tools::getValue('baencode');
        if ($ext == 'xlsx') {
            require_once JPATH_COMPONENT . DS . 'libs' . DS . 'simplexlsx.class.php';
            $datas = new SimpleXLSX($path_xls . DS . $filename);
            $arrs = $datas->rows();
        } elseif ($ext == 'xls') {
            require_once JPATH_COMPONENT . DS . 'libs' . DS . 'reader.php';
            $datas = new Spreadsheet_Excel_Reader();
            if ($baencode == 'ansi') {
                $datas->setOutputEncoding('CP1252');
            } else {
                $datas->setOutputEncoding('UTF-8');
            }
            $datas->read($path_xls . DS . $filename);
            $arrs = $datas->sheets[0]['cells'];
        } elseif ($ext == 'csv') {
            $src = $path_xls . DS . $filename;
            $arrs = array();
            $row = 0;
            if (($handle = fopen($src, "r")) !== false) {
                $id_shop = $this->context->shop->id;
                $id_shop_group = $this->context->shop->id_shop_group;
                $characters = Configuration::get('CONFIGN_CHARACTERS_CSV', null, $id_shop_group, $id_shop);
                if ($characters == 't') {
                    $characters=chr(9);
                }
                if (!empty($delimiter)) {
                    $characters = $delimiter;
                }
                while (($data = fgetcsv($handle, 10000, $characters)) !== false) {
                    if ($baencode == 'utf8') {
                        $data = array_map("bautf8encode", $data);
                    }
                    $arrs[$row] = $data;
                    $row++;
                }
                fclose($handle);
            }
        }
        $n = count($arrs);
        $arrs_new = array();
        $index = 1;
        if ($ext != 'xls') {
            for ($i = 0; $i < $n; $i++) {
                $e_new = array();
                foreach ($arrs[$i] as $key => $value) {
                    $e_new[$key + 1] = @$value;
                }
                $arrs_new[$index] = $e_new;
                $index++;
            }
        } else {
            for ($i = 1; $i <= $n; $i++) {
                $arrs_new[$index] = $arrs[$i];
                $index++;
            }
        }
        return $arrs_new;
    }
    public function getImageFromUrl($url, $saveto)
    {
        $url = trim($url);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        $raw = curl_exec($ch);
        curl_close($ch);
        if (file_exists($saveto)) {
            @unlink($saveto);
        }
        $fp = fopen($saveto, 'x');
        fwrite($fp, $raw);
        fclose($fp);
    }

    public function urlExists($url)
    {
        $ch = @curl_init($url);
        @curl_setopt($ch, CURLOPT_HEADER, true);
        @curl_setopt($ch, CURLOPT_NOBODY, true);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $status = array();
        preg_match('/HTTP\/.* ([0-9]+) .*/', @curl_exec($ch), $status);
        return (@$status[1] == 200 || @$status[1] == 301 || @$status[1] == 302);
    }

    public function updateWebservice($use_webservice)
    {
        $link = new Link();
        $admin_folder = basename(_PS_ADMIN_DIR_);
        $path = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . $admin_folder;
        $cron_url = $path . '/' . $link->getAdminLink('AdminBaCronJobs', false);
        $ba_webservice_id = Configuration::get('CRONJOB_BA_IMPORT_WEBSERVICE_ID');
        $webservice_id = Configuration::get('CRONJOB_BA_IMPORT_WEBSERVICE_ID') ? '/' . $ba_webservice_id : null;
        $data = array(
            'callback' => $link->getModuleLink($this->name, 'callback'),
            'domain' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__,
            'cronjob' => $cron_url . '&token=' . Configuration::getGlobalValue('CRONJOBS_EXECUTION_TOKEN'),
            'cron_token' => Configuration::getGlobalValue('CRONJOBS_EXECUTION_TOKEN'),
            'active' => (bool) $use_webservice
        );
        $context_options = array('http' => array(
                'method' => (is_null($webservice_id) == true) ? 'POST' : 'PUT',
                'content' => http_build_query($data)
        ));
        $context_opt = stream_context_create($context_options);
        $result = Tools::file_get_contents($this->webservice_url . $webservice_id, false, $context_opt);
        if ($result != false) {
            $id_shop = $this->context->shop->id;
            $id_group = $this->context->shop->id_shop_group;
            Configuration::updateValue('CRONJOB_BA_IMPORT_WEBSERVICE_ID', (int) $result, false, $id_group, $id_shop);
        }
    }

    public function sendCallback()
    {
        ignore_user_abort(true);
        ob_start();
        echo 'cronjobs_prestashop';
        header('Connection: close');
        header('Content-Length: ' . ob_get_length());
        ob_end_flush();
        ob_flush();
        flush();
    }
    // import Accessories by Accessories IDs - đầu vào là chuỗi ID ngăn cách bởi dấu ,
    public function updateAccessories($id_product, $accessories_ids)
    {
        if (empty($accessories_ids)) {
            return false;
        }
        $db = Db::getInstance();
        // remove OLD Accessories
        $db->execute('DELETE FROM `'._DB_PREFIX_.'accessory` WHERE `id_product_1` = '.(int)$id_product, false);
        // add New Accessories
        $accessories_ids = array_unique(explode(',', $accessories_ids));
        if (count($accessories_ids)<=0) {
            return false;
        }
        foreach ($accessories_ids as $id_product_2) {
            $db->insert('accessory', array(
                'id_product_1' => (int) $id_product,
                'id_product_2' => (int)$id_product_2
            ));
        }
    }
    // import Accessories by Refs - đầu vào là chuỗi Ref ngăn cách bởi dấu ,
    // trả về 1 chuỗi ID ngăn cách bởi dấu ,
    public function updateAccessoriesbyRef($id_product, $refs)
    {
        $ids=$this->getIDsByReferences($refs);
        $this->updateAccessories($id_product, $ids);
    }
    // đầu vào là 1 chuỗi Refs với dấu , ngăn cách
    public function getIDsByReferences($refs)
    {
        if (empty($refs)) {
            return false;
        }
        $ref_arr= explode(',', $refs);
        $ref_arr= array_map('pSQL', $ref_arr);
        
        $refs = "'".implode("','", $ref_arr)."'";
        $query = new DbQuery();
        $query->select('p.id_product');
        $query->from('product', 'p');
        $query->where('p.reference IN ('.$refs.')');

        $rows=Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query, true, false);
        if (empty($rows)) {
            return false;
        }
        $result=array();
        foreach ($rows as $item) {
            $result[]=$item['id_product'];
        }
        return implode(',', $result);
    }
    public function selectSettings()
    {
        $id_shop = $this->context->shop->id;
        
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $select_import_settings = 'SELECT * FROM ' . _DB_PREFIX_ . 'ba_importer_config WHERE id_shop=' . $id_shop;
        $select_is = $db->ExecuteS($select_import_settings, true, false);
        $this->smarty->assign('select_is', $select_is);
        
        $id_importer_configg = Tools::getValue('id_importer_config');
        if ($id_importer_configg === false) {
            $select_iss = '';
        } else {
            $select_import_settingss = 'SELECT * FROM ' . _DB_PREFIX_ . 'ba_importer_config ';
            $select_import_settingss .= 'WHERE id_importer_config = '.$id_importer_configg.' AND id_shop=' . $id_shop;
            $select_iss = $db->ExecuteS($select_import_settingss, true, false);
            $select_iss = $select_iss['0']['ba_name_setting'];
        }
        $this->smarty->assign('select_iss', $select_iss);
    }
    
    public function initList()
    {
        $this->resetHelperList();
        $this->setCookieFilter();
        return $this->renderHelperList();
    }
    public function getNameByIdImportLocal($id_im_lo)
    {
        $name_im_lo = '';
        if ($id_im_lo == 0) {
            $name_im_lo = $this->l('Remote URL');
        }
        if ($id_im_lo == 1) {
            $name_im_lo = $this->l('Local');
        }
        if ($id_im_lo == 2) {
            $name_im_lo = $this->l('FTP');
        }
        return $name_im_lo;
    }
    public function getStatusAutoImport($status, $row)
    {
        $name_im_lo = '';
        if ($status == 1) {
            return '';
        } elseif ($status == 2) {
            $name_im_lo .= $this->l('Running');
        } elseif ($status == 3) {
            $name_im_lo .= $this->l('Done');
        }
        $products_imported = $row['products_imported'];
        if (!empty($products_imported)) {
            $arr = explode('/', $products_imported);
            $name_im_lo .= $this->l(' (');
            $name_im_lo .= $arr[0];
            $name_im_lo .= $this->l('/');
            $name_im_lo .= $arr[1];
            $name_im_lo .= $this->l(')');
        }
        return $name_im_lo;
    }
    public function getNameFileCsvOrExcel($import_local)
    {
        $name_file = Tools::getValue('url_excel');
        $im_name_file = '';
        if ($import_local == 0) {
            $im_name_file = explode('/', $name_file);
            $a = (int) count($im_name_file) - 1;
            $im_name_file = '/' . $im_name_file[$a];
        }
        if ($import_local == 1) {
            $im_name_file = '--';
        }
        $name_file = Tools::getValue('ftp_link_excel');
        if ($import_local == 2) {
            $im_name_file = $name_file;
        }
        return $im_name_file;
    }
    public function updateConfigSelect()
    {
        $id_shop = $this->context->shop->id;
        $settingchoose = Tools::getValue('id_importer_config');
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        if ($settingchoose !== false) {
            $select_import_settings = 'SELECT * FROM ' . _DB_PREFIX_ . 'ba_importer_config ';
            $select_import_settings .= 'WHERE id_importer_config=' . $settingchoose . ' AND id_shop=' . $id_shop;
            $select_is = $db->ExecuteS($select_import_settings, true, false);
            Configuration::updateValue('CONFIGN_IMPORTER_BC1', $select_is[0]['ba_step1'], false, '', $id_shop);
            Configuration::updateValue('CONFIG_SELECT_IMPORTER', $select_is[0]['ba_step2'], false, '', $id_shop);
            Configuration::updateValue('CONFIGN_CRONJOB', $select_is[0]['ba_step3'], false, '', $id_shop);
        }
    }
    public function replacePrice($price)
    {
        if ($price === null) {
            return null;
        }
        $search = array('$', ' ', ',');
        $price = (float) str_replace($search, '', $price);
        return $price;
    }
    public function calcPricebeforeTax($price, $id_tax_rules_group = null)
    {
        if ($id_tax_rules_group == null) {
            return $price;
        }
        $price = (float) $price;
        $address = Address::initialize();
        $tax_manager = TaxManagerFactory::getManager($address, $id_tax_rules_group);
        $product_tax_calculator = $tax_manager->getTaxCalculator();
        $tax_rate = $product_tax_calculator->getTotalRate();
        if ($tax_rate>0) {
            $price = (float) number_format($price / (1 + $tax_rate / 100), 6, '.', '');
        }
        return $price;
    }
    // import specific price
    public function addSpecificPrices($array_specific, $row, $id_product, $id_attr = null, $id_shop = null)
    {
        $db = Db::getInstance();
        $an_specific = array();
        $sql_specific_select = array();
        $sql_column_specific = array();
        $sql_specific = array();
        $sql_update_set = array();
        $sql_update_where = array();
       
        foreach ($array_specific as $key_specific => $value_specific) {
            if (strpos($key_specific, "id_") === 0) {
                $row[$value_specific] = (int) $row[$value_specific];
            }
            if (strpos($key_specific, "price") === 0) {
                $row[$value_specific] = $this->replacePrice($row[$value_specific]);
            }
            if (strpos($key_specific, "reduction") === 0 && strpos($key_specific, "reduction_") !== 0) {
                $row[$value_specific] = $this->replacePrice($row[$value_specific]);
            }
            if (strpos($key_specific, "reduction") === 0 && strpos($key_specific, "reduction_") === 0) {
                $row[$value_specific] = Tools::strtolower($row[$value_specific]);
            }
            $an_specific[$key_specific] = $row[$value_specific];
        }
        if (isset($an_specific['id_specific_price'])) {
            $id_specific_price = $an_specific['id_specific_price'];
            unset($an_specific['id_specific_price']);
            $db->update("specific_price", $an_specific, $id_specific_price);
            return true;
        }
        if (!isset($an_specific['reduction']) && !isset($an_specific['price'])) {
            // neu ko ton tai so luong giam gia thi bo qua
            return true;
        }
        $an_specific['reduction'] = (float) @$an_specific['reduction'];
        if (isset($an_specific['price'])) {
            $an_specific['price'] = (float) @$an_specific['price'];
        } else {
            $an_specific['price'] = -1;
        }
        
        if ($an_specific['reduction']<0 && $an_specific['price'] <=0) {
            // neu ton tai so luong giam gia ma <0 thi cung bo qua ko import
            return true;
        }
        if (!empty($an_specific)) {
            $an_specific['id_product_attribute'] = (int) $id_attr;
            if (!array_key_exists('from', $an_specific)) {
                $an_specific['from'] = '0000-00-00 00:00:00';
            }
            if (isset($an_specific['from']) && empty($an_specific['from'])) {
                $an_specific['from'] = '0000-00-00 00:00:00';
            }
            if (!array_key_exists('to', $an_specific)) {
                $an_specific['to'] = '0000-00-00 00:00:00';
            }
            if (isset($an_specific['to']) && empty($an_specific['to'])) {
                $an_specific['to'] = '0000-00-00 00:00:00';
            }
            if (isset($an_specific['to']) && $an_specific['to'] != '0000-00-00 00:00:00') {
                // them 23:59:59 to To
                $t = date('H', strtotime($an_specific['to']));
                if ($t == '00') {
                    $an_specific['to'] .= ' 23:59:59';
                }
            }
            if (!array_key_exists('from_quantity', $an_specific)) {
                $an_specific['from_quantity'] = '1';
            }
            if (!array_key_exists('price', $an_specific)) {
                $an_specific['price'] = '-1';
            }
            if (!array_key_exists('reduction', $an_specific)) {
                $an_specific['reduction'] = '0';
            }
            if (!array_key_exists('reduction_type', $an_specific)) {
                $an_specific['reduction_type'] = 'amount';
            }
            if (isset($an_specific['reduction_type']) && ($an_specific['reduction_type'] == 'percentage')) {
                $an_specific['reduction'] = (float) $an_specific['reduction']/100;
            }
            if (!array_key_exists('reduction_tax', $an_specific)) {
                $an_specific['reduction_tax'] = '1';
            }
            if (Tools::version_compare(_PS_VERSION_, '1.6.1.0', '<')) {
                unset($an_specific['reduction_tax']);
                // khong ton tai field nay trong bang specific_price
            }
            foreach ($an_specific as $key_an_specific => $value_an_specific) {
                $value_an_specific = trim($value_an_specific);
                if ($key_an_specific == 'price' || $key_an_specific == 'reduction') {
                    $value_an_specific = number_format($value_an_specific, 6, '.', '');
                }
                $sql_specific_select[] = '`' . $key_an_specific . '`' . ' = \''.$value_an_specific.'\'';
                
                $sql_column_specific[] = '`' . $key_an_specific . '`';
                $sql_specific[] = '\''.$value_an_specific.'\'';
                
                if ($key_an_specific != 'from' && $key_an_specific != 'to') {
                    $sql_update_set[] = '`' . $key_an_specific . '`' . ' = \''.$value_an_specific.'\'';
                } else {
                    $sql_update_where[] = '`' . $key_an_specific . '`' . ' = \''.$value_an_specific.'\'';
                }
            }
            
            $sql_column = implode(', ', $sql_column_specific);
            $sql_where = implode(', ', $sql_specific);
            $sql = 'REPLACE INTO ' . _DB_PREFIX_ . 'specific_price(`id_product`, `id_shop`, '.$sql_column.') '
                . 'VALUES(\''.$id_product.'\', \''.$id_shop.'\', '.$sql_where.')';
            $db->query($sql);
        }
        // delete SpecificPrice if reduction <=0
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'specific_price WHERE reduction<=0 AND id_product = '.(int) $id_product;
        $db->query($sql);

        $sql_specific_price_priority = 'SELECT * FROM ' . _DB_PREFIX_ . 'specific_price_priority '
                                    . 'WHERE id_product=' . $id_product;
        $result_specific_price_priority = $db->ExecuteS($sql_specific_price_priority, true, false);
        if (empty($result_specific_price_priority)) {
            $sql_insert = 'REPLACE INTO ' . _DB_PREFIX_ . 'specific_price_priority(`id_product`, `priority`) '
                        . 'VALUES(\''.$id_product.'\', \'id_shop;id_currency;id_country;id_group\')';
            $db->query($sql_insert);
        }
        Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', '1');
    }
    public $id_product_attr = 0;
    public function setCombination($bie, $a, $row, $id_pro, $b, $qty = 0, $ostock = 0, $deps = 0, $in_q = 0, $re = 0)
    {
        $array_combination_field = $a;
        $a_com = $a;
        $id_product = $id_pro;
        $array_attribute = $b;
        $_quantities = $qty;
        $_out_of_stock = $ostock;
        $_depends_on_stock = $deps;
        $increase_quantity = $in_q;
        $remove_image = $re;
        $id_shop = (int) $this->shop_id;
        $db = Db::getInstance();
        $check_id_product_attribute_exits = false;
        $arr_id_attr = array();
        foreach ($array_attribute as $value) {
            foreach ($value as $id_attribute) {
                $arr_id_attr[] = $id_attribute;
            }
        }
        $sql = "SELECT id_product_attribute FROM "._DB_PREFIX_."product_attribute WHERE id_product = ".$id_product;
        $arr_id_pro_attr = $db->executeS($sql, true, false);
        if (!empty($arr_id_pro_attr)) {
            foreach ($arr_id_pro_attr as $value_id_pro_attr) {
                $sql = "SELECT id_attribute FROM "._DB_PREFIX_."product_attribute_combination "
                    ."WHERE id_product_attribute = ".$value_id_pro_attr["id_product_attribute"];
                $result_arr_id_attr = $db->executeS($sql, true, false);
                if (!empty($result_arr_id_attr)) {
                    $arr_id_attr_select = array();
                    foreach ($result_arr_id_attr as $value_id_attr) {
                        if (in_array($value_id_attr["id_attribute"], $arr_id_attr)) {
                            $arr_id_attr_select[] = "true";
                        } else {
                            $arr_id_attr_select[] = "false";
                        }
                    }
                    if (!in_array("false", $arr_id_attr_select)) {
                        $check_id_product_attribute_exits = true;
                        $id_product_attribute = (int) $value_id_pro_attr["id_product_attribute"];
                        break;
                    }
                }
            }
        }
        $data = array();
        $data_shop = array();
        
        foreach ($array_combination_field as $key => $value) {
            if (strpos($key, "ba_combination") === 0) {
                $key = Tools::substr($key, -(Tools::strlen($key) - 15));
                if (@$row[$value] != null) {
                    $data[$key] = $row[$value];
                    if ($key == "available_date") {
                        $data[$key] = "";
                        if (empty($data[$key])) {
                            $data[$key] = $row[$value];
                        }
                    }
                    if ($key == "wholesale_price") {
                        $data[$key] = $this->replacePrice($row[$value]);
                    }
                    if ($key == "ean13") {
                        $data[$key] = $this->isEan($row[$value]);
                    }
                    if ($key == "upc") {
                        $data[$key] = $this->isEan($row[$value]);
                    }
                    if ($key == "default_on") {
                        $row[$value] = $this->dataStatusNo($row[$value]);
                        $data[$key] = $row[$value];
                        if ($row[$value] == '1') {
                            $this->removeCombinationDefault($id_product);
                        } else {
                            unset($data[$key]);
                        }
                    }
                }
            }
        }
        
        if (isset($a_com['ba_combination_quantity'])) {
            $data['quantity'] = (int) @$_quantities;
        }
        $data["id_product"] = $id_product;
       
        // Tinh toan lai gia truoc thue
        if (!isset($data['price']) && isset($data['price_incl'])) {
            $price_incl = (@$data['price_incl']>0) ? (float) @$data['price_incl']: -abs($data['price_incl']);
            // If a tax is already included in price, withdraw it from price
            $_p = new Product($id_product);
            $id_tax_rules_group_tmp = $_p->id_tax_rules_group;
            $data['price'] = $this->calcPricebeforeTax($price_incl, $id_tax_rules_group_tmp);
        }
        unset($data['price_incl']);
        unset($data['id']);
        $data_arr = $data;
        $data = array();
        // remove empty elements
        foreach ($data_arr as $key => $value) {
            if ($value !== "" && $value !== null) {
                $data[$key] = $value;
            }
        }
        $data_shop = $data;
        $data_shop["id_shop"] = $id_shop;
        unset($data_shop["reference"]);
        unset($data_shop["ean13"]);
        unset($data_shop["upc"]);
        unset($data_shop["quantity"]);
        unset($data_shop["isbn"]);

        if (strpos(_PS_VERSION_, '1.6.0') === 0 || strpos(_PS_VERSION_, '1.5') === 0) {
            unset($data_shop["id_product"]);
        }
        if ($check_id_product_attribute_exits === false) {
            $db->insert("product_attribute", $data);
            $id_product_attribute = (int) $db->Insert_ID();
            
            $data_shop["id_product_attribute"] = $id_product_attribute;
            $this->updateProductAttributeShop($data_shop);
        } else {
            if ($increase_quantity == 1) {
                // combination_quantity
                $sql = "SELECT quantity FROM "._DB_PREFIX_."stock_available WHERE id_product = "
                        .$id_product." AND id_product_attribute = ".$id_product_attribute;
                $shop_group = new ShopGroup((int)Shop::getGroupFromShop($id_shop));
                // if quantities are shared between shops of the group
                if ($shop_group->share_stock) {
                    $sql .= " AND id_shop = 0";
                    $sql .= " AND id_shop_group = ".(int)$shop_group->id;
                } else {
                    $sql .= " AND id_shop = ".$id_shop;
                }
                $_combination_quantity_old = (int) $db->getValue($sql, false);
                $_quantities = (int) ($_quantities + $_combination_quantity_old);
            }
            if (isset($a_com['ba_combination_quantity'])) {
                $data['quantity'] = (int) @$_quantities;
            }
                        
            $where_product_attr = "id_product_attribute = " . $id_product_attribute
                                . " AND id_product = " . $id_product;
            $db->update("product_attribute", $data, $where_product_attr);
            
            $data_shop["id_product_attribute"] = $id_product_attribute;
            if (strpos(_PS_VERSION_, '1.6.0') === 0 || strpos(_PS_VERSION_, '1.5') === 0) {
            } else {
                $data_shop["id_product"] = $id_product;
            }
            $this->updateProductAttributeShop($data_shop);
        }
        foreach ($array_attribute as $value) {
            foreach ($value as $id_attribute) {
                $sql = "REPLACE INTO " . _DB_PREFIX_ . "product_attribute_combination VALUES('"
                        . (int) $id_attribute . "', '" . $id_product_attribute . "')";
                $db->query($sql);
            }
        }
        $this->id_product_attr = $id_product_attribute;
        // Add Combination Image
        if (!empty($array_combination_field["combination_images"])) {
            $id_combination_image = array();
            foreach ($array_combination_field["combination_images"] as $ba_com_img) {
                $name_img = @$row[$ba_com_img];
                @$name_img = explode(',', $name_img);
                foreach (@$name_img as $item_3) {
                    $id_combination_image[] = $this->addImages($item_3, $id_product, $ba_com_img, 0, 1, $remove_image);
                }
            }
        }
        if (!empty($id_combination_image)) {
            foreach ($id_combination_image as $id_image) {
                if ($id_image>0) {
                    if ($remove_image==1) {
                        $db->delete('product_attribute_image', "id_product_attribute = ".$id_product_attribute);
                    }
                    $sql = "REPLACE INTO " . _DB_PREFIX_ . "product_attribute_image VALUES('"
                            . $id_product_attribute . "', '" . (int) $id_image . "')";
                    $db->query($sql);
                }
            }
        }
        
        $data = array(
            'quantity' => @(int) ($_quantities),
            'id_product' => @(int) ($id_product),
            'id_product_attribute' => $id_product_attribute,
            'id_shop' => $id_shop,
            'id_shop_group' => 0,
            'out_of_stock' => (int) $_out_of_stock,
        );
        if ($_depends_on_stock !== null) {
            $data['depends_on_stock'] = (int) ($_depends_on_stock);
        }
        if (!isset($a_com['ba_combination_quantity']) && empty($_quantities)) {
            // lay quantity cu
            $sql = "SELECT quantity FROM "._DB_PREFIX_."stock_available WHERE id_product = "
                    .$id_product." AND id_product_attribute = ".$id_product_attribute;
            $sql .= ' AND id_shop = '.$id_shop;
            $_combination_quantity_old = (int) $db->getValue($sql, false);
            $data['quantity'] = $_combination_quantity_old;
        }
        // since 1.1.2
        $shop_group = new ShopGroup((int)Shop::getGroupFromShop((int)$id_shop));
        if ($shop_group->share_stock) {
            $data['id_shop'] = 0;
            $data['id_shop_group'] = (int)$shop_group->id;
        } else {
            $data['id_shop_group'] = 0;
        }

        $db->insert('stock_available', $data, false, true, Db::REPLACE);
        if (isset($a_com['ba_combination_quantity'])) {
            $sql = "UPDATE "._DB_PREFIX_."stock_available SET quantity = quantity + "
                .$_quantities." WHERE id_product = ". $id_product." AND id_product_attribute = 0";
            $sql .= ' AND id_shop = '.$id_shop;
            $db->query($sql);
            StockAvailable::setQuantity($id_product, $id_product_attribute, $_quantities, $id_shop);
        }
        
        $sql = "SELECT * FROM "._DB_PREFIX_."product_attribute WHERE id_product = ".$id_product;
        $arr_id_pro_attr = $db->executeS($sql, true, false);
        if (!empty($arr_id_pro_attr)) {
            foreach ($arr_id_pro_attr as $key => $value) {
                if ($arr_id_pro_attr[$key]['quantity'] == 0 && $bie["combi_quanti"] == 0) {
                    $zxc = new Combination($arr_id_pro_attr[$key]['id_product_attribute']);
                    $zxc->delete();
                }
            }
        }
        self::updateDefaultAttribute($id_product, $id_shop);
        return $id_product_attribute;
    }
    public function setCombinationBIE($bie, $a_com, $row, $id_pro, $qty = 0, $ostock = 0, $deps = 0, $in_q = 0, $re = 0)
    {
        $array_combination_field = $a_com;
        $id_product = $id_pro;
        $_quantities = $qty;
        $_out_of_stock = $ostock;
        $_depends_on_stock = $deps;
        $increase_quantity = $in_q;
        $remove_image = $re;
        $id_shop = (int) $this->shop_id;
        $db = Db::getInstance();
        $data = array();
        $data_shop = array();
        foreach ($array_combination_field as $key => $value) {
            // 1.6.0.1 have not had Tools::strpos function caused error
            if (strpos($key, "ba_combination") === 0) {
                $key = Tools::substr($key, -(Tools::strlen($key) - 15));
                if (@$row[$value] != null) {
                    $data[$key] = $row[$value];
                    if ($key == "available_date") {
                        $data[$key] = "";
                        if (empty($data[$key])) {
                            $data[$key] = $row[$value];
                        }
                    }
                    if ($key == "wholesale_price") {
                        $data[$key] = $this->replacePrice($row[$value]);
                    }
                    if ($key == "ean13") {
                        $data[$key] = $this->isEan($row[$value]);
                    }
                    if ($key == "upc") {
                        $data[$key] = $this->isEan($row[$value]);
                    }
                    if ($key == "default_on") {
                        $row[$value] = $this->dataStatusNo($row[$value]);
                        $data[$key] = $row[$value];
                        if ($row[$value] == '1') {
                            $this->removeCombinationDefault($id_product);
                        } else {
                            unset($data[$key]);
                        }
                    }
                }
            }
        }
        if (isset($a_com['ba_combination_quantity'])) {
            $data['quantity'] = (int) @$_quantities;
        }
        $ba_combination_id = 0;
        if (isset($a_com['ba_combination_id']) && !empty($data['id'])) {
            $ba_combination_id = (int) $data['id'];
            unset($data['id']);
        }
        $data["id_product"] = $id_product;
        // Tinh toan lai gia truoc thue
        if (!isset($data['price']) && isset($data['price_incl'])) {
            $price_incl = (@$data['price_incl']>0) ? (float) @$data['price_incl']: -abs($data['price_incl']);
            // If a tax is already included in price, withdraw it from price
            $_p = new Product($id_product);
            $id_tax_rules_group_tmp = $_p->id_tax_rules_group;
            $data['price'] = $this->calcPricebeforeTax($price_incl, $id_tax_rules_group_tmp);
        }
        unset($data['price_incl']);

        $data_arr = $data;
        $data = array();
        foreach ($data_arr as $key => $value) {
            if ($value !== "" && $value !== null) {
                $data[$key] = $value;
            }
        }
        $data_shop = $data;
        $data_shop["id_shop"] = $id_shop;
        unset($data_shop["reference"]);
        unset($data_shop["ean13"]);
        unset($data_shop["upc"]);
        unset($data_shop["quantity"]);
        unset($data_shop["isbn"]);
        $sql = 'SELECT id_product_attribute FROM '._DB_PREFIX_.'product_attribute ';
        $sql .= 'WHERE id_product = '.(int) $id_product.' ';
 
        if ($bie["identify_existing_items_combi"] == 'Combi Reference code') {
            if (empty($data_arr["reference"])) {
                return null;
            }
            $sql .= "AND reference = '".pSQL($data_arr["reference"])."'";
        }
        if ($bie["identify_existing_items_combi"] == 'Combi EAN-13 or JAN barcode') {
            if (empty($data_arr["ean13"])) {
                return null;
            }
            $sql .= "AND ean13 = '".pSQL($data_arr["ean13"])."'";
        }
        if ($bie["identify_existing_items_combi"] == 'Combi UPC barcode') {
            if (empty($data_arr["upc"])) {
                return null;
            }
            $sql .= "AND upc = '".pSQL($data_arr["upc"])."'";
        }
        if ($bie["identify_existing_items_combi"] == 'Combination ID (Attribute ID)') {
            if (empty($ba_combination_id)) {
                return null;
            }
            $sql .= "AND id_product_attribute = ".$ba_combination_id;
        }
        $arr_id_pro_attr = $db->executeS($sql, true, false);

        if (strpos(_PS_VERSION_, '1.6.0') === 0 || strpos(_PS_VERSION_, '1.5') === 0) {
            unset($data_shop["id_product"]);
        }
        $array_id_product_attribute = array();
        if (!empty($arr_id_pro_attr)) {
            foreach ($arr_id_pro_attr as $value_arr_id_pro_attr) {
                $id_product_attribute = $value_arr_id_pro_attr['id_product_attribute'];
                if ($increase_quantity == 1) {
                    // combination_quantity
                    $sql = "SELECT quantity FROM "._DB_PREFIX_."stock_available WHERE id_product = "
                            .$id_product." AND id_product_attribute = ".$id_product_attribute;
                    $shop_group = new ShopGroup((int)Shop::getGroupFromShop($id_shop));
                    // if quantities are shared between shops of the group
                    if ($shop_group->share_stock) {
                        $sql .= " AND id_shop = 0";
                        $sql .= " AND id_shop_group = ".(int)$shop_group->id;
                    } else {
                        $sql .= " AND id_shop = ".$id_shop;
                    }
                    $_combination_quantity_old = (int) $db->getValue($sql, false);
                    $_quantities = (int) ($_quantities + $_combination_quantity_old);
                }
                if (isset($a_com['ba_combination_quantity'])) {
                    $data['quantity'] = (int) @$_quantities;
                }
                $data2 = array();
                $data['id_product_attribute'] = (int) $id_product_attribute;
                $data_key = array_keys($data);
                $b = 0;
                foreach ($data as $value_data) {
                    if ($data_key[$b] != 'id_product') {
                        $data2[] = $data_key[$b] . ' = \'' .pSQL($value_data) .'\'';
                    }
                    $b++;
                }
                $sql = 'UPDATE '._DB_PREFIX_.'product_attribute SET ';
                $sql .= implode(", ", $data2);
                $sql .= ' WHERE id_product_attribute = '.(int) $id_product_attribute.' ';
                $sql .= 'AND id_product = '.(int) $id_product.'';
                $db->query($sql);
                
                $data_shop["id_product_attribute"] = $id_product_attribute;
                if (strpos(_PS_VERSION_, '1.6.0') === 0 || strpos(_PS_VERSION_, '1.5') === 0) {
                } else {
                    $data_shop['id_product'] = $id_product;
                }
                $this->updateProductAttributeShop($data_shop);
                $this->id_product_attr = $id_product_attribute;
                // Add Combination Image
                if (!empty($array_combination_field["combination_images"])) {
                    $id_combination_image = array();
                    foreach ($array_combination_field["combination_images"] as $ba_com_img) {
                        $name_img = @$row[$ba_com_img];
                        @$name_img = explode(',', $name_img);
                        foreach (@$name_img as $item_3) {
                            $i3 = $item_3;
                            $ip = $id_product;
                            $id_combination_image[] = $this->addImages($i3, $ip, $ba_com_img, 0, 1, $remove_image);
                        }
                    }
                }
                if (!empty($id_combination_image)) {
                    foreach ($id_combination_image as $id_image) {
                        if ($id_image>0) {
                            if ($remove_image==1) {
                                $db->delete('product_attribute_image', "id_product_attribute = ".$id_product_attribute);
                            }
                            $sql = "REPLACE INTO " . _DB_PREFIX_ . "product_attribute_image VALUES('"
                                    . $id_product_attribute . "', '" . (int) $id_image . "')";
                            $db->query($sql);
                        }
                    }
                }
                $array_id_product_attribute[] = $id_product_attribute;
            }
            $data = array(
                'quantity' => @(int) ($_quantities),
                'id_product' => @(int) ($id_product),
                'id_product_attribute' => $id_product_attribute,
                'id_shop' => $id_shop,
                'id_shop_group' => 0,
                'out_of_stock' => (int) $_out_of_stock,
            );
            if ($_depends_on_stock !== null) {
                $data['depends_on_stock'] = (int) ($_depends_on_stock);
            }
            if (!isset($a_com['ba_combination_quantity']) && empty($_quantities)) {
                // lay quantity cu
                $sql = "SELECT quantity FROM "._DB_PREFIX_."stock_available WHERE id_product = "
                        .$id_product." AND id_product_attribute = ".$id_product_attribute;
                $_combination_quantity_old = (int) $db->getValue($sql, false);
                $data['quantity'] = $_combination_quantity_old;
            }
            // since 1.1.2
            $shop_group = new ShopGroup((int)Shop::getGroupFromShop((int)$id_shop));
            if ($shop_group->share_stock) {
                $data['id_shop'] = 0;
                $data['id_shop_group'] = (int)$shop_group->id;
            } else {
                $data['id_shop_group'] = 0;
            }
            $db->insert('stock_available', $data, false, true, Db::REPLACE);
            
            if (isset($a_com['ba_combination_quantity'])) {
                $sql = "UPDATE "._DB_PREFIX_."stock_available SET quantity = quantity + "
                    .$_quantities." WHERE id_product = ". $id_product." AND id_product_attribute = 0";
                $sql .= ' AND id_shop = '.$id_shop;
                $db->query($sql);
                StockAvailable::setQuantity($id_product, $id_product_attribute, $_quantities, $id_shop);
            }
            $sql = "SELECT * FROM "._DB_PREFIX_."product_attribute WHERE id_product = ".$id_product;
            $arr_id_pro_attr = $db->executeS($sql, true, false);
            if (!empty($arr_id_pro_attr)) {
                foreach ($arr_id_pro_attr as $key => $value) {
                    if ($arr_id_pro_attr[$key]['quantity'] == 0 && $bie["combi_quanti"] == 0) {
                        $co = new Combination($arr_id_pro_attr[$key]['id_product_attribute']);
                        $co->delete();
                    }
                }
            }
        }
        if (empty($array_id_product_attribute)) {
            return null;
        }
        self::updateDefaultAttribute($id_product, $id_shop);
        return $array_id_product_attribute;
    }
    public function logImport($arr_log_import, $type, $id_import)
    {
        $outputFlie = _PS_MODULE_DIR_."ba_importer/cronjob/log_".$type."_import_".$id_import.".txt";
        $logMail = $arr_log_import;
        file_put_contents($outputFlie, $logMail, FILE_APPEND);
    }
    public function isEan($ean)
    {
        if ($ean === null) {
            return null;
        }
        $a = preg_match('/^[0-9]{0,13}$/', $ean);
        if ($a == 0) {
            $chars = preg_split('//', $ean, -1, PREG_SPLIT_NO_EMPTY);
            $ean = "";
            foreach ($chars as $value) {
                $a = preg_match('/^[0-9]$/', $value);
                if ($a == 1) {
                    $ean .= $value;
                }
            }
            $n = Tools::strlen($ean);

            if ($n > 13) {
                $ean = Tools::substr($ean, 0, 13);
            }
        }
        return $ean;
    }
    public function validateShowCondition($status)
    {
        if ($status === null) {
            return null;
        }
        $status = Tools::strtolower($status);
        $int_status = 0;
        if ($status == "yes") {
            $int_status = 1;
        }
        if ($status == "y") {
            $int_status = 1;
        }
        if ($status == "1") {
            $int_status = 1;
        }
        return (int) $int_status;
    }
    public function validateOnlineOnly($status)
    {
        if ($status === null) {
            return null;
        }
        $status = Tools::strtolower($status);
        $int_status = 0;
        if ($status == "yes") {
            $int_status = 1;
        }
        if ($status == "y") {
            $int_status = 1;
        }
        if ($status == "1") {
            $int_status = 1;
        }
        return (int) $int_status;
    }
    public function dataStatusNo($status)
    {
        $status = Tools::strtolower($status);
        $int_status = 0;
        if ($status == "yes") {
            $int_status = 1;
        }
        if ($status == "y") {
            $int_status = 1;
        }
        if ($status == "1") {
            $int_status = 1;
        }
        return $int_status;
    }
    public function dataStatusUsable($status)
    {
        $status = Tools::strtolower($status);
        $int_status = true;
        if ($status == "no") {
            $int_status = false;
        }
        if ($status == "n") {
            $int_status = false;
        }
        if ($status == "0") {
            $int_status = false;
        }
        return $int_status;
    }
    public function dataStatus($status)
    {
        $status = Tools::strtolower($status);
        $int_status = 1;
        if ($status == "no") {
            $int_status = 0;
        }
        if ($status == "n") {
            $int_status = 0;
        }
        if ($status == "0") {
            $int_status = 0;
        }
        return $int_status;
    }
    // add features
    public function addFeatures($id_product, $id_lang, $id_feature, $feature_value, $customize = 0)
    {
        $feature_value = trim($feature_value);
        if (empty($feature_value)) {
            return true;
        }
        $id_product = (int) $id_product;
        $id_lang = (int) $id_lang;
        $id_feature = (int) $id_feature;
        $feature_value_arr = explode(";", $feature_value);
        if (empty($feature_value_arr)) {
            return false;
        }
        foreach ($feature_value_arr as $f_value) {
            $f_value = trim($f_value);
            $feature_value = $f_value;
            // kiem xem feature nay da ton tai hay chua?
            $sql = 'SELECT a.id_feature_value FROM ' . _DB_PREFIX_ . 'feature_value AS a INNER JOIN '
                    . _DB_PREFIX_ . 'feature_value_lang AS b ON a.id_feature_value=b.id_feature_value'
                    . " WHERE a.id_feature=$id_feature AND b.value='" .pSQL($feature_value)."'";
            $id_feature_value = (int) Db::getInstance()->getValue($sql, false);
            if (empty($id_feature_value)) {
                // chua ton tai value cho feature nay
                $customize_fea = 1;
                if ($customize == 1) {
                    $customize_fea = 0;
                }
                $data = array(
                    'id_feature' => $id_feature,
                    'custom' => $customize_fea
                );
                Db::getInstance()->insert('feature_value', $data);
                $id_feature_value = Db::getInstance()->Insert_ID();
                $arr_id_lang = array();
                $languagesArr = Language::getLanguages(false);
                foreach ($languagesArr as $v) {
                    $arr_id_lang[]=(int) ($v['id_lang']);
                }
                if (!empty($arr_id_lang)) {
                    foreach ($arr_id_lang as $value_lang) {
                        $id_lang = (int) $value_lang;
                        $data = array(
                            'id_feature_value' => $id_feature_value,
                            'id_lang' => $id_lang,
                            'value' => pSQL($feature_value)
                        );
                        
                        Db::getInstance()->insert('feature_value_lang', $data, false, true, DB::REPLACE);
                    }
                }
                $data = array(
                    'id_feature_value' => $id_feature_value,
                    'id_feature' => $id_feature,
                    'id_product' => $id_product
                );
                Db::getInstance()->insert('feature_product', $data, false, true, Db::REPLACE);
            } else {
                if ($customize == 1) {
                    $sql = "UPDATE " . _DB_PREFIX_ . "feature_value SET custom = 0 "
                        ."WHERE id_feature_value = $id_feature_value";
                    Db::getInstance()->query($sql);
                }
                $sql = "REPLACE INTO " . _DB_PREFIX_ . "feature_product
                        (id_feature,id_product,id_feature_value) VALUES($id_feature,$id_product,$id_feature_value)";
                Db::getInstance()->query($sql);
            }
        }
        return true;
    }
    public function addFeaturesLang($id_product, $id_lang, $feature_column, $row, $customize = 0)
    {
        $languages = Language::getLanguages(false);
        $db = Db::getInstance();
        $features = array();
        static $lang_f = array();
        if (empty($lang_f)) {
            foreach ($languages as $l) {
                $lang_f[$l['iso_code']] = (int) $l['id_lang'];
            }
        }
        foreach ($feature_column as $k => $v) {
            $id_iso = explode('_', $k);
            $id_feature = (int) $id_iso[0];
            $iso = $id_iso[1];
            $id_lang = (int) $lang_f[$iso];
            $text = trim($row[$v]);
            if (!empty($text)) {
                $features[$id_feature][$id_lang] = explode(";", $text);
            }
        }
        // duyet qua tung cot feature
        if (empty($features)) {
            return false;
        }
        foreach ($features as $id_feature => $features_lang) {
            foreach ($features_lang as $id_lang => $f_value_arr) {
                $n = count($f_value_arr);
                for ($i = 0; $i < $n; $i++) {
                    $feature_value = trim($f_value_arr[$i]);
                    if (empty($feature_value)) {
                        continue; // bo qua gia tri nay vi empty
                    }
                    // kiem tra xem feature nay da ton tai chua?
                    $sql = 'SELECT a.id_feature_value FROM ' . _DB_PREFIX_ . 'feature_value AS a INNER JOIN '
                            . _DB_PREFIX_ . 'feature_value_lang AS b ON a.id_feature_value=b.id_feature_value'
                            . " WHERE a.id_feature=$id_feature AND b.value='" . pSQL($feature_value) . "'"
                            . " AND b.id_lang= ".$id_lang
                            . " AND a.custom = 0"
                            . " ORDER BY a.id_feature_value ASC";
                    $id_feature_value = (int) $db->getValue($sql, false);
                    if (!empty($id_feature_value)) {
                        $sql = "REPLACE INTO " . _DB_PREFIX_ . "feature_product";
                        $sql .= " (id_feature,id_product,id_feature_value)";
                        $sql .= " VALUES($id_feature,$id_product,$id_feature_value)";
                        $db->query($sql);
                        continue;
                    }
                    // chua ton tai customization feature, kiem tra xem co customize value khong?
                    $sql = 'SELECT a.id_feature_value FROM ' . _DB_PREFIX_ . 'feature_value AS a INNER JOIN '
                            . _DB_PREFIX_ . 'feature_value_lang AS b ON a.id_feature_value=b.id_feature_value'
                            . " WHERE a.id_feature=$id_feature AND b.value='" . pSQL($feature_value) . "'"
                            . " AND b.id_lang= ".$id_lang
                            . " AND a.custom = 1"
                            . " ORDER BY a.id_feature_value ASC";
                    $id_feature_value = (int) $db->getValue($sql, false);
                    if (!empty($id_feature_value)) {
                        if ($customize == 1) {
                            $sql = "UPDATE " . _DB_PREFIX_ . "feature_value SET custom = 0";
                            $sql .= " WHERE id_feature_value = $id_feature_value";
                            $db->query($sql);
                        }
                        $sql = "REPLACE INTO " . _DB_PREFIX_ . "feature_product";
                        $sql .= " (id_feature,id_product,id_feature_value)";
                        $sql .= " VALUES($id_feature,$id_product,$id_feature_value)";
                        $db->query($sql);
                        continue;
                    }
                    // khong ton tai ca Value va Customize - > tạo mới
                    $custom = 1;
                    if ($customize == 1) {
                        $custom = 0;
                    }
                    $data = array(
                        'id_feature' => $id_feature,
                        'custom' => $custom
                    );
                    Db::getInstance()->insert('feature_value', $data);
                    $id_feature_value = $db->Insert_ID();
                    // duyet qua tung ngon ngu
                    foreach ($languages as $l) {
                        $id_lang2 = (int) $l['id_lang'];
                        $value_lang = $feature_value;
                        // neu co thu tu ung voi vi tri nay thi chen vao
                        if (isset($features_lang[$id_lang2]) && isset($features_lang[$id_lang2][$i])) {
                            if (!empty($features_lang[$id_lang2][$i])) {
                                $value_lang = trim($features_lang[$id_lang2][$i]);
                            }
                        }
                        $data = array(
                            'id_feature_value' => $id_feature_value,
                            'id_lang' => $id_lang2,
                            'value' => pSQL($value_lang)
                        );
                        if (!empty($value_lang)) {
                            $db->insert('feature_value_lang', $data, false, true, DB::REPLACE);
                        }
                    }
                    // assign feature to product
                    $sql = "REPLACE INTO " . _DB_PREFIX_ . "feature_product(id_feature,id_product,id_feature_value) ";
                    $sql .= "VALUES($id_feature,$id_product,$id_feature_value)";
                    $db->query($sql);
                }
            }
        }
        return true;
    }
    // reset cover=0 tat anh cover cua product
    public function removeCoverPhoto($id_product)
    {
        $sql = "UPDATE " . _DB_PREFIX_ . "image SET cover=NULL WHERE id_product = " . (int) $id_product;
        Db::getInstance()->query($sql);
        //lay tat ca cac anh cover
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'image WHERE  id_product=' . (int) $id_product;
        if ($results = Db::getInstance()->ExecuteS($sql, true, false)) {
            foreach ($results as $row) {
                $id_image = $row["id_image"];
                $sql = "UPDATE " . _DB_PREFIX_ . "image_shop SET cover=NULL WHERE id_image = " . (int) $id_image;
                Db::getInstance()->query($sql);
            }
        }
    }
    public function updateDefaultAttribute($id_product, $id_shop = null)
    {
        $id_product_attribute = (int) Product::getDefaultAttribute($id_product);
        if ($id_shop === null) {
            $id_shop = (int) $this->context->shop->id;
        }
        if ($id_product_attribute > 0) {
            if (Tools::version_compare(_PS_VERSION_, '1.6.1.0', '<')) {
                $sql = "UPDATE " . _DB_PREFIX_ . "product_attribute_shop SET default_on=1 ";
                $sql .= " WHERE ";
                $sql .= " id_product_attribute = " . (int) $id_product_attribute;
                $sql .= " AND id_shop = " . (int) $id_shop;
            } else {
                $sql = "UPDATE " . _DB_PREFIX_ . "product_attribute_shop SET default_on=1 ";
                $sql .= " WHERE id_product=".(int) $id_product;
                $sql .= " AND id_product_attribute = " . (int) $id_product_attribute;
                $sql .= " AND id_shop = " . (int) $id_shop;
            }
            Db::getInstance()->query($sql);

            $sql = "UPDATE " . _DB_PREFIX_ . "product_attribute SET default_on=1 ";
            $sql .=" WHERE id_product = " . (int) $id_product;
            $sql .= " AND id_product_attribute = " . (int) $id_product_attribute;
            Db::getInstance()->query($sql);
        }
    }
    public function saveDataCsvToDatabase($file_name, $table_name, $import_header = null)
    {
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $array = $this->readFileXls($file_name);
        if (Tools::getValue('baencode') == 'ansi') {
            $db->query(pSQL("SET NAMES latin1", true));
        }
        $drop_table = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . $table_name;
        $db->query($drop_table);
        
        // create table if not exists
        $array_query = array();
        $create_table = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . $table_name . ' (';
        foreach ($array[1] as $key => $value) {
            $value;
            $array_query[] = '`'.$key.'` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL';
        }
        $create_table .= implode(', ', $array_query);
        $create_table .= ')';
        $db->query($create_table);
        
        // insert data
        $count_column = count($array[1]);
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . $table_name;
        $exists_data = (int) $db->getRow($sql, false);
        if (empty($import_header)) {
            $import_header = Tools::getValue('import_header');
        }
        if ($exists_data == 0) {
            foreach ($array as $key => $value) {
                if ($import_header == 0 && $key == 1) {
                    continue;
                } else {
                    $array_value_row = array();
                    foreach ($array[$key] as $key2 => $value2) {
                        if ($count_column >= $key2) {
                            $array_value_row[$key2] = pSQL($value2, true);
                        }
                    }
                    $db->insert($table_name, $array_value_row);
                }
            }
        }
    }
    public function moveFileDownload($filename_tmp)
    {
        $filename = '';
        $dir = _PS_MODULE_DIR_ . "ba_importer";
        $path_old = $dir . '/images/' . $filename_tmp;
        $http = trim(Tools::strtolower($filename_tmp));

        if (strpos($http, "http://") === 0 || strpos($http, "https://") === 0) {
            $arr = explode("/", $filename_tmp);
            $path_old = $dir . '/images/' . trim(end($arr));
            $this->getImageFromUrl($filename_tmp, $path_old);
        }
        $check_exit = file_exists($path_old);
        if ($check_exit) {
            $filename = ProductDownload::getNewFilename();
            copy($path_old, _PS_DOWNLOAD_DIR_ . $filename);
        }
        return $filename;
    }
    public function saveFileImageZip($img, $id_importer_configg)
    {
        $token = Tools::getAdminTokenLite('AdminModules');
        $advance = AdminController::$currentIndex;
        if ($img['error'] == 0) {
            move_uploaded_file($img['tmp_name'], dirname(__FILE__) . "/images/" . $img['name']);
            $zip = new ZipArchive;
            $a = _PS_MODULE_DIR_ . "ba_importer/images/" . $img['name'];
            $res = $zip->open($a);
            if ($res === true) {
                $zip->extractTo(_PS_MODULE_DIR_ . 'ba_importer/images/');
                $zip->close();
            }
            @unlink($a);
            // move file
            $dir = dirname(__FILE__) . "/images/";
            $scan = scandir($dir);
            unset($scan[0]);
            unset($scan[1]);
            foreach ($scan as $value) {
                $isfile = is_file($dir . $value);
                if ($isfile == false) {
                    $scan_dir = scandir($dir . $value);
                    foreach ($scan_dir as $value1) {
                        $isfiledir = is_file($dir . $value . "/" . $value1);
                        if ($isfiledir == true) {
                            copy($dir . $value . "/" . $value1, $dir . $value1);
                            @unlink($dir . $value . "/" . $value1); // since 1.1.7
                        }
                    }
                    $this->deleteDirectory($dir . $value);// since 1.1.7
                }
            }
        }
        if ($img["size"] != 0) {
            $post_file = strpos(Tools::strtolower($img["name"]), ".zip");
            if ($post_file === false) {
                $src = $advance . '&token=' . $token . '&configure=ba_importer&tab_module=others&';
                $src .= 'module_name=ba_importer&notzip=1&viewba_importer'. $id_importer_configg;
                Tools::redirectAdmin($src);
            }
        }
    }
    public function addBaAttachments($array_attachments, $id_product, $row, $_post)
    {
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $id_default_language = (int) (Configuration::get('PS_LANG_DEFAULT'));
        $arr_lang = array();
        $lang = Language::getLanguages(false);
        foreach ($lang as $value_lang) {
            $arr_lang[$value_lang["id_lang"]] = $value_lang["iso_code"];
        }
        $array_attachments_tmp = array();
        foreach ($array_attachments as $key_attachments => $value_attachments) {
            $array_attachments_tmp[$key_attachments] = explode(',', $row[$value_attachments]);
        }
        if (isset($array_attachments_tmp['nameoffile'])) {
            foreach ($array_attachments_tmp['nameoffile'] as $key_nameoffile => $value_nameoffile) {
                $sql = 'SELECT id_attachment FROM '._DB_PREFIX_.'attachment '
                    . 'WHERE file_name="'.pSQL($value_nameoffile).'"';
                $id_attachment = $db->getValue($sql, false);
                if (!empty($id_attachment)) {
                    $attachment = new Attachment($id_attachment);
                    // remove old row in product_attachment for duplicate key
                    $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'product_attachment`';
                    $sql .= ' WHERE `id_product` = ' . (int) $id_product;
                    $sql .= ' AND `id_attachment` = ' . (int) $id_attachment;
                    Db::getInstance()->execute($sql, false);
                    $attachment->attachProduct($id_product);
                } else {
                    $url_image = _PS_MODULE_DIR_ . "ba_importer/images/".$value_nameoffile;
                    $typemine = mime_content_type($url_image);
                    $attachment_filename = $this->moveFileDownload($value_nameoffile);
                    $attachment = new Attachment();
                    $displayname = '';
                    $description = '';
                    if ($_post["multi_lang"] == "0") {
                        $isodefault = $arr_lang[$id_default_language];
                        $namedefault = '';
                        $descriptiondefault = '';
                        if (isset($array_attachments_tmp['displaynamefile_'.$isodefault][$key_nameoffile])) {
                            $namedefault = $array_attachments_tmp['displaynamefile_'.$isodefault][$key_nameoffile];
                        }
                        if (empty($namedefault)) {
                            $namedefault = $value_nameoffile;
                        }
                        if (isset($array_attachments_tmp['description_'.$isodefault][$key_nameoffile])) {
                            $descriptiondefault = $array_attachments_tmp['description_'.$isodefault][$key_nameoffile];
                        }
                        if (empty($descriptiondefault)) {
                            $descriptiondefault = $value_nameoffile;
                        }
                        foreach ($arr_lang as $key_arr_lang => $value_arr_lang) {
                            if (!empty($array_attachments_tmp['displaynamefile_'.$value_arr_lang][$key_nameoffile])) {
                                $aat = $array_attachments_tmp['displaynamefile_'.$value_arr_lang][$key_nameoffile];
                                $displayname = $aat;
                            } else {
                                $displayname = $namedefault;
                            }
                            if (!empty($array_attachments_tmp['description_'.$value_arr_lang][$key_nameoffile])) {
                                $description = $array_attachments_tmp['description_'.$value_arr_lang][$key_nameoffile];
                            } else {
                                $description = '';
                            }
                            $attachment->name[$key_arr_lang] = Tools::substr($displayname, 0, 32);
                            $attachment->description[$key_arr_lang] = $description;
                        }
                    } else {
                        if (!empty($array_attachments_tmp['displaynamefile'][$key_nameoffile])) {
                            $displayname = $array_attachments_tmp['displaynamefile'][$key_nameoffile];
                        } else {
                            $displayname = $value_nameoffile;
                        }
                        if (!empty($array_attachments_tmp['description'][$key_nameoffile])) {
                            $description = $array_attachments_tmp['description'][$key_nameoffile];
                        } else {
                            $description = '';
                        }
                        foreach ($arr_lang as $key_arr_lang => $value_arr_lang) {
                            $attachment->name[$key_arr_lang] = Tools::substr($displayname, 0, 32);
                            $attachment->description[$key_arr_lang] = $description;
                        }
                    }
                    $attachment->file = $attachment_filename;
                    $attachment->mime = $typemine;
                    $attachment->file_name = $value_nameoffile;
                    $attachment->add();
                    $attachment->attachProduct($id_product);
                }
            }
        }
    }
    
    public function cookiekeymodule()
    {
        $keygooglecookie = sha1(_COOKIE_KEY_ . 'ba_importer');
        $md5file = md5($keygooglecookie);
        return $md5file;
    }
    /** by hatt July 29, 2019 since 1.0.65+ **/
    public function importDeliveryTime($id_product, $id_shop, $deliverytimes, $row, $post)
    {
        $post;
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        if (empty($deliverytimes)) {
            return false;
        }
        if (Tools::version_compare(_PS_VERSION_, '1.7.3.0', '<')) {
            return false;// chi hoat dong tu 1.7.3.0
        }
        $arr = array();
        $id_lang = (int) (Configuration::get('PS_LANG_DEFAULT'));
        $id_product = (int) $id_product;
        foreach ($deliverytimes as $field => $key) {
            $arr = explode('_', $field);
            $field_name = $arr[1];
            if (isset($arr[2])) {
                $id_lang = (int) $arr[2];
            }
            if ($field_name == 'additional') {
                $value = (int) $row[$key];
                if (!in_array($value, array(0, 1, 2))) {
                    $value = 0;
                }
                $sql = 'UPDATE '._DB_PREFIX_.'product';
                $sql .= ' SET additional_delivery_times = '.$value;
                $sql .= ' WHERE id_product = '.$id_product;
                $db->query($sql);
            }
            if ($field_name == 'instock') {
                $value = $row[$key];
                $sql = 'UPDATE '._DB_PREFIX_.'product_lang';
                $sql .= ' SET delivery_in_stock = "'.pSQL($value).'"';
                $sql .= ' WHERE id_product = '.$id_product;
                $sql .= ' AND id_shop = '.(int) $id_shop;
                $sql .= ' AND id_lang = '.(int) $id_lang;
                $db->query($sql);
            }
            if ($field_name == 'outstock') {
                $value = $row[$key];
                $sql = 'UPDATE '._DB_PREFIX_.'product_lang';
                $sql .= ' SET delivery_out_stock = "'.pSQL($value).'"';
                $sql .= ' WHERE id_product = '.$id_product;
                $sql .= ' AND id_shop = '.(int) $id_shop;
                $sql .= ' AND id_lang = '.(int) $id_lang;
                $db->query($sql);
            }
        }
        return true;
    }
    /**** by hatt since Feb 14, 2020 version 1.0.74+ ****/
    public function sincePrestashop170()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            return true;
        }
        return false;
    }
    public function validateRedirection($value)
    {
        if ($value === null) {
            return null;
        }
        $value = Tools::strtolower($value);
        if (Tools::version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {
            switch ($value) {
                case '':
                case ' ':
                    return '';
                case '404':
                    return '404';
                case '301-product':
                    return '301-product';
                case '302-product':
                    return '302-product';
                case '302-category':
                    return '302-category';
                default:
                    return '301-category';
            }
        } else {
            // since 1.7.0.0 -> 1.7.0.6: 301, 302, 404
            switch ($value) {
                case '':
                case ' ':
                    return '';
                case '301-product':
                case '301':
                    return '301';
                case '302-product':
                case '302':
                    return '302';
                default:
                    return '404';
            }
        }
    }
    public function validateIDRedirectedColumn()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {
            return 'id_type_redirected';
        } else {
            // since 1.7.0.0 -> 1.7.0.6: 301, 302, 404
            return 'id_product_redirected';
        }
    }
    public function validateDeleteProduct($value)
    {
        if ($value === null) {
            return null;
        }
        $value = Tools::strtolower($value);
        $int_status = 0;
        if ($value == "yes" || $value == "y" || $value == "1") {
            $int_status = 1;
        }
        return (int) $int_status;
    }
    /** use since 1.0.75 **/
    public function searchByPath($id_lang, $id_shop, $path, $object = false, $method = false, $multi_lang = false)
    {
        $categories = explode('#', trim($path));
        $category = $id_parent = false;
        if ($multi_lang == true) {
            // Find Category Tree in all language
            $languages = Language::getLanguages(false, (int) $id_shop);
            if (is_array($languages) && count($languages)) {
                foreach ($languages as $lang) {
                    $id_lang_2 = (int) $lang['id_lang'];
                    if (is_array($categories) && count($categories)) {
                        $category = $id_parent = false;
                        $found = true;
                        foreach ($categories as $category_name) {
                            if ($category_name == '') {
                                $id_root_category = (int) Configuration::get('PS_HOME_CATEGORY', null, null, $id_shop);
                                $category = (array) new Category($id_root_category, $id_lang_2, $id_shop);
                            } elseif ($id_parent) {
                                $il = $id_lang_2;
                                $this->disableCache();
                                $category = Category::searchByNameAndParentCategoryId($il, $category_name, $id_parent);
                            } else {
                                $category = Category::searchByName($id_lang_2, $category_name, true, true);
                            }
                            // 1 Category khong ton tai, stop
                            if (isset($category['id_category']) && $category['id_category']) {
                                $id_parent = (int)$category['id_category'];
                                $this->addShop($category['id_category'], $id_shop);
                            } else {
                                $found = false;
                                break 1;
                            }
                        }
                        if ($found == true && isset($category['id_category']) && $category['id_category']) {
                            $this->enableCache();
                            return $category;
                        }
                    }
                }
            }
        }
        // not found, find category based on id_lang and create it
        $category = $id_parent = false;
        if (is_array($categories) && count($categories)) {
            foreach ($categories as $category_name) {
                $category_name = $this->validCategoryName($category_name);
                if ($category_name == '') {
                    $id_root_category = (int) Configuration::get('PS_HOME_CATEGORY', null, null, $id_shop);
                    $category = (array) new Category($id_root_category, $id_lang, $id_shop);
                } elseif ($id_parent) {
                    $this->disableCache();
                    $category = Category::searchByNameAndParentCategoryId($id_lang, $category_name, $id_parent);
                } else {
                    $category = Category::searchByName($id_lang, $category_name, true, true);
                }
                if (!$category && $object && $method) {
                    call_user_func_array(array($object, $method), array($id_lang, $category_name , $id_parent));
                    if ($id_parent) {
                        $this->disableCache();
                        $category = Category::searchByNameAndParentCategoryId($id_lang, $category_name, $id_parent);
                    } else {
                        $category = Category::searchByName($id_lang, $category_name, true, true);
                    }
                }
                if (isset($category['id_category']) && $category['id_category']) {
                    $id_parent = (int)$category['id_category'];
                    $this->addShop($category['id_category'], $id_shop);
                }
            }
        }
        $this->enableCache();
        return $category;
    }
    public function addShop($id_category, $id_shop)
    {
        if (!$this->existsInShop($id_category, $id_shop)) {
            $data = array(
                'id_category' => (int)$id_category,
                'id_shop' => (int)$id_shop,
            );
            return Db::getInstance()->insert('category_shop', $data);
        }
        return true;
    }
    public function existsInShop($id_category, $id_shop)
    {
        return (bool) Db::getInstance()->getValue('
        SELECT `id_category`
        FROM `'._DB_PREFIX_.'category_shop`
        WHERE `id_category` = '.(int) $id_category.'
        AND `id_shop` = '.(int)$id_shop);
    }
    public function createCat($default_language_id, $category_name, $id_parent_category = null)
    {
        $category_to_create = new Category();
        $shop_is_feature_active = Shop::isFeatureActive();
        if (!$shop_is_feature_active) {
            $category_to_create->id_shop_default = 1;
        } else {
            $category_to_create->id_shop_default = (int)$this->shop_id;
        }
        $category_to_create->name = $this->createMultiLangField(trim($category_name));
        $category_to_create->active = 1;
        $id_home = (int) Configuration::get('PS_HOME_CATEGORY', null, null, $category_to_create->id_shop_default);
        $category_to_create->id_parent = (int)$id_parent_category ? (int)$id_parent_category : $id_home;
        $category_link_rewrite = Tools::link_rewrite($category_to_create->name[$default_language_id]);
        $category_to_create->link_rewrite = $this->createMultiLangField($category_link_rewrite);
        $category_to_create->add();
    }
    protected function createMultiLangField($field)
    {
        $res = array();
        $array_id_lang = array();
        foreach (Language::getLanguages(false) as $value) {
            $array_id_lang[] = $value['id_lang'];
        }
        foreach ($array_id_lang as $id_lang) {
            $res[$id_lang] = $field;
        }
        return $res;
    }
    public function deleteElement($element, $array)
    {
        if (!empty($array)) {
            foreach ($array as $key => $value) {
                if ($value == $element) {
                    unset($array[$key]);
                }
            }
        }
        return array_values($array);
    }
    public function getCronjobButton($value)
    {
        $href = '';
        $text = $this->l('Remove Auto Import');
        if (empty($value)) {
            return $this->l('No');
        } else {
            $href = 'index.php?controller=AdminModules';
            $href .= '&token='.Tools::getAdminTokenLite('AdminModules');
            $href .= '&configure=ba_importer&deleteCronjob';
            $href .= '&id_cronjob='.(int) $value;
        }
        $this->context->smarty->assign('href', $href);
        $this->context->smarty->assign('text', $text);
        $link = "ba_importer/views/templates/admin/link.tpl";
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $link);
    }
    public function since170()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            return true;
        }
        return false;
    }
    public function since1610()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.6.1.0', '>=')) {
            return true;
        }
        return false;
    }
    public function processProductsNotInFile($id_setting, $action)
    {
        $action = Tools::strtolower($action);
        switch ($action) {
            case 'disable':
                $this->disableProductsNotInFile($id_setting);
                break;
            case 'quantity':
                $this->quantityProductsNotInFile($id_setting);
                break;
            case 'delete':
                $this->deleteProductsNotInFile($id_setting);
                break;
        }
        $this->cleanProductsInFile($id_setting);
        return true;
    }
    public function deleteProductsNotInFile($id_setting)
    {
        $id_setting = (int) $id_setting;
        $db = Db::getInstance();
        $productsinfile = $this->getProductsInFile($id_setting);
        if (empty($productsinfile)) {
            return false;
        }
        $id_shop = (int) $productsinfile[0]['id_shop'];
        unset($productsinfile);
        $sql = 'SELECT id_product FROM '._DB_PREFIX_."product_shop";
        $sql .= " WHERE id_shop = ".$id_shop;
        $sql .= " AND id_product NOT IN (";
        $sql .= " SELECT id_product FROM "._DB_PREFIX_."ba_importer_productsinfile";
        $sql .= " )";
        $products  = $db->executeS($sql, true, false);
        if (empty($products)) {
            return false;
        }
        foreach ($products as $p) {
            $id_product = (int) $p['id_product'];
            $pd = new Product($id_product, false, null, $id_shop);
            $pd->delete();
            unset($pd);
        }
        return true;
    }
    public function disableProductsNotInFile($id_setting)
    {
        $id_setting = (int) $id_setting;
        $products = $this->getProductsInFile($id_setting);
        if (empty($products)) {
            return false;
        }
        $id_shop = (int) $products[0]['id_shop'];
        $sql = 'UPDATE '._DB_PREFIX_."product_shop";
        $sql .= " SET active = 0";
        $sql .= " WHERE id_shop = ".$id_shop;
        $sql .= " AND id_product NOT IN (";
        $sql .= " SELECT id_product FROM "._DB_PREFIX_."ba_importer_productsinfile";
        $sql .= " )";
        return Db::getInstance()->query($sql);
    }
    public function quantityProductsNotInFile($id_setting)
    {
        $id_setting = (int) $id_setting;
        $products = $this->getProductsInFile($id_setting);
        if (empty($products)) {
            return false;
        }
        $id_shop = (int) $products[0]['id_shop'];
        $db = Db::getInstance();
        $sql = 'UPDATE '._DB_PREFIX_."product";
        $sql .= " SET quantity = 0";
        $sql .= " WHERE ";
        $sql .= " id_product NOT IN (";
        $sql .= " SELECT id_product FROM "._DB_PREFIX_."ba_importer_productsinfile";
        $sql .= " )";
        $db->query($sql);
        // update stock_available
        $sql = 'UPDATE '._DB_PREFIX_."stock_available";
        $sql .= " SET quantity = 0";

        // since 1.1.2
        $shop_group = new ShopGroup((int)Shop::getGroupFromShop($id_shop));
        if ($shop_group->share_stock) {
            $sql .= " WHERE (id_shop = ".$id_shop;
            $sql .= " OR (id_shop = 0 AND id_shop_group = ".(int)$shop_group->id.")";
            $sql .= ")";
        } else {
            $sql .= " WHERE id_shop = ".$id_shop;
        }

        $sql .= " AND id_product NOT IN (";
        $sql .= " SELECT id_product FROM "._DB_PREFIX_."ba_importer_productsinfile";
        $sql .= " )";
        $db->query($sql);
    }
    public function getProductsInFile($id_setting)
    {
        $id_setting = (int) $id_setting;
        $sql = 'SELECT id_product,id_shop,id_product_attribute FROM '._DB_PREFIX_."ba_importer_productsinfile";
        $sql .= " WHERE id_setting = ".$id_setting;
        return Db::getInstance()->executeS($sql, true, false);
    }
    public function cleanProductsInFile($id_setting)
    {
        $id_setting = (int) $id_setting;
        $sql = "DELETE FROM "._DB_PREFIX_."ba_importer_productsinfile";
        $sql .= " WHERE id_setting = ".$id_setting;
        return (bool) Db::getInstance()->query($sql);
    }
    public function insertProductsInFile($id_setting, $id_shop, $id_product, $id_attribute = 0)
    {
        $id_setting = (int) $id_setting;
        $id_shop = (int) $id_shop;
        $id_product = (int) $id_product;
        $id_attribute = (int) $id_attribute;
        $db =  Db::getInstance();
        $sql = 'SELECT count(*) FROM '._DB_PREFIX_."ba_importer_productsinfile";
        $sql .= " WHERE id_setting = ".$id_setting;
        $sql .= " AND id_product = ".$id_product;
        $sql .= " AND id_product_attribute = ".$id_attribute;
        $count = $db->getValue($sql, false);
        if ($count > 0) {
            return false;
        }
        $data = array(
            'id_setting' => $id_setting,
            'id_shop' => $id_shop,
            'id_product' => $id_product,
            'id_product_attribute' => $id_attribute,
            'date_add' => date('Y-m-d H:i:s')
        );
        $db->insert('ba_importer_productsinfile', $data);
        return true;
    }
    public function resetHelperList()
    {
        if (Tools::isSubmit('submitResetimporter_config')) {
            $this->context->cookie->{$this->helperlist_id.'Filter_id_importer_config'} = null;
            $this->context->cookie->{$this->helperlist_id.'Filter_ba_name_setting'} = null;
            $this->context->cookie->{$this->helperlist_id.'Filter_import_local'} = null;
            $this->context->cookie->{$this->helperlist_id.'Filter_ba_name_file'} = null;
            $this->context->cookie->{$this->helperlist_id.'Filter_date_add'} = null;
            $this->context->cookie->{$this->helperlist_id.'Filter_date_up'} = null;
            $this->context->cookie->{$this->helperlist_id.'Filter_update_at'} = null;
            $this->context->cookie->{$this->helperlist_id.'Filter_status_imported'} = null;
            $this->context->cookie->{$this->helperlist_id.'Orderby'} = 'id_importer_config';
            $this->context->cookie->{$this->helperlist_id.'Orderway'} = 'asc';
            $admin_token = Tools::getAdminTokenLite('AdminModules');
            $url = AdminController::$currentIndex.'&configure='.$this->name.'&token='.$admin_token;
            Tools::redirectAdmin($url);
            return true;
        }
    }
    public function setCookieFilter()
    {
        if (Tools::getValue($this->helperlist_id . "Filter_id_importer_config", null) !== null) {
            $i = pSQL(Tools::getValue($this->helperlist_id.'Filter_id_importer_config'));
            $this->context->cookie->{$this->helperlist_id.'Filter_id_importer_config'} = $i;
        }
        if (Tools::getValue($this->helperlist_id . "Filter_ba_name_setting", null) !== null) {
            $b = pSQL(Tools::getValue($this->helperlist_id.'Filter_ba_name_setting'));
            $this->context->cookie->{$this->helperlist_id.'Filter_ba_name_setting'} = $b;
        }
        if (Tools::getValue($this->helperlist_id . "Filter_ba_name_file", null) !== null) {
            $b = pSQL(Tools::getValue($this->helperlist_id.'Filter_ba_name_file'));
            $this->context->cookie->{$this->helperlist_id.'Filter_ba_name_file'} = $b;
        }
        if (Tools::getValue($this->helperlist_id . "Filter_import_local", null) !== null) {
            $i = pSQL(Tools::getValue($this->helperlist_id.'Filter_import_local'));
            $this->context->cookie->{$this->helperlist_id.'Filter_import_local'} = $i;
        }
        if (Tools::getValue($this->helperlist_id . "Filter_date_add", null) !== null) {
            $d = Tools::getValue($this->helperlist_id.'Filter_date_add');
            $this->context->cookie->{$this->helperlist_id.'Filter_date_add'} = serialize($d);
        }
        if (Tools::getValue($this->helperlist_id . "Filter_date_up", null) !== null) {
            $d = Tools::getValue($this->helperlist_id.'Filter_date_up');
            $this->context->cookie->{$this->helperlist_id.'Filter_date_up'} = serialize($d);
        }
        if (Tools::getValue($this->helperlist_id . "Filter_update_at", null) !== null) {
            $d = Tools::getValue($this->helperlist_id.'Filter_update_at');
            $this->context->cookie->{$this->helperlist_id.'Filter_update_at'} = serialize($d);
        }
        if (Tools::getValue($this->helperlist_id . "Filter_status_imported", null) !== null) {
            $s = Tools::getValue($this->helperlist_id.'Filter_status_imported');
            $this->context->cookie->{$this->helperlist_id.'Filter_status_imported'} = $s;
        }
        // Sort by
        if (Tools::getValue($this->helperlist_id . "Orderby", '') != '') {
            $o = pSQL(Tools::getValue($this->helperlist_id . "Orderby"));
            $this->context->cookie->{$this->helperlist_id.'Orderby'} = $o;
        }
        if (empty($this->context->cookie->{$this->helperlist_id.'Orderby'})) {
            $this->context->cookie->{$this->helperlist_id.'Orderby'} = 'id_importer_config';
        }
        if (Tools::getValue($this->helperlist_id . "Orderway", '') != '') {
            $o = pSQL(Tools::getValue($this->helperlist_id . "Orderway"));
            $this->context->cookie->{$this->helperlist_id.'Orderway'} = $o;
        }
        if (empty($this->context->cookie->{$this->helperlist_id.'Orderway'})) {
            $this->context->cookie->{$this->helperlist_id.'Orderway'} = 'asc';
        }
        //pagination
        $item_per_page=$this->context->cookie->{$this->helperlist_id.'_pagination'};
        $item_per_page=(int) Tools::getValue($this->helperlist_id.'_pagination', $item_per_page);
        if ($item_per_page <=0) {
            $item_per_page = 20;
        }
        $this->context->cookie->{$this->helperlist_id.'_pagination'} = $item_per_page;
        // page_number
        $page = (int) Tools::getValue('submitFilter'.$this->helperlist_id);
        if (!$page) {
            $page = 1;
        }
        $start = ($page -1 )* $item_per_page;
        $this->context->cookie->{$this->helperlist_id.'_start'} = ($start <0) ? 0 : $start;
        return true;
    }
    public function renderHelperList()
    {
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->actions = array('view','duplicate','delete');

        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&add'
            . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );
        $helper->toolbar_btn['reset'] = array(
            'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&stopcronjob'
            . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Reset auto import settings in the Queue')
        );

        $helper->identifier = 'id_importer_config';
        $helper->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
        $helper->show_toolbar = true;
        $helper->title = $this->l('Settings List');
        $helper->table = $this->helperlist_id;
        $helper->list_id = $this->helperlist_id;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure='.$this->name;
        $orderby = $this->context->cookie->{$helper->list_id.'Orderby'};
        $orderway = $this->context->cookie->{$helper->list_id.'Orderway'};
        $helper->orderBy = $orderby;
        $helper->orderWay = Tools::strtoupper($orderway);
        
        $fields_list = array(
            'id_importer_config' => array(
                'title' => $this->l('ID'),
                'width' => 35,
                'type' => 'text'
            ),
            'ba_name_setting' => array(
                'title' => $this->l('Setting'),
                'width' => 120,
                'type' => 'text'
            ),
            'import_local' => array(
                'title' => $this->l('Mode'),
                'width' => 120,
                'type' => 'select',
                'list' => array(
                    0 => $this->l('Remote URL'),
                    1 => $this->l('Local'),
                    2 => $this->l('FTP')
                ),
                'callback' => 'getNameByIdImportLocal',
                'callback_object' => $this,
                'filter_key' => 'import_local',
            ),
            'ba_name_file' => array(
                'title' => $this->l('File'),
                'width' => 120,
                'type' => 'text'
            ),
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'width' => 140,
                'type' => 'datetime'
            ),
            'date_up' => array(
                'title' => $this->l('Last Update'),
                'width' => 140,
                'type' => 'datetime',
                'width' => 50,
                'align' => 'right'
            ),
            'update_at' => array(
                'title' => $this->l('Last execution'),
                'width' => 140,
                'type' => 'datetime',
                'width' => 50,
                'align' => 'right'
            ),
            'status_imported' => array(
                'title' => $this->l('Status AutoImport'),
                'width' => 130,
                'type' => 'select',
                'list' => array(
                    1 => $this->l('--'),
                    2 => $this->l('Running'),
                    3 => $this->l('Done')
                ),
                'callback' => 'getStatusAutoImport',
                'callback_object' => $this,
                'filter_key' => 'status_imported'
            ),
            'id_cronjob' => array(
                'title' => $this->l('Is Auto Import?'),
                'width' => 130,
                'search' => false,
                'orderby' => false,
                'remove_onclick' => true,
                'type' => 'text',
                'callback' => 'getCronjobButton',
                'callback_object' => $this
            )
        );
        $helper->listTotal = $this->getTotalList($helper);
        $html = $helper->generateList($this->getListContent(), $fields_list);
        return $html;
    }
    public function getListContent()
    {
        $db = Db::getInstance();

        $sql = "SELECT bic.*, bci.id_cronjob, bci.ba_name, bci.update_at, bci.products_imported";
        $sql .= ",bci.status_imported, bci.imported,bci.CONFIGN_DATA_POST";
        $sql .= ' FROM '._DB_PREFIX_.'ba_importer_config AS bic';
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'ba_cronjobs_importer AS bci';
        $sql .= ' ON bic.id_importer_config = bci.id_importer_config';
        $sql .= " WHERE ".$this->getWhereClause();
        
        $orderby = $this->context->cookie->{$this->helperlist_id.'Orderby'};
        $orderway = $this->context->cookie->{$this->helperlist_id.'Orderway'};
        if (!empty($orderby) && !empty($orderby)) {
            $sql.=' ORDER BY '.pSQL($orderby).' '.pSQL($orderway);
        }
        // Pagination
        $item_per_page = $this->context->cookie->{$this->helperlist_id.'_pagination'};
        if ($item_per_page < 20) {
            $item_per_page = 20;
        }
        $start = $this->context->cookie->{$this->helperlist_id.'_start'};
        $sql.= ' LIMIT '.(int) $start.', '.(int) $item_per_page;

        $results = $db->ExecuteS($sql);
        return $results;
    }
    public function getTotalList()
    {
        $db = Db::getInstance();
        $sql = "SELECT count(*)";
        $sql .= ' FROM '._DB_PREFIX_.'ba_importer_config AS bic ';
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'ba_cronjobs_importer AS bci';
        $sql .= ' ON bic.id_importer_config = bci.id_importer_config';
        $sql .= " WHERE ".$this->getWhereClause();
        $count = $db->getValue($sql);
        return $count;
    }
    public function getWhereClause()
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $where = array("bic.id_shop = ".$id_shop);
        
        $id_importer_config = $this->context->cookie->{$this->helperlist_id.'Filter_id_importer_config'};
        if (!empty($id_importer_config)) {
            $where[] = "bic.id_importer_config LIKE '%".(int) $id_importer_config."%'";
        }
        
        $ba_name_setting = $this->context->cookie->{$this->helperlist_id.'Filter_ba_name_setting'};
        if (!empty($ba_name_setting)) {
            $where[] = "bic.ba_name_setting LIKE '%".pSQL($ba_name_setting)."%'";
        }
        
        $import_local = $this->context->cookie->{$this->helperlist_id.'Filter_import_local'};
        if ($import_local !== "" && $import_local !== false && $import_local !== null) {
            $where[] = "bic.import_local = ".(int) $import_local;
        }
        
        $ba_name_file = $this->context->cookie->{$this->helperlist_id.'Filter_ba_name_file'};
        if (!empty($ba_name_file)) {
            $where[] = "bic.ba_name_file LIKE '%".pSQL($ba_name_file)."%'";
        }
        
        $date_add = $this->context->cookie->{$this->helperlist_id.'Filter_date_add'};
        if (!empty($date_add)) {
            $date_add = unserialize($date_add);
            if (!empty($date_add[0])) {
                $where[] = " bic.date_add >='".pSQL($date_add[0]." 00:00:00")."' ";
            }
            if (!empty($date_add[1])) {
                $where[] = " bic.date_add <='".pSQL($date_add[1]." 23:59:59")."' ";
            }
        }
        
        $date_up = $this->context->cookie->{$this->helperlist_id.'Filter_date_up'};
        if (!empty($date_up)) {
            $date_up = unserialize($date_up);
            if (!empty($date_up[0])) {
                $where[] = " bic.date_up >='".pSQL($date_up[0]." 00:00:00")."' ";
            }
            if (!empty($date_up[1])) {
                $where[] = " bic.date_up <='".pSQL($date_up[1]." 23:59:59")."' ";
            }
        }
        
        $update_at = $this->context->cookie->{$this->helperlist_id.'Filter_update_at'};
        if (!empty($update_at)) {
            $update_at = unserialize($update_at);
            if (!empty($update_at[0])) {
                $where[] = " bci.update_at >='".pSQL($update_at[0]." 00:00:00")."' ";
            }
            if (!empty($date_up[1])) {
                $where[] = " bci.update_at <='".pSQL($update_at[1]." 23:59:59")."' ";
            }
        }

        $status_imported = $this->context->cookie->{$this->helperlist_id.'Filter_status_imported'};
        if (!empty($status_imported)) {
            $where[] = "bci.status_imported = ".(int) $status_imported;
        }
        return implode(" AND ", $where);
    }
    /** end 1.1.0 **/
    /** since 1.1.3 **/
    public function updateProductAttributeShop($data)
    {
        $where = array(1);
        $where_sql = '';
        if (isset($data['id_product_attribute'])) {
            $where[] = 'id_product_attribute = '.(int) $data['id_product_attribute'];
        }
        if (isset($data['id_shop'])) {
            $where[] = 'id_shop = '.(int) $data['id_shop'];
        }
        if (!empty($where)) {
            $where_sql = implode(' AND ', $where);
        }
        $db = Db::getInstance();
        $sql = "SELECT id_product_attribute ";
        $sql .= ' FROM '._DB_PREFIX_.'product_attribute_shop';
        $sql .= ' WHERE '.$where_sql;
        $id_product_attribute = $db->getValue($sql, false);
        if (empty($id_product_attribute)) {
            $db->insert('product_attribute_shop', $data);
        } else {
            $db->update('product_attribute_shop', $data, $where_sql);
        }
        return true;
    }
    public function moveImages($path_old, $id_image, $id_product, $cover = 0)
    {
        $img_quality = Configuration::get('PS_IMAGE_QUALITY');
        $x = new Image($id_image);
        $x->createImgFolder();
        $dirimage = _PS_PROD_IMG_DIR_ . $x->getImgFolder();
        // image information
        $image = getimagesize($path_old);
        $type = $image[2];
        $fileType = 'jpg';
        if ($img_quality == 'png_all' || ($img_quality == 'png' && $type == IMAGETYPE_PNG)) {
            $fileType = 'png';
        }
        ImageManager::resize($path_old, $dirimage . $id_image . ".jpg");
        if ($fileType == 'png') {
            //ImageManager::resize($path_old, $dirimage . $id_image . ".png");
        }
        if ($cover == 1) {
            if (Configuration::get('PS_LEGACY_IMAGES') == 1) {
                ImageManager::resize($path_old, _PS_PROD_IMG_DIR_ . $id_product . '-' . $id_image . ".jpg");
                if ($fileType == 'png') {
                    //ImageManager::resize($path_old, _PS_PROD_IMG_DIR_ . $id_product . '-' . $id_image . ".png");
                }
            }
            $src = _PS_TMP_IMG_DIR_ . "product_mini_" . $id_product . "_" . $this->shop_id . ".jpg";
            ImageManager::resize($path_old, $src, 45, 45);
            if ($fileType == 'png') {
                $src = _PS_TMP_IMG_DIR_ . "product_mini_" . $id_product . "_" . $this->shop_id . ".png";
                //ImageManager::resize($path_old, $src, 45, 45);
            }
        }

        $db = Db::getInstance();
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "image_type WHERE products='1'";
        $row = $db->executeS($sql, true, false);

        foreach ($row as $value) {
            $dir = $dirimage . $id_image . "-" . $value["name"] . ".jpg";
            ImageManager::resize($path_old, $dir, $value["width"], $value["height"]);
            if ($fileType == 'png') {
                $dir = $dirimage . $id_image . "-" . $value["name"] . ".png";
                //ImageManager::resize($path_old, $dir, $value["width"], $value["height"]);
            }
        }
    }
    // since 1.1.4
    public function disableCache()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.6.1.0', '>=')) {
            Db::getInstance()->disableCache();
        }
    }
    // since 1.1.5
    public function enableCache()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.6.1.0', '>=')) {
            Db::getInstance()->enableCache();
        }
    }
    public function validateRequired($text)
    {
        $text = Tools::strtolower($text);
        $required = array('1', 'required', 'yes', 'y');
        if (in_array($text, $required)) {
            return 1;
        }
        return 0;
    }
    public function customizationField($id_product, $type, $id_lang, $name)
    {
        $name = trim($name);
        if (empty($name)) {
            return false;
        }
        $name_arr = explode("|", $name);
        $name = $name_arr[0];
        $required = 0;
        if (isset($name_arr[1])) {
            $required = $this->validateRequired($name_arr[1]);
        }
        $db = Db::getInstance();
        // check exist customizationField
        $sql = "SELECT COUNT(*) FROM " . _DB_PREFIX_ . "customization_field cf";
        $sql .= " INNER JOIN " . _DB_PREFIX_ . "customization_field_lang cfl";
        $sql .= " ON cf.id_customization_field = cfl.id_customization_field";
        $sql .= " WHERE cf.id_product = ".(int) $id_product;
        $sql .= " AND cfl.name = '".pSQL($name)."'";
        $sql .= " AND cf.type = ".(int) $type;
        $n = (int) $db->getValue($sql);
        if ($n > 0) {
            return false;
        }

        $sql = "REPLACE INTO " . _DB_PREFIX_ . "customization_field ";
        $sql .= " (id_customization_field,id_product,type,required) VALUES('', '"
                . (int) $id_product . "', '" . (int) $type . "', '".$required."')";
        $db->query($sql);
        $id_customization_field = (int) $db->Insert_ID();
        //
        $arr_id_lang = array();
        $languagesArr = Language::getLanguages(false);
        foreach ($languagesArr as $v) {
            $arr_id_lang[]=(int) ($v['id_lang']);
        }
        if (!empty($arr_id_lang)) {
            foreach ($arr_id_lang as $value_lang) {
                $id_lang = (int) $value_lang;
                if (strpos(_PS_VERSION_, '1.6.0') === 0 || strpos(_PS_VERSION_, '1.5') === 0) {
                    if (strpos(_PS_VERSION_, '1.6.0.14') === false) {
                        $sql = "REPLACE INTO " . _DB_PREFIX_ . "customization_field_lang(`id_customization_field`,"
                               ." `id_lang`, `name`) VALUES('". $id_customization_field . "', '"
                               . $id_lang . "', '" . pSQL($name) . "')";
                        $db->query($sql);
                    } else {
                        $sql = "REPLACE INTO " . _DB_PREFIX_ . "customization_field_lang VALUES('"
                                . $id_customization_field . "', '" . $id_lang . "', '"
                                . (int) $this->shop_id . "', '" . pSQL($name) . "')";
                        $db->query($sql);
                    }
                } else {
                    $sql = "REPLACE INTO " . _DB_PREFIX_ . "customization_field_lang VALUES('"
                            . $id_customization_field . "', '" . $id_lang . "', '"
                            . (int) $this->shop_id . "', '" . pSQL($name) . "')";
                    $db->query($sql);
                }
            }
        }
        return true;
    }
    // since 1.1.6
    /** modify features **/
    public function validateText($text)
    {
        $order = array("^", "[", "<", ">", "=", "{", "}", "]", "*");
        $text = str_replace($order, '', $text);
        return $text;
    }
    public function parseFeatures($text)
    {
        $text = str_replace('""', '', $text);
        $rows = array();
        $DOM = new DOMDocument;
        $DOM->loadHTML($text);
        $items = $DOM->getElementsByTagName('tr');
        foreach ($items as $node) {
            $td = $node->getElementsByTagName('td');
            $td_array = array();
            foreach ($td as $td_node) {
                $td_array[] = $td_node->nodeValue;
            }
            $rows[] = $td_array;
        }
        return $rows;
    }
    public function addFeature($id_product, $name, $value)
    {
        $db = Db::getInstance();
        $languages = Language::getLanguages(true);
        $stores = Shop::getCompleteListOfShopsID();
        $name = trim($name);
        $name = $this->validateText($name);
        $value = trim($value);
        $value = $this->validateText($value);
        if (empty($name) || empty($value)) {
            return false;
        }
        // kiểm tra đã có feature này chưa?
        $sql = "SELECT id_feature FROM "._DB_PREFIX_."feature_lang fl";
        $sql .= " WHERE fl.name='".pSQL($name)."'";
        $id_feature = $db->getValue($sql);
        if (empty($id_feature)) {
            // chua ton tai, tao moi
            $position = Feature::getHigherPosition() + 1;
            $data = array(
                'id_feature' => null,
                'position' => $position,
            );
            Db::getInstance()->insert('feature', $data);
            $id_feature = (int) $db->Insert_ID();
            // chen feature_shop
            foreach ($stores as $id_shop) {
                $data = array(
                    'id_feature' => $id_feature,
                    'id_shop' => (int) $id_shop,
                );
                Db::getInstance()->insert('feature_shop', $data);
            }
            // chen feature_lang
            foreach ($languages as $lang) {
                $data = array(
                    'id_feature' => $id_feature,
                    'id_lang' => (int) $lang['id_lang'],
                    'name' => $name,
                );
                Db::getInstance()->insert('feature_lang', $data);
            }
        }
        /** kiem tra value **/
        $sql = "SELECT fv.id_feature_value FROM "._DB_PREFIX_."feature_value fv";
        $sql .= " LEFT JOIN "._DB_PREFIX_."feature_value_lang fvl ON fv.id_feature_value = fvl.id_feature_value";
        $sql .= " WHERE fv.custom = 0";
        $sql .= " AND fvl.value ='".pSQL($value)."'";
        $sql .= " AND fv.id_feature = ". (int) $id_feature;
        $id_feature_value = $db->getValue($sql);
        if (empty($id_feature_value)) {
            // chua ton tai, tao moi
            $data = array(
                'id_feature_value' => null,
                'id_feature' => $id_feature,
                'custom' => 0,
            );
            Db::getInstance()->insert('feature_value', $data);
            $id_feature_value = (int) $db->Insert_ID();
            // chen feature_lang
            foreach ($languages as $lang) {
                $data = array(
                    'id_feature_value' => $id_feature_value,
                    'id_lang' => (int) $lang['id_lang'],
                    'value' => pSQL($value),
                );
                Db::getInstance()->insert('feature_value_lang', $data);
            }
        }
        /** Match product to feature **/
        $data = array(
            'id_feature' => (int) $id_feature,
            'id_product' => (int) $id_product,
            'id_feature_value' => (int) $id_feature_value,
        );
        $db->insert('feature_product', $data, false, true, Db::REPLACE);
        return $id_feature_value;
    }
    public function importFullFeatures($id_product, $text)
    {
        if (empty($text)) {
            return false;
        }
        $rows = $this->parseFeatures($text);
        if (empty($rows)) {
            return false;
        }
        foreach ($rows as $feature) {
            $name = $feature[0];
            $value = $feature[1];
            $this->addFeature($id_product, $name, $value);
        }
        return true;
    }
    // since 1.1.7, delete a directory not empty
    public function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }
    // since 1.1.13
    public function validCategoryName($text)
    {
        $removeable = array("<", ">", ";", "=", "#", "{", "}");
        $text = str_replace($removeable, "", $text);
        return $text;
    }
}
function bautf8encode($value)
{
    $arr_encodeing=mb_detect_encoding($value, mb_list_encodings(), true);
    if (!empty($arr_encodeing)) {
        $value = mb_convert_encoding($value, "UTF-8", $arr_encodeing);
    }
    return $value;
}
