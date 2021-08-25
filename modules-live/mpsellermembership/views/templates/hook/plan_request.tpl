{*
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if $no_permi==1}
	<div class="alert alert-danger">
		{l s='You do not have permission to add product' mod='mpsellermembership'}
	</div>	
{else}
	{if $plan_expire==1}
		<div class="alert alert-warning">
			{l s='You have not requested any membership plan yet, to add product(s) - buy a plan.' mod='mpsellermembership'}
			<a class="btn btn-primary" href="{$plan_request_link}">
				<span>{l s='Buy Plan' mod='mpsellermembership'}</span>
			</a>
		</div>
	{else if $plan_expire==2}
		<div class="alert alert-warning">
			{l s='Your membership plan has been expired.' mod='mpsellermembership'}
			<a class="btn btn-primary" href="{$plan_request_link}">
				<span>{l s='Upgrade Your Plan' mod='mpsellermembership'}</span>
			</a>
			{l s='Before Adding product' mod='mpsellermembership'}
		</div>
	{else if $plan_expire==3}
		<div class="alert alert-info">
			{l s='Your activated membership plan will be expire after ' mod='mpsellermembership'}{$days}{l s=' day(s)' mod='mpsellermembership'}
			<a class="btn btn-primary" href="{$plan_request_link}">
				<span>{l s='Upgrade Your Plan' mod='mpsellermembership'}</span>
			</a>
		</div>
	{else if $plan_expire==4}
		<div class="alert alert-warning">
			{l s='You have reached your maximum product limit. You can not add products now.' mod='mpsellermembership'}
			<a class="btn btn-primary" href="{$plan_request_link}">
				<span>{l s='Upgrade Your Plan' mod='mpsellermembership'}</span>
			</a>
		</div>
	{else if $plan_expire==5}
		<div class="alert alert-info">
			{l s='Your membership plan left only ' mod='mpsellermembership'}{$products}{l s=' product(s)' mod='mpsellermembership'}
			<a class="btn btn-primary" href="{$plan_request_link}">
				<span>{l s='Upgrade Your Plan' mod='mpsellermembership'}</span>
			</a>
		</div>
	{else if $plan_expire==6}
		<div class="alert alert-info">
			{l s='Your marketplace membership plan request has been sent to admin. Please wait till the approval from admin.'  mod='mpsellermembership'}
		</div>
	{/if}
{/if}