{*
* 2010-2016 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{block name='content'}
	<div class="wk-mp-block">
		{hook h ="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="dashboard">
				<div class="page-title" style="background-color:{$title_bg_color};">
					<span style="color:{$title_text_color};">
						{if isset($id)} 
							{l s='Edit Vacation' mod='mpsellervacation'}
						{else}
							{l s='Add Vacation' mod='mpsellervacation'}
						{/if}
					</span>
				</div>
				<div class="wk-mp-right-column">
					<div class="left full">
					</div>
					<div class="box-account box-recent">
						<form  enctype="multipart/form-data" class="std contact-form-box" id="" method="post" action="{$link->getModuleLink('mpsellervacation', 'addSellerVacation')}">
							{if isset($id)}
								<input type="hidden" name="id" value="{$id}">
							{/if}
							<input type="hidden" name="multi_lang" id="multi_lang" value="{$multi_lang}">
							<input type="hidden" name="selected_lang" value="{$current_lang.id_lang}" id="selected_lang">
							<div class="container">
								{block name='change-product-language'}
									{include file='module:marketplace/views/templates/front/product/_partials/change-product-language.tpl'}
								{/block}
								<div class="row">
									<div class="form-group col-md-4">				
										<label class="control-label required">{l s='From' mod='mpsellervacation'}</label>
										<input type="text" name="from" value="{if isset($id)}{$vacation_info.from}{/if}" style="text-align: center" class="fromdate form-control" >
									</div>
									<div class="form-group col-md-4">
										<label class="control-label required">{l s='To' mod='mpsellervacation'}</label>
										<input type="text" name="to" value="{if isset($id)}{$vacation_info.to}{/if}" style="text-align: center" class="todate form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label required">{l s='Description' mod='mpsellervacation'}
										<div class="wk_tooltip">
											<span class="wk_tooltiptext">
												{l s='The description of vacation will be displayed on shop page and seller profile page' mod='mpsellervacation'}.
											</span>
										</div>
										{block name='mp-form-fields-flag'}
											{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
										{/block}
									</label>
									<div class="row">
										<input type="hidden" class="total_languages" value="{$total_languages}">
										<div class="col-md-12">
											{foreach from=$languages item=language}
												{assign var="description" value="description_`$language.id_lang`"}
												<div id="desc_div_{$language.id_lang}" class="wk_text_field_all wk_text_field_{$language.id_lang} 





												{if $current_lang.id_lang != $language.id_lang}wk_display_none{/if}">
													<textarea 
													name="description_{$language.id_lang}" 
													id="description_{$language.id_lang}" 
													cols="2" rows="3" 
													class="required form-control description {if $allow_multilang}{if $current_lang.id_lang == $language.id_lang}seller_default_lang_class{/if}{/if}" data-lang-name="{$language.name|escape:'html':'UTF-8'}">{if isset($id)}{$vacation_info.description[{$language.id_lang}]}{/if}</textarea>
												</div>
											{/foreach}
								  		</div>
									</div>
								</div>
								<div class="checkbox">
		    						<label>
		      							<input type="checkbox" name="addtocart" {if isset($id)}{if $vacation_info.addtocart == '1'}checked="checked"{/if}{/if}>
		      							{l s='Checked if you want to enable Add to cart button. By default its disabled.' mod='mpsellervacation'}
		    						</label>
		  						</div>
								<div style="text-align:center;">
									<button class="btn btn-primary submit_btn" id="s" type="submit" name="mp_vac_submit_btn">
										<span>
											{if isset($id)}
												{l s='Update' mod='mpsellervacation'}
											{else}
												{l s='Add Vacation' mod='mpsellervacation'}
											{/if}
										</span>
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
{/block}
