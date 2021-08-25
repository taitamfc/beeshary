/**
 * @copyright Commercial License By LeoTheme.Com 
 * @email leotheme.com
 * @visit http://www.leotheme.com
 */

$(document).ready(function() {
	$('.alert.alert-info').css( 'cursor', 'pointer' );
	
	$("#module_form").validate({
		
		rules: {
			'group[blockCols]': {
				min: 5	// fix : Slideshow Iview die Timer
			}
		}
		
	});


	$('.form-action').change(function() {
		var elementName = $(this).attr('name');
		
		elementName = elementName.replace('[', '');
		elementName = elementName.replace(']', '');
		
		$('.' + elementName + '_sub').closest(".form-group").hide(300);
		$('.' + elementName + '-' + $(this).val()).closest(".form-group").show(300);
		
		$('.' + elementName + '_sub').closest(".form_sub").hide(300);
		$('.' + elementName + '-' + $(this).val()).closest(".form_sub").show(300);	// color has two class form-group
	});
	$('.form-action').trigger("change");


	// $('.sperator_form').click(function() {
		// var elementName = $(this).attr('name');
		// var flag = $(this).attr('nextClick');
		
		// if(flag=='false'){
			// flag = 'true';
			// $('.' + elementName + '_sub').closest(".form-group").hide(300);
			// $('.' + elementName + '_sub').closest(".form_sub").hide(300);
		// }else{
			// flag = 'false';
			// $('.' + elementName + '_sub').closest(".form-group").show(300);
			// $('.' + elementName + '_sub').closest(".form_sub").show(300);
			// $('.form-action').trigger("change");
		// }
		
		// $(this).attr( 'nextClick',flag);
	// });
	// $('.sperator_form').trigger("click");
			
	//DONGND:: tab change in group config
	var id_panel = $("#slideshowgeneralsetting .leoslideshow-tablist li.active a").attr("href");
	$(id_panel).addClass('active').show();
	$('.leoslideshow-tablist li').click(function(){
		if(!$(this).hasClass('active'))
		{
			var default_tab = $(this).find('a').attr("href");			
			$('#LEOSLIDESHOW_GROUP_DEFAULTTAB').val(default_tab);
		}
	})
});


(function ($) {

    $.each($.validator.methods, function (key, value) {
        $.validator.methods[key] = function () {           
            if(arguments.length > 0) {
                arguments[0] = $.trim(arguments[0]);
            }

            return value.apply(this, arguments);
        };
    });
} (jQuery));