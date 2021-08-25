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

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

include_once(dirname(__FILE__).'/leofeature.php');
include_once(dirname(__FILE__).'/classes/LeofeatureProduct.php');
require_once(_PS_MODULE_DIR_.'mpbooking/classes/WkMpBookingProductInformation.php');
$module = new Leofeature();

if ((!$module->isTokenValid() || !Tools::getValue('action')) && Tools::getValue('action') != 'get-new-review') {
    // Ooops! Token is not valid!
    // die('Token is not valid, hack stop');
    $result = '';
    die($result);
}

//DONGND:: render modal popup and dropdown cart
if (Tools::getValue('action') == 'render-modal') {
    $context = Context::getContext();
    $modal = '';
    $notification = '';
    $dropdown = '';
    if (Tools::getValue('only_dropdown') == 0) {
        $modal = $module->renderModal();
        $notification = $module->renderNotification();
    }
    if (Configuration::get('LEOFEATURE_ENABLE_DROPDOWN_DEFAULTCART') || Configuration::get('LEOFEATURE_ENABLE_DROPDOWN_FLYCART')) {
        $only_total = Tools::getValue('only_total');
        $dropdown = $module->renderDropDown($only_total);
    }

    ob_end_clean();
    header('Content-Type: application/json');
    die(Tools::jsonEncode(array(
        'dropdown' => $dropdown,
        'modal'   => $modal,
        'notification' => $notification,
    )));
}

if (Tools::getValue('action') == 'get-attribute-data') {
    $result = array();
    $context = Context::getContext();
    $id_product = Tools::getValue('id_product');
    $id_product_attribute = Tools::getValue('id_product_attribute');

    $attribute_data = new LeofeatureProduct();
    $result = $attribute_data->getTemplateVarProduct2($id_product, $id_product_attribute);
    die(Tools::jsonEncode(array(
        'product_cover' => $result['cover'],
        'price_attribute'   => $module->renderPriceAttribute($result),
        'product_url' => $context->link->getProductLink($id_product, null, null, null, $context->language->id, null, $id_product_attribute, false, false, true),
    )));
}

if (Tools::getValue('action') == 'get-new-review') {
    // $result = array();
    if (Configuration::get('LEOFEATURE_PRODUCT_REVIEWS_MODERATE')) {
        $reviews = ProductReview::getByValidate(0, false);
    } else {
        $reviews = array();
    }

    die(Tools::jsonEncode(array(
        'number_review' => count($reviews)
    )));
}

if (Tools::getValue('action') == 'check-product-outstock') {
    $id_product = Tools::getValue('id_product');
    $id_product_attribute = Tools::getValue('id_product_attribute');
    $id_customization = Tools::getValue('id_customization');
    $check_product_in_cart = Tools::getValue('check_product_in_cart');
    $quantity = Tools::getValue('quantity');
    $context = Context::getContext();
    $qty_to_check = $quantity;
    // print_r('test111');
    if ($check_product_in_cart == 'true') {
        $cart_products = $context->cart->getProducts();

        if (is_array($cart_products)) {
            foreach ($cart_products as $cart_product) {
                if ((!isset($id_product_attribute) || ($cart_product['id_product_attribute'] == $id_product_attribute && $cart_product['id_customization'] == $id_customization )) && isset($id_product) && $cart_product['id_product'] == $id_product) {
                    $qty_to_check = $cart_product['cart_quantity'];
                    $qty_to_check += $quantity;
                    break;
                }
            }
        }
    }


    $product = new Product($id_product, true, $context->language->id);
    $return = true;
    // Check product quantity availability
    if ($id_product_attribute) {
        if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !Attribute::checkAttributeQty($id_product_attribute, $qty_to_check)) {
            $return = false;
        }
    } elseif (!$product->checkQty($qty_to_check)) {
        $return = false;
    }
	$objBookingProductInfo = new WkMpBookingProductInformation();
	if( $objBookingProductInfo->isBookingProductStatic($id_product) ){
		$return = true;
	}

    die(Tools::jsonEncode(array(
        'success' => $return,
    )));
}
