{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

<script type="text/javascript">
	{literal}
	var FancyboxI18nClose = "{/literal}{l s='Close' mod='leobootstrapmenu'}{literal}";
	var FancyboxI18nNext = "{/literal}{l s='Next' mod='leobootstrapmenu'}{literal}";
	var FancyboxI18nPrev = "{/literal}{l s='Previous' mod='leobootstrapmenu'}{literal}";
	var current_link = "{/literal}{$current_link}{literal}";		
	var currentURL = window.location;
	currentURL = String(currentURL);
	currentURL = currentURL.replace("https://","").replace("http://","").replace("www.","").replace( /#\w*/, "" );
	current_link = current_link.replace("https://","").replace("http://","").replace("www.","");
	var text_warning_select_txt = "{/literal}{l s='Please select One to remove?' mod='leobootstrapmenu'}";{literal}
	var text_confirm_remove_txt = "{/literal}{l s='Are you sure to remove footer row?' mod='leobootstrapmenu'}";{literal}
	var close_bt_txt = "{/literal}{l s='Close' mod='leobootstrapmenu'}";{literal}
	var list_menu = [];
	var list_menu_tmp = {};
	var list_tab = [];
	var isHomeMenu = 0;
	{/literal}
</script>