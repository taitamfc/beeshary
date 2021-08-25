/**
 * @copyright Commercial License By LeoTheme.Com 
 * @email leotheme.com
 * @visit http://www.leotheme.com
 */
$(document).ready(function() {
	
	$.each(list_menu,function(index,value){
		// console.log(value.type);
		if (value.type == "horizontal")
		{
			var megamenu_element = $('.cavas_menu[data-megamenu-id='+value.id+']');
			// console.log(megamenu_element);
			//type horizontal menu
			//check active link
			if($("body").attr("id")=="index") isHomeMenu = 1;
			megamenu_element.find(".megamenu > li > a").each(function() {
				menuURL = $(this).attr("href").replace("https://","").replace("http://","").replace("www.","").replace( /#\w*/, "" );
				if( (currentURL == menuURL) || (currentURL.replace(current_link,"") == menuURL) || isHomeMenu){
					$(this).parent().addClass("active");
					return false;
				}
			});
			
			//check target
			// console.log($(window).width());
			if($(window).width() <= 767){
				set_target_blank(false, megamenu_element);	// set cavas NO
			}else{
				set_target_blank(true, megamenu_element);		// set cavas Yes
			}
			
			// console.log('test');
			// console.log(show_cavas);
			
			//off canvas menu
			if(value.show_cavas == 1)
			{
				// console.log('test');
				$.fn.OffCavasmenu = function(opts) {
					// default configuration
					var config = $.extend({}, {
						opt1: null,
						text_warning_select: text_warning_select_txt,
						text_confirm_remove: text_confirm_remove_txt,
						JSON: null
					}, opts);
					// main function
					// initialize every element
					this.each(function() {
						// console.log('test');
						var $btn = megamenu_element.find('.navbar-toggler');
						// console.log($btn);
						var $nav = null;
						if (!$btn.length)
							return;
						var $nav = $("<section class='off-canvas-nav-megamenu' data-megamenu-id="+value.id+"><nav class='offcanvas-mainnav' ><div class='off-canvas-button-megamenu'><span class='off-canvas-nav'></span>"+close_bt_txt+"</div></nav></section>");
						// console.log($($btn.data('target')).find('.megamenu'));
						var $menucontent = $($btn.data('target')).find('.megamenu').clone();
						$("body").append($nav);
						
						$('body main').append("<div class='megamenu-overlay' data-megamenu-id="+value.id+"></div>");
						
						$(".megamenu-overlay[data-megamenu-id="+value.id+"]").click(function(){
							$btn.trigger('click');
						});
						// console.log('test');
						$(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"] .offcanvas-mainnav").append($menucontent);
						$("html").addClass ("off-canvas");
						$(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"]").find(".off-canvas-button-megamenu").click( function(){							
							off_canvas_active();
						} );
						
						if($btn.is(':visible')) {
							$("body").removeClass("off-canvas-active").addClass("off-canvas-inactive");
						}
						
						$btn.click(function(){
							// if (!$(".off-canvas-megamenu[data-megamenu-id="+value.id+"]").hasClass('off-canvas-nav-megamenu'))
							// {
								// $(".off-canvas-megamenu[data-megamenu-id="+value.id+"]").addClass('off-canvas-nav-megamenu');
							// }
							// console.log($(window).height());
							// console.log($('.offcanvas-mainnav').height());
							// if($(window).height() > $(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"] .offcanvas-mainnav").height())
							// {
								// $(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"] .offcanvas-mainnav").height($(window).height());
							// }
							// else
							// {
								// $(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"] .offcanvas-mainnav").height('auto');
							// }
							off_canvas_active();
							$('.off-canvas-nav-megamenu').removeClass('active');
							if (!$(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"]").hasClass('active') && $('body').hasClass('off-canvas-active'))
							{
								$(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"]").addClass('active');
								auto_height(value.id);
							}
							
							// $(".off-canvas-megamenu[data-megamenu-id!="+value.id+"]").removeClass('off-canvas-nav-megamenu');
							
						});
							
							
					});
					return this;
				}
				// console.log(megamenu_element);
				megamenu_element.OffCavasmenu();
				megamenu_element.find('.navbar-toggler').click(function() {
					$('body,html').animate({
						scrollTop: 0
					}, 0);
					return false;
				});
				
				$(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"]").find('.offcanvas-mainnav .caret').click(function(){
					
					if($(this).parent('li').hasClass('open-sub'))
					{
						$(this).parent('li').find('.dropdown-menu').first().slideUp('fast',function (){
							// console.log($(window).height());
							// console.log($(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"] .offcanvas-mainnav").height());
							// $(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"] .offcanvas-mainnav").height('auto');
							// if($(window).height() > $(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"] .offcanvas-mainnav").height())
							// {
								// $(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"] .offcanvas-mainnav").height($(window).height());
							// }
							auto_height(value.id);
						});
						$(this).parent('li').removeClass('open-sub');
					}
					else
					{
						// $('.offcanvas-mainnav li.open-sub').find('.dropdown-menu').first().slideUp('fast');
						$(this).parent('li').siblings('.open-sub').find('.dropdown-menu').first().slideUp('fast');
						$(this).parent('li').siblings().removeClass('open-sub');
						$(this).parent('li').find('.dropdown-menu').first().slideDown('fast',function (){
							// console.log($(window).height());
							// console.log($(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"] .offcanvas-mainnav").height());
							// $(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"] .offcanvas-mainnav").height('auto');
							// if($(window).height() > $(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"] .offcanvas-mainnav").height())
							// {
								// $(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"] .offcanvas-mainnav").height($(window).height());
							// }
							auto_height(value.id);
						});
						$(this).parent('li').addClass('open-sub');
					}
					
					
				});
				
				$(window).resize(function() {
					
					// console.log($(window).width());
					// if( $(window).width() > 523 ){
					if( $(window).width() > 1024 ){
						$("body").removeClass("off-canvas-active").addClass("off-canvas-inactive");
					}
					else
					{						
						auto_height(value.id);
					}
				});
			}
			else
			{
				var $bt = megamenu_element.find('.navbar-toggler');
				var $menu = megamenu_element.find('.leo-top-menu');
				//add class for menu element when click button to show menu at mobile, tablet
				$bt.click(function(){				
					if ($menu.hasClass('in'))
					{
						megamenu_element.removeClass('active');
					}
					else
					{
						if (!megamenu_element.hasClass('active'))
						{
							megamenu_element.addClass('active');
						}
					}
				});
				megamenu_element.find('.leo-top-menu .dropdown-toggle').removeAttr("disabled");
				megamenu_element.find(".dropdown-toggle").click(function() {
					if($(window).width() <= 767){
						if($(this).parent("li").find("div:first").hasClass("level2"))
							return false;
						else
							return true;
					}
				});
				megamenu_element.find(".leo-top-menu li a").each(function(){
					// console.log($(this));
					// console.log((this).hasAttribute('data-toggle'));
					if((this).hasAttribute('data-toggle')){
						
						$(this).removeAttr('data-toggle');
						
					}
				});
				megamenu_element.find(".leo-top-menu li a.dropdown-toggle").click(function(){			
					if(!$(this).parent().hasClass('open') && this.href && this.href != '#'){
						window.location.href = this.href;
					}
				})
				// console.log('aaa');
				megamenu_element.find(".leo-top-menu .caret").click(function(){
					// console.log('test');
					if($(this).parent('li').hasClass('open-sub'))
					{
						$(this).parent('li').find('.dropdown-menu').first().slideUp('fast', function(){
							auto_height_off(megamenu_element);
						});
						$(this).parent('li').removeClass('open-sub');
					}
					else
					{
						// $('.offcanvas-mainnav li.open-sub').find('.dropdown-menu').first().slideUp('fast');
						$(this).parent('li').siblings('.open-sub').find('.dropdown-menu').first().slideUp('fast');
						$(this).parent('li').siblings().removeClass('open-sub');
						$(this).parent('li').find('.dropdown-menu').first().slideDown('fast', function(){
							auto_height_off(megamenu_element);
						});
						$(this).parent('li').addClass('open-sub');
					}
				});
				
				if ($(document).width() >543)
				{				
					megamenu_element.find('.leo-top-menu .dropdown-menu').css('display', '');						
				}
				auto_height_off(megamenu_element);
				$(window).resize(function(){
					auto_height_off(megamenu_element);
					if ($(document).width() >543)
					{
						megamenu_element.find('.leo-top-menu .dropdown').removeClass('open-sub');
						megamenu_element.find('.leo-top-menu .dropdown-submenu').removeClass('open-sub');
						megamenu_element.find('.leo-top-menu .dropdown-menu').css('display', '');	
					}
				});
			}
		}
		else
		{
			//type vertical menu
			var megamenu_element = $('.leo-verticalmenu[data-megamenu-id='+value.id+']');
			megamenu_element.find('.verticalmenu .dropdown-toggle').removeAttr("disabled");
			megamenu_element.find(".verticalmenu .dropdown-toggle").click(function() {
				if($(window).width() <= 767){
					if($(this).parent("li").find("div:first").hasClass("level2"))
						return false;
					else
						return true;
				}		
			});
			// megamenu_element.find('.verticalmenu .dropdown-toggle').prop('disabled', true);
			// megamenu_element.find('.verticalmenu .dropdown-toggle').data('toggle', '');
			megamenu_element.find('.verticalmenu .dropdown-toggle').removeAttr('data-toggle');
			megamenu_element.find(".verticalmenu .caret").click(function(){
				if($(this).parents('.verticalmenu').hasClass('active-button'))
				{
					// console.log('test');
					var $parent  = $(this).parent('li');
					// console.log($parent.hasClass('open-sub'));
					// $parent.toggleClass('open-sub');
					if($parent.hasClass('open-sub'))
					{
						// console.log('test');
						$parent.find('.dropdown-menu').first().slideUp('fast',function(){
							$parent.removeClass('open-sub');
						});
						
					}
					else
					{
						
						if($parent.siblings('.open-sub').length > 0)
						{
							$parent.siblings('.open-sub').find('.dropdown-menu').first().slideUp('fast',function(){								
								$parent.siblings('.open-sub').removeClass('open-sub');								
							});
							$parent.find('.dropdown-menu').first().slideDown('fast',function(){
								$parent.addClass('open-sub');
							});
						}
						else
						{
							$parent.find('.dropdown-menu').first().slideDown('fast',function(){
								$parent.addClass('open-sub');
							});
						}
						
						
						
					}
					return false;
				}
				
			});
			if ($(window).width() >1024)
			{
				megamenu_element.find('.verticalmenu').addClass('active-hover');
				megamenu_element.find('.verticalmenu').removeClass('active-button');
				megamenu_element.find('.verticalmenu .dropdown-menu').css('display', '');
				megamenu_element.find('.leo-verticalmenu').removeClass('active');
			
			}
			else
			{
				megamenu_element.find('.verticalmenu').removeClass('active-hover');
				megamenu_element.find('.verticalmenu').addClass('active-button');
			}
			$(window).resize(function(){
				if ($(window).width() >1024)
				{
					megamenu_element.find('.verticalmenu .dropdown').removeClass('open-sub');
					megamenu_element.find('.verticalmenu .dropdown-submenu').removeClass('open-sub');
					megamenu_element.find('.verticalmenu').addClass('active-hover');
					megamenu_element.find('.verticalmenu').removeClass('active-button');
					megamenu_element.find('.verticalmenu .dropdown-menu').css('display', '');
					megamenu_element.removeClass('active');
				}else{
					megamenu_element.find('.verticalmenu').removeClass('active-hover');
					megamenu_element.find('.verticalmenu').addClass('active-button');
				}
			});
			scrollSliderBarMenu(megamenu_element);
		}
		
		//js for tab html
		if ( typeof value.list_tab !== 'undefined' && value.list_tab.length > 0)
		{		
			$.each(value.list_tab,function(key,val){				
				megamenu_element.find('#tabhtml'+val+' .nav a').click(function (e) {
					e.preventDefault();
					$(this).tab('show');
				})
				
				//fix for widget tab of off canvas menu on mobile
				$(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"]").find('#tabhtml'+val+' .nav a').click(function (e) {
					e.preventDefault();					
					if (!$(this).hasClass('active'))
					{					
						var wrapper_tab = $(this).closest('.panel-group');
						var tab_href = $(this).attr('href');
						wrapper_tab.find('.nav-link').removeClass('active');
						wrapper_tab.find('.nav-item').removeClass('active');
						wrapper_tab.find('.tab-pane').removeClass('active');
						$(this).addClass('active');
						$(this).parents('.nav-item').addClass('active');
						wrapper_tab.find(tab_href).addClass('active');
					}
				})
			});		
		}
		
		//js for widget image gallery category
		if ( typeof value.level !== 'undefined' && typeof value.limit !== 'undefined')
		{
			megamenu_element.find('.widget-category_image ul.level0').each(function(){				
				$(this).find('ul').removeClass('dropdown-sub dropdown-menu');
			});
				
			megamenu_element.find(".widget-category_image ul.level0").each(function() {
				var check_level = $(this).parents('.widget-category_image').data('level');
				var check_limit = $(this).parents('.widget-category_image').data('limit');
				
				//remove .caret by check level
				$(this).find("ul.level" + check_level).parent().find('.caret').remove();
				//remove ul by check level
				$(this).find("ul.level" + check_level + " li").remove();
				
				var element = $(this).find("ul.level" + (check_level - 1) + " li").length;
				var count = 0;
				if(check_level > 0) {
					$(this).find("ul.level" + (check_level - 1) + " >li").each(function(){
						count = count + 1;
						if(count > check_limit){
							$(this).remove();
						}
					});
				}
			});
			
			//add for off canvas menu
			$(".off-canvas-nav-megamenu[data-megamenu-id="+value.id+"] .offcanvas-mainnav").find(".widget-category_image ul.level0").each(function() {
				var check_level = $(this).parents('.widget-category_image').data('level');
				var check_limit = $(this).parents('.widget-category_image').data('limit');
				//remove .caret by check level
				$(this).find("ul.level" + check_level).parent().find('.caret').remove();
				//remove ul by check level
				$(this).find("ul.level" + check_level + " li").remove();
				var element = $(this).find("ul.level" + (check_level - 1) + " li").length;
				var count = 0;
				if(check_level > 0) {
					$(this).find("ul.level" + (check_level - 1) + " >li").each(function(){
						count = count + 1;
						if(count > check_limit){
							$(this).remove();
						}
					});
				}
			});
		}
		
		if (value.type == "horizontal")
		{
			$(window).resize(function() {
				// console.log($(window).width());
				if( $(window).width() <= 767 ){
					set_target_blank(false, megamenu_element);	// set cavas NO
				}
				else {
					set_target_blank(true, megamenu_element);		// set cavas Yes
				}
			});
		}

		//if (value.type == "horizontal" && value.show_cavas == 1)
		if (value.type == "horizontal" && value.show_cavas == 1)
		{
			$(document.body).on('click', '[data-toggle="dropdown"]' ,function(){
				if(!$(this).parent().hasClass('open') && this.href && this.href != '#'){
					window.location.href = this.href;
				}
			});
		}
	})
	$('.dropdown-menu.level1').parent().removeClass('aligned-fullwidth');
	//js for widget image gallery product
	$(".fancybox").fancybox({
		openEffect	: 'none',
		closeEffect	: 'none'
	});

})

//auto calculate height of off canvas menu off
function auto_height_off(menu_object)
{	
	wrapper_height = $("#page").innerHeight();
	ul_height = menu_object.find(".leo-top-menu ul").innerHeight();
	ul_offset_top = menu_object.find(".leo-top-menu ul").offset().top;
	// console.log('test1');
	// console.log(megamenu_element.find(".leo-top-menu ul").height());
	// console.log(megamenu_element.find(".leo-top-menu ul").offset().top);
	// console.log($("#page").height());
    // $(".off-canvas-nav-megamenu[data-megamenu-id="+menu_id+"] .offcanvas-mainnav").css('min-height', windowHeight);
	if (ul_offset_top + ul_height > wrapper_height)
	{
		if (!$("#page").hasClass('megamenu-autoheight'))
		{
			$("#page").addClass('megamenu-autoheight');
		}
	}
	else
	{
		$("#page").removeClass('megamenu-autoheight');
	}
}

//auto calculate height of off canvas menu
function auto_height(menu_id)
{	
	windowHeight = $(window).innerHeight();
    $(".off-canvas-nav-megamenu[data-megamenu-id="+menu_id+"] .offcanvas-mainnav").css('min-height', windowHeight);
}

function off_canvas_active()
{
	// console.log($(window).height());
	if($('body').hasClass('off-canvas-active'))
	{
		$("body").removeClass("off-canvas-active").addClass("off-canvas-inactive");
	}
	else if($('body').hasClass('off-canvas-inactive'))
	{
		$("body").removeClass("off-canvas-inactive").addClass("off-canvas-active");
	}
	else
	{
		$("body").addClass("off-canvas-active");
	}
	
}

function set_target_blank( show, megamenu_element)
{
	if (show)
	{
		
		megamenu_element.find(".leo-top-menu li a").each(function(){
		   if( $(this).hasClass('has-category') && (this).hasAttribute('data-toggle') && $(this).attr('target')== '_blank' ){
				var value = $(this).attr('data-toggle');
				$(this).removeAttr('data-toggle');
				$(this).attr('remove-data-toggle', value);
		   }
		})
	}else 
	{
		// console.log('test');
		megamenu_element.find(".leo-top-menu li a").each(function(){	
		   if( $(this).hasClass('has-category') && (this).hasAttribute('remove-data-toggle') && $(this).attr('target')== '_blank' ){
				var value = $(this).attr('remove-data-toggle');
				$(this).removeAttr('remove-data-toggle');
				$(this).attr('data-toggle', value);
		   }
		})
	}

}

function scrollSliderBarMenu(megamenu_element){
	var menuElement = megamenu_element;
	var columnElement = null;
	var maxWindowSize = 1024;
	
	// if($(menuElement).hasClass('float-vertical-right'))
		// columnElement = $("#right_column");
	// else if($(menuElement).hasClass('float-vertical-left'))
		// columnElement = $("#left_column");
	//auto display slider bar menu when have left or right column
	if($(columnElement).length && $(window).width()>=maxWindowSize) showOrHideSliderBarMenu(columnElement, menuElement, 1);
	megamenu_element.find(".verticalmenu-button").click(function(){
		if($(menuElement).hasClass('active'))
		{
			showOrHideSliderBarMenu(columnElement, menuElement, 0);
		} 
		else
		{
			showOrHideSliderBarMenu(columnElement, menuElement, 1);
		}
	});

	var lastWidth = $(window).width();
	$(window).resize(function() {
		if($(window).width()!=lastWidth){
			if($(window).width()<maxWindowSize) {
				if($(menuElement).hasClass('active')) showOrHideSliderBarMenu(columnElement, menuElement, 0);
			}else{
				if($(columnElement).length && !$(menuElement).hasClass('active')) showOrHideSliderBarMenu(columnElement, menuElement, 1);
			}
			lastWidth = $(window).width();
		}
	});
}

function showOrHideSliderBarMenu(columnElement, menuElement, active){
	if(active){
		$(menuElement).addClass('active');
		if($(columnElement).length && $(window).width()>=1024) 
			columnElement.css('padding-top',($('.block_content',$(menuElement)).height())+'px');
	}else{
		$(menuElement).removeClass('active');
		if($(columnElement).length) columnElement.css('padding-top','');
	}
}

