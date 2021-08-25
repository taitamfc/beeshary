{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<a class="assign_shipping" href="#assign_shipping_form">
	<button class="btn btn-primary btn-sm" type="button">
		<i class="material-icons">&#xE896;</i>
		{l s='Assign Shipping' mod='mpshipping'}
	</button>
</a>

<div class="modal fade" id="assign_shipping_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-body">
		  	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='mpshipping'}</span></button>
			<div>
				<div class="headingclass">{l s='Note: Previously applied shipping will get unselected and the shipping methods selected by you will get assigned to all the products.' mod='mpshipping'}</div>
				<form method="post" action="{$ajax_link|escape:'htmlall':'UTF-8'}" id="shipping_form">
					{foreach $shipping_method as $shipping_data}
						<input type="hidden" value="{$mp_id_seller|escape:'htmlall':'UTF-8'}" name="mp_id_seller">
						<div class="wk_shipping_data">
							<div class="wk_shipping_name">
								<input type="checkbox" id="shipping_method_{$shipping_data.id_carrier|escape:'htmlall':'UTF-8'}" name="shipping_method[]" value="{$shipping_data.id_carrier|escape:'htmlall':'UTF-8'}">
							</div>
							<div style="float:left;">
								<label for="shipping_method_{$shipping_data.id_carrier|escape:'htmlall':'UTF-8'}"  style="font-weight: normal;">{$shipping_data.mp_shipping_name|escape:'htmlall':'UTF-8'}</label>
							</div>
							<div style="clear:both;"></div>
						</div>
					{/foreach}
					<a class="btn btn-primary btn-sm" id="assign" style="margin-top:10px;">
						<span>{l s='Submit' mod='mpshipping'}</span>
					</a>
				</form>
			</div>
		  </div>
		</div>
	</div>
</div>
