{* 
* @Module Name: Leo Blog
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
* @description: Content Management
*}
                    
<div class="{$class} level{$level}" {$attrw} >
    <div class="dropdown-menu-inner">
        {foreach from=$parent.megaconfig item=rows}
            {foreach from=$rows item=rowcols}
                <div class="row">
                    {foreach from=$rowcols item=col}
                        
                        {assign var=colclass value=''}
                        {if isset($col->colclass) && $col.colclass}
                            {assign var=colclass value=$col.colclass}
                        {/if}
                        {if isset($col->type) && $col->type=='menu'}
                            <div class="mega-col col-md-{$col->colwidth}" data-type="menu" {$mod_menu->getColumnDataConfig($col)}>
                                <div class="mega-col-inner {$colclass}">
                                    <ul>
                                    {foreach from=$data item=menu}
                                        {$mod_menu->renderMenuContent($menu, $level + 1, $typesub, $group_type) nofilter}{* HTML form , no escape necessary *}
                                    {/foreach}
                                    </ul>
                                </div>
                            </div>
                        {else}
                            <div class="mega-col col-md-{$col->colwidth}" {$mod_menu->getColumnDataConfig($col)}>
                                <div class="mega-col-inner {$colclass}">
                                {$mod_menu->renderWidgetsInCol($col)}
                                </div>
                            </div>
                        {/if}
                    {/foreach}
                </div>
            {/foreach}
        {/foreach}
    </div>
</div>