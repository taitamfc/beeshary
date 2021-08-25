/**
* 2010-2017 Webkul.
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

$(document).ready(function() {
	$('#account_btn').on('click', function() {
		var firstname = $('#firstname').val();
		var lastname = $('#lastname').val();
		var email = $('#email').val();
		var passwd = $('#passwd').val();
		var ps_customer_id = $('#ps_customer_id').val();

		if (ps_customer_id != '')
		{
			$('.login_act_err').show().find('.mp_error').text(emailIdError);
		}
		else if (firstname == '' || lastname == ''|| email == ''|| passwd == '')
		{
			$('.login_act_err').show().find('.mp_error').text(allFieldMandatoryError);
		}
		else if (!validate_isName(firstname))
		{
			$('.login_act_err').show().find('.mp_error').text(firstNameError);
		}
		else if (!validate_isName(lastname))
		{
			$('.login_act_err').show().find('.mp_error').text(lastNameError);
		}
		else if (!validate_isEmail(email))
		{
			$('.login_act_err').show().find('.mp_error').text(invalidEmailIdError);
		}
		else if (passwd.length < 5)
		{
			$('.login_act_err').show().find('.mp_error').text(passwordLengthError);
		}
		else if (!validate_isPasswd(passwd))
		{
			$('.login_act_err').show().find('.mp_error').text(invalidPasswordError);
		}
		else
		{
			$('.form_wrapper').toggle();
			$('.login_act_err').hide().find('.mp_error').text('');
		}
	});

	$('.wk_login_field').on('click', function() {
		$(this).css("background-image", "none");
	});

	$('#mp_register_form').on('submit', function() {
		var seller_default_lang = $('.seller_default_shop').data('lang-name');
		var mp_shop_name = $('.seller_default_shop').val().trim();
		var mp_unique_shop_name = $('#mp_shop_name_unique').val().trim();
		var mp_seller_phone = $('#mp_seller_phone').val().trim();

		if (mp_unique_shop_name == '' || mp_seller_phone == '')
		{
			$('.login_shop_err').show().find('.mp_error').text(allFieldMandatoryError);
			return false;
		}
		else if(!validate_shopname(mp_unique_shop_name))
		{
			$('.login_shop_err').show().find('.mp_error').text(invalidUniqueShopNameError);
			$('#mp_shop_name_unique').focus();
			return false;
		}
		else if(checkUniqueShopName(mp_unique_shop_name, 1))
		{
			//alert(shop_name_exist_msg);
			$('#mp_shop_name_unique').focus();
			return false;
		}
		else if(mp_shop_name == '')
		{
			if ($('#multi_lang').val() == '1') {
				$('.login_shop_err').show().find('.mp_error').text(shopNameRequiredLang+seller_default_lang);
			}
			else {
				$('.login_shop_err').show().find('.mp_error').text(shopNameRequired);
			}

			$('.seller_default_shop').focus();
			return false;
		}
		else if(!validate_shopname(mp_shop_name))
		{
			$('.login_shop_err').show().find('.mp_error').text(invalidShopNameError);
			$('.seller_default_shop').focus();
			return false;
		} else if (MP_SELLER_COUNTRY_NEED) {
			if ($('#seller_city').val().trim() == '') {
				$('.login_shop_err').show().find('.mp_error').text(cityNameRequired);
				$('#seller_city').focus();
				return false;
			} else if (!validate_isName($('#seller_city').val().trim())) {
				$('.login_shop_err').show().find('.mp_error').text(invalidCityNameError);
				$('#seller_city').focus();
				return false;
			}
		}
		else if (!validate_isPhoneNumber(mp_seller_phone))
		{
			$('.login_shop_err').show().find('.mp_error').text(phoneNumberError);
			$('#mp_seller_phone').focus();
			return false;
		}
		else
		{
			return true;
		}
	});

	$('#mp_login_form').on('submit', function() {
		var email = $('#login_email').val();
		var passwd = $('#login_passwd').val();

		if (email == '')
		{
			$('#login_email').css({"background-image": "url("+modImgDir+"icon-close.png)", "background-repeat": "no-repeat", "background-position": "98%  center"});
			return false;
		}
		else if (passwd == '')
		{
			$('#login_passwd').css({"background-image": "url("+modImgDir+"icon-close.png)", "background-repeat": "no-repeat", "background-position": "98%  center"});
			return false;
		}
		else if (!validate_isEmail(email))
		{
			$('#login_email').css({"background-image": "url("+modImgDir+"icon-close.png)", "background-repeat": "no-repeat", "background-position": "98%  center"});
			return false;
		}
		else if (!validate_isPasswd(passwd))
		{
			$('#login_passwd').css({"background-image": "url("+modImgDir+"icon-close.png)", "background-repeat": "no-repeat", "background-position": "98%  center"});
			return false;
		}
		else
		{
			return true;
		}
	});

	$('#email').on('focus', function(){
		$('.login_act_err').hide();
		$('.check_email').hide();
		$('#ps_customer_id').val('');
	});

	$('#email').on('blur', function() {
		var email = $(this).val();
		if (validate_isEmail(email) && email) {
			$(this).css({"background-image": "url("+modImgDir+"loader.gif)", "background-repeat": "no-repeat", "background-position": "98%  center"});
			checkEmailExists(email);
		}
	});

	$('#toggle_form').on('click', function() {
		if ($('#idSeller').val()) {
			$('.login_act_err').show().find('.mp_error').text(emailAlreadyExist);
		} else {
			$('.form_wrapper').toggle();
		}
	});

	$('#back_account').on('click', function(){
		$('.form_wrapper').toggle();
	});

	var wk_slerror = $('#wk_slerror').val();
	if (wk_slerror) {
		$('.error_block').slideToggle();
		setTimeout(function()
		{
			$('.error_block').slideToggle(500);
		}, 5000);
	}

	$('#seller_default_lang').on("change", function(e){
		e.preventDefault();
		if ($('#multi_lang').val() == '1') {
			var select_lang_iso = $(this).find("option:selected").data('lang-iso');
			var select_lang_id = $(this).val();

			showLangField(select_lang_iso, select_lang_id);

			$('.shop_name_all').removeClass('seller_default_shop');
			$('#mp_shop_name_'+select_lang_id).addClass('seller_default_shop');
		}
	});

	$("#mp_shop_name_unique").on('blur', function() {
		var shop_name_unique = $(this).val().trim();
		if (checkUniqueShopName(shop_name_unique)) {
	        $(this).focus();
	        return false;
	    }
	});

	$("#seller_country").on('change', function() {
		var id_country = $(this).val();
		getState(id_country);
	});

	$('#submitProfile').on('click', function() {
		let seller_lang = $('#seller_lang').val();
		let firstname = $('#pp_firstname').val().trim();
		let lastname = $('#pp_lastname').val().trim();
		let profession = $('#profession').val().trim();
		$('.pp_display_errors_profile').hide();

		if (!validate_isName(firstname) || firstname == '') {
			$('.pp_display_errors_profile').show().text(firstNameError);
			$('html, body').animate({scrollTop: 0}, 0);
			return false;
		} else if (!validate_isName(lastname) || lastname == '') {
			$('.pp_display_errors_profile').show().text(lastNameError);
			$('html, body').animate({scrollTop: 0}, 0);
			return false;
		} else if (profession == '') {
			$('.pp_display_errors_profile').show().text(sellerProfessionRequired);
			$('html, body').animate({scrollTop: 0}, 0);
			return false;
		} else if (seller_lang == null) {
			$('.pp_display_errors_profile').show().text(sellerLangRequired);
			$('html, body').animate({scrollTop: 0}, 0);
			return false;
		} else {
			$('.pp_display_errors_profile').hide();
			$('html, body').animate({scrollTop: 0}, 0);
			$('#sellerProfileForm').hide();
			$('#sellerStoreForm').show();
			$('.step_1').addClass('active');
			return true;
		}
	});

	$('#submitStore').on('click', function() {
		let store_name = $('#store_name').val().trim();
		let store_name_unique = $('#store_name_unique').val().trim();
		// let store_description = $('#store_description').val().trim();
		let store_address = $('#store_address').val().trim();
		let post_code = $('#post_code').val().trim();
		let city = $('#city').val().trim();
		let tel_pro = $('#tel_pro').val().trim();
		let email_pro = $('#email_pro').val().trim();
		let passwd = $('#passwd').val().trim();

		$('.pp_display_errors_store').hide();


		if (!validate_shopname(store_name) || store_name == '') {
			$('.pp_display_errors_store').show().text(invalidShopNameError);
			$('html, body').animate({scrollTop: 0}, 1400);
			return false;
		} else if (store_name_unique == '') {
			$('.pp_display_errors_store').show().text(invalidUniqueShopNameError);
			$('html, body').animate({scrollTop: 0}, 0);
			return false;
	    } else if (checkUniqueShopName(store_name_unique)) {
			$('html, body').animate({scrollTop: 0}, 0);
			return false;
	    } else if (!isSiretValid($('#siret').val().trim())) {
			$('.pp_display_errors_store').show().text('Veuillez saisir un numero SIRET valide (14 chiffres)');
			$('html, body').animate({scrollTop: 0}, 0);
			return false;
	    } else if (!validate_isEmail(email_pro) || email_pro == '') {
			$('.pp_display_errors_store').show().text(emailIdError);
			$('html, body').animate({scrollTop: 0}, 0);
			return false;
		} else if (checkEmailExists(email_pro)) {
			$('.pp_display_errors_store').show().text(emailAlreadyExist);
			$('html, body').animate({scrollTop: 0}, 0);
			return false;
		} else if (store_address == '') {
			$('.pp_display_errors_store').show().text(sellerAddressRequired);
			$('html, body').animate({scrollTop: 0}, 0);
			return false;
		} else if (post_code == '' || !validate_isPostCode(post_code)) {
			$('.pp_display_errors_store').show().text(sellerStorePostCodeRequired);
			$('html, body').animate({scrollTop: 0}, 0);
			return false;
		} else if (city == '' || !validate_isCityName(city)) {
			$('.pp_display_errors_store').show().text(sellerStoreCityRequired);
			$('html, body').animate({scrollTop: 0}, 0);
			return false;
		} else if (tel_pro == '' || !validate_isPhoneNumber(tel_pro)) {
			$('.pp_display_errors_store').show().text(phoneNumberError);
			$('html, body').animate({scrollTop: 0}, 0);
			return false;
		} else if (passwd == '' || !validate_isPasswd(passwd)) {
			$('.pp_display_errors_store').show().text('Mot de passe requis de plus de 5 caractères.');
			$('html, body').animate({scrollTop: 0}, 0);
			return false;
		} else {
			$('.pp_display_errors_store').hide();
			/*
			$('html, body').animate({scrollTop: 0}, 0);
			$('#sellerStoreForm').hide();
			$('#sellerImagesForm').show();
			$('.step_2').addClass('active');
			*/
			$('html, body').animate({scrollTop: 650}, 800);
			$('#sellerProfileForm').hide();
			$('#sellerStoreForm').hide();
			$('#sellerTermsForm').show();
			$('.step_2').addClass('active');			
			return true;
		}
	});

	$('#submitImages').on('click', function() {
		$('html, body').animate({scrollTop: 650}, 800);
		$('#sellerImagesForm').hide();
		$('#sellerDeliveryMethodForm').show();
		$('.step_3').addClass('active');
	});

	$('#submitDeliveryMethod').on('click', function() {
		$('html, body').animate({scrollTop: 650}, 800);
		
    		$('#sellerDeliveryMethodForm').hide();
			$('#sellerTermsForm').show();
			$('.step_3').addClass('active');
		
		
	});
	$('#submitTermsCondition').on('click', function () {
		if (!$('#adhere_charter').prop('checked') && !$('#adhere_cgv').prop('checked')) {
    		alert(chaterAdherence);
			return false;
		}else{
			$('html, body').animate({ scrollTop: 650 }, 800);
			$('#sellerSubscriptionForm').show();
			$('#sellerTermsForm').hide();
			$('.step_3').addClass('active');
		}		
	});
	
	$('#sellerCreationForm').on('submit', function() {
		
	});

	
	$(document).on('click', '.remove_preview_img', function(e) {
		e.preventDefault();
		$(this).parent().html("").css('border', 'none');
	});

	$('.prv_profile').on('click', function(e) {
		e.preventDefault();
		$('#sellerStoreForm').hide();
		$('#sellerProfileForm').show();
		$('.step_1').removeClass('active');
	});

	$('.prv_store').on('click', function(e) {
		e.preventDefault();
		$('#sellerImagesForm').hide();
		$('#sellerStoreForm').show();
		$('.step_2').removeClass('active');
	});

	$('.prv_images').on('click', function(e) {
		e.preventDefault();
		$('#sellerDeliveryMethodForm').hide();
		//$('#sellerImagesForm').show();
		//$('.step_3').removeClass('active');
		$('#sellerStoreForm').show();
		$('.step_2').removeClass('active');		
	});

	$('.prv_delivery').on('click', function(e) {
		e.preventDefault();
		$('#sellerTermsForm').hide();
		$('#sellerSubscriptionForm').show();
		$('.step_4').removeClass('active');
	});
	
	$('.prv_subscription').on('click', function(e) {
		e.preventDefault();
		$('#sellerSubscriptionForm').hide();
		$('#sellerDeliveryMethodForm').show();
		$('.step_4').removeClass('active');
	});

    $('#tags').tagify({delimiters: [13,44], addTagPrompt: 'Ajouter une étiquette',cssClass: 'tagify-container seller-creation-form'});
    $('#ferret').imgAreaSelect({ aspectRatio: '1:1', onSelectChange: preview });

	var bDays = ['Lundi', 'Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
    $('#shipping_days').on('change', function() {
    	let selDays = $(this).val();
    	dynamicTextDeliveryMethodChanging('.shipping_days_fill', (selDays == null ? "" : selDays.toString().replace(/,/g, ", ")), 'xxxx');

    	if (selDays == null) {
    		$('.shipping_wkd_fill').text('xx');
    	} else if (selDays.length == 1) {
    		$('.shipping_wkd_fill').text(selDays.join(' '));
    	} else {
    		let foundDaysIndexes = [];
    		for (let i=0, bDaysLen = bDays.length; i < bDaysLen; i++) {
			 	if ($.inArray(bDays[i], selDays) > -1) {
				 	foundDaysIndexes.push(i);
			 	}
			}
    		$('.shipping_wkd_fill').text(bDays[Math.min.apply(null, foundDaysIndexes)]);
    	}
    });

    $('#delivery_method').on('change', function() {
    	let dm_val = $(this).val().toString().replace(/,/g, " et ");
    	dynamicTextDeliveryMethodChanging('.shipping_delivery_method_fill', dm_val, 'xxxx');
    });
    $('#option_free_delivery').on('change', function() {
    	if (parseInt($(this).val()) == 0) {
			$('.frais_livraison').hide();
    	} else {
			$('.frais_livraison').show();
	    	dynamicTextDeliveryMethodChanging('.shipping_cost_fill', $(this).val(), 'x');
    	}
    });

    $("#seller_lang, #shipping_days, #delivery_method, .has-chosen").chosen({
        no_results_text: "Oops, nothing found!",
        search_contains: true
    });

    $('#post_code').on('keyup', function() {
    	let cp = $(this).val();
    	if (cp == "") {
    		$('#city').val("");
    		return;
    	}

    	getCityByPostCode(cp);
    });

    $(".artisan_charter").fancybox({
		maxWidth	: 900,
		maxHeight	: 900,
		fitToView	: false,
		width		: '80%',
		height		: '90%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	});

	$('#store_address, #city, #post_code').on('keyup keydown', function() {
        let mp_address = '', act_addr = $('#store_address').val(), atc_city = $('#city').val(), act_pc = $('#post_code').val();

        if (act_addr != "") {
            mp_address += act_addr + ', ';
        }

        if (atc_city != "") {
            mp_address += (act_pc != "" ? act_pc+' ' : ' ') + atc_city +", France";
        }

        if (mp_address != "") {
            let _geocoder = new google.maps.Geocoder();
            _geocoder.geocode({
                'address': mp_address
            }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var place = results[0];

                    $("#latitude").val(place.geometry.location.lat());
                    $("#longitude").val(place.geometry.location.lng());
                }
            });
        }
    });
});

