{**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div id="header_container" class="clearfix" style="background-color: {$themeConf['header_bg_color']}">
	<div class="container">
		<div class="row" id="page_header">
			{if isset($headerBlock)}
				{foreach from=$headerBlock key=block_key item=block_val}
					{if $block_val['block_name'] == 'logo'}
						<div id="header_logo" class="col-sm-{$block_val['width']}" style="background-color: {$block_val['block_bg_color']}; color: {$block_val['block_text_color']};">
							<a href="{$urls.base_url}" title="{$shop.name}">
								<img class="img-responsive" src="{if isset($wk_logo_url)}{$wk_logo_url}{else}{$logo_url}{/if}" alt="{$shop.name}" {if !isset($wk_logo_url)}{if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}"{/if}{/if}{if !isset($wk_logo_url)}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}"{/if}{/if} id="shop_logo" />
							</a>
						</div>
					{/if}
					{if $block_val['block_name'] == 'login'}
						<div id="header_login" class="col-sm-{$block_val['width']}" style="background-color: {$block_val['block_bg_color']}; color: {$block_val['block_text_color']};">
							<div class="row" style="margin-bottom: 5px;" id="language_div">
								<div class="btn-group" {if $loginBlockPosition.id_position == 2} style="float: right;"{else} style="float: left;" {/if}>
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										{$current_lang.name} <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										{foreach $active_languages as $language}
											<li><a href="{$link->getModuleLink('mpsellerwiselogin', 'sellerlogin', [], $smarty.const.NULL, $language.id_lang)}">{$language.name}</a></li>
										{/foreach}
									</ul>
								</div>
							</div>
							<div class="row">
								<form method="POST" action="{$link->getModuleLink('mpsellerwiselogin', 'customerformprocess')}" {if $loginBlockPosition.id_position == 2}class="pull-right"{/if} id="mp_login_form">
									<label class="text-capitalize margin-right-5" style="color: {$block_val['block_text_color']};">{l s='Seller Login' mod='mpsellerwiselogin'}</label>
									{hook h="displayMpHookBeforeLoginField"}

									<input type="email" placeholder="{l s='Email' mod='mpsellerwiselogin'}" name="email" id="login_email" class="wk_login_field margin-right-5">
									<input type="password" placeholder="{l s='Password' mod='mpsellerwiselogin'}" name="passwd" id="login_passwd" class="wk_login_field margin-right-5">
									
									{hook h="displayMpHookAfterLoginField"}

									<button type="submit" class="btn btn-warning text-capitalize" name="loginform" style="vertical-align: unset;">{l s='Login' mod='mpsellerwiselogin'}</button>
								</form>
							</div>
						</div>
					{/if}
				{/foreach}
			{/if}
		</div>
	</div>
</div>