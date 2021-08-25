{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<div class="widget-row col-md-12 {$eItem.file} plist-element" data-element='{$eItem.file}'{if isset($configElement)} data-form='{$configElement}'{/if}><a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag me' mod='appagebuilder'}" class="group-action gaction-drag label-tooltip"><i class="icon-move"></i> </a>
    <a class="show-postion" href="#postion_layout" title="Click to see postion">
    {$eItem.name|escape:'html':'UTF-8'}
    </a>
    {if isset($eItem.icon)}<i class="{$eItem.icon}"></i>{/if}
    <div class="pull-right">
        {if isset($eItem.config)}
        <a href="javascript:void(0)" data-config="{$eItem.config}" title="{l s='Config element' mod='appagebuilder'}" class="element-config" data-type="ApColumn" data-for=".column-row"><i class="icon-pencil"></i></a>
        {/if}
        <a class="plist-eremove"><i class="icon-trash"></i></a>
        <a class="plist-eedit" data-element='{$eItem.file}'><i class="icon-edit"></i></a>
    </div>
</div>