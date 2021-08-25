/**
 * Slider Editor
 *
 * @copyright Commercial License By LeoTheme.Com 
 * @email leotheme.com
 * @visit http://www.leotheme.com
 */
(function($) {

    $.fn.leoSliderEditor = function(initvar) {

        /**
         * Variables.
         */
        this.data = null;
        this.currentLayer = null;
        this.stoolbar = $(".slider-toolbar");
        this.seditor = $(".slider-editor");
        this.ilayers = $(".layer-collection");
        this.lform = $(".layer-form");
        this.siteURL = null;
        this.adminURL = null;
        this.countItem = new Array();
        this.delayTime = 9000;
        this.state = false;
        /**
         * Create List Layers By JSON Data.
         */
        this.createList = function(JSLIST, langID) {

            var list = jQuery.parseJSON(JSLIST);            
            var $this = this;
            //var $stoolbar = $( ".slider-toolbar" );	
            var layer = '';
            if (list) {
                $.each(list, function(i, jslayer) {
                    var type = list[i]['layer_type'] ? 'add-' + list[i]['layer_type'] : 'add-text';
                    $this.countItem[langID]++;
                    layer = $this.createLayer(type, list[i], list[i]['layer_id'], langID);
                });
            }
        }

        /**
         * Crete A Layer By Type with Default data or specified data.
         */
        this.createLayer = function(type, data, slayerID, langID) {
            var $this = this;
            if (!langID)
                langID = findActiveLang();

            this.seditor = $("#slider-editor_" + langID);
            this.ilayers = $("#layer-collection_" + langID);
            this.lform = $("#layer-form_" + langID);

            if (data !== null && data.layer_status == "0") {
                var layer = $('<div class="draggable-item tp-caption in-active"><div class="caption-layer"></div></div>');
                layer.attr('id', 'slayerID_' + slayerID);
                var ilayer = $('<div class="layer-index"></div>').attr("id", "i-" + layer.attr("id"));
                ilayer.append('<div class="slider-wrap"></div><div class="clearfix"></div>');
                ilayer.append('<span class="i-no">' + ($(".draggable-item", $this.seditor).length + 1) + '</span>');
                ilayer.append('<span class="layer-index-caption"></span>');
                ilayer.append('<span class="status in-active">show</span>');
            } else {
                var layer = $('<div class="draggable-item tp-caption"><div class="caption-layer"></div></div>');
                layer.attr('id', 'slayerID_' + slayerID);
                var ilayer = $('<div class="layer-index"></div>').attr("id", "i-" + layer.attr("id"));
                ilayer.append('<div class="slider-wrap"></div><div class="clearfix"></div>');
                ilayer.append('<span class="i-no">' + ($(".draggable-item", $this.seditor).length + 1) + '</span>');
                ilayer.append('<span class="layer-index-caption"></span>');
                ilayer.append('<span class="status">show</span>');
            }            

            switch (type) {
                case 'add-text':
                    if (data === null)
                        $this.addLayerText(layer, ilayer, "Your Caption Here " + $this.countItem[langID], langID);
                    else
                        $this.addLayerText(layer, ilayer, data.layer_caption, langID);
                    break;
                case 'add-video':
                    if (data === null)
                        $this.addLayerVideo(layer, ilayer, "Your Video Here " + $this.countItem[langID], langID);
                    else
                        $this.addLayerVideo(layer, ilayer, data.layer_caption, langID);
                    break;
                case 'add-image':                    
                    if (data === null)
                        $this.addLayerImage(layer, ilayer, "Your Image Here " + $this.countItem[langID], langID);
                    else
                        $this.addLayerImage(layer, ilayer, data.layer_caption, langID);
                    break;
            }


            $("#layer_id_" + langID).val(slayerID);

            // create slider timing 

            $('#islider_' + slayerID).slider({
                max: $this.delayTime,
                value: (400 * $this.countItem[langID]),
                slide: function(event, ui) {
                    $('#input-islider_' + slayerID).val(ui.value);
                }
            });
            $('#input-islider_' + slayerID).val(400 * $this.countItem[langID]);
            // auto set current active.
            $this.setCurrentLayerActive(layer, langID);
            //auto bind the drag and drap for this            

            $(layer).draggable({
                containment: "#slider-editor_" + langID,
                drag: function() {
                    $this.setCurrentLayerActive(layer, langID);
                    $this.updatePosition(layer.css('left'), layer.css("top"));
                },
                create: function() {
                    $this.createDefaultLayerData(layer, data, langID);
                }
            });


            // bind current layer be actived when this selected. 	    
            layer.click(function() {
                if (!$(this).hasClass('layer-active')) {
                    $this.setCurrentLayerActive(layer, langID);
                }
            });
            $("#i-" + layer.attr("id")).click(function() {
                if (!$(this).hasClass('layer-active')) {
                    // if ($this.currentLayer != null) {
                    // $this.storeCurrentLayerData(langID);
                    // }
                    $this.setCurrentLayerActive(layer, langID);
                }
            });
            /// insert typo
            return layer;
        };


        /**
         * Process All First Handler.
         */
        this.process = function(adminURL, delayTime) {

            this.adminURL = adminURL;
            this.delayTime = delayTime;
            var $this = this;

            $("div.btn-create", $this.stoolbar).click(function() {
                langID = findActiveLang();
                ++$this.countItem[langID];
                slayerID = langID + "_" + $this.countItem[langID];
                var layer = $this.createLayer($(this).attr("data-action"), null, slayerID);
                if ($(this).attr("data-action") == 'add-image') {
                    $this.showDialogImage('img-' + layer.attr('id'));
                }
                if ($(this).attr("data-action") == 'add-video') {
                    $this.showDialogVideo();
                }
                return false;
            });

            $(".btn-delete").click(function() {
            	if(confirm($('.text-alert').data("remove"))){
            		$this.deleteCurrentLayer();
            	}                
            });

            /////////// FORM SETTING ///////////
            // auto save when any change of element form.
            $('input, select ,textarea', '.slider-form').change(function() {
                langID = findActiveLang();
                if ($(this).hasClass('layer_top') || $(this).hasClass('layer_left')) {
                    $this.currentLayer.css({
                        top: $('.layer_top', '#slider-form_' + langID + ' #slider-toolbar_' + langID).val() + "px",
                        left: $('.layer_left', '#slider-form_' + langID + ' #slider-toolbar_' + langID).val() + "px"
                    });
                }
                $this.state = true;
                if ($this.currentLayer != null) {
                    $this.storeCurrentLayerData(langID);
                }
            });

            $('.layer_class', '.slider-form').change(function() {
                $this.currentLayer.attr("class", "draggable-item tp-caption layer-text layer-active ui-draggable " + $(this).val())
            });

            $('.layer_background_color', '.slider-form').change(function() {
                $this.currentLayer.css("background-color", $(this).val());
            });

            $('.layer_color', '.slider-form').change(function() {
                $this.currentLayer.css("color", $(this).val());
            });
            $('.layer_font_size', '.slider-form').change(function() {
                $this.currentLayer.css("font-size", $(this).val());
            });

            // auto fill text for name or any.
            $('.input-slider-caption', '.slider-form').keyup(function() {
                setTimeout(function() {
                    langID = findActiveLang();
                    $(".caption-layer", $this.currentLayer).html($('.input-slider-caption', '#slider-form_' + langID + ' #slider-toolbar_' + langID).val());
                    $('.layer-index-caption', "#i-" + $this.currentLayer.attr("id")).text($(".caption-layer", $this.currentLayer).text());
                }, 6);
                $this.state = true;
            });
            $('.input-slider-caption', '.slider-form').change(function() {
                langID = findActiveLang();
                $this.storeCurrentLayerData(langID);
            });

            /**** GLOBAL PROCESS ****/
            $('.slider-editor').each(function() {
                $(".draggable-item", $(this)).draggable({
                    containment: $(this)
                });

            });

            //$(".draggable-item", this.seditor).draggable({ containment: "#slider-toolbar .slider-editor" });

            $(".layer-collection").sortable({
                accept: "div",
                update: function(e,ui) {
					var j = 1;
					$(e.target.children).each(function(i, e) {
                        $(".i-no", $(this)).html((j++));
                        //id = 
                        editor = $(this).closest(".slider-layers").find('.slider-editor').first();                      
                        lang = $(editor).attr("id").substr($(editor).attr("id").lastIndexOf("_"));
                        ceditor = $("#slayerID"+lang+"_"+j).clone(1);
                        $("#slayerID"+lang+"_"+j).remove();
                        $(editor).prepend($(ceditor));                        
                    });
                    if($(".translatable-field").length){
                    	alert($('.text-alert').data("move"));
                    }					
                },
                // stop: function() {
                // 	console.log($this.ilayers.children());
                // 	$this.ilayers.children().each(function(){
                //     	id = $(this).attr("id");
                //     	console.log(id);                    	
                //     });
                // }
            });
            $this.ilayers.delegate('.status', 'click', function() {
                $(this).toggleClass('in-active');
                $('#' + ($(this).parent('.layer-index').attr("id").replace("i-", ""))).toggleClass("in-active");

                // togle value hidden input status
                langID = findActiveLang();
                val = $('[name="layer_status"]', '#slider-form_' + langID).val();
                val = (val === "1" ? "0" : "1");
                $('[name="layer_status"]', '#slider-form_' + langID).val(val).trigger('change');
            });

            /**
             * - Change image
             */
            $this.seditor.delegate('.btn-change-img', 'click', function() {
                $this.showDialogImage('img-' + $this.currentLayer.attr('id'));
            });

            $('.slider-editor').each(function() {
                $(this).delegate('.btn-change-video', 'click', function() {
                    $this.showDialogVideo();
                });
            });

            /**
             * Close video for layer
             */
            $(".layer-close-video").click(function() {
                langID = findActiveLang();
                $("#dialog-video_" + langID).hide();
                //$("#" + $this.currentLayer.attr('id')).remove();
                $this.deleteCurrentLayer();
            });

            /**
             * Find video for layer
             */
            $(".layer-find-video").click(function() {
                langID = findActiveLang();
                if ($("#dialog_video_id_" + langID).val()) {
                    $this.videoDialogProcess($("#dialog_video_id_" + langID).val(), langID);
                } else {
                    $("#video-preview_" + langID).html('<div class="error">Could not find any thing</div>');
                }
            });

            $('.translatable-field').first().closest('.form-group').each(function() {
                arrayLang = [];
                $(this).find('.translatable-field').each(function() {
                    arrayLang.push($(this).attr("class").substr($(this).attr("class").lastIndexOf("-") + 1));
                });
                $("#module_form").data("lang", arrayLang);
            });
            $(".slide-copy-alllang").click(function() {
            	cfrom = $(this).closest(".slider-form");                
            	clang = $(cfrom).attr("id").substr($(cfrom).attr("id").lastIndexOf("_")+1);                
            	$('.slider-form').each(function() {
            		if($(cfrom).attr("id") != $(this).attr("id")){
            			$sliderForm = $(this);           			
            			nlang = $(this).attr("id").substr($(this).attr("id").lastIndexOf("_")+1);
            			$("#slider-image_"+nlang).val($("#slider-image_"+clang).val());
            			$(this).find(".slider-backcolor").first().val($(cfrom).find(".slider-backcolor").first().val());

            			$(".slider-backcolor", $(this)).first().val($(".slider-backcolor", $(cfrom)).first().val());
            			$(this).find(".simage img").first().attr("src", $(cfrom).find(".simage img").first().attr("src"));

            			$(this).find('.slider-editor').first().html($(cfrom).find('.slider-editor').first().html());
            			$(this).find('.slider-editor .draggable-item').each(function() {             
            				count = $(this).attr("id").substr($(this).attr("id").lastIndexOf("_"));
            				$(this).attr('id', 'slayerID_'+nlang+count);                            
                            $(this).data('data-form', $('#slayerID_'+clang+count).data('data-form'));                            
            			});
                        
                        $(this).find('.layer-collection').first().html($(cfrom).find('.layer-collection').first().html());                        
                        $(this).find('.layer-collection .layer-index').each(function() {
                            count = $(this).attr("id").substr($(this).attr("id").lastIndexOf("_"));                            
                            $(this).attr('id', 'i-slayerID_'+nlang+count);                            
                        });
            		}
                });               

                alert($(".text-alert").data("duplicate"));
            })
            $(".layer-form .slide-copy-lang").click(function() {
                wraper = $(this).closest("tr").find("td")[1];
                lang = $("#module_form").data("lang");
                // ename = $(this).attr('name');

                //slayerID_
                name = $this.currentLayer.attr("id").substr(0, $this.currentLayer.attr("id").lastIndexOf("_"));
                name = name.substr(0, name.lastIndexOf("_") + 1);
                //_3
                number = $this.currentLayer.attr("id").substr($this.currentLayer.attr("id").lastIndexOf("_"));

                for (i = 0; i < lang.length; i++) {

                    thiselement = $("#" + name + lang[i] + number);
                    if ($(thiselement).attr("id") != $this.currentLayer.attr("id")) {
                        obj = $(thiselement).data("data-form");

                        for (j = 0; j < obj.length; j++) {
                            $(wraper).find("input").each(function() {
                                if ($(this).attr("name") == obj[j].name) {
                                    obj[j].value = $(this).val();
                                }
                            });
                            $(wraper).find("textarea").each(function() {
                                if ($(this).attr("name") == obj[j].name) {
                                    obj[j].value = $(this).val();
                                }
                            });
                            $(wraper).find("select").each(function() {
                                if ($(this).attr("name") == obj[j].name) {
                                    obj[j].value = $(this).val();
                                }
                            });
                        }

                        $(thiselement).data("data-form", obj);                        
                    }
                }
            });

            /**
             * Apply video for layer
             */
            $(".apply_this_video").click(function() {
                langID = findActiveLang();
                $("#video-" + $this.currentLayer.attr('id')).html('<img  width="' + $('[name="layer_video_width"]', '#slider-form_' + langID).val() + '"  height="' + $('[name="layer_video_height"]', '#slider-form_' + langID).val() + '" src="' + $("#layer_video_thumb_" + langID).val() + '"/>');
                $("#dialog-video_" + langID).hide();

                $this.storeCurrentLayerData(langID);
            });

            this.insertTypo();

            $("#btn-preview-slider").click(function() {
                $this.preview();
            });

            /** SUBMIT FORM **/
            this.submitForm();
        };

        this.submitForm = function() {
            var $this = this;
            $("#module_form").submit(function() {

                errorList = 0;
                //when add new
                if ($("#id_slide").length) {
                    $(".slider-title").each(function() {
                        if ($(this).val() == "") {
                            errorList = 1;
                            alert(txt_input_title);
                            return false;
                        }
                    });
                } else {
                    if ($("#title_" + $("#current_language").val()).val() == "" || $("#title_" + findActiveLang()).val() == "") {
                        errorList = 1;
                        alert(txt_input_title);
                    }
                }

                if (errorList)
                    return false;
                var data = [];
                var i = 0;
                var params = $("#module_form").serialize() + "&";                
                var times = '';
                $('.slider-form').each(function() {                    
                    langID = $(this).attr("id").replace("slider-form_", "");                                   
                    $(".layer-index", $(this)).each(function() {                        
                        var param = '';
                        var layerID = $(this).attr("id").substr(2);
                        layerContent = $("#"+layerID);                        
                        if($(layerContent).length){
                            console.log($(layerContent).data("data-form"));
                            $.each($(layerContent).data("data-form"), function(_, e) {
                                if ($(this).attr('name').indexOf('layer_time') == -1 && $(this).attr('name').indexOf('slider-image')) {
                                    value = e.value;
                                    if (e.name == 'layer_caption' || e.name == 'layer_link') {
                                        e.value = e.value.replace(/\&/g, '_ASM_');                                    
                                    } else if(e.name == 'layer_content' && e.value.indexOf('http') == 0) {                                        
                                        value = encodeURIComponent(e.value);
                                        console.log(value);
                                        console.log(e.value);
                                    } else if(e.name == 'layer_id') {
                                        if(e.value != layerID.replace("slayerID_", "")) {
                                            e.value = layerID.replace("slayerID_", "");
                                        }
                                    }
                                    param += 'layers_' + langID + '[' + i + '][' + e.name + ']=' + value + '&';
                                }
                            });
                            params += param + "&";
                            i++;
                        }
                    });

                    $(".input-time input", $(this)).each(function(i, e) {
                        params += $(e).attr('name') + "=" + $(e).val() + "&";
                    });
                    // console.log('test1');
                    $(".slider-backcolor", $(this)).each(function(i, e) {
                        // console.log('test');
                        params += "background_color_" + langID + "=" + $(e).val() + "&";
                    });
                });      
                $.ajax({
                    url: $(".slider-form").attr('action'),
                    dataType: "JSON",
                    type: "POST",
                    'data': params
                }).done(function(json) {
                    if (json && json.hasError == true) {
                        alert(json.errors);
                    } else {
                        if (json.error == 2) {
                            // add, edit SLIDE
                            $('#content #leo_error').remove();
                            $('#content').prepend(json.text);
                            //						$('body').scrollTo('#content #leo_error');
                            $('body').scrollTo('0');
                        } else if (json.error) {
                            alert(json.text);
                        } else {
                            if ($("#id_slide").length && $("#id_slide").val()) {
                                location.reload();
                            } else {
                                // console.log(admin_modules_link);
                                // console.log(json.text);
                                // location.reload();
                                location.assign(leo_admin_module_link + json.text);
                            }
                        }
                    }
                });
                return false;
            });
        };
        this.getFormsData = function(langID) {
            var objects = new Object();

            objects.params = new Object();
            objects.video = new Object();
            //get params
            $.each($("#module_form").serializeArray(), function(_, e) {
                if (e.name == "title_" + langID) {
                    objects.title = e.value;
                } else if (e.name == "link_" + langID) {
                    objects.link = e.value;
                } else if (e.name == "thumbnail_" + langID) {
                    objects.thumbnail = e.value;
                } else if (e.name == "image_" + langID) {
                    objects.image = e.value;
                } else if (e.name == "active_slide") {
                    objects.active = e.value;
                }
                //video
                else if (e.name == "usevideo_" + langID) {
                    objects.video["usevideo"] = e.value;
                } else if (e.name == "videoid_" + langID) {
                    objects.video["videoid"] = e.value;
                } else if (e.name == "videoauto_" + langID) {
                    objects.video["videoauto"] = e.value;
                }
                //param
                else if (e.name.indexOf("slider") != -1) {
                    e.name = e.name.replace("slider[", "");
                    e.name = e.name.replace("]", "");
                    objects.params[e.name] = e.value;
                }

            });

            var i = 0;
            objects.layers = new Object();
            $(".slider-editor .draggable-item", $("#slider-form_" + langID)).each(function() {
                var iobject = new Object();
                $.each($(this).data("data-form"), function(_, e) {
                    if ($(this).attr('name').indexOf('layer_time') == -1) {
                        var content_e = e.value;
                        // console.log(content_e);
                        content_e = content_e.replace(/"/g, "_LEO_DOUBLE_QUOTE_");
                        content_e = content_e.replace(/\\/g, "_LEO_BACKSLASH_");
                        // console.log(content_e);
                        iobject[e.name] = content_e;
                    }
                });


                iobject.time_start = $("#input-islider_" + iobject.layer_id).val();

                objects.layers[i] = iobject;
                i++;
            });

            objects.background_color = $('.slider-backcolor[data-lang=' + langID + ']').val();

            objects.times = new Object();

            // console.log(objects);
            return (JSON.stringify(objects));
        }
        this.preview = function() {

            langID = findActiveLang();
            var params = this.getFormsData(langID);
            // console.log(params);
            url = $("#btn-preview-slider").attr("data-link") + "&content_only=1&preview=1";
            url = url.replace("id_lang=" + $("#current_language").val(), "id_lang=" + langID);

            var field = 'input-layer-class';
            var $class = $("#" + field).val();

            $('#dialog').remove();

            $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><form action="' + url + '&field=' +
                encodeURIComponent(field) + '" method="post" target="iframename" id="formid"><input type="hidden" name="slider_preview_data" id="slider-preview-data"></form><iframe name="iframename" src="' + url + '&field=' +
                encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

            $("#slider-preview-data").val(params);
            $('#dialog').dialog({
                title: 'Preview Management',
                close: function(event, ui) {

                },
                bgiframe: false,
                width: 1024,
                height: 780,
                resizable: true,
                draggable: false,
                modal: true
            });
            $("#formid").submit();
        };

        this.insertTypo = function() {
            var $this = this;
            $(".btn-insert-typo").click(function() {
                langID = findActiveLang()
                var field = 'input-layer-class_' + langID;
                var $class = $("#" + field).val();
                $('#dialog').remove();

                $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="' + ajaxfilelink + '&typo=1&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

                $('#dialog').dialog({
                    title: 'Typo Management',
                    close: function(event, ui) {
                        if ($("#" + field).val()) {
                            $this.currentLayer.removeClass($class).addClass($("#" + field).val());
                            $this.storeCurrentLayerData(langID);
                        }
                    },
                    bgiframe: false,
                    width: 1024,
                    height: 780,
                    resizable: true,
                    modal: false
                });
            });
        }

        /**
         *
         */
        this.showDialogVideo = function() {
            $("#dialog-video_" + findActiveLang()).show();
            this.videoDialogProcess('');
        }
        this.videoDialogProcess = function(videoID, langID) {
            var $this = this;

            var error = false;

            if (videoID != "") {

                if ($("#layer_video_type_" + langID).val() == 'vimeo') {
                    $.getJSON('http://www.vimeo.com/api/v2/video/' + videoID + '.json?callback=?', {
                        format: "json"
                    }, function(data) {

                        $this.showVideoPreview(data[0].title, data[0].description, data[0].thumbnail_large, langID);
                    });
                } else {
                    $.getJSON("https://www.googleapis.com/youtube/v3/videos", {
                        key: "AIzaSyCd_0-QlEJxyXP8LerrvLUJZjQY3s7sbis",
                        part: "snippet,statistics",
                        id: videoID
                    }, function(data) {
                        if (data.items.length === 0) {
                            // Video not found.
                            return;
                        }
                        $this.showVideoPreview(data.items['0'].snippet.title, data.items['0'].snippet.description, data.items['0'].snippet.thumbnails.high.url, langID)
                    });
                }
            }
        };

        this.showVideoPreview = function(title, description, image, langID) {

            if (title) {
                var html = '';
                html += '<div class="video-title">' + title + '</div>';
                html += '<img src="' + image + '">';
                html += '<div class="video-description">' + description + '</div>';
                $("#layer_video_thumb_" + langID).val(image);
                $("#video-preview_" + langID).html(html);
                $("#apply_this_video_" + langID).show();
            } else {
                $("#video-preview_" + langID).html('<div class="error">Could not find any thing</div>');
            }
        }
        /**
         * Set Current Layer is Actived And Show Form Setting For It.
         */
        this.setCurrentLayerActive = function(layer, langID) {
            // console.log($(layer));
            //$(".draggable-item", this.seditor).removeClass("layer-active");
            $('#slider-editor_' + langID).find(".draggable-item").removeClass("layer-active");
            // console.log(this.seditor);
            $(layer).addClass("layer-active");
            // console.log($(layer));

            //$(".layer-index", this.layers).removeClass("layer-active");
            $('#layer-collection_' + langID).find(".layer-index").removeClass("layer-active");
            // console.log(this.layers);
            $("#i-" + layer.attr("id")).addClass("layer-active");
            // console.log($("#i-" + layer.attr("id")));
            this.currentLayer = layer;
            // console.log(this.currentLayer);
            this.showLayerForm(layer, langID);
            // console.log(this.currentLayer);
        };

        /**
         * Add Layer Having Type Text
         */
        this.addLayerText = function(layer, ilayer, caption, langID) {
            layer.addClass('layer-text');
            $(".caption-layer", layer).html(caption);
            this.seditor.append(layer);
            $("#layer_type_" + langID).val('text');
            this.ilayers.append(ilayer);
            $(".layer-index-caption", ilayer).html(caption);
        };

        /**
         * Add Layer Having Type Video: Support YouTuBe And Vimeo.
         */
        this.addLayerVideo = function(layer, ilayer, caption, langID) {
            layer.addClass('layer-content');
            $(".caption-layer", layer).html(caption);
            this.seditor.append(layer);

            this.ilayers.append(ilayer);
            $(".layer-index-caption", ilayer).html(caption);

            $("#layer_type_" + langID).val('video');
            layer.append('<div class="layer_video" id="' + 'video-' + layer.attr('id') + '"><div class="content-sample"></div></div><div class="btn-change-video">Change Video</div>');

        };

        /**
         * Add Layer Having Type Image.
         */
        this.addLayerImage = function(layer, ilayer, caption, langID) {
            layer.addClass('layer-content');
            $(".caption-layer", layer).html(caption);
            layer.append('<div class="layer_image" id="' + 'img-' + layer.attr('id') + '"><div class="content-sample"></div></div><div class="btn-change-img">Change Image</div>');
            this.seditor.append(layer);

            this.ilayers.append(ilayer);
            $(".layer-index-caption", ilayer).html(caption);

            $("#layer_type_" + langID).val('image');
            $("#layer_content_" + langID).val('');
            // show input form

        };


        this.showDialogImage = function(thumb) {
            var $this = this;
            var field = 'layer_content_' + findActiveLang();
            var $url = this.adminURL;
            dialog_param = {
                window: window,
                image_src: ''
            };

            $('#dialog').remove();
            $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="' + $url + '&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
            $('#dialog').dialog({
                title: 'Image Management',
                close: function(event, ui) {
                    if ($('#' + field).val()) {
                        correctLink = $('#' + field).val();
                        $('#' + field).val(correctLink);

                        var image_src = psBaseModuleUri + $('#' + field).val();
                        if (dialog_param.image_src != '') {
                            var image_src = dialog_param.image_src;
                        }

                        $('#' + thumb).replaceWith('<img src="' + image_src + '" alt="" id="' + thumb + '" />');
                        $this.storeCurrentLayerData(findActiveLang());
                    } else {
                        $this.storeCurrentLayerData(findActiveLang());
                        $this.deleteCurrentLayer(); // Close image
                    }
                },
                bgiframe: false,
                width: 1024,
                height: 780,
                resizable: true,
                modal: false
            });
        }

        /**
         * Delete Current Layer: Remove HTML and Data. Hidden Form When Delete All Layers.
         */
        this.deleteCurrentLayer = function() {
            var $this = this;
            if ($this.currentLayer) {
                $("#i-" + $this.currentLayer.attr("id")).remove();
                $this.currentLayer.remove();
                $this.currentLayer.data("data-form", null);
                $this.currentLayer = null;
                //		if( $(".draggable-item",$this.seditor).length <= 0 ) {
                $this.lform.hide();
                $('#dialog').remove();
                $('#dialog-video').hide();
                //		}
            } else {
                alert($(".text-alert").data("delete"));
            }
        };

        /**
         * Set Default Value For Data Element Form Of Layer With Default Setting Or Sepecified Data.
         */
        this.createDefaultLayerData = function(layer, data, langID) {
            var $this = this;
            if (data != null && data) {
                $.each(data, function(key, valu) {
                    if (key != 'layer_slider') {
                        if (key == 'layer_caption' || key == 'layer_link') {
                            if (valu)
                                valu = valu.replace(/_ASM_/g, '&').replace(/&apos;/g, "'");
                        }

                        $('[name="' + key + '"]', '#slider-form_' + langID).val(valu);
                    }

                    if (key == 'layer_top') {
                        $this.currentLayer.css('top', valu + 'px');
                    }
                    if (key == 'layer_left') {
                        $this.currentLayer.css('left', valu + 'px');
                    }
                });

                if (data['layer_type'] == 'image') {
                    var thumb = 'img-' + $this.currentLayer.attr('id');
                    //var src = $this.siteURL+"image/"+data['layer_content_'+langID];


                    var image_src = psBaseModuleUri + data['layer_content'];
                    //window.console.log(image_src);
                    if (((data['layer_content'].substr(0, 4) == 'http')) || ((data['layer_content'].substr(0, 10) == 'data:image'))) {                        
                            image_src = decodeURIComponent(data['layer_content']);                       
                    }

                    $('#' + thumb).replaceWith('<img src="' + image_src + '" alt="" id="' + thumb + '" />');
                    // this.siteURL 	
                }
                if (data['layer_type'] == 'video') {
                    var thumb = 'video-' + $this.currentLayer.attr('id');
                    var src = data['layer_video_thumb'];
                    $(".content-sample", $this.currentLayer).html('<img height="' + data['layer_video_height'] + '" width="' + data['layer_video_width'] + '" src="' + src + '"/>');
                    // this.siteURL 	
                }
                if (data['layer_type'] == 'text') {
                    $this.currentLayer.addClass(data['layer_class']);


                    $('[name="layer_font_size"]', '#slider-form_' + langID).trigger('change');
                    $('[name="layer_background_color"]', '#slider-form_' + langID).css('background-color', $('[name="layer_background_color"]', '#slider-form_' + langID).val());
                    $('[name="layer_background_color"]', '#slider-form_' + langID).trigger('change');
                    $('[name="layer_color"]', '#slider-form_' + langID).css('background-color', $('[name="layer_color"]', '#slider-form_' + langID).val());
                    $('[name="layer_color"]', '#slider-form_' + langID).trigger('change');

                    $this.currentLayer.addClass(data['layer_class']);
                }

                if (data['layer_caption'])
                    data['layer_caption'] = data['layer_caption'].replace(/_ASM_/g, '&').replace(/&apos;/g, "'");
                if (data['layer_link'] != undefined)
                    data['layer_link'] = data['layer_link'].replace(/_ASM_/g, '&').replace(/&apos;/g, "'");

                $(".caption-layer", $this.currentLayer).html(data['layer_caption']);

                $(".layer-index-caption", '#i-slayerID' + data['layer_id']).text($(".caption-layer", $this.currentLayer).text());

                $('[name="layer_time[' + data['layer_id'] + ']"]', '#slider-form_' + langID).val(data['time_start']);
                $("#islider_" + data['layer_id']).slider('value', data['time_start']);

                //$this.currentLayer = layer;
            } else {
                // Add new layer
                $('[name="layer_caption"]', '#slider-form_' + langID).val($(".caption-layer", layer).html()).trigger('change');
                $('[name="layer_left"]', '#slider-form_' + langID).val(0);
                $('[name="layer_top"]', '#slider-form_' + langID).val(0);
                $('[name="layer_class"]', '#slider-form_' + langID).val('');
                $('[name="layer_font_size"]', '#slider-form_' + langID).val('');
                $('[name="layer_background_color"]', '#slider-form_' + langID).val('');
                $('[name="layer_background_color"]', '#slider-form_' + langID).trigger('change');
                $('[name="layer_color"]', '#slider-form_' + langID).val('');
                $('[name="layer_color"]', '#slider-form_' + langID).trigger('change');
                $('[name="layer_link"]', '#slider-form_' + langID).val('');
                $('[name="layer_speed"]', '#slider-form_' + langID).val(350);
                $('[name="layer_endtime"]', '#slider-form_' + langID).val(0);
                $('[name="layer_endspeed"]', '#slider-form_' + langID).val(300);
                $('[name="layer_endanimation"]', '#slider-form_' + langID).val('auto');
                $('[name="layer_endeasing"]', '#slider-form_' + langID).val('nothing');
                $('[name="layer_status"]', '#slider-form_' + langID).val('1');
            }
            this.storeCurrentLayerData(langID);

        };

        /**
         * Update Position In Element Form Of Current When Draping.
         */
        this.updatePosition = function(left, top) {
            langID = findActiveLang();
            $('[name="layer_top"]', '#slider-form_' + langID).val(parseInt(top));
            $('[name="layer_left"]', '#slider-form_' + langID).val(parseInt(left));
            this.storeCurrentLayerData(langID);
        };

        /**
         * Show Layer Form When A Layer Is Actived.
         * Set data to form
         */
        this.showLayerForm = function(layer, langID) {
            // restore data form for
            var $currentLayer = this.currentLayer;
            if ($currentLayer.data("data-form")) {
                $.each($currentLayer.data("data-form"), function(_, kv) {
                    if ($(this).attr('name').indexOf('layer_time') == -1) {
                        $('[name="' + kv.name + '"]', '#slider-form_' + langID).val(kv.value);
                        //console.log('[name="'+kv.name+'"]' + ' : ' + kv);
                    }
                });
            }
            // Change color 2 button
            $('[name="layer_color"]', '#slider-form_' + langID).css('background-color', $('[name="layer_color"]', '#slider-form_' + langID).val());
            $('[name="layer_background_color"]', '#slider-form_' + langID).css('background-color', $('[name="layer_background_color"]', '#slider-form_' + langID).val());
            $("#layer-form_" + langID).show();
        };

        /**
         * Set Current Layer Data.
         */
        this.storeCurrentLayerData = function(langID) {
            this.state = false;

            this.currentLayer.data("data-form", $('#slider-form_' + langID).serializeArray());

        };

        //THIS IS VERY IMPORTANT TO KEEP AT THE END
        return this;
    };

})(jQuery);
/***/
function background_upload(field, thumb, ajaxfilelink, psBaseModuleUri) {
    $('#dialog').remove();

    $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="' + ajaxfilelink + '&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

    $('#dialog').dialog({
        title: 'Select Background',
        close: function(event, ui) {
            correctLink = $('#' + field).val();
            $('#' + field).val(correctLink);
            $('#' + thumb).attr("src", psBaseModuleUri + correctLink);
            $('#' + thumb).show();
        },
        bgiframe: false,
        width: 1024,
        height: 780,
        resizable: true,
        modal: false
    });
    return false;
}
$(document).ready(function() {
    $(".leo-w-option").click(function() {
        widthVal = $(this).data("width");
        html = $(this).html() + '<span class="caret"></span>';
        elementDiv = $(this).closest("div");
        $(".col-val", $(elementDiv)).val(widthVal);
        $("button", $(elementDiv)).html(html);
    });
    $(".choise-class").click(function() {
        $(this).parent().find('input[type=checkbox]').trigger("click");
    });
    $(".leo-col-class input[type=radio]").click(function() {
        if (!$(this).hasClass('active')) {
            // classChk = $(this).attr("name").replace("col_", "");
            classChk = $(this).data("name");
            elementText = $(this).closest('.well').find('.group-class').first();
            if ($(elementText).val() != "") {
                var check_class_exists = false;
                if ($(".leo-col-class input[type=radio]:checked").length) {
                    // console.log($(".leo-col-class input[type=radio]:checked").data("name"));
                    // console.log($(elementText).val());

                    $(".leo-col-class input[type=radio]:not(:checked)").each(function() {
                        // console.log($(this).data("name"));
                        // console.log($(elementText).val());
                        var e_val = $(elementText).val();
                        // $(elementText).val(e_val.replace($(this).data("name"),""));
                        // console.log($(elementText).val());
                        //console.log(e_val.indexOf($(this).data("name")));
                        if (e_val.indexOf($(this).data("name")) != -1) {
                            $(elementText).val(e_val.replace($(this).data("name"), classChk));
                            check_class_exists = true;
                        }
                    })
                }
                if (check_class_exists == false) {
                    // $(elementText).val($(elementText).val() + " " + classChk);
                    if ($(elementText).val() != "") {
                        $(elementText).val($(elementText).val() + " " + classChk);
                    } else {
                        $(elementText).val(classChk);
                    }
                }


            } else {
                $(elementText).val(classChk);
            }
            $(".leo-col-class input.active").removeClass('active');
            $(this).addClass('active');
        }
        // $(elementText).val('');
        // $(elementText).val(classChk);
        //add
        // if ($(this).is(':checked')) {
        // if ($(elementText).val().indexOf(classChk) == -1) {
        // if ($(elementText).val() != "") {
        // $(elementText).val($(elementText).val() + " " + classChk);
        // } else {
        // $(elementText).val(classChk);
        // }
        // }
        // } else {
        //remove
        // if ($(elementText).val().indexOf(classChk) != -1) {
        // $(elementText).val($(elementText).val().replace(classChk + " ", ""));
        // $(elementText).val($(elementText).val().replace(" " + classChk, ""));
        // $(elementText).val($(elementText).val().replace(classChk, ""));
        // }
        // }
    });

    $(".group-class").change(function() {
        elementChk = $(this).closest('.well').find('input[type=checkbox]');
        classText = $(this).val();
        $(elementChk).each(function() {
            classChk = $(this).attr("name").replace("col_", "");
            if (classText.indexOf(classChk) != -1) {
                if (!$(this).is(':checked'))
                    $(this).prop("checked", true);
            } else {
                $(this).prop("checked", false);
            }
        });
    });

    $(".group-class").trigger('change');
    $(".slideshow-mode").change(function() {
        classShow = "mode-" + $(this).val();
        $('.mode-width').each(function() {
            if ($(this).hasClass(classShow))
                $(this).closest('.form-group').show();
            else
                $(this).closest('.form-group').hide();
        });
    });
    $(".slideshow-mode").trigger('change');

    //DONGND:: move form javascript inline 
    if (typeof leo_slider_list_link != 'undefined' && leo_slider_list_link.length) {
        var $mySlides = $("#slides");
        $mySlides.sortable({
            opacity: 0.6,
            cursor: "move",
            update: function() {
                var order = $(this).sortable("serialize") + "&action=updateSlidesPosition";
                $.post(leo_slider_list_link, order).done(function() {
                    showSuccessMessage(update_success_msg);
                });
            }
        });
        $mySlides.hover(function() {
                $(this).css("cursor", "move");
            },
            function() {
                $(this).css("cursor", "auto");
            });
    }

    $("#module_form .translatable-field").each(function() {
        $('<a href="javascript:void(0)" title="Click on it to copy text to other language" class="btn btn-success slide-copy-lang">Duplicate</a>').insertAfter($(this).find('.dropdown-toggle')).click(function() {
            input = $(this).closest('.translatable-field').find('div').first().find('input').first();
            textarea = $(this).closest('.translatable-field').find('div').first().find('textarea').first();
            //ifame = $(this).closest('.translatable-field').find('div').first().find('iframe').first();
            image = $(this).closest('.translatable-field').find('div').first().find('img').first();
            isvideo = 0
            if (isvideo = $(this).closest('.translatable-field').find("select[name^='usevideo']").length) {
                usevideo = $(this).closest('.translatable-field').find("select[name^='usevideo']").val();
                videoauto = $(this).closest('.translatable-field').find("select[name^='videoauto']").val();
            }

            $(this).closest('.translatable-field').parent().closest('.form-group').find('.translatable-field').each(function() {
                $(this).find('div').first().find('input').each(function() {
                    $(this).val($(input).val());
                });

                $(this).find('div').first().find('textarea').each(function() {
                    $(this).val($(textarea).val());
                });
                $(this).find('div').first().find('img').each(function() {
                    $(this).attr('src', $(image).attr('src'));
                    $(this).attr('data-img', $(image).attr('data-img'));
                });

                $(this).find('div').first().find('img').each(function() {
                    $(this).attr('src', $(image).attr('src'));
                    $(this).attr('data-img', $(image).attr('data-img'));
                });
                if (isvideo) {
                    $(this).find('div').first().find("select[name^='usevideo']").each(function() {
                        $(this).val(usevideo);
                    });
                    $(this).find('div').first().find("select[name^='videoauto']").each(function() {
                        $(this).val(videoauto);
                    });
                }
            })
        });

    });

});