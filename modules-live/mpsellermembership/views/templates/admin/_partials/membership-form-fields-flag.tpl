{**
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
*}

{if $allow_multilang && $total_languages > 1}
	<img class="all_lang_icon" data-lang-id="{$current_lang.id_lang|escape:'html':'UTF-8'}" src="{$ps_img_dir|escape:'html':'UTF-8'}{$current_lang.id_lang|escape:'html':'UTF-8'}.jpg">
{/if}