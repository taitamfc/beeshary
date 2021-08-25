{*
* 2017-2018 PHPIST
*
*  @author    Yassine Belkaid <yassine.belkaid87@gmail.com>
*  @copyright 2017-2018 PHPIST
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{block name='content'}
<script type="text/javascript" src="{$smarty.const._MODULE_DIR_}marketplace/views/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="{$smarty.const._MODULE_DIR_}marketplace/views/js/tinymce/tinymce_wk_setup.js"></script>
<div class="wk-mp-block" >
	{if isset($updated)}
		<p class="alert alert-success">{l s='Store updated successfully' mod='marketplace'}</p>
	{/if}
	{hook h="displayMpMenu"}
	<div class="wk-mp-content">
		<div class="page-title" style="background-color:{$title_bg_color};display:none;">
			<span style="color:{$title_text_color};">{l s='Edit Profile' mod='marketplace'}</span>
		</div>
		<form id="editSubmitStore" action="{$link->getModuleLink('marketplace', 'editstore')}" method="post" enctype="multipart/form-data" accept-charset="UTF-8,ISO-8859-1,UTF-16" id="wk_mp_seller_form">
			<div class="alert alert-danger pp_display_errors_store" style="display:none;"></div>
			<div class="wk-mp-right-column">
				<div class="profile_content">
					<input type="hidden" name="token" id="wk-static-token" value="{$static_token}">
					<input type="hidden" name="mp_seller_id" id="mp_seller_id" value="{$mp_seller_info.id_seller}">
					<input type="hidden" name="current_lang_id" value="{$current_lang.id_lang}" id="current_lang_id">
					<input type="hidden" name="active_tab" value="{if isset($active_tab)}{$active_tab}{/if}" id="active_tab">

					<div class="content_top center-block text-center">
					    <img src="{$urls.img_url}bee-shop-g4.svg" />
					    <div class="content_top_title">Ma boutique</div>
						<div class="content_top_info">Ici, vous pouvez gérer votre boutique et voir vos statistiques.</div>
					</div>
					<div class="clearfix"></div>

					<div class="alert alert-danger wk_display_none" id="wk_mp_form_error"></div>
					<hr>
					<div class="tabs wk-tabs-panel">
						<ul class="nav nav-tabs">
							<li class="nav-item">
								<a class="nav-link active" href="#wk-information" data-toggle="tab">
									<i class="material-icons">&#xE88E;</i>
									{l s='Information' mod='marketplace'}
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#wk-contact" data-toggle="tab">
									<i class="material-icons">&#xE0BA;</i>
									{l s='Address' mod='marketplace'}
								</a>
							</li>
							<!-- <li class="nav-item">
								<a class="nav-link" href="#wk-bank" data-toggle="tab">
									<i class="fa fa-bank" style="font-size: 21px;"></i>
									Informations bancaires
								</a>
							</li> -->
							<!-- <li class="nav-item">
								<a class="nav-link" href="#wk-shipping" data-toggle="tab">
									<i class="fa fa-plane" style="font-size: 21px;"></i>
									Livraison
								</a>
							</li> -->
						</ul>
						<div class="tab-content" id="tab-content">
							<div class="tab-pane fade in active" id="wk-information">
								<div class="form-group seller_shop_name_uniq">
									<label for="shop_name_unique" class="control-label required">
										Nom unique de la boutique
										<div class="wk_tooltip">
											<span class="wk_tooltiptext">{l s='This name will be used in your shop URL.' mod='marketplace'}</span>
										</div>
									</label>
									<input class="form-control"
										type="text"
										value="{if isset($smarty.post.shop_name_unique)}{$smarty.post.shop_name_unique}{else}{$mp_seller_info.shop_name_unique}{/if}"
										id="shop_name_unique"
										name="shop_name_unique"
										onblur="onblurCheckUniqueshop();"
										autocomplete="off" />
									<span class="wk-msg-shopnameunique"></span>
								</div>

								<div class="form-group">
									<label for="shop_name" class="control-label required">
										Nom de la boutique

										{block name='mp-form-fields-flag'}
											{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
										{/block}
									</label>
									{foreach from=$languages item=language}
										{assign var="shop_name" value="shop_name_`$language.id_lang`"}
										<input class="form-control shop_name_all wk_text_field_all wk_text_field_{$language.id_lang}
										{if $current_lang.id_lang == $language.id_lang}seller_default_shop{/if}
										{if $current_lang.id_lang != $language.id_lang}wk_display_none{/if}"
										type="text"
										value="{if isset($smarty.post.$shop_name)}{$smarty.post.$shop_name}{else}{$mp_seller_info.shop_name[{$language.id_lang}]}{/if}"
										id="shop_name_{$language.id_lang}"
										name="shop_name_{$language.id_lang}"
										data-lang-name="{$language.name}" />
									{/foreach}
									<span class="wk-msg-shopname"></span>
								</div>

								<div class="form-group row">
									<div class="col-md-6">
										<label for="business_email" class="control-label required">
											Email professionnel
										</label>
										<input class="form-control"
										type="email"
										value="{if isset($smarty.post.business_email)}{$smarty.post.business_email}{else}{$mp_seller_info.business_email}{/if}"
										name="business_email"
										id="business_email"
										onblur="onblurCheckUniqueSellerEmail();" />
										<span class="wk-msg-selleremail"></span>
									</div>
									<div class="col-md-6">
										<label for="phone" class="control-label required">
											Téléphone professionnel
										</label>
										<input class="form-control"
										type="text"
										value="{if isset($smarty.post.phone)}{$smarty.post.phone}{else}{$mp_seller_info.phone}{/if}"
										name="phone"
										id="phone"
										maxlength="{$max_phone_digit}" />
									</div>
								</div>

								<div class="form-group">
									<label for="about_shop" class="control-label">
										À propos de la boutique

										{block name='mp-form-fields-flag'}
											{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
										{/block}
									</label>
									{foreach from=$languages item=language}
										{assign var="about_shop" value="about_shop_`$language.id_lang`"}
										<div id="about_business_div_{$language.id_lang}" class="wk_text_field_all wk_text_field_{$language.id_lang} {if $current_lang.id_lang != $language.id_lang}wk_display_none{/if}">
											<textarea
											name="about_shop_{$language.id_lang}"
											id="about_business_{$language.id_lang}" cols="2" rows="3"
											class="about_business wk_tinymce form-control">{if isset($smarty.post.$about_shop)}{$smarty.post.$about_shop}{else}{$mp_seller_info.about_shop[{$language.id_lang}]}{/if}</textarea>
										</div>
									{/foreach}
								</div>
							</div>
							<div class="tab-pane fade in" id="wk-contact">
								{block name='editprofile-contact'}
									{include file='module:marketplace/views/templates/front/seller/_partials/editprofile-contact.tpl'}
								{/block}
							</div>
							<div class="tab-pane fade in" id="wk-bank">
								{block name='editprofile-bank'}
									{include file='module:marketplace/views/templates/front/seller/_partials/editprofile-bank.tpl'}
								{/block}
							</div>
							<div class="tab-pane fade in" id="wk-shipping">
								{include file='module:marketplace/views/templates/front/seller/_partials/editprofile-shipping.tpl'}
							</div>
						</div>
					</div>
				</div>
				{block name='mp-form-fields-notification'}
					{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-notification.tpl'}
				{/block}
			</div>
			<div class="wk-mp-right-column wk_border_top_none">
				<div class="form-group row">
					<div class="col-md-12 wk_text_right" id="wk-seller-submit" data-action="{l s='Save' mod='marketplace'}">
						<img class="wk_product_loader" src="{$module_dir}marketplace/views/img/loader.gif" width="25" />
						<button type="submit" id="updateStore" name="updateStore" class="btn btn-yellow wk_btn_extra form-control-submit" style="width: 120px;">
							<span>Valider</span>
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
{/block}
