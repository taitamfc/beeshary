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
	{else if isset($smarty.get.edited_withdeactive)}
		<p class="alert alert-info">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Product has been updated successfully but it has been deactivated. Please wait till the approval from admin.' mod='mpbooking'}
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
				<span style="color:{$title_text_color};">{l s='Booking Products' mod='mpbooking'}</span>
			</div>
			<div class="wk-mp-right-column">
				<div class="wk_product_list">
					<p class="wk_text_right">
						<a title="Add product" href="{url entity='module' name='mpbooking' controller='mpbookingproduct'}">
							<button class="btn btn-primary btn-sm" type="button">
								<i class="material-icons">&#xE145;</i>
								{l s='Add Booking Product' mod='mpbooking'}
							</button>
						</a>
					</p>

					<form action="{url entity='module' name='mpbooking' controller='mpbookingproductslist'}" method="post" id="mp_productlist_form">
						<input type="hidden" name="token" id="wk-static-token" value="{$static_token}">
						<table class="table table-striped" id="mp_product_list">
							<thead>
								<tr>
									{if $booking_product_list|@count > 1}
										<th class="no-sort"><input type="checkbox" title="{l s='Select all' mod='mpbooking'}" id="mp_all_select"/></th>
									{/if}
									<th>{l s='ID' mod='mpbooking'}</th>
									<th>{l s='Image' mod='mpbooking'}</th>
									<th>{l s='Name' mod='mpbooking'}</th>
									<th><center>{l s='Price' mod='mpbooking'}</center></th>
									<th><center>{l s='Quantity' mod='mpbooking'}</center></th>
									<th><center>{l s='Booking Type' mod='mpbooking'}</center></th>
									<th><center>{l s='Status' mod='mpbooking'}</center></th>
									<th class="no-sort"><center>{l s='Actions' mod='mpbooking'}</center></th>
								</tr>
							</thead>
							<tbody>
								{if $booking_product_list != 0}
									{foreach $booking_product_list as $key => $product}
										<tr class="{if $key%2 == 0}even{else}odd{/if}">
											{if $booking_product_list|@count > 1}<td><input type="checkbox" name="mp_product_selected[]" class="mp_bulk_select" value="{$product.id_mp_product}"/></td>{/if}
											<td>{$product.id_mp_product}</td>
											<td>
												{if isset($product.unactive_image)} <!--product is not activated yet-->
													<a class="mp-img-preview" href="{$smarty.const._MODULE_DIR_}marketplace/views/img/product_img/{$product.unactive_image}">
														<img class="img-thumbnail" width="45" height="45" src="{$smarty.const._MODULE_DIR_}marketplace/views/img/product_img/{$product.unactive_image}">
													</a>
												{else if isset($product.cover_image)} <!--product is atleast one time activated-->
													<a class="mp-img-preview" href="{$product.image_path}">
														<img class="img-thumbnail" width="45" height="45" src="{$link->getImageLink($product.obj_product->link_rewrite, $product.cover_image, 'small_default')}">
													</a>
												{else}
													<img class="img-thumbnail" alt="{l s='No image' mod='mpbooking'}"	width="45" height="45" src="{$smarty.const._MODULE_DIR_}/marketplace/views/img/home-default.jpg">
												{/if}
											</td>
											<td>
												{$product.product_name}
											</td>
											<td><center>{$product.price}</center></td>
											<td><center>{$product.quantity}</center></td>
											<td><center>
												{if isset($product.booking_type) && $product.booking_type == $booking_type_date_range}
													{l s='Date Range' mod='mpbooking'}
												{else}
													{l s='Time Slot' mod='mpbooking'}
												{/if}
											</center></td>
											<td><center>
												{if isset($product.admin_approved) && $product.admin_approved}
													{if $product.active}
														{if $products_status == 1}
															<a href="{url entity='module' name='mpbooking' controller='mpbookingproductslist' params=['id_product' => $product.id_product, 'mp_product_status' => 1]}">
																<img alt="{l s='Enabled' mod='mpbooking'}" title="{l s='Enabled' mod='mpbooking'}" class="mp_product_status" src="{$smarty.const._MODULE_DIR_}marketplace/views/img/icon/icon-check.png" />
															</a>
														{else}
															<span class="wk_product_approved">{l s='Approved' mod='mpbooking'}</span>
														{/if}
													{else}
														{if $products_status == 1}
															<a href="{url entity='module' name='mpbooking' controller='mpbookingproductslist' params=['id_product' => $product.id_product, 'mp_product_status' => 1]}">
																<img alt="{l s='Disabled' mod='mpbooking'}" title="{l s='Disabled' mod='mpbooking'}" class="mp_product_status" src="{$smarty.const._MODULE_DIR_}marketplace/views/img/icon/icon-close.png" />
															</a>
														{else}
															<span class="wk_product_pending">{l s='Pending' mod='mpbooking'}</span>
														{/if}
													{/if}
												{else}
													<span class="wk_product_pending">{l s='Pending' mod='mpbooking'}</span>
												{/if}
												</center>
											</td>
											<td>
												<center>
													<a title="{l s='Edit' mod='mpbooking'}" href="{url entity='module' name='mpbooking' controller='mpbookingproduct' params=['id_mp_product' => $product.id_mp_product]}">
														<i class="material-icons">&#xE254;</i>
													</a>
													&nbsp;
													<a title="{l s='Delete' mod='mpbooking'}" href="{url entity='module' name='mpbooking' controller='mpbookingproductslist' params=['id_mp_product' => $product.id_mp_product, 'deleteproduct' => 1]}" class="delete_img">
														<i class="material-icons">&#xE872;</i>
													</a>

													<a class="edit_seq open_image_form" alt="1" product-id="{$product['id_mp_product']}" data-toggle="modal" data-target="#content{$product['id_mp_product']}" href="javascript:void(0)">
														<i class="material-icons mp-list-img-link" title="{l s='Edit Image' mod='mpbooking'}" id="edit_seq{$product['id_mp_product']}">&#xE3F4;</i>
													</a>
													<input type="hidden" id="urlimageedit" value="{$imageediturl}"/>
												</center>
											</td>
										</tr>

										<div class="modal fade" id="content{$product['id_mp_product']}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
										</div>
									{/foreach}
								{/if}
							</tbody>
						</table>
						{if $booking_product_list|@count > 1}
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
		{block name='mp_image_preview'}
			{include file='module:marketplace/views/templates/front/product/_partials/mp-image-preview.tpl'}
		{/block}
	</div>
{/block}
