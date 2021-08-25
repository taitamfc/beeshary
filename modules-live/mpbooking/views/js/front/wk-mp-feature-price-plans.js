/**
 * 2010-2019 Webkul.
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
 *  @copyright 2010-2019 Webkul IN
 *  @license   https://store.webkul.com/license.html
 */

$(document).ready(function() {
    $(".mp_bulk_delete_btn").on("click", function(e) {
        e.preventDefault();
        if (!$('.mp_bulk_select:checked').length) {
            alert(checkbox_select_warning);
            return false;
        } else {
            if (!confirm(confirm_delete_msg)) {
                return false;
            } else {
                $('#mp_productlist_form').submit();
            }
        }
    });

    $("#mp_all_select").on("click", function() {
        if ($(this).is(':checked')) {
            $('.mp_bulk_select').parent().addClass('checker checked');
            $('.mp_bulk_select').attr('checked', 'checked');
        } else {
            $('.mp_bulk_select').parent().removeClass('checker checked');
            $('.mp_bulk_select').removeAttr('checked');
        }
    });

    $("#SubmitFeaturePricePlan, #StayFeaturePricePlan").on("click", function() {
        $("#SubmitFeaturePricePlan, #StayFeaturePricePlan").css('pointer-events', 'none');
    });
  /* ----  FeaturePricesSettingsController ---- */
    $("#feature_plan_date_from").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        minDate: 0,
        onSelect: function(selectedDate) {
            var date_format = selectedDate.split("-");
            var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
            selectedDate.setDate(selectedDate.getDate() + 1);
            $("#feature_plan_date_to").datepicker("option", "minDate", selectedDate);
        },
    });

    $("#feature_plan_date_to").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        beforeShow: function() {
            var minDateTo = $('#feature_plan_date_from').val();
            var date_format = minDateTo.split("-");
            var minDateTo = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
            minDateTo.setDate(minDateTo.getDate() + 1);
            $("#feature_plan_date_to").datepicker("option", "minDate", minDateTo);
        },
    });

    $("#specific_date").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        minDate: 0,
    });

	function getMinDate(date)
	{
	  var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date)));
	  selectedDate.setDate(selectedDate.getDate() + 1);
	  return selectedDate;
	}

    $('#date_selection_type').on('change', function() {
        if ($('#date_selection_type').val() == 2) {
            $(".specific_date_type_block").show();
            $(".feature_plan_date_range_block").hide();
            $(".special_days_exists_block").hide();
        } else if ($('#date_selection_type').val() == 1) {
            $(".specific_date_type_block").hide();
            $(".feature_plan_date_range_block").show();
            $(".special_days_exists_block").show();
        } else {
            $(".specific_date_type_block").hide();
            $(".feature_plan_date_range_block").show();
            $(".special_days_exists_block").show();
        }
    });


    $(".is_special_days_exists").on ('click', function() {
        if ($(this).is(':checked')) {
            $('.week_days').show();
        } else {
            $('.week_days').hide();
        }
    });

    $('#price_impact_type').on('change', function() {
        if ($('#price_impact_type').val() == 2) {
            $(".payment_type_icon").text(defaultcurrency_sign);
        } else if ($('#price_impact_type').val() == 1) {
            $(".payment_type_icon").text('%');
        } else {
            $(".payment_type_icon").text(defaultcurrency_sign);
        }
    });

    var ajax_pre_check_var = '';
    $('.mp_booking_product_search_results_ul').hide();

    function abortRunningAjax() {
        if (ajax_pre_check_var) {
            ajax_pre_check_var.abort();
        }
    }

    $(document).on('keyup', "#mp_booking_product_name", function(event) {
      if (($('.mp_booking_product_search_results_ul').is(':visible')) && (event.which == 40 || event.which == 38)) {
          $(this).blur();
          if (event.which == 40)
              $(".mp_booking_product_search_results_ul li:first").focus();
          else if (event.which == 38)
              $(".mp_booking_product_search_results_ul li:last").focus();
      } else {
        $('.mp_booking_product_search_results_ul').empty().hide();

        if ($(this).val() != '') {
            abortRunningAjax();
            ajax_pre_check_var = $.ajax({
                url: autocomplete_product_search_url,
                data: {
                    product_name : $(this).val(),
                    id_seller : $('#id_seller').val(),
                    action : 'searchMpBookingProductByName',
                    ajax : true,
                },
                method: 'POST',
                dataType: 'JSON',
                success: function(data) {
                    var html = '';
                    if (data.status != 'failed') {
                        $.each(data, function(key, booking_product) {
                            html += '<li data-id_booking_product_info="'+booking_product.id_booking_product_info+'">'+booking_product.name+'</li>';
                        });
                        $('.mp_booking_product_search_results_ul').html(html);
                        $('.mp_booking_product_search_results_ul').show();
                        $('.plan_error_block').hide();
                    } else {
                        $('.plan_error_block').show();
                    }
                }
            });
        }
      }
    });

    $(document).on('click', '.mp_booking_product_search_results_ul li', function(event) {
        $('#mp_booking_product_name').val($(this).html());
        $('#id_booking_product_info').val($(this).data('id_booking_product_info'));
        $('.mp_booking_product_search_results_ul').empty().hide();
    });

    $('.delete_feature_plan').on('click', function() {
        if (confirm(confirm_delete_msg)) {
            return true;
        } else {
            return false;
        }
    });
});
