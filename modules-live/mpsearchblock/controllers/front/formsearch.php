<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MpSearchBlockFormSearchModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Advance Search', array(), 'Breadcrumb'),
            'url' => ''
        ];
        return $breadcrumb;
    }

    public function initContent()
    {
        $id_lang = $this->context->cookie->id_lang;
        $obj_sbhelper = new SearchBlockHelperClass();
        $count_result = 0;

        $key = Tools::getValue('top_search_box');
        $category = (int) Tools::getValue('search_type');
        $sort_by = (int) Tools::getValue('sort_by');

        if (empty($key)) {
            $this->context->smarty->assign('error', 1);
        } else {
            if ($category == 2 || $category == 1) {
                $product = $obj_sbhelper->getMpProductDetail($key, $sort_by);
                if ($product) {
                    foreach ($product as &$product_value) {
                        $product_obj = new Product($product_value['id_product'], false, $id_lang);
                        $cover_image_id = Product::getCover($product_obj->id);

                        if ($cover_image_id) {
                            $mp_product_img = $this->context->link->getImageLink($product_obj->link_rewrite, $product_obj->id.'-'.$cover_image_id['id_image'], 'home_default');
                        } else {
                            $mp_product_img = _THEME_PROD_DIR_.$this->context->language->iso_code.'.jpg';
                        }

                        $product_value['price'] = Tools::displayPrice($product_obj->getPrice());
                        $product_value['mp_product_img'] = $mp_product_img;
                    }
                    $count_result += count($product);
                    $this->context->smarty->assign('ps_product', $product);
                }
            }

            if ($category == 3 || $category == 1) {
                $shop = $obj_sbhelper->getMpShopDetail($key, $sort_by);
                if ($shop) {
                    foreach ($shop as &$shop_value) {
                        if (!$shop_value['shop_image']) {
                            $shop_img = _MODULE_DIR_.'marketplace/views/img/shop_img/defaultshopimage.jpg';
                        } else {
                            $shop_img = _MODULE_DIR_.'marketplace/views/img/shop_img/'.$shop_value['shop_image'];
                        }

                        $shop_value['mp_shop_img'] = $shop_img;
                    }
                    $count_result += count($shop);
                    $this->context->smarty->assign('mp_shop', $shop);
                }
            }

            if ($category == 4 || $category == 1) {
                $seller = $obj_sbhelper->getMpSellerDetail($key, $sort_by);
                if ($seller) {
                    foreach ($seller as &$seller_value) {
                        if ($seller_value['profile_image']) {
                            $seller_img = _MODULE_DIR_.'marketplace/views/img/seller_img/'.$seller_value['profile_image'];
                        } else {
                            $seller_img = _MODULE_DIR_.'marketplace/views/img/seller_img/defaultimage.jpg';
                        }
                        $seller_value['mp_seller_img'] = $seller_img;
                    }
                    $count_result += count($seller);
                    $this->context->smarty->assign('seller', $seller);
                }
            }

            if ($category == 5 || $category == 1) {
                $shop_locat = $obj_sbhelper->getMpShopLocationDetail($key, $sort_by);
                if ($shop_locat) {
                    foreach ($shop_locat as &$locat_value) {
                        if ($locat_value['shop_image']) {
                            $shop_locat_img = _MODULE_DIR_.'marketplace/views/img/shop_img/'.$locat_value['shop_image'];
                        } else {
                            $shop_locat_img = _MODULE_DIR_.'marketplace/views/img/shop_img/defaultshopimage.jpg';
                        }
                        $locat_value['mp_shop_img'] = $shop_locat_img;
                    }
                    $count_result += count($shop_locat);
                    $this->context->smarty->assign('shop_locat', $shop_locat);
                }
            }

            if ($category == 6 || $category == 1) {
                $category_detail = $obj_sbhelper->getPsCategoryDetail($key, $sort_by);
                if ($category_detail) {
                    $count_result += count($category_detail);
                    $this->context->smarty->assign('category_detail', $category_detail);
                }
            }

            $this->context->smarty->assign('category', $category);
            $this->context->smarty->assign('key', $key);
            if ($sort_by) {
                $this->context->smarty->assign('sortBy', $sort_by);
            }

            if ($count_result == 0) {
                $this->context->smarty->assign('error', 2);
            }
        }

        $this->context->smarty->assign('count_result', $count_result);
        $this->setTemplate('module:'.$this->module->name.'/views/templates/front/search_result.tpl');

        parent::initContent();
    }
}
