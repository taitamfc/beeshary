
{**
* AccountShoppingListIndex Template
* 
* @author Empty
* @copyright 2007-2016 PrestaShop SA
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{**************
{extends file='customer/page.tpl'}

{block name='page_title'}
    {l s='My Shopping List' mod='shoppinglist'}
{/block}

{block name='page_content'}
    <h6>{l s='You find here a page who permit to manage all your shopping list' mod='shoppinglist'}</h6>

    {if $shoppingList}
        <table id="shopping-list" class="table table-striped table-bordered table-labeled">
            <thead class="thead-default">
                <tr>
                    <th class="hide-mobile">{l s='Reference' mod='shoppinglist'}</th>
                    <th>{l s='Title' mod='shoppinglist'}</th>
                    <th class="hide-mobile">{l s='Date Add' mod='shoppinglist'}</th>
                    <th>{l s='Action' mod='shoppinglist'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$shoppingList item=itemList}
                    <tr>
                        <td class="hide-mobile">{$itemList.id_shopping_list|escape:'htmlall':'UTF-8'}</td>
                        <td>{$itemList.title|escape:'htmlall':'UTF-8'}</td>
                        <td class="hide-mobile">{$itemList.date_add|escape:'htmlall':'UTF-8'}</td>
                        <td>
                            <a href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['id_shopping_list' => $itemList.id_shopping_list|escape:'htmlall':'UTF-8'])}">
                                {l s='See Products' mod='shoppinglist'}
                            </a>&nbsp;&nbsp;
                            <a href="{$link->getModuleLink('shoppinglist', 'accountshoppinglist', ['action' => 'update', 'id_shopping_list' => $itemList.id_shopping_list|escape:'htmlall':'UTF-8'])}">
                                {l s='Update' mod='shoppinglist'}
                            </a>&nbsp;&nbsp;
                            <a href="{$link->getModuleLink('shoppinglist', 'accountshoppinglist', ['action' => 'delete', 'id_shopping_list' => $itemList.id_shopping_list|escape:'htmlall':'UTF-8'])}">
                                {l s='Delete' mod='shoppinglist'}
                            </a>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>

        <a class="btn btn-primary" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglist', ['action' => 'add'])|escape:'htmlall':'UTF-8'}">
            {l s='Add a shopping list' mod='shoppinglist'}
        </a>
    {/if}
{/block}
*************}

{extends file=$layout}

{block name='content'}
<div class="wk-mp-block">
    {hook h="displayMPMyAccountMenu"}
    <div class="wk-mp-content">
        <div class="wk-mp-right-column">
            <div class="row">
                <h6>{l s='You find here a page who permit to manage all your shopping list' mod='shoppinglist'}</h6>

                {if $shoppingList}
                    <table id="shopping-list" class="table table-striped table-bordered table-labeled">
                        <thead class="thead-default">
                            <tr>
                                <th class="hide-mobile">{l s='Reference' mod='shoppinglist'}</th>
                                <th>{l s='Title' mod='shoppinglist'}</th>
                                <th class="hide-mobile">{l s='Date Add' mod='shoppinglist'}</th>
                                <th>{l s='Action' mod='shoppinglist'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$shoppingList item=itemList}
                                <tr>
                                    <td class="hide-mobile">{$itemList.id_shopping_list|escape:'htmlall':'UTF-8'}</td>
                                    <td>{$itemList.title|escape:'htmlall':'UTF-8'}</td>
                                    <td class="hide-mobile">{$itemList.date_add|escape:'htmlall':'UTF-8'}</td>
                                    <td>
                                        <a href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['id_shopping_list' => $itemList.id_shopping_list|escape:'htmlall':'UTF-8'])}">
                                            {l s='See Products' mod='shoppinglist'}
                                        </a>&nbsp;&nbsp;
                                        {***<!--a href="{$link->getModuleLink('shoppinglist', 'accountshoppinglist', ['action' => 'update', 'id_shopping_list' => $itemList.id_shopping_list|escape:'htmlall':'UTF-8'])}">
                                            {l s='Update' mod='shoppinglist'}
                                        </a>&nbsp;&nbsp;-->****}
                                        <a href="{$link->getModuleLink('shoppinglist', 'accountshoppinglist', ['action' => 'delete', 'id_shopping_list' => $itemList.id_shopping_list|escape:'htmlall':'UTF-8'])}">
                                            {l s='Delete' mod='shoppinglist'}
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                {/if}

                <a class="btn btn-primary" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglist', ['action' => 'add'])|escape:'htmlall':'UTF-8'}">
                    {l s='Add a shopping list' mod='shoppinglist'}
                </a>
            </div>
        </div>
    </div>
</div>
{/block}
