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

{if !empty($storeLocations)}
<div id="wk_store_content" class="">
{foreach $storeLocations as $store}
<div class="wk_store col-md-12" id="{$store.id}" lat="{$store.latitude|addslashes}" lng="{$store.longitude|addslashes}">
    <div class="row">
        <div class="col-md-6">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td class="wkstore-details-heading">{l s='Address ' mod='wkstorelocator'}</td>
                    <td>: </td>
                    <td>
                        <div>{$store.address1} {$store.address2}</div>
                        <div>{$store.city_name}, {$store.state_name} {$store.zip_code}</div>
                        <div>{$store.country_name}</div>
                    </td>
                </tr>
                <tr {if !isset($store.distance)}style="display: none"{/if}>
                    <td class="wkstore-details-heading">{l s='Distance ' mod='wkstorelocator'}</td>
                        <td>: </td>
                        <td><span id="store_distance_{$store.id}">{if isset($store.distance)}{$store.distance}{/if}</span></td>
                </tr>
                {if !empty($store.email) && isset($displayEmail) && $displayEmail}
                <tr>
                    <td class="wkstore-details-heading">{l s='Email ' mod='wkstorelocator'}</td>
                    <td>: </td>
                    <td>{$store.email}</td>
                </tr>
                {/if}
                {if !empty($store.phone) && isset($displayContactDetails) && $displayContactDetails}
                <tr>
                    <td class="wkstore-details-heading">{l s='Contact Details ' mod='wkstorelocator'}</td>
                    <td>: </td>
                    <td>{$store.phone}</td>
                </tr>
                {/if}
                {if !empty($store.fax) && isset($displayFax) && $displayFax}
                <tr>
                    <td class="wkstore-details-heading">{l s='Fax No. ' mod='wkstorelocator'}</td>
                    <td>: </td>
                    <td>{$store.fax}</td>
                </tr>
                {/if}
                {if isset($paymentOptions) && $paymentOptions}
                    <tr>
                        <td class="wkstore-details-heading">{l s='Payment Method ' mod='wkstorelocator'}</td>
                        <td>: </td>
                        <td>
                            {foreach $paymentOptions as $payment}
                                <img class="wk-margn-3" src="{$imagePath}/{$payment.id_wk_store_pay}.jpg" title="{$payment.payment_name}"/>
                            {/foreach}
                        </td>
                    </tr>
                {/if}
            </table>
        </div>
        <div class="col-md-6 wk-border-left">
        {if isset($displayStoreTiming) && $displayStoreTiming}
        <table>
            <tr>
                <td class="wkstore-details-heading">{l s='Store Timing ' mod='wkstorelocator'}</td>
                <td>: </td>
                <td>
                    <div class="">
                    <table id="wk-store-timing">
                    {foreach from=$store.hours key=key item=hour}
                        <tr>

                        {if $key == $store.current_day}
                            <b><td>{$key}</td>
                            <td> : </td>
                            <td>
                                {if empty($hour)}
                                    {l s='Closed' mod='wkstorelocator'}
                                {else}
                                    {$hour}
                                {/if}
                            </td>
                            </b>
                        {else}
                            <td>{$key}</td>
                            <td> : </td>
                            <td>
                                {if empty($hour)}
                                    {l s='Closed' mod='wkstorelocator'}
                                {else}
                                    {$hour}
                                {/if}
                            </td>
                        {/if}
                        </tr>
                    {/foreach}
                        </table>
                    </div>
                {* </span> *}
                </td>
            </tr>
        </table>
        {/if}
        <a class="btn wkstore-btn" href="http://maps.google.com/maps?saddr={if isset($currentLocation)}({$currentLocation.lat},{$currentLocation.lng}){/if}&daddr=({$store.latitude},{$store.longitude})" id="store_direction_{$store.id}" id="store_direction_{$store.id}" target="_blank">
            <span class="">{l s='Get Directions' mod='wkstorelocator'}</span>
        </a>
    </div>
</div>
{/foreach}
</div>
{/if}