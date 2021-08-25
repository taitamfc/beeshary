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

$(document).ready(function() {

    //Call on page load
    hideAndShowSellerDetails();
    hideAndShowTermsCondition();
    hideAndShowMultiLangAdminApprove();
    hideAndShowLinkRewriteURL();
    hideAndShowMpCombinationActivateDeactivate();
    hideAndShowMpSocialTab();
    hideSellerOrderStatus();
    hideCustomerReviewSettings();

    //hide and show seller details tab according to switch
    $('input[name="WK_MP_SHOW_SELLER_DETAILS"]').on("click", function() {
        hideAndShowSellerDetails();
    });

    //hide and show terms and condition text box according to switch
    $('input[name="WK_MP_TERMS_AND_CONDITIONS_STATUS"]').on("click", function() {
        hideAndShowTermsCondition();
    });

    //hide and show multilang options text box according to switch
    $('input[name="WK_MP_MULTILANG_ADMIN_APPROVE"]').on("click", function() {
        hideAndShowMultiLangAdminApprove();
    });

    //hide and show link rewrite text box according to switch
    $('input[name="WK_MP_URL_REWRITE_ADMIN_APPROVE"]').on("click", function() {
        hideAndShowLinkRewriteURL();
    });

    //hide and show combination activate/deactive options for seller
    $('input[name="WK_MP_SELLER_PRODUCT_COMBINATION"]').on("click", function() {
        hideAndShowMpCombinationActivateDeactivate();
    });

    //hide and show social tabs
    $('input[name="WK_MP_SOCIAL_TABS"]').on("click", function() {
        hideAndShowMpSocialTab();
    });

    //hide and show order status
    $('input[name="WK_MP_SELLER_ORDER_STATUS_CHANGE"]').on("click", function() {
        hideSellerOrderStatus();
    });

    //hide and show customer review settings
    $('input[name="WK_MP_REVIEW_SETTINGS"]').on("click", function() {
        hideCustomerReviewSettings();
    });

    // If color picker is not working  background image for color then we have to change the path.
    if (typeof color_picker_custom != 'undefined') {
        $.fn.mColorPicker.defaults.imageFolder = '../img/admin/';
    }
});

function hideAndShowSellerDetails() {
    if ($('input[name="WK_MP_SHOW_SELLER_DETAILS"]:checked').val() == 1) {
        $(".wk_mp_seller_details").show();
    } else {
        $(".wk_mp_seller_details").hide();
    }
}

function hideAndShowTermsCondition() {
    if ($('input[name="WK_MP_TERMS_AND_CONDITIONS_STATUS"]:checked').val() == 1) {
        $(".wk_mp_termsncond").show();
    } else {
        $(".wk_mp_termsncond").hide();
    }
}

function hideAndShowMultiLangAdminApprove() {
    if ($('input[name="WK_MP_MULTILANG_ADMIN_APPROVE"]:checked').val() == 1) {
        $('.multilang_def_lang').hide();
    } else {
        $('.multilang_def_lang').show();
    }
}

function hideAndShowLinkRewriteURL() {
    if ($('input[name="WK_MP_URL_REWRITE_ADMIN_APPROVE"]:checked').val() == 1) {
        $('.mp_url_rewrite').show();
    } else {
        $('.mp_url_rewrite').hide();
    }
}

function hideAndShowMpCombinationActivateDeactivate() {
    if ($('input[name="WK_MP_SELLER_PRODUCT_COMBINATION"]:checked').val() == 1) {
        $('.wk_mp_combination_customize').show();
    } else {
        $('.wk_mp_combination_customize').hide();
    }
}

function hideAndShowMpSocialTab() {
    if ($('input[name="WK_MP_SOCIAL_TABS"]:checked').val() == 1) {
        $('.wk_mp_social_tab').show();
    } else {
        $('.wk_mp_social_tab').hide();
    }
}

function hideSellerOrderStatus() {
    if ($('input[name="WK_MP_SELLER_ORDER_STATUS_CHANGE"]:checked').val() == 1) {
        $('.wk_mp_seller_order_status').show('slow');
    } else {
        $('.wk_mp_seller_order_status').hide('slow');
    }
}

function hideCustomerReviewSettings() {
    if ($('input[name="WK_MP_REVIEW_SETTINGS"]:checked').val() == 1) {
        $('.mp_review_settings').show('slow');
    } else {
        $('.mp_review_settings').hide('slow');
    }
}
