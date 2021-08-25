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

	{if isset($smarty.get.mpsv_exist)}
		<p class="alert alert-warning">{l s='Please delete your previous vacation to request a new vacation.' mod='mpsellervacation'}</p>
	{/if}
	{if isset($smarty.get.added)}
		<p class="alert alert-success">{l s='Added Successfully' mod='mpsellervacation'}</p>
	{/if}
	{if isset($smarty.get.updated)}
		<p class="alert alert-success">{l s='Updated Successfully' mod='mpsellervacation'}</p>
	{/if}
	{if isset($smarty.get.deleted)}
		<p class="alert alert-success">{l s='Deleted Successfully' mod='mpsellervacation'} </p>
	{/if}
	<div class="wk-mp-block">
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="dashboard">
				<div class="page-title" style="background-color:{$title_bg_color};">
					<span style="color:{$title_text_color};">{l s='Seller Vacation' mod='mpsellervacation'}</span>
				</div>
				<div class="wk-mp-right-column">
					<div class="box-head-left">
						<h2>{l s='Vacation List' mod='mpsellervacation'}</h2>
					</div>
					<div class="text-xs-right litRespBtn">
						<a href="{$link->getModuleLink('mpsellervacation', 'addSellerVacation')}" class="btn btn-primary button btn-sm">
							<span>{l s='Add New Request' mod='mpsellervacation'}</span>
						</a>
					</div>
					<div class="col-sm-12">
						<table style="width:100%;" class="data-table table table-striped dataTable no-footer table-responsive" >
							<thead>
								<tr>
									<th>{l s='From' mod='mpsellervacation'}</th>
									<th>{l s='To' mod='mpsellervacation'}</th>
									<th>{l s='Description' mod='mpsellervacation'}</th>
									<th>{l s='Add to Cart' mod='mpsellervacation'}</th>
									<th>{l s='Status' mod='mpsellervacation'}</th>
									<th><center>{l s='Actions' mod='mpsellervacation'}</center></th>
								</tr>
							</thead>
							<tbody>
								{if $mps_vacation_detail}
									{foreach $mps_vacation_detail as $value}
									<tr>
										<td>{$value.from}</td>
										<td>{$value.to}</td>
										<td>{$value.description|truncate:30:"...":true}</td>
										<td>
											{if $value.addtocart == 1}
												{l s='Show' mod='mpsellervacation'}
											{else}
												{l s='Hide' mod='mpsellervacation'}
											{/if}
										</td>
										<td>
											{if $value.active == 1}
												{l s='Approved' mod='mpsellervacation'}
											{else}
												{l s='Pending' mod='mpsellervacation'}
											{/if}
										</td>
										<td><center>
											<a href="{$link->getModuleLink('mpsellervacation', 'addSellerVacation', ['id' => $value.id])}">
												<i class="material-icons">&#xE254;</i>
											</a>
											&nbsp;
											<a href="{$link->getModuleLink('mpsellervacation', 'sellerVacationDetail', ['del_id' => $value.id])}" class="delete_vacation" onclick="return confirm('{$confirm_msg}');">
												<i class="material-icons">&#xE92B;</i>
											</a>
											</center>
										</td>
									</tr>
									{/foreach}
								{else}
									<tr>
										<td colspan = "6" style="text-align:center;">{l s='No Data Found.' mod='mpsellervacation'}</td>
									</tr>
								{/if}
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
{/block}

<script type="text/javascript">
	$(document).on('click', '.delete_vacation', function(){
		if(!confirm(confirm_msg))
			return false;
	});
</script>