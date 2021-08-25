{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

{function name=menu_cat_img level=0}
  <ul class="level{$level}{if $level != 0} dropdown-sub dropdown-menu{/if}">
  {foreach $data as $category}
    {if isset($category.children) && is_array($category.children)}
      <li class="cate_{$category.id_category}" ><a href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)|escape:'html':'UTF-8'}"><span {if {$category.id_category} == {$id_root}} style="display:none"{/if}>{$category.name}{if isset($category.image)}<span {if  {$showicons} == 0 || ({$level} gt 0 && {$showicons} == 2)} style="display:none"{/if}><img height = '20px' {if isset($aplazyload) && $aplazyload}class="lazy" data-src{else}src{/if}='{$category["image"]}' alt='{$category["name"]}'></span>{/if}</span></a>
                <b class="caret"{if {$category.id_category} == {$id_root}} style="display:none"{/if}></b>
        {menu_cat_img data=$category.children level=$level+1}</li>
    {else}
      <li class="cate_{$category.id_category}"><a href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)|escape:'html':'UTF-8'}">{$category.name}{if isset($category.image)}<span {if {$showicons} == 0 || ({$level} gt 0 && {$showicons} == 2)} style="display:none"{/if}><img height = '10px' {if isset($aplazyload) && $aplazyload}class="lazy" data-src{else}src{/if}='{$category["image"]}' alt='{$category["name"]}'></span>{/if}</a></li>
    {/if}
  {/foreach}
  </ul>
{/function}

<div class="leo-widget" data-id_widget="{$id_widget}">
    {if isset($categories)}
    {*
    <div class="widget-category_image block widget-category_image_{$level}">
    *}
    <div class="widget-category_image block widget-category_image_{if $cate_depth}{$cate_depth}{else}0{/if}" data-level="{if $cate_depth}{$cate_depth}{else}0{/if}" data-limit="{if $limit}{$limit}{else}5{/if}">

            {if isset($widget_heading)&&!empty($widget_heading)}
            <h4 class="menu-title">
                    {$widget_heading}
            </h4>
            {/if}
            <div class="block_content">
        {foreach from = $categories key=key item=cate}
                    {menu_cat_img data=$cate}
        {/foreach}
        <div id="view_all_wapper" style="display:none">
            <span class ="view_all"><a href="javascript:void(0)">{l s='View all' mod='leobootstrapmenu'}</a></span>
        </div> 
            </div>
    </div>
    {/if}
    <script type="text/javascript">
            {literal}
            if ( typeof live_editor !== 'undefined' && live_editor)
            {
                    var limit = {/literal}{if $limit}{$limit}{else}5{/if}{literal};
                    var level = {/literal}{if $cate_depth}{$cate_depth}{else}0{/if}{literal};		
            }else
            {		
                    list_menu_tmp.limit = {/literal}{if $limit}{$limit}{else}5{/if}{literal};
                    list_menu_tmp.level = {/literal}{if $cate_depth}{$cate_depth}{else}0{/if}{literal};		
            }
            {/literal}
    </script>
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