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

<div class="leadin">{block name="leadin"}{/block}</div>

<script type="text/javascript">
    id_language = Number({$current_id_lang});
</script>
{block name="defaultOptions"}
    <form action="{$current}&amp;token={$token}"
          id="{if $table == null}configuration_form{else}{$table}_form{/if}"
          method="post"
          enctype="multipart/form-data" class="form-horizontal">

    {foreach from=$option_list key=category item=categoryData}
        {if isset($categoryData['top'])}{$categoryData['top']}{/if}
        <div class="portlet {if isset($categoryData['class'])}{$categoryData['class']}{/if}"
             id="{$table}_fieldset_{$category}">
        {* Options category title *}
        <div class="portlet-title">
            <div class="caption">
                <i class="{if isset($categoryData['icon'])}{$categoryData['icon']}{else}icon-cogs{/if}"></i>
                {if isset($categoryData['title'])}{$categoryData['title']}{else}{l s='Options' mod='inixframe'}{/if}
            </div>
        </div>
        <div class="portlet-body form">
        {* Category description *}

        {if (isset($categoryData['description']) && $categoryData['description'])}
            <div class="row">
                <div class="col-md-10 col-md-offset-1 margin-top-10">
                    <div class="note note-info">{$categoryData['description']}</div>
                </div>
            </div>
        {/if}
        {* Category info *}
        {if (isset($categoryData['info']) && $categoryData['info'])}
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <p class="well well-sm">{$categoryData['info']}</p>
                </div>
            </div>
        {/if}

        <div class="form-body">
        {if !$categoryData['hide_multishop_checkbox'] && $use_multishop}
            <div class="well clearfix">
                <label class="control-label col-lg-3">
                    <i class="icon-sitemap"></i> {l s='Multistore' mod='inixframe'}
                </label>

                <div class="col-lg-6">
                        <span class="switch bootstrap-switch fixed-width-lg">
                            <input type="radio" name="{$table}_multishop_{$category}"
                                   id="{$table}_multishop_{$category}_on" value="1"
                                   onclick="toggleAllMultishopDefaultValue($('#{$table}_fieldset_{$category}'), true)">
                            <label for="{$table}_multishop_{$category}_on">
                                {l s='Yes' mod='inixframe'}
                            </label>
                            <input type="radio" name="{$table}_multishop_{$category}"
                                   id="{$table}_multishop_{$category}_off" value="0" checked="checked"
                                   onclick="toggleAllMultishopDefaultValue($('#{$table}_fieldset_{$category}'), false)">
                            <label for="{$table}_multishop_{$category}_off">
                                {l s='No' mod='inixframe'}
                            </label>
                            <a class="slide-button btn"></a>
                        </span>

                    <div class="row">
                        <div class="col-lg-12">
                            <p class="help-block">
                                <strong>{l s='Check / Uncheck all' mod='inixframe'}</strong>
                                {l s='(If you are editing this page for several shops, some fields may be disabled. If you need to edit them, you will need to check the box for each field)' mod='inixframe'}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
        {foreach $categoryData['fields'] AS $key => $field}
            {if $field['type'] == 'hidden'}
                <input type="hidden" name="{$key}" value="{$field['value']}"/>
            {else}
                <div class="form-group {if isset($field.form_group_class)} {$field.form_group_class} {/if}">
                <div id="conf_id_{$key}" {if $field['is_invisible']} class="isInvisible"{/if}>
                {block name="label"}
                    {if isset($field['title']) && isset($field['hint'])}
                        <label class="control-label col-lg-3 {if isset($field['required']) && $field['required'] && $field['type'] != 'radio'}required{/if}"
                               for="{$key}">
                            {if !$categoryData['hide_multishop_checkbox'] && $field['multishop_default'] && empty($field['no_multishop_checkbox'])}
                                <input type="checkbox" name="multishopOverrideOption[{$key}]" value="1"
                                       {if !$field['is_disabled']}checked="checked"{/if}
                                       onclick="toggleMultishopDefaultValue(this, '{$key}')"/>
                            {/if}
                            <span title="" data-toggle="tooltip" class="label label-info label-tooltip"
                                  data-original-title="
                                                    {if is_array($field['hint'])}
                                                        {foreach $field['hint'] as $hint}
                                                            {if is_array($hint)}
                                                                {$hint.text}
                                                            {else}
                                                                {$hint}
                                                            {/if}
                                                        {/foreach}
                                                    {else}
                                                        {$field['hint']}
                                                    {/if}
                                                " data-html="true">
                                                    {$field['title']}
                                                </span>
                        </label>
                    {elseif isset($field['title'])}
                        <label class="control-label col-lg-3 {if isset($field['required']) && $field['required'] && $field['type'] != 'radio'}required{/if}"
                               for="{$key}">
                            {if !$categoryData['hide_multishop_checkbox'] && $field['multishop_default'] && empty($field['no_multishop_checkbox'])}
                                <input type="checkbox" name="multishopOverrideOption[{$key}]" value="1"
                                       {if !$field['is_disabled']}checked="checked"{/if}
                                       onclick="checkMultishopDefaultValue(this, '{$key}')"/>
                            {/if}
                            {$field['title']}
                        </label>
                    {/if}
                {/block}
                {block name="field"}
                    {block name="input"}
                        {if $field['type'] == 'select'}
                            <div class="col-lg-6">
                                {if $field['list']}
                                    <select class="form-control fixed-width-xxl"
                                            name="{$key}{if isset($field['multiple']) AND $field['multiple']}[]{/if}"{if isset($field['js'])} onchange="{$field['js']}"{/if}
                                            id="{$key}" {if isset($field['size'])} size="{$field['size']}"{/if}
                                            {if isset($field['multiple']) AND $field['multiple']}multiple="multiple"{/if}>
                                        {foreach $field['list'] AS $k => $option}
                                            <option value="{$option[$field['identifier']]}" {if ( $field['value'] == $option[$field['identifier']] ) OR (isset($field['is_array']) AND $field['is_array'] AND in_array($option[$field['identifier']], $field['value']))} selected="selected"{/if}>{$option['name']}</option>
                                        {/foreach}
                                    </select>
                                {elseif isset($field.empty_message)}
                                    <p>{$field.empty_message}</p>
                                {/if}
                            </div>
                        {elseif $field['type'] == 'select2'}
                            <div class="col-lg-6">
                                <select id="{$key}" name="{$key}{if isset($field['multiple']) AND $field['multiple']}[]{/if}" class="select2me fixed-width-xxl"  {if isset($field['multiple']) AND $field['multiple']}multiple="multiple"{/if}>
                                    {foreach $field['list'] AS $option}
                                        {if isset($option.children) && $option.children|@count}
                                            <optgroup label="{$option.name|escape:'html':'UTF-8'}">
                                                {foreach $option.children AS $k => $child_option}
                                                    <option value="{$child_option[$field['identifier']]}"{if $field['value'] == $child_option[$field['identifier']] OR (isset($field['is_array']) AND $field['is_array'] AND in_array($child_option[$field['identifier']], $field['value']))} selected="selected"{/if}>{$child_option['name']}</option>
                                                {/foreach}
                                            </optgroup>
                                        {else}
                                            <option value="{$option[$field['identifier']]}"{if ( $field['value'] == $option[$field['identifier']] ) OR (isset($field['is_array']) AND $field['is_array'] AND in_array($option[$field['identifier']], $field['value']))} selected="selected"{/if}>{$option['name']}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            </div>
                        {elseif $field['type'] == 'bool'}
                            <div class="col-lg-6">
                                <span class="switch bootstrap-switch fixed-width-lg margin-bottom-5 margin-top-5">
                                    <input type="radio" name="{$key}" id="{$key}_on"
                                           value="1" {if $field['value']} checked="checked"{/if}{if isset($field['js']['on'])} {$field['js']['on']}{/if}/>
                                    <label for="{$key}_on" class="radioCheck">
                                        {l s='Yes' mod='inixframe'}
                                    </label>
                                    <input type="radio" name="{$key}" id="{$key}_off"
                                           value="0" {if !$field['value']} checked="checked"{/if}{if isset($field['js']['off'])} {$field['js']['off']}{/if}/>
                                    <label for="{$key}_off" class="radioCheck">
                                        {l s='No' mod='inixframe'}
                                    </label>
                                    <a class="slide-button btn"></a>
                                </span>
                            </div>
                        {elseif $field['type'] == 'radio'}
                            <div class="col-lg-6">
                                <div class="radio-list">
                                    {foreach $field['choices'] AS $k => $v}
                                        <label for="{$key}_{$k}">
                                            <input type="radio" name="{$key}" id="{$key}_{$k}"
                                                   value="{$k}"{if $k == $field['value']} checked="checked"{/if}{if isset($field['js'][$k])} {$field['js'][$k]}{/if}/>
                                            {$v}
                                        </label>
                                    {/foreach}
                                </div>
                            </div>
                        {elseif $field['type'] == 'checkbox'}
                            <div class="col-lg-6">
                                <div class="checkbox-list">
                                    {foreach $field['choices'] AS $k => $v}
                                        <label class="col-lg-3" for="{$key}{$k}_on">
                                            <input type="checkbox" name="{$key}{if isset($field['is_array']) AND $field['is_array']}[]{/if}" id="{$key}{$k}_on"
                                                   value="{$k|intval}"{if ($k == $field['value'] )  OR  (isset($field['is_array']) AND $field['is_array'] AND in_array($k,$field['value']))} checked="checked"{/if}{if isset($field['js'][$k])} {$field['js'][$k]}{/if}/>
                                            {$v}
                                        </label>
                                    {/foreach}
                                </div>
                            </div>
                        {elseif $field['type'] == 'text'}
                            <div class="col-lg-6">
                                {if isset($field['suffix']) OR isset($field['prefix'])}
                                <div class="input-group">
                                {/if}
                                    {if isset($field['prefix'])}
                                        <span class="input-group-addon">
                                            {$field['prefix']|strval}
                                        </span>
                                    {/if}
                                    <input class="form-control"
                                           type="{$field['type']}"{if isset($field['id'])} id="{$field['id']}"{/if}
                                           size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}"
                                           name="{$key}"
                                           value="{$field['value']|escape:'html':'UTF-8'}" {if isset($field['autocomplete']) && !$field['autocomplete']}autocomplete="off"{/if}/>
                                    {if isset($field['suffix'])}
                                        <span class="input-group-addon">
                                            {$field['suffix']|strval}
                                        </span>
                                    {/if}
                                {if isset($field['suffix']) OR isset($field['prefix'])}
                                </div>
                                {/if}
                            </div>
                        {elseif $field['type'] == 'password'}
                            <div class="col-lg-6">
                                <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="icon-lock"></i>
                                                </span>
                                    <input class="form-control"
                                           type="{$field['type']}"{if isset($field['id'])} id="{$field['id']}"{/if}
                                           size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}"
                                           name="{$key}"
                                           value="" {if isset($field['autocomplete']) && !$field['autocomplete']}autocomplete="off"{/if} />
                                    {if isset($field['suffix'])}
                                        <span class="input-group-addon">
                                                    {$field['suffix']|strval}
                                                </span>
                                    {/if}
                                </div>
                            </div>
                        {elseif $field['type'] == 'textarea'}
                            <div class="col-lg-6">
                                <textarea
                                        class="form-control textarea-autosize {if isset($field['autoload_rte']) && $field['autoload_rte']}autoload_rte{/if}"
                                        name={$key} cols="{$field['cols']}"
                                        rows="{$field['rows']}">{$field['value']|escape:'html':'UTF-8'}</textarea>
                            </div>
                        {elseif $field['type'] == 'file'}
                            <div class="col-lg-6">{$field['file']}</div>

                        {elseif $field['type'] =='date'}
                            <div class="col-lg-3">
                                {if isset($field['suffix']) OR isset($field['prefix'])}
                                <div class="input-group">
                                    {/if}
                                    {if isset($field['prefix'])}
                                        <span class="input-group-addon">
                                            {$field['prefix']|strval}
                                        </span>
                                    {/if}
                                    <input class="form-control framedatepicker"
                                           data-date-format="yyyy-mm-dd"
                                           type="text" {if isset($field['id'])} id="{$field['id']}"{/if}
                                           size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}"
                                           name="{$key}"
                                           value="{$field['value']|escape:'html':'UTF-8'}" {if isset($field['autocomplete']) && !$field['autocomplete']}autocomplete="off"{/if}/>
                                    {if isset($field['suffix'])}
                                        <span class="input-group-addon">
                                            {$field['suffix']|strval}
                                        </span>
                                    {/if}
                                    {if isset($field['suffix']) OR isset($field['prefix'])}
                                </div>
                                {/if}
                            </div>
                        {elseif $field['type'] =='datetime'}
                            <div class="col-lg-3">
                                {if isset($field['suffix']) OR isset($field['prefix'])}
                                <div class="input-group">
                                    {/if}
                                    {if isset($field['prefix'])}
                                        <span class="input-group-addon">
                                            {$field['prefix']|strval}
                                        </span>
                                    {/if}
                                    <input class="form-control framedatetimepicker"
                                           data-date-format="yyyy-mm-dd hh:ii:ss"
                                           type="text" {if isset($field['id'])} id="{$field['id']}"{/if}
                                           size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}"
                                           name="{$key}"
                                           value="{$field['value']|escape:'html':'UTF-8'}" {if isset($field['autocomplete']) && !$field['autocomplete']}autocomplete="off"{/if}/>
                                    {if isset($field['suffix'])}
                                        <span class="input-group-addon">
                                            {$field['suffix']|strval}
                                        </span>
                                    {/if}
                                    {if isset($field['suffix']) OR isset($field['prefix'])}
                                </div>
                                {/if}
                            </div>
                        {elseif $field['type'] == 'color'}
                            <div class="col-md-2 col-lg-2">
                                <div class="input-group color colorpicker-default"
                                     data-color="{$field['value']|escape:'html':'UTF-8'}" data-color-format="hex">
                                    <input type="text" class="form-control"
                                           value="{$field['value']|escape:'html':'UTF-8'}"
                                           name="{if isset($field['name'])}{$field['name']}{else}{$key}{/if}">
												<span class="input-group-btn">
													<button class="btn btn-default" type="button"><i
                                                                style="background-color:{$field['value']|escape:'html':'UTF-8'} ">
                                                            &nbsp;&nbsp;&nbsp;&nbsp;</i></button>
												</span>
                                </div>
                            </div>
                        {elseif $field['type'] == 'price'}
                            <div class="col-lg-2">

                                <div class="input-group">
                                    {if !empty($currency_left_sign) OR isset($field['prefix'])}
                                        <span class="input-group-addon">{$currency_left_sign} {if isset($field['prefix'])}{$field['prefix']}{/if}</span>
                                    {/if}
                                    <input type="text" class="form-control"
                                           size="{if isset($field['size'])}{$field['size']|intval}{else}7{/if}"
                                           name="{$key}" value="{$field['value']|escape:'html':'UTF-8'}"/>
                                    {if !empty($currency_right_sign) OR isset($field['suffix'])}
                                        <span class="input-group-addon">{$currency_right_sign} {if isset($field['suffix'])}{$field['suffix']}{/if}</span>
                                    {/if}
                                </div>
                            </div>
                        {elseif $field['type'] == 'textLang' || $field['type'] == 'textareaLang' || $field['type'] == 'selectLang'}
                            {if $field['type'] == 'textLang'}
                                <div class="col-lg-6">
                                    <div class="row">
                                        {foreach $field['languages'] AS $id_lang => $value}
                                            {if $field['languages']|count > 1}
                                                <div class="translatable-field lang-{$id_lang}" {if $id_lang != $current_id_lang}style="display:none;"{/if}>
                                                <div class="col-lg-6">
                                            {else}
                                                <div class="col-lg-12">
                                            {/if}
                                            <input type="text"
                                                   class="form-control"
                                                   name="{$key}_{$id_lang}"
                                                   value="{$value|escape:'html':'UTF-8'}"
                                                   {if isset($input.class)}class="{$input.class}"{/if}
                                                    />
                                            {if $field['languages']|count > 1}
                                                </div>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-default dropdown-toggle"
                                                            data-toggle="dropdown">
                                                        {foreach $languages as $language}
                                                            {if $language.id_lang == $id_lang}{$language.iso_code}{/if}
                                                        {/foreach}
                                                        <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        {foreach $languages as $language}
                                                            <li>
                                                                <a href="javascript:hideOtherLanguage({$language.id_lang});">{$language.name}</a>
                                                            </li>
                                                        {/foreach}
                                                    </ul>
                                                </div>
                                                </div>
                                            {else}
                                                </div>
                                            {/if}
                                        {/foreach}
                                    </div>
                                </div>
                            {elseif $field['type'] == 'textareaLang'}
                                <div class="col-lg-6">
                                    {foreach $field['languages'] AS $id_lang => $value}
                                        <div class="row translatable-field lang-{$id_lang}"
                                             {if $id_lang != $current_id_lang}style="display:none;"{/if}>
                                            <div id="{$key}_{$id_lang}"
                                                 class=" {if isset($field['autoload_rte']) && $field['autoload_rte']}pull-left margin-left-15{else}col-lg-6{/if}">
                                                <textarea
                                                        class="form-control textarea-autosize {if isset($field['autoload_rte']) && $field['autoload_rte']}autoload_rte{/if}"
                                                        name="{$key}_{$id_lang}"
                                                        rows="4">{$value|replace:'\r\n':"\n"}</textarea>
                                            </div>
                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-default dropdown-toggle"
                                                        data-toggle="dropdown">
                                                    {foreach $languages as $language}
                                                        {if $language.id_lang == $id_lang}{$language.iso_code}{/if}
                                                    {/foreach}
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    {foreach $languages as $language}
                                                        <li>
                                                            <a href="javascript:hideOtherLanguage({$language.id_lang});">{$language.name}</a>
                                                        </li>
                                                    {/foreach}
                                                </ul>
                                            </div>

                                        </div>
                                    {/foreach}
                                </div>
                            {elseif $field['type'] == 'selectLang'}
                                {foreach $languages as $language}
                                    <div id="{$key}_{$language.id_lang}"
                                         style="display: {if $language.id_lang == $current_id_lang}block{else}none{/if};"
                                         class="col-lg-6">
                                        <select name="{$key}_{$language.iso_code|upper}" class="form-control">
                                            {foreach $field['list'] AS $k => $v}
                                                <option value="{if isset($v.cast)}{$v.cast[$v[$field.identifier]]}{else}{$v[$field.identifier]}{/if}"
                                                        {if $field['value'][$language.id_lang] == $v['name']} selected="selected"{/if}>
                                                    {$v['name']}
                                                </option>
                                            {/foreach}
                                        </select>
                                    </div>
                                {/foreach}
                            {/if}

                        {elseif $field['type']== 'categories'}
                            <div class="col-lg-6">
                                {$categories_tree}
                            </div>
                        {elseif $field['type'] =='controllers'}
                            <div class="col-lg-6">

                                <select name="{$key}{if isset($field.multiple) AND $field.multiple}[]{/if}"
                                        class="form-control select2me {if isset($field.class)}{$field.class}{/if}"
                                        id="{if isset($field.id)}{$field.id}{else}{$key}{/if}"
                                        {if isset($field.multiple) AND $field.multiple}multiple="multiple"{/if}
                                        {if isset($field.size)}size="{$field.size}"{/if}
                                        {if isset($field.onchange)}onchange="{$field.onchange}"{/if}>
                                    {foreach from=$controllers key=page item=controller}
                                        <option value="{$page}"
                                                {if in_array($page,$field.selected_controllers)}selected{/if}>{$page}</option>
                                    {/foreach}
                                </select>
                            </div>
                        {elseif $field['type'] == 'products'}
                            <div class="col-lg-3">

                                <input type="hidden" name="{$key}" id="inputProducts"
                                       value="{foreach from=$products item=product}{$product.id_product}-{/foreach}"/>
                                <input type="hidden" name="nameProducts" id="nameProducts"
                                       value="{foreach from=$products item=product}{$product.name|escape:'htmlall':'UTF-8'}{if !empty($product.reference)} (ref: {$product.reference}){/if}¤{/foreach}"/>

                                <div class="form-group">
                                    <div class="input-icon left">
                                        <i class="icon-info"></i>
                                    <input type="text" value="" class="form-control help-tooltip" data-toggle="tooltip"
                                           data-original-title=" {l s='Begin typing the first letters of the product name or reference number, then select the product from the drop-down list.' mod='inixframe'}"  id="product_autocomplete_input"/>

                                </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <p class="help-block">
                                            {l s='(Do not forget to save afterward)' mod='inixframe'}
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
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
                            </div>
                        {/if}
                        {if isset($field['desc']) && !empty($field['desc'])}
                            <div class="col-lg-6 col-lg-offset-3">
                                <p class="help-block">
                                    {if is_array($field['desc'])}
                                        {foreach $field['desc'] as $p}
                                            {if is_array($p)}
                                                <span id="{$p.id}">{$p.text}</span>
                                                <br/>
                                            {else}
                                                {$p}
                                                <br/>
                                            {/if}
                                        {/foreach}
                                    {else}
                                        {$field['desc']}
                                    {/if}
                                </p>
                            </div>
                        {/if}
                    {/block}{* end block input *}
                    {if $field['is_invisible']}
                        <div class="col-lg-6 col-lg-offset-3">
                            <p class="alert alert-warning row-margin-top">
                                {l s='You can\'t change the value of this configuration field in the context of this shop.' mod='inixframe'}
                            </p>
                        </div>
                    {/if}
                {/block}{* end block field *}
                </div>
                </div>
            {/if}

        {/foreach}
        {if isset($categoryData['bottom'])}{$categoryData['bottom']}{/if}
        </div>
        {block name="footer"}
            {if isset($categoryData['submit']) || isset($categoryData['buttons'])}
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            {if isset($categoryData['submit']) && !empty($categoryData['submit'])}
                                <button type="{if isset($categoryData['submit']['type'])}{$categoryData['submit']['type']}{else}submit{/if}"
                                        {if isset($categoryData['submit']['id'])}id="{$categoryData['submit']['id']}"{/if}
                                        class="btn btn-success"
                                        name="{if isset($categoryData['submit']['name'])}{$categoryData['submit']['name']}{else}submitInixOptions{$table}{/if}">
                                    <i class="icon-sm process-icon-{if isset($categoryData['submit']['imgclass'])}{$categoryData['submit']['imgclass']}{else}save{/if}"></i> {$categoryData['submit']['title']}
                                </button>
                            {/if}
                            {if isset($categoryData['buttons'])}
                                {foreach from=$categoryData['buttons'] item=btn key=k}
                                    <button type="{if isset($btn['type'])}{$btn['type']}{else}button{/if}"
                                            {if isset($btn['id'])}id="{$btn['id']}"{/if}
                                            class="{if isset($btn['class'])}{$btn['class']}{else}btn btn-default{/if}"
                                            name="{if isset($btn['name'])}{$btn['name']}{else}submitInixOptions{$table}{/if}"{if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}>{if isset($btn['icon'])}
                                            <i class="icon-sm  {$btn['icon']}" ></i> {/if}{$btn.title}</button>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                </div>
            {/if}
        {/block}
        </div>
        </div>
    {/foreach}

    </form>
{/block}
{block name="after"}{/block}
{if isset($tabs) && $tabs}
    <script type="text/javascript">

        function getCookie(c_name) {
            var c_value = document.cookie;
            var c_start = c_value.indexOf(" " + c_name + "=");
            if (c_start == -1) {
                c_start = c_value.indexOf(c_name + "=");
            }
            if (c_start == -1) {
                c_value = null;
            }
            else {
                c_start = c_value.indexOf("=", c_start) + 1;
                var c_end = c_value.indexOf(";", c_start);
                if (c_end == -1) {
                    c_end = c_value.length;
                }
                c_value = unescape(c_value.substring(c_start, c_end));
            }
            return c_value;
        }


        var selected_tab = getCookie('inixframe_tab');

        var tabs = $("");
        var list = $('<ul id="inixframe_tabs">').addClass('col-md-2 nav nav-pills nav-stacked');
        $(".inixframe form > .portlet").each(function (k, v) {
            var caption = $(v).find(".caption").first();
            var tab_title = caption.html();


            var row = $('<li>').html(tab_title).data('content', $(v).attr('id')).addClass('text-left btn btn-default');


            if (( (selected_tab == null || selected_tab == '') && k == 0 ) || $(v).attr('id') == selected_tab) {
                row.addClass('selected btn-primary').removeClass('btn-default');
                $(v).css('display', 'block');
            } else {
                $(v).css('display', 'none');
            }

           row.appendTo(list);
            caption.parent().remove();
            return;
            if ($(v).prev().is('br'))
                $(v).prev().remove();
        }).addClass('col-md-10 ').css({ clear: 'none' });

        list.prependTo($('.form-horizontal'));
        if ($('#' + $(".selected").data('content')).height() < tabs.height())
            $('#' + $(".selected").data('content')).height(tabs.height());

        $("#inixframe_tabs li").click(function () {
            $('.inixframe form > .portlet').hide();
            if ($('#' + $(this).data('content')).height() < tabs.height())
                $('#' + $(this).data('content')).height(tabs.height());
            $('#' + $(this).data('content')).show();
            $('li.selected').removeClass('btn-primary').removeClass('selected').addClass('btn-default');
            $(this).addClass('selected btn-primary');

            document.cookie = 'inixframe_tab=' + $(this).data('content');
        });

    </script>
{/if}
{if isset($tinymce) && $tinymce}
    <script type="text/javascript">
        var iso = '{$iso}';
        var pathCSS = '{$smarty.const._THEME_CSS_DIR_}';
        var ad = '{$ad}';

        $(document).ready(function () {
            {block name="autoload_tinyMCE"}
            tinySetup({
                editor_selector: "autoload_rte"
            });
            {/block}
        });
    </script>
{/if}

