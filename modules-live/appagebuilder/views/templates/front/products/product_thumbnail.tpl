{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
{block name='product_thumbnail'}
{if isset($cfg_product_list_image) && $cfg_product_list_image}
	<div class="leo-more-info" data-idproduct="{$product.id_product}"></div>
{/if}

{if $product.cover}
    {if isset($formAtts) && isset($formAtts.lazyload) && $formAtts.lazyload}
        {* ENABLE LAZY LOAD OWL_CAROUSEL *}
	<a href="{$product.url}" class="thumbnail product-thumbnail">
	  <img
		class="img-fluid lazyOwl"
		src = ""
		data-src = "{$product.cover.bySize.home_default.url}"
		alt = "{$product.cover.legend}"
		data-full-size-image-url = "{$product.cover.large.url}"
	  >
	  {if isset($cfg_product_one_img) && $cfg_product_one_img}
		<span class="product-additional" data-idproduct="{$product.id_product}"></span>
	  {/if}
	</a> 
    {else}
	<a href="{$product.url}" class="thumbnail product-thumbnail">
	  <img
		class="img-fluid"
		src = "{$product.cover.bySize.home_default.url}"
		alt = "{$product.cover.legend}"
		data-full-size-image-url = "{$product.cover.large.url}"
	  >
	  {if isset($cfg_product_one_img) && $cfg_product_one_img}
		<span class="product-additional" data-idproduct="{$product.id_product}"></span>
	  {/if}
	</a>
    {/if}
{else}
  <a href="{$product.url}" class="thumbnail product-thumbnail leo-noimage">
 <img
   src = "{$urls.no_picture_image.bySize.home_default.url}"
 >
  </a>
{/if}
  
{/block}

