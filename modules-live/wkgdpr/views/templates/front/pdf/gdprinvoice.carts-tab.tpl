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

<h2>{l s='Carts' mod='wkgdpr'}</h2>
<br>
<table id="carts-tab" width="100%">
    <tr>
        <th class="header center">{l s='ID' mod='wkgdpr'}</th>
        <th class="header center">{l s='Items Count' mod='wkgdpr'}</th>
        <th class="header center">{l s='Total' mod='wkgdpr'}</th>
        <th class="header center">{l s='Date' mod='wkgdpr'}</th>
    </tr>
    {if $carts}
		{foreach from=$carts item=cart}
			<tr>
				<td class="center">{$cart['id_cart']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">{$cart['nb_products']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">{$cart['formated_total_tax_incl']|escape:'htmlall':'UTF-8'}</td>
                <td class="center">{$cart['date_add']|escape:'htmlall':'UTF-8'}</td>
			</tr>
            {if isset($cart['products']) && $cart['products']}
                <tr>
                    <td colspan="3" class="center"><b>{l s='Product(s)' mod='wkgdpr'} : </b></td>
                    <td colspan="1" class="center"></td>
                </tr>
                <tr>
                    <td class="center"></td>
                    <td colspan="3" class="center">
                        <table width="100%">
                            <tr>
                                <th class="header center">{l s='Name' mod='wkgdpr'}</th>
                                <th class="header center">{l s='Quantity' mod='wkgdpr'}</th>
                                <th class="header center">{l s='Price' mod='wkgdpr'}</th>
                            </tr>
                            {foreach from=$cart['products'] item=product}
                                <tr>
                                    <td class="center">
                                        {$product['name']|escape:'html':'UTF-8'}
                                        {if $product['attributes']}
                                            <br>
                                            {$product['attributes']|escape:'html':'UTF-8'}
                                        {/if}
                                    </td>
                                    <td class="center">{$product['qty']|escape:'html':'UTF-8'}</td>
                                    <td class="center">{$product['price']|escape:'html':'UTF-8'}</td>
                                </tr>
                            {/foreach}
                        </table>
                    </td>
                </tr>
            {/if}
		{/foreach}
    {else}
		<tr>
			<td colspan="4" class="center">{l s='No cart found' mod='wkgdpr'}</td>
		</tr>
    {/if}
</table>