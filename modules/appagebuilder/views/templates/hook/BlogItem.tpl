{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\BlogItem -->
<div class="blog-container" itemscope itemtype="https://schema.org/Blog">
    <div class="left-block">
        <div class="blog-image-container">
            <a class="blog_img_link" href="{$blog.link|escape:'html':'UTF-8'}" title="{$blog.title|escape:'html':'UTF-8'}" itemprop="url">
			{if isset($formAtts.bleoblogs_sima) && $formAtts.bleoblogs_sima}
                            {if isset($formAtts) && isset($formAtts.lazyload) && $formAtts.lazyload}
                                    {* ENABLE LAZY LOAD OWL_CAROUSEL *}
				<img class="img-fluid lazyOwl" src="" data-src="{if (isset($blog.preview_thumb_url) && $blog.preview_thumb_url != '')}{$blog.preview_thumb_url}{else}{$blog.preview_url}{/if}{*full url can not escape*}" 
					 alt="{if !empty($blog.legend)}{$blog.legend|escape:'html':'UTF-8'}{else}{$blog.title|escape:'html':'UTF-8'}{/if}" 
					 title="{if !empty($blog.legend)}{$blog.legend|escape:'html':'UTF-8'}{else}{$blog.title|escape:'html':'UTF-8'}{/if}" 
					 {if isset($formAtts.bleoblogs_width)}width="{$formAtts.bleoblogs_width|escape:'html':'UTF-8'}" {/if}
					 {if isset($formAtts.bleoblogs_height)} height="{$formAtts.bleoblogs_height|escape:'html':'UTF-8'}"{/if}
					 itemprop="image" />
                            {else}
				<img class="img-fluid" src="{if (isset($blog.preview_thumb_url) && $blog.preview_thumb_url != '')}{$blog.preview_thumb_url}{else}{$blog.preview_url}{/if}{*full url can not escape*}" 
					 alt="{if !empty($blog.legend)}{$blog.legend|escape:'html':'UTF-8'}{else}{$blog.title|escape:'html':'UTF-8'}{/if}" 
					 title="{if !empty($blog.legend)}{$blog.legend|escape:'html':'UTF-8'}{else}{$blog.title|escape:'html':'UTF-8'}{/if}" 
					 {if isset($formAtts.bleoblogs_width)}width="{$formAtts.bleoblogs_width|escape:'html':'UTF-8'}" {/if}
					 {if isset($formAtts.bleoblogs_height)} height="{$formAtts.bleoblogs_height|escape:'html':'UTF-8'}"{/if}
					 itemprop="image" />
                            {/if}
			{/if}
            </a>
        </div>
    </div>
    <div class="right-block">
        {if isset($formAtts.show_title) && $formAtts.show_title}
        	<h5 class="blog-title" itemprop="name"><a href="{$blog.link}{*full url can not escape*}" title="{$blog.title|escape:'html':'UTF-8'}">{$blog.title|strip_tags:'UTF-8'|truncate:80:'...'}</a></h5>
        {/if}
        <div class="blog-meta">
			{if isset($formAtts.bleoblogs_saut) && $formAtts.bleoblogs_saut}
				<span class="author">
					<span class="icon-author"> {l s='Author' mod='appagebuilder'}:</span>{if isset($blog.author_name) && $blog.author_name != ''} {$blog.author_name|escape:'html':'UTF-8'}{else} {$blog.author|escape:'html':'UTF-8'}{/if}
				</span>
			{/if}		
			{if isset($formAtts.bleoblogs_scat) && $formAtts.bleoblogs_scat}
				<span class="cat"> <span class="icon-list">{l s='In' mod='appagebuilder'}</span> 
					<a href="{$blog.category_link}{*full url can not escape*}" title="{$blog.category_title|escape:'html':'UTF-8'}">{$blog.category_title|escape:'html':'UTF-8'}</a>
				</span>
			{/if}
			{if isset($formAtts.bleoblogs_scre) && $formAtts.bleoblogs_scre}
				<span class="created"><span class=""></span>
					<span class="icon-calendar"> {l s='On' mod='appagebuilder'} </span> 
					<time class="date" datetime="{strtotime($blog.date_add)|date_format:"%Y"}{*convert to date time*}">										
						{assign var='blog_date' value=strtotime($blog.date_add)|date_format:"%A"}
						{l s=$blog_date mod='appagebuilder'},	<!-- day of week -->
						{assign var='blog_month' value=strtotime($blog.date_add)|date_format:"%B"}
						{l s=$blog_month mod='appagebuilder'}
						{assign var='blog_date_add' value=strtotime($blog.date_add)|date_format:"%d"}<!-- day of month -->
						{assign var='blog_day' value=strtotime($blog.date_add)|date_format:"%e"}
						{l s=$blog_day mod='appagebuilder'}
						{assign var='blog_daycount' value=$formAtts.leo_blog_helper->string_ordinal($blog_date_add)}
						{l s=$blog_daycount mod='appagebuilder'},
						{assign var='blog_year' value=strtotime($blog.date_add)|date_format:"%Y"}						
						{l s=$blog_year mod='appagebuilder'}	<!-- year -->
					</time>
				</span>
			{/if}
			{if isset($formAtts.bleoblogs_scoun) && $formAtts.bleoblogs_scoun}
				<span class="nbcomment">
					<span class="icon-comment"> {l s='Comment' mod='appagebuilder'}:</span> {$blog.comment_count|intval}
				</span>
			{/if}
			
			{if isset($formAtts.bleoblogs_shits) && $formAtts.bleoblogs_shits}
				<span class="hits">
					<span class="icon-hits"> {l s='Hits' mod='appagebuilder'}:</span> {$blog.hits|intval}
				</span>	
			{/if}
		</div>
		{if isset($formAtts.show_desc) && $formAtts.show_desc}
	        <p class="blog-desc" itemprop="description">
	            {$blog.description|strip_tags:'UTF-8'|truncate:160:'...'}
	        </p>
        {/if}
    </div>
	
	<div class="hidden-xl-down hidden-xl-up datetime-translate">
		{l s='Sunday' mod='appagebuilder'}
		{l s='Monday' mod='appagebuilder'}
		{l s='Tuesday' mod='appagebuilder'}
		{l s='Wednesday' mod='appagebuilder'}
		{l s='Thursday' mod='appagebuilder'}
		{l s='Friday' mod='appagebuilder'}
		{l s='Saturday' mod='appagebuilder'}
		
		{l s='January' mod='appagebuilder'}
		{l s='February' mod='appagebuilder'}
		{l s='March' mod='appagebuilder'}
		{l s='April' mod='appagebuilder'}
		{l s='May' mod='appagebuilder'}
		{l s='June' mod='appagebuilder'}
		{l s='July' mod='appagebuilder'}
		{l s='August' mod='appagebuilder'}
		{l s='September' mod='appagebuilder'}
		{l s='October' mod='appagebuilder'}
		{l s='November' mod='appagebuilder'}
		{l s='December' mod='appagebuilder'}
		
		{l s='st' mod='appagebuilder'}
		{l s='nd' mod='appagebuilder'}
		{l s='rd' mod='appagebuilder'}
		{l s='th' mod='appagebuilder'}
	</div>
</div>
