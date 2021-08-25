{**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}


{extends file='checkout/order-confirmation.tpl'}
{block name='order_confirmation_table'}
    {if $customerOrders|@count}
        <div class="col-sm-12">
            <p class="wk-orderConf-header abc">{l s='Order Information' mod='mpcartordersplit'}</p>
            <div class="row wk-order-entity-wrapper">
                {assign var="firstOrderPack" value=$customerOrders|@reset}
                <div class="col-sm-6">
                    <span class="wk-order-entity-title">{l s='Order Refrence' mod='mpcartordersplit'}:</span>
                    <span class="wk-order-entity-value">#{$firstOrderPack.details.reference}</span>
                </div>
                <div class="col-sm-6">
                    <span class="wk-order-entity-title">{l s='Payment Method' mod='mpcartordersplit'}:</span>
                    <span class="wk-order-entity-value">{$firstOrderPack.details.payment}</span>
                </div>
            </div>
            {foreach from=$customerOrders item=order name=orderPackages}
                {* <p class="wk-order-package">{l s='Order Package %packIndex%' mod='mpcartordersplit' sprintf=['%packIndex%' => $smarty.foreach.orderPackages.iteration]}</p> *}
                <div class="row wk-order-prodDetail-wrapper">
                    {$order['order_confirmation_table_html'] nofilter}

                    <div class="col-sm-12">
                        <p class="wk-order-shipping-title">{l s='Shipping Method' mod='mpcartordersplit'}</p>
                        <p class="wk-order-shipping-method">{$order.carrier.name}</p>
                    </div>
                </div>
                {if !$smarty.foreach.orderPackages.last}
                    <hr class="wk-order-seperator"/>
                {/if}
            {/foreach}
        </div>
    {/if}
{/block}

{block name='order_details'}
{/block}
