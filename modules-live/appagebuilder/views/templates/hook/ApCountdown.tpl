{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApCountdown -->
{if isset($formAtts.lib_has_error) && $formAtts.lib_has_error}
    {if isset($formAtts.lib_error) && $formAtts.lib_error}
        <div class="alert alert-warning leo-lib-error">{$formAtts.lib_error}</div>
    {/if}
{else}
    {if isset($formAtts.active) && $formAtts.active == 1}
        <div  id="countdown-{$formAtts.form_id|escape:'html':'UTF-8'}" class="{(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'}">

            {if isset($formAtts.title) && !empty($formAtts.title)}
                <h4 class="title_block">
                    {$formAtts.title nofilter}{* HTML form , no escape necessary *}
                </h4>
            {/if}
            {if isset($formAtts.sub_title) && $formAtts.sub_title}
                <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
            {/if}
            {if isset($formAtts.description) && !empty($formAtts.description)}
                {$formAtts.description nofilter}{* HTML form , no escape necessary *}
            {/if}


            <ul class="ap-countdown-time deal-clock lof-clock-11-detail list-inline"></ul>

            <p class="ap-countdown-link">
                {if isset($formAtts.link) && $formAtts.link}
                    {if isset($formAtts.new_tab) && $formAtts.new_tab == 1}
                        <a href="{$formAtts.link|escape:'html':'UTF-8'}" target="_blank">{$formAtts.link_label|escape:'html':'UTF-8'}</a>
                    {/if}	
                    {if isset($formAtts.new_tab) && $formAtts.new_tab == 0}
                        <a href="{$formAtts.link|escape:'html':'UTF-8'}">{$formAtts.link_label|escape:'html':'UTF-8'}</a>
                    {/if}			
                {/if}
            </p>
        </div>

        <script type="text/javascript">
            ap_list_functions.push(function(){
                var text_d = '{l s='days' mod='appagebuilder'}';
                var text_h = '{l s='hours' mod='appagebuilder'}';
                var text_m = '{l s='min' mod='appagebuilder'}';
                var text_s = '{l s='sec' mod='appagebuilder'}';
                $(".lof-clock-11-detail").lofCountDown({
                    TargetDate:"{$formAtts.time_to|escape:'html':'UTF-8'}",
                    DisplayFormat:'<li class="z-depth-1">%%D%%<span>'+text_d+'</span></li><li class="z-depth-1">%%H%%<span>'+text_h+'</span></li><li class="z-depth-1">%%M%%<span>'+text_m+'</span></li><li class="z-depth-1">%%S%%<span>'+text_s+'</span></li>',
                });
            });
        </script>
    {/if}
{/if}