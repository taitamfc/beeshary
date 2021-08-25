
{**
* AccountShoppingListDelete Template
* 
* @author Empty
* @copyright 2007-2016 PrestaShop SA
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{extends file='customer/page.tpl'}

{block name='page_title'}
    {l s='My Shopping List' mod='shoppinglist'}
{/block}

{block name='page_content'}
    <p>{l s='Are you sure, you want to delete this shopping list' mod='shoppinglist'} : {$shoppingListObj->title|escape:'htmlall':'UTF-8'}?</p>

    <a class="btn btn-primary" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglist', ['action' => 'index'])|escape:'htmlall':'UTF-8'}">
        {l s='Cancel' mod='shoppinglist'}
    </a>
    <a class="btn btn-primary" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglist', ['action' => 'deleteConfirm', 'id_shopping_list' => $shoppingListObj->id_shopping_list])|escape:'htmlall':'UTF-8'}">
        {l s='Validate' mod='shoppinglist'}
    </a>
{/block}