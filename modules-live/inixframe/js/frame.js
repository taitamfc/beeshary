// IE mode
var isRTL = false;
var isIE8 = false;
var isIE9 = false;
var isIE10 = false;


function countDown($source, $target) {
	var max = $source.attr("data-maxchar");
	$target.html(max-$source.val().length);
	$source.keyup(function(){
		$target.html(max-$source.val().length);
	});
}

function sendBulkAction(form, action)
{
	String.prototype.splice = function(index, remove, string) {
		return (this.slice(0, index) + string + this.slice(index + Math.abs(remove)));
	};

	var form_action = $(form).attr('action');

	if (form_action.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g,'').replace(/\s+/g,' ') == '')
		return false;

	if (form_action.indexOf('#') == -1)
		$(form).attr('action', form_action + '&' + action);
	else
		$(form).attr('action', form_action.splice(form_action.lastIndexOf('&'), 0, '&' + action));

	$(form).submit();
}

function showFrameSuccessMessage(msg, title){
	if(typeof(title) != undefined){
		toastr.success(msg,title);
		return;
	}
	toastr.success(msg);
}
function showFrameErrorMessage(msg, title){
	if(typeof(title) != undefined){
		toastr.error(msg,title);
		return;
	}
	toastr.error(msg);
}

function showFrameNoticeMessage(msg, title){
	if(typeof(title) != undefined){
		toastr.info(msg,title);
		return;
	}
	toastr.info(msg);
}

function showFrameWarningMessage(msg, title){
	if(typeof(title) != undefined){
		toastr.warning(msg,title);
		return;
	}
	toastr.warning(msg);
}

function hideOtherLanguage(id)
{
	$('.translatable-field').hide();
	$('.lang-' + id).show();

	var id_old_language = id_language;
	id_language = id;



	updateCurrentText();
}

function updateCurrentText()
{
	$('#current_product').html($('#name_' + id_language).val());
}


$(document).ready(function() {
	if ($('body').css('direction') === 'rtl') {
		isRTL = true;
	}
	toastr.options = {
		"closeButton": true,
		"debug": false,
		"positionClass": "toast-top-right",
		"onclick": null,
		"showDuration": "1000",
		"hideDuration": "1000",
		"timeOut": "10000",
		"extendedTimeOut": "1000",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut"
	}
	isIE8 = !! navigator.userAgent.match(/MSIE 8.0/);
	isIE9 = !! navigator.userAgent.match(/MSIE 9.0/);
	isIE10 = !! navigator.userAgent.match(/MSIE 10.0/);

	if (isIE10) {
		jQuery('html').addClass('ie10'); // detect IE10 version
	}

	if (isIE10 || isIE9 || isIE8) {
		jQuery('html').addClass('ie'); // detect IE10 version
	}

	$("[name^='checkBoxShopGroupAsso_theme']").change(function(){
		$(this).parents('.tree-folder').find("[name^='checkBoxShopAsso_theme']").each(function(){
			var id = $(this).attr('value');
			var checked = $(this).prop('checked');
			toggleShopModuleCheckbox(id, checked);
		});
	});

	$("[name^='checkBoxShopAsso_theme']").click(function(){
		var id = $(this).attr('value');
		var checked = $(this).prop('checked');
		toggleShopModuleCheckbox(id, checked);
	});




	//bootstrap components init
	$('.dropdown-toggle').dropdown();
	$('.label-tooltip, .help-tooltip').tooltip();
	$('#error-modal').modal('show');


	if (jQuery().select2) {
		$('.select2me').select2();
	}

	if(jQuery().autosize){
		$('.textarea-autosize').autosize();
	}
	if(jQuery().colorpicker){
		$('.colorpicker-default').colorpicker({
			format: 'hex'
		});
		$('.color.colorpicker-default input.form-control').click(function(){
			$(this).closest('.colorpicker-default').colorpicker("show");
		});
	}


	if(jQuery().tagsInput){
		$('.tagsinput').tagsInput();
	}

	if(jQuery().datepicker){
		$(".framedatepicker").datepicker({
			autoclose: true
		});
	}

	if(jQuery().datetimepicker){
		$(".framedatetimepicker").datetimepicker({
			autoclose: true
		});
	}


	// 
	function toggleShopModuleCheckbox(id_shop, toggle){
		var formGroup = $("[for='to_disable_shop"+id_shop+"']").parent();
		if (toggle === true) {
			formGroup.removeClass('hide');
			formGroup.find('input').each(function(){$(this).prop('checked', 'checked');});
		}
		else {
			formGroup.addClass('hide');
			formGroup.find('input').each(function(){$(this).prop('checked', '');});
		}
	}


	//search with nav sidebar opened
	$('.page-sidebar').click(function() {
		$('#header_search .form-group').removeClass('focus-search');
	});
	$('#header_search #bo_query').on('click', function(e){
		e.stopPropagation();
		e.preventDefault();
		if($('body').hasClass('mobile-nav')){
			return false;
		}
		$('#header_search .form-group').addClass('focus-search');
	});
	
	//select list for search type
	$('#header_search_options').on('click','li a', function(e){
		e.preventDefault();
		$('#header_search_options .search-option').removeClass('active');
		$(this).closest('li').addClass('active');
		$('#bo_search_type').val($(this).data('value'));
		$('#search_type_icon').removeAttr("class").addClass($(this).data('icon'));
		$('#bo_query').attr("placeholder",$(this).data('placeholder'));
		$('#bo_query').focus();
	});



	// reset form
	$(".reset_ready").click(function () {
		var href = $(this).attr('href');
		confirm_modal(header_confirm_reset, body_confirm_reset, left_button_confirm_reset, right_button_confirm_reset,
			function () {
				window.location.href = href + '&keep_data=1';
			},
			function () {
				window.location.href = href + '&keep_data=0';
		});
		return false;
	});

	//move to hash after clicking on anchored links
	function scroll_if_anchor(href) {
		href = typeof(href) == "string" ? href : $(this).attr("href");
		var fromTop = 120;

		if(href.indexOf("#") == 0) {
			var $target = $(href);

			if($target.length) {
				$('html, body').animate({ scrollTop: $target.offset().top - fromTop });
				if(history && "pushState" in history) {
					history.pushState({}, document.title, window.location.href.split("#")[0] + href);
					return false;
				}
			}
		}
	}
	scroll_if_anchor(window.location.hash);
	$("body").on("click", "a.anchor", scroll_if_anchor);

});
