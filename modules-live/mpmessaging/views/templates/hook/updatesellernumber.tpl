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
	
	<div class="row">
		<div class="col-md-12">
			<div class="input-group">
			<label for="sellerCountryCode" class="input-group-addon control-label required">{l s='Country Code' mod='mpmessaging'}</label>
			<input type="text" class="form-control" name="sellerCountryCode" id="sellerCountryCode" {if isset($sellerCountryCode)} value="{$sellerCountryCode}" {/if} />
			</div>
			<p class="help-block">{l s='Country code eg. +1 for United States' mod='mpmessaging'}</p>
		</div>
	</div>
</div>
<div class="form-group col-sm-6">
	
	<div class="row">
		<div class="col-md-12">
			<div class="input-group">
			<label for="sellerMobileNumber" class="input-group-addon control-label required">{l s='Mobile Number' mod='mpmessaging'}</label>
			<input class="form-control is_required "  data-validate="isPhoneNumber" type="text" name="sellerMobileNumber" id="sellerMobileNumber" {if isset($sellerMobileNumber)} value="{$sellerMobileNumber}" {/if} />
			</div>
			<p class="help-block">{l s='Mobile number for messaging' mod='mpmessaging'}</p>
		</div>
	</div>
</div>