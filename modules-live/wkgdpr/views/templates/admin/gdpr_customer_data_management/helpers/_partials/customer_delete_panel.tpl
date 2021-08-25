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


<div class="panel">
	<div class="panel-heading">
		<i class="icon-cogs"></i> {l s='Delete Customer Data' mod='wkgdpr'}
	</div>
    <div class="panel-body">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3 required">
                    <span>{l s='Select Customer' mod='wkgdpr'}</span>
                </label>
                <div class="col-lg-4">
                    <div class="input-group">
                        <input type="text" class="form-control" id="customer-suggestion-input" autocomplete="off">
                        <span class="input-group-addon"> <i class="icon-search"></i> </span>
                        <ul id="wk_customer_suggestion_cont"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{strip}
	{addJsDef wkGdprCustomerDataManagementLink = $wkGdprCustomerDataManagementLink}
    {addJsDef wkGdprViewCustomerDataLink = $wkGdprViewCustomerDataLink}

	{addJsDefL name=noResultFound}{l s='No result found' js=1 mod='wkgdpr'}{/addJsDefL}
{/strip}
