{*
* 2010-2019 Webkul
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
*  @author Webkul IN <support@webkul.com>
*  @copyright  2010-2019 Webkul IN
*}
<table style="width: 100%;">
<tr>
	<td style="width: 50%;">
		{if $logo_path}
			<img src="{$logo_path|escape:'htmlall':'UTF-8'}" style="width:{$width_logo|escape:'htmlall':'UTF-8'}px; height:{$height_logo|escape:'htmlall':'UTF-8'}px;" />
		{/if}
	</td>
	<td style="width: 50%; text-align: right;">
		<table style="width: 100%;">
			<tr>
				<td style="font-weight: bold; font-size: 14pt; color: #444; width: 100%;">{if isset($header)}{$header|escape:'html':'UTF-8'|upper}{/if}</td>
			</tr>
			<tr>
				<td style="font-size: 14px; color: #9E9F9E;">{dateFormat date=$date|escape:'htmlall':'UTF-8' full=0}</td>
			</tr>
		</table>
	</td>
</tr>
</table>

