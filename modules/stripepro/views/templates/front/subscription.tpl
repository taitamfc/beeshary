{*
* 2015-2016 NTS
*
* DISCLAIMER
*
* You are NOT allowed to modify the software. 
* It is also not legal to do any changes to the software and distribute it in your own name / brand. 
*
* @author NTS
* @copyright  2015-2016 NTS
* @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
* International Registered Trademark & Property of NTS
*}
{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='My Subscriptions' mod='stripepro'} ({count($subscriptions)|escape:'htmlall':'UTF-8'})
{/block}

{block name='page_content'}
	{if isset($confirmation) && $confirmation}
		<p class="alert alert-success">{l s='Subscription has been cancelled successfully.' mod='stripepro'}</p>
	{/if}

	{if count($subscriptions)==0}
		<p>{l s='You did not subscribe any product yet.' mod='stripepro'}</p>
	{else}
		
				{foreach from=$subscriptions item=sub name=subscriptions}
                
             <form action="" method="post" class="col-md-12" style="box-shadow: 2px 2px 11px 0 rgba(0,0,0,.1);background: #fff;padding: 10px;margin-bottom: 5px;">
              
              <div class="col-md-2 store-picture">
                <a class="pull-right" href="{$sub['p_link']|escape:'html':'UTF-8'}" title="{$sub['p_name']|escape:'html':'UTF-8'}" itemprop="url">
							<img class="replace-2x img-responsive" src="{$link->getImageLink($sub['p_link_rewrite'], $sub['cover'], 'home_default')|escape:'html':'UTF-8'}" itemprop="image" width="153" /></a>
              </div>
              <div class="col-md-5">
                <table cellpadding="0" cellspacing="0">
                <tr><td style="padding:0px 10px;width:150px; text-align:right">{l s='Product' mod='stripepro'}:</td><td style="padding:0px">&nbsp;<b>{$sub['p_name']|escape:'htmlall':'UTF-8'}</td></tr>
                <tr><td style="padding:0px 10px;width:150px; text-align:right">{l s='Subscription ID' mod='stripepro'}:</td><td style="padding:0px">&nbsp;<b>{$sub['stripe_subscription_id']|escape:'htmlall':'UTF-8'}</td></tr>
                <tr><td style="padding:0px 10px; text-align:right">{l s='Plan' mod='stripepro'}:</td><td style="color:brown;padding:0px">&nbsp;<b>{if $sub['plan']==''}{$sub['stripe_plan_id']|escape:'htmlall':'UTF-8'}{else}{$sub['plan']|escape:'htmlall':'UTF-8'}{/if}</b></td></tr>
                <tr><td style="padding:0px 10px; text-align:right">{l s='Trial Period' mod='stripepro'}:</td><td style="padding:0px">&nbsp;<b>{$sub['trial_period_days']|escape:'htmlall':'UTF-8'} {l s='Days' mod='stripepro'}</td></tr>
                <tr><td style="padding:0px 10px; text-align:right">{l s='Quantity' mod='stripepro'}:</td><td style="padding:0px">&nbsp;<b>{$sub['quantity']|escape:'htmlall':'UTF-8'}</b></td></tr></table>
                </div>
                <div class="col-md-5">
                <table cellpadding="0" cellspacing="0">
                <tr><td style="padding:0px 10px; text-align:right">{l s='Current Period' mod='stripepro'}:</td><td style="padding:0px">&nbsp;<b>{$sub['current_period_start']|escape:'htmlall':'UTF-8'|date_format} 
                {l s='to' mod='stripepro'} {$sub['current_period_end']|escape:'htmlall':'UTF-8'|date_format}</b></td></tr>
                <tr><td style="padding:0px 10px; text-align:right">{l s='Started on' mod='stripepro'}:</td><td style="padding:0px">&nbsp;<b>{$sub['start']|escape:'htmlall':'UTF-8'|date_format}</b></td></tr>
                 {if $sub['cancel_at_period_end']==1}
                <tr><td style="padding:0px 10px; text-align:right">{l s='Canceled at' mod='stripepro'}:</td><td style="padding:0px">&nbsp;<b>{$sub['canceled_at']|escape:'htmlall':'UTF-8'|date_format}</b></td></tr>
                <tr><td style="padding:0px 10px; text-align:right">{l s='Switch off date' mod='stripepro'}:</td><td style="padding:0px">&nbsp;<b>{$sub['current_period_end']|escape:'htmlall':'UTF-8'|date_format}</b></td></tr>
                {/if}
                <tr><td style="padding:0px 10px; text-align:right">{l s='Status' mod='stripepro'}:</td><td style="padding:0px;">&nbsp;<b style="color:{if $sub['status']=='active'}#71B238{else}{if $sub['status']=='canceled'}red{else}orange{/if}{/if}">{$sub['status']|escape:'htmlall':'UTF-8'|upper}</b>
                {if $sub['cancel_at_period_end']==1}{l s='-- Will cancel at period end' mod='stripepro'}{/if}</td></tr>
                <tr><td colspan="2">&nbsp;</td></tr></table>
                {if $allow_cancel}
                <input type="hidden" name="stripe_subscription_id" value="{$sub['stripe_subscription_id']|escape:'htmlall':'UTF-8'}">
                 {if $sub['cancel_at_period_end']==0}
                <button type="submit" class="button btn btn-primary" style="float:right;{if $sub['status']=='canceled'}'visibility: hidden'{/if}" onclick="return confirm('{l s='Do you want to Cancel this Subscription?' mod='stripepro'}');" name="SubmitCancelSub">{l s='Cancel Now' mod='stripepro'}</button>
                <button type="submit" style="margin-right:10px;" class="button btn btn-default pull-right" style="{if $sub['status']=='canceled'}'visibility: hidden'{/if}" onclick="return confirm('{l s='Do you want to Cancel this Subscription?' mod='stripepro'}');" name="SubmitCancelSubAtPeriodEnd">{l s='Auto Cancel at period end' mod='stripepro'}</button>
                 {/if}
                {else}
                <a class="button btn pull-right" href="{$link->getPageLink('contact')|escape:'htmlall':'UTF-8'}" style="float:right;">{l s='Contact Support to cancel' mod='stripepro'}</a>
                {/if}
                </div>
                </form>
                
				{/foreach}
	{/if}
{/block}