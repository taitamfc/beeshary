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

class marketplaceGetStorebyCategoryModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        $this->display_header = false;
        $this->display_footer = false;
    }

    public function initContent()
    {
        $themes = Tools::getValue('themes', null);
        $seller_city = Tools::getValue('seller_city', null);
        $action = Tools::getValue('action', null);
        $id_lang = $this->context->language->id;
        $theme_stores = [];
        $item_el = 'li';

        if ($action && $action == 'filterByThemeByCity') {
            $item_el = 'div';
            if (!$themes) {
                $themes = MpStoreLocator::getSearchEngineCategoriesIDs();
            }

            if (!is_array($themes)) {
                $themes = [$themes];
            }

            $theme_stores = WkMpSeller::getAllSellersByCategories($id_lang, $themes, $seller_city, 36);

            if (!$theme_stores) {
                $theme_stores = WkMpSeller::getAllSellersByCategories($id_lang, $themes, $seller_city, 36, true);
            }
        } else {
            if (Tools::isEmpty($themes)) {
                $theme_stores = WkMpSeller::getLatestSellers($id_lang, 0, 6);
            } else {
                $catIDs = WkMpSellerProductCategory::getCategoriesIdsFromNames($id_lang, $themes);
                $theme_stores = WkMpSeller::getAllSellersByCategories($id_lang, explode(',', $catIDs), null, 6);
            }
        }

        if (isset($theme_stores) && $theme_stores) {
            $this->assignSmartyVar($theme_stores, $item_el);
        } else {
            // if no details found
           die(false);
        }
    }

    public function assignSmartyVar($stores, $item_el = 'li')
    {
        if ($stores) {
            $link = $this->context->link;
            $_html = '';

            foreach ($stores as $store) {
                $_html .= '<'. $item_el .' class="home_store" data-is-banner="true" data-shop-banner="'. $store['shop_banner'] .'">
                    <a href="'.$store['store_det_url'].'">
                        <div class="media craft-shop-media">
                            <div class="media-left media-middle">';
                                $_html .= '<img class="img-circle" src="'. $store['seller_image'] .'" />';
                        $_html .= '</div>
                            <div class="media-body">
                                <h3>'. Tools::ucfirst($store['shop_name']) .'</h3>
                                <h4>'. Tools::ucfirst($store['seller_firstname']) .', '. Tools::ucfirst($store['seller_job']) .'</h4>
                                <p>'. Tools::ucfirst($store['city']) .($store['post_code'] ? ' ('. substr($store['post_code'], 0, 2) .')' : '') .'</p>
                            </div>
                        </div>
                        <div class="media-corner"><img src="'. _THEME_IMG_DIR_ .'media-corner.jpg" alt="" /></div>
                    </a>
                </'. $item_el .'>';
            }

            $resonse = array('allStoreTpl' => $_html, 'allstore' => $stores);
            die(Tools::jsonEncode($resonse));
        }
    }
}
