{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApBlockOwlCarouselItem -->
{if isset($formAtts.title) && $formAtts.title}
	<h4 class="title_block">{$formAtts.title|escape:'html':'UTF-8'}</h4>
{/if}
{if isset($formAtts.sub_title) && $formAtts.sub_title}
    <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
{/if}
{if isset($formAtts.description) && $formAtts.description}
	<p>{$formAtts.description nofilter}{* HTML form , no escape necessary *}</p>
{/if}
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
	<div id="{$carouselName|escape:'html':'UTF-8'}" class="owl-carousel owl-theme owl-loading">
            {$Num=array_chunk($formAtts.slides, $formAtts.itempercolumn)}
            {foreach from=$Num item=sliders name=manuloop} 
            <div class="item">
                {foreach from=$sliders item=slider}
                    <div class="block-carousel-container">
                        <div class="left-block">
                            <div class="block-carousel-image-container image">
                                <div class="ap-more-info" data-id="{$slider.id|intval}"></div>
                                {if $slider.link}
                                <a title="{l s='%s' sprintf=[$slider.title] mod='appagebuilder'}" {if $formAtts.is_open}target="_blank"{/if} href="{$slider.link}{*full link can not escape*}">
                                {/if}

                                {if isset($slider.image) && !empty($slider.image)}
                                    {if isset($formAtts) && isset($formAtts.lazyload) && $formAtts.lazyload}
                                        {* ENABLE LAZY LOAD OWL_CAROUSEL *}
                                        <img class="img-fluid lazyOwl" src="" data-src="{$slider.image}{*full link can not escape*}" alt="{if isset($slider.title)}{$slider.title|escape:'html':'UTF-8'}{/if}"/>
                                    {else}
                                        <img class="img-fluid" src="{$slider.image}{*full link can not escape*}" alt="{if isset($slider.title)}{$slider.title|escape:'html':'UTF-8'}{/if}"/>
                                    {/if}
                                {/if}
                                {if isset($slider.title) && !empty($slider.title)}
                                        <div class="title">{$slider.title|escape:'html':'UTF-8' nofilter}</div>
                                {/if}
							{if isset($slider.sub_title) && !empty($slider.sub_title)}
								<p class="sub-title">{$slider.sub_title|escape:'html':'UTF-8' nofilter}</p>
							{/if}
                                {if isset($slider.descript) && !empty($slider.descript)}
                                        <div class="descript">{$slider.descript|rtrim nofilter}{* HTML form , no escape necessary *}</div>
                                {/if}
                                {if $slider.link}{*full link can not escape*}
                                </a>
                                {/if}
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