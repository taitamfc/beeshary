{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
{if (isset($nbReviews) && $nbReviews > 0) || $show_zero_product_review}

	<div class="leo-list-product-reviews" {if (isset($nbReviews) && $nbReviews > 0)}itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating"{/if}>
		<div class="leo-list-product-reviews-wraper">
			<div class="star_content clearfix">
				{section name="i" start=0 loop=5 step=1}
					{if $averageTotal le $smarty.section.i.index}
						<div class="star"></div>
					{else}
						<div class="star star_on fas fa-star"></div>
					{/if}
				{/section}
				{if (isset($nbReviews) && $nbReviews > 0)}
					<meta itemprop="worstRating" content = "0" />
					<meta itemprop="ratingValue" content = "{if isset($ratings.avg)}{$ratings.avg|round:1|escape:'html':'UTF-8'}{else}{$averageTotal|round:1|escape:'html':'UTF-8'}{/if}" />
					<meta itemprop="bestRating" content = "5" />
				{/if}
			</div>
			{if isset($nbReviews) && $nbReviews > 0}
				{if $show_number_product_review}
					<span class="nb-revews"><span itemprop="reviewCount">{$nbReviews}</span> {l s='Review(s)' mod='leofeature'}</span>
				{else}
					<meta itemprop="reviewCount" content = "{$nbReviews}" />
				{/if}
			{/if}
		</div>
	</div>

{/if}
