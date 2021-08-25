{*
* 2010-2018 Webkul.
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

{if $mpmenu==0}
  <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" title="{l s='Mangopay Details' mod='mpmangopaypayment'}" href="{$link->getModuleLink('mpmangopaypayment', 'mangopayselledetails')}">
    <span class="link-item">
      <i class="material-icons">&#xE8A1;</i>
      {l s='Mangopay Details' mod='mpmangopaypayment'}
    </span>
  </a>
	{if isset($seller_bank_details_enable) && $seller_bank_details_enable}
			<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" title="{l s='Mangopay Bank Details' mod='mpmangopaypayment'}" href="{$link->getModuleLink('mpmangopaypayment', 'mangopaysellerbankdetails')}">
        <span class="link-item">
  				<i class="material-icons">&#xE8A1;</i>
  				<span>{l s='Mangopay Bank Details' mod='mpmangopaypayment'}</span>
        </span>
			</a>
	{/if}
	{if isset($seller_cashout_enable) && $seller_cashout_enable}
			<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" title="{l s='Mangopay Bank Details' mod='mpmangopaypayment'}" href="{$link->getModuleLink('mpmangopaypayment', 'mangopaysellercashout')}">
        <span class="link-item">
  				<i class="material-icons">&#xE8A1;</i>
  				<span>{l s='Mangopay Wallet Cash Out' mod='mpmangopaypayment'}</span>
        </span>
			</a>
	{/if}
{else}
	<li {if $logic == 'mgp_seller_details'}class="menu_active"{/if}>
		<span>
			<a href="{$link->getModuleLink('mpmangopaypayment', 'mangopayselledetails')}">
				<i class="material-icons">&#xE8A1;</i>
	        	{l s='Mangopay Details' mod='mpmangopaypayment'}
	      </a>
		</span>
	</li>
	{if isset($seller_bank_details_enable) && $seller_bank_details_enable}
		<li {if $logic == 'mgp_seller_bank_details'}class="menu_active"{/if}>
			<span>
				<a href="{$link->getModuleLink('mpmangopaypayment', 'mangopaysellerbankdetails')}">
					<i class="material-icons">&#xE8A1;</i>
					{l s='Mangopay Bank Details' mod='mpmangopaypayment'}
				</a>
			</span>
		</li>
	{/if}
	{if isset($seller_cashout_enable) && $seller_cashout_enable}
		<li {if $logic == 'mgp_seller_cash_out'}class="menu_active"{/if}>
			<span>
				<a href="{$link->getModuleLink('mpmangopaypayment', 'mangopaysellercashout')}">
					<i class="material-icons">&#xE8A1;</i>
					{l s='Mangopay Cash Out' mod='mpmangopaypayment'}
				</a>
			</span>
		</li>
	{/if}
{/if}

