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

<div id="mangopay_direct_debit_container" class="row">
	{* <h2 class="page-subheading">
		<span>{l s='Bank Account Detail' mod='mpmangopaypayment'}</span>
	</h2> *}
	<form id="customer_mgp_bank_details_form" action="" method="post" enctype="multipart/form-data" accept-charset="UTF-8,ISO-8859-1,UTF-16">
		<div class="cust_mgp_account_creation_errors">
		</div>
		<input id="pay_with_new_account" name="pay_with_new_account" type="hidden" value="{if isset($userBankAccounts) && $userBankAccounts && isset($save_bank_account_enable) && $save_bank_account_enable}0{else}1{/if}">
		{if isset($userBankAccounts) && $userBankAccounts && isset($save_bank_account_enable) && $save_bank_account_enable}
			<div class="col-sm-12 mangopay_saved_account_container">
				{foreach from=$userBankAccounts item=account name=bankAccount}
					<div class="payment-option clearfix">
						<div class="col-lg-1 col-xs-2 form-control-static">
							<span class="custom-radio float-xs-left">
								<input value="{$account->Id}" class="saved_customer_account" name="saved_customer_account" type="radio" id_bank_account_mgp="{$account->Id}" {if $smarty.foreach.bankAccount.first}checked{/if}>
								<span></span>
							</span>
						</div>
						<div class="col-lg-11 col-xs-10 account_info">
							[ <span class="account_details_head">{l s='OwnerName' mod='mpmangopaypayment'} : </span><span class="account_details_value">{$account->OwnerName}</span> ]
							[ <span class="account_details_head">{l s='Account Type' mod='mpmangopaypayment'} : </span><span class="account_details_value">{$account->Type}</span> ]<br>
							[ <span class="account_details_head">{l s='Account Details' mod='mpmangopaypayment'} : </span><span class="account_details_value">
							{if $account->Type == 'IBAN'}
								{l s='IBAN' mod='mpmangopaypayment'} - {$account->Details->IBAN} , {l s='BIC' mod='mpmangopaypayment'} - {$account->Details->BIC}
							{elseif $account->Type == 'GB'}
								{l s='AccountNumber' mod='mpmangopaypayment'} - {$account->Details->AccountNumber} , {l s='SortCode' mod='mpmangopaypayment'} - {$account->Details->SortCode}
							{else}
								{l s='Not Found' mod='mpmangopaypayment'}
							{/if}

							</span> ]
							<span class="remove_saved_account_link" id_account_mgp="{$account->Id}"><i class="material-icons float-xs-left">delete</i>
						</div>
					</div>
				{/foreach}
				<div class="payment-option clearfix">
					<div class="col-lg-1 col-xs-2">
					</div>
					<div class="col-lg-11 col-xs-10 add_new_account">
						<span class="add_new_account_link"><i class="material-icons">add</i> {l s='Add New Account' mod='mpmangopaypayment'}</span>
					</div>
				</div>
			</div>
		{/if}
		<div class="mangopay_new_account_container" {if isset($userBankAccounts) && $userBankAccounts && isset($save_bank_account_enable) && $save_bank_account_enable}style="display:none;"{/if} id="customer_bank_details_fields">
			<div class="row">
				<div class="form-group col-sm-6">
					<label for="mgp_bank_type" class="control-label required">{l s='Type' mod='mpmangopaypayment'}</label>
					<div style="width: 100%;">
						<select class="form-control" name="mgp_bank_type" id="mgp_bank_type">
							<option value="IBAN">{l s='IBAN' mod='mpmangopaypayment'}</option>
							<option value="GB">{l s='GB' mod='mpmangopaypayment'}</option>
							{* <option value="US">{l s='US' mod='mpmangopaypayment'}</option>
							<option value="CA">{l s='CA' mod='mpmangopaypayment'}</option>
							<option value="OTHER">{l s='OTHER' mod='mpmangopaypayment'}</option> *}
						</select>
					</div>
				</div>
				<div class="form-group buyer_bank_details col-sm-6" id="mgp_owner_name_block">
					<label for="mgp_owner_name" class="control-label required">{l s='Owner Name' mod='mpmangopaypayment'}</label>
					<input type="text" id="mgp_owner_name" name="mgp_owner_name" class="form-control" />
				</div>
			</div>

			<div class="row">
				<div class="form-group buyer_bank_details col-sm-6" id="mgp_owner_address_addressline1">
					<label for="mgp_owner_addressline1" class="control-label required">{l s='Address Line 1' mod='mpmangopaypayment'}</label>
					<input type="text" id="mgp_owner_addressline1" name="mgp_owner_addressline1" class="form-control" />
				</div>
				<div class="form-group buyer_bank_details col-sm-6" id="mgp_owner_address_addressline2">
					<label for="mgp_owner_addressline2" class="control-label">{l s='Address Line 2 (optional)' mod='mpmangopaypayment'}</label>
					<input type="text" id="mgp_owner_addressline2" name="mgp_owner_addressline2" class="form-control" />
				</div>
			</div>

			<div class="row">
				<div class="form-group buyer_bank_details col-sm-6" id="mgp_owner_address_city">
					<label for="mgp_owner_city" class="control-label required">{l s='City' mod='mpmangopaypayment'}</label>
					<input type="text" id="mgp_owner_city" name="mgp_owner_city" class="form-control" />
				</div>
				<div class="form-group buyer_bank_details col-sm-6" id="mgp_owner_address_postalCode">
					<label for="mgp_owner_postcode" class="control-label required">{l s='Postal Code' mod='mpmangopaypayment'}</label>
					<input type="text" id="mgp_owner_postcode" name="mgp_owner_postcode" class="form-control" />
				</div>
			</div>

			<div class="row">
				<div class="form-group buyer_bank_details col-sm-6" id="mgp_owner_address_region">
					<label for="mgp_owner_region" class="control-label required">{l s='Region' mod='mpmangopaypayment'}</label>
					<input type="text" id="mgp_owner_region" name="mgp_owner_region" class="form-control" />
				</div>
				<div class="form-group buyer_bank_details col-sm-6" id="mgp_owner_address_country">
					<label for="mgp_owner_country" class="control-label required">{l s='Country' mod='mpmangopaypayment'}</label>
					{if isset($countries)}
						<select name="mgp_owner_country" id="mgp_owner_country" class="form-control">
							{foreach $countries as $country}
								<option value="{$country.iso_code}">{$country.name}</option>
							{/foreach}
						</select>
					{else}
					<p>{l s='Country list not available.' mod='mpmangopaypayment'}</p>
					{/if}
				</div>
			</div>

			<div class="row">
				<div class="form-group buyer_bank_details col-sm-6" id="mgp_iban_block">
					<label for="mgp_iban" class="control-label required">{l s='IBAN' mod='mpmangopaypayment'}</label>
					<input type="text" id="mgp_iban" name="mgp_iban" class="form-control" />
				</div>

				<div class="form-group buyer_bank_details col-sm-6" id="mgp_bic_block">
					<label for="mgp_bic" class="control-label required">{l s='BIC' mod='mpmangopaypayment'}</label>
					<input type="text" id="mgp_bic" name="mgp_bic" class="form-control" />
				</div>
				<div class="form-group buyer_bank_details col-sm-6" id="mgp_account_number_block">
					<label for="mgp_account_number" class="control-label required">{l s='Account Number' mod='mpmangopaypayment'}</label>
					<input type="text" id="mgp_account_number" name="mgp_account_number" class="form-control" />
				</div>

				<div class="form-group buyer_bank_details col-sm-6" id="mgp_sort_code_block">
					<label for="mgp_sort_code" class="control-label required">{l s='Sort Code' mod='mpmangopaypayment'}</label>
					<input type="text" id="mgp_sort_code" name="mgp_sort_code" class="form-control" />
				</div>
				<div class="form-group buyer_bank_details col-sm-6" id="mgp_aba_block">
					<label for="mgp_aba" class="control-label required">{l s='ABA' mod='mpmangopaypayment'}</label>
					<input type="text" id="mgp_aba" name="mgp_aba" class="form-control" />
				</div>

				<div class="form-group buyer_bank_details col-sm-6" id="mgp_bank_name_block">
					<label for="mgp_bank_name" class="control-label required">{l s='Bank Name' mod='mpmangopaypayment'}</label>
					<input type="text" id="mgp_bank_name" name="mgp_bank_name" class="form-control" />
				</div>
			</div>
			<div class="row">
				<div class="form-group buyer_bank_details col-sm-6" id="mgp_institution_number_block">
					<label for="mgp_institution_number" class="control-label required">{l s='Institution Number' mod='mpmangopaypayment'}</label>
					<input type="text" id="mgp_institution_number" name="mgp_institution_number" class="form-control" />
				</div>

				<div class="form-group buyer_bank_details col-sm-6" id="mgp_branch_code_block">
					<label for="mgp_branch_code" class="control-label required">{l s='Branch Code' mod='mpmangopaypayment'}</label>
					<input type="text" id="mgp_branch_code" name="mgp_branch_code" class="form-control" />
				</div>
			</div>
			{if isset($save_bank_account_enable) && $save_bank_account_enable}
				<div class="row">
					<div class="form-group col-sm-12">
						<input name="save_trans_account" type="checkbox" id="save_trans_account">
						&nbsp; <i><span>{l s='Save this account for faster checkout' mod='mpmangopaypayment'}</span></i>
					</div>
				</div>
			{/if}
			{if isset($userBankAccounts) && $userBankAccounts}
				<div class="row">
					<div class="col-sm-12 cancel_new_account">
						<span class="cancel_new_account_link">{l s='Cancel' mod='mpmangopaypayment'}</span>
					</div>
				</div>
			{/if}
		</div>
	</form>
</div>
<div class="loading_overlay">
	<img src="{$module_dir}mpmangopaypayment/views/img/ajax-loader.gif" class="loading-img" alt="{l s='Not Found' mod='mpmangopaypayment'}"/>
</div>