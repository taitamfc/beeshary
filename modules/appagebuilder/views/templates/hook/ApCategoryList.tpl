{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file /modules/appagebuilder/views/templates/hook/ApCategoryList -->
<div id="ap-category-list-{$formAtts.form_id|escape:'html':'UTF-8'}" class="block">
<div class="wk_catg_list">
    <ul class="wk_catg_list_ul">
        {foreach from=$seller_categories item=category}
            <li>
                <a class="wk-collection-category cat-{$category->id_category}" data-catid="{$category->id_category}" href="{$link->getCategoryLink($category->id_category, $category->link_rewrite)|escape:'html':'UTF-8'}">
                    <span>{$category->name}</span>
                </a>
            </li>
        {/foreach}
    </ul>
</div>
{($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
</div>