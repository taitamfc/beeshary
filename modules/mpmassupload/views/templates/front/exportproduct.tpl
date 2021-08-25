{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{block name='content'}
<div class="wk-mp-block">
	{hook h="displayMpMenu"}
	<div class="wk-mp-content">
		<div class="page-title" style="background-color:{$title_bg_color};">
			<span style="color:{$title_text_color};">{l s='EXPORT PRODUCT CSV' mod='mpmassupload'}</span>
		</div>
		<div class="wk-mp-right-column">
			<div class="left upload_request_container">
				<div class="upload_header_div">
			        <a class="btn btn-primary" href="{$link->getModuleLink('mpmassupload','massuploadview')}">
			          <span>{l s='Back to upload list' mod='mpmassupload'}</span>
			        </a>
			    </div>
				<div class="upload_form_div">
					<form class="form-horizontal" method='post' enctype="multipart/form-data" action="{$link->getModuleLink('mpmassupload','exportdetails')}" id="export_form">
						<div class="form-group row">
							<label class="control-label col-lg-4 required">{l s='Select Category: ' mod='mpmassupload'}</label>
							<div class="col-lg-4">
								<select name="mass_export_category" id="mass_export_category" class="form-control" data-allow-edit-combination="">
									<option value="1" selected="selected">{l s='Products' mod='mpmassupload'}</option>
									{if isset($massupload_combination_approve)}
										<option value="2">{l s='Combinations' mod='mpmassupload'}</option>
									{/if}
								</select>
							</div>
						</div>
                        <div class="form-group row" id='wk_export_category_product'>
							<label class="control-label col-lg-4 required">{l s='Select Columns: ' mod='mpmassupload'}</label>
							<div class="col-lg-8">
								<select name="wk_massupload_selected_col[]" class="multiselect-ui form-control" multiple="multiple">
                                    <option value="" disabled selected>{l s='Choose your Columns' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_product' value="mp_id_product">{l s='mp_id_product' mod='mpmassupload'}</option>
									<option class='wk_export_category_product' value="name">{l s='name' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_product' value="category_id">{l s='category_id' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_product' value="default_category">{l s='default_category' mod='mpmassupload'}</option>
									<option class='wk_export_category_product' value="short_description">{l s='short_description' mod='mpmassupload'}</option>
									<option class='wk_export_category_product' value="description">{l s='description' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_product' value="price">{l s='price' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_product' value="quantity">{l s='quantity' mod='mpmassupload'}</option>
									{if $allowDownloadImages}
                                    	<option class='wk_export_category_product' value="image_ref">{l s='image_ref' mod='mpmassupload'}</option>
									{/if}

									<option class='wk_export_category_combination' value="Seller Product ID">{l s='Seller Product ID' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_combination' value="Attribute (Name: Type)">{l s='Attribute (Name: Type)' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_combination' value="Value">{l s='Value' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_combination' value="reference">{l s='reference' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_combination' value="EAN13">{l s='EAN13' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_combination' value="UPC">{l s='UPC' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_combination' value="Wholesale Price">{l s='Wholesale Price' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_combination' value="Impact on Price">{l s='Impact on Price' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_combination' value="Quantity">{l s='Quantity' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_combination' value="Minimal quantity">{l s='Minimal quantity' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_combination' value="Impact on weight">{l s='Impact on weight' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_combination' value="Default (0=NO, 1=YES)">{l s='Default (0=NO, 1=YES)' mod='mpmassupload'}</option>
                                    <option class='wk_export_category_combination' value="Combination available date">{l s='Combination available date' mod='mpmassupload'}</option>
                                    {* <option class='wk_export_category_combination' value="Image ID">{l s='Image ID' mod='mpmassupload'}</option> *}
                                </select>
							</div>
						</div>
						<div class="form-group row wk_export_category_product" id='wk_export_product_lang'>
							<label class="control-label col-lg-4">{l s='Select Language: ' mod='mpmassupload'}</label>
							<div class="col-lg-8">
								<select name="wk_massupload_selected_lang[]" class="multiselect-ui form-control" multiple="multiple">
									{foreach $languages as $lang}
										<option class='wk_export_category_product' value="{$lang.id_lang}">{$lang.name}</option>
									{/foreach}
								</select>
							</div>
						</div>
						<div class="form-group wk_export_category_product row">
							<div class="offset-sm-4 col-sm-4">
								<button type="submit" class="btn btn-primary" id="export_csv" name="export_csv">
									<span>{l s='Export' mod='mpmassupload'}</span>
								</button>
							</div>
						</div>
						<div class="form-group wk_export_category_combination row">
							<div class="offset-sm-4 col-sm-4">
								<button type="submit" class="btn btn-primary" id="export_comb_csv" name="export_comb_csv">
									<span>{l s='Export' mod='mpmassupload'}</span>
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="form-group wk_export_zipfile row" style="display: none;">
	<div class="offset-sm-4 col-sm-4">
		<form method='post' enctype="multipart/form-data" action="{$link->getModuleLink('mpmassupload', 'exportdetails', ['zipexp'=> 1])}" id="wk_export_zip">
			<input type="hidden" name="zip_upload" value="1"/>
			<button type="submit" name="wk_export_zip" class="btn btn-primary">
				<span>{l s='Zip Export' mod='mpmassupload'}</span>
			</button>
		</form>
	</div>
</div>
{/block}
