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

<div class="row disable_dates_information_block">
    {*To Show search form in the page*}
    <div id="stats_search_form" class="row">
        <div class="form-group col-sm-12">
            <div class="col-sm-2">
                <label class="contrl-label required form-control-static">{l s='Select Duration' mod='mpbooking'} :</label>
            </div>
            <div class="col-sm-3">
                <input id="search_date_from" {if isset($availablity_date_from) && $availablity_date_from}value="{$availablity_date_from|date_format:"%d-%m-%Y"}"{/if} type="text" class="form-control datepicker-input" autocomplete="off" placeholder="From" name="availability_date_from" readonly>
            </div>
            <div class="col-sm-3">
                <input id="search_date_to" {if isset($availablity_date_to) && $availablity_date_to}value="{$availablity_date_to|date_format:"%d-%m-%Y"}"{/if} type="text" class="form-control datepicker-input" autocomplete="off" placeholder="From" name="availability_date_to" readonly>
            </div>
            <div class="col-sm-3 form-group">
                <button type="submit" class="btn btn-primary pull-right" name="availability-search-submit" id="availability-search-submit">
                    <span>{l s='Search' mod='mpbooking'}</span>
                </button>
            </div>
        </div>
    </div>
    <hr>
    {*To Show calender in the page*}
    <div id="stats-calendar-info" class="row">
        <div id="stats-calendar" class="col-sm-12">
        </div>
    </div>
    <hr class="hr_style col-sm-8">
    {*To Show rooms representation colors*}
    <div id="rooms_presentation" class="row">
        <div class="col-sm-12 presentation_div">
            <div class="row">
                <div class="col-sm-6">
                    <p><i class="icon-circle" style="color:#7EC77B;"></i>&nbsp;&nbsp;{l s='If minimum one quantity of product/any slot on date is available' mod='mpbooking'}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <p><i class="icon-circle" style="color:#CD5D5D;"></i>&nbsp;&nbsp;{l s='If no quantity is available of product/any slot on date' mod='mpbooking'}</p>
                </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
                {if isset($productBookingType) && $productBookingType == $booking_type_time_slot}
                    <i><p>** {l s='Faded dates are indicating all slots in the date are disabled.' mod='mpbooking'}</p></i>
                {else}
                    <i><p>** {l s='Faded dates are indicating the disabled dates or disabled days.' mod='mpbooking'}</p></i>
                {/if}
            </div>
          </div>
        </div>
    </div>
</div>