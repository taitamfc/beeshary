/**
 * 2010-2019 Webkul.
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
 *  @copyright 2010-2019 Webkul IN
 *  @license   https://store.webkul.com/license.html
 */

$(document).ready(function () {
    $("input[name='WK_GDPR_COOKIE_BLOCK_IMAGE_SHOW']").on('change', function () {
        if (parseInt($(this).val())) {
            $("#cookie_block_img_div").removeClass('hidden');
        } else {
            $("#cookie_block_img_div").addClass('hidden');
        }
    });

    $("input[name='WK_GDPR_COOKIE_BLOCK_ENABLE']").on('change', function () {
        if (parseInt($(this).val())) {
            $(".cookie_block_fields").removeClass('hidden');
        } else {
            $(".cookie_block_fields").addClass('hidden');
        }
    });

    // shoe error message when image size execeed from allowed file size
    $('input[type="file"]').on("change", function () {
        if (typeof this.files[0] != "undefined") {
            if (this.files[0].size > maxSizeAllowed * 1000000) {
                $("#showImagePopUp").modal({
                    backdrop: "static",
                    keyboard: false
                });
            }
        }
    });
    $("#closeModal").on("click", function () {
        location.reload();
    });
});

//for tinymce setup
tinySetup({
    editor_selector: "wk_tinymce",
    width: 700
});

$(window).bind("load", function () {
    $('.mColorPickerTrigger').html("<img src='" + baseDir + "img/admin/color.png' style='border:0;margin:0 0 0 3px' align='absmiddle'>");
    $('#mColorPickerImg').css({
        'background-image': "url('" + baseDir + "img/admin/colorpicker.png')"
    });
    $('#mColorPickerImgGray').css({
        'background-image': "url('" + baseDir + "img/admin/graybar.jpg')"
    });
    $('#mColorPickerFooter').css({
        'background-image': "url('" + baseDir + "img/admin/grid.gif')"
    });
});