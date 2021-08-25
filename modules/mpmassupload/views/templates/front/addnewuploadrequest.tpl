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
{if isset($error)}
	{if $error == 1}
		<div class="alert alert-danger">
			{l s='CSV Validate Error !!!' mod='mpmassupload'}<br>{l s='Invalid Product Name in csv file, at line number'  mod='mpmassupload'} {$count}.
		</div>
	{else if $error == 2}
		<div class="alert alert-danger">
			{l s= 'Product name is too long (32 chars max). at line' mod='mpmassupload'} {$count}.
		</div>
	{else if $error == 3}
		<div class="alert alert-danger">
			{l s= 'Some column are missing form csv file, Columns must be at the first line of csv file. please do not delete predefined header field references.' mod='mpmassupload'}
		</div>
	{else if $error	== 4}
		<div class="alert alert-danger">
			{l s= 'Uploaded csv file is blank.' mod='mpmassupload'}
		</div>
	{/if}
{/if}

<div class="wk-mp-block">
	{hook h="displayMpMenu"}
	<div class="wk-mp-content">
		<div class="page-title" style="background-color:{$title_bg_color};">
			<span style="color:{$title_text_color};">{l s='Add New Upload Request' mod='mpmassupload'}</span>
		</div>
		<div class="wk-mp-right-column">
			<div class="left upload_request_container">
				<div class="upload_header_div">
			        <a class="btn btn-primary" href="{$link->getModuleLink('mpmassupload','massuploadview')}">
			          <span>{l s='Back to upload list' mod='mpmassupload'}</span>
			        </a>
			    </div>
				<div class="upload_form_div">
					<div class="row">
						<div class="col-sm-12 col-xs-12">
							<div class="demo_files_div">
								<div class="tabs">
									<ul class="nav nav-tabs">
										<li class="nav-item">
											<a class="nav-link active" href="#wk-addCsv" data-toggle="tab">
												<i class="material-icons">&#xE88E;</i>
												{l s='Add CSV' mod='mpmassupload'}
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#wk-updateCsv" data-toggle="tab">
												<i class="material-icons">&#xE3C9;</i>
												{l s='Update CSV' mod='mpmassupload'}
											</a>
										</li>
									</ul>
									<div class="tab-content" id="tab-content">
										<div class="tab-pane fade in active" id="wk-addCsv">
											<div class="row">
												<div class="col-sm-9 col-md-10 col-xs-12">
													<p>
														<strong>
															{l s='1. ' mod='mpmassupload'}
															{l s='Product CSV: ' mod='mpmassupload'}
														</strong>
														{l s='Download this demo Csv for Products. Make a similar CSV file. Or Make changes in Demo CSV file.' mod='mpmassupload'}
													</p>
												</div>
												<div class="col-sm-3 col-md-2 col-xs-12">
													<a href="{$productCsv['add']|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
														<span>{l s='Download' mod='mpmassupload'}</span>
													</a>
												</div>
											</div>

											<div class="row">
												<div class="col-sm-9 col-md-10 col-xs-12">
													<p>
														<strong>
															{l s='2. ' mod='mpmassupload'}
															{l s='Languages CSV: ' mod='mpmassupload'}
														</strong>
														{l s='Download this demo Csv for Languages.' mod='mpmassupload'}
													</p>
												</div>
												<div class="col-sm-3 col-md-2 col-xs-12">
													<a href="{$languageCsv|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
														<span>{l s='Download' mod='mpmassupload'}</span>
													</a>
												</div>
											</div>

											<div class="row">
												<div class="col-sm-9 col-md-10 col-xs-12">
													<p>
														<strong>
															{l s='3. ' mod='mpmassupload'}
															{l s='Image .zip: ' mod='mpmassupload'}
														</strong>
														{l s='Download the sample Product image .zip file. Create a directory having respective product image and the name of this directory mentioned in product csv in "image_ref" column . After that make a directory named as "product_image" and move all the product image directory inside "product_image" directory keep image_ref directory inside it and now zip the "product_image" directory.' mod='mpmassupload'}
													</p>
												</div>
												<div class="col-sm-3 col-md-2 col-xs-12">
													<a href="{$demoZip|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
														<span>{l s='Download' mod='mpmassupload'}</span>
													</a>
												</div>
											</div>

											<div class="row">
												<div class="col-sm-12 col-xs-12">
													<p>
														<strong>
															{l s='4. ' mod='mpmassupload'}
															{l s='Image Extentions: ' mod='mpmassupload'}
														</strong>
														{l s='Image file must have (.jpg, .png, .gif) extension, otherwise other type of image will not be uploaded.' mod='mpmassupload'}
													</p>
												</div>
											</div>

											<div class="row">
												<div class="col-sm-9 col-md-10 col-xs-12">
													<p>
														<strong>
															{l s='5. ' mod='mpmassupload'}
															{l s='Category CSV: ' mod='mpmassupload'}
														</strong>
														{l s='Download Category csv to choose the "category_id".' mod='mpmassupload'}
													</p>
												</div>
												<div class="col-sm-3 col-md-2 col-xs-2">
													<a href="{$categoryCsv|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
														<span>{l s='Download' mod='mpmassupload'}</span>
													</a>
												</div>
											</div>

											{if isset($massupload_combination_approve)}
												<div class="row">
													<div class="col-sm-9 col-md-10 col-xs-12">
														<p>
															<strong>
																{l s='6. ' mod='mpmassupload'}
																{l s='Combination CSV: ' mod='mpmassupload'}
															</strong>
															{l s='Download this demo Csv file for product Combinations. Make a similar CSV file. Or just add the combinations details in Demo CSV file.' mod='mpmassupload'}
														</p>
													</div>
													<div class="col-sm-3 col-md-2 col-xs-12">
														<a href="{$combinationCsv|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
															<span>{l s='Download' mod='mpmassupload'}</span>
														</a>
													</div>
												</div>
											{/if}
										</div>
										<div class="tab-pane fade in" id="wk-updateCsv">
											<p>
												<div class="row">
													<div class="col-sm-9 col-md-10 col-xs-12">
														<p>
															<strong>
																{l s='1. ' mod='mpmassupload'}
																{l s='Product CSV: ' mod='mpmassupload'}
															</strong>
															{l s='Product id will be the mandatory field to update products via CSV file. For other fields -If no update required in any product mention “empty” for that particular field.' mod='mpmassupload'}
														</p>
													</div>
													<div class="col-sm-3 col-md-2 col-xs-12">
														<a href="{$productCsv['update']|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
															<span>{l s='Download' mod='mpmassupload'}</span>
														</a>
													</div>
												</div>

												<div class="row">
													<div class="col-sm-9 col-md-10 col-xs-12">
														<p>
															<strong>
																{l s='2. ' mod='mpmassupload'}
																{l s='Languages CSV: ' mod='mpmassupload'}
															</strong>
															{l s='Download this demo Csv for Languages.' mod='mpmassupload'}
														</p>
													</div>
													<div class="col-sm-3 col-md-2 col-xs-12">
														<a href="{$languageCsv|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
															<span>{l s='Download' mod='mpmassupload'}</span>
														</a>
													</div>
												</div>

												<div class="row">
													<div class="col-sm-9 col-md-10 col-xs-12">
														<p>
															<strong>
																{l s='3. ' mod='mpmassupload'}
																{l s='Image .zip: ' mod='mpmassupload'}
															</strong>
															{l s='If no update required in any product mention “empty” for that particular field.' mod='mpmassupload'}
														</p>
													</div>
													<div class="col-sm-3 col-md-2 col-xs-12">
														<a href="{$demoZip|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
															<span>{l s='Download' mod='mpmassupload'}</span>
														</a>
													</div>
												</div>

												<div class="row">
													<div class="col-sm-12 col-xs-12">
														<p>
															<strong>
																{l s='4. ' mod='mpmassupload'}
																{l s='Image Extentions: ' mod='mpmassupload'}
															</strong>
															{l s='Image file must have (.jpg, .png, .gif) extension, otherwise other type of image will not be uploaded.' mod='mpmassupload'}
														</p>
													</div>
												</div>

												<div class="row">
													<div class="col-sm-9 col-md-10 col-xs-12">
														<p>
															<strong>
																{l s='5. ' mod='mpmassupload'}
																{l s='Category CSV: ' mod='mpmassupload'}
															</strong>
															{l s='Download Category csv to choose the "category_id".' mod='mpmassupload'}
														</p>
													</div>
													<div class="col-sm-3 col-md-2 col-xs-12">
														<a href="{$categoryCsv|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
															<span>{l s='Download' mod='mpmassupload'}</span>
														</a>
													</div>
												</div>

												{if isset($massupload_combination_approve)}
													<div class="row">
														<div class="col-sm-9 col-md-10 col-xs-12">
															<p>
																<strong>
																	{l s='6. ' mod='mpmassupload'}
																	{l s='Combination CSV: ' mod='mpmassupload'}
																</strong>
																{l s='Product id, Attribute group and Value will be the mandatory fields to update products via CSV file. For other fields - If no update required in any product mention “empty” for that particular field.' mod='mpmassupload'}
															</p>
														</div>
														<div class="col-sm-3 col-md-2 col-xs-12">
															<a href="{$combinationCsv|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
																<span>{l s='Download' mod='mpmassupload'}</span>
															</a>
														</div>
													</div>
												{/if}
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<form onsubmit="return validateUploadCsvForm();" class="form-horizontal" method='post' enctype="multipart/form-data" action="{$link->getModuleLink('mpmassupload','addnewuploadrequest')}" id="upload_form">
						<div class="form-group row">
							<label class="control-label col-lg-4 required">{l s='Request Number: ' mod='mpmassupload'}</label>
							<div class="col-lg-4">
								<input class="reg_sel_input form-control" value="{$random_number}" type="text" id="request_id" name="request_id" readonly/>
							</div>
						</div>

						<div class="form-group row">
							<label class="control-label col-lg-4 required">{l s='Select Category: ' mod='mpmassupload'}</label>
							<div class="col-lg-4">
								<select name="mass_upload_category" id="mass_upload_category" class="form-control" data-allow-edit-combination="{$MASS_UPLOAD_ALLOW_EDIT_COMBINATION}">
									<option value="1" selected="selected">{l s='Products' mod='mpmassupload'}</option>
									{if isset($massupload_combination_approve)}
										<option value="2">{l s='Combinations' mod='mpmassupload'}</option>
									{/if}
								</select>
							</div>
						</div>

						<div class="form-group row">
							<label class="control-label col-lg-4 required">{l s='CSV Type: ' mod='mpmassupload'}</label>
							<div class="col-lg-4">
								<label class="control-label csvTypeAdd">
									<input type="radio" name="csvType" class="form-control" value="1" checked="checked">
									{l s='Add' mod='mpmassupload'}
								</label>
								<label class="control-label csvTypeUpd">
									<input type="radio" name="csvType" class="form-control" value="2">
									{l s='Update' mod='mpmassupload'}
								</label>
							</div>
						</div>

						<div class="form-group row">
							<label class="control-label col-lg-4 required">{l s='Upload Info(.csv) File: ' mod='mpmassupload'}</label>
							<div class="col-lg-4">
								<input class="fileSelect form-control" type="file" name="product_info" id="product_info">
								<span class="error_csv"></span>
							</div>
						</div>

						<div class="form-group row" id="product_image_zip">
							<label class="control-label col-lg-4">{l s='Upload Product Image(.zip) File: ' mod='mpmassupload'}</label>
							<div class="col-lg-4">
								<input class="fileSelect form-control" type="file" name="product_image" id="product_image">
								<span class="error_zip"></span>
							</div>
						</div>

						<div class="form-group row">
							<div class="offset-sm-4 col-sm-4">
								<button type="submit" class="btn btn-primary" id="upload_save" name="submit_csv">
									<span>{l s='Upload' mod='mpmassupload'}</span>
								</button>
							</div>
						</div>
					</form>
				</div>	
			</div>
		</div>
	</div>
</div>
{/block}