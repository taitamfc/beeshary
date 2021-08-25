{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\hook\ApImage -->
{if isset($formAtts.lib_has_error) && $formAtts.lib_has_error}
    {if isset($formAtts.lib_error) && $formAtts.lib_error}
        <div class="alert alert-warning leo-lib-error">{$formAtts.lib_error}</div>
    {/if}
{else}
    <div id="image-{$formAtts.form_id|escape:'html':'UTF-8'}" class="block {(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'} {(isset($formAtts.class) && $formAtts.class) ? $formAtts.class : ''|escape:'html':'UTF-8'}">
        {($apLiveEdit)?$apLiveEdit:''}{* HTML form , no escape necessary *}

        {if isset($formAtts.title) && $formAtts.title}
            <h4 class="title_block">{$formAtts.title}{*contain html, no escape necessary*}</h4>
        {/if}
        {if isset($formAtts.sub_title) && $formAtts.sub_title}
            <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
        {/if}
        {if isset($formAtts.image) && $formAtts.image}
            {if isset($formAtts.url) && $formAtts.url}
            <a href="{$formAtts.url}{*full url can not escape*}" {(isset($formAtts.is_open) && $formAtts.is_open) ? 'target="_blank"' : ''|escape:'html':'UTF-8'}>
            {/if}

            <div class="imagehotspot-container">
                <div style="position: relative; height: 0px; padding-bottom: 48%; /* padding-bottom: image's height divided by width multiply by 100 */">
                    {*<img style="position: absolute; top: 0px; left: 0px; z-index: 102;" alt="" src="images/ford-fusion.jpg" />*}

                    <img src="{$path|escape:'html':'UTF-8'}{$formAtts.image|escape:'html':'UTF-8'}" class="{(isset($formAtts.animation) && $formAtts.animation != 'none') ? 'has-animation' : ''|escape:'html':'UTF-8'}"
                            {if isset($formAtts.animation) && $formAtts.animation != 'none'} data-animation="{$formAtts.animation|escape:'html':'UTF-8'}" {/if}

                            title="{((isset($formAtts.title) && $formAtts.title) ? $formAtts.title : '')|escape:'html':'UTF-8'}"
                            alt="{((isset($formAtts.alt) && $formAtts.alt) ? $formAtts.alt : '')|escape:'html':'UTF-8'}"
                            style=" width:{((isset($formAtts.width) && $formAtts.width) ? $formAtts.width : 'auto')|escape:'html':'UTF-8'}; 
                            height:{((isset($formAtts.height) && $formAtts.height) ? $formAtts.height : 'auto')|escape:'html':'UTF-8'}" />


                    {*{if isset($formAtts.title) && $formAtts.title=='White Cart'}
                        <div class="redhotspot" id="fhotspot1" style="position: absolute; z-index: 103; width: 1.667%; height: 3.472%; top: 74%; left: 8.9%;"></div>
                        <div class="redhotspot" id="fhotspot2" style="position: absolute; z-index: 103; width: 1.667%; height: 3.472%; top: 38%; left: 54%;"></div>
                    {/if}*}

                    {if isset($formAtts.items) && $formAtts.items}
                        {foreach from=$formAtts.items item=item}
                            <div class="redhotspot {$formAtts.form_id}_{$item.id}" id="hotspot_{$formAtts.form_id}_{$item.id}" style="position: absolute; z-index: 103; width: 1.667%; height: 3.472%; top: {$item.temp_top}%; left: {$item.temp_left}%;"></div>
                            <script>
                                ap_list_functions.push(function(){
                                $("#hotspot_{$formAtts.form_id}_{$item.id}").LiteTooltip({
                                    location : "{$item.temp_location}",
                                    textalign: "{$item.temp_textalign}",
                                    trigger: "{$item.temp_trigger}",
                                    templatename: "{$item.temp_class}",
                                    debug : true,
                                    width : '{$item.temp_width}',
                                    //delay : '1000',
                                    opacity : '{$item.temp_opacity}',
                                    margin : '{$item.temp_margin}',
                                    padding: '{$item.temp_padding}',
                                    textcolor: '{$item.temp_textcolor}',
                                    backcolor: '{$item.temp_backcolor}',
                                    title:
                                    '<div class="template"><p>'
                                    {if isset($item.temp_image)&& $item.temp_image}
                                        + '<img src="{$item.temp_image}" class="{if $item.temp_imagealign == 'right'}image-right{elseif $item.temp_imagealign == 'left'}image-left{/if}" style="width: 100%; max-width:200px" />'
                                    {/if}
                                        + '{$item.temp_description}'
                                    + '</p></div>'

                                });

                                    $(".{$formAtts.form_id}_{$item.id}").css({
                                        "background": "{$item.temp_hpcolor}",
                                        "border-radius": "19.2px"
                                    });
                                });

                            </script>
                        {/foreach}
                    {/if}
                </div>
            </div>




            {if isset($formAtts.url) && $formAtts.url}
            </a>
            {/if}
        {/if}


        {($apLiveEditEnd)?$apLiveEditEnd:''}{* HTML form , no escape necessary *}
            {if isset($formAtts.description) && $formAtts.description}
                <div class='image_description'>
                                    {($formAtts.description) ? $formAtts.description:''}{* HTML form , no escape necessary *}
                </div>
            {/if}
    </div>



    <script type="text/javascript">
        ap_list_functions.push(function(){
{*            var $window = $(window)*}
            //window.prettyPrint();

            $(".redhotspot").css({
{*                "border": "solid 1px #990000",*}
                "background": "#cc0000",
                "border-radius": "19.2px"
            });

            function blink_hotspot() {
                // car image
                $('.redhotspot').animate({ "opacity": '0.3' }, 'slow')
                                .animate({ 'opacity': '0.8' }, 'fast', function () { blink_hotspot(); });
            }

            blink_hotspot();
        });


// Dien config vao day
// có config : image-left, image-right
        /*
        $("#fhotspot1").LiteTooltip({
            textalign: "left",
            trigger: "hover",   // click
            templatename: "BostonBlue",
            backcolor: '#ff0000',
            delay : '1000',
            title:
            '<div class="template">' +
            '<p style="padding: 15px; font-size: 11px; line-height: 20px;">' +
            '<img src="animal_1920_1080_1.jpg" class="image-right" style="max-width: 75px; width: 100%;" />' +
            'Take your pick of standard and available wheels on the 2013 Ford Fusion.' +
            '</p>' +
            '</div>'
        });

        $("#fhotspot2").LiteTooltip({
            location : "left-bottom",
            textalign: "center",
            trigger: "hover",
            templatename: "BostonBlue",
            debug : true,
            width : '500px',
            delay : '1000',
            opacity : '0.2',
            margin : '50px',
            padding: '10px',
            textcolor: '#ff0000',
            backcolor: '#ff0000',
            title:
            '<div class="template">' +
            '<p style="padding: 15px; font-size: 11px; line-height: 20px;">' +
            '<img src="animal_1920_1080_1.jpg" class="image-right" style="max-width: 75px; width: 100%;" />' +
            'Ambient lighting in the Titanium with cool interior illumination.' +
            '</p>' +
            '</div>'
        });


                location:   top | right | bottom | left | top-left | top-right | right-top | right-bottom |bottom-left | bottom-right | left-top | left-bottom
                title:          
                backcolor:      #ff0000
                textalign:      left, right, center
                trigger:        hoverable, hover, focus, click
                textcolor:      #ff0000
                opacity:
                templatename:
                width:
                margin:
                padding:
                delay:
                issticky: true, false
                container:
                shadow:
                debug:true
         */

    </script>
{/if}