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
{if isset($is_admin) && $is_admin == 1}
<li>
    <a href="#wk-seller-free_shipping" data-toggle="tab">
        <i class="icon-truck"></i>
        {l s='Avail Free Shipping' mod='mpshipping'}
    </a>
</li>
{else}
    <li class="nav-item">
        <a class="nav-link" href="#wk-seller-free_shipping" data-toggle="tab">
            <i class="material-icons">local_shipping</i>
            {l s='Avail Free Shipping' mod='mpshipping'}
        </a>
    </li>
{/if}
