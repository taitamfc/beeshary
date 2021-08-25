{*
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
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

{if !isset($mptheme)}
	<style type="text/css">
		.pg-seller-block{
			text-align: center;
			padding:10px;
			background: white none repeat scroll 0 0;
		    box-shadow: 2px 2px 11px 0 rgba(0, 0, 0, 0.1);
		    margin-top: 24px;
		}
	</style>
{/if}

<div class="pg-seller-block">
	<h3>{l s='Store Locator' mod='mpstorelocator'}</h3>
	<div class="pg-seller-product">
		<a href="{$storeLink}" class="btn btn-primary">
			<span>{l s='Store' mod='mpstorelocator'}</span>
		</a>
	</div>
</div>