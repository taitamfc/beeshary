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
    $(".delete_shipping").on("click", function() {
        if ($(this).data('prod')) {
            var mpshipping_id = $(this).data('shipping-id');
            if (mpshipping_id) {
                $.ajax({
                    type: 'POST',
                    url: ajaxurl_shipping_extra,
                    async: true,
                    cache: false,
                    data: {
                        mpshipping_id: mpshipping_id,
                        'delete_action': 1
                    },
                    success: function(data) {
                        if (data != 0) {
                            $('#delete_shipping_id').val(data);
                            $('#extra_shipping option[value="' + data + '"]').remove();

                            var selectObject = $('#extra_shipping option');
                            if (!selectObject.length) {
                                $('#shippingactive').remove();
                                $('#noshippingactive').show();
                            }
                        }
                    }
                });

                $('.delete_shipping').fancybox();
            }
        } else {
            if (!confirm(confirm_msg)) {
                return false;
            }
        }
    });

    $('#add_default_shipping').on('click', function() {
        $('#default_shipping_div').slideDown();
        $('#default_shipping_show').hide();
    });

    $('#cancel_default_shipping').on('click', function() {
        $('#default_shipping_div').slideUp();
        $('#default_shipping_show').show();
    });

    if (typeof wk_dataTables != 'undefined') {
        $('#mp_shipping_list').DataTable({
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
            },
            "order": [
                [0, "desc"]
            ]
        });

        $('select[name="mp_shipping_list_length"]').addClass('form-control-select');
    }
});