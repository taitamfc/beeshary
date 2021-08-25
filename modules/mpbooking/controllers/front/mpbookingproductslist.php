<?php
/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MpBookingMpBookingProductsListModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Marketplace', array(), 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        ];
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Booking Products List', array(), 'Breadcrumb'),
            'url' => '',
        ];
        return $breadcrumb;
    }

    public function initContent()
    {
        parent::initContent();
        if ($idCustomer = $this->context->customer->id) {
            $sellerInfo = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($sellerInfo && $sellerInfo['active']) {
                //delete selected checkbox process
                if ($selectedProducts = Tools::getValue('mp_product_selected')) {
                    $this->deleteSelectedProducts($selectedProducts, $sellerInfo['id_seller']);
                }
                // If delete product by seller
                if ($deleteProduct = Tools::getValue('deleteproduct')) {
                    $mpIdProduct = Tools::getValue('id_mp_product');
                    // if seller delete product, delete process
                    $objMpSellerProduct = new WkMpSellerProduct($mpIdProduct);
                    // If seller of current product and current seller customer is match
                    if ($objMpSellerProduct->id_seller == $sellerInfo['id_seller']) {
                        if ($objMpSellerProduct->delete()) {
                            Tools::redirect(
                                $this->context->link->getModuleLink(
                                    'mpbooking',
                                    'mpbookingproductslist',
                                    array('deleted' => 1)
                                )
                            );
                        }
                    } else {
                        Tools::redirect($this->context->link->getModuleLink('marketplace', 'dashboard'));
                    }
                }
                if (Tools::getValue('mp_product_status')) {
                    $this->changeProductStatus($sellerInfo['id_seller']);
                }
                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    $idLang = $this->context->language->id;
                } else {
                    if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') {
                        $idLang = Configuration::get('PS_LANG_DEFAULT');
                    } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') {
                        $idLang = $sellerInfo['default_lang'];
                    }
                }
                $objBookingProductInfo = new WkMpBookingProductInformation();
                if ($sellerBookingProducts = $objBookingProductInfo->getSellerBookingProductsInfo(
                    $sellerInfo['id_seller'],
                    'all',
                    $idLang
                )) {
                    // filter products to show only booking products in the list
                    $sellerBookingProducts = $this->getProductDetails($sellerBookingProducts);
                    foreach ($sellerBookingProducts as &$product) {
                        $product['price'] = Tools::displayPrice($product['price']);
                        $bookingProductInfo = $objBookingProductInfo->getBookingProductInfo(
                            $product['id_mp_product'],
                            0
                        );
                        if ($bookingProductInfo) {
                            $product['quantity'] = $bookingProductInfo['quantity'];
                            $product['booking_type'] = $bookingProductInfo['booking_type'];
                        } else {
                            unset($product);
                        }
                    }
                }

                $this->context->smarty->assign(
                    array(
                        'products_status' => Configuration::get('WK_MP_SELLER_PRODUCTS_SETTINGS'),
                        'booking_type_date_range' => WkMpBookingProductInformation::WK_PRODUCT_BOOKING_TYPE_DATE,
                        'imageediturl' => $this->context->link->getModuleLink('marketplace', 'productimageedit'),
                        'booking_product_list' => $sellerBookingProducts,
                        'logic' => 'mpbookingproduct',
                        'static_token' => Tools::getToken(false),
                    )
                );
                //Assign Js vars
                Media::addJsDef(
                    array(
                        'ajax_urlpath' => $this->context->link->getModuleLink('marketplace', 'productimageedit'),
                        'update_success' => $this->module->l('Updated Successfully', 'mpbookingproductslist'),
                        'confirm_delete_msg' => $this->module->l('Are you sure you want to delete?', 'mpbookingproductslist'),
                    )
                );
                WkMpBookingHelper::assignDataTableVariables();
                $this->setTemplate('module:mpbooking/views/templates/front/mpbookingproductslist.tpl');
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect(
                'index.php?controller=authentication&back='.
                urlencode($this->context->link->getModuleLink('mpbooking', 'mpbookingproductslist'))
            );
        }
    }

    private function deleteSelectedProducts($mpIdProducts, $idSeller)
    {
        $mpDelete = true;
        $objMpProduct = new WkMpSellerProduct();
        foreach ($mpIdProducts as $idMpProduct) {
            $objMpProduct = new WkMpSellerProduct($idMpProduct);
            if ($objMpProduct->id_seller == $idSeller) {
                if (!$objMpProduct->delete()) {
                    $mpDelete = false;
                }
            }
        }
        if ($mpDelete) {
            Tools::redirect(
                $this->context->link->getModuleLink('mpbooking', 'mpbookingproductslist', array('deleted' => 1))
            );
        }
    }

    private function changeBookingProductStatus()
    {
        $idProduct = Tools::getValue('id');
        $objMpBookingProduct = new WkMpBookingProductInformation($idProduct);
        if (Validate::isLoadedObject($objMpBookingProduct)) {
            $statusToChange = Tools::getValue('mp_product_status');
            if ($statusToChange) {
                $objMpBookingProduct->active = 0;
            } else {
                $objMpBookingProduct->active = 1;
            }
            if ($objMpBookingProduct->save()) {
                Tools::redirect(
                    $this->context->link->getModuleLink(
                        'mpbooking',
                        'mpbookingproductslist',
                        array('status_updated' => 1)
                    )
                );
            }
        } else {
            $this->errors[] = $this->module->l('Object not loaded.', 'mpbookingproductslist');
        }
    }

    public function getProductDetails($productList)
    {
        //if multilang is OFF then current lang will be PS Default Lang
        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
            $idLang = $this->context->language->id;
        } else {
            $idLang = Configuration::get('PS_LANG_DEFAULT');
        }

        $language = Language::getLanguage((int) $idLang);

        foreach ($productList as &$product) {
            if ($product['id_ps_product']) { // if product activated

                $idPsProduct = $product['id_ps_product'];

                $objProduct = new Product($idPsProduct, false, $idLang);
                $cover = Product::getCover($idPsProduct);

                if ($cover) {
                    $objImage = new Image($cover['id_image']);
                    $product['image_path'] = _THEME_PROD_DIR_.$objImage->getExistingImgPath().'.jpg';
                    $product['cover_image'] = $idPsProduct.'-'.$cover['id_image'];
                }

                $product['id_product'] = $idPsProduct;
                $product['id_lang'] = $idLang;
                $product['lang_iso'] = $language['iso_code'];
                $product['obj_product'] = $objProduct;
            } else { //if product not active
                $unactiveImage = WkMpSellerProduct::getInactiveProductImageByIdProduct($product['id_mp_product']);
                // product is inactive so by default first image is taken because no one is cover image
                if ($unactiveImage) {
                    $product['unactive_image'] = $unactiveImage[0]['seller_product_image_name'];
                }
            }
            //convert price for multiple currency
            $product['price'] = Tools::convertPrice($product['price']);
        }
        return $productList;
    }

    public function changeProductStatus($idSeller)
    {
        $idProduct = Tools::getValue('id_product');
        $objMpProduct = new WkMpSellerProduct();
        $sellerProduct = WkMpSellerProduct::getSellerProductByPsIdProduct($idProduct);
        if ($sellerProduct && ($sellerProduct['id_seller'] == $idSeller)) {
            $mpIdProduct = $sellerProduct['id_mp_product'];

            Hook::exec('actionBkBeforeToggleMPProductStatus', array('id_mp_product' => $mpIdProduct));
            if (!count($this->errors)) {
                $objMpProduct = new WkMpSellerProduct($mpIdProduct);

                if ($objMpProduct->active) {
                    $objMpProduct->active = 0;
                    $objMpProduct->status_before_deactivate = 0;
                    $objMpProduct->save();
                    $product = new Product($idProduct);
                    $product->active = 0;
                    $product->save();
                    //Update id_image as mp_image_id when product is going to deactivate
                    WkMpProductAttributeImage::setCombinationImagesAsMp($mpIdProduct);
                } else {
                    $objMpProduct->active = 1;
                    $objMpProduct->status_before_deactivate = 1;
                    $objMpProduct->save();

                    $objMpProduct->updateSellerProductToPs($mpIdProduct, 1);
                    $objMpProductAttribute = new WkMpProductAttribute();
                    $objMpProductAttribute->updateMpProductCombinationToPs($mpIdProduct, $idProduct);

                    Hook::exec(
                        'actionBkToogleMPProductActive',
                        array('id_mp_product' => $mpIdProduct, 'active' => $objMpProduct->active)
                    );
                }
                Hook::exec(
                    'actionBkAfterToggleMPProductStatus',
                    array('id_product' => $idProduct, 'active' => $objMpProduct->active)
                );
                Tools::redirect(
                    $this->context->link->getModuleLink(
                        'mpbooking',
                        'mpbookingproductslist',
                        array('status_updated' => 1)
                    )
                );
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addJqueryPlugin('tablednd');
        $this->addjQueryPlugin('growl', null, false);

        //data table file included
        $this->registerStylesheet('datatable_bootstrap', 'modules/marketplace/views/css/datatable_bootstrap.css');
        $this->registerJavascript('mp-jquery-dataTables', 'modules/marketplace/views/js/jquery.dataTables.min.js');
        $this->registerJavascript('mp-dataTables.bootstrap', 'modules/marketplace/views/js/dataTables.bootstrap.js');

        $this->registerStylesheet('marketplace_accountcss', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerJavascript('mp-imageedit-js', 'modules/marketplace/views/js/imageedit.js');
    }
}
