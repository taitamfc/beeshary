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

class MpSlotPricingAddPriceSlotsModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        $this->display_header = false;
        $this->display_footer = false;
    }

    public function initContent()
    {
        $mp_id_product = Tools::getValue('mp_product_id');
        $min_qty = Tools::getValue('min_qty');
        $discount_type = Tools::getValue('discount_type');
        $amount = Tools::getValue('amount');

        $obj_mp_slot_price = new MpPriceSlots();
        if ($mp_id_product != '' && $min_qty != '' && $discount_type != '' && $amount != '') {
            $price_slot_added_to_mp = 0;
            $obj_price_slots = new MpPriceSlots();
            $obj_price_slots->mp_id_product = $mp_id_product;
            $obj_price_slots->min_qty = $min_qty;
            $obj_price_slots->discount_type = $discount_type;
            $obj_price_slots->amount = $amount;

            if (!MpPriceSlots::checkMinQtyExists($mp_id_product, $min_qty)) {
                $result = $obj_price_slots->add();
                $id = $obj_price_slots->id;
                if ($result) {
                    $price_slot_added_to_mp = 1;
                    echo 1;
                } else {
                    $price_slot_added_to_mp = 0;
                    echo 0;
                }
            } else {
                $price_slot_added_to_mp = 0;
                echo 'min qty error';
            }
        } else {
            $price_slot_added_to_mp = 0;
            echo 0;
        }
        if ($price_slot_added_to_mp == 1) {
            $obj_mp_seller_product_detail = new SellerProductDetail($mp_id_product);
            $mp_pro_status = $obj_mp_seller_product_detail->active;
            $ps_product_id = $obj_mp_seller_product_detail->id_ps_product;
            if ($mp_pro_status && $ps_product_id) {
                $obj_specific_price = new SpecificPrice();
                if ($discount_type == 'percentage') {
                    $reduction_amount = (float) $amount / 100;
                } else {
                    $reduction_amount = $amount;
                }

                $obj_specific_price->id_shop = $this->context->customer->id_shop;
                $obj_specific_price->id_cart = 0;
                $obj_specific_price->id_product = $ps_product_id;
                $obj_specific_price->id_currency = 0;
                $obj_specific_price->id_country = 0;
                $obj_specific_price->id_group = 0;
                $obj_specific_price->id_customer = 0;
                $obj_specific_price->price = -1;
                $obj_specific_price->from_quantity = $min_qty;
                $obj_specific_price->reduction = $reduction_amount;
                $obj_specific_price->reduction_type = $discount_type;
                $obj_specific_price->from = '0000-00-00 00:00:00';
                $obj_specific_price->to = '0000-00-00 00:00:00';
                $obj_specific_price->save();
                $slot_id = $obj_specific_price->id;
                if ($slot_id) {
                    if ($result) {
                        $obj_mp_slot_price = new MpPriceSlots($id);
                        $obj_mp_slot_price->ps_slot_id = $slot_id;
                        $obj_mp_slot_price->save();
                    }
                }
            }
        }
    }
}
