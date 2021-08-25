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

<div class="modal fade" id="wk_delivery_form" role="dialog">
	<div class="modal-dialog">
		<form action="{$update_url_link}" method="post">
			<div class="modal-content">
		        <div class="modal-header">
		        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					<div class="box-head-left">
		        		<h4 class="modal-title">{l s='Shipping Address' mod='marketplace'}</h4>
					</div>
					<div class="box-head-right">
					<a id="edit_delivery" href="#">
						<span>{l s='Edit' mod='marketplace'}</span>
					</a>
					</div>
				</div>
				<div class="modal-body" id="shipping_address">
					<div class="box-content" >
						<div id="wk_address_details">
							<div class="">
								{*{displayAddressDetail address=$addresses.delivery newLine='<br />'}
								{if $addresses.delivery->other}
									<hr />{$addresses.delivery->other}<br />
								{/if}*}
							</div>
						</div>
					</div>

					<div class="clearfix box-head">
						<div class="box-head-left">
							<h2>{l s='Delivery Date' mod='marketplace'}</h2>
						</div>
					</div>
					
					<div id="shipping_date_block">
						<div class="wk_shipping_head">
							<input id="text_delivery_date" type="text" class="datepicker form-control" name="delivery_date" style="display:none;" {if isset($delivery_date)} value="{$delivery_date|date_format:"%Y-%m-%d"}" {/if} />
							<p id="label_delivery_date">
								{if isset($delivery_date)}
									{dateFormat date=$delivery_date full=0}
								{else}
									{l s='No data found' mod='marketplace'}
								{/if} 
							</p>
						</div>
					</div>

					<div class="clearfix box-head">
						<div class="box-head-left">
							<h2>{l s='Received By' mod='marketplace'}</h2>
						</div>
					</div>
					<div class="wk_received">
						<span>
							<input type="hidden" name="id_order_state_checked" class="id_order_state_checked" value="{$currentState}" />
							<input type="hidden" name="delivery_info_set" value="1">
							<input type="text" id="edit_text_received_by" class="form-control"  name="edit_received_by" style="display:none;" {if isset($received_by)} value="{$received_by}" {/if}/>
							<p id="label_received_by">
								{if isset($received_by) && $received_by != ''}
									{$received_by}
								{else}
									{l s='No data found' mod='marketplace'}
								{/if}
							</p>
						</span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button name="delivery_info" class="btn btn-yellow" type="submit">
						<span>{l s='Submit' mod='marketplace'}</span>
					</button>
				</div>
			</div>
		</form>
	</div>
</div>