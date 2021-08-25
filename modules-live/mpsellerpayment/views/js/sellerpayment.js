/**
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

$(document).ready(function() {
	$('.open-popup-link').click(function() 	{
		var transaction_id = $(this).attr('transaction_id');
		var total_due = $(this).attr('total_due');
		var id_currency = $(this).attr('id_currency');
		var sign = $(this).attr('sign');
		$(':hidden#transaction_id').attr('value',transaction_id);
		$(':hidden#total_due').attr('value',total_due);
		$(':hidden#id_currency').attr('value',id_currency);
		$('.money_text + sup').text(sign);
	});

	$('.change_status').on('click', function() {
		var transaction_type = $(this).attr('type');
        var check_status = $(this).val();
        var id_seller = $('#id_seller').val();
        var curr_id = $(this).attr('curr_id');
        if (transaction_type == 'Voucher' && wallet_not_exists == 0) {
        	if (check_status == 1) {
		        var info_id = $(this).attr('wallet_info_id');
		        var voucher_amount = $(this).attr('voucher_amt');
		        var voucher_code = $(this).attr('voucher_code');
		        var seller_payment_table_id = $(this).attr('seller_payment_id');

		        if (confirm(confirm_status)) {
			        $(".loading_overlay").css("display", "block");
			        $.ajax({
			            type: 'POST',
			            url: voucher_refund_link,
			            data: {
			                currency_id: curr_id,
			                seller_id: id_seller,
			                info_id: info_id,
			                voucher_amount: voucher_amount,
			                voucher_code: voucher_code,
			                seller_payment_table_id: seller_payment_table_id,
			            },
                  dataType:"json",
			            success: function(data) {
			                if (data.status == "ok") {
			                    alert(success_msg_refund);
			                    location.reload();
			                } else
			                    alert(error_transaction_msg);
			            }
			        });
			    }
        	} else {
		        var id_transaction = $(this).attr('id-transaction');
		        var transaction_amount = $(this).attr('transaction_amount');
		        var seller_payment_table_id = $(this).attr('trans_slr_pay_id');
		        if (confirm(confirm_status)) {
			        $(".loading_overlay").css("display", "block");
			        $.ajax({
			            type: 'POST',
			            url: voucher_create_link,
			            data: {
			            	currency_id: curr_id,
			                id_transaction: id_transaction,
			                seller_id: id_seller,
			                amount: transaction_amount,
			                id_seller_payment: seller_payment_table_id,
			            },
                  dataType:"json",
			            success: function(data) {
			                if (data.status == 'ok') {
			                    alert(success_msg_create_voucher);
			                    location.reload();
			                } else {
			                    alert(error_transaction_msg);
			                }
			            }
			        });
			    }
        	}
            //alert(voucher_case_error);
		} else if (transaction_type == 'Payment'){
			var check = confirm(confirm_status);
			if(check == true) {
				var id = $(this).attr('id-transaction');
				var contentspan = $(this);

				$.ajax({
					type: "POST",
					url: module_path,
					data: {
						id_seller:id_seller,
						id_currency:curr_id,
						id_transaction:id,
						check_status:check_status
					},
					success: function(data){
						if(data == '-1') {
							alert(low_amt_error);
						}
						else if(data == '1') {
							window.location.href = window.location.href;
						}
						else {
							alert(error_transaction_msg);
						}
					}
				});
			}
		} else if (transaction_type == 'Voucher' && wallet_not_exists == 1) {
			alert(voucher_when_no_wallet_txt);
		}
	});

	$('.money_text:eq(0)').focus(function() {
		if($(this).val() == 'amount') {
			$(this).attr('value', '');
		}
	});

	$('.money_text:eq(0)').focusout(function() {
		if($(this).val() == '') {
			$(this).attr('value', 'amount');
		}
	});

	$('.money_text:eq(1)').focus(function()	{
		if($(this).val() == 'currency') {
			$(this).attr('value', '');
		}
	});

	$('.money_text:eq(1)').focusout(function() {
		if($(this).val() == '') {
			$(this).attr('value', 'currency');
		}
	});

	$(document).on('click', '#pay_money', function() {
		if($('.money_text').val() == '' || $('.money_text').val() == 'amount') {
			alert(blank_error);
			return false;
		}
		else if(!$.isNumeric($('.money_text').val())) {
			alert(numeric_error);
			return false;
		}
		else if(parseFloat($('.money_text').val()) <= 0) {
			alert(not_zero_error);
			return false;
		}
		else if(parseFloat($('.money_text').val()) > parseFloat($('#total_due').val())) {
			alert(due_total_error);
			return false;
		}
	});
});
