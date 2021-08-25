{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

<div class="leo-widget" data-id_widget="{$id_widget}">
    {if isset($html)&& !empty($html)}
        <div class="alert {$alert_type}">
            {$html nofilter}{* HTML form , no escape necessary *}
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