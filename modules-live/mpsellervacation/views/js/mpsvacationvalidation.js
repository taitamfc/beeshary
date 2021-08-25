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
	$.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );
	
	$('.submit_btn').on('click',function(){
		var seller_lang_name = $('.seller_default_lang_class').data('lang-name');		
		var description = $('.seller_default_lang_class').val();		

		if ($(".fromdate").val() == "") {
			alert(startdate_error);
			return false;
		}
		else if ($(".todate").val() == "") {
			alert(enddate_error);
			return false;
		}
		else if ($(".todate").val() < $(".fromdate").val()) {
			alert(date_must_less);
			return false;
		}
		else if (description == "") {
			var total_languages = $('.total_languages').val();
			if ($('#multi_lang').val() == '1' && total_languages > 1) {
				alert(description_error+seller_lang_name);
			}
			else {
				alert(description_error_other);
			}
			
			return false;
		}
	});
	
	$.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );
	console.log('hi');

	$('.fromdate').datepicker({
		dateFormat: 'yy-mm-dd',
		minDate: 0,
	    beforeShow: function() {
	    //$(this).datepicker('option', 'maxDate', $('.todate').val());
		},
		beforeShowDay: DisableSpecificDates,
		closeText: "Fermer",
		prevText: "Précédent",
		nextText: "Suivant",
		currentText: "Aujourd'hui",
		monthNames: [ "janvier", "février", "mars", "avril", "mai", "juin",
			"juillet", "août", "septembre", "octobre", "novembre", "décembre" ],
		monthNamesShort: [ "janv.", "févr.", "mars", "avr.", "mai", "juin",
			"juil.", "août", "sept.", "oct.", "nov.", "déc." ],
		dayNames: [ "dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi" ],
		dayNamesShort: [ "dim.", "lun.", "mar.", "mer.", "jeu.", "ven.", "sam." ],
		dayNamesMin: [ "D","L","M","M","J","V","S" ],
		weekHeader: "Sem.",
		dateFormat: "dd/mm/yy",
	}).attr('readonly','readonly');

	$('.todate').datepicker({
		dateFormat: 'yy-mm-dd',
		minDate: 0,
		beforeShow: function() {
	    $(this).datepicker('option', 'minDate', $('.fromdate').val());
	    //if ($('.fromdate').val() === '') $(this).datepicker('option', 'minDate', 0);
		},
		beforeShowDay: DisableSpecificDates,
		closeText: "Fermer",
		prevText: "Précédent",
		nextText: "Suivant",
		currentText: "Aujourd'hui",
		monthNames: [ "janvier", "février", "mars", "avril", "mai", "juin",
			"juillet", "août", "septembre", "octobre", "novembre", "décembre" ],
		monthNamesShort: [ "janv.", "févr.", "mars", "avr.", "mai", "juin",
			"juil.", "août", "sept.", "oct.", "nov.", "déc." ],
		dayNames: [ "dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi" ],
		dayNamesShort: [ "dim.", "lun.", "mar.", "mer.", "jeu.", "ven.", "sam." ],
		dayNamesMin: [ "D","L","M","M","J","V","S" ],
		weekHeader: "Sem.",
		dateFormat: "dd/mm/yy",
	}).attr('readonly','readonly');
	
});


//example var disableddates1 = ["2016-5-13", "2016-5-18", "2016-5-25", "2016-5-28"];
if (typeof(dates_array) != 'undefined' && dates_array) {
	var disableddates = $.parseJSON(dates_array);
}


function DisableSpecificDates(date) {
	if (typeof(dates_array) != 'undefined' && dates_array) {
		var m = date.getMonth();
		var d = date.getDate();
		var y = date.getFullYear();

		var currentdate =  date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
		// We will now check if the date belongs to disableddates array 
		for (var i = 0; i < disableddates.length; i++) {
			// Now check if the current date is in disabled dates array. 
			if ($.inArray(currentdate, disableddates) != -1) {
				return [false, '', ''];
			}
			else {
				return [true, '', ''];
			}
		}
	} else {
		return [true, '', ''];
	}
}