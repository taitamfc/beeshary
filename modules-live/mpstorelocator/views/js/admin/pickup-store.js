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
    $('.mp-multiselect-countries').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        maxHeight: 200,
    });
    $('i.glyphicon.glyphicon-search').removeClass('glyphicon glyphicon-search').addClass('icon icon-search');
    $('i.glyphicon.glyphicon-remove-circle').removeClass('glyphicon glyphicon-remove-circle').addClass('icon icon-remove-circle');

    $('input[name="MP_STORE_COUNTRY_ENABLE"]').on('change', function() {
        toggleMarkerElement($(this).val(), $('.mp_store_country_restrict'));
    });

    $('input[name="MP_STORE_PICKUP_DATE"]').on('change', function() {
        toggleMarkerElement($(this).val(), $('.mp_store_date_enable'));
    });
    $('input[name="MP_STORE_TIME"]').on('change', function() {
        toggleMarkerElement($(this).val(), $('.mp_store_time_enable'));
    });

    
    toggleMarkerElement(
        $('input[name="MP_STORE_COUNTRY_ENABLE"]:checked').val(),
        $('.mp_store_country_restrict')
    );
    toggleMarkerElement(
        $('input[name="MP_STORE_PICKUP_DATE"]:checked').val(),
        $('.mp_store_date_enable')
    );
    toggleMarkerElement(
        $('input[name="MP_STORE_TIME"]:checked').val(),
        $('.mp_store_time_enable'),
        $('input[name="MP_STORE_PICKUP_DATE"]:checked').val()
    );


    function toggleMarkerElement(value, element, thirdElement = 2) {
        if(value == 1) {
            element.show();
        } else {
            element.hide();
        }
        if (thirdElement!= 2){
            if(thirdElement == 1 && value == 1) {
                element.show();
            } else {
                element.hide();
            }   
        }
    }
});