function dynamicTextDeliveryMethodChanging(sel_filled, val, def_val) {
	if (val == "" || (typeof val === "undefined")) {
		$(sel_filled).text(def_val);
	} else {
		$(sel_filled).text(val);
	}
}

function preview(img, selection) {
    var scaleX = 100 / (selection.width || 1);
    var scaleY = 100 / (selection.height || 1);
    console.log(img, selection)

    // $('#ferret + div > img').css({
    //     width: Math.round(scaleX * 400) + 'px',
    //     height: Math.round(scaleY * 300) + 'px',
    //     marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
    //     marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
    // });
}

function getState(id_country)
{
	$.ajax({
		method:"POST",
		url:checkCustomerAjaxUrl,
		data: {
			id_country: id_country,
			case: 'getSellerState',
		},
		success: function(result) {
			if (result) {
				$("#sellerStateCont").show();
				$("#seller_state").empty();
				$.each(jQuery.parseJSON(result), function(index, state) {
					$("#seller_state").append('<option value='+state.id_state+'>'+state.name+'</option>');
				});

				// Code if "Move JavaScript to the end" option is truned ON
				$("#uniform-seller_state > span").remove();
				$("#uniform-seller_state").css('width','100%');

				$("#state_avl").val(1);
			} else {
				$("#sellerStateCont").hide();
				$("#seller_state").empty();
				$("#state_avl").val(0);
			}
		}
	});
}

