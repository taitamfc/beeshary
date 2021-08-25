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

<div class="form-group pkprod_container">
    <input type="hidden" name="current_lang_id" id="current_lang_id" value="{$current_lang.id_lang}">
    <div class="form-group">
		<label class="control-label" for="selectproduct">{l s='Add products to your pack' mod='mppackproducts'}</label>
		<input class="form-control" type="text" name="selectproduct" id="selectproduct" data-value="" data-img="" autocomplete="off" placeholder="{l s='Start by typing the first letter of the product name, then select the product from the drop-down list.' mod='mppackproducts'}"/>
		<div class="row no_margin sug_container">
			<ul id="sugpkprod_ul"></ul>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label" for="packproductquant">{l s='Quantity' mod='mppackproducts'}</label>
		<div class="input-group">
			<span class="input-group-addon">x</span>
			<input class="form-control" type="text" name="quant" id="packproductquant" value="1" autocomplete="off">
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-3">
			<button class="btn btn-primary" id="addpackprodbut">
				<span> <i class="icon-plus-sign-alt"></i> {l s='Add this product to the pack' mod='mppackproducts'} </span>
			</button>
		</div>
	</div>

	<div class="row margin-top-20 no_margin pkprodlist">
		{if isset($isPackProduct)}
			{if $mpPackProducts}
				{foreach from=$mpPackProducts key=k item=mpPackProduct}
					<div class="col-sm-4 col-xs-12">
						<div class="row no_margin pk_sug_prod" ps_prod_id="{$mpPackProduct.id_ps_product}" ps_id_prod_attr="{$mpPackProduct['ps_prod_attr_id']}">
							<div class="col-sm-12 col-xs-12"> 
								<img class="img-responsive pk_sug_img" src="{$link->getImageLink($mpPackProduct.link_rewrite, $mpPackProduct.img_id, 'home_default')}">
								<p class="text-center">{$mpPackProduct.product_name}</p>
								<span class="pull-left">x{$mpPackProduct.quantity}</span> 
								<a class="pull-right dltpkprod"><i class="material-icons">&#xE872;</i></a>
								<input type="hidden" class="pspk_id_prod" name="pspk_id_prod[]" value="{$mpPackProduct.id_ps_product}">
								<input type="hidden" name="pspk_prod_quant[]" value="{$mpPackProduct.quantity}">
								<input type='hidden' class='pspk_id_prod_attr' name='pspk_id_prod_attr[]' value="{$mpPackProduct.ps_prod_attr_id}">
							</div>
						</div>
					</div>
				{/foreach}
			{/if}
		{/if}
	</div>
</div>