{**
 * 2017-2018 PHPIST.
 *
 *  @author    Yassine belkaid <yassine.belkaid87@gmail.com>
 *  @copyright 2017-2018 PHPIST
 *  @license   https://store.webkul.com/license.html
 *}

<div id="sellerTermsForm">
	<input type="hidden" name="id_ps_wk_mp_seller_bank" value="{if isset($seller_bank_obj) && $seller_bank_obj.id_ps_wk_mp_seller_bank}{$seller_bank_obj.id_ps_wk_mp_seller_bank}{/if}" />
	<div class="form-group">
		<label class="terms_title">{l s='Bank account to credit' mod='mpsellerwiselogin'}</label>
	</div>

	<div class="form-group">
		<label class="col-md-2 wt_bold">{l s='Type' mod='mpsellerwiselogin'}</label>
		<select class="form-control" name="bank_type" id="bank_type">
			<option value="iban">IBAN</option>
		</select>
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<label class="col-md-2 wt_bold">{l s='Beneficiary' mod='mpsellerwiselogin'}</label>
		<input type="text" class="form-control" id="bank_beneficiary" name="bank_beneficiary" placeholder="{l s='Last/First name' mod='mpsellerwiselogin'}" value="{if isset($smarty.post.bank_beneficiary)} {$smarty.post.bank_beneficiary|escape:'htmlall':'utf-8'}{elseif isset($seller_bank_obj) && $seller_bank_obj.id_ps_wk_mp_seller_bank}{$seller_bank_obj.beneficiary}{/if}" />
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<label class="col-md-2 wt_bold">{l s='banking establishment' mod='mpsellerwiselogin'}</label>
		<input type="text" class="form-control" id="bank_establishment" name="bank_establishment" value="{if isset($smarty.post.bank_establishment)}{$smarty.post.bank_establishment|escape:'htmlall':'utf-8'}{elseif isset($seller_bank_obj) && $seller_bank_obj.id_ps_wk_mp_seller_bank}{$seller_bank_obj.establishment}{/if}" />
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<label class="col-md-2 wt_bold">{l s='Code IBAN' mod='mpsellerwiselogin'}</label>
		<div class="input-group col-md-6">
			<div class="input-group-addon">FR&nbsp;&nbsp;&nbsp;</div>
			<input type="text" class="form-control" id="bank_iban_code" name="bank_iban_code" placeholder="00-0000-0000-0000-0000-0000-0000" value="{if isset($smarty.post.bank_iban_code)}{$smarty.post.bank_iban_code|escape:'htmlall':'utf-8'}{elseif isset($seller_bank_obj) && $seller_bank_obj.id_ps_wk_mp_seller_bank}{$seller_bank_obj.iban_code|substr:2}{/if}" />
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<label class="col-md-2 wt_bold">{l s='Code BIC' mod='mpsellerwiselogin'}</label>
		<input type="text" class="form-control" id="bank_code_bic" name="bank_code_bic" value="{if isset($smarty.post.bank_code_bic)}{$smarty.post.bank_code_bic|escape:'htmlall':'utf-8'}{elseif isset($seller_bank_obj) && $seller_bank_obj.id_ps_wk_mp_seller_bank}{$seller_bank_obj.code_bic}{/if}" />
		<p class="help col-md-10">({l s='Bank Identification Code' mod='mpsellerwiselogin'})</p>
		<div class="clearfix"></div>
	</div>
</div> 
