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
    $('input[name="enableCustomMarker"]').on('change', function() {
        toggleMarkerElement($(this).val(), $('.mp_marker_enable').closest('.form-group'));
    });

    toggleMarkerElement($('input[name="enableCustomMarker"]:checked').val(), $('.mp_marker_enable').closest('.form-group'));

    $('.mp-multiselect-countries').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        maxHeight: 200,
        enableCaseInsensitiveFiltering: true
    });
    $('i.glyphicon.glyphicon-search').removeClass('glyphicon glyphicon-search').addClass('material-icons').html('search');
    $('i.glyphicon.glyphicon-remove-circle').removeClass('glyphicon glyphicon-remove-circle').addClass('material-icons').html('cancel');

    $('input[name="enableCountryRestriction"]').on('change', function() {
        toggleMarkerElement($(this).val(), $('.mp_store_country_restrict'));
    });

    $('input[name="enableDateSelection"]').on('change', function() {
        toggleMarkerElement($(this).val(), $('.mp_store_date_enable'));
        $('input[name="enableTimeSelection"]').removeAttr('checked');
        $('input[name="enableTimeSelection"][value="'+$(this).val()+'"]').prop('checked', true);
    });
    $('input[name="enableTimeSelection"]').on('change', function() {
        toggleMarkerElement($(this).val(), $('.mp_store_time_enable'));
    });

    toggleMarkerElement(
        $('input[name="enableCountryRestriction"]:checked').val(),
        $('.mp_store_country_restrict')
    );
    toggleMarkerElement(
        $('input[name="enableDateSelection"]:checked').val(),
        $('.mp_store_date_enable')
    );
    toggleMarkerElement(
        $('input[name="enableTimeSelection"]:checked').val(),
        $('.mp_store_time_enable'),
        $('input[name="enableDateSelection"]:checked').val()
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

    $('.wk-tabs-panel .nav-link').on('click', function() {
        activeTab =$(this).attr('href');
        activeTab = activeTab.replace('#', "");
        $('#active_tab').val(activeTab);
    });
});
