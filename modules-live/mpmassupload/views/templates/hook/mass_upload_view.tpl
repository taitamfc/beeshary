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

{if $mpmenu == 0}
	<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" title="{l s='Product Mass Upload' mod='marketplace'}" href="{$massuploadview}">
		<span class="link-item">
			<i class="material-icons">&#xE2C6;</i>
			{l s='Mass Upload' mod='mpmassupload'}
		</span>
	</a>
{else}
	<li {if $logic=='massupload'}class="menu_active"{/if}>
		<span>
			<a href="{$massuploadview}" title="{l s='Product Mass Upload' mod='mpproductfeatures'}">
				<i class="material-icons">&#xE2C6;</i>
				{l s='Mass upload' mod='mpproductfeatures'}
			</a>
		</span>
	</li>
{/if}