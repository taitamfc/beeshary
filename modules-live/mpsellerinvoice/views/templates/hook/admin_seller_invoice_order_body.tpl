{*
* 2010-2019 Webkul
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
*  @author Webkul IN <support@webkul.com>
*  @copyright  2010-2019 Webkul IN
*}

<td>
    {if $show_view_send}
        <a
            target="_blank"
            href="{$link->getAdminLink('AdminCommissionInvoice')}&submitAction=viewAdminInvoice&sellerCustomerId={$idSellerCustomer}&idOrder={$id_order}" class="btn btn-default">
                <i class="icon-file"></i>
                {l s='View commission invoice' mod='mpsellerinvoice'}
        </a>
        <a
            href="{$link->getAdminLink('AdminCommissionInvoice')}&submitAction=sendAdminInvoice&sellerCustomerId={$idSellerCustomer}&idOrder={$id_order}"
            class="btn btn-default">
                <i class="icon-mail-reply"></i>
                {l s='Send commission invoice' mod='mpsellerinvoice'}
        </a>
    {/if}
</td>