{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApColumn -->
<div{if isset($formAtts.id) && $formAtts.id} id="{$formAtts.id|escape:'html':'UTF-8'}"{/if}
    class="{(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'} {(isset($formAtts.animation) && $formAtts.animation != 'none'|escape:'html':'UTF-8') ? 'has-animation' : ''}"
	{if isset($formAtts.animation) && $formAtts.animation != 'none'} data-animation="{$formAtts.animation|escape:'html':'UTF-8'}"
	{if isset($formAtts.animation_delay) && $formAtts.animation_delay != ''} data-animation-delay="{$formAtts.animation_delay|escape:'html':'UTF-8'}" {/if}
	{if isset($formAtts.animation_duration) && $formAtts.animation_duration != ''} data-animation-duration="{$formAtts.animation_duration|escape:'html':'UTF-8'}" {/if}
	{if isset($formAtts.animation_iteration_count) && $formAtts.animation_iteration_count != ''} data-animation-iteration-count="{$formAtts.animation_iteration_count|escape:'html':'UTF-8'}" {/if}
	{if isset($formAtts.animation_infinite) && $formAtts.animation_infinite != ''} data-animation-infinite="{$formAtts.animation_infinite|escape:'html':'UTF-8'}" {/if}
	{/if}
    >
    {if isset($formAtts.title) && $formAtts.title}
    <h4 class="title_block title-ap-column">{$formAtts.title nofilter}{* HTML form , no escape necessary *}</h4>
    {/if}
    {if isset($formAtts.sub_title) && $formAtts.sub_title}
        <div class="sub-title-widget sub-title-ap-column">{$formAtts.sub_title nofilter}</div>
    {/if}
    {if isset($formAtts.content_html)}
        {$formAtts.content_html|escape:'html':'UTF-8' nofilter}
    {else}
        {$apContent nofilter}
    {/if}
</div>