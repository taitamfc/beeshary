/*
 *  @Website: apollotheme.com - prestashop template provider
 *  @author Apollotheme <apollotheme@gmail.com>
 *  @copyright  2007-2018 Apollotheme
 *  @description: 
 */
function initFullSlider(type) {
	var total = parseInt($("#total_slider").val());
	$(".apfullslider-row").addClass("hide");
	$(".apfullslider-row input, .apfullslider-row textarea").removeAttr("name");
}
function updateListIdFullSlider() {
	var listId = "";
	var sep = "";
	$("#list-slider li").each(function() {
		listId += sep + $(this).attr("id");
		sep = "|";
	});
	$("#total_slider").val(listId);
}
$(document).ready(function() {
	$("#modal_form").draggable({
		handle: ".modal-header"
	});
	miniLeftMenu();
//    $('.addnew-group').popover({
//        html: true,
//        content: function () {
//            return $('#addnew-group-form').html();
//        }
//    });

	// NOT WORK FOR AJAX
	$('.form-action').change(function(){
		var elementName = $(this).attr('name');
		$('.'+elementName+'_sub').hide();
		$('.'+elementName+'-'+$(this).val()).show();
	});
	$('.form-action').trigger("change");

	 $('.checkbox-group').change(function(){
		id = $(this).attr('id');
		if($(this).is(':checked'))
			$('.'+id).show();
		else
			$('.'+id).hide();
	});
	$('.checkbox-group').trigger("change");


    $(document).on("click", ".hook-top", function() {
		$(".hook-content", $(this).parent()).each(function(){
			$(this).toggle('clip');
			var groupTop = $(".open-group i", $(this).parent());
			if($(groupTop).attr('class').indexOf('up') >-1){
				$(groupTop).attr('class',$(groupTop).attr('class').replace('up', 'down'));
			}else{
				$(groupTop).attr('class',$(groupTop).attr('class').replace('down', 'up'));
			}
		});
	});
	
	//DONGND:: fix can't click tab 1 when create new widget tab
	// $('.ApTabs .nav-tabs a:first').tab('show');
	// $('.ApTabs:not(#default_ApTabs)').each(function(){
		// console.log($(this).data('form'));
		// console.log($(this).attr('form'));
		// var data_form = $(this).data();
		// console.log(data_form.type);
		// console.log(data_form.form);
		// console.log(data_form['form'].active_tab);
		// $(this).find('.nav-tabs a:first').tab('show');
	// }) 
	
	$(".ApAccordions").each(function(){
	   $('.panel-collapse:first' , $(this)).collapse('show'); 
	});
	$('.btn-form-toggle').click(function () {
		if ($('.displayLeftColumn').hasClass('col-md-3')) {
			$('i', $(this)).attr('class', 'icon-resize-small');
			$(".hook-content").hide();
			$(".open-group i").attr('class', $(".open-group i").attr('class').replace('down', 'up'));
			$('.displayLeftColumn').removeClass('col-md-3').addClass('col-md-12');
			$('.displayRightColumn').removeClass('col-md-3').addClass('col-md-12');
			$('.home-content-wrapper').removeClass('col-md-6').addClass('col-md-12');
		} else {
			$('i', $(this)).attr('class', 'icon-resize-full');
			$(".hook-content").show();
			$(".open-group i").attr('class', $(".open-group i").attr('class').replace('up', 'down'));
			$('.displayLeftColumn').removeClass('col-md-12').addClass('col-md-3');
			$('.displayRightColumn').removeClass('col-md-12').addClass('col-md-3');
			$('.home-content-wrapper').removeClass('col-md-12').addClass('col-md-6');
		}
	});

	//only for product generate
	$( ".product-container .content" ).sortable({
	  revert: false
	});
	$('.element-list .plist-element').draggable({
	  connectToSortable: ".product-container .content",
	  revert: "true",
	  helper: "clone",
	  stop: function() {

	   $( ".product-container .content" ).sortable({
		revert: false
	  });
	  }
	});
	$(document).on("click", "#list-slider .btn-delete-slider", function() {
		if(confirm($("#form_content").data("delete"))) {
			$(this).closest("li").remove();
			$("#frm-slider").removeAttr("edit");
			updateListIdFullSlider();
		}
	});
	$(document).on("click", "#list-slider .btn-delete-fullslider", function() {
		if(confirm($("#form_content").data("delete"))) {
			$(this).closest("li").remove();
			$("#frm-slider").removeAttr("edit");
			updateListIdFullSlider();
		}
	});
	$(document).on("click", "#btn-add-slider", function() {
		$("#frm-slider, .apfullslider-row, #frm-block-slider").removeClass("hide");
		$(".btn-reset-slider, .btn-reset-fullslider").trigger("click");
		$("#frm-slider, #frm-block-slider").removeAttr("edit");
	});
	$(document).on("click", ".btn-cancel-slider, .btn-cancel-fullslider", function() {
		$("#frm-slider, .apfullslider-row, #frm-block-slider").addClass("hide");
	});
	// $(document).on("click", ".btn-reset-slider", function() {
	// 	$("#frm-slider, #frm-block-slider").removeAttr("edit");
	// 	$("#s-open").removeAttr("checked");
	// 	$("#s-image").attr("src", "");
	// 	$("#s-image").hide();
	// 	$("#frm-slider input, #frm-slider textarea, #frm-block-slider input, #frm-block-slider textarea").val("");
	// 	$("#s-tit").focus();
	// });
	$(document).on("click", ".btn-reset-fullslider, .btn-reset-slider", function() {
		$("#frm-slider, #frm-block-slider").removeAttr("edit");
		$(".apfullslider-row img").attr("src", "").hide();
		$(".apfullslider-row input, .apfullslider-row textarea").val("");
	});
	$(document).on("click", ".btn-edit-slider", function() {
		var li = $(this).closest("li");
		var idRow = $(li).attr("id");
		var lengthLang = Object.keys($globalthis.languages).length;
		$("#frm-slider, .apfullslider-row").removeClass("hide");
		$("#frm-slider").attr("edit", $(li).attr("id"));

		if(lengthLang > 1) {
			$(".select-img .translatable-field").each(function() {
				currentLang = $(this).data("lang");
				var tempId = idRow + "_" + currentLang;
				var img = $(li).find("#img_" + tempId).val();
				var title = $(li).find("#tit_" + tempId).val();
				var link= $(li).find("#link_" + tempId).val();
				var descript = $(li).find("#descript_" + tempId).val();
				$("#temp_title_" + currentLang).val(title);
				$("#temp_image_" + currentLang).val(img);
				// Check only diplay image for language
				if(img) {
					if($(".select-img .lang-" + currentLang).find("img").length == 0) {
						$(".select-img .lang-" + currentLang + " div:first-child").prepend("<img src='" + img + "' class='img-thumbnail'/>");
					} else {
						$(".select-img .lang-" + currentLang).find("img").attr("src", img);
						$(".select-img .lang-" + currentLang).find("img").css("display", "block");
					}
				}
				$("#temp_link_" + currentLang).val(link);
				$(".description-slide .lang-"  + currentLang + " textarea").val(descript.replace(/_APNEWLINE_/g, "&#10;"));
			});
		} else {
			currentLang = default_language;
			var tempId = idRow + "_" + currentLang;
			var img = $(li).find("#img_" + tempId).val();
			var title = $(li).find("#tit_" + tempId).val();
			var link= $(li).find("#link_" + tempId).val();
			var descript = $(li).find("#descript_" + tempId).val();
			$("#temp_title_" + currentLang).val(title);
			$("#temp_image_" + currentLang).val(img);
			// Check only diplay image for language
			if(img) {
				if($(".select-img img").length == 0) {
					$(".select-img div:first-child").prepend("<img src='" + img + "' class='img-thumbnail'/>");
				} else if(img) {
					$(".select-img img").attr("src", img);
				}
			}
			$("#temp_link_" + currentLang).val(link);
			$(".description-slide textarea").val(descript.replace(/_APNEWLINE_/g, "&#10;"));
		}
	});
	$(document).on("click", ".btn-edit-fullslider", function() {
		var li = $(this).closest("li");
		var idRow = $(li).attr("id");
		var lengthLang = Object.keys($globalthis.languages).length;
		$("#frm-slider, .apfullslider-row").removeClass("hide");
		$("#frm-slider").attr("edit", $(li).attr("id"));

		if(lengthLang > 1) {
			$(".select-img .translatable-field").each(function() {
				var currentLang = $(this).data("lang");
				var tempId = idRow + "_" + currentLang;
				var img = $(li).find("#img_" + tempId).val();
				var imgLink = imgModuleLink+img;
				var title = $(li).find("#tit_" + tempId).val();
				var sub_title = $(li).find("#sub_tit_" + tempId).val();
				var link= $(li).find("#link_" + tempId).val();
				var descript = $(li).find("#descript_" + tempId).val();
				$("#temp_title_" + currentLang).val(title);
				$("#temp_sub_title_" + currentLang).val(sub_title);
				$("#temp_image_" + currentLang).val(img);
				// Check only diplay image for language
				if(img) {
					if($(".select-img .lang-" + currentLang).find("img").length == 0) {
						$(".select-img .lang-" + currentLang + " div").first().prepend("<img src='" + imgLink + "' class='img-thumbnail'/>");
					} else if(img) {
						$(".select-img .lang-" + currentLang).find("img").attr("src", imgLink);
						$(".select-img .lang-" + currentLang).find("img").css("display", "block");
					}
				}else{
					// NOT EXIST IMAGE
					$(".select-img .lang-" + currentLang).find("img").css("display", "none");
				}
				$("#temp_link_" + currentLang).val(link);
				$(".description-slide .lang-"  + currentLang + " textarea").val(descript.replace(/_APNEWLINE_/g, "&#10;"));
			});
		} else {
			var currentLang = default_language;
			var tempId = idRow + "_" + currentLang;
			var img = $(li).find("#img_" + tempId).val();
			var imgLink = imgModuleLink+img;
			var title = $(li).find("#tit_" + tempId).val();
			var sub_title = $(li).find("#sub_tit_" + tempId).val();
			var link= $(li).find("#link_" + tempId).val();
			var descript = $(li).find("#descript_" + tempId).val();
			$("#temp_title_" + currentLang).val(title);
			$("#temp_sub_title_" + currentLang).val(sub_title);
			$("#temp_image_" + currentLang).val(img);
			// Check only diplay image for language
			if(img) {
				if($(".select-img img").length == 0) {
					$(".select-img .selectImg div").first().prepend("<img src='" + imgLink + "' class='img-thumbnail'/>");
				} else if(img) {
					$(".select-img img").attr("src", imgLink);
					$(".select-img img").css("display", "block");
				}
			}else{
				// NOT EXIST IMAGE
				$(".select-img").find("img").css("display", "none");
			}
			$("#temp_link_" + currentLang).val(link);
			$(".description-slide textarea").val(descript.replace(/_APNEWLINE_/g, "&#10;"));
		}
	});
	$(document).on("click", ".btn-save-slider", function() {
		// Validate
		// Get current language code selected
		var currentLang = default_language;
		var lengthLang = Object.keys($globalthis.languages).length;
		var temp_title = lengthLang > 1 ? ".title-slide .lang-" + default_language + " input" : ".title-slide input";
		var title = $.trim($(temp_title).val());
		var temp_sub_title = lengthLang > 1 ? ".sub-title-slide .lang-" + default_language + " input" : ".sub-title-slide input";
		var sub_title = $.trim($(temp_sub_title).val());
		var temp_image = lengthLang > 1 ? ".select-img .lang-" + default_language + " img" : ".select-img img";
		var image = $.trim($(temp_image).attr("src"));
		var imageName = $.trim($(temp_image).data("img"));
		var temp_link = lengthLang > 1 ? ".link-slide .lang-" + default_language + " input" : ".link-slide input";
		var link = $.trim($(temp_link).val());
		var temp_description = lengthLang > 1 ? ".description-slide .lang-" + default_language + " textarea" : ".description-slide textarea";
		var description = $.trim($(temp_description).val());
		var countLimit = 0;
		if(!image) {
			countLimit++;
		}
		if(!title) {
			countLimit++;
		}
		if(!sub_title) {
			countLimit++;
		}
		if(!description) {
			countLimit++;
		}
		// Require enter value for one in of [image, title, sub_title, description]
		if(countLimit == 4) {
			alert($(this).data("error"));
			return;
		}
		
		var idForm = "#frm-slider";
		var idRow = (typeof $(idForm).attr("edit") != "undefined") ? $(idForm).attr("edit") : "";
		if(!idRow) {
			var html = $("#temp-list li:first").html();
			idRow = 1;
			var arr = $("#total_slider").val().split("|");
			arr.sort(function (a, b) { return a - b; });
			for(var i = 0; i < arr.length; i++) {
				if(idRow != arr[i]) {
					break;
				}
				idRow++;
			}
			if(lengthLang > 1) {
				// console.log(idRow);
				// Duplicate for new slider and build name and id by language
				$(".select-img .translatable-field").each(function() {
					currentLang = $(this).data("lang");
					var tempId = idRow + "_" + currentLang + "'";
					html += "<input type='hidden' name='tit_" + tempId + " id='tit_" + tempId + "/>";
					html += "<input type='hidden' name='sub_tit_" + tempId + " id='sub_tit_" + tempId + "/>";
					html += "<input type='hidden' name='img_" + tempId + " id='img_" + tempId + "/>";
					html += "<input type='hidden' name='link_" + tempId + " id='link_" + tempId + "/>";
					html += "<input type='hidden' name='descript_" + tempId + " id='descript_" + tempId + "/>";
				});
			} else {
				var tempId = idRow + "_" + currentLang + "'";
				html += "<input type='hidden' name='tit_" + tempId + " id='tit_" + tempId + " value='" + title + "'/>";
				html += "<input type='hidden' name='sub_tit_" + tempId + " id='sub_tit_" + tempId + " value='" + sub_title + "'/>";
				html += "<input type='hidden' name='img_" + tempId + " id='img_" + tempId + " value='" + imageName + "'/>";
				html += "<input type='hidden' name='link_" + tempId + " id='link_" + tempId + " value='" + link + "'/>";
				html += "<input type='hidden' name='descript_" + tempId + " id='descript_" + tempId + " value='" + description + "'/>";
			}
			$("#list-slider").prepend("<li id='" + idRow + "'>" + html + "</li>");
		}
		// Update labels for diplay interface
		var label = (title ? '<div class="col-lg-5">'+ title +'</div>' : "");
		label += (image ? '<img src="' + image + '">': "");
		$("#" + idRow + " div:first").html(label);
                
		if(lengthLang > 1) {
			// Update value for other language by default language and save to dum hidden fields
			$(".select-img .translatable-field").each(function() {
				currentLang = $(this).data("lang");
				var titleOther = $.trim($(".title-slide .lang-" + currentLang + " input").val());
				var subtitleOther = $.trim($(".sub-title-slide .lang-" + currentLang + " input").val());
				var imageOther = $.trim($(".select-img #temp_image_" + currentLang).val());
				var linkOther = $.trim($(".link-slide .lang-" + currentLang + " input").val());
				var descriptionOther = $.trim($(".description-slide .lang-" + currentLang + " textarea").val());
				if(currentLang != default_language) {
					if(!titleOther) {
						titleOther = title;
						$(".title-slide .lang-" + currentLang + " input").val(title);
					}
					if(!subtitleOther) {
						subtitleOther = sub_title;
						$(".sub-title-slide .lang-" + currentLang + " input").val(sub_title);
					}
					if(!imageOther) {
						imageOther = imageName;
						$(".select-img .lang-" + currentLang + " input").val(imageOther);
					}
					if(!linkOther) {
						linkOther = link;
						$(".link-slide .lang-" + currentLang + " input").val(link);
					}
					if(!descriptionOther) {
						descriptionOther = description;
						$(".description-slide .lang-" + currentLang + " textarea").val(description);
					}
				}
				var tempId = idRow + "_" + currentLang;
                                
				$("#tit_" + tempId).val(titleOther);
				$("#sub_tit_" + tempId).val(subtitleOther);
				$("#img_" + tempId).val(imageOther);
				$("#link_" + tempId).val(linkOther);
				$("#descript_" + tempId).val(descriptionOther);
			});
		} else {
			var tempId = idRow + "_" + currentLang;
			// FIX CUSTOMER HAVE ONE LANGUAGE
			var imageName = $("#temp_image_" + default_language).val();
			$("#tit_" + tempId).val(title);
			$("#sub_tit_" + tempId).val(sub_title);
			$("#img_" + tempId).val(imageName);
			$("#link_" + tempId).val(link);
			$("#descript_" + tempId).val(description);
		}
		$(idForm).attr("edit", idRow);
		updateListIdFullSlider();
		$(idForm).addClass("hide");
		$(".apfullslider-row").addClass("hide");
	});
	/**
	* Validate and gender data for fullsilder and fill data for all language from current language selected in form
	*/
	$(document).on("click", ".btn-save-fullslider", function() {
		// Validate
		// Get current language code selected
		var currentLang = default_language;
		var lengthLang = Object.keys($globalthis.languages).length;
		var temId = lengthLang > 1 ? ".title-slide .lang-" + default_language + " input" : ".title-slide input";
		var title = $.trim($(temId).val());
		temId = lengthLang > 1 ? ".select-img .lang-" + default_language + " img" : ".select-img img";
		var image = $.trim($(temId).attr("src"));
		var imageName = $.trim($(temId).data("img"));
		temId = lengthLang > 1 ? ".link-slide .lang-" + default_language + " input" : ".link-slide input";
		var link = $.trim($(temId).val());
		temId = lengthLang > 1 ? ".description-slide .lang-" + default_language + " textarea" : ".description-slide textarea";
		var description = $.trim($(temId).val());
		var countLimit = 0;
		if(!image) {
			countLimit++;
		}
		if(!title) {
			countLimit++;
		}
		if(!description) {
			countLimit++;
		}
		// Require enter value for one in of [image, title, description]
		if(countLimit == 3) {
			alert($(this).data("error"));
			return;
		}
		
		var idForm = "#frm-slider";
		var idRow = (typeof $(idForm).attr("edit") != "undefined") ? $(idForm).attr("edit") : "";
		if(!idRow) {
			var html = $("#temp-list li:first").html();
			idRow = 1;
			var arr = $("#total_slider").val().split("|");
			arr.sort();
			for(var i = 0; i < arr.length; i++) {
				if(idRow != arr[i]) {
					break;
				}
				idRow++;
			}
			if(lengthLang > 1) {
				//console.log(idRow);
				// Duplicate for new slider and build name and id by language
				$(".select-img .translatable-field").each(function() {
					currentLang = $(this).data("lang");
					var tempId = idRow + "_" + currentLang + "'";
					html += "<input type='hidden' name='tit_" + tempId + " id='tit_" + tempId + "/>";
					html += "<input type='hidden' name='img_" + tempId + " id='img_" + tempId + "/>";
					html += "<input type='hidden' name='link_" + tempId + " id='link_" + tempId + "/>";
					html += "<input type='hidden' name='descript_" + tempId + " id='descript_" + tempId + "/>";
				});
			} else {
				var tempId = idRow + "_" + currentLang + "'";
				html += "<input type='hidden' name='tit_" + tempId + " id='tit_" + tempId + " value='" + title + "'/>";
				html += "<input type='hidden' name='img_" + tempId + " id='img_" + tempId + " value='" + image + "'/>";
				html += "<input type='hidden' name='link_" + tempId + " id='link_" + tempId + " value='" + link + "'/>";
				html += "<input type='hidden' name='descript_" + tempId + " id='descript_" + tempId + " value='" + description + "'/>";
			}
			$("#list-slider").prepend("<li id='" + idRow + "'>" + html + "</li>");
		}
		// Update labels for diplay interface
		var label = (title ? '<div class="col-lg-5">'+ title +'</div>' : "");
		label += (image ? '<img src="' + image + '">': "");
		$("#" + idRow + " div:first").html(label);

		if(lengthLang > 1) {
			// Update value for other language by default language and save to dum hidden fields
			$(".select-img .translatable-field").each(function() {
				currentLang = $(this).data("lang");
				var titleOther = $.trim($(".title-slide .lang-" + currentLang + " input").val());
				var imageOther = $.trim($(".select-img #temp_image_" + currentLang).val());
				var linkOther = $.trim($(".link-slide .lang-" + currentLang + " input").val());
				var descriptionOther = $.trim($(".description-slide .lang-" + currentLang + " textarea").val());
				if(currentLang != default_language) {
					if(!titleOther) {
						titleOther = title;
						$(".title-slide .lang-" + currentLang + " input").val(title);
					}
					if(!imageOther) {
						imageOther = imageName;
						$(".select-img .lang-" + currentLang + " input").val(imageOther);
					}
					if(!linkOther) {
						linkOther = link;
						$(".link-slide .lang-" + currentLang + " input").val(link);
					}
					if(!descriptionOther) {
						descriptionOther = description;
						$(".description-slide .lang-" + currentLang + " textarea").val(description);
					}
				}
				var tempId = idRow + "_" + currentLang;
				$("#tit_" + tempId).val(titleOther);
				$("#img_" + tempId).val(imageOther);
				$("#link_" + tempId).val(linkOther);
				$("#descript_" + tempId).val(descriptionOther);
			});
		} else {
			var tempId = idRow + "_" + currentLang;
			// FIX CUSTOMER HAVE ONE LANGUAGE
			var imageName = $("#temp_image_" + default_language).val();
			$("#tit_" + tempId).val(title);
			$("#img_" + tempId).val(imageName);
			$("#link_" + tempId).val(link);
			$("#descript_" + tempId).val(description);
		}
		$(idForm).attr("edit", idRow);
		updateListIdFullSlider();
		$(idForm).addClass("hide");
		$(".apfullslider-row").addClass("hide");
	});
	$(document).on("click", ".latest-blog-category input[type='checkbox']", function() {
		ckb = $(this).is(':checked');
		if(ckb) {
			$(this).closest("li").find('input').attr("checked", "checked");
		} else {
			$(this).closest("li").find('input').removeAttr("checked");
		}
	});
	$(document).on("click", ".list-font-awesome li", function() {
		$(".list-font-awesome li").removeClass("selected");
		$("#font_name").val($(this).find("i").data("default"));
		$(".preview-widget i").attr("class", $(this).find("i").attr("class"));
		$(this).addClass("selected");
		renderDefaultPreviewFontwesome();
	});
	$(document).on("change", "#font_type, #font_size, #is_spin", function() {
		renderDefaultPreviewFontwesome();
	});
    
    if(typeof ap_controller != "undefined"  && ap_controller == 'AdminApPageBuilderThemeConfigurationController')
    {
        initApTabForm();
    }

});
function renderDefaultPreviewFontwesome() {
	var cls = "icon " + $("#font_name").val() + " " + $("#font_type").val()
			+ " " + $("#font_size").val()
			+ " " + $("#is_spin").val();
	$(".preview-widget i").attr("class", cls);
}
/**
* Start block for module ApCategoryImage
*/
/**
 * Update status check a current category, this function is called from event in file home.js in this module
 * @param {type} obj: install of image just selected
 * @returns {Boolean}
 */
