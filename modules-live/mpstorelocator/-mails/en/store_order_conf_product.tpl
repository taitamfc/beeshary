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

<tr>
    <td>
        <font size="2" face="Open-sans, sans-serif" color="#555454">
            <p data-html-only="1" style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">
                Store wise product details
            </p>
        </font>
    </td>
</tr>
{foreach $list as $store}
<tr>
    <td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
        <table class="table" style="width:100%">
            <tr>
                <td width="10" style="padding:7px 0">&nbsp;</td>
                <td style="padding:7px 0">
                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                        <p data-html-only="1" style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">
                            {$store.storeDetails.name}						</p>
                         <table class="table" style="width:100%">
                            <tr>
                                <td style="vertical-align: top">    <span style="color:#777">
                                        <span style="color:#333"><strong>Address:</strong></span> <br>
                                        <span>{$store.storeDetails.address1} {$store.storeDetails.address2}</span><br>
                                        <span>{$store.storeDetails.city_name}, {$store.storeDetails.state_name} <br>{$store.storeDetails.zip_code}</span><br>
                                        <span>{$store.storeDetails.country_name}</span><br>
                                    </span>
                                </td>
                                {if isset($store.storeDetails.payment_options) && $store.storeDetails.payment_options}
                                <td style="vertical-align: top">
                                    <span style="color:#333"><strong>Payment:</strong></span> <br>
                                    {foreach $store.storeDetails.payment_options as $payment}
                                        <span>{$payment.payment_name}</span><br>
                                    {/foreach}
                                </td>
                                {/if}
                            </tr>
                            <tr>
                                <td>
                                    <span style="color:#333"><strong><a target="_blank" href="http://maps.google.com/maps?saddr=&daddr=({$store.storeDetails.latitude}, {$store.storeDetails.longitude})">Get Directions</a></strong></span>
                                </td>
                            </tr>
                        </table>
                    </font>
                </td>
                <td width="10" style="padding:7px 0">&nbsp;</td>
            </tr>
            <tr>
                <td width="10" style="padding:7px 0">&nbsp;</td>
                <td style="padding:7px 0">
                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                        <table class="table table-recap" bgcolor="#ffffff" style="width:100%;border-collapse:collapse"><!-- Title -->
                            <tr>
                                <th bgcolor="#f8f8f8" style="border:1px solid #D6D4D4;background-color: #fbfbfb;color: #333;font-family: Arial;font-size: 13px;padding: 10px;">Reference</th>
                                <th bgcolor="#f8f8f8" style="border:1px solid #D6D4D4;background-color: #fbfbfb;color: #333;font-family: Arial;font-size: 13px;padding: 10px;">Product</th>
                                <th bgcolor="#f8f8f8" style="border:1px solid #D6D4D4;background-color: #fbfbfb;color: #333;font-family: Arial;font-size: 13px;padding: 10px;">Quantity</th>
                                {if $store.storeDetails.enable_date}
                                <th bgcolor="#f8f8f8" style="border:1px solid #D6D4D4;background-color: #fbfbfb;color: #333;font-family: Arial;font-size: 13px;padding: 10px;">Pick Up Details</th>
                                {/if}
                            </tr>
                            <tr>
                                <td colspan="{if $store.storeDetails.enable_date}4{else}3{/if}" style="border:1px solid #D6D4D4;text-align:center;color:#777;padding:7px 0">
                                    &nbsp;&nbsp;
                                    {foreach $store.products as $index => $productDetails}
                                        {foreach $productDetails as $key => $product}
                                            <tr>
                                                <td style="border:1px solid #D6D4D4;">
                                                    <table class="table">
                                                        <tr>
                                                            <td width="10">&nbsp;</td>
                                                            <td>
                                                                <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                                    <strong>{$orderedProducts.$product.product_reference}</strong>
                                                                </font>
                                                            </td>
                                                            <td width="10">&nbsp;</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td style="border:1px solid #D6D4D4;">
                                                    <table class="table">
                                                        <tr>
                                                            <td width="10">&nbsp;</td>
                                                            <td>
                                                                <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                                    <strong>{$orderedProducts.$product.product_name}</strong>
                                                                </font>
                                                            </td>
                                                            <td width="10">&nbsp;</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td style="border:1px solid #D6D4D4;">
                                                    <table class="table">
                                                        <tr>
                                                            <td width="10">&nbsp;</td>
                                                            <td>
                                                                <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                                    <strong>{$orderedProducts.$product.product_quantity}</strong>
                                                                </font>
                                                            </td>
                                                            <td width="10">&nbsp;</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                {if $key == 0 && $store.storeDetails.enable_date}
                                                    <td rowspan="{$store.count[$index]}" style="border:1px solid #D6D4D4;">
                                                        <table class="table">
                                                            <tr>
                                                                <td width="10">&nbsp;</td>
                                                                <td align="left">
                                                                    <div>
                                                                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                                            <strong>Pick up date</strong>
                                                                            {$store.pickup_date[$index]}
                                                                    </font>
                                                                    </div>
                                                                    {if $store.storeDetails.enable_time}
                                                                    <div>
                                                                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                                        <strong>Pick up time</strong>
                                                                        {$store.pickup_time[$index]}
                                                                    </font>
                                                                    </div>
                                                                    {/if}
                                                                </td>
                                                                <td width="10">&nbsp;</td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                {/if}
                                            </tr>
                                        {/foreach}
                                    {/foreach} 
                                </td>
                            </tr>
                            {* <!-- <tr>
                                <td colspan="5" style="border:1px solid #D6D4D4;text-align:center;color:#777;padding:7px 0">
                                    &nbsp;&nbsp;{discounts}
                                </td>
                            </tr> --> *}
                            <tr class="conf_body">
                                <td bgcolor="#f8f8f8" colspan="{if $store.storeDetails.enable_date}3{else}2{/if}" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
                                    <table class="table" style="width:100%;border-collapse:collapse">
                                        <tr>
                                            <td width="10" style="color:#333;padding:0">&nbsp;</td>
                                            <td align="right" style="color:#333;padding:0">
                                                <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                    <strong>Products</strong>
                                                </font>
                                            </td>
                                            <td width="10" style="color:#333;padding:0">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                                <td bgcolor="#f8f8f8" align="right" colspan="1" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
                                    <table class="table" style="width:100%;border-collapse:collapse">
                                        <tr>
                                            <td width="10" style="color:#333;padding:0">&nbsp;</td>
                                            <td align="right" style="color:#333;padding:0">
                                                <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                    {$store.store_total}
                                                </font>
                                            </td>
                                            <td width="10" style="color:#333;padding:0">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr class="conf_body">
                                <td bgcolor="#f8f8f8" colspan="{if $store.storeDetails.enable_date}3{else}2{/if}" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
                                    <table class="table" style="width:100%;border-collapse:collapse">
                                        <tr>
                                            <td width="10" style="color:#333;padding:0">&nbsp;</td>
                                            <td align="right" style="color:#333;padding:0">
                                                <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                    <strong>Discounts</strong>
                                                </font>
                                            </td>
                                            <td width="10" style="color:#333;padding:0">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                                <td bgcolor="#f8f8f8" colspan="1" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
                                    <table class="table" style="width:100%;border-collapse:collapse">
                                        <tr>
                                            <td width="10" style="color:#333;padding:0">&nbsp;</td>
                                            <td align="right" style="color:#333;padding:0">
                                                <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                    {$store.store_discount}
                                                </font>
                                            </td>
                                            <td width="10" style="color:#333;padding:0">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr class="conf_body">
                                <td bgcolor="#f8f8f8" colspan="{if $store.storeDetails.enable_date}3{else}2{/if}" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
                                    <table class="table" style="width:100%;border-collapse:collapse">
                                        <tr>
                                            <td width="10" style="color:#333;padding:0">&nbsp;</td>
                                            <td align="right" style="color:#333;padding:0">
                                                <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                    <strong>Total paid</strong>
                                                </font>
                                            </td>
                                            <td width="10" style="color:#333;padding:0">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                                <td bgcolor="#f8f8f8" colspan="1" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
                                    <table class="table" style="width:100%;border-collapse:collapse">
                                        <tr>
                                            <td width="10" style="color:#333;padding:0">&nbsp;</td>
                                            <td align="right" style="color:#333;padding:0">
                                                <font size="4" face="Open-sans, sans-serif" color="#555454">
                                                    {$store.totalPaid}
                                                </font>
                                            </td>
                                            <td width="10" style="color:#333;padding:0">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </font>
                </td>
                <td width="10" style="padding:7px 0">&nbsp;</td>
            </tr>
        </table>
    </td>
</tr>
{/foreach}