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

class MpShippingUpdateDefaultShippingModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $idCustomer = $this->context->customer->id;
        if ($idCustomer) {
            $defaultShipping = Tools::getValue('default_shipping');
            if ($defaultShipping) {
                $mpCustomerInfo = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
                if ($mpCustomerInfo && $mpCustomerInfo['active']) {
                    $idMpSeller = $mpCustomerInfo['id_seller'];
                    $objMpShipping = new MpShippingMethod();
                    $alreadyDefaultShipping = $objMpShipping->getDefaultMpShippingMethods($idMpSeller);
                    if ($alreadyDefaultShipping) {
                        foreach ($alreadyDefaultShipping as $mpShipping) {
                            MpShippingMethod::updateDefaultShipping($mpShipping['id'], 0);
                        }
                    }

                    foreach ($defaultShipping as $defaultShippingId) {
                        $shippingSellerId = MpShippingMethod::getSellerIdByMpShippingId($defaultShippingId);
                        if ($shippingSellerId == $idMpSeller) {
                            MpShippingMethod::updateDefaultShipping($defaultShippingId, 1);
                        }
                    }
                }
                Tools::redirect($this->context->link->getModuleLink('mpshipping', 'mpshippinglist', array('updatempshipping_success' => 1)));
            } else {
                Tools::redirect($this->context->link->getModuleLink('mpshipping', 'mpshippinglist', array('no_shipping' => 1)));
            }
        }
    }
}
