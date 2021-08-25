{*
* 2010-2017 Webkul.
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
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{foreach $product['customizedDatas'] as $customizationfield}
	{foreach $customizationfield as $customizationId => $customization}
		<tr>
			<td colspan="2">
				<div class="form-horizontal">
					{foreach $customization.datas as $type => $datas}
						{if ($type == Product::CUSTOMIZE_FILE)}
							{foreach from=$datas item=data}
								<div class="form-group">
									<span class="col-lg-4 control-label"><strong>{if $data['name']|escape:'html':'UTF-8'}{$data['name']|escape:'html':'UTF-8'}{else}{l s='Picture #' mod='mpproductcustomization'}{$data@iteration|escape:'html':'UTF-8'}{/if}</strong></span>
										<div class="col-lg-8">
											<a href="{$smarty.const._THEME_PROD_PIC_DIR_|escape:'html':'UTF-8'}{$data['value']|escape:'html':'UTF-8'}" class="_blank">
												<img class="img-thumbnail" src="{$smarty.const._THEME_PROD_PIC_DIR_|escape:'quotes':'UTF-8'}{$data['value']|escape:'html':'UTF-8'}_small" alt=""/>
											</a>
										</div>
									</div>
								{/foreach}
							{elseif ($type == Product::CUSTOMIZE_TEXTFIELD)}
								{foreach from=$datas item=data}
									<div class="form-group">
										<span class="col-lg-4 control-label"><strong>{if $data['name']}{l s='%s' sprintf=[$data['name']] mod='mpproductcustomization'}{else}{l s='Text #%s' sprintf=[$data@iteration] mod='mpproductcustomization'}{/if}</strong></span>
										<div class="col-lg-8">
											<p class="form-control-static">{$data['value']|escape:'html':'UTF-8'}</p>
										</div>
									</div>
								{/foreach}
							{/if}
						{/foreach}
					</div>
				</td>
				<td class="productQuantity text-center">
					<span class="product_quantity_show{if (int)$customization['quantity'] > 1} red bold{/if}">{$customization['quantity']|escape:'html':'UTF-8'}</span>
				</td>
				
				{if ($order->hasBeenDelivered())}<td class="text-center">{$customization['quantity_returned']|escape:'html':'UTF-8'}</td>{/if}
				<td class="text-center">-</td>
				<td class="total_product">
					{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
						{$product['product_price'] * $customization['quantity']}
					{else}
						{$product['product_price_wt'] * $customization['quantity']}
					{/if}
				</td>
			</tr>
		{/foreach}
{/foreach}