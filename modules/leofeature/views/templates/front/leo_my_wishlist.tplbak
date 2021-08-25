{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
{extends file=$layout}

{block name='content'}
	<section id="main">
		<div id="mywishlist">
			<h2>{l s='New wishlist' mod='leofeature'}</h2>
			<div class="new-wishlist">
				<div class="form-group">
					<label for="wishlist_name">{l s='Name' mod='leofeature'}</label>
					<input type="text" class="form-control" id="wishlist_name" placeholder="{l s='Enter name of new wishlist' mod='leofeature'}">
				</div>
				<div class="form-group has-success">
					<div class="form-control-feedback"></div>			 
				</div>
				<div class="form-group has-danger">		 
				  <div class="form-control-feedback"></div>		 
				</div>
				<button type="submit" class="btn btn-primary leo-save-wishlist-bt">
					<span class="leo-save-wishlist-loading cssload-speeding-wheel"></span>
					<span class="leo-save-wishlist-bt-text">
						{l s='Save' mod='leofeature'}
					</span>
				</button>
			</div>
			
				<div class="list-wishlist">
					<table class="table table-striped">
					  <thead class="wishlist-table-head">
						<tr>
						  <th>{l s='Name' mod='leofeature'}</th>
						  <th>{l s='Quantity' mod='leofeature'}</th>
						  <th>{l s='Viewed' mod='leofeature'}</th>
						  <th class="wishlist-datecreate-head">{l s='Created' mod='leofeature'}</th>
						  <th>{l s='Direct Link' mod='leofeature'}</th>
						  <th>{l s='Default' mod='leofeature'}</th>
						  <th>{l s='Delete' mod='leofeature'}</th>
						</tr>
					  </thead>
					  <tbody>
						{if $wishlists}
							{foreach from=$wishlists item=wishlists_item name=for_wishlists}
								<tr>					 
									<td><a href="javascript:void(0)" class="view-wishlist-product" data-name-wishlist="{$wishlists_item.name}" data-id-wishlist="{$wishlists_item.id_wishlist}"><i class="material-icons">&#xE8EF;</i>{$wishlists_item.name}</a><div class="leo-view-wishlist-product-loading leo-view-wishlist-product-loading-{$wishlists_item.id_wishlist} cssload-speeding-wheel"></div></td>
									<td class="wishlist-numberproduct wishlist-numberproduct-{$wishlists_item.id_wishlist}">{$wishlists_item.number_product|intval}</td>
									<td>{$wishlists_item.counter|intval}</td>
									<td class="wishlist-datecreate">{$wishlists_item.date_add}</td>							
									<td><a class="view-wishlist" data-token="{$wishlists_item.token}" target="_blank" href="{$view_wishlist_url}{if $leo_is_rewrite_active}?{else}&{/if}token={$wishlists_item.token}" title="{l s='View' mod='leofeature'}">{l s='View' mod='leofeature'}</a></td>
									<td>
										
											<label class="form-check-label">
												<input class="default-wishlist form-check-input" data-id-wishlist="{$wishlists_item.id_wishlist}" type="checkbox" {if $wishlists_item.default == 1}checked="checked"{/if}>
											</label>
									
									</td>
									<td><a class="delete-wishlist" data-id-wishlist="{$wishlists_item.id_wishlist}" href="javascript:void(0)" title="{l s='Delete' mod='leofeature'}"><i class="material-icons">&#xE872;</i></a></td>
								</tr>
							{/foreach}
						{/if}
					  </tbody>
					</table>
				</div>
			<div class="send-wishlist">
				<a class="leo-send-wishlist-button btn btn-info" href="javascript:void(0)" title="{l s='Send this wishlist' mod='leofeature'}">
					<i class="material-icons">&#xE163;</i>
					{l s='Send this wishlist' mod='leofeature'}
				</a>
			</div>
			<section id="products">
				<div class="leo-wishlist-product products row">
				
				</div>
			</section>
			<ul class="footer_links">
				<li class="pull-xs-left"><a class="btn btn-outline" href="{$link->getPageLink('my-account', true)|escape:'html'}"><i class="material-icons">&#xE317;</i>{l s='Back to Your Account' mod='leofeature'}</a></li>
				<li class="pull-xs-right"><a class="btn btn-outline" href="{$urls.base_url}"><i class="material-icons">&#xE88A;</i>{l s='Home' mod='leofeature'}</a></li>
			</ul>
		</div>
	</section>
{/block}

