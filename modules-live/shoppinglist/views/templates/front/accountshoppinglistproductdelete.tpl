
{**
* AccountShoppingListProductDelete Template
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
    <p>{l s='Are you sure, you want to delete this product to the shopping list' mod='shoppinglist'} : {$title|escape:'htmlall':'UTF-8'}?</p>

    <div id="action-shopping-list">
        <a class="cancel-shopping-list btn btn-default button button-medium exclusive" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['action' => 'index', 'id_shopping_list' => $id_shopping_list])|escape:'htmlall':'UTF-8'}">
            <span>{l s='Cancel' mod='shoppinglist'}</span>
        </a>
        <a class="validate-shopping-list btn btn-default button red button-medium" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['action' => 'deleteConfirm', 'id_shopping_list' => $id_shopping_list, 'id_product' => $id_product, 'id_product_attribute' => $id_product_attribute])|escape:'htmlall':'UTF-8'}">
            <span>{l s='Validate' mod='shoppinglist'}</span>
        </a>
    </div>
{/block}