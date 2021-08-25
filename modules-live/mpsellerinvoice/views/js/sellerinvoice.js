/*
* 2010-2019 Webkul
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
*  @author Webkul IN <support@webkul.com>
*  @copyright  2010-2019 Webkul IN
*/
$(document).ready(function(){
	// if ($('input[name=MP_SELLER_INVOICE_ACTIVE]:checked').val() == 1) {
	// 	$('.mpsellerprefix').show('slow');
	// } else {
	// 	$('.mpsellerprefix').hide('slow');
	// }

	if ($('input[name=MP_SELLER_INVOICE_TO_ADMIN]:checked').val() == 1) {
		$('.mpsellerinvoiceadmin').show();
	} else {
		$('.mpsellerinvoiceadmin').hide();
	}

	$('input[name=MP_SELLER_INVOICE_TO_ADMIN]').on('change', function(){
		if ($(this).val() == 1) {
			$('.mpsellerinvoiceadmin').show('slow');
		} else {
			$('.mpsellerinvoiceadmin').hide('slow');
		}
	});

	// $('input[name=MP_SELLER_INVOICE_ACTIVE]').on('change', function(){
	// 	if ($(this).val() == 1) {
	// 		$('.mpsellerprefix').show('slow');
	// 		if ($('input[name=MP_SELLER_INVOICE_TO_ADMIN]:checked').val() == 1) {
	// 			$('.mpsellerinvoiceadmin').show();
	// 		} else {
	// 			$('.mpsellerinvoiceadmin').hide();
	// 		}
	// 	} else {
	// 		$('.mpsellerprefix').hide('slow');
	// 	}
	// });

	if ($('input[name=MP_SELLER_INVOICE_TO_SELLER]:checked').val() == 0 && $('input[name=MP_COMMISSION_INVOICE_TO_SELLER_AUTOMATIC]:checked').val() == 0) {
		$('.wk_mp_seller_order_status').hide('slow');
	} else {
		$('.wk_mp_seller_order_status').show('slow');
	}

	$('input[name=MP_SELLER_INVOICE_TO_SELLER]').on('change', function(){
		if ($(this).val() == 0 && $('input[name=MP_COMMISSION_INVOICE_TO_SELLER_AUTOMATIC]:checked').val() == 0) {
			$('.wk_mp_seller_order_status').hide('slow');
		} else {
			$('.wk_mp_seller_order_status').show('slow');
		}
	});

	$('input[name=MP_COMMISSION_INVOICE_TO_SELLER_AUTOMATIC]').on('change', function(){
		if ($(this).val() == 0 && $('input[name=MP_SELLER_INVOICE_TO_SELLER]:checked').val() == 0) {
			$('.wk_mp_seller_order_status').hide('slow');
		} else {
			$('.wk_mp_seller_order_status').show('slow');
		}
	});


	$('#my-orders-table tr td:not(:last-child)').click(function () {
		var tr = $(this).closest('tr');
        var id_order =  tr.attr('data-id-order');
		if (id_order) {
			if (friendly_url) {
				window.location.href = mporderdetails_link+'?id_order='+id_order;
			} else {
				window.location.href = mporderdetails_link+'&id_order='+id_order;
			}
		}
    });
	$(document).on('click', 'li.nav-item .nav-link', function(){
		if ($('.wk_invoice_msg').length) {
			var stateObj = {
				wkDemo : "wkDemo"
			};
			$('.wk_invoice_msg').remove();
			window.history.pushState(stateObj, undefined, manageinvoice_link);
		}
	});

	if ($('#my-orders-table-2').length) {
		$('#my-orders-table-2').DataTable({
			"language": {
				"lengthMenu": display_name+" _MENU_ "+records_name,
				"zeroRecords": no_product,
				"info": show_page+" _PAGE_ "+ show_of +" _PAGES_ ",
				"infoEmpty": no_record,
				"infoFiltered": "("+filter_from +" _MAX_ "+ t_record+")",
				"sSearch" : search_item,
				"oPaginate": {
					"sPrevious": p_page,
					"sNext": n_page
					}
			},
			"order": [[ 0, "desc" ]]
		});
	}
});