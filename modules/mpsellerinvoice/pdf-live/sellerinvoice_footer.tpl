{*
* 2010-2016 Webkul
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
*  @copyright  2010-2016 Webkul IN
*}
<table style="width: 100%;">
	<tr>
		<td style="text-align: center; font-size: 6pt; color: #444;  width:87%;">
			{if $available_in_your_account}
				{l s='An electronic version of this invoice is available in your account. To access it, log in to our website using your e-mail address and password (which you created when placing your first order).' mod='mpsellerinvoice'}
				<br />
			{/if}
			<!-- {$admin_shop_address} -->
			<!-- |escape:'html':'UTF-8' -->
				2047 Chemin de La Cour,	83680 La Garde-Freinet
				<br />

				<!-- {if !empty($admin_phone)}
					{l s='Tel: %s' sprintf=[$admin_phone|escape:'html':'UTF-8'] pdf='true'}
				{/if}
				{if !empty($admin_mail)}
				{l s='Fax: %s' sprintf=[$admin_mail|escape:'html':'UTF-8'] pdf='true'}
				{/if} -->

				{if !empty($admin_mail)}
					{l s='For more assistance, contact Support:' pdf='true'}<br />
					{if !empty($admin_mail)}
						{l s='%s' sprintf=[$admin_mail|escape:'html':'UTF-8'] pdf='true'}
					{/if}
				<br />
			{/if}
			Beeshary, Be Happy !
			<!-- {if isset($shop_details)}
				{$shop_details|escape:'html':'UTF-8'}<br />
			{/if} -->

		</td>
		<td style="text-align: right; font-size: 8pt; color: #444;  width:13%;">
            {literal}{:pnp:} / {:ptp:}{/literal}
        </td>
	</tr>
</table>
