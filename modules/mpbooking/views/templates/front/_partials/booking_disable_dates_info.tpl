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

<div class="disable_dates_information_block col-sm-12">
    {*disable special days*}
    <div class="row">
        <div class="alert alert-info">
            <i class="material-icons">&#xE887;</i> {l s='If you want to disable bookings for some special days. Then enable the option and select the days for which you want to disable the bookings. For Ex.- you can disable bookings for every sun by selecting Sun.' mod='mpbooking'}
        </div>
        <div class="form-group row">
            <label class="col-lg-3 control-label">{l s='Disable Special Days' mod='mpbooking'}</label>
            <div class="col-lg-6">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input class="is_disabled_week_days_exists" type="radio" value="1" id="disable_special_days_active_on" name="disable_special_days_active" {if isset($DISABLE_SPECIAL_DAYS_ACTIVE) && $DISABLE_SPECIAL_DAYS_ACTIVE} checked="checked"{/if}>
                    <label for="disable_special_days_active_on">{l s='Yes' mod='mpbooking'}</label>
                    <input class="is_disabled_week_days_exists" type="radio" value="0" id="disable_special_days_active_off" name="disable_special_days_active" {if !isset($DISABLE_SPECIAL_DAYS_ACTIVE) || !$DISABLE_SPECIAL_DAYS_ACTIVE} checked="checked"{/if}>
                    <label for="disable_special_days_active_off">{l s='No' mod='mpbooking'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
        <div class="form-group row">
            <div class="form-group disabled_week_days" {if !isset($DISABLE_SPECIAL_DAYS_ACTIVE) || !$DISABLE_SPECIAL_DAYS_ACTIVE} style="display:none;" {/if}>
                <label class="col-sm-3 control-label required" for="date_to" >
                    {l s='Select Special Days' mod='mpbooking'} :
                </label>
                <div class="col-sm-7">
                    <div class="col-sm-1">
                        <input type="checkbox" name="disabled_special_days[]" value="1" {if isset($bookingDisableDates['disabled_special_days']) && $bookingDisableDates['disabled_special_days'] && in_array('1', $bookingDisableDates['disabled_special_days'])}checked="checked"{/if}/>
                        <p>{l s='Mon' mod='mpbooking'}</p>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" name="disabled_special_days[]" value="2" {if isset($bookingDisableDates['disabled_special_days']) && $bookingDisableDates['disabled_special_days'] && in_array('2', $bookingDisableDates['disabled_special_days'])}checked="checked"{/if}/>
                        <p>{l s='Tue' mod='mpbooking'}</p>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" name="disabled_special_days[]" value="3" {if isset($bookingDisableDates['disabled_special_days']) && $bookingDisableDates['disabled_special_days'] && in_array('3', $bookingDisableDates['disabled_special_days'])}checked="checked"{/if}/>
                        <p>{l s='Wed' mod='mpbooking'}</p>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" name="disabled_special_days[]" value="4" {if isset($bookingDisableDates['disabled_special_days']) && $bookingDisableDates['disabled_special_days'] && in_array('4', $bookingDisableDates['disabled_special_days'])}checked="checked"{/if}/>
                        <p>{l s='Thu' mod='mpbooking'}</p>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" name="disabled_special_days[]" value="5" {if isset($bookingDisableDates['disabled_special_days']) && $bookingDisableDates['disabled_special_days'] && in_array('5', $bookingDisableDates['disabled_special_days'])}checked="checked"{/if}/>
                        <p>{l s='Fri' mod='mpbooking'}</p>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" name="disabled_special_days[]" value="6" {if isset($bookingDisableDates['disabled_special_days']) && $bookingDisableDates['disabled_special_days'] && in_array('6', $bookingDisableDates['disabled_special_days'])}checked="checked"{/if}/>
                        <p>{l s='Sat' mod='mpbooking'}</p>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" name="disabled_special_days[]" value="0" {if isset($bookingDisableDates['disabled_special_days']) && $bookingDisableDates['disabled_special_days'] && in_array('0', $bookingDisableDates['disabled_special_days'])}checked="checked"{/if}/>
                        <p>{l s='Sun' mod='mpbooking'}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {*disable specific days*}
    <div class="row">
        <hr>
        <div class="alert alert-info">
            <i class="material-icons">&#xE887;</i> {l s='If you want to disable bookings for some specific dates. Then enable the option and select the dates for which you want to disable the bookings. For Ex.- you can disable bookings for 25 Dec then select the date to disable the bookings.' mod='mpbooking'}
        </div>
        <div class="form-group row">
            <label class="col-lg-3 control-label">{l s='Disable Specific Dates' mod='mpbooking'}</label>
            <div class="col-lg-6">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input class="is_disabled_specific_dates_exists" type="radio" value="1" id="disable_specific_days_active_on" name="disable_specific_days_active" {if isset($DISABLE_SPECIFIC_DAYS_ACTIVE) && $DISABLE_SPECIFIC_DAYS_ACTIVE} checked="checked"{/if}>
                    <label for="disable_specific_days_active_on">{l s='Yes' mod='mpbooking'}</label>
                    <input class="is_disabled_specific_dates_exists" type="radio" value="0" id="disable_specific_days_active_off" name="disable_specific_days_active" {if !isset($DISABLE_SPECIFIC_DAYS_ACTIVE) || !$DISABLE_SPECIFIC_DAYS_ACTIVE} checked="checked"{/if}>
                    <label for="disable_specific_days_active_off">{l s='No' mod='mpbooking'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
        <div class="form-group disabled_specific_dates row" {if !isset($DISABLE_SPECIFIC_DAYS_ACTIVE) || !$DISABLE_SPECIFIC_DAYS_ACTIVE} style="display:none;" {/if}>
            <div id="stats_search_form" class="row">
                <div class="form-group col-sm-12">
                    <div class="col-sm-3">
                        <label class="contrl-label required form-control-static">{l s='Select Duration' mod='mpbooking'} :</label>
                    </div>
                    <div class="col-sm-3">
                        <input id="date-start" {if isset($date_from) && $date_from}value="{$date_from|date_format:"%d-%m-%Y"}"{/if} type="text" class="form-control datepicker-input" autocomplete="off" placeholder="From" name="date_from" readonly>
                    </div>
                    <div class="col-sm-3">
                        <input id="date-end" {if isset($date_to) && $date_to}value="{$date_to|date_format:"%d-%m-%Y"}"{/if} type="text" class="form-control datepicker-input" autocomplete="off" placeholder="From" name="date_to" readonly>
                    </div>
                    <div class="col-sm-3 form-group">
                        <button type="submit" class="btn btn-success wk_btn_extra form-control-submit" name="submitDateRange">
                            <span>{l s='Save' mod='mpbooking'}</span>
                        </button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="disabled_specific_dates_json" id="disabled_specific_dates_json" {if isset($bookingDisableDates['disabled_dates_slots']) && $bookingDisableDates['disabled_dates_slots']} value="{$bookingDisableDates['disabled_dates_slots']|escape:'html':'UTF-8'}"{else} value="" {/if}/>
            <div style="clear:both;"></div>
            <div class="col-sm-12">
                <div class="row selected_disabled_dates">
                    {if isset($bookingDisableDates['disabled_dates_slots_array']) && $bookingDisableDates['disabled_dates_slots_array']}
                        {foreach $bookingDisableDates['disabled_dates_slots_array'] as $key => $disabedRange}
                            <div class="col-sm-4">
                                {if isset($product_info.booking_type) && $product_info.booking_type == 1}
                                    <div class="disabled_date_container">
                                        <span>{$disabedRange['date_from']} &nbsp; {l s='To' mod='mpbooking'} &nbsp; {$disabedRange['date_to']}</span><span  class="remove_disable_date" remove-date-index="{$key}"><i class="material-icons">&#xE888;</i></span>
                                    </div>
                                {elseif isset($product_info.booking_type) && $product_info.booking_type == 2}
                                    <div class="disabled_date_container">
                                        <span><span>{$disabedRange['date_from']} &nbsp; {l s='To' mod='mpbooking'} &nbsp; {$disabedRange['date_to']}</span>
                                        <span class="remove_disable_date" remove-date-index="{$key}">
                                            <i class="material-icons">&#xE888;</i>
                                        </span>
                                        <span date_end="{$disabedRange['date_to']}" date_start="{$disabedRange['date_from']}" class="edit_disable_date_slots">
                                            <i class="material-icons">&#xE254;</i>
                                        </span>
                                    </div>
                                {/if}
                            </div>
                        {/foreach}
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>

{*Disable Dates Model*}
<div class="modal fade" id="disableTimeSlotsModal" tabindex="-1" role="dialog" aria-labelledby="disableTimeSlotsLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close margin-right-10" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title"><i class="icon-clock-o"></i>&nbsp; {l s='Disable Selected Slots' mod='mpbooking'}</h4>
			</div>
			<div class="modal-body booking-disable-slots-content">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success wk_btn_extra disableSlotsModalSubmit">{l s='Done' mod='mpbooking'}</button>
			</div>
		</div>
	</div>
</div>
{*END*}