{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApManuFacturersCarousel -->
{if isset($formAtts.lib_has_error) && $formAtts.lib_has_error}
    {if isset($formAtts.lib_error) && $formAtts.lib_error}
        <div class="alert alert-warning leo-lib-error">{$formAtts.lib_error}</div>
    {/if}
{else}
    {if !empty($manufacturers)}
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
			<div class="slick-manufacturers slick-slider slick-loading" id="{$formAtts.form_id|escape:'html':'UTF-8'}">
			{foreach from=$manufacturers item=manu name=mypLoop}
				<div class="slick-slide {if $smarty.foreach.mypLoop.index == 0} first{/if}">
					<div class="item">
						<div class="manufacturer-container manufacturer-block" itemscope itemtype="https://schema.org/Brand">
		    				<div class="left-block">
		    					<div class="manufacturer-image-container image">
		    						<a title="{l s='%s' sprintf=[$manu.name] mod='appagebuilder'}" href="{$link->getmanufacturerLink($manu.id_manufacturer, $manu.link_rewrite)|escape:'html':'UTF-8'}" itemprop="url">
		    								<img class="img-fluid" src="{$img_manu_dir}{*full link can not escape*}{$manu.id_manufacturer|intval}-{$image_type|escape:'html':'UTF-8'}.jpg" alt="{$manu.name|escape:'html':'UTF-8'}" itemprop="image" />
		    						</a>
		    					</div>
		    				</div>
		    			</div>
		    		</div>
		    	</div>
			{/foreach}
			</div>
		</div>
    {else}
        <p class="alert alert-info">{l s='No manufacturer at this time.' mod='appagebuilder'}</p>
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