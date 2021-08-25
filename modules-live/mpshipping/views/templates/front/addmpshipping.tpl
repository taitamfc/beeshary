{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{block name='content'}
<div class="wk-mp-block">
	{hook h="displayMpMenu"}
	<div class="wk-mp-content">
		<div class="page-title" style="background-color:{$title_bg_color};">
			<span style="color:{$title_text_color};">
				{if isset($mp_shipping_id)}
					{l s='Update Carrier' mod='mpshipping'}
				{else}
					{l s='Add Carrier' mod='mpshipping'}
				{/if}
			</span>
		</div>
		<div class="wk-mp-right-column">
			<div class="shipping_list_container wk_product_list left">
				<div class="shipping_heading">
					<div class="right_links">
						<div class="home_link">
							<a class="btn btn-primary btn-sm" href="{$link->getModuleLink('mpshipping', 'mpshippinglist')|escape:'html':'UTF-8'}">
								<span><i class="material-icons">&#xE896;</i> {l s='Carrier list' mod='mpshipping'}</span>
							</a>
						</div>
					</div>
				</div>
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
							<a class="steptab {if isset($mp_shipping_id)} done {else} disabled {/if}" rel="2" id="step_heading2">
								<label class="stepNumber">2</label>
								<span class="stepDesc">
									{l s='Shipping locations and costs' mod='mpshipping'}
									<br />
								</span>
							</a>
						</li>
						<li style="width:33%;">
							<a class="steptab {if isset($mp_shipping_id)} done {else} disabled {/if}" rel="3" id="step_heading3">
								<label class="stepNumber">3</label>
								<span class="stepDesc">
									{l s='Size, weight and group access' mod='mpshipping'}
									<br />
								</span>
							</a>
						</li>
					</ul>
					<form role="form" id="addshippingmethod" class="defaultForm form-horizontal" enctype="multipart/form-data" method="post" action="{$mpshippingprocess|escape:'htmlall':'UTF-8'}">
					<div class="stepContainer left">
						{if isset($mp_shipping_id)}
							<input type="hidden" name="mp_shipping_id" id="mp_shipping_id" value="{$mp_shipping_id|escape:'html':'UTF-8'}">
						{/if}
						<input type="hidden" name="current_lang" id="current_lang" value="{$current_lang.id_lang|escape:'html':'UTF-8'}">
						<div id="step-1">
							{include file='module:mpshipping/views/templates/front/addshippingstep1.tpl'}
						</div>
						<div id="step-2" style="display:none;">
							{include file='module:mpshipping/views/templates/front/addshippingstep2.tpl'}
						</div>
						<div id="step-3" style="display:none;">
							{include file='module:mpshipping/views/templates/front/addshippingstep3.tpl'}
						</div>
					</div>
					<div class="actionBar">
						<div class="msgBox">`
							<div class="content"></div>
								<a class="close" href="#">X</a>
							</div>
						<div class="loader">{l s='Loading' mod='mpshipping'}</div>
						<button type="submit" id="FinishButtonclick" style="display:none;" class="buttonFinish">{l s='Finish' mod='mpshipping'}</button>
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
</div>
{/block}
