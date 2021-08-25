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

{extends file=$layout}
{block name='content'}
	{if isset($smarty.get.success)}
		{if $smarty.get.success == 1}
			<p class="alert alert-success">
				{if $manage_status}
					{l s='Store location created.' mod='mpstorelocator'}
				{else}
					{l s='Store location created. Location will be activated after admin approval. Please wait.' mod='mpstorelocator'}
				{/if}
			</p>
		{else if $smarty.get.success == 2}
			<p class="alert alert-success">
				{l s='Store location updated.' mod='mpstorelocator'}
			</p>
		{/if}
	{/if}

	{if isset($smarty.get.deleted)}
		{if $smarty.get.deleted == 1}
			<p class="alert alert-success">
				{l s='Store deleted successfully.' mod='mpstorelocator'}
			</p>
		{else if $smarty.get.deleted == 2}
			<p class="alert alert-danger">
				{l s='Some problem while deleting this store.' mod='mpstorelocator'}
			</p>
		{/if}
	{/if}

	{if isset($smarty.get.delete_logo_msg)}
		{if $smarty.get.delete_logo_msg == 1}
			<p class="alert alert-success">
				{l s='Store logo deleted successfully.' mod='mpstorelocator'}
			</p>
		{else if $smarty.get.delete_logo_msg == 2}
			<p class="alert alert-danger">
				{l s='Error while deleting image.' mod='mpstorelocator'}
			</p>
		{/if}
	{/if}

	{hook h='displayMpAddProductHeader'}
	<div class="wk-mp-block">
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color};">
				<span style="color:{$title_text_color};">{if isset($id_store)}{l s='Update Store' mod='mpstorelocator'}{else}{l s='Add Store' mod='mpstorelocator'}{/if}</span>
			</div>
			<form action="{$link->getModuleLink('mpstorelocator', 'addstore')}" method="post" id="tagform-full" class="form-horizontal" role="form" enctype="multipart/form-data" novalidate>
				<input type="hidden" value="" id="active_tab" name="active_tab"/>
				<div class="row addStoreCont wk-mp-right-column">
					<div class="form-group back_add_btnCont clearfix">
						<div class="col-sm-12 col-xs-12">
							{if isset($id_store)}
								<a href="{$link->getModuleLink('mpstorelocator', 'addstore')}" class="btn btn-primary" style="margin-right:5px;">
									<span>{l s='Add new store' mod='mpstorelocator'}</span>
								</a>
							{/if}
						</div>
					</div>
					<div class="col-xs-12 col-sm-12">
						<input type="hidden" name="latitude" id="latitude" value="{if isset($id_store)}{$store.latitude}{else}{if isset($smarty.post.latitude)}{$smarty.post.latitude}{/if}{/if}">
						<input type="hidden" name="longitude" id="longitude" value="{if isset($id_store)}{$store.longitude}{else}{if isset($smarty.post.longitude)}{$smarty.post.longitude}{/if}{/if}">
						<input type="hidden" name="map_address" id="map_address" value="{if isset($id_store)}{$store.map_address}{else}{if isset($smarty.post.map_address)}{$smarty.post.map_address}{/if}{/if}">
						<input type="hidden" name="map_address_text" id="map_address_text" value="{if isset($id_store)}{$store.map_address_text}{else}{if isset($smarty.post.map_address_text)}{$smarty.post.map_address_text}{/if}{/if}">
						<input type="hidden" name="id_customer" id="id_customer" value="{$id_customer}" />
						<input type="hidden" id="seller_name" name="id_seller" value="{$id_seller}" />

						{if isset($id_store)}
							<input type="hidden" name="id_store" id="id_store" value="{$id_store}">
						{/if}
						<div class="row">
							<div class="col-sm-12">
								<input id="pac-input" class="controls" type="text" value="{if isset($id_store)}{$store.map_address_text}{/if}" placeholder="{l s='Enter location' mod='mpstorelocator'}">
								<input type="button" value="{l s='Search' mod='mpstorelocator'}" id="btn_store_search" class="btn btn-primary controls">
								<div id="map-canvas"></div>
							</div>
							<div class="col-md-12">
								<div class="tabs wk-tabs-panel wk-margin-top-20">
									<ul class="nav nav-tabs">
										<li class="nav-item">
											<a class="nav-link {if isset($activeTab) && ($activeTab =='' || $activeTab == 'mp_store_detail')}active{/if}" href="#mp_store_detail" data-toggle="tab">
												<i class="material-icons">&#xE0C8;</i>
												{l s='Store Details' mod='mpstorelocator'}
											</a>
										</li>

										<li class="nav-item">
											<a class="nav-link {if isset($activeTab) && $activeTab == 'mp_store_pick_up_detail'}active{/if}" href="#mp_store_pick_up_detail" data-toggle="tab">
												<i class="material-icons">&#xE8D1;</i>
												{l s='Store pick up Details' mod='mpstorelocator'}
											</a>
										</li>
									</ul>
									<div class="tab-content panel collapse in clearfix">
										<div class="tab-pane {if isset($activeTab) && ($activeTab =='' || $activeTab == 'mp_store_detail')}active{/if} clearfix" id="mp_store_detail">
											<div class="col-sm-12 addStoreFormCont">
												<div class="form-group row">
													<label for="sellername" class="col-sm-4 control-label">{l s='Seller :' mod='mpstorelocator'}</label>
													<div class="col-sm-8">
														<label>{$seller_name}</label>
													</div>
												</div>

												<div class="form-group row">
													<label for="name" class="col-sm-4 control-label required">{l s='Store Name :' mod='mpstorelocator'}</label>
													<div class="col-sm-8">
														<input type="text" class="form-control" name="shop_name" id="shop_name" value="{if isset($id_store)}{$store.name|escape:'htmlall':'UTF-8'}{else}{if isset($smarty.post.shop_name)}{$smarty.post.shop_name}{/if}{/if}">
													</div>
												</div>

												<div class="form-group row">
													<label for="email" class="col-sm-4 control-label">{l s='Email' mod='mpstorelocator'}</label>
													<div class="col-sm-8">
														<input type="text" class="form-control" name="email" id="email" value="{if isset($store)}{$store.email}{else}{if isset($smarty.post.email)}{$smarty.post.email}{/if}{/if}"/>
													</div>
												</div>

												<div class="form-group row">
													<label for="address1" class="col-sm-4 control-label required">{l s='Address1 :' mod='mpstorelocator'}</label>
													<div class="col-sm-8">
														<input type="text" class="form-control" name="address1" id="address1" value="{if isset($id_store)}{$store.address1}{else}{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}{/if}">
													</div>
												</div>
												<div class="form-group row">
													<label for="address2" class="col-sm-4 control-label">{l s='Address2 :' mod='mpstorelocator'}</label>
													<div class="col-sm-8">
														<input type="text" class="form-control" name="address2" id="address2" value="{if isset($id_store)}{$store.address2}{else}{if isset($smarty.post.address2)}{$smarty.post.address2}{/if}{/if}">
													</div>
												</div>

												<div class="form-group row">
													<label for="name" class="col-sm-4 control-label required">{l s='City :' mod='mpstorelocator'}</label>
													<div class="col-sm-8">
														<input type="text" class="form-control" name="city_name" id="city_name" value="{if isset($id_store)}{$store.city_name}{else}{if isset($smarty.post.city_name)}{$smarty.post.city_name}{/if}{/if}">
													</div>
												</div>

												<div class="form-group row">
													<label for="country" class="col-sm-4 control-label required">{l s='Country :' mod='mpstorelocator'}</label>
													<div class="col-sm-8">
														<select  name="countries" id="countries" class="form-control">
															<option value="">{l s='Select' mod='mpstorelocator'}</option>
															{foreach $countries as $country}
																<option value="{$country.id_country}" {if isset($id_store)}{if $store.country_id == $country.id_country}Selected="selected"{/if}{/if}>{$country.name}</option>
															{/foreach}
														</select>
													</div>
												</div>

												<div class="form-group row country_states_div">
													<label for="country" class="col-sm-4 control-label">{l s='State :' mod='mpstorelocator'}</label>
													<div class="col-sm-8">
														<select  name="state" id="state" class="form-control">
															<option value="">{l s='Select' mod='mpstorelocator'}</option>
														</select>
													</div>
												</div>

												<div class="form-group row">
													<label for="zipcode" class="col-sm-4 control-label required">{l s='Zip/Postal Code :' mod='mpstorelocator'}</label>
													<div class="col-sm-8">
														<input type="text" class="form-control" name="zip_code" id="zip_code" maxlength="12" value="{if isset($id_store)}{$store.zip_code}{else}{if isset($smarty.post.zip_code)}{$smarty.post.zip_code}{/if}{/if}">
													</div>
												</div>

												<div class="form-group row">
													<label for="zipcode" class="col-sm-4 control-label">{l s='Phone :' mod='mpstorelocator'}</label>
													<div class="col-sm-8">
														<input type="text" class="form-control" name="phone" id="phone" value="{if isset($id_store)}{$store.phone}{else}{if isset($smarty.post.phone)}{$smarty.post.phone}{/if}{/if}">
													</div>
												</div>

												<div class="form-group row">
													<label for="fax" class="col-sm-4 control-label">{l s='Fax' mod='mpstorelocator'}</label>
													<div class="col-sm-8">
														<input type="text" class="form-control" name="fax" id="fax" value="{if isset($store)}{$store.fax}{else}{if isset($smarty.post.fax)}{$smarty.post.fax}{/if}{/if}">
													</div>
												</div>
												{if $manage_status}
													<div class="form-group row">
														<label for="status" class="col-sm-4 control-label">{l s='Status :' mod='mpstorelocator'}</label>
														<div class="col-sm-8">
															<label class="radio-inline">
																<span class="pull-left">
																	<input type="radio" name="store_status" id="store_status" value="1" {if isset($id_store)}{if $store.active == 1}checked="checked"{/if}{/if}>
																</span>
																<span class="pull-left" style="margin: 5px;">{l s='Active' mod='mpstorelocator'}</span>
															</label>
															<label class="radio-inline">
																<span class="pull-left">
																	<input type="radio" name="store_status" id="store_status" value="0" {if isset($id_store)}{if $store.active == 0}checked="checked"{/if}{else}checked="checked"{/if}>
																</span>
																<span class="pull-left" style="margin: 5px;">{l s='Inactive' mod='mpstorelocator'}</span>
															</label>
														</div>
													</div>
												{/if}

												{if isset($id_store)}
													<div class="form-group row">
														<label class="col-sm-4 control-label"></label>
														<div class="col-sm-8">
															<div>
																{if isset($img_exist)}
																	<p>
																		<img class="img-thumbnail" src="{$modules_dir}mpstorelocator/views/img/store_logo/{$store.id}.jpg" title="{$store.name|escape:'htmlall':'UTF-8'}" alt="{l s='Image' mod='mpstorelocator'}"/>
																	</p>
																	{* <p>
																		<a class="btn btn-default delete_store_logo" href="{$link->getModuleLink('mpstorelocator', 'addstore', ['id_store' => $store.id, 'id_delete_logo' => $store.id])}"><i class="material-icons">&#xE872;</i> {l s='Delete' mod='mpstorelocator'}</a>
																	</p> *}
																{else}
																	<p>
																		<img class="img-thumbnail" src="{$modules_dir}mpstorelocator/views/img/store_logo/default.jpg" title="{$store.name|escape:'htmlall':'UTF-8'}" alt="{l s='Image' mod='mpstorelocator'}"/>
																	</p>
																{/if}
															</div>
														</div>
													</div>
												{/if}

												<div class="form-group row">
													<label for="storelogo" class="col-sm-4 control-label">{l s='Store Logo :' mod='mpstorelocator'}</label>
													<div class="col-sm-8">
														<input type="file" name="store_logo" id="store_logo"/>
														<p class="help-block">{l s='Image maximum size must be 800 x 800 px' mod='mpstorelocator'}</p>
													</div>
												</div>

												<div class="form-group row">
													<label for="storeproducts" class="col-sm-4 control-label">{l s='Store Products :' mod='mpstorelocator'}</label>
													<div class="col-sm-8">
														{if isset($mp_products)}
															<select name="store_products[]" id="mp_store_products" class="form-control" multiple="multiple">
																{foreach $mp_products as $product}
																	<option value="{$product.id_product}"
																	{if isset($id_store)}{if !empty($store.products)}
																		{foreach $store.products as $p}
																			{if $p.id_product == $product.id_product}
																				Selected="selected"
																			{/if}
																		{/foreach}
																	{/if}{/if}
																	>{$product.product_name}</option>
																{/foreach}
															</select>
															<p class="help-block">{l s='Select the products located on this store.' mod='mpstorelocator'}</p>
														{else}
															<p class="form-control-static">{l s='No product(s) found' mod='mpstorelocator'}</p>
														{/if}
													</div>
												</div>
											</div>
										</div>
										<div class="tab-pane {if isset($activeTab) && $activeTab == 'mp_store_pick_up_detail'}active{/if} clearfix" id="mp_store_pick_up_detail">
											<div class="col-sm-12">
												<div class="form-group clearfix">
													<label for="status" class="col-sm-4 control-label">{l s='Apply Store pick up' mod='mpstorelocator'}</label>
													<div class="col-lg-8">
														<span class="switch prestashop-switch fixed-width-lg">
															<input type="radio" value="1" id="store_pickup_available_on" name="store_pickup_available" {if isset($smarty.post.store_pickup_available)}{if $smarty.post.store_pickup_available == 1} checked="checked"{/if}{else}{if isset($store)}{if $store.store_pickup_available == 1} checked="checked"{/if}{/if}{/if}>
															<label for="store_pickup_available_on">{l s='Yes' mod='mpstorelocator'}</label>
															<input type="radio" value="0" id="store_pickup_available_off" name="store_pickup_available" {if isset($smarty.post.store_pickup_available)}{if $smarty.post.store_pickup_available == 0} checked="checked"{/if}{else}{if isset($store)}{if $store.store_pickup_available == 0} checked="checked"{/if}{else}checked="checked"{/if}{/if}>
															<label for="store_pickup_available_off">{l s='No' mod='mpstorelocator'}</label>
															<a class="slide-button btn"></a>
														</span>
													</div>
												</div>
												<div class="form-group clearfix" id="wk_store_pickup_time_slot">
													<label for="storelogo" class="col-sm-4 control-label">{l s='Pick Up Slot' mod='mpstorelocator'}</label>
													<div class="col-sm-4">
														<div class="table-responsive">
															<table class="table table-bordered table-striped table-highlight">
																<thead>
																	<th>{l s='Start Time' mod='mpstorelocator'}</th>
																	<th>{l s='End Time' mod='mpstorelocator'}</th>
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
												<div class="form-group clearfix row">
													<label for="storetiming" class="col-sm-4 control-label">{l s='Store Timing :' mod='mpstorelocator'}</label>
													<div class="col-sm-6">
														<div class="table-responsive">
															<table class="table table-bordered table-striped table-highlight">
																<thead>
																	<th>{l s='Days' mod='mpstorelocator'}</th>
																	<th>{l s='Opening Time' mod='mpstorelocator'}</th>
																	<th>{l s='Closing Time' mod='mpstorelocator'}</th>
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
																				<div class="">{l s={$weekdays[$i]} mod='mpstorelocator'}</div>
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
												{if isset($storePaymentOptions) && $storePaymentOptions}
												<div class="form-group">
													<label class="control-label col-lg-4">
														<span class="" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='' mod='mpstorelocator'}">{l s='Select Store Payment Option' mod='mpstorelocator'}</span>
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
																				{l s='Select Payment' mod='mpstorelocator'}
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
				<div class="wk-mp-right-column wk_border_top_none">
					<div class="form-group row">
						<div class="col-md-12">
							<div class="col-xs-4 col-sm-4 col-md-6">
								<a href="{url entity='module' name='mpstorelocator' controller='storelist'}" class="btn wk_btn_cancel wk_btn_extra">
									{l s='Cancel' mod='mpstorelocator'}
								</a>
							</div>
							<div class="col-xs-8 col-sm-8 col-md-6 wk_text_right" id="wk-store-submit" data-action="{l s='Save' mod='mpstorelocator'}">
								<img class="wk_product_loader" src="{$module_dir}marketplace/views/img/loader.gif" width="25" />
								<button type="submit" id="submit_and_stay_store" name="submit_and_stay_store" class="btn btn-success wk_btn_extra form-control-submit">
									<span>{l s='Save & Stay' mod='mpstorelocator'}</span>
								</button>
								<button type="submit" id="submit_store" name="submit_store" class="btn btn-success wk_btn_extra form-control-submit">
									<span>{l s='Save' mod='mpstorelocator'}</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div
{/block}
