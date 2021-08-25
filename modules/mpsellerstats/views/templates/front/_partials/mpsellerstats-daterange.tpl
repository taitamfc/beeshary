{*
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="row dashboard-date-content">
    <div class="col-md-7">
        <div class="btn-group margin-btm-10">
            {*Display current day graph*}
            <button data-trigger-function="Day" data-date-range="1" class="btn setPreselectDateRange {if $preselectDateRange == '1'}btn-primary{else}btn-default{/if}" type="button">
                {l s='Day' mod='mpsellerstats'}
            </button>
            {*Display current month graph*}
            <button data-trigger-function="Month" data-date-range="2" class="btn setPreselectDateRange {if $preselectDateRange == '2'}btn-primary{else}btn-default{/if}" type="button">
                {l s='Month' mod='mpsellerstats'}
            </button>
            {*Display current year graph*}
            <button data-trigger-function="Year" data-date-range="3" class="btn setPreselectDateRange {if $preselectDateRange == '3'}btn-primary{else}btn-default{/if}" type="button">
                {l s='Year' mod='mpsellerstats'}
            </button>
            {*Display previous day graph*}
            <button data-trigger-function="PreviousDay" data-date-range="4" class="btn setPreselectDateRange {if $preselectDateRange == '4'}btn-primary{else}btn-default{/if}" type="button">
                {l s='Day-1' mod='mpsellerstats'}
            </button>
            {*Display previous month graph*}
            <button data-trigger-function="PreviousMonth" data-date-range="5" class="btn setPreselectDateRange {if $preselectDateRange == '5'}btn-primary{else}btn-default{/if}" type="button">
                {l s='Month-1' mod='mpsellerstats'}
            </button>
            {*Display previous year graph*}
            <button data-trigger-function="PreviousYear" data-date-range="6" class="btn setPreselectDateRange {if $preselectDateRange == '6'}btn-primary{else}btn-default{/if}" type="button">
                {l s='Year-1' mod='mpsellerstats'}
            </button>
        </div>
    </div>
    <div class="col-md-5">
        <input type="hidden" id="dashboardDateFrom" name="dashboardDateFrom" value="{$dateFrom|escape:'htmlall':'UTF-8'|date_format:"%Y-%m-%d"}">
        <input type="hidden" id="dashboardDateTo" name="dashboardDateTo" value="{$dateTo|escape:'htmlall':'UTF-8'|date_format:"%Y-%m-%d"}">
        <input type="hidden" name="preselectDateRange" id="preselectDateRange" value="{$preselectDateRange|escape:'htmlall':'UTF-8'}">
        <div class="input-group">
            <span class="input-group-addon"><i class="material-icons">&#xE8A3;</i></span>
            <input type="text" class="form-control" id="date-range-picker">
        </div>
    </div>
</div>