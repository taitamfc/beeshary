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

<div class="form-group">
	<label>{l s='Maximum package width (cm)' mod='mpshipping'}</label>
	<input type="text" class="form-control" name="max_width" id="max_width" value="{$max_width}">
	<p class="help-block">
		{l s='Maximum width managed by this carrier. Set the value to "0", or leave this field blank to ignore. The value must be an integer.' mod='mpshipping'}
	</p>
</div>
<div class="form-group">
	<label>{l s='Maximum package height (cm)' mod='mpshipping'}</label>
	<input type="text" class="form-control" name="max_height" id="max_height" value="{$max_height}">
	<p class="help-block">
		{l s='Maximum height managed by this carrier. Set the value to "0", or leave this field blank to ignore. The value must be an integer.' mod='mpshipping'}
	</p>
</div>
<div class="form-group">
	<label>{l s='Maximum package depth (cm)' mod='mpshipping'}</label>
	<input type="text" class="form-control" name="max_depth" id="max_depth" value="{$max_depth}">
	<p class="help-block">
		{l s='Maximum depth managed by this carrier. Set the value to "0", or leave this field blank to ignore. The value must be an integer. ' mod='mpshipping'}
	</p>
</div>
<div class="form-group">
	<label>{l s='Maximum package weight (kg)' mod='mpshipping'}</label>
	<input type="text" class="form-control" name="max_weight" id="max_weight" value="{$max_weight}">
	<p class="help-block">
		{l s='Maximum weight managed by this carrier. Set the value to "0", or leave this field blank to ignore. ' mod='mpshipping'}
	</p>
</div>
{*Display Group access*}
{if isset($customerAllGroups)}
<div class="left full form-group">
	<label>{l s='Group Access' mod='mpshipping'}</label>
	<table class="table" style="width:40%;">
		<thead>
			<tr>
				<th class="fixed-width-xs">
					<span class="title_box">
						<input type="checkbox" id="wk_select_all_checkbox">
					</span>
				</th>
				<th class="fixed-width-xs"><span class="title_box">{l s='ID' mod='mpshipping'}</span></th>
				<th>
					<span class="title_box">
						{l s='Group Name' mod='mpshipping'}
					</span>
				</th>
			</tr>
		</thead>
		<tbody>
			{foreach $customerAllGroups as $groupVal}
				<tr>
					<td><input type="checkbox" value="{$groupVal.id_group}" name="shipping_group[]" {if isset($mp_shipping_id)}{if isset($shippingGroup) && in_array($groupVal.id_group, $shippingGroup)}checked="checked"{/if}{else}checked="checked"{/if}>
					</td>
					<td>{$groupVal.id_group}</td>
					<td><label for="groupBox_{$groupVal.id_group}">{$groupVal.name nofilter}</label></td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>
{/if}