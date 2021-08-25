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

class MpStoreLocatorValidationModuleFrontController extends ModuleFrontController
{

    /**
     * Generate the Order
     * Insert the entry into Bankwire Order Table for the generated order.
     */
    public function postProcess()
    {
        $cart = $this->context->cart;
        if (!$cart->id
            || !$cart->id_currency
            || !$cart->id_customer
            || !$cart->id_shop
            || !$cart->id_address_invoice
            || !$cart->id_address_delivery
        ) {
            Tools::redirect($this->context->link->getPageLink('order', true, null, 'step=3'));
        } else {
            $totalOrderCost = (float)$cart->getOrderTotal(true, Cart::BOTH);
            $objStorePayment = new MpStorePayment();
            $objStorePayment->validateOrder(
                $cart->id,
                Configuration::get('MP_STORE_OS_WAITING'),
                $totalOrderCost,
                'Pay in store',
                null,
                null,
                null,
                false,
                $cart->secure_key,
                null
            );
            Tools::redirect(
                $this->context->link->getPageLink(
                    'order-confirmation',
                    null,
                    $this->context->language->id,
                    array(
                        'id_cart'=>$cart->id,
                        'id_module'=>$this->module->id,
                        'id_order'=>$objStorePayment->currentOrder,
                        'key'=>$cart->secure_key
                    )
                )
            );
        }
    }
}
