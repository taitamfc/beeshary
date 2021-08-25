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

<div class="tab-pane fade" id="tracking_id">
    {if isset($smarty.get.invalid_tracking_number)}
		<div class="alert alert-danger">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Tracking number is not valid.' mod='mpshipping'}
        </div>
    {/if}
    {if isset($smarty.get.update_tracking_success)}
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Tracking Number Updated Successfully' mod='mpshipping'}
        </div>
    {/if}
    {if isset($update_trackingNumber_action)}
        <form method="post" action="{$update_trackingNumber_action}" name="mp_order_tracking_number">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <input type="text" class="form-control" value="{if isset($shipping_number)}{$shipping_number}{/if}" name="mp_tracking_number" id="mp_tracking_number" autocomplete="off" placeholder="{l s='Enter tracking number' mod='mpshipping'}" required>
                </div>
                <div class="col-md-4 col-sm-4">
                    <button class="btn btn-primary" name="submit_mp_tracking_number" id="submit_mp_tracking_number">
                        {l s='Update' mod='mpshipping'}
                    </button>
                </div>
            </div>
        </form>
    {else}
        <div class="alert alert-danger">
            {l s='You are allowed to update tracking number on those orders which will be shipped by your shipping method.' mod='mpshipping'}
        </div>
    {/if}
</div>
