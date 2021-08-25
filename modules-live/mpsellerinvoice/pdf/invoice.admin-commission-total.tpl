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
		<td class="white" width="100%">
			{l s='Order Reference' mod='mpsellerinvoice'} ({$order->getUniqReference()})
		</td>
	</tr>
	{if isset($productDiscountsTaxExcl)}
		<tr class="bold">
			<td class="white">
				{l s='Total Order Discounts :' mod='mpsellerinvoice'}
				{$productDiscountsTaxExcl}
			</td>
		</tr>
    {/if}
    {if isset($totalAdminCommission)}
		<tr class="bold">
			<td class="white">
				{l s='Commission :' mod='mpsellerinvoice'}
				{$totalAdminCommission}
			</td>
		</tr>
    {/if}
    <tr class="bold">
		<td class="white">
			{l s='Tax :' mod='mpsellerinvoice'}
			{$totalAdminCommissionTax}
		</td>
	</tr>
    <tr class="bold">
		<td class="white">
			{l s='Total :' mod='mpsellerinvoice'}
			{$totalCommission}
		</td>
	</tr>
</table>
