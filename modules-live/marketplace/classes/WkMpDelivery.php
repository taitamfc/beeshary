<?php
/**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkMpDelivery extends ObjectModel
{
    public $order_id;
    public $received_by;
    public $delivery_date;

    public static $definition = array(
        'table' => 'wk_mp_delivery',
        'primary' => 'id_mp_delivery',
        'fields' => array(
            'order_id' => array('type' => self::TYPE_INT,'required' => true),
            'received_by' => array('type' => self::TYPE_STRING),
            'delivery_date' => array('type' => self::TYPE_DATE),
        ),
    );

    /**
     * Get Delivery Information by using Order ID
     *
     * @param  int $idOrder Order ID
     * @return array/boolean
     */
    public function getDeliveryByIdOrder($idOrder)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'wk_mp_delivery` WHERE `order_id` = '.(int) $idOrder);
    }

    /**
     * Update order state by using Order Id and new order state
     *
     * @param  int $idOrder Order ID
     * @param  int $idOrderState New Order State
     * @return boolean true/false
     */
    public function updateOrderByIdOrderAndIdOrderState($idOrder, $idOrderState)
    {
        if (isset($idOrder) && $idOrder != '' && isset($idOrderState) && $idOrderState != '') {
            $order = new Order($idOrder);
            if (isset($order)) {
                $orderState = new OrderState($idOrderState);
                $currentOrderState = $order->getCurrentOrderState();
                if ($currentOrderState->id != $orderState->id) {
                    // Create new OrderHistory
                    $history = new OrderHistory();
                    $history->id_order = $order->id;

                    $useExistingsPayment = false;
                    if (!$order->hasInvoice()) {
                        $useExistingsPayment = true;
                    }
                    $history->changeIdOrderState((int) $orderState->id, $order, $useExistingsPayment);

                    //Increase product qty if order status updated as Cancelled
                    $orderProducts = $order->getproducts();
                    if ($orderProducts && $order->current_state == '6') {
                        foreach ($orderProducts as $orderProduct) {
                            $mpProductDetail = WkMpSellerProduct::getSellerProductByPsIdProduct($orderProduct['product_id']);
                            if ($mpProductDetail) {
                                $objMpProduct = new WkMpSellerProduct($mpProductDetail['id_mp_product']);
                                $currentProductQty = $objMpProduct->quantity;
                                $objMpProduct->quantity = $currentProductQty + $orderProduct['product_quantity'];
                                $objMpProduct->save();

                                if ($orderProduct['product_attribute_id']) {
                                    WkMpProductAttribute::updateAttributeQuantity($mpProductDetail['id_mp_product'], $orderProduct['product_attribute_id'], $orderProduct['product_quantity'], 2);
                                }
                            }
                        }
                    }

                    $carrier = new Carrier($order->id_carrier, $order->id_lang);
                    $templateVars = array();
                    if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number) {
                        $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));
                    }
                    // Save all changes
                    if ($history->addWithemail(true, $templateVars)) {
                        // synchronizes quantities if needed..
                        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                            foreach ($order->getProducts() as $product) {
                                if (StockAvailable::dependsOnStock($product['product_id'])) {
                                    StockAvailable::synchronize($product['product_id'], (int) $product['id_shop']);
                                }
                            }
                        }

                        return true;
                    }
                } elseif ($currentOrderState->id == $orderState->id) {
                    return true;
                }
            }
        }

        return false;
    }
}
