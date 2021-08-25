{* 
* @Module Name: Leo Blog
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
* @description: Content Management
*}

<div class="{$class} dropdown-sub mega-cols cols{$parent.colums}" {$attrw}>
    <div class="dropdown-menu-inner">
        <div class="row">
            {foreach from=$cols key=i  item=menus}
                {assign var="colwidth" value= $o_spans.{$i + 1}}
                {$colwidth|replace:'col-sm-':''}
                <div class="mega-col {$o_spans.{$i + 1}} col-{$i + 1}" data-type="menu" data-colwidth="{$colwidth}">
                    <div class="inner">
                        <ul>
                            {foreach from=$menus item=menu}
                                {$mod_menu->renderMenuContent($menu, $level + 1, $typesub, $group_type) nofilter}{* HTML form , no escape necessary *}
                            {/foreach}
                        </ul>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>