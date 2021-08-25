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

<style>
#seller_badge{
	float:left;
	margin-right:10px;
}
</style>
<div class="form-group" style="margin-top:10px;">  
	<label class="col-lg-3 control-label">{l s='Seller Badges:' mod='mpbadgesystem'}</label>
	<div class="col-lg-9">
		{if !empty($seller_badge_info)}
			{foreach $seller_badge_info as $seller_badge}
			<div id="seller_badge">
				<img src="{$modules_dir|escape:'htmlall':'UTF-8'}mpbadgesystem/views/img/badge_img/{$seller_badge['badge_id']|escape:'htmlall':'UTF-8'}.jpg" title="{$seller_badge['badge_name']|escape:'htmlall':'UTF-8'}" width="100" height="100" style="float:left;"/>
			</div>
			{/foreach}
		{/if}
	</div>
</div>