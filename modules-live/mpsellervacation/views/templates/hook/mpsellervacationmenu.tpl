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

{if isset($show_tpl)}
	<li><a href="{$link->getModuleLink('mpsellervacation', 'sellerVacationDetail')}" title="{l s='Seller Vacation' mod='mpsellervacation'}" class="col-xs-12">
		<span class="link-item">
			<i class="material-icons">&#xE195;</i>
			{l s='Seller Vacation' mod='mpsellervacation'}
		</span>
	</a></li>
{else}
	<li {if $logic=='vac_details'}class="menu_active"{/if}>
		<span>
			<a href="{$link->getModuleLink('mpsellervacation', 'sellerVacationDetail')}" title="{l s='Seller Vacation' mod='mpsellervacation'}">
				<i class="material-icons">&#xE195;</i>
				{l s='Seller Vacation' mod='mpsellervacation'}
			</a>
		</span>
	</li>	
{/if}