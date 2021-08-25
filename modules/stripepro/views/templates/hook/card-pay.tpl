{*
* 2015-2016 NTS
*
* DISCLAIMER
*
* You are NOT allowed to modify the software. 
* It is also not legal to do any changes to the software and distribute it in your own name / brand. 
*
* @author NTS
* @copyright  2015-2016 NTS
* @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
* International Registered Trademark & Property of NTS
*}

<div class="payment_module stripe-payment-17">
    <div id="card-token-success" class="alert alert-success" style="display:none">{l s='Payment token has been created successfully, now transaction is in progress...' mod='stripepro'}</div>
    <div id="stripe-ajax-loader"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/ajax-loader.gif" alt="" /> {l s='Do not press BACK or REFRESH while processing...' mod='stripepro'}</div>
     <div id="stripe-translations">
      <span id="stripe-incorrect_ownername">{l s='The card owner name is empty.' mod='stripepro'}</span>
      <span id="stripe-incorrect_number">{l s='The card number is incorrect.' mod='stripepro'}</span>
      <span id="stripe-invalid_number">{l s='The card number is not a valid credit card number.' mod='stripepro'}</span>
      <span id="stripe-invalid_expiry_month">{l s='The card\'s expiration month is invalid.' mod='stripepro'}</span>
      <span id="stripe-invalid_expiry_year">{l s='The card\'s expiration year is invalid.' mod='stripepro'}</span>
      <span id="stripe-invalid_cvc">{l s='The card\'s security code is invalid.' mod='stripepro'}</span>
      <span id="stripe-expired_card">{l s='The card has expired.' mod='stripepro'}</span>
      <span id="stripe-incorrect_cvc">{l s='The card\'s security code is incorrect.' mod='stripepro'}</span>
      <span id="stripe-incorrect_zip">{l s='The card\'s zip code failed validation.' mod='stripepro'}</span>
      <span id="stripe-card_declined">{l s='The card was declined.' mod='stripepro'}</span>
      <span id="stripe-missing">{l s='There is no card on a customer that is being charged.' mod='stripepro'}</span>
      <span id="stripe-processing_error">{l s='An error occurred while processing the card.' mod='stripepro'}</span>
      <span id="stripe-rate_limit">{l s='An error occurred due to requests hitting the API too quickly. Please let us know if you\'re consistently running into this error.' mod='stripepro'}</span>
      <span id="stripe-3d_declined">{l s='The card doesn\'t support 3DS.' mod='stripepro'}</span>
      <span id="stripe-no_api_key">{l s='There\'s an error with your API keys. If you\'re the administrator of this website, please go on the "Connection" tab of your plugin.' mod='stripepro'}</span>
      </div>

	<form action="#" method="POST" id="stripe-payment-form"{if $stripe_credit_card!=''} style="display: none;"{/if}>
		<div class="stripe-payment-errors">{if isset($smarty.get.stripe_error)}{$smarty.get.stripe_error|escape:'htmlall':'UTF-8'}{/if}</div>
        <a name="stripe_error" style="display:none"></a>
        <input type="hidden" id="stripe-publishable-key" value="{$publishableKey|escape:'htmlall':'UTF-8'}"/>
        <div>
          <label>{l s='Cardholder\'s Name' mod='stripepro'}</label>  <label class="required"> </label>
          <input type="text"  autocomplete="off" class="stripe-name" data-stripe="name" title="{$customer_name|escape:'htmlall':'UTF-8'}" value="{$customer_name|escape:'htmlall':'UTF-8'}"/>
        </div>
        <div>
          <label>{l s='Card Number ' mod='stripepro'}</label>  <label class="required"> </label>
          <input type="text" size="20" autocomplete="off" class="stripe-card-number" id="card_number" data-stripe="number" placeholder="&#9679;&#9679;&#9679;&#9679; &#9679;&#9679;&#9679;&#9679; &#9679;&#9679;&#9679;&#9679; &#9679;&#9679;&#9679;&#9679;"/>
        </div>
        <div style="width: 125px;float: left;">
          <label>{l s='Expiry date' mod='stripepro'}</label>  <label class="required"> </label>
          <input type="text" size="7" autocomplete="off" id="card_expiry" class="stripe-card-expiry" maxlength = 5 placeholder="MM/YY"/>
        </div>
        <div>
          <label>{l s='CVC/CVV' mod='stripepro'}</label>  <label class="required"> </label>
          <input type="text" size="7" autocomplete="off" data-stripe="cvc" class="stripe-card-cvc" placeholder="&#9679;&#9679;&#9679;"/>
          <a href="javascript:void(0)" class="stripe-card-cvc-info" style="border: none;">
          {l s='What\'s this?' mod='stripepro'}
            <div class="cvc-info">
              {l s='The CVC (Card Validation Code) is a 3 or 4 digit code on the reverse side of Visa, MasterCard and Discover cards and on the front of American Express cards.' mod='stripepro'}
            </div>
          </a>
        </div>
        <img class="powered_by_stripe" alt="Powered by Stripe" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/powered_by_stripe.png"/>
	</form>
</div>

<script type="text/javascript">
  var mode = {$stripe_mode|escape:'htmlall':'UTF-8'};
  var currency = "{$currency|escape:'htmlall':'UTF-8'}";
  var amount_ttl = {$amount_ttl|escape:'htmlall':'UTF-8'};
  var baseDir = "{$baseDir|escape:'htmlall':'UTF-8'}";
  var billing_address = {$billing_address|escape nofilter};
  var module_dir = "{$module_dir|escape:'htmlall':'UTF-8'}";
  var StripePubKey = "{$publishableKey|escape:'htmlall':'UTF-8'}";
</script>

