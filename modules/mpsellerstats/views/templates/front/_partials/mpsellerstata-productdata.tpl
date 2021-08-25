{*
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($product_lists) && $product_lists !=0}
    {foreach $product_lists as $key => $product}
        <tr class="{if $key%2 == 0}even{else}odd{/if}">
            <td>{$product.id_mp_product}</td>
            <td>
                {if isset($product.unactive_image)} <!--product is not activated yet-->
                    <a class="mp-img-preview" href="{$smarty.const._MODULE_DIR_}marketplace/views/img/product_img/{$product.unactive_image}">
                        <img class="img-thumbnail" width="45" height="45" src="{$smarty.const._MODULE_DIR_}marketplace/views/img/product_img/{$product.unactive_image}">
                    </a>
                {else if isset($product.cover_image)} <!--product is atleast one time activated-->
                    <a class="mp-img-preview" href="{$product.image_path}">
                        <img class="img-thumbnail" width="45" height="45" src="{$link->getImageLink($product.obj_product->link_rewrite, $product.cover_image, 'small_default')}">
                    </a>
                {else}
                    <img class="img-thumbnail" alt="{l s='No image' mod='marketplace'}"	width="45" height="45" src="{$smarty.const._MODULE_DIR_}/marketplace/views/img/home-default.jpg">
                {/if}
            </td>
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
{else}
    <tr>
        <td>
        {l s='Cannot find any data.' mod='mpsellerstats'}</td>
    </tr>
{/if}