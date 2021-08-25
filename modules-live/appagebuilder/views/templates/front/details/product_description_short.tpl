{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
{block name='product_description_short'}
  <div id="product-description-short-{$product.id}" class="description-short" itemprop="description">{$product.description_short nofilter}</div>
{/block}