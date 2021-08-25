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

{extends file=$layout}
{block name='content'}
<div class="wk-mp-block">
	{hook h="displayMpMenu"}
	<div class="wk-mp-content">
		<div class="page-title" style="background-color:{$title_bg_color};">
			<span style="color:{$title_text_color};">{l s='Product Details' mod='marketplace'}</span>
		</div>
		<div class="wk-mp-right-column">
			<div class="wk_head row">
				<div class="col-xs-12 col-sm-6 col-md-6">
					<a href="{$link->getModuleLink('marketplace','productlist')}" class="btn btn-link wk_padding_none">
						<i class="material-icons">&#xE5C4;</i>
						<span>{l s='Back to product list' mod='marketplace'}</span>
					</a>
				</div>
				{if isset($is_approved)}
				<div class="col-xs-12 col-sm-6 col-md-6">
					<a title="{l s='View Product' mod='marketplace'}" href="{$link->getProductLink($obj_product)}" class="wk-edit-profile-link" style="float:right;">
						<button class="btn btn-primary btn-sm wk_edit_profile_btn">
							{l s='View Product' mod='marketplace'}
						</button>
					</a>
				</div>
				{/if}
			</div>
			<div class="wk_product_details row">
				<input type="hidden" name="token" id="wk-static-token" value="{$static_token}">
				<div class="wk_details">
					<div class="row">
						<label class="col-md-4">{l s='Product Name' mod='marketplace'} - </label>
						<div class="col-md-8">{$product.product_name}</div>
					</div>
					{if $product.description != ''}
						<div class="row">
							<label class="col-md-4">{l s='Description' mod='marketplace'} -	</label>
							<div class="col-md-8">{$product.description nofilter}</div>
						</div>
					{/if}
					<div class="row">
						<label class="col-md-4">{l s='Price' mod='marketplace'} -</label>
						<div class="col-md-8">{$product.price}</div>
					</div>
					<div class="row">
						<label class="col-md-4">{l s='Quantity' mod='marketplace'} -</label>
						<div class="col-md-8">{$product.quantity}</div>
					</div>
					<div class="row">
						<label class="col-md-4">{l s='Status' mod='marketplace'} -</label>
						<div class="col-md-8">
							{if $product.active == 1}
							   {l s='Approved' mod='marketplace'}
							 {else}
					           {l s='Pending' mod='marketplace'}
					         {/if}
						</div>
					</div>
					<div class="row">
						<label class="col-md-2"></label>
						<div class="col-md-10">
							{if isset($admin_commission)}
								<div id="wk_display_admin_commission" class="alert alert-info">{l s='Admin commission will be' mod='marketplace'} {$admin_commission|escape:'html':'UTF-8'}% {l s='of your product price' mod='marketplace'}</div>
							{/if}
						</div>
					</div>
				</div>
				<div class="wk_image">
					{if isset($cover_image)}
						<a class="mp-img-preview" href="{$smarty.const._MODULE_DIR_}/marketplace/views/img/product_img/{$cover_image.seller_product_image_name}">
							<img id="wk-product-detail-cover" src="{$smarty.const._MODULE_DIR_}/marketplace/views/img/product_img/{$cover_image.seller_product_image_name}" style="width: 83%;height: auto;" />
						</a>
					{/if}
				</div>
			</div>

			{block name='imageedit'}
				{include file='module:marketplace/views/templates/front/product/imageedit.tpl'}
			{/block}

			<div class="left full">
				{hook h="displayMpProductDetailsFooter"}
			</div>
		</div>
	</div>

	{block name='mp_image_preview'}
		{include file='module:marketplace/views/templates/front/product/_partials/mp-image-preview.tpl'}
	{/block}
</div>
{/block}
