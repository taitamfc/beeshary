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

<h2>{l s='Addresses' mod='wkgdpr'}</h2>
<table id="addresses-tab" width="100%" class="common-table-style">
    <tr>
        <th class="header center">{l s='Alias' mod='wkgdpr'}</th>
        <th class="header center">{l s='Company' mod='wkgdpr'}</th>
        <th class="header center">{l s='Name' mod='wkgdpr'}</th>
        <th class="header center">{l s='City' mod='wkgdpr'}</th>
        <th class="header center">{l s='Country' mod='wkgdpr'}</th>
        <th class="header center">{l s='State' mod='wkgdpr'}</th>
        <th class="header center">{l s='Postcode' mod='wkgdpr'}</th>
        <th class="header center">{l s='Address' mod='wkgdpr'}</th>
        <th class="header center">{l s='Phone(s)' mod='wkgdpr'}</th>
    </tr>
    {if $addresses}
		{foreach from=$addresses item=address}
			<tr>
				<td class="center">{$address['alias']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">{$address['company']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">{$address['firstname']|escape:'htmlall':'UTF-8'} {$address['lastname']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">{$address['city']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">{$address['country']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">{$address['state']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">{$address['postcode']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">
					{$address['address1']|escape:'htmlall':'UTF-8'}
					{if $address['address2']}
						, <br>
						{$address['address2']|escape:'htmlall':'UTF-8'}
					{/if}
				</td>
				<td class="center">
					{if $address['phone']}
						{$address['phone']|escape:'htmlall':'UTF-8'}
						{if $address['phone_mobile']}
							, <br>
							{$address['phone_mobile']|escape:'htmlall':'UTF-8'}
						{/if}
					{/if}
				</td>
			</tr>
		{/foreach}
    {else}
		<tr>
			<td colspan="10" class="center">{l s='No address found' mod='wkgdpr'}</td>
		</tr>
    {/if}
</table>