{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\admin\ap_page_builder_shortcodes\ApModule -->
<div {if !isset($apInfo)}id="default_widget"{/if} class="widget-row clearfix{if isset($apInfo)} {$apInfo.name|escape:'html':'UTF-8'}{if isset($apInfo.icon_class)} widget-icon{/if}{/if}{if isset($formAtts)} {$formAtts.form_id|escape:'html':'UTF-8'}{/if}{if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if}" {if isset($apInfo)}data-type="{$apInfo.name|escape:'html':'UTF-8'}"{/if}>
    <div class="widget-controll-top pull-right">
        <a href="javascript:void(0)" title="{l s='Drag to sort Widget' mod='appagebuilder'}" class="widget-action waction-drag label-tooltip"><i class="icon-move"></i> </a>
        <a href="javascript:void(0)" title="{l s='Disable or Enable Column' mod='appagebuilder'}" class="widget-action btn-status  {if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if} label-tooltip">
            {if isset($formAtts.active) && !$formAtts.active}
                <i class="icon-remove"></i>
            {else}
                <i class="icon-ok"></i>
            {/if}
        </a>
        <a href="javascript:void(0)" title="{l s='Edit Widget' mod='appagebuilder'}" class="widget-action btn-edit label-tooltip" {if isset($apInfo)}data-type="{$apInfo.name|escape:'html':'UTF-8'}"{/if}><i class="icon-pencil"></i></a>
        <a href="javascript:void(0)" title="{l s='Duplicate Widget' mod='appagebuilder'}" class="widget-action btn-duplicate label-tooltip"><i class="icon-paste"></i></a>
        <a href="javascript:void(0)" title="{l s='Delete Column' mod='appagebuilder'}" class="widget-action btn-delete label-tooltip"><i class="icon-trash"></i></a>
    </div>
    <div class="widget-content">
        <img class="w-img" width="16" src="{$moduleDir|escape:'html':'UTF-8'}appagebuilder/logo.gif" title="{l s='Appolo Widget' mod='appagebuilder'}" alt="{l s='Appolo Widget' mod='appagebuilder'}"/>
        <i class="icon w-icon{if isset($apInfo) && isset($apInfo.icon_class)} {$apInfo.icon_class|escape:'html':'UTF-8'}{/if}"></i>
        <a href="javascript:void(0);" title="" class="widget-title">
			{if isset($formAtts.title) && $formAtts.title}{$formAtts.title|rtrim|escape:'html':'UTF-8'} - 
			{elseif isset($formAtts.name_module) && $formAtts.name_module}{$formAtts.name_module|escape:"html"|escape:'html':'UTF-8'} - 
			{/if}
		
			{if isset($apInfo)}{$apInfo.label|escape:'html':'UTF-8'}{/if}</a>
    </div>
</div>