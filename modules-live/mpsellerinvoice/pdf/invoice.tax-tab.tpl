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
<!--  TAX DETAILS -->
{if $tax_exempt}

	{l s='Exempt of VAT according to section 259B of the General Tax Code.' mod='mpsellerinvoice'}

{elseif (isset($tax_breakdowns))}
	<table id="tax-tab" width="100%">
		<thead>
			<tr>
				<th class="header small">{l s='Calcul de la TVA' mod='mpsellerinvoice'}</th>
				<th class="header small">{l s='Taux de TVA' mod='mpsellerinvoice'}</th>
				{if $display_tax_bases_in_breakdowns}
					<th class="header small">{l s='Total (HT)' mod='mpsellerinvoice'}</th>
				{/if}
				<th class="header-right small">{l s='Total TVA' mod='mpsellerinvoice'}</th>
			</tr>
		</thead>
		<tbody>
		{assign var=has_line value=false}

		{if isset($tax_breakdowns) && $tax_breakdowns}
			{foreach $tax_breakdowns as $label => $bd}
				{assign var=label_printed value=false}

				{foreach $bd as $line}
					{if $line.rate == 0}
						{continue}
					{/if}
					{assign var=has_line value=true}
					<tr>
						<td class="white">
							{if !$label_printed}
								{if $label == 'product_tax'}
									{l s='Produits' mod='mpsellerinvoice'}
								{elseif $label == 'shipping_tax'}
									{l s='Livraison' mod='mpsellerinvoice'}
								{elseif $label == 'ecotax_tax'}
									{l s='Ecotax' mod='mpsellerinvoice'}
								{elseif $label == 'wrapping_tax'}
									{l s='Emballage' mod='mpsellerinvoice'}
								{/if}
								{assign var=label_printed value=true}
							{/if}
						</td>

						<td class="center white">
							{$line.rate} %
						</td>

						{if $display_tax_bases_in_breakdowns}
							<td class="right white">
								{if isset($is_order_slip) && $is_order_slip}- {/if}
								{if isset($line.total_tax_excl)}{$line.total_tax_excl}{/if}
							</td>
						{/if}

						<td class="right white">
							{if isset($is_order_slip) && $is_order_slip}- {/if}
							{$line.total_amount}
						</td>
					</tr>
				{/foreach}
			{/foreach}
		{/if}
		{if !$has_line}
		<tr>
			<td class="white center" colspan="{if $display_tax_bases_in_breakdowns}4{else}3{/if}">
				{l s='Aucune taxe' mod='mpsellerinvoice'}
			</td>
		</tr>
		{/if}

		</tbody>
	</table>

{/if}
<!--  / TAX DETAILS -->
