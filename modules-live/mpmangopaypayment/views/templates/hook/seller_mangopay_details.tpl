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

<div class="panel clearfix">
	<div class="panel-heading">
		<i class="icon-money"></i>
		{l s='Mangopay Bank Account details' mod='mpmangopaypayment'}
	</div>
	<div class="form-horizontal">
		<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="text-center">{l s='User Id' mod='mpmangopaypayment'}</th>
						<th class="text-center">{l s='Type' mod='mpmangopaypayment'}</th>
						<th class="text-center">{l s='Mangopay Bank Acc.Id' mod='mpmangopaypayment'}</th>
						<th class="text-center">{l s='Owner Name' mod='mpmangopaypayment'}</th>
						<th class="text-center">{l s='Owner Address' mod='mpmangopaypayment'}</th>
            <th class="text-center">{l s='Status' mod='mpmangopaypayment'}</th>
					</tr>
				</thead>
				<tbody>
					{if isset($mgp_registered_bank_acc_ids)}
						{foreach from=$mgp_registered_bank_acc_ids key = key item=data}
							<tr>
								<td class="text-center">{$data->UserId|escape:'htmlall':'UTF-8'}</td>
								<td class="text-center">{$data->Type|escape:'htmlall':'UTF-8'}</td>
								<td class="text-center">{$data->Id|escape:'htmlall':'UTF-8'}</td>
								<td class="text-center">{$data->OwnerName|escape:'htmlall':'UTF-8'}</td>
								<td class="text-center">{$data->OwnerAddress->AddressLine1|escape:'htmlall':'UTF-8'}</td>
								<td class="text-center">
								{if $data->Active}
									<form method="post" action="">
										<input type="hidden" value="{$data->Id}" name="mgp_bank_account_id">
										<input type="hidden" value="{$data->UserId}" name="mgp_bank_author_id">
										<button class="btn btn-default deactivate_bank_account" name="mgp_bank_account_deactivate" type="submit">
										{l s='Deactivate' mod='mpmangopaypayment'}
										</button>
									</form>
								{else}
									{l s='Deactivated' mod='mpmangopaypayment'}
								{/if}
								</td>
							</tr>
						{/foreach}
					{else}
						<tr>
							<td colspan="4">{l s='No accounts created yet' mod='mpmangopaypayment'}</td>
						</tr>
					{/if}
				</tbody>
			</table>
		</div>
	</div>
</div>
{strip}
  {addJsDefL name=confirm_account_deactivate_msg}{l s='Are you sure? once deactivated, a bank account can not be reactivated afterwards.' js=1 mod='mpmangopaypayment'}{/addJsDefL}
{/strip}
