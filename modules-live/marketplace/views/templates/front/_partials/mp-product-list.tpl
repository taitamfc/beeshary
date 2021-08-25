{*
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}


{block name='content'}
<div class="container">
<div class="main_block col-sm-12">
	
	
		
			{if isset($mp_shop_product)}
				{block name='shopcollection_pagination_sort'}
					{include file='module:marketplace/views/templates/front/shop/_partials/shopcollection-top.tpl'}
				{/block}

				{foreach $mp_shop_product as $key => $product}
					{if $product.active}
						<a href="{$link->getProductLink($product.product)|addslashes}" class="product_img_link" title="{$product.product_name}">
							<div class="wk_collection_data" {if ($key+1)%3 == 0}style="margin-right:0px;"{/if}>
								<div class="wk_img_block">
									{if isset($product.image)}
										<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.image, 'home_default')}" alt="{$product.name}">
									{else}
										<img class="img-responsive" src="{$smarty.const._MODULE_DIR_}/marketplace/views/img/home-default.jpg" alt="{$product.product_name}">
									{/if}
								</div>
								
								{* TODO:: Need to create model box with ajax response *}
								{*<a class="quick-view" href="{$product.link}" rel="{$product.link}">
									<span>{l s='Quick view' mod='marketplace'}</span>
								</a>*}

								<div class="wk_collecion_details">
									<div class="mp-product-name">
										{$product.product_name|truncate:45:'...'}
									</div>
									{if $product.show_price}
										<div class="mp-product-price">{$product.price}</div>
									{/if}
									<div>
										{* if catalog mode is disabled by config (by default) *}
										{if !$PS_CATALOG_MODE}
											{if $product.qty_available > 0 && $product.available_for_order}
												{block name='shopcollection_add_to_cart'}
													{include file='module:marketplace/views/templates/front/_partials/product-add-to-cart.tpl' product=$product}
												{/block}
											{else}
												<span class="button ajax_add_to_cart_button btn btn-default disabled">
													<span>{l s='Add to cart' mod='marketplace'}</span>
												</span>
											{/if}
										{/if}
									</div>
								</div>
							</div>
						</a>
					{else}
						<div class="alert alert-info">{l s='No item found' mod='marketplace'}</div>
					{/if}
				{/foreach}

				{block name='shopcollection_pagination_sort'}
					{include file='module:marketplace/views/templates/front/shop/_partials/shopcollection-pagination.tpl'}
				{/block}
			{else}
				<div class="alert alert-info">{l s='No item found' mod='marketplace'}</div>
			{/if}
		

		{hook h="displayMpcollectionfooterhook"}

</div>
</div>
{/block}
