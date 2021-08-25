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

<nav id="cbp-hrmenu1" class="cbp-hrmenu  iqitmegamenu-all cbp-vertical  {if $menu_settings_v.ver_animation==1}cbp-fade{/if} {if $menu_settings_v.ver_s_arrow}cbp-arrowed{/if} {if !$menu_settings_v.ver_arrow} cbp-submenu-notarrowed{/if} {if !$menu_settings_v.ver_arrow} cbp-submenu-notarrowed{/if}  ">
	<div class="cbp-vertical-title"><i class="fa fa-bars cbp-iconbars"></i> {l s='Navigation' mod='iqitmegamenu'}</div>
					<ul>
						{foreach $vertical_menu as $tab}
						<li class="cbp-hrmenu-tab cbp-hrmenu-tab-{$tab.id_tab} {if $tab.active_label} cbp-onlyicon{/if}{if $tab.float} pull-right cbp-pulled-right{/if}">
	{if $tab.url_type == 2}<a role="button" class="cbp-empty-mlink">{else}<a href="{$tab.url}" onclick="" {if $tab.new_window}target="_blank"{/if}>{/if}
								{if $tab.icon_type && !empty($tab.icon_class)} <i class="fa fa {$tab.icon_class} cbp-mainlink-icon"></i>{/if}
								{if !$tab.icon_type && !empty($tab.icon)} <img src="{$tab.icon}" alt="{$tab.title}" class="cbp-mainlink-iicon" />{/if}
								{if !$tab.active_label}<span>{$tab.title|replace:'/n':'<br />'}</span>{/if}{if $tab.submenu_type} <i class="fa fa icon-angle-right cbp-submenu-aindicator"></i>{/if}
								{if !empty($tab.label)}<span class="label cbp-legend cbp-legend-vertical cbp-legend-main">{if !empty($tab.legend_icon)} <i class="fa fa {$tab.legend_icon} cbp-legend-icon"></i>{/if} {$tab.label}
								<span class="cbp-legend-arrow"></span></span>{/if}
						</a>
							{if $tab.submenu_type && !empty($tab.submenu_content)}
							<div class="cbp-hrsub-wrapper">
							<div class="cbp-hrsub col-xs-{$tab.submenu_width}">
								<div class="cbp-triangle-container"><div class="cbp-triangle-left"></div><div class="cbp-triangle-left-back"></div></div>
								<div class="cbp-hrsub-inner">

									{if $tab.submenu_type==1}
									<div class="container-xs-height cbp-tabs-container">
									<div class="row row-xs-height">
									<div class="col-xs-2 col-xs-height">
										<ul class="cbp-hrsub-tabs-names cbp-tabs-names">
											{if isset($tab.submenu_content_tabs)}
											{foreach $tab.submenu_content_tabs as $innertab name=innertabsnames}
											<li class="innertab-{$innertab->id} {if $smarty.foreach.innertabsnames.first}active{/if}"><a data-target="#{$innertab->id}-innertab-{$tab.id_tab}" {if $innertab->url_type != 2} href="{$innertab->url}" {/if}>
												{if $innertab->icon_type && !empty($innertab->icon_class)} <i class="fa fa {$innertab->icon_class} cbp-mainlink-icon"></i>{/if}
												{if !$innertab->icon_type && !empty($innertab->icon)} <img src="{$innertab->icon}" alt="{$innertab->title}" class="cbp-mainlink-iicon" />{/if}
												{$innertab->title}
												{if !empty($innertab->label)}<span class="label cbp-legend cbp-legend-inner">{if !empty($innertab->legend_icon)} <i class="fa fa {$innertab->legend_icon} cbp-legend-icon"></i>{/if} {$innertab->label}
												<span class="cbp-legend-arrow"></span></span>{/if}
											</a><i class="fa fa icon-angle-right cbp-submenu-it-indicator"></i><span class="cbp-inner-border-hider"></span></li>
											{/foreach}
											{/if}
										</ul>
									</div>

											{if isset($tab.submenu_content_tabs)}
											<div class="tab-content">
											{foreach $tab.submenu_content_tabs as $innertab name=innertabscontent}
											<div role="tabpanel" class="col-xs-10 col-xs-height tab-pane cbp-tab-pane {if $smarty.foreach.innertabscontent.first}active{/if} innertabcontent-{$innertab->id}"  id="{$innertab->id}-innertab-{$tab.id_tab}">

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

								</div>
							</div></div>
							{/if}
						</li>
						{/foreach}
					</ul>
				</nav>
