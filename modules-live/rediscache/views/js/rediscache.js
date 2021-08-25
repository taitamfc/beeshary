/**
 * Redis Cache powered by Vopster
 *
 *    @author    Vopster
 *    @copyright 2017 Vopster
 *    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 *    @link      https://addons.prestashop.com/en/contact-us?id_product=26866
 */


(function($) {
    window.RedisCache = window.RedisCache || {
        params: {
            url: redis_admin_ajax_url,
            caching_systems: redis_caching_systems,
        },
        overrideCacheHandlerForm: function() {
            var params = this.params;

            $.ajax({
                url: params.url,
                type: 'POST',
                cache: false,
                data: {
                    ajax: true,
                    action: 'getStatus',
                    controller: 'AdminRediscache',
                },
                dataType: 'json',
                success: function (data) {
                    // PS1.7
                    if ($('#caching_systems').length > 0) {
                        $('#caching_systems').closest('.card-text').html(params.caching_systems);
                    }
                    // PS1.6
                    if ($('#fieldset_5_5').length > 0) {
                        $('#fieldset_5_5').html(params.caching_systems);
                    }
                },
            });
        }
    };

    $(document).ready(function() {
        RedisCache.overrideCacheHandlerForm();
    });

}(jQuery));
