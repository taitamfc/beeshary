{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-9999 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}
{extends file="helpers/form/form.tpl"}
{block name="input" append}
    {if isset($input)}
        {if $input.name == 'hbp_body'}
            {if isset($input.maxchar) && $input.maxchar}<div class="input-group">{/if}
            {assign var=use_textarea_autosize value=true}
            {if isset($input.lang) AND $input.lang}
            {foreach $languages as $language}
            {if $languages|count > 1}
                <div class="form-group translatable-field lang-{$language.id_lang}"{if $language.id_lang != $defaultFormLanguage} style="display:none;"{/if}>
                <div class="col-lg-9 clearfix" style="margin-bottom:10px;">
                    <a class="btn btn-default" onclick="toggleEditor('{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_{$language.id_lang}')">
                        <i class="fa fa-code"></i> {l s='Switch editor'}
                    </a>
                </div>
                <div class="col-lg-10">
            {/if}
                {if isset($input.maxchar) && $input.maxchar}
                    <span id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter" class="input-group-addon">
					    <span class="text-count-down">{$input.maxchar|intval}</span>
					</span>
                {/if}
                <textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name}_{$language.id_lang}" id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_{$language.id_lang}" class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}</textarea>
            {if $languages|count > 1}
                </div>
                <div class="col-lg-1">
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
            {if isset($input.maxchar) && $input.maxchar}
                <script type="text/javascript">
                    $(document).ready(function () {
                        {foreach from=$languages item=language}
                        countDown($("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"), $("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter"));
                        {/foreach}
                    });
                </script>
            {/if}
            {else}
            {if isset($input.maxchar) && $input.maxchar}
                <span id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter" class="input-group-addon">
												<span class="text-count-down">{$input.maxchar|intval}</span>
											</span>
            {/if}
                <textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name}" id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}" {if isset($input.cols)}cols="{$input.cols}"{/if} {if isset($input.rows)}rows="{$input.rows}"{/if} class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name]|escape:'html':'UTF-8'}</textarea>
            {if isset($input.maxchar) && $input.maxchar}
                <script type="text/javascript">
                    $(document).ready(function () {
                        countDown($("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"), $("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter"));
                    });
                </script>
            {/if}
            {/if}
            {if isset($input.maxchar) && $input.maxchar}</div>{/if}
        {/if}
    {/if}
{/block}