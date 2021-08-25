/**
 * @copyright Commercial License By LeoTheme.Com 
 * @email leotheme.com
 * @visit http://www.leotheme.com
 */
$(document).ready(function(){
	$("#pcategories").closest(".form-group").hide();
	$("#ptype").closest(".form-group").hide();
	$("#pproductids").closest(".form-group").hide();
	$("#pmanufacturers").closest(".form-group").hide();
	
	$( "#source option:selected" ).each(function() {
		$("#limit").closest(".form-group").hide();
		var val = $(this).val();
		$("#"+val).closest(".form-group").show(500);
		if( val != 'pproductids'){
			$("#limit").closest(".form-group").show(500);
		}
	});
	$("#source").change(function(){
		$("#pcategories").closest(".form-group").hide();
		$("#ptype").closest(".form-group").hide();
		$("#pproductids").closest(".form-group").hide();
		$("#pmanufacturers").closest(".form-group").hide();
		$("#limit").closest(".form-group").hide();
        var val = $(this).val();
        $("#"+val).closest(".form-group").show(500);
			if(val != 'pproductids')
				$("#limit").closest(".form-group").show(500);
    });
	
    //for imageproduct widget
    $("#ip_pcategories").closest(".form-group").hide();
    $("#ip_pproductids").closest(".form-group").hide();
    $( "#ip_source option:selected" ).each(function() {
        var val = $(this).val();
        $("#"+val).closest(".form-group").show();
    });
    $("#ip_source").change(function(){
        $("#ip_pcategories").closest(".form-group").hide();
        $("#ip_pproductids").closest(".form-group").hide();
        var val = $(this).val();
        $("#"+val).closest(".form-group").show(500);
    });
    //done for imageproduct widget
    //for category_image widget
    //hide checkbox of root node
    $("input[type=checkbox]", "#image_cate_tree").first().hide();
    var root_id = $("input[type=checkbox]", "#image_cate_tree").first().val();
    Array.prototype.remove = function(v) { this.splice(this.indexOf(v) == -1 ? this.length : this.indexOf(v), 1); }
    var selected_images = {};
    if($("#category_img").val()){
        selected_images = JSON.parse($("#category_img").val());
    }
    $("input[type=checkbox]", "#image_cate_tree").click(function(){
        if($(this).is(":checked")){
            //find parent category
            //all parent category must be not checked
            var check = checkParentNodes($(this));
            if(!check){          
                $(this).prop("checked",false);
                alert("All parent of this category must be not checked"); 
            }
        }else{
            $(".list-image-"+$(this).val()).remove();
            delete  selected_images[$(this).val()];
        }
    });
    $(".list-image a").click(function(){
        var selText = $(this).text();
        $(this).parents('.btn-group').find('.dropdown-toggle').html(selText+' <span class="caret"></span>');
        $(this).parents('.btn-group').find('.dropdown-menu').hide();
        if(selText != "none"){
            cate_id = $(this).parents('.btn-group').find('.dropdown-toggle').closest("li").find("input[type=checkbox]").val();
            selected_images[cate_id] = selText.trim();
        }
        return false;
    });
    $(".dropdown-toggle").click(function(){
        $(this).parents('.btn-group').find('.dropdown-menu').show();
        return false;
    });
    $(".list-image .dropdown-menu").mouseleave(function(){
        $(".list-image .dropdown-menu").hide();
        return false;
    });
    $('[name="saveleowidget"].sub_categories').click(
        function(){
             $("#category_img").val(JSON.stringify(selected_images));
    });
    $('[name="saveandstayleowidget"].sub_categories').click(
        function(){
             $("#category_img").val(JSON.stringify(selected_images));
    });
  //  show selected_image when loaded page
    $("input[type=checkbox]", $(".form-select-icon")).each(function(){
            if($(this).val() != root_id){
                listImage = $(".list-image","#list_image_wrapper").clone(1);
                listImage.addClass("list-image-"+$(this).val());
                listImage.appendTo($(this).closest("li").find("span").first());
            }
            for(var key in selected_images){
                if(key == $(this).val()){
                    image_name = selected_images[key];
                    listImage.find(".dropdown-toggle").html(image_name+' <span class="caret"></span>');
                    break;
                }
            }
            //$(this).closest("ul.tree").css("display", "none");
    });
    //$("ul.tree").css("display", "none");
    function checkParentNodes(obj){
        var flag = true;
        if(parent = obj.closest("ul").closest("li").find("input[type=checkbox]")){
            if(parent.val() != root_id){
                if($("input[value=" + parent.val() + "]","#image_cate_tree").is(":checked")){
                    flag = false;
                }else{
                    flag = checkParentNodes(parent);                  
                }
            }
       }
       return flag;
    }
	
	
	//update link type
	_updateLinkType(); 
	$("#link_type").on('change',_updateLinkType);
	var array_id_lang = [];
	if (typeof list_id_lang !== "undefined")
	{
		array_id_lang = $.parseJSON(list_id_lang);
	}
	
	//hiden tmp form
	$('.tmp').each(function(){
		if($(this).closest(".translatable-field").length)
		{
			// console.log($(this).closest(".form-group"));
			// console.log($(this).closest(".form-group").closest(".form-group"));
			if($(this).hasClass('element'))
			{
				var id = $(this).attr('id');
				id = id.substring(0, id.lastIndexOf('_'));
				var index = id.substring(id.lastIndexOf('_')+1);;
				// console.log(index);
				
				$(this).closest(".form-group").parents(".form-group").addClass('element-tmp hidden element-'+index);
			}
			else
			{
				$(this).closest(".form-group").parents(".form-group").addClass('parent-tmp hidden');
			}
			
			if(!$(this).closest(".form-group").find('.copy_lang_value').length)
				$(this).closest(".form-group").append("<button class='btn btn-info copy_lang_value'>"+copy_lang_button_text+"</button>");
		}
		else
		{
			if($(this).hasClass('element'))
			{
				
				var id = $(this).attr('id');
				if(array_id_lang.length == 1 && $(this).hasClass('element-lang'))
				{
					// console.log(array_id_lang.length);
					id = id.substring(0, id.lastIndexOf('_'));
				}
				
				var index = id.substring(id.lastIndexOf('_')+1);;
				// console.log(index);
				$(this).closest(".form-group").addClass('element-tmp hidden element-'+index);
				
			}
			else
			{
				$(this).closest(".form-group").addClass('parent-tmp hidden');
			}
			
		}
	});
	
	//display link group when edit block link
	if ($('#list_id_link').length && $('#list_id_link').val() != '')
	{
		var list_id_link = $('#list_id_link').val().split(',');
		var button_tmp = "<div class='form-group'>";
				button_tmp += "<div class='col-lg-3'></div>";
				button_tmp += "<div class='col-lg-9'>";
					button_tmp += "<button class='btn btn-primary duplicate_link'>"+duplicate_button_text+"</button>";
					button_tmp += "<button class='btn btn-danger remove_link'>"+remove_button_text+"</button>";
				button_tmp += '</div>';
			button_tmp += '</div>';
		button_tmp += '</div>';
		$.each(list_id_link, function( index, value ) {
			if (value != '')
			{
				
				//$("[id^=text_link_"+value+"]");
				// if($("[id^=text_link_"+value+"]").closest('.form-group').find('.translatable-field').length)
					// $("[id^=text_link_"+value+"]").closest('.form-group').parents('.element-tmp').before('<div class="link_group new"><hr>');
				// else
					// $("[id^=text_link_"+value+"]").closest('.element-tmp').before('<div class="link_group new"><hr>');
				
				// if($("[id^=controller_type_parameter_"+value+"]").closest('.form-group').find('.translatable-field').length)
					// $("[id^=controller_type_parameter_"+value+"]").closest('.form-group').parents('.element-tmp').after(button_tmp);
				// else
					// $("[id^=controller_type_parameter_"+value+"]").closest('.element-tmp').after(button_tmp);
				$('.element-'+value).wrapAll('<div class="link_group new">');
				$('.link_group.new').prepend('<hr>');
				$('.link_group.new').append(button_tmp);
				$('.link_group.new').data('index',value);
				$('.link_group.new .element-tmp').removeClass('element-tmp hidden');
				$('.link_group.new').removeClass('new');
				_updateLinkType(value);
				$("#link_type_"+value).on('change',function(){
					_updateLinkType(value);
				});
			}
		});
		
		$(".link_group:odd").css("background-color", "#DAE4F0");
		$(".link_group:even").css("background-color", "#FFFFFF");
	}
	
	//add new link
	
	// console.log(array_id_lang[0]);
	// console.log(array_id_lang[1]);
	$('.add-new-link').on('click',function(e){
		
		e.preventDefault();
		
		// var total_link = parseInt($("#total_link").val()) + 1;
		var total_link = getMaxIndex();
		var i=0;
		var new_link_tmp = '';
		$('.parent-tmp.hidden').each(function(){
			if (i == 0)
			{
				//$('.add-new-link').closest('.form-group').parent().append('<div class="link_group"><hr>');
				new_link_tmp += '<div class="link_group new"><hr>';
			}
			new_link_tmp += '<div class="form-group">'+$(this).html()+'</div>';
			// $('.add-new-link').closest('.form-group').parent().append('<div class="form-group new">'+$(this).html()+'</div>');
			i++;
			if (i == $('.parent-tmp.hidden').length)
			{
				// console.log('test');
				// $('.add-new-link').closest('.form-group').parent().append('</div>');
					new_link_tmp += "<div class='form-group'>";
						new_link_tmp += "<div class='col-lg-3'></div>";
						new_link_tmp += "<div class='col-lg-9'>";
							new_link_tmp += "<button class='btn btn-primary duplicate_link'>"+duplicate_button_text+"</button>";
							new_link_tmp += "<button class='btn btn-danger remove_link'>"+remove_button_text+"</button>";
						new_link_tmp += '</div>';
					new_link_tmp += '</div>';
				new_link_tmp += '</div>';
			}
				
		});
		$('.add-new-link').closest('.form-group').parent().append(new_link_tmp);
		$('.link_group.new').data('index',total_link);
		updateNewLink(total_link, true , 0);
		
	});
	
	//duplicate link - block link
	$('.duplicate_link').live('click',function(e){
		e.preventDefault();
		//var html_duplicate = $(this).closest('.link_group').html();
		var html_duplicate = $(this).closest('.link_group').clone().prop('class', 'link_group new');
		// console.log(html_duplicate);
		//html_duplicate.filter('.link_group').prop('class', 'link_group new');
		//var total_link = parseInt($("#total_link").val()) + 1;
		var total_link = getMaxIndex();
		$(this).closest('.link_group').after(html_duplicate);
		var current_index = $(this).closest('.link_group').data('index');
		$('.link_group.new').data('index',total_link);
		updateNewLink(total_link, false, current_index);
	});
	
	//remove link - block link
	$('.remove_link').live('click',function(e){
		e.preventDefault();
		if (confirm('Are you sure you want to delete?')) {
			//console.log($(this).find('.tmp'));
			$(this).closest('.link_group').find('.tmp').each(function(){
				// console.log($(this).attr('name'));
				var name_val = $(this).attr('name');
				
				if($(this).closest(".translatable-field").length)
				{
					name_val = name_val.substring(0, name_val.lastIndexOf('_'));
					updateField('remove',name_val,true);
				}
				else
				{
					updateField('remove',name_val,false);
				}
			});
			
			$(this).closest('.link_group').fadeOut(function(){
				$(this).remove();
				$(".link_group:odd").css( "background-color", "#DAE4F0" );
				$(".link_group:even").css( "background-color", "#FFFFFF" );
				var total_link = parseInt($("#total_link").val())-1;
				$("#total_link").val(total_link);
				
				$('#list_id_link').val('');
				$('.link_group').each(function(){
					$('#list_id_link').val($('#list_id_link').val()+$(this).data('index')+',');
				})
			});
			
		}	
	});
	
	//copy to other language - block link
	$('.copy_lang_value').live('click',function(e){
		e.preventDefault();
		// console.log('test');
		// console.log($(this).parent().find('.translatable-field:visible'));
		var value_copy = $(this).parent().find('.translatable-field:visible input').val();
		// console.log($(this).parent().find('.translatable-field:hidden'));
		$(this).parent().find('.translatable-field:hidden input').val(value_copy);
		$(this).text(copy_lang_button_text_done);
		var ele_obj = $(this);
		// console.log(value_copy);
		//copy_lang_button_text_done
		setTimeout(function(){ 
			ele_obj.text(copy_lang_button_text); 
		}, 2000);
	});
	
	//update value of input select - block link
	// $('.link_group input').live('keyup',function(){
		// console.log($(this).val());
	// })
	$('.link_group select').live('change',function(){
		if($(this).val() != $(this).find('option[selected=selected]').val())
		{
			
			$(this).find('option[selected=selected]').removeAttr("selected");
			$(this).find('option[value='+$(this).val()+']').attr('selected','selected');
			
		}
	});
	
    //done for category_image widget
	
	 // Check type of Carousel type - BEGIN
    $('.form-action').change(function(){
        elementName = $(this).attr('name');
        $('.'+elementName+'_sub').hide(300);
        $('.'+elementName+'-'+$(this).val()).show(500);
    });
    $('.form-action').trigger("change");
    // Check type of Carousel type - END
    
    $("#configuration_form").validate({
        rules : {
                owl_items : {
                    min : 1,
                },
                owl_rows : {
                    min : 1,
                }
            }        
    });
});

