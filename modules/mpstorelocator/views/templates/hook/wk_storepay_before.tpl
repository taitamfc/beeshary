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

<form method="post" action="{$formSubmit}">
    <div class="row">
        <div class="col-xs-12">
            <div class="alert alert-info">
                {l s='There are some products in your cart which are not available for "Pay in Store". So if you want to "Pay in Store" click on button' mod='wkstorelocator'} 
                <button type="submit" class="wk-btn"><span>{l s='Click here' mod='wkstorelocator'}</span></button>
                {l s=' to remove the product from cart which is not available for "Pay in Store"' mod='wkstorelocator'}
            </div>
        </div>
    </div>
</form>