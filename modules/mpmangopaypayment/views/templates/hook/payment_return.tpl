{*
* 2010-2018 Webkul
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

{if $valid == 1}
	<div class="conf confirmation">
		{l s='Your order on %s is complete with' sprintf=[$shop_name] mod='mpmangopaypayment'}
		{if isset($reference)}
			{l s='reference' mod='mpmangopaypayment'} <b>{$reference}</b>
		{else}
			{l s='Order ID' mod='mpmangopaypayment'} <b>{$id_order}</b>
		{/if}.
	</div>
	<br />
	{if (isset($bankwire))}
		<div class="box">
			{l s='Please send us bank wire on following details' mod='mpmangopaypayment'} - <br /><br />
			{l s='Amount:' mod='mpmangopaypayment'} {$total_paid}<br />
			{l s='Bankwire Reference:' mod='mpmangopaypayment'} {$bankwire['mgp_wire_reference']}<br />
			{l s='Account Owner Name:' mod='mpmangopaypayment'} {$bankwire['mgp_account_owner_name']}<br />
			{l s='Account IBAN:' mod='mpmangopaypayment'} {$bankwire['mgp_account_iban']}<br />
			{l s='Account BIC:' mod='mpmangopaypayment'} {$bankwire['mgp_account_bic']}
		</div>
	{/if}</br>
	<p>
		{l s='For any questions or for further information, please contact our' mod='mpmangopaypayment'} <a href="{$contact_url}">{l s='customer service department.' mod='mpmangopaypayment'}</a>.
	</p>
{else}
	<div class="error">
		{l s='Unfortunately, an error occurred during the transaction.' mod='mpmangopaypayment'}<br /><br />
		{l s='Please double-check your credit card details and try again. If you need further assistance, feel free to contact' mod='mpmangopaypayment'}
		<a href="{$contact_url}">{l s='customer service department.' mod='mpmangopaypayment'}</a>
		{l s='anytime.' mod='mpmangopaypayment'}<br /><br />
		<a href="{$contact_url}">{l s='customer service department.' mod='mpmangopaypayment'}</a>.
		{if isset($reference)}
			({l s='Your Order\'s Reference:' mod='mpmangopaypayment'} <b>{$reference}</b>)
		{else}
			({l s='Your Order\'s ID:' mod='mpmangopaypayment'} <b>{$id_order}</b>)
		{/if}
	</div>
{/if}