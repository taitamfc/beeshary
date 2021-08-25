{*
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="panel">
	<h3><i class="icon-tag"></i> {l s='Booking price Rules Priority management' mod='mpbooking'}</h3>
	<div class="alert alert-info">
		{l s='Sometimes one booking product can fit into multiple booking price rules. In this case priorities allow you to define which rule applies to the booking Product.' mod='mpbooking'}
	</div>
	<form id="{$table}_form" class="defaultForm form-horizontal" action="{$current}&configure=mpbooking&tab_module=front_office_features&module_name=mpbooking&token={$token}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style}"{/if}>
		<div class="form-group">
			<label class="control-label col-lg-3" for="featurePricePriority">{l s='Booking Price Calculation Priorities' mod='mpbooking'} :: </label>
			<div class="input-group col-lg-9">
				<select name="featurePricePriority[]" class="featurePricePriority">
					<option class="specific_date" value="specific_date" {if isset($featurePricePriority) && $featurePricePriority[0]=='specific_date'}selected="selected"{/if}>{l s='Specific Date' mod='mpbooking'}</option>
					<option class="special_day" value="special_day" {if isset($featurePricePriority) && $featurePricePriority[0]=='special_day'}selected="selected"{/if}>{l s='Special Days' mod='mpbooking'}</option>
					<option class="date_range" value="date_range" {if isset($featurePricePriority) && $featurePricePriority[0]=='date_range'}selected="selected"{/if}>{l s='Date Ranges' mod='mpbooking'}</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="featurePricePriority[]" class="featurePricePriority">
					<option class="specific_date" value="specific_date" {if isset($featurePricePriority) && $featurePricePriority[1]=='specific_date'}selected="selected"{/if}>{l s='Specific Date' mod='mpbooking'}</option>
					<option class="special_day" value="special_day" {if isset($featurePricePriority) && $featurePricePriority[1]=='special_day'}selected="selected"{/if}>{l s='Special Days' mod='mpbooking'}</option>
					<option class="date_range" value="date_range" {if isset($featurePricePriority) && $featurePricePriority[1]=='date_range'}selected="selected"{/if}>{l s='Date Ranges' mod='mpbooking'}</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="featurePricePriority[]" class="featurePricePriority">
					<option class="specific_date" value="specific_date" {if isset($featurePricePriority) && $featurePricePriority[2]=='specific_date'}selected="selected"{/if}>{l s='Specific Date' mod='mpbooking'}</option>
					<option class="special_day" value="special_day" {if isset($featurePricePriority) && $featurePricePriority[2]=='special_day'}selected="selected"{/if}>{l s='Special Days' mod='mpbooking'}</option>
					<option class="date_range" value="date_range" {if isset($featurePricePriority) && $featurePricePriority[2]=='date_range'}selected="selected"{/if}>{l s='Date Ranges' mod='mpbooking'}</option>
				</select>
			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" name="submitAddFeaturePricePriority" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save' mod='mpbooking'}
			</button>
		</div>
	</form>
</div>
