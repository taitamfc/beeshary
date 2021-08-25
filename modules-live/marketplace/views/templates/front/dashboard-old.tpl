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

{extends file=$layout}
{block name='content'}
{if $logged}
	<div class="wk-mp-block">
		{hook h="displayMpMenu"}
		{**<!--div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color};">
				<span style="color:{$title_text_color};">{l s='My Dashboard' mod='marketplace'}</span>
			</div>
			<div class="clearfix wk-mp-right-column">
				<div class="left full">
					<p>
						<strong>{l s='Hello' mod='marketplace'}, {$seller_name}!</strong>
						<br>
						{l s='From your My Account Dashboard, You have the ability to view a snapshot of your recent account activity and update your account information.' mod='marketplace'}
					</p>
					{hook h='displayMpDashboardTop'}
				</div>
				{*{hook h='displayMpDashboardBottom'}*}
				<div class="box-account box-recent">
					<div class="box-head">
						<div class="box-head-left">
							<h6>{l s='Recent Orders' mod='marketplace'}</h6>
						</div>
						<div class="box-head-right">
							<a class="btn btn-primary btn-sm" href="{$link->getModuleLink('marketplace','mporder')}">
								<span>{l s='View All' mod='marketplace'}</span>
							</a>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="box-content" >
						<div class="wk_order_table">
							<table class="table">
								<thead>
									<tr>
										<th>{l s='ID' mod='marketplace'}</th>
										<th>{l s='Reference' mod='marketplace'}</th>
										<th>{l s='Customer' mod='marketplace'}</th>
										<th>{l s='Total' mod='marketplace'}</th>
										<th>{l s='Status' mod='marketplace'}</th>
										<th>{l s='Payment' mod='marketplace'}</th>
										<th>{l s='Date' mod='marketplace'}</th>
									</tr>
								</thead>
								<tbody>
									{if isset($mporders)}
										{foreach $mporders as $order}
											<tr class="mp_order_row" is_id_order="{$order.id_order}" is_id_order_detail="{$order.id_order_detail}">
												<td>{$order.id_order}</td>
												<td>{$order.reference}</td>
												<td>{$order.buyer_info->firstname} {$order.buyer_info->lastname}</td>
												<td>{$order.total_paid}</td>
												<td>{$order.order_status}</td>
												<td>{$order.payment_mode}</td>
												<td>{dateFormat date=$order.date_add full=1}</td>
											</tr>
										{/foreach}
									{else}
										<tr>
											<td colspan="7">{l s='No order found' mod='marketplace'}</td>
										</tr>
									{/if}
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="box-account box-recent">
					<div class="box-head">
						<h6>{l s='Orders Graph' mod='marketplace'}</h6>
						<div class="wk_border_line"></div>
					</div>
					<div class="box-content">
						<div class="wk_from_to">
							<div class="wk_from">
								<div class="labels label">
									{l s='From' mod='marketplace'}
								</div>
								<div class="input_type">
									<input id="graph_from" class="datepicker form-control" type="text" style="text-align: center" value="{$from_date|date_format:"%Y-%m-%d"}" name="graph_from">
								</div>
							</div>
							<div class="wk_to">
								<div class="labels label">
									{l s='To' mod='marketplace'}
								</div>
								<div class="input_type">
									<input id="graph_to" class="datepicker1 form-control" type="text" style="text-align: center" value="{$to_date|date_format:"%Y-%m-%d"}" name="graph_to">
								</div>
							</div>
						</div>
						<div id="chart_div" style="width:100%; height:500px;overflow:hidden;"></div>
					</div>
				</div>
			</div>
		</div-->**}
	</div>
{/if}
{/block}
