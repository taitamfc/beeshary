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

<div class="panel">
	<div class="panel-heading">
		<i class="icon-list"></i>
		{l s='Seller Product List' mod='marketplace'}
		<span class="badge">{$mp_seller_order_details|@count}</span>
	</div>
	<div class="table-responsive">
		<table class="table wk-table">
			<thead>
				<tr>
					<th><span class="title_box">{l s='Unique Shop Name' mod='marketplace'}</span></th>
					<th>
						<span class="title_box">{l s='Current Order Status' mod='marketplace'}</span>
					</th>
					<th>{l s='Seller Detail' mod='marketplace'}</th>
					<th>{l s='Product Detail' mod='marketplace'}</th>
					{hook h='displayAdminPsSellerOrderViewHead'}
				</tr>
			</thead>
			<tbody>
				{foreach $mp_seller_order_details as $mp_order_detail}
					<tr>
						<td>{$mp_order_detail.0.seller_shop}</td>
						<td>
							<span style="background:{if isset($mp_order_detail.0.ostate_name)}{$mp_order_detail.0.color}{else}{$currentState->color}{/if};color:white !important; border-radius: 5px; padding: 5px; ">
							{if isset($mp_order_detail.0.ostate_name)}
								{$mp_order_detail.0.ostate_name}
							{else}
								{$currentState->name}
							{/if}
							</span>
						</td>
						<td><a class="btn btn-default" target="_blank" href="{$link->getAdminLink('AdminSellerInfoDetail')}&id_seller={$mp_order_detail.0.id_seller}&viewwk_mp_seller"><i class="icon-search-plus"></i> {l s='View Seller' mod='marketplace'}</a></td>
						<td>
							<a data-id="{$mp_order_detail.0.id_seller}" class="btn btn-default wk-seller-prod" href="javascript:void(0);">
								<i class="icon-search-plus"></i> {l s='View Detail' mod='marketplace'}
							</a>
						</td>
						{hook h='displayAdminPsSellerOrderViewBody' idSellerCustomer=$mp_order_detail.0.seller_customer_id}
					</tr>
					<tr class="wk-product-detail-{$mp_order_detail.0.id_seller}" style="display: none;">
						<td colspan="12">
							<div class="panel">
							<table class="table">
								<thead>
									<tr>
										<th>{l s='Product Name' mod='marketplace'}</th>
										<th>{l s='Quantity' mod='marketplace'}</th>
										<th>{l s='Price(ti)' mod='marketplace'}</th>
										<th>{l s='Price(te)' mod='marketplace'}</th>
									</tr>
								</thead>
								<tbody>
									<div class="panel-heading">
										{l s='Product Details' mod='marketplace'}
									</div>
									{foreach $mp_order_detail as $order_detail}
									<tr>
										<td>
											<a href="{if isset($order_detail.id_mp_product)}{$link->getAdminLink('AdminSellerProductDetail')}&id_mp_product={$order_detail.id_mp_product}&updatewk_mp_seller_product{/if}" target="_blank">
												<span class="productName">{$order_detail.product_name}</span>
											</a>
										</td>
										<td>
											<span class="productName">{$order_detail.quantity}</span>
										</td>
										<td>
											<span class="productName">{displayPrice price=$order_detail.price_ti currency=$order_detail.id_currency}</span>
										</td>
										<td>
											<span class="productName">{displayPrice price=$order_detail.price_te currency=$order_detail.id_currency}</span>
										</td>
									</tr>
									{/foreach}
								</tbody>
							</table>
							</div>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$(document).on('click', '.wk-seller-prod', function(){
			var idSeller = $(this).attr('data-id');
			$('.wk-product-detail-'+idSeller).toggle();
		});
	});
</script>