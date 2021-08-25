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

{extends file=$layout}
{block name='content'}
{if isset($smarty.get.updatempshipping_success)}
	{if $smarty.get.updatempshipping_success == 1}
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Updated successfully' mod='mpshipping'}
		</div>
	{/if}
{/if}
{if isset($smarty.get.delete_success)}
	{if $smarty.get.delete_success == 1}
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Deleted successfully' mod='mpshipping'}
		</div>
	{/if}
{/if}
{if isset($smarty.get.no_shipping)}
	{if $smarty.get.no_shipping == 1}
		<div class="alert alert-danger">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Select atleast one shipping method' mod='mpshipping'}
		</div>
	{/if}
{/if}
<div class="wk-mp-block">
	{hook h="displayMpMenu"}
	<div class="wk-mp-content">
		<div class="page-title" style="background-color:{$title_bg_color};">
			<span style="color:{$title_text_color};">{l s='Carriers' mod='mpshipping'}</span>
		</div>
		<div class="wk-mp-right-column" style="border:none">
			<div class="shipping_list_container wk_product_list left">
				<div class="box-account box-recent">
					<div class="box-head">
						<div class="box-head-right">
							<a href="{$link->getModuleLink('mpshipping','addmpshipping')}">
								<button class="btn btn-primary btn-sm" id="add_new_shipping">
									<span><i class="material-icons">&#xE145;</i> {l s='Add Carrier' mod='mpshipping'}</span>
								</button>
							</a>
							<button class="btn btn-primary btn-sm" id="add_default_shipping">
								<span><i class="material-icons">&#xE83A;</i> {l s='Set Default Shipping' mod='mpshipping'}</span>
							</button>
						</div>
					</div>
					<div class="box-content" id="wk_shipping_list">
						<div id="default_shipping_div" style="display:none;">
							<div class="panel panel-default">
								<h4 class="panel-heading" style="margin: 0;">{l s='Default Shipping Method' mod='mpshipping'}</h4>
								<div class="panel-body">
									{if isset($mp_shipping_active)}
										<form method="post" action="{$default_shipping_link}" class="form-horizontal">
											<div class="form-group">
												<label for="default_shipping" class="col-lg-3 col-md-3 col-sm-3 col-xs-12 text-right">{l s='Select default shipping' mod='mpshipping'}</label>
												<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
													<div style="height:155px;overflow:auto;">
													{foreach $mp_shipping_active as $mp_sp_det}
														<div>
															<div class="shipping_checkbox">
																<input type="checkbox" name="default_shipping[]" id="default_shipping_{$mp_sp_det.id|escape:'htmlall':'UTF-8'}" value="{$mp_sp_det.id|escape:'htmlall':'UTF-8'}" {if $mp_sp_det.is_default_shipping == 1}checked="checked" {/if}>
															</div>
															<div class="floatleft" style="padding:4px 10px;">
																<label for="default_shipping_{$mp_sp_det.id|escape:'htmlall':'UTF-8'}" style="font-weight: normal;">
																	{$mp_sp_det.mp_shipping_name|escape:'htmlall':'UTF-8'}
																</label>
															</div>
															<div style="clear:both;"></div>
														</div>
													{/foreach}
													</div>
												</div>
											</div>
											<div class="form-group" style="text-align:center;">
												<button type="submit" id="submit_default_shipping" class="btn btn-primary btn-sm"><span>{l s='Update' mod='mpshipping'}</span></button>
												<button type="button" id="cancel_default_shipping" class="btn btn-primary btn-sm"><span>{l s='Cancel' mod='mpshipping'}</span></button>
											</div>
										</form>
									{else}
										<div class="alert alert-info">{l s='You do not have any active shipping method(s).' mod='mpshipping'}</div>
									{/if}
								</div>
							</div>
						</div>
						<table id="default_shipping_show" cellpadding="7" class="data-table" style="margin-bottom:15px;">
							<tr class="first last">
								<th>{l s='Default shipping method' mod='mpshipping'}</th>
								<td>
									{if isset($default_shipping_name)}
										{$default_shipping_name|escape:'htmlall':'UTF-8'}
									{else}
										{l s='There is no default shipping method' mod='mpshipping'}
									{/if}
								</td>
							</tr>
						</table>
						<table class="table table-striped" {if isset($mp_shipping_detail)}id="mp_shipping_list"{/if}>
							<thead>
								<tr>
									<th>{l s='ID' mod='mpshipping'}</th>
									<th>{l s='Carrier Name' mod='mpshipping'}</th>
									<th class="no-sort">{l s='Logo' mod='mpshipping'}</th>
									<th>{l s='Shipping Method' mod='mpshipping'}</th>
									<th>{l s='Status' mod='mpshipping'}</th>
									<th class="no-sort"><center>{l s='Actions' mod='mpshipping'}</center></th>
								</tr>
							</thead>
							<tbody>
								{if isset($mp_shipping_detail)}
									{foreach $mp_shipping_detail as $num => $mp_sp_det}
										<tr>
											<td>{$mp_sp_det.id|escape:'htmlall':'UTF-8'}</td>
											<td>{$mp_sp_det.mp_shipping_name|escape:'htmlall':'UTF-8'}</td>
											<td>
												{if $mp_sp_det.image_exist == 1}
													<img src="{$smarty.const._MODULE_DIR_}mpshipping/views/img/logo/{$mp_sp_det.id|escape:'htmlall':'UTF-8'}.jpg" width="30px" height="30px" alt="{$mp_sp_det.mp_shipping_name|escape:'htmlall':'UTF-8'}">
												{else}
													<span>{l s='No Image' mod='mpshipping'}</span>
												{/if}
											</td>

											<td>
												{if $mp_sp_det.is_free == 1}
													{l s='Free Shipping' mod='mpshipping'}
												{else}
													{if $mp_sp_det.shipping_method == 2}
														{l s='Shipping charge on price' mod='mpshipping'}
													{elseif $mp_sp_det.shipping_method == 1}
														{l s='Shipping charge on weight' mod='mpshipping'}
													{/if}
												{/if}
											</td>
											<td>
												{if $mp_sp_det.active == 0}
													<span class="wk_product_pending">{l s='Pending' mod='mpshipping'}</span>
												{else}
													<span class="wk_product_approved">{l s='Approved' mod='mpshipping'}</span>
												{/if}
											</td>
											<td style="text-align:right;padding-right:30px;">
												{if !($mp_sp_det.is_free)}
													<a title="{l s='View Impact Price' mod='mpshipping'}" href="{$link->getModuleLink('mpshipping','addmpshipping',['mpshipping_id'=>{$mp_sp_det.id|escape:'htmlall':'UTF-8'}, 'addmpshipping_step4'=>1, 'updateimpact' => 1])|escape:'htmlall':'UTF-8'}" id="impact_edit">
														<i class="material-icons">&#xE417;</i>
													</a>
													&nbsp;
												{/if}
												<a title="{l s='Basic Edit' mod='mpshipping'}" href="{$link->getModuleLink('mpshipping','addmpshipping',['mpshipping_id'=>{$mp_sp_det.id|escape:'htmlall':'UTF-8'} ])|escape:'htmlall':'UTF-8'}" id="shipping_basicedit">
													<i class="material-icons">&#xE254;</i>
												</a>
												&nbsp;
												{if $mp_sp_det.shipping_on_product == 1}
													<a title="{l s='Delete' mod='mpshipping'}" href="#delete_shipping_form" data-prod="{$mp_sp_det.shipping_on_product|escape:'htmlall':'UTF-8'}" data-shipping-id="{$mp_sp_det.id|escape:'htmlall':'UTF-8'}" class="delete_shipping">
														<i class="material-icons">&#xE872;</i>
													</a>
												{else}
													<a title="{l s='Delete' mod='mpshipping'}" href="{$link->getModuleLink('mpshipping','mpshippinglist',['mpshipping_id'=>{$mp_sp_det.id|escape:'htmlall':'UTF-8'},'delete_shipping'=>1])|escape:'htmlall':'UTF-8'}" data-prod="{$mp_sp_det.shipping_on_product|escape:'htmlall':'UTF-8'}" class="delete_shipping">
														<i class="material-icons">&#xE872;</i>
													</a>
												{/if}
											</td>
										</tr>
									{/foreach}
								{else}
									<tr>
										<td colspan="7"><center>{l s='No Carrier Yet' mod='mpshipping'}</center></td>
									</tr>
								{/if}
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>

