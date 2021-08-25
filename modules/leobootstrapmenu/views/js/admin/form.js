/**
 * @copyright Commercial License By LeoTheme.Com 
 * @email leotheme.com
 * @visit http://www.leotheme.com
 */
(function($) {
	$.fn.PavMegaMenuList = function(opts) {
		// default configuration
		var config = $.extend({}, {
			action:null, 
			addnew:null, 
			confirm_del:'Are you sure delete this?',
			confirm_duplicate:'Are you sure duplicate this?'
		}, opts);

		function checkInputHanlder(){
			var _updateMenuType = function(){
				$(".menu-type-group").parent().parent().hide();
				if($("[id^=url_type_]").closest('.form-group').find('.translatable-field').length)
					$("[id^=url_type_]").closest('.form-group').parent().parent().hide();
				else
					$("[id^=url_type_]").closest('.form-group').hide();
				if($("[id^=content_text_]").closest('.form-group').hasClass('translatable-field'))
					$("[id^=content_text_]").closest('.form-group').parent().parent().hide();
				else
					$("[id^=content_text_]").closest('.form-group').hide();	
				if ($("#menu_type").val() =='html' ){
					if($("[id^=content_text_]").closest('.form-group').hasClass('translatable-field'))
						$("[id^=content_text_]").closest('.form-group').parent().parent().show();
					else
						$("[id^=content_text_]").closest('.form-group').show();	
				}else if( $("#menu_type").val() =='url' ){
					if($("[id^=url_type_]").closest('.form-group').find('.translatable-field').length)
						$("[id^=url_type_]").closest('.form-group').parent().parent().show();
					else
						$("[id^=url_type_]").closest('.form-group').show();
				}
				else {
					$("#"+$("#menu_type").val()+"_type").parent().parent().show();
					if($("#menu_type").val() == 'controller')
						$("#"+$("#menu_type").val()+"_type_parameter").parent().parent().show();
				}
			};
			_updateMenuType(); 
			$("#menu_type").change(  _updateMenuType );

			// var _updateSubmenuType = function(){
				// if( $("#type_submenu").val() =='html' ){
					// $("[for^=submenu_content_text_]").parent().show();
				// }else{
					// $("[for^=submenu_content_text_]").parent().hide();
				// }
			// };
			// _updateSubmenuType();
			// $("#type_submenu").change(  _updateSubmenuType );
			
			

		}

                function manageTreeMenu(){
                    if($('ol').hasClass("sortable")){
                        $('ol.sortable').nestedSortable({
                            forcePlaceholderSize: true,
                            handle: 'div',
                            helper:	'clone',
                            items: 'li',
                            opacity: .6,
                            placeholder: 'placeholder',
                            revert: 250,
                            tabSize: 25,
                            tolerance: 'pointer',
                            toleranceElement: '> div',
                            maxLevels: 4,
                            isTree: true,
                            expandOnHover: 700,
                            startCollapsed: true,
                            stop: function(){
                                var serialized = $(this).nestedSortable('serialize');	
                                // console.log(serialized);
                                $.ajax({
                                    type: 'POST',
                                    url: config.action+"&ajax=1&doupdatepos=1&rand="+Math.random(),
                                    data : serialized+'&updatePosition=1',
                                    dataType: 'json',
                                    cache: false,
                                }).done( function (json) {
                                    if (json && json.hasError == true){
                                        alert(json.errors);
                                    }else{
                                        showSuccessMessage(json.errors);
                                    }

                                    if ($('#id_btmegamenu').val() != 0)
                                    {
                                        var id_btmegamenu = $('#id_btmegamenu').val();
                                        var id_parent;
                                        // console.log($('#list_'+id_btmegamenu).parent().parent('li'));
                                        if ($('#list_'+id_btmegamenu).parent().parent('li').length)
                                        {
                                            id_parent = $('#list_'+id_btmegamenu).parent().parent('li').data('id-menu');
                                        }
                                        else
                                        {
                                            id_parent = 0;
                                        };
                                        $('#id_parent').find('option[selected=selected]').removeAttr("selected");
                                        $('#id_parent').find('option[value='+id_parent+']').attr('selected','selected');
                                    }
                                });
                            }
                        });


                        $('#addcategory').click(function(){
                                location.href=config.addnew;
                        });
                    }
			
			
                    $('.show_cavas').change(function(){
                        var show_cavas = $(this).val();
                        //var text = $(this).val();
                        //var $this  = $(this);
                        //$(this).val( $(this).data('loading-text') );
                        $.ajax({
                            type: 'POST',
                            url: config.action+"&show_cavas=1&rand="+Math.random(),
                            data : 'show='+show_cavas+'&updatecavas=1' 
                        }).done( function (msg) {
                            //$this.val( msg );					
                            showSuccessMessage(msg);						
                        });
                    });
		}
	 	/**
	 	 * initialize every element
	 	 */
		this.each(function() {  
	 		$(".quickedit",this).click( function(){  
	 			location.href=config.action+"&id_btmegamenu="+$(this).attr('rel').replace("id_","");
	 		} );

	 		$(".quickdel",this).click( function(){  
	 			if( confirm(config.confirm_del) ){
	 				location.href=config.action+"&dodel=1&id_btmegamenu="+$(this).attr('rel').replace("id_","");
	 			}
	 			
	 		} );
                        
                    $(".delete_many_menus",this).click( function(){
                        if (confirm('Delete selected items?'))
                        {
                            var list_menu = '';
                            $('.quickselect:checkbox:checked').each(function () {
                                list_menu += $(this).val() + ",";
                                
                            });

                            if(list_menu != ''){
                                location.href=config.action+"&delete_many_menu=1&list="+list_menu;
                            }
                        }
                    });
			
			$(".quickduplicate",this).click( function(){  
	 			if( confirm(config.confirm_duplicate) ){
	 				location.href=config.action+"&doduplicate=1&id_btmegamenu="+$(this).attr('rel').replace("id_","");
	 			}
	 			
	 		} );
			
	 		manageTreeMenu();
	 		checkInputHanlder();




		});

		return this;
	};
	
})(jQuery);


