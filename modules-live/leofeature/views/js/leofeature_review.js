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
	if ($('.open-review-form').length)
	{
		var id_product = $('.open-review-form').data('id-product');		
		var is_logged = $('.open-review-form').data('is-logged');
		$.ajax({
			type: 'POST',
			headers: {"cache-control": "no-cache"},
			url: prestashop.urls.base_url + 'modules/leofeature/psajax_review.php?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			data: {
				"action": "render-modal-review",
				"id_product": id_product,				
				"is_logged": is_logged,
				"token": leo_token
			},
			success: function (result)
			{
				if(result != '')
				{						
					$('body').append(result);
					activeEventModalReview();
					activeStar();
					$('.open-review-form').fadeIn('fast');
				}
				else
				{
					alert(review_error);
				}
							
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				// alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
		
		$('.open-review-form').click(function(){
			if ($('#criterions_list').length)
			{	
				$('.leo-modal-review').modal('show');
			}
			else
			{
				if ($('.leo-modal-review .modal-body .disable-form-review').length)
				{
					$('.leo-modal-review').modal('show');
				}
				else
				{
					$('.leo-modal-review-bt').remove();
					$('.leo-modal-review .modal-header').remove();
					$('.leo-modal-review .modal-body').empty();
					$('.leo-modal-review .modal-body').append('<div class="form-group disable-form-review has-danger text-center"><label class="form-control-label">'+disable_review_form_txt+'</label></div>');
					$('.leo-modal-review').modal('show');
				}
				
			}
			return false;
		});
	}
	
	$('.read-review').click(function(){
		// if ($('.leo-product-show-review-title').length && $('#leo-product-show-review-content').length)
		if ($('.leo-product-show-review-title').length)
		{
			if ($('.leo-product-show-review-title').hasClass('leofeature-accordion'))
			{
				if ($('.leo-product-show-review-title').hasClass('collapsed'))
				{
					$('.leo-product-show-review-title').trigger('click');
				}
				var timer = setInterval(function() {
				   if ($('#collapseleofeatureproductreview').hasClass('collapse in') || $('#collapsereviews').hasClass('collapse in')) {
					   //run some other function 
						$('html, body').animate({
							scrollTop: $('.leo-product-show-review-title').offset().top
						}, 500);					   
					   clearInterval(timer);
				   }
				}, 200);
			}
			else
			{
				$('.leo-product-show-review-title').trigger('click');
				$('html, body').animate({
					scrollTop: $('.leo-product-show-review-title').offset().top
				}, 500);
			}
		}
		return false;
	});
	
	$('.usefulness_btn').click(function(){
		if (!$(this).hasClass('disabled'))
		{
			$(this).addClass('active');
			$(this).parents('.review_button').find('.usefulness_btn').addClass('disabled');
			var id_product_review = $(this).data('id-product-review');
			var is_usefull = $(this).data('is-usefull');
			var e_parent_button = $(this).parent();
			$.ajax({
				type: 'POST',
				headers: {"cache-control": "no-cache"},
				url: prestashop.urls.base_url + 'modules/leofeature/psajax_review.php?rand=' + new Date().getTime(),
				async: true,
				cache: false,
				data: {
					"action": "add-review-usefull",
					"id_product_review": id_product_review,				
					"is_usefull": is_usefull,
					"token": leo_token
				},
				success: function (result)
				{
					if (result != '')
					{
						e_parent_button.fadeOut(function(){
							e_parent_button.remove();
						});
					}
					else
					{
						alert(review_error);
					}
					
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					// window.location.replace($('.open-review-form').data('product-link'));
				}
			});
		}
	});
	
	$('.report_btn').click(function(){
		if (!$(this).hasClass('disabled'))
		{
			$(this).addClass('disabled');
			var e_button = $(this);
			var id_product_review = $(this).data('id-product-review');
			$.ajax({
				type: 'POST',
				headers: {"cache-control": "no-cache"},
				url: prestashop.urls.base_url + 'modules/leofeature/psajax_review.php?rand=' + new Date().getTime(),
				async: true,
				cache: false,
				data: {
					"action": "add-review-report",
					"id_product_review": id_product_review,
					"token": leo_token
				},
				success: function (result)
				{
					if (result != '')
					{
						e_button.fadeOut(function(){
							e_button.remove();
						});
					}
					else
					{
						alert(review_error);
					}
					
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					// window.location.replace($('.open-review-form').data('product-link'));
				}
			});
		}
		return false;
	});
	
	// activeEventModalReview();
	activeStar();
});

