<section class="box" id="order-history">
	<h3>Checkout your product status step-by-step</h3>
	{if isset($sellerArray)}
		{foreach $sellerArray as $sellerInfo}
			<div class="product_1" style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">
				<h4>{l s='Shop' mod='marketplace'} : {$sellerInfo.seller->shop_name}</h4>
				
				<table class="table table-bordered">
					<thead class="thead-default">
						<tr>
							<th>{l s='Products' mod='marketplace'}</th>
						</tr>
					</thead>
					<tbody>
						{foreach $sellerInfo.product as $product}
						<tr>
							<td>{$product}</td>
						</tr>
						{/foreach}
					</tbody>
				</table>

				<table class="table table-bordered table-labeled hidden-xs-down">
					<thead class="thead-default">
						<tr>
							<th>{l s='Date' mod='marketplace'}</th>
							<th>{l s='Status' mod='marketplace'}</th>
						</tr>
					</thead>
					<tbody>
						{foreach $sellerInfo.history as $history}
						<tr>
							<td>{$history.date_add}</td>
							<td><span class="label label-pill bright" style="background: {$history.color}; color: #FFF;">{$history.ostate_name}</span></td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		{/foreach}
	{/if}
</section>