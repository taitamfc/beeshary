{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<div class="highlighted-informations{if !$product.main_variants} no-variants{/if} hidden-sm-down">
	{block name='product_variants'}
	  {if $product.main_variants}
		{include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
	  {/if}
	{/block}
  </div>