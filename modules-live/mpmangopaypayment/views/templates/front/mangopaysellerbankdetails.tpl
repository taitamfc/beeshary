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
  {if $logged}
  	{if isset($smarty.get.mgp_account_success)}
  		<div class="alert alert-success">
  			{l s='Bank details saved successfully' mod='mpmangopaypayment'}
  		</div>
  	{/if}
	{if isset($smarty.get.mgp_account_deactivate_success)}
		<div class="alert alert-success">
		{l s='Bank account deactivated successfully' mod='mpmangopaypayment'}
		</div>
	{/if}
  	{hook h='displayMpMangopayBankDetailsHeader'}
  	<div class="wk-mp-block">
  		{hook h="displayMpMenu"}
  		<div class="wk-mp-content">
  			<div class="page-title" style="background-color:{$title_bg_color};">
  				<span style="color:{$title_text_color};">{l s='Mangopay Bank Details' mod='mpmangopaypayment'}</span>
  			</div>
  			<div class="wk-mp-right-column">
  				<div class="form-horizontal seller_bank_accounts">
  					<div class="table-responsive">
  						<table class="table table-bordered">
  							<thead>
  								<tr>
  									<th class="text-center">{l s='User Id' mod='mpmangopaypayment'}</th>
  									<th class="text-center">{l s='Type' mod='mpmangopaypayment'}</th>
  									<th class="text-center">{l s='Mangopay Bank A/c Id' mod='mpmangopaypayment'}</th>
  									<th class="text-center">{l s='Owner Name' mod='mpmangopaypayment'}</th>
  									<th class="text-center">{l s='Owner Address' mod='mpmangopaypayment'}</th>
									<th class="text-center">{l s='Status' mod='mpmangopaypayment'}</th>
  								</tr>
  							</thead>
  							<tbody>
  								{if isset($mgp_registered_bank_acc_ids)}
  									{foreach from=$mgp_registered_bank_acc_ids key = key item=data}
  										<tr>
  											<td class="text-center">{$data->UserId}</td>
  											<td class="text-center">{$data->Type}</td>
  											<td class="text-center">{$data->Id}</td>
  											<td class="text-center">{$data->OwnerName}</td>
  											<td class="text-center">{$data->OwnerAddress->AddressLine1}</td>
											<td class="text-center">
												{if $data->Active}
													<form method="post" action="">
														<input type="hidden" value="{$data->Id}" name="mgp_bank_account_id">
														<input type="hidden" value="{$data->UserId}" name="mgp_bank_author_id">
														<button class="btn btn-primary btn-sm deactivate_bank_account" name="mgp_bank_account_deactivate" type="submit">
														{l s='Deactivate' mod='mpmangopaypayment'}
														</button>
													</form>
												{else}
													{l s='Deactivated' mod='mpmangopaypayment'}
												{/if}
											</td>
  										</tr>
  									{/foreach}
  								{else}
  									<tr>
  										<td colspan="4">{l s='No accounts created yet' mod='mpmangopaypayment'}</td>
  									</tr>
  								{/if}
  							</tbody>
  						</table>
  					</div>
  				</div>
  				{if isset($seller_bank_details_enable) && $seller_bank_details_enable}
				  	<div class="form-horizontal seller_new_bank_account">
						<h1 class="seller_new_account_heading">
							<span>{l s='Create New Account' mod='mpmangopaypayment'}</span>
						</h1>
						{if isset($seller_not_registered) && $seller_not_registered}
							<form action="{$link->getModuleLink('mpmangopaypayment', 'mangopaysellerbankdetails')}" method="post" enctype="multipart/form-data" accept-charset="UTF-8,ISO-8859-1,UTF-16">
								<div class="form-group row">
									<div class="col-sm-12">
										<label for="mgp_bank_type" class="control-label required">
											{l s='Type :' mod='mpmangopaypayment'}
										</label>
									</div>
									<div class="col-sm-6">
										<select class="form-control required" name="mgp_bank_type" id="mgp_bank_type">
											<option value="IBAN">{l s='IBAN' mod='mpmangopaypayment'}</option>
											<option value="GB">{l s='GB' mod='mpmangopaypayment'}</option>
											<option value="US">{l s='US' mod='mpmangopaypayment'}</option>
											<option value="CA">{l s='CA' mod='mpmangopaypayment'}</option>
											<option value="OTHER">{l s='OTHER' mod='mpmangopaypayment'}</option>
										</select>
									</div>
									{if isset($seller_bank_details)}
										<input type="hidden" name="mgp_id_bank_details" value="{$seller_bank_details.id}">
									{/if}
								</div>

								<div class="form-group seller_bank_details" id="mgp_owner_name_block">
									<label for="mgp_owner_name" class="control-label required">{l s='Owner Name :' mod='mpmangopaypayment'}</label>
									<input type="text" id="mgp_owner_name" name="mgp_owner_name" class="form-control" />
								</div>
								<div class="form-group seller_bank_details" id="mgp_owner_address_addressline1">
									<label for="mgp_owner_addressline1" class="control-label required">{l s='Address Line 1 :' mod='mpmangopaypayment'}</label>
									<input type="text" id="mgp_owner_addressline1" name="mgp_owner_addressline1" class="form-control" />
								</div>
								<div class="form-group seller_bank_details" id="mgp_owner_address_addressline2">
									<label for="mgp_owner_addressline2" class="control-label">{l s='Address Line 2 :' mod='mpmangopaypayment'}</label>
									<input type="text" id="mgp_owner_addressline2" name="mgp_owner_addressline2" class="form-control" />
								</div>
								<div class="form-group seller_bank_details" id="mgp_owner_address_city">
									<label for="mgp_owner_city" class="control-label required">{l s='City :' mod='mpmangopaypayment'}</label>
									<input type="text" id="mgp_owner_city" name="mgp_owner_city" class="form-control" />
								</div>
								<div class="form-group seller_bank_details" id="mgp_owner_address_postalCode">
									<label for="mgp_owner_postcode" class="control-label required">{l s='Postal Code :' mod='mpmangopaypayment'}</label>
									<input type="text" id="mgp_owner_postcode" name="mgp_owner_postcode" class="form-control" />
								</div>
								<div class="form-group seller_bank_details" id="mgp_owner_address_region">
									<label for="mgp_owner_region" class="control-label required">{l s='Region :' mod='mpmangopaypayment'}</label>
									<input type="text" id="mgp_owner_region" name="mgp_owner_region" class="form-control" />
								</div>

								<div class="form-group row">
									<div class="col-sm-12">
										<label for="mgp_owner_country" class="control-label required">{l s='Country :' mod='mpmangopaypayment'}</label>
									</div>
									<div class="col-sm-6">
										{if isset($countries)}
											<select name="mgp_owner_country" id="mgp_owner_country" class="form-control">
												{foreach $countries as $country}
													<option value="{$country.iso_code|escape:'htmlall':'UTF-8'}">{$country.name|escape:'htmlall':'UTF-8'}</option>
												{/foreach}
											</select>
										{else}
											<p>{l s='Country list not available.' mod='mpmangopaypayment'}</p>
										{/if}
									</div>
								</div>
								<div class="form-group seller_bank_details" id="mgp_iban_block">
									<label for="mgp_iban" class="control-label required">{l s='IBAN :' mod='mpmangopaypayment'}</label>
									<input type="text" id="mgp_iban" name="mgp_iban" class="form-control" />
								</div>

								<div class="form-group seller_bank_details" id="mgp_bic_block">
									<label for="mgp_bic" class="control-label required">{l s='BIC :' mod='mpmangopaypayment'}</label>
									<input type="text" id="mgp_bic" name="mgp_bic" class="form-control" />
								</div>

								<div class="form-group seller_bank_details" id="mgp_account_number_block">
									<label for="mgp_account_number" class="control-label required">{l s='Account Number :' mod='mpmangopaypayment'}</label>
									<input type="text" id="mgp_account_number" name="mgp_account_number" class="form-control" />
								</div>

								<div class="form-group seller_bank_details" id="mgp_sort_code_block">
									<label for="mgp_sort_code" class="control-label required">{l s='Sort Code :' mod='mpmangopaypayment'}</label>
									<input type="text" id="mgp_sort_code" name="mgp_sort_code" class="form-control" />
								</div>

								<div class="form-group seller_bank_details" id="mgp_aba_block">
									<label for="mgp_aba" class="control-label required">{l s='ABA :' mod='mpmangopaypayment'}</label>
									<input type="text" id="mgp_aba" name="mgp_aba" class="form-control" />
								</div>

								<div class="form-group seller_bank_details" id="mgp_bank_name_block">
									<label for="mgp_bank_name" class="control-label required">{l s='Bank Name :' mod='mpmangopaypayment'}</label>
									<input type="text" id="mgp_bank_name" name="mgp_bank_name" class="form-control" />
								</div>

								<div class="form-group seller_bank_details" id="mgp_institution_number_block">
									<label for="mgp_institution_number" class="control-label required">{l s='Institution Number :' mod='mpmangopaypayment'}</label>
									<input type="text" id="mgp_institution_number" name="mgp_institution_number" class="form-control" />
								</div>

								<div class="form-group seller_bank_details" id="mgp_branch_code_block">
									<label for="mgp_branch_code" class="control-label required">{l s='Branch Code :' mod='mpmangopaypayment'}</label>
									<input type="text" id="mgp_branch_code" name="mgp_branch_code" class="form-control" />
								</div>
								{* Code for adding GDPR consent box *}
                                {if isset($isWkGdpr)}
                                    {hook h='displayGDPRConsent' mod='wkgdpr' id_module=$id_module}
                                {else}
                                    {hook h='displayGDPRConsent' mod='psgdpr' id_module=$id_module}
                                {/if}
								<div class="form-group wk_text_center">
									<button type="submit" id="submit_mgp_bank_account" name="submit_mgp_bank_account" class="btn btn-success wk_btn_extra form-control-submi">
										<span>{l s='Save' mod='mpmangopaypayment'}</span>
									</button>
								</div>
							</form>
						{else}
							<div class="alert alert-info">
								<i class="material-icons">&#xE000;</i>&nbsp;&nbsp;</i> <a href="/module/mpmangopaypayment/mangopayselledetails">Cliquez ici pour choisir un pays pour mes informations bancaires</a>
							</div>
						{/if}
					</div>
  				{else}
  					<div class="alert alert-warning">
  						{l s='Sellers are not allowed to register their bank details Please contact to the admin to register bank details.' mod='mpmangopaypayment'}
  					</div>
  				{/if}

  			</div>
  		</div>
  	</div>
  {/if}
{/block}
