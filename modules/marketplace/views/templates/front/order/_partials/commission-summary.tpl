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

<div class="box-account box-recent">
	<div class="box-head">
		<div class="">
			<h4><i class="icon-exchange"></i> {l s='Commission Summary' mod='marketplace'}</h4>
		</div>
	</div>
	<div class="table-responsive clearfix box-content wk-commission-table">
		<table class="table">
			<thead>
				<th>
                    <span class="title_box">{l s='Product' mod='marketplace'}</span>
                </th>
				<th>
                    <span class="title_box">{l s='Final Price' mod='marketplace'}</span>
					{if isset($mp_voucher_info) && $mp_voucher_info}
						<small class="text-muted">{l s='After voucher applied (Tax excl.)' mod='marketplace'}</small>
					{else}
						<small class="text-muted">{l s='(Tax excl.)' mod='marketplace'}</small>
					{/if}
                </th>
				<th>
                    <span class="title_box">{l s='Total Tax' mod='marketplace'}</span>
                </th>
                <th>
                    <span class="title_box">{l s='Final Price' mod='marketplace'}</span>
					{if isset($mp_voucher_info) && $mp_voucher_info}
						<small class="text-muted">{l s='After voucher applied (Tax incl.)' mod='marketplace'}</small>
					{else}
						<small class="text-muted">{l s='(Tax incl.)' mod='marketplace'}</small>
					{/if}
                </th>
                <th>
                    <span class="title_box">{l s='Comm. Rate' mod='marketplace'}</span>
                </th>
                <th>
                    <span class="title_box">{l s='Admin Tax' mod='marketplace'}</span>
                </th>
                <th>
                    <span class="title_box">{l s='Seller Tax' mod='marketplace'}</span>
                </th>
				<th>
                    <span class="title_box">{l s='Admin Comm.' mod='marketplace'}</span>
                    <small class="text-muted">{l s='(Tax incl.)' mod='marketplace'}</small>
                </th>
				<th>
                    <span class="title_box">{l s='Seller Earn' mod='marketplace'}</span>
                    <small class="text-muted">{l s='(Tax incl.)' mod='marketplace'}</small>
                </th>

			</thead>
			<tbody>
				{foreach $order_products as $product}
					<tr>
						<td>
                            {if isset($product.active) && $product.active == 1}
                                <a href="{$link->getProductLink($product.product_id)|addslashes}" target="_blank" title="{l s='View Products' mod='marketplace'}">{$product.product_name|escape:'html':'UTF-8'}</a>
                            {else}{$product.product_name|escape:'html':'UTF-8'}
                            {/if}
                        </td>
						<td>{$product.price_te|escape:'html':'UTF-8'}</td>
						<td>{$product.total_tax|escape:'html':'UTF-8'}</td>
                        <td>{$product.price_ti|escape:'html':'UTF-8'}</td>
                        <td class="text-center">{$product.commission_rate|escape:'html':'UTF-8'}%</td>
                        <td>{$product.admin_tax|escape:'html':'UTF-8'}</td>
                        <td>{$product.seller_tax|escape:'html':'UTF-8'}</td>
						<td>{$product.admin_total_commission|escape:'html':'UTF-8'}</td>
						<td>{$product.seller_total_amount|escape:'html':'UTF-8'}</td>
					</tr>
                    {hook h="displayMpOrderDetailListRow" params=$product}
				{/foreach}
                    <tr>
						<td></td>
						<td></td>
						<td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-center"><strong>{l s='Total' mod='marketplace'}</strong></td>
						<td><strong>{$admin_commission_total|escape:'html':'UTF-8'}</strong></td>
						<td><strong>{$seller_total|escape:'html':'UTF-8'}</strong></td>
					</tr>
			</tbody>
		</table>
	</div>
</div>