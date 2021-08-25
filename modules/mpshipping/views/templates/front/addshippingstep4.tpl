{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{block name='content'}
<div id="newbody"></div>
<div id="impact_price_block">
	{include file='module:mpshipping/views/templates/front/addimpactprice.tpl'}
</div>
{if isset($mp_shipping_id)}
	<input type="hidden" name="mpshipping_id" value="{$mp_shipping_id}">
{/if}
{if isset($addmpshipping_success)}
	{if $addmpshipping_success == 1}
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Carrier added successfully' mod='mpshipping'}
		</div>
	{/if}
{/if}
{if isset($deleteimpact)}
	{if $deleteimpact == 1}
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Impact price deleted successfully' mod='mpshipping'}
		</div>
	{/if}
{/if}
<div class="wk-mp-block">
	{hook h="displayMpMenu"}
	<div class="wk-mp-content">
		<div class="page-title" style="background-color:{$title_bg_color};">
			<span style="color:{$title_text_color};">
			{if isset($updateimpact)}
				{l s='Update Impact Price' mod='mpshipping'}
			{else}
				{l s='Add Impact Price' mod='mpshipping'}
			{/if}
			{if isset($mpshipping_name)}
				- {$mpshipping_name}
			{/if}
			</span>
		</div>
		<div class="wk-mp-right-column" style="border: none;">
			<div class="shipping_heading">
				<div class="right_links">
					<div class="home_link">
						<a class="btn btn-primary btn-sm pull-right" href="{$link->getModuleLink('mpshipping', 'mpshippinglist')}">
							<span>{l s='Carrier list' mod='mpshipping'}</span>
						</a>
					</div>
				</div>
			</div>
			<input type="hidden" name="mpshipping_id" id="mpshipping_id" value="{$mpshipping_id}">
			<input type="hidden" name="step4_shipping_method" value="{$shipping_method}" class="step4_shipping_method" />
			<div class="left full row">
				<div class="left lable">
					{l s='Zone' mod='mpshipping'}
				</div>
				<div class="left input_label">
					<select name="step4_zone" id="step4_zone" class="form-control" style="width:40%;">
						<option value="-1">{l s='Select Zone' mod='mpshipping'}</option>
					{foreach $zones as $zon}
						<option value="{$zon['id_zone']}">{$zon['name']}</option>
					{/foreach}
					</select>
				</div>
			</div>
			<div class="left full" id="country_container" style="display:none;">
				<div class="left full row">
					<div class="left lable">
						{l s='Country' mod='mpshipping'}
					</div>
					<div class="left input_label">
						<select name="step4_country" id="step4_country" class="form-control" style="width:40%;">
							<option value="-1">{l s='Select country' mod='mpshipping'}</option>
						</select>
					</div>
				</div>
				<div class="left full" id="state_container" style="display:none;">
					<div class="left full row">
						<div class="left lable">
							{l s='State' mod='mpshipping'}
						</div>
						<div class="left input_label">
							<select name="step4_state" id="step4_state" class="form-control" style="width:40%;">
								<option value="0">{l s='All state' mod='mpshipping'}</option>
							</select>
						</div>
					</div>
					<div class="left full row" style="text-align:center;">
						{if isset($updateimpact)}
							<input type="button" class="btn btn-primary btn-sm" id="impactprice_button" value="{l s='Click to update impact price' mod='mpshipping'}">
						{else}
							<input type="button" class="btn btn-primary btn-sm" id="impactprice_button" value="{l s='Click to add impact price' mod='mpshipping'}">
						{/if}
					</div>
				</div>
			</div>
			<div class="left full text-center" id="loading_ajax"></div>
		</div>
		<div class="clearfix"></div>

		{if isset($updateimpact)}
		<div class="wk-mp-right-column">
		<div class="box-content" style="margin: 10px;">
			<table class="table table-striped">
			<thead>
				<tr class="first last">
					<th style="width: 10%;">{l s='Id' mod='mpshipping'}</th>
					<th style="width: 20%;">{l s='Zone' mod='mpshipping'}</th>
					<th style="width: 20%;">{l s='Country' mod='mpshipping'}</th>
					<th style="width: 20%;">{l s='State' mod='mpshipping'}</th>
					<th style="width: 20%;">{l s='Impact Price' mod='mpshipping'}</th>
					<th style="width: 20%;">
						{if $shipping_method == 2}
							{l s='Price Range' mod='mpshipping'}
						{else}
							{l s='Weight Range' mod='mpshipping'}
						{/if}
					</th>
					<th style="width: 10%;">{l s='Action' mod='mpshipping'}</th>
				</tr>
			</thead>
			<tbody>
				{if isset($impactprice_arr)}
					{foreach $impactprice_arr as $impactprice}
						<tr class="even">
							<td>{$impactprice.id}</td>
							<td>{$impactprice.id_zone}</td>
							<td>{$impactprice.id_country}</td>
							<td>{$impactprice.id_state}</td>
							<td>{$impactprice.impact_price_display}</td>
							<td>
								{if $shipping_method == 2}
									{$impactprice.price_range}
								{else}
									{$impactprice.weight_range}
								{/if}
							</td>
							<td>
								<a href="{$link->getModuleLink('mpshipping','addmpshipping',['mpshipping_id'=>{$impactprice['mp_shipping_id']}, 'impact_id'=>{$impactprice['id']}, 'addmpshipping_step4'=>1, 'updateimpact' => 1])}" class="delete_shipping" title="{l s='Delete' mod='mpshipping'}">
									<i class="material-icons">&#xE872;</i>
								</a>
							</td>
						</tr>
					{/foreach}
				{else}
					<tr>
						<td colspan="7"><center>{l s='No Impact Price Yet' mod='mpshipping'}</center></td>
					</tr>
				{/if}
			</tbody>
			</table>
		</div>
		</div>
		{/if}
	</div>
</div>
<div class="loading_overlay">
	<img src="{$modules_dir}mpshipping/views/img/loader.gif" class="loading-img"/>
</div>
{/block}
