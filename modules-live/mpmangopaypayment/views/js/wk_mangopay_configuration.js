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
    $("input[name='wk_mangopay_direct_debit_enable']").on('change', function () {
        if (parseInt($(this).val())) {
            $(".wk_mgp_save_bank_account_div").removeClass('hidden');
        } else {
            $(".wk_mgp_save_bank_account_div").addClass('hidden');
        }
    });

    $("input[name='wk_mangopay_card_pay_enable']").on('change', function () {
        if (parseInt($(this).val())) {
            $(".wk_mgp_save_card_div").removeClass('hidden');
        } else {
            $(".wk_mgp_save_card_div").addClass('hidden');
        }
    });
});
