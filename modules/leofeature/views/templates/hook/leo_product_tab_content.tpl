{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
{if isset($USE_PTABS) && $USE_PTABS == 'default'}
	
{else if isset($USE_PTABS) && $USE_PTABS == 'accordion'}
	<div id="collapseleofeatureproductreview" class="collapse" role="tabpanel">
          <div class="card-block">
{else}
	<div class="tab-pane fade in" id="leo-product-show-review-content">	
{/if}

		<div id="product_reviews_block_tab">
			{if $reviews}
				{foreach from=$reviews item=review}
					{if $review.content}
					<div class="review" itemprop="review" itemscope itemtype="https://schema.org/Review">
						<div class="review-info row">
							<div class="review_author col-sm-3">
								<span>{l s='Grade' mod='leofeature'}&nbsp;</span>
								<div class="star_content clearfix"  itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
									{section name="i" start=0 loop=5 step=1}
										{if $review.grade le $smarty.section.i.index}
											<div class="star"></div>
										{else}
											<div class="star star_on"></div>
										{/if}
									{/section}
									<meta itemprop="worstRating" content = "0" />
									<meta itemprop="ratingValue" content = "{$review.grade|escape:'html':'UTF-8'}" />
									<meta itemprop="bestRating" content = "5" />
								</div>
								<div class="review_author_infos">
									<strong itemprop="author">{$review.customer_name|escape:'html':'UTF-8'}</strong>
									<meta itemprop="datePublished" content="{$review.date_add|escape:'html':'UTF-8'|substr:0:10}" />
									<em>{dateFormat date=$review.date_add|escape:'html':'UTF-8' full=0}</em>
								</div>
							</div>

							<div class="review_details col-sm-9">
								<p itemprop="name" class="title_block">
									<strong>{$review.title}</strong>
								</p>
								<p itemprop="reviewBody">{$review.content|escape:'html':'UTF-8'|nl2br nofilter}</p>
								
							</div><!-- .review_details -->
						</div>
						
						<div class="review_button">
							<ul>
								{if $review.total_advice > 0}
									<li>
										{l s='%1$d out of %2$d people found this review useful.' sprintf=[$review.total_useful,$review.total_advice] mod='leofeature'}
									</li>
								{/if}
								{if $customer.is_logged}
									{if !$review.customer_advice && $allow_usefull_button}
									<li>
										<span>{l s='Was this review useful to you?' mod='leofeature'}</span>
										<button class="usefulness_btn btn btn-default button button-small" data-is-usefull="1" data-id-product-review="{$review.id_product_review}">
											<span>{l s='Yes' mod='leofeature'}</span>
										</button>
										<button class="usefulness_btn btn btn-default button button-small" data-is-usefull="0" data-id-product-review="{$review.id_product_review}">
											<span>{l s='No' mod='leofeature'}</span>
										</button>
									</li>
									{/if}
									{if !$review.customer_report && $allow_report_button}
									<li>
										<a href="javascript:void(0)" class="btn report_btn" data-id-product-review="{$review.id_product_review}">
											{l s='Report abuse' mod='leofeature'}
										</a>
									</li>
									{/if}
								{/if}
							</ul>
						</div>
					</div> <!-- .review -->
					{/if}
				{/foreach}
				{if (!$too_early AND ($customer.is_logged OR $allow_guests))}
					<a class="open-review-form" href="javascript:void(0)" data-id-product="{$id_product_tab_content}" data-is-logged="{$customer.is_logged}" data-product-link="{$link_product_tab_content}">
						<i class="material-icons">&#xE150;</i>
						{l s='Write a review' mod='leofeature'}
					</a>
				{/if}
			{else}
				{if (!$too_early AND ($customer.is_logged OR $allow_guests))}
					<a class="open-review-form" href="javascript:void(0)" data-id-product="{$id_product_tab_content}" data-is-logged="{$customer.is_logged}" data-product-link="{$link_product_tab_content}">
						<i class="material-icons">&#xE150;</i>
						{l s='Be the first to write your review!' mod='leofeature'}
					</a>			
				{else}
					<p class="align_center">{l s='No customer reviews for the moment.' mod='leofeature'}</p>
				{/if}
			{/if}
		</div> 
{if isset($USE_PTABS) && $USE_PTABS == 'default'}
		
{else if isset($USE_PTABS) && $USE_PTABS == 'accordion'}
		</div>
	</div>
{else}
	</div>	
{/if}
