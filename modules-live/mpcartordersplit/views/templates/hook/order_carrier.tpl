{**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="delivery-options byHook">
	{assign var="default_delivery_option" value=(","|explode:$delivery_option[$id_address])}
	{foreach $option_list as $key => $option}
		{foreach from=$option.carrier_list key=id_seller item=sellerDtl}
			<div class="row wk_product_carriers_wrapper">
				<div class="col-xs-12 col-sm-12 wk_carrier_primary_block">
					<p class="wk_shop_link">{l s='STORE NAME : ' mod='mpcartordersplit'}<a href="{if isset($sellerDtl.seller_detail)}{$sellerDtl.seller_detail.shopcollection}{else}{$urls.base_url}{/if}">
						{if isset($sellerDtl.seller_detail)}
							{$sellerDtl.seller_detail.shop_name}
						{else}
							{$PS_SHOP_NAME}
						{/if}
					</a></p>
				</div>
				<div class="col-xs-12 col-sm-12 wk_carrier_secondary_block">
					<div class="row sub_prodCarrier_wrapper">
						<div class="col-xs-12 col-sm-12 productBlock">
							{foreach from=$sellerDtl.product_carrier_list key=pid_carrier item=carrier name=shopcarrier}
								{foreach from=$carrier.product_list key=k item=product name=shopProd}
									<div class="row wk_product_carriers_cont {if $smarty.foreach.shopcarrier.last && $smarty.foreach.shopProd.last}shopLastProd{/if}">
										<div class="row wk_product_wrapper">
											<!--img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" height="{$homeSize.height}" width="{$homeSize.width}" alt="{$product.name|escape:html:'UTF-8'}"  class="img-responsive img-thumbnail"-->
											<img src="{$link->getImageLink($product.link_rewrite, Product::getCover($product.id_product)|@current, 'home_default')}" height="{$homeSize.height}" width="{$homeSize.width}" alt="{$product.name|escape:html:'UTF-8'}"  class="img-responsive img-thumbnail">
											<div>
												<p class="product_name">{$product.name}</p>
												{if isset($product.attributes)}
													<p class="product_attr">{$product.attributes}</p>
												{/if}
											</div>
										</div>
										{foreach from=$product.carrier_list key=id_carr_k item=id_carrier}
											<div class="delivery-option {if ($id_carrier@index % 2)}alternate_{/if}item">
												<div>
													<table class="resume table table-bordered">
														<tr>
															<td class="delivery_option_radio">
																<input type="radio"
																id="delivery_option_{$id_carrier}_{$product.id_product}_{$product.id_product_attribute}"
																data-delivery-option-list-k="delivery_option[{$id_address|intval}]"
																name="delivery_option[{$product.id_product|intval}{$product.id_product_attribute|intval}]"
																data-id-product-attr="{$product.id_product_attribute}"
																data-id-product="{$product.id_product}"
																data-id-carrier="{$id_carrier}"
																class="delivery_option_radio delivery_option_{$id_seller}_{$id_carrier} delivery_option_{$id_seller}"
																data-id_address="{$id_address}"
																data-key="{$delivery_option[$id_address]}"
																{if in_array($id_carrier, $default_delivery_option) && isset($selected_delivery_option[$id_carrier][$product.id_product])&& in_array($product.id_product_attribute, $selected_delivery_option[$id_carrier][$product.id_product])}checked="checked"{/if}>
															</td>
															<td class="delivery_option_logo">
																{if $product.prod_carrier_instance[$id_carrier]->logo}
																	<img class="order_carrier_logo" src="{$product.prod_carrier_instance[$id_carrier]->logo}" alt="{$product.prod_carrier_instance[$id_carrier]->name}"/>
																{/if}
															</td>
															<td>
																<strong>{$product.prod_carrier_instance[$id_carrier]->name}</strong>
																{if isset($product.prod_carrier_instance[$id_carrier]->delay[$language.id])}
																	<br />
																	{l s='Delivery time:' mod='mpcartordersplit'}&nbsp;{$product.prod_carrier_instance[$id_carrier]->delay[$language.id]}
																{/if}
																{if count($product.carrier_list) > 1}
																	<br />
																	{if $product.prod_carrier_dtl.best_grade == $product.prod_carrier_instance[$id_carrier]->grade}
																		{if $product.prod_carrier_dtl.best_price == $product.prod_carrier_dtl[$id_carrier].price_with_tax}
																			<span class="best_grade best_grade_price best_grade_speed">{l s='The best price and speed' mod='mpcartordersplit'}</span>
																		{else}
																			<span class="best_grade best_grade_speed">{l s='The fastest' mod='mpcartordersplit'}</span>
																		{/if}
																	{elseif $product.prod_carrier_dtl.best_price == $product.prod_carrier_dtl[$id_carrier].price_with_tax}
																		<span class="best_grade best_grade_price">{l s='The best price' mod='mpcartordersplit'}</span>
																	{/if}
																{/if}
															</td>
															<td class="delivery_option_price">
																<div class="delivery_option_price">
																	{if $product.prod_carrier_dtl[$id_carrier].price_with_tax && !$product.prod_carrier_dtl[$id_carrier].is_free && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}
																		{if $use_taxes == 1}
																			{if $priceDisplay == 1}
																				{$product.prod_carrier_dtl[$id_carrier].price_without_tax}
																				{*{convertPrice price=$product.prod_carrier_dtl[$id_carrier].price_without_tax}*}{if $display_tax_label} {l s='(tax excl.)' mod='mpcartordersplit'}{/if}
																			{else}
																				{$product.prod_carrier_dtl[$id_carrier].price_with_tax}
																				{*{convertPrice price=$product.prod_carrier_dtl[$id_carrier].price_with_tax}*}{if $display_tax_label} {l s='(tax incl.)' mod='mpcartordersplit'}{/if}
																			{/if}
																		{else}
																			{$product.prod_carrier_dtl[$id_carrier].price_without_tax}
																			{*{convertPrice price=$product.prod_carrier_dtl[$id_carrier].price_without_tax}*}
																		{/if}
																	{else}
																		{l s='Free' mod='mpcartordersplit'}
																	{/if}
																</div>
															</td>
														</tr>
													</table>
												</div>
											</div>
										{/foreach}
									</div>
								{/foreach}
							{/foreach}
						</div>
						{if !$bookingProductInfo}
						<div class="col-xs-12 col-sm-12 carrierBlock">
							{if $sellerDtl.commonCarrier|@count}
								{if (($sellerDtl.commonCarrier|@count) == 1) && !($sellerDtl.commonCarrier|@reset)}
									<div class="col-sm-12">
										<div class="alert alert-danger">
											<strong>{l s='Error!' mod='mpcartordersplit'}</strong> {l s='No carrier is available for this product, please remove this product from cart' mod='mpcartordersplit'}
										</div>
									</div>
								{else}
									<p>{l s='SELECT SHIPPING' mod='mpcartordersplit'}</p>
									<div class="dropdown wk_dropdown_cont">
										<button class="btn btn-default dropdown-toggle wk_dropdown_btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											{foreach from=$sellerDtl.commonCarrierDetail key=id_carrier item=carrierDtl}
												{if in_array($id_carrier, $default_delivery_option) && in_array($id_carrier, $sellerDtl.selectedCarriers)}
													{*customization by Amit Webkul*}
													<input type="hidden" class="wk_selected_carrier" data-id_seller="{$id_seller}" data-is_free="{if $carrierDtl.price_with_tax && !$carrierDtl.is_free && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}0{else}1{/if}">
													{*end customization by Amit Webkul*}
													{if $carrierDtl.logo}
														<div class="wk_carrier_com_div wk_carrier_img_div">
															<img class="order_carrier_logo" src="{$carrierDtl.logo}" alt="{$carrierDtl.name}"/>
														</div>
													{/if}
													<div class="wk_carrier_com_div {if $carrierDtl.logo}wk_carrier_dtl_div{else}wk_carrier_dtl_noimg_div{/if}">
														<strong>{$carrierDtl.name}</strong>
														{if isset($carrierDtl.delay)}
															<br />
															{l s='Delivery time:' mod='mpcartordersplit'}&nbsp;{$carrierDtl.delay}
														{/if}
														{if count($sellerDtl.commonCarrierDetail) > 1}
															<br />
															{if $carrierDtl.best_grade}
																{if $carrierDtl.best_price}
																	<span class="best_grade best_grade_price best_grade_speed">{l s='The best price and speed' mod='mpcartordersplit'}</span>
																{else}
																	<span class="best_grade best_grade_speed">{l s='The fastest' mod='mpcartordersplit'}</span>
																{/if}
															{elseif $carrierDtl.best_price}
																<span class="best_grade best_grade_price">{l s='The best price' mod='mpcartordersplit'}</span>
															{/if}
														{/if}
													</div>
													<div class="wk_carrier_com_div wk_carrier_price_div">
														{if $carrierDtl.price_with_tax && !$carrierDtl.is_free && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}
															{if $use_taxes == 1}
																{if $priceDisplay == 1}
																	{$carrierDtl.displayPriceWithoutTax}
																	{if $display_tax_label} {l s='(tax excl.)' mod='mpcartordersplit'}{/if}
																{else}
																	{$carrierDtl.displayPriceWithTax}
																	{if $display_tax_label} {l s='(tax incl.)' mod='mpcartordersplit'}{/if}
																{/if}
															{else}
																{$carrierDtl.displayPriceWithoutTax}
																{*{convertPrice price=$carrierDtl.price_without_tax}*}
															{/if}
														{else}
															{l s='Free' mod='mpcartordersplit'}
														{/if}
													</div>
												{/if}
											{/foreach}
										</button>
										<ul class="dropdown-menu wk_carrier_dp_ul">
											{foreach from=$sellerDtl.commonCarrierDetail key=id_carrier item=carrierDtl}
												<li>
													<div class="table-responsive">
														<table class="resume table">
															{* hook added by Amit Kumar Tiwari*}
															<tr class="carrier_data_tr" data-id-carrier="{$id_carrier}" data-id-seller="{$id_seller}" data-is_free="{if $carrierDtl.price_with_tax && !$carrierDtl.is_free && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}0{else}1{/if}">
															{*end hook added by Amit Kumar Tiwari*}
																<td class="col-xs-3 col-sm-3 delivery_option_logo">
																	{if $carrierDtl.logo}
																		<img class="order_carrier_logo" src="{$carrierDtl.logo}" alt="{$carrierDtl.name}"/>
																	{/if}
																</td>
																<td class="col-xs-6 col-sm-6 delivery_option_dtl">
																	<strong>{$carrierDtl.name}</strong>
																	{if isset($carrierDtl.delay)}
																		<br />
																		{l s='Delivery time:' mod='mpcartordersplit'}&nbsp;{$carrierDtl.delay}
																	{/if}
																	{if count($sellerDtl.commonCarrierDetail) > 1}
																		<br />
																		{if $carrierDtl.best_grade}
																			{if $carrierDtl.best_price}
																				<span class="best_grade best_grade_price best_grade_speed">{l s='The best price and speed' mod='mpcartordersplit'}</span>
																			{else}
																				<span class="best_grade best_grade_speed">{l s='The fastest' mod='mpcartordersplit'}</span>
																			{/if}
																		{elseif $carrierDtl.best_price}
																			<span class="best_grade best_grade_price">{l s='The best price' mod='mpcartordersplit'}</span>
																		{/if}
																	{/if}
																</td>
																<td class="col-xs-3 col-sm-3 delivery_option_price">
																	<div class="delivery_option_price">
																		{if $carrierDtl.price_with_tax && !$carrierDtl.is_free && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}
																			{if $use_taxes == 1}
																				{if $priceDisplay == 1}
																					{$carrierDtl.displayPriceWithoutTax}
																					{if $display_tax_label} {l s='(tax excl.)' mod='mpcartordersplit'}{/if}
																				{else}
																					{$carrierDtl.displayPriceWithTax}
																					{if $display_tax_label} {l s='(tax incl.)' mod='mpcartordersplit'}{/if}
																				{/if}
																			{else}
																				{$carrierDtl.displayPriceWithoutTax}
																			{/if}
																		{else}
																			{l s='Free' mod='mpcartordersplit'}
																		{/if}
																	</div>
																</td>
															</tr>
														</table>
													</div>
												</li>
											{/foreach}
										</ul>
									</div>
								{/if}
							{else}
								{assign var="shippingCostTi" value = $sellerDtl.diffCarrierTotal.price_with_tax}
								{assign var="shippingCostTe" value = $sellerDtl.diffCarrierTotal.price_without_tax}
								<p>{l s='AVAILABLE SHIPPING' mod='mpcartordersplit'}</p>
								<div class="multiShippingCont">
									<div class="multiShippingBlock msInputCont">
										<input type="radio" checked="checked">
									</div>
									<div class="multiShippingBlock msShippingCont">
										{foreach from=$sellerDtl.diffCarrierDetail key=id_carrier item=carrierDtl name=diffShopShipping}
											<div data-id-carrier="{$id_carrier}" data-id-seller="{$id_seller}" class="msShipping {if !$smarty.foreach.diffShopShipping.last}msShippingBorder{/if}">
												{if !$id_carrier}
													<div class="col-sm-12">
														<div class="alert alert-danger">
															<strong>{l s='Error!' mod='mpcartordersplit'}</strong> {l s='No carrier is available for this product, please remove this product from cart' mod='mpcartordersplit'}
														</div>
													</div>
												{else}
													<div class="table-responsive">
														<table class="resume table">
															<tr>
																<td class="col-xs-3 col-sm-3 delivery_option_logo">
																	{if $carrierDtl.logo}
																		<img class="order_carrier_logo" src="{$carrierDtl.logo}" alt="{$carrierDtl.name}"/>
																	{/if}
																</td>
																<td class="col-xs-9 col-sm-9 delivery_option_dtl">
																	<strong>{$carrierDtl.name}</strong>
																	{if isset($carrierDtl.delay)}
																		<br />
																		{l s='Delivery time:' mod='mpcartordersplit'}&nbsp;{$carrierDtl.delay}
																	{/if}
																</td>
															</tr>
														</table>
													</div>
												{/if}
											</div>
										{/foreach}
									</div>
									<div class="multiShippingBlock msPriceCont">
										<span>
											{if $shippingCostTi && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}
												{if $use_taxes == 1}
													{if $priceDisplay == 1}
														{$sellerDtl.diffCarrierTotal.displayPriceWithoutTax}
														<br />
														{if $display_tax_label} {l s='(tax excl.)' mod='mpcartordersplit'}{/if}
													{else}
														{$sellerDtl.diffCarrierTotal.displayPriceWithTax}
														<br />
														{if $display_tax_label} {l s='(tax incl.)' mod='mpcartordersplit'}{/if}
													{/if}
												{else}
													{$sellerDtl.diffCarrierTotal.displayPriceWithoutTax}
													<br />
													{if $display_tax_label} {l s='(tax excl.)' mod='mpcartordersplit'}{/if}
												{/if}
											{else}
												{l s='Free' mod='mpcartordersplit'}
											{/if}
										</span>
									</div>
								</div>
							{/if}
						</div>
						{/if}
						{* hook added by Amit Kumar Tiwari*}
							{hook h="displayAfterCarrierMpSplit" id_seller=$id_seller}
						{*end*}
					</div>
				</div>
			</div>
		{/foreach}
	{/foreach}
</div>