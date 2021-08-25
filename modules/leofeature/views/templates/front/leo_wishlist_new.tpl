{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
<tr class="new">
	<td>
		<a href="javascript:void(0)" class="view-wishlist-product" data-name-wishlist="{$wishlist->name}" data-id-wishlist="{$wishlist->id}">
			<i class="material-icons">&#xE8EF;</i>
			{$wishlist->name}
		</a>
		<div class="leo-view-wishlist-product-loading leo-view-wishlist-product-loading-{$wishlist->id} cssload-speeding-wheel"></div>
	</td>
	<td class="wishlist-numberproduct wishlist-numberproduct-{$wishlist->id}">0</td>
	<td>0</td>
	<td class="wishlist-datecreate">{$wishlist->date_add}</td>					
	<td><a class="view-wishlist" data-token="{$wishlist->token}" target="_blank" href="{$url_view_wishlist}" title="{l s='View' mod='leofeature'}">{l s='View' mod='leofeature'}</a></td>
	<td>
		<label class="form-check-label">
			<input class="default-wishlist form-check-input" data-id-wishlist="{$wishlist->id}" type="checkbox" {$checked}>
		</label>
	</td>
	<td><a class="delete-wishlist" data-id-wishlist="{$wishlist->id}" href="javascript:void(0)" title="{l s='Delete' mod='leofeature'}"><i class="material-icons">&#xE872;</i></a></td>
</tr>

