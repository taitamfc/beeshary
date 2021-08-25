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

<div class="row pkprod_container">
	<input type="hidden" name="current_lang_id" id="current_lang_id" value="{$current_lang.id_lang}">
	<div class="col-sm-12">
		<div class="form-group">
			<label class="col-sm-3 text-right">{l s='Select Pack Products' mod='mppackproducts'}</label>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label" for="selectproduct">{l s='Product :' mod='mppackproducts'}</label>
			<div class="col-sm-6">
				<input class="form-control" type="text" name="selectproduct" id="selectproduct" data-value="" data-img="" autocomplete="off">
				<p class="help-block"> {l s='Start by typing the first letter of the product name, then select the product from the drop-down list.' mod='mppackproducts'} </p>
				<div class="row no_margin sug_container">
					<ul id="sugpkprod_ul" style="top: -50px;"></ul>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label" for="packproductquant">{l s='Quantity :' mod='mppackproducts'}</label>
			<div class="col-sm-6">
				<div class="input-group">
					<span class="input-group-addon">x</span>
					<input class="form-control" type="text" name="quant" id="packproductquant" value="1" autocomplete="off">
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
				<button class="btn btn-primary" id="addpackprodbut">
					<span> <i class="icon-plus-sign-alt"></i> {l s='Add this product to the pack' mod='mppackproducts'} </span>
				</button>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
				<div class="row no_margin pkprodlist">
					{if isset($isPackProduct)}
						{foreach from=$mpPackProducts key=k item=mpPackProduct}
							<div class="col-sm-4 col-xs-12">
								<div class="row no_margin pk_sug_prod" ps_prod_id="{$mpPackProduct.id_ps_product}" ps_id_prod_attr="{$mpPackProduct.ps_prod_attr_id}">
									<div class="col-sm-12 col-xs-12">
										<img class="img-responsive pk_sug_img" src="{$link->getImageLink($mpPackProduct.link_rewrite, $mpPackProduct.img_id, 'home_default')}">
										<p class="text-center">{$mpPackProduct.product_name}</p>
										<span class="pull-left">x{$mpPackProduct.quantity}</span>
										<a class="pull-right dltpkprod">
										<i class="material-icons">&#xE872;</i></a>
										<input type="hidden" class="pspk_id_prod" name="pspk_id_prod[]" value="{$mpPackProduct.id_ps_product}">
										<input type="hidden" name="pspk_prod_quant[]" value="{$mpPackProduct.quantity}">
										<input type='hidden' class='pspk_id_prod_attr' name='pspk_id_prod_attr[]' value="{$mpPackProduct.ps_prod_attr_id}">
									</div>
								</div>
							</div>
						{/foreach}
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>
{strip}
	{addJsDefL name=prod_err}{l s='Please enter valid product name' js=1 mod='mppackproducts'}{/addJsDefL}
	{addJsDefL name=quant_err}{l s='Please enter valid product quantity' js=1 mod='mppackproducts'}{/addJsDefL}
	{addJsDef sugpackprod_url = $link->getModuleLink('mppackproducts', 'suggestpackproducts')}
	{addJsDef module_dir = $module_dir}
	{if isset($id_seller)}
		{addJsDef id_seller = $id_seller|intval}
	{/if}
	{if isset($mp_id_prod)}
		{addJsDef mp_pk_id_prod = $mp_id_prod|intval}
	{/if}
{/strip}