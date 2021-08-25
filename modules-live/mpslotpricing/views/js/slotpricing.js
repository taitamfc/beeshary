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

$(document).ready(function(){
	$('#leave_bprice').click(function() {
		if (this.checked)
			$('#sp_price').attr('disabled', 'disabled');
		else
			$('#sp_price').removeAttr('disabled');
	});
	
	$('#sp_reduction_type').on('change', function() {
		if ($(this).attr('value') == 'percentage') {
			$('#sp_reduction_tax').hide();
		}
		else {
			$('#sp_reduction_tax').show();
		}
	});

	$('.slot_delete').click(function(e){
		e.preventDefault();
		var slot_id = $(this).attr('id_slot');
		if (confirm(conf_delete))
			delete_slot(slot_id);
	});

	$("#sp_from, #sp_to").datetimepicker({
        showSecond: true,
        dateFormat:"yy-mm-dd",
        timeFormat: "hh:mm:ss",
    });

	$('#show_specific_price').click(function() {
		$('#SubmitCreate').hide();
		$('#add_specific_price').slideToggle('slow');
		$('#hide_specific_price').show();
		$('#show_specific_price').hide();
		$('#showTpl').val('1');
		return false;
	});

	$('#hide_specific_price').click(function() {
		$('#SubmitCreate').show();
		$('#add_specific_price').slideToggle('slow');
		$('#hide_specific_price').hide();
		$('#show_specific_price').show();
		$('#showTpl').val('0');
		return false;
	});
});

// delete slot price 
$(document).on('click', 'a[name=slot_delete_link]', function(e){
	e.preventDefault();
	if (!confirm(conf_delete)) {
		return false;
	}
	$('.wkslotprice_loader').show();
	var del_id = this.id;
	$.ajax({
		type 	:	'post',
		//url 	:	$('#ajax_link').val(),
		url   	: 	modules_dir+'mpslotpricing/ajax/priceslotprocess.php',
		async 	: 	true,
		cache 	: 	false,
		dataType: "json",
		data:{
			id_delete : del_id,
			delete_slot : 1,
		},
		success:function(data1)
		{
			$('.wkslotprice_loader').hide();
			if(data1 == 1) {
				$('#slotcontent'+del_id).fadeOut(500, function(){ $(this).remove();});
			}
			else if(data1 == 2) {
				alert(delete_err);
				return false;
			}
		}
	});
	return false;
});

// aadding slot price
$(document).on('click', '#add_btn', function(e){
	e.preventDefault();
	var sp_reduction = $('#sp_reduction').val();
	var sp_from_quantity = $('#sp_from_quantity').val();
	if (!sp_from_quantity) {
		alert(sp_quantity_empty);
		return false;
	} else if(!isInt(sp_from_quantity)) {
		alert(invalid_qty);
		return false;
	}
	if (!sp_reduction) {
		alert(sp_reduction_err);
		return false;
	}
	$('.wkslotprice_loader').show();
	var str = $("#add_specific_price *").serialize();
	$.ajax({
		type 	:	'post',
		//url 	:	$('#ajax_link').val(),
		url   	: 	modules_dir+'mpslotpricing/ajax/priceslotprocess.php',
		async 	: 	true,
		cache 	: 	false,
		dataType: "json",
		data:{
			dataval : str
		},
		success:function(data1)
		{
			$('.wkslotprice_loader').hide();
			if(data1 == 1) {
				alert(success);
			}
			else if(data1 == 2) {
				alert(no_reduction);
				return false;
			}
			else if(data1 == 3) {
				alert(invalid_range);
				return false;	
			}
			else if(data1 == 4) {
				alert(reduction_range);
				return false;
			}
			else if(data1 == 5) {
				alert(wrong_id);
				return false;
			}
			else if(data1 == 6) {
				alert(invalid_price);
				return false;
			}
			else if(data1 == 7) {
				alert(invalid_qty);
				return false;
			}
			else if(data1 == 8) {
				alert(select_dis_type);
				return false;
			}
			else if(data1 == 9) {
				alert(date_invalid);
				return false;
			}
			else if(data1 == 10) {
				alert(already_exist);
				return false;
			}
			location.reload(true);
		}
	});
	return false;
});
$(window).load(function(){
	$('.selector').attr('style','');
});
function selectcustomer(id_customer, name)
{
	$('#id_customer').val(id_customer);
	$('#wkslotcustomer').val(name);
	$('#customers').empty();
}

function changeCurrencySpecificPrice(index)
{
	var id_currency = $('#spm_currency_'+index).val();
	if (id_currency > 0) {
		$('#sp_reduction_type option[value="amount"]').text($('#spm_currency_'+index+' option[value='+id_currency+']').text());
	}
	else if (typeof currencyName !== 'undefined') {
		$('#sp_reduction_type option[value="amount"]').text(currencyName);
	}
}

function isInt(value)
{
	return !isNaN(value) && parseInt(Number(value)) == value;
}

function delete_slot(slot_id)
{
	var mp_product_id = $('#mp_product_id').val();
	$.ajax({
		type:'post',
		url:$('#delete_link').val(),
		async: true,
		data:{
			slot_id:slot_id,
			mp_product_id:mp_product_id
		},
		cache: false,
		success: function(data1)
		{
			if(data1)
				$('#slotcontent'+slot_id).remove();
			else
				alert(error);
		}
	});
}
$(document).on('keyup', '#wkslotcustomer', function(e){
//$('#wkslotcustomer').on('keyup', function(){
		var field =  $('#wkslotcustomer').val();
		if(field != '' && field.length > 2) {
			$.ajax({
				'type': 'POST',
				'url'   : modules_dir+'mpslotpricing/ajax/priceslotprocess.php',
				//'url': "{$link->getModuleLink('mpslotpricing', 'process')|addslashes}",
				'async': true,
				'dataType': 'json',
				'data': {
					'cust_search': '1',
					'keywords' : field,		
				},
				'success': function(result)
				{
					if(result.found) {
						var html = '<ul class="list-unstyled">';
						$.each(result.customers, function() {
							html += '<li>'+this.firstname+' '+this.lastname;
							html += ' - '+this.email;
							html += '<a onclick="selectcustomer('+this.id_customer+', \''+this.firstname+' '+this.lastname+'\'); return false" href="#" class="btn btn-default">'+Choose+'</a></li>';
						});
						html += '</ul>';
					}
					else
						html = '<div class="alert alert-warning">'+no_customers_found+'</div>';
					$('#customers').html(html);
				},
			});
		}
	});