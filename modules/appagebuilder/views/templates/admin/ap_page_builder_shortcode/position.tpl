{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\admin\ap_page_builder_home\position -->
<div class="position-area">
	<div class="hook-wrapper apshortcode {if isset($data_shortcode_content.apshortcode.class)}{$data_shortcode_content.apshortcode.class|escape:'html':'UTF-8'}{/if}" data-hook="apshortcode">
		<div class="hook-top">
			<div class="pull-left hook-desc"></div>
			<div class="hook-info text-center">
				<a href="javascript:;" tabindex="0" class="open-group label-tooltip" title="{l s='Expand Hook' mod='appagebuilder'}" id="apshortcode" name="apshortcode">
					{l s='Shortcode Content' mod='appagebuilder'} <i class="icon-circle-arrow-down"></i>
				</a>
			</div>
		</div>
		<div class="hook-content">
			{if isset($data_shortcode_content.apshortcode.content)}
			{$data_shortcode_content.apshortcode.content}{* HTML form , no escape necessary *}
			{/if}
			<div class="hook-content-footer text-center">
				<a href="javascript:void(0)" tabindex="0" class="btn-new-widget-group" title="{l s='Add Widget in new Group' mod='appagebuilder'}" data-container="body" data-toggle="popover" data-placement="top" data-trigger="focus">
					<i class="icon-plus"></i>
				</a>
			</div>
		</div>
	</div>
</div>
