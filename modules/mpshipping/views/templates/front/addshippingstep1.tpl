{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($isAdminAddCarrier) && $isAdminAddCarrier}
	{if !isset($mp_shipping_id)}
		<div class="form-group row">
			<label class="control-label required" style="font-weight: normal;">
				{l s='Choose Seller' mod='mpshipping'}
			</label>
			<div class="control-label">
				{if isset($customer_info)}
					<select name="seller_customer_id" id="seller_customer_id" class="col-lg-3">
						{foreach $customer_info as $cusinfo}
							<option value="{$cusinfo['id_customer']|escape:'html':'UTF-8'}" {if isset($smarty.post.seller_customer_id)}{if $smarty.post.seller_customer_id == $cusinfo['id_customer']}Selected="Selected"{/if}{/if}>
								{$cusinfo['business_email']|escape:'html':'UTF-8'}
							</option>
						{/foreach}
					</select>
				{else}
					<p class="text-left">{l s='No seller found.' mod='mpshipping'}</p>
				{/if}
			</div>
		</div>
	{else}
		<input type="hidden" value="{$seller_customer_id|escape:'htmlall':'UTF-8'}" name="seller_customer_id" />
	{/if}
	<div class="form-group">
		<label class="control-label">
			{l s='Enable Shipping' mod='mpshipping'}
		</label>
		<div class="control-label">
			<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" {if isset($mpShippingActive) && $mpShippingActive == 1} checked="checked" {/if} value="1" id="mpShippingActive_on" name="mpShippingActive">
				<label for="mpShippingActive_on">{l s='Yes' mod='mpshipping'}</label>
				<input type="radio" {if isset($mpShippingActive) && $mpShippingActive == 0} checked="checked"  {/if} value="0" id="mpShippingActive_off" name="mpShippingActive">
				<label for="mpShippingActive_off">{l s='No' mod='mpshipping'}</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>
{/if}
{if isset($mp_shipping_id)}
	<input type="hidden" name="mpshipping_id" value="{$mp_shipping_id}">
{/if}
<div class="form-group">
	<label class="control-label required">{l s='Carrier Name' mod='mpshipping'}</label>
	<input type="text" name="shipping_name" id="shipping_name" class="form-control" value="{$mp_shipping_name}">
	<p class="help-block">
		{l s='Carrier name displayed during checkout' mod='mpshipping'}
	</p>
</div>
<div class="form-group">
	<label class="control-label required">{l s='Transit time' mod='mpshipping'}</label>
	<div class="row">
		{if $allow_multilang && $total_languages > 1}
		<div class="col-md-10">
		{else}
		<div class="col-md-12">
		{/if}
			{foreach from=$languages item=language}
				{assign var="transit_time" value="transit_time_`$language.id_lang`"}
				<input type="text"
				id="transit_time_{$language.id_lang}"
				name="transit_time_{$language.id_lang}"
				value="{if isset($transit_delay[$language.id_lang])}{$transit_delay[$language.id_lang]}{/if}"
				class="form-control transit_time_all {if $current_lang.id_lang == $language.id_lang}seller_default_lang_class{/if}"
				data-lang-name="{$language.name}"
				{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} maxlength="128"/>
			{/foreach}
		</div>
		{if $allow_multilang && $total_languages > 1}
		<div class="col-md-2">
			<button type="button" id="mpship_lang_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				{$current_lang.iso_code}
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				{foreach from=$languages item=language}
					<li>
						<a class="lang_presta" href="javascript:void(0)" onclick="showShippingLangField('{$language.iso_code}', {$language.id_lang});">{$language.name}</a>
					</li>
				{/foreach}
			</ul>
			<input type="hidden" name="multi_lang" id="multi_lang" value="{$multi_lang}">
			{* <input type="hidden" name="seller_default_lang" value="{$seller_default_lang}" id="seller_default_lang"> *}
			<input type="hidden" name="current_lang_id" value="{$current_lang.id_lang}" id="current_lang_id">
		</div>
		{/if}
	</div>
	<p class="help-block">
		{l s='Estimated delivery time will be displayed during checkout.' mod='mpshipping'}
	</p>
</div>
<div class="form-group">
	<label class="control-label">{l s='Speed grade' mod='mpshipping'}</label>
	<input type="text" name="grade" id="grade" class="form-control" value="{$grade}">
	<p class="help-block">
		{l s='Enter "0" for a longest shipping delay, or "9" for the shortest shipping delay.' mod='mpshipping'}
	</p>
</div>

<div class="form-group">
	<label class="control-label">{l s='Logo' mod='mpshipping'}</label>
	<input type="file" name="shipping_logo" id="shipping_logo"/>
	<p class="help-block">
		{l s='Image size should not exceed 125*125' mod='mpshipping'}
	</p>
	<img style="display:none;" id="testImg" src="#" alt="" />
</div>
<div class="form-group">
	<label class="control-label">{l s='Tracking URL' mod='mpshipping'}</label>
	<input type="text" name="tracking_url" id="tracking_url" class="form-control" value="{$tracking_url}">
	<p class="help-block">
		{l s='Delivery tracking URL: Type @ where the tracking number should appear. It will then be automatically replaced by the tracking number.' mod='mpshipping'}
	</p>
</div>