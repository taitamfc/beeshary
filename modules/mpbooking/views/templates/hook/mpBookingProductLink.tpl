{*
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if $mpmenu == 0}
    <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" href="{$mp_bookingproductlist_link}" title="{l s='Booking Products' mod='mpbooking'}">
        <span class="link-item">
            <i class="material-icons">&#xE8EF;</i> {l s='Booking Products' mod='mpbooking'}
        </span>
    </a>
    <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" href="{$mp_featurepriceplans_link}" title="{l s='Booking Price Rules' mod='mpbooking'}">
        <span class="link-item">
        <i class="material-icons">&#xE54E;</i> {l s='Booking Price Rules' mod='mpbooking'}
        </span>
    </a>
{else}
    <li {if $logic == 'mpbookingproduct'}class="menu_active"{/if}>
        <span>
            <a href="{$mp_bookingproductlist_link}">
                <i class="material-icons">&#xE8EF;</i> {l s='Booking Products' mod='mpbooking'}
                <span class="wkbadge-primary" style="float:right;">{$countBookingProducts}</span>
                <div class="clearfix"></div>
            </a>
        </span>
        <div class="clearfix"></div>
    </li>
    <li {if $logic == 'mpfeaturepriceplans'}class="menu_active"{/if}>
        <span>
            <a href="{$mp_featurepriceplans_link}" title="{l s='Booking Price Rules' mod='mpbooking'}">
                <i class="material-icons">&#xE54E;</i> {l s='Booking Price Rules' mod='mpbooking'}
            </a>
        </span>
    </li>
{/if}
