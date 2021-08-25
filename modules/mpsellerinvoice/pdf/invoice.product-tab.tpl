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
<table class="product" width="100%" cellpadding="4" cellspacing="0">
	<thead>
		<tr>
			{if !isset($adminInvoice)}
				<th class="product header small" width="{$layout.reference.width}%">
					{l s='ID' mod='mpsellerinvoice'}
				</th>
			{/if}
			<th style="text-align:left;" class="product header small" width="{$layout.product.width}%">
				{if !$is_booking}
				{l s='Produit' mod='mpsellerinvoice'}
				{else}
				{l s='Activité' mod='mpsellerinvoice'}
				{/if}
			</th>

			{if isset($adminInvoice)}
				<th class="product header small" width="{$layout.tax_code.width}%">
					{l s='Comm. Rate' mod='mpsellerinvoice'}
				</th>
			{else}
				<th class="product header small" width="{$layout.tax_code.width}%">
					{l s='Taux de TVA' mod='mpsellerinvoice'}
				</th>
			{/if}

			{if isset($layout.before_discount)}
			{* {if $invoice_admin_seller == '1'}{$layout.quantity.width}{else}{$layout.unit_price_tax_excl.width}{/if} *}
				<th class="product header small" width="{if $invoice_admin_seller == '1'}{$layout.quantity.width}{else}{$layout.unit_price_tax_excl.width}{/if}%">
					{l s='Total' mod='mpsellerinvoice'} <br /> {l s='(HT)' mod='mpsellerinvoice'}
				</th>
			{/if}

			<th class="product header small" width="{$layout.unit_price_tax_excl.width}%">
				{l s='Prix unitaire' mod='mpsellerinvoice'} <br /> {l s='(HT)' mod='mpsellerinvoice'}
			</th>

			{if isset($adminInvoice)}
				<th class="product header small" width="{$layout.unit_price_tax_excl.width}%">
					{l s='Comm.' mod='mpsellerinvoice'} <br /> {l s='(HT)' mod='mpsellerinvoice'}
				</th>
			{/if}

			<th class="product header small" width="{$layout.unit_price_tax_incl.width}%">
				{l s='Prix unitaire' mod='mpsellerinvoice'} <br /> {l s='(TTC)' mod='mpsellerinvoice'}
			</th>

			{if isset($adminInvoice)}
				<th class="product header small" width="{$layout.unit_price_tax_excl.width}%">
					{l s='TVA' mod='mpsellerinvoice'}
				</th>
			{/if}
			<th class="product header small" width="{$layout.quantity.width}%">
				{l s='Qté' mod='mpsellerinvoice'}
			</th>
			{if isset($adminInvoice)}
				<th class="product header small" width="{$layout.total_tax_excl.width}%">
					{l s='Total Comm.' mod='mpsellerinvoice'} <br /> {l s='(TTC)' mod='mpsellerinvoice'}
				</th>
			{else}
				<th class="product header small" width="{$layout.total_tax_excl.width}%">
					{l s='Total' mod='mpsellerinvoice'} <br /> {l s='(HT)' mod='mpsellerinvoice'}
				</th>
			{/if}
		</tr>
	</thead>
	<tbody>

	<!-- PRODUCTS -->
	{foreach $order_details as $order_detail}
		{cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
		<tr class="product {$bgcolor_class}">
			{if !isset($adminInvoice)}
				<td class="product center">
					{if !empty($order_detail.product_id)}
						{$order_detail.product_id}
					{else}--{/if}
				</td>
			{/if}
			<td class="product">
				{if $display_product_images}
					<table width="100%">
						<tr>
							<td width="15%">
								{if isset($order_detail.image) && $order_detail.image->id}
									{$order_detail.image_tag}
								{/if}
							</td>
							<td width="5%">&nbsp;</td>
							<td width="80%">
								{$order_detail.product_name}
							</td>
						</tr>
					</table>
				{else}
					{$order_detail.product_name}
				{/if}
				{if $order_detail['is_booking'] }
					<br>
					<!--p>
					Nom et Prénom du client : {$order_detail['customer_name']}<br>
					Email du client : {$order_detail['customer_email']} 
					</p!-->
					Boutique : {$order_detail['mp_seller_info']['shop_name_unique']} <br>
					Nom et Prénom de l'artisan : {$order_detail['mp_seller_info']['seller_firstname']} {$order_detail['mp_seller_info']['seller_lastname']}<br>
					Email de l'artisan : {$order_detail['mp_seller_info']['business_email']}<br>
					Nom de l'activité réservée : {$order_detail['name']}<br>
					Lieu de rendez-vous : 
					{$order_detail['bookingProductInfo']['activity_addr']} 
					{$order_detail['bookingProductInfo']['activity_postcode']} 
					{$order_detail['bookingProductInfo']['activity_city']} 
					<br>
					Date : {$order_detail['bookingProductInfo']['date_from']}<br>
					
					Créneau horaire : 
					{if $order_detail['bookingProductInfo']['booking_type'] == 1}
					  {$order_detail['bookingProductInfo']['date_from']}</br>
					  {l s='To' mod='psbooking'}</br>
					  {$order_detail['bookingProductInfo']['date_to']}
					{else}
					  {$order_detail['bookingProductInfo']['time_from']} - {$order_detail['bookingProductInfo']['time_to']}
					{/if}
					
					<br>
					Durée de l'activité: {$order_detail['bookingProductInfo']['activity_period']}<br>
					Nombre d'unité : {$order_detail['bookingProductInfo']['quantity']}
				{/if}
			</td>

			{if isset($adminInvoice)}
				<td class="product center">
					{if isset($order_detail.commission_rate)}
						{$order_detail.commission_rate}%
					{elseif isset($sellerCommissionRate)}
						{$sellerCommissionRate}%
					{else}
						--
					{/if}
				</td>
			{else}
				<td class="product center">
					{$order_detail.order_detail_tax_label}
				</td>

			{/if}
			{if isset($layout.before_discount)}
				<td class="product center">
					{if isset($order_detail.unit_price_tax_excl_before_specific_price)}
						{$order_detail.unit_price_tax_excl_before_specific_price}
					{else}
						--
					{/if}
				</td>
			{/if}
			<td class="product right">
				{$order_detail.unit_price_tax_excl}
				{if $order_detail.ecotax_tax_excl > 0}
					<br>
					<small>{{$order_detail.ecotax_tax_excl}|string_format:{l s='ecotax: %s' mod='mpsellerinvoice'}}</small>
				{/if}
			</td>
			{if isset($adminInvoice)}
				<td class="product right">
					{$order_detail.admin_commission}
				</td>
			{/if}

			<td class="product right">
				{$order_detail.unit_price_tax_incl}
				{if $order_detail.ecotax_tax_incl > 0}
					<br>
					<small>{{$order_detail.ecotax_tax_incl}|string_format:{l s='ecotax: %s' mod='mpsellerinvoice'}}</small>
				{/if}
			</td>

			{if isset($adminInvoice)}
				<td class="product right">
					{$order_detail.admin_commission_tax}
				</td>
			{/if}

			<td class="product center">
				{$order_detail.product_quantity}
			</td>
			{if isset($adminInvoice)}
				<td  class="product right">
					{$order_detail.order_total_commission}
				</td>
			{else}
				<td  class="product right">
					{$order_detail.total_price_tax_excl}
				</td>
			{/if}
		</tr>
		{foreach $order_detail.customizedDatas as $customizationPerAddress}
			{foreach $customizationPerAddress as $customizationId => $customization}
				<tr class="customization_data {$bgcolor_class}">
					<td class="center"> &nbsp;</td>

					<td>
						{if isset($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) && count($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) > 0}
							<table style="width: 100%;">
								{foreach $customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_] as $customization_infos}
									<tr>
										<td style="width: 30%;">
											{$customization_infos.name|string_format:{l s='%s:' mod='mpsellerinvoice'}}
										</td>
										<td>{$customization_infos.value}</td>
									</tr>
								{/foreach}
							</table>
						{/if}

						{if isset($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) && count($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) > 0}
							<table style="width: 100%;">
								<tr>
									<td style="width: 70%;">{l s='image(s):' mod='mpsellerinvoice'}</td>
									<td>{count($customization.datas[$smarty.const._CUSTOMIZE_FILE_])}</td>
								</tr>
							</table>
						{/if}
					</td>

					<td class="center">
						({if $customization.quantity == 0}1{else}{$customization.quantity}{/if})
					</td>

					{assign var=end value=($layout._colCount-3)}
					{for $var=0 to $end}
						<td class="center">
							--
						</td>
					{/for}

				</tr>
				<!--if !$smarty.foreach.custo_foreach.last-->
			{/foreach}
		{/foreach}
	{/foreach}
		{if isset($adminInvoice)}
		<tr class="customization_data {$bgcolor_class}">
			<td></td>
			<td></td>
			{if isset($layout.before_discount)}
				<td></td>
			{/if}
			<td style="text-align:right;" class="center">{l s='Total :' mod='mpsellerinvoice'}</td>
			<td style="text-align:right;" class="center">{$totalAdminCommission}</td>
			<td></td>
			<td style="text-align:right;" class="center">{$totalAdminCommissionTax}</td>
			<td></td>
			<td style="text-align:right;" class="center">{$totalCommission}</td>
		</tr>
		{/if}
	<!-- END PRODUCTS -->

	<!-- CART RULES -->

	</tbody>

</table>
