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

<form novalidate="" enctype="multipart/form-data" method="post" action="{$current}{if isset($token) && $token}&amp;token={$token}{/if}" class="defaultForm form-horizontal AdminCustomizeLogin" id="marketplace_login_content_form_1">
	<input type="hidden" value="1" name="submitAddmarketplace_login_content">
	
	<div id="fieldset_0_1_5" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> {l s='Theme Setting' mod='mpsellerwiselogin'}
		</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller Login Page Header background color' mod='mpsellerwiselogin'}">{l s='Header Background Color' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9 ">
					<div class="form-group">
						<div class="col-lg-2">
							<div class="row">
								<div class="input-group">
									<input type="text" {if $themeConfig['header_bg_color']}value="{$themeConfig['header_bg_color']}"{/if} name="header_bg_color" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_0" style="{if $themeConfig['header_bg_color']}background-color:{$themeConfig['header_bg_color']}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_0" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller Login Page Body background color' mod='mpsellerwiselogin'}">{l s='Body Background Color' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9 ">
					<div class="form-group">
						<div class="col-lg-2">
							<div class="row">
								<div class="input-group">
									<input type="text" {if $themeConfig['body_bg_color']}value="{$themeConfig['body_bg_color']}"{/if} name="body_bg_color" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_1" style="{if $themeConfig['body_bg_color']}background-color:{$themeConfig['body_bg_color']}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_1" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='This will be your Page Title' mod='mpsellerwiselogin'}">{l s='Meta Title' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9">
					<div class="row">
						{if $total_languages > 1}
							<div class="col-md-10">
						{else}
							<div class="col-md-12">
						{/if}
							{foreach from=$languages item=language}								
								{assign var="meta_tit" value="metaTitle_`$language.id_lang`"}
								<input type="text" 
								id="metaTitle_{$language.id_lang}" 
								name="metaTitle_{$language.id_lang}"
								{if isset($themeConfig)}
									value="{$themeConfig['meta_title'][{$language.id_lang}]}"
								{else}
									value="{if isset($smarty.post.$meta_tit)}{$smarty.post.$meta_tit}{/if}"
								{/if}
								class="form-control metaTitleAll"
								{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
							{/foreach}
						</div>
						{if $total_languages > 1}
						<div class="col-lg-2">
							<button type="button" id="metaTitleLang_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								{$current_lang.iso_code}
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								{foreach from=$languages item=language}
									<li>
										<a href="javascript:void(0)" onclick="showMetaTitleLangField('{$language.iso_code}', {$language.id_lang});">{$language.name}</a>
									</li>
								{/foreach}
							</ul>
						</div>
						{/if}
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='This will be your Page Meta Description' mod='mpsellerwiselogin'}">{l s='Meta Description' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9 ">
					<div class="row">
						{if $total_languages > 1}
							<div class="col-md-10">
						{else}
							<div class="col-md-12">
						{/if}
							{foreach from=$languages item=language}								
								{assign var="meta_desc" value="metaDescription_`$language.id_lang`"}
								<input type="text" 
								id="metaDescription_{$language.id_lang}" 
								name="metaDescription_{$language.id_lang}"
								{if isset($themeConfig)}
									value="{$themeConfig['meta_description'][{$language.id_lang}]}"
								{else}
									value="{if isset($smarty.post.$meta_desc)}{$smarty.post.$meta_desc}{/if}"
								{/if}
								class="form-control metaDescriptionAll"
								{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
							{/foreach}
						</div>
						{if $total_languages > 1}
						<div class="col-lg-2">
							<button type="button" id="metaDescriptionLang_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								{$current_lang.iso_code}
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								{foreach from=$languages item=language}
									<li>
										<a href="javascript:void(0)" onclick="showMetaTitleLangField('{$language.iso_code}', {$language.id_lang});">
											{$language.name}
										</a>
									</li>
								{/foreach}
							</ul>
						</div>
						{/if}
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="submit_1" id="marketplace_login_content_form_submit_btn_5" value="1" type="submit"><i class="process-icon-save"></i> {l s='Save' mod='mpsellerwiselogin'}
			</button>
		</div>
	</div>
	<div id="fieldset_1_1_6" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> {l s='Header Block(s) Positions' mod='mpsellerwiselogin'}
		</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller Login Page Header Blocks Text color' mod='mpsellerwiselogin'}">{l s='Header Block Text Color' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9 ">
					<div class="form-group">
						<div class="col-lg-2">
							<div class="row">
								<div class="input-group">
									<input type="text" {if $headerLogoDetails['block_text_color']}value="{$headerLogoDetails['block_text_color']}"{/if} name="hdBlockTextColor" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_3" style="{if $headerLogoDetails['block_text_color']}background-color:{$headerLogoDetails['block_text_color']}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_3" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Image file dimentions must be less than or equal to 350*99 px and image size must be less than 8M' mod='mpsellerwiselogin'}">{l s='Logo' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9 ">
					{if isset($wk_logo_url)}
						<div class="form-group">
							<div id="wk_logo-images-thumbnails" class="col-lg-12">
								<img src="{$wk_logo_url}" class="img-thumbnail" style="background-color: #f5fffa;" height="70px;" />
							</div>
						</div>
					{/if}
					<div class="form-group">
						<div class="col-sm-6">
							<input type="file" class="hide" name="wk_logo" id="wk_logo">
							<div class="dummyfile input-group">
								<span class="input-group-addon"><i class="icon-file"></i></span>
								<input type="text" readonly="" name="filename" id="wk_logo-name">
								<span class="input-group-btn">
									<button class="btn btn-default" name="submitAddAttachments" type="button" id="wk_logo-selectbutton">
										<i class="icon-folder-open"></i> {l s='Add file' mod='mpsellerwiselogin'}
									</button>
								</span>
							</div>
							<i>{l s='Image width and height should not greater than 350px and 99px respectively.' mod='mpsellerwiselogin'}</i>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Image width compared to screen' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9 ">
					<select id="shopLogoWidth" class="fixed-width-xl" name="shopLogoWidth">
						{foreach $width as $logo_width}
							<option value="{$logo_width.id_value}" {if $headerLogoDetails['width'] == $logo_width.id_value}selected="selected"{/if}>
								{$logo_width.name}
							</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Image Block Position' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9 ">
					<select id="shopLogoPosition" class="fixed-width-xl" name="shopLogoPosition">
						{foreach $head_pos as $logo_pos}
							<option value="{$logo_pos.id}" {if $headerLogoDetails['id_position'] == $logo_pos.id}selected="selected"{/if}>{$logo_pos.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Login Block Width' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9 ">
					<select id="loginWidth" class="fixed-width-xl" name="loginWidth">
						{foreach $width as $login_wid}
							<option value="{$login_wid.id_value}" {if $headerLoginDetails['width'] == $login_wid.id_value}selected="selected"{/if}>{$login_wid.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Login Block Position' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9 ">
					<select id="loginBlockPosition" class="fixed-width-xl" name="loginBlockPosition">
						{foreach $head_pos as $login_pos}
							<option value="{$login_pos.id}" {if $headerLoginDetails['id_position'] == $login_pos.id}selected="selected"{/if}>{$login_pos.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="submit_2" id="marketplace_login_content_form_submit_btn_6" value="1" type="submit"><i class="process-icon-save"></i> {l s='Save' mod='mpsellerwiselogin'}</button>
		</div>
	</div>
	<div id="fieldset_2_1_8" class="panel">
		<div class="panel-heading"><i class="icon-cogs"></i> {l s='Registration Block Configuration' mod='mpsellerwiselogin'}</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Banner Block Position' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9 ">
					<select id="regBannerPosition" class="fixed-width-xl" name="regBannerPosition">
						{foreach $two_block_position as $banPos}
							<option value="{$banPos.id}" {if $regBannerPosition == $banPos.id}selected="selected"{/if}>{$banPos.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Maximum image size: 8M' mod='mpsellerwiselogin'}">{l s='Banner Image' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9 ">
					<div class="form-group">
						<div id="banner_img-images-thumbnails" class="col-lg-12">
							<div>
								<img src="{$bannerImgUrl}" height="70px;">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-6">
							<input type="file" class="hide" name="banner_img" id="banner_img">
							<div class="dummyfile input-group">
								<span class="input-group-addon"><i class="icon-file"></i></span>
								<input type="text" readonly="" name="filename" id="banner_img-name">
								<span class="input-group-btn">
									<button class="btn btn-default" name="submitAddAttachments" type="button" id="banner_img-selectbutton">
										<i class="icon-folder-open"></i> {l s='Add file' mod='mpsellerwiselogin'}
									</button>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Banner Block Active' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" value="1" id="regPBlockActive_on" name="regPBlockActive" {if $regPBlockActive}checked="checked"{/if}>
						<label for="regPBlockActive_on">{l s='Yes' mod='mpsellerwiselogin'}</label>
						<input type="radio" value="0" id="regPBlockActive_off" name="regPBlockActive" {if !$regPBlockActive}checked="checked"{/if}>
						<label for="regPBlockActive_off">{l s='No' mod='mpsellerwiselogin'}</label>
						<a class="slide-button btn"></a>
					</span>
					<p class="help-block">{l s='If Disabled, This block will not display' mod='mpsellerwiselogin'}.</p>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Title Block Position' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9 ">
					<select id="regTitleBlockPos" class=" fixed-width-xl" name="regTitleBlockPos">
						{foreach $reg_pos as $title_wid}
							<option value="{$title_wid.id}" {if $regBlockTitleDetails['id_position'] == $title_wid.id}selected="selected"{/if}>{$title_wid.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Title Block Width' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9 ">
					<select id="regTitleBlockWidth" class=" fixed-width-xl" name="regTitleBlockWidth">
						{foreach $width as $title_wid}
							<option value="{$title_wid.id_value}" {if $regBlockTitleDetails['width'] == $title_wid.id_value}selected="selected"{/if}>{$title_wid.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Title Text Color' mod='mpsellerwiselogin'}">{l s='Title Text Color' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9 ">
					<div class="form-group">
						<div class="col-lg-2">
							<div class="row">
								<div class="input-group">
									<input type="text" {if $regBlockTitleDetails['block_text_color']}value="{$regBlockTitleDetails['block_text_color']}"{/if} name="regTitleTextColor" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_6" style="{if $regBlockTitleDetails['block_text_color']}background-color:{$regBlockTitleDetails['block_text_color']}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_6" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='This will be your Title Text' mod='mpsellerwiselogin'}">{l s='Title Line' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9">
					<div class="row">
						{if $total_languages > 1}
							<div class="col-md-10">
						{else}
							<div class="col-md-12">
						{/if}
							{foreach from=$languages item=language}								
								{assign var="regTitle" value="regTitleLine_`$language.id_lang`"}
								<input type="text" 
								id="regTitleLine_{$language.id_lang}" 
								name="regTitleLine_{$language.id_lang}"
								{if isset($regTitleLine)}
									value="{$regTitleLine['content'][{$language.id_lang}]}"
								{else}
									value="{if isset($smarty.post.$regTitle)}{$smarty.post.$regTitle}{/if}"
								{/if}
								class="form-control regTitleLineAll"
								{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
							{/foreach}
						</div>
						{if $total_languages > 1}
						<div class="col-lg-2">
							<button type="button" id="regTitleLineLang_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								{$current_lang.iso_code}
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								{foreach from=$languages item=language}
									<li>
										<a href="javascript:void(0)" onclick="showTitleLineLangField('{$language.iso_code}', {$language.id_lang});">{$language.name}</a>
									</li>
								{/foreach}
							</ul>
						</div>
						{/if}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Title Block Active' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" value="1" id="regTitleBlockActive_on" name="regTitleBlockActive" {if $regBlockTitleDetails['active']}checked="checked"{/if}>
						<label for="regTitleBlockActive_on">{l s='Yes' mod='mpsellerwiselogin'}</label>
						<input type="radio" value="0" id="regTitleBlockActive_off" name="regTitleBlockActive" {if !$regBlockTitleDetails['active']}checked="checked"{/if}>
						<label for="regTitleBlockActive_off">{l s='No' mod='mpsellerwiselogin'}</label>
						<a class="slide-button btn"></a>
					</span>
					<p class="help-block">{l s='If Disabled, Title block will not display' mod='mpsellerwiselogin'}.</p>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller Login Page Registration Blocks background color' mod='mpsellerwiselogin'}">{l s='Registration Block Background Color' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9 ">
					<div class="form-group">
						<div class="col-lg-2">
							<div class="row">
								<div class="input-group">
									<input type="text" {if $regBlockDetails['block_bg_color']}value="{$regBlockDetails['block_bg_color']}"{/if} name="regBgColor" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_7" style="{if $regBlockDetails['block_bg_color']}background-color:{$regBlockDetails['block_bg_color']}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_7" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller Login Page Registration Blocks text color' mod='mpsellerwiselogin'}">{l s='Block Text Color' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9 ">
					<div class="form-group">
						<div class="col-lg-2">
							<div class="row">
								<div class="input-group">
									<input type="text" {if $regBlockDetails['block_text_color']}value="{$regBlockDetails['block_text_color']}"{/if} name="regBlockTextColor" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_8" style="{if $regBlockDetails['block_text_color']}background-color:{$regBlockDetails['block_text_color']}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_8" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Registration Block Width' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9 ">
					<select id="regBlockWidth" class="fixed-width-xl" name="regBlockWidth">
						{foreach $width as $reg_wid}
							<option value="{$reg_wid.id_value}" {if $regBlockDetails['width'] == $reg_wid.id_value}selected="selected"{/if}>{$reg_wid.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Registration Block Position' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9 ">
					<select id="regBlockPosition" class="fixed-width-xl" name="regBlockPosition">
						{foreach $reg_pos as $subreg_pos}
							<option value="{$subreg_pos.id}" {if $regBlockDetails['id_position'] == $subreg_pos.id}selected="selected"{/if}>{$subreg_pos.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Registration Block Active' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" value="1" id="regBlockActive_on" name="regBlockActive" {if $regBlockDetails['active']}checked="checked"{/if}>
						<label for="regBlockActive_on">{l s='Yes' mod='mpsellerwiselogin'}</label>
						<input type="radio" value="0" id="regBlockActive_off" name="regBlockActive" {if !$regBlockDetails['active']}checked="checked"{/if}>
						<label for="regBlockActive_off">{l s='No' mod='mpsellerwiselogin'}</label>
						<a class="slide-button btn"></a>
					</span>
					<p class="help-block">{l s='If Disabled, Registration block will not display' mod='mpsellerwiselogin'}.</p>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="submit_3" id="marketplace_login_content_form_submit_btn_8" value="1" type="submit"><i class="process-icon-save"></i> {l s='Save' mod='mpsellerwiselogin'}</button>
		</div>
	</div>
	
	<div id="fieldset_3_1_7" class="panel">
		<div class="panel-heading"><i class="icon-cogs"></i> {l s='Content Block Configuration' mod='mpsellerwiselogin'}</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Content Block Position' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9 ">
					<select id="contentPosition" class=" fixed-width-xl" name="contentPosition">
						{foreach $two_block_position as $cont_pos}
							<option value="{$cont_pos.id}" {if $contentPosition == $cont_pos.id}selected="selected"{/if}>{$cont_pos.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Content Block Active' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" value="1" id="contentPBlockActive_on" name="contentPBlockActive" {if $contentPBlockActive}checked="checked"{/if}>
						<label for="contentPBlockActive_on">{l s='Yes' mod='mpsellerwiselogin'}</label>
						<input type="radio" value="0" id="contentPBlockActive_off" name="contentPBlockActive" {if !$contentPBlockActive}checked="checked"{/if}>
						<label for="contentPBlockActive_off">{l s='No' mod='mpsellerwiselogin'}</label>
						<a class="slide-button btn"></a>
					</span>
					<p class="help-block">{l s='If Disabled, Content block will not display' mod='mpsellerwiselogin'}.</p>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="submit_4" id="marketplace_login_content_form_submit_btn_7" value="1" type="submit"><i class="process-icon-save"></i> {l s='Save' mod='mpsellerwiselogin'}</button>
		</div>
	</div>
	<div id="fieldset_4_1_8" class="panel">
		<div class="panel-heading"><i class="icon-cogs"></i> {l s='Feature Block Configuration' mod='mpsellerwiselogin'}</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller Login Page Feature Blocks background color' mod='mpsellerwiselogin'}">{l s='Block Background Color' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9 ">
					<div class="form-group">
						<div class="col-lg-2">
							<div class="row">
								<div class="input-group">
									<input type="text" {if $blockFeatureDetail['block_bg_color']}value="{$blockFeatureDetail['block_bg_color']}"{/if} name="featureBgColor" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_4" style="{if $blockFeatureDetail['block_bg_color']}background-color:{$blockFeatureDetail['block_bg_color']}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_4" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller Login Page Feature Blocks text color' mod='mpsellerwiselogin'}">{l s='Block Text Color' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9 ">
					<div class="form-group">
						<div class="col-lg-2">
							<div class="row">
								<div class="input-group">
									<input type="text" {if $blockFeatureDetail['block_text_color']}value="{$blockFeatureDetail['block_text_color']}"{/if} name="featureTextColor" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_5" style="{if $blockFeatureDetail['block_text_color']}background-color:{$blockFeatureDetail['block_text_color']}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_5" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Feature Block width' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9 ">
					<select id="featureBlockWidth" class=" fixed-width-xl" name="featureBlockWidth">
						{foreach $width as $feature_wid}
							<option value="{$feature_wid.id_value}" {if $blockFeatureDetail['width'] == $feature_wid.id_value}selected="selected"{/if}>{$feature_wid.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Feature Block Position' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9 ">
					<select id="featureBlockPosition" class=" fixed-width-xl" name="featureBlockPosition">
						{foreach $content_pos as $feature_pos}
							<option value="{$feature_pos.id}" {if $blockFeatureDetail['id_position'] == $feature_pos.id}selected="selected"{/if}>{$feature_pos.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Invalid characters: &lt;&gt;;=#{}' mod='mpsellerwiselogin'}">{l s='Page content' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9 ">
					<div class="row">
						{if $total_languages > 1}
						<div class="col-md-10">
						{else}
						<div class="col-md-12">
						{/if}
							{foreach from=$languages item=language}
								{assign var="featureContent_name" value="featureContent_`$language.id_lang`"}
								<div id="featureContentDiv_{$language.id_lang}" class="featureContentAll" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>
									<textarea 
									name="featureContent_{$language.id_lang}" 
									id="featureContent_{$language.id_lang}" cols="2" rows="3" 
									class="te autoload_rte rte wk_tinymce" aria-hidden="true">{if isset($smarty.post.$featureContent_name)}{$smarty.post.$featureContent_name}{else}{$blockLangContent['content'][{$language.id_lang}]}{/if}</textarea>
								</div>
							{/foreach}
						</div>
						{if $total_languages > 1}
						<div class="col-md-2">
							<button type="button" id="featureContent_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								{$current_lang.iso_code}
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								{foreach from=$languages item=language}
									<li>
										<a href="javascript:void(0)" onclick="showPageContentLangField('{$language.iso_code}', {$language.id_lang});">{$language.name}</a>
									</li>
								{/foreach}
							</ul>
						</div>
						{/if}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Active' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" value="1" id="featureBlockActive_on" name="featureBlockActive" {if $blockFeatureDetail['active']}checked="checked"{/if}>
						<label for="featureBlockActive_on">{l s='Yes' mod='mpsellerwiselogin'}</label>
						<input type="radio" value="0" id="featureBlockActive_off" name="featureBlockActive" {if !$blockFeatureDetail['active']}checked="checked"{/if}>
						<label for="featureBlockActive_off">{l s='No' mod='mpsellerwiselogin'}</label>
						<a class="slide-button btn"></a>
					</span>
					<p class="help-block">{l s='If Disabled, This block will not display' mod='mpsellerwiselogin'}.</p>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="submit_5" id="marketplace_login_content_form_submit_btn_8" value="1" type="submit"><i class="process-icon-save"></i> {l s='Save' mod='mpsellerwiselogin'}</button>
		</div>
	</div>
	<div id="fieldset_5_1_9" class="panel">
		<div class="panel-heading"><i class="icon-cogs"></i> {l s='Terms And Conditions' mod='mpsellerwiselogin'}</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller Login Page T&C Blocks background color' mod='mpsellerwiselogin'}">{l s='Block Background Color' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9 ">
					<div class="form-group">
						<div class="col-lg-2">
							<div class="row">
								<div class="input-group">
									<input type="text" {if $termsConditionDetails['block_bg_color']}value="{$termsConditionDetails['block_bg_color']}"{/if} name="tcBgColor" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_9" style="{if $termsConditionDetails['block_bg_color']}background-color:{$termsConditionDetails['block_bg_color']}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_9" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller Login Page T&C Blocks text color' mod='mpsellerwiselogin'}">{l s='Block Text Color' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9 ">
					<div class="form-group">
						<div class="col-lg-2">
							<div class="row">
								<div class="input-group">
									<input type="text" {if $termsConditionDetails['block_text_color']}value="{$termsConditionDetails['block_text_color']}"{/if} name="tcTextColor" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_10" style="{if $termsConditionDetails['block_text_color']}background-color:{$termsConditionDetails['block_text_color']}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_10" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='T&C Block width' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9 ">
					<select id="tcBlockWidth" class=" fixed-width-xl" name="tcBlockWidth">
						{foreach $width as $tc_wid}
							<option value="{$tc_wid.id_value}" {if $termsConditionDetails['width'] == $tc_wid.id_value}selected="selected"{/if}>{$tc_wid.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='T&C Block Position' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9 ">
					<select id="tcBlockPosition" class=" fixed-width-xl" name="tcBlockPosition">
						{foreach $content_pos as $tc_pos}
							<option value="{$tc_pos.id}" {if $termsConditionDetails['id_position'] == $tc_pos.id}selected="selected"{/if}>{$tc_pos.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='	Invalid characters: &lt;&gt;;=#{}' mod='mpsellerwiselogin'}">{l s='T&C Content' mod='mpsellerwiselogin'}</span>
				</label>
				<div class="col-lg-9">
					<div class="row">
						{if $total_languages > 1}
						<div class="col-md-10">
						{else}
						<div class="col-md-12">
						{/if}
							{foreach from=$languages item=language}
								{assign var="tcBlockContent_name" value="tcBlockContent_`$language.id_lang`"}
								<div id="tcBlockContentDiv_{$language.id_lang}" class="tcBlockContentAll" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>
									<textarea 
									name="tcBlockContent_{$language.id_lang}" 
									id="tcBlockContent_{$language.id_lang}" cols="2" rows="3" 
									class="te autoload_rte rte wk_tinymce" aria-hidden="true">{if isset($smarty.post.$tcBlockContent_name)}{$smarty.post.$tcBlockContent_name}{else}{$tcBlockContent['content'][{$language.id_lang}]}{/if}</textarea>
								</div>
							{/foreach}
						</div>
						{if $total_languages > 1}
						<div class="col-md-2">
							<button type="button" id="tcBlockContent_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								{$current_lang.iso_code}
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								{foreach from=$languages item=language}
									<li>
										<a href="javascript:void(0)" onclick="showTcContentLangField('{$language.iso_code}', {$language.id_lang});">{$language.name}</a>
									</li>
								{/foreach}
							</ul>
						</div>
						{/if}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Active' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" value="1" id="tcBlockActive_on" name="tcBlockActive" {if $termsConditionDetails['active']}checked="checked"{/if}>
						<label for="tcBlockActive_on">{l s='Yes' mod='mpsellerwiselogin'}</label>
						<input type="radio" value="0" id="tcBlockActive_off" name="tcBlockActive" {if !$termsConditionDetails['active']}checked="checked"{/if}>
						<label for="tcBlockActive_off">{l s='No' mod='mpsellerwiselogin'}</label>
						<a class="slide-button btn"></a>
					</span>
					<p class="help-block">{l s='If Disabled, This block will not display' mod='mpsellerwiselogin'}.</p>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="submit_6" id="marketplace_login_content_form_submit_btn_9" value="1" type="submit">
			<i class="process-icon-save"></i> {l s='Save' mod='mpsellerwiselogin'}
			</button>
		</div>
	</div>

	{hook h='displayMpAddNewWizard'}
</form>
{block name=script}
<script type="text/javascript">
	var iso = '{$iso}';
	var pathCSS = '{$smarty.const._THEME_CSS_DIR_}';
	var ad = '{$ad}';

	$(document).ready(function(){
		tinySetup({
			editor_selector :"wk_tinymce"
		});
	});
</script>
{/block}

<script type="text/javascript">
	function showMetaTitleLangField(lang_iso_code, id_lang)
	{
		displayHideLangField('metaTitleLang_btn', 'metaTitleAll', 'metaTitle_', lang_iso_code, id_lang);
		displayHideLangField('metaDescriptionLang_btn', 'metaDescriptionAll', 'metaDescription_', lang_iso_code, id_lang);
	}

	function showTitleLineLangField(lang_iso_code, id_lang)
	{
		displayHideLangField('regTitleLineLang_btn', 'regTitleLineAll', 'regTitleLine_', lang_iso_code, id_lang);
	}

	function showPageContentLangField(lang_iso_code, id_lang)
	{
		displayHideLangField('featureContent_btn', 'featureContentAll', 'featureContentDiv_', lang_iso_code, id_lang);
	}

	function showTcContentLangField(lang_iso_code, id_lang)
	{
		displayHideLangField('tcBlockContent_btn', 'tcBlockContentAll', 'tcBlockContentDiv_', lang_iso_code, id_lang);
	}

	function displayHideLangField(btnField, classField, nameField, lang_iso_code, id_lang)
	{
		$('#'+btnField).html(lang_iso_code + ' <span class="caret"></span>');
		$('.'+classField).hide();
		$('#'+nameField+id_lang).show();
	}
</script>