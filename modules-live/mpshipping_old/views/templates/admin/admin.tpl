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

<div class="bootstrap" style="display:none;">
    <div class="module_error alert alert-danger" style="display:none;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <span></span>
    </div>
    <div class="module_confirmation conf confirm alert alert-success" style="display:none;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
         <span></span>
    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i> {l s='Carrier Settings' mod='mpshipping'}
    </div>
    <div class="form-wrapper">
    	<form action="" method="post">
            <button class="btn btn-primary" type="submit" name="assign_shipping" id="assign_shipping">
                {l s='Assign Carriers to Admin Products' mod='mpshipping'}
            </button>
            <img src="{$this_path}views/img/loader.gif" class="loader" style="display:none;" alt="{l s='Loading' mod='mpshipping'}" title="{l s='Loading' mod='mpshipping'}">
        </form>
        <br>
        <div class="alert alert-info">
            {l s='Using this option, You can assign all the admin carriers to all the admin products. Seller carriers will not be assigned on admin products.' mod='mpshipping'}
            <br>
            {l s='In case, if you have selected any specific carrier on any of the admin product, then it will be replaced by all the available carriers of admin.' mod='mpshipping'}
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i> {l s='Admin Default Shipping Method' mod='mpshipping'}
    </div>
    <div class="panel-body">
        {if isset($all_ps_carriers_arr) && $all_ps_carriers_arr}
            <form method="post" class="form-horizontal">
                <div class="form-group">
                    <label for="default_shipping" class="control-label col-lg-2 text-right">
                       <span class="label-tooltip" title="" data-html="true" data-toggle="tooltip" data-original-title="{l s='If No seller shipping applied on products then Admin default shipping will be applied on seller products' mod='mpshipping'}"> {l s='Select default shipping' mod='mpshipping'} </span>
                    </label>
                    <div class="col-lg-10">
                        <div style="height:155px;overflow:auto;">
                        {foreach $all_ps_carriers_arr as $carrier}
                            <div>
                                <div class="shipping_checkbox">
                                    <input type="checkbox" name="default_shipping[]" id="default_shipping_{$carrier.id_reference}" value="{$carrier.id_reference}"
                                    {if in_array($carrier.id_reference, $admin_def_shipping)}checked="checked"{/if}>
                                </div>
                                <div class="checkbox_name">
                                    <label for="default_shipping_{$carrier.id_reference}" style="font-weight: normal;">
                                        {$carrier.name}
                                    </label>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                        {/foreach}
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button class="btn btn-default pull-right" id="submit_admin_default_shipping" name="submit_admin_default_shipping" value="1" type="submit">
                        <i class="process-icon-save"></i> {l s='Save' mod='mpshipping'}
                    </button>
                </div>
            </form>
        {else}
            <div class="alert alert-info">{l s='You do not have any active shipping method(s).' mod='mpshipping'}</div>
        {/if}
    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i> {l s='Approval Settings' mod='mpshipping'}
    </div>
    <div class="panel-body">
        <form method="post" class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3">
                    <span class="label-tooltip" title="" data-html="true" data-toggle="tooltip" data-original-title=" {l s='If No, shipping request will be automatically approved' mod='mpshipping'} "> {l s='Shipping needs to be approved by admin' mod='mpshipping'} </span>
                </label>
                <div class="col-lg-9">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" checked="checked" value="1" id="MP_SHIPPING_ADMIN_APPROVE_on" name="MP_SHIPPING_ADMIN_APPROVE" {if (isset($MP_SHIPPING_ADMIN_APPROVE) || $MP_SHIPPING_ADMIN_APPROVE == 1)} checked="checked"{/if}>
                        <label for="MP_SHIPPING_ADMIN_APPROVE_on">{l s='Yes' mod='mpshipping'}</label>
                        <input type="radio" value="0" id="MP_SHIPPING_ADMIN_APPROVE_off" name="MP_SHIPPING_ADMIN_APPROVE" {if (!isset($MP_SHIPPING_ADMIN_APPROVE) || $MP_SHIPPING_ADMIN_APPROVE == 0)} checked="checked"{/if}>
                        <label for="MP_SHIPPING_ADMIN_APPROVE_off">{l s='No' mod='mpshipping'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>

            {if isset($MP_SHIPPING_ADMIN_DISTRIBUTION)}
                <div class="form-group">
                    <label class="control-label col-lg-3">
                        <span class="label-tooltip" title="" data-html="true" data-toggle="tooltip" data-original-title="{l s='Shipping distribution will be allowed' mod='mpshipping'}">{l s='Allow Shipping Distribution' mod='mpshipping'}</span>
                    </label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" checked="checked" value="1" id="MP_SHIPPING_DISTRIBUTION_ALLOW_on" name="MP_SHIPPING_DISTRIBUTION_ALLOW" {if (isset($MP_SHIPPING_DISTRIBUTION_ALLOW) || $MP_SHIPPING_DISTRIBUTION_ALLOW == 1)} checked="checked"{/if}>
                            <label for="MP_SHIPPING_DISTRIBUTION_ALLOW_on">{l s='Yes' mod='mpshipping'}</label>
                            <input type="radio" value="0" id="MP_SHIPPING_DISTRIBUTION_ALLOW_off" name="MP_SHIPPING_DISTRIBUTION_ALLOW" {if (!isset($MP_SHIPPING_DISTRIBUTION_ALLOW) || $MP_SHIPPING_DISTRIBUTION_ALLOW == 0)} checked="checked"{/if}>
                            <label for="MP_SHIPPING_DISTRIBUTION_ALLOW_off">{l s='No' mod='mpshipping'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>
                <div class="form-group wk-admin-shipping-distribute">
                    <label class="control-label col-lg-3">
                        <span class="label-tooltip" title="" data-html="true" data-toggle="tooltip" data-original-title="{l s='Distribute shipping if Admin product exists with seller product in same order.' mod='mpshipping'}">{l s='Distribute shipping between admin and seller both' mod='mpshipping'}</span>
                    </label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" checked="checked" value="1" id="MP_SHIPPING_ADMIN_DISTRIBUTION_on" name="MP_SHIPPING_ADMIN_DISTRIBUTION" {if (isset($MP_SHIPPING_ADMIN_DISTRIBUTION) || $MP_SHIPPING_ADMIN_DISTRIBUTION == 1)} checked="checked"{/if}>
                            <label for="MP_SHIPPING_ADMIN_DISTRIBUTION_on">{l s='Yes' mod='mpshipping'}</label>
                            <input type="radio" value="0" id="MP_SHIPPING_ADMIN_DISTRIBUTION_off" name="MP_SHIPPING_ADMIN_DISTRIBUTION" {if (!isset($MP_SHIPPING_ADMIN_DISTRIBUTION) || $MP_SHIPPING_ADMIN_DISTRIBUTION == 0)} checked="checked"{/if}>
                            <label for="MP_SHIPPING_ADMIN_DISTRIBUTION_off">{l s='No' mod='mpshipping'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                        <p class="help-block">{l s='If Admin product exists with any seller product in same order and that order carrier distribution is set as Seller or Both then Shipping will be distributed between admin and seller on the basis of product price or weight.' mod='mpshipping'}</p>
                    </div>
                </div>
            {/if}

            <div class="form-group">
                    <label class="control-label col-lg-3">
                        <span class="label-tooltip" title="" data-html="true" data-toggle="tooltip" data-original-title=" {l s='If yes then, admin shipping will be shown with seller shipping' mod='mpshipping'} "> {l s='Allow admin shipping with seller shipping' mod='mpshipping'} </span>
                    </label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" checked="checked" value="1" id="MP_SHIPPING_ADMIN_SELLER_on" name="MP_SHIPPING_ADMIN_SELLER" {if (isset($MP_SHIPPING_ADMIN_SELLER) || $MP_SHIPPING_ADMIN_SELLER == 1)} checked="checked"{/if}>
                            <label for="MP_SHIPPING_ADMIN_SELLER_on">{l s='Yes' mod='mpshipping'}</label>
                            <input type="radio" value="0" id="MP_SHIPPING_ADMIN_SELLER_off" name="MP_SHIPPING_ADMIN_SELLER" {if (!isset($MP_SHIPPING_ADMIN_SELLER) || $MP_SHIPPING_ADMIN_SELLER == 0)} checked="checked"{/if}>
                            <label for="MP_SHIPPING_ADMIN_SELLER_off">{l s='No' mod='mpshipping'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
            </div>
            <div class="panel-footer">
                <button class="btn btn-default pull-right" id="submit_shipping_approval" name="submit_shipping_approval" value="1" type="submit">
                    <i class="process-icon-save"></i> {l s='Save' mod='mpshipping'}
                </button>
            </div>
        </form>
    </div>
</div>
<div class="panel">
	<div class="panel-heading">
        <i class="icon-cogs"></i> {l s='Email Settings' mod='mpshipping'}
    </div>
    <div class="panel-body">
    	<form method="post" class="form-horizontal">
    		<div class="form-group">
                <label class="control-label col-lg-3">
                   {l s='Mail to admin when seller add new shipping' mod='mpshipping'}
                </label>
                <div class="col-lg-9">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" checked="checked" value="1" id="MP_MAIL_ADMIN_SHIPPING_ADDED_on" name="MP_MAIL_ADMIN_SHIPPING_ADDED" {if (isset($MP_MAIL_ADMIN_SHIPPING_ADDED) || $MP_MAIL_ADMIN_SHIPPING_ADDED == 1)} checked="checked"{/if}>
                        <label for="MP_MAIL_ADMIN_SHIPPING_ADDED_on">{l s='Yes' mod='mpshipping'}</label>
                        <input type="radio" value="0" id="MP_MAIL_ADMIN_SHIPPING_ADDED_off" name="MP_MAIL_ADMIN_SHIPPING_ADDED" {if (!isset($MP_MAIL_ADMIN_SHIPPING_ADDED) || $MP_MAIL_ADMIN_SHIPPING_ADDED == 0)} checked="checked"{/if}>
                        <label for="MP_MAIL_ADMIN_SHIPPING_ADDED_off">{l s='No' mod='mpshipping'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-3">
                   {l s='Mail to seller on shipping approval or disapproval' mod='mpshipping'}
                </label>

                <div class="col-lg-9">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" checked="checked" value="1" id="MP_MAIL_SELLER_SHIPPING_APPROVAL_on" name="MP_MAIL_SELLER_SHIPPING_APPROVAL" {if (isset($MP_MAIL_SELLER_SHIPPING_APPROVAL) || $MP_MAIL_SELLER_SHIPPING_APPROVAL == 1)} checked="checked"{/if}>
                        <label for="MP_MAIL_SELLER_SHIPPING_APPROVAL_on">{l s='Yes' mod='mpshipping'}</label>
                        <input type="radio" value="0" id="MP_MAIL_SELLER_SHIPPING_APPROVAL_off" name="MP_MAIL_SELLER_SHIPPING_APPROVAL" {if (!isset($MP_MAIL_SELLER_SHIPPING_APPROVAL) || $MP_MAIL_SELLER_SHIPPING_APPROVAL == 0)} checked="checked"{/if}>
                        <label for="MP_MAIL_SELLER_SHIPPING_APPROVAL_off">{l s='No' mod='mpshipping'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>
            <div class="panel-footer">
                <button class="btn btn-default pull-right" id="submit_email_setting" name="submit_email_setting" value="1" type="submit">
                    <i class="process-icon-save"></i> {l s='Save' mod='mpshipping'}
                </button>
            </div>
    	</form>
    </div>
</div>