{*
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if $mpmenu==0}
  <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" title="{l s='Seller Transaction' mod='mpsellerpayment'}" href="{$sellertransactions_link}">
    <span class="link-item">
      <i class="material-icons">&#xE53E;</i>
      {l s='Seller Transactions' mod='mpsellerpayment'}
    </span>
  </a>
{else}
  <li {if $logic=='seller_trans'}class="menu_active"{/if}>
    <span>
      <a title="{l s='Seller Transaction' mod='mpsellerpayment'}" href="{$sellertransactions_link}">
        {l s='Seller Transactions' mod='mpsellerpayment'}
      </a>
    </span>
  </li>
{/if}
