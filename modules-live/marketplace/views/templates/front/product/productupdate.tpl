{*
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{block name='content'}
{*tiny MCE added here because included in setMedia will not work with performance config CCC use js cache*}
<script type="text/javascript" src="{$smarty.const._MODULE_DIR_}marketplace/views/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="{$smarty.const._MODULE_DIR_}marketplace/views/js/tinymce/tinymce_wk_setup.js"></script>
{if isset($product_upload)}
	{if $product_upload == 1}
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Your product uploaded successfully' mod='marketplace'}
		</div>
	{else if $product_upload == 2}
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='There was some error occurs while uploading your product' mod='marketplace'}
		</div>		
	{/if}
{/if}

{if isset($smarty.get.edited_conf)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Updated Successfully' mod='marketplace'}
	</p>
{/if}

{hook h='displayMpupdateproductheaderhook'}
<div class="main_block">
	{hook h="DisplayMpmenuhook"}
	<div class="dashboard_content">
		<div class="page-title" style="background-color:{$title_bg_color};display: none;">
			<span style="color:{$title_text_color};">{l s='Update Product' mod='marketplace'}</span>
		</div>
		<div class="wk_right_col">
			<form action="{$link->getModuleLink('marketplace', 'productupdate', ['edited' => 1, 'id' => $id])}" method="post" enctype="multipart/form-data" accept-charset="UTF-8,ISO-8859-1,UTF-16" >
				{hook h='displayMpUpdateProductBodyHeaderOption'}
				<input type="hidden" name="multi_lang" id="multi_lang" value="{$multi_lang}">
				<input type="hidden" name="seller_default_lang" value="{$seller_default_lang}" id="seller_default_lang">
				<input type="hidden" name="current_lang_id" value="{$current_lang.id_lang}" id="current_lang_id">
				<div class="tabs">
					<ul class="nav nav-tabs">
						<li class="nav-item">
							<a class="nav-link active" href="#information" data-toggle="tab">
								<i class="material-icons">&#xE88E;</i>
								{l s='Information' mod='marketplace'}
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#images" data-toggle="tab">
								<i class="material-icons">&#xE410;</i>
								{l s='Images' mod='marketplace'}
							</a>
						</li>
						{hook h='displayMpProductOption'}
					</ul>
					<div class="tab-content" id="tab-content">
						<div class="tab-pane fade in active" id="information">
							{hook h='displayMpupdateproducttoppanel'}
							<div class="required form-group">
								<label for="product_name" class="control-label required">{l s='Product Name :' mod='marketplace'}</label>
								<div class="row">
									{if $allow_multilang && $total_languages > 1}
									<div class="col-md-10">
									{else}
									<div class="col-md-12">
									{/if}
										{foreach from=$languages item=language}
											<input type="text" 
											id="product_name_{$language.id_lang}" 
											name="product_name_{$language.id_lang}" 
											value="{$pro_info['product_name'][{$language.id_lang}]}"
											class="form-control product_name_all {if $seller_default_lang == $language.id_lang}seller_default_lang_class{/if}" 
											data-lang-name="{$language.name}"
											{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
										{/foreach}
									</div>
									{if $allow_multilang && $total_languages > 1}
									<div class="col-md-2">
										<button type="button" id="prod_lang_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											{$current_lang.iso_code}
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											{foreach from=$languages item=language}
												<li>
													<a href="javascript:void(0)" onclick="showProdLangField('{$language.iso_code}', {$language.id_lang});">{$language.name}</a>
												</li>
											{/foreach}
										</ul>
									</div>
									{/if}
								</div>
							</div>
							{hook h='displayMpupdateproductnamebottom'}
							<div class="form-group">
								<label for="prod_short_desc" class="control-label">
									{l s='Short Description :' mod='marketplace'}
								</label>
								<div class="row">
									{if $allow_multilang && $total_languages > 1}
									<div class="col-md-10">
									{else}
									<div class="col-md-12">
									{/if}
										{foreach from=$languages item=language}
											<div id="short_desc_div_{$language.id_lang}" class="short_desc_div_all" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>
												<textarea 
												name="short_description_{$language.id_lang}" 
												id="short_description_{$language.id_lang}" cols="2" rows="3" 
												class="wk_tinymce form-control">{$pro_info['short_description'][{$language.id_lang}]}</textarea>
											</div>
										{/foreach}
									</div>
									{if $allow_multilang && $total_languages > 1}
									<div class="col-md-2">
										<button type="button" id="short_desc_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											{$current_lang.iso_code}
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											{foreach from=$languages item=language}
												<li>
													<a href="javascript:void(0)" onclick="showProdLangField('{$language.iso_code}', {$language.id_lang});">{$language.name}</a>
												</li>
											{/foreach}
										</ul>
									</div>
									{/if}
								</div>
							</div>
							<div class="form-group">
								<label for="prod_desc" class="control-label">
									{l s='Description :' mod='marketplace'}
								</label>
								<div class="row">
									{if $allow_multilang && $total_languages > 1}
									<div class="col-md-10">
									{else}
									<div class="col-md-12">
									{/if}
										{foreach from=$languages item=language}
											<div id="product_desc_div_{$language.id_lang}" class="product_desc_div_all" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>
												<textarea 
												name="product_description_{$language.id_lang}" 
												id="product_description_{$language.id_lang}" cols="2" rows="3" 
												class="wk_tinymce form-control">{$pro_info['description'][{$language.id_lang}]}</textarea>
											</div>
										{/foreach}							  			
							  		</div>
							  		{if $allow_multilang && $total_languages > 1}
									<div class="col-md-2">
										<button type="button" id="product_desc_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											{$current_lang.iso_code}
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											{foreach from=$languages item=language}
												<li>
													<a href="javascript:void(0)" onclick="showProdLangField('{$language.iso_code}', {$language.id_lang});">{$language.name}</a>
												</li>
											{/foreach}
										</ul>
									</div>
									{/if}
								</div>
							</div>
							<div class="form-group">
								<label for="product_condition" class="control-label">
									{l s='Condition :' mod='marketplace'}
								</label>
								<div class="row">	
									<div class="col-lg-3">
									  	<select class="form-control" name="product_condition">
									  		<option value="new" {if $pro_info['condition'] == 'new'}selected{/if}>{l s='New' mod='marketplace'}</option>
									  		<option value="used" {if $pro_info['condition'] == 'used'}selected{/if}>{l s='Used' mod='marketplace'}</option>
									  		<option value="refurbished" {if $pro_info['condition'] == 'refurbished'}selected{/if}>{l s='Refurbished' mod='marketplace'}</option>
									  	</select>
								  	</div>
							  	</div>
							</div>
							<div class="form-group">
								<label for="prod_price" class="control-label required">{l s='Base Price :' mod='marketplace'}</label>
								<div class="row">
									{if $allow_multilang && $total_languages > 1}
									<div class="col-md-10">
									{else}
									<div class="col-md-12">
									{/if}
										<div class="input-group">
											<input type="text" id="product_price" name="product_price" value="{$pro_info['price']}"  class="form-control"/>
											<span class="input-group-addon">{$obj_default_currency->sign}</span>
										</div>
									</div>
								</div>
							</div>
							{hook h='displayMpaddproductpricehook'}
							<div class="form-group">
								<label for="prod_quantity" class="control-label required">{l s='Quantity :' mod='marketplace'}</label>
								<div class="row">
									{if $allow_multilang && $total_languages > 1}
									<div class="col-md-10">
									{else}
									<div class="col-md-12">
									{/if}
										<input type="text" id="product_quantity" name="product_quantity" value="{$pro_info['quantity']}" class="form-control"/>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="prod_category" class="control-label required">{l s='Category :' mod='marketplace'}</label>
								<div>{$categoryTree nofilter}</div>
							</div>
							<div class="form-group" id="default_category_div">
								<label for="default_category" class="control-label">
									{l s='Default Category :' mod='marketplace'}
								</label>
								<div class="row">	
									<div class="col-lg-3">
									  	<select class="form-control" name="default_category" id="default_category">
									  		{if isset($default_cat)}
												{foreach $default_cat as $d_cat}
													<option id="default_cat{$d_cat.id_category}" value="{$d_cat.id_category}" name="{$d_cat.name}" {if isset($defaultcatid)}{if $defaultcatid == $d_cat.id_category} selected {/if}{/if}>{$d_cat.name}</option>
												{/foreach}
											{/if}
									  	</select>
								  	</div>
							  	</div>
							</div>
							{hook h="displayMpupdateproductfooterhook"}
						</div>
						<div class="tab-pane fade in" id="images">
							{block name='productupdate_images'}
								{include file='module:marketplace/views/templates/front/product/_partials/productupdate-images.tpl'}
							{/block}
						</div>
						{hook h="displayMpupdateproducttabhook"}
						<div class="form-group" style="text-align:center;">
							<button type="submit" id="SubmitCreate" class="btn btn-yellow form-control-submitm">
								<span>Valider{**l s='Update' mod='marketplace'**}</span>
							</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	{block name='image_upload'}
		{include file='module:marketplace/views/templates/front/_partials/uploadimage_popup.tpl'}
	{/block}
</div>
{/block}