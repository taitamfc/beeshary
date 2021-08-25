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

<div id="mgp_payment_module" class="row">
	<form action="{$payment_controller_link}" method="post" name="mangopay_form" id="wk_mangopay_form">
		{if $payment_type == 1}
			{if isset($userCards) && $userCards && isset($save_card_enable) && $save_card_enable}
				<div class="col-sm-12 mangopay_saved_card_container">
					{foreach from=$userCards item=card name=userCard}
						<div class="payment-option clearfix">
							<div class="col-lg-1 col-xs-2 form-control-static customer_saved_card_payment">
								<input value="{$card->CardType}" class="saved_customer_card_type" name="saved_customer_card_type" type="radio" {if $smarty.foreach.userCard.first}checked{/if}>
								<span class="custom-radio float-xs-left">
									<input value="{$card->Id}" class="saved_customer_card" name="saved_customer_card" type="radio" id_card_mgp="{$card->Id}" {if $smarty.foreach.userCard.first}checked{/if}>
									<span></span>
								</span>
							</div>
							<div class="col-lg-11 col-xs-10 card_info">
								<span>{$card->Alias} | {$card->CardProvider}</span>
								<span class="remove_saved_card_link" id_card_mgp="{$card->Id}"><i class="material-icons float-xs-left">delete</i>
							</div>
						</div>
					{/foreach}
					<div class="payment-option clearfix">
						<div class="col-xs-2 col-lg-1">
						</div>
						<div class="col-xs-10 col-lg-11 add_new_card">
							<span class="add_new_card_link"><i class="material-icons">add</i> {l s='Add New Card' mod='mpmangopaypayment'}</span>
						</div>
					</div>
				</div>
			{/if}
			<div class="col-sm-12 mangopay_new_card_container" {if isset($userCards) && $userCards && isset($save_card_enable) && $save_card_enable}style="display:none;"{/if}>
				<input id="pay_with_new_card" name="pay_with_new_card" type="hidden" value="{if isset($userCards) && $userCards && isset($save_card_enable) && $save_card_enable}0{else}1{/if}">
				<div class="block_mgp_img">
					<img for="wk_payment_checkbox" src="{$module_dir}mpmangopaypayment/views/img/logo-mangopay.png" class="img-responsive mangopay-logo" alt="Mangopay" height="50px" />
				</div>
				<div class="row">
					<div class="form-group col-sm-6">
						<label for="mangopay_cardnum">{l s='Credit Card number' mod='mpmangopaypayment'}</label>
						<input type="text" name="x_card_num" id="mangopay_cardnum" size="30" maxlength="16" autocomplete="Off" class="form-control" />
						<p class="wk_card_error wk-error"></p>
					</div>
					<div class="form-group col-sm-6">
						<label for="card_type">{l s='Credit Card Type' mod='mpmangopaypayment'}</label>
						<select name="card_type" id="card_type" class="form-control" >
							{foreach from=$card_type key=card_name item=card_code}
								{if ($card_code != 'BCMC')}
									<option value="{$card_code}">{$card_name}</option>
								{/if}
							{/foreach}
						</select>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-sm-6">
						<label>{l s='Expiration date' mod='mpmangopaypayment'}</label>
							{html_select_date month_id='mangopay_exp_date_month' year_id='mangopay_exp_date_year' prefix='Exp' class="form-control" end_year='+10' display_days=false}
						<label class="wk_exp_error wk-error"></label>
					</div>
					<div class="form-group col-sm-6">
						<label for="mangopay_card_code">{l s='CVV' mod='mpmangopaypayment'}</label>
						<input type="text" name="mangopay_card_code" id="mangopay_card_code" autocomplete="Off" size="4" maxlength="4" class="form-control"  />
						<a href="javascript:void(0)" class="mangopay-card-cvc-info" style="border: none;">
							<img src="{$module_dir}mpmangopaypayment/views/img/help.png" id="mangopay_cvv_help" title="{l s='What\'s this?' mod='mpmangopaypayment'}" alt="" />{l s='What\'s this?' mod='mpmangopaypayment'}
							<div class="cvc-info">
								<img src="{$module_dir}mpmangopaypayment/views/img/cvv.png" id="mangopay_cvv_help_img"/>
							</div>
						</a>
						<p class="wk_cvv_error wk-error"></p>
					</div>
				</div>
				{if isset($save_card_enable) && $save_card_enable}
					<div class="row">
						<div class="form-group col-sm-12">
							<input name="save_trans_card" type="checkbox" id="save_trans_card">
							<i><span>{l s='Save this card for faster checkout' mod='mpmangopaypayment'}</span></i>
						</div>
					</div>
				{/if}
				{if isset($userCards) && $userCards}
					<div class="row">
						<div class="col-sm-12 cancel_new_card">
							<span class="cancel_new_card_link">{l s='Cancel' mod='mpmangopaypayment'}</span>
						</div>
					</div>
				{/if}
			</div>
			<div style="display: none;" id="mangopay_submitload"><img src="{$img_ps_dir}loader.gif" /></div>
		{else}
			<div class="col-sm-12 mangopay_web_payment_container">
				<div class="block_mgp_img">
					<img for="wk_payment_checkbox" src="{$module_dir}mpmangopaypayment/views/img/logo-mangopay.png" class="img-responsive mangopay-logo" alt="Mangopay" height="50px" />
				</div>
				<div class="row">
					<div class="form-group col-sm-6">
						<label for="card_type">{l s='Credit Card Type' mod='mpmangopaypayment'}</label>
						<select name="card_type" id="card_type" class="form-control" >
							{foreach from=$card_type key=card_name item=card_code}
								<option value="{$card_code|escape:'htmlall':'UTF-8'}">{$card_name|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
		{/if}
	</form>
</div>
<div class="loading_overlay">
	<img src="{$module_dir}mpmangopaypayment/views/img/ajax-loader.gif" class="loading-img" alt="{l s='Not Found' mod='mpmangopaypayment'}"/>
</div>