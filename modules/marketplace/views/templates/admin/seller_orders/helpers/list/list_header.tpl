{*
* 2010-2017 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file="helpers/list/list_header.tpl"}
{block name=leadin}

{if !isset($shippingDetail)}
	<div class="clearfix"></div>
	<div class="panel">
		{if isset($seller)}
		<div class="panel-heading">{l s='%s --- Earning' sprintf=[$seller['shop_name_unique']] mod='marketplace'}</div>
		{else}
		<div class="panel-heading">{l s='Total Earning' mod='marketplace'}</div>
		{/if}
		{hook h="displayMpTransactionTopContent"}
		<div class="table-responsive-row clearfix">
			<table class="table wk_mp_seller_order">
				<thead>
					<tr class="nodrag nodrop">
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Total earned">
									{l s='Total Earn' mod='marketplace'}
								</span>
							</span>
						</th>
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Total commission earned">
									{l s='Admin Commission' mod='marketplace'}
								</span>
							</span>
						</th>
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Total tax earned">
									{l s='Admin Tax' mod='marketplace'}
								</span>
							</span>
						</th>
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Total seller earned">
									{l s='Seller Earning' mod='marketplace'}
								</span>
							</span>
						</th>
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Total seller received">
									{l s='Seller Received' mod='marketplace'}
								</span>
							</span>
						</th>
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Total seller due">
									{l s='Seller Due' mod='marketplace'}
								</span>
							</span>
						</th>
						{if Configuration::get('WK_MP_COMMISSION_DISTRIBUTE_ON') == 1}
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Order amount of pending orders">
									{l s='Pending Order Amount' mod='marketplace'}
								</span>
							</span>
						</th>
						{/if}
						{if isset($noListHeader)}
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Total Orders">
									{l s='Total Orders' mod='marketplace'}
								</span>
							</span>
						</th>
						{if Configuration::get('WK_MP_COMMISSION_DISTRIBUTE_ON') == 1}
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Pending Orders">
									{l s='Pending Orders' mod='marketplace'}
								</span>
							</span>
						</th>
						{/if}
						{/if}
						{if isset($settlement)}
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Settle Seller Payment">{l s='Action' mod='marketplace'}</span>
							</span>
						</th>
						{else if !isset($noshipping)}
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Total Shipping">
									{l s='Total Shipping' mod='marketplace'}
								</span>
							</span>
						</th>
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="View Shipping Details">{l s='Action' mod='marketplace'}</span>
							</span>
						</th>
						{/if}
					</tr>
				</thead>
				<tbody>
				{if isset($sellerOrderTotal) && $sellerOrderTotal}
					{foreach $sellerOrderTotal as $orderTotal}
					<tr class="wk_mp_order">
						<td class="center">
							<input type="hidden" name="wk_earn_ti" id="wk_earn_ti_{$orderTotal.id_currency}" value="{$orderTotal.no_currency_earn_ti}">
							{$orderTotal.earn_ti}
						</td>
						<td class="center">
							<input type="hidden" name="wk_admin_commission" id="wk_admin_commission_{$orderTotal.id_currency}" value="{$orderTotal.no_currency_admin_commission}">
							{$orderTotal.admin_commission}
						</td>
						<td class="center">
							<input type="hidden" name="wk_admin_tax" id="wk_admin_tax_{$orderTotal.id_currency}" value="{$orderTotal.no_currency_admin_tax}">
							{$orderTotal.admin_tax}
						</td>
						<td class="center">
							<input type="hidden" name="wk_seller_amount" id="wk_seller_amount_{$orderTotal.id_currency}" value="{$orderTotal.no_currency_seller_amount}">
							<span class="badge badge-success">{$orderTotal.seller_amount}</span>
						</td>
						<td class="center">
							<input type="hidden" name="wk_seller_paid" id="wk_seller_paid_{$orderTotal.id_currency}" value="{$orderTotal.no_currency_seller_paid}">
							<span class="badge badge-paid" style="background-color: #9e5ba1;">{$orderTotal.seller_paid}</span>
						</td>
						<td class="center">
							<input type="hidden" name="wk_seller_due" id="wk_seller_due_{$orderTotal.id_currency}" value="{$orderTotal.no_currency_seller_due}">
							<span class="badge badge-pending">{$orderTotal.seller_due}</span>
						</td>
						{if Configuration::get('WK_MP_COMMISSION_DISTRIBUTE_ON') == 1}
						<td class="center">
							<input type="hidden" name="wk_pending_amount" id="wk_pending_amount_{$orderTotal.id_currency}" value="{$orderTotal.no_currency_pending_amount}">
							<span class="badge badge-warning">{$orderTotal.pending_amount}</span>
						</td>
						{/if}
						{if isset($noListHeader)}
						<td class="center">
							{$orderTotal.total_order}
						</td>
						{if Configuration::get('WK_MP_COMMISSION_DISTRIBUTE_ON') == 1}
						<td class="center">
							<span class="badge badge-danger">{$orderTotal.pending_order}</span>
						</td>
						{/if}
						{/if}
						{if isset($settlement)}
						<th class="center">
							<a id="wk_seller_settlement" data-id-currency="{$orderTotal.id_currency}" data-mfp-src="#test-popup"
								class="btn btn-default open-popup-link"
								data-toggle="modal" data-target="#basicModal"
								{if Tools::ps_round($orderTotal.no_currency_seller_due, 2) <= 0}disabled="disabled"{/if}>
								{l s='Settle' mod='marketplace'}
							</a>
						</th>
						{hook h=displayMpSellerPaymentTableColumn seller_payment_data=$orderTotal id_seller_customer=$seller['seller_customer_id']}
						{else if !isset($noshipping)}
						<td class="center">
							<input type="hidden" name="wk_total_shipping" id="wk_total_shipping_{$orderTotal.id_currency}" value="{$orderTotal.no_currency_total_shipping}">
							{$orderTotal.total_shipping}
						</td>
						<th class="center">
							<a style="text-transform: none;" href="{$link->getAdminLink('AdminSellerOrders')}&view{$table}&mp_shipping_detail=1&id_currency={$orderTotal.id_currency}" id="wk_seller_settlement" class="btn btn-default" title="View Shipping Detail"><i class="icon-search-plus"></i>
								{l s='View Shipping Detail' mod='marketplace'}
							</a>
						</th>
						{/if}
					</tr>
				{/foreach}
				{else}
				<tr>
					<td colspan="8" class="list-empty">
						<div class="list-empty-msg">
							<i class="icon-warning-sign list-empty-icon"></i>
							No records found
						</div>
					</td>
				</tr>
				{/if}
				</tbody>
			</table>
		</div>
	</div>

	{if isset($settlement)}
		<!--- PopUp Box -->
		<div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title">{l s='SETTLEMENT SELLER AMOUNT' mod='marketplace'}</h4>
				      </div>
					<form method="post" action="" id="payment_transaction" class="form-horizontal">
						<div class="modal-body">
							<input type="hidden" id="id_seller" name="id_seller" value="{$seller['id_seller']}" />
							<input type="hidden" id="transaction_id" name="transaction_id" value="" />
							<input type="hidden" id="total_due" name="total_due" value="" />
							<input type="hidden" id="id_currency" name="id_currency" value="" />

							<div style="display: none;" class="alert alert-danger" id="wk_seller_error"></div>
							<div class="form-group">
								<label class="col-lg-3 control-label">{l s='Payment Method:' mod='marketplace'}</label>
								<div class="col-lg-5">
									<p class="form-control-static">
										{if isset($payment_mode)}{$payment_mode}{/if}
									</p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-3 control-label">{l s='Payment Details:' mod='marketplace'}</label>
								<div class="col-lg-5">
									<p class="form-control-static">
										{if isset($payment_mode_details)}{$payment_mode_details}{/if}
									</p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-3 control-label">
									{l s='Payment Method:' mod='marketplace'}
								</label>
								<div class="col-lg-5">
									<input type="text" name="wk_mp_payment_method" id="wk_mp_payment_method" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-3 control-label">
									{l s='Transaction ID:' mod='marketplace'}
								</label>
								<div class="col-lg-5">
									<input type="text" name="wk_mp_transaction_id" id="wk_mp_transaction_id" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-3 control-label">
									{l s='Remark:' mod='marketplace'}
								</label>
								<div class="col-lg-5">
									<input type="text" name="wk_mp_remark" id="wk_mp_remark" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-3 control-label">{l s='Amount:' mod='marketplace'}</label>
								<div class="col-lg-5">
									<input type="text" name="amount" id="amount" placeholder="{l s='amount' mod='marketplace'}" class="form-control" />
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="wkbtn btn btn-primary" name="submit_payment" id="pay_money"><span>{l s='Pay' mod='marketplace'}</span>
							</button>
							<button type="button" class="wkbtn btn btn-primary" data-dismiss="modal">
								<span>{l s='Cancel' mod='marketplace'}</span>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	{/if}

{/if}
<style type="text/css">
	tr.wk_mp_order td {
		padding: 8px 10px !important;
	}
	.wkbtn {
		padding: 6px 30px !important;
	}
</style>

{strip}
	{addJsDefL name=pay_more_error}{l s='You can not pay more than total due amount' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=empty}{l s='Please enter amount to pay' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=invalid_amount}{l s='Please enter valid amount' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=negative_err}{l s='Amount must be greater than zero' js=1 mod='marketplace'}{/addJsDefL}
{/strip}

{/block}
