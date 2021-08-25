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

<div class="form-group wk-add-margin">
	<label class="control-label col-lg-3 wk-add-margin">
		{l s='Add Handling Cost' mod='mpshipping'}
	</label>
	<div class="control-label col-lg-3 wk-textalign">
		<span class="switch prestashop-switch fixed-width-lg">
			<input id="shipping_handling_on" type="radio" value="1" name="shipping_handling" >
			<label  for="shipping_handling_on">{l s='Yes' mod='mpshipping'}</label>
			<input id="shipping_handling_off" type="radio" value="0" name="shipping_handling" {if empty($mp_shipping_id)}checked="checked"{/if}>
			<label for="shipping_handling_off">{l s='No' mod='mpshipping'}</label>
			<a class="slide-button btn"></a>
		</span>
	</div>
	<div style="clear:both;"></div>
</div>
<div class="form-group wk-add-margin" id="shipping_handling_charge" {if empty($mp_shipping_id)}style="display:none;"{/if}>
	<label class="control-label col-lg-3">
		{l s='Handling Charge' mod='mpshipping'}
	</label>
	<label class="control-label col-lg-9" style="text-align: left;font-weight: normal !important;">
		{l s='As per admin configuration, handling charge of %1$s%2$s will be applied' sprintf=[$currency_sign, $shipping_handling_charge] mod='mpshipping'}
	</label>
	<div style="clear:both;"></div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		{l s='Free Shipping' mod='mpshipping'}
	</label>
	<div class="control-label col-lg-3 wk-textalign">
		<span class="switch prestashop-switch fixed-width-lg">
			<input type="radio" value="1" id="is_free_on" name="is_free">
			<label for="is_free_on">{l s='Yes' mod='mpshipping'}</label>
			<input type="radio" checked="checked" value="0" id="is_free_off" name="is_free">
			<label for="is_free_off">{l s='No' mod='mpshipping'}</label>
			<a class="slide-button btn"></a>
		</span>
	</div>
	<div style="clear:both;"></div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		{l s='Billing' mod='mpshipping'}
	</label>
	<div class="control-label col-lg-9" style="text-align: left;">
		<input id="billing_price" type="radio" value="2" name="shipping_method">
		<label class="t" for="billing_price">{l s='According to total price' mod='mpshipping'}</label>
		<br/>
		<input id="billing_weight" type="radio" value="1" name="shipping_method">
		<label class="t" for="billing_weight">{l s='According to total weight' mod='mpshipping'}</label>
	</div>
	<div style="clear:both;"></div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		{l s='Tax' mod='mpshipping'}
	</label>
	<div class="col-lg-9">
	  	<select class="form-control wk-dropdown" name="id_tax_rule_group"  >
	  		<option value="0">{l s='No Tax' mod='mpshipping'}</option>
	  		{foreach from=$tax_rules key=k item=range}
	  			<option value="{$range['id_tax_rules_group']}" {if isset($id_tax_rule_group) && $range['id_tax_rules_group']==$id_tax_rule_group}selected{/if}>{$range['name']}</option>
	  		{/foreach}
	  	</select>
  	</div>
  	<div style="clear:both;"></div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Out-of-range behavior occurs when no defined range matches the customer\'s cart (e.g. when the weight of the cart is greater than the highest weight limit defined by the weight ranges).' mod='mpshipping'}">
			{l s='Out-of-range behavior' mod='mpshipping'}
		</span>
	</label>
	<div class="col-lg-9">
	  	<select id="range_behavior"  class="form-control wk-dropdown" name="range_behavior">
	  		<option {if isset($range_behavior) &&  $range_behavior == 0} selected {/if} value="0">{l s='Apply the cost of the highest defined range' mod='mpshipping'}</option>
			<option value="1" {if isset($range_behavior) &&  $range_behavior == 1} selected {/if}>{l s='Disable carrier' mod='mpshipping'}</option>
	  	</select>
  	</div>
  	<div style="clear:both;"></div>
</div>

<div class="left full form-group">
	<div class="left full pricezoneheader">
		{l s='Default Shipping Price according to zone' mod='mpshipping'}
	</div>
</div>

