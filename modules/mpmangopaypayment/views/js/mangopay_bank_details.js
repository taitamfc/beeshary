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

$(document).ready(function() {
    changeBankDetailsByBankType($("#mgp_bank_type").val());

    $("#mgp_bank_type").on("change", function() {
        changeBankDetailsByBankType($(this).val());
    });

    function changeBankDetailsByBankType(bank_type) {
        $(".seller_bank_details").hide();
        $("#mgp_owner_name_block").show();
        // $("#mgp_owner_address_block").show();
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

    $("#submit_payout_amount").on("click", function(e) {
        var bank_id = $("#seller_mgp_account").val();
        var payout_amount = $("#payout_amount").val();
        if (!(parseInt(bank_id))) {
            alert(bank_id_err);
            return false;
        } else if (!payout_amount) {
            alert(payout_amt_err);
            return false;
        } else if (!$.isNumeric(payout_amount)) {
            alert(payout_amt_err);
            return false;
        }
    });

    $("#submit_mgp_bank_account").on("click", function(e) {
        var mgp_owner_name = $('#mgp_owner_name').val();
        var owner_address_addressline1 = $("#mgp_owner_addressline1").val();
        var owner_address_city = $("#mgp_owner_city").val();
        var owner_address_postalCode = $("#mgp_owner_postcode").val();
        var owner_address_region = $("#mgp_owner_region").val();
        var owner_address_country = $("#mgp_owner_country").val();
        var bank_type = $("#mgp_bank_type").val();
        var mgp_owner_address = $('#mgp_owner_address').val();
        var mgp_iban = $('#mgp_iban').val();
        var mgp_bic = $('#mgp_bic').val();
        var mgp_account_number = $('#mgp_account_number').val();
        var mgp_sort_code = $('#mgp_sort_code').val();
        var mgp_aba = $('#mgp_aba').val();
        var mgp_bank_name = $('#mgp_bank_name').val();
        var mgp_institution_number = $('#mgp_institution_number').val();
        var mgp_branch_code = $('#mgp_branch_code').val();

        if ($.trim(owner_address_addressline1) == '') {
            alert(mgp_owner_addrline1_err);
            return false;
        } else if ($.trim(owner_address_city) == '') {
            alert(mgp_owner_addr_city_err);
            return false;
        } else if ($.trim(owner_address_postalCode) == '') {
            alert(mgp_owner_addr_zipcode_err);
            return false;
        } else if ($.trim(owner_address_region) == '') {
            alert(mgp_owner_addr_region_err);
            return false;
        } else if ($.trim(owner_address_country) == '') {
            alert(mgp_owner_addr_country_err);
            return false;
        }

        if (bank_type == 'IBAN') {
            if (!mgp_owner_name) {
                alert(mgp_owner_name_err);
                return false;
            } else if (!mgp_iban) {
                alert(mgp_iban_err);
                return false;
            } else if (!mgp_bic) {
                alert(mgp_bic_err);
                return false;
            }
        } else if (bank_type == 'GB') {
            if (!mgp_owner_name) {
                alert(mgp_owner_name_err);
                return false;
            } else if (!mgp_account_number) {
                alert(mgp_account_number_err);
                return false;
            } else if (!mgp_sort_code) {
                alert(mgp_sort_code_err);
                return false;
            }
        } else if (bank_type == 'US') {
            if (!mgp_owner_name) {
                alert(mgp_owner_name_err);
                return false;
            } else if (!mgp_account_number) {
                alert(mgp_account_number_err);
                return false;
            } else if (!mgp_aba) {
                alert(mgp_aba_err);
                return false;
            }
        } else if (bank_type == 'CA') {
            if (!mgp_owner_name) {
                alert(mgp_owner_name_err);
                return false;
            } else if (!mgp_account_number) {
                alert(mgp_account_number_err);
                return false;
            } else if (!mgp_bank_name) {
                alert(mgp_bank_name_err);
                return false;
            } else if (!mgp_institution_number) {
                alert(mgp_institution_number_err);
                return false;
            } else if (!mgp_branch_code) {
                alert(mgp_branch_code_err);
                return false;
            }
        } else if (bank_type == 'OTHER') {
            if (!mgp_owner_name) {
                alert(mgp_owner_name_err);
                return false;
            } else if (!mgp_account_number) {
                alert(mgp_account_number_err);
                return false;
            } else if (!mgp_bic) {
                alert(mgp_bic_err);
                return false;
            } else if (!mgp_country) {
                alert(mgp_country_err);
                return false;
            }
        }
    });
    $('.deactivate_bank_account').on('click', function() {
        if (confirm(confirm_account_deactivate_msg)) {
            return true;
        }
        return false;
    });

});
//When search on change product for bundle
$(document).on('change', '#seller_id', function() {
    var idSeller = $('#seller_id').val();
    $('#payout_seller_bank_acc_id').find('.wkbankdetails').remove();
    if (idSeller != '') {
        $.ajax({
            url: ajaxurlBankDetails,
            type: 'POST',
            cache: false,
            data: {
                ajax: true,
                action: 'getMangopayBankDetails',
                idSeller: idSeller,
            },
            success: function(result) {
                if (result != 'fail') {
                    var listHTML = '';
                    $.each(jQuery.parseJSON(result), function(index, data) {
                        listHTML += "<option  class='wkbankdetails' value=" + data.Id + ">" + data.Type + "/" + data.Id + "/" + data.OwnerName + "</option>";
                    });
                    $('#payout_seller_bank_acc_id').append(listHTML);
                }
            }
        });
    }
});