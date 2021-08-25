{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

<div class="leo-widget" data-id_widget="{$id_widget}">
{if isset($tabhtmls)}
    <div id="tabhtml{$id|escape:'html':'UTF-8'}" class="widget-tab block">
        {if isset($widget_heading)&&!empty($widget_heading)}
        <h4 class="title_block">
            {$widget_heading|escape:'html':'UTF-8'}
        </h4>
        {/if}
        <div class="block_content"> 
            <div id="tabhtmls{$id|escape:'html':'UTF-8'}" class="panel-group">
                <ul class="nav nav-tabs">
                    {foreach $tabhtmls as $key => $ac}  
                    <li class="nav-item{if $key==0} active{/if}"><a href="#tabhtml{$id|escape:'html':'UTF-8'}{$key|escape:'html':'UTF-8'}" class="nav-link tab-link" >{$ac.title|escape:'html':'UTF-8'}</a></li>
                    {/foreach}
                </ul>

                <div class="tab-content">
                    {foreach $tabhtmls as $key => $ac}
                        <div class="tab-pane{if $key==0} active{/if} " id="tabhtml{$id|escape:'html':'UTF-8'}{$key|escape:'html':'UTF-8'}">{$ac.content nofilter}{* HTML form , no escape necessary *}</div>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">
    {literal}
    if ( typeof live_editor !== 'undefined' && live_editor)
    {
        // var tabhtml_id = {/literal}{$id}{literal};
        list_tab_live_editor.push({/literal}{$id}{literal});
    }
    else
    {		
        // list_menu_tmp.list_tab.push({$id});
        list_tab.push({/literal}{$id}{literal})	;
    }
    {/literal}
</script>
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