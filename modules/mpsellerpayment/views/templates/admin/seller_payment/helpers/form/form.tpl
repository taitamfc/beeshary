{*
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($check_transaction)}
	{if $check_transaction == 1}
		<div class="alert alert-success">{l s='Payment has been done successfully.' mod='mpsellerpayment'}</div>
	{elseif $check_transaction == 2}
		<div class="alert alert-success">{l s='Some problem occured while making transaction.' mod='mpsellerpayment'}</div>
	{/if}
{/if}
<div id="fieldset_0" class="panel">
    <h3>{l s='Seller Payment Details' mod='mpsellerpayment'}</h3>
    <form class="form-horizontal">
		<div class="row">
			<label class="col-lg-3 control-label" for="product_name" >{l s='Seller Name:' mod='mpsellerpayment'}</label>
			<div class="col-lg-5">
				<p class="form-control-static">{$seller.name}</p>
			</div>
		</div>

		<div class="row">
			<label class="col-lg-3 control-label" for="product_name" >{l s='Seller Email:' mod='mpsellerpayment'}</label>
			<div class="col-lg-5">
				<p class="form-control-static">{$seller.email}</p>
			</div>
		</div>

		<div class="row">
			<label class="col-lg-3 control-label" for="product_name" >{l s='Payment Method:' mod='mpsellerpayment'}</label>
			<div class="col-lg-5">
				<p class="form-control-static">{$payment_mode}</p>
			</div>
		</div>

		<div class="row">
			<label class="col-lg-3 control-label" for="product_name" >{l s='Payment Details:' mod='mpsellerpayment'}</label>
			<div class="col-lg-5">
				<p class="form-control-static">{$payment_mode_details}</p>
			</div>
		</div>
	</form>
</div>

{hook h="DisplayMpWalletRefundhook"}
<div id="fieldset_1" class="panel">
	<div class="table-responsive-row clearfix" style="padding-top:10px;">
		<table class="table">
			<thead>
				<tr>
					<th>{l s='Total Due' mod='mpsellerpayment'}</th>
					<th>{l s='Total Earned' mod='mpsellerpayment'}</th>
					<th>{l s='Total Paid' mod='mpsellerpayment'}</th>
					<th>{l s='Currency' mod='mpsellerpayment'}</th>
					<th>{l s='Pay' mod='mpsellerpayment'}</th>
				</tr>
			</thead>
			<tbody>
			{if isset($payment_currency) && $payment_currency}
				{foreach from=$payment_currency item=data}
					<tr>
						<td>{$data.total_due}</td>
						<td>{$data.total_earning}</td>
						<td>{$data.total_paid}</td>
						<td>{$data.iso_code}</td>
						<td>
							{if $data.total_due != 0}
								<a id="pay" data-mfp-src="#test-popup"
								transaction_id="{$data.id}"
								total_due="{$data.total_due}"
								sign="{$data.sign}"
								id_currency="{$data.id_currency}"
								class="btn btn-primary open-popup-link"
								data-toggle="modal" data-target="#basicModal">
									{l s='Pay' mod='mpsellerpayment'}
								</a>
							{/if}
							{hook h=displayAdminAddToWalletLink seller_wallet_data=$data}
						</td>
					</tr>
				{/foreach}
				{else}
				<tr>
					<td colspan="5" align="center">{l s='No Records Yet' mod='mpsellerpayment'}</td>
				</tr>
				{/if}
			</tbody>
		</table>
	</div>
</div>

<div id="fieldset_2" class="panel">
	<h3 style="text-align: center;">{l s='Payment Details' mod='mpsellerpayment'}</h3>
	<div class="table-responsive-row clearfix">
		<table class="table">
			<thead>
				<tr>
					<th class="col-lg-1">{l s='Transaction Id' mod='mpsellerpayment'}</th>
					<th class="col-lg-2">{l s='Currency' mod='mpsellerpayment'}</th>
					<th class="col-lg-2">{l s='Amount' mod='mpsellerpayment'}</th>
					<th class="col-lg-2">{l s='Date' mod='mpsellerpayment'}</th>
					<th class="col-lg-2">{l s='Type' mod='mpsellerpayment'}</th>
					<th class="col-lg-2">{l s='Status' mod='mpsellerpayment'}</th>
					<th class="col-lg-1"></th>
	            </tr>
	        </thead>
	       	<tbody>
	       	{if isset($payment_transactions_details) && $payment_transactions_details}
				{foreach from=$payment_transactions_details item=data}
			        <tr>
			            <td align="center">{$data.id}</td>
						<td>{$data.currency}({$data.sign})</td>
						<td>{$data.amount}</td>
						<td>{$data.date}</td>
						<td>{$data.type}</td>
						<td>
							{if $data.status == '1'}
								<span class="status_{$data.id}" style="color:green;text-transform: uppercase;">{l s='Success' mod='mpsellerpayment'}</span>
							{else}
								<span class="status_{$data.id}" style="color:red;text-transform: uppercase;">{l s='Cancel' mod='mpsellerpayment'}</span>
							{/if}
						</td>
						<td>
							<button id="change" {if isset($data.wallet_info_id)}wallet_info_id="{$data.wallet_info_id}" voucher_amt="{$data.voucher_amt}"  voucher_code="{$data.voucher_code}"seller_payment_id="{$data.seller_payment_id}"{/if} curr_id="{$data.id_currency}" type="{$data.type}" id-transaction="{$data.id}"  transaction_amount="{$data.amount}" trans_slr_pay_id="{$data.transaction_seller_payment_id}" value="{$data.status}" class="btn btn-primary change_status">
								{if $data.status == '1'}{l s='Cancel Payment' mod='mpsellerpayment'}
								{else}{l s='Pay Again' mod='mpsellerpayment'}{/if}
							</button>
						</td>
			        </tr>
				{/foreach}
				{else}
				<tr>
					<td colspan="6" align="center">{l s='No Records Yet' mod='mpsellerpayment'}</td>
				</tr>
				{/if}
			</tbody>
		</table>
	</div>
