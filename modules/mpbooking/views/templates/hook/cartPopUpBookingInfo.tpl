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

{if isset($bookingProductCartInfo)}
	<style>
	.cart-product-shipping { display: none; }
	</style>
  {foreach $bookingProductCartInfo as $key => $productBooking}
    <div class="cart_pop_up_data range-period">
        <div class="booking-dates">
          {if $productBooking['booking_type'] == $booking_type_date_range}
            {$productBooking['date_from']|date_format:"%e %b, %Y"}&nbsp;
            {l s='To' mod='psbooking'}&nbsp;
            {$productBooking['date_to']|date_format:"%e %b, %Y"}
          {else}
            {$productBooking['date_from']|date_format:"%e %b, %Y"}&nbsp;
            {$productBooking['time_from']} - {$productBooking['time_to']}
          {/if}
        </div>
        <div class="booking-quantity">
          <span style="font-weight: bold;">{l s='Quantité totale' mod='psbooking'}</span>&nbsp;&nbsp;-&nbsp;&nbsp;{$productBooking['quantity']}
        </div>
        <div class="booking-price">
          <span style="font-weight: bold;">{l s='Prix total' mod='psbooking'}</span>&nbsp;&nbsp;-&nbsp;&nbsp;{$productBooking['totalPriceTE']} (HT)
        </div>
    </div>
  {/foreach}
{/if}
<style type="text/css">
  .product-name ~ p {
    display: none;
  }
  .booking-dates {
    font-weight:bold;
  }
  .cart_pop_up_data {
    font-size: 12px;
    color: #333;
    border-bottom:1px solid #333;
  }
</style>
