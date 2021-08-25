/**
 * 2010-2019 Webkul.
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
 *  @copyright 2010-2019 Webkul IN
 *  @license   https://store.webkul.com/license.html
 */

$(document).ready(function() {
    // by default forms submit will be disabled only will enable if agreement checkbox is checked
    if ($('.wk_gdpr_agreement').prop('checked') != true) {
        $('.wk_gdpr_agreement').closest('form').find('[type="submit"]').attr('disabled', 'disabled');
    }
    $(document).on("change" ,".wk_gdpr_agreement", function() {
        if ($(this).prop('checked') == true) {
            $(this).closest('form').find('[type="submit"]').removeAttr('disabled');
        } else {
            $(this).closest('form').find('[type="submit"]').attr('disabled', 'disabled');
        }
    });
});
