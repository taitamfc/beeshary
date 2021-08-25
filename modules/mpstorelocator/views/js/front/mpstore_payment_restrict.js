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
    var idAddress = $('input[name="id_address_delivery"]:checked').val();
    if ($('#checkout-delivery-step').hasClass('-reachable -current')) {
        var idCarriers = $('input[name="delivery_option['+idAddress+']"]:checked').val();
        idCarriers = idCarriers.slice(0, -1);
        idCarriers = (idCarriers).split(',');
        // shouldProceed = 1;
        if ($.inArray(storeCarrierId, idCarriers) != -1) {
            $.ajax({
                url: ajaxurlStoreByKey,
                type: "POST",
                data: {
                    action: 'getStoreDetails',
                    ajax: true,
                },
                async: false,
                dataType: 'json',
                success: function(result) {
                    if (result.hasError) {
                        shouldProceed = 0;
                        $.growl.error({title: '', message: result.error});
                    }
                }
            });
        }
        return false;
    }
    $('#payment-confirmation > .ps-shown-by-js > button').click(function(e) {
        var idCarriers = $('input[name="delivery_option['+idAddress+']"]:checked').val();
        idCarriers = idCarriers.slice(0, -1);
        idCarriers = (idCarriers).split(',');
        shouldProceed = 1;
        if ($.inArray(storeCarrierId, idCarriers) != -1) {
            $.ajax({
                url: ajaxurlStoreByKey,
                type: "POST",
                data: {
                    action: 'checkStoreDetails',
                    ajax: true,
                },
                async: false,
                dataType: 'json',
                success: function(result) {
                    if (result.hasError) {
                        shouldProceed = 0;
                        $.each(result.errors, function(index, error) {
                            $.growl.error({title: '', message: error});
                        })
                    }
                }
            });
        }
        if (shouldProceed == '1') {
            return true;
        } else {
            return false;
        }
        return false;
    });

    var paymentReachable = 0;
    if ($('#checkout-payment-step').removeClass('-reachable')) {
        paymentReachable = 1;
    }
    $(document).on('change', 'input[name="delivery_option['+idAddress+']"]', function() {
        if ($(this).attr('id') == ('delivery_option_'+storeCarrierId)) {
            getShippingCarrierDetails();
        } else {
            $('.wk-store-shipping').slideUp();
        }
    });
    $('.carrier_data_tr').on('click', function() {
        if ($(this).attr('data-id-carrier') == storeCarrierId) {
            getShippingCarrierDetails($(this));
        } else {
            $('.wk-store-shipping').slideUp();
        }
    });
    getShippingCarrierDetails();
})

function getShippingCarrierDetails() {
    // if (element != undefined) {
        var idAddress = $('input[name="id_address_delivery"]:checked').val();
        var idCarriers = $('input[name="delivery_option['+idAddress+']"]:checked').val();
        // var idCarriers = element.attr('data-id-carrier');
        if (typeof idCarriers != 'undefined') {
            idCarriers = idCarriers.slice(0, -1);
            idCarriers = (idCarriers).split(',');
            if ($.inArray(storeCarrierId, idCarriers) != -1) {
                $('#checkout-payment-step').removeClass('-reachable').addClass('-unreachable');
                $.ajax({
                    url: ajaxurlStoreByKey,
                    type: "POST",
                    data: {
                        action: 'getStoreProductHtml',
                        ajax: true,
                        idCarriers: JSON.stringify(idCarriers)
                    },
                    async: false,
                    dataType: 'json',
                    success: function(result) {
                        if ($('.wk-store-shipping').length > 0) {
                            $('.wk-store-shipping').html(result.html).slideDown();
                        } else {
                            // element.closest('.carrierBlock').append('<div class="wk-store-shipping">'+result.html+'</div>').slideDown();
                            $('<div class="row wk-store-shipping">'+result.html+'</div>').insertAfter($('input[name="delivery_option['+idAddress+']"]:checked').closest('.delivery-option')).slideDown();
                        }
                    }
                });
            }
        }
    // }
}