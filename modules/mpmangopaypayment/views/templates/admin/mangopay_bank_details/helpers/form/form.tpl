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
	<div class="panel-heading">
		{l s='Add Bank Details' mod='mpmangopaypayment'}
	</div>
	<div class="panel-body">
		{if isset($mgp_client_id) && $mgp_client_id}
			<form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'htmlall':'UTF-8'}"{/if}>
				<div class="form-group">
					<label for="mgp_bank_type" class="col-lg-3 control-label required">
						{l s='Type :' mod='mpmangopaypayment'}
					</label>
					<div class="col-lg-2">
						<select class="form-control" name="mgp_bank_type" id="mgp_bank_type">
					  		<option value="IBAN">{l s='IBAN' mod='mpmangopaypayment'}</option>
					  		<option value="GB">{l s='GB' mod='mpmangopaypayment'}</option>
					  		<option value="US">{l s='US' mod='mpmangopaypayment'}</option>
					  		<option value="CA">{l s='CA' mod='mpmangopaypayment'}</option>
					  		<option value="OTHER">{l s='OTHER' mod='mpmangopaypayment'}</option>
					  	</select>
					  	{if isset($seller_bank_details)}
					  		<input type="hidden" name="mgp_id_bank_details" value="{$seller_bank_details.id|escape:'htmlall':'UTF-8'}">
					  	{/if}
					</div>
				</div>
				<div class="form-group seller_bank_details" id="mgp_owner_name_block">
					<label for="mgp_owner_name" class="col-lg-3 control-label required">{l s='Owner Name :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-6">
						<input type="text" id="mgp_owner_name" name="mgp_owner_name" class="form-control" />
					</div>
				</div>
				<div class="form-group seller_bank_details" id="mgp_owner_address_addressline1">
					<label for="mgp_owner_addressline1" class="control-label col-lg-3 required">{l s='Address Line 1 :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-6">
						<input type="text" id="mgp_owner_addressline1" name="mgp_owner_addressline1" class="form-control" />
					</div>
				</div>
				<div class="form-group seller_bank_details" id="mgp_owner_address_addressline2">
					<label for="mgp_owner_addressline2" class="control-label col-lg-3">{l s='Address Line 2 :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-6">
						<input type="text" id="mgp_owner_addressline2" name="mgp_owner_addressline2" class="form-control" />
					</div>
				</div>
				<div class="form-group seller_bank_details" id="mgp_owner_address_city">
					<label for="mgp_owner_city" class="control-label required col-lg-3">{l s='City :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-6">
						<input type="text" id="mgp_owner_city" name="mgp_owner_city" class="form-control" />
					</div>
				</div>
				<div class="form-group seller_bank_details" id="mgp_owner_address_postalCode">
					<label for="mgp_owner_postcode" class="control-label required col-lg-3">{l s='Postal Code :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-6">
						<input type="text" id="mgp_owner_postcode" name="mgp_owner_postcode" class="form-control" />
					</div>
				</div>
				<div class="form-group seller_bank_details" id="mgp_owner_address_region">
					<label for="mgp_owner_region" class="control-label required col-lg-3">{l s='Region :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-6">
						<input type="text" id="mgp_owner_region" name="mgp_owner_region" class="form-control" />
					</div>
				</div>
				<div class="form-group seller_bank_details" id="mgp_owner_address_country">
					<label for="mgp_owner_country" class="col-lg-3 control-label required">{l s='Country :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-4">
						{if isset($countries)}
							<select class="form-control" name="mgp_owner_country" id="mgp_owner_country">
						  		{foreach $countries as $country}
									<option value="{$country.iso_code|escape:'htmlall':'UTF-8'}">{$country.name|escape:'htmlall':'UTF-8'}</option>
								{/foreach}
						  	</select>
						{else}
							{l s = 'No country found' mod='mpmangopaypayment'}
						{/if}
					</div>
				</div>
				{*<div class="form-group seller_bank_details" id="mgp_owner_address_block">
					<label for="mgp_owner_address" class="col-lg-3 control-label required">{l s='Owner Address :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-6">
						<textarea id="mgp_owner_address" name="mgp_owner_address" class="form-control"></textarea>
					</div>
				</div>*}
				<div class="form-group seller_bank_details" id="mgp_iban_block">
					<label for="mgp_iban" class="col-lg-3 control-label required">{l s='IBAN :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-6">
						<input type="text" id="mgp_iban" name="mgp_iban" class="form-control" />
					</div>
				</div>
				<div class="form-group seller_bank_details" id="mgp_bic_block">
					<label for="mgp_bic" class="col-lg-3 control-label required">{l s='BIC :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-6">
						<input type="text" id="mgp_bic" name="mgp_bic" class="form-control" />
					</div>
				</div>
				<div class="form-group seller_bank_details" id="mgp_account_number_block">
					<label for="mgp_account_number" class="col-lg-3 control-label required">{l s='Account Number :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-6">
						<input type="text" id="mgp_account_number" name="mgp_account_number" class="form-control" />
					</div>
				</div>
				<div class="form-group seller_bank_details" id="mgp_sort_code_block">
					<label for="mgp_sort_code" class="col-lg-3 control-label required">{l s='Sort Code :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-6">
						<input type="text" id="mgp_sort_code" name="mgp_sort_code" class="form-control" />
					</div>
				</div>
				<div class="form-group seller_bank_details" id="mgp_aba_block">
					<label for="mgp_aba" class="col-lg-3 control-label required">{l s='ABA :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-6">
						<input type="text" id="mgp_aba" name="mgp_aba" class="form-control" />
					</div>
				</div>
				<div class="form-group seller_bank_details" id="mgp_bank_name_block">
					<label for="mgp_bank_name" class="col-lg-3 control-label required">{l s='Bank Name :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-6">
						<input type="text" id="mgp_bank_name" name="mgp_bank_name" class="form-control" />
					</div>
				</div>
				<div class="form-group seller_bank_details" id="mgp_institution_number_block">
					<label for="mgp_institution_number" class="col-lg-3 control-label required">{l s='Institution Number :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-6">
						<input type="text" id="mgp_institution_number" name="mgp_institution_number" class="form-control" />
					</div>
				</div>
				<div class="form-group seller_bank_details" id="mgp_branch_code_block">
					<label for="mgp_branch_code" class="col-lg-3 control-label required">{l s='Branch Code :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-6">
						<input type="text" id="mgp_branch_code" name="mgp_branch_code" class="form-control" />
					</div>
				</div>

				<div class="panel-footer">
					<button type="submit" name="saveSellerbankDetails" class="btn btn-default pull-right">
						<i class="process-icon-save"></i> {l s='Save' mod='mpmangopaypayment'}
					</button>
				</div>
			</form>
		{else}
			<div class="alert alert-warning">
				{l s = 'Save your mangopay details from module configuration before any action.' mod='mpmangopaypayment'}
			</div>
		{/if}
	</div>
</div>
