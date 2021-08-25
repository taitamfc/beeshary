{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}
<style>
    .wk-margin-20 {
        margin: 20px;
    }
</style>

<div class="tab-pane fade in" id="wk_store_pick_up">

    <div class="form-group clearfix">
        <label for="status" class="col-sm-4 control-label">{l s='Apply Store pick up' mod='mpstorelocator'}</label>
        <div class="col-lg-8">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" value="1" id="enableStorePickUp_on" name="enableStorePickUp" {if isset($smarty.post.enableStorePickUp)}{if $smarty.post.enableStorePickUp == 1} checked="checked"{/if}{else}{if isset($enable_pickup)}{if $enable_pickup == 1} checked="checked"{/if}{/if}{/if}>
                <label for="enableStorePickUp_on">{l s='Yes' mod='mpstorelocator'}</label>
                <input type="radio" value="0" id="enableStorePickUp_off" name="enableStorePickUp" {if isset($smarty.post.enableStorePickUp)}{if $smarty.post.enableStorePickUp == 0} checked="checked"{/if}{else}{if isset($enable_pickup)}{if $enable_pickup == 0} checked="checked"{/if}{else}checked="checked"{/if}{/if}>
                <label for="enableStorePickUp_off">{l s='No' mod='mpstorelocator'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>
</div>
