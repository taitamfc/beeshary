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
    <table class="table" id="my-orders-table">
        <thead>
            <tr>
                <th>{l s='ID' mod='mpsellerinvoice'}</th>
                <th>{l s='Reference' mod='mpsellerinvoice'}</th>
                <th>{l s='Customer' mod='mpsellerinvoice'}</th>
                <th>{l s='Total' mod='mpsellerinvoice'}</th>
                <th>{l s='Status' mod='mpsellerinvoice'}</th>
                <th style="text-align:center;">{l s='Invoice' mod='mpsellerinvoice'}</th>
            </tr>
        </thead>
        <tbody>
            {if isset($mpsellerorders)}
                {foreach $mpsellerorders as $order}
                    <tr class="mp_seller_order_row wk-mp-data-list" data-id-order="{$order.id_order}">
                        <td>{$order.id_order|escape:'html':'UTF-8'}</td>
                        <td>{$order.reference|escape:'html':'UTF-8'}</td>
                        <td>{$order.buyer_info->firstname|escape:'html':'UTF-8'} {$order.buyer_info->lastname|escape:'html':'UTF-8'}</td>
                        <td>{$order.total_paid|escape:'html':'UTF-8'}</td>
                        <td>{$order.order_status|escape:'html':'UTF-8'}</td>
                        <td class="text-sm-center">
                        {if $order.pdf_download}
                            <a
                                target='_blank'
                                id="wk_mp_commission_invoice"
                                href="{$link->getModuleLink(
                                    'mpsellerinvoice',
                                    'pdfdownload',
                                    [
                                        'id_order' => $order.id_order,
                                        'id_seller' => $seller.id_seller,
                                        'invoice' => 1
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
</div>