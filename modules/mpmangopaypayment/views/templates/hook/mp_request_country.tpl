{*
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

<div class="form-group" >
	<label for="mgp_seller_country" class="control-label required">{l s='Country' mod='mpmangopaypayment'}</label>
	{if isset($countries)}
		<select name="seller_id_country" class="form-control" style="width:300px;">
			{foreach $countries as $country}
				<option value="">{l s='Select' mod='mpmangopaypayment'}</option>
				<option value="{$country.id_country|escape:'htmlall':'UTF-8'}">{$country.name|escape:'htmlall':'UTF-8'}</option>
			{/foreach}
		</select>
	{else}
		<p>{l s='Country list not available.' mod='mpmangopaypayment'}</p>
	{/if}
</div>