</div>

<!--- PopUp Box -->

<div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" action="" id="payment_transaction" class="form-horizontal">
				<div class="modal-body">
					<input type="hidden" id="id_seller" name="id_seller" value="{$id_seller}" />
					<input type="hidden" id="transaction_id" name="transaction_id" value="" />
					<input type="hidden" id="total_due" name="total_due" value="" />
					<input type="hidden" id="id_currency" name="id_currency" value="" />

					<div class="row">
						<label class="col-lg-3 control-label" for="product_name" >{l s='Payment Method:' mod='mpsellerpayment'}</label>
						<div class="col-lg-5">
							<p class="form-control-static">{$payment_mode}</p>
						</div>
					</div>

					<div class="row">
						<label class="col-lg-3 control-label" for="product_name" >{l s='Payment Details:' mod='mpsellerpayment'}</label>
						<div class="col-lg-5">
							<p class="form-control-static">{$payment_mode_details}</p>
						</div>
					</div>
					<div class="row">
						<label class="col-lg-3 control-label" for="product_name" >{l s='Money Send:' mod='mpsellerpayment'}</label>
						<div class="col-lg-5">
							<input type="text" name="amount" class="money_text" placeholder="{l s='amount' mod='mpsellerpayment'}" class="form-control" />
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" name="submit_btn" id="pay_money">
						<span>{l s='Pay' mod='mpsellerpayment'}</span>
					</button>
					<button type="button" class="btn btn-primary" data-dismiss="modal">
						<span>{l s='Cancel' mod='mpsellerpayment'}</span>
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

{strip}
	{addJsDefL name=voucher_when_no_wallet_txt}{l s='Unable to create this transaction again because Marketplace seller wallet module is uninstalled. So it may lead to data conflict.' js=1 mod='mpsellerpayment'}{/addJsDefL}
	{addJsDefL name=success_msg_create_voucher}{l s='Voucher has been created successfully.' js=1 mod='mpsellerpayment'}{/addJsDefL}
	{addJsDefL name=success_msg_refund}{l s='Voucher has been refunded successfully.' js=1 mod='mpsellerpayment'}{/addJsDefL}
	{addJsDef wallet_not_exists = $wallet_not_exists}
	{addJsDef voucher_create_link = $link->getModulelink('mpsellerwallet', 'mpwalletdataprocess')}
	{addJsDef voucher_refund_link = $link->getModulelink('mpsellerwallet', 'sellervoucherrefund')}

	{addJsDef module_path = $link->getModulelink('mpsellerpayment', 'changestatus')}

	{addJsDefL name=voucher_case_error}{l s='You can\'t change this status. Go to Wallet Balance and refund this voucher if you want.' js=1 mod='mpsellerpayment'}{/addJsDefL}
	{addJsDefL name=confirm_status}{l s='Are you sure you want to change the status' js=1 mod='mpsellerpayment'}{/addJsDefL}
	{addJsDefL name=low_amt_error}{l s='Seller has not enough amount in this transaction currency to change this status.' js=1 mod='mpsellerpayment'}{/addJsDefL}
	{addJsDefL name=error_transaction_msg}{l s='Some error occured.Please try again.' js=1 mod='mpsellerpayment'}{/addJsDefL}

	{addJsDefL name=blank_error}{l s='Money send amount is required' js=1 mod='mpsellerpayment'}{/addJsDefL}
	{addJsDefL name=numeric_error}{l s='Money send amount must be numeric value' js=1 mod='mpsellerpayment'}{/addJsDefL}
	{addJsDefL name=not_zero_error}{l s='Money send amount must be greater than Zero' js=1 mod='mpsellerpayment'}{/addJsDefL}
	{addJsDefL name=due_total_error}{l s='Money send amount must be less than Total Due payment' js=1 mod='mpsellerpayment'}{/addJsDefL}
	{addJsDefL name=Success}{l s='Success' js=1 mod='mpsellerpayment'}{/addJsDefL}
	{addJsDefL name=Cancel}{l s='Cancel' js=1 mod='mpsellerpayment'}{/addJsDefL}
	{addJsDefL name=cancel_payment}{l s='CANCEL PAYMENT' js=1 mod='mpsellerpayment'}{/addJsDefL}
	{addJsDefL name=pay_again}{l s='PAY AGAIN' js=1 mod='mpsellerpayment'}{/addJsDefL}
{/strip}
