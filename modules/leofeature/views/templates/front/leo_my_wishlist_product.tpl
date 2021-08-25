{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
{if $products && count($products) >0}
	{foreach from=$products item=product_item name=for_products}
		{assign var='product' value=$product_item.product_info}
		{assign var='wishlist' value=$product_item.wishlist_info}
		<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 product-miniature js-product-miniature leo-wishlistproduct-item leo-wishlistproduct-item-{$wishlist.id_wishlist_product} product-{$product.id_product}" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
			<div class="delete-wishlist-product clearfix">
				<a class="leo-wishlist-button-delete btn" href="javascript:void(0)" title="{l s='Remove from this wishlist' mod='leofeature'}" data-id-wishlist="{$wishlist.id_wishlist}" data-id-wishlist-product="{$wishlist.id_wishlist_product}" data-id-product="{$product.id_product}"><i class="material-icons">&#xE872;</i>
				</a>
			</div>
			<div class="thumbnail-container clearfix">

				{block name='product_thumbnail'}
				  <a href="{$product.url}" class="thumbnail product-thumbnail">
					<img class="img-fluid"
					  src = "{$product.cover.bySize.home_default.url}"
					  alt = "{$product.cover.legend}"
					  data-full-size-image-url = "{$product.cover.large.url}"
					>
				  </a>
				{/block}

				<div class="product-description">							
				  {block name='product_name'}
					<h1 class="h3 product-title" itemprop="name"><a href="{$product.url}">{$product.name|truncate:30:'...'}</a></h1>
				  {/block}
				  
				</div>			
			</div>
			<div class="wishlist-product-info">
				<div class="form-group">
					<label>{l s='Quantity' mod='leofeature'}</label>
					<input class="form-control wishlist-product-quantity wishlist-product-quantity-{$wishlist.id_wishlist_product}" type="number" min=1 value="{$wishlist.quantity}">					
				</div>
				<div class="form-group">
					<label>{l s='Priority' mod='leofeature'}</label>
					<select class="form-control wishlist-product-priority wishlist-product-priority-{$wishlist.id_wishlist_product}">					  
						{for $i=0 to 2}
							<option value="{$i}"{if $i == $wishlist.priority} selected="selected"{/if}>								
								{if $i == 0}{l s='High' mod='leofeature'}{/if}
								{if $i == 1}{l s='Medium' mod='leofeature'}{/if}
								{if $i == 2}{l s='Low' mod='leofeature'}{/if}								
							</option>
						{/for}
					</select>
				  </div>
			</div>		
			<div class="wishlist-product-action">
				<a class="leo-wishlist-product-save-button btn btn-primary" href="javascript:void(0)" title="{l s='Save' mod='leofeature'}" data-id-wishlist="{$wishlist.id_wishlist}" data-id-wishlist-product="{$wishlist.id_wishlist_product}" data-id-product="{$product.id_product}">{l s='Save' mod='leofeature'}
				</a>
				{if isset($wishlists) && count($wishlists) > 0}					
					<div class="dropdown leo-wishlist-button-dropdown">					 
					  <button class="leo-wishlist-button dropdown-toggle btn btn-primary show-list" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{l s='Move' mod='leofeature'}</button>
					  <div class="dropdown-menu leo-list-wishlist leo-list-wishlist-{$product.id_product}">				
						{foreach from=$wishlists item=wishlists_item}							
							<a href="#" class="dropdown-item list-group-item list-group-item-action move-wishlist-item" data-id-wishlist="{$wishlists_item.id_wishlist}" data-id-wishlist-product="{$wishlist.id_wishlist_product}" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" title="{$wishlists_item.name}">{$wishlists_item.name}</a>			
						{/foreach}
					  </div>
					</div>
				{/if}
			</div>
		</div>
	{/foreach}
{else}
	<div class="col-xl-12"><p class="alert alert-warning">{l s='No products' mod='leofeature'}</p></div>
{/if}

