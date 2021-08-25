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

var wkDataTable = '';
$(document).ready(function() {
    // Display datatables in lead request page
    if ($("table.wk-mpbooking-datatable").length) {
        wkDataTable = $('table.wk-mpbooking-datatable').DataTable({
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

function showBookingProductLangField(lang_iso_code, id_lang)
{
	$('.booking_product_fields_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
	$('.feature_price_name_all').hide();
	$('#feature_price_name_'+id_lang).show();
}

function getDatePickerMinDate(selectedDate) {
    if (selectedDate) {
        var date_format = selectedDate.split("-");
        var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
        selectedDate.setDate(selectedDate.getDate());
        return selectedDate;
    } else {
        return 0;
    }
}

// To highlight the dates
function highlightDateBorder(elementVal, date) {
    if (elementVal) {
        var currentDate = date.getDate();
        var currentMonth = date.getMonth() + 1;
        if (currentMonth < 10) {
            currentMonth = '0' + currentMonth;
        }
        if (currentDate < 10) {
            currentDate = '0' + currentDate;
        }
        dmy = date.getFullYear() + "-" + currentMonth + "-" + currentDate;
        var date_format = elementVal.split("-");
        var check_in_time = (date_format[2]) + '-' + (date_format[1]) + '-' + (date_format[0]);
        if (dmy == check_in_time) {
            return [true, "selectedCheckedDate", "Check-In date"];
        } else {
            return [true, ""];
        }
    } else {
        return [true, ""];
    }
}