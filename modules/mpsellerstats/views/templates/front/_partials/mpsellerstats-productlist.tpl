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

<hr>
<div class="panel">
    <h4><i class="material-icons">list</i> {l s='List' mod='mpsellerstats'}</h4>
    <div class="panel-content">
    </div>
    <table class="table table-striped" id="mp_product_list">
        <thead>
            <tr>
                <th>{l s='ID' mod='mpsellerstats'}</th>
                <th>{l s='Image' mod='mpsellerstats'}</th>
                <th>{l s='Name' mod='mpsellerstats'}</th>
                <th><center>{l s='Visitors' mod='mpsellerstats'}</center></th>
                <th><center>{l s='Visits' mod='mpsellerstats'}</center></th>
                <th class="no-sort"><center>{l s='Actions' mod='mpsellerstats'}</center></th>
            </tr>
        </thead>
        <tbody>
            {* {if $product_lists != 0}
                {foreach $product_lists as $key => $product}
                    <tr class="{if $key%2 == 0}even{else}odd{/if}">
                        <td>{$product.id_mp_product}</td>
                        <td>
                            <a href="{$link->getModuleLink('marketplace', 'productdetails', ['id_mp_product' => $product.id_mp_product])}">
                            {$product.product_name}
                            </a>
                        </td>
                        <td><center>{$product.visitor}</center></td>
                        <td><center>{$product.visits}</center></td>
                        <td>
                            <center>
                            <a title="{l s='View stats' mod='mpsellerstats'}" href="{$link->getModuleLink('mpsellerstats', 'mpsellerproductstats', ['id_ps_product' => $product.id_ps_product, 'viewstats' => 1])}">
                                <i class="material-icons">&#xE8F4;</i>
                            </a>
                            </center>
                        </td>
                    </tr>
                {/foreach}
            {/if} *}
        </tbody>
    </table>
</div>