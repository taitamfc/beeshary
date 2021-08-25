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

    $('.settle_payment_request').on('click', function() {
        $("#mpsellerpaymentrequest_modal button.approve").show();
        $.ajax({
            type: "POST",
            url: window.location.href+'&time='+Date.now(),
            data: {
                id_seller_payment_request: $(this).data('id_request'),
                action: 'GetModalForm',
                ajax: true,
            },
            dataType: 'html',
            cache: false,
            headers: { 'cache-control': 'no-cache' },
            success: function(response) {
                if (response) {
                    $('#mpsellerpaymentrequest_modal .modal-body').replaceWith(response);
                    if ($("#mpsellerpaymentrequest_modal .request_accept_form").length == 0) {
                        $("#mpsellerpaymentrequest_modal button.approve").hide();
                    }
                    $('#mpsellerpaymentrequest_modal').addClass('show').removeClass('fade');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (textStatus != 'error' || errorThrown != '')
                    showErrorMessage(textStatus + ': ' + errorThrown);
            }
        });
    });

    $('body').on('click', '[data-dismiss="modal"]', function(){
        $(this).parents('.modal').removeClass('show').addClass('fade');
    });
});
