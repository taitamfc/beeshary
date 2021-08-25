/**
 * 2010-2020 Webkul.
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
 *  @copyright 2010-2020 Webkul IN
 *  @license   https://store.webkul.com/license.html
 */

$(document).ready(function() {
    var tabs_element = $('#mp_tracking_div').parents('.tabs');
    tabs_element.find('.nav-tabs li.nav-item a').removeClass('active');
    tabs_element.find('.tab-pane').removeClass('active');

    $('#mp_tracking_div .nav-link').addClass('active');
    $('#tracking_id').addClass('active in');
});