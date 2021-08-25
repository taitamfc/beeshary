{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApAlert -->
<div id="alert-{$formAtts.form_id|escape:'html':'UTF-8'}" class="block">
	{($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
	{if isset($formAtts.title) && !empty($formAtts.title)}
	<h4 class="title_block">
		{$formAtts.title|rtrim|escape:'html':'UTF-8'}
	</h4>
	{/if}
    {if isset($formAtts.sub_title) && $formAtts.sub_title}
        <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
    {/if}
	<div class="alert {$formAtts.alert_type|escape:'html':'UTF-8'}">
	{if isset($formAtts.content_html)}
		{$formAtts.content_html nofilter}{* HTML form , no escape necessary *}
	{/if}
	</div>
	{($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
</div>