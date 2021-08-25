{**
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-9999 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER
* support@mypresta.eu
*}
{literal}
<script>
    $(document).ready(function ($) {
      $("#carousel-featured").owlCarousel({
			autoHeight: false,
			autoplay: false,
			autoplayTimeout: 3000,
			smartSpeed: 2000,
			loop: true,
			dots: true,
			nav: false,
			margin: 0,
			items: 4,
			slideSpeed: 5000
      });
	      });
</script>
{/literal}
<div id="featured-category-products-block-center" class="featured-products clearfix">
    <!--h1 class="products-section-title text-uppercase ">{l s='Featured products' d='Module.FeaturedCategory.Shop'}</h1-->
    {if isset($products) && $products}
        <div class="owl-carousel" id="carousel-featured">

            {foreach from=$products item="product"}
                {include file="catalog/_partials/miniatures/featuredproduct.tpl" product=$product}
            {/foreach}

        </div>
    {else}
        <ul id="categoryfeatured" class="categoryfeatured tab-pane">
            <li class="alert alert-info">{l s='No featured products at this time.' d='Module.FeaturedCategory.Shop'}</li>
        </ul>
    {/if}
</div>

              <div class="zigZagLine"  style="clear:both;"></div>  
