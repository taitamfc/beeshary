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
		<th class="header small" valign="middle">{l s='Numéro de facture' mod='mpsellerinvoice'}</th>
		<th class="header small" valign="middle">{l s='Date de la Facture' mod='mpsellerinvoice'}</th>
		<th class="header small" valign="middle">{l s='Référence de la commande' mod='mpsellerinvoice'}</th>
		<th class="header small" valign="middle">{l s='Date de la commande' mod='mpsellerinvoice'}</th>
		{if $addresses.invoice->vat_number}
			<th class="header small" valign="middle">{l s='Numéro de TVA' mod='mpsellerinvoice'}</th>
		{/if}
	</tr>
	<tr>
		<td class="center small white">{$seller_invoice_title|escape:'html':'UTF-8'}</td>
		<td class="center small white">{dateFormat date=$order->date_upd full=0}</td>
		<td class="center small white">{$order->getUniqReference()}</td>
		<td class="center small white">{dateFormat date=$order->date_add full=0}</td>
		{if $addresses.invoice->vat_number}
			<td class="center small white">
				{$addresses.invoice->vat_number}
			</td>
		{/if}
	</tr>
</table>
