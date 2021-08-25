/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

/*Marketplace shipping lang fields change*/
function showShippingLangField(lang_iso_code, id_lang) {
    $('#mpship_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');

    $('.transit_time_all').hide();
    $('#transit_time_' + id_lang).show();
}

//Find seo on add product page according to seller choose
function getSellerShippintDefaultLangId(customer_id) {
    if (customer_id != '') {
        $.ajax({
            url: adminproducturl,
            method: 'POST',
            dataType: 'json',
            data: {
                customer_id: customer_id,
                action: "findSellerDefaultLang",
                ajax: "1"
            },
            success: function(data) {
                showShippingLangField(data.iso_code, data.id_lang);
                $('.transit_time_all').removeClass('seller_default_lang_class');
                $('#transit_time_' + data.id_lang).addClass('seller_default_lang_class');
            }
        });
    }
}

$(document).ready(function() {
    $(document).on('change', "select[name='seller_customer_id']", function() {
        var seller_customer_id = $("select[name='seller_customer_id'] option:selected").val();
        getSellerShippintDefaultLangId(seller_customer_id);
    });

    $('#step4_zone').on('change', function(e) {
        e.preventDefault();
        var id_zone = $(this).val();
        if (id_zone == -1) {
            $('#step4_country').html("<option value='-1'>" + select_country + "</option>");
            $('#country_container').css('display', 'none');
            $('#state_container').css('display', 'none');
        } else {
            $('#country_container').css('display', 'none');
            $('#state_container').css('display', 'none');
            findCountry(id_zone);
        }
    });

    $('#step4_country').on('change', function(e) {
        e.preventDefault();
        var id_country = $(this).val();
        if (id_country == -1) {
            $('#step4_state').html("<option value='0'>" + select_state + "</option>");
            $('#state_container').css('display', 'none');
        } else
            findState(id_country);
    });

    $('#impactprice_button').on('click', function(e) {
        e.preventDefault();
        findRange();
    });

    $('#close_popup').on('click', function(e) {
        e.preventDefault();
        closePopup();
    });

    $('#step_carrier_range').on('submit', function(e) {
        e.preventDefault();
        if ($.isNumeric($('#range_info_detail .range_head_right').children('input').val())) {
            $(".loading_overlay").show();
            $.ajax(shipping_ajax_link, {
                type: 'POST',
                data: $('#step_carrier_range').serialize() + '&fun=range_add',
                dataType: 'json',
                success: function(data, status, xhr) {
                    $(".loading_overlay").hide();
                    if (data == 0) {
                        alert(message_impact_price_error);
                    } else {
                        alert(message_impact_price);
                        window.location.href = update_impact_link;
                    }
                }
            });
        } else {
            $('#range_info_detail').find('.alert').remove();
            $('#range_info_detail').prepend('<div class="alert alert-danger">'+interger_price_text+'</div>');
        }
    });

    // check all check box when seller want to check all permission
    $(document).on('change', '#wk_select_all_checkbox', function() {
        $('input[name="shipping_group[]"]').prop('checked', $(this).prop("checked"));
        if (false == $(this).prop("checked")) {
            $('input[name="shipping_group[]"]').parent('span').removeClass('checked');
        } else {
            $('input[name="shipping_group[]"]').parent('span').addClass('checked');
        }
    });

    //" uncheck checkbox if any other checkbox get uncheck
    $('input[name="shipping_group[]"]').change(function() {
        if (false == $(this).prop("checked")) {
            $("#wk_select_all_checkbox").prop('checked', false);
            $("#wk_select_all_checkbox").parent('span').removeClass('checked');
        }
    });
});

