{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
<div data-type="{$type_fly_cart}" style="position: {$type_position}; {$vertical_position}; {$horizontal_position}" class="leo-fly-cart solo type-{$type_position}{if $type_fly_cart == 'dropup' || $type_fly_cart == 'dropdown'} enable-dropdown{/if}{if $type_fly_cart == 'slidebar_top' || $type_fly_cart == 'slidebar_bottom' || $type_fly_cart == 'slidebar_right' || $type_fly_cart == 'slidebar_left'} enable-slidebar{/if}">
	<div class="leo-fly-cart-icon-wrapper">
		<a href="javascript:void(0)" class="leo-fly-cart-icon" data-type="{$type_fly_cart}"><i class="material-icons">&#xE8CC;</i></a>
		<span class="leo-fly-cart-total"></span>
	</div>
	{*
	<div class="leo-fly-cssload-speeding-wheel"></div>
	*}
	<div class="leo-fly-cart-cssload-loader"></div>
</div>