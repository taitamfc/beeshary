<?php
/**
 * 2010-2016 Webkul.
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
 *  @copyright 2010-2016 Webkul IN
 *  @license   https://store.webkul.com/license.html
 */

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

class marketplaceSearchShopsModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $id_lang = $this->context->language->id;
        $catIDs = Tools::getValue('pp_theme');
        $pp_city = Tools::getValue('pp_place');
        $pp_wish = Tools::getValue('pp_wish');
        // $pp_cravings = Tools::getValue('cravings');

        if (!is_numeric($catIDs)) {
            $catIDs = WkMpSellerProductCategory::getCategoriesIdsFromNames($id_lang, [$catIDs]);
        }

        if ($catIDs) {
            if (in_array($catIDs, [16, 17, 18, 123, 19, 20, 21, 22, 23, 24, 25, 26])) {
                Tools::redirect($this->context->link->getCategoryLink((int)$catIDs));
            }
        }

        if($pp_wish == false) {
            $date_from = "";
            $date_to = "";
            $activities = $this->getActivitiesData($date_from, $date_to, 0, $pp_city, $catIDs, null);

            $this->defineJSVars();
            $this->context->smarty->assign(array(
                'mpseract_categories' => self::getSpecificCategories((int)  $this->context->language->id,
                    MpStoreLocator::getSearchEngineCategoriesIDs()),
                'MP_GEOLOCATION_API_KEY' => Configuration::get('MP_GEOLOCATION_API_KEY'),
                'participants' => range(1, 12),
                'activities' => $activities,
                'addr' => $pp_city,
                'cat_id' => $catIDs,
                'date_from' => $date_from,
                'date_to' => $date_to,
            ));
            //if ($pp_cravings == '1')
            //   Tools::redirect($this->context->link->getModuleLink('mpbooking', 'activitysearchresult'));
            // Tools::redirect($this->context->link->getModuleLink('store-sellerstores', 'modules/mpstorelocator/views/js/sellerstores.js'));
            // Tools::redirect($this->context->link->getModuleLink('marketplace', 'searchshops'));
        }

        if (!$catIDs && !$pp_city) {
            $seller_stores = WkMpSeller::getAllSellersByCategories($id_lang, [], null, 0, true, true);
        } else {
            $seller_stores = WkMpSeller::getAllSellersByCategories($id_lang, ($catIDs ? explode(',', $catIDs) : []), $pp_city, 0, false, true);
        }

        if (!$seller_stores) {
            $seller_stores = WkMpSeller::getAllSellersByCategories($id_lang, ($catIDs ? explode(',', $catIDs) : []), $pp_city, 0, true, true);
        }

        // $sellersCities = [$pp_city];
        $sellersCats = WkMpSellerProductCategory::getSimpleCategories($id_lang, MpStoreLocator::getSearchEngineCategoriesIDs(), 'category_shop.`position`');

        /*if ($seller_stores) {
            // get sellers cities
            if (!$pp_city) {
                foreach ($seller_stores as $store) {
                    $sellersCities[] = Tools::strtolower($store['city']);
                }
            }
            // get sellers categories
            if (!$sellersCats) {
                $sellers_ids = [];
                foreach ($seller_stores as $store) {
                    $sellers_ids[] = (int)$store['id_seller'];
                }
            }
        }*/

        Media::addJsDef([
            'storeLocationsJson' => Tools::jsonEncode($seller_stores),
            'bee_icon' => _THEME_IMG_DIR_ .'bee-activite-g4.svg',
            'pp_theme_default_opt_name' => 'Sélectionnez une thématique',
            'pp_theme_default_city_name' => 'Sélectionnez un lieu',
        ]);

        $this->context->smarty->assign(array(
            'store_locations' => $seller_stores,
            'title_text_color' => Configuration::get('MP_TITLE_TEXT_COLOR'),
            'title_bg_color' => Configuration::get('MP_TITLE_BG_COLOR'),
            'MP_GEOLOCATION_API_KEY' => Configuration::get('MP_GEOLOCATION_API_KEY'),
            'country' => $this->context->country,
            'cravings' => MpStoreLocator::getCravingsAndThemes(),
            'sel_pp_theme' => WkMpSellerProductCategory::getCategoryNameByID((int)Tools::getValue('pp_theme')),
            'sel_pp_place' => Tools::getValue('pp_place'),
            'sel_pp_wish' => Tools::getValue('pp_wish'),
            // 'sellers_cities' => $sellersCities,
            'sellers_cats' => $sellersCats,
            'sel_pp_wish' => Tools::getValue('pp_wish'),
        ));

        $this->defineJSVars();
        $this->setTemplate('module:marketplace/views/templates/front/seller/searchshops.tpl');
    }

    private function getActivitiesData($date_from = "", $date_to = "", $qty = 0, $addr = "", $id_category = 0, $activity_curious = "", $type = 'main', $distance = "", $latlong = "")
    {
        if (!Tools::isEmpty($date_from) && !Tools::isEmpty($date_to)) {
            list($fd, $fm, $fy) = explode('/', $date_from);
            list($td, $tm, $ty) = explode('/', $date_to);
            $date_from = $fy .'-'. $fm .'-'. $fd;
            $date_to = $ty .'-'. $tm .'-'. $td;
        } else {
            $date_from = date('Y-m-d', strtotime('+1 day'));
            // $date_to = date('Y-m-d', strtotime('+100 days'));
        }

        $sql = 'SELECT s.`id_seller`, s.`seller_firstname`, s.`profile_image`, sp.id_mp_product, sp.id_ps_product, btsp.date_from, btsp.date_to, btsp.price, bpi.activity_addr, bpi.latitude, bpi.longitude, s.city, bpi.quantity, bpi.activity_curious, bpi.activity_period, btsp.time_slot_from, btsp.time_slot_to, CONCAT(sp.id_ps_product, "-", spi.id_ps_image) as id_image, spl.`link_rewrite`, s.`link_rewrite` as shop_link_rewrite, spl.`product_name`';
        // TIMESTAMPDIFF(MINUTE, CONCAT(CURRENT_DATE(), " ", btsp.time_slot_from), CONCAT(CURRENT_DATE(), " ", btsp.time_slot_to)) as activity_period
        // if ($type == 'filter' && !Tools::isEmpty($latlong) && !Tools::isEmpty($distance)) {
             //list($lat, $long) = explode('#', $latlong);
            // $sql .= ", ( 6371 * acos( cos( radians($lat) ) * cos( radians( bpi.latitude ) ) * cos( radians( bpi.longitude ) - radians($long) ) + sin( radians($lat) ) * sin( radians( bpi.latitude ) ) ) ) as distance";
        //}

        $sql .= ' FROM `'._DB_PREFIX_.'wk_mp_seller` s
            LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_lang` sl ON (s.id_seller=sl.id_seller)
            LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_product` sp ON (s.id_seller=sp.id_seller)
            LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_product_lang` spl ON (sp.id_mp_product=spl.id_mp_product)
            LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_product_category` spc ON (sp.id_mp_product=spc.id_seller_product)
            LEFT JOIN `'._DB_PREFIX_.'wk_mp_booking_time_slots_prices` btsp ON (sp.id_mp_product=btsp.id_mp_product)
            LEFT JOIN `'._DB_PREFIX_.'wk_mp_booking_product_info` bpi ON (sp.id_mp_product=bpi.id_mp_product)
            LEFT JOIN `'._DB_PREFIX_.'wk_mp_seller_product_image` spi ON (sp.id_mp_product=spi.seller_product_id)
            WHERE spl.`id_lang` = '. (int)$this->context->language->id
            ." AND ('$date_from' >= btsp.date_from AND '$date_from' <= btsp.date_to)"
            .(!Tools::isEmpty($date_to) ? " AND btsp.date_to <= '$date_to'" : '')
            .' AND '. ($qty ? 'bpi.quantity >= '. (int)$qty : 'bpi.quantity >= 1')
            .((int)$id_category ? ' AND spc.id_category = '. (int)$id_category : '')
            .(!Tools::isEmpty($addr) ? " AND (s.city = '$addr' OR bpi.activity_addr LIKE '%$addr%')" : '')
            .(!Tools::isEmpty($activity_curious) ? " AND bpi.activity_curious LIKE '%$activity_curious%'" : '')
            .' GROUP BY sp.id_mp_product';

        //if ($type == 'filter' && !Tools::isEmpty($latlong) && !Tools::isEmpty($distance)) {
        //    list($d_from, $d_to) = explode('-', $distance);
         //   $sql .= " HAVING distance BETWEEN ". (int)$d_from ." AND ". (int)$d_to ." ORDER BY distance";
        //}

        // if ($type == 'main') {
        // echo $sql; die();
        // }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return array();
        }

        $link = $this->context->link;
        foreach ($result as &$res) {
            $res['seller_job'] = WkMpSeller::getSellerJob($res['id_seller']);
            $res['seller_image'] = WkMpSeller::getSellerImageLink($res);

            // if ($res['activity_period'] >= 60) {
            //     $expTime = explode('.', Tools::ps_round($res['activity_period']/60));
            //     $res['activity_period'] = $expTime[0] .'h'. (isset($expTime[1]) ? $expTime[1] : '00');
            // } else {
            //     $res['activity_period'] = $res['activity_period'] .'min';
            // }
            switch ($res['activity_period']) {
                case '< 15 minutes':
                    $res['activity_period'] = '<15 min';
                    break;
                case '30 minutes':
                    $res['activity_period'] = '30 min';
                    break;
                case '1 heure':
                    $res['activity_period'] = '1h00';
                    break;
                case '1h30':
                    $res['activity_period'] = '1h30';
                    break;
                case '> 2h':
                    $res['activity_period'] = '2h00';
                    break;
                default:
                    $res['activity_period'] = 'N/A';
                    break;
            }

            $res['store_det_url'] = $link->getProductLink(new Product((int)$res['id_ps_product']), $res['link_rewrite']);
            $res['store_act_image'] = $link->getImageLink($res['link_rewrite'], $res['id_image'], 'booking_home_default');
        }
        unset($res);

        return $result;
    }

    private static function getSpecificCategories($idLang, array $id_cats)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT c.`id_category`, cl.`name`
            FROM `'._DB_PREFIX_.'category` c
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').')
            '.Shop::addSqlAssociation('category', 'c').'
            WHERE cl.`id_lang` = '.(int) $idLang.'
            AND c.`id_category` IN ('. implode(',', array_map('intval', $id_cats)) .')
            GROUP BY c.id_category
            ORDER BY c.`id_category`, category_shop.`position`');
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        // Register JS
        $this->registerJavascript('store-sellerstores', 'modules/mpstorelocator/views/js/sellerstores.js', [
            'priority' => 250,
            'position' => 'bottom',
        ]);
        $this->registerJavascript('chosen-jquery', 'js/jquery/plugins/jquery.chosen.js', ['priority' => 100,'position' => 'bottom']);

        // Register CSS
        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerStylesheet('store-details', 'modules/mpstorelocator/views/css/store_details.css');
        $this->registerStylesheet('chosen-jquery', 'themes/beeshary/assets/css/jquery.chosen.css');
    }

    public function defineJSVars()
    {
        $jsVars = [
            'url_getstorebycategory' => $this->context->link->getModulelink('marketplace', 'getstorebycategory'),
            'se_popup' => true,
            'mpstore_ajax_url' => $this->context->link->getModulelink('mpstorelocator', 'ajax'),
            'no_store_msg' => $this->trans('No store found', [], 'Modules.MpStoreLocator'),
        ];
        return Media::addJsDef($jsVars);
    }
}