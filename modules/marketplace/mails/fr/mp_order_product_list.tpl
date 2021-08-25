{*
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{foreach $list as $product_attribute}
	{foreach $product_attribute as $product}
	<tr>
		<td style="border:1px solid #D6D4D4;">
			<table class="table">
				<tr>
					<td width="10">&nbsp;</td>
					<td>
						<font size="2" face="Open-sans, sans-serif" color="#555454">
							{if $product['is_booking'] }<strong>Réservation :<br></strong>{/if}
							<strong>{$product['name']}</strong><br>
							{if $product['is_booking'] }
							Client : {$product['customer_name']}<br>
							Email du client : {$product['customer_email']}<br>
							Lieu de rendez-vous : {$product['bookingProductInfo']['activity_addr']} {$product['bookingProductInfo']['activity_postcode']} {$product['bookingProductInfo']['activity_city']}<br>
							Date :{$product['bookingProductInfo']['date_to']|date_format:"%e %b, %Y"}<br>
							Créneau horaire : 
							{if $product['bookingProductInfo']['booking_type'] == 1}
							  {$product['bookingProductInfo']['date_from']|date_format:"%e %b, %Y"}</br>
							  {l s='To' mod='psbooking'}</br>
							  {$product['bookingProductInfo']['date_to']|date_format:"%e %b, %Y"}
							{else}
							  {$product['bookingProductInfo']['date_from']|date_format:"%e %b, %Y"}</br>
							  {$product['bookingProductInfo']['time_from']} - {$product['bookingProductInfo']['time_to']}
							{/if}
						
							
							{/if}
						  </font>
					</td>
					<td width="10">&nbsp;</td>
				</tr>
			</table>
		</td>
		<td style="border:1px solid #D6D4D4;">
			<table class="table">
				<tr>
					<td width="10">&nbsp;</td>
					<td align="right">
						<font size="2" face="Open-sans, sans-serif" color="#555454">
							{$product['unit_price_tax_excl']}
						</font>
					</td>
					<td width="10">&nbsp;</td>
				</tr>
			</table>
		</td>
		<td style="border:1px solid #D6D4D4;">
			<table class="table">
				<tr>
					<td width="10">&nbsp;</td>
					<td align="right">
						<font size="2" face="Open-sans, sans-serif" color="#555454">
							{$product['unit_price_tax_incl']}
						</font>
					</td>
					<td width="10">&nbsp;</td>
				</tr>
			</table>
		</td>
		<td style="border:1px solid #D6D4D4;">
			<table class="table">
				<tr>
					<td width="10">&nbsp;</td>
					<td align="right">
						<font size="2" face="Open-sans, sans-serif" color="#555454">
							{$product['product_quantity']}
						</font>
					</td>
					<td width="10">&nbsp;</td>
				</tr>
			</table>
		</td>
		<td style="border:1px solid #D6D4D4;">
			<table class="table">
				<tr>
					<td width="10">&nbsp;</td>
					<td align="right">
						<font size="2" face="Open-sans, sans-serif" color="#555454">
							{$product['total_price_tax_incl']}
						</font>
					</td>
					<td width="10">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	{/foreach}
{/foreach}