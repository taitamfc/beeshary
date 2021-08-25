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

class MarketplacePaymentProcessModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if (isset($this->context->customer->id)) {
            $objMpSeller = new SellerInfoDetail();
            $seller = $objMpSeller->getSellerDetailsByCustomerId($this->context->customer->id);
            if ($seller && $seller['active']) {
                $idMpPayment = Tools::getValue('id');
                $paymentMode = Tools::getValue('payment_mode');
                $paymentDetail = Tools::getValue('payment_detail');

                if ($idMpPayment) {
                    $mpPayment = new MarketplaceCustomerPaymentDetail($idMpPayment);
                } else {
                    $mpPayment = new MarketplaceCustomerPaymentDetail();
                }

                $mpPayment->seller_customer_id = $this->context->customer->id;
                $mpPayment->payment_mode_id = $paymentMode;
                $mpPayment->payment_detail = $paymentDetail;
                if ($mpPayment->save()) {
                    if ($idMpPayment) {
                        Tools::redirect($this->context->link->getModuleLink('marketplace', 'mppayment', array('edited' => 1)));
                    } else {
                        Tools::redirect($this->context->link->getModuleLink('marketplace', 'mppayment', array('created' => 1)));
                    }
                }
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }
}
