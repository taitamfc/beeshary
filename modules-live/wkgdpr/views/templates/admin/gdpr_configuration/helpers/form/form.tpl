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

{* General Configuration *}
<div class="panel">
	<div class="panel-heading">
		<i class="icon-cogs"></i> {l s='GDPR General Configuration' mod='wkgdpr'}
	</div>
    <form class="form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&add{$table|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<label class="col-sm-3 control-label">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='If enable, Admin will delete customer data on his request. If disabed, customer will be able to delete his data by himself.' mod='wkgdpr'}">{l s='Customer data delete approval' mod='wkgdpr'}</span>
			</label>
			<div class="col-sm-6">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" value="1" id="wk_gdpr_customer_data_delete_approve_on" name="WK_GDPR_CUSTOMER_DATA_DELETE_APPROVE"
					{if $WK_GDPR_CUSTOMER_DATA_DELETE_APPROVE}
						checked="checked"
					{/if}>
					<label for="wk_gdpr_customer_data_delete_approve_on">{l s='Yes' mod='wkgdpr'}</label>
					<input type="radio" value="0" id="wk_gdpr_customer_data_delete_approve_off" name="WK_GDPR_CUSTOMER_DATA_DELETE_APPROVE"
					{if !$WK_GDPR_CUSTOMER_DATA_DELETE_APPROVE}
						checked="checked"
					{/if}>
					<label for="wk_gdpr_customer_data_delete_approve_off">{l s='No' mod='wkgdpr'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label required">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='If agreement text is not set for any GDPR agreement checkbox, Then this content will be considered as default GDPR agreement text.' mod='wkgdpr'}">{l s='GDPR default agreement content' mod='wkgdpr'}</span>
			</label>
			<div class="col-sm-9">
				<textarea name="WK_GDPR_DEFAULT_AGREEMENT_CONTENT" class="form-control wk_tinymce">{if isset($WK_GDPR_DEFAULT_AGREEMENT_CONTENT) && $WK_GDPR_DEFAULT_AGREEMENT_CONTENT}{$WK_GDPR_DEFAULT_AGREEMENT_CONTENT}{else if isset($smarty.post.default_agreement_content)}{$smarty.post.default_agreement_content}{/if}</textarea>
			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" name="submitGDPRGeneralConfig" class="btn btn-default pull-right">
				<i class="process-icon-save"></i>{l s='Save' mod='wkgdpr'}
			</button>
		</div>
	</form>
</div>

{* Mail Configuration *}
<div class="panel">
	<div class="panel-heading">
		<i class="icon-cogs"></i> {l s='GDPR Mail Configuration' mod='wkgdpr'}
	</div>
    <form class="form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&add{$table|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<label class="col-sm-3 control-label">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='If enable, Admin will recieve an email when customer requests for personal data update.' mod='wkgdpr'}">{l s='Email to admin on data update request' mod='wkgdpr'}</span>
			</label>
			<div class="col-sm-6">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" value="1" id="wk_gdpr_admin_mail_data_update_request_on" name="wk_gdpr_admin_mail_data_update_request"
					{if $WK_GDPR_ADMIN_MAIL_DATA_UPDATE_REQUEST}
						checked="checked"
					{/if}>
					<label for="wk_gdpr_admin_mail_data_update_request_on">{l s='Yes' mod='wkgdpr'}</label>
					<input type="radio" value="0" id="wk_gdpr_admin_mail_data_update_request_off" name="wk_gdpr_admin_mail_data_update_request"
					{if !$WK_GDPR_ADMIN_MAIL_DATA_UPDATE_REQUEST}
						checked="checked"
					{/if}>
					<label for="wk_gdpr_admin_mail_data_update_request_off">{l s='No' mod='wkgdpr'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='If enable, Admin will recieve an email when customer requests for personal data erasure.' mod='wkgdpr'}">{l s='Email to admin on data erasure request' mod='wkgdpr'}</span>
			</label>
			<div class="col-sm-6">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" value="1" id="wk_gdpr_admin_mail_data_erasure_request_on" name="wk_gdpr_admin_mail_data_erasure_request"
					{if $WK_GDPR_ADMIN_MAIL_DATA_ERASURE_REQUEST}
						checked="checked"
					{/if}>
					<label for="wk_gdpr_admin_mail_data_erasure_request_on">{l s='Yes' mod='wkgdpr'}</label>
					<input type="radio" value="0" id="wk_gdpr_admin_mail_data_erasure_request_off" name="wk_gdpr_admin_mail_data_erasure_request"
					{if !$WK_GDPR_ADMIN_MAIL_DATA_ERASURE_REQUEST}
						checked="checked"
					{/if}>
					<label for="wk_gdpr_admin_mail_data_erasure_request_off">{l s='No' mod='wkgdpr'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" name="submitGDPRMailConfig" class="btn btn-default pull-right">
				<i class="process-icon-save"></i>{l s='Save' mod='wkgdpr'}
			</button>
		</div>
	</form>
</div>

{* Cookie law Configuration *}
<div class="panel">
	<div class="panel-heading">
		<i class="icon-cogs"></i> {l s='EU Cookie Law Configuration' mod='wkgdpr'}
	</div>
    <form class="form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&add{$table|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<label class="col-sm-3 control-label">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='If disabled, Cookie block will not display in the front end of the website.' mod='wkgdpr'}">{l s='Show cookie block on website' mod='wkgdpr'}</span>
			</label>
			<div class="col-sm-6">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" value="1" id="wk_gdpr_cookie_block_enable_on" name="WK_GDPR_COOKIE_BLOCK_ENABLE"
					{if $WK_GDPR_COOKIE_BLOCK_ENABLE}
						checked="checked"
					{/if}>
					<label for="wk_gdpr_cookie_block_enable_on">{l s='Yes' mod='wkgdpr'}</label>
					<input type="radio" value="0" id="wk_gdpr_cookie_block_enable_off" name="WK_GDPR_COOKIE_BLOCK_ENABLE"
					{if !$WK_GDPR_COOKIE_BLOCK_ENABLE}
						checked="checked"
					{/if}>
					<label for="wk_gdpr_cookie_block_enable_off">{l s='No' mod='wkgdpr'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		<div class="cookie_block_fields {if !$WK_GDPR_COOKIE_BLOCK_ENABLE}hidden{/if}">
			<div class="form-group">
				<label class="col-sm-3 control-label">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select the position of the cookie block.' mod='wkgdpr'}">{l s='Cookie block position' mod='wkgdpr'}</span>
				</label>
				<div class="col-sm-9">
					<div class="col-sm-4">
						<select name="WK_GDPR_COOKIE_BLOCK_POSITION">
							<option value="left" {if $WK_GDPR_COOKIE_BLOCK_POSITION == 'left'}selected="selected"{/if}>{l s='Left' mod='wkgdpr'}</option>
							<option value="right" {if $WK_GDPR_COOKIE_BLOCK_POSITION == 'right'}selected="selected"{/if}>{l s='Right' mod='wkgdpr'}</option>
							{* <option value="top" {if $WK_GDPR_COOKIE_BLOCK_POSITION == 'top'}selected="selected"{/if}>{l s='Top' mod='wkgdpr'}</option>
							<option value="bottom" {if $WK_GDPR_COOKIE_BLOCK_POSITION == 'bottom'}selected="selected"{/if}>{l s='Bottom' mod='wkgdpr'}</option> *}
						</select>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select the color which you want to set as cookie block background color.' mod='wkgdpr'}">{l s='Cookie block background color' mod='wkgdpr'}</span>
				</label>
				<div class="input-group col-lg-3">
					<input type="color" name="WK_GDPR_COOKIE_BLOCK_BG_COLOR" class="form-control mColorPickerInput mColorPicker" data-hex="true"
					value="{if isset($smarty.post.WK_GDPR_COOKIE_BLOCK_BG_COLOR)}{$smarty.post.WK_GDPR_COOKIE_BLOCK_BG_COLOR|escape:'html':'UTF-8'}{elseif isset($WK_GDPR_COOKIE_BLOCK_BG_COLOR) && $WK_GDPR_COOKIE_BLOCK_BG_COLOR}{$WK_GDPR_COOKIE_BLOCK_BG_COLOR|escape:'html':'UTF-8'}{/if}" readonly/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select the color which you want to set as cookie block text color.' mod='wkgdpr'}">{l s='Cookie block text color' mod='wkgdpr'}</span>
				</label>
				<div class="input-group col-lg-3">
					<input type="color" name="WK_GDPR_COOKIE_BLOCK_TEXT_COLOR" class="form-control mColorPickerInput mColorPicker" data-hex="true"
					value="{if isset($smarty.post.WK_GDPR_COOKIE_BLOCK_TEXT_COLOR)}{$smarty.post.WK_GDPR_COOKIE_BLOCK_TEXT_COLOR|escape:'html':'UTF-8'}{elseif isset($WK_GDPR_COOKIE_BLOCK_TEXT_COLOR) && $WK_GDPR_COOKIE_BLOCK_TEXT_COLOR}{$WK_GDPR_COOKIE_BLOCK_TEXT_COLOR|escape:'html':'UTF-8'}{/if}" readonly/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select the color which you want to set as cookie block border color.' mod='wkgdpr'}">{l s='Cookie block border color' mod='wkgdpr'}</span>
				</label>
				<div class="input-group col-lg-3">
					<input type="color" name="WK_GDPR_COOKIE_BLOCK_BORDER_COLOR" class="form-control mColorPickerInput mColorPicker" data-hex="true"
					value="{if isset($smarty.post.WK_GDPR_COOKIE_BLOCK_BORDER_COLOR)}{$smarty.post.WK_GDPR_COOKIE_BLOCK_BORDER_COLOR|escape:'html':'UTF-8'}{elseif isset($WK_GDPR_COOKIE_BLOCK_BORDER_COLOR) && $WK_GDPR_COOKIE_BLOCK_BORDER_COLOR}{$WK_GDPR_COOKIE_BLOCK_BORDER_COLOR|escape:'html':'UTF-8'}{/if}" readonly/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select the color which you want to set as cookie block\'s button background color.' mod='wkgdpr'}">{l s='Cookie block button background color' mod='wkgdpr'}</span>
				</label>
				<div class="input-group col-lg-3">
					<input type="color" name="WK_GDPR_COOKIE_BLOCK_BUTTON_BG_COLOR" class="form-control mColorPickerInput mColorPicker" data-hex="true"
					value="{if isset($smarty.post.WK_GDPR_COOKIE_BLOCK_BUTTON_BG_COLOR)}{$smarty.post.WK_GDPR_COOKIE_BLOCK_BUTTON_BG_COLOR|escape:'html':'UTF-8'}{elseif isset($WK_GDPR_COOKIE_BLOCK_BUTTON_BG_COLOR) && $WK_GDPR_COOKIE_BLOCK_BUTTON_BG_COLOR}{$WK_GDPR_COOKIE_BLOCK_BUTTON_BG_COLOR|escape:'html':'UTF-8'}{/if}" readonly/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select the color which you want to set as cookie block\'s button text color.' mod='wkgdpr'}">{l s='Cookie block button text color' mod='wkgdpr'}</span>
				</label>
				<div class="input-group col-lg-3">
					<input type="color" name="WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_COLOR" class="form-control mColorPickerInput mColorPicker" data-hex="true"
					value="{if isset($smarty.post.WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_COLOR)}{$smarty.post.WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_COLOR|escape:'html':'UTF-8'}{elseif isset($WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_COLOR) && $WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_COLOR}{$WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_COLOR|escape:'html':'UTF-8'}{/if}" readonly/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select the color which you want to set as cookie block\'s button border color.' mod='wkgdpr'}">{l s='Cookie block button border color' mod='wkgdpr'}</span>
				</label>
				<div class="input-group col-lg-3">
					<input type="color" name="WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_COLOR" class="form-control mColorPickerInput mColorPicker" data-hex="true"
					value="{if isset($smarty.post.WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_COLOR)}{$smarty.post.WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_COLOR|escape:'html':'UTF-8'}{elseif isset($WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_COLOR) && $WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_COLOR}{$WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_COLOR|escape:'html':'UTF-8'}{/if}" readonly/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select the color which you want to set as cookie block\'s button background color on hover.' mod='wkgdpr'}">{l s='Cookie block button background color on hover' mod='wkgdpr'}</span>
				</label>
				<div class="input-group col-lg-3">
					<input type="color" name="WK_GDPR_COOKIE_BLOCK_BUTTON_BG_HOVER_COLOR" class="form-control mColorPickerInput mColorPicker" data-hex="true"
					value="{if isset($smarty.post.WK_GDPR_COOKIE_BLOCK_BUTTON_BG_HOVER_COLOR)}{$smarty.post.WK_GDPR_COOKIE_BLOCK_BUTTON_BG_HOVER_COLOR|escape:'html':'UTF-8'}{elseif isset($WK_GDPR_COOKIE_BLOCK_BUTTON_BG_HOVER_COLOR) && $WK_GDPR_COOKIE_BLOCK_BUTTON_BG_HOVER_COLOR}{$WK_GDPR_COOKIE_BLOCK_BUTTON_BG_HOVER_COLOR|escape:'html':'UTF-8'}{/if}" readonly/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select the color which you want to set as cookie block\'s button text color on hover.' mod='wkgdpr'}">{l s='Cookie block button text color on hover' mod='wkgdpr'}</span>
				</label>
				<div class="input-group col-lg-3">
					<input type="color" name="WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_HOVER_COLOR" class="form-control mColorPickerInput mColorPicker" data-hex="true"
					value="{if isset($smarty.post.WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_HOVER_COLOR)}{$smarty.post.WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_HOVER_COLOR|escape:'html':'UTF-8'}{elseif isset($WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_HOVER_COLOR) && $WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_HOVER_COLOR}{$WK_GDPR_COOKIE_BLOCK_BUTTON_TEXT_HOVER_COLOR|escape:'html':'UTF-8'}{/if}" readonly/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select the color which you want to set as cookie block\'s button border color hover.' mod='wkgdpr'}">{l s='Cookie block button border color on hover' mod='wkgdpr'}</span>
				</label>
				<div class="input-group col-lg-3">
					<input type="color" name="WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_HOVER_COLOR" class="form-control mColorPickerInput mColorPicker" data-hex="true"
					value="{if isset($smarty.post.WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_HOVER_COLOR)}{$smarty.post.WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_HOVER_COLOR|escape:'html':'UTF-8'}{elseif isset($WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_HOVER_COLOR) && $WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_HOVER_COLOR}{$WK_GDPR_COOKIE_BLOCK_BUTTON_BORDER_HOVER_COLOR|escape:'html':'UTF-8'}{/if}" readonly/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Disable, if your don\'t want to show image on cookie block.' mod='wkgdpr'}">{l s='Show image on cookie block' mod='wkgdpr'}</span>
				</label>
				<div class="col-sm-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" value="1" id="wk_gdpr_cookie_block_image_show_on" name="WK_GDPR_COOKIE_BLOCK_IMAGE_SHOW"
						{if $WK_GDPR_COOKIE_BLOCK_IMAGE_SHOW}
							checked="checked"
						{/if}>
						<label for="wk_gdpr_cookie_block_image_show_on">{l s='Yes' mod='wkgdpr'}</label>
						<input type="radio" value="0" id="wk_gdpr_cookie_block_image_show_off" name="WK_GDPR_COOKIE_BLOCK_IMAGE_SHOW"
						{if !$WK_GDPR_COOKIE_BLOCK_IMAGE_SHOW}
							checked="checked"
						{/if}>
						<label for="wk_gdpr_cookie_block_image_show_off">{l s='No' mod='wkgdpr'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
			<div class="form-group {if !$WK_GDPR_COOKIE_BLOCK_IMAGE_SHOW}hidden{/if}" id="cookie_block_img_div">
				<label class="col-sm-3 control-label">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Upload the image icon which you want to show in your cookie block.' mod='wkgdpr'}">{l s='Cookie block Image' mod='wkgdpr'}</span>
				</label>
				<div class="col-sm-3">
					{assign var="cookieBlockUploadedImg" value="`$psModuleDir`wkgdpr/views/img/uploads/wk_cookie_block_img.png"}
					{if file_exists($cookieBlockUploadedImg)}
						<div class="cookieBlockUploadedImg" style="margin-bottom:3px;">
							<img class="img-thumbnail img-responsive" src="{$moduleDir|escape:'html':'UTF-8'}wkgdpr/views/img/uploads/wk_cookie_block_img.png">
						</div>
					{/if}
					<input type="file" name="wk_cookie_block_img">
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Enter the content you want to show in the cookie block.' mod='wkgdpr'}">{l s='Cookie block content' mod='wkgdpr'}</span>
				</label>
				<div class="col-sm-9">
					<textarea name="WK_GDPR_COOKIE_BLOCK_CONTENT" class="form-control wk_tinymce">{if isset($WK_GDPR_COOKIE_BLOCK_CONTENT) && $WK_GDPR_COOKIE_BLOCK_CONTENT}{$WK_GDPR_COOKIE_BLOCK_CONTENT}{else if isset($smarty.post.WK_GDPR_COOKIE_BLOCK_CONTENT)}{$smarty.post.WK_GDPR_COOKIE_BLOCK_CONTENT}{/if}</textarea>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" name="submitCookieLawConfig" class="btn btn-default pull-right">
				<i class="process-icon-save"></i>{l s='Save' mod='wkgdpr'}
			</button>
		</div>
	</form>
</div>
{*pop up for showinng error message*}
<div class="container">
	<div class="modal fade" id="showImagePopUp" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content alert alert-danger">
				<button type="button" class="close" data-dismiss="modal" id="closeModal">&times;</button>
				<p>{l s='Image size exceeds from maximum upload size.' mod='wkgdpr'}</p>
			</div>
		</div>
	</div>
</div>
