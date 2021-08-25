{* 
* @Module Name: Leo Blog
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
* @description: Content Management
*}

<!-- Block RSS module-->
<div id="rss_block_left" class="block">
	<h4 class="title_block">{$title|escape:'html':'UTF-8'}</h4>
	<div class="block_content">
		{if $rss_links}
			<ul>
				{foreach from=$rss_links item='rss_link'}
					<li><a href="{$rss_link.url|escape:'html':'UTF-8'}" title="{$rss_link.title|escape:'html':'UTF-8'}">{$rss_link.title|escape:'html':'UTF-8'}</a></li>
				{/foreach}
			</ul>
		{else}
			<p>{l s='No RSS feed added' mod='leoblog'}</p>
		{/if}
	</div>
</div>
<!-- /Block RSS module-->
