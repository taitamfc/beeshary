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

<h2>{l s='Connections' mod='wkgdpr'}</h2>
<br>
<table id="connections-tab" width="100%">
    <tr>
        <th class="header center">{l s='Date' mod='wkgdpr'}</th>
        <th class="header center">{l s='Pages viewed' mod='wkgdpr'}</th>
        <th class="header center">{l s='Total time' mod='wkgdpr'}</th>
        <th class="header center">{l s='IP Address' mod='wkgdpr'}</th>
    </tr>
    {if $connections}
		{foreach from=$connections item=connection}
			<tr>
				<td class="center">{dateFormat date=$connection['date_add']|escape:'htmlall':'UTF-8' full=0}</td>
                <td class="center">{$connection['pages']|escape:'htmlall':'UTF-8'}</td>
                <td class="center">{$connection['time']|escape:'htmlall':'UTF-8'}</td>
                <td class="center">{$connection['ipaddress']|escape:'htmlall':'UTF-8'}</td>
			</tr>
		{/foreach}
    {else}
		<tr>
			<td colspan="4" class="center">{l s='No connections found' mod='wkgdpr'}</td>
		</tr>
    {/if}
</table>