$(document).ready(function() {
    displayAllValueOnLoadPage();

    $("input[name='shipping_handling']").on('change', function() {
        if ($("#shipping_handling_on").is(":checked"))
            $("#shipping_handling_charge").show();
        else
            $("#shipping_handling_charge").hide();
    });

    $("#NextButtonclick").click(function(e) {
        e.preventDefault();
        var is_error = false;
        var message = '';
        var shipping_name = $('#shipping_name').val();
        var transit_time = $('.seller_default_lang_class').val();
        var speed_grade = $('#grade').val();
        var rel = $('#carrier_wizard .selected').attr('rel');
        var logo = $("#shipping_logo").val();
        var exts = ['jpg', 'png', 'jpeg', 'gif'];
        var seller_lang_name = $('.seller_default_lang_class').data('lang-name');

        if (shipping_name == '') {
            alert(shipping_name_error);
            is_error = true;
        } else if (transit_time == '') {
            if ($('#multi_lang').val() == '1') {
                alert(transit_time_error + ' ' + seller_lang_name);
            } else {
                alert(transit_time_error_other);
            }
            $('.seller_default_lang_class').focus();
            is_error = true;
        } else if (isNaN(speed_grade)) {
            alert(speedgradeinvalid);
            is_error = true;
        } else if (speed_grade.length != 1) {
            alert(speedgradevalue);
            is_error = true;
        } else if (logo) {
            var get_ext = logo.split('.'); // split file name at dot
            get_ext = get_ext.reverse(); // reverse name to check extension
            if ($.inArray(get_ext[0].toLowerCase(), exts) <= -1) {
                alert(invalid_logo_file_error);
                return false;
            }
        }

        if (is_error == true) {
            $('.wizard_error').fadeIn(1000);
            $('.wizard_error ul').append(message)
            return false;
        } else {
            if (rel == 2) {
                var error_range = 0;
                var is_free_checkoff = $("#is_free_off").is(":checked");
                shipping_charge_error_message = "";

                var checked_zone = $('.input_zone:checked').length;

                $('.edit_price_value_lower').each(function() {
                    var lwr_range_length = $(this).data('lwr_range_length');
                    var price_value_lower = $(".value_lower_low" + lwr_range_length).val();
                    var price_value_upper = $(".value_upper_low" + lwr_range_length).val();
                    if (is_free_checkoff) {
                        var shipping_method_choose = $('input[name=shipping_method]:checked').val();
                        if (shipping_method_choose == 1) {
                            if (lwr_range_length != 1) {
                                var price_value_lower1 = parseInt($(".value_lower_low" + (parseInt(lwr_range_length) - 1)).val());
                                var price_value_upper1 = parseInt($(".value_upper_low" + (parseInt(lwr_range_length) - 1)).val());
                            }

                            if (price_value_lower === "")
                            shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_lower_limit_error1);
                            else if (price_value_lower < 0)
                                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_lower_limit_error2);
                            else if (price_value_upper === "")
                                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_upper_limit_error1);
                            else if (price_value_upper < 0)
                                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_upper_limit_error2);
                            else if (parseFloat(price_value_upper) < parseFloat(price_value_lower))
                                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_limit_error);
                            else if (parseFloat(price_value_upper) === parseFloat(price_value_lower))
                                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_limit_equal_error);
                            else if (checked_zone < 1)
                                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_select_zone_err);

                        } else {
                            if (lwr_range_length != 1) {
                                var price_value_lower1 = parseInt($(".value_lower_low" + (parseInt(lwr_range_length) - 1)).val());
                                var price_value_upper1 = parseInt($(".value_upper_low" + (parseInt(lwr_range_length) - 1)).val());
                                if (price_value_lower < price_value_upper1) {
                                    shipping_charge_error_message = shipping_charge_error_message.concat(invalid_range_value);
                                }
                            }
                            if (price_value_lower === "" || !$.isNumeric(price_value_lower))
                                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_lower_limit_error1);
                            else if (price_value_lower < 0)
                                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_lower_limit_error2);
                            else if (price_value_upper === "" || !$.isNumeric(price_value_upper))
                                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_upper_limit_error1);
                            else if (price_value_upper < 0)
                                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_upper_limit_error2);
                            else if (parseFloat(price_value_upper) < parseFloat(price_value_lower))
                                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_limit_error);
                            else if (parseFloat(price_value_upper) === parseFloat(price_value_lower))
                                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_limit_equal_error);
                            else if (checked_zone < 1)
                                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_select_zone_err);
                        }
                    }
                });
                if (shipping_charge_error_message != "") {
                    alert(shipping_charge_error_message);
                    return false;
                }
            }

            var shippingstep = $("#getshippingstep").val();
            var nextshippingstep = parseInt(shippingstep) + 1;

            if (nextshippingstep != 1) {
                $('#PreviousButtonclick').show();
                $('#Previousdisablebutton').hide();

                if (nextshippingstep == 3) {
                    $('#Nextdisablebutton').show();
                    $('#NextButtonclick').hide();
                    $('#Finishdisablebutton').hide();
                    $('#FinishButtonclick').show();
                } else {
                    $('#Finishdisablebutton').show();
                    $('#FinishButtonclick').hide();
                }
            }

            $('#step-' + shippingstep).hide();
            $('#step-' + nextshippingstep).show();
            $('#step_heading' + shippingstep).removeClass('selected');
            $('#step_heading' + shippingstep).addClass('done');
            $('#step_heading' + nextshippingstep).removeClass('done');
            $('#step_heading' + nextshippingstep).removeClass('disabled');
            $('#step_heading' + nextshippingstep).addClass('selected');
            $("#getshippingstep").val(nextshippingstep);
        }
    });

    $('#PreviousButtonclick').click(function(e) {
        e.preventDefault();
        var shippingstep = $("#getshippingstep").val();
        var preshippingstep = parseInt(shippingstep) - 1;

        if (preshippingstep == 1) {
            $('#PreviousButtonclick').hide();
            $('#Previousdisablebutton').show();
        } else {
            $('#PreviousButtonclick').show();
            $('#Previousdisablebutton').hide();
            if (preshippingstep == 3) {
                $('#Nextdisablebutton').show();
                $('#NextButtonclick').hide();
                $('#Finishdisablebutton').hide();
                $('#FinishButtonclick').show();
            } else {
                $('#Nextdisablebutton').hide();
                $('#NextButtonclick').show();
                $('#Finishdisablebutton').show();
                $('#FinishButtonclick').hide();
            }
        }

        $('#step-' + shippingstep).hide();
        $('#step-' + preshippingstep).show();
        $('#step_heading' + shippingstep).removeClass('selected');
        $('#step_heading' + shippingstep).addClass('done');
        $('#step_heading' + preshippingstep).removeClass('done');
        $('#step_heading' + preshippingstep).addClass('selected');
        $("#getshippingstep").val(preshippingstep);
    });

    $('.steptab').click(function(e) {
        e.preventDefault();
        var hasDone = $(this).hasClass('done');
        if (hasDone == true) {
            var randomshippingstep = $(this).attr('rel');
            if (randomshippingstep == 1) {
                $('#PreviousButtonclick').hide();
                $('#Previousdisablebutton').show();
            } else {
                $('#PreviousButtonclick').show();
                $('#Previousdisablebutton').hide();
                if (randomshippingstep == 3) {
                    $('#Nextdisablebutton').show();
                    $('#NextButtonclick').hide();
                    $('#Finishdisablebutton').hide();
                    $('#FinishButtonclick').show();
                }
            }

            var lastrandomselectedstep = $('#carrier_wizard .selected').attr('rel');
            $('#step-' + lastrandomselectedstep).hide();
            $('#step-' + randomshippingstep).show();
            $('#step_heading' + lastrandomselectedstep).removeClass('selected');
            $('#step_heading' + lastrandomselectedstep).addClass('done');
            $('#step_heading' + randomshippingstep).removeClass('done');
            $('#step_heading' + randomshippingstep).removeClass('disabled');
            $('#step_heading' + randomshippingstep).addClass('selected');


            $("#getshippingstep").val(randomshippingstep);
            hasDone = false;
        }
    });

    $('input[name=shipping_method]').change(function() {
        var shipping_method_choose = $('input[name=shipping_method]:checked').val();
        if (shipping_method_choose == 1) //Show Weight
        {
            string = string_weight;
            $('.weight_unit').show();
            $('.price_unit').hide();
        } else if (shipping_method_choose == 2) // Show Price
        {
            string = string_price;
            $('.price_unit').show();
            $('.weight_unit').hide();
        }
        $('.range_type').html(string);
    });

    $(document).on('blur', '.rangeAllforzone', function() {
        var range_len_val = $(this).data('range_len_val');
        var rangevalue = $(this).val();
        if (isNaN(rangevalue)) {
            alert('Enter Numeric Value');
            return false;
        } else {
            $('.zone_val' + range_len_val + ':not(:disabled)').val(rangevalue);
            $(this).val('');
        }
    });

    $('input[name="is_free"]').change(function() {
        var is_free = $(this).val();
        if (is_free == 1) {
            if ($('#shipping_handling_on').is(":checked")) {
                $('#uniform-shipping_handling_on span').removeClass('checked');
                $('#shipping_handling_on').prop('checked', false);
                $('#uniform-shipping_handling_off span').addClass('checked');
                $('#shipping_handling_off').prop('checked', true);
                $("#shipping_handling_charge").hide();
            }
            $('input[name=shipping_handling]').prop('disabled', true);
            $('.edit_price_value_lower').prop('disabled', true).addClass('wk-form-control').css('border-color', '#999999');
            $('.edit_price_value_upper').prop('disabled', true).addClass('wk-form-control').css('border-color', '#999999');
            $('.rangeAllforzone').prop('disabled', true).addClass('wk-form-control').css('border-color', '#999999');
            $('.other_input_zone').prop('disabled', true).addClass('wk-form-control').css('border-color', '#999999');
            $('.new_range #add_new_range').addClass('disabled');
        } else {
            $('input[name=shipping_handling]').removeAttr('disabled');
            $('.edit_price_value_lower').removeAttr('disabled').removeClass('wk-form-control').css('border-color', '');
            $('.edit_price_value_upper').removeAttr('disabled').removeClass('wk-form-control').css('border-color', '');
            $('.rangeAllforzone').removeAttr('disabled').removeClass('wk-form-control').css('border-color', '');
            $('.other_input_zone').removeAttr('disabled').removeClass('wk-form-control').css('border-color', '');
            $('.new_range #add_new_range').removeClass('disabled');
        }
    });

    $('#add_new_range').click(function(e) {
        e.preventDefault();
        var res = true;
        //var is_free_checkoff = $("#is_free_off").is(":checked");

        shipping_charge_error_message = "";
        $('.edit_price_value_lower').each(function() {
            var lwr_range_length = $(this).data('lwr_range_length');
            var price_value_lower = $(".value_lower_low" + lwr_range_length).val();
            var price_value_upper = $(".value_upper_low" + lwr_range_length).val();
            //alert(is_free_checkoff);
            //if(is_free_checkoff)
            //{
            if (price_value_lower == "" || !$.isNumeric(price_value_lower)) {
                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_lower_limit_error1);
            } else if (price_value_lower < 0)
                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_lower_limit_error2);
            else if (price_value_upper == "" || !$.isNumeric(price_value_upper))
                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_upper_limit_error1);
            else if (price_value_upper < 0)
                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_upper_limit_error2);
            else if (parseFloat(price_value_upper) < parseFloat(price_value_lower))
                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_limit_error);
            else if (parseFloat(price_value_upper) === parseFloat(price_value_lower))
                shipping_charge_error_message = shipping_charge_error_message.concat(shipping_charge_limit_equal_error);
            //}
        });
        if (shipping_charge_error_message != "") {
            alert(shipping_charge_error_message);
            return false;
        }

        last_sup_val = $('tr.range_sup td:last input').val();

        //add new rand sup input
        var length_lwr_range = (parseInt($('.edit_price_value_lower').length) + 1);
        $('tr.range_sup td:last').after('<td class="center"><div class="input-group fixed-width-md"><input class="form-control edit_price_value_upper value_upper_low' + length_lwr_range + '" name="range_sup[]" type="text" /></div></td>');
        //add new rand inf input
        $('tr.range_inf td:last').after('<td class="border_bottom center"><div class="input-group fixed-width-md"><input class="form-control edit_price_value_lower value_lower_low' + length_lwr_range + '" data-lwr_range_length="' + length_lwr_range + '"  name="range_inf[]" type="text" value="' + last_sup_val + '" /></div></td>');
        var length_range = parseInt($('.rangeAllforzone').length) + 1;
        $('tr.fees_all td:last').after('<td class="border_top border_bottom"><div class="input-group fixed-width-md"><input class="form-control rangeAllforzone" data-range_len_val="' + length_range + '" type="text" /></div></td>');
        $('tr.fees').each(function() {
            if ($('#zone_' + $(this).data('zoneid')).is(':checked')) {
                $(this).children('td:last').after('<td class="center"><div class="input-group fixed-width-md"><input name="fees[' + $(this).data('zoneid') + '][]" class="form-control input_zone_' + $(this).data('zoneid') + ' other_input_zone zone_val' + length_range + '" type="text" /></div></td>');
            } else {
                $(this).children('td:last').after('<td class="center"><div class="input-group fixed-width-md"><input disabled="disabled" name="fees[' + $(this).data('zoneid') + '][]" class="form-control input_zone_' + $(this).data('zoneid') + ' other_input_zone zone_val' + length_range + '" type="text" /></div></td>');
            }
        });

        $('tr.delete_range td:last').after('<td class="center"><div class="btn btn-primary-outline delbutton">'+labelDelete+'</div></td>');

    });

    //Delete Range
    $(document).on('click', '.delbutton', function(e) {
        e.preventDefault();
        var index;
        if (confirm(delete_range_confirm)) {
            index = $(this).parent('td').index();
            //$('#zones_table tr td:nth-child('+index+')').remove();
            $('tr.range_sup td:eq(' + index + '), tr.range_inf td:eq(' + index + '), tr.fees_all td:eq(' + index + '), tr.delete_range td:eq(' + index + ')').remove();
            $('tr.fees').each(function() {
                $(this).children('td:eq(' + index + ')').remove();
            });
            return false;
        }
    });
});

