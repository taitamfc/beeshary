{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApRow -->
{if isset($formAtts.container) && $formAtts.container}
<div class="wrapper" {if isset($formAtts.bg_config) && $formAtts.bg_config == "fullwidth" && isset($formAtts.bg_data) && $formAtts.bg_data}style="background:{$formAtts.bg_data}{*contain link can not escape*}"{/if}
     {if isset($formAtts.parallax) && $formAtts.parallax}{$formAtts.parallax}{*contain img link, can not escape*}{/if}>
	 {if isset($formAtts.bg_config) && $formAtts.bg_config == "fullwidth"}
        {$formAtts.bg_data=""}
        {$formAtts.parallax=""}
    {/if}
<div class="{$formAtts.container|escape:'html':'UTF-8'}">
{/if}
    <div{if isset($formAtts.id) && $formAtts.id} id="{$formAtts.id|escape:'html':'UTF-8'}"{/if}
        class="{(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'} {(isset($formAtts.animation) && $formAtts.animation != 'none') ? ' has-animation' : ''} {$formAtts.bg_class}{*contain link can not escape*}"
	{if isset($formAtts.animation) && $formAtts.animation != 'none'} data-animation="{$formAtts.animation|escape:'html':'UTF-8'}" {if isset($formAtts.animation_delay) && $formAtts.animation_delay != ''} data-animation-delay="{$formAtts.animation_delay|escape:'html':'UTF-8'}" {/if}{if isset($formAtts.animation_duration) && $formAtts.animation_duration != ''} data-animation-duration="{$formAtts.animation_duration|escape:'html':'UTF-8'}" {/if}{if isset($formAtts.animation_iteration_count) && $formAtts.animation_iteration_count != ''} data-animation-iteration-count="{$formAtts.animation_iteration_count|escape:'html':'UTF-8'}" {/if}{if isset($formAtts.animation_infinite) && $formAtts.animation_infinite != ''} data-animation-infinite="{$formAtts.animation_infinite|escape:'html':'UTF-8'}" {/if}{/if}
        {if isset($formAtts.bg_data) && $formAtts.bg_data}data-bg="{$formAtts.bg_data nofilter}{* HTML form , no escape necessary *}"{/if}
        {if isset($formAtts.parallax) && $formAtts.parallax}{$formAtts.parallax nofilter}{* HTML form , no escape necessary *}{/if}
        {if isset($formAtts.css_style) && $formAtts.css_style}{$formAtts.css_style nofilter}{* HTML form , no escape necessary *}{/if}
        >
        {$formAtts.bg_video nofilter}{* HTML form , no escape necessary *}
        {if isset($formAtts.title) && $formAtts.title}
        <h4 class="title_block title-ap-group">{$formAtts.title nofilter}{* HTML form , no escape necessary *}</h4>
        {/if}
        {if isset($formAtts.sub_title) && $formAtts.sub_title}
            <div class="sub-title-widget sub-title-ap-group">{$formAtts.sub_title nofilter}</div>
        {/if}
        {if isset($formAtts.content_html)}
            {$formAtts.content_html nofilter}{* HTML form , no escape necessary *}
        {else}
            {$apContent nofilter}{* HTML form , no escape necessary *}
        {/if}
    </div>
{if isset($formAtts.container) && $formAtts.container}
</div>
</div>
{/if}
{if isset($leoConfiguration) && $leoConfiguration->get('APPAGEBUILDER_LOAD_STELLAR')}
    {if isset($formAtts.parallax) && $formAtts.parallax}
    {literal}
    <script>
        ap_list_functions.push(function(){
            $.stellar({horizontalScrolling:false}); 
        });
    </script>
    {/literal}
    {/if}
{/if}