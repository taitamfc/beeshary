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

{extends file=$layout}
{block name='content'}
    {if isset($smarty.get.config) && $smarty.get.config == 1}
        <div class="alert alert-success wk_invoice_msg">{l s='Successfully updated' mod='mpsellerinvoice'}</div>
    {/if}
    <div class="wk-mp-block">
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color};">
				<span style="color:{$title_text_color};">{l s='Seller Invoice' mod='mpsellerinvoice'}</span>
			</div>
			<div class="wk-mp-right-column">
				<div class="tabs">
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item">
							<a
                                href="#wk-invoice-config"
                                data-toggle="tab"
                                class="nav-link active">
								<i class="material-icons">&#xE8B0;</i>
								{l s='Invoice Setting' mod='mpsellerinvoice'}
							</a>
						</li>
						<li class="nav-item">
							<a
                                class="nav-link"
                                href="#wk-order-invoice"
                                data-toggle="tab">
								<i class="material-icons">&#xE8A1;</i>
								{l s='Order Invoice' mod='mpsellerinvoice'}
							</a>
						</li>
                        <li class="nav-item">
							<a
                                data-toggle="tab"
                                class="nav-link"
                                href="#wk-admin-invoice">
								<i class="material-icons">&#xE8A1;</i>
								{l s='Admin Commission Invoice' mod='mpsellerinvoice'}
							</a>
						</li>
					</ul>
					<div class="tab-content" id="tab-content">
						<div class="tab-pane fade in active" id="wk-invoice-config">
							<div class="box-account box-recent">
								{block name='mpseller_invoice_config'}
									{include file="module:mpsellerinvoice/views/templates/front/_partials/sellerinvoiceconfig.tpl"}
								{/block}
							</div>
						</div>
                        <div class="tab-pane fade in" id="wk-order-invoice">
							<div class="box-account box-recent">
								{block name='mpseller_order_invoice'}
									{include file="module:mpsellerinvoice/views/templates/front/_partials/sellerorderinvoice.tpl"}
								{/block}
							</div>
						</div>
                        <div class="tab-pane fade in" id="wk-admin-invoice">
							<div class="box-account box-recent">
								{block name='mpseller_admin_invoice'}
									{include file="module:mpsellerinvoice/views/templates/front/_partials/admincommissioninvoice.tpl"}
								{/block}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
{/block}