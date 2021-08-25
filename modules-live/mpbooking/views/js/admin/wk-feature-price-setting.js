/*
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
    $('#wk_shop_seller').on('change', function() {
        $('#booking_product_name').val('');
        $('#id_booking_product_info').val('');
    });
    //date picker for date ranges
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
    $('.booking_product_search_results_ul').hide();

    function abortRunningAjax() {
        if (ajax_pre_check_var) {
            ajax_pre_check_var.abort();
        }
    }

    $(document).on('keyup', "#booking_product_name", function(event) {
        if (($('.booking_product_search_results_ul').is(':visible')) && (event.which == 40 || event.which == 38)) {
            $(this).blur();
            if (event.which == 40) {
                $(".booking_product_search_results_ul li:first").focus();
            } else if (event.which == 38) {
                $(".booking_product_search_results_ul li:last").focus();
            }
        } else {
            var $idSeller = 0;
            if (typeof $('#wk_shop_seller').val() !== 'undefined' && $('#wk_shop_seller').val()) {
                $idSeller = $('#wk_shop_seller').val();
            }

            $('.booking_product_search_results_ul').empty().hide();

            if ($(this).val() != '') {
                abortRunningAjax();
                ajax_pre_check_var = $.ajax({
                    url: booking_product_price_plans_url,
                    data: {
                        product_name : $(this).val(),
                        action : 'searchBookingProductByName',
                        id_seller : $idSeller,
                        ajax : true,
                    },
                    method: 'POST',
                    dataType: 'JSON',
                    success: function(data) {
                        var html = '';
                        if (data.status != 'failed') {
                            $.each(data, function(key, booking_product) {
                                html += '<li data-id_booking_product_info="'+booking_product.id_booking_product_info;
                                html += '">'+booking_product.name+'</li>';
                            });
                            $('.booking_product_search_results_ul').html(html);
                            $('.booking_product_search_results_ul').show();
                            $('.error-block').hide();
                        } else {
                            $('.error-block').show();
                        }
                    }
                });
            }
        }
    });

    $(document).on('click', '.booking_product_search_results_ul li', function(event) {
        $('#booking_product_name').attr('value', $(this).html());
        $('#id_booking_product_info').val($(this).data('id_booking_product_info'));
        $('.booking_product_search_results_ul').empty().hide();
    });

    $('#date_selection_type').on('change', function() {
        if ($('#date_selection_type').val() == 2) {
            $(".specific_date_type").show();
            $(".date_range_type").hide();
            $(".special_days_content").hide();
        } else if ($('#date_selection_type').val() == 1) {
            $(".specific_date_type").hide();
            $(".date_range_type").show();
            $(".special_days_content").show();
        } else {
            $(".specific_date_type").hide();
            $(".date_range_type").show();
            $(".special_days_content").show();
        }
    });
});

function getMinDate(date)
{
    var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date)));
    selectedDate.setDate(selectedDate.getDate() + 1);
    return selectedDate;
}

function highlightDateBorder(elementVal, date)
{
    if (elementVal) {
        var currentDate = date.getDate();
        var currentMonth = date.getMonth()+1;
        if (currentMonth < 10) {
            currentMonth = '0' + currentMonth;
        }
        if (currentDate < 10) {
            currentDate = '0' + currentDate;
        }
        dmy = date.getFullYear() + "-" + currentMonth + "-" + currentDate;
        var date_format = elementVal.split("-");
        var check_in_time = (date_format[2]) + '-' + (date_format[1]) + '-' + (date_format[0]);
        if (dmy == check_in_time) {
            return [true, "selectedCheckedDate", "Check-In date"];
        } else {
            return [true, ""];
        }
    } else {
        return [true, ""];
    }
}

function showBookingPriceRuleLangField(lang_iso_code, id_lang)
{
	$('#feature_price_rule_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
	$('.feature_price_name_all').hide();
	$('#feature_price_name_'+id_lang).show();
}