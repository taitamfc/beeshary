{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
	{if $only_total != 1}
		<div class="leo-dropdown-cart-content clearfix">
			<div class="leo-dropdown-list-item-warpper">
				<ul class="leo-dropdown-list-item">{foreach from=$cart.products item=product name="cart_product"}<li style="width: {$width_cart_item}px; height: {$height_cart_item}px" class="leo-dropdown-cart-item clearfix{if ($product.attributes|count && $show_combination) || ($product.customizations|count && $show_customization)} has-view-additional{/if}{if $smarty.foreach.cart_product.first} first{/if}{if $smarty.foreach.cart_product.last} last{/if}">						
							<div class="leo-cart-item-img">
								{if $product.images}
									<a class="label" href="{$product.url}" title="{$product.name}"><img class="img-fluid" src="{$product.images.0.bySize.small_default.url}" alt="{$product.name}" title="{$product.name}"/></a>
								{/if}	
							</div>						
							<div class="leo-cart-item-info">					
								<div class="product-name"><a class="label" href="{$product.url}" title="{$product.name}">{$product.name|truncate:18:'...'}</a></div>
								<div class="product-price">
									{if $product.has_discount}
										<div class="product-discount">
										  <span class="regular-price">{$product.regular_price}</span>
										  
										  {if $product.discount_type === 'percentage'}
											<span class="discount discount-percentage">
												-{$product.discount_percentage_absolute}
											  </span>
										  {else}
											<span class="discount discount-amount">
												-{$product.discount_to_display}
											  </span>
										  {/if}
										  
										</div>
									  {/if}
									  <div class="current-price">
										<span class="price">{$product.price}</span>
									  </div>
										{if $product.unit_price_full}
										  <div class="unit-price-cart">{$product.unit_price_full}</div>
										{/if}
								</div>
								
								{if $enable_update_quantity}
									<div class="product-quantity">												
										{if $enable_button_quantity}
											<a href="javascript:void(0)" class="leo-bt-product-quantity leo-bt-product-quantity-down"><i class="material-icons">&#xE15B;</i></a>
										{/if}
										<input
											class="leo-input-product-quantity input-group"
											data-down-url="{$product.down_quantity_url}"
											data-up-url="{$product.up_quantity_url}"
											data-update-url="{$product.update_quantity_url}"
											data-id-product = "{$product.id_product|escape:'javascript'}"
											data-id-product-attribute = "{$product.id_product_attribute|escape:'javascript'}"
											data-id-customization = "{$product.id_customization|escape:'javascript'}"
											data-min-quantity="{$product.minimal_quantity}"
											data-product-quantity="{$product.quantity}"
											data-quantity-available="{$product.quantity_available}"									
											type="text"
											value="{$product.quantity}"								
											min="{$product.minimal_quantity}"
										  />
										{if $enable_button_quantity}	
											<a href="javascript:void(0)" class="leo-bt-product-quantity leo-bt-product-quantity-up"><i class="material-icons">&#xE145;</i></a>
										{/if}
									</div>
								{else}
									<div class="product-quantity"><span class="lablel">{l s='Quantity' mod='leofeature'}</span>: {$product.quantity}</div>
								{/if}
							</div>
							<a class="leo-remove-from-cart"					
								href="javascript:void(0)"					
								title="{l s='Remove from cart' mod='leofeature'}" 
								data-link-url="{$product.remove_from_cart_url}"
								data-id-product = "{$product.id_product|escape:'javascript'}"
								data-id-product-attribute = "{$product.id_product_attribute|escape:'javascript'}"
								data-id-customization = "{$product.id_customization|escape:'javascript'}"
							>
								<i class="material-icons">&#xE872;</i>
							</a>
							{if ($product.attributes|count && $show_combination) || ($product.customizations|count && $show_customization)}
								<div class="view-additional">								
									<div class="view-leo-dropdown-additional"></div>
								</div>
							{/if}
							<div class="leo-dropdown-overlay">
								<div class="leo-dropdown-cssload-speeding-wheel"></div>
							</div>
							<div class="leo-dropdown-additional">
								{if $product.attributes|count && $show_combination}							
									<div class="view-combination label">
										
									</div>
									<div class="combinations">
										{foreach from=$product.attributes key="attribute" item="value"}
											  <div class="product-line-info">
												<span class="label">{$attribute}:</span>
												<span class="value">{$value}</span>
											  </div>
										{/foreach}
									</div>
								{/if}
								{if $product.customizations|count && $show_customization}
									<div class="view-customization label">
										
									</div>
									<div class="customizations">								
										{foreach from=$product.customizations item='customization'}			
											
											<ul>
												{foreach from=$customization.fields item='field'}
													<li>
														<span class="label">{$field.label}</span>
														{if $field.type == 'text'}
															: <span class="value">{$field.text nofilter}</span>
														{else if $field.type == 'image'}
															<img src="{$field.image.small.url}">
														{/if}
													</li>
												{/foreach}
											</ul>								
										{/foreach}								
									</div>
								{/if}
							</div>
						</li>{/foreach}</ul>
			</div>
			<div class="leo-dropdown-bottom">
			{/if}
				<div class="leo-dropdown-total" data-cart-total="{$cart.products_count}">
					<div class="leo-dropdown-cart-subtotals">
						{foreach from=$cart.subtotals item="subtotal"}
							{if $subtotal}
								<div class="{$subtotal.type} clearfix">
									<div class="row">
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">
											<span class="label">{$subtotal.label}</span>
										</div>
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">
											<span class="value">{$subtotal.value}</span>
										</div>
									</div>
								</div>
							{/if}
						{/foreach}
					</div>
					<div class="leo-dropdown-cart-total clearfix">
						<div class="row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">
								<span class="label">{$cart.totals.total.label}</span>
							</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">
								<span class="value">{$cart.totals.total.value}</span>
							</div>
						</div>
					</div>
				</div>
			{if $only_total != 1}
				<div class="leo-cart-dropdown-action clearfix">
					<a class="cart-dropdow-button cart-dropdow-viewcart btn btn-primary btn-outline" href="{$cart_url}">{l s='View Cart' mod='leofeature'}</a>
					<a class="cart-dropdow-button cart-dropdow-checkout btn btn-primary btn-outline" href="{$order_url}">{l s='Check Out' mod='leofeature'}</a>
				</div>
			</div>
		</div>
	{/if}
