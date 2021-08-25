{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}
{extends file=$layout}
{block name='content'}

{if isset($smarty.get.success)}
	{if $smarty.get.success == 1}
		<p class="alert alert-success">
			{if $manage_status}
				{l s='Store location created.' mod='mpstorelocator'}
			{else}
				{l s='Store location created. Location will be activated after admin approval. Please wait.' mod='mpstorelocator'}
			{/if}
		</p>
	{else if $smarty.get.success == 2}
		<p class="alert alert-success">
			{l s='Store location updated.' mod='mpstorelocator'}
		</p>
	{/if}
{/if}

{if isset($smarty.get.deleted)}
	{if $smarty.get.deleted == 1}
		<p class="alert alert-success">
			{l s='Store deleted successfully.' mod='mpstorelocator'}
		</p>
	{else if $smarty.get.deleted == 2}
		<p class="alert alert-danger">
			{l s='Some problem while deleting this store.' mod='mpstorelocator'}
		</p>
	{/if}
{/if}

{if isset($smarty.get.delete_logo_msg)}
	{if $smarty.get.delete_logo_msg == 1}
		<p class="alert alert-success">
			{l s='Store logo deleted successfully.' mod='mpstorelocator'}
		</p>
	{else if $smarty.get.delete_logo_msg == 2}
		<p class="alert alert-danger">
			{l s='Error while deleting image.' mod='mpstorelocator'}
		</p>
	{/if}
{/if}

{if isset($store_locations)}
	<div id="wrapper_store">
		<button class="btn btn-primary other_btn" id="reset_btn" style="float: left;">
			<span>{l s='Reset' mod='mpstorelocator'}</span>
		</button>
		<a class="btn btn-primary" style="margin-bottom:5px; float: right;" href="{$link->getModuleLink('mpstorelocator', 'addstore')}">
			<span>{l s='Add New Store' mod='mpstorelocator'}</span>
		</a>

		<div class="page-title" style="background-color:{$title_bg_color}; clear: both;">
			<span style="color:{$title_text_color};">
				<i class="icon-map-marker"></i>
				{l s='Store Locator' mod='mpstorelocator'}
			</span>
		</div>

		<div id="wrapper_content">
			<div class="wrapper_left_div">
				<div id="search_city_block">
					<div id="search_city_field">
						<input id="search_city" class="form-control" type="text" placeholder="{l s='Enter Location' mod='mpstorelocator'}" />
						<div id="wk_sl_search_spyglass">
							<i class="material-icons">&#xE8B6;</i>
						</div>
					</div>
					<button class="btn btn-primary" id="go_btn">
						<span>{l s='Go' mod='mpstorelocator'}</span>
					</button>
				</div>
		
				<div id="search_products">
					{if isset($mp_products)}
						<select name="search_products" id="select_search_products">
							<option value="0">{l s='All Products' mod='mpstorelocator'}</option>
							{foreach $mp_products as $product}
								<option value="{$product.id_product}">{$product.product_name}</option>
							{/foreach}
						</select>
					{else}
						<select name="search_products">
							<option>{l s='No product found' mod='mpstorelocator'}</option>
						</select>
					{/if}
				</div>

				<div id="wrapper_content_left">
					{foreach $store_locations as $store}
						<div class="wk_full_store">
							<div class="wk_store" id="{$store.id}" addr="{$store.map_address}" lat="{$store.latitude}" lng="{$store.longitude}">
								<div class="wk_store_img">
									{if $store.img_exist}
										<img src="{$modules_dir}mpstorelocator/views/img/store_logo/{$store.id}.jpg"/>
									{else}
										<img src="{$modules_dir}mpstorelocator/views/img/store_logo/default.jpg"/>
									{/if}
								</div>
								<div class="wk_store_details">
									<ul>
										<li class="store_name">{$store.name}</li>
										<li>{$store.address1}, {$store.address2}, {$store.city_name}</li>
										<li>{$store.state_name} {$store.zip_code}</li>
										<li>{$store.country_name}</li>
										<li>{$store.phone}</li>
										<li class="text-right">
											<a class="edit_store" title="{l s='Edit' mod='mpstorelocator'}" href="{$link->getModuleLink('mpstorelocator', 'addstore', ['id_store' => $store.id])}">
												<i class="material-icons">&#xE254;</i>
											</a>
											&nbsp;
											<a title="{l s='Delete' mod='mpstorelocator'}" class="delete_store" href="{$link->getModuleLink('mpstorelocator', 'addstore', ['id_store' => $store.id, 'delete' => 1])}">
												<i class="material-icons">&#xE872;</i>
											</a>

											{if $store.active}
												<i class="material-icons"  title="{l s='Active' mod='mpstorelocator'}">&#xE86C;</i>
											{else}
												<i class="material-icons"  title="{l s='Pending' mod='mpstorelocator'}">&#xE888;</i>
											{/if}
										</li>
									</ul>
								</div>
							</div>
						</div>
					{/foreach}
				</div>
			</div>
			<div id="wrapper_content_right">
				<div id="map-canvas"></div>
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>
{else}
	<div class="alert alert-info">
		{l s='There is no store yet.' mod='mpstorelocator'}&nbsp;&nbsp;
		<a href="{$link->getModuleLink('mpstorelocator', 'addstore')}" class="btn btn-default">
			<span>{l s='Create Store Location' mod='mpstorelocator'}</span>
		</a>
	</div>
{/if}
{/block}