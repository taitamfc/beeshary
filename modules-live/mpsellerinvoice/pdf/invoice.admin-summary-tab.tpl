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
<table id="summary-tab" width="100%">
	<tr>
		<th class="header small" valign="middle">{l s='Invoice Number' mod='mpsellerinvoice'}</th>
		<th class="header small" valign="middle">{l s='Invoice Date' mod='mpsellerinvoice'}</th>
		{if isset($adminInvoice)}
			<th class="header small" valign="middle">{l s='From' mod='mpsellerinvoice'}</th>
			<th class="header small" valign="middle">{l s='To' mod='mpsellerinvoice'}</th>
		{else}
			<th class="header small" valign="middle">{l s='Order Reference' mod='mpsellerinvoice'}</th>
			<th class="header small" valign="middle">{l s='Order date' mod='mpsellerinvoice'}</th>
			{if $addresses.invoice->vat_number}
				<th class="header small" valign="middle">{l s='VAT Number' mod='mpsellerinvoice'}</th>
			{/if}
		{/if}
	</tr>
	<tr>
		{if isset($adminInvoice)}
			<td class="center small white">{$seller_invoice_title|escape:'html':'UTF-8'}</td>
			<td class="center small white">{$invoiceHistory.to}</td>
			<td class="center small white">{$invoiceHistory.from}</td>
			<td class="center small white">{$invoiceHistory.to}</td>
		{else}
			<td class="center small white">{$seller_invoice_title|escape:'html':'UTF-8'}</td>
			<td class="center small white">{dateFormat date=$order->date_upd full=0}</td>
			<td class="center small white">{$order->getUniqReference()}</td>
			<td class="center small white">{dateFormat date=$order->date_add full=0}</td>
			{if $addresses.invoice->vat_number}
				<td class="center small white">
					{$addresses.invoice->vat_number}
				</td>
			{/if}
		{/if}
	</tr>
</table>
