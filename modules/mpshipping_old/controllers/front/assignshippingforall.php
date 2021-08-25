<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MpShippingAssignShippingforAllModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        $this->display_header = false;
        $this->display_footer = false;
    }

    public function initContent()
    {
        $mpShippingMethods = Tools::getValue('shipping_method');
        $mpIdSeller = Tools::getValue('mp_id_seller');
        $objShippingMethod = new MpShippingMethod();
        $mpSellerProducts = $objShippingMethod->getAllProducts($mpIdSeller);
        $error = array();
        $carriers = array();
        if ($mpSellerProducts) {
            foreach ($mpSellerProducts as $mpPro) {
                $objMpShippingProductMap = new MpShippingProductMap();
                $objMpShippingProductMap->deleteMpShippingProductMapDetails($mpPro['id_mp_product']);
                foreach ($mpShippingMethods as $mp_shipping_id) {
                    $mpShippingIdNew = (int) MpShippingMethod::getMpShippingId($mp_shipping_id);
                    if ($mpShippingIdNew) {
                        $idPsReference = MpShippingMethod::getReferenceByMpShippingId($mpShippingIdNew);
                        $objMpShippingProductMap->mp_shipping_id = $mpShippingIdNew;
                        $objMpShippingProductMap->id_ps_reference = $idPsReference;
                        $objMpShippingProductMap->mp_product_id = $mpPro['id_mp_product'];
                        $result = $objMpShippingProductMap->add();

                        $carriers[] = $idPsReference;
                    } else {
                        $carriers[] = $mp_shipping_id;
                    }
                    $objMpSellerProductDetail = new WkMpSellerProduct($mpPro['id_mp_product']);
                    $objMpSellerProductDetail->ps_id_carrier_reference = serialize($carriers);
                    $objMpSellerProductDetail->update();
                    if ($mpPro['active'] == 1) {
                        $objMpSellerProductDetail = new WkMpSellerProduct($mpPro['id_mp_product']);
                        $psProductId = $objMpSellerProductDetail->id_ps_product;
                        $objProduct = new Product($psProductId);
                        $objProduct->setCarriers($carriers);
                        $objProduct->save();
                    } else {
                        $error[] = $mpPro['id_mp_product'];
                    }
                }
            }
        }

        if (empty($error)) {
            echo 1;
        } else {
            echo 0;
        }
        die; //ajax close
    }
}
