{*
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file="helpers/list/list_header.tpl"}
{block name=leadin}
<div class="modal fade" id="send_invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        	<h4 class="modal-title" id="myModalLabel">
                    {l s='Send Commission Invoice' mod='mpsellerinvoice'}
                </h4>
	        </div>
			<form id="send-form" action="{$link->getAdminLink('AdminCommissionInvoice')|escape:'htmlall':'UTF-8'}" method="post">
				<div class="modal-body">
					<input type="hidden" name="invoice_number" id="invoice_number" value=""/>
					<div class="row">
						<label class="col-lg-12 control-label empty_error" style="color:red;display:none;">
                            {l s='Please provide email address' mod='mpsellerinvoice'}
                        </label>
						<label class="col-lg-12 control-label invalid_error" style="color:red;display:none;">
                            {l s='Email is not valid' mod='mpsellerinvoice'}
                        </label>
					</div>
					<div class="row">
                        <label class="col-lg-12 control-label">{l s='Email' mod='mpsellerinvoice'}</label>
						<div class="col-lg-12">
							<input type="email" class="form-control" value="" name="wk_commission_email" id="wk_commission_email">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" name="submit_btn" id="sendInvoice">
                        <span>{l s='Send' mod='mpsellerinvoice'}</span>
                    </button>
				</div>
			</form>
		</div>
	</div>
</div>

{/block}