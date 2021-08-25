/**
 * @copyright Commercial License By LeoTheme.Com 
 * @email leotheme.com
 * @visit http://www.leotheme.com
 */
$(document).ready(function() {
	if (typeof enable_js_lang != 'undefined')
	{
		
		$('ul#first-currencies li:not(.selected)').css('opacity', 0.3);
		$('ul#first-currencies li:not(.selected)').hover(function(){
			$(this).css('opacity', 1);
		}, function(){
			$(this).css('opacity', 0.3);
		});
	}
	
	if (typeof enable_js_currency != 'undefined')
	{
		
		$("#setCurrency").mouseover(function(){
			$(this).addClass("countries_hover");
			$(".currencies_ul").addClass("currencies_ul_hover");
		});
		$("#setCurrency").mouseout(function(){
			$(this).removeClass("countries_hover");
			$(".currencies_ul").removeClass("currencies_ul_hover");
		});
	}
	
	if (typeof js_country != 'undefined')
	{
		
		$("#countries").mouseover(function(){
			$(this).addClass("countries_hover");
			$(".countries_ul").addClass("countries_ul_hover");
		});
		$("#countries").mouseout(function(){
			$(this).removeClass("countries_hover");
			$(".countries_ul").removeClass("countries_ul_hover");
		});
	}
})

function setCurrency(id_currency)
{
	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: prestashop['urls']['base_url'] + 'index.php' + '?rand=' + new Date().getTime(),
		data: 'controller=change-currency&id_currency='+ parseInt(id_currency),
		success: function(msg)
		{
			location.reload(true);
		}
	});
}


