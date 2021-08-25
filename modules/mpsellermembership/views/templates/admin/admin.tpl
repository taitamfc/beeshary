{*
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($errors)}
	<div class="alert alert-danger">
		{foreach $errors as $err}
			{$err}<br />
		{/foreach}
	</div>
{/if}
{if isset($success)}
	<div class="alert alert-success">{$success}</div>
{/if}

<div class="panel">
	<div class="panel-heading">
		<i class="icon-book"></i>
		{l s='Documentation' mod='mpsellermembership'}
	</div>

	<div class="panel-body">
		<div class="alert alert-info">
			<p>{l s='Refer the' mod='mpsellermembership'} <a href="https://webkul.com/blog/prestashop-marketplace-membership-addon/" target="_blank"> {l s='User Guide' mod='mpsellermembership'} <i class="icon-external-link-sign"></i></a> {l s='to checkout the complete workflow of the Prestashop Marketplace Membership module.' mod='mpsellermembership'}</p>
		</div>
	</div>
</div>

<div class="panel">
	<div class="panel-heading">
		<i class="icon-cogs"></i>
		{l s='Cron Setting' mod='mpsellermembership'}
	</div>

	<div class="panel-body">
		<div class="alert alert-info">
			<p>{l s='To send the expiry and warning mail to the seller about plan and to disable product after expire the plan' mod='mpsellermembership'}</p>
			<br />
			<p>{l s='Please set the CRON, insert the following line in your cron tasks manager:' mod='mpsellermembership'}</p>
			<br />
			<ul class="list-unstyled">
				<li><code>{$cron_url|escape:'html':'UTF-8'}</code></li>
			</ul>
		</div>
	</div>
</div>

{if $is_module_configured == 0}
	<div class="alert alert-danger">{l s='Currently this module is not active because you don\'t configure this module. For activating this module you need to configure this module' mod='mpsellermembership'}</div>
{/if}

