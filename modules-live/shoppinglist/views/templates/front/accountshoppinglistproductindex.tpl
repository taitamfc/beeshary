
{**
* AccountShoppingListProductIndex Template
* 
* @author Empty
* @copyright 2007-2016 PrestaShop SA
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{****************
{extends file='customer/page.tpl'}

{block name='page_title'}
    {$shoppingListObj->title|escape:'htmlall':'UTF-8'}
{/block}

{block name='page_content'}
    <h6>{l s='You find here all your product' mod='shoppinglist'}</h6>

    {if $shoppingListProducts}
        <table id="shopping-list" class="table table-striped table-bordered table-labeled">
            <thead class="thead-default">
                <tr>
                    <th class="hide-mobile">{l s='Reference' mod='shoppinglist'}</th>
                    <!--<th>{l s='Itemisation' mod='shoppinglist'}</th>-->
                    <th>{l s='Title' mod='shoppinglist'}</th>
                    <th>{l s='Action' mod='shoppinglist'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$shoppingListProducts item=itemList}
                    <tr>
                        <td class="hide-mobile">{$itemList.id_product|escape:'htmlall':'UTF-8'}</td>
                        <!--<td>{$itemList.id_product_attribute|escape:'htmlall':'UTF-8'}</td>-->
                        <td>{$itemList.title|escape:'htmlall':'UTF-8'}</td>
                        <td>
                            <a class="btn" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['action' => 'addOneToCart', 'add' => '1', 'id_shopping_list' => $itemList.id_shopping_list|escape:'htmlall':'UTF-8', 'id_product' => $itemList.id_product|escape:'htmlall':'UTF-8', 'id_product_attribute' => $itemList.id_product_attribute|escape:'htmlall':'UTF-8'])}">
                                {l s='Add to cart' mod='shoppinglist'}
                            </a>
                            <a class="btn" href="{$link->getProductLink($itemList.id_product|escape:'htmlall':'UTF-8', null, null, null, null, null, $itemList.id_product_attribute|escape:'htmlall':'UTF-8')}" target="_blank">
                                {l s='See' mod='shoppinglist'}
                            </a>
                            <a class="btn" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['action' => 'delete', 'id_shopping_list' => $itemList.id_shopping_list|escape:'htmlall':'UTF-8', 'id_product' => $itemList.id_product|escape:'htmlall':'UTF-8', 'id_product_attribute' => $itemList.id_product_attribute|escape:'htmlall':'UTF-8'])}?id_product={$itemList.id_product|escape:'htmlall':'UTF-8'}">
                                {l s='Delete' mod='shoppinglist'}
                            </a>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {else}
        <p id="no-product">{l s='No product in this shopping list' mod='shoppinglist'}</p>
    {/if}

    {if $shoppingListProducts}
        <a class="btn btn-primary" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['action' => 'addAllToCart', 'id_shopping_list' => $shoppingListObj->id_shopping_list|escape:'htmlall':'UTF-8'])}">
            {l s='Add all products to cart' mod='shoppinglist'}<i class="icon-shopping-cart right"></i>
        </a>
    {/if}
    <a class="btn btn-primary" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglist', ['id_shopping_list' => $shoppingListObj->id_shopping_list|escape:'htmlall':'UTF-8'])}">
        {l s='Back to list' mod='shoppinglist'}<i class="icon-chevron-left right"></i>
    </a>
{/block}
****************}

{extends file=$layout}

{block name='content'}
<div class="wk-mp-block">
    {hook h="displayMPMyAccountMenu"}
    <div class="wk-mp-content">
        <div class="wk-mp-right-column">
            <div class="row">
                <h6>{l s='You find here all your product' mod='shoppinglist'}</h6>

                {if $shoppingListProducts}
                    <table id="shopping-list" class="table table-striped table-bordered table-labeled">
                        <thead class="thead-default">
                            <tr>
                                <th class="hide-mobile">{l s='Reference' mod='shoppinglist'}</th>
                                <!--<th>{l s='Itemisation' mod='shoppinglist'}</th>-->
                                <th>{l s='Title' mod='shoppinglist'}</th>
                                <th>{l s='Action' mod='shoppinglist'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$shoppingListProducts item=itemList}
                                <tr>
                                    <td class="hide-mobile">{$itemList.id_product|escape:'htmlall':'UTF-8'}</td>
                                    <!--<td>{$itemList.id_product_attribute|escape:'htmlall':'UTF-8'}</td>-->
                                    <td>{$itemList.title|escape:'htmlall':'UTF-8'}</td>
                                    <td>
                                        <a class="btn" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['action' => 'addOneToCart', 'add' => '1', 'id_shopping_list' => $itemList.id_shopping_list|escape:'htmlall':'UTF-8', 'id_product' => $itemList.id_product|escape:'htmlall':'UTF-8', 'id_product_attribute' => $itemList.id_product_attribute|escape:'htmlall':'UTF-8'])}">
                                            {l s='Add to cart' mod='shoppinglist'}
                                        </a>
                                        <a class="btn" href="{$link->getProductLink($itemList.id_product|escape:'htmlall':'UTF-8', null, null, null, null, null, $itemList.id_product_attribute|escape:'htmlall':'UTF-8')}" target="_blank">
                                            {l s='See' mod='shoppinglist'}
                                        </a>
                                        <a class="btn" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['action' => 'delete', 'id_shopping_list' => $itemList.id_shopping_list|escape:'htmlall':'UTF-8', 'id_product' => $itemList.id_product|escape:'htmlall':'UTF-8', 'id_product_attribute' => $itemList.id_product_attribute|escape:'htmlall':'UTF-8'])}?id_product={$itemList.id_product|escape:'htmlall':'UTF-8'}">
                                            {l s='Delete' mod='shoppinglist'}
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                {else}
                    <p id="no-product">{l s='No product in this shopping list' mod='shoppinglist'}</p>
                {/if}

                {if $shoppingListProducts}
                    <a class="btn btn-primary" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['action' => 'addAllToCart', 'id_shopping_list' => $shoppingListObj->id_shopping_list|escape:'htmlall':'UTF-8'])}">
                        {l s='Add all products to cart' mod='shoppinglist'}<i class="icon-shopping-cart right"></i>
                    </a>
                {/if}
                <a class="btn btn-primary" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglist', ['id_shopping_list' => $shoppingListObj->id_shopping_list|escape:'htmlall':'UTF-8'])}">
                    {l s='Back to list' mod='shoppinglist'}<i class="icon-chevron-left right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
{/block}