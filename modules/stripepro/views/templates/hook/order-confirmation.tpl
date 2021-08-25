{*
* 2015-2016 NTS
*
* DISCLAIMER
*
* You are NOT allowed to modify the software. 
* It is also not legal to do any changes to the software and distribute it in your own name / brand. 
*
* @author NTS
* @copyright  2015-2016 NTS
* @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
* International Registered Trademark & Property of NTS
*}
{if $stripe_order.valid == 1}
	<div class="success alert alert-success">{l s='Congratulations, your payment has been approved and your order has been saved under the reference' mod='stripepro'} <b>{$stripe_order.reference|escape:'htmlall':'UTF-8'}</b>.</div>
{else}
	{if $order_pending}
		<div class="warning alert alert-warning">{l s='Unfortunately we detected a problem while processing your order and it needs to be reviewed.' mod='stripepro'}<br /><br />
		{l s='Do not try to submit your order again, as the funds have already been received.  We will review the order and provide a status shortly.' mod='stripepro'}<br /><br />
		({l s='Your Order\'s Reference:' mod='stripepro'} <b>{$stripe_order.reference|escape:'htmlall':'UTF-8'}</b>)</div>
	{else}
		<div class="error alert alert-danger">{l s='Sorry, unfortunately an error occured during the transaction.' mod='stripepro'}<br /><br />
		{l s='Please double-check your credit card details and try again or feel free to contact us to resolve this issue.' mod='stripepro'}<br /><br />
		({l s='Your Order\'s Reference:' mod='stripepro'} <b>{$stripe_order.reference|escape:'htmlall':'UTF-8'}</b>)</div>
	{/if}
{/if}
