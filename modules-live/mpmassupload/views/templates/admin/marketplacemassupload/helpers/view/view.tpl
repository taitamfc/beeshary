{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<style>
	.left_span{
		float:left;
		font-weight:bold;
		width: 20%;
	}	
	fieldset > legend#view_mass
	{
		font-size: 16px;
	}
</style>

{block name="override_tpl"}
	<div class="panel col-lg-12">
			<div class="panel-heading">{l s='MP Seller Mass Upload Request' mod='mpmassupload'}</div>
		<fieldset>
			<legend id="view_mass">{l s='View Mass Upload Request' mod='mpmassupload'}</legend>
			<div style="margin-left:5%;">
				<div class="left_span">{l s='Request Number' mod='mpmassupload'}:</div>
				{$request_details['request_id']}
			</div><br/>
			<div style="margin-left:5%;">
				<div class="left_span">{l s='Request Status' mod='mpmassupload'}: </div>
				{$request_details['status']}
			</div><br/>
			
			<div style="margin-left:5%;">
				<div class="left_span">{l s='Shop Name' mod='mpmassupload'}: </div>
				{$request_details['shop_name']}
			</div><br/>
			<div style="margin-left:5%;">
				<div class="left_span">{l s='Download Uploaded Csv' mod='mpmassupload'}: </div>
				<a style="color:blue;" href="{$csv_link}/{$request_details['request_id']}.csv">{$request_details['request_id']}.csv</a>
			</div><br/>
			<div style="margin-left:5%;">
				<div class="left_span">{l s='Requested Date' mod='mpmassupload'}: </div>
				{$request_details['date_add']}
			</div>
		</fieldset>
	</div>
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminMarketplacemassupload')}" class="btn btn-default" id="desc-customer_group-back">
			<i class="process-icon-back "></i> <span>{l s='Back to list' mod='mpmassupload'}</span>
		</a>
	</div>
{/block}
