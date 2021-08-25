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

<form novalidate="" enctype="multipart/form-data" method="post" action="{$current|escape:'html':'UTF-8'}{if isset($token) && $token}&amp;token={$token|escape:'html':'UTF-8'}{/if}" class="defaultForm form-horizontal AdminSelectTheme" id="marketplace_login_theme_form">
	<input type="hidden" value="1" name="submitAddmarketplace_login_theme">
	<div id="fieldset_0" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> {l s='Select Theme' mod='mpsellerwiselogin'}
		</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Themes' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9 ">
					<select id="login_theme" class=" fixed-width-xl" name="login_theme">
						{foreach from=$all_theme key=k item=v}
							<option value="{$v['id']|escape:'html':'UTF-8'}" {if $v['active']}selected="selected"{/if}>{$v['name']|escape:'html':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">{l s='Theme Preview' mod='mpsellerwiselogin'}</label>
				<div class="col-lg-9 ">
					<img src="{$prev_img|escape:'quotes':'UTF-8'}" class="img-responsive" id="theme_preview">
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="submit_login_theme" id="marketplace_login_theme_form_submit_btn" value="1" type="submit">
				<i class="process-icon-save"></i> {l s='Save' mod='mpsellerwiselogin'}
			</button>
			<button class="btn btn-default pull-right" name="edit_login_theme" id="marketplace_login_theme_form_edit_btn" value="2" type="submit">
				<i class="process-icon-edit"></i> {l s='Edit' mod='mpsellerwiselogin'}
			</button>
		</div>
	</div>
</form>

{strip}
	{addJsDef preview_img_dir=$preview_img_dir}
{/strip}