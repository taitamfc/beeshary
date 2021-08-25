{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{block name='content'}

	{if isset($smarty.get.success)}
		{if $smarty.get.success == 1}
			<p class="alert alert-success">
				<button data-dismiss="alert" class="close" type="button">×</button>
				{if $manage_status}
					{l s='Store location created.' mod='mpstorelocator'}
				{else}
					{l s='Store location created. Location will be activated after admin approval. Please wait.' mod='mpstorelocator'}
				{/if}
			</p>
		{else if $smarty.get.success == 2}
			<p class="alert alert-success">
				<button data-dismiss="alert" class="close" type="button">×</button>
				{l s='Store location updated.' mod='mpstorelocator'}
			</p>
		{/if}
	{/if}

	{if isset($smarty.get.mperror)}
		{if $smarty.get.mperror == 1}
			<p class="alert alert-danger">
				<button data-dismiss="alert" class="close" type="button">×</button>
				{l s='Invalid Store Access.' mod='mpstorelocator'}
			</p>
		{/if}
	{/if}

	{if isset($smarty.get.deleted)}
		{if $smarty.get.deleted == 1}
			<p class="alert alert-success">
				<button data-dismiss="alert" class="close" type="button">×</button>
				{l s='Store deleted successfully.' mod='mpstorelocator'}
			</p>
		{else if $smarty.get.deleted == 2}
			<p class="alert alert-danger">
				<button data-dismiss="alert" class="close" type="button">×</button>
				{l s='Some problem while deleting this store.' mod='mpstorelocator'}
			</p>
		{/if}
	{/if}

	{if isset($smarty.get.delete_logo_msg)}
		{if $smarty.get.delete_logo_msg == 1}
			<p class="alert alert-success">
				<button data-dismiss="alert" class="close" type="button">×</button>
				{l s='Store logo deleted successfully.' mod='mpstorelocator'}
			</p>
		{else if $smarty.get.delete_logo_msg == 2}
			<p class="alert alert-danger">
				<button data-dismiss="alert" class="close" type="button">×</button>
				{l s='Error while deleting image.' mod='mpstorelocator'}
			</p>
		{/if}
	{/if}


	{if isset($smarty.get.created_conf)}
		<p class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Created Successfully' mod='mpstorelocator'}
		</p>
	{else if isset($smarty.get.edited_conf)}
		<p class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Updated Successfully' mod='mpstorelocator'}
		</p>
	{else if isset($smarty.get.edited_withdeactive)}
		<p class="alert alert-info">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Store has been updated successfully but it has been deactivated. Please wait till the approval from admin.' mod='mpstorelocator'}
		</p>
	{/if}
	{* {if isset($smarty.get.deleted)}
		<p class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Deleted Successfully' mod='mpstorelocator'}
		</p>
	{/if} *}

	{if isset($smarty.get.status_updated)}
		<p class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Status updated Successfully' mod='mpstorelocator'}
		</p>
	{/if}
	<div class="wk-mp-block">
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color};">
				<span style="color:{$title_text_color};">{l s='Store' mod='mpstorelocator'}</span>
			</div>
			<div class="wk-mp-right-column">
				<div class="wk_product_list">
					<p class="wk_text_right">
						<a title="{l s='Store configuration' mod='mpstorelocator'}" href="{$link->getModuleLink('mpstorelocator', 'storeconfiguration')}">
							<button class="btn btn-primary btn-sm float-xs-left" type="button">
								<i class="material-icons">&#xE8D1;</i>
								{l s='Store Configuration' mod='mpstorelocator'}
							</button>
						</a>
						<a title="{l s='Add store' mod='mpstorelocator'}" href="{$link->getModuleLink('mpstorelocator', 'addstore')}">
							<button class="btn btn-primary btn-sm" type="button">
								<i class="material-icons">&#xE145;</i>
								{l s='Add Store' mod='mpstorelocator'}
							</button>
						</a>
					</p>
					<form action="{$link->getModuleLink('mpstorelocator', storelist)}" method="post" id="mp_storelist_form">
						<input type="hidden" name="token" id="wk-static-token" value="{$static_token}">
						<table class="table table-striped" id="mp_store_list">
							<thead>
								<tr>
									{if $storelists|@count > 1}
										<th class="no-sort"><input type="checkbox" title="{l s='Select all' mod='mpstorelocator'}" id="mp_store__all_select"/></th>
									{/if}
									<th>{l s='ID' mod='mpstorelocator'}</th>
									<th>{l s='Image' mod='mpstorelocator'}</th>
									<th>{l s='Name' mod='mpstorelocator'}</th>
									<th><center>{l s='Store Email' mod='mpstorelocator'}</center></th>
									<th><center>{l s='Store Contact' mod='mpstorelocator'}</center></th>
									<th><center>{l s='Status' mod='mpstorelocator'}</center></th>
									<th class="no-sort"><center>{l s='Actions' mod='mpstorelocator'}</center></th>
								</tr>
							</thead>
							<tbody>
								{if $storelists != 0}
									{foreach $storelists as $key => $store}
										<tr class="{if $key%2 == 0}even{else}odd{/if}">
											{if $storelists|@count > 1}<td><input type="checkbox" name="mp_store_selected[]" class="mp_store_select" value="{$store.id}"/></td>{/if}
											<td>{$store.id}</td>
											<td>
												<a class="mp-img-preview" href="{$link->getModuleLink('mpstorelocator', 'addstore', ['id_store' => $store.id])}">
													<img class="img-thumbnail" width="45" height="45" src="{$smarty.const._MODULE_DIR_}mpstorelocator/views/img/store_logo/{$store.id}.jpg">
												</a>
											</td>
											<td>
												<a href="{$link->getModuleLink('mpstorelocator', 'addstore', ['id_store' => $store.id])}">
												{$store.name}
												</a>
											</td>
											<td data-order="{$store.email}"><center>{$store.email}</center></td>
											<td><center>{$store.phone}</center></td>
											<td>
												<center>
													{if $store.active}
														{if $manageStoreStatus == 1}
															<a href="{$link->getModuleLink('mpstorelocator', 'storelist', ['id_store' => {$store.id}, 'mp_store_status' => 1])|addslashes}">
																<img alt="{l s='Enabled' mod='mpstorelocator'}" title="{l s='Enabled' mod='mpstorelocator'}" class="mp_store_status" src="{$smarty.const._MODULE_DIR_}marketplace/views/img/icon/icon-check.png" />
															</a>
														{else}
															<span class="wk_product_approved">{l s='Approved' mod='mpstorelocator'}</span>
														{/if}
													{else}
														{if $manageStoreStatus == 1}
															<a href="{$link->getModuleLink('mpstorelocator', 'storelist', ['id_store' => {$store.id}, 'mp_store_status' => 1])|addslashes}">
																<img alt="{l s='Disabled' mod='mpstorelocator'}" title="{l s='Disabled' mod='mpstorelocator'}" class="mp_store_status" src="{$smarty.const._MODULE_DIR_}marketplace/views/img/icon/icon-close.png" />
															</a>
														{else}
															<span class="wk_product_pending">{l s='Pending' mod='mpstorelocator'}</span>
														{/if}
													{/if}											
												</center>
											</td>
											<td>
												<center>
												<a title="{l s='Edit' mod='mpstorelocator'}" href="{$link->getModuleLink('mpstorelocator', 'addstore', ['id_store' => $store.id])}">
													<i class="material-icons">&#xE254;</i>
												</a>
												&nbsp;
												<a title="{l s='Delete' mod='mpstorelocator'}" href="{$link->getModuleLink('mpstorelocator', 'addstore', ['id_store' => $store.id, 'delete' => 1])}" class="delete_img">
													<i class="material-icons">&#xE872;</i>
												</a>
												</center>
											</td>
										</tr>
									{/foreach}
								{/if}
							</tbody>
						</table>
						{if $storelists|@count > 1}
							<div class="btn-group">
								<button class="btn btn-default btn-sm dropdown-toggle wk_language_toggle" type="button" data-toggle="dropdown" aria-expanded="false">
								{l s='Bulk actions' mod='mpstorelocator'} <span class="caret"></span>
								</button>
								<ul class="dropdown-menu wk_bulk_actions" role="menu">
									<li><a href="" class="mp_store_bulk_delete_btn"><i class='icon-trash'></i> {l s='Delete selected' mod='mpstorelocator'}</a></li>
								</ul>
							</div>
						{/if}
					</form>
				</div>
			</div>
		</div>
	</div>
{/block}
