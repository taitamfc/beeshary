/**
* 2010-2017 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
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

function showCustomizationLangField(lang_iso_code, id_lang)
{
	$('.filename_btn').html(lang_iso_code + ' <span class="caret"></span>');
	$('.textname_btn').html(lang_iso_code + ' <span class="caret"></span>');

	$('.filename_div_all').hide();
	$('.filename_div_0_'+id_lang).show();

	$('.textname_div_all').hide();
	$('.textname_div_1_'+id_lang).show();
}