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

$(document).ready(function() {

	$(document).on('click',  '#add_payment_reqeust', function(e){
		$.ajax({
			'type' : 'POST',
			'url' : window.location.href,
			'cache' : false,
			'async' : false,
			'data' : {
				'ajax' : true,
				'action' : 'getPaymentRequestForm',
			},
			success : function(result) {
				$('#wk_seller_payment_request').html(result);
				$('#paymentRequest').modal('show');
			}
		});
		return false;
	});

	$('#payment_request-table').DataTable({
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

});
