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

class MarketplaceShopCollectionModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $shopLinkRewrite = Tools::getValue('mp_shop_name');
        $idCategory = Tools::getValue('id_category');

        $mpProduct = new SellerProductDetail();
        $mpSeller = new SellerInfoDetail();

        $seller = $mpSeller->getSellerDetailsByLinkRewrite($shopLinkRewrite, $this->context->language->id);

        if ($seller) {
            $orderBy = Tools::getValue('orderby');
            $orderWay = Tools::getValue('orderway');
            $p = Tools::getValue('p');
            $n = Configuration::get('PS_PRODUCTS_PER_PAGE');

            // default page number
            if (!$p) {
                $p = 1;
            }

            // default orderby and orderway
            if (!$orderBy) {
                $orderBy = 'id';
            }

            if (!$orderWay) {
                $orderWay = 'desc';
            }

            // for creating pagination
            $activeProduct = $mpProduct->findAllActiveProductInMarketPlaceShop($seller['id'], $orderBy, $orderWay, $this->context->language->id);

            if ($activeProduct) {
                $paginationProducts = $activeProduct;
                if ($idCategory) {
                    foreach ($paginationProducts as $key => $product) {
                        $obj_prod = new Product($product['id_ps_product'], false, $this->context->language->id);
                        $catgs = $obj_prod->getCategories();
                        if (!in_array($idCategory, $catgs)) {
                            unset($paginationProducts[$key]);
                        }
                    }
                }
            }

            // mpShopProduct have product according filter while
            // activeProduct have all product of current seller for category list display
            if ($idCategory) {
                $mpShopProduct = $this->getMpProductByCategory($idCategory, $activeProduct);
            } else {
                $mpShopProduct = $activeProduct;
            }

            if ($orderBy == 'price' && $mpShopProduct) { // sort product by price
                $mpShopProduct = $this->sortProductsByPrice($mpShopProduct, $orderWay);
            }

            $productCount = count($mpShopProduct);

            // get products by page
            $mpShopProduct = $this->filterProductsByPage($mpShopProduct, $p, $n);

            if ($mpShopProduct) {
                //get category details
                if ($activeProduct) {
                    $catg_details = $this->getMpProductCategoryCount($activeProduct);
                    if ($catg_details) {
                        $this->context->smarty->assign('catg_details', $catg_details);
                    }
                }

                //Product array by category or by default
                foreach ($mpShopProduct as &$shopProduct) {
                    $product = new Product($shopProduct['id_ps_product'], true, $this->context->language->id);
                    if ($cover = Product::getCover($shopProduct['id_ps_product'])) {
                        $shopProduct['image'] = $shopProduct['id_ps_product'].'-'.$cover['id_image'];
                    } else {
                        $shopProduct['image'] = null;
                    }

                    $shopProduct['qty_available'] = StockAvailable::getQuantityAvailableByProduct($shopProduct['id_ps_product']);
                    $shopProduct['product'] = $product;
                    $shopProduct['link'] = $this->context->link->getProductLink($product);
                    $shopProduct['product_name'] = $product->name;
                    $shopProduct['lang_iso'] = $this->context->language->iso_code;
                    $shopProduct['link_rewrite'] = $product->link_rewrite;
                    $shopProduct['available_for_order'] = $product->available_for_order;
                    $shopProduct['show_price'] = $product->show_price;
                    $shopProduct['price'] = Tools::displayPrice($product->price);

                    // check if http url exist by getimagesize
                    $productImageURL = $this->context->link->getImageLink($shopProduct['link_rewrite'], $shopProduct['image'], 'home_default');
                    if (@getimagesize($productImageURL)) {
                        $shopProduct['image_url'] = $productImageURL;
                    } else {
                        $shopProduct['image_url'] = _MODULE_DIR_.'/marketplace/views/img/home-default.jpg';
                    }
                }

                $this->context->smarty->assign('mp_shop_product', $mpShopProduct);

                // Define JS Vars
                if ($idCategory = Tools::getValue('id_category')) {
                    Media::addJSDef(['requestSortProducts' => $this->context->link->getModuleLink(
                        'marketplace', 'shopcollection', ['mp_shop_name' => $shopLinkRewrite,
                        'id_category' => (int) $idCategory])]);
                } else {
                    Media::addJSDef(['requestSortProducts' => $this->context->link->getModuleLink('marketplace',
                        'shopcollection', ['mp_shop_name' => $shopLinkRewrite])]);
                }
            }

            $this->context->smarty->assign([
                'logged' => $this->context->customer->isLogged(),
                'link' => $this->context->link,
                'PS_CATALOG_MODE' => Configuration::get('PS_CATALOG_MODE'),
                'name_shop' => $shopLinkRewrite,
                'p' => $p,
                'n' => $n,
                'currentProductCount' => count($mpShopProduct),
                'defaultorederby' => 'id',
                'orderby' => $orderBy,
                'orderway' => $orderWay,
                'nb_products' => $productCount,
                'page_count' => (int) ceil($productCount/$n),
                'static_token' => Tools::getToken(false),
                'cart_page_url' => $this->context->link->getPageLink('cart', true),
            ]);

            $this->setTemplate('module:marketplace/views/templates/front/shop/shopcollection.tpl');
        }
    }

    public function filterProductsByPage($mpShopProduct, $p, $n)
    {
        $result = array();
        $start = ($p - 1) * $n;
        $end = $start + $n;
        for($i = $start; $i < $end; $i++) {
            if (array_key_exists($i, $mpShopProduct)) {
                $result[] = $mpShopProduct[$i];
            }
        }

        return $result;
    }

    public function sortProductsByPrice($mpShopProduct, $orderWay)
    {
        // get all product price
        foreach ($mpShopProduct as &$product) {
            $product['price'] = Product::getPriceStatic($product['id_ps_product']);
        }

        // use bubble sort to sort product by price
        $noOfProducts = (count($mpShopProduct)-1);
        for ($i = 0; $i < $noOfProducts; $i++) {
            for ($j = 0; $j < ($noOfProducts-$i); $j++) {
                if ($orderWay == 'desc') {
                    if ($mpShopProduct[$j]['price'] < $mpShopProduct[$j+1]['price']) {
                        $tempProduct = $mpShopProduct[$j];
                        $mpShopProduct[$j] = $mpShopProduct[$j+1];
                        $mpShopProduct[$j+1] = $tempProduct;
                    }
                } else {
                    if ($mpShopProduct[$j]['price'] > $mpShopProduct[$j+1]['price']) {
                        $tempProduct = $mpShopProduct[$j+1];
                        $mpShopProduct[$j+1] = $mpShopProduct[$j];
                        $mpShopProduct[$j] = $tempProduct;
                    }
                }
            }
        }

        return $mpShopProduct;
    }

    public function getMpProductCategoryCount($mpProduct)
    {
        $mpCategory = array();
        if ($mpProduct) {
            foreach ($mpProduct as $p) {
                if ($p['active']) {
                    $product = new Product($p['id_ps_product'], false, $this->context->language->id);
                    $categories = $product->getCategories();
                    foreach ($categories as $catg) {
                        $category = new Category($catg, $this->context->language->id);
                        if (!array_key_exists($catg, $mpCategory)) {
                            if ($catg != Category::getRootCategory()->id) {
                                $mpCategory[$catg] = array(
                                    'id_category' => $catg,
                                    'Name' => $category->name,
                                    'NoOfProduct' => 1,
                                );
                            }
                        } else {
                            $mpCategory[$catg]['NoOfProduct'] += 1;
                        }
                    }
                }
            }
        }

        if ($mpCategory) {
            return $mpCategory;
        }

        return false;
    }

    public function getMpProductByCategory($idCategory, $activeProduct)
    {
        if ($activeProduct) {
            foreach ($activeProduct as $key => $mpProduct) {
                $product = new Product($mpProduct['id_ps_product'], false, $this->context->language->id);
                $catgs = $product->getCategories();
                if (!in_array($idCategory, $catgs)) {
                    unset($activeProduct[$key]);
                }
            }
        }

        return array_values($activeProduct);
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Marketplace', [], 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard')
        ];
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Collection', [], 'Breadcrumb'),
            'url' => ''
        ];
        return $breadcrumb;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->registerStylesheet('shop_collection', 'modules/'.$this->module->name.'/views/css/shop_collection.css');
        $this->registerJavascript('shop_collection', 'modules/'.$this->module->name.'/views/js/shop_collection.js');
    }
}
