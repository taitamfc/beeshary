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

<h2>{l s='Messages' mod='wkgdpr'}</h2>
<br>
<table id="messages-tab" width="100%">
    <tr>
        <th class="header center">{l s='IP Address' mod='wkgdpr'}</th>
        <th class="header center">{l s='Email' mod='wkgdpr'}</th>
        <th class="header center">{l s='Message' mod='wkgdpr'}</th>
        <th class="header center">{l s='Status' mod='wkgdpr'}</th>
        <th class="header center">{l s='Date' mod='wkgdpr'}</th>
    </tr>
    {if $messages}
		{foreach from=$messages item=message}
			<tr>
				<td class="center">{$message['ip_address']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">{$message['email']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">{$message['message']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">{$message['status']|escape:'htmlall':'UTF-8'}</td>
				<td class="center">{$message['date_add']|escape:'htmlall':'UTF-8'}</td>
			</tr>
		{/foreach}
    {else}
		<tr>
			<td colspan="5" class="center">{l s='No messages found' mod='wkgdpr'}</td>
		</tr>
    {/if}
</table>