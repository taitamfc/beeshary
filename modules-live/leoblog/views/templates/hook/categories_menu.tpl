{* 
* @Module Name: Leo Blog
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
* @description: Content Management
*}

<!-- Block categories module -->
    {if $tree}
    <div id="categories_blog_menu" class="block blog-menu">
      <h4 class="title_block">{if isset($currentCategory)}{$currentCategory->title|escape:'html':'UTF-8'}{else}{l s='Blog Categories' mod='leoblog'}{/if}</h4>
        <div class="block_content">
            {$tree nofilter}{* HTML form , no escape necessary *}
        </div>
    </div>
    {/if}
    <!-- /Block categories module -->
