{*
* 2007-2017 IQIT-COMMERCE.COM
*
* NOTICE OF LICENSE
*
*  @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
*  @copyright 2007-2017 IQIT-COMMERCE.COM
*  @license   GNU General Public License version 2
*
* You can not resell or redistribute this software.
*
*}
	<ul class="cbp-products-list {if $perline==12}cbp-products-list-one{/if} row ">
	{foreach from=$products item=productMenu name=homeFeaturedProducts}
	<li class="ajax_block_product col-xs-{$perline}">
		<div class="product-container clearfix">
		<div class="product-image-container">
			<a class="product_img_link"	href="{$productMenu.link}" title="{$productMenu.name}" >
				          <img class="img-fluid img-responsive"
                                 src="{$productMenu.cover.bySize.medium_default.url}"
                                 alt="{if !empty($productMenu.legend)}{$productMenu.legend}{else}{$productMenu.name}{/if}"
                                    {if isset($mediumSize)} width="{$mediumSize.width}" height="{$mediumSize.height}"{/if}/>
			</a>
		</div>
		<div class="cbp-product-info">
			{if isset($productMenu.pack_quantity) && $productMenu.pack_quantity}{$productMenu.pack_quantity|intval|cat:' x '}{/if}
			<a class="cbp-product-name" href="{$productMenu.link}" title="{$productMenu.name}" >
				{$productMenu.name|truncate:35:'...'}
			</a>

                    {if $productMenu.show_price}
            <div class="product-price-and-shipping">
              {if $productMenu.has_discount}
                {hook h='displayProductPriceBlock' product=$productMenu type="old_price"}

                <span class="sr-only">{l s='Regular price' d='Shop.Theme.Catalog'}</span>
                <span class="regular-price">{$productMenu.regular_price}</span>
                {if $productMenu.discount_type === 'percentage'}
                  <span class="discount-percentage">{$productMenu.discount_percentage}</span>
                {/if}
              {/if}

              {hook h='displayProductPriceBlock' product=$productMenu type="before_price"}

              <span class="sr-only">{l s='Price' d='Shop.Theme.Catalog'}</span>
              <span itemprop="price" class="price">{$productMenu.price}</span>
              {hook h='displayPpcPriceList' id_product=$productMenu.id_product}

              {hook h='displayProductPriceBlock' product=$productMenu type='unit_price'}

            {hook h='displayProductPriceBlock' product=$productMenu type='weight'}
          </div>
        {/if}
			
	</div></div>
	</li>

	{/foreach}
</ul>
