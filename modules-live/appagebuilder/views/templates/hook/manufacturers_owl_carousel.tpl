{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\manufacturers_owl_carousel -->
<div class="owl-row">
	<div class="timeline-wrapper clearfix prepare owl-manu"
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
						</div>
					</div>
				{/for}
			</div>
		{/for}
	</div>
    <div id="{$carouselName|escape:'html':'UTF-8'}" class="owl-carousel owl-theme owl-loading">
    	{if array_key_exists('value_by_manufacture',$formAtts) && $formAtts.value_by_manufacture eq '1' }
    		{$Num=array_chunk($manuselect,$formAtts.itempercolumn)}
    	{else}
    		{$Num=array_chunk($manuselect,$formAtts.itempercolumn)}
    	{/if}

    	{foreach from=$Num item=manuselect name=manuloop} 
    	<div class="item {if $smarty.foreach.manuloop.index == 0} first{/if}">
    		{foreach from=$manuselect item=manu}
    			<div class="manufacturer-container manufacturer-block" itemscope itemtype="https://schema.org/Brand">
    				<div class="left-block">
    					<div class="manufacturer-image-container image">
    						<a title="{l s='%s' sprintf=[$manu.name] mod='appagebuilder'}" href="{$link->getmanufacturerLink($manu.id_manufacturer, $manu.link_rewrite)|escape:'html':'UTF-8'}" itemprop="url">
                                                    {if isset($formAtts) && isset($formAtts.lazyload) && $formAtts.lazyload}
                                                        {* ENABLE LAZY LOAD OWL_CAROUSEL *}
                                                        <img class="img-fluid lazyOwl" src="" data-src="{$img_manu_dir}{*full link can not escape*}{$manu.id_manufacturer|intval}-{$image_type|escape:'html':'UTF-8'}.jpg" alt="{$manu.name|escape:'html':'UTF-8'}" itemprop="image" />
                                                    {else}
                                                        <img class="img-fluid" src="{$img_manu_dir}{*full link can not escape*}{$manu.id_manufacturer|intval}-{$image_type|escape:'html':'UTF-8'}.jpg" alt="{$manu.name|escape:'html':'UTF-8'}" itemprop="image" />
                                                    {/if}
    						</a>
    					</div>
    				</div>
    			</div>
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