{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
 <!-- @file modules\appagebuilder\views\templates\hook\ApTwitter -->
{if isset($formAtts.twidget_id) && $formAtts.twidget_id}
{if !isset($formAtts.accordion_type) || $formAtts.accordion_type == 'full'}{* Default : always full *}
<div class="block widget-twitter {(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'}">
	{($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
    {if isset($formAtts.title) && $formAtts.title}
    <h4 class="title_block">{$formAtts.title|escape:'html':'UTF-8'}</h4>
    {/if}
    {if isset($formAtts.sub_title) && $formAtts.sub_title}
        <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
    {/if}
	
    <div class="block_content">
		<div id="ap-twitter{$formAtts.twidget_id|escape:'html':'UTF-8'}" class="apollo-twitter">
            <a class="twitter-timeline" href="https://twitter.com/{$formAtts.username|escape:'html':'UTF-8'}"
                data-width="{$formAtts.width|intval}"
                data-height="{$formAtts.height|intval}"
            	data-dnt="true"
            	data-widget-id="{$formAtts.twidget_id|escape:'html':'UTF-8'}"
            	{if isset($formAtts.link_color) && $formAtts.link_color}data-link-color="{$formAtts.link_color|escape:'html':'UTF-8'}"{/if}
            	{if isset($formAtts.border_color) && $formAtts.border_color}data-border-color="{$formAtts.border_color|escape:'html':'UTF-8'}"{/if}
            	{if isset($formAtts.count) && $formAtts.count}data-tweet-limit="{$formAtts.count|intval}"{/if}
            	data-show-replies="{if isset($formAtts.show_replies) && $formAtts.show_replies}true{else}false{/if}"
            	data-chrome="{if isset($formAtts.transparent) && !$formAtts.transparent} transparent{/if} 
            				{if isset($formAtts.show_scrollbar) && !$formAtts.show_scrollbar} noscrollbar{/if}
            				{if isset($formAtts.show_border) && !$formAtts.show_border} noborders{/if}
            				{if isset($formAtts.show_header) && !$formAtts.show_header} noheader{/if}
            				{if isset($formAtts.show_footer) && !$formAtts.show_footer} nofooter{/if}"
        	>{l s='Tweets by' mod='appagebuilder'} {$formAtts.username|escape:'html':'UTF-8'}</a>
		</div>
	</div>
	{($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
</div>
{elseif isset($formAtts.accordion_type) && ($formAtts.accordion_type == 'accordion' || $formAtts.accordion_type == 'accordion_small_screen')}{* Case : full or accordion*}
    
<div class="block widget-twitter block-toggler {(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'}{if $formAtts.accordion_type == 'accordion_small_screen'} accordion_small_screen{/if}">
	{($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
    {if isset($formAtts.title) && $formAtts.title}
    <div class="title clearfix" data-target="#ap-twitter{$formAtts.twidget_id|escape:'html':'UTF-8'}" data-toggle="collapse">
        <h4 class="title_block">{$formAtts.title|escape:'html':'UTF-8'}</h4>
        <span class="float-xs-right">
          <span class="navbar-toggler collapse-icons">
            <i class="material-icons add">&#xE313;</i>
            <i class="material-icons remove">&#xE316;</i>
          </span>
        </span>
    </div>
    {/if}
    {if isset($formAtts.sub_title) && $formAtts.sub_title}
        <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
    {/if}
	
    <div class="block_content">
		<div id="ap-twitter{$formAtts.twidget_id|escape:'html':'UTF-8'}" class="apollo-twitter collapse">
            <a class="twitter-timeline" href="https://twitter.com/{$formAtts.username|escape:'html':'UTF-8'}"
                data-width="{$formAtts.width|intval}"
                data-height="{$formAtts.height|intval}"
            	data-dnt="true"
            	data-widget-id="{$formAtts.twidget_id|escape:'html':'UTF-8'}"
            	{if isset($formAtts.link_color) && $formAtts.link_color}data-link-color="{$formAtts.link_color|escape:'html':'UTF-8'}"{/if}
            	{if isset($formAtts.border_color) && $formAtts.border_color}data-border-color="{$formAtts.border_color|escape:'html':'UTF-8'}"{/if}
            	{if isset($formAtts.count) && $formAtts.count}data-tweet-limit="{$formAtts.count|intval}"{/if}
            	data-show-replies="{if isset($formAtts.show_replies) && $formAtts.show_replies}true{else}false{/if}"
            	data-chrome="{if isset($formAtts.transparent) && !$formAtts.transparent} transparent{/if} 
            				{if isset($formAtts.show_scrollbar) && !$formAtts.show_scrollbar} noscrollbar{/if}
            				{if isset($formAtts.show_border) && !$formAtts.show_border} noborders{/if}
            				{if isset($formAtts.show_header) && !$formAtts.show_header} noheader{/if}
            				{if isset($formAtts.show_footer) && !$formAtts.show_footer} nofooter{/if}"
        	>{l s='Tweets by' mod='appagebuilder'} {$formAtts.username|escape:'html':'UTF-8'}</a>
		</div>
	</div>
	{($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
</div>
{/if}
<script>
	ap_list_functions.push(function(){
        // Check avoid include duplicate library Facebook SDK
        if($("#twitter-wjs").length == 0) {
            window.twttr = (function (d, s, id) {
                var t, js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id; js.src= "https://platform.twitter.com/widgets.js";
                fjs.parentNode.insertBefore(js, fjs);
                return window.twttr || (t = { _e: [], ready: function (f) { t._e.push(f) } });
            }(document, "script", "twitter-wjs"));
        }
    
		var leo_flag_twitter_set_css = 1;
		$('#ap-twitter{$formAtts.twidget_id}').bind("DOMSubtreeModified", function() {
			leo_flag_twitter_set_css++;
			
			var isRun = 10;
			
                        {*var is_chrome = navigator.userAgent.indexOf('Chrome') > -1;
                        var is_explorer = navigator.userAgent.indexOf('MSIE') > -1;
                        var is_firefox = navigator.userAgent.indexOf('Firefox') > -1;*}
                        var is_safari = navigator.userAgent.indexOf("Safari") > -1;
                        {*var is_opera = navigator.userAgent.toLowerCase().indexOf("op") > -1;
                        if ((is_chrome)&&(is_safari)) {is_safari=false;}
                        if ((is_chrome)&&(is_opera)) {is_chrome=false;}                        *}
			if(window.chrome || is_safari){
				isRun = 5;
			}
            
			if(leo_flag_twitter_set_css == isRun)
			{
				// Run only one time
				
				$('#ap-twitter{$formAtts.twidget_id} iframe').ready(function() {

					{if (isset($formAtts.border_color) && $formAtts.border_color) && isset($formAtts.show_border) && $formAtts.show_border }
						// SHOW BORDER COLOR
						$('#ap-twitter{$formAtts.twidget_id} iframe').css('border', '1px solid {$formAtts.border_color}');
					{/if}
					{if (isset($formAtts.name_color) && $formAtts.name_color) }
						$('#ap-twitter{$formAtts.twidget_id} iframe').contents().find('.TweetAuthor-name.Identity-name.customisable-highlight').css('color', '{$formAtts.name_color}');
					{/if}
					{if (isset($formAtts.mail_color) && $formAtts.mail_color) }
						$('#ap-twitter{$formAtts.twidget_id} iframe').contents().find('.TweetAuthor-screenName.Identity-screenName').css('color', '{$formAtts.mail_color}');
					{/if}

					var head = jQuery("#ap-twitter{$formAtts.twidget_id} iframe").contents().find("head");

					var css = {literal}'<style type="text/css">\n\{/literal}
					{if (isset($formAtts.text_color) && $formAtts.text_color) }\n\
						body { color : {$formAtts.text_color} ; }\n\
					{/if}\n\
					{if (isset($formAtts.link_color) && $formAtts.link_color) }\n\
						.timeline-Tweet-text a { color : {$formAtts.link_color} ; }\n\
					{/if}\n\
							</style>';
					$(head).append(css);
				});
			
			}
		});	
	});
</script>
{/if}