{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<li class="nav-item">
    <a class="nav-link" href="#wk_store_pick_up" data-toggle="tab">
        {if isset($controller) && $controller == 'admin'}
            <i class="icon-home"></i>
        {else}
            <i class="material-icons">&#xE8D1;</i>
        {/if}
        {l s='Store PickUp' mod='mpstorelocator'}
    </a>
</li>