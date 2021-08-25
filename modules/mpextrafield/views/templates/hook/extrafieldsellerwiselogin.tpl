{**
* 2010-2018 Webkul
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
*  @copyright 2010-2018 Webkul IN
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
		{*<input type="hidden" name="seller_default_lang" value="{$seller_default_lang|escape:'html':'UTF-8'}" id="seller_default_lang">
		<input type="hidden" name="current_lang_id" value="{$current_lang.id_lang|escape:'html':'UTF-8'}" id="current_lang_id">*}
		{if $extrafield.inputtype == 1}
			<div class="required field_label">
				<label class="{if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}{if $extrafield.field_req == 1}<sup class="mand_field">*</sup>{/if}
				{* {include file="$self/../../views/templates/front/_partials/mp-form-fields-flag.tpl"} *}
				</label>
                <div class="row">  
                    {if $allow_multilang && $total_languages > 1}
                        <div class="col-md-9">
                    {else}
                        <div class="col-md-12">
                    {/if}
                        {foreach from=$languages item=language}
                            {assign var="label_name" value="{$extrafield.attribute_name}_`$language.id_lang`"}
                            <input type="text" class="form-control wk_text_field_all
                            wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}" 
                            name="{$label_name|escape:'htmlall':'UTF-8'}" id="" 
                            value="{if isset($smarty.post.{$label_name|escape:'htmlall':'UTF-8'})}{$smarty.post.{$label_name|escape:'htmlall':'UTF-8'}|escape:'htmlall':'UTF-8'}{else if isset($extrafield.default_value.{$language.id_lang}) && $extrafield.asplaceholder eq 0}{$extrafield.default_value.{$language.id_lang}|escape:'htmlall':'UTF-8'}{/if}"{if $extrafield.asplaceholder eq 1}placeholder="{$extrafield.default_value[$language.id_lang]|escape:'htmlall':'UTF-8'}"{/if} {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
                        {/foreach}
                    </div>
                    {if $allow_multilang && $total_languages > 1}
                        <div class="col-md-3">
                            <button type="button" id="" class="btn btn-default dropdown-toggle lang_padding shop_lang" data-toggle="dropdown">
                            {$current_lang.iso_code|escape:'html':'UTF-8'}
                            <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                <li>
                                    <a href="javascript:void(0)" onclick="showExtraLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'});">{$language.name|escape:'html':'UTF-8'}</a>
                                </li>
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                </div>
			</div>
		{/if}
		{if $extrafield.inputtype ==2} 
			<div class="required field_label">
				<label class="{if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}{if $extrafield.field_req == 1}<sup class="mand_field">*</sup>{/if}
				{* {include file="$self/../../views/templates/front/_partials/mp-form-fields-flag.tpl"} *}
				</label>
                <div class="row">  
                    {if $allow_multilang && $total_languages > 1}
                        <div class="col-md-9">
                    {else}
                        <div class="col-md-12">
                    {/if}
                        {foreach from=$languages item=language}
                            {assign var="textarea_name" value="{$extrafield.attribute_name}_`$language.id_lang`"}
                                <textarea name="{$textarea_name|escape:'htmlall':'UTF-8'}" id=""
                                class="form-control wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                                {if $extrafield.asplaceholder eq 1}
                                placeholder="{$extrafield.default_value[$language.id_lang]|escape:'htmlall':'UTF-8'}"{/if}
                                {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>{if isset($smarty.post.{$textarea_name|escape:'htmlall':'UTF-8'})}{$smarty.post.{$textarea_name|escape:'htmlall':'UTF-8'}|escape:'htmlall':'UTF-8'}{elseif !empty($extrafield.default_value[$language.id_lang]) && $extrafield.asplaceholder eq 0}{$extrafield.default_value[$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}</textarea>
                        {/foreach}
                    </div>
                    {if $allow_multilang && $total_languages > 1}
                        <div class="col-md-3">
                            <button type="button" id="" class="btn btn-default dropdown-toggle lang_padding shop_lang" data-toggle="dropdown">
                            {$current_lang.iso_code|escape:'html':'UTF-8'}
                            <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                <li>
                                    <a href="javascript:void(0)" onclick="showExtraLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'});">{$language.name|escape:'html':'UTF-8'}</a>
                                </li>
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                </div>
			</div>
		{/if}
		{if $extrafield.inputtype == 3}
			<div class="field_label">
				<label class="{if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}{if $extrafield.field_req == 1}<sup class="mand_field">*</sup>{/if}
				</label>
				<div class="row">
					<div class="col-xs-4">
						<div class="" id="" style="width: 82px;">
							<select name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}[]{/if}" class="form-control" {if $extrafield.multiple==1}multiple{/if}>
								{foreach $extrafield['extfieldoption'] as $extfieldopt}
								<option value="{$extfieldopt.id|intval|escape:'htmlall':'UTF-8'}" 
								{if isset($smarty.post.{$extrafield.attribute_name})}
									{foreach $smarty.post.{$extrafield.attribute_name} as $key => $smarty_val}
										{if $smarty_val == $extfieldopt.id}
											selected="selected"
										{/if}
									{/foreach}
								{/if}>{$extfieldopt['display_value']|escape:'htmlall':'UTF-8'}
								</option>
								{/foreach}
							</select>
						</div>
					</div>
				</div>
			</div>
		{/if}
		{if $extrafield.inputtype ==4}
			<div class="clearfix field_label">
				<label class="{if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}{if $extrafield.field_req == 1}<sup class="mand_field">*</sup>{/if}
				</label><br>
				{foreach $extrafield['extfieldoption'] as $extfieldopt}
				<div class="checkbox">
					<label for="id_check">
						<input type="checkbox" name="{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}[]" value="{$extfieldopt.id|escape:'html':'UTF-8'}" 
						{if isset($smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'})}
							{foreach $smarty.post.{$extrafield.attribute_name} as $key => $smarty_val}
								{if $smarty_val == $extfieldopt.id} checked="checked"{/if}
							{/foreach}
						{/if}>
						{$extfieldopt.display_value|escape:'html':'UTF-8'}
					</label>
				</div>
				{/foreach}
			</div>
		{/if}
		{if $extrafield.inputtype ==5}
			<div class="required field_label">
				<label class="{if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}{if $extrafield.field_req == 1}<sup class="mand_field">*</sup>{/if}
				</label>
				<input class='extra-file' id="{$extrafield.id|escape:'htmlall':'UTF-8'}" type="file" value="{if $extrafield.file_type=='1'}1{else if $extrafield.file_type=='2'}2{else if $extrafield.file_type=='3'}3{else}0{/if}" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}{/if}">
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
						{l s='Valid extensions are gif,jpg,jpeg,png,zip,pdf,doc' mod='mpextrafield'}
					</p>
				{/if}
			</div>
		{/if}
		{if $extrafield.inputtype == 6}
			<div class="clearfix field_label">
				<label class="{if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}{if $extrafield.field_req == 1}<sup class="mand_field">*</sup>{/if}
				</label><br>
				{foreach $extrafield['extfieldradio'] as $extfieldopt}
					<div class="radio-inline">
						<div><label for="id_radio">
							<input type="radio" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}{/if}" value="{$extfieldopt.id|intval|escape:'htmlall':'UTF-8'}"
							{if isset($smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}) && $extfieldopt.id == $smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}}checked="checked"{/if}>
							{$extfieldopt.display_value|escape:'html':'UTF-8'}
						</label></div>
					</div>
				{/foreach}
			</div>
		{/if}
	{/foreach}
{/if}

<script>
    function showExtraLangField(lang_iso_code, id_lang)
    {
        $('#shop_name_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
        $('.shop_lang').html(lang_iso_code + ' <span class="caret"></span>');

        $('.shop_name_all').hide();
        $('.wk_text_field_all').hide();
        $('.wk_text_field_'+id_lang).show();
        $('#mp_shop_name_'+id_lang).show();
    }
</script>