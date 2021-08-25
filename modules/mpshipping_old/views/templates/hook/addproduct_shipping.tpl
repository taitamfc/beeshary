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

<div id="mp_product_shipping_tab">
	<img src="{$mp_module_dir}mpshipping/views/img/loader.gif" id="ajax_loader" style="display: none;z-index: 10000;position: absolute;top: 20%;left: 50%;" />
	<div id="mp_shipping_method_block">
		{if !empty($mp_shipping_data)}
		{foreach $mp_shipping_data as $shipping_data}
			<div>
				<div class="checkbox">
					<label class="">
						{if isset($mp_product_id)}
							<input style="margin-right: 5px;" type="checkbox"
							value="{$shipping_data.id_carrier}" name="carriers[]"
							id="carriers_{$shipping_data.id_carrier}"
							{if isset($mp_shipping_id_map)}
								{if in_array($shipping_data.id_carrier, $mp_shipping_id_map)}
									checked="checked"
								{/if}
							{/if}>
						{else}
							<input style="margin-right: 5px;" type="checkbox"
							value="{$shipping_data.id_carrier}" name="carriers[]"
							id="carriers_{$shipping_data.id_carrier}"
							{if isset($shipping_data.is_default_shipping) && $shipping_data.is_default_shipping == 1}
							checked="checked"{/if}>
						{/if}{$shipping_data.mp_shipping_name}
					</label>
			    </div>
		    </div>
		{/foreach}
		{else}
			<div class="alert alert-warning">
				{l s='There is no active shipping method' mod='mpshipping'}
			</div>
		{/if}
	</div>
	{if !empty($mp_shipping_data)}
	<div class="form-group wk_carrier">
		<div role="alert" class="clearfix alert alert-warning">
			{if !isset($backendController)}
				<i class="material-icons wkmp_icon">info_outline</i>
			{/if}
			<span>
				{l s='If No Carrier selected, Admin default shipping will apply on this product.' mod='mpshipping'}
			</span>
			<br>
			{if isset($allCarrierNames) && $allCarrierNames}
				<ul style="list-style: disc; {if isset($backendController)}padding-left: 10px;{else}padding-left: 35px;{/if}">
					{foreach $allCarrierNames as $carrierName}
						<li>{$carrierName}</li>
					{/foreach}
				</ul>
			{/if}
		</div>
	</div>
	{/if}
</div>

{*This code is working only on Backend*}
{if isset($is_admin_controller) && $is_admin_controller == 1}
<script type="text/javascript">
	var admin_ajax_link = "{$link->getModuleLink('mpshipping', 'adminajax')}";
	var no_shipping = "{l s='There is no active shipping method ' js=1 mod='mpshipping'}";
	var is_admin_controller = "{$is_admin_controller}";
	$(document).ready(function(){
		if (is_admin_controller == 1) {
			var selected_id_customer = $("[name='shop_customer']").val();
			function getShippingMethodByIdCustomer(selected_id_customer) {
				$('body').css('opacity', '0.5');
				$('#ajax_loader').css('display', 'block');
				$.ajax({
					url: admin_ajax_link,
					type: 'POST',
					data: {
						selected_id_customer: selected_id_customer
					},
					dataType: 'json',
					success: function(data) {
						$('body').css('opacity', '1');
						$('#ajax_loader').css('display', 'none');
						$('#mp_shipping_method_block').empty();
						if (data.status == 1) {
							$.each(data.info, function(index, mp_shipping_data) {
								if (mp_shipping_data.is_default_shipping == 1) {
									$('#mp_shipping_method_block').append("<div style='width:5%;float:left;'><input type='checkbox' name='carriers[]' id='carriers_"+mp_shipping_data.id_carrier+"' value='"+mp_shipping_data.id_carrier+"' checked='checked'></div><div style='float:left;'><label for='carriers_"+mp_shipping_data.id_carrier+"' style='font-weight: normal;'>"+mp_shipping_data.mp_shipping_name+"</label></div><div style='clear:both;'></div>");
								} else {
									$('#mp_shipping_method_block').append("<div style='width:5%;float:left;'><input type='checkbox' name='carriers[]' id='carriers_"+mp_shipping_data.id_carrier+"' value='"+mp_shipping_data.id_carrier+"'></div><div style='float:left;'><label for='carriers_"+mp_shipping_data.id_carrier+"' style='font-weight: normal;'>"+mp_shipping_data.mp_shipping_name+"</label></div><div style='clear:both;'></div>");
								}
							});
						} else {
							$('#mp_shipping_method_block').append("<div class='alert alert-warning'>"+no_shipping+"</div>");
						}
					}, fail: function(data) {
						$('body').css('opacity', '1');
						$('#ajax_loader').css('display', 'none');
						$('#mp_shipping_method_block').empty();
						$('#mp_shipping_method_block').append("<div class='alert alert-warning'>"+no_shipping+"</div>");
					}, error: function(data) {
						$('body').css('opacity', '1');
						$('#ajax_loader').css('display', 'none');
						$('#mp_shipping_method_block').empty();
						$('#mp_shipping_method_block').append("<div class='alert alert-warning'>"+no_shipping+"</div>");
					}
				});
			}
			getShippingMethodByIdCustomer(selected_id_customer);
			$(document).on('change', "[name='shop_customer']", function(){
				selected_id_customer = $("[name='shop_customer']").val();
				getShippingMethodByIdCustomer(selected_id_customer);
			});
		}
	});
</script>
{/if}