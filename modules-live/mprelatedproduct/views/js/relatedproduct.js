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
    var width = $('body #content-wrapper').width();
    var slideWidth = 0;
    var maxSlides = 4;
    if (width < 768) {
        slideWidth = width;
        maxSlides = 1;
    } else {
        slideWidth = width / 4;
        maxSlides = 4;
    }

    $('.catblog').bxSlider({
        infiniteLoop: true,
        hideControlOnEnd: true,
        pager: false,
        autoHover: true,
        auto: false,
        slideWidth: slideWidth,
        minSlides: 1,
        maxSlides: 4,
        speed: parseInt("500"),
        pause: 3000,
        controls: true,
        displaySlideQty: maxSlides,
        responsive: true,
        nextText: '<i class="material-icons">navigate_next</i>',
        prevText: '<i class="material-icons">navigate_before</i>',
        onSliderLoad: function(e) {
            $('.mp-related-products').removeClass('wk-aria-hidden');
        }
    });
    //$('.bx-wrapper').removeAttr('style');
});
