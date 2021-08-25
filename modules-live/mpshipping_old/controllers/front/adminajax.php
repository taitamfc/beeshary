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

class MpShippingAdminAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $selectedIdCustomer = Tools::getValue('selected_id_customer');
        $result = array();
        $result['status'] = 0;
        if ($selectedIdCustomer) {
            /*$obj_seller_info = new WkMpSeller();*/
            $mpCustomerInfo = WkMpSeller::getSellerDetailByCustomerId($selectedIdCustomer);
            if ($mpCustomerInfo) {
                $mpIdSeller = $mpCustomerInfo['id_seller'];
                $idLang = $this->context->language->id;
                $allPsCarriersArr = MpShippingMethod::getOnlyPrestaCarriers($idLang);
                $objMpShippingMethod = new MpShippingMethod();
                $mpShippingData = $objMpShippingMethod->getMpShippingMethods($mpIdSeller);
                if ($mpShippingData) {
                    foreach ($mpShippingData as $key => $value) {
                        $mpShippingData[$key]['id_carrier'] = $value['id_ps_reference'];
                    }
                }

                if (Configuration::get('MP_SHIPPING_ADMIN_SELLER')) {
                    if ($allPsCarriersArr) {
                        foreach ($allPsCarriersArr as $key => $value) {
                            $allPsCarriersArr[$key]['id'] = 0;
                            $allPsCarriersArr[$key]['mp_shipping_name'] = $value['name'];
                        }
                    }
                    if (!$mpShippingData) {
                        $mpShippingData = $allPsCarriersArr;
                    } else {
                        $mpShippingData = array_merge($mpShippingData, $allPsCarriersArr);
                    }
                }

                if ($mpShippingData) {
                    $result['status'] = 1;
                    $result['info'] = $mpShippingData;
                }
            }
        }
        $data = Tools::jsonEncode($result);
        die($data);
    }
}
