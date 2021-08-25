{**
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
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

{extends file=$extendFilePath}
{block name='cart_detailed_product'}
    <div class="cart-overview js-cart" data-refresh-url="{url entity='cart' params=['ajax' => true, 'action' => 'refresh']}">
        {if $sellerWiseProducts}
            {foreach from=$sellerWiseProducts item=sellerProductsInfo name=sellerProductsLoop}
                <div class="row {if !$smarty.foreach.sellerProductsLoop.last}wk-sellerWiseProduct-block{/if}">
                    <div class="col-sm-12 col-xs-12">
                        <div class="wk-seller-block">
                            <a href="{$sellerProductsInfo['seller']['shop_link']}" title="{$sellerProductsInfo['seller']['shop_name']}">
                                <span>{$sellerProductsInfo['seller']['shop_name']}</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xs-12">
                        <ul class="cart-items">
                            {foreach from=$sellerProductsInfo.products item=product}
                                <li class="cart-item">
                                    {block name='cart_detailed_product_line'}
                                        {include file='checkout/_partials/cart-detailed-product-line.tpl' product=$product}
                                    {/block}
                                </li>
                                {if $product.customizations|count >1}<hr>{/if}
                            {/foreach}
                        </ul>
                    </div>
                </div>
            {/foreach}
        {else}
            <span class="no-items">{l s='There are no more items in your cart' d='Shop.Theme.Checkout'}</span>
        {/if}
    </div>
{/block}
