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
  	{if isset($smarty.get.payout_success)}
  		<div class="alert alert-success">
  			{l s='Amount from seller\'s wallet has been paid out to the bank account successfully' mod='mpmangopaypayment'}
  		</div>
  	{/if}
  	<div class="wk-mp-block">
  		{hook h="displayMpMenu"}
  		<div class="wk-mp-content">
  			<div class="page-title" style="background-color:{$title_bg_color};">
  				<span style="color:{$title_text_color};">{l s='Mangopay Cash Out' mod='mpmangopaypayment'}</span>
  			</div>
  			<div class="wk-mp-right-column">
  				{if isset($seller_cashout_enable) && $seller_cashout_enable}
  					{if isset($seller_not_registered) && $seller_not_registered}
  						<form method="post" class="defaultForm form-horizontal" action="{$link->getModuleLink('mpmangopaypayment', 'mangopaysellercashout')}" method="post" accept-charset="UTF-8,ISO-8859-1,UTF-16">
  							<div class="form-group row">
  								<label for="total_wallet_balance" class="col-lg-3 control-label">{l s='Total Wallet Balance :' mod='mpmangopaypayment'}</label>
  								<div class="col-lg-3">
  									<p class="form-control-sm-static form-control-xs-static">{if isset($total_wallet_balance) && $total_wallet_balance}{$total_wallet_balance}&nbsp;{$seller_wallet_currency}{else}{l s='No Balance Available' mod='mpmangopaypayment'}{/if}</p>
  								</div>
  							</div>
  							<div class="form-group row">
  								<label for="payout_debit_amt_seller" class="required control-label col-lg-3">
  									{l s='Amount' mod='mpmangopaypayment'} :
  								</label>
  								<div class="col-lg-4">
  									<div class="input-group">
  										<input type="hidden" name="seller_wallet_currency" value="{$seller_wallet_currency}">
  										<input type="hidden" name="seller_wallet_amount" value="{$total_wallet_balance}">
  										<input name="payout_amount" id="payout_amount" type="text" autocomplete="off" class="form-control" placeholder="{l s='Enter Amount' mod='mpmangopaypayment'}">
  										<span class="input-group-addon">{$wallet_currency_sign}</span>
  									</div>
  								</div>
  							</div>
  							<div class="form-group row">
  								<label for="seller_mgp_account" class="col-lg-3 control-label required">{l s='Bank Account Id' mod='mpmangopaypayment'} :</label>
  								<div class="col-lg-4">
  									<select class="form-control" name="seller_mgp_account" id="seller_mgp_account">
  								  		<option selected="selected" value="0">{l s='Select Bank Account' mod='mpmangopaypayment'}</option>
  								  		{if isset($mgp_registered_bank_acc_ids)}
  											{foreach from=$mgp_registered_bank_acc_ids key = key item=data}
  												<option value="{$data->Id}">{$data->Type}/{$data->Id}/({$data->OwnerName})</option>
  											{foreachelse}
  												{l s='No Account Available' mod='mpmangopaypayment'}
  											{/foreach}
  										{/if}
  									</select>
  								</div>
  							</div>
  							<div class="form-group row">
  								<div class="col-lg-3"></div>
  								<div class="help-block col-lg-9">
  									{l s='Note : Bank account format is in this pattern - A/c Type / Mangopay Bank A/c Id / Mangopay Bank A/c Owner Name' mod='mpmangopaypayment'}
  								</div>
  							</div>
  							<div class="row panel-footer">
  								<div class="col-sm-12">
  									<button class="btn btn-primary pull-right" id="submit_payout_amount" name="submit_payout_amount" type="submit">
  										<span>{l s='PayOut' mod='mpmangopaypayment'}</span>
  									</button>
  								</div>
  							</div>
  						</form>
  					{else}
					    {if isset($seller_mgp_user) && $seller_mgp_user}
							<div class="alert alert-info">
								<i class="material-icons">&#xE000;</i>&nbsp;&nbsp;{l s = 'For the current mangopay currency, Wallet id has not been generated. To get a Wallet Id in the current mangopay currency please save your country once more. If you do not save the country, then also wallet Id will automatically gets generated on the first order of your product.' mod='mpmangopaypayment'}
							</div>
						{else}
							<div class="alert alert-info">
								<i class="material-icons">&#xE000;</i>&nbsp;&nbsp;{l s = "L'ID utilisateur Mangopay est manquant pour la configuration actuelle. Vous devez d'abord enregistrer votre pays. Peut-être que pour la devise actuelle de mangopay, l'identifiant de portefeuille n'a pas été généré. Pour obtenir un identifiant de portefeuille dans la devise actuelle de mangopay, veuillez enregistrer à nouveau votre pays." mod='mpmangopaypayment'}
							</div>
						{/if}
  					{/if}
  				{else}
  					<div class="alert alert-warning">
  						{l s = 'Sellers are not allowed to cash out their wallets Please contact to the admin to cash out wallets.' mod='mpmangopaypayment'}
  					</div>
  				{/if}
  			</div>
  		</div>
  	</div>
  {/if}
{/block}
