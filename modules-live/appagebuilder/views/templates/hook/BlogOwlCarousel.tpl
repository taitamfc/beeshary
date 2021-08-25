{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\BlogOwlCarousel -->
<div class="owl-row">

	<div class="timeline-wrapper clearfix prepare"
		data-item="{$formAtts.number_fake_item}" 
		data-xl="{$formAtts.array_fake_item.xl}" 
		data-lg="{$formAtts.array_fake_item.lg}" 
		data-md="{$formAtts.array_fake_item.md}" 
		data-sm="{$formAtts.array_fake_item.sm}" 
		data-m="{$formAtts.array_fake_item.m}"
	>
		{for $foo=1 to $formAtts.number_fake_item}			
			<div class="timeline-parent">
				{for $foo_child=1 to $formAtts.itempercolumn}
					<div class="timeline-item">
						<div class="animated-background">					
							<div class="background-masker content-top"></div>							
							<div class="background-masker content-second-line"></div>							
							<div class="background-masker content-third-line"></div>							
							<div class="background-masker content-fourth-line"></div>
						</div>
					</div>
				{/for}
			</div>
		{/for}
	</div>
    <div id="{$carouselName nofilter}{* HTML form , no escape necessary *}" class="owl-carousel owl-theme owl-loading">
    {$mproducts=array_chunk($products, $formAtts.itempercolumn)}
    {foreach from=$mproducts item=products name=mypLoop}
    	<div class="item">
    		{foreach from=$products item=blog name=products}
                {assign var=leo_include_file value=$leo_helper->getTplTemplate('BlogItem.tpl', $formAtts['override_folder'])}
                {include file=$leo_include_file}
    		{/foreach}
    	</div>
    {/foreach}
    </div>
</div>
<script type="text/javascript">
ap_list_functions.push(function(){
	$('#{$carouselName|escape:'html':'UTF-8'}').imagesLoaded( function() {
		$('#{$carouselName|escape:'html':'UTF-8'}').owlCarousel({
			items :             {if $formAtts.items}{$formAtts.items|intval}{else}false{/if},
			itemsDesktop :      {if isset($formAtts.itemsdesktop) && $formAtts.itemsdesktop}[1200,{$formAtts.itemsdesktop|intval}]{else}false{/if},
			itemsDesktopSmall : {if isset($formAtts.itemsdesktopsmall) && $formAtts.itemsdesktopsmall}[992,{$formAtts.itemsdesktopsmall|intval}]{else}false{/if},
			itemsTablet :       {if isset($formAtts.itemstablet) && $formAtts.itemstablet}[768,{$formAtts.itemstablet|intval}]{else}false{/if},
			itemsMobile :       {if isset($formAtts.itemsmobile) && $formAtts.itemsmobile}[576,{$formAtts.itemsmobile|intval}]{else}false{/if},
			itemsCustom :       {if isset($formAtts.itemscustom) && $formAtts.itemscustom}{$formAtts.itemscustom|escape:'html':'UTF-8'}{else}false{/if},
			singleItem :        false,       // true : show only 1 item
			itemsScaleUp :      false,
			slideSpeed :        {if $formAtts.slidespeed}{$formAtts.slidespeed|intval}{else}200{/if},        //  change speed when drag and drop a item
			paginationSpeed :   {if $formAtts.paginationspeed}{$formAtts.paginationspeed|intval}{else}800{/if},       // change speed when go next page
			autoPlay :          {if $formAtts.autoplay}true{else}false{/if},       // time to show each item
			stopOnHover :       {if $formAtts.stoponhover}true{else}false{/if},
			navigation :        {if $formAtts.navigation}true{else}false{/if},
			navigationText :    ["&lsaquo;", "&rsaquo;"],
			scrollPerPage :     {if $formAtts.scrollperpage}true{else}false{/if},
			pagination :        {if $formAtts.pagination}true{else}false{/if},       // show bullist
			paginationNumbers : {if $formAtts.paginationnumbers}true{else}false{/if},       // show number
			responsive :        {if $formAtts.responsive}true{else}false{/if},
			lazyLoad :          {if $formAtts.lazyload}true{else}false{/if},
			lazyFollow :        {if $formAtts.lazyfollow}true{else}false{/if},       // true : go to page 7th and load all images page 1...7. false : go to page 7th and load only images of page 7th
			lazyEffect :        "{$formAtts.lazyeffect|escape:'html':'UTF-8'}",
			autoHeight :        {if $formAtts.autoheight}true{else}false{/if},
			mouseDrag :         {if $formAtts.mousedrag}true{else}false{/if},
			touchDrag :         {if $formAtts.touchdrag}true{else}false{/if},
			addClassActive :    true,
			direction:          {if $formAtts.rtl}'rtl'{else}false{/if},
			afterInit: OwlLoaded,
			afterAction : SetOwlCarouselFirstLast,
		});
	});
});
function OwlLoaded(el){
    el.removeClass('owl-loading').addClass('owl-loaded').parents('.owl-row').addClass('hide-loading');
   
};

</script>