jQuery(document).ready(function(){
 	
 	$("#widgetds a.btn").fancybox( {'type':'iframe'} );
 	$(".leo-modal-action, #widgets a.btn").fancybox({
	 	'type':'iframe',
	 	'width':950,
	 	'height':500,
		beforeLoad:function(){
	 		$('.inject_widget').empty().append('<option value="">Loading...</option>').attr('disabled', 'disabled');;
	 	},
	 	afterLoad:function(){
	 		 hideSomeElement();
			$('.fancybox-iframe').load( hideSomeElement );
	 	},
 		afterClose: function (event, ui) {  
			// location.reload();
			// console.log(ui);
			if(typeof _action_loadwidget !== 'undefined')
			{
				$.ajax({
					type: 'POST',
					url: _action_loadwidget,					
				}).done( function (result) {
						$('.inject_widget').empty().append(result).show().removeAttr('disabled');						
						$('#btn-inject-widget').show();
						// console.log('Load widgets sucessfull');
						//$this.val( msg );					
						//showSuccessMessage(msg);						
					}	
				);
			}
				// console.log(_action_loadwidget);
		},	
	});
	
	$(".leo-col-class input[type=radio]").click(function() {
		if (!$(this).hasClass('active'))
		{
			// classChk = $(this).attr("name").replace("col_", "");
			classChk = $(this).data("name");
			elementText = $(this).closest('.well').find('.group-class').first();
			if ($(elementText).val() != "")
			{
				var check_class_exists = false;
				if ($(".leo-col-class input[type=radio]:checked").length)
				{
					// console.log($(".leo-col-class input[type=radio]:checked").data("name"));
					// console.log($(elementText).val());
						
					$(".leo-col-class input[type=radio]:not(:checked)").each(function(){
						// console.log($(this).data("name"));
						// console.log($(elementText).val());
						var e_val = $(elementText).val();
						// $(elementText).val(e_val.replace($(this).data("name"),""));
						// console.log($(elementText).val());
						// console.log(e_val.indexOf($(this).data("name")));
						if (e_val.indexOf($(this).data("name")) != -1) {
							$(elementText).val(e_val.replace($(this).data("name"),classChk));
							check_class_exists = true;
						}
					})			
				}
				if (check_class_exists == false)
				{
					// $(elementText).val($(elementText).val() + " " + classChk);
					if ($(elementText).val() != "")
					{
						$(elementText).val($(elementText).val() + " " + classChk);
					}
					else
					{
						$(elementText).val(classChk);
					}
				}
				
				
			}
			else
			{
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
	
	var _updateGroupType = function(){
		// console.log('test');
		if( $("#group_type").val() =='horizontal' ){
			$("#show_cavas").parent().parent().show();
			$("#type_sub").parent().parent().hide();
		}else if ( $("#group_type").val() =='vertical' ){
			$("#show_cavas").parent().parent().hide();
			$("#type_sub").parent().parent().show();
		}
	};
	_updateGroupType();
	$("#group_type").change(  _updateGroupType );
	
	if($('#megamenu').length)
	{
		$("html, body").animate({ scrollTop: $('#megamenu').offset().top - 150 }, 2000);
	}
	
	//add hook to clear cache
	// $('.list_hook').change(function(){
		
	// });
	$('.clear_cache').click(function(e){
		// console.log('aaa');
		// e.stopPropagation();
		var hook_name = $('.list_hook').val();
		var href_attr = $(this).attr('href')+$('.list_hook').val();
		// console.log(href_attr);
		$(this).attr('href',href_attr);
		// location.reload(href_attr);
		// window.location.href(href_attr);
		// return false;
	})
	
	//update position for group
	if($('ol').hasClass("tree-group")){
		$('ol.tree-group').nestedSortable({
			forcePlaceholderSize: true,
			// handle: 'div',
			helper:	'clone',
			items: 'li.nav-item',
			opacity: .6,
			placeholder: 'placeholder',
			revert: 250,
			tabSize: 600,
			// tolerance: 'pointer',
			// toleranceElement: '> div',
			maxLevels: 1,

			isTree: false,
			expandOnHover: 700,
			// startCollapsed: true,
			stop: function(){ 							
				var serialized = $(this).nestedSortable('serialize');
				// console.log(serialized);
				$.ajax({
					type: 'POST',
					url: update_group_position_link+"&ajax=1&doupdategrouppos=1&rand="+Math.random(),
					data : serialized+'&updateGroupPosition=1',
                                        dataType: 'json',
                                        cache: false,
				}).done( function (json) {
                                    if (json && json.hasError == true){
                                        alert(json.errors);
                                    }else{
                                        showSuccessMessage(json.errors);
                                    }
				});
			}
		});
	}
	//disable click when editting group
	$('.editting').click(function(){
		return false;
	})
});
 var hideSomeElement = function(){
    $('body',$('.fancybox-iframe').contents()).find("#header").hide();
    $('body',$('.fancybox-iframe').contents()).find("#footer").hide();
    $('body',$('.fancybox-iframe').contents()).find(".page-head, #nav-sidebar ").hide();
    $('body',$('.fancybox-iframe').contents()).find("#content.bootstrap").css( 'padding',0).css('margin',0);
	//remove responsive table
	$('body',$('.fancybox-iframe').contents()).find('.table.btmegamenu_widgets').parent().removeClass('table-responsive-row');

 };

jQuery(document).ready(function(){
    if($("#image-images-thumbnails img").length){
	$("#image-images-thumbnails").append('<a class="del-img btn color_danger" href="#"><i class="icon-remove-sign"></i> delete image</a>');
    }
    $(".del-img").click(function(){
        if (confirm('Are you sure to delete this image?')) {
            $(this).parent().parent().html('<input type="hidden" value="1" name="delete_icon"/>');
        }
		return false;
    });
    $(".leobootstrapmenu td").attr('onclick','').unbind('click');
});