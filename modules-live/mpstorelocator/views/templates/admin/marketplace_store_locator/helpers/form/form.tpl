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

<div class="panel">
<form action="" id="tagform-full" method="post" class="form-horizontal" role="form" enctype="multipart/form-data" novalidate>
	<div class="form-wrapper">
		<div class="row"> 
			<input type="hidden" id="autocomplete_link"  value="{$autocomplete_link}" />
			<input type="hidden" name="latitude" id="latitude" value="{if isset($store)}{$store.latitude}{/if}">
			<input type="hidden" name="longitude" id="longitude" value="{if isset($store)}{$store.longitude}{/if}">
			<input type="hidden" name="map_address" id="map_address" value="{if isset($store)}{$store.map_address}{/if}">
			<input type="hidden" name="map_address_text" id="map_address_text" value="{if isset($store)}{$store.map_address_text}{/if}">
			<div class="col-sm-12">
				<input id="pac-input" class="controls" type="text" value="{if isset($store)}{$store.map_address_text}{/if}"
					placeholder="{l s='Enter location' mod='mpstorelocator'}">
				<input type="button" value="Search" id="btn_store_search" class="btn btn-primary controls">
				<div id="map-canvas"></div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="tabs wk-tabs-panel wk-margin-top-20">
						<ul class="nav nav-tabs">
							<li class="active">
								<a href="#mp_store_detail" data-toggle="tab">
									{l s='Store Details' mod='wkstorelocator'}
								</a>
							</li>
							<li>
								<a href="#mp_store_pick_up_detail" data-toggle="tab" >
									{l s='Store pick up Details' mod='wkstorelocator'}
								</a>
							</li>
						</ul>
						<div class="tab-content panel collapse in clearfix">
							<div class="tab-pane active clearfix" id="mp_store_detail">
								<div class="col-sm-12">
									<div class="form-group">
										<label for="country" class="col-sm-4 control-label required">{l s='Select Seller' mod='mpstorelocator'}</label>
										<div class="col-sm-6">
											<select name="seller_name" id="seller_name" class="form-control" value="{if isset($store)}{$store.id_seller}{else}{if isset($smarty.post.seller_name)}{$smarty.post.seller_name}{/if}{/if}">
												{if isset($seller_info)}
													{if !isset($store)}
														<option value="0">{l s='Select Seller' mod='mpstorelocator'}</option>
													{/if}
													{foreach $seller_info as $seller}
														{if isset($store)}
															{if $store.id_seller == $seller.id_seller}
																<option value="{$seller.id_seller}" Selected="selected">{$seller.seller_firstname} {$seller.seller_lastname}</option>
															{/if}
														{else}
															<option value="{$seller.id_seller}" {if isset($smarty.post.seller_name) && $smarty.post.seller_name == $seller.id_seller}Selected="selected"{/if}>{$seller.seller_firstname} {$seller.seller_lastname}</option>
														{/if}
													{/foreach}
												{else}
													<option value=''>{l s='No seller found' mod='mpstorelocator'}</option>
												{/if}
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="name" class="col-sm-4 control-label required">{l s='Store Name' mod='mpstorelocator'}</label>
										<div class="col-sm-6">
											<input type="text" class="form-control" name="shop_name" id="shop_name" value="{if isset($store)}{$store.name|escape:'htmlall':'UTF-8'}{else}{if isset($smarty.post.shop_name)}{$smarty.post.shop_name}{/if}{/if}">
											{if isset($store)}
												<input type="hidden" value="{$store.id}" name="id_store">
											{/if}
										</div>
									</div>

									<div class="form-group">
										<label for="email" class="col-sm-4 control-label">{l s='Email' mod='mpstorelocator'}</label>
										<div class="col-sm-6">
											<input type="text" class="form-control" name="email" id="email" value="{if isset($store)}{$store.email}{else}{if isset($smarty.post.email)}{$smarty.post.email}{/if}{/if}"/>
										</div>
									</div>
									<div class="form-group">
										<label for="address1" class="col-sm-4 control-label required">{l s='Address1' mod='mpstorelocator'}</label>
										<div class="col-sm-6">
											<input type="text" class="form-control" name="address1" id="address1" value="{if isset($store)}{$store.address1}{else}{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}{/if}"/>
										</div>
									</div>
									<div class="form-group">
										<label for="address2" class="col-sm-4 control-label">{l s='Address2' mod='mpstorelocator'}</label>
										<div class="col-sm-6">
											<input type="text" class="form-control" name="address2" id="address2" value="{if isset($store)}{$store.address2}{else}{if isset($smarty.post.address2)}{$smarty.post.address2}{/if}{/if}"/>
										</div>
									</div>

									<div class="form-group">
										<label for="name" class="col-sm-4 control-label required">{l s='City' mod='mpstorelocator'}</label>
										<div class="col-sm-6">
											<input type="text" class="form-control" name="city_name" id="city_name" value="{if isset($store)}{$store.city_name}{else}{if isset($smarty.post.city_name)}{$smarty.post.city_name}{/if}{/if}">
										</div>
									</div>

									<div class="form-group">
										<label class="control-label col-lg-4 required">{l s='Country' mod='mpstorelocator'}</label>
										<div class="col-lg-6">
											<select  name="countries" id="countries" class="form-control">
												<option value="">{l s='Select' mod='mpstorelocator'}</option>
												{foreach $countries as $country}
													<option value="{$country.id_country}" {if isset($smarty.post.countries) && $smarty.post.countries == $country.id_country}Selected="selected"{else} {if isset($store)}{if $store.country_id == $country.id_country}Selected="selected"{/if}{/if}{/if}>{$country.name}</option>
												{/foreach}
											</select>
										</div>
									</div>

									<div class="form-group country_states_div">
										<label for="state" class="col-sm-4 control-label required">{l s='State' mod='mpstorelocator'}</label>
										<div class="col-sm-6">
											<select  name="state" id="state" class="form-control">
												<option value="">{l s='Select' mod='mpstorelocator'}</option>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="zipcode" class="col-sm-4 control-label required">{l s='Zip/Postal Code' mod='mpstorelocator'}</label>
										<div class="col-sm-6">
											<input type="text" class="form-control" name="zip_code" id="zip_code" maxlength="12" value="{if isset($store)}{$store.zip_code}{else}{if isset($smarty.post.zip_code)}{$smarty.post.zip_code}{/if}{/if}">
										</div>
									</div>

									<div class="form-group">
										<label for="phone" class="col-sm-4 control-label">{l s='Phone' mod='mpstorelocator'}</label>
										<div class="col-sm-6">
											<input type="text" class="form-control" name="phone" id="phone" value="{if isset($store)}{$store.phone}{else}{if isset($smarty.post.phone)}{$smarty.post.phone}{/if}{/if}">
										</div>
									</div>
									<div class="form-group">
										<label for="fax" class="col-sm-4 control-label">{l s='Fax' mod='mpstorelocator'}</label>
										<div class="col-sm-6">
											<input type="text" class="form-control" name="fax" id="fax" value="{if isset($store)}{$store.fax}{else}{if isset($smarty.post.fax)}{$smarty.post.fax}{/if}{/if}">
										</div>
									</div>

									<div class="form-group">
										<label for="status" class="col-sm-4 control-label">{l s='Status' mod='mpstorelocator'}</label>
										<div class="col-lg-8">
											<span class="switch prestashop-switch fixed-width-lg">
												<input type="radio" value="1" id="store_status_on" name="store_status" {if isset($store)}{if $store.active == 1}checked="checked"{/if}{/if}>
												<label for="store_status_on">{l s='Yes' mod='mpstorelocator'}</label>
												<input type="radio" value="0" id="store_status_off" name="store_status" {if isset($store)}{if $store.active == 0}checked="checked"{/if}{else}checked="checked"{/if}>
												<label for="store_status_off">{l s='No' mod='mpstorelocator'}</label>
												<a class="slide-button btn"></a>
											</span>
										</div>
									</div>

									{if isset($store)}
										<div class="form-group">
											<label class="col-sm-4 control-label"></label>
											<div class="col-sm-8">
												<div class="form-group">
													<div>
														{if isset($img_exist)}
															<p id="storelogo_img">
																<img class="img-thumbnail" src="{$logo_path}" title="{$store.name|escape:'htmlall':'UTF-8'}" alt="{l s='No image found' mod='mpstorelocator'}"/>
															</p>
															{* <p>
																<a class="btn btn-default delete_store_logo_admin" data-id_store ="{$store.id}" href="#"><i class="icon-trash"></i> {l s='Delete' mod='mpstorelocator'}</a>
															</p> *}
															<div class="alert alert-danger delete_store_logo_error"></div>
															<p class="alert alert-success delete_store_logo_success"></p>
														{else}
															<p>
																<img class="img-thumbnail" src="{$default_logo_path}" title="{$store.name|escape:'htmlall':'UTF-8'}" alt="{l s='Image' mod='mpstorelocator'}"/>
															</p>
														{/if}
													</div>
												</div>
											</div>
										</div>
									{/if}

									<div class="form-group">
										<label for="storelogo" class="col-sm-4 control-label">{l s='Store Logo' mod='mpstorelocator'}</label>
										<div class="col-sm-8">
											<input type="file" name="store_logo" id="store_logo"/>
											<p class="help-block">{l s='Image maximum size must be 800 x 800 px' mod='mpstorelocator'}</p>
										</div>
									</div>

									<div class="form-group">
										<label for="storeproducts" class="col-sm-4 control-label">{l s='Store Products' mod='mpstorelocator'}</label>
										<div class="col-sm-6">
											<select name="store_products[]" id="store_products" class="form-control" multiple="multiple">
												<option value="0">{l s='Select seller first' mod='mpstorelocator'}</option>
											</select>
											<p class="help-block">{l s='Select the products located on this store.' mod='mpstorelocator'}</p>
										</div>
									</div>

									{* <div class="form-group">
										<div class="col-sm-offset-4 col-sm-6">
											<input type="submit" id="submit_store" name="submit_store" class="btn btn-default button button-medium" value="{l s='Submit' mod='mpstorelocator'}"/>
										</div>
									</div> *}
								</div>
							</div>
							<div class="tab-pane clearfix" id="mp_store_pick_up_detail">
								<div class="col-sm-12">
									<div class="form-group">
										<label for="status" class="col-sm-4 control-label">{l s='Apply Store pick up' mod='wkstorelocator'}</label>
										<div class="col-lg-8">
											<span class="switch prestashop-switch fixed-width-lg">
												<input type="radio" value="1" id="store_pickup_available_on" name="store_pickup_available" {if isset($smarty.post.store_pickup_available)}{if $smarty.post.store_pickup_available == 1} checked="checked"{/if}{else}{if isset($store)}{if $store.store_pickup_available == 1} checked="checked"{/if}{/if}{/if}>
												<label for="store_pickup_available_on">{l s='Yes' mod='wkstorelocator'}</label>
												<input type="radio" value="0" id="store_pickup_available_off" name="store_pickup_available" {if isset($smarty.post.store_pickup_available)}{if $smarty.post.store_pickup_available == 0} checked="checked"{/if}{else}{if isset($store)}{if $store.store_pickup_available == 0} checked="checked"{/if}{else}checked="checked"{/if}{/if}>
												<label for="store_pickup_available_off">{l s='No' mod='wkstorelocator'}</label>
												<a class="slide-button btn"></a>
											</span>
										</div>
									</div>
									<div class="form-group" id="wk_store_pickup_time_slot">
										<label for="storelogo" class="col-sm-4 control-label">{l s='Pick Up Slot' mod='wkstorelocator'}</label>
										<div class="col-sm-4">
											<div class="table-responsive">
												<table class="table table-bordered table-striped table-highlight">
													<thead>
														<th>{l s='Start Time' mod='wkstorelocator'}</th>
														<th>{l s='End Time' mod='wkstorelocator'}</th>
													</thead>
													<tbody>
														<tr>
															<td>
																<input class="pick-time form-control" type="text" name="pickup_start_time" id="pickup_start_time" readonly value="{if isset($smarty.post.pickup_start_time)}{$smarty.post.pickup_start_time}{else}{if isset($store.pickup_start_time) && !empty($store.pickup_start_time)}{$store.pickup_start_time}{else}{if isset($smarty.post.pickup_start_time)}{$smarty.post.pickup_start_time}{/if}{/if}{/if}">
															</td>
															<td>
																<input class="pick-time form-control" type="text" name="pickup_end_time" readonly value="{if isset($smarty.post.pickup_end_time)}{$smarty.post.pickup_end_time}{else}{if isset($store.pickup_end_time) && !empty($store.pickup_end_time)}{$store.pickup_end_time}{else}{if isset($smarty.post.pickup_end_time)}{$smarty.post.pickup_end_time}{/if}{/if}{/if}">
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
									<div class="form-group row">
										<label for="storetiming" class="col-sm-4 control-label">{l s='Store Timing :' mod='wkstorelocator'}</label>
										<div class="col-sm-4">
											<div class="table-responsive">
												<table class="table table-bordered table-striped table-highlight">
													<thead>
														<th>{l s='Days' mod='wkstorelocator'}</th>
														<th>{l s='Opening Time' mod='wkstorelocator'}</th>
														<th>{l s='Closing Time' mod='wkstorelocator'}</th>
													</thead>
													<tbody>
														{for $i=0 to 6}
															{assign var="storeOpeningDays" value="store_opening_days_`$i`"}
															{assign var="openingTime" value="opening_time_`$i`"}
															{assign var="closingTime" value="closing_time_`$i`"}
															<tr>
																<td>
																	<div class="" style="margin: 5px;">
																		<input type="checkbox" name="store_opening_days_{$i}" class="store_opening_days" value="{if isset($store) && 1 == $store.store_opening_days.$i}1{else}{if isset($smarty.post.$storeOpeningDays)}1{else}0{/if}{/if}" {if isset($store) && 1 == $store.store_opening_days.$i}checked="checked"{else}{if isset($smarty.post.$storeOpeningDays)}checked="checked"{/if}{/if}>
																	</div>
																	<div class="">{l s={$weekdays[$i]} mod='wkstorelocator'}</div>
																</td>
																<td>
																	<input class="pick-time form-control" type="text" name="opening_time_{$i}" readonly value="{if isset($store.opening_time[$i]) && !empty($store.opening_time.$i)}{$store.opening_time.$i}{else}{if isset($smarty.post.$openingTime)}{$smarty.post.$openingTime}{/if}{/if}">
																</td>
																<td>
																	<input class="pick-time form-control" type="text" name="closing_time_{$i}" readonly value="{if isset($store.closing_time.$i)}{$store.closing_time.$i}{else}{if isset($smarty.post.$closingTime)}{$smarty.post.$closingTime}{/if}{/if}">
																</td>
															</tr>
														{/for}
													</tbody>
												</table>
											</div>
										</div>
									</div>
									{if $storePaymentOptions}
									<div class="form-group">
										<label class="control-label col-lg-4">
											<span class="" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='' mod='wkstorelocator'}">{l s='Select Store Payment Option' mod='wkstorelocator'}</span>
										</label>
										<div class="col-sm-4">
											<div class="row">
												<table class="table table-bordered">
													<thead>
														<tr>
															<th class="fixed-width-xs">
																<span class="title_box">
																	<input name="checkme" id="checkme" onclick="checkDelBoxes(this.form, 'wk_store_payment_option[]', this.checked)" type="checkbox">
																</span>
															</th>
															<th>
																<span class="title_box">
																	{l s='Select Payment' mod='wkstorelocator'}
																</span>
															</th>
														</tr>
													</thead>
													<tbody>
														{foreach $storePaymentOptions as $key=>$payment}
															<tr>
																<td>
																	<input name="wk_store_payment_option[]" class="groupBox" id="groupBox_{$payment.id_mp_store_pay|escape:'htmlall':'UTF-8'}" value="{$payment.id_mp_store_pay|escape:'htmlall':'UTF-8'}" {if isset($store.payment_option) && in_array($payment.id_mp_store_pay, $store.payment_option)}checked="checked"{/if} type="checkbox">
																</td>
																<td>
																	<label for="groupBox_{$payment.id_mp_store_pay|escape:'htmlall':'UTF-8'}">{$payment.payment_name|escape:'htmlall':'UTF-8'}</label>
																</td>
															</tr>
														{/foreach}
													</tbody>
												</table>
											</div>
										</div>
									</div>
									{/if}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<button type="submit" value="1" id="submit_store" name="submit_store" class="btn btn-default pull-right">
			<i class="process-icon-save"></i> {l s='Save' mod='mpstorelocator'}
		</button>
		<button type="submit" value="1" id="submit_and_stay_store" name="submit_and_stay_store" class="btn btn-default pull-right">
			<i class="process-icon-save"></i> {l s='Save & Stay' mod='mpstorelocator'}
		</button>
	</div>
