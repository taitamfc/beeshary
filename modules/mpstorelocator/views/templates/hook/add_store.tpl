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

{if !$mpmenu}
  <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" title="{l s='Store Location' mod='mpstorelocator'}" href="{$link->getModuleLink('mpstorelocator', 'storelist')}">
    <span class="link-item">
      <i class="material-icons">&#xE55F;</i>
      {l s='Store Location' mod='mpstorelocator'}
    </span>
  </a>
{else}
  <li {if isset($logic) && ($logic == 'manage_store_list' || $logic == 'manage_store_configuration' || $logic == 'add_new_store')}class="menu_active"{/if}>
    <span>
      <a href="{$link->getModuleLink('mpstorelocator', 'storelist')|addslashes}" title="{l s='Store Location' mod='mpstorelocator'}">
        <i class="material-icons">&#xE55F;</i>
        {l s='Store Location' mod='mpstorelocator'}
      </a>
    </span>
  </li>
{/if}
