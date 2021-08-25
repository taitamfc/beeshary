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
	createLeoCompareModalPopup();
	LeoCompareButtonAction();
	prestashop.on('updateProductList', function() {
		LeoCompareButtonAction();
	});
	//DONGND:: recall button action if need when change attribute at product page
	prestashop.on('updatedProduct', function() {  
		LeoCompareButtonAction();
	});
	prestashop.on('clickQuickView', function() {		
		check_active_compare = setInterval(function(){
			if($('.quickview.modal').length)
			{			
				$('.quickview.modal').on('shown.bs.modal', function (e) {
					LeoCompareButtonAction();
				})
				clearInterval(check_active_compare);
			}
			
		}, 300);
		
	});
	activeEventModalCompare();
});

function createLeoCompareModalPopup()
{
	var leoCompareModalPopup = '';
	leoCompareModalPopup += '<div class="modal leo-modal leo-modal-compare fade" tabindex="-1" role="dialog" aria-hidden="true">';
		leoCompareModalPopup += '<div class="modal-dialog" role="document">';
			leoCompareModalPopup += '<div class="modal-content">';
				leoCompareModalPopup += '<div class="modal-header">';
					leoCompareModalPopup += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
						leoCompareModalPopup += '<span aria-hidden="true">&times;</span>';
					leoCompareModalPopup += '</button>';
					leoCompareModalPopup += '<h5 class="modal-title text-xs-center">';
					leoCompareModalPopup += '</h5>';
				leoCompareModalPopup += '</div>';
			leoCompareModalPopup += '</div>';
		leoCompareModalPopup += '</div>';
	leoCompareModalPopup += '</div>';
	$('body').append(leoCompareModalPopup);
}
function LeoCompareButtonAction()
{
	$('.leo-compare-button').click(function(){
		if (!$('.leo-compare-button.active').length)
		{
			var total_product_compare = compared_products.length;
			var id_product = $(this).data('id-product');
			
			var content_product_compare_mess_remove = productcompare_remove+'. <a href="'+productcompare_url+'" target="_blank"><strong>'+productcompare_viewlistcompare+'.</strong></a>';
			var content_product_compare_mess_add = productcompare_add+'. <a href="'+productcompare_url+'" target="_blank"><strong>'+productcompare_viewlistcompare+'.</strong></a>';
			var content_product_compare_mess_max = productcompare_max_item+'. <a href="'+productcompare_url+'" target="_blank"><strong>'+productcompare_viewlistcompare+'.</strong></a>';
			
			$(this).addClass('active');
			$(this).find('.leo-compare-bt-loading').css({'display':'block'});
			$(this).find('.leo-compare-bt-content').hide();
			var object_e = $(this);
			if ($(this).hasClass('added') || $(this).hasClass('delete'))
			{
				//DONGND:: remove product form list product compare
				//DONGND:: add product to list product compare
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: productcompare_url+ '?rand=' + new Date().getTime(),
					async: true,
					cache: false,
					data: {
						"ajax": 1,
						"action": "remove",
						"id_product": id_product,
						"token": leo_token
					},
					success: function (result)
					{
						// console.log(result);
						if (result == 1)
						{
							//Leotheme add: update number product on icon compare
							if ($('.ap-btn-compare .ap-total-compare').length)
							{
								var old_num_compare = parseInt($('.ap-btn-compare .ap-total-compare').data('compare-total'));
								var new_num_compare = old_num_compare-1;
								$('.ap-btn-compare .ap-total-compare').data('compare-total',new_num_compare);
								$('.ap-btn-compare .ap-total-compare').text(new_num_compare);
							}
													
							compared_products.splice($.inArray(parseInt(id_product), compared_products), 1);
							if (object_e.hasClass('delete'))
							{
								//DONGND:: remove from page product compare
								if ($('.leo-productscompare-item').length == 1)
								{								
									window.location.replace(productcompare_url);
								}
								else
								{
									$('td.product-'+id_product).fadeOut(function(){
										$(this).remove();
										
									});
								}
							}
							else
							{
								//DONGND:: remove from page product list
								$('.leo-modal-compare .modal-title').html(content_product_compare_mess_remove);
								$('.leo-modal-compare').modal('show');
								$('.leo-compare-button[data-id-product='+id_product+']').removeClass('added');
								$('.leo-compare-button[data-id-product='+id_product+']').attr('title',buttoncompare_title_add);
								// object_e.find('.leo-compare-bt-loading').hide();
								// object_e.find('.leo-compare-bt-content').show();
							}
						}
						else
						{
							$('.leo-modal-compare .modal-title').html(productcompare_remove_error);
							$('.leo-modal-compare').modal('show');
							
						}
						object_e.find('.leo-compare-bt-loading').hide();
						object_e.find('.leo-compare-bt-content').show();
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
			}
			else
			{
				if (total_product_compare < comparator_max_item)
				{
					//DONGND:: add product to list product compare
					$.ajax({
						type: 'POST',
						headers: {"cache-control": "no-cache"},
						url: productcompare_url+ '?rand=' + new Date().getTime(),
						async: true,
						cache: false,
						data: {
							"ajax": 1,
							"action": "add",
							"id_product": id_product,
							"token": leo_token,
						},
						success: function (result)
						{
							// console.log(result);
							if (result == 1)
							{
								$('.leo-modal-compare .modal-title').html(content_product_compare_mess_add);
								$('.leo-modal-compare').modal('show');
								//Leotheme add: update number product on icon compare
								if ($('.ap-btn-compare .ap-total-compare').length)
								{								
									var old_num_compare = parseInt($('.ap-btn-compare .ap-total-compare').data('compare-total'));
									var new_num_compare = old_num_compare+1;
									$('.ap-btn-compare .ap-total-compare').data('compare-total',new_num_compare);
									$('.ap-btn-compare .ap-total-compare').text(new_num_compare);
								}
								
								compared_products.push(id_product);
								$('.leo-compare-button[data-id-product='+id_product+']').addClass('added');
								$('.leo-compare-button[data-id-product='+id_product+']').attr('title',buttoncompare_title_remove);
							}
							else
							{
								$('.leo-modal-compare .modal-title').html(productcompare_add_error);
								$('.leo-modal-compare').modal('show');
							}
							
							object_e.find('.leo-compare-bt-loading').hide();
							object_e.find('.leo-compare-bt-content').show();
										
						},
						error: function (XMLHttpRequest, textStatus, errorThrown) {
							alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
						}
					});
					
				}
				else
				{
					//DONGND:: list product compare limited
					$('.leo-modal-compare .modal-title').html(content_product_compare_mess_max);
					$('.leo-modal-compare').modal('show');
					object_e.find('.leo-compare-bt-loading').hide();
					object_e.find('.leo-compare-bt-content').show();
				}
			}
		}
		return false;
	})
}

function activeEventModalCompare()
{
	$('.leo-modal-compare').on('hide.bs.modal', function (e) {
		// console.log($('.leo-modal-review-bt').length);
		if ($('.leo-compare-button.active').length)
		{
			// console.log('aaa');
			$('.leo-compare-button.active').removeClass('active');
		}
	})
	$('.leo-modal-compare').on('hidden.bs.modal', function (e) {
		$('body').css('padding-right', '');
	})
	$('.leo-modal-compare').on('shown.bs.modal', function (e) {
		if ($('.quickview.modal').length)
		{			
			$('.quickview.modal').modal('hide');		
		}
	});
}