function getMaxIndex()
{
	if($('.link_group').length == 0)
	{
		return 1;
	}
	else
	{
		var list_index = [];
		$('.link_group').each(function(){
			list_index.push($(this).data('index'));
		})
		// console.log(list_index);
		return Math.max.apply(Math,list_index) + 1;
		// console.log(total_link);
	}
	
}
//update when add a new link
function updateNewLink(total_link, scroll_to_new_e, current_index)
{
	// console.log(id_language);
	var array_id_lang = $.parseJSON(list_id_lang);
	
	updateField('add','text_link_'+total_link,true);
	updateField('add','url_type_'+total_link,true);
	updateField('add','controller_type_parameter_'+total_link,true);
	
	// console.log($('.link_group.new .form-group .tmp').closest(".translatable-field").length);
	$('.link_group.new .form-group .tmp').each(function(){
		var e_obj = $(this);
		if($(this).closest(".translatable-field").length)
		{
			// console.log('aaaa');
			$.each(array_id_lang, function( index, value ) {
				// if (current_index == 0)
				// {
					// switch(e_obj.attr('id'))
					// {
						// case 'text_link_'+value:
							// e_obj.attr('id','text_link_'+total_link+'_'+value);
							// e_obj.attr('name','text_link_'+total_link+'_'+value);
							
							// break;
						// case 'url_type_'+value:
							// e_obj.attr('id','url_type_'+total_link+'_'+value);
							// e_obj.attr('name','url_type_'+total_link+'_'+value);
							
							// break;
						// case 'controller_type_parameter_'+value:
							// e_obj.attr('id','controller_type_parameter_'+total_link+'_'+value);
							// e_obj.attr('name','controller_type_parameter_'+total_link+'_'+value);
							
							// break;
					// }
				// }
				// else
				// {
					// console.log('test');
					// console.log(e_obj.attr('id'));
					switch(e_obj.attr('id'))
					{
						case 'text_link_'+current_index+'_'+value:
							e_obj.attr('id','text_link_'+total_link+'_'+value);
							e_obj.attr('name','text_link_'+total_link+'_'+value);
							
							break;
						case 'url_type_'+current_index+'_'+value:
							e_obj.attr('id','url_type_'+total_link+'_'+value);
							e_obj.attr('name','url_type_'+total_link+'_'+value);
							
							break;
						case 'controller_type_parameter_'+current_index+'_'+value:
							e_obj.attr('id','controller_type_parameter_'+total_link+'_'+value);
							e_obj.attr('name','controller_type_parameter_'+total_link+'_'+value);
							
							break;
					}
				// }
				
			});
			
		}
		else
		{
			// console.log(array_id_lang.length);
			if(array_id_lang.length == 1)
			{
				switch(e_obj.attr('id'))
				{
					case 'text_link_'+current_index+'_'+id_lang:
						e_obj.attr('id','text_link_'+total_link+'_'+id_lang);
						e_obj.attr('name','text_link_'+total_link+'_'+id_lang);
						
						break;
					case 'url_type_'+current_index+'_'+id_lang:
						e_obj.attr('id','url_type_'+total_link+'_'+id_lang);
						e_obj.attr('name','url_type_'+total_link+'_'+id_lang);
						
						break;
					case 'controller_type_parameter_'+current_index+'_'+id_lang:
						e_obj.attr('id','controller_type_parameter_'+total_link+'_'+id_lang);
						e_obj.attr('name','controller_type_parameter_'+total_link+'_'+id_lang);
						
						break;
					default:
						var old_id = e_obj.attr('id');
						var old_name = e_obj.attr('name');
						old_id = old_id.substring(0, old_id.lastIndexOf('_'));
						old_name = old_name.substring(0, old_name.lastIndexOf('_'));
						
						e_obj.attr('id',old_id+'_'+total_link);
						e_obj.attr('name',old_name+'_'+total_link);
						updateField('add',old_name+'_'+total_link, false);
						if(old_id == 'product_type' || old_id == 'cms_type' || old_id == 'category_type' || old_id == 'manufacture_type' || old_id == 'supplier_type' || old_id == 'controller_type')
						{
							if (e_obj.is( "input" ))
							{
								e_obj.attr('class','link_type_group_'+total_link+' tmp');
							}
							
							if (e_obj.is( "select" ))
							{
								e_obj.attr('class','link_type_group_'+total_link+' tmp fixed-width-xl');
							}
						}
						break;
				}
			}
			else
			{							
				// if(scroll_to_new_e == true)
				// {
					// var old_id = e_obj.attr('id');
					// var old_name = e_obj.attr('name');				
				// }
				// else
				// {
					var old_id = e_obj.attr('id');
					var old_name = e_obj.attr('name');
					old_id = old_id.substring(0, old_id.lastIndexOf('_'));
					old_name = old_name.substring(0, old_name.lastIndexOf('_'));
				// }
				e_obj.attr('id',old_id+'_'+total_link);
				e_obj.attr('name',old_name+'_'+total_link);
				updateField('add',old_name+'_'+total_link, false);
				if(old_id == 'product_type' || old_id == 'cms_type' || old_id == 'category_type' || old_id == 'manufacture_type' || old_id == 'supplier_type' || old_id == 'controller_type')
				{
					if (e_obj.is( "input" ))
					{
						e_obj.attr('class','link_type_group_'+total_link+' tmp');
					}
					
					if (e_obj.is( "select" ))
					{
						e_obj.attr('class','link_type_group_'+total_link+' tmp fixed-width-xl');
					}
				}
			}
				
		}
	});
	
	_updateLinkType(total_link);
	$("#link_type_"+total_link).on('change',function(){
		_updateLinkType(total_link);
	});
	if(scroll_to_new_e == true)
	{
		$(".link_group:odd").css("background-color", "#DAE4F0");
		$(".link_group:even").css("background-color", "#FFFFFF");
	}
	if(scroll_to_new_e == true)
	{
		$('html, body').animate({
			scrollTop: $('.link_group.new').offset().top
		}, 500, function (){
			$('.link_group.new').removeClass('new');
		});
	}
	else
	{
		setTimeout(function(){ 
			$('.link_group.new').removeClass('new'); 
			$(".link_group:odd").css("background-color", "#DAE4F0");
			$(".link_group:even").css("background-color", "#FFFFFF");
		}, 500);
		
	}
	
	
	$("#total_link").val(total_link);
}

