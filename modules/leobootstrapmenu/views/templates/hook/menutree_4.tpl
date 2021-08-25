{* 
* @Module Name: Leo Blog
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
* @description: Content Management
*}

<div class="dropdown-sub {$class} level{$level}" {$attrw} >
    <div class="dropdown-menu-inner">
        {foreach from=$parent.megaconfig->rows item=rows}
            {foreach from=$rows item=rowcols}
                <div class="row">
                    {foreach from=$rowcols key=key item=col}
                        {assign var=colwidth value='6'}
                        {if isset($col->colwidth) && $col.colwidth}
                            {assign var=colwidth value=$col.colwidth}
                        {/if}
                        
                        {assign var=colclass value=''}
                        {if isset($col->colclass) && $col.colclass}
                            {assign var=colclass value=$col.colclass}
                        {/if}
                        
                        {if isset($col->type) && $col->type == 'menu' && isset($cols.$key)}
                            <div class="mega-col col-md-{$col->colwidth}" data-type="menu" {$mod_menu->getColumnDataConfig($col)}>
                                <div class="mega-col-inner {$colclass}">
                                    <ul>
                                        {foreach from=$cols.$key item=menu}
                                            {$mod_menu->renderMenuContent($menu, $level + 1, $typesub, $group_type nofilter}{* HTML form , no escape necessary *}
                                        {/foreach}
                                    </ul>
                                </div>
                            </div>
                        {else}
                            <div class="mega-col col-md-{$col->colwidth}" {$mod_menu->getColumnDataConfig($col)}>
                                <div class="mega-col-inner {$colclass}">
                                    {$mod_menu->renderWidgetsInCol($col) nofilter}{* HTML form , no escape necessary *}
                                </div>
                            </div>
                        {/if}

                    {/foreach}
                </div>
            {/foreach}
        {/foreach}
    </div>
</div>