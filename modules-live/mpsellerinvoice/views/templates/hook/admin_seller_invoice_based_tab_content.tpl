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

<div class="tab-pane fade in" id="wk-seller-invoice">
    <div class="form-wrapper">
        <div class="form-group">
            <label class="col-lg-3 control-label">
                {l s='Commission Invoice Based On' mod='mpsellerinvoice'}
            </label>
            <div class="col-lg-5">
                <select name="seller_invoice_based" class="form-control">
                    <option
                        {if isset($result) && $result['invoice_based'] == 1}
                            selected="selected"
                        {/if}
                        value="1">
                        {l s='Invoice for each order' mod='mpsellerinvoice'}
                    </option>
                    <option
                        {if isset($result) && $result['invoice_based'] == 2}
                            selected="selected"
                        {/if}
                        value="2">
                        {l s='Invoice on Time period' mod='mpsellerinvoice'}
                    </option>
                    <option
                        {if isset($result) && $result['invoice_based'] == 3}
                            selected="selected"
                        {/if}
                        value="3">
                        {l s='Invoice on Threshold Amount Value' mod='mpsellerinvoice'}
                    </option>
                </select>
            </div>
        </div>
        <div id="seller_time_interval" class="form-group" style="display:none;">
            <label class="col-lg-3 control-label required">
                {l s='Set Time Interval' mod='mpsellerinvoice'}
            </label>
            <div class="col-lg-3">
                <input
                    {if isset($result) && $result['value'] && $result['invoice_based'] == 2}
                    value="{$result['value']}"
                    {/if}
                    type="text"
                    name="seller_invoice_value"
                    class="form-control">
            </div>
            <div class="col-lg-1">
                <select name="seller_invoice_interval" class="custom-select">
                    <option
                        {if isset($result) && $result['time_interval'] == 'day'}
                            selected="selected"
                        {/if}
                        value="day">
                        {l s='Days' mod='mpsellerinvoice'}
                    </option>
                    <option
                        {if isset($result) && $result['time_interval'] == 'month'}
                            selected="selected"
                        {/if}
                        value="month">
                        {l s='Month' mod='mpsellerinvoice'}
                    </option>
                    <option
                        {if isset($result) && $result['time_interval'] == 'year'}
                            selected="selected"
                        {/if}
                        value="year">
                        {l s='Year' mod='mpsellerinvoice'}
                    </option>
                </select>
            </div>
        </div>
        <div id="seller_threshold" class="form-group" style="display:none;">
            <label class="col-lg-3 control-label required">
                {l s='Set Threshold Amount Value' mod='mpsellerinvoice'}
            </label>
            <div class="col-lg-3">
                <div class="input-group">
                    <input
                        {if isset($result) && $result['value'] && $result['invoice_based'] == 3}
                        value="{$result['value']}"{/if}
                        type="text"
                        name="seller_threshold"
                        class="form-control"
                    >
                    <span class="input-group-addon">{$wkcurrency->sign}</span>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
    var sellerInvoiceBased = $('select[name="seller_invoice_based"]').val();
    if (sellerInvoiceBased == '2') {
        $('#seller_time_interval').show('slow');
        $('#seller_threshold').hide('slow');
    } else if (sellerInvoiceBased == '3') {
        $('#seller_threshold').show('slow');
        $('#seller_time_interval').hide('slow');
    } else {
        $('#seller_threshold, #seller_time_interval').hide('slow');
    }

    $('select[name="seller_invoice_based"]').on('change', function(){
        if ($(this).val() == '2') {
            $('#seller_time_interval').show('slow');
            $('#seller_threshold').hide('slow');
        } else if ($(this).val() == '3') {
            $('#seller_threshold').show('slow');
            $('#seller_time_interval').hide('slow');
        } else {
            $('#seller_threshold, #seller_time_interval').hide('slow');
        }
    });
});
</script>