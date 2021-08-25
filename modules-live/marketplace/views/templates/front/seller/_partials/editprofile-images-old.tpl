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
			<div id="profileuploader" class="wk_uploader_wholediv">
				<div class="col-md-11 wk_padding_none">
					<input type="file" id="sellerprofileimage" class="uploadimg_container" data-img-w="300" data-img-h="300" data-uploadfile="#seller_profileimage" data-aspect-ratio="1" />
			    </div>
			    <div class="clearfix"></div>
		    </div>
			<div class="jFiler-items-seller_img {if isset($seller_img_path)}_wk_hover_img{/if}">
				<ul class="jFiler-items-list jFiler-items-grid" style="padding:0px;">
					<li class="jFiler-item">
						<div class="jFiler-item-container">
							<div class="jFiler-item-inner">
								<div class="cropped_img_result"></div>
								<div class="img-result hide">
									<!-- result of crop -->
									<img class="img_cropped" src="" alt="" />
								</div>
								<!-- save btn -->
								<button class="btn btn-yellow save_cropped hide">Recadrer</button>
								<!-- download btn -->
								<a href="" class="btn download hide">Download</a>

								<img class="old_img" src="{if isset($seller_img_path)}{$seller_img_path}?timestamp={$timestamp}{else}{$seller_default_img_path}?timestamp={$timestamp}{/if}" alt="{if isset($seller_img_path)}{l s='Seller Profile Image' mod='marketplace'}{else}{l s='Default Image' mod='marketplace'}{/if}"/>
								{if isset($seller_img_path)}
								<div class="wk_text_right hidden">
									<a class="icon-jfi-trash wk_delete_img" data-id_seller="{$mp_seller_info.id_seller}" data-imgtype="seller_img" data-uploaded="1" title="{l s='Delete' mod='marketplace'}"></a>
								</div>
								{/if}
								<input id="seller_profileimage" type="hidden" name="sellerprofileimage" class="hidden" />
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
			<div id="shopuploader" class="wk_uploader_wholediv">
				<div class="col-md-11 wk_padding_none">
					<input type="file" id="shopimage" name="shopimgs[]" class="uploadimg_container" data-img-w="300" data-img-h="300" data-uploadfile="#seller_shopimg" data-aspect-ratio="1" />
			    </div>
			    <div class="clearfix"></div>
		    </div>
			<div class="jFiler-items-shop_img {if isset($shop_img_path)}_wk_hover_img{/if}">
				<ul class="jFiler-items-list jFiler-items-grid" style="padding:0px;">
					<li class="jFiler-item">
						<div class="jFiler-item-container">
							<div class="jFiler-item-inner">
								<div class="cropped_img_result"></div>
								<div class="img-result hide">
									<!-- result of crop -->
									<img class="img_cropped" src="" alt="" />
								</div>
								<!-- save btn -->
								<button class="btn btn-yellow save_cropped hide">Recadrer</button>
								<!-- download btn -->
								<a href="" class="btn download hide">Download</a>

								<img class="old_img" src="{if isset($shop_img_path)}{$shop_img_path}?timestamp={$timestamp}{else}{$shop_default_img_path}?timestamp={$timestamp}{/if}" alt="{if isset($shop_img_path)}{l s='Shop Logo' mod='marketplace'}{else}{l s='Default Image' mod='marketplace'}{/if}"/>
								<input id="seller_shopimg" type="hidden" name="shopimage" class="hidden" />
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
			<div id="profilebanneruploader" class="wk_uploader_wholediv">
				<div class="col-md-11 wk_padding_none">
					<input type="file" id="profilebannerimage" class="uploadimg_container" data-img-w="1538" data-img-h="380" data-uploadfile="#seller_profilebannerimage" data-aspect-ratio="16/5" />
			    </div>
			    <div class="clearfix"></div>
		    </div>
			<div class="jFiler-items-seller_banner {if isset($seller_banner_path)}_wk_hover_img{/if}">
				<ul class="jFiler-items-list jFiler-items-grid" style="padding:0px;">
					<li class="jFiler-item">
						<div class="jFiler-item-container">
							<div class="jFiler-item-inner">
								<div class="cropped_img_result"></div>
								<div class="img-result hide">
									<!-- result of crop -->
									<img class="img_cropped" src="" alt="" />
								</div>
								<!-- save btn -->
								<button class="btn btn-yellow save_cropped hide">Recadrer</button>
								<!-- download btn -->
								<a href="" class="btn download hide">Download</a>

								<img class="old_img" width="1538" src="{if isset($seller_banner_path)}{$seller_banner_path}?timestamp={$timestamp}{else}{$no_image_path}{/if}" alt="{if isset($seller_banner_path)}{l s='Seller Profile Banner' mod='marketplace'}{else}{l s='No Image' mod='marketplace'}{/if}"/>
								<input id="seller_profilebannerimage" type="hidden" name="profilebannerimage" class="hidden" />
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
			<div id="shopbanneruploader" class="wk_uploader_wholediv">
				<div class="col-md-11 wk_padding_none">
					<input type="file" id="shopbannerimage" class="uploadimg_container" data-img-w="750" data-img-h="750" data-uploadfile="#seller_shopbannerimage" data-aspect-ratio="5/3" />
			    </div>
			    <div class="clearfix"></div>
		    </div>
			<div class="jFiler-items-shop_banner {if isset($shop_banner_path)}_wk_hover_img{/if}">
				<ul class="jFiler-items-list jFiler-items-grid" style="padding:0px;">
					<li class="jFiler-item">
						<div class="jFiler-item-container">
							<div class="jFiler-item-inner">
								<div class="cropped_img_result"></div>
								<div class="img-result hide">
									<!-- result of crop -->
									<img class="img_cropped" src="" alt="" />
								</div>
								<!-- save btn -->
								<button class="btn btn-yellow save_cropped hide">Recadrer</button>
								<!-- download btn -->
								<a href="" class="btn download hide">Download</a>

								<img class="old_img" width="750" src="{if isset($shop_banner_path)}{$shop_banner_path}?timestamp={$timestamp}{else}{$no_image_path}{/if}" alt="{if isset($shop_banner_path)}{l s='Shop Logo' mod='marketplace'}{else}{l s='No Image' mod='marketplace'}{/if}"/>
								<input id="seller_shopbannerimage" type="hidden" name="shopbannerimage" class="hidden" />
							</div>
						</div>
					</li>
					<div class="clearfix"></div>
				</ul>
			</div>
		</div>
	</div>
</div>