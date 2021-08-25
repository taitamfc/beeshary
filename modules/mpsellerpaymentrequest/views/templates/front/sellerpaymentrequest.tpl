{*
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{block name='content'}
	<div class="wk-mp-block">
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
				<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Payment Request' mod='mpsellerpaymentrequest'}</span>
			</div>
			<div class="wk-mp-right-column">
				<p class="wk_text_right">
					<a title="{l s='Add New Payment Request' mod='mpsellerpaymentrequest'}"
					id="add_payment_reqeust">
						<button class="btn btn-primary btn-sm" type="button">
							<i class="material-icons"></i>
							{l s='Add New Payment Request' mod='mpsellerpaymentrequest'}
						</button>
					</a>
				</p>
				<div id="seller_transactions">
					<div class="table-responsive">
						<table class="table" id="payment_request-table">
							<thead>
								<tr>
									<th style="display:none;">{l s='ID' mod='mpsellerpaymentrequest'}</th>
									<th>{l s='Request Amount' mod='mpsellerpaymentrequest'}</th>
									<th>{l s='Status' mod='mpsellerpaymentrequest'}</th>
									<th>{l s='Remark' mod='mpsellerpaymentrequest'}</th>
									<th>{l s='Date' mod='mpsellerpaymentrequest'}</th>
								</tr>
							</thead>
							<tbody>
								{if isset($requests)}
									{foreach from=$requests item=request}
										<tr>
											<td style="display:none;">{$request.id_seller_payment_request|intval}</td>
											<td>{$request.request_amount_currency|escape:'htmlall':'UTF-8'}</td>
											<td>{if $request.status eq 0}
												<span class="wkbadge wkbadge-pending">
													{l s='Pending' mod='mpsellerpaymentrequest'}
												</span>
												{elseif $request.status eq 1}
												<span class="wkbadge wkbadge-success">
													{l s='Approved' mod='mpsellerpaymentrequest'}
												</span>
												{else}
												<span class="wkbadge wkbadge-danger">
													{l s='Declined' mod='mpsellerpaymentrequest'}
												</span>
												{/if}
											</td>
											<td>{if $request.remark neq ''}
												{$request.remark|escape:'htmlall':'UTF-8'}
											{/if}</td>
											<td>{dateFormat date=$request.date_add full=1}</td>
										</tr>
									{/foreach}
								{else}
								<td colspan="3">{l s='No request found' mod='mpsellerpaymentrequest'}</td>
								{/if}
							</tbody>
						</table>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<!--- Order Detail PopUp Box -->
	<div class="modal fade" id="paymentRequest" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content" id="wk_seller_payment_request"></div>
	    </div>
	</div>
{/block}
