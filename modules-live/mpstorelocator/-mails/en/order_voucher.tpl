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

{if isset($list) && $list}
	{foreach $list as $voucher}
		<tr>
			<td style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px; text-align: center;">{$voucher['name']}</td>
			<td style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px; text-align: center;">{$voucher['value']}</td>
		</tr>
	{/foreach}
{else}
	<tr>
		<td style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px; text-align: center;">--</td>
		<td style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px; text-align: center;">--</td>
	</tr>
{/if}