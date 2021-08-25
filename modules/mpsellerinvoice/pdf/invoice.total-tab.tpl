{*
* 2010-2019 Webkul
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
*  @copyright  2010-2019 Webkul IN
*}
<table id="total-tab" width="100%">

	<tr>
		<td class="grey" width="70%">
			 {l s='Total HT' mod='mpsellerinvoice'}
		</td>
		<td class="white" width="30%">
			{$footer.products_before_discounts_tax_excl}
		</td>
	</tr>
	{if isset($footer.seller_voucher)}
		{if $footer.show_seller_voucher}
		<tr>
			<td class="grey" width="70%">
				 {l s='Total Bon de réduction' mod='mpsellerinvoice'}
			</td>
			<td class="white" width="30%">
				- {$footer.seller_voucher}
			</td>
		</tr>
		{/if}
	{/if}

	<tr>
		<td class="grey" width="70%">
			 {l s='Total TVA ' mod='mpsellerinvoice'}
		</td>
		<td class="white" width="30%">
			{$footer.product_taxes}
		</td>
	</tr>

	<tr class="bold big">
		<td class="grey">
			{l s='Total TTC' mod='mpsellerinvoice'}
		</td>
		<td class="white">
			{$sellerTotal}
		</td>
	</tr>
</table>

<div width="100%;"></div>
{if !$order->isVirtual()}
<table id="total-tab" width="100%">
	<tr>
		<td class="grey" width="100%">
			{l s='Total de la commande' mod='mpsellerinvoice'}
		</td>
	</tr>
	{if $footer.show_product_discounts_tax_excl }
		<tr>
			<td class="white" width="70%">
				{l s='Frais de livraison' mod='mpsellerinvoice'}
			</td>
			<td class="white" width="30%">
				- {$footer.product_discounts_tax_excl}
			</td>
		</tr>

	{/if}
	
		<tr>
			<td class="white" width="70%">
				{l s='Frais de livraison' mod='mpsellerinvoice'}
			</td>
			<td class="white" width="30%">
				{if $footer.shipping_tax_excl && !$footer.free_shipping}
					{$footer.shipping_tax_excl}
				{else}
					{l s='Livraison gratuite' mod='mpsellerinvoice'}
				{/if}
			</td>
		</tr>
	
	{if $footer.show_wrapping_tax_excl }
		<tr>
			<td class="white">
				{l s="Total coût de l'emballage" mod='mpsellerinvoice'}
			</td>
			<td class="white">{$footer.wrapping_tax_excl}</td>
		</tr>
	{/if}
	<tr class="bold">
		<td class="white">
			{l s='Total HT' mod='mpsellerinvoice'}
		</td>
		<td class="white">
			{$footer.total_paid_tax_excl}
		</td>
	</tr>
	{if $footer.total_taxes}
		<tr class="bold">
			<td class="white">
				{l s='Total TVA' mod='mpsellerinvoice'}
			</td>
			<td class="white">
				{$footer.total_taxes}
			</td>
		</tr>
	{/if}
	<tr class="bold big">
		<td class="white">
			{l s='Total TTC' mod='mpsellerinvoice'}
		</td>
		<td class="white">
			{$footer.total_paid_tax_incl}
		</td>
	</tr>
</table>
{/if}