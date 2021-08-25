{*
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($admin)}
  <li>
	<a href="#mp_product_customization" data-toggle="tab">
		<i class="icon-star-empty"></i>
		{l s='Customization' mod='mpproductcustomization'}
	</a>
</li>
{else}
	<li class="nav-item">
		<a class="nav-link" href="#mp_product_customization" data-toggle="tab">
			<i class="material-icons">&#xE83A;</i>
			{l s='Customization' mod='mpproductcustomization'}
		</a>
	</li>
{/if}