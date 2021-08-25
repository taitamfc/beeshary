{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}


<li class="nav-item">
	<a data-toggle="tab" href="#mp_product_shipping_tab" class="nav-link">
		{if isset($admin) && $admin}
			<i class="icon-truck"></i>
		{else}
			<i class="material-icons">&#xE905;</i>
		{/if}
		{l s='Shipping' mod='mpshipping'}
	</a>
</li>
