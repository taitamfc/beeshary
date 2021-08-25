{*
* 2007-2017 PrestaShop
*

* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2017 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}

<div class="payment_module stripe-payment-17" style="padding:15px 10px;">
<div id="stripe-ajax-loader-cc" style="display:none;"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/ajax-loader.gif" alt="" /> {l s='Do not press BACK or REFRESH while processing...' mod='stripepro'}</div>
<form action="#" id="stripe-payment-form-cc">
<input type="hidden" name="stripeToken" value="{$credit_card.token|escape:'htmlall':'UTF-8'}" />
<label class="card_line">
XXXXXXXXXXXX<b>{$credit_card.cc_last_digits|escape:'htmlall':'UTF-8'}</b>&nbsp;<img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-{if $credit_card.cc_type=='American Express'}amex{elseif $credit_card.cc_type=='Diners Club'}diners{elseif $credit_card.cc_type=='Mastercard (prepaid)'}mastercard{elseif $credit_card.cc_type=='Mastercard (debit)'}mastercard{elseif $credit_card.cc_type=='Visa (debit)'}visa{else}{$credit_card.cc_type|escape:'htmlall':'UTF-8'}{/if}.png" alt="" /><br /><br />
{l s='Expiry date' mod='stripepro'}:<strong>{$credit_card.cc_exp|escape:'htmlall':'UTF-8'}</strong>
  </label>
  <div class="stripe-payment-errors"></div>
  <a name="stripe_error" style="display:none"></a>
</form>
</div>