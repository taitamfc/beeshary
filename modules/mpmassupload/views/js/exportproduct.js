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
    $('#export_csv').on('click', function(e) {
        setTimeout(function(){ $('#wk_export_zip').submit(); }, 3000);
        setTimeout(function(){ window.location.href = exportControllerLink;
    }, 5000);
    });
    var massUpdateAction = $('#mass_export_category').val();
    if (massUpdateAction == 1) {
        $('.wk_export_category_combination').hide();
        $('.wk_export_category_product').show();
    } else if (massUpdateAction == 3) {
        $('.wk_export_category_combination').show();
        $('.wk_export_category_product').hide();
    }
    $('#mass_export_category').on('change', function() {
        var selectedCat = $(this).val();
        if (selectedCat == 1) {
            $('.wk_export_category_combination').hide();
            $('.wk_export_category_product').show();
        } else if (selectedCat == 2) {
            $('.wk_export_category_combination').show();
            $('.wk_export_category_product').hide();
        }
    });
})