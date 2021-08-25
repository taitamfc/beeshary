/**
 * 2010-2018 Webkul.
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
 *  @copyright 2010-2018 Webkul IN
 *  @license   https://store.webkul.com/license.html
 */

function validateUploadCsvForm() {
	var product_info = $("#product_info").val();
	var product_image = $("#product_image").val();
	var re1 = /(\.csv)$/i;
	var re2 = /(\.zip)$/i;

	if (product_info == "") {
		$(".error_csv").html("please select csv file!!!");
		$(".error_csv").css('display', 'block');
		return false;
	} else if (!re1.exec(product_info)) {
		$(".error_csv").html("Invalid File Extension, please upload .csv file!");
		$(".error_csv").css('display', 'block');
		return false;
	}
	if (product_image) {
		if (!re2.exec(product_image)) {
			$(".error_zip").html("Invalid File Extension, please upload .zip file!");
			$(".error_zip").css('display', 'block');
			$(".error_csv").css('display', 'none');
			return false;
		}
	}
	return true;
}

$(document).ready(function () {
	$('#marketplace_mass_upload_form_submit_btn').on('click', function(e) {
        setTimeout(function(){ $('#wk_export_zip').submit(); }, 3000);
    //     setTimeout(function(){ window.location.href = exportControllerLink;
    // }, 5000);
    });
	//export product js
	//$("#marketplace_mass_upload_form .form-wrapper .form-group:nth-child(4)").hide();
	$('#wk_csv_category').on('change', function () {
		var selectedCat = $(this).val();
		if (selectedCat == 1) {
			$("#marketplace_mass_upload_form .form-wrapper .form-group:nth-child(4)").hide();
			$("#marketplace_mass_upload_form .form-wrapper .form-group:nth-child(3)").show();
			$("#marketplace_mass_upload_form .form-wrapper .form-group:nth-child(5)").show();
		} else {
			$("#marketplace_mass_upload_form .form-wrapper .form-group:nth-child(3)").hide();
			$("#marketplace_mass_upload_form .form-wrapper .form-group:nth-child(5)").hide();
			$("#marketplace_mass_upload_form .form-wrapper .form-group:nth-child(4)").show();
		}
	});
	$('#mass_upload_category').on('change', function () {
		var mass_upload_category = parseInt($(this).val());
		var allowEditComb = parseInt($(this).attr('data-allow-edit-combination'));
		if (mass_upload_category == 2) { // Combination select
			$('#product_image_zip').hide();
			if (!allowEditComb) {
				$('.csvTypeUpd').hide();
			}
		} else {
			$('#product_image_zip, .csvTypeUpd').show();
		}
	});

	/* ===== FOR ADMIN ===== */
	$('#csv_category').on('change', function () {
		var mass_upload_category = $(this).val();
		var comb_form_grp = $('#img_zip_file').parent().parent().parent().parent();
		if (mass_upload_category == 2) { // Combination select
			comb_form_grp.hide();

			// Hide update option for combiantion CSV if disallowed by admin
			var allowEditComb = parseInt($("#allowEditComb").val());
			if (!allowEditComb) {
				$("#typeUpdate").parents("div.radio").hide();
			}
		} else {
			comb_form_grp.show();
			$("#typeUpdate").parents("div.radio").show();
		}
	});

	if ($('td.csv_toggle_status').find('a.action-enabled')) {
		var toggle_btn = $('td.csv_toggle_status').find('a.action-enabled, a.action-enabled:hover').css({
			"color": "#ccc",
			"cursor": "default"
		});

		toggle_btn.on('click', function (e) {
			e.preventDefault();
			return false;
		});
	}

	// For configuration page
	$("input[name='MASS_UPLOAD_COMBINATION_APPROVE']").on('click', function (e) {
		var combAllowed = parseInt($(this).val());
		if (combAllowed) {
			$("input[name='MASS_UPLOAD_ALLOW_EDIT_COMBINATION']").prop('disabled', false);
		} else {
			$("input[name='MASS_UPLOAD_ALLOW_EDIT_COMBINATION']").prop('disabled', true);
			$('#MASS_UPLOAD_ALLOW_EDIT_COMBINATION_off').prop('checked', true);
		}
	});
	/* ===== FOR ADMIN ===== */
});