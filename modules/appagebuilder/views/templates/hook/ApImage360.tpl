{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApImage360 -->
{if isset($formAtts.lib_has_error) && $formAtts.lib_has_error}
    {if isset($formAtts.lib_error) && $formAtts.lib_error}
        <div class="alert alert-warning leo-lib-error">{$formAtts.lib_error}</div>
    {/if}
{else}
        
    {if isset($formAtts.title) && $formAtts.title}
        <h4 class="title_block">{$formAtts.title}{*contain html, no escape necessary*}</h4>
    {/if}
    {if isset($formAtts.sub_title) && $formAtts.sub_title}
        <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
    {/if}
    <div class="clearfix" id="image-block">
        <div class="LeoImageToolboxContainer leo-image-360-block">
                <a id="apImage360" data-magic360-options="columns:{$formAtts.columns};rows:{$formAtts.row};
                   images:{foreach $formAtts.image_list as $image} {$formAtts.image_path}{$image}{/foreach}"
                    href="#" class="Magic360"
                    style="display: inline-block; visibility: visible; overflow: hidden; position: relative; vertical-align: middle; text-decoration: none; color: rgb(0, 0, 0); background-repeat: no-repeat; background-size: 458px 458px;
                    background-image:{foreach $formAtts.image_list as $image} url('{$formAtts.image_path}{$image}'),{/foreach};
                    background-position:{foreach $formAtts.image_list as $image} 0px -10000px,{/foreach} 
                    ">
                    <img src="{$formAtts.image_path}{$formAtts.image_default}" style="width: 100%; opacity: 1;" alt="">
                </a>

            {if ($formAtts.message)}
            <div class="LeoImageToolboxMessage">{$formAtts.message|default:'Loading image...'|escape:'html':'UTF-8'}</div>
            {/if}

            <div style="width:0px;height:1px;overflow:hidden;visibility:hidden;">
                <img src="{$formAtts.image_path}{$formAtts.image_default}" id="bigpic" style="position: absolute; top: 5px; left: 4.5px;" alt="">
            </div>
        </div>
    </div>

    <script type="text/javascript">
    ap_list_functions.push(function(){

        Magic360.options = {
            'rows':{if $formAtts.row}{$formAtts.row}{else}1{/if},
            'columns':{if $formAtts.columns}{$formAtts.columns}{else}5{/if},
            'magnify':false,                                                                                        // Magnifier effect
            'magnifier-width':'80%',
            'magnifier-shape':'inner',
            'fullscreen':false,
            'spin':'{$formAtts.spin|escape:'html':'UTF-8'}',                                                        // drag, hover
            'autospin-direction':'{$formAtts.autospin_direction|escape:'html':'UTF-8'}',                            // clockwise, anticlockwise, alternate-clockwise, alternate-anticlockwise
            'speed':{if $formAtts.speed}{$formAtts.speed|intval}{else}50{/if},                                      // 1 -> 100
            'mousewheel-step':{if $formAtts.mousewheel_step}{$formAtts.mousewheel_step|intval}{else}1{/if},
            'autospin-speed':{if $formAtts.autospin_speed}{$formAtts.autospin_speed|intval}{else}3600{/if},         // Choose speed of auto-spin
            'smoothing':{if $formAtts.smoothing}true{else}false{/if},                                               // Smoothly stop the image spinning
            'autospin':'{$formAtts.autospin|escape:'html':'UTF-8'}',                                                // once, twice, infinite, off
            'autospin-start':'{$formAtts.autospin_start|escape:'html':'UTF-8'}',                                    // load, hover, click, load,hover       : Autospin starts on
            'autospin-stop':'{$formAtts.autospin_stop|escape:'html':'UTF-8'}',                                      // click, hover, never, 
            'initialize-on':'{$formAtts.initialize_on|escape:'html':'UTF-8'}',                                      // load, hover, hover                   : When to initialize 360 (download images).
            'start-column':{if $formAtts.start_column}{$formAtts.start_column|intval}{else}1{/if},                  //                                      : Column from which to start spin. auto means to start from the middle
            'loop-column':{if $formAtts.loop_column}true{else}false{/if},                                           // true, false                          : Continue spin after the last image on X-axis
            'reverse-column':{if $formAtts.reverse_column}false{else}true{/if},                                     // true, false                          : 
            'hint':{if $formAtts.hint}true{else}false{/if},                                                         // true, false                          : Show hint message
        }
        
        Magic360.lang = {
            'hint-text':'{$formAtts.message_desktop_hint|default:'Drag to spin'|escape:'html':'UTF-8'}',
            'mobile-hint-text':'{$formAtts.message_mobile_hint|default:'Swipe to spin'|escape:'html':'UTF-8'}',
        }
    });
    </script>
{/if}