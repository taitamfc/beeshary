<?php
/**
 * 2007-2015 Apollotheme
 *
 * NOTICE OF LICENSE
 *
 * ApPageBuilder is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Apollotheme <apollotheme@gmail.com>
 *  @copyright 2007-2015 Apollotheme
 *  @license   http://apollotheme.com - prestashop template provider
 */
abstract class ProductListingFrontController extends ProductListingFrontControllerCore
{
    /**
     * Override
     * Category page show shortcode at product list
     */
//    protected function prepareMultipleProductsForTemplate(array $products)
//    {
//        if ((bool)Module::isEnabled('appagebuilder')) {
//            foreach ($products as &$product) {
//                $appagebuilder = Module::getInstanceByName('appagebuilder');
//                $product['description'] = $appagebuilder->buildShortCode($product['description']);
//                $product['description_short'] = $appagebuilder->buildShortCode($product['description_short']);
//            }
//        }
//        
//        return parent::prepareMultipleProductsForTemplate($products);
//    }
}
