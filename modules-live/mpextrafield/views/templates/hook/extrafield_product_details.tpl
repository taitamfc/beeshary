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
<style type="text/css">
	.block-wk-sellerdetails{
		margin-bottom: 10px;
		margin-top:10px;
	}
	.sellerinfo .wk_row span {
		margin: 10px;
		width: 65%;
	}
	.sellerinfo .wk_row label {
		width: 30%;
		font-size: 15px;
	}
</style>
{if isset($extrafielddetail)}
<div class="block-wk-sellerdetails">
	<h3 class="h5 text-uppercase">{l s='Additional Information' mod='mpextrafield'}</h3>
	<div class="sellerinfo">
	{foreach $extrafielddetail as $extrafield}
	{if $extrafield.inputtype eq 1 && ((!empty($extrafield.default_value) && $extrafield.default_value|@count == 0) || ($extrafield.default_value|is_array && !empty($extrafield.default_value.$id_lang)))}
	<div class="wk_row">
		<label>{$extrafield.label_name|escape:'htmlall':'UTF-8'}</label>
		<span>{if $extrafield.default_value|is_array}{$extrafield.default_value.$id_lang|escape:'htmlall':'UTF-8'}{else}{$extrafield.default_value|escape:'htmlall':'UTF-8'}{/if}</span>
	</div>
	{/if}
	{if $extrafield.inputtype eq 2 && ((!empty($extrafield.default_value) && $extrafield.default_value|@count == 0) || ($extrafield.default_value|is_array && !empty($extrafield.default_value.$id_lang)))}
	<div class="wk_row">
		<label>{$extrafield.label_name|escape:'htmlall':'UTF-8'}</label>
		<span>{if $extrafield.default_value|is_array}{$extrafield.default_value.$id_lang|escape:'htmlall':'UTF-8'}{else}{$extrafield.default_value|escape:'htmlall':'UTF-8'}{/if}</span>
	</div>
	{/if}
	{if $extrafield.inputtype eq 3}
	{if $extrafield.multiple != 1}
	{foreach $extrafield['extfielddrop'] as $extfieldopt}
	{if !empty($extfieldopt.selected_value)}
	{assign var=select value="1"}
	{/if}
	{/foreach}
	{else if $extrafield.multiple == 1}
	{foreach $extrafield['extfielddrop'] as $extfieldopt}
	{assign var=select value=","|explode:$extfieldopt.selected_value}
	{if in_array($extfieldopt.id, $select)}
	{assign var=select value="1"}
	{/if}
	{/foreach}
	{/if}
	{if isset($select)}
	<div class="wk_row">
		<label>{$extrafield.label_name|escape:'htmlall':'UTF-8'}</label>
		{if $extrafield.multiple != 1}
		{foreach $extrafield['extfielddrop'] as $extfieldopt}
		{if $extfieldopt.selected_value == $extfieldopt.id}
		<span>{$extfieldopt['display_value']|escape:'htmlall':'UTF-8'}</span>
		{/if}
		{/foreach}
		{else if $extrafield.multiple == 1}
		<span>
		{foreach $extrafield['extfielddrop'] as $key => $extfieldopt}
		{assign var=select value=","|explode:$extfieldopt.selected_value}
			{if in_array($extfieldopt.id, $select)}
				{$extfieldopt['display_value']|escape:'htmlall':'UTF-8'}
					{if isset($extrafield['extfielddrop'][$key+1])},{/if}
			{/if}
		{/foreach}
		</span>
		{/if}
	</div>
	{/if}
	{/if}
	{if $extrafield.inputtype eq 4 && !empty($extfieldopt.selected_value)}
	<div class="wk_row">
		<label>{$extrafield.label_name|escape:'htmlall':'UTF-8'}</label>
		<span>
		{foreach $extrafield['extfieldcheck'] as $extfieldopt}
		{assign var=check value=","|explode:$extfieldopt.selected_value}
		{foreach $check as $key => $chk}
			{if $chk == $extfieldopt.id}
				{$extfieldopt.display_value|escape:'html':'UTF-8'}
				{if isset($check[$key+1])},{/if}
			{/if}
		{/foreach}
		{/foreach}
		</span>
	</div>
	{/if}
	{if $extrafield.inputtype eq 5}
	{if isset($extrafield.default_value) && $extrafield.value_id && $extrafield.default_value != '0'}
	<div class="wk_row">
		<label>{$extrafield.label_name|escape:'htmlall':'UTF-8'}</label>
		<span><a href="{$link->getModuleLink('mpextrafield', 'mediadownload',['id_attachment' => $extrafield['value_id']])|escape:'html':'UTF-8'}" class="btn btn-primary"><i class="material-icons">&#xE2C4;</i> {l s='Download file' mod='mpextrafield'}</a>
			<input type="hidden" value="{$extrafield.id|escape:'htmlall':'UTF-8'}" name="{if isset($extrafield.attribute_name)}{$extrafield.attribute_name|escape:'htmlall':'UTF-8'}{/if}"></span>
	</div>
	{/if}
	{/if}
	{if $extrafield.inputtype == 6 && !empty($extrafield.selected_value)}
	<div class="wk_row">
		<label>{$extrafield.label_name|escape:'htmlall':'UTF-8'}</label>
		{if $extrafield.selected_value == 1}
		<span>{$extrafield.extfieldradio.0.left_value|escape:'htmlall':'UTF-8'}</span>
		{else}
		<span>{$extrafield.extfieldradio.0.left_value|escape:'htmlall':'UTF-8'}</span>
		{/if}
	</div>
	{/if}
	{/foreach}
	</div>
</div>
{/if}