<div class="left full row" style="overflow-x: auto;">
	<script>var zones_nbr = {$zones|count +3} ; /*corresponds to the third input text (max, min and all)*/</script>
	<div style="float:left" id="zone_ranges">
		<table cellpadding="5" cellspacing="0" id="zones_table">
			<tr class="range_inf">
				<td class="range_type"></td>
				<td class="border_left border_bottom range_sign">>=</td>
				{if isset($mp_shipping_id)}
				{assign var=incr value=1}
				{foreach from=$ranges key=r item=range}

					<td class="border_bottom center">
						<div class="input-group fixed-width-md">
							<span class="input-group-addon price_unit">&nbsp; {$currency_sign|escape:'htmlall':'UTF-8'}</span>
							<span class="input-group-addon weight_unit">{$PS_WEIGHT_UNIT|escape:'htmlall':'UTF-8'}</span>
							<input name="range_inf[{$range.id_range|intval}]" type="text" class="form-control edit_price_value_lower value_lower_low{$incr|escape:'htmlall':'UTF-8'}" data-id_range="{$range.id_range|intval}" value="{$range.delimiter1|string_format:"%.2f"}" data-lwr_range_length="{$incr|escape:'htmlall':'UTF-8'}" />
						</div>
					</td>
					{assign var=incr value=$incr+1}
				{/foreach}
				{else}
				<td class="border_bottom center">
					<div class="input-group fixed-width-md">
						<span class="input-group-addon price_unit">&nbsp; {$currency_sign|escape:'htmlall':'UTF-8'}</span>
						<span class="input-group-addon weight_unit">{$PS_WEIGHT_UNIT|escape:'htmlall':'UTF-8'}</span>
						<input name="range_inf[]" type="text" class="form-control edit_price_value_lower value_lower_low1" value="" data-lwr_range_length="1"/>
					</div>
				</td>
				{/if}
			</tr>
			<tr class="range_sup">
				<td class="center range_type"></td>
				<td class="border_left range_sign"><</td>
				{if isset($mp_shipping_id)}
				{assign var=incr value=1}
				{foreach from=$ranges key=r item=range}
					<td class="center">
						<div class="input-group fixed-width-md">
							<span class="input-group-addon price_unit edit_price_sign">&nbsp; {$currency_sign|escape:'htmlall':'UTF-8'}</span>
							<span class="input-group-addon weight_unit edit_weight_sign">{$PS_WEIGHT_UNIT|escape:'htmlall':'UTF-8'}</span>
							<input name="range_sup[{$range.id_range|intval}]" type="text" class="form-control  edit_price_value_upper  value_upper_low{$incr|escape:'htmlall':'UTF-8'}" {if isset($form_id) && !$form_id} value="" {else} value="{if isset($change_ranges) && $range.id_range == 0} {else}{$range.delimiter2|string_format:"%.2f"}{/if}" {/if}  data-id_range="{$range.id_range|intval}" data-lwr_range_length="{$incr|escape:'htmlall':'UTF-8'}" />
						</div>
					</td>
					{assign var=incr value=$incr+1}
				{/foreach}
				{else}
				<td class="center">
					<div class="input-group fixed-width-md">
						<span class="input-group-addon price_unit">&nbsp; {$currency_sign|escape:'htmlall':'UTF-8'}</span>
						<span class="input-group-addon weight_unit">{$PS_WEIGHT_UNIT|escape:'htmlall':'UTF-8'}</span>
						<input name="range_sup[]" class="form-control edit_price_value_upper value_upper_low1" type="text"  data-lwr_range_length="1"/>
					</div>
				</td>
				{/if}
			</tr>
			<tr class="fees_all">
				<td class="border_top border_bottom border_bold">
					<span class="fees_all" {if isset($mp_shipping_id)}{if $ranges|count == 0}style="display:none"{/if}{/if}>{l s='All' mod='mpshipping'}</span>
				</td>
				<td>
					<input type="checkbox" id="allcheckrangezone" onclick="checkAllZones(this);" value="1">
				</td>
				{if isset($mp_shipping_id)}
				{assign var=incr value=1}
				{foreach from=$ranges key=r item=range}
					<td class="center border_top border_bottom">
						<div class="input-group fixed-width-md">
							<span class="input-group-addon">&nbsp; {$currency_sign|escape:'htmlall':'UTF-8'}</span>
							<input type="text" data-range_len_val="{$incr|escape:'htmlall':'UTF-8'}" class="form-control rangeAllforzone" disabled="disabled"/>
							<span class="currency_sign"></span>
						</div>
					</td>
				{assign var=incr value=$incr+1}
				{/foreach}
				{else}
					<td class="center border_top border_bottom">
						<div class="input-group fixed-width-md">
							<span class="input-group-addon">&nbsp; {$currency_sign|escape:'htmlall':'UTF-8'}</span>
							<input type="text" class="form-control rangeAllforzone" data-range_len_val="1" disabled="disabled"/>
							<span class="currency_sign"></span>
						</div>
					</td>
				{/if}
			</tr>
			{foreach from=$zones key=i item=zone}
			<tr class="fees {if $i is odd}alt_row{/if}" data-zoneid="{$zone.id_zone|escape:'htmlall':'UTF-8'}">
				<td><label for="zone_{$zone.id_zone|escape:'htmlall':'UTF-8'}">{$zone.name|escape:'htmlall':'UTF-8'}</label></td>
				<td class="zone">
					<input class="input_zone" onclick="enableTextField(this);" id="zone_{$zone.id_zone|escape:'htmlall':'UTF-8'}" name="zone_{$zone.id_zone|escape:'htmlall':'UTF-8'}" value="1" type="checkbox" {if isset($fields_val['zones'][$zone.id_zone]) && $fields_val['zones'][$zone.id_zone]} checked="checked"{/if}/>
				</td>
				{if isset($mp_shipping_id)}
					{assign var=incr value=1}
					{foreach from=$ranges key=r item=range}
						<td class="center">
							<div class="input-group fixed-width-md">
								<span class="input-group-addon">&nbsp; {$currency_sign|escape:'htmlall':'UTF-8'}</span>
								<input name="fees[{$zone.id_zone|intval}][{$range.id_range|intval}]" class="form-control input_zone_{$zone.id_zone|escape:'htmlall':'UTF-8'} other_input_zone zone_val{$incr|escape:'htmlall':'UTF-8'}" type="text"
								{if !isset($fields_val['zones'][$zone.id_zone]) || (isset($fields_val['zones'][$zone.id_zone]) && !$fields_val['zones'][$zone.id_zone])} disabled="disabled"{/if} {if isset($price_by_range[$range.id_range][$zone.id_zone]) && $price_by_range[$range.id_range][$zone.id_zone] && isset($fields_val['zones'][$zone.id_zone]) && $fields_val['zones'][$zone.id_zone]} value="{$price_by_range[$range.id_range|escape:'htmlall':'UTF-8'][$zone.id_zone|escape:'htmlall':'UTF-8']|string_format:'%.2f'}"{else}value="0.00"{/if} />
							</div>
						</td>
					{assign var=incr value=$incr+1}
					{/foreach}
				{else}
					<td class="center">
						<div class="input-group fixed-width-md">
							<span class="input-group-addon">&nbsp; {$currency_sign|escape:'htmlall':'UTF-8'}</span>
							<input type="text" name="fees[{$zone.id_zone|intval}][]" class="form-control input_zone_{$zone.id_zone|escape:'htmlall':'UTF-8'} other_input_zone zone_val1" disabled="disabled"/>
						</div>
					</td>
				{/if}
			</tr>
			{/foreach}
			<tr class="delete_range">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				{if isset($mp_shipping_id)}
				{foreach from=$ranges name=ranges key=r item=range}
					{if $smarty.foreach.ranges.first}
						<td class="center">&nbsp;</td>
					{else}
						<td class="center"><div class="btn btn-primary-outline delbutton">{l s='Delete' mod='mpshipping'}</div></td>
					{/if}
				{/foreach}
				{else}
					<td class="center">&nbsp;</td>
				{/if}
			</tr>
		</table>
	</div>
	<div class="new_range">
		<a id="add_new_range" class="btn btn-primary-outline">
			<span>{l s='Add new range' mod='mpshipping'}</span>
		</a>
	</div>
</div>