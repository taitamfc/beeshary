
{**
* AccountShoppingListForm Template
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

    <h2>{$introduction|escape:'htmlall':'UTF-8'}</h2><br />

    <form action="{$link->getModuleLink('shoppinglist', 'accountshoppinglist')|escape:'htmlall':'UTF-8'}" method="post">
        <section class="form-fields">
            <div class="form-group row">
                <label class="col-md-3 form-control-label">{l s='Title' mod='shoppinglist'}</label>
                <div class="col-md-6">
                    <input type="hidden" name="action" value="{$action|escape:'htmlall':'UTF-8'}" />
                    <input type="hidden" name="id_shopping_list" value="{$shoppingListObj->id_shopping_list|escape:'htmlall':'UTF-8'}" />
                    <input class="form-control" name="title" id="title" type="text" required="required" value="{$shoppingListObj->title|escape:'htmlall':'UTF-8'}">
                </div>
            </div>
        </section>

        <footer class="form-footer clearfix">
            <button class="btn btn-primary form-control-submit pull-xs-right" type="submit">
                {$submit|escape:'htmlall':'UTF-8'}
            </button>
        </footer>
    </form>
{/block}
*************}

{extends file=$layout}

{block name='content'}
<div class="wk-mp-block">
    {hook h="displayMPMyAccountMenu"}
    <div class="wk-mp-content">
        <div class="wk-mp-right-column">
            <div class="row">
                <h2>{$introduction|escape:'htmlall':'UTF-8'}</h2><br />

                <form action="{$link->getModuleLink('shoppinglist', 'accountshoppinglist')|escape:'htmlall':'UTF-8'}" method="post">
                    <section class="form-fields">
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">{l s='Title' mod='shoppinglist'}</label>
                            <div class="col-md-6">
                                <input type="hidden" name="action" value="{$action|escape:'htmlall':'UTF-8'}" />
                                <input type="hidden" name="id_shopping_list" value="{$shoppingListObj->id_shopping_list|escape:'htmlall':'UTF-8'}" />
                                <input class="form-control" name="title" id="title" type="text" required="required" value="{$shoppingListObj->title|escape:'htmlall':'UTF-8'}">
                            </div>
                        </div>
                    </section>

                    <footer class="form-footer clearfix">
                        <button class="btn btn-primary form-control-submit pull-xs-right" type="submit">
                            {$submit|escape:'htmlall':'UTF-8'}
                        </button>
                    </footer>
                </form>
            </div>
        </div>
    </div>
</div>
{/block}
