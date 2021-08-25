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
    //Tab active code
    if ($('#current_active_tab').val() != '') {
        var current_active_tab = $('#current_active_tab').val();
        changeTabStatus(current_active_tab);
    }

    $(document).on("click", '.wk-data-erasure-confirm, .wk-gdpr-data-update-btn, .wk-gdpr-email-btn', function(e) {
        //put active tab in input hidden type
        var active_tab_id = $('.wk-gdpr-data-container .nav-tabs li.active a').attr('href');
        if (typeof active_tab_id !== 'undefined') {
            var active_tab_name = active_tab_id.substring(1, active_tab_id.length);
            $('.active_tab').val(active_tab_name);
            $('#current_active_tab').val(active_tab_name);
        }
    });

    $(document).on('click', '.fancybox-cancel-btn', function() {
        $.fancybox.close();
    });

    $('#wk-data-erasure-submit').on('click', function(e) {
        e.preventDefault();
        if ($('#data_erasure_reason').length && $.trim($('#data_erasure_reason').val()) == '') {
            $('#data_erasure_reason').addClass('errorField');
            return false;
        } else {
            $('#data_erasure_reason').removeClass('errorField');
            if ($('#data_erasure_confirm_popup').length) {
                $('#data_erasure_confirm_popup').modal('show');
            }
        }
    });

    $(document).on('click', '.wk-data-erasure-confirm', function() {
        $('.data_erasure_confirmed').val('1');
        $('#data_erasure_form').submit();
    });

    $('.wk-gdpr-other-updates').on('click', function(e) {
        e.preventDefault();
        $('.data-update-block #wk_data_update_form').show();
        return false;
    });

    $('.wk-gdpr-data-update-btn').on('click', function(e) {
        if ($.trim($('#data_update_reason').val()) == '') {
            $('#data_update_reason').addClass('errorField');
            return false;
        } else {
            $('#data_update_reason').removeClass('errorField');
        }
    });

    $('.wk-gdpr-email-btn').on('click', function(e) {
        var emailRegex = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;

        if ($.trim($('#wk-gdpr-cusstomer-email').val()) == '') {
            $('#wk-gdpr-cusstomer-email').addClass('errorField');
            return false;
        } else if (!emailRegex.test($('#wk-gdpr-cusstomer-email').val())) {
            $('#wk-gdpr-cusstomer-email').addClass('errorField');
            return false;
        } else {
            $('#wk-gdpr-cusstomer-email').removeClass('errorField');
        }
    });
});


function changeTabStatus(active_tab) {
    //Remove all tabs from active (make normal)
    $('.wk-gdpr-data-container .nav-tabs li').removeClass('active');
    $('.wk-gdpr-data-container .tab-content .tab-pane').removeClass('active');
    //Add active class in selected tab
    $('[href*="#' + active_tab + '"]').parent('li').addClass('active');
    $('#' + active_tab).addClass('active in');
}
