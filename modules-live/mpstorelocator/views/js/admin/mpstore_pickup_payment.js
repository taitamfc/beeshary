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
    $('input[name="MP_STORE_PICK_UP_PAYMENT"]').on('change', function() {
        toggleMarkerElement($(this).val(), $('.wk_store_payment_enable'));
    });

    toggleMarkerElement(
        $('input[name="MP_STORE_PICK_UP_PAYMENT"]:checked').val(),
        $('.wk_store_payment_enable'),  
    );


    function toggleMarkerElement(value, element) {
        if(value == 1) {
            element.show();
        } else {
            element.hide();
        }
    }
});
