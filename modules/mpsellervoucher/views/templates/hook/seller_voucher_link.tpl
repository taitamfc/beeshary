{*
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if $mpmenu == 0}
	<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" title="{l s='Manage Voucher'}" href="{$link->getModuleLink('mpsellervoucher', 'managevoucher')}">
		<span class="link-item">
			<i class="material-icons">&#xE89A;</i>
			{l s='Manage Voucher' mod='mpsellervoucher'}
		</span>
	</a>
{else}
	<li {if $logic=='seller_voucher'}class="menu_active"{/if}>
		<span>
			<a title="{l s='Manage Voucher' mod='mpsellervoucher'}" href="{$link->getModuleLink('mpsellervoucher', 'managevoucher')}">
				<i class="material-icons">&#xE89A;</i>
				{l s='Manage Voucher' mod='mpsellervoucher'}
			</a>
		</span>
	</li>
{/if}