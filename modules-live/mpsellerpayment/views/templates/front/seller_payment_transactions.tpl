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

{extends file=$layout}
{block name='content'}
<div class="main_block">
  {hook h="DisplayMpmenuhook"}
  <div class="dashboard_content">
    <div class="page-title" style="background-color:{$title_bg_color};">
      <span style="color:{$title_text_color};">{l s='Seller Transactions' mod='mpsellerpayment'}</span>
    </div>
    <div class="wk_right_col">
      {hook h="DisplayMpWalletRefundhook"}
			<div id="seller_transactions">
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>{l s='Total Due' mod='mpsellerpayment'}</th>
								<th>{l s='Total Earning' mod='mpsellerpayment'}</th>
								<th>{l s='Total Paid' mod='mpsellerpayment'}</th>
								<th>{l s='Currency' mod='mpsellerpayment'}</th>
							</tr>
						</thead>
						<tbody>
							{if isset($payment_currency)}
								{foreach from=$payment_currency item=data}
									<tr>
										<td>{$data.total_due|escape:'htmlall':'UTF-8'}</td>
										<td>{$data.total_earning|escape:'htmlall':'UTF-8'}</td>
										<td>{$data.total_paid|escape:'htmlall':'UTF-8'}</td>
										<td>{$data.iso_code|escape:'htmlall':'UTF-8'}
										{hook h=displayAddToWalletLink seller_wallet_data=$data}
										</td>
									</tr>
								{/foreach}
							{else}
								<tr>
									<td colspan="4">{l s='No data found' mod='mpsellerpayment'}</td>
								</tr>
							{/if}
						</tbody>
					</table>
				</div>
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>{l s='Transaction Id' mod='mpsellerpayment'}</th>
								<th>{l s='Currency' mod='mpsellerpayment'}</th>
								<th>{l s='Amount' mod='mpsellerpayment'}</th>
								<th>{l s='Date' mod='mpsellerpayment'}</th>
								<th>{l s='Type' mod='mpsellerpayment'}</th>
								<th>{l s='Status' mod='mpsellerpayment'}</th>
							</tr>
						</thead>
						<tbody>
							{if isset($payment_transactions)}
								{foreach from=$payment_transactions item=data}
				                	<tr>
				                        <td>{$data.id|escape:'htmlall':'UTF-8'}</td>
				                        <td>{$data.currency|escape:'htmlall':'UTF-8'}({$data.sign|escape:'htmlall':'UTF-8'})</td>
				                        <td>{$data.amount|escape:'htmlall':'UTF-8'}</td>
				                        <td>{$data.date|escape:'htmlall':'UTF-8'}</td>
				                        <td>{$data.type|escape:'htmlall':'UTF-8'}</td>
										<td>
											{if $data.status == '1'}
												<span style="color:green;text-transform: uppercase;">{l s='Success' mod='mpsellerpayment'}</span>
											{else}
												<span style="color:red;text-transform: uppercase;">{l s='Cancel' mod='mpsellerpayment'}</span>
											{/if}
										</td>
				                    </tr>
				                {/foreach}
			                {else}
			                	<tr>
			                		<td colspan="6">{l s='No data found' mod='mpsellerpayment'}</td>
			                	</tr>
			                {/if}
			            </tbody>
			        </table>
			    </div>
			</div>
		</div>
	</div>
</div>
<style type="text/css">
#seller_transactions{
	float: left;
	width: 100%;
}
</style>
{/block}
