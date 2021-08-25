{*
* 2010-2021 Webkul
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
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="panel">
	<div class="panel-heading">
		<i class="icon-info"></i>
		{l s='Marketplace Webservice Details' mod='mpwebservice'}
	</div>
	<div class="form-wrapper">
		<p>{l s='Activate prestashop webservice' mod='mpwebservice'}:</p>
		<ul>
			<li>{l s='Go to ' mod='mpwebservice'} <a href="{$admin_webservice_link}" target="_blank">{l s='Advanced Parameters -> Webservice' mod='mpwebservice'}</a></li>
			<ul>
				<li>{l s='Enable PrestaShop\'s webservice' mod='mpwebservice'}</li>
				<li>{l s='Create a webservice key with only \'seller\' resource permission' mod='mpwebservice'}</li>
			</ul>
		</ul>
		<p>{l s='Marketplace Resources' mod='mpwebservice'}:</p>
		{if $mp_resources}
			<ul>
				{foreach $mp_resources as $resource}
					<li>{$resource}</li>
				{/foreach}
			</ul>
		{/if}

		<p>{l s='You can access the Marketplace API\'s using the above resource name' mod='mpwebservice'}</p>
		<p>{l s='Example' mod='mpwebservice' mod='mpwebservice'}: <a href="{$wk_shop_url}seller/sellerinfo" target="_blank">{$wk_shop_url}seller/sellerinfo?auth_key=SELLER_AUTHENTICATION_KEY</a></p>
	</div>
</div>

<div class="panel">
	<form action="{$current}{if isset($token) && $token}&token={$token}{/if}&configure=mpwebservice&tab_module=market_place&module_name=mpwebservice" class="defaultForm form-horizontal" method="post" enctype="multipart/form-data" novalidate>
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Configuration' mod='mpwebservice'}
		</div>
		<div class="panel-body">
			<div class="form-wrapper">
				<div class="form-group">
					<label class="control-label col-lg-4">
						<span>{l s='Admin WebService Key' mod='mpwebservice'}</span>
					</label>
					<div class="col-lg-8">
						<div class="row">
							<div class="col-lg-9">
								<input type="text" name="MP_ADMIN_WS_KEY" id="code" value="{$MP_ADMIN_WS_KEY}">
							</div>
							<div class="col-lg-2">
								<button type="button" class="btn btn-default" onclick="gencode(32)">
									{l s='Generate!' mod='mpwebservice'}
								</button>
							</div>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-4">
						<div class="help-block">
							{l s='For admin access use' mod='mpwebservice'}: admin_auth_key
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-4">
						<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Webservice key need to approve by admin' mod='mpwebservice'}">{l s='Seller can create Webservice request' mod='mpwebservice'}</span>
					</label>
					<div class="col-lg-8">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" value="1" id="seller_ws_key_on" name="WK_WS_SELLER_WEBSERVICE" {if $WK_WS_SELLER_WEBSERVICE == 1} checked="checked"{/if}>
							<label for="seller_ws_key_on">{l s='Yes' mod='mpwebservice'}</label>
							<input type="radio" value="0" id="seller_ws_key_off" name="WK_WS_SELLER_WEBSERVICE" {if $WK_WS_SELLER_WEBSERVICE == 0} checked="checked"{/if}>
							<label for="seller_ws_key_off">{l s='No' mod='mpwebservice'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-4">
						<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='If Yes, Seller Webservice request will automatically approved' mod='mpwebservice'}">{l s='Webservice request need to approve by admin' mod='mpwebservice'}</span>
					</label>
					<div class="col-lg-8">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" value="1" id="admin_approve_ws_key_on" name="WK_WS_KEY_ADMIN_APPROVE" {if $WK_WS_KEY_ADMIN_APPROVE == 1} checked="checked"{/if}>
							<label for="admin_approve_ws_key_on">{l s='Yes' mod='mpwebservice'}</label>
							<input type="radio" value="0" id="admin_approve_ws_key_off" name="WK_WS_KEY_ADMIN_APPROVE" {if $WK_WS_KEY_ADMIN_APPROVE == 0} checked="checked"{/if}>
							<label for="admin_approve_ws_key_off">{l s='No' mod='mpwebservice'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-4">
						{l s='Seller can change status of the Authentication key' mod='mpwebservice'}</span>
					</label>
					<div class="col-lg-8">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" value="1" id="seller_status_ws_key_on" name="WK_WS_KEY_SELLER_STATUS" {if $WK_WS_KEY_SELLER_STATUS == 1} checked="checked"{/if}>
							<label for="seller_status_ws_key_on">{l s='Yes' mod='mpwebservice'}</label>
							<input type="radio" value="0" id="seller_status_ws_key_off" name="WK_WS_KEY_SELLER_STATUS" {if $WK_WS_KEY_SELLER_STATUS == 0} checked="checked"{/if}>
							<label for="seller_status_ws_key_off">{l s='No' mod='mpwebservice'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" name="submitMpWsConfig" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save' mod='mpwebservice'}
			</button>
		</div>
	</form>
</div>
