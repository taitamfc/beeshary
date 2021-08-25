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
$(document).ready(function() {
	
	//DONGND:: tab change in group config
	var id_panel = $("#leoquicklogin-setting .leoquicklogin-tablist li.active a").attr("href");
	$(id_panel).addClass('active').show();
	$('.leoquicklogin-tablist li').click(function(){
            if(!$(this).hasClass('active'))
            {
                var default_tab = $(this).find('a').attr("href");
                $('#LEOQUICKLOGIN_DEFAULT_TAB').val(default_tab);
            }
	})
	
        
        var html_save_and_stay = 
        '<li>' +
            '<a id="page-header-desc-appagebuilder_shortcode-SaveAndStay" class="toolbar_btn  pointer" href="javascript:void(0);" title="Save and stay" onclick="TopSaveAndStay()">' +
                '<i class="process-icon-save"></i>' +
                '<div>Save and stay</div>' +
            '</a>' +
        '</li>';
        $('.toolbarBox .btn-toolbar ul').prepend(html_save_and_stay);
});

TopSaveAndStay_Name = 'submitLeoquickloginConfig';
function TopSaveAndStay(){
    if (typeof TopSaveAndStay_Name !== 'undefined') {
        $("button[name$='"+TopSaveAndStay_Name+"']").click();
    }
}