/**
 * 2007-2017 PrestaShop
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

$(document).ready(function() {
	
		$('#payment-confirmation button').click(function (event) {
		   if ($('input[name=payment-option]:checked').data('module-name')=='savedstripepro') {
            $('#stripe-payment-form-cc').submit();
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
    });
	
	$('#stripe-payment-form-cc').submit(function (event) {
		var $form = $(this);
		var stripeToken = $('input[name=stripeToken]').val();
		if (stripeToken=='') {
            $('.stripe-payment-errors').show();
            $form.find('.stripe-payment-errors').text("Empty token, please use another card or contact us.").fadeIn(1000);
            return false;
        }
		
		 /* Disable the submit button to prevent repeated clicks */
        $('#payment-confirmation button[type=submit]').attr('disabled', 'disabled');
		$('#stripe-payment-form-cc').hide();
        $('.stripe-payment-errors').hide();
        $('#stripe-ajax-loader-cc').show();
  
		 $.ajax({
				type: 'POST',
				dataType: 'json',
				url: baseDir + 'modules/stripepro/payment.php',
				data: {
					stripeToken: stripeToken,
					sourceType: 'card',
					ajax: true,
				},
				success: function(data) {
					if (data.code == '1') {
						// Charge ok : redirect the customer to order confirmation page
						location.replace(data.url);
					} else {
						//  Charge ko
						$('#stripe-ajax-loader-cc').hide();
						$('#stripe-payment-form-cc').show();
						$('.stripe-payment-errors').show();
						$('.stripe-payment-errors').text(data.msg).fadeIn(1000);
						$('#payment-confirmation button[type=submit]').removeAttr('disabled');
					}
				},
				error: function(err) {
					// AJAX ko
					$('#stripe-ajax-loader-cc').hide();
					$('#stripe-payment-form-cc').show();
					$('.stripe-payment-errors').show();
					$('.stripe-payment-errors').text('An error occured during the request. Please contact us').fadeIn(1000);
					$('#payment-confirmation button[type=submit]').removeAttr('disabled');
				}
			});
		 return false;
	});
});