function displayAllValueOnLoadPage() {
    $("#getshippingstep").val(1);

    if (typeof(mp_shipping_id) != 'undefined' && mp_shipping_id == '') {
        //$('#uniform-billing_price span').addClass('checked');
        $("#billing_price").prop('checked', true);
        $('#shipping_handling_off').prop('checked', true);
        $("#shipping_handling_charge").hide();
        $('.range_type').html(string_price);
        $('.price_unit').show();
        $('.weight_unit').hide();
    } else {
        if (typeof(shipping_method) != 'undefined' && shipping_method == 1) // weight
        {
            //$('#uniform-billing_price span').removeClass('checked');
            $('#billing_price').prop('checked', false);
            $('#uniform-billing_weight span').addClass('checked');
            $('#billing_weight').prop('checked', true);
            $('.range_type').html(string_weight);
            $('.weight_unit').show();
            $('.price_unit').hide();
        } else if (typeof(shipping_method) != 'undefined' && shipping_method == 2) // price
        {
            $('#uniform-billing_weight span').removeClass('checked');
            $('#billing_weight').prop('checked', false);
            //$('#uniform-billing_price span').addClass('checked');
            $('#billing_price').prop('checked', true);
            $('.range_type').html(string_price);
            $('.price_unit').show();
            $('.weight_unit').hide();
        }

        if (typeof(is_free) != 'undefined' && is_free == 1) {
            $('#uniform-is_free_off span').removeClass('checked');
            $('#is_free_off').prop('checked', false);
            $('#uniform-is_free_on span').addClass('checked');
            $('#is_free_on').prop('checked', true);
            $('#shipping_handling_on').prop('disabled', true);
            $('.edit_price_value_lower').prop('disabled', true).addClass('wk-form-control').css('border-color', '#999999');
            $('.edit_price_value_upper').prop('disabled', true).addClass('wk-form-control').css('border-color', '#999999');
            $('.rangeAllforzone').prop('disabled', true).addClass('wk-form-control').css('border-color', '#999999');
            $('.other_input_zone').prop('disabled', true).addClass('wk-form-control').css('border-color', '#999999');
            $('.new_range #add_new_range').addClass('disabled');
        } else if (typeof(is_free) != 'undefined' && is_free == 0) {
            $('#uniform-is_free_on span').removeClass('checked');
            $('#is_free_on').prop('checked', false);
            $('#uniform-is_free_off span').addClass('checked');
            $('#is_free_off').prop('checked', true);
        }

        if (typeof(shipping_handling) != 'undefined' && shipping_handling == 1) {
            $('#uniform-shipping_handling_off span').removeClass('checked');
            $('#shipping_handling_off').prop('checked', false);
            $('#uniform-shipping_handling_on span').addClass('checked');
            $('#shipping_handling_on').prop('checked', true);
            $("#shipping_handling_charge").show();
        } else if (typeof(shipping_handling) != 'undefined' && shipping_handling == 0) {
            $('#uniform-shipping_handling_on span').removeClass('checked');
            $('#shipping_handling_on').prop('checked', false);
            $('#uniform-shipping_handling_off span').addClass('checked');
            $('#shipping_handling_off').prop('checked', true);
            $("#shipping_handling_charge").hide();
        }
    }
}

