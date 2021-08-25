{* 
* @Module Name: Leo Blog
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
* @description: Content Management
*}

{extends file=$layout}

{block name='content'}
	<section id="main">
		{if isset($no_follow) AND $no_follow}
			{assign var='no_follow_text' value='rel="nofollow"'}
		{else}
			{assign var='no_follow_text' value=''}
		{/if} 
		<div id="blog-listing" class="blogs-container box">
			{if isset($filter.type)}
				{if $filter.type=='tag'}
					<h1>{l s='Filter Blogs By Tag' mod='leoblog'} : <span>{$filter.tag|escape:'html':'UTF-8'}</span></h1>
				{elseif $filter.type=='author'}
					{if isset($filter.id_employee)}
						<h1>{l s='Filter Blogs By Blogger' mod='leoblog'} : <span>{$filter.employee->firstname|escape:'html':'UTF-8'} {$filter.employee->lastname|escape:'html':'UTF-8'}</span></h1>
					{else}
						<h1>{l s='Filter Blogs By Blogger' mod='leoblog'} : <span>{$filter.author_name|escape:'html':'UTF-8'}</span></h1>
					{/if}
					
				{/if}
			{else}
				<h1 class="blog-lastest-title">{l s='Lastest Blogs' mod='leoblog'}</h1>
				{if $url_rss != ''}
					<h4 class="blog-lastest-rss"><a href="{$url_rss}">{l s='RSS' mod='leoblog'}</a></h4>
				{/if}
			{/if}

			<div class="inner">
				{if count($leading_blogs)+count($secondary_blogs)>0}
					{if count($leading_blogs)}
					<div class="leading-blog">  
						{foreach from=$leading_blogs item=blog name=leading_blog}
						 
							{if ($smarty.foreach.leading_blog.iteration%$listing_leading_column==1)&&$listing_leading_column>1}
							  <div class="row">
							{/if}
							<div class="{if $listing_leading_column<=1}no{/if}col-lg-{floor(12/$listing_leading_column|escape:'html':'UTF-8')}">
								
									{include file="module:leoblog/views/templates/front/default/_listing_blog.tpl"}
								
							</div>	
							{if ($smarty.foreach.leading_blog.iteration%$listing_leading_column==0||$smarty.foreach.leading_blog.last)&&$listing_leading_column>1}
								</div>
							{/if}
						{/foreach}
					</div>
					{/if}


					{if count($secondary_blogs)}
					<div class="secondary-blog">

						{foreach from=$secondary_blogs item=blog name=secondary_blog}
							{if ($smarty.foreach.secondary_blog.iteration%$listing_secondary_column==1)&&$listing_secondary_column>1}
							  <div class="row">
							{/if}
							<div class="{if $listing_secondary_column<=1}no{/if}col-lg-{floor(12/$listing_secondary_column|escape:'html':'UTF-8')}">
								
									{include file="module:leoblog/views/templates/front/default/_listing_blog.tpl"}
							
								
								
							</div>	
							{if ($smarty.foreach.secondary_blog.iteration%$listing_secondary_column==0||$smarty.foreach.secondary_blog.last)&&$listing_secondary_column>1}
							</div>
							{/if}
						{/foreach}
					</div>
					{/if}
					<div class="top-pagination-content clearfix bottom-line">
						
									{include file="module:leoblog/views/templates/front/default/_pagination.tpl"}
						
							
						
					</div>
				{else}
					<div class="alert alert-warning">{l s='Sorry, We are update data, please come back later!!!!' mod='leoblog'}</div>
				{/if}    

			</div>
		</div>
	</section>
{/block}