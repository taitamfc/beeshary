{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
{if $enable_overlay_background}
	<div class="leo-fly-cart-mask"></div>
{/if}

<div class="leo-fly-cart-slidebar {$type}">
	
	<div class="leo-fly-cart disable-dropdown">
		<div class="leo-fly-cart-wrapper">
			<div class="leo-fly-cart-icon-wrapper">
				<a href="javascript:void(0)" class="leo-fly-cart-icon"><i class="material-icons">&#xE8CC;</i></a>
				<span class="leo-fly-cart-total"></span>
			</div>
			{*
			<div class="leo-fly-cssload-speeding-wheel"></div>
			*}
			<div class="leo-fly-cart-cssload-loader"></div>
		</div>
	</div>

</div>