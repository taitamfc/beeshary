{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApBlockCarousel -->
{if isset($formAtts.lib_has_error) && $formAtts.lib_has_error}
    {if isset($formAtts.lib_error) && $formAtts.lib_error}
        <div class="alert alert-warning leo-lib-error">{$formAtts.lib_error}</div>
    {/if}
{else}
	{if isset($formAtts.title) && $formAtts.title}
		<h4 class="title_block">{$formAtts.title|escape:'html':'UTF-8'}</h4>
	{/if}
	{if isset($formAtts.sub_title) && $formAtts.sub_title}
	    <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
	{/if}
	{if isset($formAtts.description) && $formAtts.description}
		<p>{$formAtts.description nofilter}{* HTML form , no escape necessary *}</p>
	{/if}
    {if !empty($formAtts.slides)}
		<div class="slick-row">
			<div class="timeline-wrapper clearfix prepare"
				data-item="{$formAtts.number_fake_item}" 
				data-xl="{if isset($formAtts.array_fake_item.xl)}{$formAtts.array_fake_item.xl}{/if}" 
				data-lg="{if isset($formAtts.array_fake_item.lg)}{$formAtts.array_fake_item.lg}{/if}" 
				data-md="{if isset($formAtts.array_fake_item.md)}{$formAtts.array_fake_item.md}{/if}" 
				data-sm="{if isset($formAtts.array_fake_item.sm)}{$formAtts.array_fake_item.sm}{/if}" 
				data-m="{if isset($formAtts.array_fake_item.m)}{$formAtts.array_fake_item.m}{/if}"
			>
				{for $foo=1 to $formAtts.number_fake_item}			
					<div class="timeline-parent">
						{for $foo_child=1 to $formAtts.slick_row}
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
			<div class="slick-blogs slick-slider slick-loading" id="{$formAtts.form_id|escape:'html':'UTF-8'}">
				{foreach from=$formAtts.slides item=slider name=mypLoop} 
					<div class="slick-slide {if $smarty.foreach.mypLoop.index == 0} first{/if}">
						<div class="item">
							<div class="block-carousel-container">
								<div class="left-block">
									<div class="block-carousel-image-container image">
										<div class="ap-more-info" data-id="{$slider.id|intval}"></div>
										{if $slider.link}
										<a title="{l s='%s' sprintf=[$slider.title] mod='appagebuilder'}" {if $formAtts.is_open}target="_blank"{/if} href="{$slider.link}{*full link can not escape*}">
										{/if}
										
										{if isset($slider.image) && !empty($slider.image)}
											<img class="img-fluid" src="{$slider.image}{*full link can not escape*}" alt="{if isset($slider.title)}{$slider.title|escape:'html':'UTF-8'}{/if}"/>
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
						</div>
					</div>
				{/foreach}
			</div>
		</div>
    {else}
        <p class="alert alert-info">{l s='No slide at this time.' mod='appagebuilder'}</p>
    {/if}
{/if}

<script type="text/javascript">
ap_list_functions.push(function(){
	$('#{$formAtts.form_id|escape:'html':'UTF-8'}').imagesLoaded( function() {
		$('#{$formAtts.form_id|escape:'html':'UTF-8'}').slick(
			{if $formAtts.slick_custom_status}
				{$formAtts.slick_custom}
			{else}
			{
				centerMode: {if isset($formAtts.slick_centermode) && $formAtts.slick_centermode}true{else}false{/if},
				centerPadding: '{if isset($formAtts.slick_centerpadding) && $formAtts.slick_centerpadding}{$formAtts.slick_centerpadding}{else}0{/if}px',
				dots: {if $formAtts.slick_dot}true{else}false{/if},
				infinite: {if isset($formAtts.slick_loopinfinite) && $formAtts.slick_loopinfinite}true{else}false{/if},
				vertical: {if $formAtts.slick_vertical}true{else}false{/if},
				verticalSwiping : {if $formAtts.slick_vertical}true{else}false{/if},
				autoplay: {if $formAtts.slick_autoplay}true{else}false{/if},
				pauseonhover: {if $formAtts.slick_pauseonhover}true{else}false{/if},
				autoplaySpeed: 2000,
				arrows: {if $formAtts.slick_arrows}true{else}false{/if},
				rows: {$formAtts.slick_row},
				slidesToShow: {$formAtts.slick_slidestoshow},
				slidesToScroll: {if $formAtts.slick_dot}{$formAtts.slick_slidestoshow}{else}{$formAtts.slick_slidestoscroll}{/if},
				rtl: {if isset($IS_RTL) && $IS_RTL}true{else}false{/if},
				responsive: [ 
					{foreach from=$formAtts.slick_items_custom item=mobile name=mobiles}
					{
					  breakpoint: {$mobile.0},
					  settings: {
						centerMode: {if isset($formAtts.slick_centermode) && $formAtts.slick_centermode}true{else}false{/if},
						centerPadding: '{if isset($formAtts.slick_centerpadding) && $formAtts.slick_centerpadding}{$formAtts.slick_centerpadding}{else}0{/if}px',
						slidesToShow: {$mobile.1},
						slidesToScroll: {if $formAtts.slick_dot}{$mobile.1}{else}{$formAtts.slick_slidestoscroll}{/if},
					  }
					},
					{/foreach}
				]
			}
			{/if}
		);
		$('#{$formAtts.form_id|escape:'html':'UTF-8'}').removeClass('slick-loading').addClass('slick-loaded').parents('.slick-row').addClass('hide-loading');
	});
});
</script>