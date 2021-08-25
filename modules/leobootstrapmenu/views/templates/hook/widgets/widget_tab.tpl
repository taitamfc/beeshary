{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

<div class="leo-widget" data-id_widget="{$id_widget}">
{if isset($tabs)}
    <div class="widget-tab">
	{if isset($widget_heading)&&!empty($widget_heading)}
	<div class="menu-title">
            {$widget_heading}
	</div>
	{/if}
	<div class="widget-inner">	
            <div id="tabs{$id}" class="panel-group">
                <ul class="nav nav-tabs">
                    {foreach $tabs as $key => $ac}  
                    <li class="{if $key==0}active{/if}"><a href="#tab{$id}{$key}" >{$ac.header}</a></li>
                    {/foreach}
                </ul>
                <div class="tab-content">
                    {foreach $tabs as $key => $ac}
                        <div class="tab-pane{if $key==0} active{/if} " id="tab{$id}{$key}">{$ac.content}</div>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
{/if}
{if $show_widget_bo == 'admin'}
    <div class="w-name">
        <select name="inject_widget" class="inject_widget_name">
            {foreach from=$widgets item=w}
                <option value="{$w['key_widget']}">
                    {$w['name']}
                </option>
            {/foreach}
        </select>
    </div>
{/if}
</div>