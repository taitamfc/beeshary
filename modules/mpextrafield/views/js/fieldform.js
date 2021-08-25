/**
* 2010-2017 Webkul
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

$(document).on('change', '#inputtype', function(e){		// on change of input type
	var inputtype_value = $(this).val();
	actionOnField(inputtype_value);
});

$(document).ready(function(e){							// on document ready
	var inputtype_value = $('#inputtype').val();
	actionOnField(inputtype_value);
});

// perform hide and show action based on input type selection
function actionOnField(val)
{
	if (val == 1)			// input type Text
	{
		$('#charlimit').show();
		$('#validationtype').show();
		$('#char250').show();
		$('#default_value').show();
		$('#placeholder').show();
		$('#req_field').show();
		$('#status_info').show();
		$('#char1000').hide();
		$('#multiple_option').hide();
		$('#radio_info').hide();
		$('#dropdown_label_info').hide();
		$('#check_info').hide();
		$('#update_label_info').hide();
		
	}
	else if (val == 2)			// input type Textarea
	{
		$('#charlimit').show();
		$('#char1000').show();
		$('#default_value').show();
		$('#placeholder').show();
		$('#req_field').show();
		$('#status_info').show();
		$('#validationtype').hide();
		$('#char250').hide();
		$('#multiple_option').hide();
		$('#radio_info').hide();
		$('#dropdown_label_info').hide();
		$('#check_info').hide();
		$('#update_label_info').hide();
	}
	else if (val == 3)			// input type Dropdown
	{
		$('#charlimit').hide();
		$('#validationtype').hide();
		$('#radio_info').hide();		
		$('#check_info').hide();
		$('#file_info').hide();
		$('#default_value').hide();
		$('#placeholder').hide();
		$('#multiple_option').show();
		$('#status_info').show();
		$('#dropdown_label_info').show();
		$('#update_label_info').show();
		$('#req_field').show();
	}
	else if (val == 4)			// input type Checkbox
	{
		$('#charlimit').hide();
		$('#validationtype').hide();
		$('#multiple_option').hide();
		$('#radio_info').hide();
		$('#dropdown_label_info').hide();
		$('#file_info').hide();
		$('#default_value').hide();
		$('#placeholder').hide();
		$('#check_info').show();
		$('#status_info').show();
		$('#req_field').show();
		$('#update_label_info').hide();
	}
	else if (val == 5)			// input type Filetype
	{
		$('#charlimit').hide();
		$('#validationtype').hide();
		$('#multiple_option').hide();
		$('#radio_info').hide();
		$('#dropdown_label_info').hide();
		$('#check_info').hide();
		$('#default_value').hide();
		$('#placeholder').hide();
		$('#file_info').show();
		$('#req_field').show();
		$('#status_info').show();
		$('#update_label_info').hide();
	}
	else if (val == 6)			// input type Radio button
	{
		$('#charlimit').hide();
		$('#validationtype').hide();
		$('#multiple_option').hide();
		$('#dropdown_label_info').hide();
		$('#check_info').hide();
		$('#file_info').hide();
		$('#default_value').hide();
		$('#placeholder').hide();
		$('#radio_info').show();
		$('#req_field').show();
		$('#status_info').show();
		$('#update_label_info').hide();
	}
}

$(document).on('click', '.delete_me', function(e){
	e.preventDefault();
	var id = this.id;
	if (typeof id !== typeof undefined) {
		if (window.confirm('Are you want to delete this attachment'))
		{
			$('.mp_extra_loading').show();
			$.ajax({
				url : ajax_urlpath,
				cache :  false,
				type : 'POST',
				data : 
				{
					admin_id_delete : id
				},
				success : function(data)
				{
					if (data)
					{
						$('.mp_extra_loading').hide();
						location.reload(true);
					}
				}
			});
		}
	}
});

function showExtraLangField(lang_iso_code, id_lang)
{	
	$('#label_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
	$('#default_value_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
	$('.dropdown_value_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
	$('.checkbox_value_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
	$('.radio1_value_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
	$('.radio2_value_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
	
	$('.label_name_all').hide();
	$('#label_name_'+id_lang).show();
	$('.default_value_all').hide();
	$('#default_value_'+id_lang).show();
	$('.dropdown_value_all').hide();
	$('.checkbox_value_all').hide();
	$('.radio1_value_all').hide();
	$('.radio2_value_all').hide();

	$('.dropdown_value_'+id_lang).show();
	$('.checkbox_value_'+id_lang).show();
	$('.radio1_value_'+id_lang).show();
	$('.radio2_value_'+id_lang).show();
}