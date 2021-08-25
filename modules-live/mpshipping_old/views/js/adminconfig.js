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
    $('#assign_shipping').on('click', function(e) {
        e.preventDefault();
        $('.loader').show();
        $.ajax({
            url: ajaxurl_admin_mpshipping_url,
            data: {
                action: "updateCarrierToMainProducts",
                ajax: "1"
            },
            dataType: "json",
            async: false,
            success: function(result) {
                if (result.status == 'ok') {
                    $('.bootstrap').show();
                    $('.module_confirmation').show();
                    $('.module_confirmation span').text(result.msg);
                } else {
                    $('.bootstrap').show();
                    $('.module_error').show();
                    $('.module_error span').text(result.msg);
                }
                $('.loader').hide();
            }
        });
        return false;
    });

    //on page load
    hideShowAdminShippingDistribution();

    //hide and show seller details tab according to switch
    $('input[name="MP_SHIPPING_DISTRIBUTION_ALLOW"]').on("click", function() {
        hideShowAdminShippingDistribution();
    });

});

function hideShowAdminShippingDistribution() {
    if ($('input[name="MP_SHIPPING_DISTRIBUTION_ALLOW"]:checked').val() == 1) {
        $(".wk-admin-shipping-distribute").show();
    } else {
        $(".wk-admin-shipping-distribute").hide();
    }
}


