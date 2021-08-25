{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if $remaining_amount > 0}
    <p style="margin-top:10px;margin-left:15px;font-weight:bold;" class="wk_free_ship_msg_{$id_seller}">{l s='Add %price_more% more for free shipping.' sprintf=['%price_more%' =>$remaining_amount_d] mod='mpshipping'}<p>
{/if}
