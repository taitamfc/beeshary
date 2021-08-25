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

<div class="form-group row">							
	<div class="col-md-12">
		<label for="product_image">{l s='Upload Image :' mod='marketplace'}</label>
		<div id="holder" class="photo-uploader">
			<div>
				<div class="seller-upload-img">
					<img src="{$module_dir}marketplace/views/img/upload.svg" class="img-responsive">
				</div>
				<div class="seller-dragdrop">
					{l s='Drag & Drop to Upload' mod='marketplace'}
				</div>
			</div>
		</div>
		<div class="seller-photo-or">{l s='or' mod='marketplace'}</div>
		<button type="button" class="button seller-pickphoto" onclick="chooseFile('0');">{l s='Pick Image' mod='marketplace'}</button>
        <div class="upload-error"></div>        
    </div>
</div>

<div class="form-group">
	<input type="hidden" name="total_image" id="total_image" value="0">
	<input type="hidden" name="forproduct" id="forproduct" value="1">
	<input type="hidden" name="editprofile" id="editprofile" value="0">
	<input type="hidden" name="pick_id" id="pick_id" value="">
	
	<div id="product_image_1" class="productimg_blank">
		<input type='hidden' name='image_name' id='image_name_1' value=''>
	</div>
</div>

{block name='imageedit'}
	{include file='module:marketplace/views/templates/front/product/imageedit.tpl'}
{/block}