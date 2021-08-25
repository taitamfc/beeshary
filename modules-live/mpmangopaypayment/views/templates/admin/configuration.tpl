{*
* 2010-2018 Webkul
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

{if isset($unauthorization)}
    <div class="alert alert-danger">{l s='Unauthorized credentials' mod='mpmangopaypayment'}.</div>
{/if}
<div class="panel">
    <h3><i class="icon-cog">&nbsp;&nbsp;</i>{l s='Mangopay Account Configuration' mod='mpmangopaypayment'}</h3>
    <h2>{l s='Have you opened a Mangopay account?' mod='mpmangopaypayment'}</h2>
    <p>
        {l s='You can create an account by clicking this link : ' mod='mpmangopaypayment'}
        <a href="https://www.mangopay.com/signup/" target="_blank">{l s='https://www.mangopay.com/signup/' mod='mpmangopaypayment'}</a>.
    </p>
    <h2>{l s='Connect this module to your Mangopay account' mod='mpmangopaypayment'}</h2>
    {if $moduleInstalled}
        <br />
        <form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}">
            <div class="form-group row">
                <label class="col-lg-3 col-md-3 col-xs-6 text-right required">{l s='Passphrase' mod='mpmangopaypayment'} </label>
                <div class="col-lg-4 col-md-4 col-xs-6 col-offset-lg-6">
                    <input type="text" name="passphrase" id="passphrase" value="{if isset($passphrase)}{$passphrase|escape:'htmlall':'UTF-8'}{/if}" autocomplete="Off" class="form-control">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-3 col-md-3 col-xs-6 text-right required">{l s='Client ID' mod='mpmangopaypayment'} </label>
                <div class="col-lg-4 col-md-4 col-xs-6 col-offset-lg-6">
                    <input type="text" name="clientid" id="clientid" value="{if isset($clientid)}{$clientid|escape:'htmlall':'UTF-8'}{/if}" autocomplete="Off" class="form-control">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-3 col-md-3 col-xs-6 text-right required">{l s='Credit Card Type' mod='mpmangopaypayment'} </label>
                <div class="col-lg-4 col-md-4 col-xs-6 col-offset-lg-6">
                    <select id="mgp_creditcard" name="creditcard[]" multiple>
                        {foreach from=$direct_debit_supp_cards key=card_name item=card_code}
                            <option value="{$card_name|escape:'htmlall':'UTF-8'}" {foreach from=$creditcard key=card item=card_val}{if $card == $card_name}selected{/if}{/foreach}>{$card_name|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="help-block col-lg-6 col-lg-offset-3">
                    {l s='Note: Przelewy24, iDeal, PayLib will only use on PayIn Web.' mod='mpmangopaypayment'}
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-3 col-md-3 col-xs-6 text-right">
                    {l s='Allowed Currency' mod='mpmangopaypayment'}
                </label>
                <div class="col-lg-4 col-md-4 col-xs-6 col-offset-lg-6">
                    <select id="mgp_currency" name="currency">
                        <option value="USD" {if $currency == 'USD'}selected{/if}>{l s='USD' mod='mpmangopaypayment'}</option>
                        <option value="EUR" {if $currency == 'EUR'}selected{/if}>{l s='EUR' mod='mpmangopaypayment'}</option>
                        <option value="GBP" {if $currency == 'GBP'}selected{/if}>{l s='GBP' mod='mpmangopaypayment'}</option>
                        <option value="PLN" {if $currency == 'PLN'}selected{/if}>{l s='PLN' mod='mpmangopaypayment'}</option>
                        <option value="CHF" {if $currency == 'CHF'}selected{/if}>{l s='CHF' mod='mpmangopaypayment'}</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-3 col-md-3 col-xs-6 text-right">
                    {l s='Mode' mod='mpmangopaypayment'}
                </label>
                <div class="col-lg-4 col-md-4 col-xs-6 col-offset-lg-6">
                    <select name="mode" id="mode">
                        <option value="sandbox" {if $mode == 'sandbox'}selected{/if}>{l s='Sandbox' mod='mpmangopaypayment'}</option>
                        <option value="production" {if $mode == 'production'}selected{/if}>{l s='Production' mod='mpmangopaypayment'}</option>
                    </select>
                </div>
            </div>
            <div class="panel-footer">
                <button type="submit" name="mgp_api_configuration" class="btn btn-default pull-right" id="mgp_data">
                    <i class="process-icon-save"></i> {l s='Save & Generate Mangopay User' mod='mpmangopaypayment'}
                </button>
            </div>
        </form>
    {/if}
</div>

<div class="panel">
    <h3><i class="icon-AdminTools"></i>&nbsp;&nbsp;{l s='Mangopay General Configuration' mod='mpmangopaypayment'}</h3>
    <form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}">
        <div class="form-group row">
            <label class="control-label col-lg-3 text-right">
                <span class="label-tooltip" data-toggle="tooltip" title="{l s='Show mangopay card payment option to the customers. With this method customer can pay with his credit|debit cards.' mod='mpmangopaypayment'}">
                    {l s='Enable Mangopay Card Payment' mod='mpmangopaypayment'}
                </span>
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="wk_mangopay_card_pay_enable" id="wk_mangopay_card_pay_enable_on" value="1" {if $wk_mangopay_card_pay_enable|intval}checked="checked"{/if}/>
                    <label for="wk_mangopay_card_pay_enable_on">{l s='Yes' mod='mpmangopaypayment'}</label>
                    <input type="radio" name="wk_mangopay_card_pay_enable" id="wk_mangopay_card_pay_enable_off" value="0" {if !$wk_mangopay_card_pay_enable|intval}checked="checked"{/if} />
                    <label for="wk_mangopay_card_pay_enable_off">{l s='No' mod='mpmangopaypayment'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
        <div class="form-group row wk_mgp_save_card_div {if !$wk_mangopay_card_pay_enable}hidden{/if}">
            <label class="control-label col-lg-3 text-right">
                <span class="label-tooltip" data-toggle="tooltip" title="{l s='Enable save card feature for the customers. So that they can be able to make payments with saved cards through mangopay card payment.' mod='mpmangopaypayment'}">
                    {l s='Enable Save Card Feature' mod='mpmangopaypayment'}
                </span>
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="wk_mangopay_save_cards_enable" id="wk_mangopay_save_cards_enable_on" value="1" {if $wk_mangopay_save_cards_enable|intval}checked="checked"{/if}/>
                    <label for="wk_mangopay_save_cards_enable_on">{l s='Yes' mod='mpmangopaypayment'}</label>
                    <input type="radio" name="wk_mangopay_save_cards_enable" id="wk_mangopay_save_cards_enable_off" value="0" {if !$wk_mangopay_save_cards_enable|intval}checked="checked"{/if} />
                    <label for="wk_mangopay_save_cards_enable_off">{l s='No' mod='mpmangopaypayment'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
        <div class="form-group row">
            <label class="control-label col-lg-3 text-right">
                <span class="label-tooltip" data-toggle="tooltip" title="{l s='Show mangopay direct debit payment option to the customers. With this method customer can pay with his bank details' mod='mpmangopaypayment'}">
                    {l s='Enable Mangopay Direct Debit Payment' mod='mpmangopaypayment'}
                </span>
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="wk_mangopay_direct_debit_enable" id="wk_mangopay_direct_debit_enable_on" value="1" {if $wk_mangopay_direct_debit_enable|intval}checked="checked"{/if}/>
                    <label for="wk_mangopay_direct_debit_enable_on">{l s='Yes' mod='mpmangopaypayment'}</label>
                    <input type="radio" name="wk_mangopay_direct_debit_enable" id="wk_mangopay_direct_debit_enable_off" value="0" {if !$wk_mangopay_direct_debit_enable|intval}checked="checked"{/if} />
                    <label for="wk_mangopay_direct_debit_enable_off">{l s='No' mod='mpmangopaypayment'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
        <div class="form-group row wk_mgp_save_bank_account_div {if !$wk_mangopay_direct_debit_enable}hidden{/if}">
            <label class="control-label col-lg-3 text-right">
                <span class="label-tooltip" data-toggle="tooltip" title="{l s='Enable save bank account feature for the customers on direct debit payments. So that they can be able to make payment with their saved bank accounts.' mod='mpmangopaypayment'}">
                    {l s='Enable Save Bank Account Feature' mod='mpmangopaypayment'}
                </span>
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="wk_mangopay_save_bank_account_enable" id="wk_mangopay_save_bank_account_enable_on" value="1" {if $wk_mangopay_save_bank_account_enable|intval}checked="checked"{/if}/>
                    <label for="wk_mangopay_save_bank_account_enable_on">{l s='Yes' mod='mpmangopaypayment'}</label>
                    <input type="radio" name="wk_mangopay_save_bank_account_enable" id="wk_mangopay_save_bank_account_enable_off" value="0" {if !$wk_mangopay_save_bank_account_enable|intval}checked="checked"{/if} />
                    <label for="wk_mangopay_save_bank_account_enable_off">{l s='No' mod='mpmangopaypayment'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
        <div class="form-group row">
            <label class="control-label col-lg-3 text-right">
                <span class="label-tooltip" data-toggle="tooltip" title="{l s='Show mangopay bankwire payment option to the customers' mod='mpmangopaypayment'}">
                    {l s='Enable Mangopay Bankwire Payment' mod='mpmangopaypayment'}
                </span>
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="wk_mangopay_bankwire_enable" id="wk_mangopay_bankwire_enable_on" value="1" {if $wk_mangopay_bankwire_enable|intval}checked="checked"{/if}/>
                    <label for="wk_mangopay_bankwire_enable_on">{l s='Yes' mod='mpmangopaypayment'}</label>
                    <input type="radio" name="wk_mangopay_bankwire_enable" id="wk_mangopay_bankwire_enable_off" value="0" {if !$wk_mangopay_bankwire_enable|intval}checked="checked"{/if} />
                    <label for="wk_mangopay_bankwire_enable_off">{l s='No' mod='mpmangopaypayment'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
        <div class="form-group row">
            <label class="control-label col-lg-3 text-right">
                <span class="label-tooltip" data-toggle="tooltip" title="{l s='On which order status will the money be transfered from the customer\'s wallet to admin\'s wallet.' mod='mpmangopaypayment'}">
                    {l s='Order Status For Transfer ' mod='mpmangopaypayment'}
                </span>
            </label>
            <div class="col-lg-2 col-md-2 col-xs-6">
                <select name="mgp_transfer_order_status">
                    <option value="2" {if $wallet_transf_status == 2}selected{/if}>{l s='Payment Accepted' mod='mpmangopaypayment'}</option>
                    <option value="5" {if $wallet_transf_status == 5}selected{/if}>{l s='Delivered' mod='mpmangopaypayment'}</option>
                </select>
            </div>
            <div class="help-block col-lg-7">
                {l s='Note' mod='mpmangopaypayment'} : {l s='This setting is only for mangopay card payments' mod='mpmangopaypayment'}
            </div>
        </div>
        <div class="form-group row">
            <label class="control-label col-lg-3 text-right">
                <span class="label-tooltip" data-toggle="tooltip" title="{l s='which type of payment you want for your customers.' mod='mpmangopaypayment'}">
                    {l s='Mangopay \'Card Payment\' Type ' mod='mpmangopaypayment'}
                </span>
            </label>
            <div class="col-lg-2 col-md-2 col-xs-6">
                <select name="mgp_payin_type">
                    <option value="1" {if $mgp_payin_type == 1}selected{/if}>{l s='Direct Payment' mod='mpmangopaypayment'}</option>
                    <option value="2" {if $mgp_payin_type == 2}selected{/if}>{l s='PayIn Web' mod='mpmangopaypayment'}</option>
                </select>
            </div>
        </div>
        <div class="form-group row" id="wksellertransfer">
            <label class="control-label col-lg-3 text-right">
                <span class="label-tooltip" data-toggle="tooltip" title="{l s='When refunding mangopay transfers, amount should be transfered to wallet or buyer\'s card ?' mod='mpmangopaypayment'}">
                    {l s='Transfer Refund Amount To' mod='mpmangopaypayment'}
                </span>
            </label>
            <div class="col-lg-2 col-md-2 col-xs-6">
                <select name="mgp_transfer_in">
                    <option value="1" {if $mgp_transfer_in == 1}selected{/if}>{l s='Wallet' mod='mpmangopaypayment'}</option>
                    <option value="2" {if $mgp_transfer_in == 2}selected{/if}>{l s='Card' mod='mpmangopaypayment'}</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="control-label col-lg-3 text-right">
                <span class="label-tooltip" data-toggle="tooltip" title="{l s='Previous data will be deleted or not on uninstalling the module' mod='mpmangopaypayment'}">
                    {l s='Data Delete On Uninstall' mod='mpmangopaypayment'}
                </span>
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="mangopay_payment_data_delete" id="mangopay_payment_data_delete_on" value="1" {if $mangopay_payment_data_delete|intval}checked="checked"{/if}/>
                    <label for="mangopay_payment_data_delete_on">{l s='Yes' mod='mpmangopaypayment'}</label>
                    <input type="radio" name="mangopay_payment_data_delete" id="mangopay_payment_data_delete_off" value="0" {if !$mangopay_payment_data_delete|intval}checked="checked"{/if} />
                    <label for="mangopay_payment_data_delete_off">{l s='No' mod='mpmangopaypayment'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" name="submit_general_mgp_conf" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save' mod='mpmangopaypayment'}
            </button>
        </div>
    </form>
</div>

<div class="panel">
    <h3><i class="icon-users"></i>&nbsp;&nbsp;{l s='Mangopay Seller Configuration' mod='mpmangopaypayment'}</h3>
    <form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}"  {if not $displayForm}style="display: none;"{/if}>
        <div class="form-group row">
            <label class="control-label col-lg-3 text-right">
                <span class="label-tooltip" data-toggle="tooltip" title="{l s='If yes, Seller will be able to payout amount from his wallet.' mod='mpmangopaypayment'}">
                    {l s='Seller Cash-Out' mod='mpmangopaypayment'}
                </span>
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="is_seller_cashout" id="cashout_on" value="1" {if $is_seller_cashout|intval}checked="checked"{/if}/>
                    <label class="t" for="cashout_on">{l s='Yes' mod='mpmangopaypayment'}</label>
                    <input type="radio" name="is_seller_cashout" id="cashout_off" value="0" {if !$is_seller_cashout|intval}checked="checked"{/if}/>
                    <label class="t" for="cashout_off">{l s='No' mod='mpmangopaypayment'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
        <div class="form-group row">
            <label class="control-label col-lg-3 text-right">
                <span class="label-tooltip" data-toggle="tooltip" title="{l s='If yes, Seller will be able to refund his mangopay transfers.' mod='mpmangopaypayment'}">
                    {l s='Seller Refund' mod='mpmangopaypayment'}
                </span>
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="is_seller_refund" id="refund_on" value="1" {if $is_seller_refund|intval}checked="checked"{/if}/>
                    <label class="t" for="refund_on">{l s='Yes' mod='mpmangopaypayment'}</label>
                    <input type="radio" name="is_seller_refund" id="refund_off" value="0" {if !$is_seller_refund|intval}checked="checked"{/if}/>
                    <label class="t" for="refund_off">{l s='No' mod='mpmangopaypayment'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
        <div class="form-group row">
            <label class="control-label col-lg-3 text-right">
                <span class="label-tooltip" data-toggle="tooltip" title="{l s='If yes, Seller will be able to add his bank details to the mangopay.' mod='mpmangopaypayment'}">
                    {l s='Seller Bank Details' mod='mpmangopaypayment'}
                </span>
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="is_seller_bank_dtl" id="bank_dtl_on" value="1" {if $is_seller_bank_dtl|intval}checked="checked"{/if}/>
                    <label for="bank_dtl_on">{l s='Yes' mod='mpmangopaypayment'}</label>
                    <input type="radio" name="is_seller_bank_dtl" id="bank_dtl_off" value="0" {if !$is_seller_bank_dtl|intval}checked="checked"{/if} />
                    <label for="bank_dtl_off">{l s='No' mod='mpmangopaypayment'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" name="submit_seller_mgp_conf" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save' mod='mpmangopaypayment'}
            </button>
        </div>
    </form>
</div>

<div class="panel">
    <h3><i class="icon-bell"></i>&nbsp;&nbsp;{l s='Mail Configuration' mod='mpmangopaypayment'}</h3>
    <form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}"  {if not $displayForm}style="display: none;"{/if}>
         <div class="form-group row">
            <label class="control-label col-lg-3 text-right">
                <span class="label-tooltip" data-toggle="tooltip" title="{l s='If enabled, seller will receive a mail when admin refunds seller\'s transaction.' mod='mpmangopaypayment'}">
                    {l s='Mail to seller when refund by admin' mod='mpmangopaypayment'}
                </span>
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="is_mail_refundby_admin" id="is_mail_refundby_admin_on" value="1" {if $is_mail_refundby_admin|intval}checked="checked"{/if}/>
                    <label for="is_mail_refundby_admin_on">{l s='Yes' mod='mpmangopaypayment'}</label>
                    <input type="radio" name="is_mail_refundby_admin" id="is_mail_refundby_admin_off" value="0" {if !$is_mail_refundby_admin|intval}checked="checked"{/if} />
                    <label for="is_mail_refundby_admin_off">{l s='No' mod='mpmangopaypayment'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>

         <div class="form-group row">
            <label class="control-label col-lg-3 text-right">
                <span class="label-tooltip" data-toggle="tooltip" title="{l s='If enabled, admin will receives a mail when seller will refund from his dashboard.' mod='mpmangopaypayment'}">
                    {l s='Mail to admin when refund by seller' mod='mpmangopaypayment'}
                </span>
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="is_mail_refundby_seller" id="is_mail_refundby_seller_on" value="1" {if $is_mail_refundby_seller|intval}checked="checked"{/if}/>
                    <label for="is_mail_refundby_seller_on">{l s='Yes' mod='mpmangopaypayment'}</label>
                    <input type="radio" name="is_mail_refundby_seller" id="is_mail_refundby_seller_off" value="0" {if !$is_mail_refundby_seller|intval}checked="checked"{/if} />
                    <label for="is_mail_refundby_seller_off">{l s='No' mod='mpmangopaypayment'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" name="submit_mail_mgp_conf" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save' mod='mpmangopaypayment'}
            </button>
        </div>
    </form>
</div>