function activeStar()
{
	//DONGND:: add txt cancel rating to translate
	$('input.star').rating({cancel: cancel_rating_txt});
	$('.auto-submit-star').rating({cancel: cancel_rating_txt});
}

function activeEventModalReview()
{
	$('.form-new-review').submit(function(){
		if ($('.new_review_form_content .form-group.leo-has-error').length || $('.leo-fake-button').hasClass('validate-ok'))
		{
			return false;
		}
	});
	$('.leo-modal-review').on('show.bs.modal', function (e) {
		$('.leo-modal-review-bt').click(function(){
			if (!$(this).hasClass('active'))
			{
				$(this).addClass('active');
				$('.leo-modal-review-bt-text').hide();
				$('.leo-modal-review-loading').css({'display':'block'});
				
				$('.new_review_form_content input, .new_review_form_content textarea').each(function(){
					
					if ($(this).val() == '')
					{
						$(this).parent('.form-group').addClass('leo-has-error');
						$(this).attr("required", "");
					}
					else
					{
						$(this).parent('.form-group').removeClass('leo-has-error');
						$(this).removeAttr('required');
					}
				})
				
				if ($('.new_review_form_content .form-group.leo-has-error').length)
				{
					$(this).removeClass('active');
					$('.leo-modal-review-bt-text').show();
					$('.leo-modal-review-loading').hide();
				}
				else
				{
					// console.log('pass');
					// $('.leo-modal-review-bt').remove();
					// console.log($( ".new_review_form_content input, .new_review_form_content textarea" ).serialize());
					$('.leo-fake-button').addClass('validate-ok');
					$.ajax({
						type: 'POST',
						headers: {"cache-control": "no-cache"},
						url: prestashop.urls.base_url + 'modules/leofeature/psajax_review.php?action=add-new-review&token='+leo_token+'&rand=' + new Date().getTime(),
						async: true,
						cache: false,
						data: $( ".new_review_form_content input, .new_review_form_content textarea" ).serialize(),
						success: function (result)
						{
							if (result != '')
							{
								var object_result = $.parseJSON(result);
								// console.log(object_result);
								$('.leo-modal-review-bt').fadeOut('slow', function(){
									$(this).remove();
									
								});
								
								$('.leo-modal-review .modal-body>.row').fadeOut('slow', function(){
									$(this).remove();
									if (object_result.result)
									{
										$('.leo-modal-review .modal-body').append('<div class="form-group has-success"><label class="form-control-label">'+object_result.sucess_mess+'</label></div>');
									}
									else
									{
										// $('.leo-modal-review .modal-body').append('<div class="form-group has-danger text-center"></div>');
										$.each(object_result.errors, function(key, val){
											$('.leo-modal-review .modal-body').append('<div class="form-group has-danger text-center"><label class="form-control-label">'+val+'</label></div>');
										});
									}
								});
							}
							else
							{
								alert(review_error);
							}
							
						},
						error: function (XMLHttpRequest, textStatus, errorThrown) {
							alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
							window.location.replace($('.open-review-form').data('product-link'));
						}
					});
				}
				$('.leo-fake-button').trigger('click');
			}
			
		})
	})
	
	$('.leo-modal-review').on('hide.bs.modal', function (e) {
		// console.log($('.leo-modal-review-bt').length);
		if (!$('.leo-modal-review-bt').length && !$('.leo-modal-review .modal-body .disable-form-review').length)
		{
			// console.log('aaa');
			// window.location.replace($('.open-review-form').data('product-link'));
			location.reload();
			
		}
	})
	
}

