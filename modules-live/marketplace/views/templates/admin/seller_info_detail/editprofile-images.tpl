{*
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="form-group row">
	<div class="col-md-6">
		<div class="form-group sellerprofileimage_wrapper">
			<button type="button" class="btn btn-yellow wk_uploader_margin" id="uploadprofileimg">{l s='Upload Profile Image' mod='marketplace'}</button>
			<div class="clearfix"></div>
			<div id="profileuploader" class="wk_uploader_wholediv" style="display: none;">
				<div class="col-md-11 wk_padding_none wk_upload_product_image">
					<!--<input type="file" name="sellerprofileimage[]" class="uploadimg_container" data-img-w="300" data-img-h="300" data-uploadfile="#seller_profileimage" data-aspect-ratio="1" data-jfiler-name="sellerprofileimage" />-->
					{include file="$self/../../views/templates/front/_partials/image-upload.tpl"
						uploadName='sellerprofileimage' cropWidth=250 cropHeight=250 aspectRatio=1 index=''}	
			    </div>
			    <div class="clearfix"></div>
		    </div>
			<div class="jFiler-items-seller_img {if isset($seller_img_path)}_wk_hover_img{/if}">
				<ul class="jFiler-items-list jFiler-items-grid" style="padding:0px;">
					<li class="jFiler-item">
						<div class="jFiler-item-container">
							<div class="jFiler-item-inner">
								<img class="old_img" src="{if isset($seller_img_path)}{$seller_img_path}?timestamp={$timestamp}{else}{$seller_default_img_path}?timestamp={$timestamp}{/if}" alt="{if isset($seller_img_path)}{l s='Seller Profile Image' mod='marketplace'}{else}{l s='Default Image' mod='marketplace'}{/if}" />
								{if isset($seller_img_path)}
								<div class="wk_text_right hidden">
									<a class="icon-jfi-trash wk_delete_img" data-id_seller="{$mp_seller_info.id_seller}" data-imgtype="seller_img" data-uploaded="1" title="{l s='Delete' mod='marketplace'}"></a>
								</div>
								{/if}
							</div>
						</div>
					</li>
					<div class="clearfix"></div>
				</ul>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group shopimage_wrapper">
			<button type="button" class="btn btn-yellow wk_uploader_margin" id="uploadshoplogo">{l s='Upload Shop Logo' mod='marketplace'}</button>
			<div id="shopuploader" class="wk_uploader_wholediv" style="display: none;">
				<div class="col-md-11 wk_padding_none">
					<!--<input type="file" name="shopimage[]" class="uploadimg_container" data-img-w="300" data-img-h="300" data-uploadfile="#seller_shopimg" data-aspect-ratio="1" data-jfiler-name="shopimage" />-->
					{include file="$self/../../views/templates/front/_partials/image-upload.tpl"
						uploadName='shopimage' cropWidth=250 cropHeight=250 aspectRatio=1 index=1}
			    </div>
			    <div class="clearfix"></div>
		    </div>
			<div class="jFiler-items-shop_img {if isset($shop_img_path)}_wk_hover_img{/if}">
				<ul class="jFiler-items-list jFiler-items-grid" style="padding:0px;">
					<li class="jFiler-item">
						<div class="jFiler-item-container">
							<div class="jFiler-item-inner">
								<img class="old_img" src="{if isset($shop_img_path)}{$shop_img_path}?timestamp={$timestamp}{else}{$shop_default_img_path}?timestamp={$timestamp}{/if}" alt="{if isset($shop_img_path)}{l s='Shop Logo' mod='marketplace'}{else}{l s='Default Image' mod='marketplace'}{/if}"/>
							</div>
						</div>
					</li>
					<div class="clearfix"></div>
				</ul>
			</div>
		</div>
	</div>
</div>

<h2 class="text-uppercase" style="border-bottom: 1px solid #d5d5d5;padding-bottom: 11px;">
	{l s='Banner Image' mod='marketplace'}
</h2> 

<div class="form-group row">
	<!-- Seller Profile Page Banner -->
	<div class="col-md-6">
		<div class="form-group profilebannerimage_wrapper">
			<button type="button" class="btn btn-yellow wk_uploader_margin" id="uploadsellerbanner">{l s='Upload Profile Banner' mod='marketplace'}</button>
			<div id="profilebanneruploader" class="wk_uploader_wholediv" style="display: none;">
				<div class="col-md-11 wk_padding_none">
					<!--
						<input type="file" name="profilebannerimage" class="uploadimg_container" data-img-w="1538" data-img-h="380" data-uploadfile="#seller_profilebannerimage" data-aspect-ratio="16/5" data-jfiler-name="profilebannerimage" />
					-->

					{include file="$self/../../views/templates/front/_partials/image-upload.tpl"
						uploadName='profilebannerimage' cropWidth=1540 cropHeight=385 aspectRatio=4 index=2}		
			    </div>
			    <div class="clearfix"></div>
		    </div>
			<div class="jFiler-items-seller_banner {if isset($seller_banner_path)}_wk_hover_img{/if}">
				<ul class="jFiler-items-list jFiler-items-grid" style="padding:0px;">
					<li class="jFiler-item">
						<div class="jFiler-item-container">
							<div class="jFiler-item-inner">
								<img class="old_img" width="100%" src="{if isset($seller_banner_path)}{$seller_banner_path}?timestamp={$timestamp}{else}{$no_image_path}{/if}" alt="{if isset($seller_banner_path)}{l s='Seller Profile Banner' mod='marketplace'}{else}{l s='No Image' mod='marketplace'}{/if}"/>
							</div>
						</div>
					</li>
					<div class="clearfix"></div>
				</ul>
			</div>
		</div>
	</div>

	<!-- Shop Store Page Banner -->
	<div class="col-md-6">
		<div class="form-group shopbannerimage_wrapper">
			<button type="button" class="btn btn-yellow wk_uploader_margin" id="uploadshopbanner">{l s='Upload Shop Banner' mod='marketplace'}</button>
			<div id="shopbanneruploader" class="wk_uploader_wholediv" style="display: none;">
				<div class="col-md-11 wk_padding_none">
					<!--
					<input type="file" name="shopbannerimage" class="uploadimg_container" data-img-w="750" data-img-h="750" data-uploadfile="#seller_shopbannerimage" data-aspect-ratio="5/3" data-jfiler-name="shopbannerimage" />-->
					{include file="$self/../../views/templates/front/_partials/image-upload.tpl"
						uploadName='shopbannerimage' cropWidth=1540 cropHeight=385 aspectRatio=4 index=3}	
			    </div>
			    <div class="clearfix"></div>
		    </div>
			<div class="jFiler-items-shop_banner {if isset($shop_banner_path)}_wk_hover_img{/if}">
				<ul class="jFiler-items-list jFiler-items-grid" style="padding:0px;">
					<li class="jFiler-item">
						<div class="jFiler-item-container">
							<div class="jFiler-item-inner">
								<img class="old_img" width="100%" src="{if isset($shop_banner_path)}{$shop_banner_path}?timestamp={$timestamp}{else}{$no_image_path}{/if}" alt="{if isset($shop_banner_path)}{l s='Shop Logo' mod='marketplace'}{else}{l s='No Image' mod='marketplace'}{/if}"/>
							</div>
						</div>
					</li>
					<div class="clearfix"></div>
				</ul>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="getCroppedCanvasModal" aria-labelledby="modalLabel" role="dialog" tabindex="-1">
  	<div class="modal-dialog" role="document">
	    <div class="modal-content">
	      	<div class="modal-header">
	        	<h5 class="modal-title" id="modalLabel">Recadrer l'image</h5>
	        	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          		<span aria-hidden="true">&times;</span>
	        	</button>
	      	</div>
	      	<div class="modal-body">
	        	<div>
	          		<img id="image" src="" alt="Photo" />
	        	</div>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-default" data-dismiss="modal">Continue</button>
	      	</div>
	    </div>
	</div>
</div>