{*
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="table-responsive wk_order_table">
    <table class="table" id="my-orders-table-2">
        <thead>
            <tr>
                <th>{l s='Invoice Number' mod='mpsellerinvoice'}</th>
                <th>{l s='Id Order' mod='mpsellerinvoice'}</th>
                <th>{l s='From' mod='mpsellerinvoice'}</th>
                <th>{l s='To' mod='mpsellerinvoice'}</th>
                <th>{l s='Action' mod='mpsellerinvoice'}</th>
            </tr>
        </thead>
        <tbody>
            {if isset($mporders)}
                {foreach $mporders as $order}
                    <tr class="wk-mp-data-list">
                        <td>{$order.invoice_number|escape:'html':'UTF-8'}</td>
                        <td>
                            {if $order.invoice_based == 1}
                                {$order.orders|escape:'html':'UTF-8'}
                            {else}
                                --
                            {/if}
                        </td>
                        <td>{$order.from|escape:'html':'UTF-8'}</td>
                        <td>{$order.to|escape:'html':'UTF-8'}</td>
                        <td class="text-sm-center">
                        {if $order.is_send_to_seller}
                            <a
                                target='_blank'
                                id="wk_mp_commission_invoice"
                                href="{$link->getModuleLink(
                                    'mpsellerinvoice',
                                    'pdfdownload',
                                    [
                                        'id_seller' => $seller.id_seller,
                                        'invoice_number' => $order.invoice_number,
                                        'submitAction' => 'downloadAdminCommissionInvoice'
                                    ])}">
                                <i class="material-icons">&#xE415;</i>
                            </a>
                        {else}
                            --
                        {/if}
                        </td>
                    </tr>
                {/foreach}
            {/if}
        </tbody>
    </table>
    {if isset($mporders) && $mporders}
        <div class="">{l s='No record found' mod='mpsellerinvoice'}</div>
    {/if}
</div>