function checkAllZones(elt) {
    if ($(elt).is(':checked')) {
        $('.rangeAllforzone').removeAttr('disabled');
        $('.input_zone').prop('checked', true);
        $('.fees input:text').each(function() {
            index = $(this).closest('td').index();
            $('span.fees_all').show();
            $('tr.fees_all td:eq(' + index + ')').children('input').show().removeAttr('disabled');
            $('tr.fees_all td:eq(' + index + ')').children('.currency_sign').show();
            if ($('tr.fees_all td:eq(' + index + ')').hasClass('validated')) {
                $(this).removeAttr('disabled');
                $('.fees_all td:eq(' + index + ') input:text').removeAttr('disabled');
            }
        });
        $('.fees input:text, .fees_all input:text').removeAttr('disabled');
        $('.zone').children().removeClass('checker');
    } else {
        $('.input_zone').removeAttr('checked');
        $('.fees input:text, .fees_all input:text').prop('disabled', true);
        $('.zone').children().addClass('checker');
        $('.zone').children().children().removeClass('checked');
    }
}

function enableTextField(checkbox_obj) {
    var text_id = ".input_" + $(checkbox_obj).attr('id');
    if ($(checkbox_obj).is(':checked'))
        $(text_id).removeAttr('disabled');
    else
        $(text_id).prop('disabled', true);
}

