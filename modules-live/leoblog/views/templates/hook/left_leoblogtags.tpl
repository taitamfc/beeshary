{* 
* @Module Name: Leo Blog
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
* @description: Content Management
*}

{if isset($leoblogtags) AND !empty($leoblogtags)}
    <section id="tags_blog_block_left" class="block leo-blog-tags hidden-sm-down">
        <h4 class='title_block'><a href="">{l s='Tags Post' mod='leoblog'}</a></h4>
        <div class="block_content clearfix">
            {foreach from=$leoblogtags item="tag"}
                <a href="{$tag.link}">{$tag.name}</a>
            {/foreach}
        </div>
    </section>
{/if}