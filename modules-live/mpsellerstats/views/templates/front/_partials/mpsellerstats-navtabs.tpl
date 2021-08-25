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

<div class="tabs wk-margin-bottom">
	<ul class="nav nav-tabs">
		<li class="nav-item {if $nav_logic=='seller_stats'}active{/if} wk-transform">
			<a class="nav-link {if $nav_logic=='seller_stats'}active{/if}" href="{$link->getModulelink('mpsellerstats', 'mpsellerstatsdetail')|escape:'htmlall':'UTF-8'}" >
				<i class="icon-tags"></i>
				{l s='Shop page statistics' mod='mpsellerstats'}
			</a>
		</li>

		<li class="nav-item {if $nav_logic=='product_stats'}active{/if}">
			<a class="nav-link {if $nav_logic=='product_stats'}active{/if} wk-transform" href="{$link->getModulelink('mpsellerstats', 'mpsellerproductstats')|escape:'htmlall':'UTF-8'}" >
				<i class="icon-gift"></i>
				{l s='Product statistics' mod='mpsellerstats'}
			</a>
		</li>

		<li class="nav-item {if $nav_logic=='search'}active{/if}">
			<a class="nav-link {if $nav_logic=='search'}active{/if} wk-transform" href="{$link->getModulelink('mpsellerstats', 'mpsearchkeyword')|escape:'htmlall':'UTF-8'}" >
				<i class="icon-gift"></i>
				{l s='Search keyword' mod='mpsellerstats'}
			</a>
		</li>

		{* <li class="nav-item {if $logic=='customer_config'}active{/if}">
			<a class="nav-link {if $logic=='customer_config'}active{/if}" href="{$link->getModulelink('mpauction', 'auctioncustomerconfig')|escape:'htmlall':'UTF-8'}" >
				<i class="icon-user"></i>
				{l s='Auction Customer Configuration' mod='mpauction'}
			</a>
		</li> *}
	</ul>
</div>