//update list field
function updateField(action, value, is_lang)
{
	// console.log('test');
	if (action == 'add')
	{
		if (is_lang == true)
		{
			$('#list_field_lang').val($('#list_field_lang').val()+value+',');
		}
		else
		{
			$('#list_field').val($('#list_field').val()+value+',');
		}
	}
	else
	{
		// console.log('test');
		if (is_lang == true)
		{
			var old_list_field_lang = $('#list_field_lang').val();
			var new_list_field_lang = old_list_field_lang.replace(value,'');
			$('#list_field_lang').val(new_list_field_lang);
		}
		else
		{
			var old_list_field = $('#list_field').val();
			var new_list_field = old_list_field.replace(value,'');
			$('#list_field').val(new_list_field);
		}
		
	}
	
	$('#list_id_link').val('');
	$('.link_group').each(function(){
		$('#list_id_link').val($('#list_id_link').val()+$(this).data('index')+',');
	})	
}
//update link type
function _updateLinkType(total_link)
{
	if (typeof total_link === "undefined" || total_link === null) { 
		var total_link_new = ""; 
		total_link = "";
	}
	else
	{
		// var total_link_old = total_link;
		var total_link_new = '_'+total_link;
	}
	$(".link_type_group"+total_link_new).parent().parent().hide();
	if($("[id^=url_type_"+total_link+"]").closest('.form-group').find('.translatable-field').length)
		$("[id^=url_type_"+total_link+"]").closest('.form-group').parent().parent().hide();
	else
		$("[id^=url_type_"+total_link+"]").closest('.form-group').hide();
	
	if($("[id^=controller_type_parameter_"+total_link+"]").closest('.form-group').find('.translatable-field').length)
		$("[id^=controller_type_parameter_"+total_link+"]").closest('.form-group').parent().parent().hide();
	else
		$("[id^=controller_type_parameter_"+total_link+"]").closest('.form-group').hide();
	
	if($("[id^=content_text_"+total_link+"]").closest('.form-group').find('.translatable-field').length)
		$("[id^=content_text_"+total_link+"]").closest('.form-group').parent().parent().hide();
	else
		$("[id^=content_text_"+total_link+"]").closest('.form-group').hide();	
	// console.log(total_link);
	// console.log(total_link_new);
	// console.log($("#link_type"+total_link_new).val());
	if( $("#link_type"+total_link_new).val() =='url' ){
		if($("[id^=url_type_"+total_link+"]").closest('.form-group').find('.translatable-field').length)
			$("[id^=url_type_"+total_link+"]").closest('.form-group').parent().parent().show();
		else
			$("[id^=url_type_"+total_link+"]").closest('.form-group').show();
	}
	else {
		$("#"+$("#link_type"+total_link_new).val()+"_type"+total_link_new).parent().parent().show();
		if($("#link_type"+total_link_new).val() == 'controller')
		{
			// $("#"+$("#link_type").val()+"_type_parameter").parent().parent().show();
			if($("[id^=controller_type_parameter_"+total_link+"]").closest('.form-group').find('.translatable-field').length)
				$("[id^=controller_type_parameter_"+total_link+"]").closest('.form-group').parent().parent().show();
			else
				$("[id^=controller_type_parameter_"+total_link+"]").closest('.form-group').show();
		}
			
	}
}
/*
 * Owl carousel
 */
 // $(document).ready(function(){
   
 // });
 
$.validator.addMethod("owl_items_custom", function(value, element) {
    pattern_en = /^\[\[[0-9]+, [0-9]+\](, [\[[0-9]+, [0-9]+\])*\]$/;  // [[320, 1], [360, 1]]
    pattern_dis = /^0?$/
    //console.clear();
    //console.log (pattern.test(value));
    return (pattern_en.test(value) || pattern_dis.test(value));
    //return false;
}, "Please enter correctly config follow under example.");
