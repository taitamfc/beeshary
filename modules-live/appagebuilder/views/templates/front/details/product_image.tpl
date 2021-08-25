{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
{block name='page_content_container'}
  <section class="page-content" id="content">
    {block name='page_content'}
      <div class="images-container">
        {block name='product_cover_thumbnails'}
          {include file='catalog/_partials/product-cover.tpl'}
          {include file='catalog/_partials/product-images.tpl'}
        {/block}
        {hook h='displayAfterProductThumbs'}
      </div>
    {/block}
  </section>
{/block}