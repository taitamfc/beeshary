{**
* 2010-2017 Webkul
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($display_badge_on_seller_profile) && $display_badge_on_seller_profile}
	<div class="wk_row">
		<label>{l s=' Seller Badges' mod='mpbadgesystem'} - </label>
		<span>
			{foreach $seller_badges as $badge}
				<img src="{$modules_dir|escape:'htmlall':'UTF-8'}mpbadgesystem/views/img/badge_img/{$badge['badge_id']|escape:'htmlall':'UTF-8'}.jpg" title="{$badge['badge_name']|escape:'htmlall':'UTF-8'}" width="50" height="50" style="margin-right:10px;"/>
			{/foreach}
		</span>
		<div class="clearfix"></div>
	</div>
{else}
	<div id="block-reassurance" class="wk-edit-product">
		<p>
			<strong> {l s='Seller Badges -' mod='mpbadgesystem'} </strong>
			<span>
				{foreach $seller_badges as $badge}
					<img src="{$modules_dir|escape:'htmlall':'UTF-8'}mpbadgesystem/views/img/badge_img/{$badge['badge_id']|escape:'htmlall':'UTF-8'}.jpg" title="{$badge['badge_name']|escape:'htmlall':'UTF-8'}" width="50" height="50" style="margin-right:10px;opacity: 1;width: 50px; height: 50px"/>
				{/foreach}
			</span>
		</p>
	</div>
{/if}
