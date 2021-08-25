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

{extends file=$layout}
{block name='content'}
{if $logged}
	<div class="wk-mp-block">
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color};">
				<span style="color:{$title_text_color};">
					{l s='Attribute Generator' mod='marketplace'}
				</span>
			</div>
			<div class="wk-mp-right-column">
				<p style="margin:bottom:25px;">
					<a href="{$link->getModuleLink('marketplace', 'updateproduct',['id_mp_product'=>$id_mp_product])}" class="btn btn-link wk_padding_none">
				        <i class="material-icons">&#xE5C4;</i>
				        <span>{l s='Back to product' mod='marketplace'}</span>
				    </a>
				</p>
				{block name='generate-combination-fields'}
					{include file='module:marketplace/views/templates/front/product/combination/_partials/generate-combination-fields.tpl'}
				{/block}
			</div>
		</div>
	</div>
{else}
	<div class="alert alert-danger">
		{l s='You are logged out. Please login to generate combination.' mod='marketplace'}</span>
	</div>
{/if}
{/block}
