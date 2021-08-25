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
    //display mp shipping list in popup
    $(document).on('click', '.assign_shipping', function(e) {
        e.preventDefault();
        $('#assign_shipping_form').modal('show');
    });

    //Assign shipping on products
    $('#assign').click(function(e) {
        e.preventDefault();
        var form = $('#shipping_form');

        if ($(':checkbox:checked').length > 0) {
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                async: true,
                cache: false,
                data: form.serialize(),
                success: function(dataresult) {
                    $('#assign_shipping_form').hide();
                    $('.modal-backdrop.in').css('opacity', 0);
                    if (dataresult == 1) {
                        alert(success_msg);
                    } else {
                        alert(error_msg);
                    }
                    window.location.href = window.location.href;
                }
            });
        } else {
            alert(check_msg);
        }
    });
});