/**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

$(document).ready(function(){
    $(document).on('change', '.smsSetting', function(){
        if ($('#'+$(this).attr('name')+'_on').is(':checked')) {
            $('.'+$(this).attr('name')+'_Div').slideDown();
        } else {
            $('.'+$(this).attr('name')+'_Div').slideUp();
        }
    });
    
    $(document).on('change', '#WK_MPSMS_API', function(){
        $(".apiDetailAll").hide();
        $("."+$(this).val()).show();
    });
});

function showSMSLangField(langIsoCode, idLang, currentObj)
{
	$(currentObj).parent().parent().siblings().html(langIsoCode + ' <span class="caret"></span>');

	$('.'+$(currentObj).attr('data-cls-name')).hide();
	$('#'+$(currentObj).attr('data-cls-name')+'_'+idLang).show();
}