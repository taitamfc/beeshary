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

class MarketplaceValidateUniqueShopModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        $this->display_header = false;
        $this->display_footer = false;
    }

    public function initContent()
    {
        $shopName = Tools::getValue('shop_name');
        $sellerEmail = Tools::getValue('seller_email');
        $idSeller = Tools::getValue('id_seller');

        if ($shopName) {
            if (SellerInfoDetail::isShopNameExist(Tools::link_rewrite($shopName), $idSeller)) {
                die("1");
            } else {
                if (!Validate::isCatalogName($shopName)) {
                    die("2");
                } else {
                    die("0");
                }
            }
        }

        if ($sellerEmail) {
            if (SellerInfoDetail::isSellerEmailExist($sellerEmail, $idSeller)) {
                die("1");
            } else {
                die("0");
            }
        }
    }
}
