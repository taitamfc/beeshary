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

{if isset($all_mangopay_transfers_details) && $all_mangopay_transfers_details}
	<style>
	.box-account.box-recent{
		clear:both;
	}
	</style>
	<div class="box-account box-recent">
		{if isset($smarty.get.payin_refunded)}
			<div class="alert alert-success">
				{l s='Transfer and Pay in Refunded successfully' mod='mpmangopaypayment'}
			</div>
		{/if}
		{if isset($smarty.get.transfer_refunded)}
			<div class="alert alert-success">
				{l s='Transfer Refunded successfully' mod='mpmangopaypayment'}
			</div>
		{/if}
		{if isset($mgpRefundErr)}
			<div class="alert alert-danger">
				{$mgpRefundErr|escape:'htmlall':'UTF-8'}
			</div>
		{/if}
		<div class="box-head">
			<h2><i class="icon-user"></i> {l s='Mangopay transfer Detail' mod='mpmangopaypayment'}</h2>
			<div class="wk_border_line"></div>
		</div>
		<div class="box-content">
			{if isset($mgp_client_id) && $mgp_client_id}
				<div class="mangopay_transfer_refund">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th class="text-center">{l s='Transfer Id' mod='mpmangopaypayment'}</th>
									<th class="text-center">{l s='Credited Wallet Id' mod='mpmangopaypayment'}</th>
									<th class="text-center">{l s='Debited Wallet Id' mod='mpmangopaypayment'}</th>
									<th class="text-center">{l s='Debited Amount' mod='mpmangopaypayment'}</th>
									<th class="text-center">{l s='Credited Amount' mod='mpmangopaypayment'}</th>
									<th class="text-center">{l s='Fees' mod='mpmangopaypayment'}</th>
									<th class="text-center">{l s='Action' mod='mpmangopaypayment'}</th>
								</tr>
							</thead>
							<tbody>
								{foreach from=$all_mangopay_transfers_details key = key item=data}
									<tr>
										<td class="text-center">{$data['Id']|escape:'htmlall':'UTF-8'}</td>
										<td class="text-center">{$data['CreditedWalletId']|escape:'htmlall':'UTF-8'}</td>
										<td class="text-center">{$data['DebitedWalletId']|escape:'htmlall':'UTF-8'}</td>
										<td class="text-center">
											{($data['DebitedFunds']->Amount)/100|escape:'htmlall':'UTF-8'}&nbsp;{$data['DebitedFunds']->Currency|escape:'htmlall':'UTF-8'}
										</td>
										<td class="text-center">{($data['CreditedFunds']->Amount)/100|escape:'htmlall':'UTF-8'}&nbsp;{$data['CreditedFunds']->Currency|escape:'htmlall':'UTF-8'}</td>
										<td class="text-center">{($data['Fees']->Amount)/100|escape:'htmlall':'UTF-8'}&nbsp;{$data['Fees']->Currency|escape:'htmlall':'UTF-8'}</td>
										<td class="text-center">
											<form method="post" action="">
												<input type="hidden" value="{$data['Id']|escape:'htmlall':'UTF-8'}" name="mgp_transfer_id">
												<input type="hidden" value="{$data['CreditedFunds']->Currency|escape:'htmlall':'UTF-8'}" name="mgp_currency">
												<input type="hidden" value="{$data['AuthorId']|escape:'htmlall':'UTF-8'}" name="mgp_transfer_author_id">
												{if $data['is_refunded']}
													<span class="btn btn-info btn-sm" disabled="disabled">
														{l s='Refunded by' mod='mpmangopaypayment'}&nbsp;{$data['refunded_by']}
														{if $data['send_to_card']}
															{l s='To Card' mod='mpmangopaypayment'}
														{else}
															{l s='To Wallet' mod='mpmangopaypayment'}
														{/if}
													</span>
												{else}
													<button class="btn btn-primary btn-sm" name="partial_mgp_transfer_Refund" type="submit">
													<i class="icon-check"></i> {l s='Refund' mod='mpmangopaypayment'}
													</button>
												{/if}
											</form>
										</td>
									</tr>
								{/foreach}
							</tbody>
						</table>
					</div>
				</div>
			{else}
				<div class="alert alert-warning">
					{l s = 'Save Mangopay details from module configuration before any action.' mod='mpmangopaypayment'}
				</div>
			{/if}
		</div>
	</div>
{/if}
