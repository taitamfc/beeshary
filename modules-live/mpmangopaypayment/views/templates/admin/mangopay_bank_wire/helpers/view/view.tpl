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

{if isset($bankwireDetails) && $bankwireDetails}
	<div class="alert alert-info">
		{l s='Transaction details for Order Reference : ' mod='mpmangopaypayment'}{$bankwireDetails['order_reference']|escape:'htmlall':'UTF-8'}</br>
		{l s='Transaction Status : ' mod='mpmangopaypayment'}{$status|escape:'htmlall':'UTF-8'}
	</div>
{/if}

<div class="panel">
	<h3 class="tab"> <i class="icon-book"></i> {l s='Mangopay BankWire Details' mod='mpmangopaypayment'}</h3>
	<div class="panel-body">
		{if isset($bankwireDetails) && $bankwireDetails}
			<div class="row">
				<label class="col-lg-4 text-right">{l s='PayIn Id' mod='mpmangopaypayment'} : </label>
				<div class="col-lg-8">
					<p class="form-control-static">{$bankwireDetails['transaction_id']|escape:'htmlall':'UTF-8'}</p>
				</div>
			</div>
			<div class="row">
				<label class="col-lg-4 text-right">{l s='Author Id' mod='mpmangopaypayment'} : </label>
				<div class="col-lg-8">
					<p class="form-control-static">{$bankwireDetails['buyer_mgp_userid']|escape:'htmlall':'UTF-8'}</p>
				</div>
			</div>
			<div class="row">
				<label class="col-lg-4 text-right">{l s='BankWire Reference' mod='mpmangopaypayment'} : </label>
				<div class="col-lg-8">
					<p class="form-control-static">{$bankwireDetails['mgp_wire_reference']|escape:'htmlall':'UTF-8'}</p>
				</div>
			</div>
			<div class="row">
				<label class="col-lg-4 text-right">{l s='Account Type' mod='mpmangopaypayment'} : </label>
				<div class="col-lg-8">
					<p class="form-control-static">{$bankwireDetails['mgp_account_type']|escape:'htmlall':'UTF-8'}</p>
				</div>
			</div>
			<div class="row">
				<label class="col-lg-4 text-right">{l s='Account Owner Name' mod='mpmangopaypayment'} : </label>
				<div class="col-lg-8">
					<p class="form-control-static">{$bankwireDetails['mgp_account_owner_name']|escape:'htmlall':'UTF-8'}</p>
				</div>
			</div>
			<div class="row">
				<label class="col-lg-4 text-right">{l s='Account IBAN' mod='mpmangopaypayment'} : </label>
				<div class="col-lg-8">
					<p class="form-control-static">{$bankwireDetails['mgp_account_iban']|escape:'htmlall':'UTF-8'}</p>
				</div>
			</div>
			<div class="row">
				<label class="col-lg-4 text-right">{l s='Account BIC' mod='mpmangopaypayment'} : </label>
				<div class="col-lg-8">
					<p class="form-control-static">{$bankwireDetails['mgp_account_bic']|escape:'htmlall':'UTF-8'}</p>
				</div>
			</div>
			<div class="row">
				<label class="col-lg-4 text-right">{l s='Date' mod='mpmangopaypayment'} : </label>
				<div class="col-lg-8">
					<p class="form-control-static">{dateFormat date=$bankwireDetails['date_add'] full=true}</p>
				</div>
			</div>
			<div class="row">
				<label class="col-lg-4 text-right">{l s='PayIn State' mod='mpmangopaypayment'} : </label>
				{if $status == 'SUCCEEDED' && empty($transferDetails)}
					<div class="col-lg-5">
						<span class="badge badge-success">
							{$status|escape:'htmlall':'UTF-8'}
						</span>
						<a class="btn btn-primary" href="{$link->getAdminLink('AdminMangopayBankWire')|escape:'htmlall':'UTF-8'}&id={$bankwireDetails['id']|escape:'htmlall':'UTF-8'}&releasepayment=1">
							<i class="icon-check"></i>
							{l s='click here to transfer amount to wallet' mod='mpmangopaypayment'}
						</a>
					</div>
				{elseif $status == 'SUCCEEDED' && !empty($transferDetails)}
					<div class="col-lg-2">
						<span class="badge badge-success">
							{$status|escape:'htmlall':'UTF-8'}
						</span>
					</div>
				{else}
					<div class="col-lg-2">
						<span class="badge badge-warning">
							{$status|escape:'htmlall':'UTF-8'}
						</span>
					</div>
				{/if}
			</div>
			{if $status != 'SUCCEEDED'}
				<br>
				<div class="row">
					<div class="alert alert-info">
						{l s='Currently Bankwire from customer is not received so you can not transfer amount to sellers. As soon as bankwire will be recieved you can transfer amount to mangopay wallet.' mod='mpmangopaypayment'}
					</div>
				</div>
			{/if}
		{else}
			<div class="alert alert-info">
				{l s='No bankwire details found for this transaction.' mod='mpmangopaypayment'}
			</div>
		{/if}
	</div>
</div>

<div class="panel">
	<h3 class="tab"> <i class="icon-cogs"></i>&nbsp;&nbsp; {l s='Mangopay Transfers Refund Management' mod='mpmangopaypayment'}</h3>
	<div class="panel-body">
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
					{if !empty($transferDetails)}
						{foreach $transferDetails as $transfer}
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
							<td class="list-empty" colspan="7">
								<div class="list-empty-msg">
									<i class="icon-warning-sign list-empty-icon"></i>
									{l s='No transfers yet' mod='mpmangopaypayment'}
								</div>
							</td>
						</tr>
					{/if}
				</tbody>
			</table>
		</div>
	</div>
</div>