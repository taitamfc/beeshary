{*
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SAS Comptoir du Code
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SAS Comptoir du Code is strictly forbidden.
 * In order to obtain a license, please contact us: contact@comptoirducode.com
 *
 * @package   cdc_googletagmanager
 * @author    Vincent - Comptoir du Code
 * @copyright Copyright(c) 2015-2016 SAS Comptoir du Code
 * @license   Commercial license
 *}
<style>
	.cdc-info {
		background: #d9edf7;
		color: #1b809e;
		padding: 7px;
		/*border-left: solid 3px #1b809e;*/
		margin-top: 50px;
		font-weight: normal;
	}

	.cdc-warning-box {
		background: #FFF3D7;
		color: #D2A63C;
		padding: 16px;
		font-weight: bold;
		border: solid 2px #fcc94f;
		margin: 30px 0;
		text-align: center;
		font-size: 1.2em;
	}
</style>
<div class="bootstrap">


<div class="panel text-center">
	<img src="{$module_dir|escape:'htmlall':'UTF-8'}/logo.png" >
	<h1>
		Google Tag Manager Enhanced E-commerce
		<br /><small>GTM integration + Enhanced E-commerce + Google Customer Reviews</small>
	</h1>
</div>

<div>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active">
    	<a href="#tagmanager" role="tab" data-toggle="tab">{l s='Google Tag Manager' mod='cdc_googletagmanager'}</a>
    </li>
    <li role="presentation">
    	<a href="#customerreviews" role="tab" data-toggle="tab">{l s='Google Customer Reviews' mod='cdc_googletagmanager'}</a>
    </li>
  </ul>

	<!-- Tab panes -->
	<form id="configuration_form" class="defaultForm form-horizontal cdc_googletagmanager" method="post" action="{$form_action|escape:'htmlall':'UTF-8'}">
		<div class="tab-content">

			<!-- GENERAL GTM SETTINGS -->
			<div role="tabpanel" class="tab-pane active panel" id="tagmanager">
				<div class="panel-body">

					<div class="form-group">
						<label class="control-label col-lg-3"><b>{l s='Base configuration' mod='cdc_googletagmanager'}</b></label>
						<div class="margin-form col-lg-9"></div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-3" rel="only_map">{l s='Enable Google Tag Manager' mod='cdc_googletagmanager'}</label>
						<div class="margin-form col-lg-9">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="CDC_GTM_ENABLE" id="ENABLE_ON"  value="1" {if $CDC_GTM_ENABLE}checked{/if} /><label for="ENABLE_ON" class="label-checkbox">{l s='Yes' mod='cdc_googletagmanager'}</label>
								<input type="radio" name="CDC_GTM_ENABLE" id="ENABLE_OFF" value="0" {if !$CDC_GTM_ENABLE}checked{/if} /><label for="ENABLE_OFF" class="label-checkbox">{l s='No' mod='cdc_googletagmanager'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3" for="CDC_GTM_GTMID">Google Tag Manager ID</label>
						<div class="margin-form col-lg-3">
							<input type="text" class="form-control" id="CDC_GTM_GTMID" placeholder="GTM-XXXXXX" name="CDC_GTM_GTMID" value="{$CDC_GTM_GTMID|escape:'htmlall':'UTF-8'}">
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3" rel="only_map">{l s='Enable Automatic Re-send Orders' mod='cdc_googletagmanager'}</label>
						<div class="margin-form col-lg-3">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="CDC_GTM_ENABLE_RESEND" id="ENABLE_RESEND_ON"  value="1" {if $CDC_GTM_ENABLE_RESEND}checked{/if} /><label for="ENABLE_RESEND_ON" class="label-checkbox">{l s='Yes' mod='cdc_googletagmanager'}</label>
								<input type="radio" name="CDC_GTM_ENABLE_RESEND" id="ENABLE_RESEND_OFF" value="0" {if !$CDC_GTM_ENABLE_RESEND}checked{/if} /><label for="ENABLE_RESEND_OFF" class="label-checkbox">{l s='No' mod='cdc_googletagmanager'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
						<div class="col-lg-6">
							<p class="cdc-info">{l s='When an order couldn\'t be sent in the first place, the module tries to re-send it later. However, the date of the order shown in Analytics will be the date when the order is sent and not the real date of the order.' mod='cdc_googletagmanager'}</p>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3" for="CDC_GTM_RESEND_DAYS">Maximum days to re-send orders</label>
						<div class="margin-form col-lg-3">
							<input type="number" class="form-control" id="CDC_GTM_RESEND_DAYS" name="CDC_GTM_RESEND_DAYS" value="{$CDC_GTM_RESEND_DAYS|escape:'htmlall':'UTF-8'}" min="1" step="1">
						</div>
						<div class="col-lg-6">
							<p class="cdc-info">{l s='Maximum number of days after the order has been placed to re-send it. If this number is too small, some orders won\'t be re-sent. If the number is too big, re-sent orders may be out of sync (date too long after the real date).' mod='cdc_googletagmanager'}</p>
						</div>
					</div>


                    <div class="form-group">
                        <label class="control-label col-lg-3" for="CDC_GTM_MAX_CAT_ITEMS">Maximum category items to send in datalayer</label>
                        <div class="margin-form col-lg-3">
                            <input type="number" class="form-control" id="CDC_GTM_MAX_CAT_ITEMS" name="CDC_GTM_MAX_CAT_ITEMS" value="{$CDC_GTM_MAX_CAT_ITEMS|escape:'htmlall':'UTF-8'}" min="1" step="1">
                        </div>
                        <div class="col-lg-6">
                            <p class="cdc-info">{l s='Maximum number of items sent to datalayer in category pages. If ou have big product name, please lower this value so the datalayer does not exceed size limit.' mod='cdc_googletagmanager'}</p>
                        </div>
                    </div>


                    <hr />

                    <div class="form-group">
                        <label class="control-label col-lg-3"><b>{l s='Customize data format' mod='cdc_googletagmanager'}</b></label>
                        <div class="margin-form col-lg-9"></div>
                    </div>


					<div class="form-group">
						<label class="control-label col-lg-3" for="CDC_GTM_DISPLAY_VARIANT_ID">{l s='How to display id for product with variant' mod='cdc_googletagmanager'}</label>
						<div class="margin-form col-lg-3 form-inline">
							<select class="form-control" id="CDC_GTM_DISPLAY_VARIANT_ID" name="CDC_GTM_DISPLAY_VARIANT_ID">
								{foreach from=$CDC_GTM_DISPLAY_VARIANT_ID_LIST key=k item=v}
									<option value="{$k}" {if $k == $CDC_GTM_DISPLAY_VARIANT_ID}selected="selected"{/if}>{$v}</option>
								{/foreach}
							</select>
						</div>
						<div class="col-lg-6">
							<p class="cdc-info">{l s='Always display variant id with product id (PRODUCT_ID-VARIANT_ID)' mod='cdc_googletagmanager'}</p>
						</div>
					</div>


                    <div class="form-group">
                        <label class="control-label col-lg-3" rel="only_map">{l s='Display category hierarchy' mod='cdc_googletagmanager'}</label>
                        <div class="margin-form col-lg-3">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="CDC_GTM_CATEGORY_HIERARCHY" id="CATEGORY_HIERARCHY_ON"  value="1" {if $CDC_GTM_CATEGORY_HIERARCHY}checked{/if} /><label for="CATEGORY_HIERARCHY_ON" class="label-checkbox">{l s='Yes' mod='cdc_googletagmanager'}</label>
                                <input type="radio" name="CDC_GTM_CATEGORY_HIERARCHY" id="CATEGORY_HIERARCHY_OFF" value="0" {if !$CDC_GTM_CATEGORY_HIERARCHY}checked{/if} /><label for="CATEGORY_HIERARCHY_OFF" class="label-checkbox">{l s='No' mod='cdc_googletagmanager'}</label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                        <div class="col-lg-6">
                            <p class="cdc-info">{l s='Display category with all parents categories: "/cat1/cat2/cat3" instead of "cat3"' mod='cdc_googletagmanager'}</p>
                        </div>
                    </div>

                    
                    <div class="form-group">
                        <label class="control-label col-lg-3" for="CDC_GTM_PRODUCT_ID_FIELD">{l s='Product id' mod='cdc_googletagmanager'}</label>
                        <div class="margin-form col-lg-9 form-inline">
                            <select class="form-control" id="CDC_GTM_PRODUCT_ID_FIELD" name="CDC_GTM_PRODUCT_ID_FIELD">
                                {foreach from=$CDC_GTM_ID_TYPES item='product_id_field'}
                                <option value="{$product_id_field}" {if $product_id_field == $CDC_GTM_PRODUCT_ID_FIELD}selected="selected"{/if}>{$product_id_field}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-3" for="CDC_GTM_PRODUCT_NAME_FIELD">{l s='Product name' mod='cdc_googletagmanager'}</label>
                        <div class="margin-form col-lg-9 form-inline">
                            {$product_name_fields = ['name','link_rewrite', 'id']}
                            <select class="form-control" id="CDC_GTM_PRODUCT_NAME_FIELD" name="CDC_GTM_PRODUCT_NAME_FIELD">
                                {foreach from=$product_name_fields item='product_name_field'}
                                <option value="{$product_name_field}" {if $product_name_field == $CDC_GTM_PRODUCT_NAME_FIELD}selected="selected"{/if}>{$product_name_field}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-3" for="CDC_GTM_CATEGORY_NAME_FIELD">{l s='Category name' mod='cdc_googletagmanager'}</label>
                        <div class="margin-form col-lg-9 form-inline">
                            {$category_name_fields = ['name','link_rewrite', 'id']}
                            <select class="form-control" id="CDC_GTM_CATEGORY_NAME_FIELD" name="CDC_GTM_CATEGORY_NAME_FIELD">
                                {foreach from=$category_name_fields item='category_name_field'}
                                <option value="{$category_name_field}" {if $category_name_field == $CDC_GTM_CATEGORY_NAME_FIELD}selected="selected"{/if}>{$category_name_field}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>


					<div class="form-group">
						<label class="control-label col-lg-3" for="CDC_GTM_DATA_LANGUAGE">{l s='Language used for the datalayer' mod='cdc_googletagmanager'}</label>
						<div class="margin-form col-lg-6 form-inline">
							<select class="form-control" id="CDC_GTM_DATA_LANGUAGE" name="CDC_GTM_DATA_LANGUAGE">
								{foreach from=$CDC_GTM_DATA_LANGUAGES key=k item=v}
									<option value="{$k}" {if $k == $CDC_GTM_DATA_LANGUAGE}selected="selected"{/if}>{$v}</option>
								{/foreach}
							</select>
						</div>
					</div>

					<hr />

					<div class="form-group">
						<label class="control-label col-lg-3"><b>{l s='Google Analytics User ID feature' mod='cdc_googletagmanager'}</b></label>
						<div class="margin-form col-lg-9"></div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-3" rel="only_map">{l s='Add User ID in datalayer' mod='cdc_googletagmanager'}</label>
						<div class="margin-form col-lg-3">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="CDC_GTM_ENABLE_USERID" id="ENABLE_USERID_ON"  value="1" {if $CDC_GTM_ENABLE_USERID}checked{/if} /><label for="ENABLE_USERID_ON" class="label-checkbox">{l s='Yes' mod='cdc_googletagmanager'}</label>
								<input type="radio" name="CDC_GTM_ENABLE_USERID" id="ENABLE_USERID_OFF" value="0" {if !$CDC_GTM_ENABLE_USERID}checked{/if} /><label for="ENABLE_USERID_OFF" class="label-checkbox">{l s='No' mod='cdc_googletagmanager'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
						<div class="col-lg-6">
							<p class="cdc-info">{l s='Add variables "userId" and "userLogged" in datalayer' mod='cdc_googletagmanager'}.</p>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3" rel="only_map">{l s='Add User ID in datalayer for guests' mod='cdc_googletagmanager'}</label>
						<div class="margin-form col-lg-3">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="CDC_GTM_ENABLE_GUESTID" id="ENABLE_GUESTID_ON"  value="1" {if $CDC_GTM_ENABLE_GUESTID}checked{/if} /><label for="ENABLE_GUESTID_ON" class="label-checkbox">{l s='Yes' mod='cdc_googletagmanager'}</label>
								<input type="radio" name="CDC_GTM_ENABLE_GUESTID" id="ENABLE_GUESTID_OFF" value="0" {if !$CDC_GTM_ENABLE_GUESTID}checked{/if} /><label for="ENABLE_GUESTID_OFF" class="label-checkbox">{l s='No' mod='cdc_googletagmanager'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
						<div class="col-lg-6">
							<p class="cdc-info">{l s='Variable "userId" is set with guest_[GUEST_ID] when user is guest. This option allows tracking of user not loggued accross multiple sessions' mod='cdc_googletagmanager'}.</p>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3" rel="only_map">{l s='Asynchronous loading of User Info' mod='cdc_googletagmanager'}</label>
						<div class="margin-form col-lg-3">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="CDC_GTM_ASYNC_USER_INFO" id="ASYNC_USER_INFO_ON"  value="1" {if $CDC_GTM_ASYNC_USER_INFO}checked{/if} /><label for="ASYNC_USER_INFO_ON" class="label-checkbox">{l s='Yes' mod='cdc_googletagmanager'}</label>
								<input type="radio" name="CDC_GTM_ASYNC_USER_INFO" id="ASYNC_USER_INFO_OFF" value="0" {if !$CDC_GTM_ASYNC_USER_INFO}checked{/if} /><label for="ASYNC_USER_INFO_OFF" class="label-checkbox">{l s='No' mod='cdc_googletagmanager'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
						<div class="col-lg-6">
							<p class="cdc-info">{l s='If you have a full page cache system, you will need to load user informations asynchronously' mod='cdc_googletagmanager'}.</p>
						</div>
					</div>

					<hr />

					<div class="form-group">
						<label class="control-label col-lg-3"><b>{l s='Remarketing' mod='cdc_googletagmanager'}</b></label>
						<div class="margin-form col-lg-9"></div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-3" rel="only_map">{l s='Enable Remarketing Parameters' mod='cdc_googletagmanager'}</label>
						<div class="margin-form col-lg-9">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="CDC_GTM_REMARKETING_ENABLE" id="REMARKETING_ENABLE_ON"  value="1" {if $CDC_GTM_REMARKETING_ENABLE}checked{/if} /><label for="REMARKETING_ENABLE_ON" class="label-checkbox">{l s='Yes' mod='cdc_googletagmanager'}</label>
								<input type="radio" name="CDC_GTM_REMARKETING_ENABLE" id="REMARKETING_ENABLE_OFF" value="0" {if !$CDC_GTM_REMARKETING_ENABLE}checked{/if} /><label for="REMARKETING_ENABLE_OFF" class="label-checkbox">{l s='No' mod='cdc_googletagmanager'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3" for="CDC_GTM_REMARKETING_PRODUCTID">{l s='Product ID in Merchant Center' mod='cdc_googletagmanager'}</label>
						<div class="margin-form col-lg-9 form-inline">
							<select class="form-control" id="CDC_GTM_REMARKETING_PRODUCTID" name="CDC_GTM_REMARKETING_PRODUCTID">
								{foreach from=$CDC_GTM_ID_TYPES item='product_identifier'}
								<option value="{$product_identifier}" {if $product_identifier == $CDC_GTM_REMARKETING_PRODUCTID}selected="selected"{/if}>{$product_identifier}</option>
								{/foreach}
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3" for="CDC_GTM_REMARKETING_PRODUCTPREF">{l s='Product ID prefix' mod='cdc_googletagmanager'}</label>
						<div class="margin-form col-lg-3">
							<input type="text" class="form-control" id="CDC_GTM_REMARKETING_PRODUCTPREF" placeholder="" name="CDC_GTM_REMARKETING_PRODUCTPREF" value="{$CDC_GTM_REMARKETING_PRODUCTPREF|escape:'htmlall':'UTF-8'}">
						</div>
                        <div class="col-lg-6">
                            <p class="cdc-info">{l s='You can add variables to the product id prefix. Available variables are: {lang} / {LANG} -> replaced with current language ISO code (en, fr...). For example you can use: FEED1_{LANG}_' mod='cdc_googletagmanager'}</p>
                        </div>
					</div>

				</div>
			</div>

			<!-- GOOGLE CUSTOMER REVIEWS SETTINGS -->
			<div role="tabpanel" class="tab-pane panel" id="customerreviews" {if (version_compare($CDC_PS_VERSION, '1.6', '<'))}style="display: block;"{/if}>
				<div class="panel-body">
					<div class="form-group">
						<label class="control-label col-lg-3" rel="only_map">{l s='Enable Google Customer Reviews' mod='cdc_googletagmanager'}</label>
						<div class="margin-form col-lg-3">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="CDC_GTM_GCR_ENABLE" id="GCR_ENABLE_ON"  value="1" {if $CDC_GTM_GCR_ENABLE}checked{/if} /><label for="GCR_ENABLE_ON" class="label-checkbox">{l s='Yes' mod='cdc_googletagmanager'}</label>
								<input type="radio" name="CDC_GTM_GCR_ENABLE" id="GCR_ENABLE_OFF" value="0" {if !$CDC_GTM_GCR_ENABLE}checked{/if} /><label for="GCR_ENABLE_OFF" class="label-checkbox">{l s='No' mod='cdc_googletagmanager'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3" rel="only_map">{l s='Add the Customer Reviews badge code' mod='cdc_googletagmanager'}</label>
						<div class="margin-form col-lg-3">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="CDC_GTM_GCR_BADGE_CODE" id="GCR_BADGE_CODE_ON"  value="1" {if $CDC_GTM_GCR_BADGE_CODE}checked{/if} /><label for="GCR_BADGE_CODE_ON" class="label-checkbox">{l s='Yes' mod='cdc_googletagmanager'}</label>
								<input type="radio" name="CDC_GTM_GCR_BADGE_CODE" id="GCR_BADGE_CODE_OFF" value="0" {if !$CDC_GTM_GCR_BADGE_CODE}checked{/if} /><label for="GCR_BADGE_CODE_OFF" class="label-checkbox">{l s='No' mod='cdc_googletagmanager'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
						<div class="col-lg-6">
							<p class="cdc-info">{l s='You must set this option to YES, unless you have already added the badge code in your source file or with Google Tag Manager' mod='cdc_googletagmanager'}.</p>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3" for="CDC_GTM_GCR_MERCHANT_ID">{l s='Merchant ID' mod='cdc_googletagmanager'}</label>
						<div class="margin-form col-lg-3">
							<input type="text" class="form-control" id="CDC_GTM_GCR_MERCHANT_ID" placeholder="000000" name="CDC_GTM_GCR_MERCHANT_ID" value="{$CDC_GTM_GCR_MERCHANT_ID|escape:'htmlall':'UTF-8'}">
						</div>
						<div class="col-lg-6">
							<p class="cdc-info">{l s='You can find your Google Merchant ID ' mod='cdc_googletagmanager'}<a href="https://merchants.google.com/mc/customerreviews/configuration" target="_blank">{l s='in your Google merchants center' mod='cdc_googletagmanager'}</a>.</p>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3" for="CDC_GTM_GCR_BADGE_POSITION">{l s='Badge position' mod='cdc_googletagmanager'}</label>
						<div class="margin-form col-lg-3">
							<select class="form-control" id="CDC_GTM_GCR_BADGE_POSITION" name="CDC_GTM_GCR_BADGE_POSITION">
								<option value="BOTTOM_RIGHT" {if $CDC_GTM_GCR_BADGE_POSITION == "BOTTOM_RIGHT"}selected="selected"{/if}>
									BOTTOM_RIGHT
								</option>
								<option value="BOTTOM_LEFT" {if $CDC_GTM_GCR_BADGE_POSITION == "BOTTOM_LEFT"}selected="selected"{/if}>
									BOTTOM_LEFT
								</option>
							</select>
						</div>
						<div class="col-lg-6">
							<p class="cdc-info">{l s='Position of the Google Customer Reviews badge.' mod='cdc_googletagmanager'}</p>
						</div>
					</div>

					<hr />

					<div class="form-group">
						<label class="control-label col-lg-3" rel="only_map">{l s='Enable the order confirmation module' mod='cdc_googletagmanager'}</label>
						<div class="margin-form col-lg-3">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="CDC_GTM_GCR_ORDER_CODE" id="GCR_ORDER_CODE_ON"  value="1" {if $CDC_GTM_GCR_ORDER_CODE}checked{/if} /><label for="GCR_ORDER_CODE_ON" class="label-checkbox">{l s='Yes' mod='cdc_googletagmanager'}</label>
								<input type="radio" name="CDC_GTM_GCR_ORDER_CODE" id="GCR_ORDER_CODE_OFF" value="0" {if !$CDC_GTM_GCR_ORDER_CODE}checked{/if} /><label for="GCR_ORDER_CODE_OFF" class="label-checkbox">{l s='No' mod='cdc_googletagmanager'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
						<div class="col-lg-6">
							<p class="cdc-info">{l s='You must set this option to YES, unless you have already added the order confirmation code in your order confirmation page' mod='cdc_googletagmanager'}.</p>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3" for="CDC_GTM_GCR_DELIVERY_DAYS">{l s='Delivery delay (days)' mod='cdc_googletagmanager'}</label>
						<div class="margin-form col-lg-3">
							<input type="number" class="form-control" id="CDC_GTM_GCR_DELIVERY_DAYS" placeholder="X" name="CDC_GTM_GCR_DELIVERY_DAYS" value="{$CDC_GTM_GCR_DELIVERY_DAYS}" min="0" step="1">
						</div>
						<div class="col-lg-6">
							<p class="cdc-info">{l s='The estimated number of days before an order is delivered.' mod='cdc_googletagmanager'}</p>
						</div>
					</div>

				</div>
			</div>
		</div>

		<div class="text-right">
			<button type="submit" value="1" id="configuration_form_submit_btn" name="submitcdc_googletagmanager" class="button btn btn-default">
				<i class="process-icon-save"></i> {l s='Save All' mod='cdc_googletagmanager'}
			</button>
		</div>
	</form>


	<div style="margin-top: 10px;">
		<p>
			<a href="https://comptoirducode.com/prestashop/modules/google-tag-manager/documentation-google-tag-manager-prestashop/" target="_blank" class="btn btn-default">{l s='Read the module documentation' mod='cdc_googletagmanager'}</a>

            <a href="{$form_action}&force_check_hooks" class="btn btn-default">{l s='Check hooks installation' mod='cdc_googletagmanager'}</a>

            <a href="http://addons.prestashop.com/ratings.php?id_product=23806" class="btn btn-default">{l s='Rate the module' mod='cdc_googletagmanager'}</a>

            <a href="https://addons.prestashop.com/contact-community.php?id_product=23806" class="btn btn-default">{l s='Contact support' mod='cdc_googletagmanager'}</a>
		</p>

	</div>

	<div style="margin-top: 15px; border-top: 1px dotted #999;padding-top: 15px;">
		<p>
			<b>{l s='This module fits your needs?' mod='cdc_googletagmanager'}</b><br>
			<a href="http://addons.prestashop.com/ratings.php?id_product=23806" target="_blank">{l s='Thanks to rate-us on Prestashop marketplace' mod='cdc_googletagmanager'}</a>. {l s='The more we have ratings and satisfied customers, the more we enjoy to develop new features for you!' mod='cdc_googletagmanager'}
		</p>
	</div>


</div>
</div>


<script>
$(document).ready(function() {

	$('#CDC_GTM_GTS_BADGE_POSITION').change(function() {
		if($(this).val() == 'USER_DEFINED') {
			$('#wrapper_CDC_GTM_GTS_CONTAINER').show(400).highlight();
		} else {
			$('#wrapper_CDC_GTM_GTS_CONTAINER').hide(400);
		}
	}).change();
});
</script>
