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

{if isset($orderBookingProducts) && $orderBookingProducts}
    <div class="box-account box-recent">
        <div class="box-head">
            <div>
                <h4><i class="material-icons">date_range</i> {l s='Booking Products Details' mod='mpbooking'}</h4>
            </div>
        </div>
        <div class="clearfix box-content wk-order-table">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{l s='Image' mod='mpbooking'}</th>
                            <th>{l s='Product Name' mod='mpbooking'}</th>
                            <th>{l s='Duration' mod='mpbooking'}</th>
                            <th class="text-xs-center">{l s='Quantity' mod='mpbooking'}</th>
                            <th>{l s='Unit price (tax excl.)' mod='mpbooking'}</th>
                            <th>{l s='Total price (tax excl.)' mod='mpbooking'}</th>
                            <th>{l s='Total price (tax incl.)' mod='mpbooking'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$orderBookingProducts item=product}
                            {if isset($product.booking_product_data) && $product.booking_product_data}
                                {foreach from=$product.booking_product_data item=bookingProduct}
                                    <tr>
                                        <td>
                                            <span class="image">
                                                {if isset($product.image) && $product.image->id}{$product.image_tag nofilter}{/if}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{$product.mpBookingProductLink}" target="_blank">
                                                <span class="productName">{$product['product_name']}</span>
                                            </a>
                                        </td>
                                        <td>
                                            {if $bookingProduct['booking_type'] == $booking_type_date_range}
                                                {$bookingProduct['date_from']|date_format:"%e %b, %Y"}</br> {l s='To' mod='mpbooking'} </br> {$bookingProduct['date_to']|date_format:"%e %b, %Y"}
                                            {else}
                                                {$bookingProduct['date_from']|date_format:"%e %b, %Y"}</br>
                                                {$bookingProduct['time_from']} - {$bookingProduct['time_to']}
                                            {/if}
                                        </td>
                                        <td class="text-xs-center">{$bookingProduct['quantity']}</td>
                                        <td>{$bookingProduct['unit_feature_price_tax_excl_formated']}</td>
                                        <td>{$bookingProduct['total_range_feature_price_tax_excl_formated']}</td>
                                        <td>{$bookingProduct['total_range_feature_price_tax_incl_formated']}</td>
                                    </tr>
                                {/foreach}
                            {/if}
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{/if}