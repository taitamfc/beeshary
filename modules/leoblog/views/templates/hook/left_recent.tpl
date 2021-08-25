{* 
* @Module Name: Leo Blog
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
* @description: Content Management
*}

{if isset($leading_blogs) AND !empty($leading_blogs)}
    <section id="blogRecentBlog" class="block leo-block-sidebar hidden-sm-down">
        <h4 class='title_block'><a href="">{l s='Recent Articles' mod='leoblog'}</a></h4>
            <div class="block_content products-block">
                <ul class="lists">
                    {foreach from=$leading_blogs item="blog" name=leading_blog}
                        <li class="list-item clearfix{if $smarty.foreach.leading_blog.last} last_item{elseif $smarty.foreach.leading_blog.first} first_item{else}{/if}">
                            <div class="blog-image">
                                <a class="products-block-image" title="{$blog.title|escape:'html':'UTF-8'}" href="{$blog.link|escape:'html':'UTF-8'}">
                                    <img alt="{$blog.title|escape:'html':'UTF-8'}" {if isset($aplazyload) && $aplazyload}data-src{else}src{/if}="{$blog.preview_url|escape:'html':'UTF-8'}" class="img-fluid{if isset($aplazyload) && $aplazyload} lazy{/if}">
                                </a>
                            </div>
                            <div class="blog-content">
                            	<h3 class="post-name"><a title="{$blog.title}" href="{$blog.link|escape:'html':'UTF-8'}">{$blog.title}</a></h3>
                            	<span class="info">{$blog.date_add|date_format:"%b %d, %Y"}</span>
                            </div>
                        </li> 
                    {/foreach}
                </ul>
            </div>
    </section>
{/if}

