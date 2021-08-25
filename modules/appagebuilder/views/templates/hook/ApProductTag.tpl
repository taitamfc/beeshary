{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApProductTag.tpl -->
{if !isset($formAtts.accordion_type) || $formAtts.accordion_type == 'full'}{* Default : always full *}
    <div {if isset($formAtts.form_id) && $formAtts.form_id} id="{$formAtts.form_id|escape:'html':'UTF-8' nofilter}"{/if}
        {if isset($formAtts.class)} class="block {$formAtts.class|escape:'html':'UTF-8'}"{/if}>
        {if isset($formAtts.title) && $formAtts.title}
            <h4 class="title_block">{$formAtts.title|escape:'html':'UTF-8' nofilter}</h4>
        {/if}
        {if isset($formAtts.sub_title) && $formAtts.sub_title}
            <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
        {/if}

            <p class="block_content">
        {if isset($formAtts.tags) &&  $formAtts.tags}
            {foreach from=$formAtts.tags item=tag name=myLoop}
                <a href="{$leolink->getPageLink('search', true, NULL, "tag={$tag.name|urlencode}")|escape:'html'}" title="{l s='More about' mod='appagebuilder'} {$tag.name|escape:html:'UTF-8'}" class="{$tag.class} {if $smarty.foreach.myLoop.last}last_item{elseif $smarty.foreach.myLoop.first}first_item{else}item{/if}">{$tag.name|escape:html:'UTF-8'}</a>
            {/foreach}
        {else}
            {l s='No tags have been specified yet.' mod='appagebuilder'}
        {/if}
            </p>
    </div>
{elseif isset($formAtts.accordion_type) && ($formAtts.accordion_type == 'accordion' || $formAtts.accordion_type == 'accordion_small_screen')}{* Case : full or accordion*}
    <div {if isset($formAtts.form_id) && $formAtts.form_id} id="{$formAtts.form_id|escape:'html':'UTF-8' nofilter}"{/if}
        class="{if isset($formAtts.class)}block block-toggler {$formAtts.class|escape:'html':'UTF-8'}{/if}{if $formAtts.accordion_type == 'accordion_small_screen'} accordion_small_screen{/if}">
    {if isset($formAtts.title) && $formAtts.title}
        <div class="title clearfix" data-target="#ApProductTag_{$formAtts.form_id|escape:'html':'UTF-8'}" data-toggle="collapse">
            <h4 class="title_block">
                {$formAtts.title|escape:'html':'UTF-8' nofilter}
            </h4>
            <span class="float-xs-right">
              <span class="navbar-toggler collapse-icons">
                <i class="material-icons add">&#xE313;</i>
                <i class="material-icons remove">&#xE316;</i>
              </span>
            </span>
        </div>
    {/if}
    
    {if isset($formAtts.sub_title) && $formAtts.sub_title}
        <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
    {/if}

        <div class="block_content">
        {if isset($formAtts.tags) &&  $formAtts.tags}
            <ul class="collapse" id="ApProductTag_{$formAtts.form_id|escape:'html':'UTF-8'}">
            {foreach from=$formAtts.tags item=tag name=myLoop}
                <li><a href="{$leolink->getPageLink('search', true, NULL, "tag={$tag.name|urlencode}")|escape:'html'}" title="{l s='More about' mod='appagebuilder'} {$tag.name|escape:html:'UTF-8'}" class="{$tag.class} {if $smarty.foreach.myLoop.last}last_item{elseif $smarty.foreach.myLoop.first}first_item{else}item{/if}">{$tag.name|escape:html:'UTF-8'}</a></li>
            {/foreach}
            </ul>
        {else}
            {l s='No tags have been specified yet.' mod='appagebuilder'}
        {/if}
        </div>
    </div>
    
{/if}