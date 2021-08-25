{*
* 2010-2019 Webkul
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
*  @author Webkul IN <support@webkul.com>
*  @copyright  2010-2019 Webkul IN
*}

<style type="text/css">
	.wksuccess{
		width: 100%;
		float: left;
	}
	.wksuccess div {
		width: 97%;
		float: right;
	}
</style>
<div class="box-head">
	<h2><i class="icon-file"></i> {l s='Invoice Details' mod='mpsellerinvoice'}</h2>
	<div class="wk_border_line"></div>
</div>
{if isset($invoice_sent) && $invoice_sent == 1}
	<div class="alert alert-success wksuccess">
		{l s='The message was successfully sent to the admin.' mod='mpsellerinvoice'}
	</div>
{else if isset($invoice_sent) && $invoice_sent == 2}
	<div class="alert alert-danger wksuccess">
		{l s='An error occurred during sending the message.' mod='mpsellerinvoice'}
	</div>
{/if}
<div class="box-content">
	<div class="well hidden-print">
		<a style="margin:.5rem;" target="_blank" href="{$link->getModuleLink(
			'mpsellerinvoice',
			'pdfdownload',
			[
				'id_order' => {$id_order},
				'id_seller' => {$id_seller},
				'invoice' => 1])}" class="btn btn-primary">
			<i class="icon-file"></i> {l s='View Invoice' mod='mpsellerinvoice'}</a>
		{if Configuration::get('MP_SELLER_INVOICE_TO_ADMIN') == 1}
		<a style="margin:.5rem;" href="{$link->getModuleLink(
			'mpsellerinvoice',
			'pdfdownload',
			[
				'id_order' => {$id_order},
				'id_seller' => {$id_seller},
				'send' => 1])}" class="btn btn-primary">
			<i class="icon-mail-reply"></i> {l s='Send Invoice' mod='mpsellerinvoice'}</a>
		{/if}
	</div>
</div>