</form>
</div>

{strip}
	{addJsDef url_filestate=$link->getModuleLink('mpstorelocator', filterstate)}
	{addJsDefL name='req_seller_name'}{l s='Seller name is required.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='req_shop_name'}{l s='Store name is required.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='inv_shop_name'}{l s='Store name is invalid.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='req_street'}{l s='Street is required.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='req_city_name'}{l s='City name is required.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='inv_city_name'}{l s='City name is invalid.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='req_countries'}{l s='Country is required.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='req_zip_code'}{l s='Zip/Postal code is required.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='inv_zip_code'}{l s='Zip/Postal code is inavlid.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='req_latitude'}{l s='Please select location on map' js=1 mod='mpstorelocator'}{/addJsDefL}

	{if isset($store)}
		{addJsDef store = $store}
		{addJsDef id_store = $store.id}
		{addJsDef lat = $store.latitude}
		{addJsDef lng = $store.longitude}
		{addJsDef map_address = $store.map_address}
		{addJsDef country_id = $store.country_id}
		{addJsDef state_id = $store.state_id}
		{addJsDef products = $store.products}
		{addJsDef id_seller = $store.id_seller}
	{/if}
	
	{addJsDef admin_str_loc = $link->getAdminLink('AdminMarketplaceStoreLocator')}
	{addJsDefL name='no_product'}{l s='No product(s) found' js=1 mod='mpstorelocator'}{/addJsDefL}
{/strip}

<script type="text/javascript">
{if isset($store)}
	var id_store = {$store.id};
	var lat = {$store.latitude};
	var lng = {$store.longitude};
	var map_address = "{$store.map_address}";

	$("#latitude").val(lat);
	$("#longitude").val(lng);
	$("#map_address").val(map_address);
{/if}
$(document).ready(function(){
	var no_product = "{l s='No product(s) found' mod='mpstorelocator'}";
	$(".delete_store_logo_error").hide()
	$(".delete_store_logo_success").hide()
	// when edit store
	{if isset($store)}
		getStateJs({$store.country_id}, {$store.state_id});
		{if !empty($store.products)}
			getSellerProductsJs({$store.id_seller}, {$store.products});
		{/if}
	{/if}
	
	$("#countries").on("change", function(){
		var id_country = $(this).val();
		if (id_country == "") {
			alert("{l s='Please select a country' mod='mpstorelocator'}");
			$("#state").empty();
			$("#state").append("<option value=''>Select</option>");
		}
		else {
			getStateJs(id_country);
		}
	});

	{if !isset($store)}
		if (typeof $('#countries').val() != 'undefined' && $('#countries').val()) {
			getStateJs($('#countries').val());
		}
		if (typeof $('#seller_name').val() != 'undefined' && $('#seller_name').val()) {
			getSellerProductsJs($('#seller_name').val());
		}
	{/if}
	// filter state by country
	function getStateJs(id_country, id_state_selected)
	{
		$.ajax({
			url: admin_str_loc,
			dataType: "json",
			data: {
				ajax: "1",
				action: "filterStates",
				id_country: id_country
			},
			success: function(result){
				if (result == 'no_states')
				{
					$("#state").empty();
					$(".country_states_div").hide();
				}
				else if (result != 'failed')
				{
					$(".country_states_div").show();
					$("#state").empty();
					$("#state").append("<option value=''>Select</option>");
					$.each(result, function(index, value){
						if (id_state_selected == value.id_state) {
							$("#state").append("<option value="+value.id_state+" selected>"+value.name+"</option>");
						}
						else {
							$("#state").append("<option value="+value.id_state+">"+value.name+"</option>");
						}
					});
				}
			}
		});
	}

	$("#seller_name").on("change", function(){
		var id_seller = $(this).val();
		if (id_seller == 0) {
			alert("Please select seller");
			$("#store_products").empty();
			return false;
		}
		else {
			getSellerProductsJs(id_seller);
		}
	});	

	// getting seller products
	function getSellerProductsJs(id_seller, id_products)
	{
		var selected_products = [];
		var all_products = [];
		$.ajax({
			url: admin_str_loc,
			dataType: "json",
			data: {
				ajax: "1",
				action: "getSellerProducts",
				id_seller: id_seller
			},
			success: function(result){
				$("#store_products").empty();
				if (result != 'failed') {
					$.each(result, function(index, value){
						if (id_products) {
							$.each(id_products, function(i, v) {
								if (v.id_product == value.id_product) {
									$("#store_products").append("<option value="+value.id_product+" selected>"+value.product_name+"</option>");
									selected_products.push(value.id_product);
								}
							});
						}
						else {
							$("#store_products").append("<option value="+value.id_product+">"+value.product_name+"</option>");
						}
						all_products.push(value.id_product);
					});

					if (id_products) {
						var other = getArrayDiff(all_products, selected_products);
						$.each(result, function(index, value){
							$.each(other, function(i, v){
								if (value.id_product == v)
									$("#store_products").append("<option value="+value.id_product+">"+value.product_name+"</option>");
							});
						});
					}
					$(document).find('#store_products').multiselect({
						includeSelectAllOption: true,
						enableFiltering: true,
						enableCaseInsensitiveFiltering: true,
						maxHeight: 200,
					});
				}
				else {
					$("#store_products").append("<option value='0'>"+no_product+"</option>");

				}

                $('i.glyphicon.glyphicon-search').removeClass('glyphicon glyphicon-search').addClass('icon icon-search');
                $('i.glyphicon.glyphicon-remove-circle').removeClass('glyphicon glyphicon-remove-circle').addClass('icon icon-remove-circle');
			}
		});
	}

	$(document).on("click", ".delete_store_logo_admin", function(e){
		e.preventDefault();
		var id_store = $(this).data("id_store");
		if (confirm("Are you sure?"))
		{
			$.ajax({
				url: admin_str_loc,
				dataType: "json",
				data: {
					ajax: "1",
					action: "deleteStoreLogo",
					id_store: id_store
				},
				success: function(result){
					if (result.status == "success") {
						$('.delete_store_logo_admin').hide();
						$('.delete_store_logo_success').show();
						$('.delete_store_logo_success').html(result.msg);
						$('#storelogo_img').html('<img class="img-thumbnail" src="{$default_logo_path}">');
						//window.location.href = window.location.href;
					}
					else {
						$(".delete_store_logo_error").show();
						$(".delete_store_logo_error").html(result.msg);
					}
				}
			})
		}
	});

	function getArrayDiff(large_array, small_array)
	{
		var diff = [];
		$.grep(large_array, function(el) {
	        if ($.inArray(el, small_array) == -1)
	        	diff.push(el);
		});

		return diff;
	}
});
</script>