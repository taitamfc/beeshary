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
		{l s='Seller Pay Out Management' mod='mpmangopaypayment'}
	</div>
	{if isset($sellers_info)}
		<div class="panel-body">
			{if isset($mgp_client_id) && $mgp_client_id}
				<form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'htmlall':'UTF-8'}"{/if}>
					<div class="form-group">
						<label class="col-lg-3 control-label required">{l s='Choose Seller :' mod='mpmangopaypayment'}</label>
						<div class="col-lg-3">
							{if isset($sellers_info)}
								<select name="seller_id" id="seller_id">
									{foreach $sellers_info as $seller_info}
										<option value="{$seller_info['id_seller']|escape:'html':'UTF-8'}">
											{$seller_info['business_email']|escape:'html':'UTF-8'}
										</option>
									{/foreach}
								</select>
							{else}
								<p>{l s='No seller found.' mod='mpmangopaypayment'}</p>
							{/if}
						</div>
					</div>
					<div class="form-group">
						<label for="payout_tag_seller" class="col-lg-3 control-label">{l s='Tag :' mod='mpmangopaypayment'}</label>
						<div class="col-lg-3">
							<input type="text" id="payout_tag_seller" name="payout_tag_seller" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label for="payout_debit_amt_seller" class="required control-label col-lg-3">
							{l s='Amount' mod='mpmangopaypayment'}
						</label>
						<div class="col-lg-3">
							<div class="input-group">
								<input type="text" id="payout_debit_amt_seller" name="payout_debit_amt_seller" placeholder="{l s='Enter Amount' mod='mpmangopaypayment'}">
								<span class="input-group-addon">{$wallet_currency_sign|escape:'htmlall':'UTF-8'}</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="payout_fees_seller" class="required control-label col-lg-3">
							{l s='Fees' mod='mpmangopaypayment'}
						</label>
						<div class="col-lg-3">
							<div class="input-group">
								<input type="text" id="payout_fees_seller" name="payout_fees_seller">
								<span class="input-group-addon">{$wallet_currency_sign|escape:'htmlall':'UTF-8'}</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="payout_seller_bank_acc_id" class="col-lg-3 control-label required">{l s='Seller bank Account Id :' mod='mpmangopaypayment'}</label>
						<div class="col-lg-3">
							<select class="form-control" name="payout_seller_bank_acc_id" id="payout_seller_bank_acc_id">
								<option selected="selected" value="0">{l s='Select Bank Account' mod='mpmangopaypayment'}</option>
								{if isset($mgp_registered_bank_acc_ids)}
									{foreach from=$mgp_registered_bank_acc_ids key = key item=data}
										<option class="wkbankdetails" value="{$data->Id}">{$data->Type}/{$data->Id}/({$data->OwnerName})</option>
									{foreachelse}
										{l s='No Account Available' mod='mpmangopaypayment'}
									{/foreach}
								{else}
									<p>{l s='No seller Bank Account.' mod='mpmangopaypayment'}</p>
								{/if}
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-3"></div>
						<div class="help-block col-lg-5">
							{l s='Note : Bank account format is in this pattern - A/c Type / Mangopay Bank A/c Id / Mangopay Bank A/c Owner Name' mod='mpmangopaypayment'}
						</div>
					</div>
					<div class="panel-footer">
						<button type="submit" name="submit_seller_payout" class="btn btn-default pull-right">
							<i class="process-icon-save"></i> {l s='PayOut' mod='mpmangopaypayment'}
						</button>
					</div>
				</form>
			{else}
				<div class="alert alert-warning">
					{l s = 'Save Your Mangopay details from module configuration before any action.' mod='mpmangopaypayment'}
				</div>
			{/if}
		</div>
	{else}
		<div class="alert alert-warning">
			{l s = 'No Sellers found to add bank details. Please add sellers and add them as a user to mangopay account before PayOut Process.' mod='mpmangopaypayment'}
		</div>
	{/if}
</div>
