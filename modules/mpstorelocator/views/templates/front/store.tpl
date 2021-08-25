{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{block name='content'}
<div id='wk_store_locator' class="clearfix">
	<div class="wk-store-heading">{l s='Store Locator' mod='mpstorelocator'}</div>
    {if !empty($store_locations)}
        <div id="wrapper_store" class="clearfix">
            <div id="wrapper_content">
                <div id="wrapper_content_right" class="">
                    <div id="wk_store_details_page" class="controls">
                        <table cellpadding="0">
                            <tbody>
                                <tr>
                                    <td style="vertical-align: top">
                                        <img src="{$storeLogoImgPath}{$store_locations.0.id}.jpg" height="100px" class="wk-padding-right-15"/>
                                    </td>
                                    <td>
                                        <a href="{$link->getModuleLink('mpstorelocator', 'storedetails', ['id_store' => $store_locations.0.id])}" class="wkstore-name">
                                            {$store_locations.0.name}
                                        </a>
                                        <div class="padding-2">{$store_locations.0.address1} {$store_locations.0.address2}
                                        </div>
                                        <div class="padding-2">
                                        {$store_locations.0.city_name} 
                                        {if !empty($store_locations.0.state_name)}    
                                            {$store_locations.0.state_name}
                                        {/if}
                                        {if !empty($store_locations.0.zip_code)}
                                        , {$store_locations.0.zip_code}
                                        {/if}
                                        </div>
                                        <div class="padding-2">{$store_locations.0.country_name}</div>
                                        {if isset($displayStoreTiming) && $displayStoreTiming}
                                        <div class="padding-2">
                                            {l s='Store Timing ' mod='mpstorelocator'} :
                                            {if empty({$store_locations.0.current_hours})}
                                                {l s='Closed' mod='mpstorelocator'}
                                            {else}
                                                {$store_locations.0.current_hours}
                                            {/if}
                                        </div>
                                        {/if}
                                        {if !empty($store_locations.0.phone) && isset($displayContactDetails) && $displayContactDetails}
                                            <div class="padding-2">
                                                {l s='Contact' mod='mpstorelocator'} : {$store_locations.0.phone}
                                            </div>
                                        {/if}
                                        {if !empty($store_locations.0.email) && isset($displayEmail) && $displayEmail}
                                            <div class="padding-2">
                                                {l s='Email' mod='mpstorelocator'} : {$store_locations.0.email}
                                            </div>
                                        {/if}
                                        <div class="padding-2">
                                            {* <a class="btn wkstore-btn" href="http://maps.google.com/maps?saddr=&daddr=(28.62076410, 77.36392920)" target="_blank">{l s='Get directions' mod='mpstorelocator'}</a> *}
                                        </div>
                                    </td>
                                    <td>
                                        <a target="_blank" href="http://maps.google.com/maps?saddr=&daddr=({$store_locations.0.latitude}, {$store_locations.0.longitude})"><img src="{$directionImg}"/></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="map-canvas" class="col-md-12"></div>
                </div>
                <div id="wrapper_content_left" class="">
                    {if isset($store_locations)}
                    {include file="module:mpstorelocator/views/templates/front/store_detail.tpl" storeLocations=$store_locations}
                    {/if}
                </div>
            </div>
        </div>
        {if isset($products) && $products}
        <div class="wk_store_products_container">
            <div class="row">
                <div class="wk-store-heading col-md-4">{l s='Products' mod='mpstorelocator'}</div>
                <div class="col-md-3 float-md-right">
                    {include file="module:mpstorelocator/views/templates/front/mpstorelocator-nbr-product-page.tpl"}
                </div>
            </div>
            {* <div class="float-md-left wkstore-products tab-pane" id="wk_store_products"> *}
            <div class="float-md-left wkstore-products tab-pane" id="wk_store_products">
                {include file="module:mpstorelocator/views/templates/front/store_product_list.tpl" products=$products class='wkstore-products tab-pane' id='wk_store_products'}
            </div>
        </div>
        {/if}
    {else}
        <div class="alert alert-warning">{l s='No Store Found' mod='mpstorelocator'}</div>
    {/if}

</div>
{/block}