<div id="delete_shipping_form" style="display:none;">
	<label><strong>{l s='Note: This shipping method is assigned on product(s). So before deleting this shipping you have to choose a shipping method for that products.' mod='mpshipping'}</strong></label>
	<div class="panel-body">
		<form method="post" action="{$link->getModuleLink('mpshipping','mpshippinglist')|escape:'htmlall':'UTF-8'}" class="form-horizontal">
		{if isset($mp_shipping_active)}
			<input type="hidden" name="delete_shipping_id" id="delete_shipping_id" value="">
			<div id="shippingactive" class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 text-right">{l s='Select Shipping' mod='mpshipping'}</label>
				<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
					<select name="extra_shipping" id="extra_shipping" style="height: 29px;width: 40%;">
						{foreach $mp_shipping_active as $mp_sp_det}
							<option value="{$mp_sp_det.id|escape:'htmlall':'UTF-8'}">{$mp_sp_det.mp_shipping_name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div id="noshippingactive" style="display:none;">
				<div class="alert alert-info">{l s='There is no other active shipping method.' mod='mpshipping'}</div>
				<div class="help-block">{l s='If No Carrier selected, Admin first default shipping will apply on product(s)' mod='mpshipping'}</div>
			</div>
		{else}
			<div class="alert alert-info">{l s='There is no other active shipping method.' mod='mpshipping'}</div>
			<div class="help-block">{l s='If No Carrier selected, Admin first default shipping will apply on product(s)' mod='mpshipping'}</div>
		{/if}
			<div class="form-group" style="text-align:center;">
				<button type="submit" name="submit_extra_shipping" class="btn btn-primary btn-sm"><span>{l s='Submit' mod='mpshipping'}</span></button>
			</div>
		</form>
	</div>
</div>
{/block}
