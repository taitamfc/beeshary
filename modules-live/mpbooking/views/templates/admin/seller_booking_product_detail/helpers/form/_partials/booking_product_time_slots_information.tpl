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

<div class="row time_slots_information_block">
    <input type="hidden" value="1" name="time_slots_data_save">
    <!-- for time slots type bookings -->
    {if isset($bookingProductTimeSlots) && $bookingProductTimeSlots}
        <div class="time_slots_prices_content">
            {assign var=date_ranges_count value=0}
            {foreach $bookingProductTimeSlots as $key => $dateRangesInfo}
                <div class="single_date_range_slots_container" date_range_slot_num="{$date_ranges_count}">
                    <div  class="form-group table-responsive-row col-sm-5 booking_date_ranges">
                        <table class="table">
                            <thead>
                                <tr>
                                <th class="center">
                                    <span>{l s='Date From' mod='mpbooking'}</span>
                                </th>
                                <th class="center">
                                    <span>{l s='Date To' mod='mpbooking'}</span>
                                </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="center">
                                        <div class="input-group">
                                            <input autocomplete="off" class="form-control sloting_date_from" type="text" name="sloting_date_from[]" value="{$dateRangesInfo['date_from']}" readonly>
                                            <span class="input-group-addon">
                                                <i class="icon-calendar"></i>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <div class="input-group">
                                            <input autocomplete="off" class="form-control sloting_date_to" type="text" name="sloting_date_to[]" value="{$dateRangesInfo['date_to']}" readonly>
                                            <span class="input-group-addon">
                                                <i class="icon-calendar"></i>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div  class="form-group table-responsive-row col-sm-7 time_slots_prices_table_div">
                        <table class="table time_slots_prices_table">
                            <thead>
                                <tr>
                                    <th class="center">
                                        <span>{l s='Slot Time From' mod='mpbooking'}</span>
                                    </th>
                                    <th class="center">
                                        <span>{l s='Slot Time To' mod='mpbooking'}</span>
                                    </th>
                                    <th class="center">
                                        <span>{l s='Price (tax excl.)' mod='mpbooking'}</span>
                                    </th>
                                    <th class="center">
                                        <span>{l s='Status' mod='mpbooking'}</span>
                                    </th>
                                </tr>
                            </thead>
                        <tbody>
                            {assign var=key_time_slot value=0}
                            {foreach $dateRangesInfo.time_slots as $timeSlots}
                                <tr>
                                    <td class="center">
                                        <div class="input-group">
                                            <input autocomplete="off" type="hidden" name="time_slot_id{$date_ranges_count}[]" value="{$timeSlots['id_slot']}">
                                            <input autocomplete="off" class="booking_time_from" type="text" name="booking_time_from{$date_ranges_count}[]" value="{$timeSlots['time_from']}" readonly>
                                            <span class="input-group-addon">
                                                <i class="icon-clock-o"></i>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <div class="input-group">
                                            <input autocomplete="off" class="form-control booking_time_to" type="text" name="booking_time_to{$date_ranges_count}[]" value="{$timeSlots['time_to']}" readonly>
                                            <span class="input-group-addon">
                                                <i class="icon-clock-o"></i>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <div class="input-group">
                                            <input type="text" name="slot_range_price{$date_ranges_count}[]" value="{$timeSlots['slot_price']|round:2}">
                                            <span class="input-group-addon">{$defaultCurrencySign}</span>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <div class="slot_status_div">
                                            <input type="hidden" value="{if $timeSlots['active']}1{else}0{/if}" name="slot_active{$date_ranges_count}[]" class="time_slot_status">
                                            <img src="{$modules_dir}mpbooking/views/img/icon/icon-check.png" class="slot_active_img" {if !$timeSlots['active']}style="display:none;"{/if}>
                                            <img src="{$modules_dir}mpbooking/views/img/icon/icon-close.png" class="slot_deactive_img" {if $timeSlots['active']}style="display:none;"{/if}>
                                        </div>
                                    </td>
                                    {if $key_time_slot}
                                        <td class="center">
                                            <a href="#" class="remove_time_slot btn btn-default"><i class="icon-trash"></i></a>
                                        </td>
                                    {else}
                                        <td class="center">
                                            <a href="#" class="remove_time_slot btn btn-default"><i class="icon-trash"></i></a>
                                        </td>
                                    {/if}
                                </tr>
                                {assign var=key_time_slot value=$key_time_slot+1}
                            {/foreach}
                        </tbody>
                        </table>
                        <div class="form-group">
                            <div class="col-lg-12">
                                <button class="add_more_time_slot_price" class="btn btn-default" type="button" data-size="s" data-style="expand-right">
                                    <i class="icon-calendar-empty"></i>
                                    {l s='Add More Slots' mod='mpbooking'}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {assign var=date_ranges_count value=$date_ranges_count+1}
            {/foreach}
        </div>
    {else}
        <div class="time_slots_prices_content row">
            <div class="single_date_range_slots_container" date_range_slot_num="0">
                <div  class="form-group table-responsive-row col-sm-6 booking_date_ranges">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="center">
                                    <span>{l s='Date From' mod='mpbooking'}</span>
                                </th>
                                <th class="center">
                                    <span>{l s='Date To' mod='mpbooking'}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="center">
                            <div class="input-group">
                                <input autocomplete="off" class="form-control sloting_date_from" type="text" name="sloting_date_from[]" value="{$date_from|date_format:'%d-%m-%Y'}" readonly>
                                <span class="input-group-addon">
                                <i class="icon-calendar"></i>
                                </span>
                            </div>
                            </td>

                            <td class="center">
                            <div class="input-group">
                                <input autocomplete="off" class="form-control sloting_date_to" type="text" name="sloting_date_to[]" value="{$date_to|date_format:'%d-%m-%Y'}" readonly>
                                <span class="input-group-addon">
                                <i class="icon-calendar"></i>
                                </span>
                            </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div  class="form-group table-responsive-row col-sm-6 time_slots_prices_table_div">
                    <table class="table time_slots_prices_table">
                        <thead>
                            <tr>
                                <th class="center">
                                    <span>{l s='Slot Time From' mod='mpbooking'}</span>
                                </th>
                                <th class="center">
                                    <span>{l s='Slot Time To' mod='mpbooking'}</span>
                                </th>
                                <th class="center">
                                    <span>{l s='Price (tax excl.)' mod='mpbooking'}</span>
                                </th>
                                <th class="center">
                                    <span>{l s='Status' mod='mpbooking'}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="center">
                                    <div class="input-group">
                                        <input autocomplete="off" class="booking_time_from" type="text" name="booking_time_from0[]" readonly>
                                        <span class="input-group-addon">
                                            <i class="icon-clock-o"></i>
                                        </span>
                                    </div>
                                </td>
                                <td class="center">
                                    <div class="input-group">
                                        <input autocomplete="off" class="form-control booking_time_to" type="text" name="booking_time_to0[]" readonly>
                                        <span class="input-group-addon">
                                            <i class="icon-clock-o"></i>
                                        </span>
                                    </div>
                                </td>
                                <td class="center">
                                    <div class="input-group">
                                        <input class="form-control" type="text" name="slot_range_price0[]" value="{$product_info['price']}">
                                        <span class="input-group-addon">{$defaultCurrencySign}</span>
                                    </div>
                                </td>
                                <td class="center">
                                    <div class="slot_status_div">
                                        <input type="hidden" value="1" name="slot_active0[]" class="time_slot_status">
                                        <img src="{$modules_dir}mpbooking/views/img/icon/icon-check.png" class="slot_active_img">
                                        <img src="{$modules_dir}mpbooking/views/img/icon/icon-close.png" style="display:none;" class="slot_deactive_img">
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="form-group">
                        <div class="col-lg-12">
                            <button class="add_more_time_slot_price" class="btn btn-default" type="button" data-size="s" data-style="expand-right">
                                <i class="icon-calendar-empty"></i>
                                {l s='Add More Slots' mod='mpbooking'}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}
    <div class="form-group">
        <div class="col-lg-12">
            <button id="add_more_date_ranges" class="btn btn-default" type="button" data-size="s" data-style="expand-right">
                <i class="icon-calendar-empty"></i>
                {l s='Add More Date Ranges' mod='mpbooking'}
            </button>
        </div>
    </div>
</div>