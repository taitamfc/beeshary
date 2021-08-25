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

<form id="upload_form" enctype="multipart/form-data" method="post" onsubmit="return checkForm()">
	<input type="hidden" id="x1" name="x1" />
	<input type="hidden" id="y1" name="y1" />
	<input type="hidden" id="x2" name="x2" />
	<input type="hidden" id="y2" name="y2" />
	<input type="file" id="product_image" name="product_image" value="" class="form-control" size="chars" style="visibility:hidden;" />
	<div class="uploadpicture">
		<input type="file" name="image_file" id="image_file" onchange="fileSelectHandler('')">
	</div>
	<input type="hidden" name="dropImage_file" id="dropImage_file">

	<input type="hidden" id="filesize" name="filesize" />
	<input type="hidden" id="filetype" name="filetype" />
	<input type="hidden" id="filedim" name="filedim" />
	<input type="hidden" id="w" name="w" />
	<input type="hidden" id="h" name="h" />	
</form>
<div id="step2-blank" class="step2-blank">
    <div class="step2-crop">
        <div class="step2-div">					                    
            <div class="crop-heading">{l s='Crop Your Image' mod='marketplace'}</div>
            <img id="preview" />
            <div class="upload-error"></div>
			<div>
	    		<button class="btn col-lg-6 picuploadbutton" style="background:#c1c1c1;" onclick="closePhotoPopup()">{l s='Cancel' mod='marketplace'}</button>
	  			<button class="btn col-lg-6 picuploadbutton" name="uploadimage" id="uploadimage">
	  				{l s='Save' mod='marketplace'}
	  			</button>
	  			<div style="clear:both;"></div>
			</div>
        </div>
    </div>
</div>

<div id="wholepage_div"></div>