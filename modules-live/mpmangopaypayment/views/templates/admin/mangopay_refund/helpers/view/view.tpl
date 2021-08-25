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

{if isset($transactionsDetails) && $transactionsDetails}
	<div class="alert alert-info">
		{l s='Order Reference : ' mod='mpmangopaypayment'}{$transactionsDetails['order_reference']}
	</div>
{/if}
<div class="panel">
	<h3 class="tab"> <i class="icon-cogs"></i>&nbsp;&nbsp; {l s='Mangopay Transfer Refund Management' mod='mpmangopaypayment'}</h3>
	<div class="panel-body">
		{if isset($mgp_client_id) && $mgp_client_id}
			<div class="mangopay_transfer_refund">
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th class="text-center">{l s='User Type' mod='mpmangopaypayment'}</th>
								<th class="text-center">{l s='User Email Id' mod='mpmangopaypayment'}</th>
								<th class="text-center">{l s='Amount' mod='mpmangopaypayment'}</th>
								<th class="text-center">{l s='fees' mod='mpmangopaypayment'}</th>
								<th class="text-center">{l s='Action' mod='mpmangopaypayment'}</th>
							</tr>
						</thead>
						<tbody>
						{if isset($all_mangopay_transfers_details) && $all_mangopay_transfers_details}
							{foreach from=$all_mangopay_transfers_details key = key item=data}
								<tr>
									<td class="text-center">
										{if {$data['id_seller']} == 0}
											{l s='Admin' mod='mpmangopaypayment'}
										{else}
											{l s='Seller' mod='mpmangopaypayment'}
										{/if}
									</td>
									<td class="text-center">
										{if {$data['id_seller']} != 0}
											{if isset($data['not_exist_seller']) && $data['not_exist_seller']}
												{$data['email']}
											{else}
												{$data['email']}
												(<a href="{$data['my_account_link']}">#{$data['id_seller']}</a>)
											{/if}
										{else}
											{$data['email']}
										{/if}
									</td>
									<td class="text-center">{$data['amount']}&nbsp;{$data['currency']}</td>
									<td class="text-center">{$data['fees']}&nbsp;{$data['currency']}
									</td>
									<td class="text-center">
										<form method="post" class="form-inline">
											<input type="hidden" value="{$data['order_reference']}" name="mgp_order_reference">
											<input type="hidden" value="{$data['transfer_id']}" name="mgp_transfer_id">
											<input type="hidden" value="{$data['buyer_mgp_userid']}" name="mgp_transfer_author_id">
											{if $data['is_refunded']}
												<span class="btn btn-info" disabled="disabled">
													{l s='Refunded by' mod='mpmangopaypayment'}&nbsp;{$data['refunded_by']}
													{if $data['send_to_card']}
														{l s='To Card' mod='mpmangopaypayment'}
													{else}
														{l s='To Wallet' mod='mpmangopaypayment'}
													{/if}
												</span>
											{else}
												<button class="btn btn-default" name="partial_mgp_transfer_Refund" type="submit">
												<i class="icon-check"></i> {l s='Refund' mod='mpmangopaypayment'}
												</button>
											{/if}
										</form>
									</td>
								</tr>
							{/foreach}
						{else}
							<tr>
								<td class="list-empty" colspan="12">
									<div class="list-empty-msg">
										<i class="icon-warning-sign list-empty-icon"></i>
										{l s='No Transfers Yet' mod='mpmangopaypayment'}
									</div>
								</td>
							</tr>
						{/if}
						</tbody>
					</table>
				</div>
			</div>
		{else}
			<div class="alert alert-warning">
				{l s = 'Save Your Mangopay details from module configuration before any action.' mod='mpmangopaypayment'}
			</div>
		{/if}
	</div>
</div>

<div class="panel">
	<h3 class="tab"> <i class="icon-cogs"></i>&nbsp;&nbsp; {l s='Mangopay PayIn Refunds Management' mod='mpmangopaypayment'}</h3>
	<div class="panel-body">
		{if isset($mgp_client_id) && $mgp_client_id}
			<div class="mangopay_transfer_refund">
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th class="text-center">{l s='Pay In Id' mod='mpmangopaypayment'}</th>
								<th class="text-center">{l s='Credited Wallet Id' mod='mpmangopaypayment'}</th>
								<th class="text-center">{l s='Payment Type' mod='mpmangopaypayment'}</th>
								<th class="text-center">{l s='Credited Amount' mod='mpmangopaypayment'}</th>
								<th class="text-center">{l s='Fees' mod='mpmangopaypayment'}</th>
								<th class="text-center">{l s='Action' mod='mpmangopaypayment'}</th>
							</tr>
						</thead>
						<tbody>
							{if isset($transactionsDetails) && $transactionsDetails}
								<tr>
									<td class="text-center">{$transactionsDetails['transaction_id']}</td>
									<td class="text-center">{$transactionsDetails['buyer_mgp_userid']}</td>
									<td class="text-center">{$transactionsDetails['payment_type']}</td>
									<td class="text-center">{$transactionsDetails['credited_amount']}&nbsp;{$transactionsDetails['currency']}</td>
									<td class="text-center">{$transactionsDetails['fees']}&nbsp;{$transactionsDetails['currency']}</td>
									<td class="text-center">
										<form method="post" class="form-inline">
											<div class="input-group fixed-width-md">
												<div class="input-group-addon">{$transactionsDetails['currency']}</div>
												<input type="text" value="{($transactionsDetails['credited_amount'] - ($transactionsDetails['refunded_amt']/100))}" name="mgp_partial_amount" {if ($transactionsDetails['credited_amount'] - ($transactionsDetails['refunded_amt']/100)) <= 0}disabled="disabled"{/if}>
												<input type="hidden" value="{$transactionsDetails['transaction_id']}" name="mgp_payin_id">
												<input type="hidden" value="{$transactionsDetails['order_reference']}" name="mgp_order_reference">
												<input type="hidden" value="{$transactionsDetails['buyer_mgp_userid']}" name="mgp_author_id">
												<input type="hidden" value="{$transactionsDetails['currency']}" name="mgp_currency">
											</div>

											<button class="btn btn-default" name="partial_mgp_payin_Refund" type="submit" {if ($transactionsDetails['credited_amount']-($transactionsDetails['refunded_amt']/100)) <= 0}disabled="disabled" style="background-color: #92d097"{/if}>
												<i class="icon-check"></i> {if ($transactionsDetails['credited_amount']-($transactionsDetails['refunded_amt']/100)) <= 0}{l s='Refunded' mod='mpmangopaypayment'}{else}{l s='Refund' mod='mpmangopaypayment'}{/if}
											</button>
										</form>
									</td>
								</tr>
							{else}
								<tr>
									<td class="list-empty" colspan="12">
										<div class="list-empty-msg">
											<i class="icon-warning-sign list-empty-icon"></i>
											{l s='No Pay Ins Yet' mod='mpmangopaypayment'}
										</div>
									</td>
								</tr>
							{/if}
						</tbody>
					</table>
				</div>
			</div>
		{else}
			<div class="alert alert-warning">
				{l s = 'Save Your Mangopay details from module configuration before any action.'  mod='mpmangopaypayment'}
			</div>
		{/if}
	</div>
</div>