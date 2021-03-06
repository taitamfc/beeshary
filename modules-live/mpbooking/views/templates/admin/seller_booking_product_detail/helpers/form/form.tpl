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

<div class="panel">
	<div class="panel-heading">
		{if isset($edit)}
			{l s='Edit Product' mod='mpbooking'}
		{else}
			{l s='Add New Product' mod='mpbooking'}
		{/if}
	</div>
	<form name="mp_admin_saveas_button" id="mp_admin_saveas_button" class="defaultForm {$name_controller} form-horizontal" action="{if isset($edit)}{$current}&update{$table}&id_mp_product={$product_info.id}&token={$token}{else}{$current}&add{$table}&token={$token}{/if}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style}"{/if}>

		{hook h='displayBkMpAddProductHeader'}
		<div class="form-group">
			<div class="col-lg-6">
				{if !isset($edit)}
					<div class="form-group">
						<label class="control-label pull-left required">
							{l s='Choose Seller' mod='mpbooking'}&nbsp;
						</label>
						{if isset($customer_info)}
							<select name="id_seller" id="wk_shop_customer" class="fixed-width-xl pull-left">
								{foreach $customer_info as $cusinfo}
									<option value="{$cusinfo['id_seller']}">
										{$cusinfo['business_email']}
									</option>
								{/foreach}
							</select>
						{else}
							<p>{l s='No seller found.' mod='mpbooking'}</p>
						{/if}
					</div>
				{/if}
				{if $multi_lang}
					<div class="form-group">
						<label class="control-label">
							&nbsp;&nbsp;{l s='Seller Default Language -' mod='mpbooking'}
							<span id="seller_default_lang_div">{$current_lang.name}</label>
						</label>
					</div>
				{/if}
			</div>
			{if $allow_multilang && $total_languages > 1}
				<div class="col-lg-6">
					<label class="control-label">{l s='Choose Language' mod='mpbooking'}</label>
					<input type="hidden" name="choosedLangId" id="choosedLangId" value="{$current_lang.id_lang}">
					<button type="button" id="seller_lang_btn" class="btn btn-default dropdown-toggle wk_language_toggle" data-toggle="dropdown">
						{$current_lang.name}
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu wk_language_menu" style="left:14%;top:32px;">
						{foreach from=$languages item=language}
							<li>
								<a href="javascript:void(0)" onclick="showProdLangField('{$language.name}', {$language.id_lang});">
									{$language.name}
								</a>
							</li>
						{/foreach}
					</ul>
					<p class="help-block">{l s='Change language for updating information in multiple language.' mod='mpbooking'}</p>
				</div>
			{/if}
		</div>

		<input type="hidden" name="active_tab" value="{if isset($active_tab)}{$active_tab}{/if}" id="active_tab">
		<input type="hidden" value="{if isset($edit)}{$product_info.id}{/if}" name="id_mp_product" id="mp_product_id" />
		<input type="hidden" name="seller_default_lang" id="seller_default_lang" value="{$current_lang.id_lang}">
		<input type="hidden" name="id_booking_product_info" id="id_booking_product_info" value="{if isset($idproduct_info) && $idproduct_info}{$idproduct_info}{/if}">
		<div class="alert alert-danger wk_display_none" id="wk_mp_form_error"></div>
		<div class="tabs wk-tabs-panel">
			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#wk-information" data-toggle="tab">
						<i class="icon-info-sign"></i>
						{l s='Information' mod='mpbooking'}
					</a>
				</li>
				<li>
					<a href="#wk-images" data-toggle="tab">
						<i class="icon-image"></i>
						{l s='Images' mod='mpbooking'}
					</a>
				</li>
				{if isset($edit)}
					{if isset($product_info.booking_type) && $product_info.booking_type == $booking_type_time_slot}
						<li>
							<a href="#wk-booking_configuration" data-toggle="tab">
								<i class="icon-clock-o"></i>
								{l s='Time Slots Booking Plans' mod='mpbooking'}
							</a>
						</li>
					{/if}
					<li>
						<a href="#booking_disable_dates" data-toggle="tab">
							<i class="icon-ban"></i>
							{l s='Disable Dates/Slots' mod='mpbooking'}
						</a>
					</li>
					<li>
						<a href="#booking_availability_info" data-toggle="tab">
							<i class="icon-calendar"></i>
							{l s='Availability & Rates' mod='mpbooking'}
						</a>
					</li>
				{/if}
				{hook h='displayBkMpProductNavTab'}
			</ul>
			<div class="tab-content panel collapse in">
				<div class="tab-pane active" id="wk-information">
					{if isset($edit)}
						{hook h='displayBkMpUpdateProductContentTop'}
					{else}
						{hook h='displayBkMpAddProductContentTop'}
					{/if}
					{if !isset($edit)}
						<div class="form-group">
							<label class="col-lg-3 control-label">{l s='Enable product' mod='mpbooking'}</label>
							<div class="col-lg-6">
								<span class="switch prestashop-switch fixed-width-lg">
									<input type="radio" checked="checked" value="1" id="product_active_on" name="product_active">
									<label for="product_active_on">{l s='Yes' mod='mpbooking'}</label>
									<input type="radio" value="0" id="product_active_off" name="product_active">
									<label for="product_active_off">{l s='No' mod='mpbooking'}</label>
									<a class="slide-button btn"></a>
								</span>
							</div>
						</div>
					{/if}
					<div class="form-group">
						<label for="product_name" class="col-lg-3 control-label required">
							{l s='Product Name' mod='mpbooking'}
							{include file="$ps_modules_dir/marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl"}
						</label>
						<div class="col-lg-6">
							{foreach from=$languages item=language}
								{assign var="product_name" value="product_name_`$language.id_lang`"}
								<input type="text"
								id="product_name_{$language.id_lang}"
								name="product_name_{$language.id_lang}"
								value="{if isset($edit)}{$product_info.product_name[{$language.id_lang}]}{elseif isset($smarty.post.$product_name)}{$smarty.post.$product_name}{/if}"
								class="form-control product_name_all wk_text_field_all wk_text_field_{$language.id_lang}"
								maxlength="128"
								{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
							{/foreach}
						</div>
					</div>
					{if !isset($edit)}
						{hook h="displayBkMpAddProductNameBottom"}
					{else}
						{hook h="DisplayBkMpUpdateProductNameBottom"}
					{/if}
					<div class="form-group">
						<label for="short_description" class="col-lg-3 control-label">
							{l s='Short Description' mod='mpbooking'}
							{include file="$ps_modules_dir/marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl"}
						</label>
						<div class="col-lg-9">
							{foreach from=$languages item=language}
								{assign var="short_desc_name" value="short_description_`$language.id_lang`"}
								<div id="short_desc_div_{$language.id_lang}" class="wk_text_field_all wk_text_field_{$language.id_lang}" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>
									<textarea
									name="short_description_{$language.id_lang}"
									id="short_description_{$language.id_lang}"
									cols="2" rows="3"
									class="wk_tinymce form-control">{if isset($edit)}{$product_info.short_description[{$language.id_lang}]}{elseif isset($smarty.post.$short_desc_name)}{$smarty.post.$short_desc_name}{/if}</textarea>
								</div>
							{/foreach}
						</div>
					</div>
					<div class="form-group">
						<label for="product_description" class="col-lg-3 control-label">
							{l s='Description' mod='mpbooking'}
							{include file="$ps_modules_dir/marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl"}
						</label>
						<div class="col-lg-9">
							{foreach from=$languages item=language}
								{assign var="description" value="description_`$language.id_lang`"}
								<div id="product_desc_div_{$language.id_lang}" class="wk_text_field_all wk_text_field_{$language.id_lang}" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>
									<textarea
									name="description_{$language.id_lang}"
									id="description_{$language.id_lang}"
									cols="2" rows="3"
									class="wk_tinymce form-control">{if isset($edit)}{$product_info.description[{$language.id_lang}]}{elseif isset($smarty.post.$description)}{$smarty.post.$description}{/if}</textarea>
								</div>
							{/foreach}
						</div>
					</div>
					<div class="form-group">
						<label for="reference" class="control-label col-lg-3">
							{l s='Reference Code' mod='mpbooking'}
							<div class="wk_tooltip">
								<span class="wk_tooltiptext">{l s='Your internal reference code for this product. Allowed max 32 character. Allowed special characters' mod='mpbooking'}:.-_#."' mod='mpbooking'}</span>
							</div>
						</label>
						<div class="col-lg-6">
						<input type="text"
							class="form-control"
							name="reference"
							id="reference"
							value="{if isset($smarty.post.reference)}{$smarty.post.reference}{else if isset($edit)}{$product_info.reference}{/if}"
							maxlength="32" />
						</div>
					</div>
					
					<!--Start custom fields-->
					<div class="form-group">
						<label for="activity_addr" class="control-label col-lg-3">Adresse de votre activit??</label>
						<div class="col-lg-9">
							<input type="text" class="form-control" name="activity_addr" id="activity_addr" placeholder="Adresse de votre activit??" maxlength="500" required=""
							value="{if isset($bookingProductInfo) }{$bookingProductInfo.activity_addr}{/if}"
							>
						</div>
					</div>
					<div class="form-group">
						<label for="activity_city" class="control-label col-lg-3">Ville de votre activit??</label>
						<div class="col-lg-9">
							<input type="text" class="form-control input_left" name="activity_city" id="activity_city" placeholder="Ville de votre activit??" maxlength="80" required=""
							value="{if isset($bookingProductInfo) }{$bookingProductInfo.activity_city}{/if}"
							>
						</div>
					</div>
					<div class="form-group">
						<label for="activity_city" class="control-label col-lg-3">Code postal de votre activit??</label>
						<div class="col-lg-9">
							<input type="text" class="form-control input_right" name="activity_postcode" id="activity_postcode" placeholder="Code postal de votre activit??" maxlength="6" required=""
							value="{if isset($bookingProductInfo) }{$bookingProductInfo.activity_postcode}{/if}"
							>
						</div>
					</div>
					<!--$bookingProductInfo.activity_period-->
					<div class="form-group">
						<label for="activity_city" class="control-label col-lg-3">Dur??e de l'activit??</label>
						<div class="col-lg-9">
							<select name="activity_period" id="activity_period" class="form-control input_left" required="">
									<option value="">Dur??e de l'activit??</option>
									<option value="< 15 minutes"
									{if isset($bookingProductInfo) && $bookingProductInfo.activity_period == '< 15 minutes' }selected{/if}
									>&lt; 15 minutes</option>
									<option value="30 minutes"
									{if isset($bookingProductInfo) && $bookingProductInfo.activity_period == '30 minutes' }selected{/if}
									>30 minutes</option>
									<option value="1 heure"
									{if isset($bookingProductInfo) && $bookingProductInfo.activity_period == '1 heure' }selected{/if}
									>1 heure</option>
									<option value="1h30"
									{if isset($bookingProductInfo) && $bookingProductInfo.activity_period == '1h30' }selected{/if}
									>1h30</option>
									<option value="> 2h"
									{if isset($bookingProductInfo) && $bookingProductInfo.activity_period == '> 2h' }selected{/if}
									>&gt; 2h</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="activity_participants" class="control-label col-lg-3">Nombre max de participants</label>
						<div class="col-lg-9">
							<select name="activity_participants" id="activity_participants" class="form-control input_right" required>
								<option value="">Nombre max de participants</option>
								<option value="1" {if isset($bookingProductInfo) && $bookingProductInfo.activity_participants == 1 }selected{/if}>1</option>
								<option value="2" {if isset($bookingProductInfo) && $bookingProductInfo.activity_participants == 2 }selected{/if}>2</option>
								<option value="3" {if isset($bookingProductInfo) && $bookingProductInfo.activity_participants == 3 }selected{/if}>3</option>
								<option value="4" {if isset($bookingProductInfo) && $bookingProductInfo.activity_participants == 4 }selected{/if}>4</option>
								<option value="5" {if isset($bookingProductInfo) && $bookingProductInfo.activity_participants == 5 }selected{/if}>5</option>
								<option value="6" {if isset($bookingProductInfo) && $bookingProductInfo.activity_participants == 6 }selected{/if}>6</option>
								<option value="7" {if isset($bookingProductInfo) && $bookingProductInfo.activity_participants == 7 }selected{/if}>7</option>
								<option value="8" {if isset($bookingProductInfo) && $bookingProductInfo.activity_participants == 8 }selected{/if}>8</option>
								<option value="9" {if isset($bookingProductInfo) && $bookingProductInfo.activity_participants == 9 }selected{/if}>9</option>
								<option value="10" {if isset($bookingProductInfo) && $bookingProductInfo.activity_participants == 10 }selected{/if}>10</option>
								<option value="11" {if isset($bookingProductInfo) && $bookingProductInfo.activity_participants == 11 }selected{/if}>11</option>
								<option value="12" {if isset($bookingProductInfo) && $bookingProductInfo.activity_participants == 12 }selected{/if}>12</option>
								<option value="+ 12 nous consulter" {if isset($bookingProductInfo) && $bookingProductInfo.activity_participants == '+ 12 nous consulter' }selected{/if}>+ 12 nous consulter</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="activity_curious" class="control-label col-lg-3">Curieux concern??s</label>
						<div class="col-lg-9">
							<select name="activity_curious" id="activity_curious" class="form-control" data-placeholder="Curieux concern??s">
									<option value="Tout public" {if isset($bookingProductInfo) && $bookingProductInfo.activity_curious == 'Tout public' }selected{/if}>Tout public</option>
									<option value="Enfants" {if isset($bookingProductInfo) && $bookingProductInfo.activity_curious == 'Enfants' }selected{/if}>Enfants</option>
									<option value="Personnes ?? mobilit??" {if isset($bookingProductInfo) && $bookingProductInfo.activity_curious == 'Personnes ?? mobilit??' }selected{/if}>Personnes ?? mobilit??</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="activity_material" class="control-label col-lg-3">Mat??riel ?? pr??voir (optionnel)</label>
						<div class="col-lg-9">
							<input type="text" class="form-control input_right" name="activity_material" id="activity_material" placeholder="Mat??riel ?? pr??voir (optionnel)" maxlength="500"
							value="{if isset($bookingProductInfo) }{$bookingProductInfo.activity_material}{/if}"
							>
						</div>
					</div>
					
					
					<div class="form-group">
						<label for="video_link" class="control-label col-lg-3">Votre lien de la video</label>
						<div class="col-lg-9">
							<input type="text" class="form-control" name="video_link" id="video_link" placeholder="Votre lien de la video" maxlength="225"
							value="{if isset($bookingProductInfo) }{$bookingProductInfo.video_link}{/if}"
							>
							<small>example: https://www.youtube.com/embed/xxxxxxxx</small><br>
							<small>example: https://player.vimeo.com/video/xxxxxxxx</small>
						</div>
					</div>
					<!--End custom fields-->
									
					<div class="form-group">
						<label for="condition" class="control-label col-lg-3">
							{l s='Condition' mod='mpbooking'}
							<div class="wk_tooltip">
								<span class="wk_tooltiptext">{l s='This option enables you to indicate the condition of the product.' mod='mpbooking'}</span>
							</div>
						</label>
						<div class="col-lg-4">
							<select class="form-control" name="condition" id="condition">
								<option value="new" {if isset($edit)}{if $product_info.condition == 'new'}Selected="Selected"{/if}{else}{if isset($smarty.post.condition)}{if $smarty.post.condition == 'new'}Selected="Selected"{/if}{/if}{/if}>
									{l s='New' mod='mpbooking'}
								</option>
								<option value="used" {if isset($edit)}{if $product_info.condition == 'used'}Selected="Selected"{/if}{else}{if isset($smarty.post.condition)}{if $smarty.post.condition == 'used'}Selected="Selected"{/if}{/if}{/if}>
									{l s='Used' mod='mpbooking'}
								</option>
								<option value="refurbished" {if isset($edit)}{if $product_info.condition == 'refurbished'}Selected="Selected"{/if}{else}{if isset($smarty.post.condition)}{if $smarty.post.condition == 'refurbished'}Selected="Selected"{/if}{/if}{/if}>
									{l s='Refurbished' mod='mpbooking'}
								</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="booking_type" class="col-lg-3 control-label required">
							{l s='Product Booking Type :' mod='mpbooking'}
						</label>
						<div class="col-lg-6">
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
					<div class="form-group">
						<label for="quantity" class="col-lg-3 control-label required">
							{l s='Quantity' mod='mpbooking'}
							<div class="wk_tooltip">
								<span class="wk_tooltiptext">{l s='How many products should be available for sale?' mod='mpbooking'}</span>
							</div>
						</label>
						<div class="col-lg-6">
							<input type="text"
							class="form-control"
							id="quantity"
							name="quantity"
							value="{if isset($smarty.post.quantity)}{$smarty.post.quantity}{else if isset($edit)}{$product_info.quantity}{else}0{/if}"
							pattern="\d*"
							{if isset($hasAttribute)}readonly{/if} />

							<input type="hidden"
							class="form-control"
							id="minimal_quantity"
							name="minimal_quantity"
							value="{if isset($smarty.post.minimal_quantity)}{$smarty.post.minimal_quantity}{else if isset($edit)}{$product_info.minimal_quantity}{else}1{/if}"
							pattern="\d*"
							{if isset($hasAttribute)}readonly{/if} />
						</div>
					</div>
					{*<div class="form-group">
						<label for="minimal_quantity" class="col-lg-3 control-label required">
							{l s='Minimum quantity for sale' mod='mpbooking'}
							<div class="wk_tooltip">
								<span class="wk_tooltiptext">{l s='The minimum quantity to buy this product (set to 1 to disable this feature)' mod='mpbooking'}</span>
							</div>
						</label>
						<div class="col-lg-6">
							<input type="text"
							class="form-control"
							id="minimal_quantity"
							name="minimal_quantity"
							value="{if isset($smarty.post.minimal_quantity)}{$smarty.post.minimal_quantity}{else if isset($edit)}{$product_info.minimal_quantity}{else}1{/if}"
							pattern="\d*"
							{if isset($hasAttribute)}readonly{/if} />
						</div>
					</div>*}
					<div class="form-group">
						<label class="col-lg-3 control-label required" for="product_category">
							{l s='Category' mod='mpbooking'}
							<div class="wk_tooltip">
								<span class="wk_tooltiptext">{l s='Where should the product be available on your site? The main category is where the product appears by default: this is the category which is seen in the product page\'s URL.' mod='mpbooking'}</span>
							</div>
						</label>
						<div class="col-lg-6">
							<div id="categorycontainer"></div>
							<input type="hidden" name="product_category" id="product_category" value="{if isset($catIdsJoin)}{$catIdsJoin}{/if}" />
						</div>
					</div>
					<div class="form-group" id="default_category_div">
						<label class="col-lg-3 control-label required" for="default_category">
							{l s='Default Category' mod='mpbooking'}
						</label>
						<div class="col-lg-4">
							<select name="default_category" class="form-control" id="default_category">
								{if isset($defaultCategory)}
									{foreach $defaultCategory as $defaultCategoryVal}
										<option
										id="default_cat{$defaultCategoryVal.id_category}"
										value="{$defaultCategoryVal.id_category}"
										{if isset($defaultIdCategory)}{if $defaultIdCategory == $defaultCategoryVal.id_category} selected {/if}{/if}>
											{$defaultCategoryVal.name}
										</option>
									{/foreach}
								{else}
									<option id="default_cat2" value="2">Home</option>
								{/if}
							</select>
						</div>
					</div>
					</br></br>
					<div>
						<div class="row">
							<h3 class="col-lg-12">&nbsp;&nbsp;{l s='Pricing' mod='mpbooking'}</h3>
						</div>
						<div class="form-group row">
							<div class="col-lg-12 ">
								<label for="price" class="control-label required col-lg-3">
									{l s='Price (tax excl.)' mod='mpbooking'}

									<div class="wk_tooltip">
										<span class="wk_tooltiptext">{l s='This is the retail price at which you intend to sell this product to your customers.' mod='mpbooking'}</span>
									</div>
								</label>
								<div class="col-lg-6">
									<div class="input-group">
										<div class="input-group-addon">
											{$defaultCurrencySign}
										</div>
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
												{if isset($productBookingType) && $productBookingType==$booking_type_date_range}
												{l s='day' mod='mpbooking'}
												{else if isset($productBookingType) && $productBookingType==$booking_type_time_slot}
												{l s='slot' mod='mpbooking'}
												{else}
												{l s='day' mod='mpbooking'}
												{/if}
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- Product Tax Rule  -->
						{if isset($mp_seller_applied_tax_rule) && $mp_seller_applied_tax_rule && isset($tax_rules_groups)}
							<div class="form-group ">
								<label for="id_tax_rules_group" class="control-label col-sm-3">
									{l s='Tax Rate' mod='marketplace'}
								</label>
								<div class="row">
									<div class="col-sm-6">
										<select name="id_tax_rules_group" id="id_tax_rules_group" class="form-control form-control-select" data-action="input_excl">
											<option value="0">{l s='No tax' mod='marketplace'}</option>
											{foreach $tax_rules_groups as $tax_rule}
												<option value="{$tax_rule.id_tax_rules_group|escape:'html':'UTF-8'}"
												{if isset($id_tax_rules_group)}{if $id_tax_rules_group == $tax_rule.id_tax_rules_group} selected="selected"{/if}{else}{if $defaultTaxRuleGroup == $tax_rule.id_tax_rules_group} selected="selected" {/if}{/if}>
													{$tax_rule.name|escape:'html':'UTF-8'}
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
							value="{if isset($id_tax_rules_group)}{$id_tax_rules_group|escape:'html':'UTF-8'}{else}1{/if}" >
						{/if}
					</div>
					<div class="panel-footer">
						<a href="{$link->getAdminLink('AdminSellerBookingProductDetail')}" class="btn btn-default">
							<i class="process-icon-cancel"></i>{l s='Cancel' mod='mpbooking'}
						</a>
						<button type="submit" name="submitAdd{$table}" class="btn btn-default pull-right submitBookingProduct">
							<i class="process-icon-save"></i>{l s='Save' mod='mpbooking'}
						</button>
						<button type="submit" name="submitAdd{$table}AndStay" class="btn btn-default pull-right submitBookingProduct">
							<i class="process-icon-save"></i> {l s='Save and stay' mod='mpbooking'}
						</button>
					</div>
					{if isset($edit)}
						{hook h="displayMpUpdateProductFooter"}
					{else}
						{hook h="displayMpAddProductFooter"}
					{/if}
				</div>
				<div class="tab-pane" id="wk-images">
					{if isset($edit)}
						<div class="form-group">
							<div class="wk_upload_product_image">
								<input type="file" name="productimages[]" class="uploadimg_container" data-jfiler-name="productimg">
							</div>
						</div>
						{include file="$ps_modules_dir/marketplace/views/templates/front/product/imageedit.tpl"}
					{else}
						<div class="alert alert-danger">
							{l s='You must save this product before adding images.' mod='mpbooking'}
						</div>
					{/if}
					<div class="panel-footer">
						<a href="{$link->getAdminLink('AdminSellerBookingProductDetail')}" class="btn btn-default">
							<i class="process-icon-cancel"></i>{l s='Cancel' mod='mpbooking'}
						</a>
						<button type="submit" name="submitAdd{$table}" class="btn btn-default pull-right submitBookingProduct">
							<i class="process-icon-save"></i>{l s='Save' mod='mpbooking'}
						</button>
						<button type="submit" name="submitAdd{$table}AndStay" class="btn btn-default pull-right submitBookingProduct">
							<i class="process-icon-save"></i> {l s='Save and stay' mod='mpbooking'}
						</button>
					</div>
				</div>
				{if isset($edit)}
					{if isset($product_info.booking_type) && $product_info.booking_type == $booking_type_time_slot}
						<div class="tab-pane" id="wk-booking_configuration">
							{include file='./_partials/booking_product_time_slots_information.tpl'}
							<div class="panel-footer">
								<a href="{$link->getAdminLink('AdminSellerBookingProductDetail')}" class="btn btn-default">
									<i class="process-icon-cancel"></i>{l s='Cancel' mod='mpbooking'}
								</a>
								<button type="submit" name="submitAdd{$table}" class="btn btn-default pull-right submitBookingProduct">
									<i class="process-icon-save"></i>{l s='Save' mod='mpbooking'}
								</button>
								<button type="submit" name="submitAdd{$table}AndStay" class="btn btn-default pull-right submitBookingProduct">
									<i class="process-icon-save"></i> {l s='Save and stay' mod='mpbooking'}
								</button>
							</div>
						</div>
					{/if}
					<div class="tab-pane" id="booking_disable_dates">
						{include file='./_partials/booking_disable_dates_info.tpl'}
						<div class="panel-footer">
							<a href="{$link->getAdminLink('AdminSellerBookingProductDetail')}" class="btn btn-default">
								<i class="process-icon-cancel"></i>{l s='Cancel' mod='mpbooking'}
							</a>
							<button type="submit" name="submitAdd{$table}" class="btn btn-default pull-right submitBookingProduct">
								<i class="process-icon-save"></i>{l s='Save' mod='mpbooking'}
							</button>
							<button type="submit" name="submitAdd{$table}AndStay" class="btn btn-default pull-right submitBookingProduct">
								<i class="process-icon-save"></i> {l s='Save and stay' mod='mpbooking'}
							</button>
						</div>
					</div>
					<div class="tab-pane" id="booking_availability_info">
						{include file='./_partials/availablity_rates_info.tpl'}
					</div>
				{/if}
				{hook h='displayBkMpProductTabContent'}
			</div>
		</div>
	</form>
</div>
{block name=script}
	<script type="text/javascript">
		$(document).ready(function() {
			tinySetup({
				editor_selector: "wk_tinymce",
			});
		});
		$('.fancybox').fancybox();
	</script>
{/block}