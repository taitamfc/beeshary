{*
* 2010-2016 Webkul
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
*  @author Webkul IN <support@webkul.com>
*  @copyright  2010-2016 Webkul IN
*}

<div class="row">
	<div class="col-sm-12">
	  	<label class="control-label">
	  		<input type="radio" name="product_type" class="product_type" value="2" {if (isset($product_type) && ($product_type==2))}checked{/if} {if (isset($is_pack_item) && ($is_pack_item==1))}disabled{/if}>
				{l s='Pack of existing products' mod='mppackproducts'}
		</label>
	</div>
</div>