{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}
<tr>
	<th bgcolor="#f8f8f8" style="border:1px solid #D6D4D4;background-color: #fbfbfb;color: #333;font-family: Arial;font-size: 13px;padding: 10px;">Product</th>
	<th bgcolor="#f8f8f8" style="border:1px solid #D6D4D4;background-color: #fbfbfb;color: #333;font-family: Arial;font-size: 13px;padding: 10px;" width="17%">Unit price(Tax excl.)</th>
	<th bgcolor="#f8f8f8" style="border:1px solid #D6D4D4;background-color: #fbfbfb;color: #333;font-family: Arial;font-size: 13px;padding: 10px;" width="17%">Unit price(Tax incl.)</th>
	<th bgcolor="#f8f8f8" style="border:1px solid #D6D4D4;background-color: #fbfbfb;color: #333;font-family: Arial;font-size: 13px;padding: 10px;">Quantity</th>
	{if $enableDatePicker}
	<th bgcolor="#f8f8f8" style="border:1px solid #D6D4D4;background-color: #fbfbfb;color: #333;font-family: Arial;font-size: 13px;padding: 10px;">Pick Up Details</th>
	{/if}
	<th bgcolor="#f8f8f8" style="border:1px solid #D6D4D4;background-color: #fbfbfb;color: #333;font-family: Arial;font-size: 13px;padding: 10px;">Total Price</th>
</tr>
<tr>
	<td colspan="{if $enableDatePicker}6{else}5{/if}" style="border:1px solid #D6D4D4;text-align:center;color:#777;padding:7px 0">
		&nbsp;&nbsp;
		{foreach $list as $products}
		{foreach $products.products as $key => $product}
		<tr>
			<td style="border:1px solid #D6D4D4;">
				<table class="table">
					<tr>
						<td width="10">&nbsp;</td>
						<td>
							<font size="2" face="Open-sans, sans-serif" color="#555454">
								<strong>{$orderedProducts.$product.product_name}</strong>
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
								{$orderedProducts.$product.unit_price_tax_excl}
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
								{$orderedProducts.$product.unit_price_tax_incl}
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
								{$orderedProducts.$product.product_quantity}
							</font>
						</td>
						<td width="10">&nbsp;</td>
					</tr>
				</table>
			</td>
			{if $key == 0 && $enableDatePicker}
				<td rowspan="{$products.count}" style="border:1px solid #D6D4D4;">
					<table class="table">
						<tr>
							<td width="10">&nbsp;</td>
							<td align="left">
								<div>
								<font size="2" face="Open-sans, sans-serif" color="#555454">
										<strong>Pick up date</strong>
										{$products.pickup_date}
								</font>
								</div>
								{if $enableTimePicker}
								<div>
								<font size="2" face="Open-sans, sans-serif" color="#555454">
									<strong>Pick up time</strong>
									{$products.pickup_time}
								</font>
								</div>
								{/if}
							</td>
							<td width="10">&nbsp;</td>
						</tr>
					</table>
				</td>
			{/if}
			<td style="border:1px solid #D6D4D4;">
				<table class="table">
					<tr>
						<td width="10">&nbsp;</td>
						<td align="right">
							<font size="2" face="Open-sans, sans-serif" color="#555454">
								{$orderedProducts.$product.totalPrice}
							</font>
						</td>
						<td width="10">&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
		{/foreach}
		{/foreach}
	</td>
</tr>
