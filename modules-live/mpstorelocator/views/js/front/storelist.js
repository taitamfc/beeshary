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
$(document).ready(function () {
    $(".mp_store_bulk_delete_btn").on("click", function (e) {
        e.preventDefault();
        if (!$('.mp_store_select:checked').length) {
            alert(checkbox_select_warning);
            return false;
        } else {
            if (!confirm(confirm_delete_msg))
                return false;
            else
                $('#mp_storelist_form').submit();
        }
    });
    $(".delete_img").on("click", function (e) {
        if (!confirm(confirm_delete_msg))
            e.preventDefault();
    });

    $("#mp_store__all_select").on("click", function () {
        if ($(this).is(':checked')) {
            $('.bundle_bulk_select').parent().addClass('checker checked');
            $('.bundle_bulk_select').attr('checked', 'checked');
        } else {
            $('.bundle_bulk_select').parent().removeClass('checker checked');
            $('.bundle_bulk_select').removeAttr('checked');
        }
    });

    if ($("#mp_store_list").length) {
        $('#mp_store_list').DataTable({
            "order": [],
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
            }],
            "language": {
                "lengthMenu": display_name + " _MENU_ " + records_name,
                "zeroRecords": no_product,
                "info": show_page + " _PAGE_ " + show_of + " _PAGES_ ",
                "infoEmpty": no_record,
                "infoFiltered": "(" + filter_from + " _MAX_ " + t_record + ")",
                "sSearch": search_item,
                "oPaginate": {
                    "sPrevious": p_page,
                    "sNext": n_page
                }
            }
        });
    }
});
