{*
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{block name='content'}
<div class="wk-mp-block">
	{hook h="displayMpMenu"}
	<div class="wk-mp-content">
		<div class="page-title" style="background-color:{$title_bg_color};">
			<span style="color:{$title_text_color};">
				{l s='MemberShip Plan Details' mod='mpsellermembership'}
			</span>
		</div>
		<div class="wk-mp-right-column table-responsive">
			<div style="width:100%;float:left;text-align:right;margin-bottom:1%;">
				<a class="pull-right" href="{$link->getModuleLink('mpsellermembership', 'mpmembershipplans')}">
					<button class="btn btn-primary btn-sm" type="button">
						<i class="material-icons">&#xE8F7;</i>
						{l s='View All membership Plans' mod='mpsellermembership'}
					</button>
				</a>
			</div>
			<table class="table table-hover table-bordered" style="float:left;width:100%;">
				<thead>
					<tr>
						<th>{l s='Plan Name' mod='mpsellermembership'}</th>
						<th>{l s='Plan Price' mod='mpsellermembership'}</th>
						<th>{l s='Number of Products Allowed' mod='mpsellermembership'}</th>
						<th>{l s='Requested On' mod='mpsellermembership'}</th>
						<th>{l s='Activated On' mod='mpsellermembership'}</th>
						<th>{l s='Expire On' mod='mpsellermembership'}</th>
						<th>{l s='Status' mod='mpsellermembership'}</th>
					</tr>
				</thead>
				<tbody>
					{assign var=no_plan value=1}
					{if isset($free_plan)}
						{assign var=no_plan value=0}
						<tr>
							<td>{l s='Free Plan' mod='mpsellermembership'}</td>
							<td>{l s='Free Plan' mod='mpsellermembership'}</td>
							<td>{$free_plan['num_products_allow']}</td>
							<td>{$free_plan['date_add']}</td>
							<td>{$free_plan['active_from']}</td>
							<td>{$free_plan['expire_on']}</td>
							<td>{if $free_plan['status'] == 3}{l s='Active' mod='mpsellermembership'}{else if $free_plan['status'] == 2}{l s='Pending' mod='mpsellermembership'}{else}{l s='Expired' mod='mpsellermembership'}{/if}</td>
						</tr>
					{/if}
					{if isset($all_plan)}
						{assign var=no_plan value=0}
						{foreach $all_plan as $key => $plan_details}
							<tr>
								<td>{$plan_details['plan_name']}</td>
								<td>{$plan_details['plan_price']}</td>
								<td>{$plan_details['num_products_allow']}</td>
								<td>{$plan_details['date_add']}</td>
								<td>{$plan_details['active_from']}</td>
								<td>{$plan_details['expire_on']}</td>
								<td>
									{if $plan_details['status'] == 3}{l s='Active' mod='mpsellermembership'}{else if $plan_details['status'] == 2}{l s='Pending' mod='mpsellermembership'}{else}{l s='Expired' mod='mpsellermembership'}{/if}
									{if ($key+1) == $all_plan_count && $is_any_plan_active == 0 && $plan_details['plan_status'] == 1}
										<a href="{$link->getModuleLink('mpsellermembership', 'mpsellerplans', ['addtocart' => 1, 'id_product' => $plan_details['id_product']])}" class="btn btn-primary"><span>{l s='Renew' mod='mpsellermembership'}</span></a>
									{/if}
								</td>
							</tr>
						{/foreach}
					{/if}
					{if $no_plan == 1}
						<tr>
							<td colspan="7">{l s='You have not requested any membership plan yet' mod='mpsellermembership'}</td>
						</tr>
					{/if}
				</tbody>
			</table>
		</div>
	</div>
</div>
{/block}