function getCityByPostCode(post_code)
{
	$.ajax({
		method:"POST",
		url:checkCustomerAjaxUrl,
		data: {
			post_code: post_code,
			case: 'getCityByPostCode',
		},
		dataType: 'json',
		success: function(resp) {
			if (resp && resp.city != false) {
				$("#city").val(resp.city);
			} else {
				$("#city").val("");
			}
		}
	});
}

function showLangField(lang_iso_code, id_lang)
{
	$('#shop_name_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
	$('#address_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');

	$('.shop_name_all').hide();
	$('#mp_shop_name_'+id_lang).show();

	$('.address_all').hide();
	$('#mp_shop_address_'+id_lang).show();
}

function validate_shopname(name)
{
	if (!/^[^<>;=#{}]*$/i.test(name))
		return false;
	else
		return true;
}

var id_seller;
var shop_name_exist = false;
//function checkUniqueShopName(shop_name, isformSubmit = 0)
function checkUniqueShopName(shop_name, isformSubmit)
{
	if (isformSubmit == undefined) {
		isformSubmit = 0
	}
	if (shop_name.trim()) {
		if (!isformSubmit) {
			$('#mp_shop_name_unique').css({"background-image": "url("+modImgDir+"loader.gif)", "background-repeat": "no-repeat", "background-position": "98%  center"});
		}
		$.ajax({
	        url: checkCustomerAjaxUrl,
	        type: "POST",
	        data: {
	            shop_name: shop_name,
	            case: 'checkUniqueShopName',
	        },
	        async: false,
	        success: function(result) {
	        	if (!isformSubmit) {
					$('#mp_shop_name_unique').css("background-image", "none");
				}
	   			if (result == 1) {
					$('.login_shop_err').show().find('.mp_error').text(shopNameAlreadyExist);
					$('.pp_display_errors_store').show().text(shopNameAlreadyExist);
					shop_name_exist = true;
				}
				else if (result == 2) {
					$('.login_shop_err').show().find('.mp_error').text(shopNameError);
					$('.pp_display_errors_store').show().text(shopNameError);
					shop_name_exist = true;
				}
				else {
					$('.login_shop_err').hide();
					shop_name_exist = false;
				}
	        },
			error: function(error) {
				console.log(error);
			}
	    });
	}

	return shop_name_exist;
}

function checkEmailExists(email) {
	var found = false;
	$.ajax({
		url: checkCustomerAjaxUrl,
		type: 'POST',
		dataType: 'JSON',
		data: {
			user_email: email,
			id_seller: id_seller !== 'undefined' ? id_seller : false,
			case: 'checkEmailRegister',
		},
		async: false,
		success: function(result) {
			$('#email').css("background-image", "none");
			if (result) {
				$('#ps_customer_id').val(parseInt(result.idCustomer));
				$('.check_email').show();

				if (!parseInt(result.idSeller)) {
					$('.login_act_err').hide();
				} else {
					$('#idSeller').val(parseInt(result.idSeller));
				}
				found = true;
			}
		}
	});
	return found;
}

function displayLogoImg(elem) {
    var file = document.getElementById(elem).files[0], _id = 'img_'+elem;
    let reader  = new FileReader();
    $('#'+ elem).parent().find('.image_preview').html("");
    reader.onload = function(e)  {
        let image = document.createElement("img");
        image.src = e.target.result;
        image.setAttribute('id', _id);
        image.setAttribute('class', 'img-circle');

        $('#'+ elem).parent().find('.image_preview').append(image);
        $('#'+ elem).parent().find('.image_preview').append('<a class="remove_preview_img" href="#"><i class="fa fa-trash"></i></a>');
        $('#'+ elem).parent().find('.image_preview').append('<div class="clearfix"></div>');
     }
     reader.readAsDataURL(file);
}

function displayBannerImg(elem) {
    var file = document.getElementById(elem).files[0], _id = 'img_'+ elem;
    let reader  = new FileReader();
    $('#'+ elem).parent().find('.image_banner_preview').html("");
    reader.onload = function(e)  {
        let image = document.createElement("img");
        image.src = e.target.result;
        image.setAttribute('id', _id);
        image.setAttribute('class', 'img-rectangle');

        $('#'+ elem).parent().find('.image_banner_preview').append(image);
        $('#'+ elem).parent().find('.image_banner_preview').append('<a class="remove_preview_img" href="#"><i class="fa fa-trash"></i></a>');
        $('#'+ elem).parent().find('.image_banner_preview').append('<div class="clearfix"></div>');
        $('#'+ elem).parent().find('.image_banner_preview').css('border', '1px #bbb solid');
     }
     reader.readAsDataURL(file);
}

function isSiretValid(siret) {
    var isValid;
    if ( (siret.length != 14) || (isNaN(siret)) ) {
      isValid = false;
    } else {
       // Donc le SIRET est un numérique à 14 chiffres
       // Les 9 premiers chiffres sont ceux du SIREN (ou RCS), les 4 suivants
       // correspondent au numéro d'établissement
       // et enfin le dernier chiffre est une clef de LUHN.
      var somme = 0;
      var tmp;
      for (var cpt = 0; cpt<siret.length; cpt++) {
        if ((cpt % 2) == 0) { // Les positions impaires : 1er, 3è, 5è, etc...
          tmp = siret.charAt(cpt) * 2; // On le multiplie par 2
          if (tmp > 9)
            tmp -= 9;	// Si le résultat est supérieur à 9, on lui soustrait 9
        }
       else
         tmp = siret.charAt(cpt);
         somme += parseInt(tmp);
      }
      if ((somme % 10) == 0)
        isValid = true; // Si la somme est un multiple de 10 alors le SIRET est valide
      else
        isValid = false;
    }
    return isValid;
}
