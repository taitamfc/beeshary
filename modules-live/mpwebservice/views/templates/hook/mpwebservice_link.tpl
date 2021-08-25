{*
* 2010-2021 Webkul.
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
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($mpmyaccountmenu)}
    <a title="{l s='Webservice' mod='mpwebservice'}" href="{$webservice}" class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
        <span class="link-item">
            <i class="material-icons">&#xE850;</i>
            {l s='Webservice' mod='mpwebservice'}
        </span>
    </a>
{else}
    <li {if $logic == 'mpwebservice_link'}class="menu_active"{/if}>
        <span>
            <a href="{$webservice}">
                <i class="material-icons">&#xE850;</i>
                {l s='Webservice' mod='mpwebservice'}
            </a>
        </span>
    </li>
{/if}
