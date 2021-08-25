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
    var elementSearchAjax = null;
    $("body").on('keyup', "#customer-suggestion-input", function () {
        var suggestionElement = $(this).siblings('#wk_customer_suggestion_cont');

        // Clear DOM Elements
        suggestionElement.hide().empty();
        if ($(this).val().trim().length) {
            var searchText = $(this).val().trim();

            if (elementSearchAjax) {
                elementSearchAjax.abort();
            }

            elementSearchAjax = $.ajax({
                url: wkGdprCustomerDataManagementLink,
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax: true,
                    action: 'customerSearch',
                    searchText: searchText,
                },
                success: function (result) {
                    var html = '';
                    if (Object.keys(result).length) {
                        $.each(result, function (index, element) {
                            html += "<li class='wk_customer_suggestion_list' data-url='" + wkGdprViewCustomerDataLink + "&id_customer=" + element.id_customer + "' data-id-customer='" + element.id_customer + "' data-customer-name='" + element.firstname + " " + element.lastname + "'>" + element.firstname + " " + element.lastname + "</li>";
                        });
                    } else {
                        html += "<li>" + noResultFound + "</li>";
                    }
                    if (html) {
                        suggestionElement.append(html).show();
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }
    });

    $("body").on("click", ".wk_customer_suggestion_list", function () {
        $("#customer-suggestion-input").val($(this).attr("data-customer-name"));
        $("#wk_customer_suggestion_cont").hide().empty();
        window.location.href = $(this).attr("data-url");
    });

    $(document).on("click", "body", function () {
        if ($('#wk_customer_suggestion_cont li').is(":visible")) {
            $("#wk_customer_suggestion_cont").hide().empty();
        }
    });

    $("#customer-data-erase-btn").on('click', function (e) {
        if (!confirm(eraseConfirmString)) {
            return false;
        }
    });

    if ($("table.wk-gdpr-datatable").length) {
        wkDataTable = $('table.wk-gdpr-datatable').DataTable({
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