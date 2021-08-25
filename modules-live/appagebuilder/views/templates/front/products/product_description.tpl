{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
{block name='product_description'}
  <div class="product-description" itemprop="description">
  	{$product.description|strip_tags nofilter}
  </div>
{/block}