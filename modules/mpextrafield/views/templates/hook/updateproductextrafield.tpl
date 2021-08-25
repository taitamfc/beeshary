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
{assign var=temp value=0}
{if isset($extrafielddetail)}
	{foreach $extrafielddetail as $extrafield}
		{if $extrafield.inputtype == 1}
			<div class="required form-group">
				<label class="control-label {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
				{block name='mp-form-fields-flag'}
					{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
				{/block}
				</label>
				{if $extrafield.attribute_name == "unproverbe"}
					<select class="form-control" name="unproverbe_1">
						{foreach $get_proverbs as $get_proverb}
							<option value="{$get_proverb|escape:'html':'UTF-8'}"{if isset($extrafield.default_value) && $extrafield.default_value|is_array}{if $extrafield.default_value.{$language.id_lang} == $get_proverb} selected{/if}{else}{if $extrafield.default_value == $get_proverb} selected{/if}{/if}>{$get_proverb|escape:'html':'UTF-8'}</option>
						{/foreach}
					</select>
					<input type="hidden" name="unproverbe_2" value="{if isset($extrafield.default_value) && $extrafield.default_value|is_array}{$extrafield.default_value.{$language.id_lang}|escape:'htmlall':'UTF-8'}{else}{$extrafield.default_value|escape:'htmlall':'UTF-8'}{/if}" />
				{elseif $extrafield.attribute_name == "labels"}
					<select class="form-control has-chosen" style="overflow: hidden;" id="labels" name="labels[]" multiple data-placeholder="{l s='My labels and certificates' mod='mpsellerwiselogin'}*" >
					{foreach from=$badges item=label}
						<option value="{$label.id|intval}" {if in_array($label.id,$seller_badge_ids) }selected{/if}>{$label.badge_name|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
					</select>
				{else}
					{foreach from=$languages item=language}
						{assign var="label_name" value="{$extrafield.attribute_name}_`$language.id_lang`"}
					<input type="text" class="form-control wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="{$label_name|escape:'htmlall':'UTF-8'}" id="" 
					value="{strip}{if isset($smarty.post.{$label_name})}{$smarty.post.{$label_name}}
					{else if isset($extrafield.default_value) && $extrafield.asplaceholder eq 0}
					{if is_array($extrafield.default_value)}
					  {$extrafield.default_value.{$language.id_lang}|escape:'htmlall':'UTF-8'}
					{else}
					  {$extrafield.default_value|escape:'htmlall':'UTF-8'}
					{/if}{/if}{strip}" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
					{/foreach}
				{/if}
			</div>
		{elseif $extrafield.inputtype ==2}
			<div class="required form-group">
				<label class="control-label {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
				{block name='mp-form-fields-flag'}
					{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
				{/block}
				</label>
				{foreach from=$languages item=language}
				{assign var="textarea_name" value="{$extrafield.attribute_name}_`$language.id_lang`"}
				<textarea name="{$textarea_name}" id="" class="form-control textarea_field_all text_field_{$language.id_lang}" {if $extrafield.asplaceholder eq 1}placeholder="{if $extrafield.asplaceholder eq 1}{if isset($extrafield.default_value) && $extrafield.default_value|is_array}{$extrafield.default_value.{$language.id_lang}|escape:'htmlall':'UTF-8'}{else}{$extrafield.default_value|escape:'htmlall':'UTF-8'}{/if}{/if}"{/if} {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>{if isset($smarty.post.{$textarea_name})}{$smarty.post.{$textarea_name}}
				{elseif $extrafield.asplaceholder eq 0}{if isset($extrafield.default_value) && $extrafield.default_value|is_array}{$extrafield.default_value.{$language.id_lang}|escape:'htmlall':'UTF-8'}{else}{$extrafield.default_value|escape:'htmlall':'UTF-8'}{/if}{/if}</textarea>
				{/foreach}
			</div>
		{elseif $extrafield.inputtype == 3 && $extrafield.attribute_name != 'spoken_langs'}
		<div class="form-group">
			<label class="control-label {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
			</label>
			<div class="row">
				<div class="col-xs-4">
					<div>
						{assign var="mpattribute_name" value="{$extrafield.attribute_name}"}
						<select name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}[]{/if}" class="form-control extra-select" {if $extrafield.multiple==1}multiple{/if}>
							{if $extrafield.multiple != 1}
							{foreach $extrafield['extfielddrop'] as $extfieldopt}
							<option value="{$extfieldopt.id|intval}"
								{if isset($smarty.post.$mpattribute_name.0) && $extfieldopt.id == $smarty.post.$mpattribute_name.0}
								selected="selected"
								{else if $extfieldopt.selected_value == $extfieldopt.id}
								selected="selected"{/if}>
								{$extfieldopt.display_value|escape:'htmlall':'UTF-8'}
							</option>
							{/foreach}
							{else if $extrafield.multiple == 1}
							{foreach $extrafield['extfielddrop'] as $extfieldopt}
							{assign var=select value=","|explode:$extfieldopt.selected_value}
							<option
								value="{$extfieldopt.id|intval}"
								{if isset($smarty.post.$mpattribute_name.0) && $extfieldopt.id == $smarty.post.$mpattribute_name.0}selected="selected"
								{else if in_array($extfieldopt.id, $select)}selected="selected"{/if}>
								{$extfieldopt.display_value|escape:'htmlall':'UTF-8'}
							</option>
							{/foreach}
							{/if}
						</select>
					</div>
				</div>
			</div>
		</div>
		{elseif $extrafield.inputtype ==4}
		<div class="clearfix form-group">
			<label class="control-label {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
			</label><br>
			{foreach $extrafield['extfieldcheck'] as $extfieldopt}
			<div class="checkbox">
				<label for="id_check">
					{assign var=check value=","|explode:$extfieldopt.selected_value}
					<input type="checkbox" class="extra-checkbox" name="{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}[]" value="{$extfieldopt.id|escape:'html':'UTF-8'}"
					{if isset($smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'})}
					{foreach $smarty.post.{$extrafield.attribute_name} as $chkval}
					{if $chkval == $extfieldopt.id}checked="checked"{/if}
					{/foreach}
					{else}
					{foreach $check as $chk}
					{if $chk == $extfieldopt.id}checked="checked"{/if}
					{/foreach}
					{/if}>
					{$extfieldopt.display_value|escape:'html':'UTF-8'}
				</label>
			</div>
			{/foreach}
		</div>
		{elseif $extrafield.inputtype ==5}
		<div class="required form-group" style="margin-top:10px;">
			<label class="control-label {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
			</label>
			{if isset($extrafield.default_value) && $extrafield.value_id && $extrafield.default_value != '0'}
			<div>
				<a href="{$link->getModuleLink('mpextrafield', 'mediadownload',['id_attachment' => $extrafield['value_id']])|escape:'html':'UTF-8'}" class="btn btn-primary"><i class="material-icons">&#xE2C4;</i> {l s='Download file' mod='mpextrafield'}</a>
				{if isset($controller) && $controller == 'updateproduct'}
				<a href="{$link->getModuleLink('mpextrafield', 'mediadownload',['product' => $id, 'id_delete' => $extrafield['value_id']])|escape:'html':'UTF-8'}" class="btn btn-primary"><i class="material-icons">&#xE2C4;</i> {l s='Remove file' mod='mpextrafield'}</a>
				{else}
				<a href="{$link->getModuleLink('mpextrafield', 'mediadownload',['profile' => $id,'id_delete' => $extrafield['value_id']])|escape:'html':'UTF-8'}" class="btn btn-primary"><i class="material-icons">&#xE2C4;</i> {l s='Remove file' mod='mpextrafield'}</a>
				{/if}
				<input type="hidden" value="{$extrafield.id|escape:'htmlall':'UTF-8'}" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}{/if}">
			</div>
			{else}
			<input class="extra-file filestyle" id="{$extrafield.id|escape:'htmlall':'UTF-8'}" type="file" value="{if $extrafield.file_type=='1'}1{else if $extrafield.file_type=='2'}2{else if $extrafield.file_type=='3'}3{else}0{/if}" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}{/if}">
			{if $extrafield.file_type=='1'}
			<p class="help-block">
				{l s='Valid image extensions are gif, jpg, jpeg and png' mod='mpextrafield'}
			</p>
			{else if $extrafield.file_type=='2'}
			<p class="help-block">
				{l s='Valid document extensions are doc,zip and pdf' mod='mpextrafield'}
			</p>
			{else}
			<p class="help-block">
				{l s='Valid extensions are gif, jpg, jpeg, png, zip, pdf, doc' mod='mpextrafield'}
			</p>
			{/if}
			{/if}
		</div>
		{elseif $extrafield.inputtype == 6}
		<div class="clearfix form-group">
			<label class="control-label {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
			</label><br>
			<div class="radio-inline">
				<label for="gender1">
					<div><input class="extra-radio" type="radio" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}{/if}" value="1" {if isset($smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}) && 1 == $smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}}checked="checked"{else if $extrafield.selected_value == 1}checked="checked"{/if}>
						{foreach $extrafield.extfieldradio as $extfieldrad}
						{$extfieldrad.left_value|escape:'htmlall':'UTF-8'}
						{/foreach}
					</div>
				</label>
			</div>
			<div class="radio-inline">
				<label for="gender2">
					<div><input class="extra-radio" type="radio" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}{/if}" value="2" {if isset($smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}) && 2 == $smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}}checked="checked"{else if $extrafield.selected_value == 2}checked="checked"{/if}>
						{foreach $extrafield.extfieldradio as $extfieldrad}
						{$extfieldrad.right_value|escape:'htmlall':'UTF-8'}
						{/foreach}
					</div>
				</label>
			</div>
		</div>
		{/if}
		{assign var=temp value=$temp+1}
	{/foreach}
{/if}