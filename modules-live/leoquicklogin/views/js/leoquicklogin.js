/**
 * 2007-2017 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo Quick Login And Social Login For Prestashop 1.7 
 *
 * DISCLAIMER
 *
 *  @Module Name: Leo Quick Login
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */
$(document).ready(function(){
	//DONGND:: build sliderbar for types
	if($('.leoquicklogin-slidebar').length)
	{
		for (var i=0; i<3; i++)
		{		
			$('.leoquicklogin-slidebar').first().clone().insertAfter('.leoquicklogin-slidebar:last');
			var slidebar_class = '';
			if (leo_push == 1)
			{
				slidebar_class = 'push_';
			}
			switch (i)
			{
				case 0:
					slidebar_class += 'slidebar_right';
					break;
				case 1:
					slidebar_class += 'slidebar_top';
					break;
				case 2:
					slidebar_class += 'slidebar_bottom';
					break;
			}
			
			$('.leoquicklogin-slidebar').last().addClass(slidebar_class);
			
		}
		if (leo_push == 1)
		{
			$('.leoquicklogin-slidebar').first().addClass('push_slidebar_left');
		}
		else
		{
			$('.leoquicklogin-slidebar').first().addClass('slidebar_left');
		}
	}
	//DONGND:: login action
	$('.leo-quicklogin').click(function(){
		if (!$(this).hasClass('leo-dropdown'))
		{
			if (!$(this).hasClass('active'))
			{
				$(this).addClass('active');
				var type = $(this).data('type');
				var layout = $(this).data('layout');
				//DONGND:: disable/enable social login for appagebuilder
				var enable_sociallogin = $(this).data('enable-sociallogin');
				if (type == 'popup')
				{
					if (enable_sociallogin == '' || enable_sociallogin == 'enable')
					{
						$('.leo-quicklogin-modal .lql-social-login').show();
					}
					else
					{
						$('.leo-quicklogin-modal .lql-social-login').hide();
					}
					if (layout != 'both')
					{
						//DONGND:: active tab navigation					
						$('.leo-quicklogin-modal .lql-bt').removeClass('lql-active');
						$('.leo-quicklogin-modal .lql-bt-'+layout).addClass('lql-active');
						$('.leo-quicklogin-modal .lql-action').show();
						
						$('.leo-quicklogin-modal .leo-form').removeClass('leo-form-active full-width').addClass('leo-form-inactive full-width');
						$('.leo-quicklogin-modal .leo-'+layout+'-form').removeClass('leo-form-inactive').addClass('leo-form-active full-width');
					}
					else
					{
						//DONGND:: inactive tab navigation
						$('.leo-quicklogin-modal .lql-action').hide();
						
						$('.leo-quicklogin-modal .leo-form').removeClass('leo-form-inactive full-width').addClass('leo-form-active');
					}
					$('.leo-quicklogin-modal').modal('show');
				}
				if (type == 'slidebar_left' || type == 'slidebar_right' || type == 'slidebar_top' || type == 'slidebar_bottom')
				{
					if (enable_sociallogin == '' || enable_sociallogin == 'enable')
					{
						$('.leoquicklogin-slidebar .lql-social-login').show();
					}
					else
					{
						$('.leoquicklogin-slidebar .lql-social-login').hide();
					}
					var prefix_class = type;
					if (leo_push == 1)
					{
						prefix_class = 'push_'+ prefix_class;
					}
					if (layout != 'both')
					{
						//DONGND:: active tab navigation
						$('.leoquicklogin-slidebar .lql-bt').removeClass('lql-active');
						$('.leoquicklogin-slidebar .lql-bt-'+layout).addClass('lql-active');
						$('.leoquicklogin-slidebar .lql-action').show();
						
						$('.leoquicklogin-slidebar.'+prefix_class+' .leo-form').removeClass('leo-form-active full-width').addClass('leo-form-inactive full-width');
						$('.leoquicklogin-slidebar.'+prefix_class+' .leo-'+layout+'-form').removeClass('leo-form-inactive').addClass('leo-form-active full-width');
					}
					else
					{
						//DONGND:: inactive tab navigation
						$('.leoquicklogin-slidebar .lql-action').hide();
						
						$('.leoquicklogin-slidebar.'+prefix_class+' .leo-form').removeClass('leo-form-inactive full-width').addClass('leo-form-active');
					}
					$('.leoquicklogin-slidebar.'+prefix_class).addClass('active');
					$('.leoquicklogin-mask').addClass('active');
					$('body').addClass('leoquicklogin-active-slidebar');
					//DONGND:: check auto gen rtl
					if (lql_is_gen_rtl && prestashop.language.is_rtl == 1)
					{
						$('body').addClass('lql_is_gen_rtl');
					}
					
					if (leo_push == 1)
					{
						$('body').addClass('leoquicklogin-active-push');
						var push_value;
						var push_type;
						
						if (type == 'slidebar_left' || type == 'slidebar_right')
						{						
							push_type = "X";
							if (type == 'slidebar_left')
							{
								push_value = $('.leoquicklogin-slidebar.'+prefix_class).outerWidth();
							}
							if (type == 'slidebar_right')
							{
								push_value = -$('.leoquicklogin-slidebar.'+prefix_class).outerWidth();
							}
						}
						
						if (type == 'slidebar_top' || type == 'slidebar_bottom')
						{						
							push_type = "Y";
							if (type == 'slidebar_top')
							{
								push_value = $('.leoquicklogin-slidebar.'+prefix_class).outerHeight();
							}
							if (type == 'slidebar_bottom')
							{
								push_value = -$('.leoquicklogin-slidebar.'+prefix_class).outerHeight();
							}
						}
						
						$('body.leoquicklogin-active-push main').css({
							"-moz-transform": "translate"+push_type+"("+push_value+"px)",
							"-webkit-transform": "translate"+push_type+"("+push_value+"px)",
							"-o-transform": "translate"+push_type+"("+push_value+"px)",
							"-ms-transform": "translate"+push_type+"("+push_value+"px)",
							"transform": "translate"+push_type+"("+push_value+"px)",
						})
						
					}
				}
			}
		}
		
	});
	// $('.leo-quicklogin').hover(function(){
		// if (!$(this).hasClass('active'))
		// {
			// var type = $(this).data('type');
			// if (type == 'slidebar_left' || type == 'slidebar_right' || type == 'slidebar_top' || type == 'slidebar_bottom')
			// {
				// var slidebar_class = type;
				// if (leo_push == 1)
				// {
					// slidebar_class = 'push_'+type;
				// }
				// $('.leoquicklogin-slidebar').attr('class', 'leoquicklogin-slidebar '+slidebar_class);
			// }
		// }
	// })
	activeEventModalLeoQuickLogin();
	activeEventSlidebarLeoQuickLogin();
	$('.leo-dropdown-wrapper').click(function (e) {
		e.stopPropagation();
	});
	//DONGND:: display forgotpass form
	// $('.leoquicklogin-forgotpass').hover(function(){
		// var parent_obj = $(this).parents('.leo-quicklogin-form');
		// parent_obj.css({'height': 'auto'});
	// });
	$('.leoquicklogin-forgotpass').click(function(){
		var parent_obj = $(this).parents('.leo-login-form');
		if (!$(this).hasClass('active'))
		{
			$(this).addClass('active');
			//parent_obj.find('.leoquicklogin-callregister').addClass('disabled');
			parent_obj.find('.leo-resetpass-form').slideDown('fast', function(){
				// parent_obj.find('.leoquicklogin-callregister').removeClass('disabled');
			});
		}
		else
		{
			$(this).removeClass('active');
			//parent_obj.find('.leoquicklogin-callregister').addClass('disabled');
			parent_obj.find('.leo-resetpass-form').slideUp('fast',function(){
				// parent_obj.find('.leoquicklogin-callregister').removeClass('disabled');
			});
		}
		
		return false;
	});
	
	
	//DONGND:: display login form
	// $('.leoquicklogin-calllogin, .leoquicklogin-callregister').hover(function(){
		// if (!$(this).hasClass('disabled'))
		// {
			
			// var parent_obj = $(this).parents('.leo-quicklogin-form');
			
			// parent_obj.width(parent_obj.find('.leo-form-active').outerWidth()).height(parent_obj.find('.leo-form-active').outerHeight());
			// parent_obj.addClass('change');
		// }
	// }, function(){
		// if ($('.leo-quicklogin-form.change').length)
		// {
			// setTimeout(function(){
				// var parent_obj = $(this).parents('.leo-quicklogin-form');
				// $('.leo-quicklogin-form.change').removeClass('change').css({'width':'100%','height': '100%'});
			// },300);
		// }
			
	// });
	$('.lql-calllogin-action').click(function(){	
		callLoginForm($(this));
		return false;
	})
	
	//DONGND:: display register form
	$('.lql-callregister-action').click(function(){
		callRegisterForm($(this));
		return false;
	});
	
	//DONGND:: button tab (only form)
	$('.lql-bt').click(function(){
		if (!$(this).hasClass('active'))
		{		
			if ($(this).hasClass('lql-bt-login'))
			{
				callLoginForm($(this));
			}
			if ($(this).hasClass('lql-bt-register'))
			{
				callRegisterForm($(this));
			}
		}
	});
	
	$('.leo-resetpass-form-content').submit(function(){
		// if ($(this).find('.form-group.leo-has-error').length)
		if ($(this).find('.leoquicklogin-reset-pass-bt').hasClass('validate-ok') || $(this).find('.leo-has-error').length)
		{
			return false;
		}
	});
	
	//DONGND:: button send email reset password
	$('.leoquicklogin-reset-pass-bt').click(function(){
		if (!$(this).hasClass('active') && !$(this).hasClass('success'))
		{		
			var object_e = $(this);
			var parent_obj = object_e.parents('.leo-resetpass-form-content');
			parent_obj.find('.lql-form-mesg.has-danger').fadeOut();
			parent_obj.find('.lql-form-mesg.has-success').fadeOut();
			// var check_submit = true;
			object_e.addClass('active');
			object_e.find('.lql-bt-txt').hide();
			object_e.find('.leoquicklogin-loading').css({'display':'block'});
			
			parent_obj.find('input').each(function(){
				// console.log($(this));
				if (!$(this).val())
				{
					$(this).parent('.form-group').addClass('leo-has-error');
					object_e.removeClass('active');
					object_e.find('.lql-bt-txt').show();
					object_e.find('.leoquicklogin-loading').hide();
					return false;
				}
				else
				{
					if (!validateEmail($(this).val()))
					{
						$(this).parent('.form-group').addClass('leo-has-error');
						object_e.removeClass('active');
						object_e.find('.lql-bt-txt').show();
						object_e.find('.leoquicklogin-loading').hide();
						return false;
					}
					else
					{
						$(this).parent('.form-group').removeClass('leo-has-error');
					}
				}
			});
			
			
			// console.log('pass');
			// $('.leo-modal-review-bt').remove();
			// console.log($( ".new_review_form_content input, .new_review_form_content textarea" ).serialize());
			if (!parent_obj.find('.leo-has-error').length)
			{
				parent_obj.find('.leoquicklogin-reset-pass-bt').addClass('validate-ok');
				var lql_email_reset = $.trim(parent_obj.find('.lql-email-reset').val());
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: lql_ajax_url,
					async: true,
					cache: false,
					data: {
						"ajax": 1,
						"action": "reset-pass",
						"lql-email-reset": lql_email_reset,
					},
					success: function (result)
					{
						parent_obj.find('.lql-form-mesg.has-danger').empty();
						parent_obj.find('.lql-form-mesg.has-success').empty();
						var object_result = $.parseJSON(result);
						// console.log(object_result);
						object_e.removeClass('active');					
						object_e.find('.leoquicklogin-loading').hide();
						if (object_result.errors.length)
						{
							$.each(object_result.errors,function(key, val){
								parent_obj.find('.lql-form-mesg.has-danger').append('<label class="form-control-label">'+val+'</label>')
							})
							parent_obj.find('.lql-form-mesg.has-danger').fadeIn();
							object_e.find('.lql-bt-txt').show();
						}
						else
						{
							parent_obj.find('.lql-form-content-element').slideUp(function(){
								$(this).remove();
							});
							parent_obj.find('.lql-form-mesg.has-success').append('<label class="form-control-label">'+object_result.success[0]+'<strong>'+lql_email_reset+'</strong></label>');
							parent_obj.find('.lql-form-mesg.has-success').fadeIn();
							object_e.find('.leoquicklogin-success-icon').fadeIn();
							object_e.addClass('success');
						}											
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
			}		
		}
		
	})
	
	$('.leo-login-form-content').submit(function(){
		// if ($(this).find('.form-group.leo-has-error').length)
		if ($(this).find('.lql-login-bt').hasClass('validate-ok') || $(this).find('.leo-has-error').length)
		{
			return false;
		}
	});
	
	//DONGND:: button login
	$('.lql-login-bt').click(function(){
		if (!$(this).hasClass('active') && !$(this).hasClass('success'))
		{		
			var object_e = $(this);
			var parent_obj = object_e.parents('.leo-login-form-content');
			parent_obj.find('.lql-form-mesg.has-danger').fadeOut();
			parent_obj.find('.lql-form-mesg.has-success').fadeOut();
			// var check_submit = true;
			object_e.addClass('active');
			object_e.find('.lql-bt-txt').hide();
			object_e.find('.leoquicklogin-loading').css({'display':'block'});
			
			parent_obj.find('input').each(function(){
				// console.log($(this));
				if (!$(this).val())
				{
					$(this).parent('.form-group').addClass('leo-has-error');					
				}
				else
				{
					if ($(this).hasClass('lql-email-login') && !validateEmail($(this).val()))
					{
						$(this).parent('.form-group').addClass('leo-has-error');					
					}
					else
					{
						$(this).parent('.form-group').removeClass('leo-has-error');
					}
				}
			});
			
			
			// console.log('pass');
			// $('.leo-modal-review-bt').remove();
			// console.log($( ".new_review_form_content input, .new_review_form_content textarea" ).serialize());
			if (!parent_obj.find('.leo-has-error').length)
			{
				parent_obj.find('.lql-login-bt').addClass('validate-ok');
				var lql_email_login = $.trim(parent_obj.find('.lql-email-login').val());
				var lql_pass_login = $.trim(parent_obj.find('.lql-pass-login').val());
				var data_send = {};
				data_send.ajax = 1;
				data_send.action = "customer-login";
				data_send.lql_email_login = lql_email_login;
				data_send.lql_pass_login = lql_pass_login;
				if (parent_obj.find('.lql-rememberme').length)
				{
					if (parent_obj.find('.lql-rememberme').is( ":checked" ))
					{
						var lql_remember_login = 1;
					}
					else
					{
						var lql_remember_login = 0;
					}
					
					data_send.lql_remember_login = lql_remember_login;
				}
				// console.log(data_send);
				// return false;
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: lql_ajax_url,
					async: true,
					cache: false,
					data: data_send,
					success: function (result)
					{
						parent_obj.find('.lql-form-mesg.has-danger').empty();
						parent_obj.find('.lql-form-mesg.has-success').empty();
						var object_result = $.parseJSON(result);
						// console.log(object_result);
						object_e.removeClass('active');					
						object_e.find('.leoquicklogin-loading').hide();
						if (object_result.errors.length)
						{
							$.each(object_result.errors,function(key, val){
								parent_obj.find('.lql-form-mesg.has-danger').append('<label class="form-control-label">'+val+'</label>')
							})
							parent_obj.find('.lql-form-mesg.has-danger').fadeIn();
							object_e.find('.lql-bt-txt').show();
						}
						else
						{
							parent_obj.find('.lql-form-content-element').slideUp(function(){
								$(this).remove();
							});
							parent_obj.find('.lql-form-mesg.has-success').append('<label class="form-control-label"><strong>'+object_result.success[0]+'</strong></label>');
							parent_obj.find('.lql-form-mesg.has-success').fadeIn();
							object_e.find('.leoquicklogin-success-icon').show();
							object_e.addClass('success');
							if (lql_redirect)
							{
								window.location.replace(lql_myaccount_url);
							}
							else
							{
								location.reload();
							}
						}											
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
				
			}
			else
			{
				object_e.removeClass('active');
				object_e.find('.lql-bt-txt').show();
				object_e.find('.leoquicklogin-loading').hide();
				// return false;	
			}
		}
		
	})
	
	$('.leo-register-form-content').submit(function(){
		// if ($(this).find('.form-group.leo-has-error').length)
		if ($(this).find('.lql-register-bt').hasClass('validate-ok') || $(this).find('.leo-has-error').length)
		{
			return false;
		}
	});
	
	//DONGND:: button login
	$('.lql-register-bt').click(function(){
		if (!$(this).hasClass('active') && !$(this).hasClass('success'))
		{		
			var object_e = $(this);
			var parent_obj = object_e.parents('.leo-register-form-content');
			parent_obj.find('.lql-form-mesg.has-danger').fadeOut();
			parent_obj.find('.lql-form-mesg.has-success').fadeOut();
			// var check_submit = true;
			object_e.addClass('active');
			object_e.find('.lql-bt-txt').hide();
			object_e.find('.leoquicklogin-loading').css({'display':'block'});
			
			parent_obj.find('input').each(function(){
				// console.log($(this));
				// console.log(!lql_register_check);
				// console.log(!$(this).val());
				if (!$(this).val())
				{
					$(this).parent('.form-group').addClass('leo-has-error');					
				}
				else
				{
					//check box
					if ($(this).hasClass('lql-register-check') && !$(this).is(':checked'))
					{
						$(this).parent('.form-group').addClass('leo-has-error');
					}
					else
					{
						$(this).parent('.form-group').removeClass('leo-has-error');
					}

					if ($(this).hasClass('lql-register-email'))
					{
						if (!validateEmail($(this).val()))
						{
							$(this).parent('.form-group').addClass('leo-has-error');					
						}
						else
						{
							$(this).parent('.form-group').removeClass('leo-has-error');
						}
					}
					
				}
			});
							
			if (!parent_obj.find('.leo-has-error').length)
			{
				parent_obj.find('.lql-register-bt').addClass('validate-ok');
				var lql_register_firstname = $.trim(parent_obj.find('.lql-register-firstname').val());
				var lql_register_lastname = $.trim(parent_obj.find('.lql-register-lastname').val());
				var lql_register_email = $.trim(parent_obj.find('.lql-register-email').val());
				var lql_register_pass = $.trim(parent_obj.find('.lql-register-pass').val());
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: lql_ajax_url,
					async: true,
					cache: false,
					data: {
						"ajax": 1,
						"action": "create-account",
						"lql-register-firstname": lql_register_firstname,
						"lql-register-lastname": lql_register_lastname,
						"lql-register-email": lql_register_email,
						"lql-register-pass": lql_register_pass,
					},
					success: function (result)
					{
						parent_obj.find('.lql-form-mesg.has-danger').empty();
						parent_obj.find('.lql-form-mesg.has-success').empty();
						var object_result = $.parseJSON(result);
						// console.log(object_result);
						object_e.removeClass('active');					
						object_e.find('.leoquicklogin-loading').hide();
						if (object_result.errors.length)
						{
							$.each(object_result.errors,function(key, val){
								parent_obj.find('.lql-form-mesg.has-danger').append('<label class="form-control-label">'+val+'</label><br/>')
							})
							parent_obj.find('.lql-form-mesg.has-danger').fadeIn();
							object_e.find('.lql-bt-txt').show();
						}
						else
						{
							parent_obj.find('.lql-form-content-element').slideUp(function(){
								$(this).remove();
							});
							parent_obj.find('.lql-form-mesg.has-success').append('<label class="form-control-label"><strong>'+object_result.success[0]+'</strong></label>');
							parent_obj.find('.lql-form-mesg.has-success').fadeIn();
							object_e.find('.leoquicklogin-success-icon').show();
							object_e.addClass('success');
							if (lql_redirect)
							{
								window.location.replace(lql_myaccount_url);
							}
							else
							{
								location.reload();
							}
						}											
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
				
			}
			else
			{
				object_e.removeClass('active');
				object_e.find('.lql-bt-txt').show();
				object_e.find('.leoquicklogin-loading').hide();
				// return false;	
			}
		}
		
	})
	if (typeof google_client_id != 'undefined' && google_client_id.length)
	{
		// console.log(gapi);
		// console.log(gapi.client.language);
		gapi.load('auth2', function(){
		  // Retrieve the singleton for the GoogleAuth library and set up the client.
		  // console.log(google_client_id);
		  auth2 = gapi.auth2.init({
			client_id: google_client_id,
			cookiepolicy: 'single_host_origin',
			// Request scopes in addition to 'profile' and 'email'
			scope: 'profile email'
		  });
		  // console.log(auth2);
		  attachGoogleSignin();
		});
		// window.___gcfg = {
			// lang: 'zh-CN',
			// parsetags: 'onload'
		  // };
	}
	
	//DONGND:: twitter login
	$('.twitter-login-bt').click(function(){
		window.open(lql_module_dir+'twitter.php?request=twitter&lang='+prestashop.language.iso_code, '_blank', 'toolbar=yes, scrollbars=yes, resizable=yes, top=100, left=300, width=700, height=600');
	});
});

//DONGND:: 
function callLoginForm($element)
{
	var parent_obj = $element.parents('.leo-quicklogin-form');
	if ($element.hasClass('lql-callreset-action') && !parent_obj.find('.leoquicklogin-forgotpass').hasClass('active'))
	{
		parent_obj.find('.leoquicklogin-forgotpass').trigger('click');
	}
	//parent_obj.removeClass('update');
	//parent_obj.width(parent_obj.outerWidth()).height(parent_obj.outerHeight());
	parent_obj.find('.leo-login-form').removeClass('leo-form-inactive').addClass('leo-form-active');
	parent_obj.find('.leo-register-form').removeClass('leo-form-active').addClass('leo-form-inactive');
	//parent_obj.addClass('update');
	// parent_obj.width(parent_obj.find('.leo-form-active').outerWidth()).height(parent_obj.find('.leo-form-active').outerHeight());
	// setTimeout(function(){
		
		// parent_obj.css({'width':'100%','height': 'auto'});
	// },300);
	parent_obj.find('.lql-bt.lql-active').removeClass('lql-active');
	parent_obj.find('.lql-bt-login').addClass('lql-active');
}

//DONGND::
function callRegisterForm($element)
{
	var parent_obj = $element.parents('.leo-quicklogin-form');
		
	//parent_obj.removeClass('update');
	//parent_obj.width(parent_obj.outerWidth()).height(parent_obj.outerHeight());
	parent_obj.find('.leo-register-form').removeClass('leo-form-inactive').addClass('leo-form-active');
	parent_obj.find('.leo-login-form').removeClass('leo-form-active').addClass('leo-form-inactive');
	//parent_obj.addClass('update');
	// parent_obj.width(parent_obj.find('.leo-form-active').outerWidth()).height(parent_obj.find('.leo-form-active').outerHeight());
	// setTimeout(function(){
		
		// parent_obj.css({'width':'100%','height': 'auto'});
	// },300);
	parent_obj.find('.lql-bt.lql-active').removeClass('lql-active');
	parent_obj.find('.lql-bt-register').addClass('lql-active');
}

//DONGND:: event for slidebar
function activeEventSlidebarLeoQuickLogin()
{
	$('.leoquicklogin-mask, .leoquicklogin-slidebar-close').click(function(){
		$('.leoquicklogin-mask.active').removeClass('active');
		$('.leo-quicklogin.active').removeClass('active');
		$('body.leoquicklogin-active-push main').css({
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
			$('body').removeClass('leoquicklogin-active-slidebar leoquicklogin-active-push');
			//DONGND:: check auto gen rtl
			if (lql_is_gen_rtl && prestashop.language.is_rtl == 1)
			{
				$('body').removeClass('lql_is_gen_rtl');
			}
		},300);
			
		$('.leoquicklogin-slidebar.active').removeClass('active');
	});
}

//DONGND:: event for modal
function activeEventModalLeoQuickLogin()
{
	$('.leo-quicklogin-modal').on('hide.bs.modal', function (e) {
		// console.log('test');
		$('.leo-quicklogin.active').removeClass('active');
		
	  // do something...
	});
	
	//DONGND:: modal social login
	// $('.lql-social-modal').on('hide.bs.modal', function (e) {
		// $('.lql-social-modal-mesg').removeClass('active');
	// });
	
}

function validateEmail(email) {
  // var regex = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
  // return regex.test(email);
	var reg = /^[a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z\p{L}0-9]+$/i;
	return reg.test(email);
}

//DONGND:: facebook login
// function statusChangeCallback(response) {
		
	// if (response.status === 'connected') {
	 
	  // processFbAPI();
	// } else {
	 
	  // console.log('Please log ' +
			// 'into this app.');
	// }
// }
function doFbLogin() {  
	FB.login(function(response) {
		if (response.status === 'connected') {
		  // Logged into your app and Facebook.
		  // console.log(response);
		  processFbAPI();
		} else {
		  // The person is not logged into your app or we are unable to tell.
		  console.log('Please log ' +
				'into this app.');
		}
	} , {scope: 'public_profile,email'}); 
}
// function checkLoginState() {
	// FB.getLoginStatus(function(response) {
	  // statusChangeCallback(response);
	// });
// }

function processFbAPI() {
	$('.leo-quicklogin-modal').modal('hide');
	$('.leoquicklogin-mask').trigger('click');
	
	setTimeout(function(){
			
		FB.api('/me?fields=email,birthday,first_name,last_name,name,gender', function(response) {
		
			if (response.email)
			{
				// console.log(response);
				$('.lql-social-modal .lql-social-modal-mesg').removeClass('active');
				$('.lql-social-modal .lql-social-modal-mesg.lql-social-loading').addClass('active');
				$('.lql-social-modal').modal('show');
				// console.log('Successful login for: ' + response.name);
				// console.log('Thanks for logging in, ' + response.name + '!');
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: lql_ajax_url,
					async: true,
					cache: false,
					data: {
						"ajax": 1,
						"action": "social-login",
						"email": response.email,
						"first_name": response.first_name,
						"last_name": response.last_name,
					},
					success: function (result)
					{
						$('.lql-social-modal .lql-social-modal-mesg').removeClass('active');
						var object_result = $.parseJSON(result);
											
						if (object_result.errors.length)
						{						
							$('.lql-social-modal .lql-social-modal-mesg.error-login').addClass('active');
						}
						else
						{						
							$('.lql-social-modal .lql-social-modal-mesg.success').addClass('active');
							if (lql_redirect)
							{
								window.location.replace(lql_myaccount_url);
							}
							else
							{
								location.reload();
							}
						}											
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
				
			}
			else
			{
				$('.lql-social-modal .lql-social-modal-mesg').removeClass('active');
				$('.lql-social-modal .lql-social-modal-mesg.error-email').addClass('active');
				$('.lql-social-modal').modal('show');
				// console.log('Fail');
				FB.api('/me/permissions', 'delete', function(response) {
					// console.log(response); 
				});
			}
		});
	
	},300);
}

//DONGND:: google login
function attachGoogleSignin() {

	$('.google-login-bt').each(function(){
		auth2.attachClickHandler(this, {},
			function(googleUser) {
				// console.log(googleUser);
				var profile = googleUser.getBasicProfile();
				$('.leo-quicklogin-modal').modal('hide');
				$('.leoquicklogin-mask').trigger('click');
				
				setTimeout(function(){  
					if (profile.getEmail())
					{
							// console.log(response);
							$('.lql-social-modal .lql-social-modal-mesg').removeClass('active');
							$('.lql-social-modal .lql-social-modal-mesg.lql-social-loading').addClass('active');
							$('.lql-social-modal').modal('show');
							// console.log('Successful login for: ' + response.name);
							// console.log('Thanks for logging in, ' + response.name + '!');
							$.ajax({
								type: 'POST',
								headers: {"cache-control": "no-cache"},
								url: lql_ajax_url,
								async: true,
								cache: false,
								data: {
									"ajax": 1,
									"action": "social-login",
									"email": profile.getEmail(),
									"first_name": profile.getGivenName(),
									"last_name": profile.getFamilyName(),
								},
								success: function (result)
								{
									$('.lql-social-modal .lql-social-modal-mesg').removeClass('active');
									var object_result = $.parseJSON(result);
														
									if (object_result.errors.length)
									{						
										$('.lql-social-modal .lql-social-modal-mesg.error-login').addClass('active');
									}
									else
									{						
										$('.lql-social-modal .lql-social-modal-mesg.success').addClass('active');
										if (lql_redirect)
										{
											window.location.replace(lql_myaccount_url);
										}
										else
										{
											location.reload();
										}
									}											
								},
								error: function (XMLHttpRequest, textStatus, errorThrown) {
									console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
								}
							});
							
					}
					else
					{
						$('.lql-social-modal .lql-social-modal-mesg').removeClass('active');
						$('.lql-social-modal .lql-social-modal-mesg.error-email').addClass('active');
						$('.lql-social-modal').modal('show');
						// console.log('Fail');
						auth2.disconnect();
					}
				},300);
			}, function(error) {
			  console.log(error);
			}
		
		);
		
	})
    
}

//DONGND:: twitter login
function twitterLogin(first_name, last_name, email) {
	$('.leo-quicklogin-modal').modal('hide');
	$('.leoquicklogin-mask').trigger('click');
	
	setTimeout(function(){  
		if (email.length)
		{			
			// console.log(response);
			//DONGND:: when can't get last name of user
			if (last_name == '')
			{
				last_name = 'Mr/Ms';
			}
			$('.lql-social-modal .lql-social-modal-mesg').removeClass('active');
			$('.lql-social-modal .lql-social-modal-mesg.lql-social-loading').addClass('active');
			$('.lql-social-modal').modal('show');
			// console.log('Successful login for: ' + response.name);
			// console.log('Thanks for logging in, ' + response.name + '!');
			$.ajax({
				type: 'POST',
				headers: {"cache-control": "no-cache"},
				url: lql_ajax_url,
				async: true,
				cache: false,
				data: {
					"ajax": 1,
					"action": "social-login",
					"email": email,
					"first_name": first_name,
					"last_name": last_name,
				},
				success: function (result)
				{
					$('.lql-social-modal .lql-social-modal-mesg').removeClass('active');
					var object_result = $.parseJSON(result);
										
					if (object_result.errors.length)
					{						
						$('.lql-social-modal .lql-social-modal-mesg.error-login').addClass('active');
					}
					else
					{						
						$('.lql-social-modal .lql-social-modal-mesg.success').addClass('active');
						if (lql_redirect)
						{
							window.location.replace(lql_myaccount_url);
						}
						else
						{
							location.reload();
						}
					}											
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
				}
			});
				
		}
		else
		{
			$('.lql-social-modal .lql-social-modal-mesg').removeClass('active');
			$('.lql-social-modal .lql-social-modal-mesg.error-email').addClass('active');
			$('.lql-social-modal').modal('show');
			// console.log('Fail');
			
		}
	},300);
}


