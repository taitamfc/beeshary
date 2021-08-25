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

class MpSlotPricingDeleteSlotModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        $this->display_header = false;
        $this->display_footer = false;
    }

    public function initContent()
    {
        $mp_id_product = Tools::getValue('mp_product_id');
        $slot_id = Tools::getValue('slot_id');
        $obj_mp_slot_price = new MpPriceSlots();
        $slot_details = $obj_mp_slot_price->getSlotDetails($slot_id);
        $ps_slot_id = $slot_details[0]['ps_slot_id'];
        $result = $obj_mp_slot_price->deleteProductPriceSlot($mp_id_product, $slot_id);
        $specificPrice = new SpecificPrice((int) $ps_slot_id);
        $specificPrice->delete();
        if ($result) {
            echo 1;
        } else {
            echo 0;
        }
    }
}
