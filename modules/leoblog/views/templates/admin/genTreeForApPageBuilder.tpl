{* 
* @Module Name: Leo Blog
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
* @description: Content Management
*}

<ol class="level{$level}">
    {foreach from=$data item=$menu}
        <li id="list_{$menu.id_leoblogcat}">
            <input type="checkbox" value="{$menu.randkey}" name="chk_cat[]" id="chk-{$menu.id_leoblogcat}" {if $menu.id_leoblogcat|array_search:$select !== false}checked="checked"{/if}/>
            <label for="chk-{$menu.id_leoblogcat}">{$menu.title} (ID:{$menu.id_leoblogcat})</label>
            {if $menu.id_leoblogcat != $parent}
                {$model_leoblogcat->genTreeForApPageBuilder($menu.id_leoblogcat, $level + 1, $select)}
            {/if}
        </li>
    {/foreach}
</ol>
