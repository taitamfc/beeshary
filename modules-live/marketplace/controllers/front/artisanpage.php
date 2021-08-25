<?php
/**
*  2017-2018 PHPIST.
*
*  @author    Yassine Belkaid <yassine.belkaid87@gmail.com>
*  @copyright 2017-2018 PHPIST
*  @license   https://store.webkul.com/license.html
*/

class MarketplaceArtisanPageModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        if (!($artisan_name = Tools::getValue('artisan_name')) || !($shop_name = Tools::getValue('shop_name'))) {
            Tools::redirect($this->context->link->getPageLink('pagenotfound'));
        }

        $id_seller = (int)Db::getInstance()->getValue('SELECT `id_seller` FROM `'. _DB_PREFIX_ .'wk_mp_seller` WHERE `link_rewrite` = "'. pSQL($shop_name) .'" AND `seller_firstname` = "'. pSQL($artisan_name) .'"');
        if (!$id_seller) {
            $mpSeller = WkMpSeller::getSellerByLinkRewrite($shop_name);
            $id_seller = (int)$mpSeller['id_seller'];

            if (!$id_seller) {
                Tools::redirect($this->context->link->getPageLink('pagenotfound'));
            }
        }

        $id_lang  = (int)$this->context->language->id;
        $mpSeller = WkMpSeller::getSeller($id_seller, $id_lang, true, true);

        if (!$mpSeller || !is_array($mpSeller)) {
            Tools::redirect($this->context->link->getPageLink('pagenotfound'));
        }

        $seller_proverb = Db::getInstance()->getRow('SELECT id, field_value FROM `'. _DB_PREFIX_ .'marketplace_extrafield_value` WHERE `extrafield_id` = 4 AND `mp_id_seller` = '. (int)$id_seller);
        if ($seller_proverb['field_value'] == "") {
            $mpSeller['seller_proverb'] = Db::getInstance()->getValue('SELECT field_val FROM `'. _DB_PREFIX_ .'marketplace_extrafield_value_lang` WHERE `id_lang` = 1 AND `id` = '. (int)$seller_proverb['id']);
        } else {
            $mpSeller['seller_proverb'] = $seller_proverb['field_value'];
        }

        $seller_passion = Db::getInstance()->getRow('SELECT id, field_value FROM `'. _DB_PREFIX_ .'marketplace_extrafield_value` WHERE `extrafield_id` = 3 AND `mp_id_seller` = '. (int)$id_seller);
        if ($seller_passion['field_value'] == "") {
            $mpSeller['seller_passion'] = Db::getInstance()->getValue('SELECT field_val FROM `'. _DB_PREFIX_ .'marketplace_extrafield_value_lang` WHERE `id_lang` = 1 AND `id` = '. (int)$seller_passion['id']);
        } else {
            $mpSeller['seller_passion'] = $seller_passion['field_value'];
        }

        $seller_identity = Db::getInstance()->getRow('SELECT id, field_value FROM `'. _DB_PREFIX_ .'marketplace_extrafield_value` WHERE `extrafield_id` = 2 AND `mp_id_seller` = '. (int)$id_seller);
        if ($seller_identity['field_value'] == "") {
            $mpSeller['seller_identity'] = Db::getInstance()->getValue('SELECT field_val FROM `'. _DB_PREFIX_ .'marketplace_extrafield_value_lang` WHERE `id_lang` = 1 AND `id` = '. (int)$seller_identity['id']);
        } else {
            $mpSeller['seller_identity'] = $seller_identity['field_value'];
        }

        $mpProducts = WkMpSellerProduct::getSellerProductWithPs($id_seller, true, 1, false, false, 5, 13);

        if ($mpProducts) {
            foreach ($mpProducts as &$_product) {
                $product = new Product($_product['id_ps_product'], true, $this->context->language->id);
                $_product['price'] = Tools::displayPrice($product->getPrice(true));

                if ($_product['image']) {
                    $_product['mp_img_path'] = $this->context->link->getImageLink($_product['link_rewrite'], $_product['image'], 'cart_default');
                } else {
                    $_product['mp_img_path'] = _THEME_IMG_DIR_ .'banner-bg.jpg';
                }
            }
            unset($_product, $product);
        }

        // Set seller profile image
        $this->setLeftImageBlock($mpSeller);

        //Check if seller banner exist
        $shopBannerPath = WkMpSeller::getShopBannerLink($mpSeller, false);
        if ($shopBannerPath) {
            $this->context->smarty->assign('shop_banner_path', $shopBannerPath);
        }

        if ($mpSeller['id_country']) {
            $mpSeller['country'] = Country::getNameById($this->context->language->id, $mpSeller['id_country']);
        }
        if ($mpSeller['id_state']) {
            $mpSeller['state'] = State::getNameById($mpSeller['id_state']);
        }

        $sellerLang = new Language((int)$mpSeller['default_lang']);
        $mpSeller['seller_lang'] = $sellerLang->name;
        $activities = $this->getActivities($id_seller, $id_lang);

        $this->context->smarty->assign(array(
            'mp_seller' => $mpSeller,
            'name_shop' => $mpSeller['link_rewrite'],
            'seller_id' => $id_seller,
            'activities' => $activities,
            'mp_products' => $mpProducts,
            'mp_customer' => new Customer((int)$mpSeller['seller_customer_id']),
            'timestamp' => time(),
        ));
        // Assign the seller details view vars
        WkMpSeller::checkSellerAccessPermission($mpSeller['seller_details_access']);
        
        $this->defineJSVars();
        $this->setTemplate('module:marketplace/views/templates/front/seller/artisan_page.tpl');
    }

    public function setLeftImageBlock($mpSeller)
    {
        $this->context->smarty->assign('seller_img_path', WkMpSeller::getSellerImageLink($mpSeller));
        // if ($mpSellerProfileImage && file_exists(_PS_MODULE_DIR_.$this->module->name.'/views/img/seller_img/'.$mpSellerProfileImage)) {
        //     $this->context->smarty->assign('seller_img_path', _MODULE_DIR_.$this->module->name.'/views/img/seller_img/'.$mpSellerProfileImage);
        //     $this->context->smarty->assign('seller_img_exist', 1);
        // } else {
        //     $this->context->smarty->assign('seller_img_path', _MODULE_DIR_.$this->module->name.'/views/img/seller_img/defaultimage.jpg');
        // }
    }

    public function defineJSVars()
    {
        $jsVars = array(
                'logged' => $this->context->customer->isLogged(),
                'moduledir' => _MODULE_DIR_,
                'mp_image_dir' => _MODULE_DIR_.'marketplace/views/img/',
                'contact_seller_ajax_link' => $this->context->link->getModuleLink('marketplace', 'contactsellerprocess'),
                'confirm_msg' => $this->module->l('Are you sure?', 'sellerprofile'),
                'email_req' => $this->module->l('Email is required field.'),
                'invalid_email' => $this->module->l('Email is not valid.'),
                'subject_req' => $this->module->l('Subject is required field.'),
                'description_req' => $this->module->l('Description is required field.'),
            );
            
        Media::addJsDef($jsVars);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->registerStylesheet('store_profile-css', 'modules/'.$this->module->name.'/views/css/store_profile.css');

        $this->registerJavascript('sellerprofile-js', 'modules/'.$this->module->name.'/views/js/sellerprofile.js');
        $this->registerJavascript('imageedit-js', 'modules/'.$this->module->name.'/views/js/imageedit.js');
        $this->registerJavascript('productsellerdetails-js', 'modules/'.$this->module->name.'/views/js/productsellerdetails.js');
		

        // bxslider removed in PS V1.7
        $this->registerJavascript('bxslider', 'modules/'.$this->module->name.'/views/js/jquery.bxslider.js');

        $this->registerJavascript('mp-jquery-raty-min', 'modules/'.$this->module->name.'/libs/rateit/lib/jquery.raty.min.js');

        // mp product slider
        $this->registerStylesheet('ps_gray', 'modules/'.$this->module->name.'/views/css/product_slider_pager/ps_gray.css');
        $this->registerJavascript('mp_product_slider-js', 'modules/'.$this->module->name.'/views/js/mp_product_slider.js');
    }

    private function getActivities($id_seller, $id_lang)
    {
        $activities = Db::getInstance()->executeS('SELECT bpi.*, spl.description, spl.short_description, spl.product_name, spl.link_rewrite, spi.seller_product_image_name as image
            FROM `'. _DB_PREFIX_ .'wk_mp_booking_product_info` bpi 
            LEFT JOIN `'. _DB_PREFIX_ .'wk_mp_seller_product` sp ON (bpi.id_mp_product = sp.id_mp_product)
            LEFT JOIN `'. _DB_PREFIX_ .'wk_mp_seller_product_lang` spl ON (sp.id_mp_product = spl.id_mp_product)
            LEFT JOIN `'. _DB_PREFIX_ .'wk_mp_seller_product_image` spi ON (sp.id_mp_product = spi.seller_product_id)
            WHERE bpi.id_seller = '. (int)$id_seller .' AND sp.active = 1 AND sp.id_category = 13 AND spl.id_lang = '. $id_lang .' GROUP BY bpi.id_mp_product ORDER BY id DESC LIMIT 2');

        if (!$activities) {
            return array();
        }

        foreach ($activities as &$activity) {
            if (file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/product_img/'. $activity['image']) && is_file(_PS_MODULE_DIR_ . 'marketplace/views/img/product_img/'. $activity['image'])) {
                $activity['activity_image'] = 'marketplace/views/img/product_img/'. $activity['image'];
            } else {
                $activity['activity_image'] = false;
            }
            $activity['activity_url'] = $this->context->link->getProductLink($activity['id_product']); 
        }

        unset($activity);
        return $activities;
    }
}
