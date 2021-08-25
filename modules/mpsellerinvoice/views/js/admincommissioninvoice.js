/*
* 2010-2019 Webkul
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
*  @author Webkul IN <support@webkul.com>
*  @copyright  2010-2019 Webkul IN
*/
$(document).ready(function(){
    $(document).on('click', '#wk_commission_send', function(){
        $('#invoice_number').attr('value', $(this).attr('data-id-invoice'));
        $("#send_invoice").modal('show');
        return false;
    });
    $(document).on('click', '#sendInvoice', function(e){
        var email = $('#wk_commission_email').val();
        email = $.trim(email);
        if (email == '') {
            $(".empty_error").show();
            $(".invalid_error").hide();
            return false;
        } else if (!ValidateEmail(email)) {
            $(".empty_error").hide();
            $(".invalid_error").show();
            return false;
        }
        $(".empty_error").hide();
        $(".invalid_error").hide();
    });
});
function ValidateEmail(email)
{
	var check = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	return check.test(email);
};