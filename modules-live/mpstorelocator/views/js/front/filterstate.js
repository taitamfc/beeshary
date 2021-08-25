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
    if ($('#id_store').val()) {
        filterState(country_id);
    }

    $("#countries").on("change", function() {
        var id_country = $(this).val();
        if (id_country == "") {
            alert(select_country);
            $("#state").empty();
            $("#state").append("<option value=''>Select</option>");
        } else {
            filterState(id_country);
        }
    });
});

function filterState(id_country) {
    $.ajax({
        url: url_filestate,
        dataType: "json",
        data: {
            id_country: id_country
        },
        success: function(result) {
            if (result == 'no_states') {
                $("#state").empty();
                $(".country_states_div").hide();
            } else if (result != 'failed') {
                $(".country_states_div").show();
                $("#state").empty();
                $("#state").append("<option value=''>Select</option>");
                $.each(result, function(index, value) {
                    if (id_state == value.id_state) {
                        $('#uniform-state span').html(value.name);
                        $("#state").append("<option value=" + value.id_state + " selected>" + value.name + "</option>");
                    } else {
                        $("#state").append("<option value=" + value.id_state + ">" + value.name + "</option>");
                    }
                });
            }
        }
    });
}