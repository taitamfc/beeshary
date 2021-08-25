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

$(document).ready(function()
{
	$('#createaccountform,#wk_edit_profile_form').on('submit', function() {
		var sellerMobileNumber = $('#sellerMobileNumber').val().trim();
		var sellerCountryCode = $('#sellerCountryCode').val().trim();
		if (sellerCountryCode == '') {
			alert(countryCodeError);
			$('#sellerCountryCode').focus();
			return false;
		} else if (sellerMobileNumber == '') {
			alert(phoneError);
			$('#sellerMobileNumber').focus();
			return false;
		} else if (isNaN(sellerMobileNumber)) {
			alert(phoneNotValid);
			$('#sellerMobileNumber').focus();
			return false;
		}
	});
});