{**
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($success)}
	{$success}
{/if}
<form action="{$actionUrl}" method="post" class="defaultForm form-horizontal">
	<div class="panel">
    	<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Twilio Setting' mod='mpmessaging'}
		</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3 required">
					{l s='Twilio Account SID' mod='mpmessaging'}
				</label>
				<div class="col-lg-9">
					<div class="form-group">
						<input type="text" value="{$twilioAccId}" name="twilioAccId" />
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">
					{l s='Twilio Account Token' mod='mpmessaging'}
				</label>
				<div class="col-lg-9">
					<div class="form-group">
						<input type="text" value="{$twilioAccPassword}" name="twilioAccPassword" />
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">
					{l s='Twilio Number' mod='mpmessaging'}
				</label>
				<div class="col-lg-9">
					<div class="form-group">
						<input type="text" value="{$twilioAccNumber}" name="twilioAccNumber" />
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button id="submittwilio" class="btn btn-default pull-right" name="submittwilio" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='mpmessaging'}
			</button>
		</div>
	</div>
</form>