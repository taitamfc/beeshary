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

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content" style="text-align:left;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">{l s='Write your query' mod='marketplace'}</h4>
			</div>
			<form id="wk_contact_seller-form" method="post" action="#">
				<div class="modal-body">
					<div class="form-group">
						<label class="control-label required">{l s='Email' mod='marketplace'}</label>
						<input type="text" name="customer_email" id="customer_email" class="form-control" />
					</div>
					<div class="form-group">
						<label class="control-label required">{l s='Subject' mod='marketplace'}</label>
						<input type="text" name="query_subject" class="form-control" id="query_subject" />
					</div>
					<div class="form-group">
						<label class="control-label required">{l s='Description ' mod='marketplace'}</label>
						<textarea name="query_description" class="form-control" id="query_description" style="height:100px;"></textarea>
					</div>
					<input type="hidden" name="id_seller" value="{$seller_id}"/>
					<input type="hidden" name="id_customer" value="{$id_customer}"/>
					<input type="hidden" name="id_product" value="{if isset($id_product)}{$id_product}{else}0{/if}"/>

					<div class="form-group">
						<div class="contact_seller_message"></div>
					</div>

					{block name='mp-form-fields-notification'}
						{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-notification.tpl'}
					{/block}
				</div>

				<div class="modal-footer">
					<div class="form-group row">
						<div class="col-xs-6 col-sm-6 col-md-6" style="text-align:left">
							<button type="button" class="btn wk_btn_cancel wk_btn_extra" data-dismiss="modal">
								{l s='Cancel' mod='marketplace'}
							</button>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6 wk_text_right">
							<button type="submit" class="btn btn-success wk_btn_extra" id="wk_contact_seller" name="wk_contact_seller">
								{l s='Send' mod='marketplace'}
							</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>