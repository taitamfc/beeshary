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
			{l s='Created Successfully' mod='mpbooking'}
		</p>
	{else if isset($smarty.get.edited_conf)}
		<p class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Updated Successfully' mod='mpbooking'}
		</p>
	{/if}
	{if isset($smarty.get.deleted)}
		<p class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Deleted Successfully' mod='mpbooking'}
		</p>
	{/if}
	{if isset($smarty.get.status_updated)}
		<p class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Status updated Successfully' mod='mpbooking'}
		</p>
	{else if isset($smarty.get.edited_withdeactive)}
		<p class="alert alert-info">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Product has been updated successfully but it has been deactivated. Please wait till the approval from admin.' mod='mpbooking'}
		</p>
	{/if}
	<script type="text/javascript" src="{$smarty.const._MODULE_DIR_}marketplace/views/js/tinymce/tinymce.min.js"></script>
	<script type="text/javascript" src="{$smarty.const._MODULE_DIR_}marketplace/views/js/tinymce/tinymce_wk_setup.js"></script>
	{if $logged}
		{if isset($id_mp_product) && $id_mp_product}
			{hook h='displayBkMpUpdateProductHeader'}
		{else}
			{hook h='displayBkMpAddProductHeader'}
		{/if}
		<div class="wk-mp-block">
			{hook h="displayMpMenu"}
			<div class="wk-mp-content">
				<div class="page-title" style="background-color:{$title_bg_color};">
					<span style="color:{$title_text_color};">{if isset($id_mp_product) && $id_mp_product}{l s='Update Booking Product' mod='mpbooking'}{else}{l s='Add Booking Product' mod='mpbooking'}{/if}</span>
				</div>
				<form id="wk_mp_seller_booking_product_form" action="{if isset($id_mp_product) && $id_mp_product}{url entity='module' name='mpbooking' controller='mpbookingproduct' params=['id_mp_product' => $id_mp_product]}{else}{url entity='module' name='mpbooking' controller='mpbookingproduct'}{/if}" method="post">
					<div class="wk-mp-right-column">
						{hook h='displayBkMpUpdateProductFormHeader'}
						{if isset($id_mp_product) && $id_mp_product}
							<div class="wk_product_list">
								<p class="wk_text_right">
									<a title="{l s='Add Booking product' mod='mpbooking'}" href="{url entity='module' name='mpbooking' controller='mpbookingproduct'}" {if $allow_multilang && $total_languages > 1}class="pull-right"{/if}>
										<button class="btn btn-primary btn-sm" type="button">
											<i class="material-icons">&#xE145;</i>
											{l s='Add Booking Product' mod='mpbooking'}
										</button>
									</a>
								</p>
							</div>
						{/if}
						<input type="hidden" name="token" id="wk-static-token" value="{$static_token}">
						<input type="hidden" name="default_lang" value="{$default_lang}" id="default_lang">
						<input type="hidden" name="current_lang_id" value="{$current_lang.id_lang}" id="current_lang_id">
						<input type="hidden" name="active_tab" value="{if isset($active_tab)}{$active_tab}{/if}" id="active_tab">
						<input type="hidden" name="id_mp_product" id="mp_product_id" value="{if isset($id_mp_product) && $id_mp_product}{$id_mp_product}{/if}">
						<input type="hidden" name="id_booking_product_info" id="id_booking_product_info" value="{if isset($idBookingProductInfo) && $idBookingProductInfo}{$idBookingProductInfo}{/if}">

						{block name='change-product-language'}
							{include file='module:marketplace/views/templates/front/product/_partials/change-product-language.tpl'}
						{/block}
						<div class="alert alert-danger wk_display_none" id="wk_mp_form_error"></div>
						<hr>
						<div class="tabs wk-tabs-panel">
							<ul class="nav nav-tabs">
								<li class="nav-item">
									<a class="nav-link active" href="#wk-information" data-toggle="tab">
										<i class="material-icons">&#xE88E;</i>
										{l s='Information' mod='mpbooking'}
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="#wk-images" data-toggle="tab">
										<i class="material-icons">&#xE410;</i>
										{l s='Images' mod='mpbooking'}
									</a>
								</li>
								{if isset($product_info) && $product_info}
									{if isset($product_info.booking_type) && $product_info.booking_type==$booking_type_time_slot}
										<li class="nav-item">
											<a class="nav-link" href="#booking-configuration" data-toggle="tab">
												<i class="material-icons">&#xE192;</i>
												{l s='Time Slots Booking Plans' mod='mpbooking'}
											</a>
										</li>
									{/if}
									<li class="nav-item">
										<a class="nav-link" href="#booking-disable-dates" data-toggle="tab">
											<i class="material-icons">&#xE033;</i>
											{l s='Disable Dates/Slots' mod='mpbooking'}
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="#booking-availability-info" data-toggle="tab">
											<i class="material-icons">&#xE8A3;</i>
											{l s='Availability & Rates' mod='mpbooking'}
										</a>
									</li>
								{/if}
								{hook h='displayBkMpProductNavTab'}
							</ul>
							<div class="tab-content" id="tab-content">
								<div class="tab-pane fade in active" id="wk-information">
									{if isset($id_mp_product) && $id_mp_product}
										{hook h='displayBkMpupdateProductContentTop'}
									{else}
										{hook h='displayBkMpAddProductContentTop'}
									{/if}

									<div class="form-group">
										<label for="product_name" class="control-label required">
											{l s='Product Name' mod='mpbooking'}
											{block name='mp-form-fields-flag'}
												{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
											{/block}
										</label>
										{foreach from=$languages item=language}
											{assign var="product_name" value="product_name_`$language.id_lang`"}
											<input type="text"
											id="product_name_{$language.id_lang}"
											name="product_name_{$language.id_lang}"
											value="{if isset($smarty.post.$product_name)}{$smarty.post.$product_name}{else if isset($product_info)}{$product_info.product_name[{$language.id_lang}]}{/if}"
											class="form-control product_name_all wk_text_field_all wk_text_field_{$language.id_lang} {if $default_lang == $language.id_lang}seller_default_lang_class{/if}
											{if $current_lang.id_lang != $language.id_lang}wk_display_none{/if}"
											data-lang-name="{$language.name}"
											maxlength="128" />
										{/foreach}
										<span class="wk-msg-productname"></span>
									</div>
									{if isset($id_mp_product) && $id_mp_product}
										{hook h='displayBkMpUpdateProductNameBottom'}
									{else}
										{hook h='displayBkMpAddProductNameBottom'}
									{/if}
									<div class="form-group">
										<label for="prod_short_desc" class="control-label">
											{l s='Short Description' mod='mpbooking'}
											{block name='mp-form-fields-flag'}
												{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
											{/block}
										</label>
										{foreach from=$languages item=language}
											{assign var="short_description" value="short_description_`$language.id_lang`"}
											<div id="short_desc_div_{$language.id_lang}" class="wk_text_field_all wk_text_field_{$language.id_lang} {if $current_lang.id_lang != $language.id_lang}wk_display_none{/if}">
												<textarea
												name="short_description_{$language.id_lang}"
												id="short_description_{$language.id_lang}" cols="2" rows="3"
												class="wk_tinymce form-control">{if isset($smarty.post.$short_description)}{$smarty.post.$short_description}{else if isset($product_info.short_description)}{$product_info.short_description[{$language.id_lang}]}{/if}</textarea>
											</div>
										{/foreach}
									</div>
									<div class="form-group">
										<label for="prod_desc" class="control-label">
											{l s='Description' mod='mpbooking'}
											{block name='mp-form-fields-flag'}
												{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
											{/block}
										</label>
										{foreach from=$languages item=language}
											{assign var="description" value="description_`$language.id_lang`"}
											<div id="product_desc_div_{$language.id_lang}" class="wk_text_field_all wk_text_field_{$language.id_lang} {if $current_lang.id_lang != $language.id_lang}wk_display_none{/if}">
												<textarea
												name="description_{$language.id_lang}"
												id="description_{$language.id_lang}" cols="2" rows="3"
												class="wk_tinymce form-control">{if isset($smarty.post.$description)}{$smarty.post.$description}{else if isset($product_info.description[{$language.id_lang}])}{$product_info.description[{$language.id_lang}]}{/if}</textarea>
											</div>
										{/foreach}
									</div>
									<div class="form-group row">
										<div class="col-md-6">
											<label for="condition" class="control-label">
												{l s='Condition' mod='mpbooking'}
												<div class="wk_tooltip">
													<span class="wk_tooltiptext">{l s='This option enables you to indicate the condition of the product.' mod='mpbooking'}</span>
												</div>
											</label>
											<select class="form-control form-control-select" name="condition" id="condition">
												<option value="new" {if isset($product_info.condition) && $product_info.condition == 'new'}selected{/if}>{l s='New' mod='mpbooking'}</option>
												<option value="used" {if isset($product_info.condition) && $product_info.condition == 'used'}selected{/if}>{l s='Used' mod='mpbooking'}</option>
												<option value="refurbished" {if isset($product_info.condition) && $product_info.condition == 'refurbished'}selected{/if}>{l s='Refurbished' mod='mpbooking'}</option>
											</select>
										</div>
										<div class="col-sm-6">
											<label for="booking_type" class="control-label required">
												{l s='Product Booking Type :' mod='mpbooking'}
											</label>
											<select class="form-control" name="booking_type" id="booking_type">
												<option value="1" {if isset($product_info.booking_type) && $product_info.booking_type==$booking_type_date_range}selected{/if}>
													{l s='Date Range' mod='mpbooking'}
												</option>
												<option value="2" {if isset($product_info.booking_type) && $product_info.booking_type==$booking_type_time_slot}selected{/if}>
													{l s='Time Slots' mod='mpbooking'}
												</option>
											</select>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-md-6">
											<label for="quantity" class="control-label required">
												{l s='Quantity' mod='mpbooking'}
												<div class="wk_tooltip">
													<span class="wk_tooltiptext">{l s='How many products should be available for sale?' mod='mpbooking'}</span>
												</div>
											</label>
											<input type="text"
											class="form-control"
											name="quantity"
											id="quantity"
											value="{if isset($smarty.post.quantity)}{$smarty.post.quantity}{else if isset($product_info.quantity)}{$product_info.quantity}{/if}"
											pattern="\d*"
											{if isset($hasAttribute)}readonly{/if} />
										</div>
										{if Configuration::get('WK_MP_SELLER_PRODUCT_REFERENCE')}
											<div class="col-md-6">
												<label for="reference" class="control-label">
													{l s='Reference' mod='mpbooking'}
													<div class="wk_tooltip">
														<span class="wk_tooltiptext">{l s='Your internal reference code for this product. Allowed max 32 character. Allowed special characters' mod='mpbooking'}:.-_#.</span>
													</div>
												</label>
												<input type="text"
												class="form-control"
												name="reference"
												id="reference"
												value="{if isset($smarty.post.reference)}{$smarty.post.reference}{else if isset($product_info.reference)}{$product_info.reference}{/if}"
												maxlength="32" />
											</div>
										{/if}
										<input type="hidden"
											class="form-control"
											name="minimal_quantity"
											id="minimal_quantity"
											value="{if isset($smarty.post.minimal_quantity)}{$smarty.post.minimal_quantity}{else if isset($product_info.minimal_quantity)}{$product_info.minimal_quantity}{else}1{/if}"
											pattern="\d*"
											{if isset($hasAttribute)}readonly{/if} />
									</div>
									<div class="form-group row">
										<div class="col-md-6">
											<label for="product_category" class="control-label required" >
												{l s='Category' mod='mpbooking'}
												<div class="wk_tooltip">
													<span class="wk_tooltiptext">{l s='Where should the product be available on your site? The main category is where the product appears by default: this is the category which is seen in the product page\'s URL.' mod='mpbooking'}</span>
												</div>
											</label>
											<div id="categorycontainer"></div>
											<input type="hidden" name="product_category" id="product_category" value="{if isset($catIdsJoin)}{$catIdsJoin}{/if}" />
										</div>
										<div class="col-md-6" id="default_category_div">
											<label for="default_category" class="control-label">
												{l s='Default Category' mod='mpbooking'}
											</label>
											<select class="form-control form-control-select" name="default_category" id="default_category">
												{if isset($defaultCategory)}
													{foreach $defaultCategory as $defaultCategoryVal}
														<option id="default_cat{$defaultCategoryVal.id_category}" value="{$defaultCategoryVal.id_category}" name="{$defaultCategoryVal.name}">{$defaultCategoryVal.name}</option>
													{/foreach}
												{/if}
											</select>
										</div>
									</div>
									<hr>
									<div>
										<div class="row">
											<h4 class="col-md-12">{l s='Pricing' mod='mpbooking'}</h4>
										</div>
										<div class="form-group row">
											<div class="col-md-6 ">
												<label for="price" class="control-label required">
													{l s='Price (tax excl.)' mod='mpbooking'}

													<div class="wk_tooltip">
														<span class="wk_tooltiptext">{l s='This is the retail price at which you intend to sell this product to your customers.' mod='mpbooking'}</span>
													</div>
												</label>
												<div class="input-group">
													<span class="input-group-addon">{$defaultCurrencySign}</span>
													<input type="text"
													id="price"
													name="price"
													value="{if isset($smarty.post.price)}{$smarty.post.price}{else if isset($product_info)}{$product_info.price}{else}0.000000{/if}"
													class="form-control"
													data-action="input_excl"
													pattern="\d+(\.\d+)?"
													autocomplete="off"
													placeholder="{l s='Enter Product Base Price' mod='mpbooking'}" />
													<div class="input-group-addon">/
														<span class="booking_price_period">
															{if isset($product_info.booking_type) && $product_info.booking_type==$booking_type_date_range}
																{l s='day' mod='mpbooking'}
															{else if isset($product_info.booking_type) && $product_info.booking_type==$booking_type_time_slot}
																{l s='slot' mod='mpbooking'}
															{else}
																{l s='day' mod='mpbooking'}
															{/if}
														</span>
													</div>
												</div>
											</div>
											<!-- Product Tax Rule  -->
											{if isset($mp_seller_applied_tax_rule) && $mp_seller_applied_tax_rule && isset($tax_rules_groups)}
												<div class="form-group ">
													<label for="id_tax_rules_group" class="control-label">
														{l s='Tax Rate' mod='mpbooking'}
													</label>
													<div class="row">
														<div class="col-md-6">
															<select name="id_tax_rules_group" id="id_tax_rules_group" class="form-control form-control-select" data-action="input_excl">
																<option value="0">{l s='No tax' mod='mpbooking'}</option>
																{foreach $tax_rules_groups as $tax_rule}
																	<option value="{$tax_rule.id_tax_rules_group}"
																	{if isset($id_tax_rules_group)}{if $id_tax_rules_group == $tax_rule.id_tax_rules_group} selected="selected"{/if}{else}{if $defaultTaxRuleGroup == $tax_rule.id_tax_rules_group} selected="selected" {/if}{/if}>
																		{$tax_rule.name}
																	</option>
																{/foreach}
															</select>
														</div>
													</div>
												</div>
											{else}
												<input type="hidden"
												name="id_tax_rules_group"
												id="id_tax_rules_group"
												value="{if isset($id_tax_rules_group)}{$id_tax_rules_group}{else}1{/if}" >
											{/if}
										</div>
									</div>
									{if isset($id_mp_product) && $id_mp_product}
										{hook h='displayBkMpupdateProductFooter'}
									{else}
										{hook h="displayBkMpAddProductFooter"}
									{/if}
								</div>
								<div class="tab-pane fade in" id="wk-images">
									{if isset($id_mp_product) && $id_mp_product}
										<div class="tab-pane fade in" id="wk-images">
											{block name='updatebookingproduct_images'}
												{include file='module:marketplace/views/templates/front/product/_partials/updateproduct-images.tpl'}
											{/block}
										</div>
									{else}
										<div class="alert alert-danger">
											{l s='You must save this product before adding images.' mod='mpbooking'}
										</div>
									{/if}
								</div>
								{if isset($product_info) && $product_info}
									{* Tab For time slots *}
									{if isset($product_info.booking_type) && $product_info.booking_type==$booking_type_time_slot}
										<div class="tab-pane fade in" id="booking-configuration">
											{include file='module:mpbooking/views/templates/front/_partials/booking_time_slots_conf.tpl'}
										</div>
									{/if}
									{* Tab For disable dates *}
									<div class="tab-pane fade in" id="booking-disable-dates">
										{include file='module:mpbooking/views/templates/front/_partials/booking_disable_dates_info.tpl'}
									</div>
									{* Tab For availability & rates information *}
									<div class="tab-pane fade in" id="booking-availability-info">
										{include file='module:mpbooking/views/templates/front/_partials/availablity_rates_info.tpl'}
									</div>
								{/if}
								{hook h='displayBkMpProductTabContent'}
							</div>
							{block name='mp-form-fields-notification'}
								{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-notification.tpl'}
							{/block}
							<div class="wk-mp-right-column wk_border_top_none">
								<div class="form-group row">
									<div class="col-xs-4 col-sm-4 col-md-6">
										<a href="{url entity='module' name='mpbooking' controller='mpbookingproductslist'}" class="btn wk_btn_cancel wk_btn_extra">
											{l s='Retour aux activités' mod='mpbooking'}
										</a>
									</div>
									<div class="col-xs-8 col-sm-8 col-md-6 wk_text_right" id="wk-mp-booking-product-submit" data-action="{l s='Save' mod='mpbooking'}">
										<img class="wk_product_loader" src="{$module_dir}marketplace/views/img/loader.gif" width="25" />
										<button type="submit" id="StayMpBookingProduct" name="StayMpBookingProduct" class="btn btn-success wk_btn_extra form-control-submit submitBookingProduct">
											<span>{l s='Save & Stay' mod='mpbooking'}</span>
										</button>
										<button type="submit" id="SubmitMpBookingProduct" name="SubmitMpBookingProduct" class="btn btn-success wk_btn_extra form-control-submit submitBookingProduct">
											<span>{l s='Save' mod='mpbooking'}</span>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	{else}
		<div class="alert alert-danger">
			{l s='You are logged out. Please login to add product.' mod='mpbooking'}</span>
		</div>
	{/if}
{/block}