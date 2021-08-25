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
    $("#submit_payment_details").on("click", function() {
        var payment_mode = $("#payment_mode").val();
        if (payment_mode == "") {
            $(".mp_payment_error").text(required_payment).css("color", "red");
            return false;
        }
    });

    $(".delete_mp_payment").on("click", function() {
        if (confirm(confirm_msg)) {
            return true;
        } else {
            return false;
        }
    });
});