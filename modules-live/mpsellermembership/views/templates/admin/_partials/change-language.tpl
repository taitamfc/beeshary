{*
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if $allow_multilang && $total_languages > 1}
	<div class="form-group">	
		<label class="col-lg-3 control-label required">{l s='Choose Language' mod='mpsellermembership'}</label>
		<div class="col-lg-7">
			<input type="hidden" name="choosedLangId" id="choosedLangId" value="{$current_lang.id_lang|escape:'html':'UTF-8'}">
			<div class="wk_seller_lang_block">
				<button type="button" id="membership_lang_btn" class="btn btn-default dropdown-toggle wk_language_toggle" data-toggle="dropdown">
					{$current_lang.name|escape:'html':'UTF-8'}
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu wk_language_menu">
					{foreach from=$languages item=language}
						<li>
							<a href="javascript:void(0)" onclick="showMembershipLangField('{$language.name|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'});">
								{$language.name|escape:'html':'UTF-8'}
							</a>
						</li>
					{/foreach}
				</ul>
			</div>
			<p class="wk_formfield_comment">{l s='Change language for updating information in multiple language.' mod='mpsellermembership'}</p>
		</div>
	</div>
{/if}