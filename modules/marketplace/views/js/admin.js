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

$(document).ready(function() {

    //show terms and conditions on page load
    if ($('input[name="MP_TERMS_AND_CONDITIONS_STATUS"]:checked').val() == 1)
        $(".wk_mp_termsncond").show();
    else
        $(".wk_mp_termsncond").hide();

    //hide and show text according to switch
    $('label[for="MP_TERMS_AND_CONDITIONS_STATUS_on"]').on("click", function() {
        $(".wk_mp_termsncond").show();
    });

    $('label[for="MP_TERMS_AND_CONDITIONS_STATUS_off"]').on("click", function() {
        $(".wk_mp_termsncond").hide();
    });

    // seller details show/hide functionality
    if (show_seller_details == 1)
        $(".wk_mp_custom_seller_details").show();
    else
        $(".wk_mp_custom_seller_details").hide();

    //hide and show group according to switch
    $('label[for="MP_SHOW_SELLER_DETAILS_on"]').on("click", function() {
        $(".wk_mp_custom_seller_details").show();
    });

    $('label[for="MP_SHOW_SELLER_DETAILS_off"]').on("click", function() {
        $(".wk_mp_custom_seller_details").hide();
    });

    if ($('input[name="MP_MULTILANG_ADMIN_APPROVE"]:checked').val() == '1') {
        $('.multilang_def_lang').closest('.form-group').hide();
    }

    $('label[for="MP_MULTILANG_ADMIN_APPROVE_off"]').on("click", function() {
        $('.multilang_def_lang').closest('.form-group').show();
    });

    $('label[for="MP_MULTILANG_ADMIN_APPROVE_on"]').on("click", function() {
        $('.multilang_def_lang').closest('.form-group').hide();
    });

    if ($('input[name="MP_URL_REWRITE_ADMIN_APPROVE"]:checked').val() == '0') {
        $('.mp_url_rewrite').closest('.form-group').hide();
    }

    $('label[for="MP_URL_REWRITE_ADMIN_APPROVE_off"]').on("click", function() {
        $('.mp_url_rewrite').closest('.form-group').hide();
    });

    $('label[for="MP_URL_REWRITE_ADMIN_APPROVE_on"]').on("click", function() {
        $('.mp_url_rewrite').closest('.form-group').show();
    });

});