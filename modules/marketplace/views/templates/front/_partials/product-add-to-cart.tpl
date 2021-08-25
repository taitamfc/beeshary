{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<form action="{url entity=cart}" method="post">
	<input type="hidden" name="token" value="{$static_token}">
	<input type="hidden" name="id_product" value="{$product.id_ps_product}">
	<input type="hidden" name="id_customization" value="0">

	{if isset($product.hasCombination) && isset($product.combinationData)}
		{foreach $product.combinationData as $combination}
			<input type="hidden" data-product-attribute="{$combination.id_attribute_group}" name="group[{$combination.id_attribute_group}]" value="{$combination.id_attribute}">
		{/foreach}
	{/if}

	<input type="hidden" name="qty" value="{if $product.minimal_quantity}{$product.minimal_quantity}{else}1{/if}">
	<button type="submit" id="wk_shop_cart_{$product.id_ps_product}" data-button-action="add-to-cart" class="btn btn-primary">
		<i class="material-icons shopping-cart">&#xE8CC;</i>{l s='Add to Cart' mod='marketplace'}
	</button>
</form>