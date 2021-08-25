{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
{extends file=$layout}

{block name='content'}
	<section id="main">
		{capture name=path}{l s='Products Comparison' mod='leofeature'}{/capture}
		<h1 class="page-heading">{l s='Products Comparison' mod='leofeature'}</h1>
		{if $hasProduct}
			<div class="products_block">
				<table id="product_comparison" class="table table-bordered table-responsive">
					<tr>
						<td class="td_empty compare_extra_information">
							
							<span>{l s='Features:' mod='leofeature'}</span>
						</td>
						
						{foreach from=$products item=product name=for_products}
							{assign var='replace_id' value=$product.id|cat:'|'}
							<td class="product-miniature js-product-miniature leo-productscompare-item product-{$product.id_product}" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
								<div class="delete-productcompare clearfix">
									<a class="leo-compare-button btn delete" href="#" title="{l s='Remove from Compare' mod='leofeature'}" data-id-product="{$product.id_product}"><i class="material-icons">&#xE872;</i>
									</a>
								</div>
							  <div class="thumbnail-container clearfix">
								<div class="product-image">
									{block name='product_thumbnail'}
									  <a href="{$product.url}" class="thumbnail product-thumbnail">
										<img class="img-fluid"
										  src = "{$product.cover.bySize.home_default.url}"
										  alt = "{$product.cover.legend}"
										  data-full-size-image-url = "{$product.cover.large.url}"
										>
									  </a>
									{/block}
									
									{block name='product_flags'}
									  <ul class="product-flags">
										{foreach from=$product.flags item=flag}
										  <li class="product-flag {$flag.type}">{$flag.label}</li>
										{/foreach}
									  </ul>
									{/block}
								</div>
								<div class="product-description">
									{hook h='displayLeoCartAttribute' product=$product}
									{hook h='displayLeoCartQuantity' product=$product}
									{hook h='displayLeoCartButton' product=$product}								
									{block name='product_name'}
										<h1 class="h3 product-title" itemprop="name"><a href="{$product.url}">{$product.name|truncate:30:'...'}</a></h1>
									{/block}
									<div class="product_desc">
										{$product.description_short|strip_tags|truncate:60:'...'}
									</div>
									{block name='product_price_and_shipping'}
										{if $product.show_price}
										  <div class="product-price-and-shipping">
											{if $product.has_discount}
											  {hook h='displayProductPriceBlock' product=$product type="old_price"}

											  <span class="regular-price">{$product.regular_price}</span>
											  {if $product.discount_type === 'percentage'}
												<span class="discount-percentage">{$product.discount_percentage}</span>
											  {/if}
											{/if}

											{hook h='displayProductPriceBlock' product=$product type="before_price"}

											<span itemprop="price" class="price">{$product.price}</span>
											
											{hook h='displayProductPriceBlock' product=$product type='unit_price'}

											{hook h='displayProductPriceBlock' product=$product type='weight'}
										  </div>
										  
										{/if}
									{/block}
								</div>
							  </div>
							</td>
						{/foreach}
					</tr>
					{if $ordered_features}
						{foreach from=$ordered_features item=feature}
							<tr>
								{cycle values='comparison_feature_odd,comparison_feature_even' assign='classname'}
								<td class="{$classname} feature-name" >
									<strong>{$feature.name|escape:'html':'UTF-8'}</strong>
								</td>
								{foreach from=$products item=product name=for_products}
									{assign var='product_id' value=$product.id}
									{assign var='feature_id' value=$feature.id_feature}
									{if isset($product_features[$product_id])}
										{assign var='tab' value=$product_features[$product_id]}
										<td class="{$classname} comparison_infos product-{$product.id}">{if (isset($tab[$feature_id]))}{$tab[$feature_id]|escape:'html':'UTF-8'}{/if}</td>
									{else}
										<td class="{$classname} comparison_infos product-{$product.id}"></td>
									{/if}
								{/foreach}
							</tr>
						{/foreach}
					{else}
						<tr>
							<td></td>
							<td colspan="{$products|@count}" class="text-center">{l s='No features to compare' mod='leofeature'}</td>
						</tr>
					{/if}
					
					{hook h='displayLeoProducReviewCompare' list_product=$list_product}
				</table>
			</div> <!-- end products_block -->
		{else}
			<p class="alert alert-warning">{l s='There are no products selected for comparison.' mod='leofeature'}</p>
		{/if}
		<ul class="footer_link">
			<li>
				<a class="button lnk_view btn btn-outline btn-sm" href="{$urls.base_url}">
					<i class="material-icons">&#xE317;</i>
					<span>{l s='Continue Shopping' mod='leofeature'}</span>
				</a>
			</li>
		</ul>
	</section>
{/block}

