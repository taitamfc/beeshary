{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div id="wk_search_wrapper" class="col-md-12 col-sm-12 search-widget">
	<form method="POST" action="{$link->getModuleLink('mpsearchblock', 'formsearch')}" id="wk_search_form">
		<input type="text" class="form-control" id="wk_search_box" name="top_search_box" placeholder="{l s='Search' mod='mpsearchblock'}" autocomplete="off" required>
		<button type="submit" id="wk_search_btn" name="form_search">
			<i class="material-icons">&#xE8B6;</i>
		</button>
		<div class="wk_search_sugg_wrapper">
			<div class="col-sm-7 wk_search_sugg_cont">
				<p class="text-capitalize wk_search_heading font_style">{l s='Choose Any Suggestion' mod='mpsearchblock'}</p>
				<hr class="hr_style">

				<div class="col-sm-12 wk_search_cont" id="wk_sugg_prod_cont">
					<p class="text-capitalize font_style">{l s='Products' mod='mpsearchblock'}<span class='pull-right more_opt'></span></p>
					<ul class="font_style" id="wk_sugg_product"></ul>
				</div>

				<div class="col-sm-12 wk_search_cont" id="wk_sugg_shop_cont">
					<p class="text-capitalize font_style">{l s='Shops' mod='mpsearchblock'}<span class='pull-right more_opt'></span></p>
					<ul class="font_style" id="wk_sugg_shop"></ul>
				</div>

				<div class="col-sm-12 wk_search_cont" id="wk_sugg_seller_cont">
					<p class="text-capitalize font_style">{l s='Sellers' mod='mpsearchblock'}<span class='pull-right more_opt'></span></p>
					<ul class="font_style" id="wk_sugg_seller"></ul>
				</div>

				{* <div class="col-sm-12 wk_search_cont" id="wk_sugg_location_cont">
					<p class="text-capitalize font_style">{l s='Shop Locations' mod='mpsearchblock'}<span class='pull-right more_opt'></span></p>
					<ul class="font_style" id="wk_sugg_location"></ul>
				</div> *}

				<div class="col-sm-12 wk_search_cont" id="wk_sugg_category_cont">
					<p class="text-capitalize font_style">{l s='Categories' mod='mpsearchblock'}<span class='pull-right more_opt'></span></p>
					<ul class="font_style" id="wk_sugg_category"></ul>
				</div>

				{* <div class="col-sm-12 wk_search_cont" id="wk_sugg_profession_cont">
					<p class="text-capitalize font_style">{l s='Profession' mod='mpsearchblock'}<span class='pull-right more_opt'></span></p>
					<ul class="font_style" id="wk_sugg_profession"></ul>
				</div> *}
			</div>

			<div class="col-sm-5">
				<p class="text-capitalize wk_search_heading font_style">{l s='Type' mod='mpsearchblock'}</p>
				<hr class="hr_style">

				<div class="col-sm-12 wk_search_cat_cont">
					<label>
						<input type="radio" name="search_type" value="1" class="wk_search_type" checked="checked">
						<span class="text-capitalize">{l s='All' mod='mpsearchblock'}</span>
					</label>

					<label>
						<input type="radio" name="search_type" value="2" class="wk_search_type">
						<span class="text-capitalize">{l s='Products' mod='mpsearchblock'}</span>
					</label>

					<label>
						<input type="radio" name="search_type" value="3" class="wk_search_type">
						<span class="text-capitalize">{l s='Shops' mod='mpsearchblock'}</span>
					</label>

					<label>
						<input type="radio" name="search_type" value="4" class="wk_search_type">
						<span class="text-capitalize">{l s='Sellers' mod='mpsearchblock'}</span>
					</label>

					{* <label>
						<input type="radio" name="search_type" value="5" class="wk_search_type">
						<span class="text-capitalize">{l s='Shop Locations' mod='mpsearchblock'}</span>
					</label> *}
					<label>
						<input type="radio" name="search_type" value="6" class="wk_search_type">
						<span class="text-capitalize">{l s='Categories' mod='mpsearchblock'}</span>
					</label>
					{* <label>
						<input type="radio" name="search_type" value="7" class="wk_search_type">
						<span class="text-capitalize">{l s='Profession' mod='mpsearchblock'}</span>
					</label> *}
				</div>
			</div>
		</div>
	</form>
</div>