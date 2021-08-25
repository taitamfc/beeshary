{*
* 2010-2016 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{block name='content'}
{if isset($not_config)}
	<div class="alert alert-warning">{l s='This module is not activated by admin. Please contact to admin.' mod='mpsellermembership'}</div>
{else}
	{if isset($free_plan)}
		<div class="alert alert-success">{l s='Free membership plan has been activated successfully.' mod='mpsellermembership'}</div>
	{/if}
	{if isset($product_added)}
		<div class="alert alert-success">{l s='Membership plan has been successfully added in your shopping cart.' mod='mpsellermembership'}</div>
	{/if}
	{if isset($no_perm)}
		<div class="alert alert-danger">
			{l s='You donot have permission to show seller membership plans. You need to first request for markerplace seller.' mod='mpsellermembership'}
			<a style="margin:0px 5px;" class="btn btn-primary" href="{$link->getModuleLink('marketplace', 'sellerrequest')}">
				<span>{l s='click here' mod='mpsellermembership'}</span>
			</a>
			{l s=' for seller request.' mod='mpsellermembership'}
		</div>
	{else}
		<style type="text/css">
			.page-title, .plan, .mp_line{
				border-color: "{$title_bg_color}";
			}
			.page-title span{
				color: "{$title_bg_color}";
			}
			.plan_name{
				background-color: "{$title_bg_color}";
				color: "{$title_text_color}";
			}
			.plan_price{
				color: "{$title_bg_color}";
			}
		</style>
		<div class="plan_container">
			<div class="plan_row row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 page-title">
					<span>{l s='All Membership Plan' mod='mpsellermembership'}</span>
				</div>
			</div>
			{if isset($all_active_plan)}
				{if $total_pages != 1}
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align:right;">
						<div id="pagination" class="pagination clearfix">
							<ul class="pagination">
								<li id="pagination_previous" {if $current_page == 1} class="disabled pagination_previous" {else} class="pagination_previous" {/if}>
									<a {if $current_page == 1} href="#" {else} href="{$link->getModuleLink('mpsellermembership', 'mpmembershipplans', ['page' => $current_page - 1])}" {/if}>
										<i class="icon-chevron-left"></i>
										<b>{l s='Previous' mod='mpsellermembership'}</b>
									</a>
								</li>
								{for $page_no=1 to $total_pages} 
									<li {if $page_no == $current_page} class="active current" {/if}>
										<a href="{$link->getModuleLink('mpsellermembership', 'mpmembershipplans', ['page' => $page_no])}">
											<span>{$page_no}</span>
										</a>
									</li>
								{/for}
								<li id="pagination_next" {if $current_page == $total_pages} class="disabled pagination_next" {else} class="pagination_next" {/if}>
									<a rel="next" {if $current_page == $total_pages} href="#" {else} href="{$link->getModuleLink('mpsellermembership', 'mpmembershipplans', ['page' => $current_page + 1])}" {/if}>
										<b>{l s='Next' mod='mpsellermembership'}</b>
										<i class="icon-chevron-right"></i>
									</a>
								</li>
							</ul>
						</div>
					</div>
				{/if}
			{/if}
			{assign var=no_plan value=0}
			{if isset($free_no_products) AND $current_page == 1}
				{assign var=no_plan value=1}
				<div class="col-md-4">
					<div class="plan">
						<p class="plan_name">{l s='Free Plan' mod='mpsellermembership'}</p>
						<div class="plan_image">
							<img src="{$module_dir}mpsellermembership/views/img/default-plan.png" height="150" width="150" />
						</div>
						<div class="plan_price">
							<span>{l s='Free Plan' mod='mpsellermembership'}</span>
							<p class="mp_line"></p>
						</div>
						<div class="plan_products">
							<span>{l s='Add ' mod='mpsellermembership'}{$free_no_products}{l s=' Products' mod='mpsellermembership'}</span>
							<p class="mp_line"></p>
						</div>
						<div class="plan_products">
							<span>{$free_no_of_days}{l s=' Days' mod='mpsellermembership'}</span>
						</div>
						<div class="plan_button">
							<a class="btn btn-primary" id="free_plan_cart_btn" href="{$link->getModuleLink('mpsellermembership', 'mpmembershipplans', ['freeplancart' => 1])}">
								<span>{l s='Get It Now' mod='mpsellermembership'}</span>
							</a>
						</div>
					</div>
				</div>
			{/if}
			{if isset($all_active_plan)}
				{assign var=no_plan value=1}
				{foreach $all_active_plan as $plan}
					<div class="col-md-4">
						<div class="plan">
							<p class="plan_name">{$plan['plan_name']}</p>
							<div class="plan_image">
								<img src="{$plan['img']}" height="150" width="150" />
							</div>
							<div class="plan_price">
								<span>{$plan['plan_price']}</span>
								<p class="mp_line"></p>
							</div>
							<div class="plan_products">
								<span>{l s='Add ' mod='mpsellermembership'}{$plan['num_products_allow']}{l s=' Products' mod='mpsellermembership'}</span>
								<p class="mp_line"></p>
							</div>
							<div class="plan_products">
								<span>{$plan['plan_duration']}{l s=' Days' mod='mpsellermembership'}</span>
							</div>
							{assign var=hrf_val value=$link->getModuleLink('mpsellermembership', 'mpmembershipplans', ['addtocart' => 1, 'id_product' => $plan['id_product']])}
							{if isset($all_active_plan)}
								{if $total_pages != 1}
									{assign var=hrf_val value=$link->getModuleLink('mpsellermembership', 'mpmembershipplans', ['addtocart' => 1, 'id_product' => $plan['id_product'], 'page' => $current_page])}
								{/if}
							{/if}
							<div class="plan_button">
								<a class="btn btn-primary add-to-cart" href="{$hrf_val}">
									<i class="material-icons shopping-cart"></i>
									{l s='Buy Now' mod='mpsellermembership'}
								</a>
							</div>
						</div>
					</div>
				{/foreach}
			{/if}
			{if $no_plan == 0}
				<div class="alert alert-info no_plan">{l s='No plan created by admin' mod='mpsellermembership'}.</div>
			{/if}
		</div>
	{/if}
{/if}
{/block}