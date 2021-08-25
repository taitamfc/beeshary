{**
* 2010-2017 Webkul
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

{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
<fieldset>
	<legend>Mass Product Update Detail</legend>
	<div class="container-massupdate">
		<div class="row_info">
			<div class="row_info_left">
				{l s='Mass Price Update On' mod='mpextrafield'}
			</div>
			<div class="row_info_right">
				{$mass_price_update_on_lang|escape:'htmlall':'UTF-8'}
			</div>
		</div>
		{if $category_info==-1}
			<div class="row_info">
			<div class="row_info_left">
				{l s='Update On' mod='mpextrafield'}
			</div>
			<div class="row_info_right">
				{l s='All Category' mod='mpextrafield'}
			</div>
		</div>
		{else}
			<div class="row_info">
				<div class="row_info_left">
					{l s='Category In Updation' mod='mpextrafield'}
				</div>
				<div class="row_info_right">
					{foreach $category_info as $cat_info}
						{$cat_info['name']|escape:'htmlall':'UTF-8'},
					{/foreach}
				</div>
			</div>
		{/if}
		<div class="row_info">
			<div class="row_info_left">
				{l s='Mass Price Update Type' mod='mpextrafield'}
			</div>
			<div class="row_info_right">
				{$mass_price_update_type_lang|escape:'htmlall':'UTF-8'}
			</div>
		</div>
		<div class="row_info">
			<div class="row_info_left">
				{l s='Mass Price Update Value' mod='mpextrafield'}
			</div>
			<div class="row_info_right">
				{$mass_price_update_value|escape:'htmlall':'UTF-8'}
			</div>
		</div>
		<div class="row_info">
			<div class="row_info_left">
				{l s='Created Date' mod='mpextrafield'}
			</div>
			<div class="row_info_right">
				{$update_on|escape:'htmlall':'UTF-8'}
			</div>
		</div>
		<div class="row_info">
			<div class="row_info_left">
				{l s='Is Revert Back' mod='mpextrafield'}
			</div>
			<div class="row_info_right">
				{$is_revert_back|escape:'htmlall':'UTF-8'}
			</div>
		</div>
		<div class="row_info">
			<div class="row_info_left">
				{l s='Is Revert Back Date' mod='mpextrafield'}
			</div>
			<div class="row_info_right">
				{$revert_back_date|escape:'htmlall':'UTF-8'}
			</div>
		</div>
	</div>
</fieldset>