var selected_images = {};
function resetSelectedImage() {
	if(typeof selected_images != "undefined") {
		selected_images = {};
	}
}
function updateStatusCheck(obj) {
	var checkbox = $(obj).closest("span").find("input[type='checkbox']").first();
	if($(obj).attr("src-url") != "") {
		selected_images[$(checkbox).val()] = $(obj).attr("src-url");
		// Set status for checkbox
		// $(checkbox).attr("checked", "checked");
		$(obj).closest("span").find(".remove-img").removeClass("hidden");	
	} else {
		$(checkbox).removeAttr("checked");
		$(obj).closest("span").find(".remove-img").addClass("hidden");
		delete selected_images[$(checkbox).val()];
	}
	$("#category_img").val(JSON.stringify(selected_images));
	return false;
}
function intiForApCategoryImage() {
	$("#categorybox").addClass('full_loaded');  // Not load AJAX Tree Category again, For action expandAll in tree.js library
    $('#collapse-all-categorybox').hide();
    
	$("#pcategories").closest(".form-group").hide();
	$("#ptype").closest(".form-group").hide();
	$("#pproductids").closest(".form-group").hide();
	$("#pmanufacturers").closest(".form-group").hide();
	$("#source option:selected").each(function() {
		var val = $(this).val();
		$("#"+val).closest(".form-group").show();
	});
	$("#source").change(function(){
		$("#pcategories").closest(".form-group").hide();
		$("#ptype").closest(".form-group").hide();
		$("#pproductids").closest(".form-group").hide();
		$("#pmanufacturers").closest(".form-group").hide();
		var val = $(this).val();
		$("#"+val).closest(".form-group").show(500);
	});
	//hide checkbox of root node
	$("input[type=checkbox]", "#categorybox").first().hide();
	var root_id = $("input[type=checkbox]", "#categorybox").first().val();
	Array.prototype.remove = function(v) { this.splice(this.indexOf(v) == -1 ? this.length : this.indexOf(v), 1); }
	if($("#category_img").val()){
		selected_images = JSON.parse($("#category_img").val());
	}
	$("input[type=checkbox]", "#categorybox").click(function(){
		if($(this).is(":checked")) {
			//find parent category
			//all parent category must be not checked
			var check = checkParentNodes($(this));
			if(!check){
				$(this).prop("checked", false);
				alert("All parent of this category must be not checked"); 
			} else {
				$(this).closest("ul").find("ul input[type=checkbox]").removeAttr("checked");
			}
		} else {
			//$(".list-image-" + $(this).val()).remove();
			delete selected_images[$(this).val()];
		}
		$("#category_img").val(JSON.stringify(selected_images));
	});
	
	// Show selected_image when loaded page
	refreshListIcon();
	function refreshListIcon() {
		$("input[type=checkbox]", $(".form-select-icon")).each(function() {
			var listImage;
			if($(this).val() != root_id){
				listImage = $(".list-image", "#list_image_wrapper").clone(1);
				var d = new Date();
				var n = "" + d.getTime() +  Math.random();
				n = n.replace(".", "");
				var span = $(this).closest("li").find("span");
				$(listImage).find("img").attr("id", "apci_" + n);
				$(listImage).find("a").data("for", "#apci_" + n);
				listImage.appendTo($(span).first());
			}
			for(var key in selected_images){
				if(key == $(this).val()){
					image_name = selected_images[key];
					if(listImage) {
						var path = $(listImage).find("img").attr("path");
						$(listImage).find("img").attr("src", path + image_name);
						$(listImage).find("img").removeClass("hidden");
						$(listImage).find(".remove-img").removeClass("hidden");
					}
					//listImage.find(".dropdown-toggle").html(image_name+' <span class="caret"></span>');
					// Set status for checkbox
					//$(this).attr("checked", "checked");
					break;
				}
			}
			$("#category_img").val(JSON.stringify(selected_images));
				//$(this).closest("ul.tree").css("display", "none");
		});
	}

	function checkParentNodes(obj) {
		var flag = true;
		if(parent = obj.closest("ul").closest("li").find("input[type=checkbox]")){
			if(parent.val() != root_id){
				if($("input[value=" + parent.val() + "]","#categorybox").is(":checked")){
					flag = false;
				} else {
					flag = checkParentNodes(parent);
				}
			}
		}
		return flag;
	}
}
function replaceSpecialString(str){
    return str.replace(/\t/g, "_APTAB_").replace(/\r/g, "_APNEWLINE_").replace(/\n/g, "_APENTER_").replace(/"/g, "_APQUOT_").replace(/'/g, "_APAPOST_");
}
/*
* End block for module ApCategoryImage
*/

function hideFormLevel2(){
    $(".row-level2").addClass("hide");
    // Remove name
    $(".row-level2 input, .row-level2 textarea, .row-level2 select").each(function(){
        $(this).attr("data-name", $(this).attr('name'));
    });
    $(".row-level2 input, .row-level2 textarea, .row-level2 select").removeAttr("name");
}

function showFormLevel2(){
    $(".row-level2").removeClass("hide");
    // get name
    $(".row-level2 input, .row-level2 textarea, .row-level2 select").each(function(){
        $(this).attr("name", $(this).attr('data-name'));
    });
    
}

$(document).on("click", ".btn-add-level2", function() {
    $(".btn-reset-level2").trigger("click");
    showFormLevel2();
});

$(document).on("click", ".btn-reset-level2", function() {
//    $("#frm-slider, #frm-block-slider").removeAttr("edit");
    $(".row-level2 input, .row-level2 textarea").val('');
    $(".row-level2 img").not('.mColorPickerTrigger img').attr("src", "").hide();// not remove image from color_picker

    $('.mColorPickerInput.mColorPicker').each(function(){

        var val = $(this).val();
        $(this).css('background-color', val);

    });
});

// $(document).undelegate(".btn-cancel-level2", "click");
$(document).on("click", ".btn-cancel-level2", function() {
    hideFormLevel2();
    $(".btn-reset-level2").trigger("click");
    $('.frm-level2').removeAttr("edit" );
});

$(document).on("click", ".btn-save-level2", function() {
    var currentLang = default_language;
    var lengthLang = Object.keys($globalthis.languages).length;
    
    var temId = lengthLang > 1 ? ".row2-title .lang-" + default_language + " input" : ".row2-title input";
    var title = $.trim($(temId).val());
    
    var countLimit = 0;     // error
    if(!title) {
        countLimit++;
    }
    
    // Require enter value for one in of [title]
    if(countLimit > 1) {
        alert($(this).data("error"));
        return;
    }
    
    var html = $("#temp-list li:first").html();
    var idRow = (typeof $('.frm-level2').attr("edit") != "undefined") ? $('.frm-level2').attr("edit") : "";
    var action = (typeof $('.frm-level2').attr("edit") != "undefined") ? 'edit' : 'add';
    
    if(action == 'add') 
    {
        idRow = 1;
        // SORT id_Row
        var arr = $("#total_slider").val().split("|");
        arr.sort();
        for(var i = 0; i < arr.length; i++) 
        {
            if(idRow != arr[i]) 
            {
                break;
            }
            idRow++;
        }
    }
    
    // LIST INPUT NAME_idrow for temp form
    var list_input = new Array();               
    $(".row-level2 *[name^='temp_']").each(function(html){
      list_input.push(  $(this).attr('name') );
    }); 

    // LIST INPUT NAME_idrow_idlang for div
    var list_input_name = new Array();          
    $(".row-level2 *[name^='temp_']").each(function(html){
        if ( $(this).parent().parent().hasClass('translatable-field') || $(this).hasClass('js-multilang') ){
            // Input Multi-lang
            var full_name=$(this).attr('name');
            var name = full_name.slice(   0, full_name.lastIndexOf("_") );
            var lang_name =  full_name.slice(   full_name.lastIndexOf("_"),  full_name.length );
            list_input_name.push( name + '_' + idRow + lang_name );
        }else{
            // Input No-lang
            var full_name=$(this).attr('name');
            list_input_name.push( full_name + '_' + idRow);
        }
    }); 

    for (var i=0; i<list_input.length; i++)
    {
        //html += "\n<input type='hidden' name='" + list_input[i] + " id='" + list_input[i] + "/>";
        var value=  $("[name='"+list_input[i]+"']").val();
        html += "\n<input type='hidden' name='" + list_input_name[i] + "' id='" + list_input_name[i] + "' value='"+value+"'/>";
    }
    
    if(action =='add')
    {
        $("#list-slider").prepend("<li id='" + idRow + "'>" + html + "</li>");
    }else if(action =='edit')
    {
        $("#list-slider #" + idRow).html(html);
    }
    
    
    // Update labels for diplay interface
    var label = (title ? '<div class="col-lg-5">'+ title +'</div>' : "");
    
    var image = '';
    if ($(".row2-image img").length)
    {
        // Exist image
        image = $.trim( $('.row2-image img').attr('src') );
    }
    label += (image ? '<img src="' + image + '">': "");
    $("#list-slider #" + idRow + " div:first").html(label);

    $('.frm-level2').removeAttr("edit" );
    updateListIdFullSlider();

    hideFormLevel2();
    
});

$(document).on("click", "#list-slider .btn-delete-level2", function() {
    if(confirm($("#form_content").data("delete"))) {
        $(this).closest("li").remove();
        $("#frm-slider").removeAttr("edit");
        updateListIdFullSlider();
    }
});

$(document).on("click", ".btn-edit-level2", function() {
    $(".btn-reset-level2").trigger("click");
    showFormLevel2();
    var li = $(this).closest("li");
    var idRow = $(li).attr("id");
    var lengthLang = Object.keys($globalthis.languages).length;
    $(".frm-level2").attr("edit", $(li).attr("id"));

    // LIST INPUT NAME of temp_form
    var list_input = new Array();               
    $(".row-level2 *[name^='temp_']").each(function(html){
      list_input.push(  $(this).attr('name') );
    }); 

    // LIST INPUT NAME of div
    var list_input_name = new Array();          
    $(".row-level2 *[name^='temp_']").each(function(html){
        if ( $(this).parent().parent().hasClass('translatable-field') || $(this).hasClass('js-multilang') ){
            // Input Multi-lang
            var full_name=$(this).attr('name');
            var name = full_name.slice(   0, full_name.lastIndexOf("_") );
            var lang_name =  full_name.slice(   full_name.lastIndexOf("_"),  full_name.length );
            list_input_name.push( name + '_' + idRow + lang_name );
        }else{
            // Input No-lang
            var full_name=$(this).attr('name');
            list_input_name.push( full_name + '_' + idRow);
        }
    }); 
  
    // SET DATA FROM div To temp_form
    for (var i=0; i<list_input.length; i++)
    {
        var input_idrow_name = list_input_name[i];
        var input_idrow_value = $("#list-slider #" + idRow + " [name='" + list_input_name[i] +"']").val();

        $(".row-level2 [name='" + list_input[i] + "']").each(function(){
            $(this).val(input_idrow_value);

            if($(this).closest('.row-level2').hasClass('row2-image') && input_idrow_value)
            {
                // IF temp_form have image
                var imgLink = imgModuleLink+input_idrow_value;
                $(this).parent().remove('img');
                $(this).parent().prepend( "<img src='" + imgLink + "' class='img-thumbnail'/>" );
            }
        });
    }
    
    $('.mColorPickerInput.mColorPicker').each(function(){

        var val = $(this).val();
        $(this).css('background-color', val);

    });
});

function initApTabForm() {
    if ($('.aptab-config').length > 0) 
    {
        //set tab aciton
        $('.aptab-config').each(function () {
            if (!$(this).parent().hasClass('active')) {
                element = $(this).attr('href').toString().replace("#", ".");
                $(element).hide();
            }
        });

        $('.aptab-config').click(function () {
            divElement = $(this).attr('href').toString().replace("#", ".");
            aElement = $(this);
            $('.aptab-config').each(function () {
                if ($(this).parent().hasClass('active')) {
                    element = $(this).attr('href').toString().replace("#", ".");
                    $(this).parent().removeClass('active');
                    $(element).hide();
                    return false;
                }
            });
            $(divElement).show();
            $(aElement).parent().addClass('active');

            $('.form-action', $(divElement)).each(function () {
                $(this).trigger("change");
            });

            $('.checkbox-group', $(divElement)).each(function () {
                $globalthis.showOrHideCheckBox($(this));
            });


            // if ($(this).attr('href') == "#aprow_animation" && $('#animation').length > 0)
                // $('#animation').trigger("change");

        });
    }
}
