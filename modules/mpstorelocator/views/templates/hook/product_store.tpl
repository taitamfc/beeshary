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

<div id="wk_store_pickup_products">
    <input type="hidden" name="confirmDeliveryOption" value="1"/>
    {foreach from=$products key=key item=product}
        {if $product.available_store}
            <div data-id-product="{$product.id_product}" data-id-product-attr="{$product.id_product_attribute}" class="row wkstore-pickup-padding{if $key == 0} wk-padding-top-0{/if}{if $products|@count != $key+1} wkstore-pickup-border-bottom{/if}">
                <div class="col-sm-4 col-xs-5 col-md-2">
                    <img src="{$product.imageLink}" class="img-responsive" width="93px"></img>
                </div>
                <div class="col-sm-4 col-xs-6 col-md-3">
                    <div class="wk-product-name">{$product.name}</div>
                    {if isset($product.attributes_small)}<div class="wk-product-attr-name">{$product.attributes_small}</div>{/if}
                </div>
                {if isset($product.id_store_pickup)}
                    <input type="hidden" name="wk_store_id_pickup_{$product.id_product}_{$product.id_product_attribute}" value="{$product.id_store_pickup}"/>
                {/if}
                <input type="hidden" id="wk_store_id_{$product.id_product}_{$product.id_product_attribute}" class="wk_store_id" apply_for_all ="{if isset($product.apply_for_all) && $products|@count > 1}{$product.apply_for_all}{else}0{/if}" name="wk_store_id_{$product.id_product}_{$product.id_product_attribute}" {if isset($product.id_store)}value="{$product.id_store}"{/if} {if isset($product.id_seller)}id-seller="{$product.id_seller}"{/if}/>
                <div class="col-sm-4 col-xs-6 col-md-4 wk-padding-0">
                    <a class="wk-store-select" data-toggle="modal" data-target="#wkshowStore" id-product="{$product.id_product}" id-product-attr="{$product.id_product_attribute}">
                        {l s='Select store' mod='mpstorelocator'}
                    </a>
                    {* <input type="text" class="form-control" name="wk_store_date_pickup" id="wk_store_date_pickup"> *}
                    <span id="wk_store_details_{$product.id_product}_{$product.id_product_attribute}" class="wk_store_details">
                        {if isset($product.store_details)}
                            <div class="wkstore-pickup-name"> {$product.store_details.name} </div>
                            <div> {$product.store_details.address1} {$product.store_details.address2} </div>
                            <div> {$product.store_details.city_name}
                            {if !empty($product.store_details.state_name)}
                                , {$product.store_details.state_name}
                            {/if}
                            {$product.store_details.zip_code}
                            </div><div> {$product.store_details.country_name}</div>
                        {/if}
                    </span>
                    <span id="wk_store_payment_details_{$product.id_product}_{$product.id_product_attribute}" class="wk_store_payment_details" style="display:none">
                        {if isset($product.paymentOptions)}
                            {include file="module:mpstorelocator/views/templates/front/partials/store_payment_options.tpl" paymentOptions=$product.paymentOptions imagePath=$imagePath}
                        {/if}
                    </span>
                </div>
                {if $MP_STORE_PICKUP_DATE}
                <div class="col-sm-4 col-xs-6 col-md-3 wk-padding-right-0">
                    <div id="wk_pickup_datetime_{$product.id_product}_{$product.id_product_attribute}" class="wk_pickup_datetime" name="wk_pickup_datetime_{$product.id_product}_{$product.id_product_attribute}" {if !isset($product.id_store_pickup_product) || (isset($product.enable_date) && $product.enable_date == 0)} style="display: none;"{/if}>
                        <span class="wk-pick">{l s='Pick Up Date:' mod='mpstorelocator'}</span>
                        <input type="hidden" class="form-control wk_store_date_pickup" name="wk_store_date_pickup_{$product.id_product}_{$product.id_product_attribute}" class="" id="wk_store_date_pickup_{$product.id_product}_{$product.id_product_attribute}" {if isset($product.store_pickup_date)}value="{$product.store_pickup_date}"{/if}>
                        <div id="wk_pickup_date_{$product.id_product}_{$product.id_product_attribute}" class="wk_pickup_date">
                            {if isset($product.store_pickup_date)}{$product.store_pickup_date}{/if}
                        </div>

                        <span class="wk-pick"{if !isset($product.id_store_pickup_product) || (isset($product.enable_time) && $product.enable_time == 0)} style="display: none;"{/if}>{l s='Pick Up Time:' mod='mpstorelocator'}</spn>
                        <input type="hidden" class="form-control wk_store_time_pickup" name="wk_store_time_pickup_{$product.id_product}_{$product.id_product_attribute}" class="" id="wk_store_time_pickup_{$product.id_product}_{$product.id_product_attribute}" {if isset($product.store_pickup_time)}value="{$product.store_pickup_time}"{/if}>
                        <div id="wk_pickup_time_{$product.id_product}_{$product.id_product_attribute}" class="wk_pickup_time">
                            {if isset($product.store_pickup_time)}{$product.store_pickup_time}{/if}
                        </div>
                        {* <div id="wk_pickup_time_{$product.id_product}">
                        </div> *}
                    </div>
                </div>
                {/if}
            </div>
        {/if}
    {/foreach}
    {if $otherPickupProducts}
        <div class="col-md-12 alert alert-warning">
            {l s='Other products will be shipped by ' mod='mpstorelocator'}
            "{$carrierName}"
            {l s=' shipping method' mod='mpstorelocator'}
        </div>
    {/if}
</div>

<div class="modal fade" id="wkshowStore" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <div class="row">
            <div class="wk-position-relative">
            <div class="wk-linear-gradient">
                <div id="search_city_block">
                    <input id="wk_current_location" class="form-control" type="text" value="" placeholder="{l s='Enter your current location' mod='mpstorelocator'}">
                </div>
            </div>
            </div>
        </div>
        <div class="row">
            <div id="map-canvas" style="height: 400px">
            </div>
        </div>
        <div class="row">
            <div id="wk_selected_store_address" class="margin-top-10 col-md-6"></div>
            <div class="col-md-6 margin-top-10" id="wk_store_payment_info">
            </div>
        </diV>
        <input type="hidden" id="selectedStoreId" name="selectedStoreId">
        {if $MP_STORE_PICKUP_DATE}
        <div class="row">
            <div class="wk-position-relative">
            <div class="wk-linear-gradient">
                <div class="clearfix" id="wk_store_pickup">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div>
                                    <input type="text" class="form-control" name="wk_store_date_pickup" id="wk_store_date_pickup" placeholder="{l s='Select Date' mod='mpstorelocator'}" readonly>
                                    <div class="col-md-12" id="wk_store_date_pickup_container" style="poistion:relative">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div>
                                    <input type="text" class="form-control" name="wk_store_time_pickup" id="wk_store_time_pickup" placeholder="{l s='Select Time' mod='mpstorelocator'}" readonly>
                                    <div class="col-md-12" id="wk_store_time_pickup_container" style="poistion:relative">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
      </div>
      {/if}
      <div class="modal-footer">
        <span id="apply_for_all">
            <input type="checkbox" name="apply_for_all" id="wk_apply_for_all" value="{if $products|@count == 1}0{/if}"/>
            <label for="wk_apply_for_all">{l s='Apply for all products' mod='mpstorelocator'}</label>
        </span>
        <button type="button" id="wk_set_store" class="btn btn-primary">
            {l s='Set' mod='mpstorelocator'}
        </button>
      </div>
    </div>
  </div>
</div>