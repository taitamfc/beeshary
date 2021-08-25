/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-9999 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

function changeLanguageMine(field, fieldsString, id_language_new, iso_code) {
    $('.langbutton' + field).addClass('button-outline');
    $('#langbutton' + field + '_' + id_language_new).removeClass('button-outline');

    $('div[id^=' + field + '_]').hide();
    $('#' + field + '_' + id_language_new).show();
}

$(document).ready(function () {
    $("#hbp_newhook_button").toggle(function () {
        $("#hbp_newhook_form").show("fast");
    }, function () {
        $("#hbp_newhook_form").hide("fast");
    });

    $(".editbutton, .duplicatebutton").hover(
        function () {
            $(this).fadeTo("fast", 1.0);
        },
        function () {
            $(this).fadeTo("fast", 0.3);
        }
    );

    $(".remove, .edit, .duplicate").hover(
        function () {
            $(this).fadeTo("fast", 1.0);
        },
        function () {
            $(this).fadeTo("fast", 0.3);
        }
    );


    $(".accordion").toggle(
        function () {
            $(".hook_blocks").css("display", "none");
            var alt = $(this).attr("alt");
            //$(".hook_"+alt).css("display","table-row");
            $(".hook_" + alt).show("fast");
        },
        function () {
            $(".hook_blocks").css("display", "none");
            var alt = $(this).attr("alt");
            //$(".hook_"+alt).css("display","none");
            $(".hook_" + alt).hide("fast");
        }
    );


});