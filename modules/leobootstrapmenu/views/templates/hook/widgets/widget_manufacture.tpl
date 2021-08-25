{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}
<div class="leo-widget" data-id_widget="{$id_widget}">
    <div class="widget-manufacture">
	{if isset($widget_heading)&&!empty($widget_heading)}
	<div class="menu-title">
		{$widget_heading}
	</div>
	{/if}
	<div class="widget-inner">
		{if manufacturers}
			<div class="manu-logo">
				{foreach from=$manufacturers item=manufacturer name=manufacturers}
				<a  href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{l s='view products' mod='leobootstrapmenu'}">
				<img {if isset($aplazyload) && $aplazyload}class="lazy" data-src{else}src{/if}="{$manufacturer.image|escape:'htmlall':'UTF-8'}" alt=""> </a>
				{/foreach}
			</div>
			{else}
   			<p class="alert alert-info">{l s='No image logo at this time.' mod='leobootstrapmenu'}</p>
		{/if}
	</div>
    </div>
	{if $show_widget_bo == 'admin'}
	    <div class="w-name">
	        <select name="inject_widget" class="inject_widget_name">
	            {foreach from=$widgets item=w}
	                <option value="{$w['key_widget']}">
	                    {$w['name']}
	                </option>
	            {/foreach}
	        </select>
	    </div>
	{/if}
</div>