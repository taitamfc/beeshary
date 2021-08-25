{*
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="panel">
	<div class="panel-heading">
		{if isset($edit)}
			<i class='icon-pencil'></i>&nbsp{l s='Edit Booking Price Rule' mod='mpbooking'}
		{else}
			<i class='icon-plus'></i>&nbsp{l s='Add New Booking Price Rule' mod='mpbooking'}
		{/if}
	</div>
	<form id="{$table}_form" class="sellerBookingRulesForm defaultForm form-horizontal" action="{$current}&{if !empty($submit_action)}{$submit_action}{/if}&token={$token}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style}"{/if}>
		{if isset($edit)}
			<input type="hidden" value="{$featurePriceInfo->id|escape:'htmlall':'UTF-8'}" name="id_feature_price_rule" />
		{/if}
		{if !isset($edit)}
			<div class="form-group">
				<label class="col-sm-3 control-label pull-left required">
					{l s='Choose Seller' mod='mpbooking'}&nbsp;
				</label>
				{if isset($customer_info)}
					<select name="id_seller" id="wk_shop_seller" class="fixed-width-xl pull-left">
						{foreach $customer_info as $custinfo}
							<option value="{$custinfo['id_seller']|escape:'htmlall':'UTF-8'}">
								{$custinfo['business_email']|escape:'htmlall':'UTF-8'}
							</option>
						{/foreach}
					</select>
				{else}
					<p>{l s='No seller found.' mod='mpbooking'}</p>
				{/if}
			</div>
		{/if}
		{if isset($sellerInfoObj->id) && $sellerInfoObj->id}
			<div class="form-group">
				<label class="col-sm-3 control-label required" for="seller_email" >
					{l s='Seller Email :' mod='mpbooking'}
				</label>
				<div class="col-sm-3">
					<p class="form-control-static">{$sellerInfoObj->business_email|escape:'htmlall':'UTF-8'}</p>
					<input type="hidden" id="wk_shop_seller" name="id_seller" value="{$sellerInfoObj->id|escape:'htmlall':'UTF-8'}">
				</div>
			</div>
		{/if}
		<div class="form-group">
			<label class="col-sm-3 control-label required" for="feature_price_name" >
				{l s='Booking Product Name :' mod='mpbooking'}
			</label>
			<div class="col-sm-3">
				<input type="text" id="booking_product_name" name="booking_product_name" class="form-control" placeholder= "{l s='Enter Booking Product Name' mod='mpbooking'}"
				value="{if isset($productName)}{$productName|escape:'htmlall':'UTF-8'}{/if}"/>
				<input type="hidden" id="id_booking_product_info" name="id_booking_product_info" class="form-control" value="{if isset($featurePriceInfo->id_booking_product_info)}{$featurePriceInfo->id_booking_product_info|escape:'htmlall':'UTF-8'}{else}0{/if}"/>
				<div class="dropdown">
	                <ul class="booking_product_search_results_ul"></ul>
	            </div>
				<p class="error-block" style="display:none; color: #CD5D5D;">{l s='No match found for this search. Please try with an existing name.' mod='mpbooking'}</p>
			</div>
			<div class="col-sm-offset-3 col-sm-9 help-block">
				**{l s='Enter booking product name and select the product for which you are going to create this price plan.' mod='mpbooking'}
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label required" for="feature_price_name" >
				{l s='Booking Price Rule Name :' mod='mpbooking'}
			</label>
			<div class="col-lg-3">
				{foreach from=$languages item=language}
					{assign var="feature_price_name" value="feature_price_name_`$language.id_lang`"}
					<input type="text" id="{$feature_price_name|escape:'htmlall':'UTF-8'}" name="{$feature_price_name|escape:'htmlall':'UTF-8'}" value="{if isset($featurePriceInfo->feature_price_name[$language.id_lang]) && $featurePriceInfo->feature_price_name[$language.id_lang]}{$featurePriceInfo->feature_price_name[$language.id_lang]|escape:'htmlall':'UTF-8'}{else if isset($smarty.post.$feature_price_name)}{$smarty.post.$feature_price_name|escape:'htmlall':'UTF-8'}{/if}" data-lang-name="{$language.name}" placeholder="{l s='Enter Booking Price Rule Name' mod='mpbooking'}" class="form-control feature_price_name_all" {if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if}/>
				{/foreach}
			</div>
			{if $languages|@count > 1}
				<div class="col-lg-2">
					<button type="button" id="feature_price_rule_lang_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						{$currentLang.iso_code|escape:'htmlall':'UTF-8'}
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						{foreach from=$languages item=language}
							<li>
								<a href="javascript:void(0)" onclick="showBookingPriceRuleLangField('{$language.iso_code|escape:'htmlall':'UTF-8'}', {$language.id_lang|escape:'htmlall':'UTF-8'});">{$language.name|escape:'htmlall':'UTF-8'}</a>
							</li>
						{/foreach}
					</ul>
				</div>
			{/if}
		</div>

		<div class="form-group">
            <label for="date_selection_type" class="control-label col-lg-3 required">
              {l s='Date Selection type :' mod='mpbooking'}
            </label>
            <div class="col-lg-3">
				<select class="form-control" name="date_selection_type" id="date_selection_type">
					<option value="1" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 1}selected = "selected"{/if}>
					  {l s='Date Range' mod='mpbooking'}
					</option>
					<option value="2" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 2}selected = "selected"{/if}>
					  {l s='Specific Date' mod='mpbooking'}
					</option>
				</select>
			</div>
		</div>

		<div class="form-group specific_date_type" {if isset($edit) && $edit}{if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type != 2}style="display:none;"{/if}{else}style="display:none;"{/if}>
			<label class="col-sm-3 control-label required" for="specific_date" >
				{l s='Specific Date' mod='mpbooking'}
			</label>
			<div class="col-sm-3">
				<input type="text" id="specific_date" name="specific_date" class="form-control datepicker-input" value="{if isset($featurePriceInfo->date_from)}{$featurePriceInfo->date_from}{else}{$date_from}{/if}" readonly/>
			</div>
		</div>

		<div class="form-group date_range_type" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 2}style="display:none;"{/if}>
			<label class="col-sm-3 control-label required" for="date_form" >
				{l s='Date From' mod='mpbooking'}
			</label>
			<div class="col-sm-3">
				<input type="text" id="feature_plan_date_from" name="date_from" class="form-control datepicker-input" value="{if isset($featurePriceInfo->date_from)}{$featurePriceInfo->date_from|date_format:'%d-%m-%Y'}{else}{$date_from|date_format:'%d-%m-%Y'}{/if}" readonly/>
			</div>
		</div>
		<div class="form-group date_range_type" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 2}style="display:none;"{/if}>
			<label class="col-sm-3 control-label required" for="date_to" >
				{l s='Date To' mod='mpbooking'}
			</label>
			<div class="col-sm-3">
				<input type="text" id="feature_plan_date_to" name="date_to" class="form-control datepicker-input" value="{if isset($featurePriceInfo->date_to)}{$featurePriceInfo->date_to|date_format:'%d-%m-%Y'}{else}{$date_to|date_format:'%d-%m-%Y'}{/if}" readonly/>
			</div>
		</div>
		<div class="form-group special_days_content" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 2}style="display:none;"{/if}>
			<label class="control-label col-lg-3">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='If you want to create this booking price rule only for some special days of the week of selected date range then you can select select days after checking this option. Otherwise rule will be created for whole selected date range.' mod='mpbooking'}">
					{l s='For Special Days' mod='mpbooking'}
				</span>
			</label>
			<div class="col-sm-2">
				<p class="checkbox">
					<label>
						<input class="is_special_days_exists pull-left" type="checkbox" name="is_special_days_exists" {if isset($featurePriceInfo->is_special_days_exists) && $featurePriceInfo->is_special_days_exists}checked="checked"{/if}/>
						{l s='Check to select special days' mod='mpbooking'}
					</label>
				</p>
			</div>
			<div class="col-sm-7 week_days" {if isset($featurePriceInfo->is_special_days_exists) && $featurePriceInfo->is_special_days_exists}style="display:block;"{/if}>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="mon" {if isset($special_days) && $special_days && in_array('mon', $special_days)}checked="checked"{/if}/>
					<p>{l s='Mon' mod='mpbooking'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="tue" {if isset($special_days) && $special_days && in_array('tue', $special_days)}checked="checked"{/if}/>
					<p>{l s='Tue' mod='mpbooking'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="wed" {if isset($special_days) && $special_days && in_array('wed', $special_days)}checked="checked"{/if}/>
					<p>{l s='Wed' mod='mpbooking'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="thu" {if isset($special_days) && $special_days && in_array('thu', $special_days)}checked="checked"{/if}/>
					<p>{l s='Thu' mod='mpbooking'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="fri" {if isset($special_days) && $special_days && in_array('fri', $special_days)}checked="checked"{/if}/>
					<p>{l s='Fri' mod='mpbooking'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="sat" {if isset($special_days) && $special_days && in_array('sat', $special_days)}checked="checked"{/if}/>
					<p>{l s='Sat' mod='mpbooking'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="sun" {if isset($special_days) && $special_days && in_array('sun', $special_days)}checked="checked"{/if}/>
					<p>{l s='Sun' mod='mpbooking'}</p>
				</div>
			</div>
		</div>

		<div class="form-group">
            <label for="Price Impact Way" class="control-label col-lg-3">
              {l s='Impact Way :' mod='mpbooking'}
            </label>
            <div class="col-lg-3">
				<select class="form-control" name="price_impact_way" id="price_impact_way">
					<option value="1" {if isset($featurePriceInfo->impact_way) && $featurePriceInfo->impact_way == 1}selected = "selected"{/if}>
					  {l s='Decrease Price' mod='mpbooking'}
					</option>
					<option value="2" {if isset($featurePriceInfo->impact_way) && $featurePriceInfo->impact_way == 2}selected = "selected"{/if}>
					  {l s='Increase Price' mod='mpbooking'}
					</option>
				</select>
			</div>
		</div>

		<div class="form-group">
            <label for="Price Impact Type" class="control-label col-lg-3 required">
              {l s='Impact Type :' mod='mpbooking'}
            </label>
            <div class="col-lg-3">
				<select class="form-control" name="price_impact_type" id="price_impact_type">
					<option value="1" {if isset($featurePriceInfo->impact_type) && $featurePriceInfo->impact_type == 1}selected = "selected"{/if}>
					  {l s='Percentage' mod='mpbooking'}
					</option>
					<option value="2" {if isset($featurePriceInfo->impact_type) && $featurePriceInfo->impact_type == 2}selected = "selected"{/if}>
					  {l s='Fixed Price' mod='mpbooking'}
					</option>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label required" for="feature_price_name" >
				{l s='Impact Value' mod='mpbooking'}({l s='tax excl.' mod='mpbooking'})
			</label>
			<div class="col-lg-3">
				<div class="input-group">
					<span class="input-group-addon payment_type_icon">{if isset($edit)} {if $featurePriceInfo->impact_type==2}{$defaultcurrency_sign}{else}%{/if}{else}%{/if}</span>
					<input type="text" id="impact_value" name="impact_value"
					value="{if isset($smarty.post.impact_value) && $smarty.post.impact_value}{$smarty.post.impact_value}{elseif isset($featurePriceInfo->impact_value)}{$featurePriceInfo->impact_value}{/if}"/>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-lg-3 required">
				<span>
					{l s='Enable Booking Price Rule' mod='mpbooking'}
				</span>
			</label>
			<div class="col-lg-9 ">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" {if isset($edit) && $featurePriceInfo->active==1} checked="checked" {else}checked="checked"{/if} value="1" id="enable_feature_price_on" name="enable_feature_price">
					<label for="enable_feature_price_on">{l s='Yes' mod='mpbooking'}</label>
					<input {if isset($edit) && $featurePriceInfo->active==0} checked="checked" {/if} type="radio" value="0" id="enable_feature_price_off" name="enable_feature_price">
					<label for="enable_feature_price_off">{l s='No' mod='mpbooking'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>

		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminHotelFeaturePricesSettings')|escape:'html':'UTF-8'}" class="btn btn-default">
				<i class="process-icon-cancel"></i>{l s='Cancel' mod='mpbooking'}
			</a>
			<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save' mod='mpbooking'}
			</button>
			<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}AndStay" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save and stay' mod='mpbooking'}
			</button>
		</div>
	</form>
</div>

{strip}
	{addJsDef booking_product_price_plans_url = $link->getAdminLink('AdminSellerBookingProductPricePlansSettings')}
	{addJsDef defaultcurrency_sign = $defaultcurrency_sign mod='mpbooking'}
{/strip}