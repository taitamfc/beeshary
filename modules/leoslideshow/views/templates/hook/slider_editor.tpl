{* 
* @Module Name: Leo Slideshow
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

<fieldset>
<div class="form-group text-alert" data-move="{l s='Click Duplicate to apply it to other language. Please edit Caption Html for other language' mod='leoslideshow'}" data-duplicate="{l s='Duplicate is done. Click save slide before doing any thing in other language' mod='leoslideshow'}" data-delete="{l s='Please click one to delete' mod='leoslideshow'}" data-remove="{l s='Are you sure to remove it' mod='leoslideshow'}">
    <div class="col-lg-9 col-lg-offset-3">
        <a class="btn btn-default dash_trend_right" href="javascript:void(0)" onclick="return $('#module_form').submit();"><i class="icon-save"></i> {l s='Save Slider' mod='leoslideshow'}</a>
        <a id="btn-preview-slider" class="btn btn-default {if $languages|count > 1}dropdown-toggle {else}slider-preview {/if}color_danger" herf="javascript:void(0);" data-link="{$previewLink|escape:'html':'UTF-8'}&id_group={$id_group|intval}"><i class="icon-eye-open"></i> {l s='Preview This Slider' mod='leoslideshow'}</a>
    </div>
</div>
{*editor for each lang*}

{*action tool*}
<div class="col-lg-1">
<div class="slider-toolbar">
    <h4>{l s='Tools Action' mod='leoslideshow'}</h4>
    <ul>
        <li>
            <div class="btn-create" href="#" data-action="add-image">
                <i class="icon-picture"></i><br/>{l s='Add Image' mod='leoslideshow'}
            </div></li>
        <li><div class="btn-create" href="#" data-action="add-video">
                <i class="icon-youtube-play"></i><br/>{l s='Add Video' mod='leoslideshow'}
            </div></li>
        <li><div class="btn-create" href="#" data-action="add-text">
                <i class="icon-text-width"></i><br/>{l s='Add Text' mod='leoslideshow'}
            </div></li>
    </ul>
    <div class="btn-delete" data-action="delete-layer"><i class="icon-remove"></i> {l s='Delete' mod='leoslideshow'}</div>
</div>
</div>
{*editor content*}
<div class="col-lg-11">
{foreach from=$languages item=language}
<form action="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leoslideshow&leoajax=1&ajax=1&action=submitslider" method="post" enctype="multipart/form-data" class="slider-form" id="slider-form_{$language.id_lang|intval}">
    {if $languages|count > 1}
        <div class="translatable-field lang-{$language.id_lang|intval} form-language" data-action="{$language.id_lang|intval}" {if $language.id_lang != $id_language}style="display:none"{/if}>
            <div class="col-lg-12">
                <div class="col-lg-10"></div>
                <div class="col-lg-2">
                    <a href="javascript:void(0)" title="{l s='Click on it to copy text to other language' mod='leoslideshow'}" class="btn btn-success slide-copy-alllang">{l s='Duplicate' mod='leoslideshow'}</a>
                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                    {$language.iso_code}{* HTML form , no escape necessary *}
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu language-select">
                    {foreach from=$languages item=lang}
                        <li data-id-lang="{$lang.id_lang|intval}"><a href="javascript:hideOtherLanguage({$lang.id_lang|intval});" tabindex="-1">{$lang.name|escape:'html':'UTF-8'}</a></li>
                        {/foreach}
                </ul>
                </div>
            </div>
        {/if}
        <div class="col-lg-12">
            <div class="form-group layers-wrapper clearfix" id="slider-toolbar_{$language.id_lang|intval}">
            <div class="back-ground">
				<div class="title-back">
					{l s='BackGround' mod='leoslideshow'}
				</div>
				<div class="col-md-6">
					<a href="javascript:void(0)" class="btn btn-default btn-update-slider">
						<i class="icon-upload"></i> {l s='Set Background Image' mod='leoslideshow'}
					</a>
					<a href="javascript:void(0)" class="btn btn-default btn-remove-backurl" style="color:red">
						<i class="icon-remove"></i> {l s='Remove' mod='leoslideshow'}
					</a>
				</div>
				<div class="col-md-6">
					<div class="col-md-4">
						<lable>{l s='Background Color' mod='leoslideshow'}:</lable>
					</div>
					<div class="col-md-3">
						<div class="input-group">
							<input type="color" data-hex="true" class="slider-backcolor color mColorPickerInput" data-lang="{$language.id_lang|intval}" value="{if isset($sliderBack[$language.id_lang])}{$sliderBack[$language.id_lang]|escape:'html':'UTF-8'}{/if}" />
						</div>
					</div>
				</div>
			</div>    
            <div class="slider-layers bannercontainer">
                
                <div class="slider-editor-wrap" id="slider-editor-wrap_{$language.id_lang|intval}" style="width:{$sliderGroup.width|intval}px;height:{if $sliderGroup.fullwidth=="fullscreen"}{$sliderGroup.height+200|intval}{else}{$sliderGroup.height|intval}{/if}px">
                    <div class="simage">
                        <img src="{if isset($slideImg[$language.id_lang]) && $slideImg[$language.id_lang] != ""}{$slideImg[$language.id_lang]|escape:'html':'UTF-8'}{/if}" alt=""/>
                    </div>
                    <div class="slider-editor" id="slider-editor_{$language.id_lang|intval}" style="width:{$sliderGroup.width|intval}px;height:{if $sliderGroup.fullwidth=="fullscreen"}{$sliderGroup.height+200|intval}{else}{$sliderGroup.height|intval}{/if}px">

                    </div>
                </div>
                <div class="layer-video-inpts dialog-video" id="dialog-video_{$language.id_lang|intval}">
                    <table class="form">
                        <tr>
                            <td>{l s='Video Type' mod='leoslideshow'}</td>
                            <td>
                                <select name="layer_video_type" id="layer_video_type_{$language.id_lang|intval}">
                                    <option value="youtube">Youtube</option>
                                    <option value="vimeo">Vimeo</option>
                                </select>	
                            </td>
                        </tr>
                        <tr>
                            <td>{l s='Video ID' mod='leoslideshow'}</td>
                            <td><input name="layer_video_id" type="text" id="dialog_video_id_{$language.id_lang|intval}">
                                <p>{l s='for example youtube' mod='leoslideshow'}: <b>VA770wpLX-Q</b> and vimeo: <b>17631561</b> </p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label>{l s='Height' mod='leoslideshow'}</label>
                                <input name="layer_video_height" type="text" value="200">
                                <label>{l s='Width' mod='leoslideshow'}</label>
                                <input name="layer_video_width" type="text" value="300">

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="hidden" name="layer_video_thumb" id="layer_video_thumb_{$language.id_lang|intval}">
                                <div class="buttons">
                                    <div class="btn layer-find-video">{l s='Find Video' mod='leoslideshow'}</div>
                                    <div class="btn layer-apply-video apply_this_video" id="apply_this_video_{$language.id_lang|intval}" style="display:none;">{l s='Use This Video' mod='leoslideshow'}</div>
                                    <div class="btn layer-close-video btn-green">{l s='Close' mod='leoslideshow'}</div>
                                </div>
                            </td>
                        </tr>	
                    </table>
                    <div id="video-preview_{$language.id_lang|intval}"></div>
                </div>
                <div class="slider-foot">
                    <div class="layer-collection-wrapper">
                        <h4>{l s='Layer Collection' mod='leoslideshow'}</h4>
                        <div class="layer-collection" id="layer-collection_{$language.id_lang|intval}"></div>	
                    </div>
                </div>
                <div class="layer-form" id="layer-form_{$language.id_lang|intval}">
                    <h4>{l s='Edit Layer Data' mod='leoslideshow'}</h4>
                    <input type="hidden" class="layer_id" id="layer_id_{$language.id_lang|intval}" name="layer_id"/>
                    <input type="hidden" class="layer_content" id="layer_content_{$language.id_lang|intval}" name="layer_content"/>
                    <input type="hidden" class="layer_type" id="layer_type_{$language.id_lang|intval}" name="layer_type"/>
                    <input type="hidden" class="layer_type" id="layer_status_{$language.id_lang|intval}" name="layer_status"/>

                    <table class="form" style="width:100%">
                        <tr>
                            <td>
                                {l s='Class Style' mod='leoslideshow'}
                                <a href="javascript:void(0)" title="{l s='Click on it to copy text to other language' mod='leoslideshow'}" class="btn btn-success slide-copy-lang">{l s='Duplicate' mod='leoslideshow'}</a>
                            </td>
                            <td>
                                <input type="text" class="layer_class" name="layer_class" id="input-layer-class_{$language.id_lang|intval}"/>
                                <span class="buttons">
                                    <span class="btn btn-typo btn-insert-typo" id="btn-insert-typo_{$language.id_lang|intval}">{l s='Insert Typo' mod='leoslideshow'}</span>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>{l s='Caption Html' mod='leoslideshow'}
                            <a href="javascript:void(0)" title="{l s='Click on it to copy text to other language' mod='leoslideshow'}" class="btn btn-success slide-copy-lang">{l s='Duplicate' mod='leoslideshow'}</a></td>
                            <td>
                                <p>{l s='Allow insert html code' mod='leoslideshow'}</p>
                                <textarea style="height:60px" class="input-slider-caption" name="layer_caption" id="input-slider-caption_{$language.id_lang|intval}" data-for="caption-layer" ></textarea>
                                
                                <table class="text-attr">
                                    <tr>
                                        <td>{l s='font-size' mod='leoslideshow'}</td>
                                        <td>{l s='Background Color' mod='leoslideshow'}</td>
                                        <td>{l s='Text Color' mod='leoslideshow'}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select name="layer_font_size" class="layer_font_size">
                                                <option value="" selected="selected">{l s='Auto' mod='leoslideshow'}</option>
                                                {for $foo=9 to 200}
                                                <option value="{$foo|intval}px">{$foo|intval}px</option>
                                                {/for}
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="color" data-hex="true" name="layer_background_color" class="layer_background_color color mColorPickerInput" value=""/>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="color" data-hex="true" name="layer_color" class="layer_color color mColorPickerInput" value=""/>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"><br/></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>{l s='Link' mod='leoslideshow'}
                            <a href="javascript:void(0)" title="{l s='Click on it to copy text to other language' mod='leoslideshow'}" class="btn btn-success slide-copy-lang">{l s='Duplicate' mod='leoslideshow'}</a></td>
                            <td>
                                <input type="text" class="layer_link" name="layer_link" id="layer_link_{$language.id_lang|intval}">
                            </td>
                        </tr>
                        <tr>
                            <td>{l s='Open in' mod='leoslideshow'}
                            <a href="javascript:void(0)" title="{l s='Click on it to copy text to other language' mod='leoslideshow'}" class="btn btn-success slide-copy-lang">{l s='Duplicate' mod='leoslideshow'}</a></td>
                            <td>
								<select class="fixed-width-xl" name="layer_target">
									<option value="same">Same Window</option>
									<option value="new">New Window</option>
								</select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                        <tr>
                            <td>
                                {l s='Position' mod='leoslideshow'}
                                <a href="javascript:void(0)" title="{l s='Click on it to copy text to other language' mod='leoslideshow'}" class="btn btn-success slide-copy-lang">{l s='Duplicate' mod='leoslideshow'}</a>
                            </td>
                            <td>
                                <label>{l s='Top' mod='leoslideshow'}:</label><input size="3" type="text" class="layer_top" name="layer_top" id="layer_top_{$language.id_lang|intval}">
                                <label>{l s='Left' mod='leoslideshow'}:</label><input size="3" type="text" class="layer_left" name="layer_left" id="layer_left_{$language.id_lang|intval}">
                        </tr>
                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                    </table>
                    <div class="other-effect">
                        <table class="form" style="width:100%">
                            <tr>
                                <td>{l s='Transition' mod='leoslideshow'}
                                <a href="javascript:void(0)" title="{l s='Click on it to copy text to other language' mod='leoslideshow'}" class="btn btn-success slide-copy-lang">{l s='Duplicate' mod='leoslideshow'}</a></td>
                                <td>
                                    <select type="text" class="layer_transition" name="layer_transition" id="layer_transition_{$language.id_lang|intval}"> 
										<option selected="selected" value="fade">{l s='Fade' mod='leoslideshow'}</option>
                                        <option value="wipeleft">{l s='Wipe Left' mod='leoslideshow'}</option>
                                        <option value="wiperight">{l s='Wipe Right' mod='leoslideshow'}</option>
                                        <option value="wipeup">{l s='Wipe Top' mod='leoslideshow'}</option>
                                        <option value="wipedown">{l s='Wipe Bottom' mod='leoslideshow'}</option>
                                        <option value="expandleft">{l s='Expand Left' mod='leoslideshow'}</option>
                                        <option value="expandright">{l s='Expand Right' mod='leoslideshow'}</option>
                                        <option value="expandup">{l s='Expand Top' mod='leoslideshow'}</option>
                                        <option value="expanddown">{l s='Expand Bottom' mod='leoslideshow'}</option>
                                    </select>
                                </td>
                            </tr>	
                        </table>
                    </div>
                </div>
            </div>
          </div>
        </div>

        {if $languages|count > 1}
        </div>
    {/if}
    
</form>    
{/foreach}
</div>

<div class="col-lg-12 form-group clearfix">
    <div class="row">
        <div class="col-lg-9 col-lg-offset-3">
            <a class="btn btn-default dash_trend_right" href="javascript:void(0)" onclick="return $('#module_form').submit();"><i class="icon-save"></i> {l s='Save Slider' mod='leoslideshow'}</a>
        </div>
    </div>
</div>

{*script for all language*}

    <script type="text/javascript"><!--
        var ajaxfilelink = "{$ajaxfilelink}{* HTML form , no escape necessary *}";
		var leo_admin_module_link = "{$leo_admin_module_link}";
        var title_image = "{l s='Image Management' mod='leoslideshow'}";
        var psBaseModuleUri = "{$psBaseModuleUri|escape:'html':'UTF-8'}";
        var txt_input_title = "{l s='Please input title of slider for all language' mod='leoslideshow'}";
        
        $(".btn-remove-backurl").click(function(){
            var field = 'slider-image_';
            langID = findActiveLang();
            if ($('#' + field + langID).val()) {
                correctLink = "";
                $('#' + field + langID).val(correctLink);
                $("#slider-editor-wrap_"+langID+" .simage").html('');
            }
        });
 
        $(".btn-update-slider").click(function() {
            var field = 'slider-image_';
            dialog_param = {
                window: window,
                image_src: ''
            };
            $('#dialog').remove();
            $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="'+ajaxfilelink+'&lang_id='+findActiveLang()+'" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

            $('#dialog').dialog({
                title: title_image,
                close: function(event, ui) {
                    langID = findActiveLang();
                    if ($('#' + field + langID).val()) {
                        correctLink = $('#' + field + langID).val();
                        $('#' + field + langID).val(correctLink);
                        var image_src = psBaseModuleUri + correctLink;
                        if (dialog_param.image_src != ''){
                            image_src = dialog_param.image_src;
                        }
                        $("#slider-editor-wrap_"+langID+" .simage").html('<img src="' + image_src + '">');
                    }
                },
                bgiframe: false,
                width: 1024,
                height: 780,
                resizable: true,
                draggable:false,
                modal: false
            });
        });


        //--></script>
    <script type="text/javascript">
        $( document ).ready( function(){
            var $leoEditor = $(document).leoSliderEditor(); 
            var SURLIMAGE = '{$ajaxfilelink}{* HTML form , no escape necessary *}';
            var delay = '{$delay|intval}';
            
            {foreach from=$languages item=language}
                $leoEditor.countItem[{$language.id_lang|intval}] = 0;
            {/foreach}
            
            $leoEditor.process(SURLIMAGE, delay);
            
            {if $layers}
                {foreach from=$layers item=layer}
                    $leoEditor.createList( '{$layer.content}' , {$layer.langID|intval});{* HTML form , no escape necessary *}
                {/foreach}
            {/if}
                
            $('#slider-editor_'+default_language).find('.layer-active').removeClass('layer-active').trigger('click');
            
            $('.language-select li').click(function(){
                $('#slider-editor_'+$(this).data('id-lang')).find('.layer-active').removeClass('layer-active').trigger('click');
            });
            
            $(".btn-actionslider").click(function(){
                var object_e = $(this);
                if($(this).attr("href").indexOf("deleteSlider") != -1) {
                    if(!confirm('Delete Selected Slider?')) return false;
                }
                {literal}
                $.ajax({
                    type: "GET",
                    url: $(this).attr("href"),
                    data: {
                        "ajax": 1,
                    },
                    dataType: "json",
                    cache: false
                }).done( function(json) {
                    
                    if (json && json.hasError == true){
                        alert(json.errors);
                    }else{
                        if(json.error == 2) {
                            // duplicate SLIDE
                            $('#content #leo_error').remove();
                            $('#content').prepend(json.text);
    //                      $('body').scrollTo('#content #leo_error');
                            $('body').scrollTo('0');
                        }else if (json.error) {
                            alert(json.text);
                        }else{
                            var id_slide = "&id_slide="+object_e.data('id-slide');
                            if (object_e.hasClass('delete-slide') && window.location.href.indexOf(id_slide) >= 0)
                            {																		
                                window.location.href = window.location.href.replace(id_slide, "").replace("&editSlider=1", "").replace("&conf=3", "").replace("&conf=4", "") + "&showsliders=1&conf=4";							
                            }
                            else
                            {
                                if (window.location.href.indexOf("&conf=4") >= 0)
                                {
                                    location.reload();
                                }
                                else
                                {
                                    if (window.location.href.indexOf("&conf=3") >= 0)
                                    {
                                        window.location.href = window.location.href.replace("conf=3", "conf=4");									
                                    }
                                    else
                                    {
                                        window.location.href = window.location.href + "&conf=4";
                                    }
                                }
                            }
                        }
                    }
                });
                {/literal}
                return false;          
            });
            
            $(".slider-backcolor").change(function() {
                $(this).closest(".back-ground").next(".slider-layers").find(".simage").first().css("background-color",$(this).val());
            });
            
            $(".slider-backcolor").each(function() {
                if($(this).val())
                $(this).closest(".back-ground").next(".slider-layers").find(".simage").first().css("background-color",$(this).val());
            });
        });


        $(".slider-preview").click(function() {
            var url = $(this).attr("href")+"&content_only=1";
            $('#dialog').remove();
            $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe name="iframename2" src="' + url + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
            $('#dialog').dialog({
                title: 'Preview Management',
                close: function(event, ui) {

                },
                bgiframe: true,
                width: 1024,
                height: 780,
                resizable: false,
                draggable:false,
                modal: true
            });
            return false;
        });
        
        function image_upload(field, thumb) {
            $('#dialog').remove();
            dialog_param = {
                window: window,
                image_src: ''
            };

            $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="'+ajaxfilelink+'&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

            $('#dialog').dialog({
                    title: title_image,
                    close: function (event, ui) {
                        correctLink = $('#' + field).val();
                        $('#' + field).val(correctLink);
                        
                        var image_src = psBaseModuleUri+correctLink;
                        if (dialog_param.image_src != ''){
                            image_src = dialog_param.image_src;
                        }
                        $('#' + thumb).attr("src", image_src);
                        $('#' + thumb).show();
                    },
                    bgiframe: false,
                    width: 1024,
                    height: 780,
                    resizable: false,
                    draggable:false,
                    modal: false
            });
        };
    </script>
    <script type="text/javascript">
    function findActiveLang(){
        languageID = $("#current_language").val();
        if($('.form-language').length){
            $('.form-language').each(function(){
                if($(this).is(':visible')){
                    languageID = $(this).attr("data-action");
                    return false;
                }
            });
        }
        return languageID;
    }
    </script>

</fieldset>