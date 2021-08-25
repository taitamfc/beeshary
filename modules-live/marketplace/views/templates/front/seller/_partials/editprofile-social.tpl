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

<div class="alert alert-info">
	{l s='Enter Social Profile User id\'s to be displayed on Seller\'s profile page and shop page (Display of these will depend on the \'Social Profile\' option selected/not selected by seller in \'Permission\' Tab )' mod='marketplace'}
</div>
<div class="form-group row">
	{if Configuration::get('WK_MP_SELLER_FACEBOOK')}
	<div class="col-md-6 wk_social_tabs">
		<label for="facebook_id" class="control-label">{l s='Facebook ID' mod='marketplace'}</label>
		<input class="form-control"
		type="text"
		value="{if isset($smarty.post.facebook_id)}{$smarty.post.facebook_id}{else}{$mp_seller_info.facebook_id}{/if}"
		name="facebook_id"
		id="facebook_id" />
	</div>
	{/if}

	{if Configuration::get('WK_MP_SELLER_TWITTER')}
	<div class="col-md-6 wk_social_tabs">
		<label for="twitter_id" class="control-label">{l s='Twitter ID' mod='marketplace'}</label>
		<input class="form-control"
		type="text"
		value="{if isset($smarty.post.twitter_id)}{$smarty.post.twitter_id}{else}{$mp_seller_info.twitter_id}{/if}"
		name="twitter_id"
		id="twitter_id" />
	</div>
	{/if}

	{if Configuration::get('WK_MP_SELLER_GOOGLE')}
	<div class="col-md-6 wk_social_tabs">
		<label for="google_id" class="control-label">{l s='Google+ ID' mod='marketplace'}</label>
		<input class="form-control"
		type="text"
		value="{if isset($smarty.post.google_id)}{$smarty.post.google_id}{else}{$mp_seller_info.google_id}{/if}"
		name="google_id"
		id="google_id" />
	</div>
	{/if}

	{if Configuration::get('WK_MP_SELLER_INSTAGRAM')}
	<div class="col-md-6 wk_social_tabs">
		<label for="instagram_id" class="control-label">{l s='Instagram ID' mod='marketplace'}</label>
		<input class="form-control"
		type="text"
		value="{if isset($smarty.post.instagram_id)}{$smarty.post.instagram_id}{else}{$mp_seller_info.instagram_id}{/if}"
		name="instagram_id"
		id="instagram_id" />
	</div>
	{/if}
</div>