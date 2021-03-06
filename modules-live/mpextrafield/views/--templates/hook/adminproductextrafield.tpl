{**
* 2010-2017 Webkul
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}
<style type="text/css">
div.radio input
{
	background: none repeat scroll 0 0 rgba(0, 0, 0, 0);
	border: medium none;
	display: inline-block;
	opacity: unset !important;
	text-align: center;
}
</style>
{if isset($extrafielddetail)}
	{foreach $extrafielddetail as $extrafield }
		{if $extrafield.inputtype == 1}
			<div class="required form-group">
				<label class="col-lg-3 control-label {if $extrafield.field_req == 1}required{/if}" for="label_name">
					{$extrafield.label_name|escape:'htmlall':'UTF-8'}
					{include file="$self/../../views/templates/front/_partials/mp-form-fields-flag.tpl"}
				</label>
				<div class="col-lg-6">
					{foreach from=$languages item=language}
						{assign var="label_name" value="{$extrafield.attribute_name}_`$language.id_lang`"}
						<input type="text" class="form-control wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="{$label_name|escape:'htmlall':'UTF-8'}" id=""
						value="{if isset($smarty.post.{$label_name})}{$smarty.post.{$label_name}}{else if isset($extrafield.default_value.{$language.id_lang}) && $extrafield.asplaceholder eq 0}{$extrafield.default_value.{$language.id_lang}|escape:'htmlall':'UTF-8'}{/if}"{if $extrafield.asplaceholder == 1}placeholder="{if isset($extrafield.default_value.{$language.id_lang}) && $extrafield.asplaceholder eq 1}{$extrafield.default_value.{$language.id_lang}|escape:'htmlall':'UTF-8'}{else if isset($extrafield.default_value) && $extrafield.asplaceholder eq 1}{$extrafield.default_value|escape:'htmlall':'UTF-8'}
						{/if}"{/if} {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
					{/foreach}
				</div>
			</div>
		{/if}
		{if $extrafield.inputtype ==2}
			<div class="required form-group">
				<label class="col-lg-3 control-label {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
				{include file="$self/../../views/templates/front/_partials/mp-form-fields-flag.tpl"}
				</label>
				<div class="col-lg-6">
					{foreach from=$languages item=language}
						{assign var="textarea_name" value="{$extrafield.attribute_name}_`$language.id_lang`"}
						<textarea name="{$textarea_name|escape:'htmlall':'UTF-8'}" id="" class="form-control wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}"
						{if $extrafield.asplaceholder eq 1}
						placeholder="{if isset($extrafield.default_value.{$language.id_lang}) && $extrafield.asplaceholder eq 1}{$extrafield.default_value.{$language.id_lang}|escape:'htmlall':'UTF-8'}{else if isset($extrafield.default_value) && $extrafield.asplaceholder eq 1}{$extrafield.default_value|escape:'htmlall':'UTF-8'}{/if}"{/if}
						{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>{if isset($smarty.post.{$textarea_name})}{$smarty.post.{$textarea_name}}{elseif !empty($extrafield.default_value.{$language.id_lang}) && $extrafield.asplaceholder eq 0}{$extrafield.default_value.{$language.id_lang}|escape:'htmlall':'UTF-8'}{/if}</textarea>
					{/foreach}
				</div>
			</div>
		{/if}
		{if $extrafield.inputtype == 3}
		<div class="form-group">
			<label class="control-label col-lg-3 {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
			</label>
			<div class="col-lg-3">
				<select name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}[]{/if}" class="form-control" {if $extrafield.multiple==1}multiple{/if}>
					{foreach $extrafield['extfieldoption'] as $extfieldopt}
						<option value="{$extfieldopt.id|escape:'htmlall':'UTF-8'|intval}"
							{if isset($smarty.post.{$extrafield.attribute_name})}
								{foreach $smarty.post.{$extrafield.attribute_name} as $key => $smarty_val}
									{if $smarty_val == $extfieldopt.id}
										selected="selected"
									{/if}
								{/foreach}
							{/if}>
						{$extfieldopt['display_value']|escape:'htmlall':'UTF-8'}
					</option>
					{/foreach}
				</select>
			</div>
		</div>
		{/if}
		{if $extrafield.inputtype ==4}
		<div class="clearfix form-group">
			<label class="control-label col-lg-3 {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
			</label>
			<div class="col-lg-6">
				{foreach $extrafield['extfieldoption'] as $extfieldopt}
				<div class="checkbox">
					<label for="id_check"> {*{$smarty.post.{$extrafield.attribute_name}|escape:'htmlall':'UTF-8'|@print_r} *}
						<input type="checkbox" name="{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}[]" value="{$extfieldopt.id|escape:'html':'UTF-8'}"
						{if isset($smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'})}
							{foreach $smarty.post.{$extrafield.attribute_name} as $key => $smarty_val}
								{if $smarty_val == $extfieldopt.id}
								checked="checked"{/if}
							{/foreach}{/if} >
							{$extfieldopt.display_value|escape:'html':'UTF-8'}
					</label>
				</div>
				{/foreach}
			</div>
		</div>
		{/if}
		{if $extrafield.inputtype ==5}
		<div class="required form-group">
			<label class="control-label col-lg-3 {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
			</label>
			<div class="col-lg-6">
				<input class="extra-file" id="{$extrafield.id|escape:'htmlall':'UTF-8'}" type="file" value="{if $extrafield.file_type=='1'}1{else if $extrafield.file_type=='2'}2{else if $extrafield.file_type=='3'}3{else}0{/if}" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}{/if}">
				{if $extrafield.file_type=='1'}
				<p class="help-block">
					{l s='Valid image extensions are gif, jpg, jpeg and png' mod='mpextrafield'}
				</p>
				{else if $extrafield.file_type=='2'}
				<p class="help-block">
					{l s='Valid document extensions are doc, zip and pdf' mod='mpextrafield'}
				</p>
				{else}
				<p class="help-block">
					{l s='Valid extensions are gif, jpg, jpeg, png, zip, pdf, doc' mod='mpextrafield'}
				</p>
				{/if}
			</div>
		</div>
		{/if}
		{if $extrafield.inputtype == 6}
		<div class="clearfix form-group">
			<label class="control-label col-lg-3 {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
			</label>
			<div class="col-lg-6">
				<div class="radio-inline">
					<label for="gender1">
						<div><input type="radio" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}{/if}" value="1" {if isset($smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}) && $extfieldopt.id == $smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}}checked="checked"{/if}>
							{foreach $extrafield.extfieldradio as $extfieldrad}
							{$extfieldrad.left_value|escape:'htmlall':'UTF-8'}
							{/foreach}
						</div>
					</label>
				</div>
				<div class="radio-inline">
					<label for="gender2">
						<div><input type="radio" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}{/if}" value="2" {if isset($smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}) && $extfieldopt.id == $smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}}checked="checked"{/if}>
							{foreach $extrafield.extfieldradio as $extfieldrad}
							{$extfieldrad.right_value|escape:'htmlall':'UTF-8'}
							{/foreach}
						</div>
					</label>
				</div>
			</div>
		</div>
		{/if}
	{/foreach}
{/if}
