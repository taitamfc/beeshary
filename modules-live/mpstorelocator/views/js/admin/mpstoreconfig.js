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
    $('.mp_store_marker input[name="MP_STORE_MARKER_ICON_ENABLE"]').on('change', function() {
        toggleMarkerElement($(this).val(), $('input[name="MP_STORE_MARKER"]').closest('.form-group').parent());
    });
    $('.mp_store_map input[name="MP_STORE_MAP_ZOOM_ENABLE"]').on('change', function() {
        toggleMarkerElement($(this).val(), $('#MP_STORE_MAP_ZOOM'));
    });

    toggleMarkerElement($('.mp_store_marker input[name="MP_STORE_MARKER_ICON_ENABLE"]:checked').val(), $('input[name="MP_STORE_MARKER"]').closest('.form-group').parent());
    toggleMarkerElement($('.mp_store_map input[name="MP_STORE_MAP_ZOOM_ENABLE"]:checked').val(), $('#MP_STORE_MAP_ZOOM'));

    function toggleMarkerElement(value, element) {
        if(value == 1) {
            element.closest('.form-group').show();
        } else {
            element.closest('.form-group').hide();
        }
    }
});
