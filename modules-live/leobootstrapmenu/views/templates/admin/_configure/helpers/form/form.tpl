{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

{extends file="helpers/form/form.tpl"}

{block name="field"}
	{if $input.type == 'img_cat'}
		{assign var=tree value=$input.tree}
		{assign var=imageList value=$input.imageList}
		{assign var=selected_images value=$input.selected_images}
		<div class="form-group form-select-icon">
			<label class="control-label col-lg-3 " for="categories"> {l s='Categories' mod='leobootstrapmenu'} </label>
			<div class="col-lg-9">
			{$tree}{* HTML form , no escape necessary *}
			</div>
			<input type="hidden" name="category_img" id="category_img" value='{$selected_images|escape:'html':'UTF-8'}'/>
			<div id="list_image_wrapper" style="display:none">
				<div class="list-image btn-group">
					<button style="background-color:#00aff0; padding:0 8px;" type="button" class="btn dropdown-toggle" data-toggle="dropdown" value="imageicon">icons
					  <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#">none</a></li>
						{foreach from=$imageList item=image}
						<li><a href="#"><img height = '10px' src='{$image["path"]|escape:'html':'UTF-8'}'> {$image["name"]|escape:'html':'UTF-8'}</a></li>
						 {/foreach}
					</ul>
				  </div>
			</div>
		</div>
	{/if}
    {if $input.type == 'file_lang'}
		<div class="col-lg-9">
        <div class="row">
            {foreach from=$languages item=language}

                {if $languages|count > 1}
                    <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                    {/if}
                    <div class="col-lg-9">
                        <div class="upload-img-form">
                            <img id="thumb_slider_thumbnail_{$language.id_lang|intval}" width="50" class="{if !$fields_value[$input.name][$language.id_lang]}nullimg{/if}" alt="" src="{$psBaseModuleUri|escape:'html':'UTF-8'}{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}"/>
                            <input id="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}" type="hidden" name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}" class="hide" value="{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}" />
                            <br>
                            <a onclick="image_upload('{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}', 'thumb_slider_thumbnail_{$language.id_lang|intval}');" href="javascript:void(0);">{l s='Browse' mod='leobootstrapmenu'}</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                            <a onclick="$('#thumb_slider_thumbnail_{$language.id_lang|intval}').attr('src', '');$('#thumb_slider_thumbnail_{$language.id_lang|intval}').addClass('nullimg'); $('#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}').attr('value', '');" href="javascript:void(0);">{l s='Clear' mod='leobootstrapmenu'}</a>
                        </div>
                        <br/>
                    </div>
                    {if $languages|count > 1}
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                {$language.iso_code}{* HTML form , no escape necessary *}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=lang}
                                    <li><a href="javascript:hideOtherLanguage({$lang.id_lang|intval});" tabindex="-1">{$lang.name|escape:'html':'UTF-8'}</a></li>
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                    {if $languages|count > 1}
                    </div>
                {/if}
                <script>
                    $(document).ready(function() {
                        $('#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}-selectbutton').click(function(e) {
                            $('#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}').trigger('click');
                        });
                        $('#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}').change(function(e) {
                            var val = $(this).val();
                            var file = val.split(/[\\/]/);
                            $('#{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}-name').val(file[file.length - 1]);
                        });
                    });
                </script>
                <input id="slider-image_{$language.id_lang|intval}" type="hidden" name="image_{$language.id_lang|intval}" class="hide" value="{$fields_value["image"][$language.id_lang]|escape:'html':'UTF-8'}" />
            {/foreach}
        </div>
		</div>
    {/if}
    {if $input.type == 'group_background'}
        <div class="col-lg-9">
            <div class="upload-img-form">
               <img id="img_{$input.id|intval}" width="50" class="{if !{$fields_value[$input.name]|escape:'html':'UTF-8'}}nullimg{/if}" alt="{l s='Group Back-ground' mod='leobootstrapmenu'}" src="{$psBaseModuleUri|escape:'html':'UTF-8'}{$fields_value[$input.name]|escape:'html':'UTF-8'}"/>
               <input id="{$input.id|intval}" type="hidden" name="group[background_url]" class="hide" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
               <br>
               <a onclick="background_upload('{$input.id|intval}', 'img_{$input.id|intval}','{$ajaxfilelink}'{* HTML form , no escape necessary *}, '{$psBaseModuleUri|escape:'html':'UTF-8'}');" href="javascript:void(0);">{l s='Browse' mod='leobootstrapmenu'}</a>&nbsp;&nbsp;|&nbsp;&nbsp;
               <a onclick="$('#img_{$input.id|intval}').attr('src', '');$('#img_{$input.id|intval}').addClass('nullimg'); $('#{$input.id|intval}').attr('value', '');" href="javascript:void(0);">{l s='Clear' mod='leobootstrapmenu'}</a>
           </div>
            <p>{l s='Click to upload or select a back-ground' mod='leobootstrapmenu'}</p>
        </div>
    {/if}
    {if $input.type == 'group_button' && $input.id_group}
        <div class="form-group">
            <div class="col-lg-9 col-lg-offset-3">
                <div class="btn-group pull-right">
                    <a class="btn btn-default {if $languages|count > 1}dropdown-toggle {else}group-preview {/if}color_danger" href="{$previewLink|escape:'html':'UTF-8'}&id_group={$input.id_group|intval}"><i class="icon-eye-open"></i> {l s='Preview Group' mod='leobootstrapmenu'}</a>
                    {if $languages|count > 1}
                    
                    <span data-toggle="dropdown" class="btn btn-default dropdown-toggle">
                        <span class="caret"></span>&nbsp;
                    </span>
                    <ul class="dropdown-menu">
                        {foreach from=$languages item=language}
                        <li>
                            {$arrayParam = ['secure_key' => $msecure_key, 'id_group' => $input.id_group|intval]}
                            <a href="{$link->getModuleLink('leoslideshow','preview', $arrayParam, null, $language.id_lang|intval)}" class="group-preview">
                                <i class="icon-eye-open"></i> {l s='Preview For' mod='leobootstrapmenu'} {$language.name|escape:'html':'UTF-8'}
                            </a>
                        </li>
                        {/foreach}
                    </ul>
                    {/if}
                </div>
                
                <button class="btn btn-default dash_trend_right" name="submitGroup" id="module_form_submit_btn" type="submit">
                        <i class="icon-save"></i> {l s='Save' mod='leobootstrapmenu'}
                </button>
                <a class="btn btn-default color_success" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leoslideshow&showsliders=1&id_group={$input.id_group|intval}"><i class="icon-film"></i> {l s='Manages Sliders' mod='leobootstrapmenu'}</a>
                <a class="btn btn-default" href="{$exportLink|escape:'html':'UTF-8'}&id_group={$input.id_group|intval}"><i class="icon-eye-open"></i> {l s='Export Group and sliders' mod='leobootstrapmenu'}</a>
                <a class="btn btn-default" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leoslideshow&deletegroup=1&id_group={$input.id_group|intval}" onclick="if (confirm('{l s='Delete Selected Group?' mod='leobootstrapmenu'}')) {
                            return true;
                        } else {
                            event.stopPropagation();
                            event.preventDefault();
                        }
                        ;" title="{l s='Delete' mod='leobootstrapmenu'}" class="delete">
                    <i class="icon-trash"></i> {l s='Delete' mod='leobootstrapmenu'}
                </a>
            </div>
        </div>


    {/if}
    {if $input.type == 'slider_button'}
        <div class="form-group">
            <div class="col-lg-9 col-lg-offset-3">
                <a class="btn btn-default dash_trend_right" href="javascript:void(0)" onclick="return $('#module_form').submit();"><i class="icon-save"></i> {l s='Save Slider' mod='leobootstrapmenu'}</a>
            </div>
        </div>
    {/if}
    {if $input.type == 'sperator_form'}
        <div class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{else}alert alert-info{/if}" 
			 {if isset($input.name)}name="{$input.name|escape:'html':'UTF-8'}"{/if}
			 nextClick="false">{$input.text}{* HTML form , no escape necessary *}
		</div>
    {/if}
    {if $input.type == 'video_config'}
		<div class="col-lg-9">
        <div class="row">
            {foreach from=$languages item=language}
                {if $languages|count > 1}
                    <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                    {/if}
                    <div class="col-lg-9">
                        <div class="radiolabel">
                            <lable>{l s='Video Type' mod='leobootstrapmenu'}</lable>
                            <select name="usevideo_{$language.id_lang|intval}" class="">
                                <option {if isset($fields_value["usevideo"][$language.id_lang]) && $fields_value["usevideo"][$language.id_lang] && $fields_value["usevideo"][$language.id_lang] eq "0"}selected="selected"{/if} value="0">{l s='No' mod='leobootstrapmenu'}</option>
                                <option {if isset($fields_value["usevideo"][$language.id_lang]) && $fields_value["usevideo"][$language.id_lang] && $fields_value["usevideo"][$language.id_lang] eq "youtube"}selected="selected"{/if} value="youtube">{l s='Youtube' mod='leobootstrapmenu'}</option>
                                <option {if isset($fields_value["usevideo"][$language.id_lang]) && $fields_value["usevideo"][$language.id_lang] && $fields_value["usevideo"][$language.id_lang] eq "vimeo"}selected="selected"{/if} value="vimeo">{l s='Vimeo' mod='leobootstrapmenu'}</option>
                            </select>
                        </div>
                        <div class="radiolabel">
                            <lable>{l s='Video ID' mod='leobootstrapmenu'}</lable>
                            <input id="videoid_{$language.id_lang|intval}" name="videoid_{$language.id_lang|intval}" type="text" {if isset($fields_value["videoid"][$language.id_lang]) && $fields_value["videoid"][$language.id_lang]} value="{$fields_value["videoid"][$language.id_lang]}"{* HTML form , no escape necessary *}{/if}/>
                            <div class="input-group col-lg-2">
                            </div>
                            <div class="input-group col-lg-2">
                                <lable>{l s='Auto Play' mod='leobootstrapmenu'}</lable>
                                <select name="videoauto_{$language.id_lang|intval}">
                                    <option value="1" {if isset($fields_value["videoauto"][$language.id_lang]) && $fields_value["videoauto"][$language.id_lang] == 1}selected="selected"{/if}>{l s='Yes' mod='leobootstrapmenu'}</option>
                                    <option value="0" {if isset($fields_value["videoauto"][$language.id_lang]) && $fields_value["videoauto"][$language.id_lang] == 0}selected="selected"{/if}>{l s='No' mod='leobootstrapmenu'}</option>
                                </select>
                                
                            </div>
                        </div>
                    </div>
                    {if $languages|count > 1}
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                {$language.iso_code}{* HTML form , no escape necessary *}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=lang}
                                    <li><a href="javascript:hideOtherLanguage({$lang.id_lang|intval});" tabindex="-1">{$lang.name|escape:'html':'UTF-8'}</a></li>
                                    {/foreach}
                            </ul>
                        </div>
                    {/if}
                    {if $languages|count > 1}
                    </div>
                {/if}
            {/foreach}   
        </div>
		</div>
        <input type="hidden" id="current_language" name="current_language" value="{$id_language|escape:'html':'UTF-8'}"/>
    {/if}
    {if $input.type == 'col_width'}
        <div class="col-lg-9">
            <input type='hidden' class="col-val {$input.class|escape:'html':'UTF-8'}" name='{$input.name|escape:'html':'UTF-8'}' value='{$fields_value[$input.name]|escape:'html':'UTF-8'}'/>
            <button type="button" class="btn btn-default leobtn-width dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                <span class="leo-width-val">{$fields_value[$input.name]|replace:'-':'.'}/12</span><span class="leo-width leo-w-{$fields_value[$input.name]|escape:'html':'UTF-8'}"> </span><span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                {foreach from=$leo_width item=itemWidth}
                <li>
                    <a class="leo-w-option" data-width="{$itemWidth|intval}" href="javascript:void(0);" tabindex="-1">                                          
                        <span class="leo-width-val">{$itemWidth|replace:'-':'.'}/12</span><span class="leo-width leo-w-{$itemWidth|intval}"> </span>
                    </a>
                </li>
                {/foreach}
            </ul>
        </div>
    {/if}
    {if $input.type == 'group_class'}
        <div class="col-lg-9">
            <div class="well">
                <p> 
                    <input type="text" class="group-class" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" name="{$input.name|escape:'html':'UTF-8'}"/><br />
                    {l s='insert new or select classes for toggling content across viewport breakpoints' mod='leobootstrapmenu'}<br />
                    <ul class="leo-col-class">
                        {foreach from=$hidden_config key=keyHidden item=itemHidden}
                        <li>
							{*
                            <input type="checkbox" name="col_{$keyHidden|escape:'html':'UTF-8'}" value="1">
							*}
							
							<input type="radio"{if strpos($fields_value[$input.name], $keyHidden) !== false} checked="checked"{/if} data-name="{$keyHidden|escape:'html':'UTF-8'}" name="col_class" value="1">
                            <label class="choise-class">{$itemHidden|escape:'html':'UTF-8'}</label>
                        </li>
                        {/foreach}
                    </ul>
                </p>
            </div>
        </div>
    {/if}
    {if $input.type == 'color_lang'}
        <div class="row">
            {foreach from=$languages item=language}
                    {if $languages|count > 1}
                            <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                    {/if}
                            <div class="col-lg-6">
                                <div class="col-md-4">
                                    <a href="javascript:void(0)" class="btn btn-default btn-update-slider">
                                                <i class="icon-upload"></i> {l s='Select slider background' mod='leobootstrapmenu'}
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                            <input type="color"
                                            data-hex="true"
                                            {if isset($input.class)}class="{$input.class|escape:'html':'UTF-8'}"
                                            {else}class="color mColorPickerInput"{/if}
                                            name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}"
                                            value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
                                    </div>
                                </div>
                            </div>
                    {if $languages|count > 1}
                            <div class="col-lg-2">
                                    <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                            {$language.iso_code}{* HTML form , no escape necessary *}
                                            <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                            {foreach from=$languages item=lang}
                                            <li><a href="javascript:hideOtherLanguage({$lang.id_lang|intval});" tabindex="-1">{$lang.name|escape:'html':'UTF-8'}</a></li>
                                            {/foreach}
                                    </ul>
                            </div>
                    {/if}
                    {if $languages|count > 1}
                            </div>
                    {/if}
            {/foreach}
        </div>
    {/if}
	{if $input.type == 'leo_switch'}
		<div class="col-lg-{if isset($input.col)}{$input.col|intval}{else}9{/if} {if !isset($input.label)}col-lg-offset-3{/if}">
			<span class="switch prestashop-switch fixed-width-lg {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}">
				{foreach $input.values as $value}
				<input type="radio" name="{$input.name|escape:'html':'UTF-8'}"{if $value.value == 1} id="{$input.name|escape:'html':'UTF-8'}_on"{else} id="{$input.name|escape:'html':'UTF-8'}_off"{/if} value="{$value.value}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>
				{strip}
				<label {if $value.value == 1} for="{$input.name|escape:'html':'UTF-8'}_on"{else} for="{$input.name|escape:'html':'UTF-8'}_off"{/if}>
					{if $value.value == 1}
						{l s='Yes' mod='leobootstrapmenu'}
					{else}
						{l s='No' mod='leobootstrapmenu'}
					{/if}
				</label>
				{/strip}
				{/foreach}
				<a class="slide-button btn"></a>
			</span>
				{if isset($input.leo_desc) && !empty($input.leo_desc)}
					<p class="help-block">
						{if is_array($input.leo_desc)}
							{foreach $input.leo_desc as $p}
								{if is_array($p)}
									<span id="{$p.id|intval}">{$p.text}{* HTML form , no escape necessary *}</span><br />
								{else}
									{$p}{* HTML form , no escape necessary *}<br />
								{/if}
							{/foreach}
						{else}
							{$input.leo_desc}{* HTML form , no escape necessary *}
						{/if}
					</p>
				{/if}
		</div>
	{/if}
    {$smarty.block.parent}
{/block}