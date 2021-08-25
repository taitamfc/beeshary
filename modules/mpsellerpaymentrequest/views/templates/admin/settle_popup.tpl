{*
*
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="modal-body">
    <form method="post" action="" id="seller_payment_request_transaction" class="form-horizontal">
            <input type="hidden" id="is_accepted" name="is_accepted" value="{if $no_prefix_seller_due < $no_prefix_request_amount}0{else}1{/if}" />
            <input type="hidden" id="id_seller_payment_request" name="id_seller_payment_request" value="{$id_seller_payment_request|intval}" />
            {if $no_prefix_seller_due < $no_prefix_request_amount}
            <div class="alert alert-danger" id="wk_seller_error">{l s='Due amount %s is not enough as per the requested amount %s, you can only decline this request for now.' sprintf=[$seller_due,$request_amount] mod='mpsellerpaymentrequest'}</div>
            {else}
            <div class="alert alert-danger hide" id="wk_seller_error"></div>
            {/if}
            {if $no_prefix_seller_due >= $no_prefix_request_amount}
            <div class="request_accept_form">
            <div class="form-group" style="margin-bottom:0px;">
                <label class="col-lg-4 control-label">{l s='Payment Method:' mod='mpsellerpaymentrequest'}</label>
                <div class="col-lg-5">
                    <p class="form-control-static" id="payment_mode">
                        {if isset($payment_mode)}{$payment_mode|escape:'htmlall':'UTF-8'}{else}{l s='N/A' mod='mpsellerpaymentrequest'}{/if}
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label"></label>
                <div class="col-lg-5">
                    <strong>{l s='OR' mod='mpsellerpaymentrequest'}</strong>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">
                    {l s='Other Mode:' mod='mpsellerpaymentrequest'}
                </label>
                <div class="col-lg-5">
                    <input type="text" name="wk_mp_payment_method" id="wk_mp_payment_method" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">{l s='Payment Details:' mod='mpsellerpaymentrequest'}</label>
                <div class="col-lg-5">
                    <p class="form-control-static" id="payment_mode_details">
                        {if isset($payment_mode_details)}{$payment_mode_details|escape:'htmlall':'UTF-8'}{else}{l s='N/A' mod='mpsellerpaymentrequest'}{/if}
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">
                    {l s='Transaction ID:' mod='mpsellerpaymentrequest'}
                </label>
                <div class="col-lg-5">
                    <input type="text" name="wk_mp_transaction_id" id="wk_mp_transaction_id" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">
                    {l s='Remark:' mod='mpsellerpaymentrequest'}
                </label>
                <div class="col-lg-5">
                    <input type="text" name="wk_mp_remark" id="wk_mp_remark" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label required">{l s='Amount:' mod='mpsellerpaymentrequest'}</label>
                <div class="col-lg-5">
                    <input type="text" name="amount" value="{$no_prefix_request_amount|floatval}" id="amount" disabled="disabled" placeholder="{l s='amount' mod='mpsellerpaymentrequest'}" class="form-control" />
                    <div class="help-block">{l s='Seller due amount is' mod='mpsellerpaymentrequest'} <span id="wk_max_amount">{$seller_due|escape:'htmlall':'UTF-8'}</span></div>
                </div>
            </div>
        </div>
        <div class="request_decline_form hide">
        {else}
        <div class="request_decline_form">
        {/if}
            <div class="form-group">
                <label class="col-lg-4 control-label required">{l s='Decline Message:' mod='mpsellerpaymentrequest'}</label>
                <div class="col-lg-8">
                    <textarea name="remark" maxlength="200" id="wk_mp_decline_message" class="form-control"></textarea>
                </div>
            </div>
        </div>
    </form>
<script>
{if $no_prefix_seller_due < $no_prefix_request_amount}
var request_decline = true;
{else}
var request_decline = false;
{/if}
$(function(){

    $("#mpsellerpaymentrequest_modal").on("click", "button.decline", function(){
        $("#mpsellerpaymentrequest_modal #is_accepted").val(0);
        var decline_message = $('#wk_mp_decline_message').val();
        if(request_decline == true && decline_message.trim() == ''){
            $('#wk_seller_error').text("{$decline_error_message|escape:'htmlall':'UTF-8'}").addClass('alert-danger').removeClass('hide');
        } else if(request_decline == true ){
            $("#seller_payment_request_transaction").submit();
        } else {
            request_decline = true;
            $(".request_accept_form").addClass('hide');
            $(".request_decline_form").removeClass('hide');
        }
    });

    $("#mpsellerpaymentrequest_modal").on("click", "button.approve", function(){
        $("#mpsellerpaymentrequest_modal #is_accepted").val(1);
        $('#wk_seller_error').addClass('hide');
        if(request_decline == false ){
            if(/<[a-z/][\s\S]*>/i.test($('#wk_mp_payment_method').val()) == true) {
                $('#wk_seller_error').text("{$mode_error_message|escape:'htmlall':'UTF-8'}").addClass('alert-danger').removeClass('hide');
            } else if(/<[a-z/][\s\S]*>/i.test($('#wk_mp_transaction_id').val()) == true) {
                $('#wk_seller_error').text("{$transaction_error_message|escape:'htmlall':'UTF-8'}").addClass('alert-danger').removeClass('hide');
            } else if(/<[a-z/][\s\S]*>/i.test($('#wk_mp_remark').val()) == true) {
                $('#wk_seller_error').text("{$remark_error_message|escape:'htmlall':'UTF-8'}").addClass('alert-danger').removeClass('hide');
            } else {
                $("#seller_payment_request_transaction").submit();
            }
        } else {
            request_decline = false;
            $(".request_accept_form").removeClass('hide');
            $(".request_decline_form").addClass('hide');
        }
    });
});
</script>
 </div>
