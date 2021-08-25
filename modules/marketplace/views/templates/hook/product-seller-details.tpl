{*
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if $MP_SHOW_SELLER_DETAILS}
		<div class="block-wk-sellerdetails">
			<h3>{l s='Seller Details' mod='marketplace'}</h3>
			<div class="sellerinfo">
				{if isset($mp_seller_info)}
					{if $MP_SELLER_DETAILS_ACCESS_1}
						<div class="wk_row">
							<label class="wk-person-icon">{l s='Seller Name' mod='marketplace'} - </label>
							<span>{$mp_seller_info.seller_name}</span>
						</div>
					{/if}
					{if $MP_SELLER_DETAILS_ACCESS_2}
						<div class="wk_row">
							<label class="wk-shop-icon">{l s='Shop Name' mod='marketplace'} - </label>
							<span>{$mp_seller_info.shop_name}</span>	
						</div>
					{/if}
					{if $MP_SELLER_DETAILS_ACCESS_3}
						<div class="wk_row">
							<label class="wk-mail-icon">{l s='Seller email' mod='marketplace'} - </label>
							<span>{$mp_seller_info.business_email}</span>
						</div>
					{/if}
					{if $MP_SELLER_DETAILS_ACCESS_4}
						<div class="wk_row">
							<label class="wk-phone-icon">{l s='Phone' mod='marketplace'} - </label>
							<span>{$mp_seller_info.phone}</span>
						</div>
					{/if}
					{if $MP_SELLER_DETAILS_ACCESS_5}
						<div class="wk_row">
							<label class="wk-share-icon">{l s='Social Profile' mod='marketplace'} - </label>
							<span class="wk-social-icon">
								{if $mp_seller_info.facebook_id != ""}
									<a class="wk-facebook-button" target="_blank" title="Facebook" href="http://www.facebook.com/{$mp_seller_info.facebook_id}"></a>
								{/if}
								{if $mp_seller_info.twitter_id != ""}
									<a class="wk-twitter-button" target="_blank" title="Twitter" href="http://www.twitter.com/{$mp_seller_info.twitter_id}"></a>
								{/if}
							</span>
						</div>
					{/if}
					{hook h='displayMpSellerDetailTabLeft'}
				{/if}
			</div>	
			<div class="sellerlink">
				<ul>
					{if $MP_SELLER_DETAILS_ACCESS_6}
						<li>
							<a id="profileconnect" title="{l s='Visit Profile' mod='marketplace'}" target="_blank" href="{$sellerprofile_link}">{l s='View Profile' mod='marketplace'}</a>
						</li>
					{/if}
					{if $MP_SELLER_DETAILS_ACCESS_7}
						<li>
							<a id="siteconnect" title="{l s='Visit Collection' mod='marketplace'}" target="_blank" href="{$shopcollection_link}">{l s='View Collection' mod='marketplace'}</a>
						</li>
					{/if}
					{if $MP_SELLER_DETAILS_ACCESS_8}
						<li>
							<a id="storeconnect" title="{l s='Visit Store' mod='marketplace'}" target="_blank" href="{$shopstore_link}">{l s='View Store' mod='marketplace'}</a>
						</li>
					{/if}
					{if $MP_SELLER_DETAILS_ACCESS_9}
						<li>
							<a href="#wk_question_form" class="open-question-form" data-toggle="modal" data-target="#myModal" title="{l s='Contact Seller' mod='marketplace'}">{l s='Contact Seller' mod='marketplace'}</a>
						</li>
					{/if}
					{hook h='displayMpSellerDetailTabRight'}
				</ul>	
			</div>
		</div>
		<div class="clearfix"></div>

		{hook h='displayMpSellerDetailTabBotttom'}

		{block name='product_images_modal'}
			{include file='module:marketplace/views/templates/hook/_partials/contact-seller-form.tpl'}
	    {/block}
{/if}