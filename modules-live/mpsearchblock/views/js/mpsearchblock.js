/**
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
*/

$(document).ready(function()
{
	var prod_ajax = '';
	var shop_ajax = '';
	var sel_ajax = '';
	var adr_ajax = '';
	var cat_ajax = '';
	var more = 'En savoir';
	var hide = 'En savoir';
	
	/*---- js for search block ----*/
	/**
	 * common code
	 * trigger : if search by key or change in category
	 * DomClear description : 1) hide all category container
	 * 						  2) empty all previous search results
	 * 						  3) empty hide/show text
	 */
	function DomClear()
	{
		$('.wk_search_cont').hide();  //$('.wk_search_sugg_wrapper ,.wk_search_cont').hide();
		$(".wk_search_cont").find('ul').html('');
		$("span.more_opt").html('');
		return true;
	}

	function abortAjax(num)
	{
		if (prod_ajax && (num == 1 || num == 2))
			prod_ajax.abort();
		if (shop_ajax && (num == 1 || num == 3))
			shop_ajax.abort();
		if (sel_ajax && (num == 1 || num == 4))
			sel_ajax.abort();
		if (adr_ajax && (num == 1 || num == 5))
			adr_ajax.abort();
		if (cat_ajax && (num == 1 || num == 6))
			cat_ajax.abort();

		return true;
	}

	/**
	 * Trigger : when search key is entered
	 * work: 1) when key entered search start using ajax
	 * 		 2) if up/down key is pressed while focus is on input box, focus switchto search results
	 */
	$('body').on('keyup', '#wk_search_box', function(event)
	{
		if (($('.wk_search_sugg_wrapper').is(':visible')) && (event.which == 40 || event.which == 38))
		{
			$(this).blur();

			if (event.which == 40)
				$(".wk_search_sugg_cont li:first a").focus();
			else if (event.which == 38)
				$(".wk_search_sugg_cont li:visible:last a").focus();
		}
		else
		{
			DomClear();
			if ($(this).val().length)
				advanceSearch(event);
			else
				$('.wk_search_sugg_wrapper').hide();
		}
	});

	/**
	 * Trigger : when category change
	 */
	$('body').on('change', '.wk_search_type', function(event)
	{
		DomClear();
		if ($(this).val().length)
			advanceSearch(event);
		else
			$('.wk_search_sugg_wrapper').hide();
	});

	/**
	 * This function is called on change of category or on search key input
	 * @param  {change | keyup} event
	 * working: for all category ajax is triggered from this function
	 * Note: before display results (creating DOM of search results <li><a>RESULT</a></li>) "we have to clear previous reults"
	 */
	function advanceSearch(event)
	{
		abortAjax(1);
		
		

		var key_word = $('#wk_search_box').val();
		var search_type = parseInt($("input[name='search_type']:checked").val());
		var data = {key:key_word, search_type:search_type};
		if (search_type == 2 || search_type == 1)
		{
			if (search_type == 1)
				data.flag = '1';

			prod_ajax = $.ajax(
			{
				url: ajaxsearch_url,
				type: 'POST',
				dataType: 'json',
				data: data,
				success: function (result)
				{
					if ($('#wk_search_box').val().length)
					{
						var prod_i = 1;
						$('.wk_search_sugg_wrapper').show();
						$("#wk_sugg_product").html('');
						if (result)
						{
							var no_prod = Object.keys(result).length;
							if (no_prod > 3)
								$('#wk_sugg_prod_cont').find('p span.more_opt').html(more+'[+]').show();

							$('#wk_sugg_prod_cont').show();
							$.each(result, function(key, value)
							{
								var prod_html = "<li class='wk_sugg_option ";
								if (prod_i > 3)
									prod_html += "wk_option_hide";
								prod_html += "'><a class='wk_option_a' href='"+value.link+"'>"+value.name+"</a></li>";

								$("#wk_sugg_product").append(prod_html);
								prod_i++;
							});
						}
					}
					else
						abortAjax(2);
				},
				error: function(error)
				{
					console.log(error);
				}
			});
		}

		if (search_type == 3 || search_type == 1)
		{
			if (search_type == 1)
				data.flag = '2';

			shop_ajax = $.ajax(
			{
				url: ajaxsearch_url,
				type: 'POST',
				dataType: 'json',
				data: data,
				success: function (result)
				{
					if ($('#wk_search_box').val().length)
					{
						var shop_i = 1;
						$('.wk_search_sugg_wrapper').show();
						$("#wk_sugg_shop").html('');
						if (result)
						{
							var no_shop = Object.keys(result).length;
							if (no_shop > 3)
								$('#wk_sugg_shop_cont').find('p span.more_opt').html(more+'[+]').show();

							$('#wk_sugg_shop_cont').show();
							$.each(result, function(key, value)
							{
								var shop_html = "<li class='wk_sugg_option ";
								if (shop_i > 3)
									shop_html += "wk_option_hide";
								shop_html += "'><a class='wk_option_a' href='"+value.link+"'>"+value.mp_shop_name+"</a></li>";

								$("#wk_sugg_shop").append(shop_html);
								shop_i++;
							});
						}
					}
					else
						abortAjax(3);
				},
				error: function(error)
				{
					console.log(error);
				}
			});
		}

		if (search_type == 4 || search_type == 1)
		{
			if (search_type == 1)
				data.flag = '3';

			sel_ajax = $.ajax(
			{
				url: ajaxsearch_url,
				type: 'POST',
				dataType: 'json',
				data: data,
				success: function (result)
				{
					if ($('#wk_search_box').val().length)
					{
						var seller_i = 1;
						$('.wk_search_sugg_wrapper').show();
						$("#wk_sugg_seller").html('');
						if (result)
						{
							var no_seller = Object.keys(result).length;
							if (no_seller > 3)
								$('#wk_sugg_seller_cont').find('p span.more_opt').html(more+'[+]').show();

							$('#wk_sugg_seller_cont').show();
							$.each(result, function(key, value)
							{
								var seller_html = "<li class='wk_sugg_option ";
								if (seller_i > 3)
									seller_html += "wk_option_hide";
								seller_html += "'><a class='wk_option_a' href='"+value.link+"'>"+value.mp_seller_name+"</a></li>";

								$("#wk_sugg_seller").append(seller_html);
								seller_i++;
							});
						}
					}
					else
						abortAjax(4);
				},
				error: function(error)
				{
					console.log(error);
				}
			});
		}

		if (search_type == 5 || search_type == 1)
		{
			if (search_type == 1)
				data.flag = '4';

			adr_ajax = $.ajax(
			{
				url: ajaxsearch_url,
				type: 'POST',
				dataType: 'json',
				data: data,
				success: function (result)
				{
					if ($('#wk_search_box').val().length)
					{
						var locat_i = 1;
						$('.wk_search_sugg_wrapper').show();
						$("#wk_sugg_location").html('');
						if (result)
						{
							var no_locat = Object.keys(result).length;
							if (no_locat > 3)
								$('#wk_sugg_location_cont').find('p span.more_opt').html(more+'[+]').show();

							$('#wk_sugg_location_cont').show();
							$.each(result, function(key, value)
							{
								var seller_html = "<li class='wk_sugg_option ";
								if (locat_i > 3)
									seller_html += "wk_option_hide";
								seller_html += "'><a class='wk_option_a' href='"+value.link+"'>"+value.mp_shop_name+" , "+value.mp_seller_name+"</a></li>";

								$("#wk_sugg_location").append(seller_html);
								locat_i++;
							});
						}
					}
					else
						abortAjax(5);
				},
				error: function(error)
				{
					console.log(error);
				}
			});
		}

		if (search_type == 6 || search_type == 1)
		{
			if (search_type == 1)
				data.flag = '5';

			cat_ajax = $.ajax(
			{
				url: ajaxsearch_url,
				type: 'POST',
				dataType: 'json',
				data: data,
				success: function (result)
				{
					if ($('#wk_search_box').val().length)
					{
						var cat_i = 1;
						$('.wk_search_sugg_wrapper').show();
						$("#wk_sugg_category").html('');
						if (result)
						{
							var no_of_category = Object.keys(result).length;
							if (no_of_category > 3)
								$('#wk_sugg_category_cont').find('p span.more_opt').html(more+'[+]').show();

							$('#wk_sugg_category_cont').show();
							$.each(result, function(key, value)
							{
								var category_html = "<li class='wk_sugg_option ";
								if (cat_i > 3)
									category_html += "wk_option_hide";
								category_html += "'><a class='wk_option_a' href='"+value.link+"'>"+value.name+"</a></li>";

								$("#wk_sugg_category").append(category_html);
								cat_i++;
							});
						}
					}
					else
						abortAjax(6);
				},
				error: function(error)
				{
					console.log(error);
				}
			});
		}

		if (search_type == 7 || search_type == 1)
		{
			if (search_type == 1)
				data.flag = '3';

			sel_ajax = $.ajax(
			{
				url: ajaxsearch_url,
				type: 'POST',
				dataType: 'json',
				data: data,
				success: function (result)
				{
					if ($('#wk_search_box').val().length)
					{
						var seller_i = 1;
						$('.wk_search_sugg_wrapper').show();
						$("#wk_sugg_profession").html('');
						if (result)
						{
							var no_seller = Object.keys(result).length;
							if (no_seller > 3)
								$('#wk_sugg_profession_cont').find('p span.more_opt').html(more+'[+]').show();

							$('#wk_sugg_profession_cont').show();
							$.each(result, function(key, value)
							{
								var seller_html = "<li class='wk_sugg_option ";
								if (seller_i > 3)
									seller_html += "wk_option_hide";
								seller_html += "'><a class='wk_option_a' href='"+value.link+"'>"+value.mp_seller_name+"</a></li>";

								$("#wk_sugg_profession").append(seller_html);
								seller_i++;
							});
						}
					}
					else
						abortAjax(4);
				},
				error: function(error)
				{
					console.log(error);
				}
			});
		}
	}

	/**
	 * working: hide search block when click in body not in search block
	 */
	$('body').on('click', function(event)
	{
		if ($('.wk_search_sugg_wrapper').css('display') == 'block')
		{
			$(".wk_search_cont").find('ul').html('');
			$(".wk_search_sugg_wrapper").hide();
		}
	});

	$('#wk_search_wrapper').on('click',function(event)
	{
		event.stopPropagation();
	});

	/**
	 * This function is used to hide and show results which are more then 3
	 */
	$('.more_opt').on('click',function(event)
	{
		if ($(this).parent().next().find('li.wk_option_hide').css('display') == 'none')
			$(this).html(hide+'[-]');
		else
			$(this).html(more+'[+]');

		$(this).parent().next().find('li.wk_option_hide').toggle();
	});

 	/**
 	 * Triggered : when up or down key is pressed
 	 * working: 1) on pressing up key focus is on previous "<a>" tag(element)
 	 * 			2) on pressing down key focus is on next "<a>" tag(element)
 	 * Note: focus will go only on visible tags | row | <a> element
 	 */
	$('body').on('keyup', '.wk_option_a', function(event)
	{
		if (event.which == 40 || event.which == 38)
		{
			$(this).blur();
			$(this).closest('ul').scrollTop($(this).index() * $(this).outerHeight());
			if (event.which == 40)
			{
				if ($(this).parent().next('li:visible').length)
					$(this).parent().next().find('a').focus();
				else if($(this).parent().parent().parent().nextAll('div.wk_search_cont:visible:first').find('ul li:visible').length)
					$(this).parent().parent().parent().nextAll('div.wk_search_cont:visible:first').find('ul li:visible:first a').focus();
				else {
					$(".wk_search_sugg_cont:visible li:visible:first a").focus();
				}
			}
			else if (event.which == 38)
			{
				if ($(this).parent().prev('li:visible').length)
					$(this).parent().prev().find('a').focus();
				else if($(this).parent().parent().parent().prevAll('div.wk_search_cont:visible:first').find('ul li:visible:last a').length)
					$(this).parent().parent().parent().prevAll('div.wk_search_cont:visible:first').find('ul li:visible:last a').focus();
				else
					$(".wk_search_sugg_cont:visible li:visible:last a").focus();
			}
		}
	});

	/**
	 * Working : Scroll is according to focus default scrolling is not allowed but only when focus is on results
	 */
	$(document).on('keydown','body', function (e)
	{
		if((e.which == 40 || e.which == 38) && $('.wk_option_a').is(':focus'))
		{
			e.preventDefault();
			return false;
		}
	});

	/*---- js for search block ----*/

	/*---- js for search result page ----*/

	$('#wk_search_form').on('submit', function()
	{
		if (!$('#wk_search_box').val())
			return false;
	});

	$('.cat_toggle_btn').on('click', function()
	{
		var related_id = $(this).data('related-id');
		$('#'+related_id).slideToggle("slow");

		// if ($(this).find('i').hasClass('icon-circle-arrow-up'))
		// 	$(this).find('i').removeClass('icon-circle-arrow-up').addClass('icon-circle-arrow-down');
		// else
		// 	$(this).find('i').removeClass('icon-circle-arrow-down').addClass('icon-circle-arrow-up');
	});

	$('.wk_view_all_js').on('click',function()
	{
		if ($(this).parent().find('div.wk_hide_result').is(':visible'))
			$(this).html('Tout afficher');
		else
			$(this).html('Voir moins');
		$(this).parent().find('div.wk_hide_result').slideToggle();
	});

	$('body').on('change', '.selectProductSort', function(e)
	{
		redirect_url = $(this).val();
		document.location.href = redirect_url;
	});

	/*---- js for search result page ----*/

});