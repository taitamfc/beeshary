/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 *  @author    Línea Gráfica E.C.E. S.L.
 *  @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 *  @license   https://www.lineagrafica.es/licenses/license_en.pdf https://www.lineagrafica.es/licenses/license_es.pdf https://www.lineagrafica.es/licenses/license_fr.pdf
 */

(function($){
    var loop;
    $.fn.extend({
        lgspinner: function (action) {
            switch (action.toLowerCase()) {
                case 'show':
                    var overlay = $('<div class="lg-overlay"></div>');
                    var spinner = $('<div class="lg-spinner"></div>');
                    var loading = $('<div class="lg-loading"></div>');
                    var deg = 0;

                    // Overlay styles
                    overlay.css({
                        'position': 'fixed',
                        'background': 'rgba(255,255,255,0.5)',
                        'width': '100%',
                        'height': '100%',
                        'z-index': '99999'
                    });

                    // Spinner styles
                    spinner.css({
                        'padding': '50px',
                        'position': 'relative',
                        'text-align': 'center',
                        'top': '40%'
                    });

                    // Loading styles
                    loading.css({
                        'height': '80px',
                        'width': '80px',
                        'margin': '-15px auto auto -15px',
                        'position': 'absolute',
                        'top': '50%',
                        'left': '50%',
                        'border-width': '3px',
                        'border-style': 'solid',
                        'border-color': '#96C45F #DDDDDD #DDDDDD',
                        'border-radius': '100%'
                    });

                    spinner.append(loading);
                    overlay.append(spinner);
                    $(this).append(overlay).fadeIn(250);

                    loop = setInterval(function () {
                        if (deg >= 359) deg = 0; else deg += 15;
                        loading.css({
                            'webkitTransform': 'rotate(' + deg + 'deg)',
                            'mozTransform': 'rotate(' + deg + 'deg)',
                            'msTransform': 'rotate(' + deg + 'deg)',
                            'oTransform': 'rotate(' + deg + 'deg)',
                            'transform': 'rotate(' + deg + 'deg)'
                        });
                    }, 20);
                    break;

                case 'hide':
                    $('.lg-overlay').fadeOut(250, function () {
                        clearInterval(loop);
                        $(this).remove();
                    });
                    break;
            }
        }
    });
}(jQuery));