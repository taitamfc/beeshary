{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($wk_ad_footer)}
	<li>
		<a href="{$sellerLink}">{l s='Become a Seller' mod='marketplace'}</a>
    </li>
{else if isset($wk_ad_nav)}
<div class="mp_advertise">
	<a href="{$sellerLink}">{l s='Become a Seller' mod='marketplace'}</a>
</div>
{else if isset($wk_ad_footer_pop) && !isset($no_advertisement) && !isset($content_only)}
	{if !$cms_content_only}
		<footer class='wk_ad_footer'>
			<div class="box">
				<a class="boxclose" id="wk_ad_close"></a>
				<div class="wk_ad_content">
					<span>{l s='Want to sell products on shop' mod='marketplace'}</span>
					<a class="btn btn-primary btn-sm" href="{$sellerLink}">
						{l s='Become a Seller' mod='marketplace'}
					</a>
				</div>
			</div>
		</footer>
	{/if}
{/if}