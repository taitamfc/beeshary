/**
 * 2007-2017 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
 *
 * DISCLAIMER
 *
 *  @Module Name: Leo Feature
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2018 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */
$(document).ready(function(){
	//DONGND:: check safari on window
	var is_safari = false;
	if (navigator.userAgent.toLowerCase().indexOf("chrome/") !== -1)
	{
		is_safari = false;
	}
	else if (navigator.userAgent.toLowerCase().indexOf("safari/") !== -1)
	{
		is_safari = true;
	}
	if (navigator.appVersion.indexOf("Win")!=-1 && is_safari)
	{
		$('html').addClass('safari-win');
	}
	//DONGND:: clone blockcart when turn off show popup
	if (typeof show_popup != 'undefined' && !show_popup)
	{
		$(".blockcart.cart-preview").addClass('leo-blockcart').removeClass('blockcart');		
	}
	
	createModalAndDropdown(0, 0);
	
	leoSelectAttr();
	leoBtCart();
	
	prestashop.on('updateProductList', function() {
		leoSelectAttr();
		leoBtCart();	
	});	
		
	// prestashop.on('updatedCart', function (event) {
		// console.log('aaa111');
		// console.log($('#blockcart-modal'));
		// $('#blockcart-modal').on('hidden.bs.modal', function (e) {
		
			// $('.leo-bt-cart.active').find('.leo-bt-cart-content').fadeIn('fast');
			// $('.leo-bt-cart.active').find('.leo-loading').hide();
			// $('.leo-bt-cart.active').removeClass('active reset');
		// });
	// });

	// $('.blockcart.cart-preview').data('refresh-url','');
	// $('.blockcart.cart-preview').removeAttr('refresh-url');
	// $('.blockcart.cart-preview').wrap('<div class="leo-blockcart-wapper"></div>');
	prestashop.on('updateCart', function (event) {
		
		// console.log('aaa');
		// console.log(event);
		//DONGND:: refresh cart if turn off show popup
		if (typeof show_popup != 'undefined' && !show_popup)
		{
			//DONGND:: loading default cart
			if ($('.leo-blockcart.cart-preview .cssload-piano').length)
			{
				$('.leo-blockcart.cart-preview .cssload-piano').show();
			}
			// console.log('test');
			var refresh_url = $('.leo-blockcart').data('refresh-url');
			$.ajax({
				type: 'POST',
				headers: {"cache-control": "no-cache"},
				url: refresh_url,
				async: true,
				cache: false,										
				success: function (resp)
				{
					$('.leo-blockcart').replaceWith($(resp.preview).find('.blockcart'));
					$(".blockcart.cart-preview").addClass('leo-blockcart').removeClass('blockcart');
					// createModalAndDropdown(1, 0);
					//DONGND:: refresh at product page with cart button default
					if (event.reason.linkAction == 'add-to-cart' && event.resp.success)
					{
						// console.log('test');
						prestashop.emit('updateProduct', {
						  reason: ''
						});
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
				}
			});
		}
		//DONGND:: show notification if add cart success
		if (event.reason.linkAction == 'add-to-cart' && event.resp.success)
		{		
			// console.log($('.leo-bt-cart.active'));		
			
			//DONGND:: run fly cart
			if (typeof enable_flycart_effect != 'undefined' && enable_flycart_effect)
			{
				if ($('.leo-bt-cart.active').length)
				{
					//DONGND:: product list
					flyCartEffect($('.leo-bt-cart.active'));
				}
				else if (prestashop.page.page_name == 'product' || $('.product-add-to-cart .add-to-cart').length)
				{
					//DONGND:: product page or quickview
					flyCartEffect($('.product-add-to-cart .add-to-cart'));
				}
			}
			
			if ($('.leo-notification').length && typeof enable_notification != 'undefined' && enable_notification)
			{
				//DONGND:: show notification
				var id_product = event.resp.id_product;
				var product_name = false;
				$.each(event.resp.cart.products, function(key, value){
					// console.log(value);
					// console.log(value.id_product);			
					// console.log(value.name);
					if (id_product == value.id_product)
					{
						product_name = value.name;
						return false;
					}
				})				
				showLeoNotification('success', 'add', product_name);				
			}
		}
		
		//DONGND:: hide cart item has been deleted from page cart
		// if ($('.leo-dropdown-cart-item.deleting').length)
		// {
			// if (event.reason.idProduct)
			// {
				// var id_product = event.reason.idProduct;
				// var id_product_attribute = event.reason.idProductAttribute;
				// var id_customization = event.reason.idCustomization;
							
				// var object_parent = $('.leo-remove-from-cart[data-id-product="'+id_product+'"][data-id-product-attribute="'+id_product_attribute+'"][data-id-customization="'+id_customization+'"]').parents('.leo-dropdown-cart-item');
				// object_parent.find('.leo-dropdown-overlay').hide();
				// object_parent.find('.leo-dropdown-cssload-speeding-wheel').hide();
				// object_parent.fadeOut(function(){
					
					// object_parent.remove();
					// updateClassCartItem();
				// });
								
				// showLeoNotification('success','delete', false);
			// }
			// else
			// {				
				// showLeoNotification('error','delete', false);
			// }
		// }
		
		//DONGND:: update cart item has been updated from page cart
		// if ($('.leo-dropdown-cart-item.updating').length)
		// {		
			// if (event.reason.success)
			// {
				// var id_product = event.reason.id_product;
				// var id_product_attribute = '';
				// if (event.reason.id_product_attribute)
				// {
					// id_product_attribute = event.reason.id_product_attribute;
				// }
				// var object_element = $('.leo-input-product-quantity[data-id-product="'+id_product+'"][data-id-product-attribute="'+id_product_attribute+'"]');
				// var object_parent = object_element.parents('.leo-dropdown-cart-item');
				// if (object_parent.hasClass('updating'))
				// {
					// object_parent.find('.leo-dropdown-overlay').hide();
					// object_parent.find('.leo-dropdown-cssload-speeding-wheel').hide();
					// object_parent.removeClass('updating');
					
					// object_parent.find('.leo-input-product-quantity').data('product-quantity', object_parent.find('.leo-input-product-quantity').val());
					// var id_customization = object_parent.find('.leo-input-product-quantity').data('id-customization');
					// $('.leo-input-product-quantity[data-id-product="'+id_product+'"][data-id-product-attribute="'+id_product_attribute+'"][data-id-customization="'+id_customization+'"]').val(object_parent.find('.leo-input-product-quantity').val()).data('product-quantity', object_parent.find('.leo-input-product-quantity').val());
				// }
			
				// showLeoNotification('success','update', false);
			// }
			// else
			// {
				// showLeoNotification('error','update', false);
			// }
			// $('.leo-dropdown-cart-item.updating').removeClass('updating');
			
		// }
		
		// console.log('test1');
		if (typeof show_popup != 'undefined' && show_popup)
		{
			// console.log('test');
			check_active_modal_cart = setInterval(function(){
				
				if ($('.leo-bt-cart.active').length && $('#blockcart-modal').length && $('#blockcart-modal').hasClass('modal fade in'))
				{
					
					$('.leo-bt-cart.active').find('.leo-bt-cart-content').fadeIn('fast');
					$('.leo-bt-cart.active').find('.leo-loading').hide();
					$('.leo-bt-cart.active').removeClass('active reset');
					
					clearInterval(check_active_modal_cart);
				}
				
			}, 200);
		}
		
		if (typeof show_popup != 'undefined' && !show_popup)
		{
			if ($('.leo-bt-cart.active').length)
			{
				$('.leo-bt-cart.active').find('.leo-bt-cart-content').fadeIn('fast');
				$('.leo-bt-cart.active').find('.leo-loading').hide();
				$('.leo-bt-cart.active').removeClass('active reset');
			}
		}

		// createModalAndDropdown(1, 0);	
		//DONGND:: loading fly cart
		if ($('.leo-fly-cart .leo-fly-cart-cssload-loader').length)
		{
			$('.leo-fly-cart .leo-fly-cart-cssload-loader').show();
		}	
                
                
	//prestashop.on('updatedCart', function (event) {
		// console.log(event);
		// var scrollToElement = false;
			
		// console.log('bbb');
		// console.log(event);
		
		if ($('.leo-dropdown-cart-item.deleting').length)
		{
			$('.leo-dropdown-cart-item.deleting .leo-dropdown-overlay').hide();
			$('.leo-dropdown-cart-item.deleting .leo-dropdown-cssload-speeding-wheel').hide();
			$('.leo-dropdown-cart-item.deleting').fadeOut(function(){
				$('.leo-dropdown-cart-item.deleting').remove();
				updateClassCartItem();
			})
			showLeoNotification('success','delete', false);
		}
		
		if ($('.leo-dropdown-cart-item.updating').length)
		{		
			$('.leo-dropdown-cart-item.updating .leo-dropdown-overlay').hide();
			$('.leo-dropdown-cart-item.updating .leo-dropdown-cssload-speeding-wheel').hide();
			$('.leo-dropdown-cart-item.updating').removeClass('updating');
			showLeoNotification('success','update', false);
		}
		
		// if ($('.leo-dropdown-cart-item.deleting').length || $('.leo-dropdown-cart-item.updating').length)
		// {
			
			// createModalAndDropdown(1, 1);
		// }
		// else
		// {
			// if (typeof show_popup && show_popup)
			// {
				//DONGND:: remove do not close dropdown-dropup when delete at page cart
				$('.leo-dropdown-cart.dropdown').removeClass('disable-close');
				$('.leo-dropdown-cart.dropup').removeClass('disable-close');
				createModalAndDropdown(1, 0);			
			// }
			
		// }
			
	//});
        
        
	});
	
	//DONGND:: call event for fly cart slide bar
	activeEventFlyCartSlideBar();
	$('.leo-fly-cart.enable-slidebar .leo-fly-cart-icon-wrapper').click(function(){
		$('.leo-fly-cart.enable-slidebar .leo-fly-cart-icon').trigger('click');
	});
	$('.leo-fly-cart.enable-slidebar .leo-fly-cart-icon').click(function(){
		showSlideBarCart($(this));
		return false;
	});
	
	$('.leo-fly-cart.enable-dropdown .leo-fly-cart-icon-wrapper , .leo-fly-cart.enable-dropdown .leo-fly-cart-icon').click(function(){		
		showDropDownCart($(this), 'flycart');
		return false;
	});
	
	//DONGND:: click out to close dropdown-dropup cart
	$(document).click(function (e) {
		e.stopPropagation();
		var container = $(".leo-dropdown-cart.dropdown.show");

		//check if the clicked area is dropDown or not
		if (container.length && container.has(e.target).length === 0) {
			if (!container.hasClass('disable-close'))
			{
				container.removeClass('show');
			}

		}
		var container1 = $(".leo-dropdown-cart.dropup.show");

		//check if the clicked area is dropDown or not
		if (container1.length && container1.has(e.target).length === 0) {
			if (!container1.hasClass('disable-close'))
			{
				container1.removeClass('show');
			}

		}
	})
	
	getOffsetFlycartIcon();
	
	//DONGND:: resize update scroll bar of fly cart slide bar
	$(window).resize(function(){
		//DONGND:: active scroll bar
		$('.leo-dropdown-list-item').each(function(){
			
			//DONGND:: scroll bar for slidebar
			if ($(this).parents('.leo-fly-cart-slidebar').length)
			{
				checkFlyCartScrollBar($(this));
			}
			//DONGND:: scroll bar for dropup/dropdown		
			if ($(this).parents('.leo-fly-cart.type-fixed.enable-dropdown').length)
			{
				checkFlyCartScrollBarDropDown($(this));
			}
			
		})
		getOffsetFlycartIcon();
	})
});

//DONGND:: event for button add cart
function leoBtCart()
{
	$('.leo-bt-cart').each(function(){
		if (!$(this).hasClass('leo-enable'))
		{	
			$(this).addClass('leo-enable');
			$(this).click(function(event){
				if ($(this).hasClass('active') || $(this).hasClass('reset') || $('.leo-bt-cart.active').length || $(this).hasClass('disabled'))
				{
					return false;
				}
				
				$(this).find('.leo-bt-cart-content').hide();
				$(this).find('.leo-loading').css({'display':'block'});
				$(this).addClass('active');
				// console.log('aaa');
				// return false;
				// var object_button_container = $(this).parents('.button-container');
				var object_button_container = $(this).parents('.product-miniature');
				if (object_button_container.find('.leo_cart_quantity').length)
				{
					object_button_container.find('.qty_product').val(object_button_container.find('.leo_cart_quantity').val());
				}
				
				var qty_product = object_button_container.find('.qty_product').val();
				var min_qty = object_button_container.find('.minimal_quantity').val();
				var quantity_product = object_button_container.find('.quantity_product').val();
				// console.log(qty_product);
				// console.log(min_qty);
				// console.log(quantity_product);
				if(Math.floor(qty_product) == qty_product && $.isNumeric(qty_product) && qty_product > 0)
				{
					// return true;
				}
				else
				{
					$(this).addClass('reset');
					// $(this).siblings('.qty_product').val(min_qty);
					$('.leo-modal-cart .modal-header').addClass('warning-mess');
					$('.leo-modal-cart .leo-warning').show();
					$('.leo-modal-cart').modal('show');
					return false;
				}
				// $('.leo-modal-cart .modal-header').addClass('block-mess');
				// $('.leo-modal-cart .leo-block').show();
				// $('.leo-modal-cart').modal('show');
				if (parseInt(qty_product) < parseInt(min_qty))
				{
					$(this).addClass('reset');
					$('.leo-modal-cart .modal-header').addClass('info-mess');
					$('.leo-modal-cart .leo-info .alert-min-qty').text(min_qty);
					$('.leo-modal-cart .leo-info').show();
					$('.leo-modal-cart').modal('show');
					return false;
				}
				
				//DONGND:: check product out stock
				//var id_product = object_button_container.find('.id_product').val();
				var id_product = jQuery('#product_page_product_id').val();
				if( !id_product ){
					var id_product = object_button_container.find('.id_product').val();
				}
				var id_product_attribute = object_button_container.find('.id_product_attribute').val();
				var id_customization = object_button_container.find('.id_customization').val();
				
				
				
				if( $( 'body' ).hasClass('product-id-category-13') && $('#booking_product_time_slots').length > 0 ){
					id_product = id_product_attribute = id_customization = $('#product_page_product_id').val();
				}

				
				// var check_product_outstock = checkProductOutStock(id_product, id_product_attribute, qty_product);
				// console.log(check_product_outstock);
				// if (parseInt(qty_product) > parseInt(quantity_product))
				// {
					// $(this).addClass('reset');
					// $('.leo-modal-cart .modal-header').addClass('block-mess');			
					// $('.leo-modal-cart .leo-block').show();
					// $('.leo-modal-cart').modal('show');
					// return false;
				// }
				// return false;
				// console.log(check_product_outstock);
				var $element = $(this);
				$(this).removeData('check-outstock');
				
				console.log(id_product);
				
				// $.when(checkProductOutStock(id_product, id_product_attribute, id_customization, qty_product, $element, true)).done(function(data){
					checkProductOutStock(id_product, id_product_attribute, id_customization, qty_product, $element, true);
					check_data_outstock = setInterval(function(){					
						if (typeof $element.data('check-outstock') != 'undefined')
						{							
							clearInterval(check_data_outstock);
							if (!$element.data('check-outstock'))
							{
								// console.log('bbbb');
								$element.addClass('reset');
								$('.leo-modal-cart .modal-header').addClass('block-mess');			
								$('.leo-modal-cart .leo-block').show();
								$('.leo-modal-cart').modal('show');
								// return false;
								event.preventDefault();
								event.stopPropagation();
								// check_product_outstock = false;
							}
							else
							{
								var $form = $element.closest('form');
								var query = $form.serialize() + '&add=1&action=update';
								
								//if( $( 'body' ).hasClass('product-id-category-13') || $('#booking_product_time_slots').length > 0 ){
									id_product = $('#product_page_product_id').val();
									query += '&id_product_attribute=0&id_product='+id_product;
									
									console.log('zo '+id_product);
								//}
				
								
								var actionURL = $form.attr('action');
								
								//DONGND:: run add to cart
								$.ajax({
									type: 'POST',
									headers: {"cache-control": "no-cache"},
									url: actionURL,
									async: true,
									cache: false,
									data: query,
									dataType: 'json',
									success: function (result)
									{
										if(result.success)
										{							
											if ($('.leo-notification').length && typeof enable_notification != 'undefined' && enable_notification)
											{
												//DONGND:: show notification
												var id_product = result.id_product;
												
												if( $( 'body' ).hasClass('product-id-category-13') && $('#booking_product_time_slots').length > 0 ){
													id_product = $('#product_page_product_id').val();
												}
				
												var product_name = false;
												$.each(result.cart.products, function(key, value){
													// console.log(value);
													// console.log(value.id_product);			
													// console.log(value.name);
													if (id_product == value.id_product)
													{
														product_name = value.name;
														return false;
													}
												})				
												showLeoNotification('success', 'add', product_name);				
											}
											
											//DONGND:: run fly cart
											if (typeof enable_flycart_effect != 'undefined' && enable_flycart_effect)
											{
												if ($('.leo-bt-cart.active').length)
												{
													//DONGND:: product list
													flyCartEffect($('.leo-bt-cart.active'));
												}
											}
											
											//DONGND:: loading fly cart
											if ($('.leo-fly-cart .leo-fly-cart-cssload-loader').length)
											{
												$('.leo-fly-cart .leo-fly-cart-cssload-loader').show();
											}
											
											//DONGND:: loading default cart
											if ($('.leo-blockcart.cart-preview .cssload-piano').length)
											{
												$('.leo-blockcart.cart-preview .cssload-piano').show();
											}
											
											//DONGND:: reset button add cart
											if (typeof show_popup != 'undefined' && !show_popup)
											{
												if ($('.leo-bt-cart.active').length)
												{
													$('.leo-bt-cart.active').find('.leo-bt-cart-content').fadeIn('fast');
													$('.leo-bt-cart.active').find('.leo-loading').hide();
													$('.leo-bt-cart.active').removeClass('active reset');
												}
												
												//DONGND:: refresh default cart
												var refresh_url = $('.leo-blockcart').data('refresh-url');
												$.ajax({
													type: 'POST',
													headers: {"cache-control": "no-cache"},
													url: refresh_url,
													async: true,
													cache: false,										
													success: function (resp)
													{
														$('.leo-blockcart').replaceWith($(resp.preview).find('.blockcart'));
														$(".blockcart.cart-preview").addClass('leo-blockcart').removeClass('blockcart');
														//DONGND:: refresh default cart
														createModalAndDropdown(1, 0);
														
													},
													error: function (XMLHttpRequest, textStatus, errorThrown) {
														console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
													}
												});
											}
																		
											if (typeof show_popup != 'undefined' && show_popup)
											{
												var refreshURL = $('.blockcart').data('refresh-url');
												var requestData = {};
											
												requestData = {
													id_product_attribute: result.id_product_attribute,
													id_product: result.id_product,
													action: 'add-to-cart'
												};
												
												$.ajax({
													type: 'POST',
													headers: {"cache-control": "no-cache"},
													url: refreshURL,
													async: true,
													cache: false,
													data: requestData,
													dataType: 'json',
													success: function (resp)
													{
														if ($('.leo-bt-cart.active').length)
														{
															$('.leo-bt-cart.active').find('.leo-bt-cart-content').fadeIn('fast');
															$('.leo-bt-cart.active').find('.leo-loading').hide();
															$('.leo-bt-cart.active').removeClass('active reset');
														}
														
														//DONGND:: refresh default cart
														$('.blockcart').replaceWith($(resp.preview).find('.blockcart'));
														//DONGND:: show modal popup
														if (resp.modal) {													
															showModalPopupCart(resp.modal);
														}
														
														//DONGND:: refresh dropdown cart
														createModalAndDropdown(1, 0);	
													},
													error: function (XMLHttpRequest, textStatus, errorThrown) {
														console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
													}
												});
																
											}																
																			
										}
										else
										{
											//DONGND:: show notification
											showLeoNotification('error','add', false);
										}
									},
									error: function (XMLHttpRequest, textStatus, errorThrown) {
										// alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
										showLeoNotification('error','add', false);
									}
								});
							}
						}			
					}, 10);
					// console.log($element);
					// console.log($element.data('check-outstock'));
					
				// });
				
				
				return false;
			});
		};
	});
	
	$('.leo_cart_quantity').each(function(){
		//DONGND:: hide input quantity when cart buton does not show
		if ($(this).parents('.product-miniature').find('.qty_product').val())
		{
			$(this).val($(this).parents('.product-miniature').find('.qty_product').val());
		}
		else
		{
			$(this).hide();
		}
	})
	// $('.leo_cart_quantity').change(function(){
		// console.log($(this).val());
		// $(this).parents('.product-miniature').find('.qty_product').val($(this).val());
	// })
}

//DONGND:: event for button add cart
function leoSelectAttr()
{
	$('.leo-select-attr').click(function(e){
		e.preventDefault();
		var id_product = $(this).data('id-product');
		var attr_txt = $(this).text();
		var id_attr = $(this).data('id-attr');
		var qty_attr = $(this).data('qty-attr');
		var min_qty_attr = $(this).data('min-qty-attr');
		// var parent_e = $(this).parents('.button-container');
		var parent_e = $(this).parents('.product-miniature');
		
		// console.log(attr_txt);
		if (!$(this).hasClass('selected'))
		{
			$(this).siblings().removeClass('selected');
			$(this).addClass('selected');
			parent_e.find('.dropdownListAttrButton_'+id_product).text(attr_txt);
			if($(this).hasClass('disable'))
			{
				if(!parent_e.find('.leo-bt-cart_'+id_product).hasClass('disabled'))
				{
					parent_e.find('.leo-bt-cart_'+id_product).addClass('disabled');
				}
			}
			else
			{
				if(parent_e.find('.leo-bt-cart_'+id_product).hasClass('disabled'))
				{
					parent_e.find('.leo-bt-cart_'+id_product).removeClass('disabled');
				}
			};
			
			var $product_article_e = $(this).parents('.product-miniature[data-id-product=' + id_product+']');
			// console.log($product_article_e);
			$product_article_e.find('.leo-bt-cart .leo-bt-cart-content').hide();
			$product_article_e.find('.leo-bt-cart .leo-loading').css({'display':'block'});
			$product_article_e.find('.leo-bt-cart').addClass('active');
			
			$.ajax({
				type: 'POST',
				headers: {"cache-control": "no-cache"},
				url: prestashop.urls.base_url + 'modules/leofeature/psajax.php?rand=' + new Date().getTime(),
				async: true,
				cache: false,
				data: {
					"action": "get-attribute-data",
					"id_product": id_product,
					"id_product_attribute": id_attr,
					"token": leo_token
				},
				success: function (result)
				{
					if(result != '')
					{						
						var obj = $.parseJSON(result);
						// console.log($(this));
						// console.log($('.product-miniature[data-id-product=' + id_product+']'));
						
						$product_article_e.find('.product-thumbnail img').attr('src', obj.product_cover.bySize.home_default.url).attr('alt', obj.product_cover.legend);
						$product_article_e.find('.product-thumbnail').attr('href', obj.product_url);
						$product_article_e.find('.product-price-and-shipping').empty().append(obj.price_attribute);
						// if (typeof enable_product_label != 'undefined' && enable_product_label)
						// {							
							// updatePostionLabel($product_article_e);
						// }
					}
					else
					{
						alert(add_cart_error);
					}
					$('.leo-bt-cart.active').find('.leo-bt-cart-content').fadeIn('fast');
					$('.leo-bt-cart.active').find('.leo-loading').hide();
					$('.leo-bt-cart.active').removeClass('active reset');
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
				}
			});
		}
		
		// $('#quantity_product_'+id_product).val(qty_attr);
		// $('#id_product_attribute_'+id_product).val(id_attr);
		// $('#minimal_quantity_'+id_product).val(min_qty_attr);
		// $('#qty_product_'+id_product).val(min_qty_attr).attr('min',min_qty_attr);
		
		parent_e.find('.quantity_product_'+id_product).val(qty_attr);
		parent_e.find('.id_product_attribute_'+id_product).val(id_attr);
		parent_e.find('.minimal_quantity_'+id_product).val(min_qty_attr);
		parent_e.find('.qty_product_'+id_product).val(min_qty_attr).data('min',min_qty_attr);
		
		parent_e.find('.leo_cart_quantity').val(min_qty_attr);
		// $('#dropdownListAttrButton_'+id_product).trigger('click');
		parent_e.find('.dropdownListAttrButton_'+id_product).trigger('click');
		
		// leoBtCart();
		// return false;
		
	});
	
	// leoBtCart();
}

//DONGND:: event for module popup after add cart
function activeEventModal()
{
	$('.leo-modal-cart').on('hide.bs.modal', function (e) {
		// console.log('test');
		$('.leo-modal-cart .modal-header').removeClass('block-mess info-mess warning-mess');
		$('.leo-modal-cart .modal-title').hide();
		var min_qty = $('.leo-bt-cart.reset').parents('.button-container').find('.minimal_quantity').val(); 
		$('.leo-bt-cart.reset').parents('.button-container').find('.qty_product').val(min_qty);
		$('.leo-bt-cart.reset').parents('.product-miniature').find('.leo_cart_quantity').val(min_qty);
		$('.leo-bt-cart.active').find('.leo-bt-cart-content').fadeIn('fast');
		$('.leo-bt-cart.active').find('.leo-loading').hide();
		$('.leo-bt-cart.active').removeClass('active reset');
		
	  // do something...
	});
	
	// $('.leo-modal-cart').on('show.bs.modal', function (e) {
		// $('.leo-bt-cart.active').find('.leo-bt-cart-content').show();
		// $('.leo-bt-cart.active').find('.leo-loading').hide();
	// })
}

//DONGND:: update position label
function updatePostionLabel($parent)
{
	var FLAG_MARGIN = 10;
      var $percent = $parent.find('.discount-percentage');
      var $onsale =  $parent.find('.on-sale');
      var $new = $parent.find('.new');
      if($percent.length){
        $new.css('top', $percent.height() * 2 + FLAG_MARGIN);
        $percent.css('top',-$parent.find('.thumbnail-container').height() + $parent.find('.product-description').height() + FLAG_MARGIN);
      }
      if($onsale.length){
        $percent.css('top', parseFloat($percent.css('top')) + $onsale.height() + FLAG_MARGIN);
        $new.css('top', ($percent.height() * 2 + $onsale.height()) + FLAG_MARGIN * 2);
      }
}

//DONGND:: show dropdown cart
function showDropDownCart($element, $type)
{
	var object_element = '';
	if ($type == 'defaultcart')
	{
		object_element = $element.siblings('.leo-dropdown-cart');
	}
	if ($type == 'flycart')
	{
		object_element = $element.parents('.leo-fly-cart').find('.leo-dropdown-cart');
	}
	
	if (!object_element.hasClass('show'))
	{
		object_element.addClass('show');
	}
	else
	{
		object_element.removeClass('show');
	}
	
}

//DONGND:: show dropdown cart
function showSlideBarCart($element)
{
	if (!$('.leo-fly-cart-slidebar.disable').length)
	{
		if (!$element.hasClass('active-slidebarcart'))
		{
			var type = $element.data('type');
			var pusheffect = $element.data('pusheffect');
			$element.addClass('active-slidebarcart');
			
			$('.leo-fly-cart-slidebar.'+type).addClass('active');
			if ($('.leo-fly-cart-mask').length)
			{
				$('.leo-fly-cart-mask').first().addClass('active');
				$('body').addClass('leoflycart-active-slidebar');
			}
			//DONGND:: update scrollbar of slidebar cart for safari win
			if ($('html').hasClass('safari-win'))
			{				
				$('.leo-fly-cart-slidebar .leo-dropdown-list-item').each(function(){		
					//DONGND:: scroll bar for slidebar
					checkFlyCartScrollBar($(this));
				})
			};

			if (pusheffect)
			{
				$('.leo-fly-cart-slidebar.'+type).addClass('push');
				$('body').addClass('leoflycart-active-push');
				var push_value;
				var push_type;
				if (type == 'slidebar_top' || type == 'slidebar_bottom')
				{
					push_type = "Y";
					if (type == 'slidebar_top')
					{
						// push_value = $('.leo-fly-cart-slidebar.push.'+type).outerHeight()+$('.leo-fly-cart-slidebar.push.'+type+' .leo-fly-cart').outerHeight();
						push_value = $('.leo-fly-cart-slidebar.push.'+type).outerHeight();
					}
					if (type == 'slidebar_bottom')
					{
						// push_value = -($('.leo-fly-cart-slidebar.push.'+type).outerHeight()+$('.leo-fly-cart-slidebar.push.'+type+' .leo-fly-cart').outerHeight());
						push_value = -$('.leo-fly-cart-slidebar.push.'+type).outerHeight();
					}

				}
				if (type == 'slidebar_right' || type == 'slidebar_left')
				{
					push_type = "X";
					if (type == 'slidebar_left')
					{
						// push_value = $('.leo-fly-cart-slidebar.push.'+type).outerWidth()+$('.leo-fly-cart-slidebar.push.'+type+' .leo-fly-cart').outerWidth();
						push_value = $('.leo-fly-cart-slidebar.push.'+type).outerWidth();
					}
					if (type == 'slidebar_right')
					{
						// push_value = -($('.leo-fly-cart-slidebar.push.'+type).outerWidth()+$('.leo-fly-cart-slidebar.push.'+type+' .leo-fly-cart').outerWidth());
						push_value = -$('.leo-fly-cart-slidebar.push.'+type).outerWidth();
					}

				}
				$('body.leoflycart-active-push main').css({
					"-moz-transform": "translate"+push_type+"("+push_value+"px)",
					"-webkit-transform": "translate"+push_type+"("+push_value+"px)",
					"-o-transform": "translate"+push_type+"("+push_value+"px)",
					"-ms-transform": "translate"+push_type+"("+push_value+"px)",
					"transform": "translate"+push_type+"("+push_value+"px)",
				})
			}
			
		}
		else
		{
			$('.leo-fly-cart-slidebar .leo-fly-cart-icon').trigger('click');
		}
	}
}

//DONGND:: event for dropdown cart
function activeDropdownEvent()
{
	//DONGND:: active scroll bar
	$('.leo-dropdown-list-item').each(function(){
		// console.log($(this).parents('.leo-fly-cart-slidebar'));
		var check_number_cartitem = 3;
		if (typeof number_cartitem_display != 'undefined')
		{
			check_number_cartitem = number_cartitem_display;
		}
		//DONGND:: scroll bar for dropdown
		// checkFlyCartScrollBarDropDown($(this));
		// if (!$(this).parents('.leo-fly-cart-slidebar').length && $(this).find('.leo-dropdown-cart-item').length > check_number_cartitem)
		// {
		if (!$(this).parents('.leo-fly-cart-slidebar').length)
		{			
			// if (typeof height_cart_item != 'undefined')
			// {
				// $(this).addClass('active-scrollbar').css({'max-height': height_cart_item*number_cartitem_display});
			// }
			// else
			// {
				// $(this).addClass('active-scrollbar').css({'max-height': $(this).find('.leo-dropdown-cart-item').outerHeight()*check_number_cartitem});
			// }
			
			
			// $(this).mCustomScrollbar({
				// theme:"dark",
				// scrollInertia: 200,
				// callbacks:{
					// onInit:function(){
					  
					// }
				// },
				// keyboard:{
					// enable:true,						
				// }
			// });
			
			// $(this).mCustomScrollbar('update');
			// console.log('test');
			checkFlyCartScrollBarDropDown($(this));
			// if ($scrollToElement != false)
			// {
				// $(this).mCustomScrollbar('scrollTo',$(this).find('.mCSB_container').find('.leo-dropdown-cart-item:eq('+$scrollToElement+')'),{scrollInertia:0});
			// }
			
			// $(this).scrollbar();
		}
		
		//DONGND:: scroll bar for slidebar
		if ($(this).parents('.leo-fly-cart-slidebar').length)
		{
			checkFlyCartScrollBar($(this));
			
		}
	})
	
	//DONGND:: highlight dropdown cart item
	$('.leo-remove-from-cart, .view-leo-dropdown-additional').hover(function(){
		
		if ($(this).hasClass('leo-remove-from-cart'))
		{
			$(this).parents('.leo-dropdown-cart-item').addClass('high-light');
		}
		
	},function(){
		if ($(this).hasClass('leo-remove-from-cart'))
		{
			$(this).parents('.leo-dropdown-cart-item').removeClass('high-light');
		}
	})
	
	//DONGND:: show additional info
	$('.view-leo-dropdown-additional').click(function(){
		var parent_obj = $(this).parents('.leo-dropdown-cart-item');
		
		var wrapper_parent_obj = $(this).parents('.leo-dropdown-list-item');
			
		if (!$(this).hasClass('show'))
		{
			if (wrapper_parent_obj.find('.leo-dropdown-cart-item.show-additional'))
			{
				wrapper_parent_obj.find('.leo-dropdown-cart-item.show-additional').removeClass('show-additional');
				wrapper_parent_obj.find('.view-leo-dropdown-additional.show').removeClass('show');
				wrapper_parent_obj.find('.fake-element').fadeOut('200',function(){
					$(this).remove();
				});
				wrapper_parent_obj.mCustomScrollbar("update");
				
				setTimeout(function() {
					wrapper_parent_obj.parents('.leo-dropdown-list-item-warpper').mCustomScrollbar("update");				
				},500);
			}
			$(this).addClass('show');
			
			if (wrapper_parent_obj.hasClass('active-scrollbar') && parent_obj.hasClass('last'))			
			{				
				// if ($(this).parents('.leo-fly-cart-slidebar.push_slidebar_top').length || $(this).parents('.leo-fly-cart-slidebar.slidebar_top').length || $(this).parents('.leo-fly-cart-slidebar.push_slidebar_bottom').length || $(this).parents('.leo-fly-cart-slidebar.slidebar_bottom').length)
				// {				
					// var width_clone_obj = parent_obj.width();
					// wrapper_parent_obj.find('.leo-dropdown-list-item').append('<li class="fake-element" style="width:'+width_clone_obj+'px"></li>');
					// wrapper_parent_obj.mCustomScrollbar("update");
					// wrapper_parent_obj.mCustomScrollbar("scrollTo","100%",{
						// callbacks: parent_obj.addClass('show-additional')
					// });
				// }
				// else
				// {
					var height_clone_obj = parent_obj.find('.leo-dropdown-additional').height();
					wrapper_parent_obj.find('.mCSB_container').append('<p class="fake-element" style="height:'+height_clone_obj+'px"></p>');
					wrapper_parent_obj.mCustomScrollbar("update");
					wrapper_parent_obj.mCustomScrollbar("scrollTo","bottom",{
						callbacks: parent_obj.addClass('show-additional')
					});
				// }
	
			} else if(wrapper_parent_obj.parents('.leo-dropdown-list-item-warpper').hasClass('active-scrollbar') && parent_obj.hasClass('last') && parent_obj.hasClass('first'))
			{
				wrapper_parent_obj.append('<li class="leo-dropdown-cart-item clearfix has-view-additional fake-element" style="width:'+width_cart_item+'px; height:'+height_cart_item+'px"></p>');
				wrapper_parent_obj.parents('.leo-dropdown-list-item-warpper').mCustomScrollbar("update");
				wrapper_parent_obj.parents('.leo-dropdown-list-item-warpper').mCustomScrollbar("scrollTo","last",{
					callbacks: parent_obj.addClass('show-additional')
				});
			}
			else
			{
				parent_obj.addClass('show-additional');
			}
			//DONGND:: disable scrollbar with fly cart slide bar top
			// if ($(this).parents('.leo-fly-cart-slidebar.push_slidebar_top.active-scroll').length || $(this).parents('.leo-fly-cart-slidebar.slidebar_top.active-scroll').length)
			// {
				// $(this).parents('.leo-fly-cart-slidebar').find('.leo-dropdown-list-item-warpper.active-scrollbar').mCustomScrollbar("destroy");
				// $(this).parents('.leo-fly-cart-slidebar').addClass('disable-scrollbar');
			// }
		}
		else
		{
			parent_obj.removeClass('show-additional');
			if (wrapper_parent_obj.hasClass('active-scrollbar') && parent_obj.hasClass('last'))			
			{			
				wrapper_parent_obj.find('.fake-element').fadeOut('200',function(){
					$(this).remove();
				});
				wrapper_parent_obj.mCustomScrollbar("update");
				
			}
			else if(wrapper_parent_obj.parents('.leo-dropdown-list-item-warpper').hasClass('active-scrollbar') && parent_obj.hasClass('last') && parent_obj.hasClass('first'))
			{
				wrapper_parent_obj.find('.fake-element').fadeOut('200',function(){
					$(this).remove();
				});
				parent_obj.removeClass('show-additional');
				$(this).removeClass('show');
				// wrapper_parent_obj.find('.fake-element').remove();
				setTimeout(function() {
					wrapper_parent_obj.parents('.leo-dropdown-list-item-warpper').mCustomScrollbar("update");				
				},500);
				
			}
			parent_obj.removeClass('show-additional');
			$(this).removeClass('show');
			//DONGND:: disable scrollbar with fly cart slide bar top
			// if ($(this).parents('.leo-fly-cart-slidebar.push_slidebar_top.active-scroll').length || $(this).parents('.leo-fly-cart-slidebar.slidebar_top.active-scroll').length)
			// {
				// if ($(this).parents('.leo-fly-cart-slidebar').hasClass('disable-scrollbar'))
				// {
					// $(this).parents('.leo-fly-cart-slidebar').removeClass('disable-scrollbar');
					// $(this).parents('.leo-fly-cart-slidebar').find('.leo-dropdown-list-item-warpper.active-scrollbar').mCustomScrollbar("update");
				// }
				
			// }
		}
		return false;
	})
	
	//DONGND:: remove dropdown cart item
	$('.leo-remove-from-cart').click(function(){
		var id_product = $(this).data('id-product');
		var id_product_attribute = $(this).data('id-product-attribute');
		var id_customization = $(this).data('id-customization');
		 
		var parent_obj = $(this).parents('.leo-dropdown-cart-item');
		
		parent_obj.addClass('deleting');
		if (parent_obj.hasClass('show-additional'))
		{
			parent_obj.find('.view-leo-dropdown-additional').trigger('click');
		}
		parent_obj.find('.leo-dropdown-overlay').show();
		parent_obj.find('.leo-dropdown-cssload-speeding-wheel').show();
		if ($('.remove-from-cart').length)
		{
			//DONGND:: do not close dropdown-dropup when delete at page cart
			$('.leo-dropdown-cart.dropdown').addClass('disable-close');
			$('.leo-dropdown-cart.dropup').addClass('disable-close');
			//DONGND:: page cart
			// console.log($('.remove-from-cart[data-id-product="'+id_product+'"][data-id-product-attribute="'+id_product_attribute+'"][data-id-customization="'+id_customization+'"]'));
			$('.remove-from-cart[data-id-product="'+id_product+'"][data-id-product-attribute="'+id_product_attribute+'"][data-id-customization="'+id_customization+'"]').trigger('click');
		}
		else
		{
			var link_url = $(this).data('link-url');
			var refresh_url = $('.leo-blockcart.cart-preview').data('refresh-url');
			// var scrollToElement = parent_obj.index()-1;
			
			$.ajax({
				type: 'POST',
				headers: {"cache-control": "no-cache"},
				url: link_url,
				async: true,
				cache: false,
				data: {
					"ajax": 1,
					"action": "update",					
				},					
				success: function (result)
				{
					var obj = $.parseJSON(result);
					parent_obj.find('.leo-dropdown-overlay').hide();
					parent_obj.find('.leo-dropdown-cssload-speeding-wheel').hide();
					
					// console.log(obj);
					if(obj.success)
					{	
						parent_obj.fadeOut(function(){
							// if (parent_obj.hasClass('last'))
							// {
								// parent_obj.prev().addClass('last');
							// }
							// if (parent_obj.hasClass('first'))
							// {
								// parent_obj.next().addClass('first');
							// }
							parent_obj.remove();
							$('.leo-remove-from-cart[data-id-product="'+id_product+'"][data-id-product-attribute="'+id_product_attribute+'"][data-id-customization="'+id_customization+'"]').parents('.leo-dropdown-cart-item').remove();
							updateClassCartItem();						
							
						});
						//DONGND:: show notification
						showLeoNotification('success','delete', false);
						
						//DONGND:: refresh cart
						$.ajax({
							type: 'POST',
							headers: {"cache-control": "no-cache"},
							url: refresh_url,
							async: true,
							cache: false,										
							success: function (resp)
							{
								$('.leo-blockcart').replaceWith($(resp.preview).find('.blockcart'));								
								$(".blockcart.cart-preview").addClass('leo-blockcart');	
								if (typeof show_popup != 'undefined' && !show_popup)
								{
									$(".blockcart.cart-preview").removeClass('blockcart')
								}
								createModalAndDropdown(1, 1);
								
							},
							error: function (XMLHttpRequest, textStatus, errorThrown) {
								console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
							}
						});
						
					}
					else
					{
						//DONGND:: show notification
						showLeoNotification('error','delete', false);
					}
					
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
				}
			});
		}
		
		return false;
	})
	
	//DONGND:: change quantity product dropdown with input
	// $("body").on('focusout','.leo-input-product-quantity',function (event) {
		// updateQuantityProductDropDown(event.currentTarget);
		
	// });
	// $("body").on('keyup','.leo-input-product-quantity',function (event) {
		// if (event.keyCode == 13) {
			// updateQuantityProductDropDown(event.currentTarget);
		// }
	// });
	$('.leo-input-product-quantity').focusout(function(){
		updateQuantityProductDropDown($(this));
	})
	$('.leo-input-product-quantity').keyup(function(event){
		if (event.keyCode == 13) {
			updateQuantityProductDropDown($(this));
		}
	})
	
	// var update_quantity = false;
	var timer;
	var flag = false;
	
	//DONGND:: change quantity product dropdown with button up-down
	$('.leo-bt-product-quantity-down, .leo-bt-product-quantity-up').on('touchstart click', function(){
		if(flag == true)
		{
			flag = false;
			clearTimeout(timer);
		}
		flag = true;
		// console.log('test');
		var action = 'up';
		var input_target = $(this).parents('.leo-dropdown-cart-item').find('.leo-input-product-quantity');
		var input_quantity = parseInt(input_target.val());
		var quantity_update;
		if ($(this).hasClass('leo-bt-product-quantity-down'))
		{
			action = 'down';
		}
		
		if (action == 'up')
		{
			quantity_update = input_quantity+1;
		}
		if (action == 'down')
		{
			quantity_update = input_quantity-1;
		}
		input_target.val(quantity_update);
		// updateQuantityProductDropDown(input_target);
		// console.log(update_quantity);
		
		timer = setTimeout(
			function() 
			{
				flag = false;
				// update_quantity = true;
				// if (update_quantity)
				// {
					// console.log(update_quantity);
					updateQuantityProductDropDown(input_target);
				// }
			}, 800);
		
		return false;
	})
}

function updateQuantityProductDropDown($element)
{
	// var $this = $(event.currentTarget);
	// if (obj instanceof jQuery)
	var $this = $element;
	var product_quantity = $this.data('product-quantity');
	var min_quantity = $this.data('min-quantity');
	var max_quantity = $this.data('quantity-available');
	var input_quantity = $this.val();
	// console.log(product_quantity);
	// console.log(input_quantity);
	
	// if (input_quantity != parseInt(input_quantity) || input_quantity < 0 || isNaN(input_quantity)) {
		// console.log('test');
		// showLeoNotification('normal','check');
		// $this.val(product_quantity);
		// return;
    // }
	
	//DONGND:: validate input
	if(Math.floor(input_quantity) == input_quantity && $.isNumeric(input_quantity) && input_quantity > 0)
	{
		// return true;
	}
	else
	{
		showLeoNotification('normal', 'check', false);
		$this.val(product_quantity);
		return;
	}
	
	//DONGND:: check min quantity
	if (parseInt(input_quantity) < parseInt(min_quantity))
	{
		showLeoNotification('warning', 'min', min_quantity);
		$this.val(product_quantity);
		return false;
	}
	
	//DONGND:: do not change
    var qty = parseInt(input_quantity) - parseInt(product_quantity);
	// console.log(qty);
	// console.log(product_quantity);
	
    if (qty == 0) {
      return;
    }
	
	//DONGND:: check stock
	// if (parseInt(input_quantity) > parseInt(max_quantity))
	// {
		// showLeoNotification('warning', 'max', false);
		// $this.val(product_quantity);
		// return false;
	// }
	var id_product = $this.data('id-product');
	var id_product_attribute = $this.data('id-product-attribute');
	var id_customization = $this.data('id-customization');
	
	$this.removeData('check-outstock');
	
	var check_product_outstock = true;
	var parent_obj = $this.parents('.leo-dropdown-cart-item');
				
	parent_obj.addClass('updating');
	
	parent_obj.find('.leo-dropdown-overlay').show();
	parent_obj.find('.leo-dropdown-cssload-speeding-wheel').show();
	// $.when(checkProductOutStock(id_product, id_product_attribute, id_customization, input_quantity, $this, false)).done(function(data){
		checkProductOutStock(id_product, id_product_attribute, id_customization, input_quantity, $this, false);
		// console.log($element);
		// console.log($this.data('check-outstock'));
		check_data_outstock = setInterval(function(){					
			if (typeof $element.data('check-outstock') != 'undefined')
			{							
				clearInterval(check_data_outstock);
				if (!$this.data('check-outstock'))
				{
					// console.log('bbbb');
					showLeoNotification('warning', 'max', false);
					$this.val(product_quantity);
					// console.log(event);
					// return false;
					// return;
					// break;
					// event.preventDefault();
					// event.stopPropagation();
					check_product_outstock = false;
					parent_obj.find('.leo-dropdown-overlay').hide();
					parent_obj.find('.leo-dropdown-cssload-speeding-wheel').hide();
					parent_obj.removeClass('updating');
				}
				if (!check_product_outstock)
				{
					return false;
				}
				
				if ($('.js-cart-line-product-quantity').length)
				{
					//DONGND:: page cart
					var e = $.Event("keyup");
					e.keyCode = 13; // # Some key code value
					// console.log($('.remove-from-cart[data-id-product="'+id_product+'"][data-id-product-attribute="'+id_product_attribute+'"][data-id-customization="'+id_customization+'"]'));
					$('.remove-from-cart[data-id-product="'+id_product+'"][data-id-product-attribute="'+id_product_attribute+'"][data-id-customization="'+id_customization+'"]').parents('.cart-item').find('.js-cart-line-product-quantity').val(input_quantity).trigger(e);	
				}
				else
				{				
					var link_url = $this.data('update-url');
					var refresh_url = $('.leo-blockcart.cart-preview').data('refresh-url');
					var op = '';
					if (qty > 0)
					{
						op = 'up';
					}
					else
					{
						op = 'down';
					}
					// var scrollToElement = parent_obj.index()-1;
					
					$.ajax({
						type: 'POST',
						headers: {"cache-control": "no-cache"},
						url: link_url,
						async: true,
						cache: false,
						data: {
							"ajax": 1,
							"action": "update",	
							"qty": Math.abs(qty),
							"op": op,			
						},					
						success: function (result)
						{
							var obj = $.parseJSON(result);
							parent_obj.find('.leo-dropdown-overlay').hide();
							parent_obj.find('.leo-dropdown-cssload-speeding-wheel').hide();
							parent_obj.removeClass('updating');
							
							// console.log(obj);
							if(obj.success)
							{							
								// $this.data('product-quantity', input_quantity);
								$('.leo-input-product-quantity[data-id-product="'+id_product+'"][data-id-product-attribute="'+id_product_attribute+'"][data-id-customization="'+id_customization+'"]').val(input_quantity).data('product-quantity', input_quantity);				
								//DONGND:: show notification
								showLeoNotification('success','update', false);
								
								//DONGND:: refresh cart
								$.ajax({
									type: 'POST',
									headers: {"cache-control": "no-cache"},
									url: refresh_url,
									async: true,
									cache: false,										
									success: function (resp)
									{
										$('.leo-blockcart').replaceWith($(resp.preview).find('.blockcart'));
										$(".blockcart.cart-preview").addClass('leo-blockcart');
										if (typeof show_popup != 'undefined' && !show_popup)
										{
											$(".blockcart.cart-preview").removeClass('blockcart')
										}
										createModalAndDropdown(1, 1);
										
									},
									error: function (XMLHttpRequest, textStatus, errorThrown) {
										console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
									}
								});
								
							}
							else
							{
								//DONGND:: show notification
								showLeoNotification('error','update', false);
							}
							
						},
						error: function (XMLHttpRequest, textStatus, errorThrown) {
							console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
						}
					});
				}
			}
		}, 10);
		
	// });
	
}

function createModalAndDropdown($only_dropdown, $only_total)
{

	//DONGD:: add loading
	// $('.blockcart.cart-preview .header').append('<span class="leo-dropdown-cssload-speeding-wheel"></span>');
	// $('.blockcart.cart-preview .leo-dropdown-cssload-speeding-wheel').css({'display':'inline-block'});
	// console.log(enable_dropdown_defaultcart);
	if (typeof enable_dropdown_defaultcart != 'undefined')
	{
		// console.log(enable_dropdown_defaultcart);
		if (enable_dropdown_defaultcart)
		{
			if ($('.blockcart.cart-preview').length)
			{
				$('.blockcart.cart-preview').addClass('leo-blockcart show-leo-loading').append('<div class="cssload-piano"><div class="cssload-rect1"></div><div class="cssload-rect2"></div><div class="cssload-rect3"></div></div>');
			}
			else
			{
				$('.leo-blockcart.cart-preview').addClass('show-leo-loading').append('<div class="cssload-piano"><div class="cssload-rect1"></div><div class="cssload-rect2"></div><div class="cssload-rect3"></div></div>');
			}
			
			$('.leo-blockcart.cart-preview .cssload-piano').show();
			// $('.blockcart.cart-preview a').attr('href', 'javascript:void(0)');
			// $('.blockcart.cart-preview').addClass('dropdown-toggle').data('toggle','dropdown').attr('aria-haspopup','true').attr('aria-expanded','false').attr('id','dropdownCartNormal');
			// $('.blockcart.cart-preview').parent().addClass('dropdown');
			$('.leo-blockcart.cart-preview.show-leo-loading').data('type',type_dropdown_defaultcart);
			// if (typeof enable_dropdown_defaultcart != 'undefined')
			// {
				// $('.leo-blockcart.cart-preview.show-leo-loading').data('pusheffect', enable_pusheffect_defaultcart);
			// }
			//DONGND:: action dropdown	
			if (typeof type_dropdown_defaultcart != 'undefined')
			{
				if (type_dropdown_defaultcart == 'dropdown' || type_dropdown_defaultcart == 'dropup')
				{
					$('.leo-blockcart.cart-preview.show-leo-loading').click(function(){
						// console.log('test');
						showDropDownCart($(this), 'defaultcart');
						return false;
					})
				}
				if (type_dropdown_defaultcart == 'slidebar_left' || type_dropdown_defaultcart == 'slidebar_right' || type_dropdown_defaultcart == 'slidebar_top' || type_dropdown_defaultcart == 'slidebar_bottom')
				{
					$('.leo-blockcart.cart-preview.show-leo-loading').click(function(){
						showSlideBarCart($(this));
						return false;
					})
				}
			}
		}
		else
		{
			// console.log('test');
			$('.blockcart.cart-preview').addClass('leo-blockcart');
		}
	}
	
	//DONGND:: loading fly cart
	if ($('.leo-fly-cart .leo-fly-cart-cssload-loader').length)
	{
		$('.leo-fly-cart .leo-fly-cart-cssload-loader').show();
	}
	$.ajax({
		type: 'POST',
		headers: {"cache-control": "no-cache"},
		url: prestashop.urls.base_url + 'modules/leofeature/psajax.php?rand=' + new Date().getTime(),
		async: true,
		cache: false,
		data: {
			"action": "render-modal",
			"only_dropdown": $only_dropdown,
			"only_total": $only_total,
			"token": leo_token
		},
		success: function (result)
		{
			if (result != '')
			{
				// $('.blockcart.cart-preview .leo-dropdown-cssload-speeding-wheel').hide();
				$('.leo-blockcart.cart-preview .cssload-piano').hide();
				if ($('.leo-fly-cart .leo-fly-cart-cssload-loader').length)
				{
                                    setTimeout(function(){
                                        $('.leo-fly-cart .leo-fly-cart-cssload-loader').hide();
                                    }, 2000); 
				}
				if(result.dropdown != '')
				{
					if ($('.leo-fly-cart-slidebar.disable').length)
					{
						$('.leo-fly-cart-slidebar').removeClass('disable');
					}
					if (!$('.leo-dropdown-cart').length)
					{
						if (typeof type_dropdown_defaultcart != 'undefined' && (type_dropdown_defaultcart == 'dropdown' || type_dropdown_defaultcart == 'dropup'))
						{
							$('.leo-blockcart.cart-preview.show-leo-loading').after('<div class="leo-dropdown-cart defaultcart '+type_dropdown_defaultcart+'"></div>');
						}				
						
						//DONGND:: add dropdown to flycart
						if ($('.leo-fly-cart.enable-dropdown').length)
						{
							$('.leo-fly-cart.enable-dropdown').append('<div class="leo-dropdown-cart flycart '+$('.leo-fly-cart.enable-dropdown').data('type')+'"></div>');
						}
						
						//DONGND:: add dropdown to flycart slide bar
						if ($('.leo-fly-cart-slidebar').length)
						{
							
							$('.leo-fly-cart-slidebar').append('<div class="leo-dropdown-cart"></div>');
						}
					}
					else
					{
						$('.leo-dropdown-cart').addClass('update');
					}
					
					if ($('.leo-dropdown-cart-content').length)
					{
						// console.log('test');
						// $('.leo-dropdown-cart-content').remove();
						// $('.leo-dropdown-cart-content').replaceWith(result.dropdown);
						
						if ($only_total == 1)
						{
							$('.leo-dropdown-cart-content .leo-dropdown-total').replaceWith(result.dropdown);
							var check_number_cartitem = 3;
							if (typeof number_cartitem_display != 'undefined')
							{
								check_number_cartitem = number_cartitem_display;
							}
							//DONGND:: turn off scroll bar
							$('.leo-dropdown-list-item').each(function(){
								// if (!$(this).parents('.leo-fly-cart-slidebar').length && $(this).find('.leo-dropdown-cart-item').length <= check_number_cartitem && $(this).hasClass('active-scrollbar'))
								// {
								if (!$(this).parents('.leo-fly-cart-slidebar').length)
								{
									// $(this).removeClass("active-scrollbar").css({'max-height': 'none'});
									// $(this).mCustomScrollbar("destroy");
									// $(this).mCustomScrollbar("disable",true);
									checkFlyCartScrollBarDropDown($(this));
								}
								//DONGND:: scroll bar for slidebar
								if ($(this).parents('.leo-fly-cart-slidebar').length && $(this).parents('.leo-fly-cart-slidebar').find('.active-scrollbar'))
								{
									// console.log('test');
									checkFlyCartScrollBar($(this));
								}
							})						
						}
						else
						{
							$('.leo-dropdown-cart-content').replaceWith(result.dropdown);
							activeDropdownEvent();
						}
						
					}
					else
					{
						// console.log('test1');
						$('.leo-dropdown-cart').append(result.dropdown);
						activeDropdownEvent();
					}
					
					
				}
				else
				{
					//DONGND:: clear cart
					if ($('.leo-dropdown-cart').length)
					{
						$('.leo-dropdown-cart').remove();
					}
					$('.leo-fly-cart-slidebar').addClass('disable');
					if ($('.leo-fly-cart-slidebar.active').length)
					{
						$('.leo-fly-cart-slidebar.active').find('.leo-fly-cart-icon').trigger('click');
					}
				}
				
				//DONGND:: create modal popup
				if(result.modal != '')
				{						
					$('body').append(result.modal);
					activeEventModal();
				}
				//DONGND:: create notification
				if(result.notification != '')
				{						
					$('body').append(result.notification);								
				}
				
				//DONGND:: update cart total for fly cart
				if ($('.leo-fly-cart-total').length)
				{
					if ($('.leo-dropdown-total').length)
					{
						$('.leo-fly-cart-total').text($('.leo-dropdown-total').data('cart-total'));
					}
					else
					{
						$('.leo-fly-cart-total').text("0");
					}
						
				}
			}
			else
			{
				alert(add_cart_error);
			}
						
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			// alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
		}
	});
}

//DONGND:: event for notification
function activeEventNotification()
{
	$(".leo-notification .notification").click(function(){
		$(this).removeClass('show').addClass("closed").parent().addClass('disable');
		// var obj = $(this);
		// setTimeout(
		  // function() 
		  // {
			// obj.parent().addClass('disable');
		  // }, 700);
		// if (!$(".leo-notification .notification.show").length)
		// {
			// setTimeout(
			  // function() 
			  // {
				// $(".leo-notification").removeClass('active');
			  // }, 700);
			
		// }
	})
}

function showLeoNotification($status, $action, $special_parameter)
{
	if (!$('.leo-notification').hasClass('active'))
	{
		$('.leo-notification').addClass('active');
	}
	var clone_obj = '';
	
	clone_obj = $('.leo-temp-'+$status+'>div').clone();
	clone_obj.find('.noti-'+$action).addClass('active');
	if ($special_parameter && $special_parameter != '')
	{
		clone_obj.find('.noti-'+$action).find('.noti-special').text($special_parameter);
	}
	// clone_obj.height(clone_obj.find('.notification').height());
	// clone_obj.find('.notification').addClass('show');
	$('.leo-notification').append(clone_obj);
	setTimeout(
		  function() 
		  {
			// $('.leo-notification .notification').last().addClass('show');
			clone_obj.find('.notification').addClass('show');
			// var height_wrapper = $('.leo-notification .notification').last().height();
			// $('.leo-notification .notification-wrapper').last().height(height_wrapper);
		  }, 100);
	
	activeEventNotification();
	setTimeout(
		  function() 
		  {
			// clone_obj.find('.notification').trigger('click');
			clone_obj.find('.notification').removeClass('show').addClass("closed").parent().addClass('disable');
		  }, 5000);
}

//DONGND:: check product out stock
function checkProductOutStock($id_product, $id_product_attribute, $id_customization, $quantity, $element, $check_product_in_cart)
{
	// console.log($element);
	// return $.ajax({
	$.ajax({
		type: 'POST',
		headers: {"cache-control": "no-cache"},
		url: prestashop.urls.base_url + 'modules/leofeature/psajax.php?rand=' + new Date().getTime(),
		async: true,
		cache: false,
		data: {
			"action": "check-product-outstock",
			"id_product": $id_product,
			"id_product_attribute": $id_product_attribute,
			"id_customization": $id_customization,
			"quantity": $quantity,
			"check_product_in_cart": $check_product_in_cart,
			"token": leo_token
		},
		success: function (result)
		{
			if (result != '')
			{
				var obj = $.parseJSON(result);
				// console.log('bbb');
				// console.log(obj.success);
				
				$element.data('check-outstock', obj.success)
				// console.log($element.data('check-outstock'));
			}
			else
			{
				alert(add_cart_error);
			}
			
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			// alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
		}
	});
}

//DONGND:: update class first last of cart item
function updateClassCartItem()
{
	$('.leo-dropdown-list-item').each(function(){
		$(this).find('.leo-dropdown-cart-item').first().addClass('first');
		$(this).find('.leo-dropdown-cart-item').last().addClass('last');
	})
}

//DONGND:: create fly cart effect
function flyCartEffect($element)
{
	if ($element.hasClass('leo-bt-cart'))
	{
		var parent_e = $element.parents('.product-miniature');
		var product_img = parent_e.find('.product-thumbnail').find("img").eq(0);
	}
	else
	{
		var product_img = '';
		if ($('.quickview .product-cover').length)
		{
			//DONGND:: quickview in produc page
			product_img = $('.quickview .product-cover').find("img").eq(0);
		}
		else
		{
			//DONGND:: product page
			product_img = $('.product-cover').find("img").eq(0);
		}	
	}
	if (typeof product_img != 'undefined' && product_img.length)
	{		
		var obj_element = '';
		if ($('.leo-fly-cart-slidebar.active .leo-fly-cart-icon').length)
		{
			obj_element = $('.leo-fly-cart-slidebar.active .leo-fly-cart-icon');
		}
		else if ($('.leo-fly-cart.solo .leo-fly-cart-icon').length)
		{
			obj_element = $('.leo-fly-cart.solo .leo-fly-cart-icon');
		}
		else if ($('.leo-blockcart.cart-preview').length)
		{
			obj_element = $('.leo-blockcart.cart-preview');
		}
		else if ($('.blockcart.cart-preview').length)
		{
			obj_element = $('.blockcart.cart-preview');
		}else
		{
			return;
		}
		$('body').addClass('enable-leo-fly-cart');
		var divider = 4;
		var flyerClone = product_img.clone();
		
		var clone_offset_top = product_img.offset().top;
		var clone_offset_left = product_img.offset().left;
		//DONGND:: update for body boxed
		if ($('body').offset().left != 0)
		{
			clone_offset_left = clone_offset_left - $('body').offset().left;
		}
		flyerClone.css({position: 'absolute', top: clone_offset_top + "px", left: clone_offset_left + "px", opacity: 1, 'z-index': 1000, width: product_img.width(), height: product_img.height()});
		
		$('body').append(flyerClone);
		var gotoX = obj_element.offset().left + (obj_element.width() / 2) - (product_img.width()/divider)/2;
		//DONGND:: update for body boxed
		if ($('body').offset().left != 0)
		{
			gotoX = gotoX - $('body').offset().left;
		}
		if ($('html').hasClass('safari-win') && $('.leo-fly-cart-slidebar.active .leo-fly-cart-icon').length)
		{
			//DONGND:: update position of fly cart icon on slidebar cart for safari win
			var gotoY = $(window).scrollTop()+obj_element.offset().top + (obj_element.height() / 2) - (product_img.height()/divider)/2;
		}
		else
		{
			var gotoY = obj_element.offset().top + (obj_element.height() / 2) - (product_img.height()/divider)/2;
		}
		 
		flyerClone.animate({
			opacity: 0.4,
			left: gotoX,
			top: gotoY,
			width: product_img.width()/divider,
			height: product_img.height()/divider
		}, 1000,
		function () {		
			flyerClone.fadeOut('fast', function () {
				flyerClone.remove();
				$('body').removeClass('enable-leo-fly-cart');
			});
			if (typeof type_flycart_effect != 'undefined')
			{
				if (type_flycart_effect == 'shake')
				{
					obj_element.parents('.leo-fly-cart').find('.leo-fly-cart-total').css({'opacity':0});
					setTimeout(function () {
						obj_element.effect("shake", {
							times: 2
						}, 200, function(){
							obj_element.parents('.leo-fly-cart').find('.leo-fly-cart-total').css({'opacity':1});
						});
					}, 500);
				}
				if (type_flycart_effect == 'fade')
				{
					obj_element.fadeOut('fast', function () {
						obj_element.fadeIn('fast', function () {
						});
					});
				}
			}
			// obj_element.fadeOut('fast', function () {
				// obj_element.fadeIn('fast', function () {
					// flyerClone.fadeOut('fast', function () {
						// flyerClone.remove();
					// });
				// });
			// });
		});
		
		// var divider = 1;
		// var imgclone = product_img.clone()
                // .offset({
                // top: product_img.offset().top,
                // left: product_img.offset().left
            // })
                // .css({
                // 'opacity': '0.5',
                    // 'position': 'absolute',
                    // 'height': product_img.width()/divider,
                    // 'width': product_img.height()/divider,
                    // 'z-index': '100'
            // })
                // .appendTo($('body'))
                // .animate({
                // 'top': obj_element.offset().top + 10,
                    // 'left': obj_element.offset().left + 10,
                    // 'width': product_img.width()/divider/2,
                    // 'height': product_img.height()/divider/2
            // }, 1000, 'easeInOutExpo');
            
		// setTimeout(function () {
			// obj_element.effect("shake", {
				// times: 2
			// }, 200);
		// }, 1500);

		// imgclone.animate({
			// 'width': 0,
				// 'height': 0
		// }, function () {
			// $(this).detach()
		// });
	}
}

//DONGND:: event for fly cart slidebar
function activeEventFlyCartSlideBar()
{
	$('.leo-fly-cart-mask, .leo-fly-cart-slidebar .leo-fly-cart-icon, .leo-fly-cart-slidebar .leo-fly-cart-icon-wrapper, .leo-fly-cart-slidebar .leo-fly-cart').click(function(){
		// console.log('test');
		$('.leo-fly-cart-mask.active').removeClass('active');
		$('.leo-fly-cart-icon.active-slidebarcart').removeClass('active-slidebarcart');
		$('.leo-blockcart.cart-preview.active-slidebarcart').removeClass('active-slidebarcart');
		$('body.leoflycart-active-push main').css({
			"-moz-transform": "translateX(0px)",
			"-webkit-transform": "translateX(0px)",
			"-o-transform": "translateX(0px)",
			"-ms-transform": "translateX(0px)",
			"transform": "translateX(0px)",
			"-moz-transform": "translateY(0px)",
			"-webkit-transform": "translateY(0px)",
			"-o-transform": "translateY(0px)",
			"-ms-transform": "translateY(0px)",
			"transform": "translateY(0px)",
		});
		setTimeout(function(){
			$('body').removeClass('leoflycart-active-slidebar leoflycart-active-push');
		},300);
			
		$('.leo-fly-cart-slidebar.active').removeClass('active');
	});
	
	$(document).keyup(function(e) {
		//DONGND:: press esc
		if (e.keyCode == 27) { 
			if ($('.leo-fly-cart-mask').hasClass('active'))
			{
				$('.leo-fly-cart-mask.active').trigger('click');
			}
			else if ($('.leo-fly-cart-slidebar.active .leo-fly-cart-icon').length)
			{
				$('.leo-fly-cart-slidebar .leo-fly-cart-icon').trigger('click');
			}
		}
	});
}

//DONGND:: update scroll bar for slidebar cart
function checkFlyCartScrollBar($element)
{
	var object_parent = $element.parents('.leo-fly-cart-slidebar');
	if (object_parent.hasClass('slidebar_top') || object_parent.hasClass('slidebar_bottom'))
	{
		// console.log('test');
		var width_bottom = object_parent.find('.leo-dropdown-bottom').outerWidth();
		var window_width = $(window).width();		
		var element_width = '';
		if (typeof width_cart_item != 'undefined')
		{
			element_width = $element.find('.leo-dropdown-cart-item').length * width_cart_item;
		}
		else
		{
			element_width = $element.find('.leo-dropdown-cart-item').length * $element.find('.leo-dropdown-cart-item').outerWidth();
		}
		// console.log(width_bottom);	
		// console.log(element_width);
		// console.log(window_width);
		if (element_width+width_bottom > window_width)
		{
			object_parent.addClass('active-scroll');
			object_parent.find('.leo-dropdown-list-item-warpper').addClass('active-scrollbar');
			object_parent.find('.leo-dropdown-list-item-warpper').css({'width': window_width-width_bottom});
			object_parent.find('.leo-dropdown-list-item-warpper').mCustomScrollbar({
				theme:"dark",
				axis: "x",
				scrollInertia: 200,
				callbacks:{
					onInit:function(){
					  
					}
				},
				advanced:{
					autoExpandHorizontalScroll:true
				},
				keyboard:{
					enable:true,
					// scrollType:"stepless",
					// scrollAmount:"auto"
				}
			});
			object_parent.find('.leo-dropdown-list-item-warpper').mCustomScrollbar('update');
		}
		else
		{
			object_parent.removeClass('active-scroll');
			object_parent.find('.leo-dropdown-list-item-warpper').removeClass("active-scrollbar").css({'width': 'auto'});
			object_parent.find('.leo-dropdown-list-item-warpper').mCustomScrollbar("destroy");
		}
	}
	
	if (object_parent.hasClass('slidebar_left') || object_parent.hasClass('slidebar_right'))
	{
		var height_bottom = object_parent.find('.leo-dropdown-bottom').outerHeight();
		var window_height = $(window).height();		
		var element_height = '';
		if (typeof height_cart_item != 'undefined')
		{
			element_height = $element.find('.leo-dropdown-cart-item').length * height_cart_item;
		}
		else
		{
			element_height = $element.find('.leo-dropdown-cart-item').length * $element.find('.leo-dropdown-cart-item').outerHeight();
		}
		if (element_height+height_bottom > window_height)
		{		
			object_parent.addClass('active-scroll');
			$element.addClass('active-scrollbar');
			$element.css({'max-height': window_height-height_bottom});
			$element.mCustomScrollbar({
				theme:"dark",
				scrollInertia: 200,
				callbacks:{
					onInit:function(){
					  
					}
				},
				keyboard:{
					enable:true,
					// scrollType:"stepless",
					// scrollAmount:"auto"
				}

			});
			$element.mCustomScrollbar('update');
		}
		else
		{		
			object_parent.removeClass('active-scroll');
			$element.removeClass("active-scrollbar").css({'max-height': 'none'});
			$element.mCustomScrollbar("destroy");
		}
	}
	
}

//DONGND:: update scroll bar for dropdown/dropup
function checkFlyCartScrollBarDropDown($element)
{
	var object_parent = $element.parents('.leo-fly-cart.type-fixed.enable-dropdown');
	var type = object_parent.data('type');
	var height_bottom = object_parent.find('.leo-dropdown-bottom').outerHeight();
	
	var height_real = height_cart_item*$element.find('.leo-dropdown-cart-item').length;
	var height_icon = object_parent.find('.leo-fly-cart-icon-wrapper').outerHeight();
	var window_height = $(window).height();
	var check_number_cartitem = 3;
	if (typeof number_cartitem_display != 'undefined')
	{
		check_number_cartitem = number_cartitem_display;
	}
	var height_default = height_cart_item*number_cartitem_display;
	
	if (object_parent.length > 0 && type == 'dropup' && height_real+height_bottom > object_parent.position().top && height_default+height_bottom > object_parent.position().top)
	{
		// console.log('test0');
		$element.addClass('active-scrollbar').css({'max-height': object_parent.position().top-height_icon-height_bottom});
		$element.mCustomScrollbar({
			theme:"dark",
			scrollInertia: 200,
			callbacks:{
				onInit:function(){
				  
				}
			},
			keyboard:{
				enable:true,					
			}

		});
		$element.mCustomScrollbar('update');
	}
	else if (object_parent.length > 0 && type == 'dropdown' && height_real+height_bottom > $(window).height()-object_parent.position().top && height_default+height_bottom > $(window).height()-object_parent.position().top)
	{
		// console.log('test1');
		$element.addClass('active-scrollbar').css({'max-height': $(window).height()-object_parent.position().top-height_icon-height_bottom});
		$element.mCustomScrollbar({
			theme:"dark",
			scrollInertia: 200,
			callbacks:{
				onInit:function(){
				  
				}
			},
			keyboard:{
				enable:true,					
			}

		});
		$element.mCustomScrollbar('update');
	}
	else if ($element.find('.leo-dropdown-cart-item').length > check_number_cartitem)
	{
		// console.log('test2');
		// console.log($(this).find('.leo-dropdown-cart-item').innerHeight());
		if (typeof height_cart_item != 'undefined')
		{
			$element.addClass('active-scrollbar').css({'max-height': height_cart_item*number_cartitem_display});
		}
		else
		{
			$element.addClass('active-scrollbar').css({'max-height': $element.find('.leo-dropdown-cart-item').outerHeight()*check_number_cartitem});
		}
		
		$element.mCustomScrollbar({
			theme:"dark",
			scrollInertia: 200,
			callbacks:{
				onInit:function(){
				  
				}
			},
			keyboard:{
				enable:true,
				// scrollType:"stepless",
				// scrollAmount:"auto"					
			}
		});
		// $(this).mCustomScrollbar('update');
		
		// if ($scrollToElement != false)
		// {
			// $(this).mCustomScrollbar('scrollTo',$(this).find('.mCSB_container').find('.leo-dropdown-cart-item:eq('+$scrollToElement+')'),{scrollInertia:0});
		// }
		
		// $(this).scrollbar();
	}
	else
	{
		// console.log('test3');
		// object_parent.removeClass('active-scroll');
		$element.removeClass("active-scrollbar").css({'max-height': 'none'});
		$element.mCustomScrollbar("destroy");
	}
}

//DONGND:: set class by position of fly cart icon
function getOffsetFlycartIcon()
{
	if ($('.leo-fly-cart.solo .leo-fly-cart-icon').length)
	{
		var offset_top = $('.leo-fly-cart.solo .leo-fly-cart-icon').offset().top;
		var offset_left = $('.leo-fly-cart.solo .leo-fly-cart-icon').offset().left;
		var window_width = $(window).width();
		
		if (offset_left <= window_width/2)
		{
			$('.leo-fly-cart.solo').removeClass('offset-right').addClass('offset-left');
		}
		else
		{
			$('.leo-fly-cart.solo').removeClass('offset-left').addClass('offset-right');
		}
		
		//DONGND:: update rtl
		if (lf_is_gen_rtl && prestashop.language.is_rtl == 1)
		{			
			if ($('.leo-fly-cart.solo').hasClass('offset-right'))
			{
				$('.leo-fly-cart.solo').removeClass('offset-right').addClass('offset-left');
			}
			else if ($('.leo-fly-cart.solo').hasClass('offset-left'))
			{
				$('.leo-fly-cart.solo').removeClass('offset-left').addClass('offset-right');
			}
		}
	}
}

//DONGND:: show modal popup cart
function showModalPopupCart(modal)
{
	if ($('#blockcart-modal').length)
	{
		$('#blockcart-modal').remove();
	}
	
	$('body').append(modal);
	
	$('#blockcart-modal').modal('show');	
}