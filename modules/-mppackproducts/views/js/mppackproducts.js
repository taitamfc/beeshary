/*
 * 2010-2016 Webkul
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
 *  @author Webkul IN <support@webkul.com>
 *  @copyright  2010-2016 Webkul IN
 */

$(document).ready(function() {
    var sug_ajax = '';

    if ($('.product_type:checked').val() == 2) {
        $('.pkprod_container').show();
        $('a[href="#wk-combination"]').hide();
    }

    $('.product_type').on('click', function() {
        if ($(this).val() == 2) {
            $('.pkprod_container').show();
            $('a[href="#wk-combination"]').hide();
        } else {
            $('.pkprod_container').hide();
            $('a[href="#wk-combination"]').show();
        }
    });

    $('body').on('click', function(event) {
        if ($('#sugpkprod_ul').css('display') == 'block') {
            $('#sugpkprod_ul').html('').hide();
        }
    });

    $('#selectproduct').on('keyup', function() {
        if ($(this).attr('data-value') != '') {
            $(this).attr('data-value', '');
        }

        if ($(this).attr('data-img') != '') {
            $(this).attr('data-img', '');
        }

        if (sug_ajax) {
            sug_ajax.abort();
        }

        var sugprod_ul = $('#sugpkprod_ul');
        sugprod_ul.html('').hide();

        var prod = $.trim($(this).val());
        if (prod) {
            var current_lang_id = $('#current_lang_id').val();
            // add prod from admin
            if (typeof id_seller == 'undefined') {
                var seller_cust_id = $("select[name='shop_customer']").val();
                ajax_data = {
                    prod_letter: prod,
                    current_lang_id: current_lang_id,
                    seller_cust_id: seller_cust_id
                };
            } else {
                ajax_data = {
                    prod_letter: prod,
                    seller_id: id_seller,
                    current_lang_id: current_lang_id
                };
            }
            var prev_id_prod = [];
            $('.mppk_id_prod').each(function(key, value) {
                prev_id_prod[key] = value.value;
            });

            if (typeof id_mp_pack_product != 'undefined')
                prev_id_prod[prev_id_prod.length] = id_mp_pack_product;

            if (prev_id_prod.length) {
                prev_id_prod = JSON.stringify(prev_id_prod);
                ajax_data.prev_id = prev_id_prod;
            }
            sug_ajax = $.ajax({
                url: mppack_module_dir + "ajax_products_list.php",
                type: 'POST',
                dataType: 'json',
                data: ajax_data,
                success: function(result) {
                    var excludeIds = getSelectedIds();
                    var returnIds = new Array();
                    sugprod_ul.show();
                    if (result) {
                        for (var i = result.length - 1; i >= 0; i--) {
                            var is_in = 0;
                            for (var j = 0; j < excludeIds.length; j++) {
                                if (result[i].id == excludeIds[j][0] && (typeof result[i].id_product_attribute == 'undefined' || result[i].id_product_attribute == excludeIds[j][1])) {
                                    is_in = 1;
                                }
                            }
                            if (!is_in) {
                                returnIds.push(result[i]);
                            }
                        }
                    } else {
                        html = "<li>";
                        html += "<div>"+ noMatchesFound +"</div>"
                        html += "</li>";
                        sugprod_ul.append(html);
                    }

                    if (returnIds) {
                        var html;
                        $.each(returnIds, function(key, value) {
                            if (typeof value.id_product_attribute == 'undefined') {
                                value.id_product_attribute = 0;
                            }

                            html = "<li class='sugpkprod_li' data-id_ps_product='" + value.id + "' data-img='" + value.image + "' data-id_ps_product_attr='" + value.id_product_attribute + "'>";
                            html += "<div style='float:left;margin-right:5px;'><img src=" + value.image + " width='40' /></div>"
                            html += "<div style='float:left;'><h4 class='li_prod_name'>" + value.name + " </h4>";
                            html += "<span> REF: " + value.ref + " </span></div>";
                            html += "</li>";
                            sugprod_ul.append(html);

                            /*html = "<li class='sugpkprod_li' data-id_ps_product='" + value.id + "' data-img='" + value.image + "' data-id_ps_product_attr='" + value.id_product_attribute + "'>";
                            html += "<div style='float:left;margin-right:5px;'><img src=" + value.image + " width='40'/></div>"
                            html += "<div style='float:left;'><h4 class='li_prod_name'>" + value.name + " </h4>";
                            html += "<span> REF: " + value.ref + " </span></div>";
                            html += "</li>";*/
                        });
                    }
                }
            });
        }
    });

    $('body').on('click', '.sugpkprod_li', function() {
        $('#selectproduct').val($(this).find('.li_prod_name').text());
        $('#selectproduct').data('id_ps_product', $(this).data('id_ps_product'));
        $('#selectproduct').data('id_ps_product_attr', $(this).data('id_ps_product_attr'));
        $('#selectproduct').data('img', $(this).data('img'));
        $('#sugpkprod_ul').html('').hide();
    });

    // validate if empty input field
    $('#addpackprodbut').on('click', function(e) {
        e.preventDefault();

        prod_name = $('#selectproduct').val();
        ps_id_prod = $('#selectproduct').data('id_ps_product');
        ps_id_prod_attr = $('#selectproduct').data('id_ps_product_attr');
        prod_img_link = $('#selectproduct').data('img');
        ps_prod_quantity = $('#packproductquant').val();

        if (prod_name == '' || ps_id_prod == '' || prod_img_link == '') {
            alert(invalid_product_name);
        } else if (!$.isNumeric(ps_prod_quantity) || ps_prod_quantity <= 0) {
            alert(invalid_quantity);
        } else {
            $('#selectproduct').val('').data('id_ps_product', '');
            $('#selectproduct').data('img', '');

            $('#packproductquant').val(1);

            listhtml = "<div class='col-sm-4 col-xs-12'>";
            listhtml += "<div class='row no_margin pk_sug_prod' ps_prod_id=" + ps_id_prod + " ps_id_prod_attr=" + ps_id_prod_attr + ">";
            listhtml += "<div class='col-sm-12 col-xs-12'>";
            listhtml += "<img src=" + prod_img_link + " class='img-responsive pk_sug_img'>";
            listhtml += "<p class='text-center'>" + prod_name + "</p>";
            listhtml += "<span class='pull-left'>x" + ps_prod_quantity + "</span>";
            listhtml += "<a class='pull-right dltpkprod'><i class='material-icons'>delete</i></a>";
            listhtml += "<input type='hidden' class='pspk_id_prod' name='pspk_id_prod[]' value='" + ps_id_prod + "'>";
            listhtml += "<input type='hidden' class='pspk_id_prod_attr' name='pspk_id_prod_attr[]' value='" + ps_id_prod_attr + "'>";
            listhtml += "<input type='hidden' name='pspk_prod_quant[]' value='" + ps_prod_quantity + "'>";
            listhtml += "</div>";
            listhtml += "</div>";
            listhtml += "</div>";

            $('.pkprodlist').append(listhtml);
        }
    });

    $('body').on('click', '.dltpkprod', function() {
        $(this).parent().parent().parent().remove();
    });
});

function getSelectedIds() {
    var packAddedIds = $('.pk_sug_prod').val();
    var ints = new Array();
    $.each($('.pk_sug_prod'), function(key, value) {
        var in_ints = new Array();
        in_ints[0] = $(this).attr('ps_prod_id');
        in_ints[1] = $(this).attr('ps_id_prod_attr');
        ints[key] = in_ints;

    });
    return ints;
}