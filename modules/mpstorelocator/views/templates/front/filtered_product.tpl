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
{if isset($products)}
	<div class="block ApProductList">
		<ul class="product_list grid row dropdown pl_activites_home">
		{if isset($products)}
			{include file="module:mpstorelocator/views/templates/front/product_detail.tpl" products=$products}
		{/if}
		</ul>
	</div>
{else}
	{l s="No Product Found" mod='mpstorelocator'}
{/if}