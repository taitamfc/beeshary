{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\admin\ap_page_builder_shortcodes\ApColumn -->
<div {if isset($defaultColumn)}id="default_column" class="column-row plist-element col-sp-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"{else}
class="column-row{foreach $column.form key=ckey item=citem} col-{$ckey}-{$citem}{/foreach} plist-element" data-form='{$column.dataForm}'{/if}>
    <div class="cover-column">
        <div class="pull-left">
            <a href="javascript:void(0)" title="{l s='Edit Column' mod='appagebuilder'}" class="column-action btn-edit-column" data-type="ApColumn" data-for=".column-row"><i class="icon-pencil"></i></a>
        </div>
        <div class="pull-right">
            <a class="plist-eremove"><i class="icon-trash"></i></a>
        </div>
    
		<div class="content row">
			{if !isset($defaultColumn)}
			{foreach $column.sub item=columnsub}
				{if $columnsub.name == 'code'}
					{include file='./code.tpl' code=$columnsub.code}
				{else}
					{include file='./element.tpl' eItem=$columnsub.config configElement=$columnsub.dataForm}
				{/if}
			{/foreach}
			{/if}
		</div>
	</div>
</div>