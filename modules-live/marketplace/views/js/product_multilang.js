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
    $('#seller_default_lang').on("change", function(e) {
        e.preventDefault();
        if ($('#multi_lang').val() == '1') {
            var select_lang_iso = $(this).find("option:selected").data('lang-iso');
            var select_lang_id = $(this).val();

            showSellerLangField(select_lang_iso, select_lang_id);

            $('.shop_name_all').removeClass('seller_default_shop');
            $('#shop_name_' + select_lang_id).addClass('seller_default_shop');
        }
    });
});

function showProdLangField(lang_iso_code, id_lang) {
    $('#prod_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
    $('#short_desc_btn').html(lang_iso_code + ' <span class="caret"></span>');
    $('#product_desc_btn').html(lang_iso_code + ' <span class="caret"></span>');

    $('.product_name_all').hide();
    $('#product_name_' + id_lang).show();
    $('.short_desc_div_all').hide();
    $('#short_desc_div_' + id_lang).show();
    $('.product_desc_div_all').hide();
    $('#product_desc_div_' + id_lang).show();
}

function showSellerLangField(lang_iso_code, id_lang) {
    $('#shop_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
    $('#about_business_btn').html(lang_iso_code + ' <span class="caret"></span>');
    $('#address_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');

    $('.shop_name_all').hide();
    $('#shop_name_' + id_lang).show();
    $('.about_business_div_all').hide();
    $('#about_business_div_' + id_lang).show();
    $('.address_div_all').hide();
    $('#address_div_' + id_lang).show();
}