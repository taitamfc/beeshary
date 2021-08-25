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
    if ($('input[name="MP_SELLER_CUSTOMER_VOUCHER_ALLOW"]:checked').val() == '0') {
        $('.mp_voucher_customer_type').closest('.form-group').hide();
    }

    $('label[for="MP_SELLER_CUSTOMER_VOUCHER_ALLOW_on"]').on("click", function() {
        $('.mp_voucher_customer_type').closest('.form-group').show();
    });

    $('label[for="MP_SELLER_CUSTOMER_VOUCHER_ALLOW_off"]').on("click", function() {
        $('.mp_voucher_customer_type').closest('.form-group').hide();
    });

    if ($(".wk_datepicker").length) {
        $('.wk_datepicker').datetimepicker({
            beforeShow: function(input, inst) {
                setTimeout(function() {
                    inst.dpDiv.css({
                        'z-index': 1031
                    });
                }, 0);
            },
            prevText: '',
            nextText: '',
            dateFormat: 'yy-mm-dd',
            // Define a custom regional settings in order to use PrestaShop translation tools
            currentText: currentText,
            closeText: closeText,
            ampm: false,
            amNames: ['AM', 'A'],
            pmNames: ['PM', 'P'],
            timeFormat: 'hh:mm:ss tt',
            timeSuffix: '',
            timeOnlyTitle: timeOnlyTitle,
            timeText: timeText,
            hourText: hourText,
            minuteText: minuteText,
        });
    }

    var trigger_ajax = '';
    $('#wk_customerFilter').on('keyup', function(event) {
        var suggestion_ul = $(this).siblings("ul.suggestion_ul");
        if (!((suggestion_ul.is(':visible')) && (event.which == 40 || event.which == 38))) {
            if (trigger_ajax)
                trigger_ajax.abort();

            suggestion_ul.empty().hide();
            suggestion_ul.siblings("input.input_primary").val(null);

            if ($(this).val().trim().length) {
                var word = $(this).val();
                var id_seller = $("#id_seller").val();
                trigger_ajax = $.ajax({
                    url: controller_link,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        ajax: true,
                        action: 'customerSearch',
                        word: word,
                        id_seller: id_seller
                    },
                    success: function(result) {
                        if (result) {
                            var html = '';
                            $.each(result, function(key, value) {
                                html += '<li class="suggestion_li"><a class="suggestion_a" data-primary="' + value.id_customer + '" data-secondary="' + value.firstname + ' ' + value.lastname + ' (' + value.email + ')">' + value.firstname + ' ' + value.lastname + ' (' + value.email + ')</a></li>';
                            });
                            suggestion_ul.html(html).show();
                        } else {
                            suggestion_ul.empty().hide();
                            suggestion_ul.siblings("input.input_primary").val(null);
                        }
                    }
                });
            }
        }
    });

    $('#mpReductionProductFilter').on('keyup', function(event) {
        var suggestion_ul = $(this).siblings("ul.suggestion_ul");
        if (!((suggestion_ul.is(':visible')) && (event.which == 40 || event.which == 38))) {
            if (trigger_ajax)
                trigger_ajax.abort();

            suggestion_ul.empty().hide();
            suggestion_ul.siblings("input.input_primary").val(null);

            if ($(this).val().trim().length) {
                var word = $(this).val();
                var id_seller = $("#id_seller").val();
                trigger_ajax = $.ajax({
                    url: controller_link,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        ajax: true,
                        action: 'productSearch',
                        word: word,
                        id_seller: id_seller
                    },
                    success: function(result) {
                        if (result) {
                            var html = '';
                            $.each(result, function(key, value) {
                                html += '<li class="suggestion_li"><a class="suggestion_a" data-primary="' + value.mp_id_prod + '" data-secondary="' + value.product_name + '">' + value.product_name + '</a></li>';
                            });
                            suggestion_ul.html(html).show();
                        } else {
                            suggestion_ul.empty().hide();
                            suggestion_ul.siblings("input.input_primary").val(null);
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

        suggestion_ul.siblings("input.input_primary").val(data_primary);
        suggestion_ul.siblings("input.input_secondary").val(data_secondary);
        suggestion_ul.empty().hide();
    });

    if ($(".reduction_type:checked").val() == 1) {
        $("#reduction_type_percent, #multiple_product_btn").show();
        $("#reduction_type_amount").hide();
    } else if ($(".reduction_type:checked").val() == 2) {
        $("#reduction_type_percent, #multiple_product_btn").hide();
        $("#reduction_type_amount").show();
    }

    $(".reduction_type").on("change", function() {
        if ($(this).val() == 1) {
            $("#reduction_type_percent, #multiple_product_btn").show(500);
            $("#reduction_type_amount").hide(500);
        } else if ($(this).val() == 2) {
            $("#reduction_type_percent, #multiple_product_btn").hide(500);
            $("#reduction_type_amount").show(500);

            if (parseInt($(".reduction_for:checked").val()) == 2) {
                $("#specific_product").prop("checked", true);
                $("#multiple_product").prop("checked", false);

                $("#specific-product-section").show(500);
                $("#multiple-product-section").hide(500);
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

    // Move right, move left coding
    $(".wk_move_left").on("click", function() {
        var right_select = $(this).siblings("select");
        var right_option = right_select.find("option:selected");
        var left_select = right_select.parents("td").siblings("td").find("select");
        left_select.append(right_option);
    });

    $(".wk_move_right").on("click", function() {
        var left_select = $(this).siblings("select");
        var left_option = left_select.find("option:selected");
        var right_select = left_select.parents("td").siblings("td").find("select");
        right_select.append(left_option);
    });

    $("#country_restriction").on('click', function() {
        if ($(this).is(":checked"))
            $("#block_country_restriction").show(500);
        else
            $("#block_country_restriction").hide(500);
    });
    if ($("#country_restriction").is(":checked")) {
        $("#block_country_restriction").show();
    }

    $("#group_restriction").on('click', function() {
        if ($(this).is(":checked"))
            $("#block_group_restriction").show(500);
        else
            $("#block_group_restriction").hide(500);
    });
    if ($("#group_restriction").is(":checked")) {
        $("#block_group_restriction").show();
    }

    $(".voucher_change_lang").on("click", function(e) {
        e.preventDefault();

        var lang_iso_code = $(this).attr('data-lang-iso-code');
        var id_lang = $(this).attr('data-id-lang');

        $('#voucher_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');

        $('.voucher_name_all').hide();
        $('#name_' + id_lang).show();
    });

    $(".changeSellerLang").on("change", function() {
        var id_seller = $(this).val();
        if (id_seller) {
            $.ajax({
                url: controller_link,
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax: true,
                    action: 'getSellerLang',
                    id_seller: id_seller
                },
                success: function(result) {
                    if (result) {
                        $('#seller_default_lang').val(result.id_lang);
                        $('#seller_default_lang_div').html(result.name);
                        $("input[name='current_seller_lang']").val(result.name);

                        $('#voucher_lang_btn').html(result.iso_code + ' <span class="caret"></span>');
                        $('.voucher_name_all').hide();
                        $('#name_' + result.id_lang).show();

                        // remove data from product input fields
                        $("#mpReductionProductFilter, #mp_reduction_product").val(null);

                        // remove data from customer input fields
                        $("#for_customer, #wk_customerFilter").val(null);

                        // empty suggestion boxes
                        $("ul.suggestion_ul").empty().hide();
                    }
                }
            });
        }
    });

    $("#mp_cart_rule_form").on("submit", function() {
        $(".selected_option option").attr("selected", "selected");
    });
});