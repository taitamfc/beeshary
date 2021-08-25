{*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2018 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
*}
<script>
    $(document).ready(function () {
        updateFeed();
        $('input[name="export_delimiter"]' +
            ', select[name="export_type"]' +
            ', select[name="export_format"]' +
            ', radio[name="export_active"]' +
            ', input[name="export_active"]' +
            ', select[name="export_tax"]' +
            ', select[name="export_category"]' +
            ', select[name="delete_images"]' +
            ', select[name="include_url"]' +
            ', select[name="export_language"]' +
            ', select[name="export_manufacturers"]' +
            ', select[name="export_suppliers"]' + '').change(function () {
            updateFeed();
        });
    });

    $('.show-links').click(function () {
        $(this).parent().find('.hide').removeClass('hide');
    });

    function updateFeed() {
        $('.feedurl').html($('#configuration_form').serialize());
        $('.feedurl').each(function () {
            var elem = $(this);
            elem.fadeOut(200)
                .fadeIn(200)
                .fadeOut(200)
                .fadeIn(200)
                .fadeOut(200)
                .fadeIn(200)
                .fadeOut(200)
                .fadeIn(200);
        });
    }
</script>

