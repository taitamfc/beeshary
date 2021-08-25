{*
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<li {if $logic=='mp_seller_payment_request'}class="menu_active"{/if}>
	<span>
		<a href="{$link->getModuleLink('mpsellerpaymentrequest', 'paymentrequest')|escape:'htmlall':'UTF-8'}" title="{l s='Payment Request' mod='mpsellerpaymentrequest'}">
			<i class="material-icons"></i>
			{l s='Payment Request' mod='mpsellerpaymentrequest'}
		</a>
	</span>
</li>
