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
            <th class="product header small" width="25%">
                {l s='Reference' mod='mpsellerinvoice'}
            </th>
            <th class="product header small" width="25%">
                {l s='Order Date' mod='mpsellerinvoice'}
            </th>
			<th class="product header small" width="25%">
                {l s='Description' mod='mpsellerinvoice'}
            </th>
			<th class="product header-right small" width="25%">
				{l s='Total' mod='mpsellerinvoice'} <br />
			</th>
		</tr>
	</thead>
	<tbody>

	{foreach $adminCommissionData as $data}
		{cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
		<tr class="product {$bgcolor_class}">
			<td class="product center">{$data.order_reference}</td>
			<td class="product center">{$data.created_date}</td>
			<td class="product center">{l s='Commission Fee' mod='mpsellerinvoice'}</td>
			<td class="product right">{displayPrice currency=$data.currency->id price=$data.commission_fee}</td>
		</tr>
	{/foreach}
	<tr class="customization_data {$bgcolor_class}">
		<td></td>
		<td></td>
		<td style="text-align:right;" class="center">
			{l s='Total :' mod='mpsellerinvoice'}
		</td>
		<td style="text-align:right;" class="center">
			{displayPrice currency=$data.currency->id price=$data.totalAdminCommission}
		</td>
		{* <!--<td style="text-align:right;" class="center">
			{displayPrice currency=$data.currency->id price=$data.totalAdminCommission}
		</td>-->*}
		<td></td>
		<td style="text-align:right;" class="center">
			{$data.totalAdminCommissionTax}
		</td>
		{* <!--<td style="text-align:right;" class="center">
			{displayPrice currency=$data.currency->id price=$data.totalAdminCommissionTax}
		</td>-->*}
		<td></td>
		<td style="text-align:right;" class="center">
			{$data.totalCommission}
		</td>
		{* <!--<td style="text-align:right;" class="center">
			{displayPrice currency=$data.currency->id price=$data.totalCommission}
		</td>-->*}
	</tr>
	</tbody>
</table>
