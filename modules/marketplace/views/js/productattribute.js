/**
 * 2010-2020 Webkul.
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
 *  @copyright 2010-2020 Webkul IN
 *  @license   https://store.webkul.com/license.html
 */

$(document).ready(function() {
    $(document).on('click', '.wk-mp-data-list', function() {
        var url = $(this).data('value-url');
        window.location.href = url;
    });

    $(document).on('click', '.edit_button', function() {
        var editable = $(this).attr('edit');
        if (editable == 0) {
            alert(error_msg1);
            return false;
        }
    });

    $(document).on('click', '.delete_button', function() {
        var editable = $(this).attr('edit');
        if (editable == 0) {
            alert(error_msg1);
            return false;
        } else if (!confirm(confirm_delete))
            return false;
    });

    $(document).on('click', '.edit_but', function() {
        var editable = $(this).attr('edit');
        if (editable == 0) {
            alert(error_msg2);
            return false;
        }
    });

    $(document).on('click', '.del_attr_val', function() {
        var editable = $(this).attr('edit');
        if (editable == 0) {
            alert(error_msg2);
            return false;
        } else if (!confirm(confirm_delete))
            return false;
    });

    //On page load if color type is selected
    if (typeof id_group != 'undefined') {
        colorCheck(id_group);
    }
    //change attribute and selected color type
    $(document).on('change', '#attrib_group', function() {
        colorCheck($(this).val());
    });
});

function colorCheck(id_group) {
    if (id_group != 0) {
        $(".loading_img").html("<img width='17' src='" + mp_image_dir + "loading-small.gif'>");
        $.ajax({
            url: createattributevalue_controller,
            type: 'POST',
            data: {
                group_id: id_group,
                ajax: true,
                action: 'checkColorType'
            },
            success: function(data) {
                if (data == 1) {
                    $('#attrib_value_color_div').show();
                } else {
                    $('#attrib_value_color_div').hide();
                }
                $(".loading_img").html("");
            },
            error: function(XHR, textStatus, errorThrown) {}
        });
    }
}