
{**
* AccountShoppingListDelete Template
*
* @author Empty
* @copyright 2007-2016 PrestaShop SA
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}


<div id="title-product" style="display:none;">{$title|escape:'htmlall':'UTF-8'}</div>

<div class="shopping-list">
    <a style="color:#000000;" class="" title="{l s='Add to my shopping list' mod='shoppinglist'}" href="#"><i class="material-icons">favorite</i>
	     {l s='Add to my shopping list' mod='shoppinglist'}</a> 
    {if $shoppingList}
        <ul>
            {foreach from=$shoppingList item=itemList}
                <li>
                    <a data-href="{$link->getModuleLink('shoppinglist', 'ajaxproductshoppinglist', ['id_shopping_list' => $itemList.id_shopping_list|escape:'htmlall':'UTF-8', 'static_token' => $static_token])}">{$itemList.title|escape:'htmlall':'UTF-8'}</a>
                </li>
            {/foreach}
        </ul>
    {/if}
</div>
