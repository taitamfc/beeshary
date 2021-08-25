{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if $edit_permission}
	<div class="form-group">
		<div class="wk_upload_product_image">
			<input type="file" name="productimages[]" class="uploadimg_container" data-jfiler-name="productimg">
		</div>
	</div>
{/if}
{block name='imageedit'}
	{include file='module:marketplace/views/templates/front/product/imageedit.tpl'}
{/block}