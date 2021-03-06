{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
 <!-- @file modules\appagebuilder\views\templates\hook\ApVideo -->
 <div id="video-{$formAtts.form_id|escape:'html':'UTF-8'}" class="video" style="clear:both;">
	{($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
	{if isset($formAtts.title) && !empty($formAtts.title)}
	<h4 class="title_block">
		{$formAtts.title|escape:'html':'UTF-8'}
	</h4>
	{/if}
    {if isset($formAtts.sub_title) && $formAtts.sub_title}
        <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
    {/if}
	<div style="text-align:{$formAtts.align|escape:'html':'UTF-8'}">
		{(isset($formAtts.content_html)) ? $formAtts.content_html : '' nofilter}{* HTML form , no escape necessary *}
	</div>
	{($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
</div>