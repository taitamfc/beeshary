{*
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($mpProducts)}
<section id="products">
	<div class="products_slider mp-related-products{if count($mpProducts) > 4} wk-aria-hidden{/if}"  id="product-slider_block_center">
		<h3 class="page-product-heading mp-heading h4">{if isset($display_id) && $display_id == 1}{l s='Also see related products' mod='mprelatedproduct'}{else if isset($display_id) && $display_id == 2}{l s='Also see related purchased products' mod='mprelatedproduct'}{/if} </h3>
		<div class="home-product-block" id="categoryproductlist_div">
			<div class="home-product-block">
				<div class="{if count($mpProducts) > 4}catblog{else}grid row tab-pane active{/if} products">
					{foreach from=$mpProducts item="product"}
						<div>
                        	{include file="modules/mprelatedproduct/views/templates/hook/product.tpl" product=$product}
						</div>
                    {/foreach}
				</div>
			</div>
		</div>
	</div>
</section>
{/if}