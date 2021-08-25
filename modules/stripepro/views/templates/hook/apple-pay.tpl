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

<script type="text/javascript">
  var mode = {$stripe_mode|escape:'htmlall':'UTF-8'};
  var baseDir = "{$baseDir|escape:'htmlall':'UTF-8'}";
  var country_iso_code = "{$country_iso_code|escape:'htmlall':'UTF-8'}";
  var currency = "{$currency|escape:'htmlall':'UTF-8'}";
  var amount_ttl = {$amount_ttl|escape:'htmlall':'UTF-8'};
  var popup_title = "{$popup_title|escape:'htmlall':'UTF-8'}";
  var popup_desc = "{$popup_desc|escape:'htmlall':'UTF-8'}";
  var StripePubKey = "{$publishableKey|escape:'htmlall':'UTF-8'}";
  var apple_pay_cart_total = {$apple_pay_cart_total|escape:'htmlall':'UTF-8'};
  var stripe_allow_applepay = {if $stripe_allow_applepay}true{else}false{/if};
  var applepay_alert = "{l s='Your device does not qualify for Apple Pay, use another Apple device.' mod='stripepro'}";
</script>
<div class="payment_module">
<div id="stripe-ajax-loader-applepay" style="display:none;"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/ajax-loader.gif" alt="" /> {l s='Do not press BACK or REFRESH while processing...' mod='stripepro'}</div>
<div id="apple-pay-success" class="alert alert-success">{l s='Your device is ready to accept payment from Apple Pay.' mod='stripepro'}</div>
<div id="apple-pay-alert" class="alert alert-warning">{l s='Your device does not qualify for Apple Pay, meet the requirements' mod='stripepro'} <a href="https://support.apple.com/en-in/KM207105" target="_blank"><b><u>{l s='here' mod='stripepro'}</u></b></a></div>
</div>