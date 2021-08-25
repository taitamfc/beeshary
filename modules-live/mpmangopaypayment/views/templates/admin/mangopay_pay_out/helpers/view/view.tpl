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
		{l s='Admin PayOut' mod='mpmangopaypayment'}
	</div>
	<div class="panel-body">
		{if isset($mgp_client_id) && $mgp_client_id}
			<form id="{$table}_form" class="defaultForm form-horizontal" action="{$current}&{if !empty($submit_action)}{$submit_action}{/if}&token={$token}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style}"{/if}>
				<div class="form-group">
					<label for="total_wallet_balance" class="col-lg-3 control-label">{l s='Total wallet balance :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-3">
						<p class="form-control-static">{if isset($total_wallet_balance) && $total_wallet_balance}{$total_wallet_balance}&nbsp;{$seller_wallet_currency}{else}{l s='No Balance Available' mod='mpmangopaypayment'}{/if}</p>
					</div>
				</div>
				<div class="form-group">
					<label for="payout_tag_seller" class="col-lg-3 control-label">{l s='Tag :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-3">
						<input type="text" id="payout_tag_seller" name="payout_tag_seller" class="form-control" />
					</div>
				</div>
				<div class="form-group">
					<label for="payout_userid_seller" class="col-lg-3 control-label required">{l s='Author Id :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-3">
						<input type="text" id="payout_userid_seller" name="payout_userid_seller" class="form-control" {if isset($admin_mgp_usr_id) && $admin_mgp_usr_id}value="{$admin_mgp_usr_id}" readonly {/if}/>
					</div>
				</div>
				<div class="form-group">
					<label for="payout_credit_walletid_seller" class="col-lg-3 control-label required">{l s='Wallet Id :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-3">
						<input type="text" id="payout_credit_walletid_seller" name="payout_credit_walletid_seller" class="form-control" {if isset($admin_mgp_wallet_id) && $admin_mgp_wallet_id}value="{$admin_mgp_wallet_id}" readonly {/if} />
					</div>
				</div>
				<div class="form-group">
					<label for="payout_debit_amt_seller" class="required control-label col-lg-3">
						{l s='Amount' mod='mpmangopaypayment'}
					</label>
					<div class="col-lg-3">
						<div class="input-group">
							<input type="text" id="payout_debit_amt_seller" name="payout_debit_amt_seller" placeholder="{l s='Enter Amount' mod='mpmangopaypayment'}">
							<span class="input-group-addon">{$wallet_currency_sign}</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="payout_fees_seller" class="control-label col-lg-3">
						{l s='Fees' mod='mpmangopaypayment'}
					</label>
					<div class="col-lg-3">
						<div class="input-group">
							<input type="text" id="payout_fees_seller" name="payout_fees_seller">
							<span class="input-group-addon">{$wallet_currency_sign}</span>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="payout_admin_bank_acc_id" class="col-lg-3 control-label required">{l s='Bank Account Id :' mod='mpmangopaypayment'}</label>
					<div class="col-lg-3">
						<select class="form-control" name="payout_admin_bank_acc_id" id="payout_admin_bank_acc_id">
							<option selected="selected" value="0">{l s='Select Bank Account' mod='mpmangopaypayment'}</option>
							{if isset($admin_mgp_account_dtls)}
								{foreach from=$admin_mgp_account_dtls key = key item=data}
									<option value="{$data->Id|escape:'htmlall':'UTF-8'}">{$data->Type|escape:'htmlall':'UTF-8'}/{$data->Id|escape:'htmlall':'UTF-8'}/({$data->OwnerName|escape:'htmlall':'UTF-8'})</option>
								{foreachelse}
									{l s='No Account Available' mod='mpmangopaypayment'}
								{/foreach}
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
					<button type="submit" name="submit_admin_payout" class="btn btn-default pull-right">
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
</div>
