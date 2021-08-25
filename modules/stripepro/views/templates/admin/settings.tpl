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
            $(document).ready(function() { 
             $(".stripe-module-wrapper .list-group .list-group-item").click(function(){
                 
                 $(".list-group .list-group-item").removeClass("active");
                 $(this).addClass("active");
                 var ID = $(this).attr("id");
                 $(".stripe-module-wrapper fieldset").removeClass("show");
                 $(".stripe-module-wrapper fieldset."+ID).addClass("show");
                 });
             });
</script>
<link href="{$stripeBOCssUrl|escape:'htmlall':'UTF-8'}" rel="stylesheet" type="text/css">
{if $success}<div class="conf confirmation alert alert-success">{l s='Settings successfully saved' mod='stripepro'}</div>{/if}
            <div class="tabs stripe-module-wrapper">
            <div class="sidebar navigation col-md-3">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}/views/img/apple.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}/views/img/logo.gif">
            <nav class="list-group categorieList">
<a class="list-group-item active" id="technical_checkes" href="javascript:void();"><i class="icon-check-circle-o tabcbpfw-icon"></i>{l s='Technical Checks' mod='stripepro'}
<span class="badge-module-tabs pull-right {if $requirements['result']}tab-success{else}tab-warning{/if}"></span></a>
<a class="list-group-item" id="stripe_settings" href="javascript:void();"><i class="icon-power-off tabcbpfw-icon"></i>{l s='Stripe Connexion' mod='stripepro'}
<span class="badge-module-tabs pull-right {if $checkSettings}tab-success{else}tab-warning{/if}"></span></a>
<a class="list-group-item" id="stripe_checkout" href="javascript:void();"><i class="icon-star tabcbpfw-icon"></i>{l s='Stripe Checkout' mod='stripepro'}</a>
<a class="list-group-item" id="order_statuses" href="javascript:void();"><i class="icon-filter tabcbpfw-icon"></i>{l s='Order Statuses' mod='stripepro'}</a>
<a class="list-group-item" id="subs_products" href="javascript:void();"><i class="icon-barcode tabcbpfw-icon"></i>{l s='Subscription Products' mod='stripepro'}</a>
<a class="list-group-item" id="stripe_sync" href="javascript:void();"><i class="icon-refresh tabcbpfw-icon"></i>{l s='Synchronize Stripe Data' mod='stripepro'}</a>
<a class="list-group-item" id="stripe-cc-numbers" href="javascript:void();"><i class="icon-dollar tabcbpfw-icon"></i>{l s='Test Credit Card Numbers' mod='stripepro'}</a>
<a class="list-group-item" id="stripe_webhooks" href="javascript:void();"><i class="icon-link tabcbpfw-icon"></i>{l s='Stripe Webhooks' mod='stripepro'}</a>
            </nav>
            </div>
            <div class="panel content-wrap form-horizontal col-lg-9">
            <fieldset class="technical_checkes show">
            <h3 class="tab"> <i class="icon-check-circle-o"></i>&nbsp;{l s='Technical Checks' mod='stripepro'}</h3>
                <div class="{if $requirements['result']}conf confirmation alert alert-success">{l s='Good news! All the checks were successfully performed. You can now configure your module and start using Stripe.' mod='stripepro'}{else}
                error alert alert-danger">{l s='Unfortunately, at least one issue is preventing you from using Stripe. Please fix the issue and reload this page.' mod='stripepro'}{/if}</div><table cellspacing="0" cellpadding="0" class="stripe-technical">
                {foreach $requirements as $k => $requirement}
                    {if $k != 'result'}
                        <tr>
                            <td><img src="../img/admin/{if $requirement['result']}enabled{else}disabled{/if}.gif" alt="" />&nbsp;</td>
                            <td>{$requirement['name']|escape:'htmlall':'UTF-8'}
                            {if !$requirement['result'] && isset($requirement['resolution'])}<br />{$requirement['resolution']|escape:'htmlall':'UTF-8'}{/if}</td>
                        </tr>
                        {/if}
                  {/foreach}
                </table>
                    <div class="alert alert-info">
                    <strong>{l s='Minimum requirements to use Apple Pay:' mod='stripepro'}</strong><hr />
                    <ul>
                    <li>{l s='In Safari on an iOS device running iOS 10. Make sure that you have at least one card in your Wallet (you can add one by going to Settings → Wallet & Apple Pay).' mod='stripepro'}</li>
                    <li>{l s='In Safari on a Mac running macOS Sierra. You will also need an iOS device running iOS 10 with a card in its Wallet to be paired to your Mac via Handoff (instructions on how to do this can be found on' mod='stripepro'}&nbsp;<a href="https://support.apple.com/en-us/HT204681" target="_blank">Apple Support website.</a>)</li>
                    <li>{l s='Devices that support Apple Pay include iPhone 6 or newer, iPhone 6 Plus or newer, iPad Air 2, and iPad mini 3.' mod='stripepro'}</li>
                    <li>{l s='For testing Apple Pay, please set module in LIVE mode because in TEST mode you need to setup a SANDBOX TESTER ACCOUNT for your device which is a long process.' mod='stripepro'}</li>
                    </ul>
                    </div>
                    <div class="alert alert-info">
                    <strong>{l s='HOW RECURRING PAYMENT WORKS:' mod='stripepro'}</strong><hr />
                    <ul>
                    <li>{l s='Create recurring Plans in your Stripe account' mod='stripepro'}</li>
                    <li>{l s='Synchronize Plans from Stripe account using "Syncronize Stripe Data" tab of this module configuration page. It will import all the recurring plans from stripe.' mod='stripepro'}</li>
                    <li>{l s='On the product edit page, this module tab to add any stripe recurring plan to that product and set Enable Stripe Recurring Payments to YES.' mod='stripepro'}</li>
                    <li>{l s='If You want charge your customer for that product amount including subscription as well then set Enable Stripe Charge for this product to YES. Leave it to NO if you do not understand it.' mod='stripepro'}</li>
					<li>{l s='Then select an recurring plan from the list and press save. Now your product is ready to accept recurring payments.' mod='stripepro'}</li>
					<li>{l s='See Orders > Subscriptions page for all the subscriptions in your shop.' mod='stripepro'}</li>
					<li>{l s='You can CANCEL or ADD subscriptions from customer page in backoffice. It will only work if customer already placed any order.' mod='stripepro'}</li>
					<li>{l s='You can see All the order details with subscription on the Order page in backoffice.' mod='stripepro'}</li>
					<li>{l s='Auto cancel at period end -  this button will not cancel the subscription immediately but Stripe auto cancel it as its current period will end' mod='stripepro'}</li>
                    </ul>
                    </div><hr />
					<h1>If you need any additional support regarding this module then click <a href="https://addons.prestashop.com/contact-community.php?id_product=19407" target="_blank">Here</a></h1>
            </fieldset>

        {if !empty($errors)}
            <fieldset class="technical_checkes show">
                <legend>Errors</legend>
                <table cellspacing="0" cellpadding="0" class="stripe-technical">
                        <tbody>
                    {foreach $errors as $error} 
                        <tr>
                            <td><img src="../img/admin/status_red.png" alt=""></td>
                            <td>{$error|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                    {/foreach}
                </tbody></table>
            </fieldset>
        {/if}
        
        <form action="" method="post">
        <fieldset class="stripe_settings">
        <h3 class="tab"> <i class="icon-power-off"></i>&nbsp;{l s='Stripe Connexion' mod='stripepro'}</h3>
         <div class="form-group">
        <label class="control-label col-lg-5" for="simple_product">{l s='Test Secret Key' mod='stripepro'}:</label>
        <div class="col-lg-5">
            <input type="text" name="stripe_private_key_test" value="{Configuration::get('STRIPE_PRIVATE_KEY_TEST')|escape:'htmlall':'UTF-8'}" />
        </div></div>
        <div class="form-group">
        <label class="control-label col-lg-5" for="simple_product">{l s='Test Publishable Key' mod='stripepro'}:</label>
        <div class="col-lg-5">
                <input type="text" name="stripe_public_key_test" value="{Configuration::get('STRIPE_PUBLIC_KEY_TEST')|escape:'htmlall':'UTF-8'}" />
        </div></div>
         <div class="form-group">
        <label class="control-label col-lg-5" for="simple_product">{l s='Live Secret Key' mod='stripepro'}:</label>
        <div class="col-lg-5">
            <input type="text" name="stripe_private_key_live" value="{Configuration::get('STRIPE_PRIVATE_KEY_LIVE')|escape:'htmlall':'UTF-8'}" />
        </div></div>
        <div class="form-group">
        <label class="control-label col-lg-5" for="simple_product">{l s='Live Publishable Key' mod='stripepro'}:</label>
        <div class="col-lg-5">
                <input type="text" name="stripe_public_key_live" value="{Configuration::get('STRIPE_PUBLIC_KEY_LIVE')|escape:'htmlall':'UTF-8'}" />
        </div></div>
        <div class="form-group">
        <label class="control-label col-lg-5" for="simple_product">{l s='Transaction Mode' mod='stripepro'}:</label>
        <div class="col-lg-5">
                <select name="stripe_mode" style="width:auto">
                <option value="0"{if !Configuration::get('STRIPE_MODE')} selected="selected"{/if}>{l s='Test' mod='stripepro'}</option>
                <option value="1"{if Configuration::get('STRIPE_MODE')} selected="selected"{/if}>{l s='Live' mod='stripepro'}</option>
                </select>
        </div></div>
        <div class="form-group">
        <label class="control-label col-lg-5" for="simple_product"><span title="{l s='Choose whether to authorize payments and manually capture them later, or to both authorize and capture (i.e. fully charge) payments when orders are placed. You can capture a payment that is only Authorized by using Stripe payment tab for the order.' mod='stripepro'}" class="label-tooltip" data-toggle="tooltip" title=""> 
                {l s='Charge Mode' mod='stripepro'}:
            </span></label>
        <div class="col-lg-5">
                <select name="STRIPE_CAPTURE_TYPE" style="width:auto">
                <option value="0"{if !Configuration::get('STRIPE_CAPTURE_TYPE')} selected="selected"{/if}>{l s='Authorize Only' mod='stripepro'}</option>
                <option value="1"{if Configuration::get('STRIPE_CAPTURE_TYPE')} selected="selected"{/if}>{l s='Authorize & Capture' mod='stripepro'}</option>
                </select>
        </div></div>
        <div class="form-group">
        <label class="control-label col-lg-5" for="simple_product"><span>{l s='Use Apple Pay:' mod='stripepro'}</span></label>
        <div class="col-lg-5">
                <select name="STRIPE_ALLOW_APPLEPAY" style="width:auto">
                <option value="1"{if Configuration::get('STRIPE_ALLOW_APPLEPAY')} selected="selected"{/if}>{l s='Yes' mod='stripepro'}</option>
                <option value="0"{if !Configuration::get('STRIPE_ALLOW_APPLEPAY')} selected="selected"{/if}>{l s='No' mod='stripepro'}</option>
                </select>{l s='To use Apple Pay, you need to ' mod='stripepro'}<br /><a href="https://dashboard.stripe.com/account/apple_pay" class="btc_link" target="_blank">{l s='add your domain in your Stripe account' mod='stripepro'}</a>
        </div></div> 
        <div class="form-group">
        <label class="control-label col-lg-5" for="simple_product"><span title="{l s='Give customers the option to cancel the subscription anytime from their account.' mod='stripepro'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='Allow Customers to Cancel subscriptions' mod='stripepro'}:</span></label>
        <div class="col-lg-5">
                <select name="STRIPE_SUBS_CANCEL_OPTN" style="width:auto">
                <option value="1"{if Configuration::get('STRIPE_SUBS_CANCEL_OPTN')} selected="selected"{/if}>{l s='Yes' mod='stripepro'}</option>
                <option value="0"{if !Configuration::get('STRIPE_SUBS_CANCEL_OPTN')} selected="selected"{/if}>{l s='No' mod='stripepro'}</option>
                </select>
        </div></div>
        <div class="form-group">
        <label class="control-label col-lg-5" for="simple_product"><span title="{l s='It creates a new order with same reference for successful recurring payments.' mod='stripepro'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='Create new order on successful recurring payment' mod='stripepro'}:</span></label>
        <div class="col-lg-5">
                <select name="STRIPE_SUBS_PAYMENT_ORDER_NEW" style="width:auto">
                <option value="1"{if Configuration::get('STRIPE_SUBS_PAYMENT_ORDER_NEW')} selected="selected"{/if}>{l s='Yes' mod='stripepro'}</option>
                <option value="0"{if !Configuration::get('STRIPE_SUBS_PAYMENT_ORDER_NEW')} selected="selected"{/if}>{l s='No' mod='stripepro'}</option>
                </select>{l s='Webhook URL setup in your Stripe Dashborad required for this action.' mod='stripepro'}
        </div></div>
         <div class="form-group">
        <label class="control-label col-lg-5" for="simple_product">{l s='E-mail customer on subscription cancel' mod='stripepro'}:</label>
        <div class="col-lg-5">
                <select name="STRIPE_SUBS_CANCEL_MAIL" style="width:auto">
                <option value="1"{if Configuration::get('STRIPE_SUBS_CANCEL_MAIL')} selected="selected"{/if}>{l s='Yes' mod='stripepro'}</option>
                <option value="0"{if !Configuration::get('STRIPE_SUBS_CANCEL_MAIL')} selected="selected"{/if}>{l s='No' mod='stripepro'}</option>
                </select>
        </div></div>
        <div class="form-group">
        <label class="control-label col-lg-5" for="simple_product"><span title="{l s='Specify whether Checkout should validate the billing ZIP code.' mod='stripepro'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='ZipCode Verification' mod='stripepro'}:</span></label>
        <div class="col-lg-5">
                <select name="STRIPE_ALLOW_ZIP" style="width:auto">
                <option value="1"{if Configuration::get('STRIPE_ALLOW_ZIP')} selected="selected"{/if}>{l s='Yes' mod='stripepro'}</option>
                <option value="0"{if !Configuration::get('STRIPE_ALLOW_ZIP')} selected="selected"{/if}>{l s='No' mod='stripepro'}</option>
                </select>
        </div></div>
        <div class="form-group">
                <label class="control-label col-lg-5" for="simple_product"><span title="{l s='If you want to notify your customers for each successful payment then you need to enable it in your Stripe account.' mod='stripepro'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='Email receipts to your customers for successful payment charges or refunds' mod='stripepro'}:</span></label>
                <div class="col-lg-4">
                    <a class="button btn btn-primary" href="https://dashboard.stripe.com/account/emails" target="_blank">{l s='Click here to enable' mod='stripepro'}</a>
                </div></div>
        <div class="panel-footer">
                <button type="submit" name="SubmitStripe" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='stripepro'}</button>
            </div>
        </fieldset>
        </form>
        <form method="post" action="">
            <fieldset class="stripe_checkout">
            <h3 class="tab"> <i class="icon-star"></i>&nbsp;{l s='Stripe Checkout' mod='stripepro'}</h3>
         <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product"><span title="{l s='Its a stripe hosted secure and device friendly checkout form. Stripe Checkout is the best payment flow, on web and mobile' mod='stripepro'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='Use Stripe Checkout Pop-up' mod='stripepro'}:</span></label>
        <div class="col-lg-5">
            <select name="STRIPE_CHKOUT_POPUP" style="width:auto">
                <option value="1"{if Configuration::get('STRIPE_CHKOUT_POPUP')} selected="selected"{/if}>{l s='Yes' mod='stripepro'}</option>
                <option value="0"{if !Configuration::get('STRIPE_CHKOUT_POPUP')} selected="selected"{/if}>{l s='No' mod='stripepro'}</option>
                </select>
        </div></div>
        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product"><span title="{l s='A relative URL pointing to a square image of your brand or product. The recommended minimum size is 128x128px. The recommended image types are .gif, .jpeg, and .png.' mod='stripepro'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='Pop-up Logo' mod='stripepro'}:</span></label>
        <div class="col-lg-5">
            <input type="text" name="STRIPE_POPUP_LOGO" value="{$logo_url|escape:'htmlall':'UTF-8'}" style="max-width: 350px;" />
            <br /><img src="{$logo_url|escape:'htmlall':'UTF-8'}" alt="stripe logo" style="max-width: 350px;">
        </div></div>
        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product"><span title="{l s='The name of your company or website.' mod='stripepro'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='Pop-up Title' mod='stripepro'}:</span></label>
        <div class="col-lg-5">
            <input type="text" name="STRIPE_POPUP_TITLE" value="{if !Configuration::get('STRIPE_POPUP_TITLE')}{Configuration::get('PS_SHOP_NAME')|escape:'htmlall':'UTF-8'}{else}{Configuration::get('STRIPE_POPUP_TITLE')|escape:'htmlall':'UTF-8'}{/if}" style="max-width: 350px;" />
        </div></div>
        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product"><span title="{l s='A description of the product or service being purchased.' mod='stripepro'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='Pop-up Description' mod='stripepro'}:</span></label>
        <div class="col-lg-5">
            <input type="text" name="STRIPE_POPUP_DESC" value="{if !Configuration::get('STRIPE_POPUP_DESC')}{l s='Complete your transaction' mod='stripepro'}{else}{Configuration::get('STRIPE_POPUP_DESC')|escape:'htmlall':'UTF-8'}{/if}" style="max-width: 350px;" />
        </div></div>
        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product"><span title="{l s='We recommend letting Checkout automatically select a language based on the user’s browser configuration by passing “auto”.' mod='stripepro'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='Pop-up language' mod='stripepro'}:</span></label>
        <div class="col-lg-5">
            <select name="STRIPE_POPUP_LOCALE" style="width:auto">
                <option value="auto"{if !Configuration::get('STRIPE_POPUP_LOCALE') || !Configuration::get('STRIPE_POPUP_LOCALE')=='auto'} selected="selected"{/if}>{l s='Auto' mod='stripepro'}</option>
                <option value="zh"{if Configuration::get('STRIPE_POPUP_LOCALE')=='zh'} selected="selected"{/if}>{l s='Chinese (zh)' mod='stripepro'}</option>
                <option value="nl"{if Configuration::get('STRIPE_POPUP_LOCALE')=='nl'} selected="selected"{/if}>{l s='Dutch (nl)' mod='stripepro'}</option>
                <option value="en"{if Configuration::get('STRIPE_POPUP_LOCALE')=='en'} selected="selected"{/if}>{l s='English (en)' mod='stripepro'}</option>
                <option value="fr"{if Configuration::get('STRIPE_POPUP_LOCALE')=='fr'} selected="selected"{/if}>{l s='French (fr)' mod='stripepro'}</option>
                <option value="de"{if Configuration::get('STRIPE_POPUP_LOCALE')=='de'} selected="selected"{/if}>{l s='German (de)' mod='stripepro'}</option>
                <option value="it"{if Configuration::get('STRIPE_POPUP_LOCALE')=='it'} selected="selected"{/if}>{l s='Italian (it)' mod='stripepro'}</option>
                <option value="ja"{if Configuration::get('STRIPE_POPUP_LOCALE')=='ja'} selected="selected"{/if}>{l s='Japanese (ja)' mod='stripepro'}</option>
                <option value="es"{if Configuration::get('STRIPE_POPUP_LOCALE')=='es'} selected="selected"{/if}>{l s='Spanish (es)' mod='stripepro'}</option>
                <option value="da"{if Configuration::get('STRIPE_POPUP_LOCALE')=='da'} selected="selected"{/if}>{l s='Danish (da)' mod='stripepro'}</option>
                <option value="fi"{if Configuration::get('STRIPE_POPUP_LOCALE')=='fi'} selected="selected"{/if}>{l s='Finnish (fi)' mod='stripepro'}</option>
                <option value="no"{if Configuration::get('STRIPE_POPUP_LOCALE')=='no'} selected="selected"{/if}>{l s='Norwegian (no)' mod='stripepro'}</option>
                <option value="sv"{if Configuration::get('STRIPE_POPUP_LOCALE')=='sv'} selected="selected"{/if}>{l s='Swedish (sv)' mod='stripepro'}</option>
                </select>
        </div></div>
         <div class="panel-footer">
                <button type="submit" name="SubmitStripeCheckout" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='stripepro'}</button>
            </div>
            </fieldset>
            </form>
            
           <form method="post" action="">
            <fieldset class="order_statuses">
            <h3 class="tab"><i class="icon-filter"></i>&nbsp;{l s='Order Statuses' mod='stripepro'}</h3>
                
                    {foreach $statuses_options as $status_options}
                  
                        <div class="form-group">
                         <label class="control-label col-lg-6" for="simple_product">{$status_options['label']|escape:'htmlall':'UTF-8'}</label>
                            <div class="col-lg-5">
                                <select name="{$status_options['name']|escape:'htmlall':'UTF-8'}" style="width:auto">';
                                    {foreach $statuses as $status}
                                        <option value="{$status['id_order_state']|escape:'htmlall':'UTF-8'}"{if $status['id_order_state'] == $status_options['current_value']} selected="selected"{/if}>{$status['name']|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                </select>
                            </div></div>
                    {/foreach}

          <div class="panel-footer">
                <button type="submit" name="SubmitOrderStatuses" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='stripepro'}</button>
            </div>
            </fieldset>
            </form>
          
            <form method="post" action="">
            <fieldset class="subs_products">
            <h3 class="tab"><i class="icon-barcode"></i>&nbsp;{l s='Subscription Products' mod='stripepro'} ({count($subs_products)|escape:'htmlall':'UTF-8'})</h3>
                <table cellspacing="0" cellpadding="0" class="stripe-cc-numbers" width="100%">
                  <thead>
                    <tr>
                    <th>{l s='Product ID' mod='stripepro'}</th>
                      <th>{l s='Product Name' mod='stripepro'}</th>
                      <th>{l s='Recurring Period' mod='stripepro'}</th>
                      <th>{l s='Action' mod='stripepro'}</th>
                    </tr>
                  </thead>
                  <tbody>
                  
        {foreach $subs_products as $sub}
          <tr><td>{$sub['id_product']|escape:'htmlall':'UTF-8'}</td><td>{$sub['name']|escape:'htmlall':'UTF-8'}</td><td class="number"><code>{$sub['id_subscription_product']|escape:'htmlall':'UTF-8'} Month</code></td><td><a class="edit btn btn-default" target="_Blank" href="{$sub['editLink']|escape:'htmlall':'UTF-8'}#tab-hooks">
        <i class="icon-pencil"></i>&nbsp;{l s='Edit' mod='stripepro'}</a></td></tr>
        {/foreach}
        {if count($subs_products)==0}
        <tr><td class="number" colspan="2"><code>{l s='No record found.' mod='stripepro'}</code></td></tr>
        {/if}
        </tbody>
                </table>
            </fieldset>
            </form>
            
             <form method="post" action="">
            <fieldset class="stripe_sync">
            <h3 class="tab"> <i class="icon-refresh"></i>&nbsp;{l s='Syncronize Stripe Data' mod='stripepro'}</h3>
                <div class="form-group">
                <label class="control-label col-lg-6" for="simple_product"><span title="{l s='You can create new recurring plan in your Stripe account.' mod='stripepro'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='To create a new Recurring Plan in your Stripe account.' mod='stripepro'}:</span></label>
                <div class="col-lg-4">
                    <a class="button btn btn-default" href="https://dashboard.stripe.com/plans" target="_blank">{l s='Click here' mod='stripepro'}</a>
                </div></div>
                <div class="form-group">
                <label class="control-label col-lg-6" for="simple_product"><span title="{l s='Total number of Stripe Recurring Plans in the shop is ' mod='stripepro'}{$plans|escape:'htmlall':'UTF-8'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='To Synchronize all the Recurring Plans from your Stripe account' mod='stripepro'} ({$plans|escape:'htmlall':'UTF-8'}):</span></label>
                <div class="col-lg-4">
                    <input type="submit" class="button btn btn-primary" onclick="return confirm({l s='Do you want to proceed to update the StripePlans List?' mod='stripepro'});" name="SubmitListPlans" value="{l s='Click here' mod='stripepro'}" />
                </div></div>
                <div class="form-group">
                <label class="control-label col-lg-6" for="simple_product"><span title="{l s='Total number of subscriptions in the shop is ' mod='stripepro'}{$subs|escape:'htmlall':'UTF-8'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='To Update all the Subscriptions data in the shop' mod='stripepro'} ({$subs|escape:'htmlall':'UTF-8'}):</span></label>
                <div class="col-lg-4">
                    <input type="submit" class="button btn btn-primary" onclick="return confirm({l s='Do you want to proceed to update the Subscriptions of all stripe customers?' mod='stripepro'});" name="SubmitSubSync" value="{l s='Click here' mod='stripepro'}" />
                </div></div>
            </fieldset>
            </form>
            
            <form method="post" action="">
            <div class="clear"></div>
            <fieldset class="stripe_webhooks">
            <h3 class="tab"><i class="icon-link"></i>&nbsp;&nbsp;{l s='Stripe Webhooks' mod='stripepro'}</h3>
            <div class="alert alert-warning warn">{l s='On receiving the below 1. & 2. event type information from stripe, Module Will change the Order status to the selected one in the "Order STATUSES" tab of this module.' mod='stripepro'}</div>
            <h4>{l s='Webhook receives the following information from Stripe:' mod='stripepro'}</h4>
            <ol>
               <li>{l s='Chargeback information. (Stripe Event type: ' mod='stripepro'}<b>charge.dispute.created</b>)</li>
               <li>{l s='Subscription payment confirmation. (Stripe Event type: ' mod='stripepro'}<b>invoice.payment_succeeded</b>)</li>
               <li>{l s='Customer subscription updated, deleted or trial end. (Stripe Event type: ' mod='stripepro'}<b>customer.subscription.updated, customer.subscription.deleted, customer.subscription.trial_will_end</b>)</li></ol>
                {l s='In Order to receive above information from Stripe, Setup the following Webhook link in Stripe\'s admin panel:' mod='stripepro'}&nbsp;<a href="https://dashboard.stripe.com/account/webhooks" target="_blank" class="button btn btn-primary">{l s='here' mod='stripepro'}</a><br />
              <strong>{$webhook_url|escape:'htmlall':'UTF-8'}</strong>
            </fieldset>
        </form>
        
            <form method="post" action="">
            <fieldset class="stripe-cc-numbers">
               <h3 class="tab"><i class="icon-dollar"></i>&nbsp;{l s='Test Credit Card Numbers' mod='stripepro'}</h3>
                <table cellspacing="0" cellpadding="0" class="stripe-cc-numbers" width="100%">
                  <thead>
                    <tr>
                      <th>{l s='Number' mod='stripepro'}</th>
                      <th>{l s='Card type' mod='stripepro'}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr><td class="number"><code>4242424242424242</code></td><td>Visa</td></tr>
                    <tr><td class="number"><code>5555555555554444</code></td><td>MasterCard</td></tr>
                    <tr><td class="number"><code>378282246310005</code></td><td>American Express</td></tr>
                    <tr><td class="number"><code>6011111111111117</code></td><td>Discover</td></tr>
                    <tr><td class="number"><code>30569309025904</code></td><td>Diner's Club</td></tr>
                    <tr><td class="number last"><code>3530111333300000</code></td><td class="last">JCB</td></tr>
                  </tbody>
                </table>
            </fieldset>
            </form>
    </div></div>