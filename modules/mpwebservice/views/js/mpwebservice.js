/**
 * 2010-2021 Webkul.
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
 *  @copyright 2010-2021 Webkul IN
 *  @license   https://store.webkul.com/license.html
 */

$(document).ready(function() {
    $(document).on('change','#wk_select_all_mpapi', function() {
        $('input[name="mpapi[]"]').prop('checked', $(this).prop("checked"));
        if ($(this).prop("checked") == true) {
            $('input[name="mpapi[]"]').parent('span').addClass('checked');
        } else {
            $('input[name="mpapi[]"]').parent('span').removeClass('checked');
        }
    });
});

function gencode(size)
{
	getE('code').value = '';
	/* There are no O/0 in the codes in order to avoid confusion */
	var chars = "123456789ABCDEFGHIJKLMNPQRSTUVWXYZ";
	for (var i = 1; i <= size; ++i)
		getE('code').value += chars.charAt(Math.floor(Math.random() * chars.length));
}

function getE(name)
{
	if (document.getElementById)
		var elem = document.getElementById(name);
	else if (document.all)
		var elem = document.all[name];
	else if (document.layers)
		var elem = document.layers[name];
	return elem;
}
