{*
* 2010-2018 Webkul
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

{if isset($allTransactionsDetails) && $allTransactionsDetails}
	<div class="alert alert-info">
		{l s='Transaction details for Order Reference : ' mod='mpmangopaypayment'}{$allTransactionsDetails['0']['order_reference']|escape:'htmlall':'UTF-8'}</br>
		{l s='Transaction Status : ' mod='mpmangopaypayment'}{$payInStatus|escape:'htmlall':'UTF-8'}
	</div>
{/if}
<div class="panel">
	<h3 class="tab"> <i class="icon-cogs"></i>&nbsp;&nbsp; {l s='Mangopay PayIn Refunds Management' mod='mpmangopaypayment'}</h3>
	<div class="panel-body">
		{if isset($mgpClientId) && $mgpClientId}
			<div class="mangopay_transfer_refund">
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th class="text-center">{l s='Pay In Id' mod='mpmangopaypayment'}</th>
								<th class="text-center">{l s='Credited Wallet Id' mod='mpmangopaypayment'}</th>
								<th class="text-center">{l s='Mandate Id' mod='mpmangopaypayment'}</th>
								<th class="text-center">{l s='Payment Type' mod='mpmangopaypayment'}</th>
								<th class="text-center">{l s='Credited Amount' mod='mpmangopaypayment'}</th>
								<th class="text-center">{l s='Fees' mod='mpmangopaypayment'}</th>
								<th class="text-center">{l s='Action' mod='mpmangopaypayment'}</th>
							</tr>
						</thead>
						<tbody>
							{if isset($transactionsDetails) && $transactionsDetails}
								<tr>
									<td class="text-center">{$transactionsDetails['transaction_id']|escape:'htmlall':'UTF-8'}</td>
									<td class="text-center">{$transactionsDetails['buyer_mgp_userid']|escape:'htmlall':'UTF-8'}</td>
									<td class="text-center">{$transactionsDetails['mandate_id']|escape:'htmlall':'UTF-8'}</td>
									<td class="text-center">{$transactionsDetails['payment_type']|escape:'htmlall':'UTF-8'}</td>
									<td class="text-center">{$transactionsDetails['credited_amount']|escape:'htmlall':'UTF-8'}&nbsp;{$transactionsDetails['currency']|escape:'htmlall':'UTF-8'}</td>
									<td class="text-center">{$transactionsDetails['fees']|escape:'htmlall':'UTF-8'}&nbsp;{$transactionsDetails['currency']|escape:'htmlall':'UTF-8'}</td>
									<td class="text-center">
										{if $payInStatus == 'SUCCEEDED'}
											<form method="post" class="form-inline">
												<div class="input-group fixed-width-md">
													<div class="input-group-addon">{$transactionsDetails['currency']|escape:'htmlall':'UTF-8'}</div>
													<input type="text" value="{($transactionsDetails['credited_amount'] - ($transactionsDetails['refunded_amt']/100))|escape:'htmlall':'UTF-8'}" name="mgp_partial_amount" {if ($transactionsDetails['credited_amount'] - ($transactionsDetails['refunded_amt']/100)) <= 0}disabled="disabled"{/if}>
													<input type="hidden" value="{$transactionsDetails['transaction_id']|escape:'htmlall':'UTF-8'}" name="mgp_payin_id">
													<input type="hidden" value="{$transactionsDetails['buyer_mgp_userid']|escape:'htmlall':'UTF-8'}" name="mgp_author_id">
													<input type="hidden" value="{$transactionsDetails['currency']|escape:'htmlall':'UTF-8'}" name="mgp_currency">
													<input type="hidden" value="{$transactionsDetails['order_reference']|escape:'htmlall':'UTF-8'}" name="mgp_order_reference">
												</div>
												<button class="btn btn-default" name="partial_mgp_payin_Refund" type="submit" {if ($transactionsDetails['credited_amount']-($transactionsDetails['refunded_amt']/100)) <= 0}disabled="disabled" style="background-color: #92d097"{/if}>
													<i class="icon-check"></i> {if ($transactionsDetails['credited_amount']-($transactionsDetails['refunded_amt']/100)) <= 0}{l s='Refunded' mod='mpmangopaypayment'}{else}{l s='Refund' mod='mpmangopaypayment'}{/if}
												</button>
											</form>
										{else}
											<i>{l s='Payment Not Recieved' mod='mpmangopaypayment'}</i>
										{/if}
									</td>
								</tr>
							{else}
								<tr>
									<td class="list-empty" colspan="12">
										<div class="list-empty-msg">
											<i class="icon-warning-sign list-empty-icon"></i>
											{l s='No PayIns Yet' mod='mpmangopaypayment'}
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
<div class="panel">
	<h3 class="tab"> <i class="icon-cogs"></i>&nbsp;&nbsp; {l s='Mangopay Transfer Refund Management' mod='mpmangopaypayment'}</h3>
	<div class="panel-body">
		{if $payInStatus == 'SUCCEEDED'}
			{if isset($allTransfersDetails) && $allTransfersDetails}
				{if isset($mgpClientId) && $mgpClientId}
					<div class="mangopay_transfer_refund">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th class="text-center">{l s='User Type' mod='mpmangopaypayment'}</th>
										<th class="text-center">{l s='User Email Id' mod='mpmangopaypayment'}</th>
										<th class="text-center">{l s='Amount' mod='mpmangopaypayment'}</th>
										<th class="text-center">{l s='fees' mod='mpmangopaypayment'}</th>
										<th class="text-center">{l s='Date' mod='mpmangopaypayment'}</th>
										<th class="text-center">{l s='Action' mod='mpmangopaypayment'}</th>
									</tr>
								</thead>
								<tbody>
								{if isset($allTransfersDetails) && $allTransfersDetails}
									{foreach $allTransfersDetails as $transfer}
										<tr>
											<td class="text-center">
												{if {$transfer['id_seller']} == 0}
													{l s='Admin' mod='mpmangopaypayment'}
												{else}
													{l s='Seller' mod='mpmangopaypayment'}
												{/if}
											</td>
											<td class="text-center">
												{if {$transfer['id_seller']} != 0}
													{if isset($transfer['not_exist_seller']) && $transfer['not_exist_seller']}
														{$transfer['email']}
													{else}
														{$transfer['email']}
														(<a href="{$transfer['my_account_link']}">#{$transfer['id_seller']}</a>)
													{/if}
												{else}
													{$transfer['email']}
												{/if}
											</td>
											<td class="text-center">
												{$transfer['amount']|escape:'htmlall':'UTF-8'}&nbsp;{$transfer['currency']|escape:'htmlall':'UTF-8'}
											</td>
											<td class="text-center">
												{$transfer['fees']|escape:'htmlall':'UTF-8'}&nbsp;{$transfer['currency']|escape:'htmlall':'UTF-8'}
											</td>
											<td class="text-center">
												{dateFormat date=$transfer['date_add']|escape:'htmlall':'UTF-8' full=true}
											</td>
											<td class="text-center">
												<form method="post" class="form-inline">
													<input type="hidden" value="{$transfer['order_reference']|escape:'htmlall':'UTF-8'}" name="mgp_order_reference">
													<input type="hidden" value="{$transfer['transfer_id']|escape:'htmlall':'UTF-8'}" name="mgp_transfer_id">
													<input type="hidden" value="{$transfer['buyer_mgp_userid']|escape:'htmlall':'UTF-8'}" name="mgp_transfer_author_id">
													{if $transfer['is_refunded']}
														<span class="btn btn-info" disabled="disabled">
															{l s='Refunded by' mod='mpmangopaypayment'}&nbsp;{$transfer['refunded_by']|escape:'htmlall':'UTF-8'}
															{if $transfer['send_to_card']}
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
			{else}
				<div class="row">
					<label class="col-lg-12">{l s='This Transaction is Sucuuessful. You can transfer money to wallets by clicking ' mod='mpmangopaypayment'}
						<a class="btn btn-primary" href="{$link->getAdminLink('AdminMangopayDirectDebit')|escape:'htmlall':'UTF-8'}&id={$id_transaction|escape:'htmlall':'UTF-8'}&releasepayment=1}">
							<i class="icon-check"></i>
							{l s='Transfer To Wallets' mod='mpmangopaypayment'}
						</a>
					</label>
				</div>
			{/if}
		{else}
			<div class="alert alert-warning">
				{l s='Payment is not recieved for this transaction till now. So please wait. As soon as payment will be recieved, you can transafer payments to wallets.' mod='mpmangopaypayment'}
			</div>
		{/if}
	</div>
</div>