function findCountry(id_zone) {
    $('#loading_ajax').html('<img src=' + img_ps_dir + 'loader.gif>');
    $.ajax(shipping_ajax_link, {
        type: 'POST',
        'data': {
            ajax: 1,
            id_zone: id_zone,
            fun: 'find_country'
        },
        dataType: 'json',
        success: function(data, status, xhr) {
            $('#step4_country').html('');
            $('#country_container').css('display', 'block');
            $('#step4_country').append($("<option></option>").text(select_country).val('-1'))
            $.each(data, function() {
                $('#step4_country').append(
                    $("<option></option>").text(this.name).val(this.id_country)
                );
            });
            $('#loading_ajax').html('');
        },
        error: function(xhr, status, error) {}
    });
}

function findState(id_country) {
    $('#loading_ajax').html('<img src=' + img_ps_dir + 'loader.gif>');
    $.ajax(shipping_ajax_link, {
        type: 'POST',
        'data': {
            ajax: 1,
            id_country: id_country,
            fun: 'find_state'
        },
        dataType: 'json',
        success: function(data, status, xhr) {
            $('#step4_state').html('');
            $('#state_container').css('display', 'block');
            $('#step4_state').append($("<option></option>").text(select_state).val('0'))
            $.each(data, function() {
                $('#step4_state').append(
                    $("<option></option>").text(this.name).val(this.id_state)
                );
            });
            $('#loading_ajax').html('');
        },
        error: function(xhr, status, error) {}
    });
}

