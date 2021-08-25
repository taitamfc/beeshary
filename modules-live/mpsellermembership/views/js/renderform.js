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
	$(document).on('change', '#seller_detail', function() {
		var seller_detail_link = $('#seller_detail_link').val();
		var mp_seller_id = $(this).val();
		document.location.href = seller_detail_link+'&mp_seller_id='+mp_seller_id;
	});
});