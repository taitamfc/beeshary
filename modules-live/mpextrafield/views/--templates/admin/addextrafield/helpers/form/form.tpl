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

{block name="other_fieldsets"}
{*{$custom_field|escape:'htmlall':'UTF-8'|@debug_print_var} *}
<div class="panel">
	 <fieldset>
		{if $set==1}
			<legend>{l s='Add Extra Field' mod='mpextrafield'}</legend>
		{else}
			<legend>{l s='Update Extra Field' mod='mpextrafield'}</legend>
		{/if}
    <form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'}  form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'htmlall':'UTF-8'}"{/if}>
	{if isset($custom_field.id) }
		<input type="hidden" name="id" id="id" value="{$custom_field.id|intval}">
	{/if}
	<input type="hidden" name="set" id="set" value="{$set|escape:'htmlall':'UTF-8'}" />
	<div class="form_registration">
		<div class="form-group">
			<label class="col-lg-3 control-label required">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="Select page where you want to display extra field required">
				{l s='Choose Page' mod='mpextrafield'}
				</span>
			</label>
			<div class="col-lg-6">
				<select id="page_info" name="page" class="form-control">
					<option value="1" {if isset($smarty.post.page) && $smarty.post.page == 1}selected{else if isset($custom_field.id) && $custom_field.page==1}selected{/if}>
					{l s='Add/update Product' mod='mpextrafield'}
					</option>
					<option value="2" {if isset($smarty.post.page) && $smarty.post.page == 2}selected{else if isset($custom_field.id) && $custom_field.page==2}selected{/if}>{l s='Add/update shop' mod='mpextrafield'}</option>					
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-3 control-label required">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="Select input type">
					{l s='Choose Type' mod='mpextrafield'}
				</span>
			</label>
			<div class="col-lg-6">
			{if isset($custom_field.id)}
				<input type="hidden" id="inputtype" name="inputtype" value="{if $custom_field.inputtype ==1}1{elseif $custom_field.inputtype ==2}2{elseif $custom_field.inputtype ==3}3{elseif $custom_field.inputtype ==4}4{elseif $custom_field.inputtype ==5}5{elseif $custom_field.inputtype ==6}6{/if}">
				<input type="text" value="{if $custom_field.inputtype ==1}Text{elseif $custom_field.inputtype ==2} Textarea{elseif $custom_field.inputtype ==3}Dropdown{elseif $custom_field.inputtype ==4}Checkbox{elseif $custom_field.inputtype ==5}File{elseif $custom_field.inputtype ==6}Radiobutton{/if}" disabled="">
				{else}
				<select id="inputtype" name="inputtype" class="from-control">
					{if $extrafieldinputtype!=-1}
						{foreach $extrafieldinputtype as $extrafit}
							<option value="{$extrafit['id']|escape:'htmlall':'UTF-8'}" {if isset($smarty.post.inputtype) && $smarty.post.inputtype == $extrafit['id']}selected{/if}>{$extrafit['inputtype_name']|escape:'htmlall':'UTF-8'}
							</option>
						{/foreach}
					{/if}
				</select>
			{/if}
			</div>
		</div>
		<div id="charlimit" class="form-group" {if !isset($custom_field.id) || $custom_field.inputtype!=1 || $custom_field.inputtype!=2 }style="display:none;"{/if}>
				<label class="col-lg-3 control-label required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="Define character limit for input field">
						{l s='Character Limit' mod='mpextrafield'}
					</span>
				</label>
				<div class="col-lg-6">
					<input type="number" value="{if isset($smarty.post.mp_char_limit)}{$smarty.post.mp_char_limit|escape:'htmlall':'UTF-8'}{else if isset($custom_field.char_limit)}{$custom_field.char_limit|escape:'htmlall':'UTF-8'}{/if}" name="mp_char_limit" class="from-control">
					<span id="char250" class="help-block" style="display:none;">
						{l s='Maximum character limit 250' mod='mpextrafield'}
					</span>
					<span id="char1000" class="help-block" style="display:none;">
						{l s='Maximum character limit 1000' mod='mpextrafield'}
					</span>
				</div>
			</div>
		<div id="validationtype" class="form-group" {if !isset($custom_field.id) || $custom_field.inputtype!=1 }style="display:none;"{/if}>
				<label class="col-lg-3 control-label required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="Select validate type eg-email">
						{l s='Validation type' mod='mpextrafield'}
					</span>
				</label>
				<div class="col-lg-6">
					<select id="validationtype" name="validationtype" class="form-control">
						{if isset($extrafieldinputtypevalidation)}
							{foreach $extrafieldinputtypevalidation as $input_validation}
								<option value="{$input_validation.validation_id|escape:'htmlall':'UTF-8'}" {if isset($smarty.post.validationtype) && $smarty.post.validationtype == $input_validation.validation_id}selected{else if isset($custom_field.id) && $custom_field.validation_type==$input_validation.validation_id}selected{/if}>{$input_validation.validation_type|escape:'htmlall':'UTF-8'}
								</option>
							{/foreach}
						{/if}
					</select>
				</div>
			</div>			
		<div id="req_field" class="form-group" {if !isset($custom_field.id) }style="display:none;"{/if}>
			<label class="col-lg-3 control-label required">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="Specify field required">
					{l s='Field Required' mod='mpextrafield'}
				</span>
			</label>
			<div class="col-lg-6 ">
				<span class="switch prestashop-switch fixed-width-lg">
					<input name="field_req" id="active_on" value="1" type="radio" {if isset($smarty.post.field_req) && $smarty.post.field_req == 1}checked="checked"{else if isset($custom_field.id) && $custom_field.field_req=='1'}checked="checked"{/if}>
					<label for="active_on">{l s='Yes' mod='mpextrafield'}</label>
					{if isset($smarty.post.field_req)}
					<input name="field_req" id="active_off" value="0" type="radio" {if isset($smarty.post.field_req) && $smarty.post.field_req == 0}checked="checked"{/if}>
					{else}
					<input name="field_req" id="active_off" value="0" type="radio" {if !isset($custom_field.id)}checked="checked"{else if isset($custom_field.id) && $custom_field.field_req=='0'}checked="checked"{/if}>
					{/if}
					<label for="active_off">{l s='No' mod='mpextrafield'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		<div id="radio_info" {if !isset($custom_field.id) || $custom_field.inputtype!=3}style="display:none;"{/if}>
			<div class="form-group">
				<label class="col-lg-3 control-label">
					<b>{l s='Radio button info' mod='mpextrafield'}</b>
				</label>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="radio values">
						{l s='Radio values' mod='mpextrafield'}
					</span>
				</label>
				<div class="row">
					<div class="col-lg-3">
						{foreach from=$languages item=language}						
							{assign var="radio1_name" value="radio_left_value_`$language.id_lang`"}
							<input type="text"
							value="{if isset($smarty.post.{$radio1_name})}{$smarty.post.{$radio1_name}|escape:'htmlall':'UTF-8'}{else if isset($custom_field_values.left_value.{$language.id_lang})}{$custom_field_values.left_value.{$language.id_lang}|escape:'htmlall':'UTF-8'}{/if}" name="{$radio1_name|escape:'htmlall':'UTF-8'}"
							class="form-control radio1_value_all radio1_value_{$language.id_lang|escape:'html':'UTF-8'}"
							placeholder="{l s='ex:male' mod='mpextrafield'}"
							{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
						{/foreach}
					</div>
					{if $allow_multilang && $total_languages > 1}
						<div class="col-lg-1">
							<button type="button" class="btn btn-default dropdown-toggle radio1_value_lang_btn" data-toggle="dropdown">
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

					<div class="col-lg-3">
						{foreach from=$languages item=language}						
							{assign var="radio2_name" value="radio_right_value_`$language.id_lang`"}
							<input type="text"
							value="{if isset($smarty.post.{$radio2_name})}{$smarty.post.{$radio2_name}|escape:'htmlall':'UTF-8'}{else if isset($custom_field_values.right_value.{$language.id_lang})}{$custom_field_values.right_value.{$language.id_lang}|escape:'htmlall':'UTF-8'}{/if}" name="{$radio2_name|escape:'htmlall':'UTF-8'}"
							class="form-control radio2_value_all radio2_value_{$language.id_lang|escape:'html':'UTF-8'}"
							placeholder="{l s='ex:female' mod='mpextrafield'}"
							{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
						{/foreach}
					</div>
					{if $allow_multilang && $total_languages > 1}
						<div class="col-lg-1">
							<button type="button" class="btn btn-default dropdown-toggle radio2_value_lang_btn" data-toggle="dropdown">
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
		</div>
		{if !isset($custom_multiple_field_values) && !isset($custom_field.id)}
		<input type="hidden" name="max_options" id="max_options" value="1">
		<input type="hidden" name="count_options" id="count_options" value="0">
		<div class="dropdown_label_info" id="dropdown_label_info" style="display: none;">
			<div class="form-group">
				<label class="col-lg-3 control-label required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="values will show in dropdown list">
						{l s='Dropdown Display Value' mod='mpextrafield'}
					</span>
				</label>

				<div class="row">
					<div class="col-lg-3">
						{foreach from=$languages item=language}						
							{assign var="deflt_name" value="default_value_`$language.id_lang`"}
							<input type="text" value=""
							name="display_value_1_{$language.id_lang|escape:'html':'UTF-8'}"
							class="form-control dropdown_value_all dropdown_value_{$language.id_lang|escape:'html':'UTF-8'}"
							placeholder="{l s='display value' mod='mpextrafield'}"
							{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
						{/foreach}
						<div class="help-block">{l s='Display In Drop Down' mod='mpextrafield'}</div>
					</div>
					{if $allow_multilang && $total_languages > 1}
						<div class="col-lg-1">
							<button type="button" class="btn btn-default dropdown-toggle dropdown_value_lang_btn" data-toggle="dropdown">
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
					<div class="col-lg-1">
						<a href="" id="add_another_dropdownvalue" class="btn btn-default">{l s='Add DropDown option' mod='mpextrafield'}</a>
					</div>
				</div>
			</div>
		</div>
		{/if}
		{if !isset($custom_multiple_field_values) && !isset($custom_field.id)}
		<div class="check_info" id="check_info" {if !isset($custom_field.id) || $custom_field.inputtype!=4}style="display:none;"{/if}>
			<div class="nextCheckBox">
			<div class="form-group">
				<label class="col-lg-3 control-label required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="checkbox values">
						{l s='Check box values' mod='mpextrafield'}
					</span>
				</label>

				<div class="row">
					<div class="col-lg-3">
						{foreach from=$languages item=language}						
							{assign var="deflt_name" value="default_value_`$language.id_lang`"}
							<input type="text" value=""
							name="mp_check_val_1_{$language.id_lang|escape:'html':'UTF-8'}"
							class="form-control checkbox_value_all checkbox_value_{$language.id_lang|escape:'html':'UTF-8'}"
							placeholder="{l s='display label' mod='mpextrafield'}"
							{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
						{/foreach}
						<div class="help-block">{l s='Checkbox display label' mod='mpextrafield'}</div>
					</div>
					{if $allow_multilang && $total_languages > 1}
						<div class="col-lg-1">
							<button type="button" class="btn btn-default dropdown-toggle checkbox_value_lang_btn" data-toggle="dropdown">
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
					<div class="col-lg-1">
						<a href="" id="add_another_checkboxlabel" class="btn btn-default">{l s='Add Checkbox option' mod='mpextrafield'}</a>
					</div>
				</div>
			</div>
			</div>
		</div>
		{/if}
		{if isset($custom_field.inputtype) && $custom_field.inputtype == 3}
			{if isset($custom_multiple_field_values) || isset($custom_field.id)}
			<div id="dropdown_label_info" class="update_dropdown">
				<input type="hidden" name="max_options" id="max_options" value="{$edit_max_options|escape:'htmlall':'UTF-8'}">
				<input type="hidden" name="count_options" id="count_options" value="{$custom_multiple_field_values|@count|escape:'htmlall':'UTF-8'}">
				{foreach from=$custom_multiple_field_values key=k item=dropdown_value}
				<div class="form-group">
					{if $k < 1}
						<label class="col-lg-3 control-label required">{l s='Dropdown Display Value' mod='mpextrafield'}
						</label>
					{else}
						<label class="col-lg-3"></label>
					{/if}

					<div class="row">
						<div class="col-lg-3">
							{foreach from=$languages item=language}
								<input type="text"
								value="{$dropdown_value.display_value.{$language.id_lang}|escape:'htmlall':'UTF-8'}"
								name="display_value_{$k+1|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}"
								class="form-control dropdown_value_all dropdown_value_{$language.id_lang|escape:'html':'UTF-8'}"
								placeholder="{l s='display value' mod='mpextrafield'}"
								{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
							{/foreach}
							{if $k < 1}<div class="help-block">{l s='Display In Drop Down' mod='mpextrafield'}</div>{/if}
						</div>
						{if $allow_multilang && $total_languages > 1 && $k < 1}
							<div class="col-lg-1">
								<button type="button" class="btn btn-default dropdown-toggle dropdown_value_lang_btn" data-toggle="dropdown">
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
						<div class="col-lg-1">
							{if $k < 1}
								<a href="" id="add_another_dropdownvalue" class="btn btn-default">{l s='Add DropDown option' mod='mpextrafield'}</a>
							{else}
								<a href="#" class="remove_dropdownvalue btn btn-default" data-remove-id="{$k+1|escape:'htmlall':'UTF-8'}">{l s='Remove' mod='mpextrafield'}</a>
							{/if}
						</div>
					</div>
				</div>
				{/foreach}
			</div>
			{/if}
		{/if}
		{if isset($custom_field.inputtype) && $custom_field.inputtype==4}
			{if isset($custom_multiple_field_values) || isset($custom_field.id)}
			<input type="hidden" name="max_options" id="max_options" value="{$edit_max_options|escape:'htmlall':'UTF-8'}">
			<input type="hidden" name="count_options" id="count_options" value="{$custom_multiple_field_values|@count|escape:'htmlall':'UTF-8'}">
			<div class="check_info" id="check_info">
				{foreach from=$custom_multiple_field_values key=k item=dropdown_value}
				<div class="form-group">
					{if $k < 1}
						<label class="col-lg-3 control-label required">{l s='Checkbox display label' mod='mpextrafield'}
						</label>
					{else}
						<label class="col-lg-3"></label>
					{/if}

					<div class="row">
					<div class="col-lg-3">
						{foreach from=$languages item=language}						
							<input type="text" value="{$dropdown_value.display_value.{$language.id_lang}|escape:'htmlall':'UTF-8'}"
							name="mp_check_val_{$k+1|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}"
							class="form-control checkbox_value_all checkbox_value_{$language.id_lang|escape:'html':'UTF-8'}"
							placeholder="{l s='display label' mod='mpextrafield'}"
							{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
						{/foreach}
						{if $k < 1}<div class="help-block">{l s='Checkbox display label' mod='mpextrafield'}</div>{/if}
					</div>
					{if $allow_multilang && $total_languages > 1 && $k < 1}
						<div class="col-lg-1">
							<button type="button" class="btn btn-default dropdown-toggle checkbox_value_lang_btn" data-toggle="dropdown">
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
					<div class="col-lg-1">
						{if $k < 1}<a href="" id="add_another_checkboxlabel" class="btn btn-default">{l s='Add Checkbox option' mod='mpextrafield'}</a>
						{else}
							<a href="" class="remove_checkbox btn btn-default" data-remove-id="{$k+1|escape:'htmlall':'UTF-8'}">{l s='Remove' mod='mpextrafield'}</a>
						{/if}
					</div>
				</div>
				</div>
				{/foreach}
			</div>
			{/if}
		{/if}
		<div id="multiple_option" class="form-group" style="display:none;">
			<label class="col-lg-3 control-label required">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="user can select multiple options">
					{l s='Multiple Selection' mod='mpextrafield'}
				</span>
			</label>
			<div class="col-lg-6 ">
				<span class="switch prestashop-switch fixed-width-lg">
					<input id="show_prices_on" type="radio" value="1" name="multiple" {if isset($smarty.post.multiple) && $smarty.post.multiple == 1}checked="checked"{else if isset($custom_field.id) && $custom_field.multiple=='1'}checked="checked"{/if} >
					<label for="show_prices_on">{l s='Yes' mod='mpextrafield'}</label>
					{if isset($smarty.post.multiple)}
					<input id="show_prices_off" type="radio" value="0" name="multiple" {if $smarty.post.multiple == 0}checked="checked"{/if}>
					{else}
					<input id="show_prices_off" type="radio" value="0" name="multiple" {if !isset($custom_field.id)}checked="checked"{else if isset($custom_field.id) && $custom_field.multiple=='0'}checked="checked"{/if}>
					{/if}
					<label for="show_prices_off">{l s='No' mod='mpextrafield'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		<div id="file_info" {if !isset($custom_field.id) || $custom_field.inputtype!=5}style="display:none;"{/if}>
			<div class="form-group" id="container_fileinfo">
				<label class="col-lg-3 control-label required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="what type of file user can upload eg-png">
						{l s='File Type' mod='mpextrafield'}
					</span>
				</label>
				<div class="col-lg-3 checkbox">
					<input style="margin-left:0px;" type="checkbox" value="1" name="file_image" {if isset($smarty.post.file_image) && $smarty.post.file_image == 1}checked="checked"{else if isset($custom_field.id) && ($custom_field.file_type==1 || $custom_field.file_type==3)}checked="checked"{/if} >
					<span style="float:left; margin-left:10px;">{l s='User can upload images eg- jpg,png,jpeg...etc.' mod='mpextrafield'}</span>
				</div>
			</div>
			<div class="form-group" id="container_fileinfo">
				<label class="col-lg-3 control-label"></label>
				<div class="col-lg-3 checkbox">
					<input style="margin-left:0px;" type="checkbox" value="2" name="file_doc" {if isset($smarty.post.file_doc) && $smarty.post.file_doc == 2}checked="checked"{else if isset($custom_field.id) && ($custom_field.file_type==2 || $custom_field.file_type==3)}checked="checked"{/if} >
					<span style="float:left; margin-left:10px;">{l s='User can upload documents  eg- .doc .pdf .zip file ...etc' mod='mpextrafield'}</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-3 control-label required">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="label name of input type">
					{l s='Label name' mod='mpextrafield'}
				</span>
			</label>
			<div class="row">
				<div class="col-lg-6">
					{foreach from=$languages item=language}						
						{assign var="lbl_name" value="label_name_`$language.id_lang`"}
						<input type="text" 
						id="label_name_{$language.id_lang|escape:'html':'UTF-8'}" 
						name="label_name_{$language.id_lang|escape:'html':'UTF-8'}"
						class="form-control label_name_all"
						 value="{if isset($smarty.post.$lbl_name)}{$smarty.post.$lbl_name|escape:'htmlall':'UTF-8'}{else if isset($custom_field.label_name.{$language.id_lang})}{$custom_field.label_name.{$language.id_lang}|escape:'htmlall':'UTF-8'}{/if}"
						{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
					{/foreach}
				</div>
				{if $allow_multilang && $total_languages > 1}
					<div class="col-lg-2">
						<button type="button" id="label_lang_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
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
		<div class="form-group">
			<label class="col-lg-3 control-label required">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="name attribute of input field">
					{l s='Attibute Name' mod='mpextrafield'}
				</span>
			</label>
			<div class="col-lg-6">
				<input type="text" value="{if isset($smarty.post.attribute_name)}{$smarty.post.attribute_name|escape:'htmlall':'UTF-8'}{else if isset($custom_field.attribute_name)}{$custom_field.attribute_name|escape:'htmlall':'UTF-8'}{/if}" class="form-control" name="attribute_name" {if isset($custom_field.attribute_name)}readonly{/if}>
				<span class="help-block">{l s='Write attribute name without space' mod='mpextrafield'}</span>
			</div>
		</div>
		<div id="default_value" class="form-group" {if isset($custom_field.id) && $custom_field.inputtype!=1 && $custom_field.inputtype!=2}style="display:none;"{/if}>
			<label class="col-lg-3 control-label">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="default value will apprear in field">
					{l s='Default Value' mod='mpextrafield'}
				</span>
			</label>
			<div class="row">
				<div class="col-lg-6">
					{foreach from=$languages item=language}						
						{assign var="deflt_name" value="default_value_`$language.id_lang`"}
						<input type="text" 
						id="default_value_{$language.id_lang|escape:'html':'UTF-8'}" 
						name="default_value_{$language.id_lang|escape:'html':'UTF-8'}"
						class="form-control default_value_all"
						 value="{if isset($smarty.post.$deflt_name)}{$smarty.post.$deflt_name|escape:'htmlall':'UTF-8'}{else if isset($custom_field.default_value.{$language.id_lang})}{$custom_field.default_value.{$language.id_lang}|escape:'htmlall':'UTF-8'}{/if}"
						{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
					{/foreach}
				</div>
				{if $allow_multilang && $total_languages > 1}
					<div class="col-lg-2">
						<button type="button" id="default_value_lang_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
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
		<div id="placeholder" class="form-group" id="place_holder_div" {if isset($custom_field.id) && $custom_field.inputtype!=1 && $custom_field.inputtype!=2}style="display:none;"{/if}>
			<span class="col-lg-3"></span>
			<div class="col-lg-6">
				<div class="checkbox">
					<input type="checkbox" id="as_placeholder" name="as_placeholder" {if isset($smarty.post.as_placeholder) && $smarty.post.as_placeholder == 'on'}checked="checked"{else if isset($custom_field.asplaceholder) && $custom_field.asplaceholder==1}checked="checked"{/if}>
					<span>{l s='Use Default value as Place Holder' mod='mpextrafield'}</span>
				</div>
			</div>
		</div>
		<div id="status_info" class="form-group" {if !isset($custom_field.id) }style="display:none;"{/if}>
			<label class="col-lg-3 control-label required">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="Enabled field">
					{l s='Status' mod='mpextrafield'}
				</span>
			</label>
			<div class="col-lg-6 ">
				<span class="switch prestashop-switch fixed-width-lg">
					<input name="status_info" id="optin_on" value="1" type="radio" {if isset($smarty.post.status_info) && $smarty.post.status_info==1}checked="checked"{else if isset($custom_field.id) && $custom_field.active=='1'}checked="checked"{/if}>
					<label for="optin_on">{l s='Yes' mod='mpextrafield'}</label>
					{if isset($smarty.post.status_info)}
					<input name="status_info" id="optin_off" value="0" type="radio" {if isset($smarty.post.status_info) && $smarty.post.status_info == 0}checked="checked"{/if}>
					{else}
					<input name="status_info" id="optin_off" value="0" type="radio" {if !isset($custom_field.id)}checked="checked"{else if isset($custom_field.id) && $custom_field.active=='0'}checked="checked"{/if}>
					{/if}
					<label for="optin_off">{l s='No' mod='mpextrafield'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminAddextrafield')|escape:'htmlall':'UTF-8'}" class="btn btn-default">
				<i class="process-icon-cancel"></i>
				{l s='Cancel' mod='mpextrafield'}
			</a>
			<button type="submit" name="submitAddmarketplace_extrafield" class="btn btn-default pull-right">
				<i class="process-icon-save"></i>
				{l s='Save' mod='mpextrafield'}
			</button>
		</div>
	</div>
</form>
</fieldset>
</div>
{/block}
{strip}
{addJsDefL name=remove}{l s='Remove' js=1 mod='mpextrafield'}{/addJsDefL}
{addJsDefL name=displayValue}{l s='Display Value' js=1 mod='mpextrafield'}{/addJsDefL}


{/strip}

<script type="text/javascript">
	var i = 1;
	var count_options = $('#count_options').val();
	count_options = parseInt(count_options);
	if (count_options > 0) {
		i = count_options;
	} else {
		i = 1;
	}
	$(document).on('click', '#add_another_dropdownvalue', function(e){
		e.preventDefault();
		var languages = new Array();
		languages = {$languages|@json_encode};
		var current_lang = new Array();
		current_lang = {$current_lang|@json_encode};
		var allow_multilang = {$allow_multilang};
		var total_languages = {$total_languages};
		var max_options = $('#max_options').val();
		i = i + 1;

		/*var data = '<div class="form-group abc"><span class="col-lg-3"></span><div class="col-lg-3"><input type="text" value="" name="display_value[]" class="display_value1" placeholder="'+displayValue+'"/>';*/

		var data = '<div class="form-group"><span class="col-lg-3"></span><div class="col-lg-3">'
		$.each(languages, function(key, language) {
			data +='<input type="text" id="default_value_'+i+'_'+language.id_lang+'" name="display_value_'+i+'_'+language.id_lang+'" class="display_value1 dropdown_value_all dropdown_value_'+language.id_lang+'"  placeholder="'+displayValue+'" value=""';

			if (current_lang.id_lang != language.id_lang)
			{
				data += ' style="display:none;"';
			}
			data += '/>';
		});
		data += '</div>';

		/*if (allow_multilang && total_languages > 1) {
			data += '<div class="col-lg-1"><button type="button" id="" class="btn btn-default dropdown-toggle dropdown_value_lang_btn" data-toggle="dropdown">'+current_lang.iso_code+'<span class="caret"></span></button><ul class="dropdown-menu">';

			$.each(languages, function(key, language) {
				data += '<li><a href="javascript:void(0)" onclick="showExtraLangField('+language.iso_code+', '+language.id_lang+');">'+language.name+'</a></li>';
			});
			data += '</ul></div>';
		}*/
		data += '<div class="col-lg-1"><a href="#" class="remove_dropdownvalue btn btn-default" data-remove-id="'+i+'">'+remove+'</a></div></div>';
		$('#dropdown_label_info').after(data);
		max_options += ','+i;
		$('#max_options').val(max_options);
	});

	// remove dropdown value option
	$(document).on('click', '.remove_dropdownvalue', function(e){
		e.preventDefault();
		remove_id = $(this).attr('data-remove-id');
		var is_set = $('#set').val();
		if(is_set==0)
		{
			var is_optionid = $(this).attr('is_optionid');
			if(typeof is_optionid == 'undefined')
			{
				//nothing to do
			}
			else
			{
				var remove_option_id = $('#remove_option_id').val();	
				if(remove_option_id == '')
					$('#remove_option_id').attr('value',is_optionid);
				else
				{
					remove_option_id = remove_option_id+','+is_optionid;
					$('#remove_option_id').attr('value',remove_option_id);
				}
			}
		}
		$(this).parent().parent().remove();
		var max_options = $('#max_options').val();
		var new_max_options = '1';
		var remove_option = max_options.split(',');
		$.each(remove_option, function(key, value) {
			if (value != remove_id && value != '1')
			{
				new_max_options += ','+value;
			}
		});
		var max_options = $('#max_options').val(new_max_options);
		/*i = i-1;
		$('#max_options').val(i);*/
	});

	$(document).on('click', '#add_another_checkboxlabel', function(e){
		e.preventDefault();
		var languages = new Array();
		languages = {$languages|@json_encode};
		var current_lang = new Array();
		current_lang = {$current_lang|@json_encode};
		var allow_multilang = {$allow_multilang};
		var total_languages = {$total_languages};
		var max_options = $('#max_options').val();
		i = i + 1;
		//alert(i);
		var data = '<div class="form-group"><span class="col-lg-3"></span><div class="col-lg-3">'
		$.each(languages, function(key, language) {
			data +='<input type="text" name="mp_check_val_'+i+'_'+language.id_lang+'" class="checkbox_value_all checkbox_value_'+language.id_lang+'"  placeholder="'+displayValue+'" value=""';

			if (current_lang.id_lang != language.id_lang)
			{
				data += ' style="display:none;"';
			}
			data += '/>';
		});
		data += '</div>';

		/*if (allow_multilang && total_languages > 1) {
			data += '<div class="col-lg-1"><button type="button" id="" class="btn btn-default dropdown-toggle dropdown_value_lang_btn" data-toggle="dropdown">'+current_lang.iso_code+'<span class="caret"></span></button><ul class="dropdown-menu">';

			$.each(languages, function(key, language) {
				data += '<li><a href="javascript:void(0)" onclick="showExtraLangField('+language.iso_code+', '+language.id_lang+');">'+language.name+'</a></li>';
			});
			data += '</ul></div>';
		}*/
		data += '<div class="col-lg-1"><a href="#" class="remove_checkbox btn btn-default" data-remove-id="'+i+'">'+remove+'</a></div></div>';
		$('#check_info').after(data);
		max_options += ','+i;
		$('#max_options').val(max_options);
	});

	// remove checkbox value option
	$(document).on('click', '.remove_checkbox', function(e){
		e.preventDefault();
		remove_id = $(this).attr('data-remove-id');
		var is_set = $('#set').val();
		if(is_set==0)
		{
			var is_optionid = $(this).attr('is_optionid');
			if(typeof is_optionid == 'undefined')
			{
				//nothing to do
			}
			else
			{
				var remove_option_id = $('#remove_option_id').val();	
				if(remove_option_id == '')
					$('#remove_option_id').attr('value',is_optionid);
				else
				{
					remove_option_id = remove_option_id+','+is_optionid;
					$('#remove_option_id').attr('value',remove_option_id);
				}
			}
		}
		$(this).parent().parent().remove();
		var max_options = $('#max_options').val();
		var new_max_options = '1';
		var remove_option = max_options.split(',');
		$.each(remove_option, function(key, value) {
			if (value != remove_id && value != '1')
			{
				new_max_options += ','+value;
			}
		});
		var max_options = $('#max_options').val(new_max_options);
		/*i = i-1;
		$('#max_options').val(i);*/
	});

</script>