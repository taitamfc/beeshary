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
    var stripe_allow_applepay = true;
    // Get Stripe public key
    if (typeof StripePubKey != 'undefined' && StripePubKey != '') {
        Stripe.setPublishableKey(StripePubKey);
    }

    if (typeof mode != 'undefined' && mode == 0) {
        $('.stripe-card-number').val('4242 4242 4242 4242');
        var card_logo = document.createElement('img');
        card_logo.src = module_dir + 'views/img/cc-visa.png';
        card_logo.id = "img-visa";
        card_logo.className = "img-card";
        $(card_logo).insertAfter('.stripe-card-number');
        $('.stripe-card-cvc').val(123);
        $('.stripe-card-expiry').val('12/25');
    }

    /* Catch callback errors */
    if ($('.stripe-payment-errors').text()) {
        $('.stripe-payment-errors').fadeIn(1000);
    }

    $('#stripe-payment-form input').keypress(function () {
        $('.stripe-payment-errors').fadeOut(500);
    });

    //Put our input DOM element into a jQuery Object
    var jqDate = document.getElementById('card_expiry');

    //Bind keyup/keydown to the input
    $(jqDate).bind('keyup', 'keydown', function (e) {
        var value_exp = $(jqDate).val();
        var v = value_exp.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        var matches = v.match(/\d{2,4}/g);

        //To accomdate for backspacing, we detect which key was pressed - if backspace, do nothing:
        if (e.which !== 8) {
            var numChars = value_exp.length;
            if (numChars === 2) {
                var thisVal = value_exp;
                thisVal += '/';
                $(jqDate).val(thisVal);
            }
            if (numChars === 5)
                return false;
        }
    });

    if (document.getElementById('card_number') != null) {
        document.getElementById('card_number').oninput = function () {
            this.value = cc_format(this.value);

            cardNmb = Stripe.card.validateCardNumber($('.stripe-card-number').val());

            var cardType = Stripe.card.cardType(this.value);
            if (cardType != "Unknown") {
                if (cardType == "American Express")
                    cardType = "amex";
                if (cardType == "Diners Club")
                    cardType = "diners";
                if ($('.img-card').length > 0) {
                    if ($('#img-' + cardType).length > 0) {
                        setTimeout(function () {
                            card_input = document.getElementById('card_number');
                            var strLength = card_input.value.length;
                            card_input.focus();
                            card_input.setSelectionRange(strLength, strLength);
                        }, 0);
                        return false;
                    } else {
                        $('.img-card').remove();
                    }
                }

                var card_logo = document.createElement('img');
                card_logo.src = module_dir + 'views/img/cc-' + cardType.toLowerCase() + '.png';
                card_logo.id = "img-" + cardType;
                card_logo.className = "img-card";
                $(card_logo).insertAfter('.stripe-card-number');
            } else {
                if ($('.img-card').length > 0) {
                    $('.img-card').remove();
                }

            }
        }
    }

    $('#stripe-payment-form input').keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    $('#payment-confirmation button').click(function (event) {
        if ($('input[name=payment-option]:checked').data('module-name') == 'stripepro') {
            $('#stripe-payment-form').submit();
            event.preventDefault();
            event.stopPropagation();
            return false;
        } else if ($('input[name=payment-option]:checked').data('module-name') == 'stripeApplePay') {

            beginApplePay();
            return false;
        }
    });

    if (stripe_allow_applepay == true) {

        Stripe.applePay.checkAvailability(function (available) {
            if (available) {
                $('#apple-pay-alert').hide();
                $('#apple-pay-success').show();
            } else {
                $('#apple-pay-alert').show();
                $('#apple-pay-success').hide();
            }
        });
    }

    function beginApplePay() {
        var paymentRequest = {
            countryCode: country_iso_code,
            currencyCode: currency,
            total: {
                label: popup_title,
                amount: apple_pay_cart_total
            }
        };
        var session = Stripe.applePay.buildSession(paymentRequest, function (result, completion) {

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: baseDir + 'modules/stripepro/payment.php',
                data: {
                    stripeToken: result.token.id,
                    last4: result.card.last4,
                    sourceType: 'applepay',
                    ajax: true,
                },
                success: function (data) {
                    if (data.code == '1') {
                        completion(ApplePaySession.STATUS_SUCCESS);
                        location.replace(data.url);
                    } else {
                        completion(ApplePaySession.STATUS_FAILURE);
                        $('#stripe-ajax-loader-applepay').hide();
                        $('.stripe-payment-errors').show();
                        $('.stripe-payment-errors').text(data.msg).fadeIn(1000);
                        $('#payment-confirmation button[type=submit]').removeAttr('disabled');
                    }
                },
                error: function (err) {
                    completion(ApplePaySession.STATUS_FAILURE);
                    $('#stripe-ajax-loader-applepay').hide();
                    $('.stripe-payment-errors').show();
                    $('.stripe-payment-errors').text('An error occured during the request. Please contact us').fadeIn(1000);
                    $('#payment-confirmation button[type=submit]').removeAttr('disabled');
                }
            });

        }, function (error) {
            alert(error.message);
        });

        session.begin();
    }

    /* Catch callback errors */
    if ($('.stripe-payment-errors').text())
        $('.stripe-payment-errors').fadeIn(1000);

    $('#stripe-payment-form').submit(function (event) {
        var $form = $(this);
        if (!StripePubKey) {
            $('.stripe-payment-errors').show();
            $form.find('.stripe-payment-errors').text($('#stripe-no_api_key').text()).fadeIn(1000);
            return false;
        }
        if ($('.stripe-name').val() == '') {
            $('.stripe-payment-errors').show();
            $form.find('.stripe-payment-errors').text($('#stripe-incorrect_ownername').text()).fadeIn(1000);
            return false;
        }
        var cardNmb = Stripe.card.validateCardNumber($('.stripe-card-number').val());
        var cvcNmb = Stripe.card.validateCVC($('.stripe-card-cvc').val());
        if (cvcNmb == false) {
            $('.stripe-payment-errors').show();
            $form.find('.stripe-payment-errors').text($('#stripe-invalid_cvc').text()).fadeIn(1000);
            return false;
        }
        if (cardNmb == false) {
            $('.stripe-payment-errors').show();
            $form.find('.stripe-payment-errors').text($('#stripe-incorrect_number').text()).fadeIn(1000);
            return false;
        }
        /* Disable the submit button to prevent repeated clicks */
        $('#payment-confirmation button[type=submit]').attr('disabled', 'disabled');
        $('.stripe-payment-errors').hide();
        $('#stripe-payment-form').hide();
        $('#card-token-success').hide();
        $('#stripe-ajax-loader').show();

        exp_month = $('.stripe-card-expiry').val();
        exp_month_calc = exp_month.substring(0, 2);
        exp_year = $('.stripe-card-expiry').val();
        exp_year_calc = "20" + exp_year.substring(3);

        Stripe.card.createToken({
            name: $('.stripe-name').val(),
            number: $('.stripe-card-number').val(),
            cvc: $('.stripe-card-cvc').val(),
            exp_month: exp_month_calc,
            exp_year: exp_year_calc,
            address_line1: billing_address.line1,
            address_line2: billing_address.line2,
            address_city: billing_address.city,
            address_state: billing_address.state,
            address_zip: billing_address.zip_code,
            address_country: billing_address.country,
        }, function (status, response) {
            var $form = $('#stripe-payment-form');

            if (response.error) {
                // Show error on the form
                $('#stripe-ajax-loader').hide();
                $('#stripe-payment-form').show();
                $('#payment-confirmation button[type=submit]').removeAttr('disabled');

                var err_msg = $('#stripe-' + response.error.code).val();
                if (!err_msg || err_msg == "undefined" || err_msg == '')
                    err_msg = response.error.message;
                $form.find('.stripe-payment-errors').text(err_msg).fadeIn(1000);
            } else {

                $('#card-token-success').show();

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: baseDir + 'modules/stripepro/payment.php',
                    data: {
                        stripeToken: response.id,
                        sourceType: 'card',
                        last4: $('.stripe-card-number').val().slice(-4),
                        ajax: true,
                    },
                    success: function (data) {
                        if (data.code == '1') {
                            // Charge ok : redirect the customer to order confirmation page
                            location.replace(data.url);
                        } else {
                            //  Charge ko
                            $('#stripe-ajax-loader').hide();
                            $('#stripe-payment-form').show();
                            $('.stripe-payment-errors').show();
                            $('.stripe-payment-errors').text(data.msg).fadeIn(1000);
                            $('#payment-confirmation button[type=submit]').removeAttr('disabled');
                        }
                    },
                    error: function (err) {
                        // AJAX ko
                        $('#stripe-ajax-loader').hide();
                        $('#stripe-payment-form').show();
                        $('.stripe-payment-errors').show();
                        $('.stripe-payment-errors').text('An error occured during the request. Please contact us').fadeIn(1000);
                        $('#payment-confirmation button[type=submit]').removeAttr('disabled');
                    }
                });
            }
        });
        return false;
    });
});

function cc_format(value) {
    var v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '')
    var matches = v.match(/\d{4,16}/g);
    var match = matches && matches[0] || ''
    var parts = []
    for (i = 0, len = match.length; i < len; i += 4) {
        parts.push(match.substring(i, i + 4))
    }
    if (parts.length) {
        return parts.join(' ')
    } else {
        return value
    }
}

