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
    //when seller choose from datepicker
    $('#date-range-picker').daterangepicker({
        "opens": "left",
        "showDropdowns": true,
        "startDate": userFriendlyDateFrom,
        "endDate": userFriendlyDateTo,
        "maxDate": currentDate,
        locale: {
            format: 'DD-MM-YYYY'
        },
    }, function(start, end, label) {
        $("#dashboardDateFrom").val(start.format('YYYY-MM-DD'));
        $("#dashboardDateTo").val(end.format('YYYY-MM-DD'));
        $('#preselectDateRange').val(0);
        $('.setPreselectDateRange').removeClass('btn-primary').addClass('btn-default');
        searchKeyword(0);
    });

    //When seller choose button day, month, year
    $(".setPreselectDateRange").on('click', function() {
        $('#preselectDateRange').val($(this).attr('data-date-range'));
        //$('button[name="submitDashboardDate"]').click();
        $('.setPreselectDateRange').removeClass('btn-primary').addClass('btn-default');
        $(this).addClass('btn-primary');
        searchKeyword($('#preselectDateRange').val());
    });

    searchKeyword($('#preselectDateRange').val());
});

function searchKeyword(preselectDateRange)
{
    var dateFrom = $('#dashboardDateFrom').val();
    var dateTo = $('#dashboardDateTo').val();
    $.ajax({
        url: stats_link,
        data: {
            ajax: true,
            action: 'getSearchKeyword',
            dateFrom: dateFrom,
            dateTo: dateTo,
            preselectDateRange: preselectDateRange,
        },
        // Ensure to get fresh data
        headers: { "cache-control": "no-cache" },
        cache: false,
        global: false,
        dataType: 'json',
        success: function(result) {
            $('#searchkeyword-table .table-responsive .table tbody').html(result.tpl_file);
        },
        contentType: 'application/json'
    });

}