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

<h2>{l s='Orders' mod='wkgdpr'}</h2>
<br>
<table id="orders-tab" width="100%" class="common-table-style">
    <tr>
        <th class="header center">{l s='Reference' mod='wkgdpr'}</th>
        <th class="header center">{l s='Items Count' mod='wkgdpr'}</th>
        <th class="header center">{l s='Total' mod='wkgdpr'}</th>
        <th class="header center">{l s='Payment' mod='wkgdpr'}</th>
        <th class="header center">{l s='Status' mod='wkgdpr'}</th>
        <th class="header center">{l s='Date' mod='wkgdpr'}</th>
    </tr>

    {if $orders}
		{foreach from=$orders item=order}
			<tr>
				<td class="center">{$order['reference']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">{$order['nb_products']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">{$order['formated_total_paid_tax_incl']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">{$order['payment']|escape:'htmlall':'UTF-8'}</td>
                <td class="center">{$order['order_state']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">{$order['date_add']|escape:'htmlall':'UTF-8'}</td>
			</tr>
            <tr>
               <td colspan ="2" class="center">
                    <strong>{l s='Product(s)' mod='wkgdpr'} : </strong>
                </td>
                <td colspan="4" class="center">
                    <table width="100%" cellspacing="0" class="common-table-style">
                        <tr>
                            <th class="header center">{l s='Name' mod='wkgdpr'}</th>
                            <th class="header center">{l s='Quantity' mod='wkgdpr'}</th>
                            <th class="header center">{l s='Price' mod='wkgdpr'}</th>
                        </tr>
                        {if isset($order['products']) && $order['products']}
                            {foreach from=$order['products'] item=product}
                                <tr>
                                    <td class="center">{$product['name']|escape:'html':'UTF-8'}</td>
                                    <td class="center">{$product['qty']|escape:'html':'UTF-8'}</td>
                                    <td class="center">{$product['price']|escape:'html':'UTF-8'}</td>
                                </tr>
                            {/foreach}
                        {/if}
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="6" class="center">
                <hr>
                </td>
            </tr>
		{/foreach}
    {else}
		<tr>
			<td colspan="6" class="center">{l s='No order found' mod='wkgdpr'}</td>
		</tr>
    {/if}
</table>