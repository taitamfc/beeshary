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
<br/>
<br/>
<table id="addresses-tab" cellspacing="0" cellpadding="0">
	<tr>
		<td width="30%">
			{if $isAdmin == 0}
				<span class="bold">{l s='Adresse du vendeur' mod='mpsellerinvoice'} </span><br/><br/>
				{if isset($shop_name)}{$shop_name}{/if}<br/>
				<span style="max-width: 5%;word-wrap:break-word;">{if isset($shop_details)}{$shop_details}{/if}</span><br/>
				{if isset($city)}{$city}{/if}<br/>
				{if isset($state)}{$state}{/if}{if isset($postcode)}- {$postcode}{/if}<br/>
				{if isset($country)}{$country}{/if}<br/>
				{if isset($sellerVat)}<span>{l s='TVA:' mod='mpsellerinvoice'} </span> {$sellerVat}<br/>{/if}
			{/if}
		</td>
		<td width="25%"></td>
		<td width="25%">
			{if $delivery_address}<span class="bold">{l s='Adresse de livraison' mod='mpsellerinvoice'}</span><br/><br/>
				{$delivery_address}
			{/if}
		</td>
		<td width="25%"><span class="bold">{l s='Adresse de facturation' mod='mpsellerinvoice'}</span><br/><br/>
			{$invoice_address}
		</td>
	</tr>
</table>
