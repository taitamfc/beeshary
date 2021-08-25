{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<p><input type="text" name="controller_pages" value="{$controller}" class="em_text"/></p>
<p><select size="25" name="controller_pages_select" class="em_list" multiple="multiple">

<option disabled="disabled">{$_core_}</option>
{foreach from=$controllers key=k item=v}
    {if in_array($k, $arr_controllers)}
        <option value="{$k}" selected="selected">{$k}</option>
    {else}
        <option value="{$k}">{$k}</option>
    {/if}
{/foreach}

{foreach from=$modules_controllers_type key=type item=label}
    <option disabled="disabled">________________________________________ {$label} ________________________________________</option>
    {assign var=all_modules_controllers value=$controllers_modules.$type}
    {foreach $all_modules_controllers key=module item=modules_controllers}
        {foreach $modules_controllers item=cont}
            {assign var=key value="module-`$module`-`$cont`"}
            {if in_array($key, $arr_controllers)}
                <option value="module-{$module}-{$cont}" selected="selected">module__{$module}__{$cont}</option>
            {else}
                <option value="module-{$module}-{$cont}">module__{$module}__{$cont}</option>
            {/if}
        {/foreach}
    {/foreach}
{/foreach}
</select></p>