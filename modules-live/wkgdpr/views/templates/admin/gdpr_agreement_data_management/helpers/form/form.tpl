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

<div class="panel">
	<div class="panel-heading">
		<i class="icon-cog"></i> {l s='GDPR Agreement Data Management' mod='wkgdpr'}
	</div>
    <form id="{$table|escape:'htmlall':'UTF-8'}_form" class="gdpr_general_config_form defaultForm form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
		<input type="hidden" name="active_tab" value="{if isset($active_tab)}{$active_tab|escape:'htmlall':'UTF-8'}{/if}" id="active_tab">
		<ul class="nav nav-tabs">
			<li class="nav-item active">
				<a class="nav-link" href="#prestashop_core_agreement_conf" data-toggle="tab">
					{l s='Core Prestashop Form Agreements' mod='wkgdpr'}
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="#prestashop_modules_agreement_conf" data-toggle="tab">
					{l s='Prestashop module\'s Form Agreements' mod='wkgdpr'}
				</a>
			</li>
		</ul>

		<div class="tab-content panel collapse in">
			<div class="tab-pane fade in active" id="prestashop_core_agreement_conf">
				{if isset($psGDPRForms) && $psGDPRForms}
					{foreach $psGDPRForms as $formGDPRData}
						<div class="gdpr-aggrement-content-box">
							<h3 class="gdpr-content-box-heading">{$formGDPRData['name']|escape:'htmlall':'UTF-8'}</h3>
							<div class="form-group">
								<label class="col-sm-3 control-label">
									<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='If enabled, GDPR agreement checkbox is visible in the form.' mod='wkgdpr'}">{l s='Enable form for agreement checkbox' mod='wkgdpr'}</span>
								</label>
								<div class="col-sm-6">
									{assign var="enable_gdpr_agreement" value="enable_gdpr_agreement[`$formGDPRData['id_agreement_data']`]"}
									<span class="switch prestashop-switch fixed-width-lg">
										<input class="active_gdpr_agreement" type="radio" value="1" id="enable_gdpr_agreement[{$formGDPRData['id_agreement_data']|escape:'html':'UTF-8'}]_on" name="enable_gdpr_agreement[{$formGDPRData['id_agreement_data']|escape:'html':'UTF-8'}]"
										{if isset($smarty.post.enable_gdpr_agreement[{$formGDPRData['id_agreement_data']}])}
											{if $smarty.post.enable_gdpr_agreement[{$formGDPRData['id_agreement_data']}]}
												checked="checked"
											{/if}
										{elseif $formGDPRData['active']}
											checked="checked"
										{/if}>

										<label for="enable_gdpr_agreement[{$formGDPRData['id_agreement_data']|escape:'html':'UTF-8'}]_on">{l s='Yes' mod='wkgdpr'}</label>

										<input class="active_gdpr_agreement" type="radio" value="0" id="enable_gdpr_agreement[{$formGDPRData['id_agreement_data']|escape:'html':'UTF-8'}]_off" name="enable_gdpr_agreement[{$formGDPRData['id_agreement_data']|escape:'html':'UTF-8'}]"
										{if isset($smarty.post.enable_gdpr_agreement[{$formGDPRData['id_agreement_data']}])}
											{if !$smarty.post.enable_gdpr_agreement[{$formGDPRData['id_agreement_data']}]}
												checked="checked"
											{/if}
										{elseif !$formGDPRData['active']}
											checked="checked"
										{/if}>

										<label for="enable_gdpr_agreement[{$formGDPRData['id_agreement_data']|escape:'html':'UTF-8'}]_off">{l s='No' mod='wkgdpr'}</label>
										<a class="slide-button btn"></a>
									</span>
								</div>
							</div>
							<div class="form-group gdpr-aggrement-content-div {if !$formGDPRData['active']}hidden{/if}">
								<label class="col-sm-3 control-label required">
									<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='It will be used as the checkbox text for the gdpr agreement checkbox in the prestashop core forms.' mod='wkgdpr'}">{l s='GDPR agreement checkbox content' mod='wkgdpr'}</span>
								</label>
								<div class="col-sm-8">
									{foreach from=$languages item=language}
										<div id="gdpr_agreement_content_{$formGDPRData['id_agreement_data']|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="gdpr_agreement_content[{$formGDPRData['id_agreement_data']|escape:'htmlall':'UTF-8'}][{$language.id_lang|escape:'htmlall':'UTF-8'}]" class="gdpr_agreement_content_{$formGDPRData['id_agreement_data']|escape:'htmlall':'UTF-8'}" {if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if}>
											<textarea name="gdpr_agreement_content[{$formGDPRData['id_agreement_data']|escape:'htmlall':'UTF-8'}][{$language.id_lang|escape:'htmlall':'UTF-8'}]" class="gdpr_agreement_content_{$formGDPRData['id_agreement_data']|escape:'htmlall':'UTF-8'} form-control wk_tinymce">
											{if isset($smarty.post.gdpr_agreement_content[$formGDPRData['id_agreement_data']][$language.id_lang])}
												{$smarty.post.gdpr_agreement_content[$formGDPRData['id_agreement_data']][$language.id_lang]}
											{elseif isset($formGDPRData['agreement_content'][$language.id_lang]) && $formGDPRData['agreement_content'][$language.id_lang]}
												{$formGDPRData['agreement_content'][$language.id_lang]}
											{/if}</textarea>
										</div>
									{/foreach}
								</div>
								{if $languages|@count > 1}
									<div class="col-sm-1">
										<button type="button" id="wk_gdpr_lang_btn_{$formGDPRData['id_agreement_data']|escape:'htmlall':'UTF-8'}" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											{$currentLang.iso_code|escape:'htmlall':'UTF-8'}
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											{foreach from=$languages item=language}
												<li>
													<a href="javascript:void(0)" onclick="showAgreementContentLangField('{$formGDPRData['id_agreement_data']|escape:'htmlall':'UTF-8'}', {$language.id_lang|escape:'htmlall':'UTF-8'}, '{$language.iso_code|escape:'htmlall':'UTF-8'}');">{$language.name|escape:'htmlall':'UTF-8'}</a>
												</li>
											{/foreach}
										</ul>
									</div>
								{/if}
							</div>
						</div>
					{/foreach}
				{else}
					{l s='No prestashop form found for GDPR agreement checkbox.' mod='wkgdpr'}
				{/if}
			</div>
			<div class="tab-pane fade in" id="prestashop_modules_agreement_conf">
				{if isset($gdprModulesAgreementData) && $gdprModulesAgreementData}
					{foreach $gdprModulesAgreementData as $modulesData}
						<div class="gdpr-aggrement-content-box">
							<h3 class="gdpr-content-box-heading">{$modulesData['name']|escape:'htmlall':'UTF-8'}</h3>
							<div class="form-group">
								<label class="col-sm-3 control-label">
									<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='If enabled, GDPR agreement checkbox will be visible in form.' mod='wkgdpr'}">{l s='Enable form for agreement checkbox' mod='wkgdpr'}</span>
								</label>
								<div class="col-sm-6">
									{assign var="enable_gdpr_agreement" value="enable_gdpr_agreement[`$formGDPRData['id_agreement_data']`]"}
									<span class="switch prestashop-switch fixed-width-lg">
										<input class="active_gdpr_agreement" type="radio" value="1" id="enable_gdpr_agreement[{$modulesData['id_agreement_data']|escape:'html':'UTF-8'}]_on" name="enable_gdpr_agreement[{$modulesData['id_agreement_data']|escape:'html':'UTF-8'}]"
										{if isset($smarty.post.enable_gdpr_agreement[{$modulesData['id_agreement_data']}])}
											{if $smarty.post.enable_gdpr_agreement[{$modulesData['id_agreement_data']}]}
												checked="checked"
											{/if}
										{elseif $modulesData['active']}
											checked="checked"
										{/if}>
										<label for="enable_gdpr_agreement[{$modulesData['id_agreement_data']|escape:'html':'UTF-8'}]_on">{l s='Yes' mod='wkgdpr'}</label>
										<input class="active_gdpr_agreement" type="radio" value="0" id="enable_gdpr_agreement[{$modulesData['id_agreement_data']|escape:'html':'UTF-8'}]_off" name="enable_gdpr_agreement[{$modulesData['id_agreement_data']|escape:'html':'UTF-8'}]"
										{if isset($smarty.post.enable_gdpr_agreement[{$modulesData['id_agreement_data']}])}
											{if !$smarty.post.enable_gdpr_agreement[{$modulesData['id_agreement_data']}]}
												checked="checked"
											{/if}
										{elseif !$modulesData['active']}
											checked="checked"
										{/if}>
										<label for="enable_gdpr_agreement[{$modulesData['id_agreement_data']|escape:'html':'UTF-8'}]_off">{l s='No' mod='wkgdpr'}</label>
										<a class="slide-button btn"></a>
									</span>
								</div>
							</div>
							<div class="form-group gdpr-aggrement-content-div {if !$modulesData['active']}hidden{/if}">
								<label class="col-sm-3 control-label required">
									<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='It will be used as the checkbox text for the gdpr agreement checkbox in the prestashop module\'s form' mod='wkgdpr'}">{l s='GDPR agreement checkbox content' mod='wkgdpr'}</span>
								</label>
								<div class="col-sm-8">
									{foreach from=$languages item=language}
										<div id="gdpr_agreement_content_{$modulesData['id_agreement_data']|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="gdpr_agreement_content[{$modulesData['id_agreement_data']|escape:'htmlall':'UTF-8'}][{$language.id_lang|escape:'htmlall':'UTF-8'}]" class="gdpr_agreement_content_{$modulesData['id_agreement_data']|escape:'htmlall':'UTF-8'}" {if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if}>
											<textarea name="gdpr_agreement_content[{$modulesData['id_agreement_data']|escape:'htmlall':'UTF-8'}][{$language.id_lang|escape:'htmlall':'UTF-8'}]" class="form-control wk_tinymce">{if isset($smarty.post.gdpr_agreement_content[$modulesData['id_agreement_data']][$language.id_lang])}
												{$smarty.post.gdpr_agreement_content[$modulesData['id_agreement_data']][$language.id_lang]}
											{elseif isset($modulesData['agreement_content'][$language.id_lang]) && $modulesData['agreement_content'][$language.id_lang]}
												{$modulesData['agreement_content'][$language.id_lang]}
											{/if}</textarea>
										</div>
									{/foreach}
								</div>
								{if $languages|@count > 1}
									<div class="col-sm-1">
										<button type="button" id="wk_gdpr_lang_btn_{$modulesData['id_agreement_data']|escape:'htmlall':'UTF-8'}" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											{$currentLang.iso_code|escape:'htmlall':'UTF-8'}
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											{foreach from=$languages item=language}
												<li>
													<a href="javascript:void(0)" onclick="showAgreementContentLangField('{$modulesData['id_agreement_data']|escape:'htmlall':'UTF-8'}', {$language.id_lang|escape:'htmlall':'UTF-8'}, '{$language.iso_code|escape:'htmlall':'UTF-8'}');">{$language.name|escape:'htmlall':'UTF-8'}</a>
												</li>
											{/foreach}
										</ul>
									</div>
								{/if}
							</div>
						</div>
					{/foreach}
				{else}
					{l s='No module for GDPR agreement checkbox.' mod='wkgdpr'}
				{/if}
			</div>
			<div class="panel-footer">
				<button type="submit" name="submitModulesAgreementContents" class="btn btn-default pull-right submitModulesAgreementContents">
					<i class="process-icon-save"></i>{l s='Save' mod='wkgdpr'}
				</button>
			</div>
		</div>
	</form>
</div>