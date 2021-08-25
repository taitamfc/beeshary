{**
* 2010-2016 Webkul.
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

<div class="form-group col-sm-6">
	<label for="sellerCountryCode" class="col-lg-3 control-label required">{l s='Country Code' mod='mpmessaging'}</label>
	<div class="col-lg-6">
		<input class="form-control"  type="text" name="sellerCountryCode" id="sellerCountryCode" {if isset($sellerCountryCode)} value="{$sellerCountryCode}" {/if} />
		<p class="help-block">{l s='Country code eg. +1 for United States' mod='mpmessaging'}</p>
	</div>
	
</div>
<div class="form-group col-sm-6">
	<label for="sellerMobileNumber" class="col-lg-3 control-label required" >{l s='Mobile Number' mod='mpmessaging'}</label>
	<div class="col-lg-6">
		<input class="is_required form-control"  data-validate="isPhoneNumber" type="text" name="sellerMobileNumber" id="sellerMobileNumber" {if isset($sellerMobileNumber)} value="{$sellerMobileNumber}" {/if} />
		<p class="help-block">{l s='Mobile number for messaging' mod='mpmessaging'}</p>
	</div>
</div>
