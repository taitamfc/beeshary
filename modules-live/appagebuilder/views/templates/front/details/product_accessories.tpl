{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
{block name='product_accessories'}
  {if $accessories}
    <section class="product-accessories clearfix">
      <h3 class="h5 products-section-title">{l s='You might also like' d='Shop.Theme.Catalog'}</h3>
      <div class="products">
        <div class="row">
          {foreach from=$accessories item="product_accessory"}
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6 col-sp-12 ajax_block_product">
              {block name='product_miniature'}
                {if isset($productProfileDefault) && $productProfileDefault}
                    {* exits THEME_NAME/profiles/profile_name.tpl -> load template*}
                    {hook h='displayLeoProfileProduct' product=$product_accessory profile=$productProfileDefault}
                {else}
                    {include file='catalog/_partials/miniatures/product.tpl' product=$product_accessory}
                {/if}
              {/block}
            </div>
          {/foreach}
        </div>
      </div>
    </section>
  {/if}
{/block}