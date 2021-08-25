{**
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-2015 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER
* support@mypresta.eu
*}

<div id="featured-category-products_block_center" class="block products_block clearfix">
    <!--h4 class="title_block">{l s='Featured products' mod='featuredcategory'}</h4-->
    {if isset($products) AND $products}
        <div class="block_content">
            {assign var='liHeight' value=250}
            {assign var='nbItemsPerLine' value=4}
            {assign var='nbLi' value=$products|@count}
            {math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
            {math equation="nbLines*liHeight" nbLines=$nbLines|ceil liHeight=$liHeight assign=ulHeight}
            <ul style="height:{$ulHeight|escape:'html'}px;"  id="featured-product" class="owl-carousel">
			
                {foreach from=$products item=product name=featuredcategoryProducts}
                    {math equation="(total%perLine)" total=$smarty.foreach.featuredcategoryProducts.total perLine=$nbItemsPerLine assign=totModulo}
                    {if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
                    <li class="ajax_block_product {if $smarty.foreach.featuredcategoryProducts.first}first_item{elseif $smarty.foreach.featuredcategoryProducts.last}last_item{else}item{/if} {if $smarty.foreach.featuredcategoryProducts.iteration%$nbItemsPerLine == 0}last_item_of_line{elseif $smarty.foreach.featuredcategoryProducts.iteration%$nbItemsPerLine == 1} {/if} {if $smarty.foreach.featuredcategoryProducts.iteration > ($smarty.foreach.featuredcategoryProducts.total - $totModulo)}last_line{/if}">
                        <a href="{$product.link|escape:'html'}" title="{$product.name|escape:html:'UTF-8'}" class="product_image"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}" height="{$homeSize.height}" width="{$homeSize.width}" alt="{$product.name|escape:html:'UTF-8'}"/>{if isset($product.new) && $product.new == 1}<span class="new">{l s='New' mod='featuredcategory'}</span>{/if}</a>
                        <h5 class="s_title_block"><a href="{$product.link|escape:'html'}" title="{$product.name|truncate:50:'...'|escape:'html':'UTF-8'}">{$product.name|truncate:35:'...'|escape:'html':'UTF-8'}</a></h5>
                        <div class="product_desc"><a href="{$product.link|escape:'html'}" title="{l s='More' mod='featuredcategory'}">{$product.description_short|strip_tags|truncate:65:'...'}</a></div>
                        <div>
                            <a class="lnk_more" href="{$product.link|escape:'html'}" title="{l s='View' mod='featuredcategory'}">{l s='View' mod='featuredcategory'}</a>
                            {if $product.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}<p class="price_container"><span class="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span></p>{else}
                                <div style="height:21px;"></div>
                            {/if}

                            {if ($product.id_product_attribute == 0 OR (isset($add_prod_display) AND ($add_prod_display == 1))) AND $product.available_for_order AND !isset($restricted_country_mode) AND $product.minimal_quantity == 1 AND $product.customizable != 2 AND !$PS_CATALOG_MODE}
                                {if ($product.quantity > 0 OR $product.allow_oosp)}
                                    <a class="exclusive ajax_add_to_cart_button" rel="ajax_id_product_{$product.id_product}" href="{$link->getPageLink('cart')|escape:'html'}?qty=1&amp;id_product={$product.id_product}&amp;token={$static_token}&amp;add" title="{l s='Add to cart' mod='featuredcategory'}">{l s='Add to cart' mod='featuredcategory'}</a>
                                {else}
                                    <span class="exclusive">{l s='Add to cart' mod='featuredcategory'}</span>
                                {/if}
                            {else}
                                <div style="height:23px;"></div>
                            {/if}
                        </div>
                    </li>
                {/foreach}
            </ul>
        </div>
    {else}
        <p>{l s='No featured products' mod='featuredcategory'}</p>
    {/if}
</div>