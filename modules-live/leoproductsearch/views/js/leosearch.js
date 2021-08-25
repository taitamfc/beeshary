/**
 * @copyright Commercial License By LeoTheme.Com 
 * @email leotheme.com
 * @visit http://www.leotheme.com
 */

var instantSearchQueries = [];
$(document).ready(function()
{
	/* TODO Ids aa blocksearch_type need to be removed*/
	
	if (typeof ajaxsearch != 'undefined' && ajaxsearch && typeof blocksearch_type !== 'undefined' && blocksearch_type){
		
		var width_ac_results = 	$("#leo_search_query_" + blocksearch_type).outerWidth();
		$("#leo_search_query_" + blocksearch_type).autocomplete(
			leo_search_url,
			{
				minChars: 3,
				max: numpro_display,
				width: (width_ac_results > 0 ? width_ac_results : 500),
				selectFirst: false,
				scroll: false,
				dataType: "json",
				formatItem: function(data, i, max, value, term) {
					return value;
				},
				parse: function(data) {
				
					var result = data.products;
					var mytab = new Array();
					if (result.length > 0)
					{
						for (var i = 0; i < result.length; i++)
						{
							//DONGND:: update config show product img and product price
							var lps_html_result = '';
							if (typeof lps_show_product_img != 'undefined' && lps_show_product_img)
							{
								lps_html_result += '<div class="lps-result-img"><img class="img-fluid" align = "center" src=' + result[i].cover.bySize.small_default.url + '></div>';
							}
							lps_html_result += '<div class="lps-result-content"><div class="lps-result-title">' + result[i].name +'</div>';
							if (typeof lps_show_product_price != 'undefined' && lps_show_product_price)
							{
								lps_html_result += '<div class="lps-result-price">' + result[i].price +'</div>';
							}
							lps_html_result += '<div>';
							
							mytab[mytab.length] = { data: result[i], value: lps_html_result };
						}
					}
					else
					{
						mytab[0] = {data: { url: window.location.href}, value: '<span class="no-result">'+txt_not_found+'</span>'};
					}
							
					return mytab;
				},
				extraParams: {
					ajaxSearch: 1,
					id_lang: prestashop.language.id,
					leoproductsearch_static_token: leoproductsearch_static_token,
					leoproductsearch_token: leoproductsearch_token
				}
			}
		)
		.result(function(event, data, formatted) {
			$('#leo_search_query_' + blocksearch_type).val(data.name);
			document.location.href = data.url;
		});
		
		//DONGND:: update when width of input has been change
		$("#leo_search_query_" + blocksearch_type).click(function(){
			width_ac_results = $(this).outerWidth();		
			//DONGND:: update option js libary option when resize
			$(this).setOptions(
				{
					width: width_ac_results
				}
			);	
		});
	}
	
	$('.cate-item').click(function(){
		if (!$(this).hasClass('active'))
		{
			$('.cate-item.active').removeClass('active');
			var cate_id = $(this).data('cate-id');
			var cate_name = $(this).data('cate-name');
			$('#leosearch-cate-id').val(cate_id);
			$('#leosearchtop-cate-id').val(cate_id);
			$('#dropdownListCate span').text(cate_name);
			$('#dropdownListCateTop span').text(cate_name);
			$(this).addClass('active');
		}
		$('#dropdownListCate').trigger('click');
		$('#dropdownListCateTop').trigger('click');
		var e = jQuery.Event("keydown");
		e.keyCode = 40;
		$(this).parents('form').find('.search_query').focus().trigger(e); 
		return false;
	});
	
	//DONGND:: show result when click to input search
	$('.search_query').click(function(){
		if ($(this).val() != '')
		{
			var e = jQuery.Event("keydown");
			e.keyCode = 40;
			$(this).trigger(e);
		}
	});
		
});

//DONGND:: update position of result when resize
$(window).resize(function(){
	updatePositionOfResult();
});

//DONGND:: update position of result when scroll
//$(window).scroll(function(){
	// updatePositionOfResult();
	// if ($('.ac_results').length)
	// {
		// $('.ac_results').hide();
	// }		
//})

function updatePositionOfResult()
{
	if ($('.ac_results').length)
	{	
		width_ac_results = $("#leo_search_query_" + blocksearch_type).outerWidth();		
		//DONGND:: update option js libary option when resize
		$("#leo_search_query_" + blocksearch_type).setOptions(
			{
				width: width_ac_results
			}
		);	
		//DONGND:: update position and with of block result when resize
		//var o_top = $("#leo_search_query_" + blocksearch_type).offset().top;
		//var o_left = $("#leo_search_query_" + blocksearch_type).offset().left;
		//var f_height = $("#leo_search_query_" + blocksearch_type).outerHeight();
		
		$('.ac_results').width(width_ac_results);
		// $('.ac_results').css({
			// 'top': o_top+f_height,
			// 'left' : o_left,
		// })	
	}
}

function tryToCloseInstantSearch()
{
	if ($('#old_center_column').length > 0)
	{
		$('#center_column').remove();
		$('#old_center_column').attr('id', 'center_column');
		$('#center_column').show();
		return false;
	}
}

function stopInstantSearchQueries()
{
	for(i=0;i<instantSearchQueries.length;i++)
		instantSearchQueries[i].abort();
	instantSearchQueries = new Array();
}
