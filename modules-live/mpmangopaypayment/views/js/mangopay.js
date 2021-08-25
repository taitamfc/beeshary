/**
 * 2010-2018 Webkul
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

$(document).ready(function() {
    $(".card_regi_mgp").on('click', function() {
        if ($(this).val() == 'new_card') {
            $('#wk_mangopay_frame').show();
        } else {
            $('#wk_mangopay_frame').hide();
        }
    });

    // saved account and new account toggle fordirect debit payments
    $(".mangopay_saved_account_container .add_new_account_link").on('click', function() {
        $(".mangopay_saved_account_container").hide();
        $(".mangopay_new_account_container").show();
        $("#pay_with_new_account").val(1);
    });
    $(".mangopay_new_account_container .cancel_new_account_link").on('click', function() {
        if ($('.saved_customer_account').length > 0) {
            $(".mangopay_saved_account_container").show();
            $(".mangopay_new_account_container").hide();
            $("#pay_with_new_account").val(0);
        } else {
            showErrorMessage(no_saved_acc_msg);
        }
    });

    $(".saved_customer_card").on("change", function() {
        $(this).closest('.customer_saved_card_payment').find('.saved_customer_card_type').prop('checked', true);
    });

    // saved Cards and new card toggle for card payments
    $(".mangopay_saved_card_container .add_new_card_link").on('click', function() {
        $(".mangopay_saved_card_container").hide();
        $(".mangopay_new_card_container").show();
        $("#pay_with_new_card").val(1);
    });
    $(".mangopay_new_card_container .cancel_new_card_link").on('click', function() {
        if ($('.saved_customer_card').length > 0) {
            $(".mangopay_saved_card_container").show();
            $(".mangopay_new_card_container").hide();
            $("#pay_with_new_card").val(0);
        } else {
            showErrorMessage(no_saved_cards_msg);
        }
    });

    $(".remove_saved_card_link").on('click', function() {
        $(".loading_overlay").show();
        var idCard = $(this).attr('id_card_mgp');
        var $current = $(this);
        $.ajax({
            url: mgp_payment_url,
            data: {
                id_user_card: idCard,
                action: 'removeUserMangopayCard',
                ajax: true,
            },
            method: 'POST',
            dataType: 'JSON',
            success: function(result) {
                $(".loading_overlay").hide();
                if (result.status == 'ok') {
                    showSuccessMessage(result.msg);
                    $current.closest(".payment-option").remove();
                    if ($('.saved_customer_card').length <= 0) {
                        $(".mangopay_saved_card_container").hide();
                        $(".mangopay_new_card_container").show();
                        $("#pay_with_new_card").val(1);
                    } else {
                        $( ".saved_customer_card" ).first().prop('checked', true);
                    }
                } else {
                    showErrorMessage(result.msg);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $(".loading_overlay").hide();
                showErrorMessage(textStatus);
            }
        });
        return false;
    });

    $(".remove_saved_account_link").on('click', function() {
        $(".loading_overlay").show();
        var idAccount = $(this).attr('id_account_mgp');
        var $current = $(this);
        $.ajax({
            url: mgp_payment_url,
            data: {
                id_user_account: idAccount,
                action: 'removeUserMangopayAccount',
                ajax: true,
            },
            method: 'POST',
            dataType: 'JSON',
            success: function(result) {
                $(".loading_overlay").hide();
                if (result.status == 'ok') {
                    showSuccessMessage(result.msg);
                    $current.closest(".payment-option").remove();
                    if ($('.saved_customer_account').length <= 0) {
                        $(".mangopay_saved_account_container").hide();
                        $(".mangopay_new_account_container").show();
                        $('.mangopay_new_account_container .cancel_new_account_link').hide();
                        $("#pay_with_new_account").val(1);
                    } else {
                        $( ".saved_customer_account" ).first().prop('checked', true);
                    }
                } else {
                    showErrorMessage(result.msg);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $(".loading_overlay").hide();
                showErrorMessage(textStatus);
            }
        });
        return false;
    });

    $('#payment-confirmation > .ps-shown-by-js > button').on('click', function(e) {
        e.preventDefault();
      //var module_name = $('#payment-option-3-container > .custom-radio > #payment-option-3').data("module-name");
        if ($('.payment-options').find("input[data-module-name='mpmangopaypayment_card_type']").is(':checked')) {
            if (paymentType == 1 && $("#pay_with_new_card").val() == 1) {
                e.preventDefault();
                $('.wk-error').empty();
                var card_num = $('#mangopay_cardnum').val().trim();
                var exp_month = $('#mangopay_exp_date_month').val();
                var exp_year = $('#mangopay_exp_date_year').val();
                var card_cvv = $('#mangopay_card_code').val().trim();
                var error = false;
                if (card_num == '' || isNaN(card_num)) {
                    $('.wk_card_error').html(card_error);
                    error = true;
                }
                if (exp_month == '' || exp_year == '') {
                    $('.wk_exp_error').html(exp_error);
                    error = true;
                }

                //if ($("#card_type").val() != 'MAESTRO') {
                if (card_cvv == '' || isNaN(card_cvv)) {
                    $('.wk_cvv_error').html(cvv_error);
                    error = true;
                }
                //}
                if (error) {
                    return false;
                } else {
                    $("#wk_mangopay_form").submit();
                }
                return false;
            }
        } else if ($('.payment-options').find("input[data-module-name='mpmangopaypayment_direct_debit_type']").is(':checked')) {
            var error = false;
            if ($("#pay_with_new_account").val() == 1) {
                $('.cust_mgp_account_creation_errors').empty();
                var owner_address_addressline1 = $("#mgp_owner_addressline1").val();
                var owner_address_city = $("#mgp_owner_city").val();
                var owner_address_postalCode = $("#mgp_owner_postcode").val();
                var owner_address_region = $("#mgp_owner_region").val();
                var owner_address_country = $("#mgp_owner_country").val();
                var bank_type = $("#mgp_bank_type").val();
                var mgp_owner_name = $('#mgp_owner_name').val();
                var mgp_owner_address = $('#mgp_owner_address').val();
                var mgp_iban = $('#mgp_iban').val();
                var mgp_bic = $('#mgp_bic').val();
                var mgp_account_number = $('#mgp_account_number').val();
                var mgp_sort_code = $('#mgp_sort_code').val();

                var errorHtml = '<div class="alert alert-danger">'
                errorHtml += '<button type="button" class="close" data-dismiss="alert">×</button>';
                errorHtml += '<ol>';

                if ($.trim(owner_address_addressline1) == '') {
                    error = true;
                    errorHtml += '<li>'+mgp_owner_addrline1_err+'</li>';
                }
                if ($.trim(owner_address_city) == '') {
                    error = true;
                    errorHtml += '<li>'+mgp_owner_addr_city_err+'</li>';
                }
                if ($.trim(owner_address_postalCode) == '') {
                    error = true;
                    errorHtml += '<li>'+mgp_owner_addr_zipcode_err+'</li>';
                }
                if ($.trim(owner_address_region) == '') {
                    error = true;
                    errorHtml += '<li>'+mgp_owner_addr_region_err+'</li>';
                }
                if ($.trim(owner_address_country) == '') {
                    error = true;
                    errorHtml += '<li>'+mgp_owner_addr_country_err+'</li>';
                }
                if (bank_type == 'IBAN') {
                    if (!mgp_owner_name) {
                        error = true;
                        errorHtml += '<li>'+mgp_owner_name_err+'</li>';
                    }
                    if (!mgp_iban) {
                        error = true;
                        errorHtml += '<li>'+mgp_iban_err+'</li>';
                    }
                    if (!mgp_bic) {
                        error = true;
                        errorHtml += '<li>'+mgp_bic_err+'</li>';
                    }
                } else if (bank_type == 'GB') {
                    if (!mgp_owner_name) {
                        error = true;
                        errorHtml += '<li>'+mgp_owner_name_err+'</li>';
                    }
                    if (!mgp_account_number) {
                        error = true;
                        errorHtml += '<li>'+mgp_account_number_err+'</li>';
                    }
                    if (!mgp_sort_code) {
                        error = true;
                        errorHtml += '<li>'+mgp_sort_code_err+'</li>';
                    }
                }
            }
            if (error) {
                errorHtml += '</ol>';
                errorHtml += '</div>';
                $('.cust_mgp_account_creation_errors').append(errorHtml);
                $('#customer_mgp_bank_details_form').animate(
                    {scrollTop:0},
                    2000
                );
                return false;
            } else {
                $(".loading_overlay").show();
                $('.cust_mgp_account_creation_errors').empty();
                $.ajax({
                    url: mgp_payment_url,
                    data: {
                        customer_bank_details_fields: $('#customer_mgp_bank_details_form').serialize(),
                        action: 'createCustomerBankDetails',
                        ajax: true,
                    },
                    method: 'POST',
                    dataType: 'JSON',
                    success: function(result) {
                        $(".loading_overlay").hide();
                        if (result.status == 'ok' && result.mandate_redirect) {
                            window.location.href = result.mandate_redirect;
                        } else {
                            showErrorMessage(result.msg);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert(textStatus);
                        showErrorMessage(textStatus);
                    }
                });
                return false;
            }
        }
    });

    // Change bank account fields according to the selected account type
    changeBankDetailsByBankType($("#mgp_bank_type").val());
    $("#mgp_bank_type").on("change", function() {
        changeBankDetailsByBankType($(this).val());
    });
});

function changeBankDetailsByBankType(bank_type) {
    $(".buyer_bank_details").hide();
    $("#mgp_owner_name_block").show();
    $("#mgp_owner_address_addressline1").show();
    $("#mgp_owner_address_addressline2").show();
    $("#mgp_owner_address_city").show();
    $("#mgp_owner_address_postalCode").show();
    $("#mgp_owner_address_region").show();
    $("#mgp_owner_address_country").show();
    if (bank_type == 'IBAN') {
        $("#mgp_iban_block").show();
        $("#mgp_bic_block").show();
    } else if (bank_type == 'GB') {
        $("#mgp_account_number_block").show();
        $("#mgp_sort_code_block").show();
    } else if (bank_type == 'US') {
        $("#mgp_account_number_block").show();
        $("#mgp_aba_block").show();
    } else if (bank_type == 'CA') {
        $("#mgp_bank_name_block").show();
        $("#mgp_institution_number_block").show();
        $("#mgp_branch_code_block").show();
        $("#mgp_account_number_block").show();
    } else if (bank_type == 'OTHER') {
        $("#mgp_country_block").show();
        $("#mgp_bic_block").show();
        $("#mgp_account_number_block").show();
    }
}

/*Growl plulin implementation to show notifications on front office*/
function showSuccessMessage(msg) {
	$.growl.notice({ title: "", message:msg});
}

function showErrorMessage(msg) {
	$.growl.error({ title: "", message:msg});
}

function showNoticeMessage(msg) {
	$.growl.notice({ title: "", message:msg});
}