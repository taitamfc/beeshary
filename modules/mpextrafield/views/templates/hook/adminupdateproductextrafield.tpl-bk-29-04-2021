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
<script type="text/javascript" src="{$module_dir|escape:'htmlall':'UTF-8'}views/js/fieldform.js"></script>
<style type="text/css">
    div.radio input
    {
        background: none repeat scroll 0 0 rgba(0, 0, 0, 0);
        border: medium none;
        display: inline-block;
        opacity: unset !important;
        text-align: center;
    }
    div.mp_extra_loading
    {
        display:none;
        position:fixed;
        left:0;
        top:0;
        height:100%;
        width:100%;
        background-color:rgba(0,0,0,0.5);
        z-index: 100000;
    }
    img.mpextra_loading_img
    {
        position:absolute;
        left:50%;
        top:50%;
    }
</style>

{assign var=temp value=0}
{if isset($extrafielddetail)}
    {if isset($controller) && $controller == 'sellerprofile'}
        <input value="{if isset($seller_id)}{$seller_id|escape:'htmlall':'UTF-8'}{/if}" name="id" type="hidden">
    {/if}
    {foreach $extrafielddetail as $extrafield }
        {if $extrafield.inputtype == 1}
            <div class="required form-group">
                <label class="col-lg-3 {if $extrafield.field_req == 1}required{/if} control-label" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
                    {include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
                </label>
                <div class="col-lg-6">
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
                </div>
            </div>
        {/if}
        {if $extrafield.inputtype ==2}
            <div class="required form-group">
                <label class="{if $extrafield.field_req == 1}required{/if} control-label col-lg-3" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
                    {include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
                </label>
                <div class="col-lg-6">
                    {foreach from=$languages item=language}
                        {assign var="textarea_name" value="{$extrafield.attribute_name}_`$language.id_lang`"}
                        <textarea name="{$textarea_name}" id="" class="form-control wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>{strip}
							{if isset($smarty.post.{$textarea_name})}{$smarty.post.{$textarea_name}}
                            {elseif !empty($extrafield.default_value) && $extrafield.asplaceholder eq 0}
                                {if is_array($extrafield.default_value)}
                                    {$extrafield.default_value.{$language.id_lang}|escape:'htmlall':'UTF-8'}
                                {else}
                                    {$extrafield.default_value|escape:'htmlall':'UTF-8'}
                                {/if}
                            {/if}
						{/strip}</textarea>
					{/foreach}
				</div>
			</div>
		{/if}
		{if $extrafield.inputtype == 3}
			<div class="form-group">
				<label class="control-label col-lg-3 {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
				</label>
				<!-- /* EDIT 27-04-21 by Claire */  -->
				<div class="col-lg-3">
					{if $extrafield.attribute_name == 'spoken_langs'}							
						 <select required name="spoken_langs[]" class="form-control extra-select" multiple="">
			               <option value="108" {if in_array(108,$sl_spoken_langs) }selected{/if}>Français</option>
			               <option value="109" {if in_array(109,$sl_spoken_langs) }selected{/if}>Anglais</option>
			               <option value="110" {if in_array(110,$sl_spoken_langs) }selected{/if}>Allemand</option>
			               <option value="111" {if in_array(111,$sl_spoken_langs) }selected{/if}>Espagnol</option>
			               <option value="112" {if in_array(112,$sl_spoken_langs) }selected{/if}>Portuguais</option>
			               <option value="113" {if in_array(113,$sl_spoken_langs) }selected{/if}>Italien</option>
			               <option value="114" {if in_array(114,$sl_spoken_langs) }selected{/if}>Chinois</option>
			               <option value="115" {if in_array(115,$sl_spoken_langs) }selected{/if}>Arabe</option>
			               <option value="116" {if in_array(116,$sl_spoken_langs) }selected{/if}>Autre</option>
			            </select>
					{else}			
						<select name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}[]{/if}" class="form-control" {if $extrafield.multiple==1}multiple{/if}>
							{if $extrafield.multiple != 1}
								{foreach $extrafield['extfielddrop'] as $extfieldopt}
								<option value="{$extfieldopt.id|escape:'htmlall':'UTF-8'|intval}" 
								{if isset($smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}) && $extfieldopt.id == $smarty.post.{$extrafield.attribute_name}}selected="selected"
								{else if $extfieldopt.selected_value == $extfieldopt.id}selected="selected"{/if}>{$extfieldopt['display_value']|escape:'htmlall':'UTF-8'}
								</option>
								{/foreach}
							{else if $extrafield.multiple == 1}
								{foreach $extrafield['extfielddrop'] as $extfieldopt}
								{assign var=select value=","|explode:$extfieldopt.selected_value}
								<option value="{$extfieldopt.id|intval}" 
								{if isset($smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'})}
									{foreach $smarty.post.{$extrafield.attribute_name} as $key => $smarty_val}
										{if $smarty_val == $extfieldopt.id}
											selected="selected"
										{/if}
									{/foreach}
								{else}
									{if in_array($extfieldopt.id, $select)}
									  	selected="selected"
									{/if}
								{/if}>
								 {$extfieldopt['display_value']|escape:'htmlall':'UTF-8'}
								</option>
								{/foreach}
							{/if}
						</select>
					{/if}
				</div>
				<!-- end claire -->
			</div>
		{/if}
		{if $extrafield.inputtype ==4}
			<div class="clearfix form-group">
				<label class="control-label col-lg-3 {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
				</label>
				<div class="col-lg-6">
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
									{if $chk == $extfieldopt.id}
										checked="checked"
									{/if}
								{/foreach}
							{/if}>
								{$extfieldopt.display_value|escape:'html':'UTF-8'}
						</label>
					</div>
				{/foreach}
				</div>
			</div>
		{/if}
		{if $extrafield.inputtype ==5}
			<div class="required form-group" style="margin-top:10px;">
				<label class="control-label col-lg-3 {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
				</label>
				{if isset($extrafield.default_value) && $extrafield.value_id && $extrafield.default_value != '0'}
					<div>
					<a href="{$link->getModuleLink('mpextrafield', 'mediadownload',['id_attachment' => $extrafield['value_id']])|escape:'html':'UTF-8'}" class="btn btn-default"><i class="icon-download"></i> {l s='Download file' mod='mpextrafield'}</a>
					<a id="{$extrafield['value_id']|escape:'htmlall':'UTF-8'}" href="#" class="btn btn-default delete_me"><i class="icon-download"></i> {l s='Remove file' mod='mpextrafield'}</a>
					<input type="hidden" value="{$extrafield.id|escape:'htmlall':'UTF-8'}" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}{/if}">
					</div>
				{else}
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
				{/if}
			</div>
		{/if}
		{if $extrafield.inputtype == 6}
			<div class="clearfix form-group">
				<label class="control-label col-lg-3 {if $extrafield.field_req == 1}required{/if}" for="label_name">{$extrafield.label_name|escape:'htmlall':'UTF-8'}
				</label>
				<div class="col-lg-6">
					<div class="radio-inline">
						<label for="gender1">
							<div>
								<input class="extra-radio" type="radio" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}{/if}" value="1" {if isset($smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}) && $extfieldopt.id == $smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}}checked="checked"{else if $extrafield.selected_value == 1}checked="checked"{/if}>
							{foreach $extrafield.extfieldradio as $extfieldrad}
								{$extfieldrad.left_value|escape:'htmlall':'UTF-8'}
							{/foreach}
							</div>
						</label>
					</div>
					<div class="radio-inline">
						<label for="gender2">
							<div><input class="extra-radio" type="radio" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}{/if}" value="2" {if isset($smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}) && $extfieldopt.id == $smarty.post.{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}}checked="checked"{else if $extrafield.selected_value == 2}checked="checked"{/if}>
							{foreach $extrafield.extfieldradio as $extfieldrad}
								{$extfieldrad.right_value|escape:'htmlall':'UTF-8'}
							{/foreach}
							</div>
						</label>
					</div>
				</div>
			</div>
		{/if}
		{assign var=temp value=$temp+1}
	{/foreach}
{/if}
<!-- ajax-loader for response -->
<div class="mp_extra_loading">
    <img src="{$module_dir|escape:'html':'UTF-8'}views/img/ajax-loader.gif" class="mpextra_loading_img"/>
</div>

{strip}
    {addJsDef ajax_urlpath = $front_path}
{/strip}



