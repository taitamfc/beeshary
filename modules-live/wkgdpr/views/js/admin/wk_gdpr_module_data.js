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
    //Tab active code
    if ($('#active_tab').val() != '') {
        var active_tab = $('#active_tab').val();
        changeTabStatus(active_tab);
    }

    $(document).on("click", '.submitModulesAgreementContents', function(e) {
        //put active tab in input hidden type
        var active_tab_id = $('.gdpr_general_config_form .nav-tabs li.active a').attr('href');
        if (typeof active_tab_id !== 'undefined') {
            var active_tab_name = active_tab_id.substring(1, active_tab_id.length);
            $('#active_tab').val(active_tab_name);
        }
    });

    $("input.active_gdpr_agreement").on('change', function () {
        if (parseInt($(this).val())) {
            $(this).closest('.gdpr-aggrement-content-box').find('.gdpr-aggrement-content-div').removeClass('hidden');
        } else {
            $(this).closest('.gdpr-aggrement-content-box').find('.gdpr-aggrement-content-div').addClass('hidden');
        }
    });
});

function changeTabStatus(active_tab) {
    //Remove all tabs from active (make normal)
    $('.gdpr_general_config_form .nav-tabs li').removeClass('active');
    $('.gdpr_general_config_form .tab-content .tab-pane').removeClass('active');
    //Add active class in selected tab
    $('[href*="#' + active_tab + '"]').parent('li').addClass('active');
    $('#' + active_tab).addClass('active in');
}

//for tinymce setup
tinySetup({
    editor_selector :"wk_tinymce",
    width : 700
});


function showAgreementContentLangField(id_element, id_lang, lang_iso_code)
{
    $('#wk_gdpr_lang_btn_'+id_element).html(lang_iso_code + ' <span class="caret"></span>');
    $('.gdpr_agreement_content_'+id_element).hide();
    $('#gdpr_agreement_content_'+id_element+'_'+id_lang).show();
}