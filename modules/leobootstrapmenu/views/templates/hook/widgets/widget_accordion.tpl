{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

<div class="leo-widget" data-id_widget="{$id_widget}">
    {if isset($accordions)}
    <div class="widget-accordion">
            {if isset($widget_heading)&&!empty($widget_heading)}
            <div class="menu-title">
                    {$widget_heading}
            </div>
            {/if}
            <div class="widget-inner">	
                    <div id="accordion{$id}" class="panel-group">
                    {foreach $accordions as $key => $ac}
                            <div class="panel panel-default">
                              <div class="panel-heading">
                                    <h4 class="panel-title">
                                      <a href="#collapseAc{$id}{$key}" data-parent="#accordion{$id}" data-toggle="collapse" class="accordion-toggle collapsed">
                                            {$ac.header}  
                                      </a>
                                    </h4>
                              </div>
                              <div class="panel-collapse collapse {if $key==0} in {else} out{/if}" id="collapseAc{$id}{$key}"  >
                                    <div class="panel-body">
                                      {$ac.content nofilter}{* HTML form , no escape necessary *}
                                    </div>
                              </div>
                            </div>
                    {/foreach}
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