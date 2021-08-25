/**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

$(document).ready(function() {
	$("#empty_customer").on("click", function() {
		var btn = $("#customer_btn").find("button");
		btn.find("span").empty();
		btn.find("input").val(null);
	});

	$(".mp_voucher_bulk_delete_btn").on("click", function(e){
		e.preventDefault();
		if (!$('.mp_voucher_bulk_select:checked').length) {
			alert(checkbox_select_warning);
			return false;
		}
		else {
			if(!confirm(confirm_delete_msg))
				return false;
			else
				$('#mp_voucherlist_form').submit();
		}
	});

	$("#mp_select_all_voucher").on("click", function(){
		if ($(this).is(':checked'))
		{
			$('.mp_voucher_bulk_select').parent().addClass('checker checked');
			$('.mp_voucher_bulk_select').attr('checked', 'checked');
		}
		else
		{
			$('.mp_voucher_bulk_select').parent().removeClass('checker checked');
			$('.mp_voucher_bulk_select').removeAttr('checked');
		}
	});

	$(".mp_voucher_status_disable").on("click", function(e) {
		e.preventDefault();
		alert(admin_approval_msg);
	});

	$(".voucher_change_lang").on("click", function(e) {
		e.preventDefault();

		var lang_iso_code = $(this).attr('data-lang-iso-code');
		var id_lang = $(this).attr('data-id-lang');

		$('#voucher_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');

		$('.voucher_name_all').hide();
		$('#name_'+id_lang).show();
	});

	$(".dropdown_a").on("click", function(e){
		e.preventDefault();
		var primary = $(this).attr('data-primary');
		var secondary = $(this).attr('data-secondary');

		var button = $(this).parents("ul.dropdown-menu").prev("button");
		button.find("span.span_display").html(secondary);
		button.find("input.input_primary").val(primary);
		button.find("input.input_secondary").val(secondary);
	});

	if ($(".reduction_type:checked").val() == 1) {
		$("#reduction_type_percent, #multiple_product_btn").show();
		$("#reduction_type_amount").hide();
	}
	else if ($(".reduction_type:checked").val() == 2) {
		$("#reduction_type_percent, #multiple_product_btn").hide();
		$("#reduction_type_amount").show();
	}

	$(".reduction_type").on("change", function() {
		if ($(this).val() == 1) {
			$("#reduction_type_percent, #multiple_product_btn").show(500);
			$("#reduction_type_amount").hide(500);
		}
		else if ($(this).val() == 2) {
			$("#reduction_type_percent, #multiple_product_btn").hide(500);
			$("#reduction_type_amount").show(500);

			if (parseInt($(".reduction_for:checked").val()) == 2) {
				$("#specific-product-section").show(500);
				$("#multiple-product-section").hide(500);

				$("#specific_product").prop("checked", true);
				$("#multiple_product").prop("checked", false);
			}
		}
	});

	if ($(".reduction_for:checked").val() == 1) {
        $("#specific-product-section").show();
        $("#multiple-product-section").hide();
    } else if ($(".reduction_for:checked").val() == 2) {
        $("#specific-product-section").hide();
        $("#multiple-product-section").show();
    }

	$(".reduction_for").on("change", function() {
		if ($(this).val() == 1) {
			$("#specific-product-section").show(500);
			$("#multiple-product-section").hide(500);
		} else if ($(this).val() == 2) {
			$("#specific-product-section").hide(500);
			$("#multiple-product-section").show(500);
		}
	});


	$(".restriction_type").on('click', function(){
		if ($(this).is(":checked"))
			$(this).parents("div.rest_checkbox").next("div.rest_maincont").show(500);
		else
			$(this).parents("div.rest_checkbox").next("div.rest_maincont").hide(500);
	});

	$(".restriction_type:checked").parents("div.rest_checkbox").next("div.rest_maincont").show();

	$("#generateVoucherCode").on("click", function(e){
		e.preventDefault();
		var voucherCode = '';
		/* There are no O/0 in the codes in order to avoid confusion */
		var chars = "123456789ABCDEFGHIJKLMNPQRSTUVWXYZ";
		for (var i = 1; i <= 8; ++i)
			voucherCode += chars.charAt(Math.floor(Math.random() * chars.length));

		$(this).parent().prev("input").val(voucherCode);
	});

	if ($("#mp_voucher_list").length) {
		$('#mp_voucher_list').DataTable({
			"language": {
			"lengthMenu": display_name+" _MENU_ "+records_name,
			"zeroRecords": no_product,
			"info": show_page+" _PAGE_ "+ show_of +" _PAGES_ ",
			"infoEmpty": no_record,
			"infoFiltered": "("+filter_from +" _MAX_ "+ t_record+")",
			"sSearch" : search_item,
			"oPaginate": {
				"sPrevious": p_page,
				"sNext": n_page
				}
			}
		});
	}

	if ($(".wk_datetimepicker").length) {		//If condition is used to avoid error in pages where ".wk_datetimepicker" class is not used
	    $('.wk_datetimepicker').datetimepicker({
	    	beforeShow: function (input, inst) {
		        setTimeout(function () {
		            inst.dpDiv.css({
		                'z-index': 50000
		            });
		        }, 0);
		    },
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd',
			// Define a custom regional settings in order to use PrestaShop translation tools
			currentText: currentText,
			closeText:closeText,
			ampm: false,
			amNames: ['AM', 'A'],
			pmNames: ['PM', 'P'],
			timeFormat: 'hh:mm:ss tt',
			timeSuffix: '',
			timeOnlyTitle: timeOnlyTitle,
			timeText: 'Temps',
			hourText: 'Heure',
			minuteText: 'Minute',
			
			clearText: 'Effacer', clearStatus: '',
			closeText: 'Fermer', closeStatus: 'Fermer sans modifier',
			prevText: '<Préc', prevStatus: 'Voir le mois précédent',
			nextText: 'Suiv>', nextStatus: 'Voir le mois suivant',
			currentText: 'Courant', currentStatus: 'Voir le mois courant',
			monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
			'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
			monthNamesShort: ['Jan','Fév','Mar','Avr','Mai','Jun',
			'Jul','Aoû','Sep','Oct','Nov','Déc'],
			monthStatus: 'Voir un autre mois', yearStatus: 'Voir un autre année',
			weekHeader: 'Sm', weekStatus: '',
			dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
			dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
			dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
			dayStatus: 'Utiliser DD comme premier jour de la semaine', dateStatus: 'Choisir le DD, MM d',
			dateFormat: 'dd-mm-yy', firstDay: 0, 
			initStatus: 'Choisir la date'
		});
	}

	var trigger_ajax = '';
	$('#mpReductionProductFilter').on('keyup',function(event)
	{
		var suggestion_ul = $(this).siblings("ul.suggestion_ul");
		if (!((suggestion_ul.is(':visible')) && (event.which == 40 || event.which == 38)))
		{
			if (trigger_ajax)
				trigger_ajax.abort();

			suggestion_ul.empty().hide();
			suggestion_ul.siblings("input#mp_reduction_product").val(null);

			if ($(this).val().trim().length) {
				var word = $(this).val();
				var id_seller = mp_seller_id;
				trigger_ajax = $.ajax({
		            url: controller_link,
		            type: 'POST',
		            dataType: 'json',
		            data: {
		            	ajax:true,
		            	word: word,
		            	id_seller: id_seller
		            },
		            success: function (result) {
						if (result) {
							var html = '';
							$.each(result, function(key, value) {
								html += '<li class="suggestion_li"><a class="suggestion_a" data-primary="'+value.mp_id_prod+'" data-secondary="'+value.product_name+'">'+value.product_name+'</a></li>';
							});
							suggestion_ul.html(html).show();
						}
						else {
							suggestion_ul.empty().hide();
							suggestion_ul.siblings("input#mp_reduction_product").val(null);
						}
		            }
		        });
			}
		}
	});

	$('body').on('click', '.suggestion_a', function(e) {
        e.preventDefault();
        var data_primary = $(this).attr('data-primary');
        var data_secondary = $(this).attr('data-secondary');
        var suggestion_ul = $(this).parents("ul.suggestion_ul");

        suggestion_ul.siblings("input#mp_reduction_product").val(data_primary);
        suggestion_ul.siblings("input#mpReductionProductFilter").val(data_secondary);
        suggestion_ul.empty().hide();
    });

    $('body').on('click', function(){
		if ($('ul.suggestion_ul').is(':visible')){
			$('ul.suggestion_ul').empty().hide();
		}
	});
});