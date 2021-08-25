{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if isset($fields.title)}<h3>{$fields.title}</h3>{/if}
{if $show_toolbar}
    {include file="{$frame_local_path}template/toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
{/if}
{block name="defaultForm"}
<form id="{if isset($fields.form.form.id_form)}{$fields.form.form.id_form|escape:'html':'UTF-8'}{else}{if $table == null}configuration_form{else}{$table}_form{/if}{/if}" class="defaultForm {$name_controller} form-horizontal" action="{$current}&amp;token={$token}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style}"{/if} novalidate>
	{if $form_id}
		<input type="hidden" name="{$identifier}" id="{$identifier}" value="{$form_id}" />
	{/if}
	{if !empty($submit_action)}
		<input type="hidden" name="{$submit_action}" value="1" />
	{/if}

	{foreach $fields as $f => $fieldset}
		{block name="fieldset"}
		<div id="fieldset_{$f}" class="portlet">
            {if isset($fieldset.form.legend)}
            {block name="legend"}
                <div class="portlet-title">
                    <div class="caption">
                        {if isset($fieldset.form.legend.image)}<img src="{$fieldset.form.legend.image}" alt="{$fieldset.form.legend.title|escape:'html':'UTF-8'}" />{/if}
                        {if isset($fieldset.form.legend.icon)}<i class="{$fieldset.form.legend.icon}"></i>{/if}
                        {$fieldset.form.legend.title}
                    </div>

                    <div class="tools">
                        {if isset($show_cancel_button) && $show_cancel_button}
                            <a href="{$back_url}" class="btn btn-default btn-sm" onclick="window.history.back()">
                                <i class="icon-sm process-icon-back"></i> {l s='Back' mod='inixframe'}
                            </a>
                        {/if}
                    </div>
                </div>
            {/block}
            {/if}
            <div class="portlet-body form">
            {if isset($fieldset.form.description) && $fieldset.form.description}
            <div class="row">
                <div class="col-md-10 col-md-offset-1 margin-top-10">
                    <div class="note note-info">{$fieldset.form.description}</div>
                </div>
            </div>
            {/if}
            <div class="form-body">
			{foreach $fieldset.form as $key => $field }

				{if $key == 'input'}
					{foreach $field as $input}
                        {if isset($dependency) AND isset($dependency.dependants[$input.name])}
                            <div id="{$input.name}" style="display: none">
                        {/if}
						{block name="input_row"}
						<div class="form-group {if isset($input.form_group_class)} {$input.form_group_class} {/if}{if $input.type == 'hidden'}hidden{/if}" {if $input.name == 'id_state'}id="contains_states"{if !$contains_states}style="display:none;"{/if}{/if}>
						{if $input.type == 'hidden'}
							<input type="hidden" name="{$input.name}" id="{$input.name}" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
						{else}
							{block name="label"}
								{if isset($input.label)}
									<label for="{if isset($input.id)}{$input.id}{if isset($input.lang) AND $input.lang}_{$current_id_lang}{/if}{else}{$input.name}{if isset($input.lang) AND $input.lang}_{$current_id_lang}{/if}{/if}" class="control-label col-lg-3 {if isset($input.required) && $input.required && $input.type != 'radio'}required{/if}">
										{if isset($input.hint)}
										<span class="label-tooltip label label-info" data-toggle="tooltip" data-html="true"
											title="{if is_array($input.hint)}
													{foreach $input.hint as $hint}
														{if is_array($hint)}
															{$hint.text}
														{else}
															{$hint}
														{/if}
													{/foreach}
												{else}
													{$input.hint}
												{/if}">
										{/if}
										{$input.label}
										{if isset($input.hint)}
										</span>
										{/if}
									</label>
								{/if}
							{/block}

							{block name="field"}
								<div class="col-lg-{if isset($input.col)}{$input.col|intval}{else}6{/if} {if !isset($input.label)}col-lg-offset-3{/if}">
								{block name="input"}
								{if $input.type == 'text' || $input.type == 'tags'}
									{if isset($input.lang) AND $input.lang}

									{foreach $languages as $language}
										{assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
										{if $languages|count > 1}
										<div class="row">
                                        <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
											<div class="col-lg-9">
										{/if}

												{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
												<div class="input-group">
												{/if}
												{if isset($input.maxchar)}
												<span id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter" class="input-group-addon">
													<span class="text-count-down">{$input.maxchar}</span>
												</span>
												{/if}
												{if isset($input.prefix)}
													<span class="input-group-addon">
													  {$input.prefix}
													</span>
													{/if}
												<input type="text"
													id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"
													name="{$input.name}_{$language.id_lang}"
													class="form-control {if $input.type == 'tags'}tagsinput {/if}{if isset($input.class)}{$input.class}{/if}"
													value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
													onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
													{if isset($input.size)} size="{$input.size}"{/if}
													{if isset($input.maxchar)} data-maxchar="{$input.maxchar}"{/if}
													{if isset($input.maxlength)} maxlength="{$input.maxlength}"{/if}
													{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
													{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
													{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
													{if isset($input.required) && $input.required } required="required" {/if} />
													{if isset($input.suffix)}
													<span class="input-group-addon">
													  {$input.suffix}
													</span>
													{/if}
												{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
												</div>
												{/if}
										{if $languages|count > 1}
											</div>
											<div class="col-lg-2">
												<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
													{$language.iso_code}
													<i class="icon-caret-down"></i>
												</button>
												<ul class="dropdown-menu">
													{foreach from=$languages item=language}
													<li><a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a></li>
													{/foreach}
												</ul>
											</div>
                                            </div>
										</div>
										{/if}
									{/foreach}
									{if isset($input.maxchar)}
									<script type="text/javascript">
									$(document).ready(function(){
									{foreach from=$languages item=language}
										countDown($("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"), $("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter"));
									{/foreach}
									});
									</script>
									{/if}

									{else}

										{assign var='value_text' value=$fields_value[$input.name]}
										{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
										<div class="input-group">
										{/if}
										{if isset($input.maxchar)}
										<span id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$input.maxchar}</span></span>
										{/if}
										{if isset($input.prefix)}
										<span class="input-group-addon">
										  {$input.prefix}
										</span>
										{/if}
										<input type="text"
											name="{$input.name}"
											id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
											value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
											class="form-control {if $input.type == 'tags'}tagsinput {/if}{if isset($input.class)}{$input.class}{/if}"
											{if isset($input.size)} size="{$input.size}"{/if}
											{if isset($input.maxchar)} data-maxchar="{$input.maxchar}"{/if}
											{if isset($input.maxlength)} maxlength="{$input.maxlength}"{/if}
											{if isset($input.class)} class="{$input.class}"{/if}
											{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
											{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
											{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
											{if isset($input.required) && $input.required } required="required" {/if}
											/>
										{if isset($input.suffix)}
										<span class="input-group-addon">
										  {$input.suffix}
										</span>
										{/if}

										{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
										</div>
										{/if}
										{if isset($input.maxchar)}
										<script type="text/javascript">
										$(document).ready(function(){
											countDown($("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"), $("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter"));
										});
										</script>
										{/if}
									{/if}
								{elseif $input.type == 'textbutton'}
									{assign var='value_text' value=$fields_value[$input.name]}


                                        <div class="input-group">
										{if isset($input.maxchar)}

											<span id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter" class="input-group-addon">
												<span class="text-count-down">{$input.maxchar}</span>
											</span>
										{/if}
										<input type="text"
											name="{$input.name}"
											id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
											value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
											class="form-control {if isset($input.class)}{$input.class}{/if}"
											{if isset($input.size)} size="{$input.size}"{/if}
											{if isset($input.maxchar)} data-maxchar="{$input.maxchar}"{/if}
											{if isset($input.maxlength)} maxlength="{$input.maxlength}"{/if}
											{if isset($input.class)} class="{$input.class}"{/if}
											{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
											{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
											{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if} />
										{if isset($input.suffix)}{$input.suffix}{/if}
									       <span class="input-group-btn">
											<button type="button" class="btn btn-default{if isset($input.button.attributes['class'])} {$input.button.attributes['class']}{/if}{if isset($input.button.class)} {$input.button.class}{/if}"
												{foreach from=$input.button.attributes key=name item=value}
													{if $name|lower != 'class'}
													 {$name}="{$value}"
													{/if}
												{/foreach} >
												{$input.button.label}
											</button>
                                            </span>
                                        </div>


									{if isset($input.maxchar)}
									<script type="text/javascript">
										$(document).ready(function() {
											countDown($("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"), $("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter"));
										});
									</script>
									{/if}
								{elseif $input.type == 'select' OR $input.type == 'select2'}

									{if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
										{$input.empty_message}
										{$input.required = false}
										{$input.desc = null}
									{else}
										<select name="{$input.name|escape:'html':'utf-8'}{if isset($input.multiple)}[]{/if}"
												class="form-control {if $input.type == 'select2'}select2me{/if} {if isset($input.class)}{$input.class|escape:'html':'utf-8'}{/if} fixed-width-xxl"
												id="{if isset($input.id)}{$input.id|escape:'html':'utf-8'}{else}{$input.name|escape:'html':'utf-8'}{/if}"
												{if isset($input.multiple)}multiple="multiple" {/if}
												{if isset($input.size)}size="{$input.size|escape:'html':'utf-8'}"{/if}
												{if isset($input.onchange)}onchange="{$input.onchange|escape:'html':'utf-8'}"{/if}>
											{if isset($input.options.default)}
												<option value="{$input.options.default.value|escape:'html':'utf-8'}">{$input.options.default.label|escape:'html':'utf-8'}</option>
											{/if}
											{if isset($input.options.optiongroup)}
												{foreach $input.options.optiongroup.query AS $optiongroup}
													<optgroup label="{$optiongroup[$input.options.optiongroup.label]}">
														{foreach $optiongroup[$input.options.options.query] as $option}
															<option value="{$option[$input.options.options.id]}"
																{if isset($input.multiple)}
																	{foreach $fields_value[$input.name] as $field_value}
																		{if $field_value == $option[$input.options.options.id]}selected="selected"{/if}
																	{/foreach}
																{else}
																	{if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
																{/if}
															>{$option[$input.options.options.name]}</option>
														{/foreach}
													</optgroup>
												{/foreach}
											{else}
												{foreach $input.options.query AS $option}
													{if is_object($option)}
														<option value="{$option->$input.options.id}"
															{if isset($input.multiple)}
																{foreach $fields_value[$input.name] as $field_value}
																	{if $field_value == $option->$input.options.id}
																		selected="selected"
																	{/if}
																{/foreach}
															{else}
																{if $fields_value[$input.name] == $option->$input.options.id}
																	selected="selected"
																{/if}
															{/if}
														>{$option->$input.options.name}</option>
													{elseif $option == "-"}
														<option value="">-</option>
													{else}
														<option value="{$option[$input.options.id]}"
															{if isset($input.multiple)}
																{foreach $fields_value[$input.name] as $field_value}
																	{if $field_value == $option[$input.options.id]}
																		selected="selected"
																	{/if}
																{/foreach}
															{else}
																{if $fields_value[$input.name] == $option[$input.options.id]}
																	selected="selected"
																{/if}
															{/if}
														>{$option[$input.options.name]}</option>

													{/if}
												{/foreach}
											{/if}
										</select>

									{/if}
								{elseif $input.type == 'radio'}
									{foreach $input.values as $value}
										<div class="radio-list {if isset($input.class)}"{$input.class}"{/if}">
											<label for="{$value.id}">
											<input type="radio"	name="{$input.name}" id="{$value.id}" value="{$value.value|escape:'html':'UTF-8'}"
												{if $fields_value[$input.name] == $value.value}checked="checked"{/if}
												{if isset($input.disabled) && $input.disabled}disabled="disabled"{/if} />
												{$value.label}
											</label>
										</div>
										{if isset($value.p) && $value.p}<p class="help-block">{$value.p}</p>{/if}
									{/foreach}

								{elseif $input.type == 'switch'}
									<div "col-lg-9">
										<span class="switch bootstrap-switch fixed-width-lg">
											{foreach $input.values as $value}
											<input
												type="radio"

												name="{$input.name}"
												{if $value.value == 1}
													id="{$input.name}_on"
												{else}
													id="{$input.name}_off"
												{/if}
												value="{$value.value}"
												{if $fields_value[$input.name] == $value.value}checked="checked"{/if}
												{if isset($input.disabled) && $input.disabled}disabled="disabled"{/if}
											/>
											<label
                                                    class="radioCheck"
												{if $value.value == 1}
													for="{$input.name}_on"
												{else}
													for="{$input.name}_off"
												{/if}
											>
												{if $value.value == 1}
													{l s='Yes' mod='inixframe'}
												{else}
													{l s='No' mod='inixframe'}
												{/if}
											</label>
											{/foreach}
											<a class="slide-button btn"></a>
										</span>
									</div>
								{elseif $input.type == 'textarea'}

									{assign var=use_textarea_autosize value=true}
									{if isset($input.lang) AND $input.lang}
                                        <div class="row">
                                        {foreach $languages as $language}
                                        {if $languages|count > 1}

                                            <div class="translatable-field lang-{$language.id_lang}"  {if $language.id_lang != $defaultFormLanguage}style="display:none;"{/if}>

                                        {/if}
                                            <div class="col-lg-9">
                                                <textarea name="{$input.name}_{$language.id_lang}" class="form-control {if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte {if isset($input.class)}{$input.class}{/if}{else}{if isset($input.class)}{$input.class}{else}textarea-autosize{/if}{/if}" >{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}</textarea>
                                            </div>
                                        {if $languages|count > 1}
                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                                    {$language.iso_code}
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    {foreach from=$languages item=language}
                                                    <li>
                                                        <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                                    </li>
                                                    {/foreach}
                                                </ul>
                                            </div>
                                            </div>

                                        {/if}

                                        {/foreach}
                                        </div>
									{else}
										<textarea name="{$input.name}" id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}" {if isset($input.cols)}cols="{$input.cols}"{/if} {if isset($input.rows)}rows="{$input.rows}"{/if} class="form-control {if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte {if isset($input.class)}{$input.class}{/if}{else}textarea-autosize{/if}">{$fields_value[$input.name]|escape:'html':'UTF-8'}</textarea>
									{/if}

								{elseif $input.type == 'checkbox'}
                                    {if isset($input.expand)}
                                        <a class="btn btn-default show_checkbox{if $input.expand.default == 'hide'} hidden {/if}" href="#">
                                            <i class="icon-{$input.expand.show.icon}"></i>
                                            {$input.expand.show.text}
                                            {if isset($input.expand.print_total) && $input.expand.print_total > 0}
                                                <span class="badge badge-info">{$input.expand.print_total}</span>
                                            {/if}
                                        </a>
                                        <a class="btn btn-default hide_checkbox{if $input.expand.default == 'show'} hidden {/if}" href="#">
                                            <i class="icon-{$input.expand.hide.icon}"></i>
                                            {$input.expand.hide.text}
                                            {if isset($input.expand.print_total) && $input.expand.print_total > 0}
                                                <span class="badge badge-info">{$input.expand.print_total}</span>
                                            {/if}
                                        </a>
                                    {/if}
									{foreach $input.values.query as $value}
										{assign var=id_checkbox value=$input.name|cat:'_'|cat:$value[$input.values.id]}
										<div class="checkbox margin-left-20 {if isset($input.expand) && $input.expand.default == 'show'} hidden {/if}">
											<label for="{$id_checkbox}">
												<input type="checkbox"
													name="{$id_checkbox}"
													id="{$id_checkbox}"
													class="{if isset($input.class)}{$input.class}{/if}"
													{if isset($value.val)}value="{$value.val|escape:'html':'UTF-8'}"{/if}
													{if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]}checked="checked"{/if} />
												{$value[$input.values.name]}
											</label>
										</div>
									{/foreach}

								{elseif $input.type == 'change-password'}
									<div class="row">
										<div class="col-lg-12">
											<button type="button" id="{$input.name}-btn-change" class="btn btn-default">
												<i class="icon-lock"></i>
												{l s='Change password...' mod='inixframe'}
											</button>
											<div id="{$input.name}-change-container" class="form-password-change well hide">
												<div class="form-group ">
													<label for="old_passwd" class="control-label col-lg-2 required">
														{l s='Current password' mod='inixframe'}
													</label>
													<div class="col-lg-10">
														<div class="input-group fixed-width-lg">
															<span class="input-group-addon">
																<i class="icon-unlock"></i>
															</span>
															<input type="password" id="old_passwd" name="old_passwd" class="form-control" value="" required="required">
														</div>
													</div>
												</div>
												<hr>
												<div class="form-group">
													<label for="{$input.name}" class="required control-label col-lg-2">
														<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="Minimum of 8 characters.">
															{l s='New password' mod='inixframe'}
														</span>
													</label>
													<div class="col-lg-9">
														<div class="input-group fixed-width-lg">
															<span class="input-group-addon">
																<i class="icon-key"></i>
															</span>
															<input type="password"
																id="{$input.name}"
																name="{$input.name}"
																class="form-control {if isset($input.class)}{$input.class}{/if}"
																value=""
																required="required"/>
														</div>
														<span id="{$input.name}-output"></span>
													</div>
												</div>
												<div class="form-group">
													<label for="{$input.name}2" class="required control-label col-lg-2">
														{l s='Confirm password' mod='inixframe'}
													</label>
													<div class="col-lg-4">
														<div class="input-group fixed-width-lg">
															<span class="input-group-addon">
																<i class="icon-key"></i>
															</span>
															<input type="password"
																id="{$input.name}2"
																name="{$input.name}2"
																class="form-control {if isset($input.class)}{$input.class}{/if}"
																value=""/>
														</div>
													</div>
												</div>
												<div class="form-group">
													<div class="col-lg-6 col-lg-offset-2">
                                                        <div class="input-group">
														<input type="text" class="form-control  " id="{$input.name}-generate-field" disabled="disabled">
														<span class="input-group-btn">
                                                            <button type="button" id="{$input.name}-generate-btn" class="btn btn-default">
															<i class="icon-random"></i>
															{l s='Generate password' mod='inixframe'}
														</button>
                                                            </span>
                                                            </div>
													</div>
												</div>
												<div class="form-group">
													<div class="col-lg-10 col-lg-offset-2">
														<p class="checkbox">
															<label for="{$input.name}-checkbox-mail">
																<input name="passwd_send_email" id="{$input.name}-checkbox-mail" type="checkbox" checked="checked">
																{l s='Send me this new password by Email' mod='inixframe'}
															</label>
														</p>
													</div>
												</div>
												<div class="row">
													<div class="col-lg-12">
														<button type="button" id="{$input.name}-cancel-btn" class="btn btn-default">
															<i class="icon-remove"></i>
															{l s='Cancel' mod='inixframe'}
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>
									<script>
										$(function(){
											var $oldPwd = $('#old_passwd');
											var $passwordField = $('#{$input.name}');
											var $output = $('#{$input.name}-output');
											var $generateBtn = $('#{$input.name}-generate-btn');
											var $generateField = $('#{$input.name}-generate-field');
											var $cancelBtn = $('#{$input.name}-cancel-btn');

											var feedback = [
												{ badge: 'text-danger', text: '{l s='Invalid' mod='inixframe' js=1}' },
												{ badge: 'text-warning', text: '{l s='Okay' mod='inixframe' js=1}' },
												{ badge: 'text-success', text: '{l s='Good' mod='inixframe' js=1}' },
												{ badge: 'text-success', text: '{l s='Fabulous' mod='inixframe' js=1}' }
											];
											$.passy.requirements.length.min = 8;
											$.passy.requirements.characters = 'DIGIT';
											$passwordField.passy(function(strength, valid) {
												$output.text(feedback[strength].text);
												$output.removeClass('text-danger').removeClass('text-warning').removeClass('text-success');
												$output.addClass(feedback[strength].badge);
												if (valid){
													$output.show();
												}
    											else {
    												$output.hide();
    											}
											});
											var $container = $('#{$input.name}-change-container');
											var $changeBtn = $('#{$input.name}-btn-change');
											var $confirmPwd = $('#{$input.name}2');

											$changeBtn.on('click',function(){
												$container.removeClass('hide');
												$changeBtn.addClass('hide');
											});
											$generateBtn.click(function() {
												$generateField.passy( 'generate', 8 );
												var generatedPassword = $generateField.val();
												$passwordField.val(generatedPassword);
												$confirmPwd.val(generatedPassword);
											});
											$cancelBtn.on('click',function() {
												$container.find("input").val("");
												$container.addClass('hide');
												$changeBtn.removeClass('hide');
											});

											$.validator.addMethod('password_same', function(value, element) {
												return $passwordField.val() == $confirmPwd.val();
											}, '{l s='Invalid password confirmation' mod='inixframe' js=1}');

											$('#employee_form').validate({
												rules: {
													"email": {
														email: true
													},
													"{$input.name}" : {
														minlength: 8
													},
													"{$input.name}2": {
														password_same: true
													},
													"old_passwd" : {},
												},
												// override jquery validate plugin defaults for bootstrap 3
												highlight: function(element) {
													$(element).closest('.form-group').addClass('has-error');
												},
												unhighlight: function(element) {
													$(element).closest('.form-group').removeClass('has-error');
												},
												errorElement: 'span',
												errorClass: 'help-block',
												errorPlacement: function(error, element) {
													if(element.parent('.input-group').length) {
														error.insertAfter(element.parent());
													} else {
														error.insertAfter(element);
													}
												}
											});
										});
									</script>
								{elseif $input.type == 'password'}
									<div class="input-group ">
										<span class="input-group-addon">
											<i class="icon-key"></i>
										</span>
										<input type="password"
											id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
											name="{$input.name}"
											class="fixed-width-lg form-control {if isset($input.class)}{$input.class}{/if}"
											value=""
											{if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if}
											{if isset($input.required) && $input.required } required="required" {/if} />
									</div>

								{elseif $input.type == 'birthday'}
								<div class="row">
									{foreach $input.options as $key => $select }
									<div class="col-lg-3 margin-right-10">
										<select name="{$key}" class="fixed-width-lg form-control {if isset($input.class)}{$input.class}{/if}">
											<option value="">-</option>
											{if $key == 'months'}
												{*
													This comment is useful to the translator tools /!\ do not remove them
													{l s='January' mod='inixframe'}
													{l s='February' mod='inixframe'}
													{l s='March' mod='inixframe'}
													{l s='April' mod='inixframe'}
													{l s='May' mod='inixframe'}
													{l s='June' mod='inixframe'}
													{l s='July' mod='inixframe'}
													{l s='August' mod='inixframe'}
													{l s='September' mod='inixframe'}
													{l s='October' mod='inixframe'}
													{l s='November' mod='inixframe'}
													{l s='December' mod='inixframe'}
												*}
												{foreach $select as $k => $v}
													<option value="{$k}" {if isset($fields_value[$key]) AND $k == $fields_value[$key]}selected="selected"{/if}>{l s=$v}</option>
												{/foreach}
											{else}
												{foreach $select as $v}
													<option value="{$v}" {if isset($fields_value[$key]) AND $v == $fields_value[$key]}selected="selected"{/if}>{$v}</option>
												{/foreach}
											{/if}

										</select>
									</div>
									{/foreach}
								</div>
								{elseif $input.type == 'group'}
									{assign var=groups value=$input.values}
                                    {assign var=group_input_name value=$input.name}
									{include file="template/helpers/form/form_group.tpl"}
								{elseif $input.type == 'shop'}
									{$input.html}
								{elseif $input.type == 'categories' OR $input.type == 'categories_select'}
									{$categories_tree}
								{elseif $input.type == 'file'}
									{$input.file}
								{elseif $input.type == 'asso_shop' && isset($asso_shop) && $asso_shop}
									{$asso_shop}
								{elseif $input.type == 'color'}
                                    <div class="row">
                                    <div class="col-md-3 col-lg-3">
                                        <div class="input-group color colorpicker-default"
                                             data-color="{$fields_value[$input.name]|escape:'html':'UTF-8'}" data-color-format="hex">
                                            <input type="text" class="form-control"
                                                   value="{$fields_value[$input.name]|escape:'html':'UTF-8'}"
                                                   name="{$input.name}">
												<span class="input-group-btn">
													<button class="btn btn-default" type="button"><i
                                                                style="background-color:{$fields_value[$input.name]|escape:'html':'UTF-8'} ">
                                                            &nbsp;&nbsp;&nbsp;&nbsp;</i></button>
												</span>
                                        </div>
                                    </div>
                                </div>
								{elseif $input.type == 'date'}

										<div class="input-group col-lg-4">
											<input
												id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
												type="text"
												data-hex="true"
                                                data-date-format="yyyy-mm-dd"
                                                class="form-control
                                                {if isset($input.class)}{$input.class}
												{else}date-picker{/if}"
												name="{$input.name}"
												value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
											<span class="input-group-addon">
												<i class="icon-calendar-empty"></i>
											</span>
										</div>
                                {elseif $input.type == 'date_range'}

                                    <div class="input-group col-lg-4 date-picker input-daterange"   data-date-format="yyyy-mm-dd">
                                        <input
                                                id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
                                                type="text"
                                                data-hex="true"
                                                class="form-control
                                                {if isset($input.class)}{$input.class}{/if}"
                                                name="{$input.name}_from"
                                                value="{if isset($fields_value["{$input.name}_from"])}{$fields_value["{$input.name}_from"]|escape:'html':'UTF-8'}{/if}" />
											<span class="input-group-addon">
												{l s='to' mod='inixframe'}
											</span>
                                        <input
                                                id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
                                                type="text"
                                                data-hex="true"
                                                class="form-control
                                                {if isset($input.class)}{$input.class}{/if}"
                                                name="{$input.name}_to"
                                                value="{if isset($fields_value["{$input.name}_to"])}{$fields_value["{$input.name}_to"]|escape:'html':'UTF-8'}{/if}" />
                                    </div>

								{elseif $input.type == 'datetime'}

										<div class="input-group col-lg-4 date">
											<input
												id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
												type="text"
												data-hex="true"
                                                class="form-control
                                                {if isset($input.class)}{$input.class}
												{else}mydatetime-picker{/if}"
												name="{$input.name}"
												value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
											<span class="input-group-addon">

												<i class="icon-calendar-empty"></i>

											</span>
										</div>

								{elseif $input.type == 'free'}
                                    {$input.name}
								{elseif $input.type == 'html'}
                                    {$input.html}
                                {elseif $input.type =='controllers'}

                                <select name="{$input.name}{if isset($input.multiple) AND $input.multiple}[]{/if}"
                                        class="form-control select2me {if isset($input.class)}{$input.class}{/if}"
                                        id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
                                        {if isset($input.multiple) AND $input.multiple}multiple="multiple"{/if}
                                        {if isset($input.size)}size="{$input.size}"{/if}
                                        {if isset($input.onchange)}onchange="{$input.onchange}"{/if}>
                                        {foreach from=$controllers key=page item=controller}
                                            <option value="{$page}" {if in_array($page,$input.selected_controllers)}selected{/if}>{$page}</option>
                                        {/foreach}
                                </select>
                                {elseif $input.type == 'products'}


                                        <input type="hidden" name="{$input.name}" id="inputProducts"
                                               value="{foreach from=$products item=product}{$product.id_product}-{/foreach}"/>
                                        <input type="hidden" name="nameProducts" id="nameProducts"
                                               value="{foreach from=$products item=product}{$product.name|escape:'htmlall':'UTF-8'}{if !empty($product.reference)} (ref: {$product.reference}){/if}¤{/foreach}"/>

                                        <div class="row">
                                            <div class="input-icon left col-lg-6">
                                                <i class="icon-info"></i>
                                                <input type="text" value="" class="form-control help-tooltip" data-toggle="tooltip"
                                                       data-original-title=" {l s='Begin typing the first letters of the product name or reference number, then select the product from the drop-down list.' mod='inixframe'}"  id="product_autocomplete_input"/>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <p class="help-block">
                                                    {l s='(Do not forget to save afterward)' mod='inixframe'}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-8">
                                                <ul id="divProducts" class="list-group">
                                                    {* @todo : donot use 3 foreach, but assign var *}
                                                    {foreach from=$products item=product}
                                                        <li class="list-group-item">
                                                            {$product.name|escape:'htmlall':'UTF-8'}{if !empty($product.reference)} (ref: {$product.reference}){/if}
                                                            <span class="delProduct btn btn-danger btn-xs pull-right"
                                                                  name="{$product.id_product}" style="cursor: pointer;">
                                                                    <i class="icon-trash"></i>
                                                                </span>
                                                        </li>
                                                    {/foreach}
                                                </ul>
                                            </div>
                                        </div>



								{/if}

								{/block}{* end block input *}
								{block name="description"}
									{if isset($input.desc) && !empty($input.desc)}
										<p class="help-block">
											{if is_array($input.desc)}
												{foreach $input.desc as $p}
													{if is_array($p)}
														<span id="{$p.id}">{$p.text}</span><br />
													{else}
														{$p}<br />
													{/if}
												{/foreach}
											{else}
												{$input.desc}
											{/if}
										</p>
									{/if}
								{/block}
								</div>
							{/block}{* end block field *}
						{/if}
						</div>
                        {if isset($dependency) AND isset($dependency.dependants[$input.name])}
                             </div>
                        {/if}
						{/block} {* /block input_row *}
					{/foreach}
				{elseif $key == 'desc'}
                   <div class="row">
                       <div class="col-lg-6 col-lg-offset-3">
					<div class="note note-info ">
						{if is_array($field)}
							{foreach $field as $k => $p}
								{if is_array($p)}
									<span{if isset($p.id)} id="{$p.id}"{/if}>{$p.text}</span><br />
								{else}
									{$p}
									{if isset($field[$k+1])}<br />{/if}
								{/if}
							{/foreach}
						{else}
							{$field}
						{/if}
					</div>
                       </div>
                    </div>
				{/if}
				{block name="other_input"}{/block}
			{/foreach}
            </div>
            {block name="footer"}

				{if isset($fieldset['form']['submit']) || isset($fieldset['form']['buttons'])}
					<div class="form-actions">
                        <div class="row">
                        <div class="col-lg-9 col-lg-offset-3">
						{if isset($fieldset['form']['submit']) && !empty($fieldset['form']['submit'])}
						<button
							type="submit"
							value="1"
							id="{if isset($fieldset['form']['submit']['id'])}{$fieldset['form']['submit']['id']}{else}{$table}_form_submit_btn{/if}"
							name="{if isset($fieldset['form']['submit']['name'])}{$fieldset['form']['submit']['name']}{else}{$submit_action}{/if}{if isset($fieldset['form']['submit']['stay']) && $fieldset['form']['submit']['stay']}AndStay{/if}"
							class="{if isset($fieldset['form']['submit']['class'])}{$fieldset['form']['submit']['class']}{else}btn btn-primary btn-sm{/if}"
							>
							<i class="icon-sm {if isset($fieldset['form']['submit']['icon'])}{$fieldset['form']['submit']['icon']}{else}process-icon-save{/if}"></i> {$fieldset['form']['submit']['title']}
						</button>
						{/if}
						{if isset($show_cancel_button) && $show_cancel_button}
						<a href="{$back_url}" class="btn btn-default btn-sm" onclick="window.history.back()">
							<i class="icon-sm process-icon-cancel"></i> {l s='Cancel' mod='inixframe'}
						</a>
						{/if}
						{if isset($fieldset['form']['reset'])}
						<button
							type="reset"
							id="{if isset($fieldset['form']['reset']['id'])}{$fieldset['form']['reset']['id']}{else}{$table}_form_reset_btn{/if}"
							class="{if isset($fieldset['form']['reset']['class'])}{$fieldset['form']['reset']['class']}{else}btn btn-default btn-sm{/if}"
							>
							{if isset($fieldset['form']['reset']['icon'])}<i class="icon-sm {$fieldset['form']['reset']['icon']}"></i> {/if} {$fieldset['form']['reset']['title']}
						</button>
						{/if}
						{if isset($fieldset['form']['buttons'])}
						{foreach from=$fieldset['form']['buttons'] item=btn key=k}
							<button type="{if isset($btn['type'])}{$btn['type']}{else}button{/if}" {if isset($btn['id'])}id="{$btn['id']}"{/if} class="btn btn-default btn-sm{if isset($btn['class'])} {$btn['class']}{/if}" name="{if isset($btn['name'])}{$btn['name']}{else}submitInixOptions{$table}{/if}"{if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}>{if isset($btn['icon'])}<i class="icon-sm {$btn['icon']}" ></i> {/if}{$btn.title}</button>
						{/foreach}
						{/if}
                       </div>
                        </div>
					</div>
				{/if}
			{/block}

            </div>

        </div>
		{/block}
		{block name="other_fieldsets"}{/block}
	{/foreach}
</form>
{/block}
{block name="after"}{/block}

{if isset($tinymce) && $tinymce}
<script type="text/javascript">
	var iso = '{$iso|addslashes}';
	var pathCSS = '{$smarty.const._THEME_CSS_DIR_|addslashes}';
	var ad = '{$ad|addslashes}';

	$(document).ready(function(){
		{block name="autoload_tinyMCE"}
			tinySetup({
				editor_selector :"autoload_rte"
			});
		{/block}
	});
</script>
{/if}
{if $firstCall}
	<script type="text/javascript">
		var module_dir = '{$smarty.const._MODULE_DIR_}';
		var id_language = {$defaultFormLanguage};
		var languages = new Array();
		var vat_number = {if $vat_number}1{else}0{/if};
		// Multilang field setup must happen before document is ready so that calls to displayFlags() to avoid
		// precedence conflicts with other document.ready() blocks
		{foreach $languages as $k => $language}
			languages[{$k}] = {
				id_lang: {$language.id_lang},
				iso_code: '{$language.iso_code}',
				name: '{$language.name}',
				is_default: '{$language.is_default}'
			};
		{/foreach}
		// we need allowEmployeeFormLang var in ajax request
		allowEmployeeFormLang = {$allowEmployeeFormLang|intval};
		displayFlags(languages, id_language, allowEmployeeFormLang);

		$(document).ready(function() {

            $(".show_checkbox").click(function () {
                $(this).addClass('hidden')
                $(this).siblings('.checkbox').removeClass('hidden');
                $(this).siblings('.hide_checkbox').removeClass('hidden');
                return false;
            });
            $(".hide_checkbox").click(function () {
                $(this).addClass('hidden')
                $(this).siblings('.checkbox').addClass('hidden');
                $(this).siblings('.show_checkbox').removeClass('hidden');
                return false;
            });

			{if isset($fields_value.id_state)}
				if ($('#id_country') && $('#id_state'))
				{
					ajaxStates({$fields_value.id_state});
					$('#id_country').change(function() {
						ajaxStates();
					});
				}
			{/if}


			{if isset($use_textarea_autosize)}
			$(".textarea-autosize").autosize();
			{/if}
		});
	state_token = '{getAdminToken tab='AdminStates'}';

        {if isset($dependency)}
            $("#{$dependency.switch}").change(function(){
                $('.showedDependency').hide('fast').removeClass('showedDependency');
                $("#"+$(this).val()).show('fast').addClass('showedDependency');


            }).change();

        {/if}


	{block name="script"}{/block}
	</script>
{/if}
