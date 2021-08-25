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

$(document).ready(function () {

	var handler = StripeCheckout.configure({
		key: StripePubKey,
		image: logo_url,
		currency: currency,
		email: cu_email,
		locale: popup_locale,
		zipCode: stripe_allow_zip,
		allowRememberMe: true,
		token: function (token) {

			/* Disable the submit button to prevent repeated clicks */
			$('#payment-confirmation button[type=submit]').attr('disabled', 'disabled');
			$('.stripe-payment-errors').hide();
			$('#stripe-ajax-loader-checkout').show();

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: baseDir + 'modules/stripepro/payment.php',
				data: {
					stripeToken: token.id,
					sourceType: token.type,
					ajax: true,
				},
				success: function (data) {
					if (data.code == '1') {
						// Charge ok : redirect the customer to order confirmation page
						location.replace(data.url);
					} else {
						//  Charge ko
						$('#stripe-ajax-loader-checkout').hide();
						$('.stripe-payment-errors').show();
						$('.stripe-payment-errors').text(data.msg).fadeIn(1000);
						$('#payment-confirmation button[type=submit]').removeAttr('disabled');
					}
				},
				error: function (err) {
					// AJAX ko
					$('#stripe-ajax-loader-checkout').hide();
					$('.stripe-payment-errors').show();
					$('.stripe-payment-errors').text('An error occured during the request. Please contact us').fadeIn(1000);
					$('#payment-confirmation button[type=submit]').removeAttr('disabled');
				}
			});
			return false;
		}
	});

	$('#payment-confirmation button').click(function (event) {

		if ($('input[name=payment-option]:checked').data('module-name') == 'stripeCheckout') {

			handler.open({
				name: popup_title,
				description: popup_desc,
				amount: amount_ttl
			});
			event.preventDefault();
			event.stopPropagation();
			return false;
		}
	});

	// Close Checkout on page navigation
	window.addEventListener('popstate', function () {
		handler.close();
	});
});