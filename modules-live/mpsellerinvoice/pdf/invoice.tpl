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
{$style_tab}
<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
	<!-- Invoicing -->
	<tr>
		<td colspan="12">

			{$addresses_tab}

		</td>
	</tr>

	<tr>
		<td colspan="12" height="30">&nbsp;</td>
	</tr>

	<!-- TVA Info -->
	<tr>
		<td colspan="12">

			{$summary_tab}

		</td>
	</tr>

	<tr>
		<td colspan="12" height="20">&nbsp;</td>
	</tr>

	<!-- Product -->
	<tr>
		<td colspan="12">

			{$product_tab}

		</td>
	</tr>

	<tr>
		<td colspan="12" height="10">&nbsp;</td>
	</tr>

	<!-- TVA -->

	<tr>
		<!-- Code TVA -->
		<td colspan="6" class="left">
			{if isset($tax_tab) && $invoice_admin_seller == '2'}
				{$tax_tab}
			{/if}

		</td>
		<td colspan="1">&nbsp;</td>
		<!-- Calcule TVA -->
		<td colspan="5" rowspan="5" class="right">

			{if isset($total_tab) && $invoice_admin_seller == '2'}
				{$total_tab}
			{else if isset($admin_total_tab)}
				{$admin_total_tab}
			{/if}

		</td>
	</tr>


	<tr>
		<td colspan="12" height="10">&nbsp;</td>
	</tr>

	{if isset($payment_tab) && $invoice_admin_seller == '2'}
		<tr>
			<td colspan="6" class="left">

				{$payment_tab}

			</td>
			<td colspan="1">&nbsp;</td>
		</tr>
	{/if}
	<tr>
		<td colspan="12" height="10">&nbsp;</td>
	</tr>
	{if isset($seller_invoice_legal_text)}
		<tr>
			<td colspan="7" class="left small">

				<table>
					<tr>
						<td>
							<p>{$seller_invoice_legal_text|escape:'html':'UTF-8'|nl2br}</p>
						</td>
					</tr>
				</table>

			</td>
		</tr>
	{/if}
	<!-- Hook -->
	{if isset($HOOK_DISPLAY_PDF)}
		<tr>
			<td colspan="12" height="30">&nbsp;</td>
		</tr>

		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="10">
				{$HOOK_DISPLAY_PDF}
			</td>
		</tr>
	{/if}

</table>