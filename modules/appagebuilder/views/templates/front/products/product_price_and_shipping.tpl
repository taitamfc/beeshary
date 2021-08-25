{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file /modules/appagebuilder/views/templates/front/products/product_price_and_shipping.tpl -->
{block name='product_price_and_shipping'}
  {if $product.show_price}
    <div class="product-price-and-shipping {if $product.has_discount}has_discount{/if}">
      {if $product.has_discount}
        {hook h='displayProductPriceBlock' product=$product type="old_price"}
        <span class="sr-only">{l s='Regular price' d='Shop.Theme.Catalog'}</span>
        <span class="regular-price">{$product.regular_price}</span>
        {if $product.discount_type === 'percentage'}
          <span class="discount-percentage">{$product.discount_percentage}</span>
        {elseif $product.discount_type === 'amount'}
          <span class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
        {/if}
      {/if}

      {hook h='displayProductPriceBlock' product=$product type="before_price"}
      
      <span class="sr-only">{l s='Price' d='Shop.Theme.Catalog'}</span>
      <span class="price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
        <span itemprop="priceCurrency" content=""></span><span itemprop="price" content="{$product.price_amount}">{$product.price}</span>
      </span>

      {hook h='displayProductPriceBlock' product=$product type='unit_price'}

      {hook h='displayProductPriceBlock' product=$product type='weight'}
    </div>
  {/if}
{/block}
