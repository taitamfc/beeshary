{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApBlockCarouselItem -->
{if isset($formAtts.title) && $formAtts.title}
	<h4 class="title_block">{$formAtts.title|escape:'html':'UTF-8'}</h4>
{/if}
{if isset($formAtts.sub_title) && $formAtts.sub_title}
    <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
{/if}
{if isset($formAtts.descript)}
	<div>{$formAtts.descript|escape:'html':'UTF-8'}</div>
{/if}
<div data-ride="carousel" class="carousel slide" id="{$carouselName|escape:'html':'UTF-8'}">
	{$NumCount = count($formAtts.slides)}
	{if $NumCount > $itemsperpage}
		<div class="direction">
			<a class="carousel-control left" href="#{$carouselName|escape:'html':'UTF-8'}" data-slide="prev">
				<span class="icon-prev hidden-xs" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			</a>
			<a class="carousel-control right" href="#{$carouselName|escape:'html':'UTF-8'}" data-slide="next">
				<span class="icon-next" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			</a>
		</div>
	{/if}
	<div class="carousel-inner">
	{$Num=array_chunk($formAtts.slides, $itemsperpage)}
	{foreach from=$Num item=sliders name=val}
		<div class="carousel-item {if $smarty.foreach.val.first}active{/if}">
			{foreach from=$sliders item=slider name="sliders"}
				{if $nbItemsPerLine == 1 || $smarty.foreach.sliders.first || $smarty.foreach.sliders.iteration%$nbItemsPerLine == 1}
					<div class="row">
				{/if}
				<div class="{$scolumn|escape:'html':'UTF-8'}">
					{if $slider.link}
					<a title="{l s='%s' sprintf=[$slider.title] mod='appagebuilder'}" {if $formAtts.is_open}target="_blank"{/if} href="{$slider.link}{*full link can not escape*}">
					{/if}
					
					{if isset($slider.image) && !empty($slider.image)}
						<img class="img-fluid" src="{$slider.image|escape:'html':'UTF-8'}" alt="{if isset($slider.title)}{$slider.title|escape:'html':'UTF-8'}{/if}"/>
					{/if}
					{if isset($slider.title) && !empty($slider.title)}
						<div class="title">{$slider.title|escape:'html':'UTF-8' nofilter}</div>
					{/if}
					{if isset($slider.sub_title) && !empty($slider.sub_title)}
						<p class="sub-title">{$slider.sub_title|escape:'html':'UTF-8' nofilter}</p>
					{/if}
					{if isset($slider.descript) && !empty($slider.descript)}
						<div class="descript">{$slider.descript nofilter}{* HTML form , no escape necessary *}</div>
					{/if}
					{if $slider.link}
					</a>
					{/if}
				</div>
				{if $nbItemsPerLine == 1 || $smarty.foreach.sliders.last || $smarty.foreach.sliders.iteration%$nbItemsPerLine == 0}
					</div>
				{/if}
			{/foreach}
		</div>
	{/foreach}
</div>
</div>
