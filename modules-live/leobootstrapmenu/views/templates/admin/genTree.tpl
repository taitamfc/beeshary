{* 
* @Module Name: Leo Blog
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
* @description: Content Management
*}

<ol class="level{$level}{$t}">
    {foreach from=$data item=$menu}
        <li data-menu-type="{$menu.type}" id="list_{$menu.id_btmegamenu}" data-id-menu="{$menu.id_btmegamenu}" class="nav-item {if $param_id_btmegamenu == $menu.id_btmegamenu}selected{/if}">
        <div>
            <span class="disclose"><span></span></span>
            {$menu.title} (ID:{$menu.id_btmegamenu})
            <input type="checkbox" name="menubox[]" value="{$menu.id_btmegamenu}" class="quickselect" title="Select to delete">
            <span title="{l s='Edit' mod='leobootstrapmenu'}" class="quickedit" rel="id_{$menu.id_btmegamenu}">E</span>
            <span title="{l s='Delete' mod='leobootstrapmenu'}" class="quickdel" rel="id_{$menu.id_btmegamenu}">D</span>
            <span title="{l s='Duplicate' mod='leobootstrapmenu'}" class="quickduplicate" rel="id_{$menu.id_btmegamenu}">DUP</span>
        </div>
        {if $menu.id_btmegamenu != $parent}
            {$model_cat->genTree($menu.id_btmegamenu, $level + 1)}
        {/if}
        </li>
    {/foreach}
</ol>