<form action="{$action_url|escape:'quotes':'UTF-8'}" method="post" class="defaultForm form-horizontal">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Configuration' mod='mpsellermembership'}
		</div>

		<div class="panel-body">
			{if $is_module_configured == 0}
				<div class="form-group">
					<label class="control-label col-lg-3 required" for="old_seller_days">
						<span>{l s='Remaining days to buy plan for existing sellers' mod='mpsellermembership'} </span>
					</label>
					<div class="col-lg-2">
						<input  class="form-control" type="text" name="old_seller_days" {if isset($old_seller_days)} value="{$old_seller_days|escape:'html':'UTF-8'}" {/if} />
					</div>
				</div>
			{/if}

			<input type="hidden" value="{$is_module_configured}" name="is_module_configured">
			<div class="form-group">
				<label class="control-label col-lg-3 required" for="expire_mail">
					<span> {l s='Do you want to send mail when membership plan will be expired' mod='mpsellermembership'} </span>
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input id="expire_mail_on" type="radio" value="1" name="expire_mail" {if isset($expire_mail)}{if $expire_mail == 1} checked="checked" {/if}{/if} />
						<label for="expire_mail_on">{l s='Yes' mod='mpsellermembership'}</label>
						<input id="expire_mail_off" type="radio" value="0" name="expire_mail" {if isset($expire_mail)}{if $expire_mail == 0} checked="checked" {/if}{/if} />
						<label for="expire_mail_off">{l s='No' mod='mpsellermembership'}</label>
						<a class="slide-button btn"></a>
					</span>
					<div class="help-block">{l s='CRON must be set to send expired mail' mod='mpsellermembership'}</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3 required" for="warning_mail">
					<span> {l s='Do you want to send warning mail' mod='mpsellermembership'} </span>
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input id="warning_mail_on" type="radio" value="1" name="warning_mail" {if isset($warning_mail)}{if $warning_mail == 1} checked="checked" {/if}{/if} />
						<label for="warning_mail_on">{l s='Yes' mod='mpsellermembership'}</label>
						<input id="warning_mail_off" type="radio" value="0" name="warning_mail" {if isset($warning_mail)}{if $warning_mail == 0} checked="checked" {/if}{/if} />
						<label for="warning_mail_off">{l s='No' mod='mpsellermembership'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

			<div class="form-group" id="warning_days_div">
				<label class="control-label col-lg-3 required" for="mail_warn_days">
					<span>{l s='Number of days left to upgrade plan, when warning mail will be send' mod='mpsellermembership'} </span>
				</label>
				<div class="col-lg-2 input-group">
					<input  class="form-control" type="text" name="mail_warn_days" {if isset($mail_warn_days)} value="{$mail_warn_days|escape:'html':'UTF-8'}" {/if} />
					<span class="input-group-addon">{l s='days' mod='mpsellermembership'}</span>
				</div>
				<div class="help-block">{l s='CRON must be set to send warning mail' mod='mpsellermembership'}</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3 required" for="display_no_of_plan">
					<span>{l s='Display Number of plans on plan page' mod='mpsellermembership'} </span>
				</label>
				<div class="col-lg-2 input-group">
					<input  class="form-control" type="text" name="display_no_of_plan" {if isset($display_no_of_plan)} value="{$display_no_of_plan|escape:'html':'UTF-8'}" {/if} />
					<span class="input-group-addon"> * 3</span>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3 required" for="warn_num_of_days">
					<span>{l s='Number of days left to upgrade plan, when warning will be displayed' mod='mpsellermembership'} </span>
				</label>
				<div class="col-lg-2 input-group">
					<input  class="form-control" type="text" name="warn_num_of_days" {if isset($warn_num_of_days)} value="{$warn_num_of_days|escape:'html':'UTF-8'}" {/if} />
					<span class="input-group-addon">{l s='days' mod='mpsellermembership'}</span>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3 required" for="warn_num_of_products">
					<span>{l s='Number of products left to upgrade plan, when warning will be displayed' mod='mpsellermembership'} </span>
				</label>
				<div class="col-lg-2 input-group">
					<input  class="form-control" type="text" name="warn_num_of_products" {if isset($warn_num_of_products)} value="{$warn_num_of_products|escape:'html':'UTF-8'}" {/if} />
					<span class="input-group-addon">{l s='products' mod='mpsellermembership'}</span>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3 required" for="free_plan_display">
					<span> {l s='Display free membership plan' mod='mpsellermembership'} </span>
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input id="free_plan_display_on" type="radio" value="1" name="free_plan_display" {if isset($free_plan_display)}{if $free_plan_display == 1} checked="checked" {/if}{/if} />
						<label for="free_plan_display_on">{l s='Yes' mod='mpsellermembership'}</label>
						<input id="free_plan_display_off" type="radio" value="0" name="free_plan_display" {if isset($free_plan_display)}{if $free_plan_display == 0} checked="checked" {/if}{/if} />
						<label for="free_plan_display_off">{l s='No' mod='mpsellermembership'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

			<div class="form-group" id="num_of_products_div" style="display:none;">
				<label class="control-label col-lg-3 required" for="num_of_products">
					<span>{l s='Number of products' mod='mpsellermembership'} </span>
				</label>
				<div class="col-lg-2">
					<input  class="form-control" type="text" name="num_of_products" {if isset($num_of_products)} value="{$num_of_products|escape:'html':'UTF-8'}" {/if} />
				</div>
			</div>

			<div class="form-group" id="plan_duration_div" style="display:none;">
				<label class="control-label col-lg-3 required"> {l s='Duration' mod='mpsellermembership'} </label>
				<div class="col-lg-2">
					<input type="text" class="form-control" name="plan_duration" {if isset($plan_duration)} value="{$plan_duration|escape:'html':'UTF-8'}" {/if} />
				</div>
				<div class="col-lg-2">
					<select name="plan_duration_type">
						<option value="1" {if isset($plan_duration_type)} {if $plan_duration_type == "1"} selected="selected" {/if}{/if} >{l s='Days' mod='mpsellermembership'}</option>
						<option value="30" {if isset($plan_duration_type)} {if $plan_duration_type == "30"} selected="selected" {/if}{/if} >{l s='Months' mod='mpsellermembership'}</option>
						<option value="360" {if isset($plan_duration_type)} {if $plan_duration_type == "360"} selected="selected" {/if}{/if} >{l s='Years' mod='mpsellermembership'}</option>
					</select>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="submitMembership" id="submitMembership" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='mpsellermembership'}
			</button>
		</div>
	</div>
</form>