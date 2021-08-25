{**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{if $themeConf['meta_title']}
	{block name='head_seo_title'}
		{$themeConf['meta_title']}
	{/block}
{/if}
{if $themeConf['meta_description']}
	{block name='head_seo_description'}
		{$themeConf['meta_description']}
	{/block}
{/if}
{block name='product_activation'}{/block}

{block name='header'}
   {include file='module:mpsellerwiselogin/views/templates/front/_partials/header.tpl'}
{/block}

{block name='content'}
<style type="text/css">
	body #wrapper {
		background-color: {$themeConf['body_bg_color']};
	}
</style>
{if isset($parentBlock)}
	{foreach from=$parentBlock key=key item=value}
		{if $value['name'] == 'registration'}
			<div style="background-image: url({$bannerImg});" class="clearfix clear-both banner_block">
				<div class="container">
					<div class="row">
						{foreach from=$value['sub_block'] key=sub_k item=sub_v}
							{if $sub_v['block_name'] == 'reg_title'}
								<div class="col-sm-{$sub_v['width']}">
									<p style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'}" class="title_style">{$sub_v['data']['content']}</p>
								</div>
							{/if}
							{if $sub_v['block_name'] == 'reg_block'}
								<div class="col-sm-{$sub_v['width']}">
									<form method="POST" action="{$link->getModuleLink('mpsellerwiselogin', 'customerformprocess')|escape:'htmlall':'UTF-8'}" class="defaultForm form-horizontal" enctype="multipart/form-data" id="mp_register_form">
										<input type="hidden" name="ps_customer_id" value="" id="ps_customer_id">
										<input type="hidden" name="idSeller" value="" id="idSeller">
										<div class="col-sm-12 form_wrapper" id="form_acc_info" style="background-color: {$sub_v['block_bg_color']}; color: {$sub_v['block_text_color']};">
											<p class="text-left text-capitalize margin-top-10 form_heading" style="color: {$sub_v['block_text_color']};"><strong>{l s='Create Your Account' mod='mpsellerwiselogin'}</strong></p>
											<hr class="hr_style">

											{hook h="displayMpBeforeAccountInfoField"}

											<label class="field_label" style="color: {$sub_v['block_text_color']};">
												<span class="text-capitalize field_heading">{l s='First Name' mod='mpsellerwiselogin'}<sup class="mand_field">*</sup></span>
												<input type="text" class="form-control" name="firstname" id="firstname">
											</label>
											<label class="field_label" style="color: {$sub_v['block_text_color']};">
												<span class="text-capitalize field_heading">{l s='Last Name' mod='mpsellerwiselogin'}<sup class="mand_field">*</sup></span>
												<input type="text" class="form-control" name="lastname" id="lastname">
											</label>
											<label class="field_label" style="color: {$sub_v['block_text_color']};">
												<span class="text-capitalize field_heading">{l s='Email' mod='mpsellerwiselogin'}<sup class="mand_field">*</sup></span>
												<input type="email" class="form-control" name="email" id="email" required="required">
											</label>
											<div class="check_email">
												<span class="email_notify">{l s='This Email is already registered as customer, if you want to continue with same Email-Id' mod='mpsellerwiselogin'}<span id="toggle_form">{l s=' Click Here' mod='mpsellerwiselogin'}</span></span>
											</div>

											<label class="field_label" style="color: {$sub_v['block_text_color']};">
												<span class="text-capitalize field_heading">{l s='Password' mod='mpsellerwiselogin'}<sup class="mand_field">*</sup></span>
												<input type="password" class="form-control" name="passwd" id="passwd">
											</label>

											{hook h="displayMpAfterAccountInfoField"}

											<div class="mp_error_block login_act_err">
												<span class="mp_error text-capitalize"></span>
											</div>
											
											<button type="button" class="btn btn-success form_button" id="account_btn">{l s='Get Started' mod='mpsellerwiselogin'}</button>
										</div>

										<div class="col-sm-12 form_wrapper" id="form_shop_info" style="background-color: {$sub_v['block_bg_color']}; color: {$sub_v['block_text_color']};">
											<input type="button" class="btn btn-info btn-xs pull-right" value="BACK" id="back_account" />
											<p class="text-left text-capitalize margin-top-10 form_heading"><strong>{l s='Create Your Shop' mod='mpsellerwiselogin'}</strong></p>
											
											<hr class="hr_style">

											{hook h="displayMpBeforeShopInfoField"}
											<input type="hidden" name="multi_lang" id="multi_lang" value="{$multi_lang}">
											<input type="hidden" name="current_lang" id="current_lang" value="{$current_lang.id_lang}">	
											<label class="field_label" style="color: {$sub_v['block_text_color']};">
												{l s='Default Language' mod='mpsellerwiselogin'}
						  						<select class="form-control" name="seller_default_lang" id="seller_default_lang">
						  						{foreach from=$languages item=language}
													<option data-lang-iso="{$language.iso_code}" 
													value="{$language.id_lang}" 
													{if isset($smarty.post.seller_default_lang)}
														{if $smarty.post.seller_default_lang == $language.id_lang}Selected="Selected"
														{/if}
													{else}
														{if $current_lang.id_lang == $language.id_lang}Selected="Selected"
														{/if}
													{/if}>
													{$language.name}
													</option>
												{/foreach}
						  						</select>
											</label>
											
											<label class="field_label" style="color: {$sub_v['block_text_color']};">
												<span class="text-capitalize field_heading">{l s='Unique Shop Name' mod='mpsellerwiselogin'}<sup class="mand_field">*</sup></span>
												<input type="text" class="form-control" name="mp_shop_name_unique" id="mp_shop_name_unique" autocomplete="off">
											</label>

											<label class="field_label" style="color: {$sub_v['block_text_color']};">
												<span class="text-capitalize field_heading">{l s='Shop Name' mod='mpsellerwiselogin'}<sup class="mand_field">*</sup></span>
												<div class="row">
													{if $allow_multilang && $total_languages > 1}
														<div class="col-md-9">
													{else}
														<div class="col-md-12">
													{/if}
														{foreach from=$languages item=language}
															<input type="text" class="form-control shop_name_all {if $current_lang.id_lang == $language.id_lang}seller_default_shop{/if}" name="mp_shop_name_{$language.id_lang}" id="mp_shop_name_{$language.id_lang}"
															data-lang-name="{$language.name}" 
															{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
														{/foreach}
													</div>
													{if $allow_multilang && $total_languages > 1}
													<div class="col-md-3">
														<button type="button" id="shop_name_lang_btn" class="btn btn-default dropdown-toggle lang_padding" data-toggle="dropdown">
														{$current_lang.iso_code}
														<span class="caret"></span>
														</button>
														<ul class="dropdown-menu">
															{foreach from=$languages item=language}
															<li>
																<a href="javascript:void(0)" onclick="showLangField('{$language.iso_code}', {$language.id_lang});">{$language.name}</a>
															</li>
															{/foreach}
														</ul>
													</div>
													{/if}
												</div>
											</label>

											{if $MP_SELLER_COUNTRY_NEED}
												{if isset($countries)}
													<label class="field_label" style="color: {$sub_v['block_text_color']};">
													{l s='Country' mod='mpsellerwiselogin'}
								  						<select class="form-control" name="seller_country" id="seller_country">
								  							{foreach $countries as $country}
									  							<option value="{$country.id_country}">{$country.name}</option>
								  							{/foreach}
								  						</select>
													</label>
												{/if}

												<label class="field_label" id="sellerStateCont" style="display:none; color: {$sub_v['block_text_color']};">
													{l s='State' mod='mpsellerwiselogin'}
							  						<select class="form-control" name="seller_state" id="seller_state">
							  							<option value="0">{l s='Select State' mod='mpsellerwiselogin'}</option>
							  						</select>
							  						<input type="hidden" name="state_avl" id="state_avl" value="0"/>
												</label>

												<label class="field_label" style="color: {$sub_v['block_text_color']};"">
													<span class="text-capitalize field_heading">{l s='City' mod='mpsellerwiselogin'}<sup class="mand_field">*</sup></span>
													<input type="text" class="form-control" name="seller_city" id="seller_city">
												</label>
											{/if}

											<label class="field_label" style="color: {$sub_v['block_text_color']};">
												<span class="text-capitalize field_heading">{l s='Phone' mod='mpsellerwiselogin'}<sup class="mand_field">*</sup></span>
												<input type="text" class="form-control" name="mp_seller_phone" id="mp_seller_phone" maxlength="{$max_phone_digit}" required>
											</label>
											
											{hook h="displayMpAfterShopInfoField"}

											<div class="mp_error_block login_shop_err">
												<span class="mp_error text-capitalize"></span>
											</div>

											<button type="submit" class="btn btn-success form_button" name="registrationform">{l s='Go To Dashboard' mod='mpsellerwiselogin'}</button>
										</div>
									</form>
								</div>	
							{/if}
						{/foreach}
					</div>
				</div>
			</div>
		{/if}
		{if $value['name'] == 'content'}
			<div class="container" style="clear: both;">
				<div class="row">
					{foreach from=$value['sub_block'] key=subc_k item=subc_v}
						{if $subc_v['block_name'] == 'feature'}
							<div class="col-sm-{$subc_v['width']} ftr_cont" style="background-color: {$subc_v['block_bg_color']}; color: {$subc_v['block_text_color']};">
								{$subc_v['data']['content'] nofilter}
							</div>
						{/if}
						{if $subc_v['block_name'] == 'termscondition'}
							<div class="col-sm-{$subc_v['width']} tc_cont" style="background-color: {$subc_v['block_bg_color']}; color: {$subc_v['block_text_color']};">
								{$subc_v['data']['content'] nofilter}
							</div>
						{/if}
					{/foreach}
				</div>
			</div>
		{/if}
	{/foreach}
{/if}
{/block}

{block name="footer"}
	{include file='module:mpsellerwiselogin/views/templates/front/_partials/footer.tpl'}
{/block}