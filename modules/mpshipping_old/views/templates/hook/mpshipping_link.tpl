{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if $mpmenu == 0}
	<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" title="{l s='Shipping Method' mod='mpshipping'}" href="{$mpshippinglist}">
		<span class="link-item">
			<i class="material-icons">&#xE905;</i>
			{l s='Shipping Method' mod='mpshipping'}
		</span>
	</a>
{else}
	<li {if $logic=='mp_carriers'}class="menu_active"{/if}>
		<span>
			<a title="Mp Shipping" href="{$mpshippinglist|escape:'htmlall':'UTF-8'}">
				<i class="material-icons">&#xE905;</i>
				{l s='Shipping Method' mod='mpshipping'}
			</a>
		</span>
	</li>
{/if}