{*
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<form
    method="post"
    class="defaultForm form-horizontal"
    action="{$link->getModuleLink('mpsellerinvoice', 'manageinvoice')|escape:'htmlall':'UTF-8'}">
	<div class="form-wrapper">
		<div class="alert alert-info">
			{l s='If you do not update invoice prefix then default prefix will be taken during invoice generation. Default invoice prefix ( %s )' sprintf=[$defaultConfig|escape:'htmlall':'UTF-8'] mod='mpsellerinvoice'}
		</div>
		<input type="hidden" name="token" id="wk-static-token" value="{$static_token}">
		<input type="hidden" name="default_lang" value="{$default_lang}" id="default_lang">
		<input type="hidden" name="current_lang_id" value="{$current_lang.id_lang}" id="current_lang_id">
		<input type="hidden" name="active_tab" value="{if isset($active_tab)}{$active_tab}{/if}" id="active_tab">
		{block name='change-product-language'}
			{if $allow_multilang && $total_languages > 1}
				<div class="clearfix form-group">
					<label class="control-label col-lg-3 required">{l s='Choose Language' mod='mpsellerinvoice'}</label>
					<input type="hidden" name="choosedLangId" id="choosedLangId" value="{$current_lang.id_lang|escape:'html':'UTF-8'}">
					<div class="wk_seller_lang_block col-md-7">
						<button type="button" id="seller_lang_btn" class="btn btn-default dropdown-toggle wk_language_toggle" data-toggle="dropdown">
							{$current_lang.name|escape:'html':'UTF-8'}
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu wk_language_menu">
							{foreach from=$languages item=language}
								<li>
									<a href="javascript:void(0)" onclick="showProdLangField('{$language.name|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'});">
										{$language.name|escape:'html':'UTF-8'}
									</a>
								</li>
							{/foreach}
						</ul>
						<p class="wk_formfield_comment">
							{l s='Change language for updating information in multiple language.' mod='mpsellerinvoice'}
						</p>
					</div>
				</div>
			{/if}
        {/block}
		<div class="clearfix form-group">
			<label class="control-label col-lg-3 required">
				{l s='Invoice Prefix : ' mod='mpsellerinvoice'}
				{block name='mp-form-fields-flag'}
					{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
				{/block}
			</label>
			<div class="col-lg-9">
				{foreach from=$languages item=language}
					{assign var="invoice_prefix" value="invoice_prefix_`$language.id_lang`"}
					<input
						type="text"
						id="invoice_prefix_{$language.id_lang|escape:'html':'UTF-8'}"
						name="invoice_prefix_{$language.id_lang|escape:'html':'UTF-8'}"
						value="{if isset($smarty.post.$invoice_prefix)}{$smarty.post.$invoice_prefix|escape:'htmlall':'UTF-8'}{else if isset($sellerInvoiceConfig.invoice_prefix[{$language.id_lang}])}{$sellerInvoiceConfig.invoice_prefix[{$language.id_lang}]|escape:'htmlall':'UTF-8'}{else}{$defaultConfig|escape:'htmlall':'UTF-8'}{/if}"
						class="form-control wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}"
						{if $default_lang != $language.id_lang}style="display:none;"{/if}/>
				{/foreach}
				<p class="wk_formfield_comment">
					{l s='Freely definable prefix for invoice number (e.g. #IN00001).' mod='mpsellerinvoice'}
				</p>
			</div>
		</div>
		<div class="clearfix form-group">
			<label class="control-label col-lg-3">
				{l s='Invoice Number :' mod='mpsellerinvoice'}
			</label>
			<div class="col-lg-9">
				<input
					type="text"
					name="invoice_number"
					value="{if isset($smarty.post.invoice_number)}{$smarty.post.invoice_number|escape:'htmlall':'UTF-8'}{else if isset($sellerInvoiceConfig.invoice_number)}{$sellerInvoiceConfig.invoice_number|escape:'htmlall':'UTF-8'}{else}0{/if}"
					class="form-control"/>
                <p class="wk_formfield_comment">
                    {l s='The next invoice will begin with this number, and then increase with each additional invoice. Set to 0 if you want to keep the current number (which is #%d)' sprintf=[$lastInsertRow|escape:'htmlall':'UTF-8'] mod='mpsellerinvoice'}
                </p>
            </div>
		</div>
		<div class="clearfix form-group">
			<label class="control-label col-lg-3">
				{l s='VAT Number :' mod='mpsellerinvoice'}
			</label>
			<div class="col-lg-9">
				<input
					type="text"
					name="invoice_vat"
					value="{if isset($smarty.post.invoice_vat)}{$smarty.post.invoice_vat|escape:'htmlall':'UTF-8'}{else if isset($sellerInvoiceConfig.invoice_vat)}{$sellerInvoiceConfig.invoice_vat|escape:'htmlall':'UTF-8'}{/if}"
					class="form-control"/>
                <p class="wk_formfield_comment">
                    {l s='This VAT number will be visible on your invoice' mod='mpsellerinvoice'}
                </p>
            </div>
		</div>
		<div class="clearfix form-group">
			<label class="control-label col-lg-3">
				{l s='Legal free text : ' mod='mpsellerinvoice'}
				{block name='mp-form-fields-flag'}
					{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
				{/block}
			</label>
			<div class="col-lg-9">
				{foreach from=$languages item=language}
					{assign var="invoice_legal_text" value="invoice_legal_text_`$language.id_lang`"}
					<textarea
						type="text"
						id="invoice_legal_text_{$language.id_lang|escape:'html':'UTF-8'}"
						name="invoice_legal_text_{$language.id_lang|escape:'html':'UTF-8'}"
						class="form-control wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}"
						{if $default_lang != $language.id_lang}style="display:none;"{/if}/>{if isset($smarty.post.$invoice_legal_text)}{$smarty.post.$invoice_legal_text|escape:'htmlall':'UTF-8'}{else if isset($sellerInvoiceConfig.invoice_legal_text[{$language.id_lang}])}{$sellerInvoiceConfig.invoice_legal_text[{$language.id_lang}]|escape:'htmlall':'UTF-8'}{/if}</textarea>
				{/foreach}
				<p class="wk_formfield_comment">
                    {l s='Use this field to show additional information on the invoice, below the payment methods summary (like specific legal information).' mod='mpsellerinvoice'}
                </p>
			</div>
		</div>
		<div class="clearfix form-group">
			<label class="control-label col-lg-3">
				{l s='Footer Text : ' mod='mpsellerinvoice'}
				{block name='mp-form-fields-flag'}
					{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
				{/block}
			</label>
			<div class="col-lg-9">
				{foreach from=$languages item=language}
					{assign var="invoice_footer_text" value="invoice_footer_text_`$language.id_lang`"}
					<textarea
						type="text"
						id="invoice_footer_text_{$language.id_lang|escape:'html':'UTF-8'}"
						name="invoice_footer_text_{$language.id_lang|escape:'html':'UTF-8'}"
						class="form-control wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}"
						{if $default_lang != $language.id_lang}style="display:none;"{/if}/>{if isset($smarty.post.$invoice_footer_text)}{$smarty.post.$invoice_footer_text|escape:'htmlall':'UTF-8'}{else if isset($sellerInvoiceConfig.invoice_footer_text[{$language.id_lang}])}{$sellerInvoiceConfig.invoice_footer_text[{$language.id_lang}]|escape:'htmlall':'UTF-8'}{/if}</textarea>
				{/foreach}
				<p class="wk_formfield_comment">
                    {l s='This text will appear at the bottom of the invoice, below your company details.' mod='mpsellerinvoice'}
                </p>
			</div>
		</div>
	</div>
	<div class="clearfix wk_text_right">
		<button type="submit" name="submitInvoiceConfig" class="btn btn-primary pull-right">
			<i class="process-icon-save"></i> {l s='Save' mod='mpsellerinvoice'}
		</button>
	</div>
</form>