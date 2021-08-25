{* 
* @Module Name: Leo Slideshow
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

{assign var="class_group" value="iview-group-`$rand_num`-`$sliderParams.id_leoslideshow_groups`"}
{if $sliderParams.slider_class == "boxed"}
	<div class="layerslider-wrapper{if $sliderParams.group_class} {$sliderParams.group_class|escape:'html':'UTF-8'}{/if}{if $sliderParams.md_width|intval} col-md-{$sliderParams.md_width|intval}{/if}{if $sliderParams.sm_width} col-sm-{$sliderParams.sm_width|intval}{/if}{if $sliderParams.sm_width} col-xs-{$sliderParams.xs_width|intval}{/if}">
{/if}
<div class="bannercontainer banner-{$sliderParams.slider_class}{if $sliderParams.group_class} {$sliderParams.group_class|escape:'html':'UTF-8'}{/if}" style="padding: {$sliderParams.padding|escape:'html':'UTF-8'};margin: {$sliderParams.margin|escape:'html':'UTF-8'};">
	<div class="iview {$class_group}">
		{if $sliders}
			{foreach from=$sliders item=slider}
				{if $slider.video.active}
					<!-- SLIDE VIDEO BEGIN -->
					<div data-leo_image="{$slider.thumbnail|escape:'html':'UTF-8'}"
						data-leo_type="video"
						data-leo_transition="strip-right-fade,strip-left-fade"
						data-leo_background="{$slider.background_type|escape:'html':'UTF-8'}"
						data-autoplay="{$slider.video.videoauto}">
						<iframe src="{$slider.video.videoURL|escape:'html':'UTF-8'}?title=0&amp;byline=0&amp;portrait=0;api=1" width="100%" height="100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
					</div>
					<!-- SLIDE VIDEO END -->
				{else}
					
					<!-- SLIDE IMAGE BEGIN -->
					<div class="slide_config {if isset($slider.data_link)&& $slider.data_link}data-link{/if}"
						{if $slider.main_image != ''} data-leo_image="{$slider.main_image|escape:'html':'UTF-8'}"{/if}
						{if $slider.background_color != ''} data-leo_background_color="{$slider.background_color|escape:'html':'UTF-8'}"{/if}					
						{if $slider.data_link != ''} data-link="{$slider.data_link|escape:'html':'UTF-8'}"{/if}
						{if $slider.data_target != ''} data-target="{$slider.data_target|escape:'html':'UTF-8'}"{/if}
						data-leo_pausetime="{$slider.data_delay|escape:'html':'UTF-8'}"
						data-leo_thumbnail="{$slider.thumbnail|escape:'html':'UTF-8'}"
						data-leo_background="{$slider.background_type|escape:'html':'UTF-8'}"					
						{if $slider.enable_custom_html_bullet}
						data-leo_bullet_description="{$slider.bullet_description}"{* HTML form , no escape necessary *}
						data-leo_bullet_class="{$slider.bullet_class|escape:'html':'UTF-8'}"
						{/if}												
						>
						
						
						{if isset($slider.layersparams)}
							{foreach from=$slider.layersparams item=layer}
								
								<div class="tp-caption {if $layer.layer_link}data-link{/if}{if $layer.layer_class} {$layer.layer_class|escape:'html':'UTF-8'}{/if}" 
									 data-x="{$layer.layer_left|escape:'html':'UTF-8'}"
									 data-y="{$layer.layer_top|escape:'html':'UTF-8'}"
									 data-transition="{$layer.layer_transition|escape:'html':'UTF-8'}"
									 {if $layer.layer_link}onclick="event.stopPropagation();window.open('{$layer.layer_link|escape:'html':'UTF-8'}','{$layer.layer_target|escape:'html':'UTF-8'}');"{/if}
									 {if $layer.css}style="{$layer.css|escape:'html':'UTF-8'};"{/if}
									 >
									
									{if $layer.layer_type == "text"}<!-- LAYER TEXT BEGIN -->
										{$layer.layer_caption|replace:"_ASM_":"&"|replace:"_LEO_BACKSLASH_":"\\"|replace:"_LEO_DOUBLE_QUOTE_":"&quot;" nofilter}
									{/if}<!-- LAYER TEXT END -->


									{if $layer.layer_type == "image"}<!-- LAYER IMAGE BEGIN -->
										<img src="{$layer.layer_content|escape:'html':'UTF-8'}" alt="" class="img_disable_drag"/>
									{/if}<!-- LAYER IMAGE END -->


									{if $layer.layer_type == "video"}<!-- LAYER VIDEO BEGIN -->
										{if $layer.layer_video_type == "vimeo"}
											<iframe src="http://player.vimeo.com/video/{$layer.layer_video_id|escape:'html':'UTF-8'}?wmode=transparent&amp;title=0&amp;byline=0&amp;portrait=0;api=1" width="{$layer.layer_video_width|escape:'html':'UTF-8'}" height="{$layer.layer_video_height|escape:'html':'UTF-8'}" ></iframe>
										{else}
											<iframe src="http://www.youtube.com/embed/{$layer.layer_video_id|escape:'html':'UTF-8'}?wmode=transparent" width="{$layer.layer_video_width|escape:'html':'UTF-8'}" height="{$layer.layer_video_height|escape:'html':'UTF-8'}"></iframe>
										{/if}
									{/if}<!-- LAYER VIDEO END -->
									
									
								</div>
							{/foreach}
						{/if}
						
				</div><!-- SLIDE IMAGE END -->
				{/if}

			{/foreach}
		{/if}
	</div>
</div>
{if $sliderParams.slider_class == "boxed"}
	</div>
{/if}

<script type="text/javascript">
{if isset($load_from_appagebuilder) AND $load_from_appagebuilder == 1}
    {* Appagebuilder load widget *}
    ap_list_functions.push(function(){
{else}
    var leoslideshow_list_functions = [];
    leoslideshow_list_functions.push(function(){
{/if}

	jQuery(".{$class_group}").iView({
		// COMMON
		pauseTime:{$sliderParams.delay|default:'5000'|intval}, // delay
		startSlide:{$sliderParams.start_with_slide|default:'0'|intval},
		autoAdvance: {$sliderParams.autoAdvance|default:'true'|escape:'html':'UTF-8'},	// enable timer thá»�i gian auto next slide
		pauseOnHover: {$sliderParams.stop_on_hover|default:'false'|escape:'html':'UTF-8'},
		randomStart: {$sliderParams.shuffle_mode|default:'false'|escape:'html':'UTF-8'}, // Ramdom slide when start

		// TIMER
		timer: "{$sliderParams.timer|default:'Pie'|escape:'html':'UTF-8'}",
		timerPosition: "{$sliderParams.timerPosition|default:'top-right'|escape:'html':'UTF-8'}", // Top-right, top left ....
		timerX: {$sliderParams.timerX|default:'10'|intval},
		timerY: {$sliderParams.timerY|default:'10'|intval},
		timerOpacity: {$sliderParams.timerOpacity|default:'0.5'|escape:'html':'UTF-8'},
		timerBg: "{$sliderParams.timerBg|default:'#000'|escape:'html':'UTF-8'}",
		timerColor: "{$sliderParams.timerColor|default:'#EEE'|escape:'html':'UTF-8'}",
		timerDiameter: {$sliderParams.timerDiameter|default:'30'|intval},
		timerPadding: {$sliderParams.timerPadding|default:'4'|intval},
		timerStroke: {$sliderParams.timerStroke|default:'3'|intval},
		timerBarStroke: {$sliderParams.timerBarStroke|default:'1'|intval},
		timerBarStrokeColor: "{$sliderParams.timerBarStrokeColor|default:'#EEE'|escape:'html':'UTF-8'}",
		timerBarStrokeStyle: "{$sliderParams.timerBarStrokeStyle|default:'solid'|escape:'html':'UTF-8'}",
		playLabel: "{l s='Play' mod='leoslideshow'}",
		pauseLabel: "{l s='Pause' mod='leoslideshow'}",
		closeLabel: "{l s='Close' mod='leoslideshow'}", // Muli language

		// NAVIGATOR controlNav
		controlNav: {$sliderParams.controlNav|default:'false'|escape:'html':'UTF-8'}, // true : enable navigate - default:'false'
		keyboardNav: {$sliderParams.keyboardNav|default:'true'|escape:'html':'UTF-8'}, // true : enable keybroad
		controlNavThumbs: {$sliderParams.controlNavThumbs|default:'false'|escape:'html':'UTF-8'}, // true show thumbnail, false show number ( bullet )
		controlNavTooltip: {$sliderParams.controlNavTooltip|default:'true'|escape:'html':'UTF-8'}, // true : hover to bullet show thumnail
		tooltipX: {$sliderParams.tooltipX|default:'5'|escape:'html':'UTF-8'},
		tooltipY: {$sliderParams.tooltipY|default:'-5'|escape:'html':'UTF-8'},
		controlNavHoverOpacity: {$sliderParams.controlNavHoverOpacity|default:'0.6'|escape:'html':'UTF-8'}, // opacity navigator

		// DIRECTION
		controlNavNextPrev: false, // false dont show direction at navigator
		directionNav: {$sliderParams.directionNav|default:'true'|escape:'html':'UTF-8'}, // true  show direction at image ( in this case : enable direction )
		directionNavHoverOpacity: {$sliderParams.directionNavHoverOpacity|default:'0.6'|escape:'html':'UTF-8'}, // direction opacity at image
		nextLabel: "{l s='Next' mod='leoslideshow'}",				// Muli language
		previousLabel: "{l s='Previous' mod='leoslideshow'}", // Muli language

		// ANIMATION 
		fx: '{$sliderParams.fx|default:'random'|escape:'html':'UTF-8'}', // Animation
		animationSpeed: {$sliderParams.animationSpeed|default:'500'|escape:'html':'UTF-8'}, // time to change slide
//		strips: {$sliderParams.strips|default:'20'|intval},
		strips: 1, // set value is 1 -> fix animation full background
		blockCols: {$sliderParams.blockCols|default:'10'|intval}, // number of columns
		blockRows: {$sliderParams.blockRows|default:'5'|intval}, // number of rows

		captionSpeed: {$sliderParams.captionSpeed|default:'500'|intval}, // speed to show caption
		captionOpacity: {$sliderParams.captionOpacity|default:'1'|intval}, // caption opacity
		captionEasing: 'easeInOutSine', // caption transition easing effect, use JQuery Easings effect
		customWidth: {$sliderParams.width|intval},
		customHtmlBullet: {if $slider.enable_custom_html_bullet}true{else}false{/if},
		rtl: {if $sliderParams.rtl}true{else}false{/if},
		height:{if $sliderParams.height}{$sliderParams.height|intval}{else}780{/if},
		timer_show : {$sliderParams.timer_show|intval},

		//onBeforeChange: function(){}, // Triggers before a slide transition
		//onAfterChange: function(){}, // Triggers after a slide transition
		//onSlideshowEnd: function(){}, // Triggers after all slides have been shown
		//onLastSlide: function(){}, // Triggers when last slide is shown
		//onPause: function(){}, // Triggers when slider has paused
		//onPlay: function(){} // Triggers when slider has played

		onAfterLoad: function() 
		{
			// THUMBNAIL
			{if $sliderParams.nav_thumbnail_height}
					$('.{$class_group} .iview-controlNav a img').height({$sliderParams.nav_thumbnail_height|intval});
					//$('.{$class_group} .iview-tooltip').height({$sliderParams.nav_thumbnail_height|intval});
			{/if}
			{if $sliderParams.nav_thumbnail_width}
					$('.{$class_group} .iview-controlNav a img').width({$sliderParams.nav_thumbnail_width|intval});
					//$('.{$class_group} .iview-tooltip').width({$sliderParams.nav_thumbnail_width|intval});
			{/if}

			// BULLET
			{if $sliderParams.nav_thumbnail_height}
					$('.{$class_group} .iview-tooltip div.holder div.container div img').width({$sliderParams.nav_thumbnail_width|intval});
			{/if}
			{if $sliderParams.nav_thumbnail_width}
					$('.{$class_group} .iview-tooltip div.holder div.container div img').height({$sliderParams.nav_thumbnail_height|intval});
			{/if}

			// Display timer
			{if $sliderParams.timer_show eq 1 or $sliderParams.timer_show eq 2}
					$('.{$class_group} .iview-timer').hide();
			{/if}
		},

	});

	$(".img_disable_drag").bind('dragstart', function() {
		return false;
	});


// Fix : Slide link, image cant swipe
	// step 1
	var link_event = 'click';

	// step 3
	$(".{$class_group} .slide_config").on("click",function(){
		
		if(link_event !== 'click'){
			link_event = 'click';
			return;
		}

		if($(this).data('link') != undefined && $(this).data('link') != '') {
			window.open($(this).data('link'),$(this).data('target'));
		}
		
	});

	// step 2
	$(".{$class_group} .slide_config").on('swipe',function(){
		link_event = 'swiped';	// do not click event
	});
});
	 
</script>
