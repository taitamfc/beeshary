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

class MarketplaceDeliveryInfo extends ObjectModel
{
    public $id;
    public $order_id;
    public $received_by;
    public $delivery_date;

    public static $definition = array(
        'table' => 'marketplace_delivery',
        'primary' => 'id',
        'fields' => array(
            'order_id' => array('type' => self::TYPE_INT,'required' => true),
            'received_by' => array('type' => self::TYPE_STRING),
            'delivery_date' => array('type' => self::TYPE_DATE),
        ),
    );

    public function getDeliveryDetailsByOrderId($id_order)
    {
        if (isset($id_order)) {
            $delivery_info = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_delivery`
										WHERE `order_id`='.$id_order);
            if ($delivery_info) {
                return $delivery_info;
            }
        }

        return false;
    }

    public function updateOrderByIdOrderAndIdOrderState($id_order, $id_order_state)
    {
        if (isset($id_order) && $id_order != '' && isset($id_order_state) && $id_order_state != '') {
            $order = new Order($id_order);
            if (isset($order)) {
                $order_state = new OrderState($id_order_state);
                $current_order_state = $order->getCurrentOrderState();
                if ($current_order_state->id != $order_state->id) {
                    // Create new OrderHistory
                    $history = new OrderHistory();
                    $history->id_order = $order->id;

                    $use_existings_payment = false;
                    if (!$order->hasInvoice()) {
                        $use_existings_payment = true;
                    }
                    $history->changeIdOrderState((int) $order_state->id, $order, $use_existings_payment);

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
                } elseif ($current_order_state->id == $order_state->id) {
                    return true;
                }
            }
        }

        return false;
    }
}
