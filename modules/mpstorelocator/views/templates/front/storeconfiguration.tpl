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
    {if isset($addConfig) && $addConfig}
        <p class="alert alert-info">
            <button data-dismiss="alert" class="close" type="button">×</button>
            {l s='Add store configuration first' mod='mpstorelocator'}
        </p>
    {/if}
	{if isset($smarty.get.success)}
		{if $smarty.get.success == 1}
			<p class="alert alert-success">
                <button data-dismiss="alert" class="close" type="button">×</button>
				{l s='Store configuration updated.' mod='mpstorelocator'}
			</p>
		{/if}
	{/if}

	{if isset($smarty.get.mp_error)}
		{if $smarty.get.mperror == 1}
			<p class="alert alert-danger">
                <button data-dismiss="alert" class="close" type="button">×</button>
				{l s='Invalid Store Configuraton access' mod='mpstorelocator'}
			</p>
		{/if}
	{/if}

	{hook h='displayMpAddProductHeader'}
	<div class="wk-mp-block">
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color};">
				<span style="color:{$title_text_color};">{l s='Store Configuration' mod='mpstorelocator'}</span>
			</div>
            <div class="addStoreCont wk-mp-right-column">
                <p class="wk_text_right">
                    <a title="{l s='Store Location' mod='mpstorelocator'}" href="{$link->getModuleLink('mpstorelocator', 'storelist')}">
                        <button class="btn btn-primary btn-sm" type="button">
                            <i class="material-icons">&#xE0C8;</i>
                            {l s='Store Location' mod='mpstorelocator'}
                        </button>
                    </a>
                </p>
                <form action="{$link->getModuleLink('mpstorelocator', 'storeconfiguration')}" method="post" id="tagform-full" class="form-horizontal" role="form" enctype="multipart/form-data" novalidate>
                    <input type="hidden" value="" id="active_tab" name="active_tab"/>
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
                                <div class="col-xs-12 col-sm-12">
                                    <div class="row">
                                        <div class="form-group clearfix">
                                            <label for="status" class="col-sm-4 control-label">{l s='Set custom Google map marker icon' mod='mpstorelocator'}</label>
                                            <div class="col-lg-8">
                                                <span class="switch prestashop-switch fixed-width-lg">
                                                    <input type="radio" value="1" id="enableCustomMarker_on" name="enableCustomMarker" {if isset($smarty.post.enableCustomMarker)}{if $smarty.post.enableCustomMarker == 1} checked="checked"{/if}{else}{if isset($storeConfiguration)}{if $storeConfiguration.enable_marker == 1} checked="checked"{/if}{/if}{/if}>
                                                    <label for="enableCustomMarker_on">{l s='Yes' mod='mpstorelocator'}</label>
                                                    <input type="radio" value="0" id="enableCustomMarker_off" name="enableCustomMarker" {if isset($smarty.post.enableCustomMarker)}{if $smarty.post.enableCustomMarker == 0} checked="checked"{/if}{else}{if isset($storeConfiguration)}{if $storeConfiguration.enable_marker == 0} checked="checked"{/if}{else}checked="checked"{/if}{/if}>
                                                    <label for="enableCustomMarker_off">{l s='No' mod='mpstorelocator'}</label>
                                                    <a class="slide-button btn"></a>
                                                </span>
                                            </div>
                                        </div>
                                        {if isset($storeConfiguration)}
                                            {if isset($storeImage)}
                                                <div class="form-group row mp_marker_enable">
                                                    <label class="col-sm-4 control-label"></label>
                                                    <div class="col-sm-8">
                                                        <div>
                                                            <p>
                                                                <img class="img-thumbnail" src="{$modules_dir}mpstorelocator/views/img/mp_store_marker_icon/{$storeImage}" title="" alt="{l s='Image' mod='mpstorelocator'}"/>
                                                            </p>
                                                        </div>
                                                    </div>
                                            </div>
                                            {/if}
                                        {/if}
                                        <div class="form-group row mp_marker_enable">
                                            <label for="storelogo" class="col-sm-4 control-label">{l s='Marker Icon :' mod='mpstorelocator'}</label>
                                            <div class="col-sm-8">
                                                <input type="file" name="mp_marker_icon" id="mp_marker_icon"/>
                                                <p class="wk-help-block">{l s='Image maximum size must be 27 x 42 px' mod='mpstorelocator'}</p>
                                            </div>
                                        </div>
                                        {if isset($MP_STORE_PICK_UP_PAYMENT) && $MP_STORE_PICK_UP_PAYMENT}
                                            <div class="form-group clearfix">
                                                <label for="status" class="col-sm-4 control-label">{l s='Enable Store Pick Up Payment' mod='mpstorelocator'}</label>
                                                <div class="col-lg-8">
                                                    <span class="switch prestashop-switch fixed-width-lg">
                                                        <input type="radio" value="1" id="enableStorePayment_on" name="enableStorePayment" {if isset($smarty.post.enableStorePayment)}{if $smarty.post.enableStorePayment == 1} checked="checked"{/if}{else}{if isset($storeConfiguration)}{if $storeConfiguration.store_payment == 1} checked="checked"{/if}{/if}{/if}>
                                                        <label for="enableStorePayment_on">{l s='Yes' mod='mpstorelocator'}</label>
                                                        <input type="radio" value="0" id="enableStorePayment_off" name="enableStorePayment" {if isset($smarty.post.enableStorePayment)}{if $smarty.post.enableStorePayment == 0} checked="checked"{/if}{else}{if isset($storeConfiguration)}{if $storeConfiguration.store_payment == 0} checked="checked"{/if}{else}checked="checked"{/if}{/if}>
                                                        <label for="enableStorePayment_off">{l s='No' mod='mpstorelocator'}</label>
                                                        <a class="slide-button btn"></a>
                                                    </span>
                                                </div>
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane clearfix {if isset($activeTab) && $activeTab == 'mp_store_pick_up_detail'}active{/if}" id="mp_store_pick_up_detail">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="row">
                                        <input type="hidden" name="mp_id_seller" value="{if isset($mpIdSeller)}{$mpIdSeller}{/if}"/>
                                        <input type="hidden" name="mp_id_store_configuration" value="{if isset($storeConfiguration)}{$storeConfiguration.id_store_configuration}{/if}"/>

                                        <div class="form-group clearfix">
                                            <label for="status" class="col-sm-4 control-label">{l s='Send Order Notification to store' mod='mpstorelocator'}</label>
                                            <div class="col-lg-8">
                                                <span class="switch prestashop-switch fixed-width-lg">
                                                    <input type="radio" value="1" id="enableStoreNotification_on" name="enableStoreNotification" {if isset($smarty.post.enableStoreNotification)}{if $smarty.post.enableStoreNotification == 1} checked="checked"{/if}{else}{if isset($storeConfiguration)}{if $storeConfiguration.enable_store_notification == 1} checked="checked"{/if}{/if}{/if}>
                                                    <label for="enableStoreNotification_on">{l s='Yes' mod='mpstorelocator'}</label>
                                                    <input type="radio" value="0" id="enableStoreNotification_off" name="enableStoreNotification" {if isset($smarty.post.enableStoreNotification)}{if $smarty.post.enableStoreNotification == 0} checked="checked"{/if}{else}{if isset($storeConfiguration)}{if $storeConfiguration.enable_store_notification == 0} checked="checked"{/if}{else}checked="checked"{/if}{/if}>
                                                    <label for="enableStoreNotification_off">{l s='No' mod='mpstorelocator'}</label>
                                                    <a class="slide-button btn"></a>
                                                </span>
                                            </div>
                                        </div>

                                        {if isset($MP_STORE_COUNTRY_ENABLE) && $MP_STORE_COUNTRY_ENABLE}
                                            <div class="form-group clearfix">
                                                <label for="status" class="col-sm-4 control-label">{l s='Enable Country restriction' mod='mpstorelocator'}</label>
                                                <div class="col-lg-8">
                                                    <span class="switch prestashop-switch fixed-width-lg">
                                                        <input type="radio" value="1" id="enableCountryRestriction_on" name="enableCountryRestriction" {if isset($smarty.post.enableCountryRestriction)}{if $smarty.post.enableCountryRestriction == 1} checked="checked"{/if}{else}{if isset($storeConfiguration)}{if $storeConfiguration.enable_country == 1} checked="checked"{/if}{/if}{/if}>
                                                        <label for="enableCountryRestriction_on">{l s='Yes' mod='mpstorelocator'}</label>
                                                        <input type="radio" value="0" id="enableCountryRestriction_off" name="enableCountryRestriction" {if isset($smarty.post.enableCountryRestriction)}{if $smarty.post.enableCountryRestriction == 0} checked="checked"{/if}{else}{if isset($storeConfiguration)}{if $storeConfiguration.enable_country == 0} checked="checked"{/if}{else}checked="checked"{/if}{/if}>
                                                        <label for="enableCountryRestriction_off">{l s='No' mod='mpstorelocator'}</label>
                                                        <a class="slide-button btn"></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group row mp_store_country_restrict">
                                                <label for="storelogo" class="col-sm-4 control-label">{l s='Set Country :' mod='mpstorelocator'}</label>
                                                <div class="col-sm-8">
                                                    <select  name="mp_countries[]" id="mp_countries[]" class="form-control mp-multiselect-countries" multiple="true">
                                                        {foreach $countries as $idCountry => $country}
                                                            <option value="{$idCountry}" {if isset($storeConfiguration) && $storeConfiguration.countries && in_array($idCountry, $storeConfiguration.countries)}Selected="selected"{/if}>{$country}</option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                            </div>
                                        {/if}
                                        {if isset($MP_STORE_PICKUP_DATE) && $MP_STORE_PICKUP_DATE}
                                            <div class="form-group clearfix">
                                                <label for="status" class="col-sm-4 control-label">{l s='Enable Date selection' mod='mpstorelocator'}</label>
                                                <div class="col-lg-8">
                                                    <span class="switch prestashop-switch fixed-width-lg">
                                                        <input type="radio" value="1" id="enableDateSelection_on" name="enableDateSelection" {if isset($smarty.post.enableDateSelection)}{if $smarty.post.enableDateSelection == 1} checked="checked"{/if}{else}{if isset($storeConfiguration)}{if $storeConfiguration.enable_date == 1} checked="checked"{/if}{/if}{/if}>
                                                        <label for="enableDateSelection_on">{l s='Yes' mod='mpstorelocator'}</label>
                                                        <input type="radio" value="0" id="enableDateSelection_off" name="enableDateSelection" {if isset($smarty.post.enableDateSelection)}{if $smarty.post.enableDateSelection == 0} checked="checked"{/if}{else}{if isset($storeConfiguration)}{if $storeConfiguration.enable_date == 0} checked="checked"{/if}{else}checked="checked"{/if}{/if}>
                                                        <label for="enableDateSelection_off">{l s='No' mod='mpstorelocator'}</label>
                                                        <a class="slide-button btn"></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group row mp_store_date_enable">
                                                <label for="mp_minimum_days" class="col-sm-4 control-label">{l s='Minimum Days :' mod='mpstorelocator'}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="mp_minimum_days" id="mp_minimum_days" maxlength="12" value="{if isset($storeConfiguration)}{$storeConfiguration.minimum_days}{else}{if isset($smarty.post.mp_minimum_days)}{$smarty.post.mp_minimum_days}{/if}{/if}">
                                                    <p class="wk-help-block">{l s='The minimum open days what the customer needs to wait before can pickup the package.' mod=''}</p>
                                                </div>
                                            </div>
                                            <div class="form-group row mp_store_date_enable">
                                                <label for="mp_maximum_days" class="col-sm-4 control-label">{l s='Maximum Days :' mod='mpstorelocator'}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="mp_maximum_days" id="mp_maximum_days" maxlength="12" value="{if isset($storeConfiguration)}{$storeConfiguration.maximum_days}{else}{if isset($smarty.post.mp_maximum_days)}{$smarty.post.mp_maximum_days}{/if}{/if}">
                                                    <p class="wk-help-block">{l s='The maximum open days until the customer can pickup the package.' mod=''}</p>
                                                </div>
                                            </div>
                                            <div class="form-group row mp_store_date_enable">
                                                <label for="mp_max_pickup" class="col-sm-4 control-label">{l s='Max Pick ups :' mod='mpstorelocator'}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="mp_max_pickup" id="mp_max_pickup" maxlength="12" value="{if isset($storeConfiguration)}{$storeConfiguration.max_pick_ups}{else}{if isset($smarty.post.mp_max_pickup)}{$smarty.post.mp_max_pickup}{/if}{/if}">
                                                    <p class="wk-help-block">{l s='Maximum number of customers allowed for pickup per day. If 0 then no restriction on max pickups' mod=''}</p>
                                                </div>
                                            </div>

                                            <div class="form-group clearfix mp_store_date_enable">
                                                <label for="status" class="col-sm-4 control-label">{l s='Enable Time selection' mod='mpstorelocator'}</label>
                                                <div class="col-lg-8">
                                                    <span class="switch prestashop-switch fixed-width-lg">
                                                        <input type="radio" value="1" id="enableTimeSelection_on" name="enableTimeSelection" {if isset($smarty.post.enableTimeSelection)}{if $smarty.post.enableTimeSelection == 1} checked="checked"{/if}{else}{if isset($storeConfiguration)}{if $storeConfiguration.enable_time == 1} checked="checked"{/if}{/if}{/if}>
                                                        <label for="enableTimeSelection_on">{l s='Yes' mod='mpstorelocator'}</label>
                                                        <input type="radio" value="0" id="enableTimeSelection_off" name="enableTimeSelection" {if isset($smarty.post.enableTimeSelection)}{if $smarty.post.enableTimeSelection == 0} checked="checked"{/if}{else}{if isset($storeConfiguration)}{if $storeConfiguration.enable_time == 0} checked="checked"{/if}{else}checked="checked"{/if}{/if}>
                                                        <label for="enableTimeSelection_off">{l s='No' mod='mpstorelocator'}</label>
                                                        <a class="slide-button btn"></a>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="form-group row mp_store_date_enable mp_store_time_enable">
                                                <label for="mp_minimum_hours" class="col-sm-4 control-label">{l s='Minimum hours :' mod='mpstorelocator'}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="mp_minimum_hours" id="mp_minimum_hours" maxlength="12" value="{if isset($storeConfiguration)}{$storeConfiguration.minimum_hours}{else}{if isset($smarty.post.mp_minimum_hours)}{$smarty.post.mp_minimum_hours}{/if}{/if}">
                                                    <p class="wk-help-block">{l s='The minimum hours what the customer needs to wait before can pickup the package.' mod=''}</p>
                                                </div>
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wk-mp-right-column wk_border_top_none">
                        <div class="form-group row">
                            <div class="col-md-12">
                                    <div class="col-xs-12 col-sm-12 col-md-12 wk_text_right" id="wk-store-submit" data-action="{l s='Save' mod='mpstorelocator'}">
                                    <img class="wk_product_loader" src="{$module_dir}marketplace/views/img/loader.gif" width="25" />
                                    <button type="submit" id="btnSubmitStoreConfig" name="btnSubmitStoreConfig" class="btn btn-success wk_btn_extra form-control-submit">
                                        <span>{l s='Save' mod='mpstorelocator'}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
		</div>
	</div
{/block}
