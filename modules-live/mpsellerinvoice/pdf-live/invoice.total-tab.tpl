{*
* 2010-2016 Webkul
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
*  @author Webkul IN <support@webkul.com>
*  @copyright  2010-2016 Webkul IN
*}
<table id="total-tab" width="100%">
	<tr>
		<td class="grey" width="70%">
			<!--{l s='Seller Products Total' pdf='true'}-->
			{l s='Total Produits' pdf='true'}
		</td>
		<td class="white" width="30%">
			{$footer.products_before_discounts_tax_incl}
		</td>
	</tr>
	{if isset($footer.seller_voucher)}
		{if $footer.seller_voucher > 0}
		<tr>
			<td class="grey" width="70%">
				{l s='Seller Voucher Total' pdf='true'}
			</td>
			<td class="white" width="30%">
				- {$footer.seller_voucher}
			</td>
		</tr>
		{/if}
	{/if}
	<!-- <tr>
		<td class="grey" width="70%">
			{l s='Seller Products Tax Total' pdf='true'}
		</td>
		<td class="white" width="30%">
			{$footer.product_taxes}
		</td>
	</tr> -->
	{* {assign var=sellertotal value=$footer.products_before_discounts_tax_excl+$footer.product_taxes} *}
	<!-- <tr class="bold big">
		<td class="grey">
			{l s='Total' pdf='true'}
		</td>
		<td class="white">
			{$sellerTotal}
		</td>
	</tr> -->
</table>

<div width="100%;"></div>

<table id="total-tab" width="100%">
	<!-- <tr>
		<td class="white" width="100%">
			{l s='Total Order Details' pdf='true'} ({$order->getUniqReference()})
		</td>
	</tr> -->
	{if $footer.product_discounts_tax_excl > 0}
		<tr>
			<td class="grey" width="70%">
				{l s='Total Order Discounts' pdf='true'}
			</td>
			<td class="white" width="30%">
				- {$footer.product_discounts_tax_excl}
			</td>
		</tr>

	{/if}
	{if !$order->isVirtual()}
	<tr>
		<td class="grey" width="70%">
			<!--{l s='Total Order Shipping' pdf='true'}-->
			{l s='Frais de livraison' pdf='true'}
		</td>
		<td class="white" width="30%">
			{if $footer.shipping_tax_excl > 0}
				{$footer.shipping_tax_excl}
			{else}
				<!--{l s='Free Shipping' pdf='true'}-->
				{l s='Gratuit' pdf='true'}
			{/if}
		</td>
	</tr>
	{/if}
	{if $footer.wrapping_tax_excl > 0}
		<tr>
			<td class="grey">
				{l s='Total Wrapping Cost' pdf='true'}
			</td>
			<td class="white">{$footer.wrapping_tax_excl}</td>
		</tr>
	{/if}
	<tr class="bold">
		<td class="grey">
			{l s='Total (Tax excl.)' pdf='true'}
		</td>
		<td class="white">
			{$footer.total_paid_tax_excl}
		</td>
	</tr>
	{if $footer.total_taxes > 0}
	<tr class="bold">
		<td class="grey">
			{l s='Total Tax' pdf='true'}
		</td>
		<td class="white">
			{$footer.total_taxes}
		</td>
	</tr>
	{/if}
	<tr class="bold big">
		<td class="grey">
			{l s='Total' pdf='true'}
		</td>
		<td class="white">
			{$footer.total_paid_tax_incl}
		</td>
	</tr>
</table>
