{*
* 2010-2017 Webkul.
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


<div class="tab-pane" id="mp_product_customization">
	<div class="form-group">
		<label class="control-label" for="file_field">{l s='File Fields:' mod='mpproductcustomization'}</label>
	   	<div class="row ">
			<div class="col-md-2">
				<input type="text" class="form-control" id="uploadable_files" name="uploadable_files" value="{if isset($files_count)}{$files_count|escape:'html':'UTF-8'}{/if}">		
			</div>
		</div>
	   	<p class="help-block">{l s='Number of upload file fields to be displayed to the user.' mod='mpproductcustomization'}</p>
	</div>
	<div class="form-group">
		<label class="control-label" for="text_field">{l s='Text Fields:' mod='mpproductcustomization'}</label>
	   	<div class="row">
			<div class="col-md-2">
				<input type="text" class="form-control" id="text_fields" name="text_fields" value="{if isset($text_count)}{$text_count|escape:'html':'UTF-8'}{/if}">
			</div>
		</div>
	   	<p class="help-block">{l s='Number of text fields to be displayed to the user.' mod='mpproductcustomization'}</p>
	</div>
	{if isset($files_count)}
		<div class="form-group">
			<label class="control-label">{l s='Define the label of file fields :' mod='mpproductcustomization'}
			{block name='mp-form-fields-flag'}
				{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
			{/block}
			</label>
			<div class="row">
				{foreach from=$meta_info item=meta_data}
					{if $meta_data.type eq 0}
					<div class="form-group">					
						<div class="col-md-5" style="padding-left:0px;">
							{foreach from=$languages item=language}
								{assign var="filetitle" value="filename_`$language.id_lang`"}
								<div  class="wk_text_field_all input-group col-md-12  wk_text_field_{$language.id_lang|escape:'html':'UTF-8'}" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} >
									<input type="text" name="filename_{$meta_data.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" id="filename_{$meta_data.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" class="form-control" value="{if isset($customization_fields)}{$customization_fields.{$meta_data.type|escape:'html':'UTF-8'}.{$meta_data.id|escape:'html':'UTF-8'}.{$language.id_lang|escape:'html':'UTF-8'}.name|escape:'html':'UTF-8'}{/if}">
								</div>
							{/foreach}							  			
		 				</div>
						<div>
							<div class="checkbox">
								<label for="require">
								<input type="checkbox" {if $customization_fields.{$meta_data.type}.{$meta_data.id}.{$language.id_lang}.required eq 1}checked{/if} id="require_{$meta_data.id|escape:'html':'UTF-8'}" value="1" name="require_{$meta_data.id|escape:'html':'UTF-8'}">{l s='Required' mod='mpproductcustomization'}</label>
							</div>
						</div>
					</div>
					{/if}
				{/foreach}
			</div>
		</div>
	{/if}
	{if isset($text_count)}
		<div class="form-group">
			<label class="control-label">{l s='Define the label of text fields :' mod='mpproductcustomization'}
			{block name='mp-form-fields-flag'}
				{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
			{/block}
			</label>
			<div class="row">
				{foreach from=$meta_info item=meta_data}
					{if $meta_data.type eq 1}
					<div class="form-group">
						<div class="col-md-5" style="padding-left:0px;">
							{foreach from=$languages item=language}
								{assign var="texttitle" value="textname_`$language.id_lang`"}
								<div class="wk_text_field_all input-group col-md-12  wk_text_field_{$language.id_lang|escape:'html':'UTF-8'}" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} >
									<input type="text" name="textname_{$meta_data.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" id="textname_{$meta_data.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" class="form-control" value="{if isset($customization_fields)}{$customization_fields.{$meta_data.type|escape:'html':'UTF-8'}.{$meta_data.id|escape:'html':'UTF-8'}.{$language.id_lang|escape:'html':'UTF-8'}.name|escape:'html':'UTF-8'}{/if}">
								</div>
							{/foreach}							  			
				  		</div>
						<div>
							<div class="checkbox">
								<label for="require">
								<input type="checkbox" {if $customization_fields.{$meta_data.type}.{$meta_data.id}.{$language.id_lang}.required eq 1}checked{/if} id="require_{$meta_data.id|escape:'html':'UTF-8'}" value="1" name="require_{$meta_data.id|escape:'html':'UTF-8'}">{l s='Required' mod='mpproductcustomization'}</label>
							</div>
						</div>
					</div>
					{/if}
				{/foreach}
			</div>
		</div>
	{/if}
</div>

