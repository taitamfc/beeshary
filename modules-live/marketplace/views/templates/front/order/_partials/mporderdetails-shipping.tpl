{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<!-- success message div for order state update -->
{if isset($is_order_state_updated)}
	{if $is_order_state_updated == 1}
		<div class="alert alert-success">
			{l s='Order status updated successfully' mod='marketplace'}
		</div>
	{/if}
{/if}

<!-- Tab -->
<div class="tabs">
	<ul class="nav nav-tabs">
		{if Configuration::get('WK_MP_SELLER_ORDER_STATUS_CHANGE')}
		<li class="nav-item">
			<a class="nav-link active" href="#status" data-toggle="tab">
				<i class="icon-time"></i>
				<span>{l s='Status' mod='marketplace'}</span>
				<span class="badge">({$history|@count})</span>
			</a>
		</li>
		{/if}
		{hook h="displayOrderDetailsExtraTab" id_order=$id_order}
	</ul>
	<div class="tab-content" id="tab-content">
		{if Configuration::get('WK_MP_SELLER_ORDER_STATUS_CHANGE')}
		<div class="tab-pane fade in active" id="status">
			<div class="table-responsive">
				<table class="table history-status row-margin-bottom">
					<tbody>
						{foreach from=$history item=row key=key}
							{if ($key == 0)}
								<tr>
									<td style="background-color:{$row['color']}">
										<img src="{$img_url}os/{$row['id_order_state']|intval}.gif" width="16" height="16" alt="{$row['ostate_name']|stripslashes}" /></td>
									<td style="background-color:{$row['color']};color:{$row['text-color']}">
										{$row['ostate_name']}
									</td>
									<td style="background-color:{$row['color']};color:{$row['text-color']}">
									</td>
									<td style="background-color:{$row['color']};color:{$row['text-color']}">
										{dateFormat date=$row['date_add'] full=true}
									</td>
								</tr>
							{else}
								<tr>
									<td>
										<img src="{$img_url}os/{$row['id_order_state']|intval}.gif" width="16" height="16" />
									</td>
									<td>
										{$row['ostate_name']}
									</td>
									<td>
									</td>
									<td>
										{dateFormat date=$row['date_add'] full=true}
									</td>
								</tr>
							{/if}
						{/foreach}
					</tbody>
				</table>
			</div>
			<!-- Change status form -->
			<form action="{$update_url_link}" method="post" class="form-horizontal well" id="change_order_status_form">
				<div class="row">
					<div class="col-md-6 form-group" id="select_ele_id">
						<select id="id_order_state" class="chosen form-control form-control-select" name="id_order_state" style="width:500px;">
						{foreach from=$states item=state}
							<option value="{$state['id_order_state']|intval}"{if $state['id_order_state'] == $currentState} selected="selected" disabled="disabled"{/if}>{$state['name']}</option>
						{/foreach}
						</select>
						<input type="hidden" name="id_order_state_checked" class="id_order_state_checked" value="{$currentState}" />
					</div>
					<div class="col-md-2"></div>
					<div class="col-md-4">
						<button type="button" style="color:white;display:none;" class="btn btn-primary" data-toggle="modal" data-target="#wk_shipping_form" id="update_order_status_shipping">{l s='Update status' mod='marketplace'}</button>

						<button type="button" style="color:white;display:none;" class="btn btn-primary" data-toggle="modal" data-target="#wk_delivery_form" id="update_order_status_delivary">{l s='Update status' mod='marketplace'}</button>

						<button type="submit" name="submitState" class="btn btn-primary" id="update_order_status">
							<span>{l s='Update status' mod='marketplace'}</span>
						</button>
					</div>
				</div>
			</form>
		</div>
		{/if}
		{hook h="displayOrderDetailsExtraTabContent" id_order=$id_order}
	</div>
</div>