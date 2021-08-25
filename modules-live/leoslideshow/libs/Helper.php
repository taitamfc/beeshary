<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Adds image, text or video to your homepage.
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

if (!class_exists('LeoSlideshowHelper')) {

    class LeoSlideshowHelper
    {
        const MODULE_NAME = 'leoslideshow';

        public static function l($string, $specific = false, $name = '')
        {
            if (empty($name)) {
                $name = self::MODULE_NAME;
            }
            return Translate::getModuleTranslation($name, $string, ($specific) ? $specific : $name);
        }

        /**
         * Copy js, css to theme folder
         */
        public static function copyToTheme()
        {
            include_once(_PS_MODULE_DIR_.'leoslideshow/libs/phpcopy.php');

            $theme_dir = _PS_ROOT_DIR_.'/themes/'.LeoSlideshowHelper::getThemeName().'/';
            $module_dir = _PS_MODULE_DIR_.self::MODULE_NAME.'/';

            $theme_js_dir = $theme_dir.'js/modules/leoslideshow/views/js';
            $theme_css_dir = $theme_dir.'css/modules/leoslideshow/views/css';

            // Create js folder
            mkdir($theme_js_dir, 0755, true);
            PhpCopy::safeCopy($module_dir.'views/js/', $theme_js_dir);

            // Create css folder
            mkdir($theme_css_dir, 0755, true);
            PhpCopy::safeCopy($module_dir.'views/css/', $theme_css_dir);

            $url = 'index.php?controller=adminmodules&configure=leoslideshow&token='.Tools::getAdminTokenLite('AdminModules')
                    .'&tab_module=front_office_features&module_name=leoslideshow';
            Tools::redirectAdmin($url);
        }
        
        public static function getImgThemeUrl()
        {
            # LeoSlideshowHelper::getImgThemeUrl()
            static $img_theme_url;
            if (!$img_theme_url) {
                // Not exit image or icon
//                $img_theme_url = _THEME_IMG_DIR_.'modules/leoslideshow/';
                $img_theme_url = _THEMES_DIR_.self::getThemeName().'/assets/img/modules/leoslideshow/';
            }

            return $img_theme_url;
        }
    
        public static function getImgThemeDir()
        {
            static $img_theme_dir;
            if (!$img_theme_dir) {
                $img_theme_dir = _PS_ALL_THEMES_DIR_.LeoSlideshowHelper::getThemeName().'/assets/img/modules/leoslideshow/';
            }
            return $img_theme_dir;
        }
        
        public static function genKey()
        {
            return md5(time().rand());
        }
        
        public static function getThemeName()
        {
            static $theme_name;
            if (!$theme_name) {
                # DEFAULT SINGLE_SHOP
                $theme_name = _THEME_NAME_;

                # GET THEME_NAME MULTI_SHOP
                if (Shop::getTotalShops(false, null) >= 2) {
                    $id_shop = Context::getContext()->shop->id;

                    $shop_arr = Shop::getShop($id_shop);
                    if (is_array($shop_arr) && !empty($shop_arr)) {
                        $theme_name = $shop_arr['theme_name'];
                    }
                }
            }

            return $theme_name;
        }
    }
}
