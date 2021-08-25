{*
* 2007-2017 IQIT-COMMERCE.COM
*
* NOTICE OF LICENSE
*
*  @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
*  @copyright 2007-2017 IQIT-COMMERCE.COM
*  @license   GNU General Public License version 2
*
* You can not resell or redistribute this software.
*
*}
	<div class="container container-iqit-menu">
	<div  class="iqitmegamenu-wrapper cbp-hor-width-{$menu_settings.hor_width} iqitmegamenu-all clearfix">
		<div id="iqitmegamenu-horizontal" class="iqitmegamenu  cbp-nosticky {if $menu_settings.hor_s_transparent && $menu_settings.hor_sticky} cbp-sticky-transparent{/if}" role="navigation">
			<div class="container">

				{if isset($menu_settings_v) && ($menu_settings_v.ver_position==2 || $menu_settings_v.ver_position==3) }

					<div class="cbp-vertical-on-top {if $menu_settings_v.ver_position==2}cbp-homepage-expanded{/if}">
						{include file="module:iqitmegamenu/views/templates/hook/vertical.tpl" ontop=1}
					</div>
				{/if}
				{hook h='displayAfterIqitMegamenu'}
				<nav id="cbp-hrmenu" class="cbp-hrmenu cbp-horizontal cbp-hrsub-narrow  {if $menu_settings.hor_animation==1}cbp-fade{/if} {if $menu_settings.hor_animation==2}cbp-fade-slide-bottom{/if} {if $menu_settings.hor_animation==3}cbp-fade-slide-top{/if} {if $menu_settings.hor_s_arrow}cbp-arrowed{/if} {if !$menu_settings.hor_arrow} cbp-submenu-notarrowed{/if} {if !$menu_settings.hor_arrow} cbp-submenu-notarrowed{/if} {if $menu_settings.hor_center} cbp-menu-centered{/if} ">
					<ul>
						{foreach $horizontal_menu as $tab}
						<li id="cbp-hrmenu-tab-{$tab.id_tab}" class="cbp-hrmenu-tab cbp-hrmenu-tab-{$tab.id_tab}{if $tab.active_label} cbp-onlyicon{/if}{if $tab.float} pull-right cbp-pulled-right{/if} {if $tab.submenu_type && !empty($tab.submenu_content)} cbp-has-submeu{/if}">
	{if $tab.url_type == 2}<a role="button" class="cbp-empty-mlink">{else}<a href="{$tab.url}" {if $tab.new_window}target="_blank"{/if}>{/if}


								<span class="cbp-tab-title">{if $tab.icon_type && !empty($tab.icon_class)} <i class="icon fa {$tab.icon_class} cbp-mainlink-icon"></i>{/if}

								{if !$tab.icon_type && !empty($tab.icon)} <img src="{$tab.icon}" alt="{$tab.title}" class="cbp-mainlink-iicon" />{/if}{if !$tab.active_label}{$tab.title|replace:'/n':'<br />'}{/if}{if $tab.submenu_type} <i class="fa fa-angle-down cbp-submenu-aindicator"></i>{/if}</span>
								{if !empty($tab.label)}<span class="label cbp-legend cbp-legend-main">{if !empty($tab.legend_icon)} <i class="icon fa {$tab.legend_icon} cbp-legend-icon"></i>{/if} {$tab.label}
								<span class="cbp-legend-arrow"></span></span>{/if}
						</a>
							{if $tab.submenu_type && !empty($tab.submenu_content)}
							<div class="cbp-hrsub col-xs-{$tab.submenu_width}">
								<div class="cbp-triangle-container"><div class="cbp-triangle-top"></div><div class="cbp-triangle-top-back"></div></div>
								<div class="cbp-hrsub-inner">
									{if $menu_settings.hor_s_width && !$menu_settings.hor_width && !$menu_settings.hor_sw_width}<div class="container">{/if}
									{if $tab.submenu_type==1}
									<div class="container-xs-height cbp-tabs-container">
									<div class="row row-xs-height">
									<div class="col-xs-2 col-xs-height">
										<ul class="cbp-hrsub-tabs-names cbp-tabs-names" >
											{if isset($tab.submenu_content_tabs)}
											{foreach $tab.submenu_content_tabs as $innertab name=innertabsnames}
											<li class="innertab-{$innertab->id} ">
												<a data-target="#{$innertab->id}-innertab-{$tab.id_tab}" {if $innertab->url_type != 2} href="{$innertab->url}" {/if} {if $smarty.foreach.innertabsnames.first}class="active"{/if}>
												{if $innertab->icon_type && !empty($innertab->icon_class)} <i class="icon fa {$innertab->icon_class} cbp-mainlink-icon"></i>{/if}
												{if !$innertab->icon_type && !empty($innertab->icon)} <img src="{$innertab->icon}" alt="{$innertab->title}" class="cbp-mainlink-iicon" />{/if}
												{if !$innertab->active_label}{$innertab->title} {/if}
												{if !empty($innertab->label)}<span class="label cbp-legend cbp-legend-inner">{if !empty($innertab->legend_icon)} <i class="icon fa {$innertab->legend_icon} cbp-legend-icon"></i>{/if} {$innertab->label}
												<span class="cbp-legend-arrow"></span></span>{/if}
											</a><i class="icon fa fa-angle-right cbp-submenu-it-indicator"></i><span class="cbp-inner-border-hider"></span></li>
											{/foreach}
											{/if}
										</ul>
									</div>

										{if isset($tab.submenu_content_tabs)}
										<div class="tab-content">
											{foreach $tab.submenu_content_tabs as $innertab name=innertabscontent}
											<div class="col-xs-10 col-xs-height tab-pane cbp-tab-pane {if $smarty.foreach.innertabscontent.first}active{/if} innertabcontent-{$innertab->id}"
												 id="{$innertab->id}-innertab-{$tab.id_tab}" role="tabpanel">

												{if !empty($innertab->submenu_content)}
												<div class="clearfix">
												{foreach $innertab->submenu_content as $element}
													{include file="module:iqitmegamenu/views/templates/hook/_partials/submenu_content.tpl" node=$element}
												{/foreach}
												</div>
												{/if}

											</div>
											{/foreach}
										</div>
										{/if}

									</div></div>
									{else}

										{if !empty($tab.submenu_content)}
											{foreach $tab.submenu_content as $element}
												{include file="module:iqitmegamenu/views/templates/hook/_partials/submenu_content.tpl" node=$element}
											{/foreach}
										{/if}

									{/if}
									{if $menu_settings.hor_s_width && !$menu_settings.hor_width && !$menu_settings.hor_sw_width}</div>{/if}
								</div>
							</div>
							{/if}
						</li>
						{/foreach}
					</ul>
				</nav>



			</div>




			<div id="iqitmegamenu-mobile">

				<div id="iqitmegamenu-shower" class="clearfix">
					<div class="iqitmegamenu-icon"><i class="icon fa fa-reorder"></i></div> <span>{l s='Menu' mod='iqitmegamenu'}</span>
				</div>
				<div id="iqitmegamenu-mobile-content">
				<div class="cbp-mobilesubmenu">
					<ul id="iqitmegamenu-accordion" class="{if $mobile_menu_style}iqitmegamenu-accordion{else}cbp-spmenu cbp-spmenu-vertical cbp-spmenu-left{/if}" style="text-align: left;">
						{include file="module:iqitmegamenu/views/templates/hook/_partials/mobile_menu.tpl" menu=$mobile_menu}
					</ul>
				</div>
					{if !$mobile_menu_style}<div id="cbp-spmenu-overlay" class="cbp-spmenu-overlay"><div id="cbp-close-mobile" class="close-btn-ui"><i class="fa fa-times"></i></div></div>{/if}
					</div>
			</div>

		</div>
	</div>
	</div>