function findRange() {
    var id_zone = $('#step4_zone').val();
    var id_country = $('#step4_country').val();
    var id_state = $('#step4_state').val();
    var shipping_method = $('.step4_shipping_method').val();
    var mpshipping_id = $('#mpshipping_id').val();

    var is_error = false;
    if (id_zone == -1) {
        alert(zone_error);
        is_error = true;
        return false;
    } else if (id_country == -1) {
        alert(zone_error);
        is_error = true;
        return false;
    }

    if (is_error == false) {
        var data1 = {
            ajax: 1,
            id_zone: id_zone,
            id_country: id_country,
            id_state: id_state,
            shipping_method: shipping_method,
            mpshipping_id: mpshipping_id,
            fun: 'find_range'
        }
        $.ajax(shipping_ajax_link, {
            type: 'POST',
            'data': data1,
            dataType: 'json',
            success: function(data, status, xhr) {
                if (data != 0) {
                    $('#range_mpshipping_id').attr('value', mpshipping_id);
                    $('#range_mpshipping_id_zone').attr('value', id_zone);
                    $('#range_mpshipping_id_country').attr('value', id_country);
                    $('#range_mpshipping_id_state').attr('value', id_state);
                    $('#range_shipping_method').attr('value', shipping_method);
                    $('#range_info_detail').empty();
                    $('#range_info_detail').append('<div class="range_head"><div class="range_head_left">' + ranges_info + '(' + range_sign + ')</div><div class="range_head_right">'+impact_price_text+'</div></div>');
                    $.each(data, function() {
                        $('#range_info_detail').append('<div class="range_data"><div class="range_head_left">' + this.delimiter1 + ' - ' + this.delimiter2 + '</div><div class="range_head_right input-group"><span class="input-group-addon">'+currency_sign+'</span><input type="text" class="form-control" name="delivery' + this.id + '" id="delivery' + this.id + '" value="' + this.impact_price + '"></div></div>');
                    });
                    $('#header').css('display', 'none');
                    $('#impact_price_block').css('display', 'block');
                    $('#newbody').fadeIn(1000);
                } else {
                    alert(no_range_available_error);
                }
            },
            error: function(xhr, status, error) {}
        });
    }
}

function closePopup() {
    $('#header').css('display', 'block');
    $('#impact_price_block').css('display', 'none');
    $('#range_info_detail').html('');
    $('#newbody').fadeOut(1000);
}

function showTransitLangField(lang_iso_code, id_lang) {
    $('#transit_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
    $('.transit_time_all').hide();
    $('#transit_time_' + id_lang).show();
}

$(document).ready(function() {
    $(".delete_impact").on("click", function() {
        if (!confirm("Are you sure?")) {
            return false;
        }
    });
});
