{*
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{block name='content'}
	{if isset($smarty.get.created_conf)}
		<p class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Booking price rule successfully created.' mod='mpbooking'}
		</p>
	{else if isset($smarty.get.edited_conf)}
		<p class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Booking price rule successfully updated.' mod='mpbooking'}
		</p>
	{/if}
	{if $logged}
		<div class="wk-mp-block">
			{hook h="displayMpMenu"}
			<div class="wk-mp-content">
				<div class="page-title" style="background-color:{$title_bg_color};">
					<span style="color:{$title_text_color};">{l s='Booking Price Rule' mod='mpbooking'}</span>
				</div>
				<form action="{$form_action}" method="post">
					<div class="wk-mp-right-column">
						{block name='change-product-language'}
							{include file='module:marketplace/views/templates/front/product/_partials/change-product-language.tpl'}
						{/block}
						<input type="hidden" value="{$id_seller}" name="id_seller" id="id_seller"/>
						<input type="hidden" name="token" id="wk-static-token" value="{$static_token}">
						<input type="hidden" name="default_lang" value="{$default_lang}" id="default_lang">
						<input type="hidden" name="current_lang_id" value="{$current_lang.id_lang}" id="current_lang_id">
						{if isset($edit)}
							<input type="hidden" value="{$featurePriceInfo->id}" name="id_feature_price_rule" />
						{/if}
						<div class="form-group row">
							<div class="col-md-6">
								<label for="mp_booking_product_name" class="control-label required">
									{l s='Booking Product Name' mod='mpbooking'}
									<div class="wk_tooltip">
										<span class="wk_tooltiptext">{l s='Search booking product name and select the product for which you want to create booking price rule.' mod='mpbooking'}</span>
									</div>
								</label>
								<input type="text" id="mp_booking_product_name" name="mp_booking_product_name" class="form-control" placeholder= "{l s='Enter Booking Product Name' mod='mpbooking'}" {if isset($productName)}value="{$productName}" readonly{/if} autocomplete="off"/>
								<input type="hidden" id="id_booking_product_info" name="id_booking_product_info" class="form-control" value="{if isset($featurePriceInfo->id_booking_product_info)}{$featurePriceInfo->id_booking_product_info}{else}0{/if}"/>
								<div class="dropdown">
									<ul class="mp_booking_product_search_results_ul"></ul>
								</div>
								<p class="plan_error_block">{l s='No match found for this search. Please try with an existing name.' mod='mpbooking'}</p>
							</div>
							<div class="col-md-6">
								<label for="feature_price_name" class="control-label required">
									{l s='Booking Price Rule Name' mod='mpbooking'}
									{block name='mp-form-fields-flag'}
										{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
									{/block}
								</label>
								{foreach from=$languages item=language}
									{assign var="feature_price_name" value="feature_price_name_`$language.id_lang`"}
									<input type="text"
									id="feature_price_name_{$language.id_lang}"
									name="feature_price_name_{$language.id_lang}"
									value="{if isset($smarty.post.$feature_price_name)}{$smarty.post.$feature_price_name}{else if isset($featurePriceInfo->feature_price_name)}{$featurePriceInfo->feature_price_name[{$language.id_lang}]}{/if}" class="form-control feature_price_name_all wk_text_field_all wk_text_field_{$language.id_lang} {if $default_lang == $language.id_lang}seller_default_lang_class{/if} {if $current_lang.id_lang != $language.id_lang}wk_display_none{/if}"
									data-lang-name="{$language.name}"
									maxlength="128" autocomplete="off"/>
								{/foreach}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-6">
								<label for="date_selection_type" class="control-label required">
									{l s='Date Selection type' mod='mpbooking'}
								</label>
								<select class="form-control" name="date_selection_type" id="date_selection_type">
									<option value="1" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 1}selected = "selected"{/if}>
										{l s='Date Range' mod='mpbooking'}
									</option>
									<option value="2" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 2}selected = "selected"{/if}>
										{l s='Specific Date' mod='mpbooking'}
									</option>
								</select>
							</div>
							<div class="col-md-6 specific_date_type_block" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 2}style="display:block;"{else}style="display:none;"{/if}>
								<label for="specific_date_type" class="control-label required">
									{l s='Specific Date' mod='mpbooking'}
								</label>
								<input type="text" id="specific_date" name="specific_date" class="form-control datepicker-input" value="{if isset($featurePriceInfo->date_from) && $featurePriceInfo->date_selection_type == 2}{$featurePriceInfo->date_from|date_format:'%d-%m-%Y'}{else}{$date_from|date_format:'%d-%m-%Y'}{/if}" readonly/>
							</div>
						</div>
						<div class="form-group row feature_plan_date_range_block" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 2}style="display:none;"{/if}>
							<div class="col-md-6">
								<label for="feature_plan_date_from" class="control-label required">
									{l s='Date From' mod='mpbooking'}
								</label>
								<input type="text" id="feature_plan_date_from" name="date_from" class="form-control datepicker-input" value="{if isset($featurePriceInfo->date_from) && $featurePriceInfo->date_selection_type == 1}{$featurePriceInfo->date_from|date_format:'%d-%m-%Y'}{else}{$date_from|date_format:'%d-%m-%Y'}{/if}" readonly/>
							</div>
							<div class="col-md-6">
								<label for="feature_plan_date_to" class="control-label required">
									{l s='Date To' mod='mpbooking'}
								</label>
								<input type="text" id="feature_plan_date_to" name="date_to" class="form-control datepicker-input" value="{if isset($featurePriceInfo->date_to) && $featurePriceInfo->date_selection_type == 1}{$featurePriceInfo->date_to|date_format:'%d-%m-%Y'}{else}{$date_to|date_format:'%d-%m-%Y'}{/if}" readonly/>
							</div>
						</div>
						<div class="form-group row special_days_exists_block" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 2}style="display:none;"{/if}>
							<div class="col-md-12">
								<label class="control-label">
									{l s='Plan For Special Days' mod='mpbooking'}
									<div class="wk_tooltip">
										<span class="wk_tooltiptext">{l s='If you want to create this booking price rule only for some special days of the week of selected date range then you can select select days after checking this option. Otherwise rule will be created for whole selected date range.' mod='mpbooking'}</span>
									</div>
								</label>
								<div class="row">
									<div class="col-sm-4">
										<p class="checkbox">
											<label>
												<input class="is_special_days_exists pull-left" type="checkbox" name="is_special_days_exists"
												{if (isset($smarty.post.is_special_days_exists) && $smarty.post.is_special_days_exists)
													|| (isset($featurePriceInfo->is_special_days_exists) && $featurePriceInfo->is_special_days_exists)}
													checked="checked"
												{/if}/>
												{l s='Check to select special days' mod='mpbooking'}
											</label>
										</p>
									</div>
									<div class="col-sm-8 week_days"
									{if (isset($smarty.post.is_special_days_exists) && $smarty.post.is_special_days_exists) 	|| (isset($featurePriceInfo->is_special_days_exists) && $featurePriceInfo->is_special_days_exists)}
										style="display:block;"
									{/if}>
										<div class="col-sm-1">
											<input type="checkbox" name="special_days[]" value="mon"
											{if (isset($smarty.post.special_days) && in_array('mon', $smarty.post.special_days))
												|| (isset($special_days) && $special_days && in_array('mon', $special_days))}
												checked="checked"
											{/if}/>
											<p>{l s='mon' mod='mpbooking'}</p>
										</div>
										<div class="col-sm-1">
											<input type="checkbox" name="special_days[]" value="tue"
											{if (isset($smarty.post.special_days) && in_array('tue', $smarty.post.special_days))
												|| (isset($special_days) && $special_days && in_array('tue', $special_days))}
												checked="checked"
											{/if}/>
											<p>{l s='tue' mod='mpbooking'}</p>
										</div>
										<div class="col-sm-1">
											<input type="checkbox" name="special_days[]" value="wed"
											{if (isset($smarty.post.special_days) && in_array('wed', $smarty.post.special_days))
												|| (isset($special_days) && $special_days && in_array('wed', $special_days))}
												checked="checked"
											{/if}/>
											<p>{l s='wed' mod='mpbooking'}</p>
										</div>
										<div class="col-sm-1">
											<input type="checkbox" name="special_days[]" value="thu"
											{if (isset($smarty.post.special_days) && in_array('thu', $smarty.post.special_days))
												|| (isset($special_days) && $special_days && in_array('thu', $special_days))}
												checked="checked"
											{/if}/>
											<p>{l s='thu' mod='mpbooking'}</p>
										</div>
										<div class="col-sm-1">
											<input type="checkbox" name="special_days[]" value="fri"
											{if (isset($smarty.post.special_days) && in_array('fri', $smarty.post.special_days))
												|| (isset($special_days) && $special_days && in_array('fri', $special_days))}
												checked="checked"
											{/if}/>
											<p>{l s='fri' mod='mpbooking'}</p>
										</div>
										<div class="col-sm-1">
											<input type="checkbox" name="special_days[]" value="sat"
											{if (isset($smarty.post.special_days) && in_array('sat', $smarty.post.special_days))
												|| (isset($special_days) && $special_days && in_array('sat', $special_days))}
												checked="checked"
											{/if}/>
											<p>{l s='sat' mod='mpbooking'}</p>
										</div>
										<div class="col-sm-1">
											<input type="checkbox" name="special_days[]" value="sun"
											{if (isset($smarty.post.special_days) && in_array('sun', $smarty.post.special_days))
												|| (isset($special_days) && $special_days && in_array('sun', $special_days))}
												checked="checked"
											{/if}/>
											<p>{l s='sun' mod='mpbooking'}</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-6">
								<label for="price_impact_way" class="control-label required">
									{l s='Impact Way' mod='mpbooking'}
								</label>
								<select class="form-control" name="price_impact_way" id="price_impact_way">
									<option value="1" {if isset($featurePriceInfo->impact_way) && $featurePriceInfo->impact_way == 1}selected = "selected"{/if}>
									{l s='Decrease Price' mod='mpbooking'}
									</option>
									<option value="2" {if isset($featurePriceInfo->impact_way) && $featurePriceInfo->impact_way == 2}selected = "selected"{/if}>
									{l s='Increase Price' mod='mpbooking'}
									</option>
								</select>
							</div>
							<div class="col-md-6">
								<label for="price_impact_type" class="control-label required">
									{l s='Impact Type' mod='mpbooking'}
								</label>
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
						<div class="form-group row">
							<div class="col-md-6">
								<label class="control-label required" for="feature_price_name" >
									{l s='Impact Value' mod='mpbooking'}({l s='tax excl.' mod='mpbooking'})
								</label>
								<div class="input-group">
									<span class="input-group-addon payment_type_icon">{if isset($edit)} {if $featurePriceInfo->impact_type==2}{$defaultCurrencySign}{else}%{/if}{else}%{/if}</span>
									<input type="text" id="impact_value" name="impact_value" value="{if isset($smarty.post.impact_value)}{$smarty.post.impact_value}{elseif isset($featurePriceInfo->impact_value)}{$featurePriceInfo->impact_value}{/if}" class="form-control">
								</div>
							</div>
						</div>
						{block name='mp-form-fields-notification'}
							{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-notification.tpl'}
						{/block}
					</div>
					<div class="wk-mp-right-column wk_border_top_none">
						<div class="form-group row">
							<div class="col-xs-4 col-sm-4 col-md-6">
								<a href="{url entity='module' name='mpbooking' controller='mpfeaturepriceplanslist'}" class="btn wk_btn_cancel wk_btn_extra">
									{l s='Cancel' mod='mpbooking'}
								</a>
							</div>
							<div class="col-xs-8 col-sm-8 col-md-6 wk_text_right" id="wk-feature-plan-submit" data-action="{l s='Save' mod='mpbooking'}">
								<button type="submit" id="StayFeaturePricePlan" name="StayFeaturePricePlan" class="btn btn-success wk_btn_extra form-control-submit">
									<span>{l s='Save & Stay' mod='mpbooking'}</span>
								</button>
								<button type="submit" id="SubmitFeaturePricePlan" name="SubmitFeaturePricePlan" class="btn btn-success wk_btn_extra form-control-submit">
									<span>{l s='Save' mod='mpbooking'}</span>
								</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	{/if}
{/block}