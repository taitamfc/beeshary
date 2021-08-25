{*
* 2010-2018 Webkul.
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


{extends file=$layout}
{block name='content'}
{if $logged}
	{if isset($smarty.get.mangopay_details_saved)}
		<div class="alert alert-success">
			{l s='Details saved successfully' mod='mpmangopaypayment'}
		</div>
	{/if}
	{hook h='displayMpMangopayBankDetailsHeader'}
	<div class="wk-mp-block">
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color|escape:'html':'UTF-8'};">
				<span style="color:{$title_text_color|escape:'html':'UTF-8'};">{l s='Mangopay Details' mod='mpmangopaypayment'}</span>
			</div>
			<div class="wk-mp-right-column">
				<form action="{$link->getModuleLink('mpmangopaypayment', 'mangopayselledetails')|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" accept-charset="UTF-8,ISO-8859-1,UTF-16">
					<div class="form-group" >
						<label for="mgp_seller_country" class="control-label required">{l s='Country' mod='mpmangopaypayment'}</label>
						{if isset($countries)}
							<select name="seller_id_country" class="form-control selectBankCountry" style="width:300px;">
								<option value="">{l s='Select' mod='mpmangopaypayment'}</option>
								{foreach $countries as $country}
									<option value="{$country.id_country|escape:'htmlall':'UTF-8'}" {if isset($id_country)}{if $id_country == $country.id_country}selected{/if}{else}{if 8== $country.id_country}selected{/if}{/if}>{$country.name|escape:'htmlall':'UTF-8'}</option>
								{/foreach}
							</select>
						{else}
							<p>{l s='Country list not available.' mod='mpmangopaypayment'}</p>
						{/if}
					</div>

					<div class="form-group" style="text-align:center;">
							<button type="submit" id="submit_mgp_details" name="submit_mgp_details" class="btn btn-primary button button-medium">
								<span>{l s='Save' mod='mpmangopaypayment'}</span>
							</button>
						</div>
				</form>
			</div>
		</div>
	</div>
{/if}
{/block}
