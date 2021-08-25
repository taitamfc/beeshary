<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

require_once(_PS_MODULE_DIR_.'leofeature/classes/WishList.php');
require_once(_PS_MODULE_DIR_.'leofeature/classes/LeofeatureProduct.php');

class LeoFeatureViewWishlistModuleFrontController extends ModuleFrontController
{
    public $php_self;

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    public function initContent()
    {
        $this->php_self = 'viewwishlist';

        parent::initContent();
        if (!Configuration::get('LEOFEATURE_ENABLE_PRODUCTWISHLIST')) {
            return Tools::redirect('index.php?controller=404');
        }
        $token = Tools::getValue('token');

        if ($token) {
            $wishlist = WishList::getByToken($token);
            $wishlists = WishList::getByIdCustomer((int)$wishlist['id_customer']);
            if (count($wishlists) > 1) {
                foreach ($wishlists as $key => $wishlists_item) {
                    if ($wishlists_item['id_wishlist'] == $wishlist['id_wishlist']) {
                        unset($wishlists[$key]);
                    }
                }
            } else {
                $wishlists = array();
            }

            $products = array();
            $wishlist_product = WishList::getSimpleProductByIdWishlist((int)$wishlist['id_wishlist']);
            $product_object = new LeofeatureProduct();
            if (count($wishlist_product) > 0) {
                foreach ($wishlist_product as $wishlist_product_item) {
                    $list_product_tmp = array();
                    $list_product_tmp['wishlist_info'] = $wishlist_product_item;
                    $list_product_tmp['product_info'] = $product_object->getTemplateVarProduct2($wishlist_product_item['id_product'], $wishlist_product_item['id_product_attribute']);
                    $list_product_tmp['product_info']['wishlist_quantity'] = $wishlist_product_item['quantity'];
                    $products[] = $list_product_tmp;
                }
            }
            WishList::incCounter((int)$wishlist['id_wishlist']);
            $this->context->smarty->assign(
                array(
                    'current_wishlist' => $wishlist,
                    'wishlists' => $wishlists,
                    'products' => $products,
                    'view_wishlist_url' => $this->context->link->getModuleLink('leofeature', 'viewwishlist'),
                    'show_button_cart' => Configuration::get('LEOFEATURE_ENABLE_AJAXCART'),
                    'leo_is_rewrite_active' => (bool)Configuration::get('PS_REWRITING_SETTINGS'),
                )
            );
        }
        $this->setTemplate('module:leofeature/views/templates/front/leo_wishlist_view.tpl');
    }

    //DONGND:: add meta title, meta description, meta keywords
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        $page['meta']['title'] = Configuration::get('PS_SHOP_NAME').' - '.$this->l('View Wishlist', 'viewwishlist');
                $page['meta']['keywords'] = $this->l('view-wishlist', 'viewwishlist');
                $page['meta']['description'] = $this->l('view Wishlist', 'viewwishlist');
        return $page;
    }

    //DONGND:: add breadcrumb
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->l('My Account', 'viewwishlist'),
            'url' => $this->context->link->getPageLink('my-account', true),
        );

        $breadcrumb['links'][] = array(
            'title' => $this->l('My Wishlist', 'viewwishlist'),
            'url' => $this->context->link->getModuleLink('leofeature', 'mywishlist'),
        );

        return $breadcrumb;
    }

    //DONGND:: get layout
    public function getLayout()
    {
        $entity = 'module-leofeature-'.$this->php_self;
        $layout = $this->context->shop->theme->getLayoutRelativePathForPage($entity);
        if ($overridden_layout = Hook::exec('overrideLayoutTemplate', array(
                'default_layout' => $layout,
                'entity' => $entity,
                'locale' => $this->context->language->locale,
                'controller' => $this,
            ))) {
            return $overridden_layout;
        }

        if ((int) Tools::getValue('content_only')) {
            $layout = 'layouts/layout-content-only.tpl';
        }
        return $layout;
    }
}
