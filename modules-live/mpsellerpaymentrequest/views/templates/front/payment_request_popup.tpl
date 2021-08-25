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
<style>
.help-block{
	font-size: 12px;
    color: #808080;
}
</style>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">×</button>
	<h4 class="modal-title"><i class="icon-money"></i> {l s='Payment Request' mod='mpsellerpaymentrequest'}</h4>
</div>
<div class="modal-body">
    <form method="post" action="" id="seller_payment_request" class="form-horizontal">
		<input type="hidden" name="submitPaymentRequest" value="1" />
        <div class="request_form">
			<div class="alert alert-info">
				<i class="material-icons">info</i>
				{l s='Request amount must not be greater than due amount of associated currency.' mod='mpsellerpaymentrequest'}
			</div>
            {if $currencies}
            <div class="alert" style="display:none;" id="wk_seller_error"></div>
            <div class="form-group">
                <div class="row">
                    <label class="col-lg-4 control-label required">{l s='Currency:' mod='mpsellerpaymentrequest'}</label>
                    <div class="col-lg-5">
                        <select name="id_currency" id="idCurrency" class="form-control">
                            {foreach from=$currencies item=currency}
                            <option value="{$currency.id_currency|intval}" data-due-amount="{$currency.due_amount}">{$currency.name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label class="col-lg-4 control-label required">{l s='Amount:' mod='mpsellerpaymentrequest'}</label>
                    <div class="col-lg-5">
                        <input type="text" name="request_amount" value="" id="requestAmount" placeholder="{l s='amount' mod='mpsellerpaymentrequest'}" class="form-control" />
                        <div class="help-block">{l s='Your current due is:' mod='mpsellerpaymentrequest'} <span id="wk_max_amount"></span></div>
                    </div>
                </div>
            </div>
        {else}
            <div class="alert alert-danger" id="wk_seller_error">
                {l s='You do not have enough amount to make the payment request.' mod='mpsellerpaymentrequest'}
            </div>
        {/if}
    </div>
    </form>
 </div>
 <div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='mpsellerpaymentrequest'}</button>
	{if $currencies}
    <button type="button" value="1" id="submitRequest" name="submitRequest" class="btn btn-primary">
		{l s='Submit' mod='mpsellerpaymentrequest'}
	</button>
	{/if}
<script>
$(function(){
	$('#idCurrency').on('change', function(){
		var due_amount = $(this).find(":selected").data('due-amount');
		$('#wk_max_amount').html(due_amount);
	});
	$('#idCurrency').trigger('change');
    $("#paymentRequest").on("click", "#submitRequest", function(){
        var idCurrency = $('#idCurrency option:selected').val();
        var requestAmount = $('#requestAmount').val();
        if(idCurrency == ''){
            $('#wk_seller_error').text("{l s='Please select the specific currency.' mod='mpsellerpaymentrequest'}").addClass('alert-danger').show();
        } else if(requestAmount == '' || isNaN(requestAmount) || requestAmount < 0){
            $('#wk_seller_error').text("{l s='Invalid requested amount.' mod='mpsellerpaymentrequest'}").addClass('alert-danger').show();
        } else {
            $("#seller_payment_request").submit();
        }
    });
});
</script>
</div>
