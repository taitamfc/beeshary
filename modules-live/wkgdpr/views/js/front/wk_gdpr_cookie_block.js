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

$(document).ready(function () {
    $(document).on('click', '.wk-cookie-accept-btn', function () {
        var secureToken = $(this).data('cookie_block_token');
        $.ajax({
            type: 'POST',
            url: wkGdprControlsLink,
            data: {
                ajax: true,
                action: 'setCookieAcceptedByCustomer',
                token: secureToken,
            },
            success: function (result) {
                if (result == 'ok') {
                    $('.wk-cookie-block-wrapper').hide('slow');
                    console.log('cookie accepted');
                } else {
                    console.log('cookie set failed');
                }
            },
        });
    });

    $(document).on('click', '.wk-cookie-close', function () {
        $('.wk-cookie-block-wrapper').hide('slow');
    });
});