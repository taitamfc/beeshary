{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApGeneral -->
<div{if isset($formAtts.id) && $formAtts.id} id="{$formAtts.id|escape:'html':'UTF-8' nofilter}"{/if}
{if !isset($formAtts.accordion_type) || $formAtts.accordion_type == 'full'}{* Default : always full *}
    {if isset($formAtts.class)} class="block {$formAtts.class|escape:'html':'UTF-8'}"{/if}>
	{($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
    {if isset($formAtts.title) && $formAtts.title}
        <h4 class="title_block">{$formAtts.title|escape:'html':'UTF-8' nofilter}</h4>
    {/if}
    {if isset($formAtts.sub_title) && $formAtts.sub_title}
        <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
    {/if}
    {if isset($formAtts.content_html)}
        <div class="block_content">{$formAtts.content_html nofilter}{* HTML form , no escape necessary *}</div>
    {else}
        {$apContent nofilter}{* HTML form , no escape necessary *}
    {/if}
	{($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
{elseif isset($formAtts.accordion_type) && ($formAtts.accordion_type == 'accordion' || $formAtts.accordion_type == 'accordion_small_screen')}{* Case : full or accordion*}
    {if isset($formAtts.class)} class="block block-toggler {$formAtts.class|escape:'html':'UTF-8'}{if $formAtts.accordion_type == 'accordion_small_screen'} accordion_small_screen{/if}"{/if}>
	{($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
    {if isset($formAtts.title) && $formAtts.title}
    <div class="title clearfix" data-target="#footer-html-{$formAtts.form_id|escape:'html':'UTF-8'}" data-toggle="collapse">
        <h4 class="title_block">{$formAtts.title|escape:'html':'UTF-8' nofilter}</h4>
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
    <div class="collapse block_content" id="footer-html-{$formAtts.form_id|escape:'html':'UTF-8'}">
    {if isset($formAtts.content_html)}
        {$formAtts.content_html nofilter}{* HTML form , no escape necessary *}
    {else}
        {$apContent nofilter}{* HTML form , no escape necessary *}
    {/if}
    </div>
	{($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}    
{/if}
</div>