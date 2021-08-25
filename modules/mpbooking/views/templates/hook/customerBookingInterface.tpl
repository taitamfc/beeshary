{*
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<link rel="stylesheet" href="{$module_dir}/views/css/front/wk-mp-customer-booking-interface.css">
<link rel="stylesheet" href="{$module_dir}/views/css/wk-datepicker-custom.css">
{if $bookingProductInformation['booking_type'] == $booking_type_date_range}
    <p id="booking_product_available_qty">
        {l s='Available Quantity' mod='mpbooking'} &nbsp;&nbsp;<span class="product_max_avail_qty_display"> {$maxAvailableQuantity} </span>
    </p>
{/if}
<input type="hidden" id="product_page_product_id" name="idProduct" value="{$idProduct}">
<input type="hidden" value="{$idProduct}" class="id_product" name="id_product">
<div class="wk-booking-container row">
    <div class="page-title">
        <span>{l s='Book Your Slot' mod='mpbooking'}</span>
    </div>
    <div class="wk-booking-content col-sm-12">
        {if $bookingProductInformation['booking_type'] == $booking_type_date_range}
            <div class="date_range_form">
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label required">
                        {l s='Du' mod='mpbooking'}
                    </label>
                    <div class="col-md-4">
                        <input id="booking_date_from" autocomplete="off" class="booking_date_from form-control datepicker-input" type="text" readonly="true" placeholder="Book From" value="{if isset($date_from)}{$date_from}{/if}">
                    </div>
                    <label class="col-sm-1 form-control-label required">
                        {l s='Au' mod='mpbooking'}
                    </label>
                    <div class="col-md-4">
                        <input id="booking_date_to" autocomplete="off" class="booking_date_to form-control datepicker-input" type="text" readonly="true" placeholder="Book To" value="{if isset($date_to)}{$date_to}{/if}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label required">
                        {l s='Quantity' mod='mpbooking'}
                    </label>
                        <!-- PAUL : change type -->
                        <input type="number" id="booking_product_quantity_wanted" value="1" class="input-group form-control" min="1">
                        <!-- PAUL -->
                </div>
            </div>
        {else}
            <div class="date_range_form">
                <div class="form-group row">
                    <label class="col-sm-1 form-control-label required">
                        {l s='Du' mod='mpbooking'}
                    </label>
                    <div class="col-md-6">
                        <input id="booking_time_slot_date" autocomplete="off" class="booking_time_slot_date form-control datepicker-input" type="text" readonly="true" placeholder="Book From" value="{if isset($date_from)}{$date_from}{/if}">
                    </div>
                </div>
                <div id="booking_product_time_slots">
                    {if isset($bookingTimeSlots) && $bookingTimeSlots}
                        {foreach $bookingTimeSlots as $time_slot}
                            <div class="time_slot_checkbox row">
                                <label class="col-sm-8 form-control-static">
                                    <input {if !$time_slot['available_qty']}disabled="disabled"{/if} {if $time_slot['checked']}checked="checked"{/if} type="checkbox" data-slot_price="{$time_slot['price']}" value="{$time_slot['id_time_slots_price']}" class="product_blooking_time_slot">&nbsp;&nbsp;&nbsp;<span class="time_slot_price">{$time_slot['formated_slot_price']}</span>&nbsp;&nbsp;{l s='Pour' mod='mpbooking'}&nbsp;&nbsp;<span class="time_slot_range">{$time_slot['time_slot_from']} &nbsp;-&nbsp;{$time_slot['time_slot_to']}</span>
                                </label>
                                {if $time_slot['available_qty']}
                                    <label class="col-sm-4" id="slot_quantity_container_{$time_slot['id_time_slots_price']}">
                                        <div class="input-group col-sm-12">
                                            <input type="hidden" id="slot_max_avail_qty_{$time_slot['id_time_slots_price']}" class="slot_max_avail_qty" value="{$time_slot['available_qty']}">
                                            <!-- PAUL : change type -->
                                            <input type="number" class="booking_time_slots_quantity_wanted  form-control" value="1" min="1">
                                            <!-- PAUL -->
                                            <div class="input-group-addon" id="qty_avail_{$time_slot['id_time_slots_price']}">/{$time_slot['available_qty']}</div>
                                        </div>
                                         <p class="personnes_p">personne(s)</p>
                                    </label>
                                {else}
                                    <label class="col-sm-4 form-control-static" id="slot_quantity_container_{$time_slot['id_time_slots_price']}">
                                        <span class="booked_slot_text">{l s='Emplacement résérvé' mod='mpbooking'}!</span>
                                    </label>
                                {/if}
                            </div>
                        {/foreach}
                    {else}
                        {l s='No Slots Available' mod='mpbooking'}
                    {/if}
                </div>
            </div>
        {/if}
        <hr>
        <p class="col-sm-12 alert-danger booking_product_errors"></p>
        <div class="row row_down">
            <div id="bookings_in_select_range" class="col-sm-12 table-responsive"></div>
            <div class="col-sm-6">
                <input type="hidden" id="max_available_qty" value="{$maxAvailableQuantity}" class="input-group form-control">
                <p class="wk_total_booking_price_container">
                    <span class="booking_total_price_text">{l s='Total Amount' mod='mpbooking'}</span>&nbsp;&nbsp;<span class="booking_total_price">{$productFeaturePrice}</span>
                </p>
            </div>
            <div class="col-sm-6">
                <div class="col-sm-12">
                    <img src="{$module_dir}/views/img/ajax-loader.gif" class="booking_loading_img" alt={l s='Not Found' mod='mpbooking'}/>
                    <button button class="btn btn-primary pull-sm-right" id="booking_button"  booking_type="{$bookingProductInformation['booking_type']}" {if $selectedDatesDisabled || !$maxAvailableQuantity || (isset($totalSlotsQty) && $totalSlotsQty == 0) || (isset($bookingTimeSlots) && !$bookingTimeSlots)}disabled{/if}>
                        {l s='Book Now' mod='mpbooking'}
                    </button>
                </div>
                <p class="col-sm-12 unavailable_slot_err"style="{if !$maxAvailableQuantity || (isset($totalSlotsQty) && $totalSlotsQty == 0) || (isset($bookingTimeSlots) && !$bookingTimeSlots)}display:block;{else}display:none;{/if}">
                    <span>{l s='Aucun créneau disponible' mod='mpbooking'} !</span>
                </p>
            </div>
        </div>
    </div>
    {if isset($bookingPricePlans) && $bookingPricePlans && $show_feature_price_rules}
        <div class="feature_plans_info col-sm-12">
            <strong>{l s='Note' mod='mpbooking'}</strong><span style="color:red;">**</span> : {l s='Following booking price rules are applying for this product' mod='mpbooking'} -
            <ol class="product_booking_feature_plans" type="1">
                {foreach $bookingPricePlans as $key=>$pricePlan}
                    <li>
                        {$pricePlan['feature_price_name']} :
                        {if $pricePlan['impact_way'] == 1}
                            {l s='Discount of' mod='mpbooking'}
                        {else}
                            {l s='Extra charges of' mod='mpbooking'}
                        {/if}
                        {if $pricePlan['impact_type'] == 1}
                            {$pricePlan['impact_value']|round:2}%
                        {else}
                            {$pricePlan['impact_value_formated']} (tax excl.)
                        {/if}
                        {l s='on unit price' mod='mpbooking'}
                        {if $pricePlan['date_selection_type'] == 1}
                            {l s='de' mod='mpbooking'} {$pricePlan['date_from']|date_format:"%e %b, %Y"} {l s='à' mod='mpbooking'} {$pricePlan['date_to']|date_format:"%e %b, %Y"}
                            {if $pricePlan['is_special_days_exists'] == 1}
                                {l s='for special days' mod='mpbooking'}
                                {foreach $pricePlan['special_days'] as $day}
                                    {$day}
                                {/foreach}
                            {/if}
                        {else}
                            {l s='Pour' mod='mpbooking'} {$pricePlan['date_from']|date_format:"%e %b, %Y"}
                        {/if}
                        .
                    </li>
                {/foreach}
            </ol>
        </div>
        <div class="feature_plans_priority col-sm-12 alert alert-info">
            <strong>{l s='Important' mod='mpbooking'}</strong><span style="color:red;">**</span> : {l s='If mutiple plans apply on a date then plans priority will be :' mod='mpbooking'}</br>
            {foreach $featurePricePriority as $key => $priority}
                {$priority} {if $key < 2 }>{/if}
            {/foreach}
        </div>
    {/if}
</div>