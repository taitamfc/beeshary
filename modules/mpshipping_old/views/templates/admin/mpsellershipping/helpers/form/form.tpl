{*
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

<div class="main_block panel">
{if isset($updateimpact) && $updateimpact}
	<div id="newbody"></div>
	<div id="impact_price_block">
		{include file="$self/../../views/templates/front/addimpactprice.tpl"}
	</div>

	<input type="hidden" name="mpshipping_id" id="mpshipping_id" value="{$mp_shipping_id|escape:'htmlall':'UTF-8'}">
	<input type="hidden" name="step4_shipping_method" value="{$shipping_method|escape:'htmlall':'UTF-8'}" class="step4_shipping_method" />
	<div class="left full row">
		<div class="left lable">
			{l s='Zone' mod='mpshipping'}
		</div>	
		<div class="left input_label col-lg-4">
			<select name="step4_zone" id="step4_zone">
				<option value="-1">{l s='Select Zone' mod='mpshipping'}</option>
			{foreach $zones as $zon}
				<option value="{$zon['id_zone']|escape:'htmlall':'UTF-8'}">{$zon['name']|escape:'htmlall':'UTF-8'}</option>
			{/foreach}
			</select>
		</div>
	</div>
	<div class="left full" id="country_container" style="display:none;">
		<div class="left full row">
			<div class="left lable">
				{l s='Country' mod='mpshipping'}
			</div>		
			<div class="left input_label col-lg-4">
				<select name="step4_country" id="step4_country">
					<option value="-1">{l s='Select country' mod='mpshipping'}</option>
				</select>
			</div>
		</div>
		<div class="left full" id="state_container" style="display:none;">
			<div class="left full row">
				<div class="left lable">
					{l s='State' mod='mpshipping'}
				</div>			
				<div class="left input_label col-lg-4">
					<select name="step4_state" id="step4_state">
						<option value="0">{l s='All state' mod='mpshipping'}</option>
					</select>
				</div>
			</div>
			<div class="left full row" style="text-align:center;">				
				<input type="button" class="btn btn-default button button-small" id="impactprice_button" value="{l s='Click to update impact price' mod='mpshipping'}">
			</div>
		</div>
	</div>
	<div class="left full text-center" id="loading_ajax"></div>
	<div style="clear:both;"></div>

	<div class="panel" style="margin-top: 10px;">
		<table class="table">
		<thead>
			<tr class="first last">
				<th style="width: 10%;">{l s='Id' mod='mpshipping'}</th>					
				<th style="width: 20%;">{l s='Zone' mod='mpshipping'}</th>
				<th style="width: 20%;">{l s='Country' mod='mpshipping'}</th>
				<th style="width: 20%;">{l s='State' mod='mpshipping'}</th>
				<th style="width: 20%;">{l s='Impact Price' mod='mpshipping'}</th>
				<th style="width: 20%;">
					{if $shipping_method == 2}
						{l s='Price Range' mod='mpshipping'}
					{else}
						{l s='Weight Range' mod='mpshipping'}
					{/if}
				</th>
				<th style="width: 10%;">{l s='Action' mod='mpshipping'}</th>
			</tr>
		</thead>
		<tbody>
			{if isset($impactprice_arr)}
				{foreach $impactprice_arr as $impactprice}
					<tr class="even">
						<td>{$impactprice.id|escape:'htmlall':'UTF-8'}</td>
						<td>{$impactprice.id_zone|escape:'htmlall':'UTF-8'}</td>
						<td>{$impactprice.id_country|escape:'htmlall':'UTF-8'}</td>
						<td>{$impactprice.id_state|escape:'htmlall':'UTF-8'}</td>
						<td>{$impactprice.impact_price|escape:'htmlall':'UTF-8'}</td>
						<td>
							{if $shipping_method == 2}
								{$impactprice.price_range|escape:'htmlall':'UTF-8'}
							{else}
								{$impactprice.weight_range|escape:'htmlall':'UTF-8'}
							{/if}
						</td>
						<td>								
							<a href="{$link->getAdminLink('AdminMpsellershipping')|escape:'htmlall':'UTF-8'}&id={$impactprice.mp_shipping_id|escape:'htmlall':'UTF-8'}&impact_id={$impactprice.id|escape:'htmlall':'UTF-8'}&deleteimpact=1" class="delete_impact" title="{l s='Delete' mod='mpshipping'}">
								<i class="icon-trash"></i>
							</a>
							
						</td>
					</tr>
				{/foreach}
			{else}
				<tr>
					<td colspan="6"><center>{l s='No Impact Price Yet' mod='mpshipping'}</center></td>
				</tr>
			{/if}
		</tbody>
		</table>
	</div>
{else}
	<div class="dashboard_content">		
		<div class="wk_right_col">
			<div class="shipping_list_container left">
				<input type="hidden" id="getshippingstep" name="getshippingstep" value="">
				<div class="shipping_add swMain"  id="carrier_wizard">
					<ul class="nbr_steps_4 anchor">
						<li style="width:33%;">
							<a class="steptab selected" isdone="1" rel="1" id="step_heading1">
								<label class="stepNumber">1</label>
								<span class="stepDesc">
									{l s='General settings' mod='mpshipping'}
									<br>
								</span>
							</a>
						</li>
						<li style="width:33%;">
							<a class="steptab disabled" rel="2" id="step_heading2">
								<label class="stepNumber">2</label>
								<span class="stepDesc">
									{l s='Shipping locations and costs' mod='mpshipping'}
									<br />
								</span>
							</a>
						</li>
						<li style="width:33%;">
							<a class="steptab disabled" rel="3" id="step_heading3">
								<label class="stepNumber">3</label>
								<span class="stepDesc">
									{l s='Size, weight, and group access' mod='mpshipping'}
									<br />
								</span>
							</a>
						</li>						
					</ul>
					<form role="form" id="addshippingmethod" class="defaultForm form-horizontal" enctype="multipart/form-data" method="post" action="">
					<div class="stepContainer left">
						{if isset($mp_shipping_id)}
							<input type="hidden" name="mp_shipping_id" id="mp_shipping_id" value="{$mp_shipping_id|escape:'html':'UTF-8'}">
						{/if}
						<input type="hidden" name="multilang" id="multilang" value="{$multi_lang|escape:'html':'UTF-8'}">
						<input type="hidden" name="current_lang" id="current_lang" value="{$current_lang.id_lang|escape:'html':'UTF-8'}">
						{if !isset($mp_shipping_id)}			
							<div class="form-group row">
								<label class="col-lg-12 required" style="font-weight: normal;">
									{l s='Choose Seller :' mod='mpshipping'}
								</label>
								<div class="col-lg-12">
									{if isset($customer_info)}
										<select name="seller_customer_id" id="seller_customer_id" class="fixed-width-xl">
											{foreach $customer_info as $cusinfo}
												<option value="{$cusinfo['id_customer']|escape:'html':'UTF-8'}" {if isset($smarty.post.seller_customer_id)}{if $smarty.post.seller_customer_id == $cusinfo['id_customer']}Selected="Selected"{/if}{/if}>
													{$cusinfo['business_email']|escape:'html':'UTF-8'}
												</option>
											{/foreach}
										</select>
									{else}
										<p>{l s='No seller found.' mod='mpshipping'}</p>
									{/if}
								</div>
							</div>
						{else}
							<input type="hidden" value="{$seller_customer_id|escape:'htmlall':'UTF-8'}" name="seller_customer_id" />
						{/if}
												
						<div id="step-1">
							{include file="$self/../../views/templates/front/addshippingstep1.tpl"}
						</div>
						<div id="step-2" style="display:none;">
							{include file="$self/../../views/templates/front/addshippingstep2.tpl"}
						</div>
						<div id="step-3" style="display:none;">
							{include file="$self/../../views/templates/front/addshippingstep3.tpl"}
						</div>
						
					</div>
					<div class="actionBar">
						<div class="msgBox">`
							<div class="content"></div>
								<a class="close" href="#">X</a>
							</div>
						<div class="loader">{l s='Loading' mod='mpshipping'}</div>
						<button type="submit" id="FinishButtonclick" name="FinishButtonclick" style="display:none;" class="buttonFinish">{l s='Finish' mod='mpshipping'}</button>
					</form>
						<div class="buttonFinish buttonDisabled" id="Finishdisablebutton" style="cursor:pointer;">{l s='Finish' mod='mpshipping'}</div>

						<div class="buttonNext buttonDisabled" id="Nextdisablebutton" style="display:none;cursor:pointer;">{l s='Next' mod='mpshipping'}</div>
						<div class="buttonNext" id="NextButtonclick" style="cursor:pointer;">{l s='Next' mod='mpshipping'}</div>

						<div class="buttonPrevious buttonDisabled" id="Previousdisablebutton" style="cursor:pointer;">{l s='Previous' mod='mpshipping'}</div>
						<div class="buttonPrevious" id="PreviousButtonclick" style="display:none;cursor:pointer;">{l s='Previous' mod='mpshipping'}</div>
					</div>
					
				</div>
			</div>
		</div>
	</div>
{/if}
<div style="clear:both;"></div>
</div>
