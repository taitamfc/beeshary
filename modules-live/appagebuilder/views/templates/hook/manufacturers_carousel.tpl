{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\manufacturers_carousel -->
<div data-ride="carousel" class="carousel slide" id="{$carouselName|escape:'html':'UTF-8'}">
	{$NumManu = count($manufacturers)}
	{if $NumManu > $itemsperpage}
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

	{if array_key_exists('value_by_manufacture',$formAtts) && $formAtts.value_by_manufacture eq '1'}
		{$Num=array_chunk($manuselect,$itemsperpage)}
	{else}
		{$Num=array_chunk($manuselect,$itemsperpage)}
	{/if}
		{foreach from=$Num item=manuselect name=manuloop}
			<div class="carousel-item {if $smarty.foreach.manuloop.first}active{/if}">
				{$i = 0}
				{foreach from=$manuselect item=manu}
					{$i = $i+1}
					{if ($i mod $nbItemsPerLine) eq 1 || $i eq 1}
						<div class="row">
					{/if}
					<div class="manufacturer-item {$scolumn|escape:'html':'UTF-8'}">
						<a title="{l s='%s' sprintf=[$manu.name] mod='appagebuilder'}" 
						   href="{$link->getmanufacturerLink($manu.id_manufacturer, $manu.link_rewrite)|escape:'html':'UTF-8'}">
							<img class="img-fluid" src="{$img_manu_dir|escape:'html':'UTF-8'}{$manu.id_manufacturer|escape:'html':'UTF-8'}-{$image_type|escape:'html':'UTF-8'}.jpg" alt="{$manu.name|escape:'html':'UTF-8'}" />
							<span>{$manu.name|escape:'html':'UTF-8'}</span>
						</a>
					</div>
					{if ($i mod $nbItemsPerLine) eq 0}
						</div>
					{/if}
				{/foreach}
				{if ($i mod $nbItemsPerLine) gt 0}
					</div>
				{/if}
			</div>
		{/foreach}
	</div>
</div>
