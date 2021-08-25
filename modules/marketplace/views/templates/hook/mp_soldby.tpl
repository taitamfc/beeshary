{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($wk_mp_product_link)}
<div class="wk_edit_product_btn">
	<a title="{l s='Edit Product' mod='marketplace'}" class="wk_seller_edit" href="{$wk_mp_product_link}">
		<i class="material-icons">&#xE254;</i>
		<span>{l s='Edit Product' mod='marketplace'}</span>
	</a>
</div>
{/if}

{if isset($showDetail)}
	{if isset($mp_seller_info)}
		<div class="clearfix wk_soldby_link">
			{* Display seller rating *}
			{if Configuration::get('WK_MP_REVIEW_SETTINGS') && isset($totalReview)}
				<div class="wk-product-page-seller-rating">
					{if isset($WK_MP_SELLER_DETAILS_ACCESS_9)}
						<div class="wk-sold-by-box">
							<img class="wk-shop-default-icon" src="{$shop_logo_path}">
							<a id="wk-profileconnect" title="{l s='Visit Shop' mod='marketplace'}" target="_blank" href="{$shopstore_link}">
								<span>{$mp_seller_info.shop_name}</span>
							</a>
						</div>
					{/if}
					{block name='mp-seller-rating-summary'}
						{include file='module:marketplace/views/templates/front/seller/_partials/seller-rating-summary.tpl'}
					{/block}
				</div>
			{else}
				{if isset($WK_MP_SELLER_DETAILS_ACCESS_9)}
					<strong>{l s='Sold By' mod='marketplace'} - </strong>
					<a id="profileconnect" title="{l s='Visit Shop' mod='marketplace'}" target="_blank" href="{$shopstore_link}">
						<span>{$mp_seller_info.shop_name}</span>
					</a>
				{/if}
			{/if}
		</div>
	{/if}
{/if}
{hook h="displayMpProductSoldByBottom"}

{* Load rating code again on QUICK VIEW or CART AJAX CALL because on changing of product qty, seller rating was going to hidden *}
{if isset($sellerRating) && $sellerRating && isset($call_ajax) && ($call_ajax == 'quickview' || $call_ajax == 'refresh')}
	{if $call_ajax == 'quickview'}
		{* We have to assign this js from here because hookActionFrontControllerSetMedia is not working on Quick View *}
		<script type="text/javascript" src="{$smarty.const._MODULE_DIR_}marketplace/views/js/libs/jquery.raty.min.js"></script>
	{/if}
	<script type="text/javascript">
		$('#seller_rating').raty({
			path: "{$smarty.const._MODULE_DIR_}marketplace/views/img/",
			score: "{$sellerRating}",
			readOnly: true,
		});
	</script>
{/if}