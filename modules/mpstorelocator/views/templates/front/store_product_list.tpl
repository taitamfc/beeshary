{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{* <div class="pull-left {$class}" id="{$id}"> *}
{* <div class="wk-store-heading">{l s='Products' mod='mpstorelocator'}</div> *}
<ul class="row">
{foreach $products as $product}
    <article class="product-miniature js-product-miniature col-md-3" data-id-product="1" data-id-product-attribute="1" itemscope="">
        <div class="thumbnail-container">
            <a href="{$product.link}" class="thumbnail product-thumbnail">
            <img src="{$product.image|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}">
            </a>
        <div class="product-description">
            <h1 class="h3 product-title" itemprop="name">
                <a href="{$product.link}">
                {$product.name}
                </a>
            </h1>
            {* <div class="product-price-and-shipping">
                <span itemprop="price" class="">{$product.price}</span>
            </div> *}
        </div>
    </article>
{/foreach}
</ul>
{* </div> *}
{block name='store_locator_pagination_sort'}
    <div class="row">
        {include file='module:mpstorelocator/views/templates/front/mpstorelocator-pagination.tpl'}
    </div>
{/block}
