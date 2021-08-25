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

<div class="payment_module">
<div class="stripe-payment-errors"></div>
<img src="{$stripe_cc|escape:'htmlall':'UTF-8'}" alt="stripe credit/ debit cards">
<div id="stripe-ajax-loader-checkout" style="display:none"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/ajax-loader.gif" alt="" /> {l s='Do not press BACK or REFRESH while processing...' mod='stripepro'}</div>
<script type="text/javascript">
  var mode = {$stripe_mode|escape:'htmlall':'UTF-8'};
  var currency = "{$currency|escape:'htmlall':'UTF-8'}";
  var amount_ttl = {$amount_ttl|escape:'htmlall':'UTF-8'};
  var baseDir = "{$baseDir|escape:'htmlall':'UTF-8'}";
  var billing_address = {$billing_address|escape nofilter};
  var module_dir = "{$module_dir|escape:'htmlall':'UTF-8'}";
  var popup_title = "{$popup_title|escape:'htmlall':'UTF-8'}";
  var popup_desc = "{$popup_desc|escape:'htmlall':'UTF-8'}";
  var StripePubKey = "{$publishableKey|escape:'htmlall':'UTF-8'}";
  var logo_url = "{$logo_url|escape:'htmlall':'UTF-8'}";
  var cu_email = "{$cu_email|escape:'htmlall':'UTF-8'}";
  var popup_locale = "{$popup_locale|escape:'htmlall':'UTF-8'}";
  var stripe_allow_zip = {if $stripe_allow_zip}true{else}false{/if};
</script>
</div>
