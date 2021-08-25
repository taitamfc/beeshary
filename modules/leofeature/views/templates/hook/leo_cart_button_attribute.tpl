{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}

{if isset($leo_cart_product_attribute.combinations) && count($leo_cart_product_attribute.combinations) > 0}		
	<div class="dropdown leo-pro-attr-section">
	  <button class="btn btn-secondary dropdown-toggle leo-bt-select-attr dropdownListAttrButton_{$leo_cart_product_attribute.id_product}" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		{$leo_cart_product_attribute.attribute_designation}
	  </button>
	  <div class="dropdown-menu leo-dropdown-attr">
		{foreach from=$leo_cart_product_attribute.combinations item=attribute}
			<a class="dropdown-item leo-select-attr{if $attribute.id_product_attribute == $leo_cart_product_attribute.id_product_attribute} selected{/if}{if $attribute.add_to_cart_url == ''} disable{/if}" href="#" data-id-product="{$attribute.id_product}" data-id-attr="{$attribute.id_product_attribute}" data-qty-attr="{$attribute.quantity}" data-min-qty-attr="{$attribute.minimal_quantity}">{$attribute.attribute_designation}</a>
		{/foreach}
		
	  </div>
	</div>
{/if}

