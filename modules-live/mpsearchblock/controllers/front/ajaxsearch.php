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

class MpSearchBlockAjaxSearchModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        // parent::initContent();
        $this->display_header = false;
        $this->display_footer = false;

        $obj_sbhelper = new SearchBlockHelperClass();

        $key = Tools::getValue('key');
        $search_type = (int) Tools::getValue('search_type');
        $flag = (int) Tools::getValue('flag');

        if ($search_type == 2 || ($search_type == 1 && $flag == 1)) {
            $product_result = $obj_sbhelper->getMpProductDetail($key, 0, 1);
            die(Tools::jsonEncode($product_result));
        }

        if ($search_type == 3 || ($search_type == 1 && $flag == 2)) {
            $shop_result = $obj_sbhelper->getMpShopDetail($key);
            die(Tools::jsonEncode($shop_result));
        }

        if ($search_type == 4 || ($search_type == 1 && $flag == 3)) {
            $seller_result = $obj_sbhelper->getMpSellerDetail($key);
            die(Tools::jsonEncode($seller_result));
        }

        if ($search_type == 5 || ($search_type == 1 && $flag == 4)) {
            $address_result = $obj_sbhelper->getMpShopLocationDetail($key);
            die(Tools::jsonEncode($address_result));
        }

        if ($search_type == 6 || ($search_type == 1 && $flag == 5)) {
            $category_result = $obj_sbhelper->getPsCategoryDetail($key);
            die(Tools::jsonEncode($category_result));
        }
		
		if ($search_type == 7 || ($search_type == 1 && $flag == 3)) {
            $seller_result = $obj_sbhelper->getMpSellerDetailByProfession($key);
            die(Tools::jsonEncode($seller_result));
        }
    }
}
