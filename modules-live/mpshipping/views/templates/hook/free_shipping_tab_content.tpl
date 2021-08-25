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

<div class="tab-pane fade in" id="wk-seller-free_shipping">
    {if isset($id_seller) && $id_seller}
        {if $is_admin == 1}
            <div class="form-wrapper">
                <input type="hidden" name="id_mp_seller" value="{$id_seller}">
                <div class="form-group">
                    <label for="free_shipping_start_price" class="col-lg-3 control-label">{l s='Free shipping starts at' mod='mpshipping'}</label>
                    <div class="col-lg-6">
                        <div class="input-group">
                        <span class="input-group-addon">
                        {$currency_sign}
                        </span>
                        <input class="form-control-static"
                            type="text"
                            name="free_shipping_start_price"
                            id="free_shipping_start_price"
                            value="{if isset($shipping_free_info['free_shipping_start_price'])}{$shipping_free_info['free_shipping_start_price']}{else}0{/if}"
                        >
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="free_shipping_start_weight" class="col-lg-3 control-label">{l s='Free shipping starts at' mod='mpshipping'}</label>
                    <div class="col-lg-6">
                        <div class="input-group">
                        <input class="form-control-static"
                            type="text"
                            name="free_shipping_start_weight"
                            id="free_shipping_start_weight"
                            value="{if isset($shipping_free_info['free_shipping_start_weight'])}{$shipping_free_info['free_shipping_start_weight']}{else}0{/if}"
                        >
                        <span class="input-group-addon">
                        {$weight_unit}
                        </span>
                        </div>
                    </div>
                </div>
            </div>
        {else}
            <input type="hidden" name="id_mp_seller" value="{$id_seller}">
            <div class="form-group row">
                <label for="free_shipping_start_price" class="col-lg-3 control-label">{l s='Free shipping starts at' mod='mpshipping'}</label>
                <div class="col-lg-6">
                    <div class="input-group">
                    <span class="input-group-addon">
                    {$currency_sign}
                    </span>
                    <input class="form-control"
                        type="text"
                        name="free_shipping_start_price"
                        id="free_shipping_start_price"
                        value="{if isset($shipping_free_info['free_shipping_start_price'])}{$shipping_free_info['free_shipping_start_price']}{else}0{/if}"
                    >
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="free_shipping_start_weight" class="col-lg-3 control-label">{l s='Free shipping starts at' mod='mpshipping'}</label>
                <div class="col-lg-6">
                    <div class="input-group">
                    <input class="form-control"
                        type="text"
                        name="free_shipping_start_weight"
                        id="free_shipping_start_weight"
                        value="{if isset($shipping_free_info['free_shipping_start_weight'])}{$shipping_free_info['free_shipping_start_weight']}{else}0{/if}"
                    >
                    <span class="input-group-addon">
                    {$weight_unit}
                    </span>
                    </div>
                </div>
            </div>
        {/if}
        <div class="alert alert-info">
            <ul>
            <li>{l s='If you set these parameters to 0, they will be disabled' mod='mpshipping'}</li>
            <li>{l s='Coupons are not taken into account when calculating free shipping.' mod='mpshipping'}</li>
            </ul
        </div>
    {else}
        <div class="alert alert-info">
            {l s='First Save the seller to set this configuration.' mod='mpshipping'}
        </div>
    {/if}
</div>