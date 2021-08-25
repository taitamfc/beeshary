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
	{if isset($smarty.get.created_conf)}
		<p class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Created Successfully' mod='mpbooking'}
		</p>
	{else if isset($smarty.get.edited_conf)}
		<p class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Updated Successfully' mod='mpbooking'}
		</p>
	{/if}
	{if isset($smarty.get.deleted)}
		<p class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Deleted Successfully' mod='mpbooking'}
		</p>
	{/if}

	{if isset($smarty.get.status_updated)}
		<p class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Status updated Successfully' mod='mpbooking'}
		</p>
	{/if}
	<div class="wk-mp-block">
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color};">
				<span style="color:{$title_text_color};">{l s='Feature Price Plans' mod='mpbooking'}</span>
			</div>
			<div class="wk-mp-right-column">
				<div class="wk_feature_plans_list wk_product_list">
					<p class="wk_text_right">
						<a title="{l s='Add Plans' mod='mpbooking'}" href="{url entity='module' name='mpbooking' controller='mpfeaturepriceplan'}">
							<button class="btn btn-primary btn-sm" type="button">
								<i class="material-icons">&#xE145;</i>
								{l s='Add Plans' mod='mpbooking'}
							</button>
						</a>
					</p>
					<form action="{url entity='module' name='mpbooking' controller='mpfeaturepriceplanslist'}" method="post" id="mp_productlist_form">
						<input type="hidden" name="token" id="wk-static-token" value="{$static_token}">
						<table class="table table-striped wk-mpbooking-datatable">
							<thead>
								<tr>
									{if $featurePricePlansList|@count > 1}
										<th class="no-sort">
											<input type="checkbox" title="{l s='Select all' mod='mpbooking'}" id="mp_all_select"/>
										</th>
									{/if}
									<th>{l s='Id' mod='mpbooking'}</th>
									<th>{l s='Product Id' mod='mpbooking'}</th>
									<th>{l s='Feature Name' mod='mpbooking'}</th>
									<th>{l s='Impact Way' mod='mpbooking'}</th>
									<th>{l s='Impact Type' mod='mpbooking'}</th>
									<th>{l s='Impact Value' mod='mpbooking'}</th>
									<th>{l s='Status' mod='mpbooking'}</th>
									<th>{l s='Action' mod='mpbooking'}</th>
								</tr>
							</thead>
							<tbody>
								{if isset($featurePricePlansList) && $featurePricePlansList}
									{foreach $featurePricePlansList as $plan}
										<tr>
											{if $featurePricePlansList|@count > 1}
												<td><input type="checkbox" name="mp_plans_selected[]" class="mp_bulk_select" value="{$plan.id_feature_price_rule}"/></td>
											{/if}
											<td>
												{$plan['id_feature_price_rule']}
											</td>
											<td>
												{$plan['id_mp_product']}
											</td>
											<td>
												{$plan['feature_price_name']}
											</td>
											<td>
												{if $plan['impact_way'] == 1}
													{l s='Decrease' mod='mpbooking'}
												{else}
													{l s='Increase' mod='mpbooking'}
												{/if}
											</td>
											<td>
												{if $plan['impact_type'] == 1}
													{l s='Percentage' mod='mpbooking'}
												{else}
													{l s='Fixed Amount' mod='mpbooking'}
												{/if}
											</td>
											<td>
												{$plan['impact_value']}
											</td>
											<td>
												<center>
													{if $plan['active']}
														<a title="{l s='Enable' mod='mpbooking'}" href="{url entity='module' name='mpbooking' controller='mpfeaturepriceplanslist' params=['id_feature_price_rule' => $plan['id_feature_price_rule'], 'mp_plan_status' => 1]}">
															<i class="material-icons">&#xE5CA;</i>
														</a>
													{else}
														<a title="{l s='Disable' mod='mpbooking'}" href="{url entity='module' name='mpbooking' controller='mpfeaturepriceplanslist' params=['id_feature_price_rule' => $plan['id_feature_price_rule'], 'mp_plan_status' => 0]}">
															<i class="material-icons">&#xE5CD;</i>
														</a>
													{/if}
												</center>
											</td>
											<td>
												<center>
													<a title="{l s='Edit' mod='mpbooking'}" href="{url entity='module' name='mpbooking' controller='mpfeaturepriceplan' params=['id_feature_price_rule' => $plan['id_feature_price_rule']]}">
														<i class="material-icons">&#xE150;</i>
													</a>
													<a class="delete_feature_plan" title="{l s='Delete' mod='mpbooking'}" href="{url entity='module' name='mpbooking' controller='mpfeaturepriceplanslist' params=['id_feature_price_rule' => $plan['id_feature_price_rule'], 'deleteplan' => 1]}">
														<i class="material-icons">&#xE872;</i>
													</a>
												</center>
											</td>
										</tr>
									{/foreach}
								{/if}
							</tbody>
						</table>
						{if $featurePricePlansList|@count > 1}
							<div class="btn-group">
								<button class="btn btn-default btn-sm dropdown-toggle wk_language_toggle" type="button" data-toggle="dropdown" aria-expanded="false">
								{l s='Bulk actions' mod='mpbooking'} <span class="caret"></span>
								</button>
								<ul class="dropdown-menu wk_bulk_actions" role="menu">
									<li><a href="" class="mp_bulk_delete_btn"><i class='icon-trash'></i> {l s='Delete selected' mod='mpbooking'}</a></li>
								</ul>
							</div>
						{/if}
					</form>
				</div>
			</div>
		</div>
	</div>
{/block}
