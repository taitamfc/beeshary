{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}
{if isset($error) && $error}
    <div class="alert alert-warning leo-lib-error">{$error}</div>
{else}
    
    {if $group_type && $group_type == 'horizontal'}
            <nav data-megamenu-id="{$megamenu_id}" class="leo-megamenu cavas_menu navbar navbar-default {if $show_cavas && $show_cavas == 1}enable-canvas{else}disable-canvas{/if} {if $group_class && $group_class != ''}{$group_class}{/if}" role="navigation">
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <div class="navbar-header">
                                    <button type="button" class="navbar-toggler hidden-lg-up" data-toggle="collapse" data-target=".megamenu-off-canvas-{$megamenu_id}">
                                            <span class="sr-only">{l s='Toggle navigation' mod='leobootstrapmenu'}</span>
                                            &#9776;
                                            <!--
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                            -->
                                    </button>
                            </div>
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            {*
                            <div id="leo-top-menu" class="collapse navbar-collapse navbar-ex1-collapse">{$boostrapmenu|escape:'html':'UTF-8'}</div>
                            *}
                            <div class="leo-top-menu collapse navbar-toggleable-md megamenu-off-canvas megamenu-off-canvas-{$megamenu_id}">{$boostrapmenu|escape:'html':'UTF-8' nofilter}{* HTML form , no escape necessary *}</div>
            </nav>
            <script type="text/javascript">{literal}
            // <![CDATA[				
                            // var type="horizontal";
                            // checkActiveLink();
                            // checkTarget();
                            list_menu_tmp.id = {/literal}'{$megamenu_id}'{literal};
                            list_menu_tmp.type = 'horizontal';
            // ]]>
            {/literal}
            {if $show_cavas && $show_cavas == 1}
                    {literal}						
                                    // offCanvas();
                                    // var show_cavas = 1;
                                    // console.log('testaaa');
                                    // console.log(show_cavas);
                                    list_menu_tmp.show_cavas =1;

                    {/literal}
                    {else}
                    {literal}
                                    // var show_cavas = 0;
                                    list_menu_tmp.show_cavas =0;	
                    {/literal}
            {/if}
                    {literal}
                    list_menu_tmp.list_tab = list_tab;
                    list_menu.push(list_menu_tmp);
                    list_menu_tmp = {};	
                    list_tab = {};
                    {/literal}
            </script>
    {else}
            <div data-megamenu-id="{$megamenu_id}" class="leo-verticalmenu {if $group_class && $group_class != ''}{$group_class}{/if}">
                    <h4 class="title_block verticalmenu-button">{$group_title}</h4>
                    <div class="box-content block_content">
                            <div class="verticalmenu" role="navigation">{$boostrapmenu|escape:'html':'UTF-8' nofilter}{* HTML form , no escape necessary *}</div>
                    </div>
            </div>
            <script type="text/javascript">
                    {literal}
                            // var type="vertical";	

                            list_menu_tmp.id = {/literal}'{$megamenu_id}'{literal};			
                            list_menu_tmp.type = 'vertical';
                            list_menu_tmp.list_tab = list_tab;
                            list_menu.push(list_menu_tmp);
                            list_menu_tmp = {};
                            list_tab = {};
                    {/literal}		
            </script>


    {/if}

{/if}
