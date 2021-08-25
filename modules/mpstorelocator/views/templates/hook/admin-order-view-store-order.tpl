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

{if isset($products) && $products}
    <div class="panel">
        <div class="panel-heading">{l s='Store product pickup location' mod='wkstorelocator'}</div>
        <table class="table" id="wk_store_products">
            {foreach $orderedProducts as $orderedProduct}
                {assign var=count value=1}
                {foreach $orderedProduct.products as $product}
                    <tr>
                        {if isset($numOfStores)}
                        {else}<td {if $count == ($orderedProduct.count)}class="wk-border-bottom"{/if}><img src="{$products.$product.imageLink}" class="img-responsive" alt="{$products.$product.product_name}"/></td>
                        <td {if $count == ($orderedProduct.count)}class="wk-border-bottom"{/if}>
                            <div>{$products.$product.product_name}</div>
                            {if isset($products.$product.product_attr_name)}
                                <div>{$products.$product.product_attr_name}</div>
                            {/if}
                        </td>
                        {/if}
                        {if $count == 1}
                        <td rowspan="{$orderedProduct.count}" class="wk-border-bottom">
                            <div class="wkstore-pickup-name"><b> {$stores[$orderedProduct.id_store].name} </b></div>
                                <div> {$stores[$orderedProduct.id_store].address1} {$stores[$orderedProduct.id_store].address2} </div>
                                <div> {$stores[$orderedProduct.id_store].city_name}
                                {if !empty($stores[$orderedProduct.id_store].state_name)}
                                    , {$stores[$orderedProduct.id_store].state_name}
                                {/if}
                                {$stores[$orderedProduct.id_store].zip_code}
                                </div><div> {$stores[$orderedProduct.id_store].country_name}</div>
                            <div>
                        </td>
                        <td rowspan="{$orderedProduct.count}" class="wk-border-bottom">
                            <div class="table" id="wk_pickup_details">
                                {if $orderedProduct.enablePickUpDate}
                                    <div class="row">
                                        <span class="col-md-3"><b>{l s='Pick Up Date' mod='wkstorelocator'}</b></span>
                                        <span class="col-md-8">
                                        {$orderedProduct.pickup_date}
                                        </span>
                                    </div>
                                    {if $orderedProduct.enablePickUpTime}
                                        <div class="row">
                                            <span class="col-md-3"><b>{l s='Pick Up Timing' mod='wkstorelocator'}</b></span>
                                            <span class="col-md-8">
                                            {$orderedProduct.pickup_time}
                                            </span>
                                        </div>
                                    {/if}
                                {/if}
                                {if $stores[$orderedProduct.id_store].payment_options && $orderedProduct.enablePaymentOptions}
                                <div class="row">
                                    <span class="col-md-4"><b>{l s='Payment Method' mod='wkstorelocator'}</b></span>
                                    <span class="col-md-8">
                                        {foreach $stores[$orderedProduct.id_store].payment_options as $payment}
                                            <img src="{$imagePath}/{$payment.id_wk_store_pay}.jpg" class="img-responsive wk-inline-block" title="{$payment.payment_name}"/>
                                        {/foreach}
                                    </span>
                                </div>
                                {/if}
                            </div>
                        </td>
                        {/if}
                        <span class="hidden-xs-up hidden">{$count++}</span>
                    </tr>
                {/foreach}
            {/foreach}
        </table>
    </div>
{/if}
