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
<div class="panel product-tab" style="padding:10px; float:left;width:100%">

  <div class="form-group form-group-products form-group-stripepro">
<input type="hidden" name="stripepro_submit" value="1" />
   		<label class="control-label col-lg-4" for="simple_product">
        <span title="{l s='By enabling this, stripe will add the selected subscription on this product order.' mod='stripepro'}" class="label-tooltip" data-toggle="tooltip" title="">
			 {l s='Enable Stripe Recurring Payments' mod='stripepro'}:
             </span>
		</label>
		<div class="col-lg-5">
			 <span class="switch prestashop-switch fixed-width-lg">
				<input name="stripe_active" id="stripe_active_on" value="1" {if $stripe_active}checked="checked"{/if} type="radio">
				<label for="stripe_active_on" class="radioCheck">
					Yes
				</label>
				<input name="stripe_active" id="stripe_active_off" value="0" {if !$stripe_active}checked="checked"{/if} type="radio">
				<label for="stripe_active_off" class="radioCheck">
					No
				</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>
    <div class="form-group">
		<label class="control-label col-lg-4" for="simple_product">
        <span title="{l s='By enabling this, stripe will create a charge for this product price amount on this product order.' mod='stripepro'}" class="label-tooltip" data-toggle="tooltip" title="">
			 {l s='Enable Stripe Charge for this product' mod='stripepro'}:
             </span>
		</label>
		<div class="col-lg-5">
			 <span class="switch prestashop-switch fixed-width-lg">
				<input name="charge" id="stripe_charge_on" value="1" {if $stripe_charge}checked="checked"{/if} type="radio">
				<label for="stripe_charge_on" class="radioCheck">
					Yes
				</label>
				<input name="charge" id="stripe_charge_off" value="0" {if !$stripe_charge}checked="checked"{/if} type="radio">
				<label for="stripe_charge_off" class="radioCheck">
					No
				</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>
    
        <div class="form-group">
		<label class="control-label col-lg-4" for="simple_product">
        <span title="{l s='Selected Stripe Recurring Plan will be added to a customer on this product order.' mod='stripepro'}" class="label-tooltip" data-toggle="tooltip" title="">
			 {l s='Choose a Stripe Recurring plan' mod='stripepro'}:
             </span>
		</label>
		<div class="col-lg-5" style="float: left;">
			 <select name="stripe_plan" id="stripe_plan">
			  <option value="">{l s='Please select...' mod='stripepro'}</option>
               {foreach $stripe_plans as $plan}
                 <option value="{$plan.stripe_plan_id|escape:'htmlall':'UTF-8'}" {if $id_subscription_product==$plan.stripe_plan_id}selected{/if}>{$plan.name|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
		</select><br /><a href="https://dashboard.stripe.com/plans" target="_blank">{l s='Create a new Recurring Plan in your Stripe Account' mod='stripepro'}</a>
        <br />------------------{l s='OR' mod='stripepro'}------------------<br /><a href="{$quick_link|escape:'htmlall':'UTF-8'}">{l s='Sync all the Recurring Plans from your Stripe Account' mod='stripepro'}</a>
		</div>
	</div>
</div>