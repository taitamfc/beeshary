{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
{if isset($USE_PTABS) && $USE_PTABS == 'default'}
	<h4 class="title-info-product leo-product-show-review-title">{l s='Reviews' mod='leofeature'}</h4>
{else if isset($USE_PTABS) && $USE_PTABS == 'accordion'}
	<div class="card-header" role="tab" id="headingleofeatureproductreview">
	  <h5 class="h5">
		<a class="collapsed leo-product-show-review-title leofeature-accordion" data-toggle="collapse" data-parent="#accordion" href="#collapseleofeatureproductreview" aria-expanded="false" aria-controls="collapseleofeatureproductreview">
		  {l s='Reviews' mod='leofeature'}
		</a>
	 </h5>
  </div>
{else}
	<li class="nav-item">
	  <a class="nav-link leo-product-show-review-title" data-toggle="tab" href="#leo-product-show-review-content">{l s='Reviews' mod='leofeature'}</a>
	</li>
{/if}

