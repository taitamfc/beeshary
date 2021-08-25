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

class MarketplaceUpdateOrderStatusProcessModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $idOrder = Tools::getValue('id_order');
        $idOrderState = Tools::getValue('id_order_state_checked');
        $shippingSubmited = Tools::getValue('shipping_info_set');
        $deliverySubmited = Tools::getValue('delivery_info_set');

        if ($shippingSubmited == 1) {
            $shippingDescription = Tools::getValue('edit_shipping_description');
            $shippingDate = Tools::getValue('shipping_date');
            if (isset($shippingDescription) && isset($shippingDescription)) {
                $objMpShipping = new MarketplaceShippingInfo();
                $mpShipping = $objMpShipping->getShippingDetailsByOrderId($idOrder);
                if ($mpShipping) {
                    $objMpShipping = new MarketplaceShippingInfo($mpShipping['id']);
                } else {
                    $objMpShipping = new MarketplaceShippingInfo();
                }
                $objMpShipping->order_id = $idOrder;
                $objMpShipping->shipping_description = $shippingDescription;
                $objMpShipping->shipping_date = $shippingDate;
                $objMpShipping->save();
                $this->redirectByIdOrderAndIdOrderState($idOrder, $idOrderState);
            }
        } elseif ($deliverySubmited == 1) {
            $deliveryDescription = Tools::getValue('edit_received_by');
            $deliveryDate = Tools::getValue('delivery_date');
            if (isset($deliveryDescription) && isset($deliveryDate)) {
                $objMpDelivery = new MarketplaceDeliveryInfo();
                $mpDelivery = $objMpDelivery->getDeliveryDetailsByOrderId($idOrder);
                if ($mpDelivery) {
                    $objMpDelivery = new MarketplaceDeliveryInfo($mpDelivery['id']);
                } else {
                    $objMpDelivery = new MarketplaceDeliveryInfo();
                }
                $objMpDelivery->order_id = $idOrder;
                $objMpDelivery->received_by = $deliveryDescription;
                $objMpDelivery->delivery_date = $deliveryDate;
                $objMpDelivery->save();
                $this->redirectByIdOrderAndIdOrderState($idOrder, $idOrderState);
            }
        } else {
            $this->redirectByIdOrderAndIdOrderState($idOrder, $idOrderState);
        }
    }

    public function redirectByIdOrderAndIdOrderState($idOrder, $idOrderState)
    {
        $objMpDelivery = new MarketplaceDeliveryInfo();
        $isUpdated = $objMpDelivery->updateOrderByIdOrderAndIdOrderState($idOrder, $idOrderState);
        if ($isUpdated) {
            Tools::redirect($this->context->link->getModuleLink('marketplace', 'mporderdetails', array('id_order' => $idOrder, 'is_order_state_updated' => 1)));
        } else {
            Tools::redirect($this->context->link->getModuleLink('marketplace', 'mporderdetails', array('id_order' => $idOrder)));
        }
    }
}
