{**
 * 2017-2018 PHPIST.
 *
 *  @author    Yassine belkaid <yassine.belkaid87@gmail.com>
 *  @copyright 2017-2018 PHPIST
 *  @license   https://store.webkul.com/license.html
 *}

<div id="sellerTermsForm" style="display: none;">
	<div class="prv_section">
		<a class="prv_delivery" href="javascript:void(0);">
			<img src="{$smarty.const._THEME_IMG_DIR_}bee-fleche.svg" />
		</a>
	</div>
	<p>&nbsp;</p>
	<br><br>
	<!-- <div class="prv_section">
		<a class="prv_delivery" href="javascript:void(0);">
			<img src="{$smarty.const._THEME_IMG_DIR_}bee-fleche.svg" />
		</a>
	</div>
	<div class="alert alert-danger pp_display_errors_store" style="display: none;"></div>
	<div class="form-group">
        <img class="center-block store_top_pic" src="{$urls.base_url}themes/beeshary_child/assets/img/picto-boutique.jpg" />
        <div class="pp_seller_profile_title">{l s='Terms of use' mod='mpsellerwiselogin'}</div>
        <div class="pp_seller_profile_subtitle">Renseignez vos informations bancaires pour recevoir vos versements.</div>
    </div>

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
		<input type="text" class="form-control" id="bank_beneficiary" name="bank_beneficiary" placeholder="{l s='Last/First name' mod='mpsellerwiselogin'}"{if isset($smarty.post.bank_beneficiary)} value="{$smarty.post.bank_beneficiary|escape:'htmlall':'utf-8'}"{/if} />
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<label class="col-md-2 wt_bold">{l s='banking establishment' mod='mpsellerwiselogin'}</label>
		<input type="text" class="form-control" id="bank_establishment" name="bank_establishment"{if isset($smarty.post.bank_establishment)} value="{$smarty.post.bank_establishment|escape:'htmlall':'utf-8'}"{/if} />
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<label class="col-md-2 wt_bold">{l s='Code IBAN' mod='mpsellerwiselogin'}</label>
		<div class="input-group col-md-6">
			<div class="input-group-addon">FR</div>
			<input type="text" class="form-control" id="bank_iban_code" name="bank_iban_code" placeholder="00-0000-0000-0000-0000-0000-0000"{if isset($smarty.post.bank_iban_code)} value="{$smarty.post.bank_iban_code|escape:'htmlall':'utf-8'}"{/if} />
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<label class="col-md-2 wt_bold">{l s='Code BIC' mod='mpsellerwiselogin'}</label>
		<input type="text" class="form-control" id="bank_code_bic" name="bank_code_bic"{if isset($smarty.post.bank_code_bic)} value="{$smarty.post.bank_code_bic|escape:'htmlall':'utf-8'}"{/if} />
		<p class="help col-md-10">({l s='Bank Identification Code' mod='mpsellerwiselogin'})</p>
		<div class="clearfix"></div>
	</div>-->

   <div>
      <span style="font-size: 1.2em;">Félicitation, vous êtes sur le point de finaliser la création de votre boutique en ligne !</span><br><br>
      <span style="font-size: 1.2em;text-align: justify; line-height:1.5em;">Une fois sur votre <span style="font-weight:bold;text-decoration: underline;">tableau de bord</span>, cliquez sur l'onglet <span style="font-weight:bold;">"Mes finances"</span> et terminez votre inscription en ajoutant en toute sécurité vos informations bancaires. La vente de vos produits sera alors activée.
      <br><br><span style="font-size: 1.2em;">À tout de suite !</span><br><br>
  </div>

	<div class="form-group">
		<div class="beeshary_charter spaceRGPD">{l s='Beeshary quality Charter' mod='mpsellerwiselogin'}</div>
		<input class="spaceRGPD" type="checkbox" id="adhere_charter"/> {l s='I have read the Beeshary Quality Charter and I adhere to it without reservation.' mod='mpsellerwiselogin'} <a class="artisan_charter" data-fancybox-type="iframe" href="{$link->getCMSLink(12)|escape:'html':'utf-8'}">({l s='Read the Charter' mod='mpsellerwiselogin'})</a></br>
    	<input class="spaceRGPD" type="checkbox" id="adhere_cgv"/> {l s='I have read the Beeshary General Terms of Sales and I adhere to it without reservation.' mod='mpsellerwiselogin'} <a class="artisan_charter" data-fancybox-type="iframe" href="{$link->getCMSLink(11)|escape:'html':'utf-8'}">({l s='Read the General Terms of Sales' mod='mpsellerwiselogin'})</a>
		<div class="clearfix"></div>
	</div>
	
	{if isset( $partner ) && $partner == 1}
	<button type="submit" class="stripe-button-el" style="visibility: visible;"><span style="display: block; min-height: 30px;">S'inscrire</span></button>
	{else}
	<div class="form-group">
		<a class="next_btn" href="#"  id="submitTermsCondition">{l s='Suivant' mod='mpsellerwiselogin'}</a>
	</div>
	{/if}
	
	
	
</div>
