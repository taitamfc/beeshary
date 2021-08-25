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
    // Remove Prestashop carriers from display
    $(".delivery-options").not(".byHook").remove();

    $("body").on("click", ".carrier_data_tr", function(e) {
        e.preventDefault();

        var id_carrier = $(this).attr("data-id-carrier");
        var id_seller = $(this).attr("data-id-seller");
        // custoization By Amit
        var isFree = $(this).attr("data-is_free")
        if (id_seller  && id_carrier) {
            if (isFree == 1) {
                $('.wk_free_ship_msg_'+id_seller).hide();
            } else {
                $('.wk_free_ship_msg_'+id_seller).show();
            }
        }
        // end customization
        $(".delivery_option_" + id_seller).attr('checked', false);
        $(".delivery_option_" + id_seller + "_" + id_carrier).prop("checked", true);

        /* ===== Get carriers according to product attr ===== */
        var selected_carriers = '';
        var delivery_option_list_k;
        var carrier_list = {};
        $.each($('input.delivery_option_radio:checked'), function(key, value) {
            if (typeof $(this).attr("data-id-carrier") != 'undefined') {
                if (selected_carriers == '') {
                    delivery_option_list_k = $(this).attr('data-delivery-option-list-k');
                }

                var id_product = parseInt($(this).attr("data-id-product"));
                var id_product_attr = parseInt($(this).attr("data-id-product-attr"));
                var id_carrier = parseInt($(this).attr("data-id-carrier"));

                if (typeof carrier_list[id_carrier] != 'undefined') {
                    if (typeof carrier_list[id_carrier][id_product] != 'undefined')
                        carrier_list[id_carrier][id_product].push(id_product_attr);
                    else {
                        carrier_list[id_carrier][id_product] = [];
                        carrier_list[id_carrier][id_product].push(id_product_attr);
                    }
                } else {
                    carrier_list[id_carrier] = {};
                    carrier_list[id_carrier][id_product] = [];
                    carrier_list[id_carrier][id_product].push(id_product_attr);

                    selected_carriers += id_carrier + ',';
                }
            }
        });

        carrier_list = JSON.stringify(carrier_list);
        var delivery_option_params = '&' + delivery_option_list_k + '=' + selected_carriers + '&';
        /* ===== Get carriers according to product attr ===== */

        /* ===== Update Delivery option in 'mp_carrierproduct_map' table  ===== */
        // @TODO :: Carrier extra details should also be displayed.
        var url = $('#js-delivery').data('url-update');
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: url,
            async: false,
            cache: false,
            dataType: "json",
            data: 'carrier_list=' + carrier_list + delivery_option_params,
            success: function(resp) {
                $('#js-checkout-summary').replaceWith(resp.preview);
            },
            error: function(resp) {
                console.log(resp);
            }
        });
        /* ===== Update Delivery option in 'mp_carrierproduct_map' table  ===== */

        /* ===== Update button html content  ===== */
        var block_img = $.trim($(this).find("td.delivery_option_logo").html());
        var block_carrier_dtl = $.trim($(this).find("td.delivery_option_dtl").html());
        var block_carrier_price = $.trim($(this).find("td.delivery_option_price div.delivery_option_price").html());

        var html = '';
        if (block_img) {
            html += '<div class="wk_carrier_com_div wk_carrier_img_div">';
            html += block_img;
            html += '</div>';
        }

        html += '<div class="wk_carrier_com_div ';
        if (block_img) {
            html += 'wk_carrier_dtl_div';
        } else {
            html += 'wk_carrier_dtl_noimg_div';
        }
        html += '">';
        html += block_carrier_dtl;
        html += '</div>';

        html += '<div class="wk_carrier_com_div wk_carrier_price_div">';
        html += block_carrier_price;
        html += '</div>';

        html += '<div class="wk_carrier_com_div wk_carrier_caret_div"><span class="caret"></span></div>';

        var button = $(this).parents("ul.dropdown-menu.wk_carrier_dp_ul").prev("button.wk_dropdown_btn");
        button.html(html);
        /* ===== Update button html content  ===== */
    });

    if (typeof carriers != 'undefined') {
        if ($('section#order-summary-content').length) {
            carriers = JSON.parse(carriers);
            var carrierHtml = '';
            var lastIteration = Object.keys(carriers).length;
            $.each(carriers, function (idCarrier, carrier) {
                carrierHtml += '<div class="row">';
                carrierHtml += '<div class="col-md-2">';
                carrierHtml += '<div class="logo-container">';
                if (carrier.logo) {
                    carrierHtml += '<img src="' + carrier.logo + '" alt="' + carrier.name + '">';
                } else {
                    carrierHtml += '&nbsp;';
                }
                carrierHtml += '</div>';
                carrierHtml += '</div>';
                carrierHtml += '<div class="col-md-4">';
                carrierHtml += '<span class="carrier-name">' + carrier.name + '</span>';
                carrierHtml += '</div>';
                carrierHtml += '<div class="col-md-4">';
                carrierHtml += '<span class="carrier-delay">' + carrier.delay + '</span>';
                carrierHtml += '</div>';
                carrierHtml += '<div class="col-md-2">';
                carrierHtml += '<span class="carrier-price">' + carrier.price + '</span>';
                carrierHtml += '</div>';
                carrierHtml += '</div>';

                lastIteration = lastIteration - 1;
                if (lastIteration) {
                    carrierHtml += '<hr style="margin:15px 0px;">';
                }
            });

            var carrierContainer = $('section#order-summary-content').find('.summary-selected-carrier');
            carrierContainer.empty();
            carrierContainer.html(carrierHtml);
        }
    }
    // customization By Amit webkul
    $('.wk_selected_carrier').each(function () {
        var idSeller = $(this).data('id_seller');
        var isFree = $(this).data('is_free');
        if (isFree == 1) {
            $('.wk_free_ship_msg_'+idSeller).hide();
        } else {
            $('.wk_free_ship_msg_'+idSeller).show();
        }
    })
});