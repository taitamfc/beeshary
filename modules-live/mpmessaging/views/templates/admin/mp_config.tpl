{**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="panel">								
	<div class="panel-heading">
		<i class="icon-book"></i> {l s='Documentation' mod='mpmessaging'}
	</div>
	<div class="alert alert-info">
		{l s='Refer the' mod='mpmessaging'} <a href="https://webkul.com/blog/prestashop-marketplace-sms-notification" target="_blank">{l s='User Guide' mod='mpmessaging'} <i class="icon-external-link-sign"></i></a> {l s='to checkout the complete workflow of the Prestashop SMS Notification module.' mod='mpmessaging'}
	</div>
</div>

<form action="" method="post" class="defaultForm form-horizontal">
	<div class="panel">
    	<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='SMS Setting' mod='mpmessaging'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3 required">
					{l s='Select Address For Customer Mobile Number' mod='mpmessaging'}
				</label>
				<div class="col-lg-3">
					<select name="WK_MPSMS_ADDRESS_TYPE" id="WK_MPSMS_ADDRESS_TYPE">
						<option value="invoice" {if $WK_MPSMS_ADDRESS_TYPE == 'invoice'} selected="selected" {/if}>{l s='Invoice Address' mod='mpmessaging'}</option>
						<option value="delivery" {if $WK_MPSMS_ADDRESS_TYPE == 'delivery'} selected="selected" {/if}>{l s='Delivery Address' mod='mpmessaging'}</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 control-label">{l s='Do you want to add prefix in customer\'s and seller\'s mobile number' mod='mpmessaging'}</label>
				<div class="col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" {if $WK_MPSMS_MOBILE_PREFIX == 1}checked="checked"{/if} value="1" id="WK_MPSMS_MOBILE_PREFIX_on" name="WK_MPSMS_MOBILE_PREFIX" class="smsSetting">
						<label for="WK_MPSMS_MOBILE_PREFIX_on">{l s='Yes' mod='mpmessaging'}</label>
						<input type="radio" {if $WK_MPSMS_MOBILE_PREFIX == 0}checked="checked"{/if} value="0" id="WK_MPSMS_MOBILE_PREFIX_off" name="WK_MPSMS_MOBILE_PREFIX" class="smsSetting">
						<label for="WK_MPSMS_MOBILE_PREFIX_off">{l s='No' mod='mpmessaging'}</label>
						<a class="slide-button btn"></a>
					</span>
					<p class="help-block">
						{l s='You can add prefix country code in customer\'s and seller\'s mobile number. Ex: +91 for India, +1 for USA.' mod='mpmessaging'}
					</p>
				</div>
			</div>

			<div class="form-group WK_MPSMS_MOBILE_PREFIX_Div" {if $WK_MPSMS_MOBILE_PREFIX == 0} style="display:none;"{/if}>
				<label class="col-lg-3 control-label">{l s='Prefix' mod='mpmessaging'}</label>
				<div class="col-lg-6">
					<input type="text" name="WK_MPSMS_MOBILE_PREFIX_NO" id="WK_MPSMS_MOBILE_PREFIX_NO" value="{$WK_MPSMS_MOBILE_PREFIX_NO|escape:'html':'UTF-8'}" />
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button id="btnSubmit" class="btn btn-default pull-right" name="btnSubmit" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='mpmessaging'}
			</button>
		</div>
	</div>


	<div class="panel">
    	<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Customer Template and SMS Setting' mod='mpmessaging'}
		</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="col-lg-3 control-label">{l s='Send Order Confirmation SMS' mod='mpmessaging'}</label>
				<div class="col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" {if $WK_MPSMS_ORDER_SMS == 1}checked="checked"{/if} value="1" id="WK_MPSMS_ORDER_SMS_on" name="WK_MPSMS_ORDER_SMS" class="smsSetting">
						<label for="WK_MPSMS_ORDER_SMS_on">{l s='Yes' mod='mpmessaging'}</label>
						<input type="radio" {if $WK_MPSMS_ORDER_SMS == 0}checked="checked"{/if} value="0" id="WK_MPSMS_ORDER_SMS_off" name="WK_MPSMS_ORDER_SMS" class="smsSetting">
						<label for="WK_MPSMS_ORDER_SMS_off">{l s='No' mod='mpmessaging'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

			<div class="form-group WK_MPSMS_ORDER_SMS_Div" {if $WK_MPSMS_ORDER_SMS == 0} style="display:none;"{/if}>
				<label class="col-lg-3 control-label">{l s='Order Confirmation Template' mod='mpmessaging'}</label>
				<div class="col-lg-6">
					{foreach from=$languages item=language}
						<textarea
						id="WK_MPSMS_ORDER_TEMPLATE_{$language.id_lang|escape:'html':'UTF-8'}" 
						name="WK_MPSMS_ORDER_TEMPLATE_{$language.id_lang|escape:'html':'UTF-8'}" 
						class="form-control WK_MPSMS_ORDER_TEMPLATE" 
						data-lang-name="{$language.name|escape:'html':'UTF-8'}"
						{if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if}>{$WK_MPSMS_ORDER_TEMPLATE[{$language.id_lang|escape:'html':'UTF-8'}]|escape:'html':'UTF-8'}</textarea>
					{/foreach}
					<p class="help-block">
						{l s='Available variable name for Order Confirmation template.' mod='mpmessaging'}
						{literal}{customerFirstName}{/literal} {l s='for customer first name.' mod='mpmessaging'}
						{literal}{customerLastName}{/literal} {l s='for customer last name.' mod='mpmessaging'}
						{literal}{customerOrderHistory}{/literal} {l s='for customer order history.' mod='mpmessaging'}
						{literal}{referenceNo}{/literal} {l s='for order reference number.' mod='mpmessaging'}
						{literal}{orderAmount}{/literal} {l s='for order total paid amount.' mod='mpmessaging'}
					</p>
				</div>
				{if $totalLanguages > 1}
					<div class="col-md-2">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							{$currentLang.iso_code|escape:'html':'UTF-8'}
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							{foreach from=$languages item=language}
								<li>
									<a href="javascript:void(0)" onclick="showSMSLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'}, this);" data-cls-name="WK_MPSMS_ORDER_TEMPLATE">{$language.name|escape:'html':'UTF-8'}</a>
								</li>
							{/foreach}
						</ul>
					</div>
				{/if}
			</div>

			<div class="form-group">
				<label class="col-lg-3 control-label">{l s='Send Order Status Update SMS' mod='mpmessaging'}</label>
				<div class="col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" {if $WK_MPSMS_STATUS_SMS == 1}checked="checked"{/if} value="1" id="WK_MPSMS_STATUS_SMS_on" name="WK_MPSMS_STATUS_SMS" class="smsSetting">
						<label for="WK_MPSMS_STATUS_SMS_on">{l s='Yes' mod='mpmessaging'}</label>
						<input type="radio" {if $WK_MPSMS_STATUS_SMS == 0}checked="checked"{/if} value="0" id="WK_MPSMS_STATUS_SMS_off" name="WK_MPSMS_STATUS_SMS" class="smsSetting">
						<label for="WK_MPSMS_STATUS_SMS_off">{l s='No' mod='mpmessaging'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

			<div class="form-group WK_MPSMS_STATUS_SMS_Div" {if $WK_MPSMS_STATUS_SMS == 0} style="display:none;"{/if}>
				<label class="col-lg-3 control-label">{l s='Order Status Template' mod='mpmessaging'}</label>
				<div class="col-lg-6">
					{foreach from=$languages item=language}
						<textarea
						id="WK_MPSMS_STATUS_TEMPLATE_{$language.id_lang|escape:'html':'UTF-8'}" 
						name="WK_MPSMS_STATUS_TEMPLATE_{$language.id_lang|escape:'html':'UTF-8'}" 
						class="form-control WK_MPSMS_STATUS_TEMPLATE" 
						data-lang-name="{$language.name|escape:'html':'UTF-8'}"
						{if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if}>{$WK_MPSMS_STATUS_TEMPLATE[{$language.id_lang|escape:'html':'UTF-8'}]|escape:'html':'UTF-8'}</textarea>
					{/foreach}
					<p class="help-block">
						{l s='Available variable name for Order Status template.' mod='mpmessaging'}
						{literal}{customerFirstName}{/literal} {l s='for customer first name.' mod='mpmessaging'}
						{literal}{customerLastName}{/literal} {l s='for customer last name.' mod='mpmessaging'}
						{literal}{customerOrderHistory}{/literal} {l s='for customer order history.' mod='mpmessaging'}
						{literal}{orderStatus}{/literal} {l s='for order status.' mod='mpmessaging'}
						{literal}{trackingNumber}{/literal} {l s='for order tracking number.' mod='mpmessaging'}
						{literal}{referenceNo}{/literal} {l s='for order reference number.' mod='mpmessaging'}
						{literal}{orderAmount}{/literal} {l s='for order total paid amount.' mod='mpmessaging'}
					</p>
				</div>
				{if $totalLanguages > 1}
					<div class="col-md-2">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							{$currentLang.iso_code|escape:'html':'UTF-8'}
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							{foreach from=$languages item=language}
								<li>
									<a href="javascript:void(0)" onclick="showSMSLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'}, this);" data-cls-name="WK_MPSMS_STATUS_TEMPLATE">{$language.name|escape:'html':'UTF-8'}</a>
								</li>
							{/foreach}
						</ul>
					</div>
				{/if}
			</div>

			<div class="form-group WK_MPSMS_STATUS_SMS_Div" {if $WK_MPSMS_STATUS_SMS == 0} style="display:none;"{/if}>
				<label class="col-lg-3 control-label">{l s='Select Order Status' mod='mpmessaging'}</label>
				<div class="col-lg-6">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th class="fixed-width-xs">
								<span class="title_box">
								<input name="checkme" id="checkme" onclick="checkDelBoxes(this.form, 'orderStatus[]', this.checked)" type="checkbox">
								</span>
								</th>
								<th class="fixed-width-xs"><span class="title_box">{l s='ID' mod='mpmessaging'}</span></th>
								<th>
								<span class="title_box">
									{l s='State' mod='mpmessaging'}
								</span>
								</th>
							</tr>
						</thead>
						<tbody>
							{foreach $orderStatusList as $status}
								<tr>
									<td>
										<input name="orderStatus[]" class="orderStatus" id="orderStatus_{$status.id_order_state|escape:'html':'UTF-8'}" value="{$status.id_order_state|escape:'html':'UTF-8'}" type="checkbox" {if $WK_MPSMS_ORDER_STATUS} {foreach $WK_MPSMS_ORDER_STATUS as $selectedStatus} {if $selectedStatus == {$status.id_order_state}} checked="checked"{/if}{/foreach}{/if}>
									</td>
									<td>{$status.id_order_state|escape:'html':'UTF-8'}</td>
									<td>
										<label for="orderStatus_{$status.id_order_state|escape:'html':'UTF-8'}">{$status.name|escape:'html':'UTF-8'}</label>
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 control-label">{l s='Send Order Tracking Number SMS' mod='mpmessaging'}</label>
				<div class="col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" {if $WK_MPSMS_TRACKING_SMS == 1}checked="checked"{/if} value="1" id="WK_MPSMS_TRACKING_SMS_on" name="WK_MPSMS_TRACKING_SMS" class="smsSetting">
						<label for="WK_MPSMS_TRACKING_SMS_on">{l s='Yes' mod='mpmessaging'}</label>
						<input type="radio" {if $WK_MPSMS_TRACKING_SMS == 0}checked="checked"{/if} value="0" id="WK_MPSMS_TRACKING_SMS_off" name="WK_MPSMS_TRACKING_SMS" class="smsSetting">
						<label for="WK_MPSMS_TRACKING_SMS_off">{l s='No' mod='mpmessaging'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

			<div class="form-group WK_MPSMS_TRACKING_SMS_Div" {if $WK_MPSMS_TRACKING_SMS == 0} style="display:none;"{/if}>
				<label class="col-lg-3 control-label">{l s='Order Tracking Template' mod='mpmessaging'}</label>
				<div class="col-lg-6">
					{foreach from=$languages item=language}
						<textarea
						id="WK_MPSMS_TRACKING_TEMPLATE_{$language.id_lang|escape:'html':'UTF-8'}" 
						name="WK_MPSMS_TRACKING_TEMPLATE_{$language.id_lang|escape:'html':'UTF-8'}" 
						class="form-control WK_MPSMS_TRACKING_TEMPLATE" 
						data-lang-name="{$language.name|escape:'html':'UTF-8'}"
						{if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if}>{$WK_MPSMS_TRACKING_TEMPLATE[{$language.id_lang|escape:'html':'UTF-8'}]|escape:'html':'UTF-8'}</textarea>
					{/foreach}
					<p class="help-block">
						{l s='Available variable name for Order Tracking template.' mod='mpmessaging'}
						{literal}{customerFirstName}{/literal} {l s='for customer first name.' mod='mpmessaging'}
						{literal}{customerLastName}{/literal} {l s='for customer last name.' mod='mpmessaging'}
						{literal}{customerOrderHistory}{/literal} {l s='for customer order history.' mod='mpmessaging'}
						{literal}{orderStatus}{/literal} {l s='for order status.' mod='mpmessaging'}
						{literal}{trackingNumber}{/literal} {l s='for order tracking number.' mod='mpmessaging'}
						{literal}{referenceNo}{/literal} {l s='for order reference number.' mod='mpmessaging'}
						{literal}{orderAmount}{/literal} {l s='for order total paid amount.' mod='mpmessaging'}
					</p>
				</div>
				{if $totalLanguages > 1}
					<div class="col-md-2">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							{$currentLang.iso_code|escape:'html':'UTF-8'}
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							{foreach from=$languages item=language}
								<li>
									<a href="javascript:void(0)" onclick="showSMSLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'}, this);" data-cls-name="WK_MPSMS_TRACKING_TEMPLATE">{$language.name|escape:'html':'UTF-8'}</a>
								</li>
							{/foreach}
						</ul>
					</div>
				{/if}
			</div>
		</div>
		<div class="panel-footer">
			<button id="btnSubmit" class="btn btn-default pull-right" name="btnSubmit" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='mpmessaging'}
			</button>
		</div>
	</div>

	<div class="panel">
    	<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Seller Template and SMS Setting' mod='mpmessaging'}
		</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="col-lg-3 control-label">{l s='Send Seller Profile Activation/De-activation SMS' mod='mpmessaging'}</label>
				<div class="col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" {if $WK_MPSMS_SELLER_PROFILE_SMS == 1}checked="checked"{/if} value="1" id="WK_MPSMS_SELLER_PROFILE_SMS_on" name="WK_MPSMS_SELLER_PROFILE_SMS" class="smsSetting">
						<label for="WK_MPSMS_SELLER_PROFILE_SMS_on">{l s='Yes' mod='mpmessaging'}</label>
						<input type="radio" {if $WK_MPSMS_SELLER_PROFILE_SMS == 0}checked="checked"{/if} value="0" id="WK_MPSMS_SELLER_PROFILE_SMS_off" name="WK_MPSMS_SELLER_PROFILE_SMS" class="smsSetting">
						<label for="WK_MPSMS_SELLER_PROFILE_SMS_off">{l s='No' mod='mpmessaging'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

			<div class="form-group WK_MPSMS_SELLER_PROFILE_SMS_Div" {if $WK_MPSMS_SELLER_PROFILE_SMS == 0} style="display:none;"{/if}>
				<label class="col-lg-3 control-label">{l s='Seller Profile Activation/De-activationn Template' mod='mpmessaging'}</label>
				<div class="col-lg-6">
					{foreach from=$languages item=language}
						<textarea
						id="WK_MPSMS_SELLER_PROFILE_TEMPLATE_{$language.id_lang|escape:'html':'UTF-8'}" 
						name="WK_MPSMS_SELLER_PROFILE_TEMPLATE_{$language.id_lang|escape:'html':'UTF-8'}" 
						class="form-control WK_MPSMS_SELLER_PROFILE_TEMPLATE" 
						data-lang-name="{$language.name|escape:'html':'UTF-8'}"
						{if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if}>{$WK_MPSMS_SELLER_PROFILE_TEMPLATE[{$language.id_lang|escape:'html':'UTF-8'}]|escape:'html':'UTF-8'}</textarea>
					{/foreach}
					<p class="help-block">
						{l s='Available variable name for Send Seller Profile Activation/De-activation template.' mod='mpmessaging'}
						{literal}{sellerFirstName}{/literal} {l s='for seller first name.' mod='mpmessaging'}
						{literal}{sellerLastName}{/literal} {l s='for seller last name.' mod='mpmessaging'}
						{literal}{status}{/literal} {l s='for seller profile status.' mod='mpmessaging'}
						{literal}{businessEmail}{/literal} {l s='for seller business email.' mod='mpmessaging'}
						{literal}{shopNameUnique}{/literal} {l s='for seller shop name unique.' mod='mpmessaging'}
					</p>
				</div>
				{if $totalLanguages > 1}
					<div class="col-md-2">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							{$currentLang.iso_code|escape:'html':'UTF-8'}
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							{foreach from=$languages item=language}
								<li>
									<a href="javascript:void(0)" onclick="showSMSLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'}, this);" data-cls-name="WK_MPSMS_SELLER_PROFILE_TEMPLATE">{$language.name|escape:'html':'UTF-8'}</a>
								</li>
							{/foreach}
						</ul>
					</div>
				{/if}
			</div>

			<div class="form-group">
				<label class="col-lg-3 control-label">{l s='Send Seller Product Activation/De-activation SMS' mod='mpmessaging'}</label>
				<div class="col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" {if $WK_MPSMS_SELLER_PRODUCT_SMS == 1}checked="checked"{/if} value="1" id="WK_MPSMS_SELLER_PRODUCT_SMS_on" name="WK_MPSMS_SELLER_PRODUCT_SMS" class="smsSetting">
						<label for="WK_MPSMS_SELLER_PRODUCT_SMS_on">{l s='Yes' mod='mpmessaging'}</label>
						<input type="radio" {if $WK_MPSMS_SELLER_PRODUCT_SMS == 0}checked="checked"{/if} value="0" id="WK_MPSMS_SELLER_PRODUCT_SMS_off" name="WK_MPSMS_SELLER_PRODUCT_SMS" class="smsSetting">
						<label for="WK_MPSMS_SELLER_PRODUCT_SMS_off">{l s='No' mod='mpmessaging'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

			<div class="form-group WK_MPSMS_SELLER_PRODUCT_SMS_Div" {if $WK_MPSMS_SELLER_PRODUCT_SMS == 0} style="display:none;"{/if}>
				<label class="col-lg-3 control-label">{l s='Seller Product Activation/De-activation Template' mod='mpmessaging'}</label>
				<div class="col-lg-6">
					{foreach from=$languages item=language}
						<textarea
						id="WK_MPSMS_SELLER_PRODUCT_TEMPLATE_{$language.id_lang|escape:'html':'UTF-8'}" 
						name="WK_MPSMS_SELLER_PRODUCT_TEMPLATE_{$language.id_lang|escape:'html':'UTF-8'}" 
						class="form-control WK_MPSMS_SELLER_PRODUCT_TEMPLATE" 
						data-lang-name="{$language.name|escape:'html':'UTF-8'}"
						{if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if}>{$WK_MPSMS_SELLER_PRODUCT_TEMPLATE[{$language.id_lang|escape:'html':'UTF-8'}]|escape:'html':'UTF-8'}</textarea>
					{/foreach}
					<p class="help-block">
						{l s='Available variable name for Send Seller Profile Activation/De-activation template.' mod='mpmessaging'}
						{literal}{productName}{/literal} {l s='for product name.' mod='mpmessaging'}
						{literal}{sellerFirstName}{/literal} {l s='for seller first name.' mod='mpmessaging'}
						{literal}{sellerLastName}{/literal} {l s='for seller last name.' mod='mpmessaging'}
						{literal}{status}{/literal} {l s='for seller profile status.' mod='mpmessaging'}
						{literal}{businessEmail}{/literal} {l s='for seller business email.' mod='mpmessaging'}
						{literal}{shopNameUnique}{/literal} {l s='for seller shop name unique.' mod='mpmessaging'}
					</p>
				</div>
				{if $totalLanguages > 1}
					<div class="col-md-2">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							{$currentLang.iso_code|escape:'html':'UTF-8'}
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							{foreach from=$languages item=language}
								<li>
									<a href="javascript:void(0)" onclick="showSMSLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'}, this);" data-cls-name="WK_MPSMS_SELLER_PRODUCT_TEMPLATE">{$language.name|escape:'html':'UTF-8'}</a>
								</li>
							{/foreach}
						</ul>
					</div>
				{/if}
			</div>

			<div class="form-group">
				<label class="col-lg-3 control-label">{l s='Send Seller Order Confirmation SMS' mod='mpmessaging'}</label>
				<div class="col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" {if $WK_MPSMS_SELLER_ORDER_SMS == 1}checked="checked"{/if} value="1" id="WK_MPSMS_SELLER_ORDER_SMS_on" name="WK_MPSMS_SELLER_ORDER_SMS" class="smsSetting">
						<label for="WK_MPSMS_SELLER_ORDER_SMS_on">{l s='Yes' mod='mpmessaging'}</label>
						<input type="radio" {if $WK_MPSMS_SELLER_ORDER_SMS == 0}checked="checked"{/if} value="0" id="WK_MPSMS_SELLER_ORDER_SMS_off" name="WK_MPSMS_SELLER_ORDER_SMS" class="smsSetting">
						<label for="WK_MPSMS_SELLER_ORDER_SMS_off">{l s='No' mod='mpmessaging'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

			<div class="form-group WK_MPSMS_SELLER_ORDER_SMS_Div" {if $WK_MPSMS_SELLER_ORDER_SMS == 0} style="display:none;"{/if}>
				<label class="col-lg-3 control-label">{l s='Seller Order Confirmation Template' mod='mpmessaging'}</label>
				<div class="col-lg-6">
					{foreach from=$languages item=language}
						<textarea
						id="WK_MPSMS_SELLER_ORDER_TEMPLATE_{$language.id_lang|escape:'html':'UTF-8'}" 
						name="WK_MPSMS_SELLER_ORDER_TEMPLATE_{$language.id_lang|escape:'html':'UTF-8'}" 
						class="form-control WK_MPSMS_SELLER_ORDER_TEMPLATE" 
						data-lang-name="{$language.name|escape:'html':'UTF-8'}"
						{if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if}>{$WK_MPSMS_SELLER_ORDER_TEMPLATE[{$language.id_lang|escape:'html':'UTF-8'}]|escape:'html':'UTF-8'}</textarea>
					{/foreach}
					<p class="help-block">
						{l s='Available variable name for Seller Order Confirmation template.' mod='mpmessaging'}
						{literal}{customerFirstName}{/literal} {l s='for customer first name.' mod='mpmessaging'}
						{literal}{customerLastName}{/literal} {l s='for customer last name.' mod='mpmessaging'}
						{literal}{referenceNo}{/literal} {l s='for order reference number.' mod='mpmessaging'}
						{literal}{orderAmount}{/literal} {l s='for order total paid amount seller wise.' mod='mpmessaging'}
						{literal}{sellerFirstName}{/literal} {l s='for customer first name.' mod='mpmessaging'}
						{literal}{sellerLastName}{/literal} {l s='for customer last name.' mod='mpmessaging'}
						{literal}{totalNumberOfProducts}{/literal} {l s='for total number of products of order seller wise.' mod='mpmessaging'}
						{literal}{totalQuantityOfAllProducts}{/literal} {l s='for total quantity of products of order seller wise.' mod='mpmessaging'}
					</p>
				</div>
				{if $totalLanguages > 1}
					<div class="col-md-2">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							{$currentLang.iso_code|escape:'html':'UTF-8'}
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							{foreach from=$languages item=language}
								<li>
									<a href="javascript:void(0)" onclick="showSMSLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'}, this);" data-cls-name="WK_MPSMS_SELLER_ORDER_TEMPLATE">{$language.name|escape:'html':'UTF-8'}</a>
								</li>
							{/foreach}
						</ul>
					</div>
				{/if}
			</div>
		</div>
		<div class="panel-footer">
			<button id="btnSubmit" class="btn btn-default pull-right" name="btnSubmit" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='mpmessaging'}
			</button>
		</div>
	</div>


	<div class="panel">
    	<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='API Setting' mod='mpmessaging'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3 required">
					{l s='Select SMS Provider' mod='mpmessaging'}
				</label>
				<div class="col-lg-3">
					<select name="WK_MPSMS_API" id="WK_MPSMS_API">
						{foreach $smsAPIList as $key => $api}
							<option value="{$key|escape:'html':'UTF-8'}" {if $WK_MPSMS_API == $key} selected="selected" {/if}>{$api|escape:'html':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			</div>
			
			<div class="twilio apiDetailAll" {if $WK_MPSMS_API != 'twilio'} style="display:none;"{/if}>
				<div class="form-group">
					<label class="control-label col-lg-3 required">
						{l s='Twilio Account SID' mod='mpmessaging'}
					</label>
					<div class="col-lg-9">
						<input type="text" value="{$WK_MPSMS_TWILIO_AC_ID|escape:'html':'UTF-8'}" name="WK_MPSMS_TWILIO_AC_ID" />
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3 required">
						{l s='Twilio Account Token' mod='mpmessaging'}
					</label>
					<div class="col-lg-9">
						<input type="text" value="{$WK_MPSMS_TWILIO_PASSWORD|escape:'html':'UTF-8'}" name="WK_MPSMS_TWILIO_PASSWORD" />
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3 required">
						{l s='Twilio Number' mod='mpmessaging'}
					</label>
					<div class="col-lg-9">
						<input type="text" value="{$WK_MPSMS_TWILIO_NUMBER|escape:'html':'UTF-8'}" name="WK_MPSMS_TWILIO_NUMBER" />
					</div>
				</div>
			</div>

			<div class="plivo apiDetailAll" {if $WK_MPSMS_API != 'plivo'} style="display:none;"{/if}>
				<div class="form-group">
					<label class="control-label col-lg-3 required">
						{l s='Plivo Auth ID' mod='mpmessaging'}
					</label>
					<div class="col-lg-9">
						<input type="text" value="{$WK_MPSMS_PLIVO_AUTH_ID|escape:'html':'UTF-8'}" name="WK_MPSMS_PLIVO_AUTH_ID" />
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3 required">
						{l s='Plivo Auth Token' mod='mpmessaging'}
					</label>
					<div class="col-lg-9">
						<input type="text" value="{$WK_MPSMS_PLIVO_TOKEN|escape:'html':'UTF-8'}" name="WK_MPSMS_PLIVO_TOKEN" />
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3 required">
						{l s='Your Number' mod='mpmessaging'}
					</label>
					<div class="col-lg-9">
						<input type="text" value="{$WK_MPSMS_PLIVO_NUMBER|escape:'html':'UTF-8'}" name="WK_MPSMS_PLIVO_NUMBER" />
					</div>
				</div>
			</div>

			<div class="clicksend apiDetailAll" {if $WK_MPSMS_API != 'clicksend'} style="display:none;"{/if}>
				<div class="form-group">
					<label class="control-label col-lg-3 required">
						{l s='ClickSend User Name' mod='mpmessaging'}
					</label>
					<div class="col-lg-9">
						<input type="text" value="{$WK_MPSMS_CLICKSEND_USER_NAME|escape:'html':'UTF-8'}" name="WK_MPSMS_CLICKSEND_USER_NAME" />
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3 required">
						{l s='ClickSend API Key' mod='mpmessaging'}
					</label>
					<div class="col-lg-9">
						<input type="text" value="{$WK_MPSMS_CLICKSEND_API_KEY|escape:'html':'UTF-8'}" name="WK_MPSMS_CLICKSEND_API_KEY" />
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3 required">
						{l s='Your Number' mod='mpmessaging'}
					</label>
					<div class="col-lg-9">
						<input type="text" value="{$WK_MPSMS_CLICKSEND_NUMBER|escape:'html':'UTF-8'}" name="WK_MPSMS_CLICKSEND_NUMBER" />
					</div>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button id="btnSubmit" class="btn btn-default pull-right" name="btnSubmit" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='mpmessaging'}
			</button>
		</div>
	</div>
</form>