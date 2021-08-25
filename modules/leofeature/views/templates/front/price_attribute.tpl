{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
{if $product_price_attribute.show_price}
	{if $product_price_attribute.has_discount}
		{hook h='displayProductPriceBlock' product=$product_price_attribute type="old_price"}
		<span class="sr-only">{l s='Regular price' mod='leofeature'}</span>
		<span class="regular-price">{$product_price_attribute.regular_price}</span>
		{if $product_price_attribute.discount_type === 'percentage'}
			<span class="discount-percentage discount-product">{$product_price_attribute.discount_percentage}</span>
		{elseif $product_price_attribute.discount_type === 'amount'}
			<span class="discount-amount discount-product">{$product_price_attribute.discount_amount_to_display}</span>
		{/if}
	{/if}

	{hook h='displayProductPriceBlock' product=$product_price_attribute type="before_price"}

	<span class="sr-only">{l s='Price' mod='leofeature'}</span>
	<span itemprop="price" class="price">{$product_price_attribute.price}</span>

	{hook h='displayProductPriceBlock' product=$product_price_attribute type='unit_price'}

	{hook h='displayProductPriceBlock' product=$product_price_attribute type='weight'}
{/if}