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

<div class="row">
{if isset($pickUpProductFound) && $pickUpProductFound == 1}
    <div class="alert alert-info">
        <form method="post" action="{$formSubmitUrl}">
            {l s='There are some products in your cart which are available for store pick up. So if you want store pick up ' mod='wkstorelocator'}
            {* <input type="hidden" name="applyStorePickUp" value="1"/> *}
            <button type="submit" name='wkForceCarrier' class="wk-btn">
                {l s='Click here' mod='wkstorelocator' mod='wkstorelocator'}
            </button>
        </form>
    </div>
{/if}
{if isset($resetStoreProduct) && $resetStoreProduct}
    <div class="alert alert-info">
        <form method="post" action="{$formSubmitUrl}">
            {l s='Click on reset button to reset the Store pick up shipping method' mod='wkstorelocator'}
            {* <input type="hidden" name="applyStorePickUp" value="1"/> *}
            <button type="submit" name='wkForceCarrier' class="wk-btn">
                {l s='Reset' mod='wkstorelocator' mod='wkstorelocator'}
            </button>
        </form>
    </div>
{/if}
</div>