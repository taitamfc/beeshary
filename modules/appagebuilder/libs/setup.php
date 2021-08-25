<?php
/*
 *  @Website: apollotheme.com - prestashop template provider
 *  @author Apollotheme <apollotheme@gmail.com>
 *  @copyright  2007-2016 Apollotheme
 *  @description: ApPageBuilder is module help you can build content for your shop
 */
if (!class_exists("ApPageSetup")) {

    class ApPageSetup
    {

        public static function getTabs()
        {
            return array(
                array(
                    'class_name' => 'AdminApPageBuilderProfiles',
                    'name' => 'Ap Profiles Manage',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderPositions',
                    'name' => 'Ap Positions Manage',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderShortcode',
                    'name' => 'Ap ShortCode Manage',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderHome',
                    'name' => 'Ap Hook Builder',
                    'id_parent' => -1,
                ),
                array(
                    'class_name' => 'AdminApPageBuilderProducts',
                    'name' => 'Ap Products List Builder',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderDetails',
                    'name' => 'Ap Products Details Builder',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderHook',
                    'name' => 'Ap Hook Control Panel',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderThemeEditor',
                    'name' => 'Ap Live Theme Editor',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderModule',
                    'name' => 'Ap Module Configuration',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderThemeConfiguration',
                    'name' => 'Ap Theme Configuration',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderImages',
                    'name' => 'Ap Image Manage',
                    'id_parent' => -1,
                ),
                array(
                    'class_name' => 'AdminApPageBuilderShortcodes',
                    'name' => 'Ap Shortcodes Builder',
                    'id_parent' => -1,
                ),
            );
        }

        public static function createTables($reset = 0)
        {
            if ($reset == 0 && file_exists(_PS_MODULE_DIR_.'appagebuilder')) {
                require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');

                $sample = new Datasample();
                if ($sample->processImport('appagebuilder')) {
                    return true;
                }
            }
            $drop = '';
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_profiles`;';
            }
            //each shop will have one or more profile
            $res = (bool)Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_profiles` (
                    `id_appagebuilder_profiles` int(11) NOT NULL AUTO_INCREMENT,
                        `name` varchar(255),
                        `group_box` varchar(255),
                        `profile_key` varchar(255),
                        `page` varchar(255),
                        `params` text,
                        `header` int(11) unsigned NOT NULL,
                        `content` int(11) unsigned NOT NULL,
                        `footer` int(11) unsigned NOT NULL,
                        `product` int(11) unsigned NOT NULL,
                        `active` TINYINT(1),
                    PRIMARY KEY (`id_appagebuilder_profiles`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_profiles_lang`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_profiles_lang` (
                   `id_appagebuilder_profiles` int(11) NOT NULL AUTO_INCREMENT,
                   `id_lang` int(10) unsigned NOT NULL,
                   `friendly_url` varchar(255),
                    `meta_title` varchar(255),
                    `meta_description` varchar(255),
                    `meta_keywords` varchar(255),
                   PRIMARY KEY (`id_appagebuilder_profiles`, `id_lang`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_profiles_shop`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_profiles_shop` (
                  `id_appagebuilder_profiles` int(11) NOT NULL AUTO_INCREMENT,
                  `id_shop` int(10) unsigned NOT NULL,
                  `active` TINYINT(1),
                  PRIMARY KEY (`id_appagebuilder_profiles`, `id_shop`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_products`;';
            }
            //we can create product item for each shop
            $res &= (bool)Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_products` (
                    `id_appagebuilder_products` int(11) NOT NULL AUTO_INCREMENT,
                        `plist_key` varchar(255),
                        `name` varchar(255),
                        `class` varchar(255),
                        `params` text,
                        `type` TINYINT(1),
                        `active` TINYINT(1),
                    PRIMARY KEY (`id_appagebuilder_products`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_products_shop`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_products_shop` (
                  `id_appagebuilder_products` int(11) NOT NULL AUTO_INCREMENT,
                  `id_shop` int(10) unsigned NOT NULL,
                  `active` TINYINT(1),
                  PRIMARY KEY (`id_appagebuilder_products`, `id_shop`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            $res &= (bool)Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_details` (
                    `id_appagebuilder_details` int(11) NOT NULL AUTO_INCREMENT,
                        `plist_key` varchar(255),
                        `name` varchar(255),
                        `class_detail` varchar(255),
						`url_img_preview` varchar(255),
                        `params` text,
                        `type` TINYINT(1),
                        `active` TINYINT(1),
                    PRIMARY KEY (`id_appagebuilder_details`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_details_shop`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_details_shop` (
                  `id_appagebuilder_details` int(11) NOT NULL AUTO_INCREMENT,
                  `id_shop` int(10) unsigned NOT NULL,
                  `active` TINYINT(1),
                  PRIMARY KEY (`id_appagebuilder_details`, `id_shop`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder`;';
            }
            $res &= (bool)Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder` (
                    `id_appagebuilder` int(11) NOT NULL AUTO_INCREMENT,
                        `id_appagebuilder_positions` int(11) NOT NULL,
                        `hook_name` varchar(255),
						`id_appagebuilder_shortcode` int(11) NOT NULL,
                    PRIMARY KEY (`id_appagebuilder`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');

            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_shop`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_shop` (
                  `id_appagebuilder` int(11) NOT NULL AUTO_INCREMENT,
                  `id_shop` int(10) unsigned NOT NULL,
                  PRIMARY KEY (`id_appagebuilder`, `id_shop`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_lang`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_lang` (
                   `id_appagebuilder` int(11) NOT NULL AUTO_INCREMENT,
                   `id_lang` int(10) unsigned NOT NULL,
                   `params` MEDIUMTEXT,
                   PRIMARY KEY (`id_appagebuilder`, `id_lang`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
                
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_positions`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_positions` (
                    `id_appagebuilder_positions` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
                    `position` varchar(255) NOT NULL,
                    `position_key` varchar(255) NOT NULL,
                    `params` text,
                    PRIMARY KEY (`id_appagebuilder_positions`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_positions_shop`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_positions_shop` (
                  `id_appagebuilder_positions` int(11) NOT NULL AUTO_INCREMENT,
                  `id_shop` int(10) unsigned NOT NULL,
                  PRIMARY KEY (`id_appagebuilder_positions`, `id_shop`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_page_shop`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_page` (
                  `id_product` int(11) unsigned NOT NULL,
                  `id_category` int(11) unsigned NOT NULL,
                  `page` varchar(255) NOT NULL,
                  `id_shop` int(10) unsigned NOT NULL,
                  PRIMARY KEY (`id_product`, `id_category`, `id_shop`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            //DONGND:: create table for ap shortcode
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_shortcode`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_shortcode` (
                  `id_appagebuilder_shortcode` int(11) NOT NULL AUTO_INCREMENT,                  
				  `shortcode_key` varchar(255) NOT NULL,
                  `active` TINYINT(1),
                  PRIMARY KEY (`id_appagebuilder_shortcode`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            //DONGND:: create table for ap shortcode (lang)
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_shortcode_lang`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_shortcode_lang` (
                   `id_appagebuilder_shortcode` int(11) unsigned NOT NULL,
                   `id_lang` int(10) unsigned NOT NULL,
                   `shortcode_name` text NOT NULL,
                   PRIMARY KEY (`id_appagebuilder_shortcode`, `id_lang`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            //DONGND:: create table for ap shortcode (shop)
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_shortcode_shop`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_shortcode_shop` (
                  `id_appagebuilder_shortcode` int(11) unsigned NOT NULL,
                  `id_shop` int(10) unsigned NOT NULL,
				  `active` TINYINT(1),
                  PRIMARY KEY (`id_appagebuilder_shortcode`, `id_shop`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            return $res;
        }

        public static function installSample()
        {
            $id_shop = Context::getContext()->shop->id;

            //table appagebuilder_profiles
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder_profiles`');
            $sql = 'INSERT INTO `'._DB_PREFIX_.'appagebuilder_profiles` (`id_appagebuilder_profiles`, `name`, `profile_key`, `page`, `params`, `header`, `content`, `footer`, `product`, `active`) VALUES
(1, \'Home page\', \'profile1426561433\', \'index\', \'{"displayTopColumn":{"exception":[""]},"displayHome":{"exception":[""]},"fullwidth_index_hook":{"displayBanner":0,"displayNav":0,"displayTop":"1","displayTopColumn":"1","displayHome":0,"displayFooter":0},"fullwidth_other_hook":{"displayBanner":0,"displayNav":0,"displayTop":0,"displayTopColumn":0,"displayFooter":0}}\', 1, 2, 27, 4, NULL),
(2, \'Detail demo\', \'profile1426579529\', \'index\', \'{"displayTopColumn":{"exception":[""]},"displayHome":{"exception":[""]},"fullwidth_index_hook":{"displayBanner":0,"displayNav":0,"displayTop":"1","displayTopColumn":"1","displayHome":0,"displayFooter":0},"fullwidth_other_hook":{"displayBanner":0,"displayNav":0,"displayTop":0,"displayTopColumn":0,"displayFooter":0}}\', 5, 6, 28, 8, NULL),
(3, \'Home page 1\', \'profile1427119013\', \'index\', \'{"displayHome":{"exception":[""]}}\', 9, 10, 25, 12, NULL),
(4, \'Home page 2\', \'profile1427116699\', \'index\', \'{"displayTopColumn":{"exception":[""]},"displayHome":{"exception":[""]}}\', 13, 14, 26, 16, NULL),
(5, \'Home page 3\', \'profile1427805353\', \'index\', \'{"displayTopColumn":{"exception":[""]},"displayHome":{"exception":[""]},"fullwidth_index_hook":{"displayBanner":0,"displayNav":0,"displayTop":"1","displayTopColumn":"1","displayHome":0,"displayFooter":0},"fullwidth_other_hook":{"displayBanner":0,"displayNav":0,"displayTop":0,"displayTopColumn":0,"displayHome":0,"displayFooter":0}}\', 21, 22, 29, 24, NULL);';
            Db::getInstance()->execute($sql);

            //table appagebuilder_profiles_shop
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder_profiles_shop`');
            $sql = 'INSERT INTO `'._DB_PREFIX_.'appagebuilder_profiles_shop` (`id_appagebuilder_profiles`, `id_shop`, `active`) VALUES
(1, ID_SHOP, 0),
(2, ID_SHOP, 0),
(3, ID_SHOP, 1),
(4, ID_SHOP, 0),
(5, ID_SHOP, 0);';
            $sql = str_replace('ID_SHOP', (int)$id_shop, $sql);
            Db::getInstance()->execute($sql);

            //table appagebuilder_positions
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder_positions`');
            $sql = "INSERT INTO `"._DB_PREFIX_."appagebuilder_positions` (`id_appagebuilder_positions`, `name`, `position`, `position_key`, `params`) VALUES
(1, 'header1426579629', 'header', 'position1426579629', NULL),
(2, 'content1426564879', 'content', 'position1426564879', NULL),
(3, 'footer1426566238', 'footer', 'position1426566238', NULL),
(4, 'product1426580519', 'product', 'position1426580519', NULL),
(5, 'header1426564187', 'header', 'position1426564187', NULL),
(6, 'content1426564490', 'content', 'position1426564490', NULL),
(7, 'footer1426578355', 'footer', 'position1426578355', NULL),
(8, 'product1426581801', 'product', 'position1426581801', NULL),
(9, 'header1427111294', 'header', 'position1427111294', NULL),
(10, 'content1427129695', 'content', 'position1427129695', NULL),
(11, 'footer1427107373', 'footer', 'position1427107373', NULL),
(12, 'product1427129206', 'product', 'position1427129206', NULL),
(13, 'header1427138535', 'header', 'position1427138535', NULL),
(14, 'content1427116604', 'content', 'position1427116604', NULL),
(15, 'footer1427111534', 'footer', 'position1427111534', NULL),
(16, 'product1427111243', 'product', 'position1427111243', NULL),
(17, 'header1427806687', 'header', 'position1427806687', NULL),
(18, 'content1427819338', 'content', 'position1427819338', NULL),
(19, 'footer1427821311', 'footer', 'position1427821311', NULL),
(20, 'product1427816721', 'product', 'position1427816721', NULL),
(21, 'header1434016210', 'header', 'position1434016210', NULL),
(22, 'content1434021220', 'content', 'position1434021220', NULL),
(23, 'footer1434021922', 'footer', 'position1434021922', NULL),
(24, 'product1434038427', 'product', 'position1434038427', NULL),
(25, 'footer1435143282', 'footer', 'position1435143282', NULL),
(26, 'footer1435158937', 'footer', 'position1435158937', NULL),
(27, 'footer1435144169', 'footer', 'position1435144169', NULL),
(28, 'footer1435153254', 'footer', 'position1435153254', NULL),
(29, 'footer1435237119', 'footer', 'position1435237119', NULL);";
            Db::getInstance()->execute($sql);

            //table appagebuilder_positions_shop
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder_positions_shop`');
            $sql = "INSERT INTO `"._DB_PREFIX_."appagebuilder_positions_shop` (`id_appagebuilder_positions`, `id_shop`) VALUES
(1, ID_SHOP),
(2, ID_SHOP),
(4, ID_SHOP),
(5, ID_SHOP),
(6, ID_SHOP),
(8, ID_SHOP),
(9, ID_SHOP),
(10, ID_SHOP),
(12, ID_SHOP),
(13, ID_SHOP),
(14, ID_SHOP),
(16, ID_SHOP),
(21, ID_SHOP),
(22, ID_SHOP),
(24, ID_SHOP),
(25, ID_SHOP),
(26, ID_SHOP),
(27, ID_SHOP),
(28, ID_SHOP),
(29, ID_SHOP),
(30, ID_SHOP);";
            $sql = str_replace('ID_SHOP', (int)$id_shop, $sql);
            Db::getInstance()->execute($sql);

            //table appagebuilder
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder`');
            $sql = "INSERT INTO `"._DB_PREFIX_."appagebuilder` (`id_appagebuilder`, `id_appagebuilder_positions`, `hook_name`) VALUES
(1, 1, 'displayBanner'),
(2, 1, 'displayNav'),
(3, 1, 'displayTop'),
(4, 2, 'displayTopColumn'),
(5, 2, 'displayLeftColumn'),
(6, 2, 'displayHome'),
(7, 2, 'displayRightColumn'),
(8, 3, 'displayFooter'),
(9, 4, 'displayFooterProduct'),
(10, 4, 'displayRightColumnProduct'),
(11, 5, 'displayBanner'),
(12, 5, 'displayNav'),
(13, 5, 'displayTop'),
(14, 6, 'displayTopColumn'),
(15, 6, 'displayLeftColumn'),
(16, 6, 'displayHome'),
(17, 6, 'displayRightColumn'),
(18, 7, 'displayFooter'),
(19, 8, 'displayFooterProduct'),
(20, 8, 'displayRightColumnProduct'),
(21, 9, 'displayBanner'),
(22, 9, 'displayNav'),
(23, 9, 'displayTop'),
(24, 10, 'displayTopColumn'),
(25, 10, 'displayLeftColumn'),
(26, 10, 'displayHome'),
(27, 10, 'displayRightColumn'),
(28, 11, 'displayFooter'),
(29, 12, 'displayFooterProduct'),
(30, 12, 'displayRightColumnProduct'),
(31, 13, 'displayBanner'),
(32, 13, 'displayNav'),
(33, 13, 'displayTop'),
(34, 14, 'displayTopColumn'),
(35, 14, 'displayLeftColumn'),
(36, 14, 'displayHome'),
(37, 14, 'displayRightColumn'),
(38, 15, 'displayFooter'),
(39, 16, 'displayFooterProduct'),
(40, 16, 'displayRightColumnProduct'),
(41, 17, 'displayBanner'),
(42, 17, 'displayNav'),
(43, 17, 'displayTop'),
(44, 18, 'displayTopColumn'),
(45, 18, 'displayLeftColumn'),
(46, 18, 'displayHome'),
(47, 18, 'displayRightColumn'),
(48, 19, 'displayFooter'),
(49, 20, 'displayFooterProduct'),
(50, 20, 'displayRightColumnProduct'),
(51, 21, 'displayBanner'),
(52, 21, 'displayNav'),
(53, 21, 'displayTop'),
(54, 22, 'displayTopColumn'),
(55, 22, 'displayLeftColumn'),
(56, 22, 'displayHome'),
(57, 22, 'displayRightColumn'),
(58, 23, 'displayFooter'),
(59, 24, 'displayFooterProduct'),
(60, 24, 'displayRightColumnProduct'),
(61, 25, 'displayFooter'),
(62, 26, 'displayFooter'),
(63, 27, 'displayFooter'),
(64, 28, 'displayFooter'),
(65, 29, 'displayFooter');";
            Db::getInstance()->execute($sql);

            
            //table appagebuilder_lang
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder_lang`');

            $sqlArray[] = Tools::file_get_contents( apPageHelper::getShortcodeTemplatePath('setup.txt'));
            $languages = Language::getLanguages(false);
            foreach ($sqlArray as $sql) {
                foreach ($languages as $lang) {
                    $sqlRun = str_replace('ID_LANG', (int)$lang["id_lang"], $sql);
                    $sqlRun = str_replace('_DB_PREFIX_', _DB_PREFIX_, $sqlRun);
                    Db::getInstance()->execute($sqlRun);
                }
            }

            //table appagebuilder_shop
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder_shop`');
            $sql = 'INSERT INTO `'._DB_PREFIX_.'appagebuilder_shop` (`id_appagebuilder`, `id_shop`) VALUES
(1, ID_SHOP),
(2, ID_SHOP),
(3, ID_SHOP),
(4, ID_SHOP),
(5, ID_SHOP),
(6, ID_SHOP),
(7, ID_SHOP),
(8, ID_SHOP),
(9, ID_SHOP),
(10, ID_SHOP),
(11, ID_SHOP),
(12, ID_SHOP),
(13, ID_SHOP),
(14, ID_SHOP),
(15, ID_SHOP),
(16, ID_SHOP),
(17, ID_SHOP),
(18, ID_SHOP),
(19, ID_SHOP),
(20, ID_SHOP),
(21, ID_SHOP),
(22, ID_SHOP),
(23, ID_SHOP),
(24, ID_SHOP),
(25, ID_SHOP),
(26, ID_SHOP),
(27, ID_SHOP),
(28, ID_SHOP),
(29, ID_SHOP),
(30, ID_SHOP),
(31, ID_SHOP),
(32, ID_SHOP),
(33, ID_SHOP),
(34, ID_SHOP),
(35, ID_SHOP),
(36, ID_SHOP),
(37, ID_SHOP),
(38, ID_SHOP),
(39, ID_SHOP),
(40, ID_SHOP),
(41, ID_SHOP),
(42, ID_SHOP),
(43, ID_SHOP),
(44, ID_SHOP),
(45, ID_SHOP),
(46, ID_SHOP),
(47, ID_SHOP),
(48, ID_SHOP),
(49, ID_SHOP),
(50, ID_SHOP),
(51, ID_SHOP),
(52, ID_SHOP),
(53, ID_SHOP),
(54, ID_SHOP),
(55, ID_SHOP),
(56, ID_SHOP),
(57, ID_SHOP),
(58, ID_SHOP),
(59, ID_SHOP),
(60, ID_SHOP),
(61, ID_SHOP),
(62, ID_SHOP),
(63, ID_SHOP),
(64, ID_SHOP),
(65, ID_SHOP);';
            $sql = str_replace('ID_SHOP', (int)$id_shop, $sql);
            Db::getInstance()->execute($sql);

            //table appagebuilder_products
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder_products`');
            $sql = 'INSERT INTO `'._DB_PREFIX_.'appagebuilder_products` (`id_appagebuilder_products`, `plist_key`, `name`, `params`, `type`, `class`, `active`) VALUES
(1, \'plist1427203522\', \'plist1427203522\', \'{"gridLeft":{"0":{"name":"image_container"},"1":{"name":"quick_view"}},"gridRight":{"0":{"name":"price"},"1":{"name":"reviews"},"2":{"name":"name"},"3":{"name":"functional_buttons","element":{"0":{"name":"wishlist"},"1":{"name":"add_to_cart"},"2":{"name":"compare"}}}}}\', 0, \'\', NULL);';
            Db::getInstance()->execute($sql);

            //table appagebuilder_products_shop
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder_products_shop`');
            $sql = 'INSERT INTO `'._DB_PREFIX_.'appagebuilder_products_shop` (`id_appagebuilder_products`, `id_shop`, `active`) VALUES
(1, ID_SHOP, 1)';
            $sql = str_replace('ID_SHOP', (int)$id_shop, $sql);
            Db::getInstance()->execute($sql);

            //copy product profile
            $folder = apPageHelper::getConfigDir('theme_profiles');
            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }
            $tpl_grid = Tools::file_get_contents(_PS_MODULE_DIR_.'appagebuilder/views/templates/front/product-item/plist1427203522.tpl');
            ApPageSetting::writeFile($folder, 'plist1427203522.tpl', $tpl_grid);
        }

        public static function installModuleTab()
        {
            $id_parent = Tab::getIdFromClassName('IMPROVE');

            //create parent tab
            $newtab = new Tab();
            $newtab->class_name = 'AdminApPageBuilder';
            $newtab->id_parent = $id_parent;
            $newtab->module = 'appagebuilder';
            foreach (Language::getLanguages(false) as $l) {
                $newtab->name[$l['id_lang']] = Context::getContext()->getTranslator()->trans('Ap PageBuilder', array(), 'Modules.Appagebuilder.Admin');
            }

            if ($newtab->save()) {

                $id_parent = $newtab->id;
                # insert icon for tab
                Db::getInstance()->execute(' UPDATE `'._DB_PREFIX_.'tab` SET `icon` = "tab" WHERE `id_tab` = "'.(int)$newtab->id.'"');

                foreach (self::getTabs() as $tab) {
                    $newtab = new Tab();
                    $newtab->class_name = $tab['class_name'];
                    $newtab->id_parent = isset($tab['id_parent']) ? $tab['id_parent'] : $id_parent;
                    $newtab->module = 'appagebuilder';
                    foreach (Language::getLanguages(false) as $l) {
                        $newtab->name[$l['id_lang']] = Context::getContext()->getTranslator()->trans($tab['name'], array(), 'Modules.Appagebuilder.Admin');
                    }
                    $newtab->save();
                }
                return true;
            }
            
            return false;
        }

        public static function installConfiguration()
        {
            $res = true;
            $res &= Configuration::updateValue('APPAGEBUILDER_PRODUCT_MAX_RANDOM', 2);
            $res &= Configuration::updateValue('APPAGEBUILDER_GUIDE', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_OWL', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_STELLAR', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_WAYPOINTS', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_INSTAFEED', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_HTML5VIDEO', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_SAVE_MULTITHREARING', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_FULLPAGEJS', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_IMAGE360', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_IMAGEHOTPOT', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_SAVE_SUBMIT', 1);
			$res &= Configuration::updateValue('APPAGEBUILDER_LOAD_PRODUCTZOOM', 1);
//            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_AJAX', 1);
//            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_PN', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_TRAN', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_IMG', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_COUNT', 1);
//            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_COLOR', 1);
//            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_ACOLOR', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_COLOR', '');
            $res &= Configuration::updateValue('APPAGEBUILDER_COOKIE_PROFILE', 0);

            $res &= Configuration::updateValue('APPAGEBUILDER_HEADER_HOOK', implode(',', ApPageSetting::getHook('header')));
            $res &= Configuration::updateValue('APPAGEBUILDER_CONTENT_HOOK', implode(',', ApPageSetting::getHook('content')));
            $res &= Configuration::updateValue('APPAGEBUILDER_FOOTER_HOOK', implode(',', ApPageSetting::getHook('footer')));
            $res &= Configuration::updateValue('APPAGEBUILDER_PRODUCT_HOOK', implode(',', ApPageSetting::getHook('product')));
			
            $res &= Configuration::updateValue('APPAGEBUILDER_GLOBAL_HEADER_ID', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_GLOBAL_CONTENT_ID', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_GLOBAL_FOOTER_ID', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_GLOBAL_PRODUCT_ID', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_GLOBAL_PROFILE_PARAM', '');
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_COOKIE', 0);
            return $res;
        }

        public static function deleteTables()
        {
            return Db::getInstance()->execute('DROP TABLE IF EXISTS `'.
                _DB_PREFIX_.'appagebuilder_profiles`, `'.
                _DB_PREFIX_.'appagebuilder_profiles_lang`, `'.
                _DB_PREFIX_.'appagebuilder_profiles_shop`, `'.
                _DB_PREFIX_.'appagebuilder_products`, `'.
                _DB_PREFIX_.'appagebuilder_products_shop` , `'.
                _DB_PREFIX_.'appagebuilder`, `'.
                _DB_PREFIX_.'appagebuilder_shop`, `'.
                _DB_PREFIX_.'appagebuilder_lang`, `'.
                _DB_PREFIX_.'appagebuilder_extracat`, `'.
                _DB_PREFIX_.'appagebuilder_extrapro`, `'.
                _DB_PREFIX_.'appagebuilder_page`, `'.
                _DB_PREFIX_.'appagebuilder_details`, `'.
                _DB_PREFIX_.'appagebuilder_details_shop`, `'.
                _DB_PREFIX_.'appagebuilder_positions`, `'.
		_DB_PREFIX_.'appagebuilder_shortcode`, `'.
                _DB_PREFIX_.'appagebuilder_shortcode_lang`, `'.
                _DB_PREFIX_.'appagebuilder_shortcode_shop`, `'.
                _DB_PREFIX_.'appagebuilder_positions_shop`;
            ');
        }
        
        public static function uninstallModuleTab()
        {
            $id = Tab::getIdFromClassName('AdminApPageBuilder');
            if ($id) {
                $tab = new Tab($id);
                $tab->delete();
            }

            foreach (self::getTabs() as $tab) {
                $id = Tab::getIdFromClassName($tab['class_name']);
                if ($id) {
                    $tab = new Tab($id);
                    $tab->delete();
                }
            }
            return true;
        }
        
        public static function uninstallConfiguration()
        {
            $res = true;
            $res &= Configuration::deleteByName('APPAGEBUILDER_PRODUCT_MAX_RANDOM');
            $res &= Configuration::deleteByName('APPAGEBUILDER_GUIDE');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_OWL');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_STELLAR');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_WAYPOINTS');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_INSTAFEED');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_HTML5VIDEO');
            $res &= Configuration::deleteByName('APPAGEBUILDER_SAVE_MULTITHREARING');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_FULLPAGEJS');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_IMAGE360');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_IMAGEHOTPOT');
            $res &= Configuration::deleteByName('APPAGEBUILDER_SAVE_SUBMIT');
			$res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_PRODUCTZOOM');
//            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_AJAX');
//            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_PN');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_TRAN');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_IMG');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_COUNT');
//            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_COLOR');
//            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_ACOLOR');
            $res &= Configuration::deleteByName('APPAGEBUILDER_COLOR');
            $res &= Configuration::deleteByName('APPAGEBUILDER_COOKIE_PROFILE');
            
            
            $res &= Configuration::deleteByName('APPAGEBUILDER_HEADER_HOOK');
            $res &= Configuration::deleteByName('APPAGEBUILDER_CONTENT_HOOK');
            $res &= Configuration::deleteByName('APPAGEBUILDER_FOOTER_HOOK');
            $res &= Configuration::deleteByName('APPAGEBUILDER_PRODUCT_HOOK');

            $res &= Configuration::deleteByName('APPAGEBUILDER_GLOBAL_HEADER_ID');
            $res &= Configuration::deleteByName('APPAGEBUILDER_GLOBAL_CONTENT_ID');
            $res &= Configuration::deleteByName('APPAGEBUILDER_GLOBAL_FOOTER_ID');
            $res &= Configuration::deleteByName('APPAGEBUILDER_GLOBAL_PRODUCT_ID');
            $res &= Configuration::deleteByName('APPAGEBUILDER_GLOBAL_PROFILE_PARAM');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_COOKIE');
            
			//DONGND:: remove config check override for shortcode
			$res &= Configuration::deleteByName('APPAGEBUILDER_OVERRIDED');
            return $res;
        }
        
        /**
         * Remove file index.php in sub folder theme/translations folder when install theme
         */
        public static function processTranslateTheme()
        {
            $theme_name = apPageHelper::getInstallationThemeName();
            if (file_exists(_PS_ALL_THEMES_DIR_.$theme_name.'/config.xml')) {
                $directories = glob(_PS_ALL_THEMES_DIR_.$theme_name.'/translations/*', GLOB_ONLYDIR);
                if (count($directories) > 0) {
                    foreach ($directories as $directories_val) {
                        if (file_exists($directories_val.'/index.php')) {
                            unlink($directories_val.'/index.php');
                        }
                    }
                }
            }
        }
        
        /**
         * Remove file index.php for translate in Quickstart version
         */
        public static function processTranslateQSTheme()
        {
            # GET ARRAY THEME_NAME
            $arr_theme_name = array();
            $themes = glob(_PS_ROOT_DIR_.'/themes/*/config/theme.yml');
            if (count($themes) > 1) {
                foreach ($themes as $key => $value) {
                    $temp_name = basename(Tools::substr($value, 0, -strlen('/config/theme.yml')));
                    if ($temp_name == 'classic') {
                        continue;
                    } else {
                        $arr_theme_name[] = $temp_name;
                    }
                }
            }
            
            foreach ($arr_theme_name as $key => $theme_name) {
                //DONGND:: remove index.php in sub folder theme/translations folder when install theme
                
                if (file_exists(_PS_ALL_THEMES_DIR_.$theme_name.'/config.xml')) {
                    $directories = glob(_PS_ALL_THEMES_DIR_.$theme_name.'/translations/*', GLOB_ONLYDIR);
                    if (count($directories) > 0) {
                        foreach ($directories as $directories_val) {
                            if (file_exists($directories_val.'/index.php')) {
                                unlink($directories_val.'/index.php');
                            }
                        }
                    }
                }
            }
        }
    }

}