{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if $allow_multilang && $total_languages > 1}
	<div class="form-group row">
		<div class="col-md-7">
			<label class="control-label">{l s='Choose Language' mod='marketplace'}</label>
			<input type="hidden" name="choosedLangId" id="choosedLangId" value="{$current_lang.id_lang}">
			<button type="button" id="seller_lang_btn" class="btn btn-default dropdown-toggle wk_language_toggle" data-toggle="dropdown">
				{$current_lang.name}
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu wk_language_menu">
				{foreach from=$languages item=language}
					<li>
						<a href="javascript:void(0)" onclick="showProdLangField('{$language.name}', {$language.id_lang});">
							{$language.name}
						</a>
					</li>
				{/foreach}
			</ul>
			<p class="wk_formfield_comment">{l s='Change language for updating information in multiple language.' mod='marketplace'}</p>
		</div>
	</div>
{/if}