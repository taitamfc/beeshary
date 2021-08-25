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
<table id="addresses-tab" cellspacing="0" cellpadding="0">
	<tr>
		<td width="33%">
			<span class="bold">{l s='Seller Address' mod='mpsellerinvoice'} </span><br/><br/>
			{if isset($shop_name)}{$shop_name}{/if}<br/>
			<span style="max-width: 5%;word-wrap:break-word;">
				{if isset($shop_details)}{$shop_details}{/if}
			</span><br/>
			{if isset($city)}{$city}{/if}<br/>
			{if isset($country)}{$country}{/if}			
		</td>
		<td width="33%">{if $delivery_address}<span class="bold">{l s='Delivery Address' mod='mpsellerinvoice'}</span><br/><br/>
				{$delivery_address}
			{/if}
		</td>
		<td width="33%"><span class="bold">{l s='Billing Address' mod='mpsellerinvoice'}</span><br/><br/>
				{$invoice_address}
		</td>
	</tr>
</table>
