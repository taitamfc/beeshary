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

class ProductController extends ProductControllerCore
{
    public function getTemplateVarProduct()
    {
        $product = parent::getTemplateVarProduct();

        if ((bool)Module::isEnabled('appagebuilder')) {
            $appagebuilder 					= Module::getInstanceByName('appagebuilder');
            $product['description'] 		= $appagebuilder->buildShortCode($product['description']);
            $product['description_short'] 	= $appagebuilder->buildShortCode($product['description_short']);
            $product['category_name'] 		= 'test';
			
			echo '<pre>';
			print_r($product);
			die();
        }

        return $product;
    }
}
