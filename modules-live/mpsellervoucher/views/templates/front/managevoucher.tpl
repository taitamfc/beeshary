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
{if isset($smarty.get.deleted)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Deleted Successfully' mod='mpsellervoucher'}
	</p>
{/if}

{if isset($smarty.get.status_updated)}
	<p class="alert {if $smarty.get.status_updated == 1}alert-success{elseif $smarty.get.status_updated == 2}alert-danger{/if}">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{if $smarty.get.status_updated == 1}
			{l s='Status updated Successfully' mod='mpsellervoucher'}
		{elseif $smarty.get.status_updated == 2}
			{l s='Admin approval is necessary to change the status of voucher.' mod='mpsellervoucher'}
		{/if}
	</p>
{/if}

{if $logged}
	<div class="wk-mp-block">
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color};">
				<span style="color:{$title_text_color};">{l s='Voucher' mod='mpsellervoucher'}</span>
			</div>
			<div class="wk-mp-right-column">
				<div class="row margin-btm-10">
					<div class="col-xs-12 col-sm-12">
						<p class="add-voucher-link">
							<a title="{l s='Ajouter un bon de réduction' mod='mpsellervoucher'}" href="{$link->getModuleLink('mpsellervoucher', 'sellercartrule')}">
								<i class="material-icons">&#xE145;</i>
								{l s='Ajouter un bon de réduction' mod='mpsellervoucher'}
							</a>
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						{if isset($seller_voucher)}
							<form action="{$link->getModuleLink('mpsellervoucher', 'managevoucher')}" method="post" id="mp_voucherlist_form">
								<div class="table-responsive">
									<table class="table" id="mp_voucher_list">
										<thead>
											<tr>
												{if $seller_voucher|@count > 1}
													<th><input type="checkbox" title="{l s='Select all' mod='mpsellervoucher'}" id="mp_select_all_voucher"/></th>
												{/if}
												<th>{l s='Voucher Id' mod='mpsellervoucher'}</th>
												<th>{l s='Name' mod='mpsellervoucher'}</th>
												<th>{l s='Code' mod='mpsellervoucher'}</th>
												<th>{l s='Quantity' mod='mpsellervoucher'}</th>
												<th>{l s='Date From' mod='mpsellervoucher'}</th>
												<th>{l s='Date To' mod='mpsellervoucher'}</th>
												<th>{l s='Active' mod='mpsellervoucher'}</th>
												<th>{l s='Admin Approved' mod='mpsellervoucher'}</th>
												<th data-sort-ignore="true">{l s='Action' mod='mpsellervoucher'}</th>
											</tr>
										</thead>
										<tbody>
											{foreach $seller_voucher as $key => $voucher}
												<tr>
													{if $seller_voucher|@count > 1}
														<td>
															<input type="checkbox" name="mp_voucher_selected[]" class="mp_voucher_bulk_select" value="{$voucher.id_mp_cart_rule}"/>
														</td>
													{/if}
													<td>{$voucher.id_mp_cart_rule}</td>
													<td>
														<a href="{$link->getModuleLink('mpsellervoucher', 'sellercartrule', ['id_mp_cart_rule' => {$voucher.id_mp_cart_rule}])|addslashes}">{$voucher.name}</a>
													</td>
													<td>{$voucher.code}</td>
													<td>{$voucher.quantity}</td>
													<td>{$voucher.date_from|date_format:"%d-%m-%Y %T"}</td>
													<td>{$voucher.date_to|date_format:"%d-%m-%Y %T"}</td>
													<td>
														<a {if $voucher.admin_approval}href="{$link->getModuleLink('mpsellervoucher', 'managevoucher', ['id_mp_cart_rule' => {$voucher.id_mp_cart_rule}, 'changeSellerSideVoucherStatus' => 1])|addslashes}"{/if} class="mp_voucher_status_change {if !$voucher.admin_approval}mp_voucher_status_disable{/if}" 
														data-active="{$voucher.active}" 
														data-admin-approval="{$voucher.admin_approval}">
															<span class="{if !$voucher.admin_approval}disabled_status{else}{if $voucher.active}text-green{else}text-red{/if}{/if}">
																{if $voucher.active}
																	<i class="material-icons">&#xE876;</i>
																{else}
																	<i class="material-icons">&#xE14C;</i>
																{/if}
															</span>
														</a>
													</td>
													<td>
														<span class="{if $voucher.admin_approval}text-green{else}text-red{/if}">
															{if $voucher.admin_approval}
																{l s='Approved' mod='mpsellervoucher'}
															{else}
																{if $voucher.id_ps_cart_rule}
																	{l s='Disapproved' mod='mpsellervoucher'}
																{else}
																	{l s='Pending' mod='mpsellervoucher'}
																{/if}
															{/if}
														</span>
													</td>
													<td>
														<a title="{l s='Edit' mod='mpsellervoucher'}" href="{$link->getModuleLink('mpsellervoucher', 'sellercartrule', ['id_mp_cart_rule' => {$voucher.id_mp_cart_rule}])|addslashes}">
															<i class="material-icons">&#xE150;</i>
														</a>
														&nbsp;
														<a title="{l s='Delete' mod='mpsellervoucher'}" href="{$link->getModuleLink('mpsellervoucher', 'managevoucher', ['id_mp_cart_rule' => {$voucher.id_mp_cart_rule}, 'deleteVoucher' => 1])|addslashes}">
															<i class="material-icons">&#xE872;</i>
														</a>
													</td>
												</tr>
											{/foreach}
										</tbody>
									</table>
								</div>
								{if $seller_voucher|@count > 1}
									<div class="btn-group">
										<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
										{l s='Actions groupées' mod='mpsellervoucher'} <span class="caret"></span>
										</button>
										<ul class="dropdown-menu" role="menu">
											<li><a href="" class="mp_voucher_bulk_delete_btn"><i class='icon-trash'></i> {l s='Delete selected' mod='mpsellervoucher'}</a></li>
										</ul>
									</div>
								{/if}
							</form>
						{else}
							<div class="alert alert-info">
								{l s='No Data Found.' mod='mpsellervoucher'}</span>
							</div>
						{/if}
					</div>
				</div>
			</div>
		</div>
	</div>
{else}
	<div class="alert alert-danger">
		{l s='You are logged out. Please login to add product.' mod='mpsellervoucher'}</span>
	</div>
{/if}
{/block}