{*
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="box-account box-recent">
	<div class="box-head">
		<h2><i class="icon-envelope"></i> {l s='Messages' mod='marketplace'} ({if isset($messages)}{count($messages)}{else}0{/if})</h2>
		<div class="wk_border_line"></div>
	</div>
	
	<div class="box-content">
		{if isset($messages)}
			<table class="table">
				<thead>
					<tr>
						<th>{l s='Customer' mod='marketplace'}</th>
						<th>{l s='Email' mod='marketplace'}</th>
						<th>{l s='Messages' mod='marketplace'}</th>
						<th>{l s='Last message' mod='marketplace'}</th>
					</tr>
				</thead>
				<tbody>
					{foreach $messages as $msg}
						<tr>
							<td>{$msg.firstname} {$msg.lastname}</td>
							<td>{$msg.email}</td>
							<td>{$msg.message}</td>
							<td>{dateFormat date=$msg.date_add full=1}</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		{else}
			<div class="alert alert-info">
				{l s='No Messages' mod='marketplace'}
			</div>
		{/if}
	</div>
</div>