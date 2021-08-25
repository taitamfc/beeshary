{*
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{capture assign=priceDisplayPrecisionFormat}{'%.'|cat:$smarty.const._PS_PRICE_DISPLAY_PRECISION_|cat:'f'}{/capture}
<hr>
<div class="wkslotprice">
	<h4>{l s='Price Slots' mod='mpslotpricing'}</h4>
	<div class="alert alert-info">
		{l s='You can set specific prices for customer(s) belonging to different groups, different countries, etc.' mod='mpslotpricing'}
	</div>

	<div class="form-group">
		<div id="slotbutton">
			<a class="btn {if isset($controller) && ($controller == 'addproduct' || $controller == 'updateproduct')} btn-primary-outline btn-sm {else} btn-default {/if}" href="#" id="show_specific_price">
				{if isset($controller)} {if $controller == 'addproduct' || $controller == 'updateproduct'} <i class="material-icons">&#xE145;</i> {else if $controller == 'AdminSellerProductDetail'}<i class="icon-plus-sign"></i> {/if} {/if} {l s='Add slot price' mod='mpslotpricing'}
			</a>
			<a class="btn {if isset($controller) && ($controller == 'addproduct' || $controller == 'updateproduct')} btn-primary-outline btn-sm {else} btn-default {/if}" href="#" id="hide_specific_price" style="display:none">
				{if isset($controller)}
					{if $controller == 'addproduct' || $controller == 'updateproduct'} 
						<i class="material-icons">&#xE14C;</i>
					{else if $controller == 'AdminSellerProductDetail'}
						<i class="icon-remove text-danger"></i>
					{/if}
				{/if}
				{l s='Cancel slot price' mod='mpslotpricing'}
			</a>
		</div>
	</div>

	<div id="add_specific_price" class="clearfix" style="display:none;">
		<input type="hidden" id="showTpl" name="showTpl" value="">
		<input type="hidden" name="mp_product_id" value="{if isset($mp_product_id) && $mp_product_id}{$mp_product_id}{/if}" id="mp_product_id">
		<div class="form-group clearfix">
			<label class="control-label col-lg-2 wklabel" for="">{l s='For' mod='mpslotpricing'}</label>
			<div class="col-lg-9">
				<div class="row">
					<div class="col-lg-3">
						<select name="sp_id_currency" id="spm_currency_0" onchange="changeCurrencySpecificPrice(0);" class="wkinput">
							<option value="0">{l s='All currencies' mod='mpslotpricing'}</option>
							{foreach from=$currencies item=curr}
							<option value="{$curr.id_currency}">
								{$curr.name|htmlentitiesUTF8}
							</option>
							{/foreach}
						</select>
					</div>
					<div class="col-lg-3">
						<select name="sp_id_country" id="sp_id_country" class="wkinput">
							<option value="0">{l s='All countries' mod='mpslotpricing'}</option>
							{foreach from=$countries item=country}
							<option value="{$country.id_country}">
								{$country.name|htmlentitiesUTF8}
							</option>
							{/foreach}
						</select>
					</div>
					<div class="col-lg-3">
						<select name="sp_id_group" id="sp_id_group" class="wkinput">
							<option value="0">{l s='All groups' mod='mpslotpricing'}</option>
							{foreach from=$groups item=group}
								<option value="{$group.id_group}">{$group.name}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group clearfix">
			<label class="control-label col-lg-2 wklabel" for="customer">{l s='Customer' mod='mpslotpricing'}</label>
			<div class="col-lg-4">
				<input type="hidden" name="sp_id_customer" id="id_customer" value="0" />
				<div class="input-group">
					<input type="text" name="customer" value="" id="wkslotcustomer" autocomplete="off"  class="form-control wk_text_field" placeholder="{l s='All customers' mod='mpslotpricing'}"/>
					<span class="input-group-addon"><i id="customerLoader" class="icon-refresh icon-spin" style="display: none;"></i> {if isset($controller) && $controller == 'AdminSellerProductDetail'} <i class="icon-search"></i> {else} <i class="material-icons">&#xE8B6;</i> {/if} </span>
				</div>
			</div>
		</div>
		<div class="form-group clearfix">
			<div class="col-lg-10 col-lg-offset-2">
				<div id="customers"></div>
			</div>
		</div>
		<div class="form-group clearfix">
			<label class="control-label col-lg-2 wklabel" for="sp_from">{l s='Available' mod='mpslotpricing'}</label>
			<div class="col-lg-9">
				<div class="row">
					<div class="col-lg-4 wkslotdate">
						<div class="input-group">
							<span class="input-group-addon">{l s='from' mod='mpslotpricing'}</span>
							<input type="text" name="sp_from" class="form-control wk_text_field" value="" style="text-align: center" id="sp_from" />
							<span class="input-group-addon">{if isset($controller) && $controller == 'AdminSellerProductDetail'} <i class="icon-calendar-empty"></i> {else} <i class="material-icons">&#xE916;</i> {/if}</span>
						</div>
					</div>
					<div class="col-lg-4 wkslotdate">
						<div class="input-group">
							<span class="input-group-addon">{l s='to' mod='mpslotpricing'}</span>
							<input type="text" name="sp_to" class="form-control wk_text_field" value="" style="text-align: center" id="sp_to" />
							<span class="input-group-addon">{if isset($controller) && $controller == 'AdminSellerProductDetail'} <i class="icon-calendar-empty"></i> {else} <i class="material-icons">&#xE916;</i> {/if}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group clearfix">
			<label class="control-label col-lg-2 wklabel" for="sp_from_quantity">
				{l s='Starting at' mod='mpslotpricing'}
			</label>
			<div class="col-lg-4">
				<div class="input-group">
					<span class="input-group-addon">{l s='unit' mod='mpslotpricing'}</span>
					<input type="text" name="sp_from_quantity" id="sp_from_quantity" value="1" class="form-control wk_text_field"/>
				</div>
			</div>
		</div>
		<div class="form-group clearfix">
			<label class="control-label col-lg-2 wklabel" for="sp_price">{l s='Product price' mod='mpslotpricing'}
				{if $country_display_tax_label}
					{l s='(tax excl.)' mod='mpslotpricing'}
				{/if}
			</label>
			<div class="col-lg-9">
				<div class="row">
					<div class="col-lg-4">
						<div class="input-group">
							<span class="input-group-addon">{$defaultCurrencySign}</span>
							<input type="text" disabled="disabled" name="sp_price" id="sp_price" value="{if isset($product_detail)}{$product_detail.price|string_format:$priceDisplayPrecisionFormat}{/if}" class="form-control wk_text_field"/>
						</div>
						{*{if isset($updateProduct) && $updateProduct == 1}
							<input type="hidden" name="leave_bprice" value="1"/>
						{/if}*}
						<p class="checkbox">
							<label for="leave_bprice">{l s='Leave base price:' mod='mpslotpricing'}</label>
							<input type="checkbox" id="leave_bprice" name="leave_bprice"  value="" checked="checked"/>
						</p>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group clearfix">
			<label class="control-label col-lg-2 wklabel" for="sp_reduction">{l s='Apply a discount of' mod='mpslotpricing'}</label>
			<div class="col-lg-6">
				<div class="row">
					<div class="col-lg-3">
						<input type="text" name="sp_reduction" id="sp_reduction" value="0.00" class="form-control wk_text_field"/>
					</div>
					<div class="col-lg-4">
						<select name="sp_reduction_type" id="sp_reduction_type" class="wkinput">
							<option value="amount" selected="selected">
								{l s='Amount' mod='mpslotpricing'}
							</option>
							<option value="percentage">{l s='%' mod='mpslotpricing'}</option>
						</select>
					</div>
					<div class="col-lg-4" id="sp_reduction_tax">
						<select name="sp_reduction_tax" class="wkinput">
							<option value="0">{l s='Tax excluded' mod='mpslotpricing'}</option>
							<option value="1" selected="selected">{l s='Tax included' mod='mpslotpricing'}</option>
						</select>
					</div>
				</div>
			</div>
		</div>
		{if isset($updateProduct) && $updateProduct == 1}
			<div class="slot-button">
				<a id="add_btn" class="btn btn-primary" href="#" {if isset($controller) && $controller == 'AdminSellerProductDetail'}style="padding: 8px 32px;"{/if}>{l s='Add' mod='mpslotpricing'}</a>
			</div>
		{/if}
	</div>
</div>
{if isset($updateProduct) && $updateProduct == 1}
	<div id="slot_listing" class="table-responsive">
		<table class="data-table table" id="my-orders-table">
			<thead>
				<tr>
					<th>{l s='#' mod='mpslotpricing'}</th>
					<th>{l s='Currency' mod='mpslotpricing'}</th>
					<th>{l s='Country' mod='mpslotpricing'}</th>
					<th>{l s='Group' mod='mpslotpricing'}</th>
					<th>{l s='Customer' mod='mpslotpricing'}</th>
					<th>{l s='Fixed price' mod='mpslotpricing'}</th>
					<th>{l s='Impact' mod='mpslotpricing'}</th>
					<th>{l s='Period' mod='mpslotpricing'}</th>
					<th>{l s='From (quantity)' mod='mpslotpricing'}</th>
					<th>{l s='Action' mod='mpslotpricing'}</th>
				</tr>
			</thead>
			<tbody>
				{if isset($price_slots)}
					{foreach $price_slots as $slots}
						<tr class="even" id="slotcontent{$slots['id']}">
							<td>{$slots['id']}</td>
							<td>{$slots['id_currency']}</td>
							<td>{$slots['id_country']}</td>
							<td>{$slots['id_group']}</td>
							<td>{$slots['id_customer']}</td>
							<td>{$slots['price']}</td>
							<td>{$slots['impact']}</td>
							<td>{$slots['period'] nofilter}</td>
							<td class="text-center">{$slots['from_quantity']}</td>
							<td>
								<a id="{$slots['id']}" class="btn btn-default" name="slot_delete_link" href=""><i class="material-icons">&#xE872;</i></a>
							</td>
						</tr>
					{/foreach}
				{else}
					<tr class="odd">
						<td colspan="10" style="text-align: center;">
							{l s='No data found' mod='mpslotpricing'}
						</td>
					</tr>
				{/if}
			</tbody>
		</table>
	</div>
{/if}
<div class="wkslotprice_loader">
    <img src="{$modules_dir}mpslotpricing/views/img/loading2.gif" class="wkslotprice-loading-img" width="60px;" />
</div>
