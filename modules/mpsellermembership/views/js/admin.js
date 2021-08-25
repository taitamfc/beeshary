/**
* 2010-2016 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

$(document).ready(function(){
	var is_free_plan_display = $("input[name='free_plan_display']:checked").val();
	if (is_free_plan_display == undefined) {
		is_free_plan_display = 1;
	}
	displayAssociatedDiv(is_free_plan_display);

	var send_warning_mail = $("input[name='warning_mail']:checked").val();
	if (send_warning_mail == undefined) {
		send_warning_mail = 0;
	}
	displayWarningDiv(send_warning_mail);
	
	$(document).on('change', "input[name='warning_mail']", function(){
		var send_warning_mail = $(this).val();
		displayWarningDiv(send_warning_mail);
	});

	$(document).on('change', "input[name='free_plan_display']", function(){
		var is_free_plan_display = $(this).val();
		displayAssociatedDiv(is_free_plan_display);
	});	

	function displayWarningDiv(send_warning_mail){
		if (send_warning_mail == 1){
			$('#warning_days_div').slideDown();
		} else {
			$('#warning_days_div').slideUp();
		}
	}

	function displayAssociatedDiv(is_free_plan_display){
		if (is_free_plan_display == 1){
			$('#num_of_products_div').slideDown();
			$('#plan_duration_div').slideDown();
		} else {
			$('#num_of_products_div').slideUp();
			$('#plan_duration_div').slideUp();
		}
	}
});